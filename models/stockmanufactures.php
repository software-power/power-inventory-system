<?php


class StockManufactures extends model
{
    var $table = "stock_manufactures";

    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($manufactureid = '', $locationid = '', $createdby = '', $fromdate = '', $todate = '', $status = '')
    {
        $sql = "select sm.*,
                       l.name                  as locationname,
                       br.id                   as branchid,
                       br.name                 as branchname,
                       users.name              as issuedby,
                       approver.name           as approver,
                       case
                           when sm.status != 'active' then 'canceled'
                           when approver.id is null then 'not-approved'
                           else 'approved' end as manufacture_status
                from stock_manufactures sm
                         inner join locations l on l.id = sm.locationid
                         inner join branches br on br.id = l.branchid
                         inner join users on users.id = sm.createdby
                         left join users as approver on approver.id = sm.approvedby
                where 1 = 1";
        if($manufactureid)$sql.=" and sm.id = $manufactureid";
        if($locationid)$sql.=" and l.id = $locationid";
        if($createdby)$sql.=" and users.id = $createdby";
        if($fromdate)$sql.=" and date_format(sm.doc,'%Y-%m-%d') >= '$fromdate'";
        if($todate)$sql.=" and date_format(sm.doc,'%Y-%m-%d') <= '$todate'";

        $sql .= " having 1 = 1";
        if($status)$sql.=" and manufacture_status = '$status'";

        $sql.=" order by sm.doc desc";

        return fetchRows($sql);

    }
}