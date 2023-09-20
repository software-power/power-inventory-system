<?php


class SalesCanceled extends model
{
    var $table = "sales_canceled";
    static $saleCanceledClass = null;

    function __construct()
    {
        self::$saleCanceledClass = $this;
    }
    function getList($locationid = "", $clientid = "", $fromdate = "", $todate = "", $branchid = "")
    {
        $sql = "select sc.*,
                       users.name as username,
                       l.name     as locationname,
                       b.name     as branchname,
                       c.name     as clientname
                from sales_canceled sc
                         inner join users on sc.createdby = users.id
                         left join locations l on sc.locationid = l.id
                         left join branches b on l.branchid = b.id
                         left join clients c on sc.clientid = c.id
                where 1 = 1";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($clientid) $sql .= " and c.id = $clientid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($fromdate) $sql .= " and date_format(sc.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sc.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by sc.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }
}