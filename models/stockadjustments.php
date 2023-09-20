<?php


class StockAdjustments extends model
{
    var $table = 'stock_adjustments';

    function getList($adjustmentId = "", $locationid = "", $branchid = "", $fromdate = "", $todate = "")
    {
        $sql = "select sa.*,
                       l.name        as locationname,
                       b.name        as branchname,
                       users.name    as issuedby,
                       count(sad.id) as productCount
                from stock_adjustments sa
                         inner join locations l on sa.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join users on sa.createdby = users.id
                         inner join stock_adjustment_details sad on sad.adjustment_id = sa.id
                where 1 = 1";
        if ($adjustmentId) $sql .= " and sa.id = $adjustmentId";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($userid) $sql .= " and users.id = $userid";
        if ($fromdate) $sql .= " and date_format(sa.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sa.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " group by sa.id order by sa.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }


}