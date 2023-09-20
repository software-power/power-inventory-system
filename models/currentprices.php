<?


class CurrentPrices extends model
{
    var $table = 'current_prices';

    static $currentPricesClass = null;

    function __construct()
    {
        self::$currentPricesClass = $this;
    }

    function quickPriceList($branchid, $productid = "", $modelid = "", $productcategoryid = "", $subcategoryid = "", $departid = "")
    {
        $sql = "select p.id                                                                                            as productid,
                       p.name                                                                                          as productname,
                       p.generic_name,
                       p.barcode_manufacture,
                       p.barcode_office,
                       b.id                                                                                            as branchid,
                       b.name                                                                                          as branchname,
                       pc.name                                                                                         as productcategory,
                       categories.name                                                                                 as taxcategory,
                       categories.vat_percent                                                                          as vat_rate,
                       cp.quicksale_price,
                       p.baseprice,
                       cp.costprice,
                       round(cp.costprice * (1 + p.baseprice / 100), 2)                                                as exc_base,
                       round(round(cp.costprice * (1 + p.baseprice / 100), 2) * (1 + categories.vat_percent / 100), 2) as inc_base,
                       cp.quick_price_inc                                                                              as inc_quicksale_price
                from products p
                         inner join categories on p.categoryid = categories.id
                         inner join product_categories pc on p.productcategoryid = pc.id
                         left join current_prices cp on p.id = cp.productid and cp.branchid = $branchid
                         left join branches b on b.id = cp.branchid
                where 1 = 1";
        if ($productid) $sql .= " and p.id = $productid";
        if ($modelid) $sql .= " and p.modelid = $modelid";
        if ($productcategoryid) $sql .= " and p.productcategoryid = $productcategoryid";
        if ($subcategoryid) $sql .= " and p.subcategoryid = $subcategoryid";
        if ($departid) $sql .= " and p.departid = $departid";
        return fetchRows($sql);
    }


    static function updatePrice($branchid, $productid, $costprice = 0, $quick_price_inc = 0.0, $gdi = 0, $remark = "Price update", $override = false)
    {
        $result ["status"] = "success";
        try {
            $currentPrice = CurrentPrices::$currentPricesClass->find(['branchid' => $branchid, 'productid' => $productid])[0];
            if (!$currentPrice && !$override) throw new Exception("Product Cost price not set in current branch!");
            $product = Products::$productClass->get($productid);
            if (!$costprice || $costprice <= 0) $costprice = $currentPrice['costprice'];
            if (!$quick_price_inc || $quick_price_inc <= 0) $quick_price_inc = $currentPrice['quick_price_inc'];


            PriceLogs::$priceLogsClass->insert([
                'branchid' => $branchid,
                'productid' => $productid,
                'costprice' => $costprice,
                'quick_price_inc' => $quick_price_inc,
                'createdby' => $_SESSION['member']['id'],
                'gdi' => $gdi,
                'remarks' => $remark
            ]);
            $logid = PriceLogs::$priceLogsClass->lastId();
            $priceData = [
                'branchid' => $branchid,
                'productid' => $productid,
                'costprice' => $costprice,
                'quick_price_inc' => $quick_price_inc,
                'createdby' => $_SESSION['member']['id'],
                'logid' => $logid
            ];
            if ($currentPrice) {
                CurrentPrices::$currentPricesClass->update($currentPrice['id'], $priceData);
            } else {
                CurrentPrices::$currentPricesClass->insert($priceData);
            }
            return $result;
        } catch (Exception $e) {
            return [
                "status" => "error",
                'msg' => $e->getMessage()
            ];
        }
    }
}