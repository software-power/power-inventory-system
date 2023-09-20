<?php


class RecurringBillsLogs extends model
{
    var $table = "recurring_bills_logs";
    public static $staticClass;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    public function getList($billid = '')
    {
        $sql = "select rbl.*,
                       sales.receipt_no as invoiceno,
                       users.name       as issuedby
                from recurring_bills_logs rbl
                         left join sales on rbl.salesid = sales.id
                         inner join users on rbl.createdby = users.id
                where 1 = 1";
        $sql .= " and rbl.billid = $billid order by rbl.billmonth desc";
        return fetchRows($sql);
    }
}