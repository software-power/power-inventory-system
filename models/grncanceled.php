<?php


class GrnCanceled extends model
{
    var $table = 'grn_canceled';

    static $grnCanceledClass = null;

    function __construct()
    {
        self::$grnCanceledClass = $this;
    }

    function getList($locationid = "", $supplierid = "", $fromdate = "", $todate = "", $branchid = "")
    {
        $sql = "select gc.*,
                       users.name as username,
                       l.name     as locationname,
                       b.name     as branchname,
                       s.name     as suppliername
                from grn_canceled gc
                         inner join users on gc.createdby = users.id
                         inner join locations l on gc.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join suppliers s on gc.supplierid = s.id
                where 1 = 1";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($supplierid) $sql .= " and s.id = $supplierid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($fromdate) $sql .= " and date_format(gc.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(gc.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by gc.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }
}