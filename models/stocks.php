<?

class Stocks extends model
{
    var $table = "stocks";
    static $stockClass = null;

    function __construct()
    {
        self::$stockClass = $this;
    }

    function getProduct($stockid)
    {
        $sql = "select p.*, s.id as stockid from products as p inner join stocks s on s.productid = p.id where s.id = $stockid";
        return fetchRow($sql);
    }

    function calcStock($locid = "", $stockid = "", $stkdate = "", $productname = "", $prodid = "", $office_barcode = "", $manufacture_barcode = "",
                       $purpose = "", $sort = "", $categories = "", $brands = "", $depart = "", $genericname = "", $batchid = "", $batchno = "",
                       $expirebefore = "", $expireafter = "", $with_expired = true, $group_batch = false, $productcategoryid = "", $subcategoryid = "",
                       $with_stock = true, $with_stock_holding = false, $trackserialno = '')
    {
        if (empty($locid)) {
            $locid = "''";
        }
//        if ($stkdate) debug($stkdate);

        if (!$stkdate) $stkdate = date('Y-m-d');
        $date_format = DateTime::createFromFormat("Y-m-d", $stkdate) !== false ? '%Y-%m-%d' : '%Y-%m-%d %H:%i:%s';

        //global filters
        $productName = !empty($productname) ? " and (p.name like '%$productname%' or (p.generic_name <> '' and p.generic_name like '%$productname%') or p.barcode_office = '$productname' or p.barcode_manufacture = '$productname')" : "";
        $genericName = !empty($genericname) ? " and (p.generic_name <> '' and p.generic_name like '%$genericname%')" : "";
        $category = !empty($categories) ? " and categories.id = " . $categories : "";
        $productcategory = !empty($productcategoryid) ? " and p.productcategoryid = " . $productcategoryid : "";
        $productsubcategory = !empty($subcategoryid) ? " and p.subcategoryid = " . $subcategoryid : "";
        $department = !empty($depart) ? " and departments.id = " . $depart : "";
        $model = !empty($brands) ? " and model.id = " . $brands : "";
        $product = !empty($prodid) ? " and s.productid = " . $prodid : "";
        $officeBarcode = !empty($office_barcode) ? " and p.barcode_office = '$office_barcode'" : "";
        $manufactureBarcode = !empty($manufacture_barcode) ? " and p.barcode_manufacture = '$manufacture_barcode'" : "";
        $quickSale = !empty($purpose) ? " and p.forquick_sale = $purpose" : "";
        $stock = !empty($stockid) ? " and s.id = " . $stockid : "";
        $batch = !empty($batchid) ? " and b.id = " . $batchid : "";
        $batchNumber = !empty($batchno) ? " and b.batch_no = '$batchno'" : "";
        if ($trackserialno == 'yes') {
            $trackSerialno = " and p.trackserialno = 1";
        } elseif ($trackserialno == 'no') {
            $trackSerialno = " and p.trackserialno = 0";
        } else {
            $trackSerialno = '';
        }

        $globalFilter = "$productName $genericName $category $productcategory $productsubcategory $department $model $product $officeBarcode 
              $manufactureBarcode $quickSale $stock $batch $batchNumber $trackSerialno";
        //grn sql
        $grnSql = "select s.id,
                 b.id             as batchId,
                 b.batch_no       as batch_no,
                 b.expire_date,
                 units.name       as unitName,
                 s.productid,
                 categories.name  as catName,
                 categories.id    as catID,
                 pc.id            as productcategoryid,
                 pc.name          as productcategoryname,
                 psc.id           as subcategoryid,
                 psc.name         as subcategoryname,
                 model.name       as brandName,
                 model.id         as modelID,
                 departments.name as departName,
                 departments.id   as departID,
                 p.name,
                 p.generic_name,
                 p.barcode_office,
                 p.barcode_manufacture,
                 p.forquick_sale,
                 p.description,
                 p.prescription_required,
                 p.track_expire_date,
                 p.trackserialno,
                 sum(b.qty)       as total
          from grn as g
                   inner join grndetails as gd on g.id = gd.grnid
                   inner join batches as b on b.gdi = gd.id
                   inner join stocks as s on s.id = gd.stockid
                   inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                   inner join categories on p.categoryid = categories.id
                   inner join product_categories pc on p.productcategoryid = pc.id
                   inner join product_subcategories psc on p.subcategoryid = psc.id
                   inner join model on p.modelid = model.id
                   inner join units on p.unit = units.id
                   inner join departments on p.departid = departments.id
          where 1=1 and g.approvedby is not null and date_format(g.doc,'$date_format') <= '$stkdate' and g.locid = $locid 
              $globalFilter
          group by batchId";

        //grn return sql
        $grnReturnSql = "select s.id,
                 b.id               as batchId,
                 b.batch_no         as batch_no,
                 b.expire_date,
                 units.name         as unitName,
                 s.productid,
                 categories.name    as catName,
                 categories.id      as catID,
                 pc.id              as productcategoryid,
                 pc.name            as productcategoryname,
                 psc.id             as subcategoryid,
                 psc.name           as subcategoryname,
                 model.name         as brandName,
                 model.id           as modelID,
                 departments.name   as departName,
                 departments.id     as departID,
                 p.name,
                 p.generic_name,
                 p.barcode_office,
                 p.barcode_manufacture,
                 p.forquick_sale,
                 p.description,
                 p.prescription_required,
                 p.track_expire_date,
                 p.trackserialno,
                 -sum(grb.qty) as total
          from grn_returns as gr
                   inner join grnreturn_details as grd ON grd.returnid = gr.id
                   inner join grn_return_batches as grb ON grd.id = grb.grdi
                   inner join batches as b on grb.batch_id = b.id
                   inner join stocks as s on s.id = grd.stockid
                   inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                   inner join categories on p.categoryid = categories.id
                   inner join product_categories pc on p.productcategoryid = pc.id
                   inner join product_subcategories psc on p.subcategoryid = psc.id
                   inner join model on p.modelid = model.id
                   inner join units on p.unit = units.id
                   inner join departments on p.departid = departments.id
          where date_format(gr.doc,'%Y-%m-%d') <= '$stkdate' and gr.locid = $locid
              $globalFilter
          group by batchId";

        //sales sql
        $saleSql = "select s.id,
                 b.id              as batchId,
                 b.batch_no        as batch_no,
                 b.expire_date,
                 units.name        as unitName,
                 s.productid,
                 categories.name   as catName,
                 categories.id     as catID,
                 pc.id             as productcategoryid,
                 pc.name           as productcategoryname,
                 psc.id            as subcategoryid,
                 psc.name          as subcategoryname,
                 model.name        as brandName,
                 model.id          as modelID,
                 departments.name  as departName,
                 departments.id    as departID,
                 p.name,
                 p.generic_name,
                 p.barcode_office,
                 p.barcode_manufacture,
                 p.forquick_sale,
                 p.description,
                 p.prescription_required,
                 p.track_expire_date,
                 p.trackserialno,
                 -sum(sb.qty) as total
          from sales sa
                   inner join salesdetails as sd on sd.salesid = sa.id
                   inner join salesbatches as sb on sd.id = sb.sdi
                   inner join batches as b on sb.batch_id = b.id
                   inner join stocks as s on s.id = sd.stockid
                   inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                   inner join categories on p.categoryid = categories.id
                   inner join product_categories pc on p.productcategoryid = pc.id
                   inner join product_subcategories psc on p.subcategoryid = psc.id
                   inner join model on p.modelid = model.id
                   inner join departments on p.departid = departments.id
                   inner join units on p.unit = units.id
          where sa.iscreditapproved = 1 and date_format(sa.doc,'%Y-%m-%d') <= '$stkdate' and sa.locationid = $locid
              $globalFilter
          group by batchId";

        //stock transfer out sql
        $transferOutSql = "select s.id,
                 b.id               as batchId,
                 b.batch_no         as batch_no,
                 b.expire_date,
                 units.name         as unitName,
                 s.productid,
                 categories.name    as catName,
                 categories.id      as catID,
                 pc.id              as productcategoryid,
                 pc.name            as productcategoryname,
                 psc.id             as subcategoryid,
                 psc.name           as subcategoryname,
                 model.name         as brandName,
                 model.id           as modelID,
                 departments.name   as departName,
                 departments.id     as departID,
                 p.name,
                 p.generic_name,
                 p.barcode_office,
                 p.barcode_manufacture,
                 p.forquick_sale,
                 p.description,
                 p.prescription_required,
                 p.track_expire_date,
                 p.trackserialno,
                 -sum(stb.qty) as total
          from stock_transfers st
                   inner join stock_transfer_details as std on std.transferid = st.id
                   inner join stock_transfer_batches as stb on std.id = stb.stdi
                   inner join batches as b on stb.batch_id = b.id
                   inner join stocks as s on s.id = std.stock_from
                   inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                   inner join categories on p.categoryid = categories.id
                   inner join product_categories pc on p.productcategoryid = pc.id
                   inner join product_subcategories psc on p.subcategoryid = psc.id
                   inner join model on p.modelid = model.id
                   inner join departments on p.departid = departments.id
                   inner join units on p.unit = units.id
          where st.approvedby is not null and date_format(st.doc,'%Y-%m-%d') <= '$stkdate' and st.location_from = $locid
               $globalFilter
          group by batchId";

        //stock transfer in sql
        $transferInSql = "select s.id,
                 b.id                as batchId,
                 b.batch_no          as batch_no,
                 b.expire_date,                 
                 units.name          as unitName,
                 s.productid,
                 categories.name     as catName,
                 categories.id       as catID,
                 pc.id               as productcategoryid,
                 pc.name             as productcategoryname,
                 psc.id              as subcategoryid,
                 psc.name            as subcategoryname,
                 model.name          as brandName,
                 model.id            as modelID,
                 departments.name    as departName,
                 departments.id      as departID,
                 p.name,
                 p.generic_name,
                 p.barcode_office,
                 p.barcode_manufacture,
                 p.forquick_sale,
                 p.description,
                 p.prescription_required,
                 p.track_expire_date,
                 p.trackserialno,
                 sum(stb.qty) as total
          from stock_transfers stin
                   inner join stock_transfer_details as stdin on stdin.transferid = stin.id
                   inner join stock_transfer_batches as stb on stdin.id = stb.stdi
                   inner join batches as b on stb.batch_id = b.id
                   inner join stocks as s on s.id = stdin.stock_to
                   inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                   inner join categories on p.categoryid = categories.id
                   inner join product_categories pc on p.productcategoryid = pc.id
                   inner join product_subcategories psc on p.subcategoryid = psc.id
                   inner join model on p.modelid = model.id
                   inner join departments on p.departid = departments.id
                   inner join units on p.unit = units.id
          where stin.approvedby is not null and  date_format(stin.doc,'%Y-%m-%d') <= '$stkdate' and stin.location_to = $locid
              $globalFilter
          group by batchId";

        //stock adjustment sql
        $adjustments = "select s.id,
                           b.id                                           as batchId,
                           b.batch_no                                     as batch_no,
                           b.expire_date,
                           units.name                                     as unitName,
                           s.productid,
                           categories.name                                as catName,
                           categories.id                                  as catID,
                           pc.id                                          as productcategoryid,
                           pc.name                                        as productcategoryname,
                           psc.id                                         as subcategoryid,
                           psc.name                                       as subcategoryname,
                           model.name                                     as brandName,
                           model.id                                       as modelID,
                           departments.name                               as departName,
                           departments.id                                 as departID,
                           p.name,
                           p.generic_name,
                           p.barcode_office,
                           p.barcode_manufacture,
                           p.forquick_sale,
                           p.description,
                           p.prescription_required,
                           p.track_expire_date,
                           p.trackserialno,
                           sum(IF(sab.action = 'add', sab.qty, -sab.qty)) as total
                    from stock_adjustments adj
                             inner join stock_adjustment_details sad on adj.id = sad.adjustment_id
                             inner join stock_adjustment_batches sab on sad.id = sab.adid
                             inner join batches b on sab.batch_id = b.id
                             inner join stocks as s on s.id = sad.stockid
                             inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                             inner join categories on p.categoryid = categories.id
                             inner join product_categories pc on p.productcategoryid = pc.id
                             inner join product_subcategories psc on p.subcategoryid = psc.id
                             inner join model on p.modelid = model.id
                             inner join departments on p.departid = departments.id
                             inner join units on p.unit = units.id
                    where date_format(adj.doc,'%Y-%m-%d') <= '$stkdate' and adj.locationid = $locid
                      $globalFilter
                    group by batchId";

        //credit notes
        $sales_returns = "select s.id,
                               b.id             as batchId,
                               b.batch_no       as batch_no,
                               b.expire_date,
                               units.name       as unitName,
                               s.productid,
                               categories.name  as catName,
                               categories.id    as catID,
                               pc.id            as productcategoryid,
                               pc.name          as productcategoryname,
                               psc.id           as subcategoryid,
                               psc.name         as subcategoryname,
                               model.name       as brandName,
                               model.id         as modelID,
                               departments.name as departName,
                               departments.id   as departID,
                               p.name,
                               p.generic_name,
                               p.barcode_office,
                               p.barcode_manufacture,
                               p.forquick_sale,
                               p.description,
                               p.prescription_required,
                               p.track_expire_date,
                               p.trackserialno,
                               sum(srb.qty)     as total
                        from sales_returns sr
                                 inner join sales on sr.salesid = sales.id
                                 inner join sales_return_details srd on sr.id = srd.srid
                                 inner join salesdetails sd on srd.sdi = sd.id
                                 inner join stocks s on sd.stockid = s.id
                                 inner join products p on s.productid = p.id and p.non_stock = 0 and p.status = 'active'
                                 inner join categories on categories.id = p.categoryid
                                 inner join product_categories pc on pc.id = p.productcategoryid
                                 inner join product_subcategories psc on psc.id = p.subcategoryid
                                 inner join model on p.modelid = model.id
                                 inner join departments on p.departid = departments.id
                                 inner join units on p.unit = units.id
                                 inner join sales_return_batches srb on srd.id = srb.srdid
                                 inner join batches b on srb.batchid = b.id
                        where sr.approvedby > 0 and (sr.type = 'full' or sr.type = 'item') and sr.status = 'active' and date_format(sr.doc, '%Y-%m-%d') <= '$stkdate' and sales.locationid = $locid
                          $globalFilter
                        group by batchId";

        //stock manufacture
        $stock_manufacture_raw_materials = "
                select s.id,
                       b.id             as batchId,
                       b.batch_no       as batch_no,
                       b.expire_date,
                       units.name       as unitName,
                       s.productid,
                       categories.name  as catName,
                       categories.id    as catID,
                       pc.id            as productcategoryid,
                       pc.name          as productcategoryname,
                       psc.id           as subcategoryid,
                       psc.name         as subcategoryname,
                       model.name       as brandName,
                       model.id         as modelID,
                       departments.name as departName,
                       departments.id   as departID,
                       p.name,
                       p.generic_name,
                       p.barcode_office,
                       p.barcode_manufacture,
                       p.forquick_sale,
                       p.description,
                       p.prescription_required,
                       p.track_expire_date,
                       p.trackserialno,
                       -sum(smub.qty)   as total
                from stock_manufactures raw_material
                         inner join stock_manufacture_details smd on raw_material.id = smd.manufactureid
                         inner join stock_manufacture_used_batches smub on smd.id = smub.smdi and smd.smdi is null
                         inner join batches b on smub.batchid = b.id
                         inner join stocks as s on s.id = smd.stockid
                         inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                         inner join categories on p.categoryid = categories.id
                         inner join product_categories pc on p.productcategoryid = pc.id
                         inner join product_subcategories psc on p.subcategoryid = psc.id
                         inner join model on p.modelid = model.id
                         inner join departments on p.departid = departments.id
                         inner join units on p.unit = units.id
                where raw_material.approvedby > 0 and raw_material.status = 'active' and date_format(raw_material.doc,'%Y-%m-%d') <= '$stkdate' and raw_material.locationid = $locid
                   $globalFilter
                group by batchId";

        $stock_manufacture_end_products = "
                select s.id,
                       b.id             as batchId,
                       b.batch_no       as batch_no,
                       b.expire_date,
                       units.name       as unitName,
                       s.productid,
                       categories.name  as catName,
                       categories.id    as catID,
                       pc.id            as productcategoryid,
                       pc.name          as productcategoryname,
                       psc.id           as subcategoryid,
                       psc.name         as subcategoryname,
                       model.name       as brandName,
                       model.id         as modelID,
                       departments.name as departName,
                       departments.id   as departID,
                       p.name,
                       p.generic_name,
                       p.barcode_office,
                       p.barcode_manufacture,
                       p.forquick_sale,
                       p.description,
                       p.prescription_required,
                       p.track_expire_date,
                       p.trackserialno,
                       sum(b.qty)       as total
                from stock_manufactures end_products
                         inner join stock_manufacture_details sme on end_products.id = sme.manufactureid and sme.smdi is not null
                         inner join batches b on sme.id = b.smei
                         inner join stocks as s on s.id = sme.stockid
                         inner join products as p on p.id = s.productid and p.non_stock = 0 and p.status = 'active'
                         inner join categories on p.categoryid = categories.id
                         inner join product_categories pc on p.productcategoryid = pc.id
                         inner join product_subcategories psc on p.subcategoryid = psc.id
                         inner join model on p.modelid = model.id
                         inner join departments on p.departid = departments.id
                         inner join units on p.unit = units.id
                where end_products.approvedby > 0 and end_products.status = 'active' and date_format(end_products.doc,'%Y-%m-%d') <= '$stkdate' and end_products.locationid = $locid
                    $globalFilter
                group by batchId";

        //main query filters

        //main query
        $mainSql = "select id,batchId,batch_no,expire_date,'$stkdate' as stock_date,datediff(expire_date,'$stkdate') as expire_remain_days,
                            unitName,productid,catName,catID,productcategoryid,productcategoryname,subcategoryid,subcategoryname,
                            brandName,modelID,departName,departID,name,generic_name,
                           barcode_office,barcode_manufacture,forquick_sale,description,track_expire_date,trackserialno,sum(total) as total
                    from (
                             ($grnSql)
                             union all
                             ($grnReturnSql)
                             union all
                             ($saleSql)
                             union all
                             ($transferOutSql)
                             union all
                             ($transferInSql)
                             union all
                             ($adjustments)
                             union all
                             ($sales_returns)
                             union all
                             ($stock_manufacture_raw_materials)
                             union all
                             ($stock_manufacture_end_products)
                        ) as x
                    group by batchId having 1=1 ";

        if ($with_stock) $mainSql .= " and  total > 0";
        if (!$with_expired) $mainSql .= " and if(expire_date is null,1=1,expire_remain_days > 0)";
        if (!empty($expirebefore)) $mainSql .= " and expire_date <= '$expirebefore'";
        if (!empty($expireafter)) $mainSql .= " and expire_date >= '$expireafter'";

        $mainSql .= " order by name, expire_remain_days, batchId" . $sort;

//        debug($mainSql);
        $batchStock = fetchRows($mainSql);
        if (!$group_batch) {
            return $batchStock;
        } else {
            $newArray = [];
            foreach ($batchStock as $index => $item) {
                $newArray[$item['id']]['id'] = $item['id'];
                $newArray[$item['id']]['stockid'] = $item['id'];
                $newArray[$item['id']]['unitName'] = $item['unitName'];
                $newArray[$item['id']]['productid'] = $item['productid'];
                $newArray[$item['id']]['catName'] = $item['catName'];
                $newArray[$item['id']]['catID'] = $item['catID'];
                $newArray[$item['id']]['productcategoryid'] = $item['productcategoryid'];
                $newArray[$item['id']]['productcategoryname'] = $item['productcategoryname'];
                $newArray[$item['id']]['subcategoryid'] = $item['subcategoryid'];
                $newArray[$item['id']]['subcategoryname'] = $item['subcategoryname'];
                $newArray[$item['id']]['brandName'] = $item['brandName'];
                $newArray[$item['id']]['modelID'] = $item['modelID'];
                $newArray[$item['id']]['departID'] = $item['departID'];
                $newArray[$item['id']]['departName'] = $item['departName'];
                $newArray[$item['id']]['name'] = $item['name'];
                $newArray[$item['id']]['productdescription'] = $item['description'];
                $newArray[$item['id']]['generic_name'] = $item['generic_name'];
                $newArray[$item['id']]['barcode_office'] = $item['barcode_office'];
                $newArray[$item['id']]['barcode_manufacture'] = $item['barcode_manufacture'];
                $newArray[$item['id']]['forquick_sale'] = $item['forquick_sale'];
                $newArray[$item['id']]['description'] = $item['description'];
                $newArray[$item['id']]['track_expire_date'] = $item['track_expire_date'];
                $newArray[$item['id']]['trackserialno'] = $item['trackserialno'];
                if (!isset($newArray[$item['id']]['total'])) $newArray[$item['id']]['total'] = 0;
                $newArray[$item['id']]['total'] += $item['total'];

                //batches
                $newArray[$item['id']]['batches'][$item['batchId']]['batchId'] = $item['batchId'];
                $newArray[$item['id']]['batches'][$item['batchId']]['batch_no'] = $item['batch_no'];
                $newArray[$item['id']]['batches'][$item['batchId']]['expire_date'] = $item['expire_date'];
                $newArray[$item['id']]['batches'][$item['batchId']]['expire_remain_days'] = $item['expire_remain_days'];
                $newArray[$item['id']]['batches'][$item['batchId']]['total'] = $item['total'];
            }
            if ($with_stock_holding) {
                $except_proformaid = defined('EXCEPT_PROFORMA') ? EXCEPT_PROFORMA : '';
                $except_proformaid = intval($except_proformaid);
                foreach ($newArray as $index => $item) {
                    $newArray[$index]['in_stock_qty'] = $item['total'];
                    if ($held = Proformas::heldStock($item['id'], '', $except_proformaid)[0]) {
                        $newArray[$index]['held_stock'] = $held['qty'];
                        $total = $item['total'] - $held['qty'];
                        if ($total <= 0) {
//                            unset($newArray[$index]);
                            $newArray[$index]['total'] = 0;
                            $newArray[$index]['batches'] = [];
                        } else {
                            $newArray[$index]['total'] = $total;
                            foreach ($reversedBatches = array_reverse($item['batches'], true) as $bi => $_) {
                                if ($reversedBatches[$bi]['total'] >= $held['qty']) {
                                    $reversedBatches[$bi]['total'] -= $held['qty'];
                                    if ($reversedBatches[$bi]['total'] == 0) unset($reversedBatches[$bi]);
                                    break;
                                } else {
                                    $held['qty'] -= $reversedBatches[$bi]['total'];
                                    unset($reversedBatches[$bi]);
                                }
                            }
                            $newArray[$index]['batches'] = array_reverse($reversedBatches, true);
                        }
                    } else {
                        $newArray[$index]['held_stock'] = 0;
                    }
                }
            }
//            debug($newArray);
            return $newArray;
        }

    }

    function stockAdjustmentBatchWise($adjustmentno = "", $productid = "", $batchno = "", $locationid = "", $fromdate = "", $todate = "", $branchid = "", $userid = "")
    {
        $sql = "select adj.*,
                       l.name                                                                     as locationname,
                       users.name                                                                 as issuedby,
                       sad.id                                                                     as detailId,
                       sad.stockid,
                       sad.remarks                                                                as detail_remarks,
                       sad.current_stock,
                       p.name                                                                     as productname,
                       p.description                                                              as productdescription,
                       p.track_expire_date,
                       p.trackserialno,
                       p.barcode_office,
                       sab.qty,
                       sab.action,
                       sab.before_qty,
                       IF(sab.action = 'add', sab.before_qty + sab.qty, sab.before_qty - sab.qty) as after_qty,
                       b.id                                                                       as batchId,
                       b.batch_no,
                       b.expire_date
                from stock_adjustments adj
                         inner join locations l on adj.locationid = l.id
                         inner join branches on l.branchid = branches.id
                         inner join users on adj.createdby = users.id
                         inner join stock_adjustment_details sad on adj.id = sad.adjustment_id
                         inner join stock_adjustment_batches sab on sab.adid = sad.id
                         inner join batches b on sab.batch_id = b.id
                         inner join stocks on sad.stockid = stocks.id
                         inner join products p on stocks.productid = p.id
                where 1 = 1";
        if ($adjustmentno) $sql .= " and adj.id = $adjustmentno";
        if ($productid) $sql .= " and p.id = $productid";
        if ($batchno) $sql .= " and  b.batch_no = '$batchno'";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and branches.id = $branchid";
        if ($userid) $sql .= " and users.id = $userid";
        if ($fromdate) $sql .= " and date_format(adj.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(adj.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by adj.id desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function productHistory($locationid, $productid, $fromdate, $todate, $batch_no = "")
    {
        $batch_filter = $batch_no ? " and b.batch_no = '$batch_no'" : " and 1=1 ";
        $sql = "select *
                from (
                         select s.id,
                                b.id                           as batchId,
                                b.batch_no                     as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'in'                           as action,
                                'grn'                          as voucher,
                                g.id                           as voucherno,
                                g.doc                          as action_date,
                                sum(b.qty)                     as qty
                         from grn as g
                                  inner join grndetails as gd on g.id = gd.grnid
                                  inner join batches as b on b.gdi = gd.id
                                  inner join stocks as s on s.id = gd.stockid
                                  inner join products as p on p.id = s.productid
                         where 1 = 1
                           and g.approvedby is not null
                           and date_format(g.doc, '%Y-%m-%d') >= '$fromdate'
                           and date_format(g.doc, '%Y-%m-%d') <= '$todate'
                           and g.locid = $locationid
                           and p.id = $productid
                           $batch_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                            as batchId,
                                b.batch_no                      as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'out'                           as action,
                                'return'                        as voucher,
                                gr.id                           as voucherno,
                                gr.doc                          as action_date,
                                sum(grb.qty)                    as qty
                         from grn_returns as gr
                                  inner join grnreturn_details as grd ON grd.returnid = gr.id
                                  inner join grn_return_batches as grb ON grd.id = grb.grdi
                                  inner join batches as b on grb.batch_id = b.id
                                  inner join stocks as s on s.id = grd.stockid
                                  inner join products as p on p.id = s.productid
                         where date_format(gr.doc, '%Y-%m-%d') >= '$fromdate'
                           and date_format(gr.doc, '%Y-%m-%d') <= '$todate'
                           and gr.locid = $locationid
                           and p.id = $productid
                           $batch_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                            as batchId,
                                b.batch_no                      as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'out'                           as action,
                                'sale'                          as voucher,
                                sa.receipt_no                   as voucherno,
                                sa.doc                          as action_date,
                                sum(sb.qty)                     as qty
                         from sales sa
                                  inner join salesdetails as sd on sd.salesid = sa.id
                                  inner join salesbatches as sb on sd.id = sb.sdi
                                  inner join batches as b on sb.batch_id = b.id
                                  inner join stocks as s on s.id = sd.stockid
                                  inner join products as p on p.id = s.productid
                         where sa.iscreditapproved = 1
                           and date_format(sa.approvedate, '%Y-%m-%d') >= '$fromdate'
                           and date_format(sa.approvedate, '%Y-%m-%d') <= '$todate'
                           and sa.locationid = $locationid
                           and p.id = $productid
                           $batch_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                            as batchId,
                                b.batch_no                      as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'out'                           as action,
                                'transfer out'                  as voucher,
                                st.id                           as voucherno,
                                st.doc                          as action_date,
                                sum(stb.qty)                    as qty
                         from stock_transfers st
                                  inner join stock_transfer_details as std on std.transferid = st.id
                                  inner join stock_transfer_batches as stb on std.id = stb.stdi
                                  inner join batches as b on stb.batch_id = b.id
                                  inner join stocks as s on s.id = std.stock_from
                                  inner join products as p on p.id = s.productid
                         where st.approvedby is not null
                           and date_format(st.doc, '%Y-%m-%d') >= '$fromdate'
                           and date_format(st.doc, '%Y-%m-%d') <= '$todate'
                           and st.location_from = $locationid
                           and p.id = $productid
                           $batch_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                              as batchId,
                                b.batch_no                        as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'in'                              as action,
                                'transfer in'                     as voucher,
                                stin.id                           as voucherno,
                                stin.doc                          as action_date,
                                sum(stb.qty)                      as total
                         from stock_transfers stin
                                  inner join stock_transfer_details as stdin on stdin.transferid = stin.id
                                  inner join stock_transfer_batches as stb on stdin.id = stb.stdi
                                  inner join batches as b on stb.batch_id = b.id
                                  inner join stocks as s on s.id = stdin.stock_to
                                  inner join products as p on p.id = s.productid
                         where stin.approvedby is not null
                           and date_format(stin.doc, '%Y-%m-%d') >= '$fromdate'
                           and date_format(stin.doc, '%Y-%m-%d') <= '$todate'
                           and stin.location_to = $locationid
                           and p.id = $productid
                           $batch_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                                as batchId,
                                b.batch_no                          as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                IF(sab.action = 'add', 'in', 'out') as action,
                                'adjustment'                        as voucher,
                                adj.id                              as voucherno,
                                adj.doc                             as action_date,
                                sum(sab.qty)                        as qty
                         from stock_adjustments adj
                                  inner join stock_adjustment_details sad on adj.id = sad.adjustment_id
                                  inner join stock_adjustment_batches sab on sad.id = sab.adid
                                  inner join batches b on sab.batch_id = b.id
                                  inner join stocks as s on s.id = sad.stockid
                                  inner join products as p on p.id = s.productid
                         where date_format(adj.doc, '%Y-%m-%d') >= '$fromdate'
                           and date_format(adj.doc, '%Y-%m-%d') <= '$todate'
                           and adj.locationid = $locationid
                           and p.id = $productid
                           $batch_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                               b.id          as batchId,
                               b.batch_no    as batch_no,
                               b.expire_date,
                               s.productid,
                               p.name,
                               'in'          as action,
                               'sale return' as voucher,
                               sr.id         as voucherno,
                               sr.doc        as action_date,
                               sum(srb.qty)  as qty
                        from sales_returns sr
                                 inner join sales as sa on sr.salesid = sa.id
                                 inner join sales_return_details srd on srd.srid = sr.id
                                 inner join salesdetails as sd on sd.id = srd.sdi
                                 inner join sales_return_batches srb on srd.id = srb.srdid
                                 inner join batches as b on srb.batchid = b.id
                                 inner join stocks as s on s.id = sd.stockid
                                 inner join products as p on p.id = s.productid
                        where sr.approvedby > 0 and (sr.type = 'full' or sr.type = 'item') and sr.status = 'active'
                          and date_format(sr.doc, '%Y-%m-%d') >= '$fromdate'
                          and date_format(sr.doc, '%Y-%m-%d') <= '$todate'
                          and sa.locationid = $locationid
                          and p.id = $productid $batch_filter
                        group by voucherno, batchId
                         union all
                         select s.id,
                               b.id                       as batchId,
                               b.batch_no                 as batch_no,
                               b.expire_date,
                               s.productid,
                               p.name,
                               'out'                      as action,
                               'manufacture raw material' as voucher,
                               sm.id                      as voucherno,
                               sm.doc                     as action_date,
                               sum(smub.qty)              as qty
                        from stock_manufactures sm
                                 inner join stock_manufacture_details smd on sm.id = smd.manufactureid and smd.smdi is null
                                 inner join stock_manufacture_used_batches smub on smub.smdi = smd.id
                                 inner join batches as b on smub.batchid = b.id
                                 inner join stocks as s on s.id = smd.stockid
                                 inner join products as p on p.id = s.productid
                        where sm.approvedby > 0 and sm.status = 'active'
                          and date_format(sm.doc, '%Y-%m-%d') >= '$fromdate'
                          and date_format(sm.doc, '%Y-%m-%d') <= '$todate'
                          and sm.locationid = $locationid
                          and p.id = $productid $batch_filter
                        group by voucherno, batchId
                         union all
                         select s.id,
                               b.id                       as batchId,
                               b.batch_no                 as batch_no,
                               b.expire_date,
                               s.productid,
                               p.name,
                               'in'                       as action,
                               'manufacture end product' as voucher,
                               sm.id                      as voucherno,
                               sm.doc                     as action_date,
                               sum(b.qty)                 as qty
                        from stock_manufactures sm
                                 inner join stock_manufacture_details sme on sm.id = sme.manufactureid and sme.smdi is not null
                                 inner join batches as b on sme.id = b.smei
                                 inner join stocks as s on s.id = sme.stockid
                                 inner join products as p on p.id = s.productid
                        where sm.approvedby > 0 and sm.status = 'active'
                          and date_format(sm.doc, '%Y-%m-%d') >= '$fromdate'
                          and date_format(sm.doc, '%Y-%m-%d') <= '$todate'
                          and sm.locationid = $locationid
                          and p.id = $productid $batch_filter
                        group by voucherno, batchId
                     ) as history
                order by action_date";
//        debug($sql);
        return fetchRows($sql);
    }

    function productsHistory($locationid, $productid = "", $fromdate = "", $todate = "", $brandid = "", $pcategoryid = "", $subcategoryid = "", $batchno = "")
    {
        if (empty($locationid)) return [];
        $global_filter = "";
         if($productid) $global_filter.= " and p.id = $productid";
         if($brandid) $global_filter.= " and p.modelid = $brandid";
         if($pcategoryid) $global_filter.= " and p.productcategoryid = $pcategoryid";
         if($subcategoryid) $global_filter.= " and p.subcategoryid = $subcategoryid";
         if($batchno) $global_filter.= " and b.batch_no = '$batchno'";

        $grn_fromdate=$grnreturn_fromdate=$sale_fromdate=$st_fromdate=$stin_fromdate=$adj_fromdate=$sr_fromdate=$sm_fromdate="";
         if($fromdate){
             $grn_fromdate=" and date_format(g.doc, '%Y-%m-%d') >= '$fromdate'";
             $grnreturn_fromdate=" and date_format(gr.doc, '%Y-%m-%d') >= '$fromdate'";
             $sale_fromdate=" and date_format(sa.approvedate, '%Y-%m-%d') >= '$fromdate'";
             $st_fromdate=" and date_format(st.doc, '%Y-%m-%d') >= '$fromdate'";
             $stin_fromdate=" and date_format(stin.doc, '%Y-%m-%d') >= '$fromdate'";
             $adj_fromdate=" and date_format(adj.doc, '%Y-%m-%d') >= '$fromdate'";
             $sr_fromdate=" and date_format(sr.doc, '%Y-%m-%d') >= '$fromdate'";
             $sm_fromdate=" and date_format(sm.doc, '%Y-%m-%d') >= '$fromdate'";
         }

        $grn_todate=$grnreturn_todate=$sale_todate=$st_todate=$stin_todate=$adj_todate=$sr_todate=$sm_todate="";
         if($todate){
             $grn_todate=" and date_format(g.doc, '%Y-%m-%d') <= '$todate'";
             $grnreturn_todate=" and date_format(gr.doc, '%Y-%m-%d') <= '$todate'";
             $sale_todate=" and date_format(sa.approvedate, '%Y-%m-%d') <= '$todate'";
             $st_todate=" and date_format(st.doc, '%Y-%m-%d') <= '$todate'";
             $stin_todate=" and date_format(stin.doc, '%Y-%m-%d') <= '$todate'";
             $adj_todate=" and date_format(adj.doc, '%Y-%m-%d') <= '$todate'";
             $sr_todate=" and date_format(sr.doc, '%Y-%m-%d') <= '$todate'";
             $sm_todate=" and date_format(sm.doc, '%Y-%m-%d') <= '$todate'";
         }

        $sql = "select *
                from (
                         select s.id,
                                b.id       as batchId,
                                b.batch_no as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'in'       as action,
                                'grn'      as voucher,
                                g.id       as voucherno,
                                g.doc      as action_date,
                                sum(b.qty) as qty
                         from grn as g
                                  inner join grndetails as gd on g.id = gd.grnid
                                  inner join batches as b on b.gdi = gd.id
                                  inner join stocks as s on s.id = gd.stockid
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where 1 = 1
                           and g.approvedby > 0 $grn_fromdate $grn_todate
                           and g.locid = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id         as batchId,
                                b.batch_no   as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'out'        as action,
                                'return'     as voucher,
                                gr.id        as voucherno,
                                gr.doc       as action_date,
                                sum(grb.qty) as qty
                         from grn_returns as gr
                                  inner join grnreturn_details as grd ON grd.returnid = gr.id
                                  inner join grn_return_batches as grb ON grd.id = grb.grdi
                                  inner join batches as b on grb.batch_id = b.id
                                  inner join stocks as s on s.id = grd.stockid
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where 1=1 $grnreturn_fromdate $grnreturn_todate
                           and gr.locid = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id          as batchId,
                                b.batch_no    as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'out'         as action,
                                'sale'        as voucher,
                                sa.receipt_no as voucherno,
                                sa.doc        as action_date,
                                sum(sb.qty)   as qty
                         from sales sa
                                  inner join salesdetails as sd on sd.salesid = sa.id
                                  inner join salesbatches as sb on sd.id = sb.sdi
                                  inner join batches as b on sb.batch_id = b.id
                                  inner join stocks as s on s.id = sd.stockid
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where sa.iscreditapproved = 1 $sale_fromdate $sale_todate
                           and sa.locationid = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id           as batchId,
                                b.batch_no     as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'out'          as action,
                                'transfer out' as voucher,
                                st.id          as voucherno,
                                st.doc         as action_date,
                                sum(stb.qty)   as qty
                         from stock_transfers st
                                  inner join stock_transfer_details as std on std.transferid = st.id
                                  inner join stock_transfer_batches as stb on std.id = stb.stdi
                                  inner join batches as b on stb.batch_id = b.id
                                  inner join stocks as s on s.id = std.stock_from
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where st.approvedby is not null $st_fromdate $st_todate
                           and st.location_from = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id          as batchId,
                                b.batch_no    as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'in'          as action,
                                'transfer in' as voucher,
                                stin.id       as voucherno,
                                stin.doc      as action_date,
                                sum(stb.qty)  as total
                         from stock_transfers stin
                                  inner join stock_transfer_details as stdin on stdin.transferid = stin.id
                                  inner join stock_transfer_batches as stb on stdin.id = stb.stdi
                                  inner join batches as b on stb.batch_id = b.id
                                  inner join stocks as s on s.id = stdin.stock_to
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where stin.approvedby is not null $stin_fromdate $stin_todate
                           and stin.location_to = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                                as batchId,
                                b.batch_no                          as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                IF(sab.action = 'add', 'in', 'out') as action,
                                'adjustment'                        as voucher,
                                adj.id                              as voucherno,
                                adj.doc                             as action_date,
                                sum(sab.qty)                        as qty
                         from stock_adjustments adj
                                  inner join stock_adjustment_details sad on adj.id = sad.adjustment_id
                                  inner join stock_adjustment_batches sab on sad.id = sab.adid
                                  inner join batches b on sab.batch_id = b.id
                                  inner join stocks as s on s.id = sad.stockid
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where 1=1 $adj_fromdate $adj_todate
                           and adj.locationid = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id          as batchId,
                                b.batch_no    as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'in'          as action,
                                'sale return' as voucher,
                                sr.id         as voucherno,
                                sr.doc        as action_date,
                                sum(srb.qty)  as qty
                         from sales_returns sr
                                  inner join sales as sa on sr.salesid = sa.id
                                  inner join sales_return_details srd on srd.srid = sr.id
                                  inner join salesdetails as sd on sd.id = srd.sdi
                                  inner join sales_return_batches srb on srd.id = srb.srdid
                                  inner join batches as b on srb.batchid = b.id
                                  inner join stocks as s on s.id = sd.stockid
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where sr.approvedby > 0
                           and (sr.type = 'full' or sr.type = 'item')
                           and sr.status = 'active' $sr_fromdate $sr_todate
                           and sa.locationid = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                       as batchId,
                                b.batch_no                 as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'out'                      as action,
                                'manufacture raw material' as voucher,
                                sm.id                      as voucherno,
                                sm.doc                     as action_date,
                                sum(smub.qty)              as qty
                         from stock_manufactures sm
                                  inner join stock_manufacture_details smd on sm.id = smd.manufactureid and smd.smdi is null
                                  inner join stock_manufacture_used_batches smub on smub.smdi = smd.id
                                  inner join batches as b on smub.batchid = b.id
                                  inner join stocks as s on s.id = smd.stockid
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where sm.approvedby > 0
                           and sm.status = 'active' $sm_fromdate $sm_todate
                           and sm.locationid = $locationid $global_filter
                         group by voucherno, batchId
                         union all
                         select s.id,
                                b.id                      as batchId,
                                b.batch_no                as batch_no,
                                b.expire_date,
                                s.productid,
                                p.name,
                                'in'                      as action,
                                'manufacture end product' as voucher,
                                sm.id                     as voucherno,
                                sm.doc                    as action_date,
                                sum(b.qty)                as qty
                         from stock_manufactures sm
                                  inner join stock_manufacture_details sme on sm.id = sme.manufactureid and sme.smdi is not null
                                  inner join batches as b on sme.id = b.smei
                                  inner join stocks as s on s.id = sme.stockid
                                  inner join products as p on p.id = s.productid
                                  inner join product_categories pc on p.productcategoryid = pc.id
                                  inner join product_subcategories psc on p.subcategoryid = psc.id
                                  inner join model on p.modelid = model.id
                         where sm.approvedby > 0
                           and sm.status = 'active' $sm_fromdate $sm_todate
                           and sm.locationid = $locationid $global_filter
                         group by voucherno, batchId
                     ) as history
                order by action_date";
//        debug($sql);
        return fetchRows($sql);
    }

    function withSuppliers($batchid = "", $limit2 = true)
    {
        $sql = "select grn.id as grnid,
                       s.id   as supplierid,
                       s.name as suppliername,
                       grn.doc,
                       gd.qty,
                       gd.billable_qty,
                       gd.rate
                from suppliers s
                         inner join grn on grn.supplierid = s.id
                         inner join grndetails gd on gd.grnid = grn.id
                         inner join batches b on b.gdi = gd.id
                where 1 = 1";
        if ($batchid) {
            if (is_array($batchid)) {
                $sql .= " and b.id in ('" . implode("','", $batchid) . "')";
            } else {
                $sql .= " and b.id = $batchid";
            }
        }
        $sql .= " group by grn.id order by grn.id desc";
        if ($limit2) $sql .= " limit 2";

        return fetchRows($sql);
    }

    static function locationStockValue($stockdate = "", $locationid = "", $branchid = "")
    {
        $locationStock = [];
        if ($locationid) {
            $locations = Locations::$locationClass->find(['id' => $locationid]);
        } elseif ($branchid) {
            $locations = Locations::$locationClass->find(['branchid' => $branchid]);
        } else {
            $locations = Locations::$locationClass->getAllActive();
        }

        foreach ($locations as $index => $location) {
            $branchid = Locations::$locationClass->getBranch($location['id'])['id'];
            $stock = Stocks::$stockClass->calcStock($location['id'], "", $stockdate,
                "", "", "", "", "", "",
                "", "", "", "", "", "", "",
                "", false, true, "", "", true);
            $stockvalue = 0;
            foreach ($stock as $bi => $item) {
                $currentBranchPrice = CurrentPrices::$currentPricesClass->find(['branchid' => $branchid, 'productid' => $item['productid']])[0];
//                debug($currentBranchPrice);
                $stockvalue += ($item['total'] * $currentBranchPrice['costprice']);
            }
            $locationStock[] = [
                'id' => $location['id'],
                'name' => $location['name'],
                'stockvalue' => $stockvalue
            ];
        }

//        debug($locationStock);
        return $locationStock;
    }
}
