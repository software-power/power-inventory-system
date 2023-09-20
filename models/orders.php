<?

/**
 *orders table
 */
class Orders extends model
{

    public const STATUS_CLOSED = 'closed';
    public const STATUS_PENDING = 'pending';

    //conditional
    public const STATUS_INVALID = 'invalid';
    public const STATUS_CANCELED = 'canceled';

    public const TYPE_QUICK = 'quick';
    public const TYPE_NORMAL = 'normal';

    public const PROCESS_TYPE_INVOICE = "invoice";
    public const PROCESS_TYPE_VFDRECEIPT = "vfdreceipt";
    public const PROCESS_TYPE_NORMALRECEIPT = "normalreceipt";
    public const PROCESS_TYPE_BOTH = "both";


    var $table = "orders";
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getAllOrders($orderno = "", $userid = "", $order_status = "", $clientid = "", $fromdate = "", $todate = "", $type = "", $locationid = "", $branchid = "")
    {
        $sql = "select o.id                                          as orderid,
                       o.status                                      as orderstatus,
                       o.sales_status                                as salestatus,
                       o.type                                        as ordertype,
                       o.billid,
                       o.foreign_orderid,
                       o.order_source,
                       o.foreign_ordertype,
                       o.doc                                         as issueddate,
                       o.closedate                                   as closedate,
                       o.clientid                                    as orderclientid,
                       o.order_value,
                       round(o.order_value * cr.rate_amount, 2)      as base_order_value,
                       currencies.name                               as currencyname,
                       currencies.description                        as currency_description,
                       currencies.base                               as base_currency,
                       cr.rate_amount                                as currency_amount,
                       sales.id                                      as salesid,
                       sales.receipt_no                              as invoiceno,
                       c.name                                        as client_name,
                       c.tinno                                       as clienttinno,
                       c.vatno                                       as clientvrn,
                       c.mobile                                      as clientmobile,
                       c.address                                     as clientaddress,
                       l.name                                        as locationname,
                       b.id                                          as branchid,
                       b.name                                        as branchname,
                       u.name                                        as issuedby,
                       closer.name                                   as closername,
                       oc.id                                         as checklistid,
                       o.validity_days,
                       o.remarks,
                       o.internal_remarks,
                       count(oc.id) > 0                              as has_checklist,
                       date_add(o.doc, INTERVAL o.validity_days day) as valid_until,
                       case
                           when o.status = 'inactive' then 'canceled'
                           when o.sales_status = 'closed' then 'closed'
                           when current_date() > date_add(o.doc, INTERVAL o.validity_days day) then 'invalid'
                           else 'pending'
                           end                                       as order_status
                from orders as o
                         inner join clients as c on c.id = o.clientid
                         inner join currencies on o.currencyid = currencies.id
                         inner join currencies_rates cr on currencies.id = cr.currencyid
                         inner join locations as l on l.id = o.locid
                         inner join branches b on l.branchid = b.id
                         inner join users as u on u.id = o.createdby
                         left join users as closer on closer.id = o.sales_closedby
                         left join sales on o.id = sales.orderid
                         left join order_checklists oc on o.id = oc.orderid
                where 1 = 1";

        if ($orderno) $sql .= " and o.id = $orderno ";
        if ($userid) $sql .= " and o.createdby = $userid ";
        if ($type) $sql .= " and o.type = '$type' ";
        if ($clientid) $sql .= " and o.clientid = $clientid ";
        if ($locationid) $sql .= " and l.id = $locationid ";
        if ($branchid) $sql .= " and b.id = $branchid ";
        if ($fromdate) $sql .= " and date_format(o.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(o.doc,'%Y-%m-%d') <= '$todate'";

        $sql .= " group by o.id having 1=1";

        if ($order_status) $sql .= " and order_status = '$order_status' ";

        $sql .= ' order by o.doc desc';
//   debug($sql);
        return fetchRows($sql);
    }

    function withDetails($orderid = "", $userid = "", $fromdate = "", $todate = "", $salestatus = "", $type = "", $locationid = "", $branchid = "", $group = true)
    {
        $sql = "select o.*,
                       case
                           when o.status = 'inactive' then 'canceled'
                           when o.sales_status = 'closed' then 'closed'
                           when current_date() > date_add(o.doc, INTERVAL o.validity_days day) then 'invalid'
                           else 'pending'
                           end                                                                                            as order_status,
                       currencies.name                                                                                    as currencyname,
                       currencies.description                                                                             as currency_description,
                       cr.id                                                                                              as currency_rateid,
                       clients.name                                                                                       as clientname,
                       clients.tinno                                                                                      as clienttinno,
                       clients.vatno                                                                                      as clientvrn,
                       clients.mobile                                                                                     as clientmobile,
                       clients.address                                                                                    as clientaddress,
                       clients.email                                                                                      as clientemail,
                       clients.tel                                                                                        as clienttel,
                       clients.mobile_country_code,
                       l.name                                                                                             as locationname,
                       b.id                                                                                               as branchid,
                       b.name                                                                                             as branchname,
                       users.name                                                                                         as issuedby,
                       closer.name                                                                                        as closername,
                       p.id                                                                                               as productid,
                       p.name                                                                                             as productname,
                       p.description                                                                                      as productdescription,
                       p.description,
                       p.baseprice,
                       p.barcode_office,
                       p.barcode_manufacture,
                       p.unit,
                       p.point,
                       p.non_stock,
                       p.trackserialno,
                       p.validate_serialno,
                       p.track_expire_date,
                       p.prescription_required,
                       od.print_extra,
                       extradesc.description                                                                          as extra_description,
                       od.id                                                                                              as detailId,
                       od.qty,
                       od.vat_rate,
                       od.price,
                       od.sinc,
                       -- od.incprice,
                       if(od.sinc, od.incprice, round(od.price * (1 + od.vat_rate / 100), 2))                             as incprice,
                       od.required_price,
                       IF(od.sinc, od.incprice * od.qty, round(od.price * od.qty * (1 + od.vat_rate / 100), 2))           as incamount,
                       IF(od.sinc, round(od.incprice * od.qty / (1 + od.vat_rate / 100), 2), round(od.price * od.qty, 2)) as excamount,
                       IF(od.sinc, (od.incprice * od.qty) - round(od.incprice * od.qty / (1 + od.vat_rate / 100), 2),
                          round(od.price * od.qty * (od.vat_rate / 100), 2))                                              as vatamount
                from orders o
                         inner join currencies on o.currencyid = currencies.id
                         inner join currencies_rates cr on cr.currencyid = currencies.id
                         inner join clients on clients.id = o.clientid
                         inner join locations as l on l.id = o.locid
                         inner join branches b on l.branchid = b.id
                         inner join users on users.id = o.createdby
                         left join users closer on closer.id = o.sales_closedby
                         inner join orderdetails od on od.orderid = o.id
                         inner join products p on od.productid = p.id
                         left join salesdescriptions extradesc on od.id = extradesc.odi
                where 1 = 1";
        if ($orderid) $sql .= " and o.id = $orderid ";
        if ($userid) $sql .= " and o.createdby = $userid ";
        if ($fromdate) $sql .= " and date_format(o.doc,'%Y-%m-%d') >= '$fromdate' ";
        if ($todate) $sql .= " and date_format(o.doc,'%Y-%m-%d') <= '$todate' ";
        if ($type) $sql .= " and o.type = '$type' ";
        if ($salestatus) $sql .= " and o.sales_status = $salestatus ";
        if ($locationid) $sql .= " and l.id = $locationid ";
        if ($branchid) $sql .= " and b.id = $branchid ";
        $result = fetchRows($sql);
        if (!$group) {
            return $result;
        } else {
            $newArray = [];
            foreach ($result as $i => $item) {
                $newArray[$item['id']]['orderid'] = $item['id'];
                $newArray[$item['id']]['currencyid'] = $item['currencyid'];
                $newArray[$item['id']]['currencyname'] = $item['currencyname'];
                $newArray[$item['id']]['currency_description'] = $item['currency_description'];
                $newArray[$item['id']]['currency_rateid'] = $item['currency_rateid'];
                $newArray[$item['id']]['billid'] = $item['billid'];
                $newArray[$item['id']]['proformaid'] = $item['proformaid'];
                $newArray[$item['id']]['op_reuse'] = $item['op_reuse'];
                $newArray[$item['id']]['order_value'] = $item['order_value'];
                $newArray[$item['id']]['type'] = $item['type'];
                $newArray[$item['id']]['order_source'] = $item['order_source'];
                $newArray[$item['id']]['foreign_orderid'] = $item['foreign_orderid'];
                $newArray[$item['id']]['foreign_ordertype'] = $item['foreign_ordertype'];
                $newArray[$item['id']]['print_size'] = $item['print_size'];
                $newArray[$item['id']]['sales_status'] = $item['sales_status'];
                $newArray[$item['id']]['sales_closedby'] = $item['sales_closedby'];
                $newArray[$item['id']]['closername'] = $item['closername'];
                $newArray[$item['id']]['closedate'] = $item['closedate'];
                $newArray[$item['id']]['doc'] = $item['doc'];
                $newArray[$item['id']]['order_status'] = $item['order_status'];
                $newArray[$item['id']]['createdby'] = $item['createdby'];
                $newArray[$item['id']]['issuedby'] = $item['issuedby'];
                $newArray[$item['id']]['deptid'] = $item['deptid'];
                $newArray[$item['id']]['departmentname'] = $item['departmentname'];
                $newArray[$item['id']]['clientid'] = $item['clientid'];
                $newArray[$item['id']]['clientname'] = $item['clientname'];
                $newArray[$item['id']]['address'] = $item['address'];
                $newArray[$item['id']]['mobile'] = $item['mobile'];
                $newArray[$item['id']]['clientmobile'] = $item['clientmobile'];
                $newArray[$item['id']]['clientemail'] = $item['clientemail'];
                $newArray[$item['id']]['clientvrn'] = $item['clientvrn'];
                $newArray[$item['id']]['clienttinno'] = $item['clienttinno'];
                $newArray[$item['id']]['clientaddress'] = $item['clientaddress'];
                $newArray[$item['id']]['clienttel'] = $item['clienttel'];
                $newArray[$item['id']]['mobile_country_code'] = $item['mobile_country_code'];
                $newArray[$item['id']]['email'] = $item['email'];
                $newArray[$item['id']]['tinno'] = $item['tinno'];
                $newArray[$item['id']]['vatno'] = $item['vatno'];
                $newArray[$item['id']]['locid'] = $item['locid'];
                $newArray[$item['id']]['locationname'] = $item['locationname'];
                $newArray[$item['id']]['branchid'] = $item['branchid'];
                $newArray[$item['id']]['branchname'] = $item['branchname'];
                $newArray[$item['id']]['remarks'] = $item['remarks'];
                $newArray[$item['id']]['internal_remarks'] = $item['internal_remarks'];
                $newArray[$item['id']]['details'][$item['detailId']]['id'] = $item['detailId'];
                $newArray[$item['id']]['details'][$item['detailId']]['productid'] = $item['productid'];
                $newArray[$item['id']]['details'][$item['detailId']]['productname'] = $item['productname'];
                $newArray[$item['id']]['details'][$item['detailId']]['productdescription'] = $item['print_extra'] ? $item['extra_description'] : $item['productdescription'];
                $newArray[$item['id']]['details'][$item['detailId']]['baseprice'] = $item['baseprice'];
                $newArray[$item['id']]['details'][$item['detailId']]['description'] = $item['description'];
                $newArray[$item['id']]['details'][$item['detailId']]['non_stock'] = $item['non_stock'];
                $newArray[$item['id']]['details'][$item['detailId']]['trackserialno'] = $item['trackserialno'];
                $newArray[$item['id']]['details'][$item['detailId']]['validate_serialno'] = $item['validate_serialno'];
                $newArray[$item['id']]['details'][$item['detailId']]['track_expire_date'] = $item['track_expire_date'];
                $newArray[$item['id']]['details'][$item['detailId']]['prescription_required'] = $item['prescription_required'];
                $newArray[$item['id']]['details'][$item['detailId']]['qty'] = $item['qty'];
                $newArray[$item['id']]['details'][$item['detailId']]['vat_rate'] = $item['vat_rate'];
                $newArray[$item['id']]['details'][$item['detailId']]['price'] = $item['price'];
                $newArray[$item['id']]['details'][$item['detailId']]['sinc'] = $item['sinc'];
                $newArray[$item['id']]['details'][$item['detailId']]['incprice'] = $item['incprice'];
                $newArray[$item['id']]['details'][$item['detailId']]['required_price'] = $item['required_price'];
                $newArray[$item['id']]['details'][$item['detailId']]['print_extra'] = $item['print_extra'];
                $newArray[$item['id']]['details'][$item['detailId']]['extra_description'] = $item['extra_description'];
                $newArray[$item['id']]['details'][$item['detailId']]['excamount'] = $item['excamount'];
                $newArray[$item['id']]['details'][$item['detailId']]['vatamount'] = $item['vatamount'];
                $newArray[$item['id']]['details'][$item['detailId']]['incamount'] = $item['incamount'];
            }

            return array_values($newArray);
        }
    }

    function totalOrders($status = "")
    {
        $sql = "select count(*) as numberOForder FROM orders as o where 1=1 ";
        if ($status) $sql .= " and o.sales_status	= '$status'";
        //echo $sql;die();
        return fetchRow($sql);
    }

    static function postToSupport($orderid)
    {
        $result['status'] = 'success';
        try {
            if (empty($orderid)) throw new Exception("Order not found in the system");
            $order = Orders::$staticClass->getAllOrders($orderid)[0];
            if ($order['order_source'] != 'support') throw new Exception("order is not from support!");
            $foreign_orderid = $order['foreign_orderid'];
            if (empty($foreign_orderid)) throw new Exception("foreign order id not found!");
            if ($order['order_status'] == Orders::STATUS_CLOSED) {
                $sale = Sales::$saleClass->salesList($order['salesid'])[0];
                $process_type = Orders::PROCESS_TYPE_INVOICE;
                if ($sale['iscreditapproved']) {
                    if ($sale['receipt_method'] == 'sr') {
                        $process_type = Orders::PROCESS_TYPE_NORMALRECEIPT;
                        $vfd_response = "Success";
                    } else {
                        if ($sale['isfiscalized']) {
                            $fisc_sale = Sales::$saleClass->salesWithFiscalization($order['salesid']);
                            if ($fisc_sale['fiscalization_type'] == 'vfd' && $fisc_sale['fiscalize_status_message'] == 'Success') {
                                $process_type = Orders::PROCESS_TYPE_VFDRECEIPT;
                                $rctvcode = $fisc_sale['rctvcode'];
                                $znumber = $fisc_sale['znumber'];
                                $vfd_qrcode = $fisc_sale['receipt_v_num']; //overridden
                                $vfd_response = $fisc_sale['fiscalize_status_message'];
                            }
                        }
                    }
                }
                $post_data = [
                    'orderid' => $foreign_orderid,
                    'ack_orderid' => $orderid,
                    'order_status' => $order['order_status'],
                    'type' => $order['foreign_ordertype'],
                    'invoiceno' => $sale['receipt_no'],
                    'process_type' => $process_type,
                    'amount' => $sale['full_amount'],
                    'rctvcode' => $rctvcode ?: '',
                    'znumber' => $znumber ?: '',
                    'vfd_qrcode' => $vfd_qrcode ?: '',
                    'vfd_response' => $vfd_response ?: '',
                ];
            } else {
                $post_data = [
                    'orderid' => $foreign_orderid,
                    'ack_orderid' => $orderid,
                    'order_status' => $order['order_status'],
                    'type' => $order['foreign_ordertype'],
                    'invoiceno' => '',
                    'process_type' => '',
                    'amount' => '',
                    'rctvcode' => '',
                    'znumber' => '',
                    'vfd_qrcode' => '',
                    'vfd_response' => '',
                ];
            }
            $url = SUPPORT_ENDPOINT['order'] . "/$foreign_orderid";
            $post_data = json_encode($post_data);
//            debug($post_data);
            $response = sendSupportRequest($url, $post_data, "PATCH", true);
//            debug($response);
//            debug($post_data);

            if ($response['status'] == 'success') {
                if ($response['data']['status'] != 'success') throw new Exception($response['data']['message']);
            } else {
                throw new Exception("Error sending to support");
            }

            $result['msg'] = "Support order updated";
            return $result;
        } catch (Exception $e) {
            $result = [
                'status' => 'error',
                'msg' => $e->getMessage()
            ];
            return $result;
        }
    }
}
