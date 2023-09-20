<?


class Batches extends model
{
    var $table = "batches";

    static $batchesClass = null;

    function __construct()
    {
        self::$batchesClass = $this;
    }

    function getList($grnid = "", $supplierid = "", $productid = '', $fromdate = "", $todate = "", $locationid = "", $branchid = "")
    {
        $sql = "select grn.id          as grnid,
                       grn.locid,
                       grn.supplierid,
                       grn.createdby,
                       grn.approvedby,
                       grn.approval_date,
                       grn.auto_approve,
                       grn.status,
                       grn.doc,
                       grn.id          as grnno,
                       l.name          as locationname,
                       b.name          as branchname,
                       grn.id          as grnno,
                       currencies.name as currencyname,
                       suppliers.name  as suppliername,
                       products.id     as productid,
                       products.name   as productname,
                       products.barcode_manufacture,
                       products.barcode_office,
                       gd.id           as gdi,
                       gd.stockid,
                       gd.rate         as price,
                       gd.qty          as quantity,
                       batches.id      as batchid,
                       batches.qty     as batchqty,
                       batches.expire_date
                from `grn`
                         inner join grndetails as gd on gd.grnid = grn.id
                         inner join suppliers on suppliers.id = grn.supplierid
                         inner join currencies_rates cr on cr.id = grn.currency_rateid
                         inner join currencies on currencies.id = cr.currencyid
                         inner join stocks on stocks.id = gd.stockid
                         inner join products on stocks.productid = products.id
                         inner join locations l on l.id = grn.locid
                         inner join branches b on b.id = l.branchid
                         inner join batches on batches.gdi = gd.id
                where 1 = 1";
        if ($productid) $sql .= " and products.id = $productid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($grnid) $sql .= " and grn.id = $grnid";
        if ($supplierid) $sql .= " and suppliers.id = $supplierid";
        if ($fromdate) $sql .= " and date_format(grn.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(grn.doc,'%Y-%m-%d') <= '$todate'";

//        debug($sql);
        return fetchRows($sql);
    }

    static function generateBatchNo($tracking = true, $manufacture = false)
    {
        $result = Batches::$batchesClass->maxID();
        $prefix = $tracking ? 'B-' : 'BN';
        if ($manufacture) $prefix = "M" . $prefix;
        return $prefix . date('Y') . '-' . str_pad($result['maxId'] + 1, 3, 0, STR_PAD_LEFT);

    }

    function batchReceiveHistory($locationid, $batchid, $ranges = [])
    {

//            debug($ranges);
        if (is_array($ranges)) {
            $ranges = array_unique($ranges);
            sort($ranges);
            $lastElement = end($ranges);
            $age_ranges = "case";
            foreach ($ranges as $index => $day) {
                if ($index == 0) {
                    $age_ranges .= " when datediff(curdate(), max(receive_date)) <= {$ranges[0]} then '<= $ranges[0]'" . PHP_EOL;
                }
                if ($lastElement == $day) {
                    $age_ranges .= " else '$day >'" . PHP_EOL;
                }
                if (($index == 0 && $lastElement != $day) or ($index != 0 && $lastElement != $day)) {
                    $next_day = $ranges[$index + 1];
                    $age_ranges .= " when datediff(curdate(), max(receive_date)) > $day and datediff(curdate(), max(receive_date)) <= $next_day then '$day - $next_day'" . PHP_EOL;
                }
            }
            $age_ranges .= " end as in_stock";
//            debug($age_ranges);
        } else {
            $age_ranges = "case
                           when datediff(curdate(), max(receive_date)) <= 30 then '< 30'
                           when datediff(curdate(), max(receive_date)) > 30 and datediff(curdate(), max(receive_date)) <= 60 then '30 - 60'
                           when datediff(curdate(), max(receive_date)) > 60 and datediff(curdate(), max(receive_date)) <= 100 then '60 - 100'
                           else '100 >'
                           end           as in_stock";
        }

        $batchid = is_array($batchid) ? " and b.id in (" . implode(',', $batchid) . ")" : " and b.id = $batchid";

        $sql = "select batchid,
                       max(receive_date) as receive_date,
                       $age_ranges
                from (
                         select b.id                             as batchid,
                                date_format(grn.approval_date, '%Y-%m-%d') as receive_date
                         from batches b
                                  inner join grndetails gd on gd.id = b.gdi
                                  inner join grn on gd.grnid = grn.id
                         where grn.approvedby > 0
                           and grn.locid = $locationid $batchid
                         union all
                         select b.id                            as batchid,
                                date_format(st.doa, '%Y-%m-%d') as receive_date
                         from batches b
                                  inner join stock_transfer_batches stb on b.id = stb.batch_id
                                  inner join stock_transfer_details std on stb.stdi = std.id
                                  inner join stock_transfers st on std.transferid = st.id
                         where st.approvedby > 0
                           and st.location_to = $locationid $batchid
                     ) as receive_history
                group by batchid";
//        debug($sql);
        return fetchRows($sql);
    }
}