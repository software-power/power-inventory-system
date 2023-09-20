<?php /**
 * model proformas
 */
class Proformas extends model
{
    var $table = 'proformas';
    static $proformaClass = null;

    function __construct()
    {
        self::$proformaClass = $this;
    }

    function proformaList($proformaid = "", $userid = "", $proforma_status = "", $clientid = "", $stock_holding = "", $fromdate = "", $todate = "", $locationid = "", $branchid = "", $currencyid = "")
    {
        $sql = "select pr.*,
                       date_add(pr.doc, INTERVAL pr.validity_days day)                                                  as valid_until,
                       date_add(pr.doc, INTERVAL pr.holding_days day)                                                   as hold_until,
                       current_timestamp() <= date_add(pr.doc, INTERVAL pr.holding_days day) and
                       current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_closedby = 0 as isholding,
                       round(pr.proforma_value * cr.rate_amount)                                                        as base_proforma_value,
                       currencies.name                                                                                  as currencyname,
                       currencies.description                                                                           as currency_description,
                       currencies.base                                                                                  as base_currency,
                       cr.rate_amount                                                                                   as exchange_rate,
                       clients.name                                                                                     as clientname,
                       clients.mobile,
                       users.name                                                                                       as issuedby,
                       closer.name                                                                                      as closedby,
                       l.name                                                                                           as locationname,
                       b.name                                                                                           as branchname,
                       ifnull(directsale.id, ordersale.id)                                                              as salesid,
                       ifnull(directsale.receipt_no, ordersale.receipt_no)                                              as invoiceno,
                       orders.id                                                                                        as orderid,
                       case
                           when current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'pending'
                               then 'pending'
                           when (directsale.id is not null or ordersale.id is not null) and pr.sales_status = 'closed'
                               then 'closed'
                           when current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'closed' and orders.id is not null
                               then 'under order'
                           when current_timestamp() > date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'pending'
                               then 'invalid'
                           end                                                                                          as proforma_status
                from proformas as pr
                         inner join currencies on currencies.id = pr.currencyid
                         inner join currencies_rates cr on currencies.id = cr.currencyid
                         inner join clients on clients.id = pr.clientid
                         inner join locations l on pr.locid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join users on users.id = pr.createdby
                         left join users as closer on pr.sales_closedby = closer.id
                         left join sales directsale on directsale.proformaid = pr.id
                         left join orders on orders.proformaid = pr.id
                         left join sales ordersale on ordersale.orderid = orders.id
                where 1 = 1";

        if ($proformaid) $sql .= " and pr.id = $proformaid";
        if ($clientid) $sql .= " and pr.clientid = $clientid";
        if ($userid) $sql .= " and pr.createdby = $userid";
        if ($currencyid) $sql .= " and pr.currencyid = $currencyid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($fromdate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') <= '$todate'";

        //having
        $sql .= " having 1=1";

        if ($stock_holding == 'yes') $sql .= " and isholding = true";
        if ($stock_holding == 'no') $sql .= " and isholding = false";
        if ($proforma_status) $sql .= " and proforma_status = '$proforma_status'";
        $sql .= " order by pr.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function withDetails($proformaid = "", $userid = "", $sales_status = "", $clientid = "", $fromdate = "", $todate = "", $locationid = "", $branchid = "", $currencyid = "", $group = true)
    {
        $sql = "select pr.*,
                       round(pr.proforma_value * cr.rate_amount)                                                          as base_proforma_value,
                       currencies.name                                                                                    as currencyname,
                       currencies.description                                                                             as currency_description,
                       currencies.base                                                                                    as base_currency,
                       cr.id                                                                                              as currency_rateid,
                       cr.rate_amount                                                                                     as exchange_rate,
                       clients.name                                                                                       as clientname,
                       clients.tinno,
                       clients.vatno,
                       clients.mobile,
                       clients.email,
                       clients.address,
                       clients.tel,
                       users.name                                                                                         as issuedby,
                       closer.name                                                                                        as closedby,
                       l.name                                                                                             as locationname,
                       b.name                                                                                             as branchname,
                       pd.id                                                                                              as detailId,
                       pd.productid,
                       if(p.name is null, 'external', 'internal')                                                         as source,
                       ifnull(p.name, pd.productname)                                                                     as productname,
                       p.description                                                                                      as productdescription,
                       p.non_stock,
                       p.trackserialno,
                       p.validate_serialno,
                       p.track_expire_date,
                       p.prescription_required,
                       p.image_path,
                       pd.qty,
                       pd.price,
                       pd.sinc,
                       pd.vat_rate,
                       pd.taxcategory,
                       pd.print_extra,
                       extradesc.description                                                                          as extra_description,
                       if(pd.sinc, pd.incprice, round(pd.price * (1 + pd.vat_rate / 100), 2))                         as incprice,
                       if(pd.sinc, round(pd.incprice * pd.qty / (1 + pd.vat_rate / 100), 2), round(pd.price * pd.qty, 2)) as excamount,
                       if(pd.sinc, (pd.incprice * pd.qty) - round(pd.incprice * pd.qty / (1 + pd.vat_rate / 100), 2),
                          round(pd.price * pd.qty * (pd.vat_rate / 100), 2))                                              as vatamount,
                       if(pd.sinc, pd.incprice * pd.qty, round(pd.price * pd.qty * (1 + pd.vat_rate / 100), 2))           as incamount
                from proformas as pr
                         inner join currencies on currencies.id = pr.currencyid
                         inner join currencies_rates cr on currencies.id = cr.currencyid
                         inner join clients on clients.id = pr.clientid
                         inner join locations l on pr.locid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join users on users.id = pr.createdby
                         left join users as closer on pr.sales_closedby = closer.id
                         inner join proformadetails pd on pr.id = pd.proformaid
                         left join products p on p.id = pd.productid
                         left join salesdescriptions extradesc on pd.id = extradesc.pdi
                where 1 = 1";

        if ($proformaid) $sql .= " and pr.id = $proformaid";
        if ($clientid) $sql .= " and pr.clientid = $clientid";
        if ($userid) $sql .= " and pr.createdby = $userid";
        if ($currencyid) $sql .= " and pr.currencyid = $currencyid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($sales_status) $sql .= " and pr.sales_status = '$sales_status'";
        if ($fromdate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by pr.doc desc";
        //echo $sql; die();
        $results = fetchRows($sql);
        if (!$group) {
            return $results;
        } else {
            $newArray = [];
            foreach ($results as $i) {
                $newArray[$i['id']]['proformaid'] = $i['id'];
                $newArray[$i['id']]['currencyid'] = $i['currencyid'];
                $newArray[$i['id']]['currencyname'] = $i['currencyname'];
                $newArray[$i['id']]['currency_description'] = $i['currency_description'];
                $newArray[$i['id']]['base_currency'] = $i['base_currency'];
                $newArray[$i['id']]['currency_rateid'] = $i['currency_rateid'];
                $newArray[$i['id']]['exchange_rate'] = $i['exchange_rate'];
                $newArray[$i['id']]['proforma_value'] = $i['proforma_value'];
                $newArray[$i['id']]['base_proforma_value'] = $i['base_proforma_value'];
                $newArray[$i['id']]['clientid'] = $i['clientid'];
                $newArray[$i['id']]['clientname'] = $i['clientname'];
                $newArray[$i['id']]['tinno'] = $i['tinno'];
                $newArray[$i['id']]['vatno'] = $i['vatno'];
                $newArray[$i['id']]['mobile'] = $i['mobile'];
                $newArray[$i['id']]['tel'] = $i['tel'];
                $newArray[$i['id']]['email'] = $i['email'];
                $newArray[$i['id']]['address'] = $i['address'];
                $newArray[$i['id']]['validity_days'] = $i['validity_days'];
                $newArray[$i['id']]['paymentterms'] = $i['paymentterms'];
                $newArray[$i['id']]['payment_days'] = $i['payment_days'] ? $i['payment_days'] : '';
                $newArray[$i['id']]['description'] = $i['description'];
                $newArray[$i['id']]['createdby'] = $i['createdby'];
                $newArray[$i['id']]['issuedby'] = $i['issuedby'];
                $newArray[$i['id']]['doc'] = $i['doc'];
                $newArray[$i['id']]['sales_status'] = $i['sales_status'];
                $newArray[$i['id']]['closedby'] = $i['closedby'];
                $newArray[$i['id']]['locid'] = $i['locid'];
                $newArray[$i['id']]['locationname'] = $i['locationname'];
                $newArray[$i['id']]['branchname'] = $i['branchname'];
                $newArray[$i['id']]['terms_conditions'] = $i['terms_conditions'];
                $newArray[$i['id']]['details'][$i['detailId']]['id'] = $i['detailId'];
                $newArray[$i['id']]['details'][$i['detailId']]['productid'] = $i['productid'];
                $newArray[$i['id']]['details'][$i['detailId']]['source'] = $i['source'];
                $newArray[$i['id']]['details'][$i['detailId']]['productname'] = $i['productname'];
                $newArray[$i['id']]['details'][$i['detailId']]['productdescription'] = $i['print_extra'] ? $i['extra_description'] : $i['productdescription'];
                $newArray[$i['id']]['details'][$i['detailId']]['non_stock'] = $i['non_stock'];
                $newArray[$i['id']]['details'][$i['detailId']]['trackserialno'] = $i['trackserialno'];
                $newArray[$i['id']]['details'][$i['detailId']]['validate_serialno'] = $i['validate_serialno'];
                $newArray[$i['id']]['details'][$i['detailId']]['track_expire_date'] = $i['track_expire_date'];
                $newArray[$i['id']]['details'][$i['detailId']]['prescription_required'] = $i['prescription_required'];
                $newArray[$i['id']]['details'][$i['detailId']]['image_path'] = $i['image_path'];
                $newArray[$i['id']]['details'][$i['detailId']]['qty'] = $i['qty'];
                $newArray[$i['id']]['details'][$i['detailId']]['price'] = $i['price'];
                $newArray[$i['id']]['details'][$i['detailId']]['sinc'] = $i['sinc'];
                $newArray[$i['id']]['details'][$i['detailId']]['incprice'] = $i['incprice'];
                $newArray[$i['id']]['details'][$i['detailId']]['vat_rate'] = $i['vat_rate'];
                $newArray[$i['id']]['details'][$i['detailId']]['print_extra'] = $i['print_extra'];
                $newArray[$i['id']]['details'][$i['detailId']]['extra_description'] = $i['extra_description'];
                $newArray[$i['id']]['details'][$i['detailId']]['taxcategory'] = $i['taxcategory'];
                $newArray[$i['id']]['details'][$i['detailId']]['excamount'] = $i['excamount'];
                $newArray[$i['id']]['details'][$i['detailId']]['vatamount'] = $i['vatamount'];
                $newArray[$i['id']]['details'][$i['detailId']]['incamount'] = $i['incamount'];
            }

            return array_values($newArray);
        }
    }

    function clientStockHolder($clientid = "", $productid = "", $locationid = "", $branchid = "", $detailed = false)
    {
        $sql = "select pr.locid,
                       pr.clientid,
                       clients.name                                   as clientname,
                       date_add(pr.doc, INTERVAL pr.holding_days day) as hold_until,
                       l.name                                         as locationname,
                       b.name                                         as branchname,
                       pr.id                                          as proformaid,
                       sales.id                                       as salesid,
                       sales.receipt_no                               as invoiceno,
                       orders.id                                      as orderid,
                       pd.productid,
                       p.name                                         as productname,
                       sum(pd.qty)                                    as qty
                from proformas as pr
                         inner join clients on pr.clientid = clients.id
                         inner join locations l on pr.locid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join proformadetails pd on pr.id = pd.proformaid
                         inner join products p on p.id = pd.productid
                         left join sales on sales.proformaid = pr.id and sales.iscreditapproved = 0 and sales.op_reuse = 0
                         left join orders on orders.proformaid = pr.id and orders.sales_status = 'pending' and orders.status = 'active' and orders.op_reuse = 0
                where current_timestamp() <= date_add(pr.doc, INTERVAL pr.holding_days day)
                  and current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day)
                  and IF(pr.sales_status = 'pending' or orders.id is not null or sales.id is not null, 1 = 1, 1 = 2)";
        if ($productid) $sql .= " and p.id = $productid";
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";

        if ($detailed) {
            $sql .= " group by l.id, clients.id, pr.id, p.id";
        } else {
            $sql .= " group by l.id,clients.id, p.id";
        }
        return fetchRows($sql);
    }

    static function heldStock($stockid = "", $productid = "", $exceptid = "", $proformaid = "", $salesid = "", $orderid = "", $locationid = "", $branchid = "")
    {
        $sql = "select stocks.id   as stockid,
                       p.name as productname,
                       sum(pd.qty) as qty
                from proformas as pr
                         inner join proformadetails pd on pd.proformaid = pr.id
                         inner join stocks on pd.productid = stocks.productid and pr.locid = stocks.locid
                         inner join products as p on p.id = stocks.productid
                         inner join locations l on pr.locid = l.id
                         inner join branches b on l.branchid = b.id
                         left join sales on sales.proformaid = pr.id and sales.iscreditapproved = 0 and sales.op_reuse = 0
                         left join orders on orders.proformaid = pr.id and orders.sales_status = 'pending' and orders.status = 'active' and orders.op_reuse = 0
                where current_timestamp() <= date_add(pr.doc, INTERVAL pr.holding_days day)
                  and current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day)
                  and IF(pr.sales_status = 'pending' or orders.id is not null or sales.id is not null, 1 = 1, 1 = 2)";
        if ($stockid) $sql .= " and stocks.id = $stockid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($proformaid) $sql .= " and pr.id = $proformaid";
        if ($salesid) $sql .= " and sales.id = $salesid";
        if ($orderid) $sql .= " and orders.id = $orderid";
        if ($exceptid) $sql .= " and pr.id != $exceptid";

        $sql .= " group by stockid";
//        debug($sql);
        return fetchRows($sql);
    }

    function auditList($proformano = "", $proforma_status = "", $fromdate = "", $todate = "", $userid = "", $clientid = "", $productid = "", $modelid = "", $depart = "", $productcategory = "", $locationid = "", $branchid = "")
    {
        $sql = "select pd.proformaid,
                       pr.currencyid,
                       cr.rate_amount                                                as exchange_rate,
                       pr.paymentterms,
                       cu.name                                                       as currencyname,
                       pr.doc,
                       clients.id                                                    as clientid,
                       clients.name                                                  as clientname,
                       pr.createdby,
                       users.name                                                    as salesperson,
                       if(p.id is null, 'external', 'internal')                      as source,
                       if(p.id is null, '', p.id)                                    as productid,
                       if(p.id is null, pd.productname, p.name)                      as productname,
                       p.non_stock,
                       depart.name                                                   as departmentname,
                       model.name                                                    as brandname,
                       pc.name                                                       as productcategoryname,
                       pd.qty,
                       cp.costprice,
                       pd.price,
                       pd.vat_rate,
                       pd.incprice,
                       pd.sinc,
                       cp.costprice * pd.qty                                         as totalcost,
                       round((pd.price - cp.costprice) * 100 / cp.costprice, 2)      as profitmargin,
                       round((pd.price - cp.costprice) * pd.qty, 2)                  as profitamount,
                       if(pd.sinc, round(pd.incprice * pd.qty / (1 + pd.vat_rate / 100), 2),
                          round(pd.price * pd.qty, 2))                               as excamount,
                       if(pd.sinc, (pd.incprice * pd.qty) - round(pd.incprice * pd.qty / (1 + pd.vat_rate / 100), 2),
                          round(pd.price * pd.qty * (pd.vat_rate / 100), 2))         as vatamount,
                       round(cp.costprice * pd.qty * cr.rate_amount, 2)              as base_totalcost,
                       round((pd.price - cp.costprice) * pd.qty * cr.rate_amount, 2) as base_profitamount,
                       if(pd.sinc, round(pd.incprice * pd.qty * cr.rate_amount / (1 + pd.vat_rate / 100), 2),
                          round(pd.price * pd.qty * cr.rate_amount, 2))              as base_excamount,
                       ifnull(directsale.id, ordersale.id)                           as salesid,
                       ifnull(directsale.receipt_no, ordersale.receipt_no)           as invoiceno,
                       orders.id                                                     as orderid,
                       case
                           when current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'pending'
                               then 'pending'
                           when (directsale.id is not null or ordersale.id is not null) and pr.sales_status = 'closed'
                               then 'closed'
                           when current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'closed' and orders.id is not null
                               then 'under order'
                           when current_timestamp() > date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'pending'
                               then 'invalid'
                           end                                                       as proforma_status
                from proformadetails pd
                         inner join proformas pr on pd.proformaid = pr.id
                         inner join currencies cu on pr.currencyid = cu.id
                         inner join currencies_rates cr on cu.id = cr.currencyid
                         inner join clients on clients.id = pr.clientid
                         inner join users on pr.createdby = users.id
                         inner join locations l on pr.locid = l.id
                         inner join branches b on l.branchid = b.id
                         left join sales directsale on directsale.proformaid = pr.id
                         left join orders on orders.proformaid = pr.id
                         left join sales ordersale on ordersale.orderid = orders.id
                         left join products p on pd.productid = p.id
                         left join current_prices cp on cp.productid = p.id and cp.branchid = b.id
                         left join model on p.modelid = model.id
                         left join departments depart on p.departid = depart.id
                         left join product_categories pc on p.productcategoryid = pc.id
                where 1 = 1";
        if ($proformano) $sql .= " and pd.proformaid = $proformano";
        if ($userid) $sql .= " and pr.createdby = $userid";
        if ($clientid) $sql .= " and pr.clientid = $clientid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($modelid) $sql .= " and model.id = $modelid";
        if ($depart) $sql .= " and depart.id = $depart";
        if ($productcategory) $sql .= " and pc.id = $productcategory";
        if ($fromdate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') <= '$todate'";

        $sql .= " having 1=1";
        if ($proforma_status) $sql .= " and proforma_status = '$proforma_status'";
        $sql .= " order by pr.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

}
