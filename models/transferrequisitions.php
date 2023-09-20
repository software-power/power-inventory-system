<?


class TransferRequisitions extends model
{
    var $table = "transfer_requisitions";

    function getList($reqno = "",$createdby="", $fromlocationid = "", $tolocationid = "", $fromdate = "", $todate = "")
    {
        $sql = "select tr.*,
                       fromloc.name  as fromlocation,
                       toloc.name    as tolocation,
                       st.id         as transferid,
                       users.name    as issuedby,
                       approver.name as approver
                from transfer_requisitions tr
                         inner join locations fromloc on tr.location_from = fromloc.id
                         inner join locations toloc on tr.location_to = toloc.id
                         left join stock_transfers st on st.reqid = tr.id
                         inner join users on users.id = tr.createdby
                         left join users approver on approver.id = tr.approvedby
                where 1 = 1";
        if ($reqno) $sql .= " and tr.id = $reqno";
        if ($createdby) $sql .= " and users.id = $createdby";
        if ($fromlocationid) $sql .= " and fromloc.id = $fromlocationid";
        if ($tolocationid) $sql .= " and toloc.id = $tolocationid";
        if ($fromdate) $sql .= " and date_format(tr.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(tr.doc,'%Y-%m-%d') <= '$todate'";
        $sql .=" order by tr.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function withDetails($reqno = "", $fromlocationid = "", $tolocationid = "", $fromdate = "", $todate = "", $group = true)
    {
        $sql = "select trd.*,
                       tr.location_from,
                       tr.location_to,
                       tr.remark,
                       tr.createdby,
                       tr.approvedby,
                       tr.approve_date,
                       tr.auto_approve,
                       fromloc.name  as fromlocation,
                       toloc.name    as tolocation,
                       st.id         as transferid,
                       users.name    as issuedby,
                       approver.name as approver,
                       p.name as productname,
                       p.description as productdescription
                from transfer_requisitions tr
                         inner join locations fromloc on tr.location_from = fromloc.id
                         inner join locations toloc on tr.location_to = toloc.id
                         left join stock_transfers st on st.reqid = tr.id
                         inner join users on users.id = tr.createdby
                         left join users approver on approver.id = tr.approvedby
                         inner join transfer_requisition_details trd on trd.reqid = tr.id
                         inner join products p on trd.productid = p.id
                where 1 = 1";
        if ($reqno) $sql .= " and tr.id = $reqno";
        if ($fromlocationid) $sql .= " and fromloc.id = $fromlocationid";
        if ($tolocationid) $sql .= " and toloc.id = $tolocationid";
        if ($fromdate) $sql .= " and date_format(tr.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(tr.doc,'%Y-%m-%d') <= '$todate'";
//        debug($sql);
        $requisitions = fetchRows($sql);
        if (!$group) {
            return $requisitions;
        } else {
            $newArray = [];
            foreach ($requisitions as $index => $item) {
                $newArray[$item['reqid']]['reqid'] = $item['reqid'];
                $newArray[$item['reqid']]['location_from'] = $item['location_from'];
                $newArray[$item['reqid']]['fromlocation'] = $item['fromlocation'];
                $newArray[$item['reqid']]['location_to'] = $item['location_to'];
                $newArray[$item['reqid']]['tolocation'] = $item['tolocation'];
                $newArray[$item['reqid']]['transferid'] = $item['transferid'];
                $newArray[$item['reqid']]['createdby'] = $item['createdby'];
                $newArray[$item['reqid']]['issuedby'] = $item['issuedby'];
                $newArray[$item['reqid']]['remark'] = $item['remark'];
                $newArray[$item['reqid']]['approvedby'] = $item['approvedby'];
                $newArray[$item['reqid']]['approver'] = $item['approver'];
                $newArray[$item['reqid']]['approve_date'] = $item['approve_date'];
                $newArray[$item['reqid']]['doc'] = $item['doc'];
                $newArray[$item['reqid']]['details'][$item['id']]['id'] = $item['id'];
                $newArray[$item['reqid']]['details'][$item['id']]['productid'] = $item['productid'];
                $newArray[$item['reqid']]['details'][$item['id']]['productname'] = $item['productname'];
                $newArray[$item['reqid']]['details'][$item['id']]['productdescription'] = $item['productdescription'];
                $newArray[$item['reqid']]['details'][$item['id']]['qty'] = $item['qty'];
            }
            return array_values($newArray);
        }
    }
}