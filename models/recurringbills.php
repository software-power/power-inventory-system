<?php


class RecurringBills extends model
{
    public const bill_type_month = "month";
    public const bill_type_year = "year";

    var $table = "recurring_bills";
    public static $staticClass;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($billid = "", $clientid = "", $createdby = "", $billmonth = "", $status = "")
    {
        $sql = "select rb.*,
                       rb.id                                                      as billid,
                       bt.name                                                    as billtypename,
                       bt.type                                                    as billtype,
                       bt.bill_interval,
                       clients.name                                               as clientname,
                       clients.mobile                                             as clientmobile,
                       clients.tinno                                              as clienttinno,
                       clients.vatno                                              as clientvrn,
                       clients.tel                                                as clienttel,
                       clients.email                                              as clientemail,
                       clients.address                                            as clientaddress,
                       cu.name                                                    as currency_name,
                       cu.description                                             as currency_description,
                       users.name                                                 as issuedby,
                       l.name                                                     as locationname,
                       b.id                                                       as branchid,
                       b.name                                                     as branchname,
                       if(rb.nextbilldate is null, rb.startdate, rb.nextbilldate) as nextbilldate
                from recurring_bills rb
                         inner join clients on rb.clientid = clients.id
                         inner join bill_types bt on rb.billtypeid = bt.id
                         inner join users on rb.createdby = users.id
                         inner join currencies cu on cu.id = rb.currencyid
                         inner join locations l on rb.locationid = l.id
                         inner join branches b on l.branchid = b.id
                where 1 = 1";
        if ($clientid) $sql .= " and rb.clientid = $clientid";
        if ($createdby) $sql .= " and rb.createdby = $createdby";
        if ($status) $sql .= " and rb.status = '$status'";

        if ($billid) {
            if (is_array($billid)) {
                $sql .= " and rb.id in ('" . implode("','", $billid) . "')";
            } else {
                $sql .= " and rb.id = $billid";
                return fetchRow($sql);
            }
        }
        $sql .= " having 1=1";
        if ($billmonth) $sql .= " and date_format(nextbilldate,'%Y-%m') = '$billmonth'";
        $sql .= " order by rb.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    static function markAsBilled($salesid)
    {
        $sale = Sales::$saleClass->get($salesid);
        if ($sale['billid'] && RecurringBillsLogs::$staticClass->countWhere(['salesid' => $salesid]) == 0) {
            $bill = RecurringBills::$staticClass->getList($sale['billid']);

            if ($bill['billtype'] == RecurringBills::bill_type_month) {
                $nextbilldate = date('Y-m-d', strtotime($bill['nextbilldate'] . " +{$bill['bill_interval']} month"));
            } elseif ($bill['billtype'] == RecurringBills::bill_type_year) {
                $nextbilldate = date('Y-m-d', strtotime($bill['nextbilldate'] . " +{$bill['bill_interval']} year"));
            }
            $lastbilldate = fDate($bill['nextbilldate'], 'Y-m-d');
            $billmonth = fDate($lastbilldate, 'F Y');

            RecurringBills::$staticClass->update($bill['billid'], [
                'lastbilldate' => $lastbilldate,
                'nextbilldate' => $nextbilldate
            ]);

            RecurringBillsLogs::$staticClass->insert([
                'billid' => $bill['billid'],
                'salesid' => $salesid,
                'billmonth' => $lastbilldate,
                'createdby' => $_SESSION['member']['id'],
                'remarks' => "Bill for month $billmonth\r\n\r\n{$bill['billtypename']}, {$bill['bill_interval']} {$bill['billtype']}"
            ]);
        }
    }
}