<?

/**
 * model for hierarchicprices
 */
class HierarchicPrices extends model
{
    var $table = 'hierarchicprices';
    static $hierarchicClass = null;

    function __construct()
    {
        self::$hierarchicClass = $this;
    }
    function getProductInfoWithPrices($branchid, $productid = "", $hierarchicId = "",$level="", $modelid = "", $productcategoryid = "", $subcategoryid = "", $departid = "")
    {
        $sql = "select p.id                                                                                                                    as productid,
                       p.name                                                                                                                  as productname,
                       p.baseprice,
                       cp.id                                                                                                                   as priceid,
                       cp.costprice,
                       round(cp.costprice * (1 + p.baseprice / 100), 2)                                                                        as exc_base,
                       round(round(cp.costprice * (1 + p.baseprice / 100), 2) * (1 + categories.vat_percent / 100), 2)                         as inc_base,
                       cp.quicksale_price,
                       cp.quick_price_inc                                                                                                      as inc_quicksale_price,
                       b.id                                                                                                                    as branchid,
                       b.name                                                                                                                  as branchname,
                       model.name                                                                                                              as brandName,
                       pc.name                                                                                                                 as productcategoryname,
                       ps.name                                                                                                                 as productsubcategoryname,
                       categories.name                                                                                                         as taxCategory,
                       departments.name                                                                                                        as departmentName,
                       categories.vat_percent,
                       h.id                                                                                                                    as hierarchicId,
                       h.name                                                                                                                  as hierarchicname,
                       h.level,
                       hp.id                                                                                                                   as hpi,
                       IFNULL(hp.percentage, h.percentage)                                                                                     as percentage,
                       IFNULL(hp.commission, h.commission)                                                                                     as commission,
                       IFNULL(hp.target, h.target)                                                                                             as target,
                       round(cp.costprice * (1 + IFNULL(hp.percentage, h.percentage) / 100), 2)                                                as exc_price,
                       round(round(cp.costprice * (1 + IFNULL(hp.percentage, h.percentage) / 100), 2) * (1 + categories.vat_percent / 100), 2) as inc_price,
                       IF(hp.percentage is null, 'default', 'entered')                                                                         as source,
                       IFNULL(hp.percentage, h.percentage) < p.baseprice                                                                       as below_base
                from products p
                         inner join model on p.modelid = model.id
                         inner join departments on p.departid = departments.id
                         inner join product_categories pc on p.productcategoryid = pc.id
                         inner join product_subcategories ps on p.subcategoryid = ps.id
                         inner join categories on p.categoryid = categories.id
                         left join current_prices cp on p.id = cp.productid and cp.branchid = $branchid
                         left join branches b on b.id = cp.branchid
                         left join hierarchics h on 1 = 1
                         left join hierarchicprices hp on hp.productid = p.id and hp.hierachicid = h.id and hp.branchid = b.id and hp.branchid = $branchid
                where 1 = 1";
        if ($productid) $sql .= " and p.id = $productid";
        if ($hierarchicId) $sql .= " and h.id = $hierarchicId";
        if ($level) $sql .= " and h.level >= $level";
        if ($modelid) $sql .= " and p.modelid = $modelid";
        if ($productcategoryid) $sql .= " and p.productcategoryid = $productcategoryid";
        if ($subcategoryid) $sql .= " and p.subcategoryid = $subcategoryid";
        if ($departid) $sql .= " and p.departid = $departid";
        $sql .= " order by p.id,h.level";
//        debug($sql);
        return fetchRows($sql);
    }

    function getProductPriceExport($branchid, $productid = "",$level="", $modelid = "", $productcategoryid = "", $subcategoryid = "", $departid = "")
    {
        $sql = "select p.id                                                                                                                    as productid,
                       p.name                                                                                                                  as productname,
                       p.generic_name,
                       p.description                                                                                                           as productdescription,
                       p.baseprice,
                       p.barcode_office,
                       p.barcode_manufacture,
                       p.baseprice,
                       cp.id                                                                                                                   as priceid,
                       cp.costprice,
                       round(cp.costprice * (1 + p.baseprice / 100), 2)                                                                        as exc_base,
                       round(round(cp.costprice * (1 + p.baseprice / 100), 2) * (1 + categories.vat_percent / 100), 2)                         as inc_base,
                       cp.quick_price_inc                                                                                                      as inc_quicksale_price,
                       round(cp.quick_price_inc /(1+ categories.vat_percent / 100),2)                                                          as exc_quicksale_price,
                       b.id                                                                                                                    as branchid,
                       b.name                                                                                                                  as branchname,
                       model.name                                                                                                              as brandname,
                       pc.name                                                                                                                 as productcategoryname,
                       ps.name                                                                                                                 as productsubcategoryname,
                       units.name                                                                                                              as unitname,
                       categories.name                                                                                                         as taxcategory,
                       categories.vat_percent,
                       departments.name                                                                                                        as departmentname,
                       h.name                                                                                                                  as hierarchicname,
                       IFNULL(hp.percentage, h.percentage)                                                                                     as percentage,
                       round(cp.costprice * (1 + IFNULL(hp.percentage, h.percentage) / 100), 2)                                                as exc_price,
                       round(round(cp.costprice * (1 + IFNULL(hp.percentage, h.percentage) / 100), 2) * (1 + categories.vat_percent / 100), 2) as inc_price,
                       h.id is null                                                                                                            as below_base
                from products p
                         inner join model on p.modelid = model.id
                         inner join departments on p.departid = departments.id
                         inner join product_categories pc on p.productcategoryid = pc.id
                         inner join product_subcategories ps on p.subcategoryid = ps.id
                         inner join categories on p.categoryid = categories.id
                         inner join units on p.unit = units.id
                         left join current_prices cp on p.id = cp.productid and cp.branchid = $branchid
                         left join branches b on b.id = cp.branchid
                         left join hierarchics h on h.id = (select ha.id from hierarchics ha where ha.level >= $level and ha.percentage >= p.baseprice limit 1)
                         left join hierarchicprices hp on hp.productid = p.id and hp.hierachicid = h.id and hp.branchid = b.id and hp.branchid = $branchid
                where 1 = 1 and cp.id is not null";
        if ($productid) $sql .= " and p.id = $productid";
        if ($modelid) $sql .= " and p.modelid = $modelid";
        if ($productcategoryid) $sql .= " and p.productcategoryid = $productcategoryid";
        if ($subcategoryid) $sql .= " and p.subcategoryid = $subcategoryid";
        if ($departid) $sql .= " and p.departid = $departid";
        $sql .= " order by p.id,h.level";
//        debug($sql);
        return fetchRows($sql);
    }

}
