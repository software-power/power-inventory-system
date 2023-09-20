<?php


class RecurringBillEditLogs extends model
{
    var $table = "recurring_bill_edit_logs";
    public static $staticClass;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    public function getList($billid = '')
    {
        $sql = "";
        return fetchRows($sql);
    }
}