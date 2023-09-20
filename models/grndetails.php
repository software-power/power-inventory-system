<?

class GRNDetails extends model
{
    var $table = "grndetails";

    static $grnDetailsClass = null;

    function __construct()
    {
        self::$grnDetailsClass = $this;
    }

    function getList($grnId = "", $productid = "")
    {
        $sql = "select gd.*,
                       p.id                                                                as productid,
                       p.name                                                              as productname,
                       p.description                                                       as productdescription,
                       p.track_expire_date,
                       p.barcode_office,
                       p.barcode_manufacture,
                       round(gd.rate * gd.billable_qty, 2)                                 as excamount,
                       round(gd.rate * gd.billable_qty * (gd.vat_percentage / 100), 2)     as vatamount,
                       round(gd.rate * gd.billable_qty * (1 + gd.vat_percentage / 100), 2) as incamount
                from grndetails as gd
                         inner join stocks as st on st.id = gd.stockid
                         inner join products as p on p.id = st.productid
                where 1 = 1";
        if ($grnId) $sql .= " and gd.grnid =" . $grnId;
        if ($productid) $sql .= " and p.id = " . $productid;
        //echo $sql;die();
        return fetchRows($sql);
    }

    function getBranchLastPurchase($branchid = "", $productid = "")
    {
        $sql = "select p.id,
                       p.name       as productname,
                       b.id         as branchid,
                       b.name       as branchname,
                       max(grn.doc) as last_purchasedate
                from grn
                         inner join locations l on l.id = grn.locid
                         inner join branches b on b.id = l.branchid
                         inner join grndetails gd on gd.grnid = grn.id
                         inner join stocks s on s.id = gd.stockid
                         inner join products p on p.id = s.productid
                where grn.approvedby > 0";
        if($branchid)$sql.=" and b.id = $branchid";
        if($productid)$sql.=" and p.id = $productid";
        $sql.=" group by b.id,p.id";
        return fetchRows($sql);
    }
}
