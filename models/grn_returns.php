<?

class GrnReturns extends model
{
    var $table = 'grn_returns';

    function getGrnReturns($returnId = "", $grnNo = "", $supplierid = "", $fromDate = "", $toDate = "", $locationid = "", $branchid = "")
    {
        $sql = "select greturn.*,
                       s.name        as suppliername,
                       l.name        as locationname,
                       br.name       as branchname,
                       users.name    as username,
                       count(grd.id) as productCount
                from grn_returns greturn
                         inner join grn on grn.id = greturn.grnid
                         inner join suppliers s on grn.supplierid = s.id
                         inner join locations l on greturn.locid = l.id
                         inner join branches br on br.id = l.branchid
                         inner join users on greturn.createdby = users.id
                         inner join grnreturn_details grd on greturn.id = grd.returnid
                where 1 = 1";
        if ($returnId) $sql .= " and greturn.id = $returnId";
        if ($grnNo) $sql .= " and grn.id = $grnNo";
        if ($supplierid) $sql .= " and s.id = $supplierid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and br.id = $branchid";
        if ($fromDate) $sql .= " and date_format(greturn.doc,'%Y-%m-%d') >= '$fromDate'";
        if ($toDate) $sql .= " and date_format(greturn.doc,'%Y-%m-%d') <= '$toDate'";
        $sql .= " group by greturn.id order by greturn.id desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function getGrnReturnBatchWise($returnId = "", $grnNo = "", $supplierid = "", $productId = "", $stockid = "", $batchNo = "", $fromDate = "", $toDate = "", $locationid = "", $branchid = "")
    {
        $sql = "select greturn.*,
                       s.name               as suppliername,
                       l.name               as locationname,
                       br.name              as branchname,
                       users.name           as username,
                       grd.id               as detailId,
                       grd.stockid,
                       grb.qty,
                       products.name        as productname,
                       products.description as productdescription,
                       products.track_expire_date,
                       products.trackserialno,
                       batches.id           as batchId,
                       batches.batch_no,
                       batches.expire_date
                from grn_returns greturn
                         inner join grn on grn.id = greturn.grnid
                         inner join suppliers s on grn.supplierid = s.id
                         inner join locations l on greturn.locid = l.id
                         inner join branches br on br.id = l.branchid
                         inner join users on greturn.createdby = users.id
                         inner join grnreturn_details grd on greturn.id = grd.returnid
                         inner join grn_return_batches grb on grd.id = grb.grdi
                         inner join stocks on grd.stockid = stocks.id
                         inner join products on stocks.productid = products.id
                         inner join batches on grb.batch_id = batches.id
                where 1 = 1";
        if ($returnId) $sql .= " and greturn.id = $returnId";
        if ($grnNo) $sql .= " and grn.id = $grnNo";
        if ($supplierid) $sql .= " and s.id = $supplierid";
        if ($productId) $sql .= " and products.id = $productId";
        if ($stockid) $sql .= " and grd.stockid = $stockid";
        if ($batchNo) $sql .= " and batches.batch_no = '$batchNo'";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and br.id = $branchid";
        if ($fromDate) $sql .= " and date_format(greturn.doc,'%Y-%m-%d') >= '$fromDate'";
        if ($toDate) $sql .= " and date_format(greturn.doc,'%Y-%m-%d') <= '$toDate'";
        $sql .= " order by greturn.doc desc";
        return fetchRows($sql);
    }

}
