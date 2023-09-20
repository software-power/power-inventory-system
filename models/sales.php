<?php

/**
 *  sales
 */
class Sales extends model
{
    public const SOURCE_QUICK = 'quick';
    public const SOURCE_DETAILED = 'detailed';

    public const DISCOUNT_MODE_EXCLUSIVE = 'exclusive';
    public const DISCOUNT_MODE_INCLUSIVE = 'inclusive';


    var $table = "sales";
    static $saleClass = null;

    function __construct()
    {
        self::$saleClass = $this;
    }

    function salesList($salesid = "", $createdby = "", $fromdate = "", $todate = "", $clientid = "", $locationid = "", $branchid = "",
                       $paymenttype = "", $tra_receipt_only = false, $incomplete_payment = false, $currencyid = "", $approved = false,
                       $payment_status = "", $order_invoice_createdby = "", $tally_post = "", $sr_only = false, $invoiceno = "")
    {
        $sql = "select s.*,
                       s.id                                                                                       as salesid,
                       prevsale.receipt_no                                                                        as prev_invoiceno,
                       fiscsale.id                                                                                as fisc_invoiceid,
                       fiscsale.receipt_no                                                                        as fisc_invoiceno,
                       converter.name                                                                             as converter,
                       s.full_amount - s.lastpaid_totalamount - s.total_increturn                                 as pending_amount,
                       round(s.grand_amount * s.currency_amount, 2)                                               as base_grand_amount,
                       round(s.grand_vatamount * s.currency_amount, 2)                                            as base_grand_vatamount,
                       round(s.full_amount * s.currency_amount, 2)                                                as base_full_amount,
                       round((s.full_amount - s.lastpaid_totalamount - s.total_increturn) * s.currency_amount, 2) as base_pending_amount,
                       c.name                                                                                     as clientname,
                       c.ledgername                                                                               as clientledgername,
                       c.tinno                                                                                    as clientinno,
                       c.address                                                                                  as client_address,
                       c.email,
                       c.mobile,
                       c.reseller,
                       cu.name                                                                                    as currencyname,
                       cu.id                                                                                      as currencyid,
                       cu.description                                                                             as currency_description,
                       cu.base                                                                                    as base_currency,
                       cr.rate_amount                                                                             as current_rate,
                       stl.name                                                                                   as stocklocation,
                       stl.tally_cash_ledger,
                       b.id                                                                                       as branchid,
                       b.name                                                                                     as branchname,
                       b.cost_center,
                       b.tally_cash_ledger                                                                        as branch_cash_ledger,
                       b.invoice_prefix                                                                           as branch_invoice_prefix,
                       s.iscreditapproved,
                       u.name                                                                                     as issuedby,
                       us.name                                                                                    as sales_person,
                       orders.id                                                                                  as orderno,
                       order_creator.id                                                                           as order_createdby,
                       order_creator.name                                                                         as order_creator
                from sales as s
                         inner join clients as c on c.id = s.clientid
                         inner join locations as stl on stl.id = s.locationid
                         inner join branches as b on stl.branchid = b.id
                         inner join currencies_rates as cr on cr.id = s.currency_rateid
                         inner join currencies as cu on cu.id = cr.currencyid
                         inner join users as u on u.id = s.createdby
                         left join users as us on us.id = s.salespersonid
                         left join users as converter on converter.id = s.fisc_convertedby
                         left join orders on orders.id = s.orderid
                         left join users as order_creator on order_creator.id = orders.createdby
                         left join sales prevsale on s.previd = prevsale.id
                         left join sales fiscsale on s.id = fiscsale.previd
                where 1 = 1";

        if ($salesid) {
            if (is_array($salesid)) {
                $sql .= " and s.id in (" . implode(',', $salesid) . ")";
            } else {
                $sql .= " and s.id = $salesid";
            }
        }
        if ($invoiceno) $sql .= " and s.receipt_no = '$invoiceno'";
        if ($createdby) $sql .= " and s.createdby = $createdby";
        if ($tally_post) $sql .= " and s.tally_post = $tally_post";
        if ($order_invoice_createdby) $sql .= " and (s.createdby = $order_invoice_createdby or orders.createdby = $order_invoice_createdby)";
        if ($clientid) $sql .= " and s.clientid = $clientid";
        if ($locationid) $sql .= " and s.locationid = $locationid";
        if ($branchid) {
            if (is_array($branchid)) {
                $sql .= " and b.id in ('" . implode("','", $branchid) . "')";
            } else {
                $sql .= " and b.id = $branchid";
            }
        }
        if ($currencyid) $sql .= " and cu.id = $currencyid";
        if ($fromdate) {
            $date_format = DateTime::createFromFormat("Y-m-d", $fromdate) !== false ? '%Y-%m-%d' : '%Y-%m-%d %H:%i';
            $sql .= " and date_format(s.doc,'$date_format') >= '$fromdate'";
        }
        if ($todate) {
            $date_format = DateTime::createFromFormat("Y-m-d", $todate) !== false ? '%Y-%m-%d' : '%Y-%m-%d %H:%i';
            $sql .= " and date_format(s.doc,'$date_format') <= '$todate'";
        }
        if ($paymenttype) $sql .= " and s.paymenttype = '$paymenttype'";
        if ($tra_receipt_only) $sql .= " and s.receipt_method != 'sr'";
        if ($sr_only) $sql .= " and s.receipt_method = 'sr'";
        if ($approved) $sql .= " and s.iscreditapproved = 1";
        if ($incomplete_payment) $sql .= " and s.payment_status != '" . PAYMENT_STATUS_COMPLETE . "'";
        if ($payment_status) $sql .= " and s.payment_status = '$payment_status'";

        $sql .= " order by s.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function salesWithFiscalization($salesid)
    {
        $sql = "select sf.*,
                       s.receipt_no,
                       s.locationid,
                       s.receipt_method,
                       s.print_size,
                       s.paymenttype,
                       s.has_installment,
                       s.grand_amount,
                       s.grand_vatamount,
                       s.full_amount,
                       round(s.grand_amount * s.currency_amount, 2)    as base_grand_amount,
                       round(s.grand_vatamount * s.currency_amount, 2) as base_grand_vatamount,
                       round(s.full_amount * s.currency_amount, 2)     as base_full_amount,
                       s.currency_amount,
                       c.name                                          as clientname,
                       c.tinno                                         as clientinno,
                       c.vatno                                         as clientvrn,
                       c.address                                       as client_address,
                       c.email,
                       c.mobile,
                       cu.name                                         as currencyname,
                       cu.id                                           as currencyid,
                       cu.description                                  as currency_description,
                       cu.base                                         as base_currency,
                       cr.rate_amount                                  as current_rate,
                       stl.name                                        as stocklocation,
                       s.iscreditapproved,
                       s.doc,
                       s.duedate,
                       s.description,
                       u.name                                          as issuedby
                from sales s
                         inner join clients as c on c.id = s.clientid
                         inner join locations as stl on stl.id = s.locationid
                         inner join branches as b on stl.branchid = b.id
                         inner join currencies_rates as cr on cr.id = s.currency_rateid
                         inner join currencies as cu on cu.id = cr.currencyid
                         inner join users as u on u.id = s.createdby
                         left join sales_fiscalization sf on sf.salesid = s.id
                where (s.receipt_method = 'vfd' or s.receipt_method = 'efd') and s.id = $salesid";
        return fetchRow($sql);
    }

    function failedFiscalization($saleid = "", $locationid = "", $branchid = "")
    {
        $sql = "select s.*,
                       s.id                                            as salesid,
                       sf.fiscalize_status_message,
                       cu.name                                         as currencyname,
                       cu.description                                  as currency_description,
                       cu.base                                         as base_currency,
                       round(s.grand_amount * s.currency_amount, 2)    as base_grand_amount,
                       round(s.grand_vatamount * s.currency_amount, 2) as base_grand_vatamount,
                       round(s.full_amount * s.currency_amount, 2)     as base_full_amount,
                       depart.name                                     as departmentname,
                       locations.name                                  as locationname,
                       branches.name                                   as branchname,
                       clients.name                                    as clientname,
                       seller.name                                     as salesperson
                from sales s
                         left join clients on s.clientid = clients.id
                         left join currencies_rates cr on cr.id = s.currency_rateid
                         left join currencies cu on cu.id = cr.currencyid
                         left join locations on s.locationid = locations.id
                         left join branches on locations.branchid = branches.id
                         left join users seller on s.createdby = seller.id
                         left join departments depart on depart.id = seller.deptid
                         left join sales_fiscalization sf on sf.salesid = s.id
                where s.iscreditapproved = 1
                  and (s.receipt_method = 'vfd' or s.receipt_method = 'efd')
                  and (sf.fiscalize_status_message is null or sf.fiscalize_status_message != 'success')";
        if ($saleid) $sql .= " and s.id = $saleid";
        if ($locationid) $sql .= " and s.locationid = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        $sql .= " order by s.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function saleDetails($createdby = "", $salesid = "", $group = true, $fromdate = "", $todate = "", $clientid = "", $locationid = "", $productid = "", $tally_post = "", $paymenttype = "", $fiscalized_only = false, $branchid = "")
    {
        $sql = "select s.id                                                                                                                           as salesid,
                       s.receipt_no,
                       s.paymenttype,
                       s.clientid,
                       s.receipt_method,
                       s.doc,
                       c.name                                                                                                                         as clientname,
                       c.tinno                                                                                                                        as clientino,
                       c.mobile,
                       cu.name                                                                                                                        as currencyname,
                       p.name                                                                                                                         as productname,
                       p.id                                                                                                                           as productid,
                       if(st.id is null, 1, 0)                                                                                                        as sold_non_stock,
                       p.productcategoryid,
                       stl.name                                                                                                                       as locationname,
                       b.id                                                                                                                           as branchid,
                       b.name                                                                                                                         as branchname,
                       s.iscreditapproved,
                       u.name                                                                                                                         as sales_person,
                       sd.price,
                       sd.sinc,
                       if(sd.sinc, sd.incprice, round(sd.price * (1 + sd.vat_rate / 100), 2))                                                         as inc_price,
                       sd.quantity,
                       sd.vat_rate,
                       IF(sd.sinc, round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2), round((sd.price - sd.discount) * sd.quantity, 2)) as amount,
                       IF(sd.sinc, (sd.incprice * sd.quantity) - round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2),
                          round((sd.price - sd.discount) * sd.quantity * sd.vat_rate / 100, 2))                                                       as vat_amount,
                       IF(sd.sinc, sd.incprice * sd.quantity, round((sd.price - sd.discount) * sd.quantity * (1 + sd.vat_rate / 100), 2))             as total_amount
                from sales as s
                         inner join clients as c on c.id = s.clientid
                         inner join locations as stl on stl.id = s.locationid
                         inner join branches as b on stl.branchid = b.id
                         inner join salesdetails as sd on sd.salesid = s.id
                         left join stocks as st on st.id = sd.stockid
                         inner join products as p on p.id = st.productid or p.id = sd.productid
                         inner join currencies_rates as cr on cr.id = s.currency_rateid
                         inner join currencies as cu on cu.id = cr.currencyid
                         inner join users as u on u.id = s.createdby
                where 1 = 1";

        if ($salesid) $sql .= " and sd.salesid = $salesid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($createdby) $sql .= " and s.createdby = $createdby";
        if ($clientid) $sql .= " and s.clientid = $clientid";
        if ($locationid) $sql .= " and s.locationid = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($fromdate) $sql .= " and s.doc >= '$fromdate'";
        if ($todate) $sql .= " and s.doc <= '$todate'";
        if ($tally_post) $sql .= " and s.tally_post = $tally_post";
        if ($paymenttype) $sql .= " and s.paymenttype = '$paymenttype'";
        if ($fiscalized_only) $sql .= " and s.receipt_method != 'sr'";
        if ($group) $sql .= " group by s.id";
        $sql .= " order by s.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function detailedSalesList($createdby = "", $salesid = "", $group = true, $fromdate = "", $todate = "", $clientid = "", $branchid = "", $productid = "", $tally_post = "")
    {
        $sql = "select s.id                                                                                                                           as salesid,
                       s.receipt_no,
                       s.previd,
                       prevsale.receipt_no                                                                                                            as prev_invoiceno,
                       fiscsale.id                                                                                                                    as fisc_invoiceid,
                       fiscsale.receipt_no                                                                                                            as fisc_invoiceno,
                       s.fisc_convertedby,
                       converter.name                                                                                                                 as converter,
                       s.fisc_convertdate,
                       s.credit_convertedby,
                       s.credit_convertedat,
                       s.credit_convert_remarks,
                       s.paymenttype,
                       s.source,
                       s.clientid,
                       cu.id                                                                                                                          as currencyid,
                       cu.name                                                                                                                        as currencyname,
                       cu.description                                                                                                                 as currency_description,
                       s.currency_amount,
                       s.orderid,
                       s.proformaid,
                       s.has_installment,
                       s.dist_plan,
                       s.vat_exempted,
                       s.payment_status,
                       s.print_size,
                       s.grand_amount,
                       s.grand_vatamount,
                       s.description                                                                                                                  as remarks,
                       s.internal_remarks,
                       s.total_discount,
                       s.full_amount,
                       s.lastpaid_totalamount,
                       s.total_increturn,
                       (s.full_amount - s.lastpaid_totalamount - s.total_increturn)                                                                   as pending_amount,
                       s.receipt_method,
                       s.doc,
                       s.approvedate,
                       date_format(s.doc, '%D-%M-%Y')                                                                                                 as issue_date,
                       orders.id                                                                                                                      as orderno,
                       orders.foreign_orderid,
                       order_creator.id                                                                                                               as order_createdby,
                       order_creator.name                                                                                                             as order_creator,
                       sd.id                                                                                                                          as sdi,
                       sd.price,
                       sd.incprice,
                       sd.sinc,
                       sd.discount,
                       (sd.discount * 100 / sd.price)                                                                                                 as discountpercent,
                       sd.price - sd.discount                                                                                                         as selling_price,
                       sd.hidden_cost                                                                                                                 as costprice,
                       IF(sd.sinc, round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2), round((sd.price - sd.discount) * sd.quantity, 2)) as amount,
                       sd.quantity,
                       sd.vat_rate,
                       sd.show_print,
                       sd.print_extra,
                       sd.vat_rate,
                       IF(sd.sinc, (sd.incprice * sd.quantity) - round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2),
                          round((sd.price - sd.discount) * sd.quantity * sd.vat_rate / 100, 2))                                                       as vat_amount,
                       IF(sd.sinc, sd.incprice * sd.quantity, round((sd.price - sd.discount) * sd.quantity * (1 + sd.vat_rate / 100), 2))             as total_amount,
                       c.name                                                                                                                         as clientname,
                       c.tinno                                                                                                                        as clientino,
                       c.vatno                                                                                                                        as clientvrn,
                       c.mobile_country_code,
                       c.mobile,
                       c.address,
                       c.email,
                       c.reseller,
                       p.name                                                                                                                         as productname,
                       p.description                                                                                                                  as product_description,
                       extradesc.description                                                                                                          as extra_description,
                       p.generic_name                                                                                                                 as generic_name,
                       p.id                                                                                                                           as productid,
                       if(st.id is null, 1, 0)                                                                                                        as sold_non_stock,
                       p.trackserialno,
                       p.validate_serialno,
                       p.track_expire_date,
                       p.prescription_required,
                       p.barcode_manufacture,
                       p.barcode_office,
                       departments.name                                                                                                               as departmentname,
                       departments.tally_sales_account,
                       stl.name                                                                                                                       as stocklocation,
                       stl.tally_cash_ledger,
                       branches.id                                                                                                                    as branchid,
                       branches.name                                                                                                                  as branchname,
                       s.iscreditapproved,
                       s.has_combine,
                       u.name                                                                                                                         as sales_person,
                       approver.name                                                                                                                  as approver,
                       units.abbr                                                                                                                     as units,
                       s.tally_post                                                                                                                   as tally_post,
                       d.name                                                                                                                         as prescription_doctor,
                       h.name                                                                                                                         as prescription_hospital,
                       spd.prescription,
                       spd.referred,
                       b.id                                                                                                                           as batchId,
                       b.batch_no,
                       b.expire_date,
                       sb.qty                                                                                                                         as batchSoldQty
                from sales as s
                         inner join clients as c on c.id = s.clientid
                         inner join locations as stl on stl.id = s.locationid
                         inner join branches on stl.branchid = branches.id
                         left join sales prevsale on s.previd = prevsale.id
                         left join sales fiscsale on s.id = fiscsale.previd
                         left join orders on orders.id = s.orderid
                         left join users as order_creator on order_creator.id = orders.createdby
                         inner join salesdetails as sd on sd.salesid = s.id
                
                         left join salesdescriptions as extradesc on sd.id = extradesc.sdi
                         left join salesbatches as sb on sd.id = sb.sdi
                         left join batches as b on b.id = sb.batch_id
                         left join sales_prescription_details as spd on sd.id = spd.sdi
                         left join doctors as d on d.id = spd.doctor_id
                         left join hospitals as h on h.id = spd.hospital_id
                
                         left join stocks as st on st.id = sd.stockid
                         inner join products as p on (p.id = st.productid or p.id = sd.productid)
                         inner join categories on categories.id = p.categoryid
                         inner join departments on departments.id = p.departid
                         inner join currencies_rates as cr on cr.id = s.currency_rateid
                         inner join currencies as cu on cu.id = cr.currencyid
                         inner join users as u on u.id = s.createdby
                         left join users as converter on converter.id = s.fisc_convertedby
                         left join users as approver on approver.id = s.approvalby
                         inner join units on units.id = p.unit
                where 1 = 1";
        if ($salesid) $sql .= " and s.id = $salesid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($createdby) $sql .= " and s.createdby = $createdby";
        if ($clientid) $sql .= " and s.clientid = $clientid";
        if ($branchid) $sql .= " and s.locationid = $branchid";
        if ($fromdate) $sql .= " and s.doc >= '$fromdate'";
        if ($todate) $sql .= " and s.doc <= '$todate'";
        if ($tally_post) $sql .= " and s.tally_post = $tally_post";
        $sql .= " order by s.doc desc";
        // echo $sql;die();
//         debug($sql);
        if (!$group) {
            return fetchRows($sql);
        } else {
            //arranging the results
            $newArray = [];
            foreach ($details = fetchRows($sql) as $index => $item) {
                $newArray[$item['salesid']]['salesid'] = $item['salesid'];
                $newArray[$item['salesid']]['receipt_no'] = $item['receipt_no'];
                $newArray[$item['salesid']]['previd'] = $item['previd'];
                $newArray[$item['salesid']]['prev_invoiceno'] = $item['prev_invoiceno'];
                $newArray[$item['salesid']]['fisc_invoiceid'] = $item['fisc_invoiceid'];
                $newArray[$item['salesid']]['fisc_invoiceno'] = $item['fisc_invoiceno'];
                $newArray[$item['salesid']]['fisc_convertedby'] = $item['fisc_convertedby'];
                $newArray[$item['salesid']]['converter'] = $item['converter'];
                $newArray[$item['salesid']]['fisc_convertdate'] = $item['fisc_convertdate'];
                $newArray[$item['salesid']]['credit_convertedby'] = $item['credit_convertedby'];
                $newArray[$item['salesid']]['credit_convertedat'] = $item['credit_convertedat'];
                $newArray[$item['salesid']]['credit_convert_remarks'] = $item['credit_convert_remarks'];
                $newArray[$item['salesid']]['paymenttype'] = $item['paymenttype'];
                $newArray[$item['salesid']]['source'] = $item['source'];
                $newArray[$item['salesid']]['clientid'] = $item['clientid'];
                $newArray[$item['salesid']]['orderid'] = $item['orderid'];
                $newArray[$item['salesid']]['proformaid'] = $item['proformaid'];
                $newArray[$item['salesid']]['has_installment'] = $item['has_installment'];
                $newArray[$item['salesid']]['dist_plan'] = $item['dist_plan'];
                $newArray[$item['salesid']]['vat_exempted'] = $item['vat_exempted'];
                $newArray[$item['salesid']]['payment_status'] = $item['payment_status'];
                $newArray[$item['salesid']]['print_size'] = $item['print_size'];
                $newArray[$item['salesid']]['grand_amount'] = $item['grand_amount'];
                $newArray[$item['salesid']]['grand_vatamount'] = $item['grand_vatamount'];
                $newArray[$item['salesid']]['remarks'] = $item['remarks'];
                $newArray[$item['salesid']]['internal_remarks'] = $item['internal_remarks'];
                $newArray[$item['salesid']]['total_discount'] = $item['total_discount'];
                $newArray[$item['salesid']]['full_amount'] = $item['full_amount'];
                $newArray[$item['salesid']]['lastpaid_totalamount'] = $item['lastpaid_totalamount'];
                $newArray[$item['salesid']]['total_increturn'] = $item['total_increturn'];
                $newArray[$item['salesid']]['pending_amount'] = $item['pending_amount'];
                $newArray[$item['salesid']]['receipt_method'] = $item['receipt_method'];
                $newArray[$item['salesid']]['doc'] = $item['doc'];
                $newArray[$item['salesid']]['issue_date'] = $item['issue_date'];
                $newArray[$item['salesid']]['orderno'] = $item['orderno'];
                $newArray[$item['salesid']]['foreign_orderid'] = $item['foreign_orderid'];
                $newArray[$item['salesid']]['order_createdby'] = $item['order_createdby'];
                $newArray[$item['salesid']]['order_creator'] = $item['order_creator'];
                $newArray[$item['salesid']]['clientname'] = $item['clientname'];
                $newArray[$item['salesid']]['clientino'] = $item['clientino'];
                $newArray[$item['salesid']]['clientvrn'] = $item['clientvrn'];
                $newArray[$item['salesid']]['clientaddress'] = $item['address'];
                $newArray[$item['salesid']]['clientemail'] = $item['email'];
                $newArray[$item['salesid']]['reseller'] = $item['reseller'];
                $newArray[$item['salesid']]['mobile_country_code'] = $item['mobile_country_code'];
                $newArray[$item['salesid']]['mobile'] = $item['mobile'] ? ($item['mobile_country_code'] ? $item['mobile_country_code'] : "") . $item['mobile'] : "";
                $newArray[$item['salesid']]['currencyid'] = $item['currencyid'];
                $newArray[$item['salesid']]['currencyname'] = $item['currencyname'];
                $newArray[$item['salesid']]['currency_amount'] = $item['currency_amount'];
                $newArray[$item['salesid']]['currency_description'] = $item['currency_description'];
                $newArray[$item['salesid']]['exchange_rate'] = $item['exchange_rate'];
                $newArray[$item['salesid']]['stocklocation'] = $item['stocklocation'];
                $newArray[$item['salesid']]['branchid'] = $item['branchid'];
                $newArray[$item['salesid']]['branchname'] = $item['branchname'];
                $newArray[$item['salesid']]['tally_cash_ledger'] = $item['tally_cash_ledger'];
                $newArray[$item['salesid']]['iscreditapproved'] = $item['iscreditapproved'];
                $newArray[$item['salesid']]['has_combine'] = $item['has_combine'];
                $newArray[$item['salesid']]['sales_person'] = $item['sales_person'];
                $newArray[$item['salesid']]['approver'] = $item['approver'];
                $newArray[$item['salesid']]['approvedate'] = $item['approvedate'];
                $newArray[$item['salesid']]['tally_post'] = $item['tally_post'];
                $newArray[$item['salesid']]['products'][$item['productid']]['productid'] = $item['productid'];
                $newArray[$item['salesid']]['products'][$item['productid']]['sdi'] = $item['sdi'];
                $newArray[$item['salesid']]['products'][$item['productid']]['sold_non_stock'] = $item['sold_non_stock'];
                $newArray[$item['salesid']]['products'][$item['productid']]['productname'] = $item['productname'];
                $newArray[$item['salesid']]['products'][$item['productid']]['generic_name'] = $item['generic_name'];
                $newArray[$item['salesid']]['products'][$item['productid']]['product_description'] = $item['product_description'];
                $newArray[$item['salesid']]['products'][$item['productid']]['barcode_manufacture'] = $item['barcode_manufacture'];
                $newArray[$item['salesid']]['products'][$item['productid']]['barcode_office'] = $item['barcode_office'];
                $newArray[$item['salesid']]['products'][$item['productid']]['trackserialno'] = $item['trackserialno'];
                $newArray[$item['salesid']]['products'][$item['productid']]['validate_serialno'] = $item['validate_serialno'];
                $newArray[$item['salesid']]['products'][$item['productid']]['track_expire_date'] = $item['track_expire_date'];
                $newArray[$item['salesid']]['products'][$item['productid']]['prescription_required'] = $item['prescription_required'];
                $newArray[$item['salesid']]['products'][$item['productid']]['departmentname'] = $item['departmentname'];
                $newArray[$item['salesid']]['products'][$item['productid']]['tally_sales_account'] = $item['tally_sales_account'];
                $newArray[$item['salesid']]['products'][$item['productid']]['price'] = $item['price'];
                $newArray[$item['salesid']]['products'][$item['productid']]['discount'] = $item['discount'];
                $newArray[$item['salesid']]['products'][$item['productid']]['discountpercent'] = $item['discountpercent'];
                $newArray[$item['salesid']]['products'][$item['productid']]['selling_price'] = $item['selling_price'];
                $newArray[$item['salesid']]['products'][$item['productid']]['costprice'] = $item['costprice'];
                $newArray[$item['salesid']]['products'][$item['productid']]['incprice'] = $item['incprice'];
                $newArray[$item['salesid']]['products'][$item['productid']]['sinc'] = $item['sinc'];
                $newArray[$item['salesid']]['products'][$item['productid']]['currencyname'] = $item['currencyname'];
                $newArray[$item['salesid']]['products'][$item['productid']]['amount'] = $item['amount'];
                $newArray[$item['salesid']]['products'][$item['productid']]['quantity'] = $item['quantity'];
                $newArray[$item['salesid']]['products'][$item['productid']]['vat_rate'] = $item['vat_rate'];
                $newArray[$item['salesid']]['products'][$item['productid']]['vat_amount'] = $item['vat_amount'];
                $newArray[$item['salesid']]['products'][$item['productid']]['total_amount'] = $item['total_amount'];
                $newArray[$item['salesid']]['products'][$item['productid']]['units'] = $item['units'];
                $newArray[$item['salesid']]['products'][$item['productid']]['prescription'] = $item['prescription'];
                $newArray[$item['salesid']]['products'][$item['productid']]['show_print'] = $item['show_print'];
                $newArray[$item['salesid']]['products'][$item['productid']]['print_extra'] = $item['print_extra'];
                $newArray[$item['salesid']]['products'][$item['productid']]['extra_description'] = $item['extra_description'];
                $newArray[$item['salesid']]['products'][$item['productid']]['show_print'] = $item['show_print'];
                $newArray[$item['salesid']]['products'][$item['productid']]['prescription_doctor'] = $item['prescription_doctor'];
                $newArray[$item['salesid']]['products'][$item['productid']]['prescription_hospital'] = $item['prescription_hospital'];
                $newArray[$item['salesid']]['products'][$item['productid']]['referred'] = $item['referred'];
                if ($item['sold_non_stock']) continue;
                $newArray[$item['salesid']]['products'][$item['productid']]['batches'][$item['batchId']]['batchId'] = $item['batchId'];
                $newArray[$item['salesid']]['products'][$item['productid']]['batches'][$item['batchId']]['batch_no'] = $item['batch_no'];
                $newArray[$item['salesid']]['products'][$item['productid']]['batches'][$item['batchId']]['expire_date'] = $item['expire_date'];
                $newArray[$item['salesid']]['products'][$item['productid']]['batches'][$item['batchId']]['batchSoldQty'] = $item['batchSoldQty'];

            }

            return array_values($newArray);
        }

    }

    function getSalesOutstanding($salesid = "", $clientid = "", $createdby = "", $approvedonly = "", $currencyid = "", $order_invoice_createdby = "", $branchid = "", $acc_mng = "", $tra_invoice_only = false, $has_installment = false)
    {
        $sql = "select s.id                                                                                    as salesid,
                       s.doc                                                                                   as invoice_date,
                       s.credit_days                                                                           as credit_days,
                       s.receipt_no,
                       s.iscreditapproved,
                       s.payment_status,
                       s.receipt_method,
                       s.has_installment,
                       s.dist_plan,
                       s.invoiceremarkid,
                       ir.name                                                                                 as outstanding_remarks,
                       s.grand_amount,
                       s.grand_vatamount,
                       s.full_amount,
                       s.lastpaid_totalamount,
                       s.total_increturn,
                       (s.full_amount - s.lastpaid_totalamount - s.total_increturn)                            as pending_amount,
                       round((s.full_amount - s.lastpaid_totalamount - s.total_increturn) * cr.rate_amount, 2) as base_pending_amount,
                       cu.id                                                                                   as currencyid,
                       cu.name                                                                                 as currencyname,
                       cu.description                                                                          as currency_description,
                       cu.base                                                                                 as base_currency,
                       cr.rate_amount                                                                          as current_exchange_rate,
                       date_format(s.doc, '%d-%M-%Y')                                                          as issue_date,
                       date_format(s.duedate, '%d-%M-%Y')                                                      as due_date,
                       c.id                                                                                    as clientid,
                       c.name                                                                                  as clientname,
                       c.acc_mng,
                       acc_mng.name                                                                            as account_manager,
                       s.iscreditapproved,
                       s.createdby                                                                             as invoice_createdby,
                       u.name                                                                                  as acc_manager,
                       orders.id                                                                               as orderno,
                       order_creator.id                                                                        as order_createdby,
                       order_creator.name                                                                      as order_creator,
                       datediff(CURDATE(), s.doc)                                                              as day,
                       case
                           when datediff(CURDATE(), s.doc) < 30
                               then (s.full_amount - s.lastpaid_totalamount - s.total_increturn) end           as '(<30 days)',
                       case
                           when datediff(CURDATE(), s.doc) >= 30 and datediff(CURDATE(), s.doc) < 45
                               then (s.full_amount - s.lastpaid_totalamount - s.total_increturn) end           as '(30 to 45 days)',
                       case
                           when datediff(CURDATE(), s.doc) >= 45 and datediff(CURDATE(), s.doc) < 90
                               then (s.full_amount - s.lastpaid_totalamount - s.total_increturn) end           as '(45 to 90 days)',
                       case
                           when datediff(CURDATE(), s.doc) >= 90
                               then (s.full_amount - s.lastpaid_totalamount - s.total_increturn) end           as '(>90 days)'
                from sales as s
                         inner join clients as c on c.id = s.clientid
                         left join users as acc_mng on c.acc_mng = acc_mng.id
                         inner join users as u on u.id = s.createdby
                         inner join currencies_rates cr on cr.id = s.currency_rateid
                         inner join currencies cu on cr.currencyid = cu.id
                         inner join locations l on s.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         left join orders on orders.id = s.orderid
                         left join users as order_creator on order_creator.id = orders.createdby
                         left join invoice_remarks ir on s.invoiceremarkid = ir.id
                where s.payment_status != '" . PAYMENT_STATUS_COMPLETE . "' and s.paymenttype = '" . PAYMENT_TYPE_CREDIT . "'";

        if ($approvedonly) $sql .= " and s.iscreditapproved =1";
        if ($clientid) $sql .= " and s.clientid = $clientid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($currencyid) $sql .= " and cu.id = $currencyid";
        if ($salesid) $sql .= " and s.id = $salesid";
        if ($createdby) $sql .= " and s.createdby = $createdby";
        if ($acc_mng) $sql .= " and c.acc_mng = $acc_mng";
        if ($tra_invoice_only) $sql .= " and s.receipt_method != 'sr'";
        if ($has_installment) $sql .= " and s.has_installment";
        if ($order_invoice_createdby) $sql .= " and (s.createdby = $order_invoice_createdby or orders.createdby = $order_invoice_createdby)";
        $sql .= " order by s.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function dashboardSales($userid = "", $clientid = "", $fromdate = "", $todate = "", $paymenttype = PAYMENT_TYPE_CASH)
    {
        $sql = "select s.*,
                       s.id                              as invoiceno,
                       s.receipt_no, 
                       s.payment_status, 
                       date_format(s.doc, '%D,%M %Y')    as issudate,
                       c.name                            as clientname,
                       s.paymenttype, 
                       cu.name                           as currency_name,
                       cu.base                           as base_currency,
                       s.currency_amount,
                       s.full_amount * s.currency_amount as base_full_amount,
                       users.name                        as salesPerson
                from sales as s
                         inner join clients as c on c.id = s.clientid
                         inner join users on s.createdby = users.id
                         inner join currencies_rates cr on s.currency_rateid = cr.id
                         inner join currencies as cu on cu.id = cr.currencyid
                where 1 = 1 and s.iscreditapproved = 1";

        if ($userid) $sql .= " and s.createdby = $userid ";
        if ($clientid) $sql .= " and c.id = $clientid ";
        if ($fromdate) $sql .= " and date_format(s.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(s.doc,'%Y-%m-%d') <= '$todate'";
        if ($paymenttype == PAYMENT_TYPE_CREDIT) {
            $sql .= " and s.paymenttype = '$paymenttype' and s.payment_status != '" . PAYMENT_STATUS_COMPLETE . "'";
        } else {
            $sql .= " and s.paymenttype = '$paymenttype' ";
        }

        $sql .= " order by base_full_amount  desc";
        $sql .= " LIMIT 5";
//         debug($sql);
        return fetchRows($sql);
    }

    function dashboardMostSold($fromdate = "", $todate = "", $userid = "", $productid = "", $modelid = "", $productcategoryid = "", $locationid = "", $branchid = "", $limit = "")
    {
        $sql = "select p.id,
                       p.name                      as productname,
                       if(stocks.id is null,1,0)   as sold_non_stock,
                       model.name                  as brandname,
                       categories.name             as taxcategory,
                       sum(sd.quantity)            as qty
                from salesdetails sd
                         inner join sales on sales.id = sd.salesid
                         inner join locations l on l.id = sales.locationid
                         inner join branches b on l.branchid = b.id
                         left join stocks on sd.stockid = stocks.id
                         inner join products p on stocks.productid = p.id or p.id = sd.productid
                         inner join product_categories pc on p.productcategoryid = pc.id
                         inner join categories on p.categoryid = categories.id
                         inner join model on p.modelid = model.id
                         inner join users on sales.createdby = users.id
                where 1 = 1";
        if ($fromdate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') <= '$todate'";
        if ($userid) $sql .= " and users.id = $userid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($modelid) $sql .= " and model.id = $modelid";
        if ($productcategoryid) $sql .= " and pc.id = $productcategoryid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";


        $sql .= " group by p.id order by qty desc";
        if ($limit) $sql .= " limit $limit";
//        debug($sql);
        return fetchRows($sql);
    }

    // if($fromdate) echo ' and sales.doc >= '$fromdate
    function salesSummary($fromdate = "", $todate = "", $userid = "", $clientid = "", $productid = "", $modelid = "", $productcategory = "", $deptid = "", $locationid = "", $branchid = "")
    {
        $sql = "select p.id                         as productid,
                       p.name                       as productname,
                       if(stocks.id is null,1,0)    as sold_non_stock,
                       model.name                   as brandname,
                       d.name                       as departmentname,
                       units.name                   as unitname,
                       sum(sd.quantity)             as qty
                from salesdetails sd
                         inner join sales s on sd.salesid = s.id
                         left join stocks on sd.stockid = stocks.id
                         inner join products p on stocks.productid = p.id or p.id = sd.productid
                         inner join model on p.modelid = model.id
                         inner join product_categories pc on p.productcategoryid = pc.id
                         inner join departments d on p.departid = d.id
                         inner join units on p.unit = units.id
                         inner join locations l on s.locationid = l.id
                         inner join branches b on b.id = l.branchid
                where 1 = 1";

        if ($fromdate) $sql .= " and date_format(s.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(s.doc,'%Y-%m-%d') <= '$todate'";
        if ($userid) $sql .= " and s.createdby = $userid";
        if ($clientid) $sql .= " and s.clientid = $clientid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($modelid) $sql .= " and model.id = $modelid";
        if ($productcategory) $sql .= " and pc.id = $productcategory";
        if ($deptid) $sql .= " and d.id = $deptid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";

        $sql .= " group by p.id order by p.name";
        // debug($sql);
        return fetchRows($sql);

    }

    function monthWiseSales($months, $userid = "", $productid = "", $modelid = "", $deptid = "", $nonstock = "", $catid = "", $productcategoryid = "", $subcategoryid = "", $locationid = "", $branchid = "")
    {
        $sql = "select products.id                     as productID,
                       products.name                   as productName,
                       products.description            as productdescription,
                       products.barcode_office         as barcode,
                       products.non_stock,
                       if(stocks.id is null,1,0)       as sold_non_stock,
                       brand.name                      as brandName,
                       categories.name                 as catName,
                       units.name                      as unit,
                       date_format(sales.doc, '%b %Y') as salesMonth,
                       sum(sd.quantity)                as quantity,
                       sum((sd.price - sd.discount - IFNULL(credit_note.discount, 0)) * sd.quantity *
                           sales.currency_amount)      as amount,
                       sum(round((sd.price - sd.discount - IFNULL(credit_note.discount, 0)) * sd.quantity * sales.currency_amount * (1 + sd.vat_rate / 100),
                                 2))                   as incamount
                from salesdetails sd
                         left join stocks on stocks.id = sd.stockid
                         inner join products on stocks.productid = products.id or products.id = sd.productid
                         inner join categories on products.categoryid = categories.id
                         inner join units on units.id = products.unit
                         inner join model brand on products.modelid = brand.id
                         inner join product_categories pc on products.productcategoryid = pc.id
                         inner join product_subcategories ps on products.subcategoryid = ps.id
                         inner join departments on products.departid = departments.id
                         inner join sales on sd.salesid = sales.id
                         inner join locations l on sales.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         left join
                     (
                         select srd.sdi, sum(srd.rate) as discount
                         from sales_return_details srd
                                  inner join sales_returns sr on sr.id = srd.srid
                         where sr.approvedby > 0
                           and sr.type = 'price'
                         group by srd.sdi
                     ) as credit_note on sd.id = credit_note.sdi
                where 1 = 1 and sales.iscreditapproved = 1";

        if (!empty($months) && is_array($months)) {
            $sql .= " and date_format(sales.doc, '%m-%Y') in ('" . implode("','", $months) . "')";
        } else {
            $sql .= " and date_format(sales.doc, '%m-%Y') = '" . date('m-Y') . "'";
        }
        if ($userid) $sql .= " and sales.createdby = $userid";
        if ($productid) $sql .= " and products.id = $productid";
        if ($modelid) $sql .= " and brand.id = $modelid";
        if ($deptid) $sql .= " and departments.id= $deptid";
        if ($nonstock == 'yes') $sql .= " and products.non_stock = 1";
        if ($nonstock == 'no') $sql .= " and products.non_stock != 1";
        if ($productcategoryid) $sql .= " and pc.id = $productcategoryid";
        if ($subcategoryid) $sql .= " and ps.id = $subcategoryid";
        if ($catid) $sql .= " and products.categoryid = $catid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) {
            if (is_array($branchid)) {
                $sql .= " and b.id in ('" . implode("','", $branchid) . "')";
            } else {
                $sql .= " and b.id = $branchid";
            }
        }

        $sql .= " group by products.id,salesMonth order by sales.doc";
//        debug($sql);
        return fetchRows($sql);
    }

    function clientMonthWiseSales($months, $clientid = "", $userid = "", $productid = "", $modelid = "", $deptid = "", $catid = "", $productcategoryid = "", $subcategoryid = "", $locationid = "", $branchid = "")
    {
        $sql = "select clients.id                      as clientid,
                       clients.name                    as clientname,
                       date_format(sales.doc, '%b %Y') as salesMonth,
                       sum(sd.quantity)                as qty,
                       sum((sd.price - sd.discount - IFNULL(credit_note.discount, 0)) * sd.quantity *
                           sales.currency_amount)      as amount,
                       sum(round((sd.price - sd.discount - IFNULL(credit_note.discount, 0)) * sd.quantity * sales.currency_amount * (1 + sd.vat_rate / 100),
                                 2))                   as incamount
                from sales
                         inner join clients on clients.id = sales.clientid
                         inner join locations l on sales.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join salesdetails sd on sd.salesid = sales.id
                         left join stocks on stocks.id = sd.stockid
                         inner join products on stocks.productid = products.id or products.id = sd.productid
                         left join
                     (
                         select srd.sdi, sum(srd.rate) as discount
                         from sales_return_details srd
                                  inner join sales_returns sr on sr.id = srd.srid
                         where sr.approvedby > 0
                           and sr.type = 'price'
                         group by srd.sdi
                     ) as credit_note on sd.id = credit_note.sdi
                where 1 = 1 and sales.iscreditapproved = 1";

        if (!empty($months) && is_array($months)) {
            $sql .= " and date_format(sales.doc, '%m-%Y') in ('" . implode("','", $months) . "')";
        } else {
            $sql .= " and date_format(sales.doc, '%m-%Y') = '" . date('m-Y') . "'";
        }
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($userid) $sql .= " and sales.createdby = $userid";
        if ($productid) $sql .= " and products.id = $productid";
        if ($modelid) $sql .= " and products.modelid = $modelid";
        if ($deptid) $sql .= " and products.departid = $deptid";
        if ($productcategoryid) $sql .= " and products.productcategoryid = $productcategoryid";
        if ($subcategoryid) $sql .= " and products.subcategoryid = $subcategoryid";
        if ($catid) $sql .= " and products.categoryid = $catid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";

        $sql .= " group by clients.id,salesMonth order by sales.doc";
//        debug($sql);
        return fetchRows($sql);
    }


    function openingLedgerBalance($clientid, $currencyid, $openingdate, $tra_only = false)
    {
        $sales_filter = $payment_filter = '';
        if ($tra_only) {
            $sales_filter = " and sales.receipt_method != 'sr'";
            $payment_filter = " and sp.receipt_type = '" . SalesPayments::RECEIPT_TYPE_TRA . "'";
        }

        $sql = "select clientid,
                       clientname,
                       currencyid,
                       currencyname,
                       sum(if(side = 'debit', amount, 0))                                       as debit,
                       sum(if(side = 'credit', amount, 0))                                      as credit,
                       sum(if(side = 'debit', amount, 0)) - sum(if(side = 'credit', amount, 0)) as balance
                from (
                         select 'debit'           as side,
                                cu.id             as currencyid,
                                cu.name           as currencyname,
                                sales.clientid    as clientid,
                                c.name            as clientname,
                                sales.full_amount as amount
                         from sales
                                  inner join currencies_rates cr on sales.currency_rateid = cr.id
                                  inner join currencies cu on cu.id = cr.currencyid
                                  inner join clients c on sales.clientid = c.id
                         where sales.iscreditapproved = 1
                           and c.id = $clientid
                           and cu.id = $currencyid
                           and date_format(sales.doc, '%Y-%m-%d') <= '$openingdate' $sales_filter
                         union all
                         select 'debit'                as side,
                                cu.id                  as currencyid,
                                cu.name                as currencyname,
                                coo.clientid           as clientid,
                                c.name                 as clientname,
                                coo.outstanding_amount as amount
                         from client_opening_outstanding coo
                                  inner join currencies cu on cu.id = coo.currencyid
                                  inner join clients c on coo.clientid = c.id
                         where c.id = $clientid
                           and cu.id = $currencyid
                           and date_format(coo.doc, '%Y-%m-%d') <= '$openingdate'
                         union all
                         select 'credit'      as side,
                                ap.currencyid as currencyid,
                                cu.name       as currencyname,
                                ap.clientid   as clientid,
                                c.name        as clientname,
                                ap.amount
                         from advance_payments ap
                                  inner join currencies cu on ap.currencyid = cu.id
                                  inner join clients c on ap.clientid = c.id
                         where 1 = 1
                           and (ap.srid = 0 or ap.srid is null)
                           and c.id = $clientid
                           and cu.id = $currencyid
                           and date_format(ap.doc, '%Y-%m-%d') <= '$openingdate'
                         union all
                         select 'credit'                                              as side,
                                cu.id                                                 as currencyid,
                                cu.name                                               as currencyname,
                                sp.clientid                                           as clientid,
                                c.name                                                as clientname,
                                (sp.paid_totalmount - ifnull(used_advance.amount, 0)) as amount
                         from salespayments as sp
                                  inner join currencies cu on cu.id = sp.currencyid
                                  inner join clients c on sp.clientid = c.id
                                  left join
                              (
                                  select spua.spid, sum(spua.amount) amount
                                  from sales_payment_used_advances spua
                                  group by spua.spid
                              ) as used_advance on used_advance.spid = sp.id
                         where 1 = 1
                           and c.id = $clientid
                           and cu.id = $currencyid
                           and date_format(sp.doc, '%Y-%m-%d') <= '$openingdate' $payment_filter
                         having amount > 0
                         union all
                         select 'credit'           as side,
                                cu.id              as currencyid,
                                cu.name            as currencyname,
                                c.id               as clientid,
                                c.name             as clientname,
                                sr.total_incamount as amount
                         from sales_returns sr
                                  inner join currencies cu on cu.id = sr.currencyid
                                  inner join sales on sr.salesid = sales.id
                                  inner join clients c on sales.clientid = c.id
                         where sr.approvedby > 0
                           and sr.status = 'active'
                           and c.id = $clientid
                           and cu.id = $currencyid
                           and date_format(sr.doc, '%Y-%m-%d') <= '$openingdate'
                         union all
                         select 'debit'          as side,
                                cu.id            as currencyid,
                                cu.name          as currencyname,
                                c.id             as clientid,
                                c.name           as clientname,
                                sr.return_amount as amount
                         from sales_returns sr
                                  inner join currencies cu on cu.id = sr.currencyid
                                  inner join sales on sr.salesid = sales.id
                                  inner join clients c on sales.clientid = c.id
                                  inner join paymentmethods pm on sr.pmethod_id = pm.id
                         where sr.approvedby > 0
                           and sr.status = 'active'
                           and c.id = $clientid
                           and cu.id = $currencyid
                           and date_format(sr.doc, '%Y-%m-%d') <= '$openingdate'
                     ) as ledger
                group by clientid,currencyid";
//        debug($sql);
        return fetchRows($sql);
    }

    function ledgerAccount($clientid, $currencyid, $fromdate = "", $todate = "", $tra_only = false)
    {
        $sales_filter = $payment_filter = '';
        if ($tra_only) {
            $sales_filter = " and sales.receipt_method != 'sr'";
            $payment_filter = " and sp.receipt_type = '" . SalesPayments::RECEIPT_TYPE_TRA . "'";
        }

        $sales_fromdate = $fromdate ? " and date_format(sales.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $opening_outstanding_fromdate = $fromdate ? " and date_format(coo.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $advance_fromdate = $fromdate ? " and date_format(ap.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $salespayment_fromdate = $fromdate ? " and date_format(sp.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $sales_returns_fromdate = $fromdate ? " and date_format(sr.doc,'%Y-%m-%d') >= '$fromdate'" : "";

        $sales_todate = $todate ? " and date_format(sales.doc,'%Y-%m-%d') <= '$todate'" : "";
        $opening_outstanding_todate = $todate ? " and date_format(coo.doc,'%Y-%m-%d') <= '$todate'" : "";
        $advance_todate = $todate ? " and date_format(ap.doc,'%Y-%m-%d') <= '$todate'" : "";
        $salespayment_todate = $todate ? " and date_format(sp.doc,'%Y-%m-%d') <= '$todate'" : "";
        $sales_returns_todate = $todate ? " and date_format(sr.doc,'%Y-%m-%d') <= '$todate'" : "";

        $sql = "select *
                from (
                         select sales.id,
                                sales.receipt_no  as voucherno,
                                'invoice'         as voucher_type,
                                'debit'           as side,
                                cu.id             as currencyid,
                                cu.name           as currencyname,
                                sales.clientid    as clientid,
                                c.name            as clientname,
                                sales.full_amount as amount,
                                sales.doc         as action_date
                         from sales
                                  inner join currencies_rates cr on sales.currency_rateid = cr.id
                                  inner join currencies cu on cu.id = cr.currencyid
                                  inner join clients c on sales.clientid = c.id
                         where sales.iscreditapproved = 1
                           and c.id = $clientid
                           and cu.id = $currencyid $sales_fromdate $sales_todate $sales_filter
                         union all
                         select coo.id,
                                coo.invoiceno          as voucherno,
                                'opening invoice'      as voucher_type,
                                'debit'                as side,
                                cu.id                  as currencyid,
                                cu.name                as currencyname,
                                coo.clientid           as clientid,
                                c.name                 as clientname,
                                coo.outstanding_amount as amount,
                                coo.doc                as action_date
                         from client_opening_outstanding coo
                                  inner join currencies cu on cu.id = coo.currencyid
                                  inner join clients c on coo.clientid = c.id
                         where c.id = $clientid
                           and cu.id = $currencyid $opening_outstanding_fromdate $opening_outstanding_todate
                         union all
                         select ap.id,
                                ap.id             as voucherno,
                                'advance payment' as voucher_type,
                                'credit'          as side,
                                ap.currencyid     as currencyid,
                                cu.name           as currencyname,
                                ap.clientid       as clientid,
                                c.name            as clientname,
                                ap.amount,
                                ap.doc            as action_date
                         from advance_payments ap
                                  inner join currencies cu on ap.currencyid = cu.id
                                  inner join clients c on ap.clientid = c.id
                         where 1 = 1
                           and (ap.srid = 0 or ap.srid is null)
                           and c.id = $clientid
                           and cu.id = $currencyid $advance_fromdate $advance_todate
                         union all
                         select sp.id,
                                sp.id                                                 as voucherno,
                                'receipt'                                             as voucher_type,
                                'credit'                                              as side,
                                cu.id                                                 as currencyid,
                                cu.name                                               as currencyname,
                                sp.clientid                                           as clientid,
                                c.name                                                as clientname,
                                (sp.paid_totalmount - ifnull(used_advance.amount, 0)) as amount,
                                sp.doc                                                as action_date
                         from salespayments as sp
                                  inner join currencies cu on cu.id = sp.currencyid
                                  inner join clients c on sp.clientid = c.id
                                  left join
                              (
                                  select spua.spid, sum(spua.amount) amount
                                  from sales_payment_used_advances spua
                                  group by spua.spid
                              ) as used_advance on used_advance.spid = sp.id
                         where 1 = 1
                           and c.id = $clientid
                           and cu.id = $currencyid $salespayment_fromdate $salespayment_todate $payment_filter
                         having amount > 0
                         union all
                         select sr.id,
                                sr.id              as voucherno,
                                'credit note'      as voucher_type,
                                'credit'           as side,
                                cu.id              as currencyid,
                                cu.name            as currencyname,
                                c.id               as clientid,
                                c.name             as clientname,
                                sr.total_incamount as amount,
                                sr.doc             as action_date
                         from sales_returns sr
                                  inner join currencies cu on cu.id = sr.currencyid
                                  inner join sales on sr.salesid = sales.id
                                  inner join clients c on sales.clientid = c.id
                         where sr.approvedby > 0
                           and sr.status = 'active'
                           and c.id = $clientid
                           and cu.id = $currencyid $sales_returns_fromdate $sales_returns_todate
                         union all
                         select sr.id,
                                sr.id                as voucherno,
                                'returned to client' as voucher_type,
                                'debit'              as side,
                                cu.id                as currencyid,
                                cu.name              as currencyname,
                                c.id                 as clientid,
                                c.name               as clientname,
                                sr.return_amount     as amount,
                                sr.doc               as action_date
                         from sales_returns sr
                                  inner join currencies cu on cu.id = sr.currencyid
                                  inner join sales on sr.salesid = sales.id
                                  inner join clients c on sales.clientid = c.id
                                  inner join paymentmethods pm on sr.pmethod_id = pm.id
                         where sr.approvedby > 0
                           and sr.status = 'active'
                           and c.id = $clientid
                           and cu.id = $currencyid $sales_returns_fromdate $sales_returns_todate
                     ) as ledger
                order by action_date";
//         debug($sql);
        return fetchRows($sql);
    }

    function salesInvoiceList($salesid = "", $salesperson = "", $clientid = "", $tra_receipt_only = false, $incomplete_payment = false, $approval_status = "", $fromdate = "", $todate = "", $payment_type = "", $payment_status = "", $locationid = "", $branchid = "", $invoiceno = '')
    {
        $sql = "select s.id                                                         as salesid,
                       s.receipt_no,
                       s.billid,
                       s.previd,
                       prevsale.receipt_no                                          as prev_invoiceno,
                       fiscsale.id                                                  as fisc_invoiceid,
                       fiscsale.receipt_no                                          as fisc_invoiceno,
                       s.fisc_convertedby,
                       converter.name                                               as converter,
                       s.fisc_convertdate,
                       s.clientid,
                       s.payment_status,
                       s.paymenttype,
                       s.has_installment,
                       s.doc,
                       s.currency_amount,
                       s.grand_amount,
                       s.grand_vatamount,
                       s.full_amount,
                       s.lastpaid_totalamount,
                       s.total_increturn,
                       (s.full_amount - s.lastpaid_totalamount - s.total_increturn) as pending_amount,
                       s.grand_amount * s.currency_amount                           as base_grand_amount,
                       s.grand_vatamount * s.currency_amount                        as base_grand_vatamount,
                       s.full_amount * s.currency_amount                            as base_full_amount,
                       s.receipt_method,
                       s.vat_exempted,
                       s.tally_post,
                       s.transfer_tally,
                       c.name                                                       as clientname,
                       l.name                                                       as locationname,
                       b.name                                                       as branchname,
                       s.iscreditapproved,
                       s.has_combine,
                       currencies.id                                                as currencyid,
                       currencies.name                                              as currencyname,
                       currencies.description                                       as currency_description,
                       currencies.base                                              as base_currency,
                       users.name                                                   as issuedby,
                       e.id                                                         as expenseid
                from sales as s
                         inner join clients as c ON c.id = s.clientid
                         inner join locations as l on l.id = s.locationid
                         inner join branches b on l.branchid = b.id
                         inner join currencies_rates cr on cr.id = s.currency_rateid
                         inner join currencies on cr.currencyid = currencies.id
                         inner join users on users.id = s.createdby
                         left join users as converter on converter.id = s.fisc_convertedby
                         left join expenses e on e.saleid = s.id and e.status = 'active'
                         left join sales prevsale on s.previd = prevsale.id
                         left join sales fiscsale on s.id = fiscsale.previd
                where 1 = 1";

        if ($salesid) $sql .= " and s.id = $salesid";
        if ($invoiceno) $sql .= " and s.receipt_no = '$invoiceno'";
        if ($salesperson) $sql .= " and s.createdby = $salesperson";
        if ($clientid) $sql .= " and c.id = $clientid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($tra_receipt_only) $sql .= " and s.receipt_method != 'sr'";
        if ($incomplete_payment) $sql .= " and s.payment_status != '" . PAYMENT_STATUS_COMPLETE . "'";
        if ($fromdate) $sql .= " and date_format(s.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(s.doc,'%Y-%m-%d') <= '$todate'";
        if ($payment_type) $sql .= " and s.paymenttype = '$payment_type'";
        if ($payment_status) $sql .= " and s.payment_status = '$payment_status'";
        if (strlen($approval_status)) $sql .= " and s.iscreditapproved = $approval_status";
        $sql .= " order by s.doc desc";
        //echo $sql;die();
//         debug($sql);
        return fetchRows($sql);
    }

    function clientInvoiceList($clientid)
    {
        $sql = "select c.*,
                       count(s.id) as total_invoice
                from clients as c
                         left join sales as s on s.clientid = c.id
                where c.status = 'active' and s.iscreditapproved = 1";
        if ($clientid) $sql .= " and c.id = $clientid ";
        $sql .= " group by c.id order by total_invoice desc";
        return fetchRows($sql);
    }

    function getPurchasedProductClient($productid = "", $salespersonid = "", $fromdate = "")
    {
        if (!$fromdate) $fromdate = date('Y-m-d', strtotime('-3 months'));
        $sql = "select s.id as invoiceno,s.receipt_no as receipt_no,date_format(s.doc,'%d %M %Y') as purchase_date,c.name as clientname from sales as s
    inner join salesdetails as sd on (sd.salesid = s.id)
    left join stocks as st on (st.id = sd.stockid)
    inner join products as p on (p.id = st.productid or p.id = sd.productid)
    inner join clients as c on (c.id = s.clientid)
    inner join users as u on (u.id = s.salespersonid)
    where 1=1 ";
        if ($productid) $sql .= " and st.productid = $productid ";
        if ($salespersonid) $sql .= " and s.salespersonid = $salespersonid";
        if ($fromdate) $sql .= " and date_format(s.doc,'%Y-%m-%d') >= '$fromdate'";
        $sql .= " order by s.doc desc ";
        //echo $sql;die();
        return fetchRows($sql);
    }

    function salesAuditReport($fromdate = "", $todate = "", $userid = "", $clientid = "", $productid = "", $modelid = "", $depart = "",
                              $categoryid = "", $locationid = "", $branchid = "", $invoiceno = "", $tra_invoice_only = false, $non_stock_only = false, $creditapproved = true, $order_invoice_by = "")
    {
        $sql = "select s.id                                                                                                           as salesid,
                       s.receipt_no,
                       cu.name                                                                                                        as currencyname,
                       cu.description                                                                                                 as currency_description,
                       cu.base                                                                                                        as base_currency,
                       s.doc,
                       s.grand_amount,
                       s.grand_vatamount,
                       s.full_amount,
                       s.total_discount,
                       s.total_increturn,
                       s.receipt_method,
                       round(s.grand_amount * s.currency_amount, 2)                                                                   as base_grand_amount,
                       round(s.grand_vatamount * s.currency_amount, 2)                                                                as base_grand_vatamount,
                       round(s.full_amount * s.currency_amount, 2)                                                                    as base_full_amount,
                       round(s.total_discount * s.currency_amount, 2)                                                                 as base_total_discount,
                       sd.show_print,
                       sd.print_extra,
                       sd.price,
                       sd.incprice,
                       IFNULL(credit_note.discount, 0)                                                                                as credit_note_discount,
                       sd.discount + IFNULL(credit_note.discount, 0)                                                                  as discount,
                       round(((sd.discount + IFNULL(credit_note.discount, 0)) / sd.price) * 100, 2)                                   as discpercent,
                       sd.quantity,
                       sd.hidden_cost                                                                                                 as unit_cost,
                       sd.vat_rate,
                       round(((sd.price - sd.discount - IFNULL(credit_note.discount, 0) - sd.hidden_cost) / sd.hidden_cost) * 100, 2) as margin_percent,
                       sd.price - sd.discount - IFNULL(credit_note.discount, 0) - sd.hidden_cost                                      as unit_profit,
                       round(sd.hidden_cost * sd.quantity, 2)                                                                         as costamount,
                       round(sd.hidden_cost * sd.quantity * s.currency_amount, 2)                                                     as base_costamount,
                       IF(sd.sinc, round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2), round((sd.price - sd.discount) * sd.quantity, 2)) -
                       round(IFNULL(credit_note.discount, 0) * sd.quantity, 2)                                                        as excamount,
                       IF(sd.sinc, (sd.incprice * sd.quantity) - round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2),
                          round((sd.price - sd.discount) * sd.quantity * sd.vat_rate / 100, 2)) -
                       round(IFNULL(credit_note.discount, 0) * sd.quantity * (sd.vat_rate / 100), 2)                                  as vatamount,
                       IF(sd.sinc, sd.incprice * sd.quantity, round((sd.price - sd.discount) * sd.quantity * (1 + sd.vat_rate / 100), 2)) -
                       round(IFNULL(credit_note.discount, 0) * sd.quantity * (1 + sd.vat_rate / 100), 2)                              as incamount,
                       round(sd.price * s.currency_amount, 2)                                                                         as base_price,
                       round((sd.discount + IFNULL(credit_note.discount, 0)) * s.currency_amount, 2)                                  as base_discount,
                       round(sd.hidden_cost * s.currency_amount, 2)                                                                   as base_unit_cost,
                       if(st.id is null, 1, 0)                                                                                        as sold_non_stock,
                       p.id                                                                                                           as productid,
                       p.name                                                                                                         as productname,
                       p.description,
                       extradesc.description                                                                                          as extra_description,
                       p.non_stock,
                       departments.name                                                                                               as depatName,
                       categories.name                                                                                                as catName,
                       model.name                                                                                                     as brandname,
                       c.id                                                                                                           as clientid,
                       c.name                                                                                                         as clientname,
                       u.name                                                                                                         as salesperson,
                       l.name                                                                                                         as locationname,
                       b.name                                                                                                         as branchname,
                       orders.id                                                                                                      as orderno,
                       order_creator.id                                                                                               as order_createdby,
                       order_creator.name                                                                                             as order_creator
                from sales as s
                         inner join salesdetails as sd on sd.salesid = s.id
                         left join salesdescriptions as extradesc on extradesc.sdi = sd.id
                         left join
                     (
                         select srd.sdi, sum(srd.rate) as discount
                         from sales_return_details srd
                                  inner join sales_returns sr on sr.id = srd.srid
                         where sr.approvedby > 0
                           and sr.type = 'price'
                         group by srd.sdi
                     ) as credit_note on sd.id = credit_note.sdi
                         left join stocks as st on st.id = sd.stockid
                         inner join products as p on p.id = st.productid or p.id = sd.productid
                         inner join categories on categories.id = p.categoryid
                         inner join departments on departments.id = p.departid
                         inner join clients as c on c.id = s.clientid
                         inner join model on p.modelid = model.id
                         inner join currencies_rates cr on cr.id = s.currency_rateid
                         inner join currencies as cu on cu.id = cr.currencyid
                         inner join users as u on u.id = s.createdby
                         inner join locations l on l.id = s.locationid
                         inner join branches b on b.id = l.branchid
                         left join orders on orders.id = s.orderid
                         left join users as order_creator on order_creator.id = orders.createdby
                where 1 = 1";

        if ($creditapproved) $sql .= " and s.iscreditapproved = 1";
        if ($fromdate) $sql .= " and date_format(s.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(s.doc,'%Y-%m-%d') <= '$todate'";
        if ($userid) $sql .= " and s.createdby = $userid";
        if ($order_invoice_by) $sql .= " and (s.createdby = $order_invoice_by or orders.createdby = $order_invoice_by)";
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($productid) $sql .= " and p.id = '$productid'";
        if ($tra_invoice_only) $sql .= " and s.receipt_method != 'sr'";
        if ($non_stock_only) $sql .= " and p.non_stock = 1";
        if ($modelid) $sql .= " and model.id = '$modelid'";
        if ($depart) $sql .= " and departments.id = $depart";
        if ($categoryid) $sql .= " and p.categoryid = $categoryid";
        if ($invoiceno) $sql .= " and s.receipt_no like '%$invoiceno%'";
        $sql .= " order by s.doc desc";
//         debug($sql);
        return fetchRows($sql);
    }

    function getProductListForFiscalize($salesid)
    {
        $sql = "select s.id                                                                                                                           as salesid,
                       sd.id                                                                                                                          sdi,
                       s.receipt_no,
                       sd.price - sd.discount                                                                                                         as price,
                       sd.sinc,
                       sd.incprice,
                       round((sd.price - sd.discount) * s.currency_amount, 2)                                                                         as base_price,
                       if(sd.sinc, sd.incprice * s.currency_amount,
                          round((sd.price - sd.discount) * s.currency_amount * (1 + sd.vat_rate / 100), 2))                                           as base_incprice,
                       IF(sd.sinc, round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2), round((sd.price - sd.discount) * sd.quantity, 2)) as amount,
                       round(IF(sd.sinc, round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2), round((sd.price - sd.discount) * sd.quantity, 2)) *
                             s.currency_amount, 2)                                                                                                    as base_amount,
                       IF(sd.sinc, sd.incprice * sd.quantity, round((sd.price - sd.discount) * sd.quantity * (1 + sd.vat_rate / 100), 2))             as incamount,
                       round(if(sd.sinc, sd.incprice * sd.quantity, round((sd.price - sd.discount) * sd.quantity * (1 + sd.vat_rate / 100), 2)) * s.currency_amount,
                             2)                                                                                                                       as base_incamount,
                       sd.quantity,
                       sd.vat_rate,
                       IF(sd.sinc, (sd.incprice * sd.quantity) - round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2),
                          round((sd.price - sd.discount) * sd.quantity * sd.vat_rate / 100, 2))                                                          as vat_amount,
                       round(IF(sd.sinc, (sd.incprice * sd.quantity) - round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2),
                                round((sd.price - sd.discount) * sd.quantity * sd.vat_rate / 100, 2)) * s.currency_amount,
                             2)                                                                                                                       as base_vat_amount,
                       sd.show_print,
                       sd.print_extra,
                       p.name                                                                                                                         as productname,
                       p.description                                                                                                                  as productdescription,
                       extradesc.description                                                                                                          as extra_description,
                       if(st.id is null, 1, 0)                                                                                                        as sold_non_stock,
                       p.id                                                                                                                           as productid,
                       p.trackserialno,
                       p.productcategoryid,
                       p.taxcode                                                                                                                      as zvfd_taxcode,
                       pc.name                                                                                                                        as product_category_name,
                       departments.name                                                                                                               as departmentname,
                       departments.tally_sales_account,
                       taxcode.vfdipa_code                                                                                                            as taxcode,
                       taxcode.vfd_code,
                       taxcode.efd_code                                                                                                               as efd_code
                from sales as s
                         inner join salesdetails as sd on sd.salesid = s.id
                         left join stocks as st on st.id = sd.stockid
                         inner join products as p on p.id = st.productid or p.id = sd.productid
                         inner join product_categories pc on p.productcategoryid = pc.id
                         inner join categories on categories.id = p.categoryid
                         inner join departments on departments.id = p.departid
                         inner join taxcode on categories.taxcode = taxcode.id
                         left join salesdescriptions extradesc on extradesc.sdi = sd.id
                where s.id = $salesid and sd.show_print = 1";
//        debug($sql);
        return fetchRows($sql);
    }

    function simpleList($fromdate = "", $todate = "", $locationid = "", $branchid = "", $currencyid = "", $with_outstanding = false, $tra_receipt_only = false)
    {
        $sql = "select sales.*,
                       (sales.full_amount - sales.lastpaid_totalamount - sales.total_increturn) as outstanding_amount,
                       currencies.id                                                            as currencyid,
                       currencies.name                                                          as currencyname,
                       currencies.description                                                   as currency_description,
                       e.total_amount                                                           as expense_amount,
                       users.name                                                               as createdby
                from sales
                         inner join users on sales.createdby = users.id
                         inner join currencies_rates cr on sales.currency_rateid = cr.id
                         inner join currencies on cr.currencyid = currencies.id
                         left join expenses e on e.saleid = sales.id
                         inner join locations l on l.id = sales.locationid
                         inner join branches b on b.id = l.branchid
                where 1 = 1 and sales.iscreditapproved = 1";
        if ($fromdate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') <= '$todate'";
        if ($currencyid) $sql .= " and currencies.id = $currencyid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($with_outstanding) $sql .= " and sales.payment_status != '" . PAYMENT_STATUS_COMPLETE . "'";
        if ($tra_receipt_only) $sql .= " and sales.receipt_method != 'sr'";
        $sql .= " order by sales.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    static function efdFiscalize($salesid, $override = false, $duplicate = false)
    {

        //check if invoice not fiscalized
        $salesDets = Sales::$saleClass->salesWithFiscalization($salesid);
        if ($salesDets['isfiscalized'] == 1 && !$override) {
            return [
                'message' => 'Invoice already fiscalized'
            ];
        }

        $invoice = [
            'invoice_no' => $salesDets['receipt_no'],
            'name' => $salesDets['clientid'] == 1 ? 'CASH' : $salesDets['clientname'],
            'vrn' => $salesDets['clientvrn'] ?: '',
            'tin' => $salesDets['clientinno'] ?: '999999999',
            'total' => $salesDets['base_full_amount'],
        ];

        $details = Sales::$saleClass->getProductListForFiscalize($salesid);
//        debug($details);
        foreach ($details as $key => $item) {
            $invoice['details'][$key]['service'] = removeSpecialCharacters($item['productname']);
            $invoice['details'][$key]['qty'] = $item['quantity'];
//            $invoice['details'][$key]['charge'] = $item['base_amount'] + $item['base_vat_amount'];
//            $invoice['details'][$key]['charge'] = addTAX($item['price'], $item['vat_rate']);
            $invoice['details'][$key]['charge'] = $item['sinc'] ? $item['incprice'] : addTAX($item['price'], $item['vat_rate']);
            $invoice['details'][$key]['efd_code'] = $item['efd_code'];
        }
//        debug($invoice);
        $result = printEFD($invoice);
        Sales::$saleClass->update($salesid, ['isfiscalized' => 1]);
        $fiscData = [
            'salesid' => $salesid,
            'fiscalize_status_message' => $result['message'],
            'fiscalization_type' => SaleFiscalization::TYPE_EFD
        ];
        $fisc = SaleFiscalization::$saleFiscalizeClass->find(['salesid' => $salesid])[0];
        if ($fisc) {
            SaleFiscalization::$saleFiscalizeClass->update($fisc['id'], $fiscData);
        } else {
            SaleFiscalization::$saleFiscalizeClass->insert($fiscData);
        }
        return $result;
    }

    static function fiscalize($invoiceNo, $override = false)
    {
        return CS_VFD_TYPE == VFD_TYPE_ZVFD
            ? self::zvfdFiscalize($invoiceNo, $override)
            : self::vfdFiscalize($invoiceNo, $override);
    }

    static function vfdFiscalize($invoiceNo, $override = false)
    {
        $salesid = $invoiceNo;
        $salesDets = Sales::$saleClass->get($salesid);
        // check if invoice not fiscalized
        if ($salesDets['isfiscalized'] == 1 && !$override) {
            return [
                'message' => 'Invoice already fiscalized'
            ];
        }


        $datesTime = explode(' ', $salesDets['doc']);

        $client = Clients::$clientClass->get($salesDets['clientid']);
        $invoice['invoice']['invoice'][0]['idate'] = fDate($salesDets['doc'], 'Y-m-d');
        $invoice['invoice']['invoice'][0]['itime'] = $datesTime[1];
//        $invoice['invoice']['invoice'][0]['custinvoiceno'] = "pos-" . $salesid;
        $invoice['invoice']['invoice'][0]['custinvoiceno'] = CS_VFD_INVOICE_PREFIX . $salesDets['receipt_no'];

        if (!empty($client['tinno']) && $client['tinno'] != 'NIL' && $client['tinno'] != '999999999') {
            $invoice['invoice']['invoice'][0]['custidtype'] = 1;
            $invoice['invoice']['invoice'][0]['custid'] = $client['tinno'];
        } else {
            $invoice['invoice']['invoice'][0]['custidtype'] = 6;
            $invoice['invoice']['invoice'][0]['custid'] = "";
        }

        $invoice['invoice']['invoice'][0]['custname'] = removeSpecialCharacters($client['name']);
        $invoice['invoice']['invoice'][0]['mobilenum'] = ($client['mobile'] == 'NIL') ? "" : $client['mobile'];
        $creator = Users::$userClass->get($salesDets['createdby']);
        $invoice['invoice']['invoice'][0]['username'] = $creator['name'];

        //checking the branch if available
        $branch = Locations::$locationClass->getBranch($salesDets['locationid']);
        $invoice['invoice']['invoice'][0]['branch'] = $branch['name'];

        //checking the department if available
        $department = Departments::$deptClass->get($creator['deptid']);
        $invoice['invoice']['invoice'][0]['department'] = $department['name'];

        $invoice['invoice']['invoice'][0]['devicenumber'] = CS_DEVICE_NO;
        $invoice['invoice']['invoice'][0]['fcode'] = CS_FCODE;
        $invoice['invoice']['invoice'][0]['fcodetoken'] = CS_FCODETOKEN;
        if ($salesDets['paymenttype'] == PAYMENT_TYPE_CREDIT) {
            $paytype = '5';
        } elseif ($salesDets['paymenttype'] == PAYMENT_TYPE_CASH) {
            $payment = SalesPayments::$salePaymentClass->getSalesPayment($salesid)[0];
            if ($payment['method'] == PaymentMethods::CASH) {
                $paytype = '1';
            } elseif ($payment['method'] == PaymentMethods::CREDIT_CARD) {
                $paytype = '4';
            } else {
                return ['status' => 'error', 'message' => 'Paytype not found'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Paytype not found'];
        }
        $invoice['invoice']['invoice'][0]['paytype'] = $paytype;

        //looping the details
        $invoiceDetails = Sales::$saleClass->getProductListForFiscalize($salesid);
        //grouping by vfd_code
//        debug($invoiceDetails);
        $grouped = [];
        foreach ($invoiceDetails as $key => $details) {
            if ($salesDets['vat_exempted']) {
                $grouped['E']['description'] = "Sales Summary E";
                $grouped['E']['qty'] = 1;
                $grouped['E']['taxcode'] = '5';
                $grouped['E']['amt'] += ($details['base_amount'] + $details['base_vat_amount']);
            } else {
                $grouped[$details['vfd_code']]['description'] = "Sales Summary {$details['vfd_code']}";
                $grouped[$details['vfd_code']]['qty'] = 1;
                $grouped[$details['vfd_code']]['taxcode'] = $details['taxcode'];
                $grouped[$details['vfd_code']]['amt'] += ($details['base_amount'] + $details['base_vat_amount']);
            }

        }
        $invoice['invoice']['invoice'][0]['invoiceDetails'] = array_values($grouped);

        $encoded = json_encode($invoice['invoice'], JSON_PRETTY_PRINT);
//        debug($encoded);
        $vfdUrl = CS_VFD_MODE == VFD_MODE_TESTING ? CS_VFD_TEST_URL : CS_VFD_LIVE_URL;
        try {
            $VFD = sendVFDRequest($vfdUrl, ['Content-Type: application/json'], $encoded, false);
//            debug($VFD);
            if ($VFD['status'] == 'success') {
                //update the invoice
                Sales::$saleClass->update($salesid, ['isfiscalized' => 1]);
                $fiscData = [
                    'salesid' => $salesid,
                    'receipt_num' => $VFD['vfdinvoicenum'],
                    'receipt_date' => "{$VFD['idate']} {$VFD['itime']}",
                    'receiptby' => $_SESSION['member']['id'],
                    'rctvcode' => $VFD['rctvcode'],
                    'znumber' => $VFD['znumber'],
                    'fiscalize_status_message' => $VFD['message'],
//                    'vfd_qrcode' => $VFD['qrcode_uri'], not in use & takes too much space
                    'receipt_v_num' => $VFD['rctvnum'],
                    'fiscalization_type' => SaleFiscalization::TYPE_VFD
                ];

                SaleFiscalization::save($salesid, $fiscData);

                saveResponseVFDJSON($salesDets['receipt_no'], $VFD);
            } else {
                throw new Exception($VFD['message']);
            }
        } catch (Exception $e) {
            $VFD['message'] = $e->getMessage();
            SaleFiscalization::save($salesid, [
                'salesid' => $salesid,
                'fiscalize_status_message' => $e->getMessage(),
                'fiscalization_type' => SaleFiscalization::TYPE_VFD
            ]);
            Sales::$saleClass->update($salesid, ['isfiscalized' => 1]);
        }
        return [
            'status' => $VFD['status'],
            'message' => $VFD['message']
        ];
    }

    static function zvfdFiscalize($invoiceNo, $override = false)
    {
        $salesid = $invoiceNo;
        $salesDets = Sales::$saleClass->get($salesid);
        // check if invoice not fiscalized
        if ($salesDets['isfiscalized'] == 1 && !$override) {
            return [
                'message' => 'Invoice already fiscalized'
            ];
        }


        $datesTime = explode(' ', $salesDets['doc']);
        $client = Clients::$clientClass->get($salesDets['clientid']);
        $invoice['invoice']['invoice'][0]['idate'] = fDate($salesDets['doc'], 'Y-m-d');
        $invoice['invoice']['invoice'][0]['itime'] = $datesTime[1];

        //customer
        if (!empty($client['tinno']) && $client['tinno'] != 'NIL') {
            $invoice['invoice']['invoice'][0]['custidtype'] = 1;
            $invoice['invoice']['invoice'][0]['custid'] = $client['tinno'];
        } else {
            $invoice['invoice']['invoice'][0]['custidtype'] = 6;
            $invoice['invoice']['invoice'][0]['custid'] = "NULL";
        }
        $invoice['invoice']['invoice'][0]['custname'] = $client['name'];
        $invoice['invoice']['invoice'][0]['mobilenum'] = empty($client['mobile']) ? "000000000" : $client['mobile'];
        $creator = Users::$userClass->get($salesDets['createdby']);
        $invoice['invoice']['invoice'][0]['username'] = $creator['name'];

        //invoice number
        $invoice['invoice']['invoice'][0]['invoicenumber'] = CS_VFD_INVOICE_PREFIX . $salesDets['receipt_no'];

        //branch
        $location = Locations::$locationClass->get($salesDets['locationid']);
        $branch = Branches::$branchClass->get($location['branchid']);
        $invoice['invoice']['invoice'][0]['branch'] = $branch['name'];

        //currency id 1 => TZS, 2 => USD, 3 => EURO
        $invoice['invoice']['invoice'][0]['currency_id'] = "1";

        //department
        $department = Departments::$deptClass->get($creator['deptid']);
        $invoice['invoice']['invoice'][0]['department'] = $department['name'];

        //sales id 1=> normal, 2 => B2B, 3 => Government
        $invoice['invoice']['invoice'][0]['salestype_id'] = "1";
        $invoice['invoice']['invoice'][0]['zrbnumber'] = "0";

        //device number
        $invoice['invoice']['invoice'][0]['device_number'] = "";
        $invoice['invoice']['invoice'][0]['authorization_id'] = CS_ZVFD_INTEGRATION_ID;  //from zvfd bridge
        $invoice['invoice']['invoice'][0]['tax_type'] = $salesDets['zvfd_tax_type'];  //from zvfd bridge

        //looping the details
        $invoiceDetails = Sales::$saleClass->getProductListForFiscalize($salesid);
//debug($invoiceDetails);
        //grouping by category
        $grouped = [];
        foreach ($invoiceDetails as $key => $detail) {
            $grouped[$key]['description'] = removeSpecialCharacters($detail['productname']);
            $grouped[$key]['qty'] = $detail['quantity'];
            $grouped[$key]['taxcode'] = $detail['zvfd_taxcode'];
            $grouped[$key]['discount'] = "0";
            $grouped[$key]['rate'] = $detail['base_incprice'];
            $grouped[$key]['amt'] = "" . ($detail['base_amount'] + $detail['base_vat_amount']);
        }

//        debug($grouped);

        $invoice['invoice']['invoice'][0]['invoiceDetails'] = array_values($grouped);
        $encoded = json_encode($invoice['invoice'], JSON_PRETTY_PRINT);
//        debug($encoded);
        $zvfdUrl = CS_VFD_MODE == VFD_MODE_TESTING ? CS_VFD_TEST_URL : CS_VFD_LIVE_URL;
        try {
            $ZVFD = sendVFDRequest($zvfdUrl, ['Content-Type: application/json'], $encoded);
//            debug($ZVFD);
            if ($ZVFD['status'] == 'success') {
                //update the invoice
                Sales::$saleClass->update($salesid, ['isfiscalized' => 1]);
                $fiscData = [
                    'salesid' => $salesid,
                    'receipt_num' => $ZVFD['zvfdinvoicenum'],
                    'bridge_invoice_num' => $ZVFD['invoicenumber'],
                    'receipt_date' => "{$ZVFD['idate']} {$ZVFD['itime']}",
                    'receipt_url' => $ZVFD['receiptweburlpath'],
                    'receiptby' => $_SESSION['member']['id'],
                    'znumber' => $ZVFD['znumber'],
                    'vrnno' => $ZVFD['vrnNumber'],
                    'street' => $ZVFD['street'],
                    'tinnumber' => $ZVFD['tinNumber'],
                    'companyname' => $ZVFD['companyName'],
                    'fiscalize_status_message' => $ZVFD['message'],
//                    'vfd_qrcode' => $ZVFD['qrcode_uri'], not in use & takes too much space
                    'rctvcode' => $ZVFD['rctvnum'],
                    'receipt_v_num' => $ZVFD['receiptverificationlink'],
                    'fiscalization_type' => SaleFiscalization::TYPE_ZVFD
                ];

                SaleFiscalization::save($salesid, $fiscData);

                //save response
                saveResponseVFDJSON($salesDets['receipt_no'], $ZVFD, true);
                //saveZVFDReceipt($ZVFD['rctvnum'], $ZVFD['receiptweburlpath']);
            } else {
                throw new Exception($ZVFD['message']);
            }
        } catch (Exception $e) {
            $ZVFD['message'] = $e->getMessage();
            SaleFiscalization::save($salesid, [
                'salesid' => $salesid,
                'fiscalize_status_message' => $e->getMessage(),
                'fiscalization_type' => SaleFiscalization::TYPE_ZVFD
            ]);
            Sales::$saleClass->update($salesid, ['isfiscalized' => 1]);
        }
        return [
            'status' => $ZVFD['status'],
            'message' => $ZVFD['message']
        ];
    }

    static function generateInvoiceNo($locationId, $receipt_method)
    {
        $location = Locations::$locationClass->get($locationId);
        $iteration = 1;
        $outer_loop = 1;
        do {
            if ($outer_loop > 10) {
                $branch = Locations::$locationClass->getBranch($locationId);
                logData("Failed invoiceno, many loops, branch {$branch['name']}, location {$location['name']}");
                return '';
            }
            $branchSalesCount = Branches::$branchClass->receiptSalesCount($location['branchid'], $receipt_method == 'sr');
            $newNo = $branchSalesCount['salesCount'] + 1;

            do {
                $receiptNo = str_pad($location['branchid'], 2, 0, STR_PAD_LEFT) . "-" . str_pad($newNo, 4, 0, STR_PAD_LEFT);
                $receiptNo = $receipt_method == 'sr' ? "SR-" . $receiptNo : $receiptNo;
                $newNo++;

                $iteration++;
            } while (Sales::$saleClass->countWhere(['receipt_no' => $receiptNo]) > 0 || SalesCanceled::$saleCanceledClass->countWhere(['invoiceno' => $receiptNo]) > 0);
            $outer_loop++;
        } while (!Sales::$saleClass->insert(['receipt_no' => $receiptNo]));
        return Sales::$saleClass->lastId();
    }

    static function verifyStock($salesid)
    {
        $result = [
            'status' => 'success'
        ];
        try {
            $sale = Sales::$saleClass->get($salesid);
            if ($sale['proformaid']) define('EXCEPT_PROFORMA', $sale['proformaid']);
            $sale['details'] = Salesdetails::$saleDetailsClass->find(['salesid' => $salesid]);
            foreach ($sale['details'] as $i => $detail) {
                if ($detail['productid']) { //non-stock item
                    if (Products::$productClass->find(['id' => $detail['productid'], 'non_stock' => 1])[0]) continue;
                }
                $stock = Stocks::$stockClass->get($detail['stockid']);
                $product = Products::$productClass->get($stock['productid']);
//                debug($product);

                if ($product['trackserialno']) {
                    $serialnos = SerialNos::$serialNoClass->find(['sdi' => $detail['id']]);
                    if (count($serialnos) != $detail['quantity']) throw new Exception("System found ({$product['name']}) do not have serial numbers chosen!");
                }

                $current_stock = Stocks::$stockClass->calcStock(
                    $sale['locationid'], $detail['stockid'], "",
                    "", "", "", "",
                    "", "", "", "", "", "",
                    "", "", "", "", false, true,
                    '', '', true, true
                );
                $current_stock = array_values($current_stock)[0];
//                debug($current_stock);
                if ($current_stock['total'] < $detail['quantity']) throw new Exception("System found ({$product['name']}) does not have enough stock");

                $chosenBatches = SalesBatches::$salesBatchesClass->find(['sdi' => $detail['id']]);
                foreach ($chosenBatches as $bi => $batch) {

                    $current_batch_stock = array_filter($current_stock['batches'], function ($b) use ($batch) {
                        return $b['batchId'] == $batch['batch_id'];
                    });
//                    debug($batch);
                    $current_batch_stock = array_values($current_batch_stock)[0];
                    if (!$current_batch_stock || $current_batch_stock['total'] < $batch['qty']) {
                        if ($product['track_expire_date'] == 1) {
                            $batchno = Batches::$batchesClass->get($batch['batch_id'])['batch_no'];
                            throw new Exception("System found batch no {$batchno} does not have enough qty");
                        } else {
                            //todo advance to next existing batches and distribute if has stock??
                            throw new Exception("System found there is a problem with the sale, try editing the sale");
                        }
                    }
                }
            }
//            mysqli_commit($db_connection);
            return $result;
        } catch (Exception $e) {
//            mysqli_rollback($db_connection);
            $result['status'] = 'error';
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    static function tallyPost($salesid)
    {
//        debug('tally');
        $sale = Sales::$saleClass->salesList($salesid, '', '', '', '', '', '', '',
            '', '', '', true, '', '')[0];
        if (!$sale['iscreditapproved']) return ['status' => 'error', 'msg' => 'Invoice not approved'];
        if ($sale['receipt_method'] == 'sr') return ['status' => 'error', 'msg' => 'SR invoice is not for Tally transfer'];
        if (!$sale['transfer_tally']) return ['status' => 'error', 'msg' => 'Not for Tally transfer'];
        if ($sale['tally_post']) return ['status' => 'error', 'msg' => 'Already posted to tally'];
        if (!$sale['tally_trxno']) {
            $tally_trxno = unique_token(60) . "-sale-{$sale['salesid']}";
            Sales::$saleClass->update($sale['salesid'], ['tally_trxno' => $tally_trxno]);
        } else {
            $tally_trxno = $sale['tally_trxno'];
        }
        $tally_invoiceno = $sale['branch_invoice_prefix'] . $sale['receipt_no'];
        Sales::$saleClass->update($sale['salesid'], ['tally_invoiceno' => $tally_invoiceno]);

//debug($sale);
        $post_data = [];
        $post_data['salesid'] = $sale['salesid'];
        $post_data['trxno'] = $tally_trxno;
        $post_data['paymenttype'] = $sale['paymenttype'];
        $post_data['invoiceno'] = $tally_invoiceno;
        $post_data['date'] = fDate($sale['doc'], 'Ymd');
        $post_data['narration'] = $sale['description'];
        $post_data['clientname'] = htmlspecialchars($sale['clientledgername'] ?: $sale['clientname']);
        $post_data['cost_center'] = htmlspecialchars($sale['cost_center']);
        $post_data['cash_ledger'] = htmlspecialchars($sale['tally_cash_ledger']);
        $post_data['full_amount'] = $sale['base_full_amount'];
        $post_data['vatamount'] = $sale['base_grand_vatamount'];
        $post_data['invoice_currency'] = $sale['currencyname'];
        $post_data['exchange_rate'] = $sale['current_rate'];
        $post_data['base_currency'] = Currencies::$currencyClass->find(['base' => 'yes'])[0]['name'];
        $post_data['is_base_currency'] = $sale['base_currency'] == 'yes';

        if ($sale['paymenttype'] == PAYMENT_TYPE_CREDIT) {
            $post_data['dr_ledgername'] = $post_data['clientname'];
        } else {
            $payment = SalesPayments::$salePaymentClass->getSalesPayment($sale['salesid'])[0];
            $post_data['paymentmethod'] = $payment['method'];
            if ($payment['method'] == PaymentMethods::CASH) {
                $post_data['dr_ledgername'] = $post_data['cash_ledger'];
            } elseif ($payment['method'] == PaymentMethods::CREDIT_CARD) {
                $post_data['dr_ledgername'] = htmlspecialchars($payment['electronic_account_ledgername']);
                $post_data['narration'] .= "\n" . $payment['electronic_account'] . " " . $payment['credit_cardno'];
            }
        }

        //clean narration
        $post_data['narration'] = htmlspecialchars($post_data['narration']);

        $details = Sales::$saleClass->getProductListForFiscalize($sale['salesid']);
        foreach ($details as $detail) {
            $post_data['sales_accounts'][$detail['tally_sales_account']] += $detail['base_amount'];
        }
//        debug($post_data,1);

        $result = createInvoiceVoucher($post_data);
//        TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $tally_trxno]);
//        debug($result);
        global $db_connection;
        mysqli_begin_transaction($db_connection);
        if ($result['status'] == 'success') {
            //save transfer
            //dr_ledgername
            $partno = 1;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => $post_data['dr_ledgername'],
                'dr_cr' => 'dr',
                'amount' => $post_data['full_amount'],
                'reference' => $post_data['invoiceno'],
                'voucher_type' => 'Sales',
                'sourceid' => $sale['salesid'],
                'sourcetable' => 'sales',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            //sales account
            foreach ($post_data['sales_accounts'] as $acc => $amount) {
                $partno++;
                TallyTransfers::$tallyTransferclass->insert([
                    'date' => $post_data['date'],
                    'partno' => $partno,
                    'ledgername' => $acc,
                    'dr_cr' => 'cr',
                    'amount' => $amount,
                    'reference' => $post_data['invoiceno'],
                    'voucher_type' => 'Sales',
                    'sourceid' => $sale['salesid'],
                    'sourcetable' => 'sales',
                    'trxno' => $tally_trxno,
                    'createdby' => $_SESSION['member']['id'],
                ]);
            }

            //vat
            $partno++;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => 'Vat',
                'dr_cr' => 'cr',
                'amount' => $post_data['vatamount'],
                'reference' => $post_data['invoiceno'],
                'voucher_type' => 'Sales',
                'sourceid' => $sale['salesid'],
                'sourcetable' => 'sales',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            Sales::$saleClass->update($sale['salesid'], ['tally_post' => 1, 'tally_message' => $result['msg']]);
        } else {
            Sales::$saleClass->update($sale['salesid'], ['tally_message' => $result['msg']]);
        }
        mysqli_commit($db_connection);

        return $result;

    }
}


