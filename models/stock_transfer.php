<?php

class StockTransfers extends model
{
    var $table = "stock_transfers";

    static $transferClass = null;

    function __construct()
    {
        self::$transferClass = $this;
    }

    function getList($transferno = "",$createdby="", $fromlocationid = "", $tolocationid = "", $fromdate = "", $todate = "", $status = "")
    {
        $sql = "select st.*,
                       locationfrom.name as fromlocation,
                       locationto.name   as tolocation,
                       branchfrom.id     as frombranchid,
                       branchfrom.name   as frombranchname,
                       branchto.id       as tobranchid,
                       branchto.name     as tobranchname,
                       users.name        as transferby,
                       approver.name     as approver,
                       count(std.id)     as productCount
                from stock_transfers st
                         inner join locations as locationfrom on locationfrom.id = st.location_from
                         inner join branches as branchfrom on branchfrom.id = locationfrom.branchid
                         inner join locations as locationto on locationto.id = st.location_to
                         inner join branches as branchto on branchto.id = locationto.branchid
                         inner join users on users.id = st.createdby
                         left join users approver on approver.id = st.approvedby
                         inner join stock_transfer_details std on st.id = std.transferid
                where 1 = 1";
        if ($transferno) $sql .= "  and st.id = $transferno";
        if ($createdby) $sql .= "  and users.id = $createdby";
        if ($fromlocationid) $sql .= "  and locationfrom.id = $fromlocationid";
        if ($tolocationid) $sql .= "  and locationto.id = $tolocationid";
        if ($fromdate) $sql .= "  and date_format(st.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= "  and date_format(st.doc,'%Y-%m-%d') <= '$todate'";

        if ($status == 'canceled') {
            $sql .= "  and st.status != 'active'";
        } elseif ($status == 'not-approved') {
            $sql .= "  and approver.id is null";
        } elseif ($status == 'approved') {
            $sql .= "  and approver.id is not null";
        }


        $sql .= " group by st.id order by st.id desc";
//        debug($sql);
        return fetchRows($sql);
    }


    function stockTransferBatchWise($transferno = "", $productid = "", $batchno = "", $fromlocationid = "", $tolocationid = "", $fromdate = "", $todate = "")
    {
        $sql = "select st.*,
                       locationfrom.name as fromlocation,
                       locationto.name   as tolocation,
                       branchfrom.id     as frombranchid,
                       branchfrom.name   as frombranchname,
                       branchto.id       as tobranchid,
                       branchto.name     as tobranchname,
                       users.name        as transferby,
                       approver.name     as approver,
                       std.id            as detailId,
                       std.stock_from    as fromstockid,
                       std.stock_to      as tostockid,
                       p.id              as productid,
                       p.name            as productname,
                       p.description     as productdescription,
                       p.track_expire_date,
                       p.trackserialno,
                       p.barcode_office,
                       p.barcode_manufacture,
                       stb.qty,
                       b.id              as batchId,
                       b.batch_no,
                       b.expire_date
                from stock_transfers st
                         inner join locations as locationfrom on locationfrom.id = st.location_from
                         inner join branches as branchfrom on branchfrom.id = locationfrom.branchid
                         inner join locations as locationto on locationto.id = st.location_to
                         inner join branches as branchto on branchto.id = locationto.branchid
                         inner join users on users.id = st.createdby
                         left join users approver on approver.id = st.approvedby
                         inner join stock_transfer_details std on st.id = std.transferid
                         inner join stock_transfer_batches stb on stb.stdi= std.id
                         inner join batches b on stb.batch_id = b.id
                         inner join stocks on std.stock_from = stocks.id
                         inner join products p on stocks.productid = p.id
                where 1 = 1";
        if ($transferno) $sql .= " and  st.id = '$transferno'";
        if ($productid) $sql .= "  and p.id = $productid";
        if ($batchno) $sql .= " and  b.batch_no = '$batchno'";
        if ($fromlocationid) $sql .= "  and locationfrom.id = $fromlocationid";
        if ($tolocationid) $sql .= "  and locationto.id = $tolocationid";
        if ($fromdate) $sql .= "  and date_format(st.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= "  and date_format(st.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by st.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }


    static function transferInfo($transferno)
    {
        $transferBatches = StockTransfers::$transferClass->stockTransferBatchWise($transferno);
        $newArray = [];
        foreach ($transferBatches as $index => $item) {
            $newArray[$item['id']]['transferno'] = $item['id'];
            $newArray[$item['id']]['reqid'] = $item['reqid'];
            $newArray[$item['id']]['doc'] = $item['doc'];
            $newArray[$item['id']]['location_from'] = $item['location_from'];
            $newArray[$item['id']]['fromlocation'] = $item['fromlocation'];
            $newArray[$item['id']]['frombranchid'] = $item['frombranchid'];
            $newArray[$item['id']]['frombranchname'] = $item['frombranchname'];
            $newArray[$item['id']]['location_to'] = $item['location_to'];
            $newArray[$item['id']]['tolocation'] = $item['tolocation'];
            $newArray[$item['id']]['tobranchid'] = $item['tobranchid'];
            $newArray[$item['id']]['tobranchname'] = $item['tobranchname'];
            $newArray[$item['id']]['transfer_cost'] = $item['transfer_cost'];
            $newArray[$item['id']]['transferby'] = $item['transferby'];
            $newArray[$item['id']]['approver'] = $item['approver'];
            $newArray[$item['id']]['auto_approve'] = $item['auto_approve'];
            $newArray[$item['id']]['doa'] = $item['doa'];
            $newArray[$item['id']]['description'] = $item['description'];
            $newArray[$item['id']]['status'] = $item['status'];
            $newArray[$item['id']]['products'][$item['detailId']]['detailId'] = $item['detailId'];
            $newArray[$item['id']]['products'][$item['detailId']]['productid'] = $item['productid'];
            $newArray[$item['id']]['products'][$item['detailId']]['productname'] = $item['productname'];
            $newArray[$item['id']]['products'][$item['detailId']]['productdescription'] = $item['productdescription'];
            $newArray[$item['id']]['products'][$item['detailId']]['barcode_office'] = $item['barcode_office'];
            $newArray[$item['id']]['products'][$item['detailId']]['barcode_manufacture'] = $item['barcode_manufacture'];
            $newArray[$item['id']]['products'][$item['detailId']]['fromstockid'] = $item['fromstockid'];
            $newArray[$item['id']]['products'][$item['detailId']]['tostockid'] = $item['tostockid'];
            $newArray[$item['id']]['products'][$item['detailId']]['qty'] += $item['qty'];
            $newArray[$item['id']]['products'][$item['detailId']]['track_expire_date'] = $item['track_expire_date'];
            $newArray[$item['id']]['products'][$item['detailId']]['trackserialno'] = $item['trackserialno'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['batchId'] = $item['batchId'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['batch_no'] = $item['batch_no'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['qty'] = $item['qty'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['expire_date'] = $item['expire_date'];
        }
        $newArray = array_values($newArray)[0];
        foreach ($newArray['products'] as $index => $product) {
            if(!$product['trackserialno'])continue;
            $serialno_Ids = array_column(StockTransferSerials::$transferSerialClass->find(['stdi' => $product['detailId']]), 'serialno_id');
            $serialnos = SerialNos::$serialNoClass->findMany(['id' => $serialno_Ids]);
//            debug($serialnos);
            $newArray['products'][$index]['serialnos'] = $serialnos;
        }
//        debug($newArray);
        return $newArray;
    }
}

