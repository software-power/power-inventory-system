<?php


class SerialNos extends model
{

    public const SOURCE_SALE = 'sale';
    public const SOURCE_TRANSFER = 'transfer';
    public const SOURCE_GRN = 'grn';
    public const SOURCE_RETURN = 'return';
    public const SOURCE_UPLOAD = 'upload';
    public const SOURCE_MANUFACTURE = 'manufacture';


    var $table = 'serialnos';

    static $serialNoClass = null;

    function __construct()
    {
        self::$serialNoClass = $this;
    }


    function getList($locationid = '', $productid = '', $number = '', $fromdate = '', $todate = '', $status = '', $initlocationid = "", $salesperson = '',
                     $invoiceno = '', $snoid = '', $manufactureperson = '')
    {
        $sql = "select sno.*,
                       p.name            as productname,
                       users.name        as creator,
                       l.name            as current_location,
                       b.name            as current_branch,
                       initlocation.name as initial_location,
                       initb.name        as initial_branch,
                       sales.receipt_no  as invoiceno,
                       salesperson.name  as salesperson
                from serialnos sno
                         inner join stocks on sno.current_stock_id = stocks.id
                         inner join products p on stocks.productid = p.id
                         inner join users on sno.createdby = users.id
                         inner join locations l on stocks.locid = l.id
                         inner join branches b on l.branchid = b.id
                         left join stocks initstock on sno.initial_stockid = initstock.id
                         left join locations initlocation on initstock.locid = initlocation.id
                         left join branches initb on initlocation.branchid = initb.id
                         left join salesdetails sd on sno.sdi = sd.id
                         left join sales on sd.salesid = sales.id
                         left join stock_manufacture_details smd on sno.smdi = smd.id
                         left join stock_manufactures sm on smd.manufactureid = sm.id
                         left join users salesperson on sno.salespersonid = salesperson.id
                where 1 = 1";
        if ($snoid) $sql .= " and sno.id = $snoid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($salesperson) $sql .= " and sno.sdi > 0 and sno.salespersonid = $salesperson";
        if ($manufactureperson) $sql .= " and sno.smdi > 0 and sno.salespersonid = $manufactureperson";
        if ($invoiceno) $sql .= " and sales.receipt_no like '%$invoiceno%'";
        if ($initlocationid) $sql .= " and initlocation.id = $initlocationid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($number) $sql .= " and sno.number  like '%$number%'";
        $filter_dates = true;
        if ($status) {
            if ($status == 'sold') {
                $sql .= " and sno.sdi > 0";
                if ($fromdate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') >= $fromdate";
                if ($todate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') <= $todate";
                $filter_dates = false;
            }
            if ($status == 'used_manufacture') {
                $sql .= " and sno.smdi > 0";
                if ($fromdate) $sql .= " and date_format(sm.doc,'%Y-%m-%d') >= $fromdate";
                if ($todate) $sql .= " and date_format(sm.doc,'%Y-%m-%d') <= $todate";
                $filter_dates = false;
            }
            if ($status == 'in_stock') $sql .= " and (sno.sdi = 0 or sno.sdi is null) and (sno.smdi = 0 or sno.smdi is null)";
        }
        if ($filter_dates) {
            if ($fromdate) $sql .= " and date_format(sno.doc,'%Y-%m-%d') >= $fromdate";
            if ($todate) $sql .= " and date_format(sno.doc,'%Y-%m-%d') <= $todate";
        }

        $sql .= " order by sno.id desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function history($snoid = '')
    {
        $snoid = $snoid ? " and sno.id = $snoid" : "";
        $sql = "select *
                from (
                         select sno.*,
                                p.name         as productname,
                                users.name     as creator,
                                suppliers.name as suppliername,
                                l.name         as locationname,
                                'grn'          as voucher_type,
                                grn.id         as voucher_no,
                                sno.doc        as action_date
                         from serialnos sno
                                  inner join stocks on sno.current_stock_id = stocks.id
                                  inner join products p on stocks.productid = p.id
                                  inner join users on sno.createdby = users.id
                                  inner join grndetails gd on gd.id = sno.gdi and source = 'grn'
                                  inner join grn on gd.grnid = grn.id
                                  inner join suppliers on grn.supplierid = suppliers.id
                                  inner join locations l on grn.locid = l.id
                         where 1 = 1 $snoid
                
                         union all
                
                         select sno.*,
                                p.name         as productname,
                                users.name     as creator,
                                suppliers.name as suppliername,
                                l.name         as locationname,
                                'GRN Return'   as voucher_type,
                                grn_returns.id as voucher_no,
                                grsno.doc      as action_date
                         from grn_return_serials grsno
                                  inner join serialnos sno on grsno.serialno_id = sno.id
                                  inner join stocks on sno.current_stock_id = stocks.id
                                  inner join products p on stocks.productid = p.id
                                  inner join users on sno.createdby = users.id
                                  inner join grnreturn_details grd on grsno.grdi = grd.id
                                  inner join grn_returns on grd.returnid = grn_returns.id
                                  inner join grn on grn_returns.grnid = grn.id
                                  inner join suppliers on grn.supplierid = suppliers.id
                                  inner join locations l on grn_returns.locid = l.id
                         where 1 = 1 $snoid
                         
                         union all
                
                         select sno.*,
                                p.name         as productname,
                                users.name     as creator,
                                ''             as suppliername,
                                l.name         as locationname,
                                'Sales Return' as voucher_type,
                                sr.id          as voucher_no,
                                sr.doc         as action_date
                         from sales_return_serialnos srsno
                                  inner join serialnos sno on srsno.snoid = sno.id
                                  inner join stocks on sno.current_stock_id = stocks.id
                                  inner join products p on stocks.productid = p.id
                                  inner join users on sno.createdby = users.id
                                  inner join sales_return_details srd on srsno.srdid = srd.id
                                  inner join sales_returns sr on srd.srid = sr.id
                                  inner join sales on sr.salesid = sales.id
                                  inner join locations l on sales.locationid = l.id
                         where 1 = 1 $snoid
                         
                         union all
                
                         select sno.*,
                                p.name     as productname,
                                users.name as creator,
                                ''         as suppliername,
                                l.name     as locationname,
                                'sales'    as voucher_type,
                                sales.id   as voucher_no,
                                sales.doc  as action_date
                         from sales_serialnos ssno
                                  inner join serialnos sno on ssno.snoid = sno.id
                                  inner join stocks on sno.current_stock_id = stocks.id
                                  inner join products p on stocks.productid = p.id
                                  inner join users on sno.createdby = users.id
                                  inner join salesdetails sd on ssno.sdi = sd.id
                                  inner join sales on sd.salesid = sales.id
                                  inner join locations l on sales.locationid = l.id
                         where 1 = 1 $snoid
                         
                         union all
                
                         select sno.*,
                                p.name     as productname,
                                users.name as creator,
                                ''         as suppliername,
                                l.name     as locationname,
                                'transfer' as voucher_type,
                                st.id      as voucher_no,
                                st.doc     as action_date
                         from stock_transfer_serials stsno
                                  inner join serialnos sno on stsno.serialno_id = sno.id
                                  inner join stocks on sno.current_stock_id = stocks.id
                                  inner join products p on stocks.productid = p.id
                                  inner join users on sno.createdby = users.id
                                  inner join stock_transfer_details std on stsno.stdi = std.id
                                  inner join stock_transfers st on std.transferid = st.id
                                  inner join locations l on st.location_to = l.id
                         where 1 = 1 $snoid
                         
                         union all
                
                         select sno.*,
                                p.name        as productname,
                                users.name    as creator,
                                ''            as suppliername,
                                l.name        as locationname,
                                'manufacture' as voucher_type,
                                sm.id         as voucher_no,
                                sm.doc        as action_date
                         from stock_manufacture_serialnos smsno
                                  inner join serialnos sno on smsno.snoid = sno.id
                                  inner join stocks on sno.current_stock_id = stocks.id
                                  inner join products p on stocks.productid = p.id
                                  inner join users on sno.createdby = users.id
                                  inner join stock_manufacture_details smd on smsno.smdi = smd.id
                                  inner join stock_manufactures sm on smd.manufactureid = sm.id
                                  inner join locations l on sm.locationid = l.id
                         where 1 = 1 $snoid
                     ) as serialno_history
                order by action_date";
//        debug($sql);
        return fetchRows($sql);
    }


    static function sendSerialnoToSupport($salesid)
    {
        $result['status'] = 'success';
        try {
            $sale = Sales::$saleClass->get($salesid);
            if (!$sale) throw new Exception("Invoice not found!");
            if (!$sale['iscreditapproved']) throw new Exception("Invoice not approved!");

            $sale = Sales::$saleClass->salesList($salesid, '', '', '', '', '', '', '',
                '', '', '', true, '', '')[0];
            $details = Salesdetails::$saleDetailsClass->getList($salesid);
            $location = Locations::$locationClass->get($sale['locationid']);
//            debug($location);
            $post_data = [];

            $post_data['clientcode'] = $sale['clientid'];
            $post_data['support_name'] = CS_SUPPORT_NAME;
            $post_data['clientname'] = $sale['clientname'];
            $post_data['invoiceno'] = $sale['receipt_no'];
            $post_data['invoicedate'] = $sale['doc'];
            $post_data['branchcode'] = $location['support_branchcode'];
            $post_data['source'] = "inventory";


            foreach ($details as $d) {
                $d['warrant_month'] = $d['warrant_month'] ?: 12; //default warranty 1 year
                foreach (SerialNos::$serialNoClass->find(['sdi' => $d['id']]) as $sno) {
                    $post_data['serialnos'][] = [
                        'snoid' => $sno['id'],
                        'number' => $sno['number'],
                        'productcode' => $d['productid'],
                        'productname' => $d['productname'],
                        'warrantyfrom' => fDate($sale['doc'], 'Y-m-d'),
                        'warrantyto' => date('Y-m-d', strtotime($sale['doc'] . " +{$d['warrant_month']} months")),
                    ];
                }
            }

            if (empty($post_data['serialnos'])) throw new Exception("No serial no found!");
            $url = SUPPORT_ENDPOINT['serials'];
            $post_data = json_encode($post_data);
            $response = sendSupportRequest($url, $post_data, "POST", true);

            if ($response['status'] == 'success') {
                if ($response['data']['status'] != 'success') {
                    throw new Exception($response['data']['message']);
                } else {
                    $result['msg'] = $response['data']['message'];
                }
            } else {
                throw new Exception("Error sending to support");
            }

        } catch (Exception $e) {
            $result = [
                'status' => 'error',
                'msg' => $e->getMessage()
            ];
        }
        return $result;
    }
}