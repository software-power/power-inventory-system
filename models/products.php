<?

class Products extends model
{
    var $table = "products";
    static $productClass = null;

    function __construct()
    {
        self::$productClass = $this;
    }


    function getList($productid = "", $search = "", $departmentid = "", $brandid = "", $taxcategory = "", $productcategoryid = "", $subcategoryid = "",
                     $unitid = "", $bulkunitid = "", $non_stock = "", $forquick_sale = false, $productid_not_in = "",$status="",$start_character="")
    {
        $sql = "select products.*,
                       products.name          as name,
                       products.id            as productid,
                       departments.name          departmentName,
                       units.name             as unitname,
                       units.abbr             as unitabbr,
                       bu.name                as bulk_unit_name,
                       bu.abbr                as bulk_unit_abbr,
                       categories.name        as categoryName,
                       categories.vat_percent as vatPercent,
                       model.name             as brandName,
                       pc.name                as productcategory,
                       ps.name                as productsubcategory
                from `products`
                         inner join departments on products.departid = departments.id
                         inner join categories on categories.id = products.categoryid
                         inner join model on model.id = products.modelid
                         left join units on units.id = products.unit
                         left join bulk_units bu on bu.id = products.bulk_units
                         left join product_categories pc on pc.id = products.productcategoryid
                         left join product_subcategories ps on ps.id = products.subcategoryid
                where 1=1";
        if ($productid) $sql .= " and products.id = $productid";
        if ($search) $sql .= " and (products.barcode_office = '" . escapeChar($search) . "' or products.barcode_manufacture = '" . escapeChar($search) . "' or products.name like '%" . escapeChar($search) . "%' or (products.generic_name <> '' and products.generic_name like '%" . escapeChar($search) . "%'))";
        if ($forquick_sale) $sql .= " and products.forquick_sale = 1";
        if ($departmentid) $sql .= " and departments.id = ".escapeChar($departmentid);
        if ($brandid) $sql .= " and model.id = ".escapeChar($brandid);
        if ($taxcategory) $sql .= " and categories.id = ".escapeChar($taxcategory);
        if ($productcategoryid) $sql .= " and pc.id = ".escapeChar($productcategoryid);
        if ($subcategoryid) $sql .= " and ps.id = ".escapeChar($subcategoryid);
        if ($unitid) $sql .= " and units.id = ".escapeChar($unitid);
        if ($bulkunitid) $sql .= " and bu.id = ".escapeChar($bulkunitid);
        if ($non_stock == 'yes') $sql .= " and products.non_stock = 1";
        if ($non_stock == 'no') $sql .= " and products.non_stock = 0";
        if ($status) $sql .= " and products.status = '" . escapeChar($status) . "'";
        if ($productid_not_in) {
            if (is_array($productid_not_in)) {
                $sql .= " and products.id not in (" . implode(",", $productid_not_in) . ")";
            } else {
                $sql .= " and products.id != " . escapeChar($productid);
            }
        }
        if($start_character){
            if($start_character=='#'){
                $sql.=" and products.name regexp '^[0-9]+'";
            }else{
                $sql.= " and products.name like '" . escapeChar($start_character) . "%'";
            }
        }

        $sql .= " order by products.id desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function search($name, $non_stock = "",$expiring="")
    {
        $sql = "select p.*,
                       p.id as productid
                from products p
                where status = 'active'
                  and (p.barcode_office = '$name' or p.barcode_manufacture = '$name' or p.name like '%$name%' or
                       (p.generic_name <> '' and p.generic_name like '%$name%'))";

        if ($non_stock == 'yes') $sql .= " and p.non_stock = 1";
        if ($non_stock == 'no') $sql .= " and p.non_stock = 0";
        if ($expiring == 'yes') $sql .= " and p.track_expire_date = 1";
        if ($expiring == 'no') $sql .= " and p.track_expire_date = 0";
        return fetchRows($sql);
    }

    function productDepartment($productId)
    {
        $sql = "select p.id,p.name as productname, d.id as departId,d.name as departname
			from products as p inner join departments as d on d.id = p.departid
			where p.status = 'active' and d.status = 'active' and p.id = $productId";
        return fetchRow($sql);
    }

    function searchResults($name = "", $department, $order = "", $limit = "")
    {
        $sql = "select * from products where status = 'active'";
        if ($name) $sql .= " and name like '%" . $name . "%'";
        if ($department == 0) {
            $sql .= "";
        } else {
            $sql .= " and departid = " . $department;
        }
        if ($order) $sql .= " order by " . $order . " desc";
        if ($limit) $sql .= " limit " . $limit;
        //echo $sql;die();
        return fetchRows($sql, $paginate = FALSE);
    }

    function byBarcode($barcode, $limit = true)
    {
        $sql = "select * from products where barcode_manufacture = '$barcode' or barcode_office = '$barcode'";
        if ($limit) {
            $sql .= "  limit 1";
            return fetchRow($sql);
        } else {
            return fetchRows($sql);
        }
    }

    function template_export()
    {
        $sql = "select p.name                  as productname,
                       p.generic_name,
                       p.description,
                       tax.name                as tax_category,
                       p.baseprice             as base_percentage,
                       departments.name        as department_name,
                       brand.name              as brand_name,
                       pc.name                 as category,
                       psc.name                as subcategory,
                       units.name              as unit_name,
                       p.barcode_manufacture   as barcode,
                       p.barcode_office        as other_barcode,
                       p.forquick_sale         as show_in_quick_sale,
                       p.non_stock,
                       p.trackserialno         as track_serialno,
                       p.validate_serialno,
                       p.track_expire_date,
                       p.expiry_notification   as get_expiry_notification,
                       p.notify_before_days,
                       p.prescription_required as require_prescription,
                       p.reorder_level,
                       p.points                as product_point
                from products p
                         inner join categories as tax on p.categoryid = tax.id
                         inner join model as brand on p.modelid = brand.id
                         inner join departments on p.departid = departments.id
                         inner join product_categories pc on p.productcategoryid = pc.id
                         left join product_subcategories psc on p.subcategoryid = psc.id
                         inner join units on p.unit = units.id
                limit 4";
         debug($sql);
    }

    function getStockProduct_details($stockid, $locationid, $productId)
    {
        $sql = "select s.id,
                       s.productid,
                       s.locid,
                       p.id          as proid,
                       p.name        as productname,
                       p.trackserialno,
                       p.validate_serialno,
                       p.track_expire_date,
                       p.prescription_required,
                       p.baseprice,
                       p.description,
                       p.forquick_sale,
                       p.point,
                       p.unit,
                       p.barcode_manufacture,
                       p.barcode_office,
                       c.name        as categoryname,
                       c.vat_percent as vat_rate,
                       bu.name       as bulkname,
                       bu.rate       as bulk_rate,
                       units.name    as unitname,
                       units.abbr    as unitabbr
                from stocks as s
                         inner join products as p on p.id = s.productid
                         inner join categories as c on c.id = p.categoryid
                         left join bulk_units bu on p.bulk_units = bu.id
                         left join units on units.id = p.unit
                where 1 = 1";
        if ($stockid) $sql .= " and s.id = $stockid";
        if ($productId) $sql .= " and p.id = $productId";
        if ($locationid) $sql .= " and s.locid = $locationid";
//         debug($sql);
        return fetchRow($sql);
    }

    function getProductDetails($productid = "")
    {
        $sql = "select p.id          as proid,
                       p.name        as productname,
                       p.non_stock,
                       p.trackserialno,
                       p.baseprice,
                       p.forquick_sale,
                       p.point,
                       p.unit,
                       p.bulk_units,
                       p.barcode_manufacture,
                       p.barcode_office,
                       p.description,
                       p.prescription_required,
                       p.track_expire_date,
                       p.generic_name,
                       p.image_path,
                       d.name        as departname,
                       m.name        as brand,
                       c.name        as categoryname,
                       c.vat_percent as vat_rate,
                       pc.name       as productcategoryname,
                       psc.name      as subcategoryname,
                       units.name    as unitname,
                       bu.name       as bulk_unit_name
                from products as p
                         inner join departments as d on d.id = p.departid
                         inner join categories as c on c.id = p.categoryid
                         inner join product_categories as pc on pc.id = p.productcategoryid
                         inner join product_subcategories as psc on psc.id = p.subcategoryid
                         inner join model as m on m.id = p.modelid
                         left join units on p.unit = units.id
                         left join bulk_units bu on p.bulk_units = bu.id
                where 1 = 1";
        if ($productid) {
            $sql .= " and p.id = $productid";
            return fetchRow($sql);
        }
        //echo $sql;die();
        return fetchRows($sql);
    }

    function forExpireNotifications()
    {
        $sql = "select * from products where status = 'active' and track_expire_date = 1 and expiry_notification = 1";
//        debug($sql);
        return fetchRows($sql);
    }

    function forStockNotifications()
    {
        $sql = "select * from products where status = 'active' and reorder_level = 1";
//        debug($sql);
        return fetchRows($sql);
    }

    function reorderList($stockid = "", $productid = "", $locationid = "",$productcategoryid="",$subcategoryid="",$brandid="")
    {
        $defaultMin = CS_REORDER_DEFAULT_MIN;
        $defaultMax = CS_REORDER_DEFAULT_MAX;
        $sql = "select p.id          as proid,
                       p.name        as productname,
                       p.description,
                       p.generic_name,
                       p.barcode_office,
                       p.barcode_manufacture,
                       stocks.id as stockid,
                       locations.id as locid,
                       locations.name as locname,
                       IF(rl.id is null,'default','entered') as source,
                       IFNULL(rl.minqty,$defaultMin) as minqty,
                       IFNULL(rl.maxqty,$defaultMax) as maxqty
                from products as p
                         inner join stocks on stocks.productid = p.id
                         inner join locations on locations.id = stocks.locid
                         left join reorder_level rl on rl.stockid= stocks.id
                where p.status = 'active' and p.reorder_level = 1";
        if ($stockid) $sql .= " and stocks.id = $stockid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($locationid) $sql .= " and locations.id = $locationid";
        if ($productcategoryid) $sql .= " and p.productcategoryid = $productcategoryid";
        if ($subcategoryid) $sql .= " and p.subcategoryid = $subcategoryid";
        if ($brandid) $sql .= " and p.modelid = $brandid";
//        debug($sql);
        return fetchRows($sql);
    }

    function getPrices($branchid, $level, $productId)
    {
        $sqlOne = "select p.id                                                                     as productid,
                          p.name                                                                   as productname,
                          p.baseprice,
                          h.name                                                                   as hierarchicname,
                          h.level,
                          b.id                                                                     as branchid,
                          b.name                                                                   as branchname,
                          IFNULL(hp.percentage, h.percentage)                                      as percentage,
                          IF(hp.percentage is null, 'default', 'entered')                          as source,
                          cp.costprice,
                          cp.quicksale_price,
                          cp.quick_price_inc,
                          round(cp.costprice * (1 + p.baseprice / 100), 2)                         as minprice,
                          round(cp.costprice * (1 + IFNULL(hp.percentage, h.percentage) / 100), 2) as levelprice
                   from products p
                             left join current_prices cp on p.id = cp.productid and cp.branchid = $branchid
                             left join branches b on b.id = cp.branchid
                             left join hierarchics h on 1 = 1
                             left join hierarchicprices hp on hp.productid = p.id and hp.hierachicid = h.id and hp.branchid = b.id and hp.branchid = $branchid
                    where 1 = 1 and h.level >= $level and p.id = $productId having percentage >= p.baseprice";
        $sqlOne .= " order by h.level asc";
        $prices = [];
        $product = Products::$productClass->getList($productId)[0];
        if ($result = fetchRows($sqlOne)) { //if prices found above base percentage
//            debug($result);
            $prices['branchid'] = $result[0]['branchid'];
            $prices['branchname'] = $result[0]['branchname'];
            $prices['costprice'] = $result[0]['costprice'];
            $prices['quick_price_inc'] = $result[0]['quick_price_inc'];
            $prices['basepercentage'] = $result[0]['baseprice'];

            $prices['minimum'] = IS_ADMIN ? 0 : $result[0]['levelprice'];

            $highestPrice = (float)$result[count($result) - 1]['levelprice'];
            $highestPrice = $highestPrice ?: $prices['minimum'];
            $prices['maximum'] = $prices['suggested'] = $highestPrice;

        } else { // if no price found >= base percentage
            $branch = Branches::$branchClass->get($branchid);
            $productPrice = CurrentPrices::$currentPricesClass->find(['branchid' => $branchid, 'productid' => $productId])[0];

            $prices['branchid'] = $branchid;
            $prices['branchname'] = $branch['name'];
            $prices['costprice'] = $productPrice['costprice'];
            $prices['quick_price_inc'] = $productPrice['quick_price_inc'];
            $prices['basepercentage'] = $product['baseprice'];
            $prices['minimum'] = IS_ADMIN ? 0 : round($productPrice['costprice'] * (1 + $product['baseprice'] / 100), 2);  //base price
            $prices['maximum'] = $prices['suggested'] = round($productPrice['costprice'] * (1 + $product['baseprice'] / 100), 2);  //base price
        }

        $max_discount = $prices['maximum'] <= 0 ? 0 : $prices['maximum'] - $prices['minimum'];
        $prices['max_discount_percent'] = $max_discount <= 0 ? 0 : round(($max_discount / $prices['maximum']) * 100, 2);

        //quick price
        if ($prices['quick_price_inc'] <= 0) $prices['quick_price_inc'] = addTAX($prices['maximum'], $product['vatPercent']);

        $incminimum = addTAX($prices['minimum'], $product['vatPercent']);
        $max_quicksale_disc = $prices['quick_price_inc'] <= $incminimum ? 0 : $prices['quick_price_inc'] - $incminimum;
        $prices['max_quicksale_disc_percent'] = $max_quicksale_disc <= 0 ? 0 : round(($max_quicksale_disc / $prices['quick_price_inc']) * 100, 2);

//        debug($prices);
        return $prices;
    }

    function maxID()
    {
        $sql = "SELECT MAX(id) AS maxID FROM products";
        return fetchRow($sql);
    }

    static function generateBarcode()
    {
        $maxID = Products::$productClass->maxID();
        return date('Ymd') . str_pad($maxID['maxID'] + 1, 6, "0", STR_PAD_LEFT);
    }

    static function getAllStock($productid)
    {
        $locationStocks = [];
        foreach (Locations::$locationClass->getAll() as $index => $location) {
            $stockBatches = Stocks::$stockClass->calcStock($location['id'], "", "",
                "", $productid, "", "", "", "",
                "", "", "", "", "", "", "",
                "", true, true, "", "", true);
            $stockBatches = array_values($stockBatches)[0];
            $locationStocks[] = [
                'locationid' => $location['id'],
                'locationname' => $location['name'],
                'batches' => $stockBatches['batches'],
            ];
        }

//        debug($locationStocks);
        return $locationStocks;
    }
}

