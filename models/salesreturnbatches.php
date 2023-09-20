<?


class SalesReturnBatches extends model
{
    var $table = "sales_return_batches";
    static $saleReturnBatchesClass = null;

    function __construct()
    {
        self::$saleReturnBatchesClass = $this;
    }

    function getList($srdid)
    {
        $sql = "select srb.*,
                       b.batch_no,
                       b.expire_date
                from sales_return_batches srb
                         inner join batches b on srb.batchid = b.id
                where 1 = 1 and srb.srdid = $srdid";

        return fetchRows($sql);
    }

    function previousReturn($batchid, $sdi = "")
    {
        $sql = "select srb.*
                from sales_return_batches srb
                         inner join sales_return_details srd on srd.id = srb.srdid
                         inner join sales_returns sr on sr.id = srd.srid
                where sr.approvedby > 0 and (sr.type = 'full' or sr.type = 'item') and srb.batchid = $batchid  and srd.sdi = $sdi";
//        debug($sql);
        return fetchRows($sql);
    }
}