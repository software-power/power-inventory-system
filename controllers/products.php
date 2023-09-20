<?
if ($action == 'product_index') {
    Users::isAllowed();
//    debug($_GET);
    $search = htmlspecialchars($_GET['search']);
    $start_char = htmlspecialchars($_GET['start_char']);
    $non_stock = $_GET['non_stock'];
    $departmentid = $_GET['departmentid'];
    $brandid = $_GET['brandid'];
    $taxcategory = $_GET['taxcategory'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $unitid = $_GET['unitid'];
    $bulkunitid = $_GET['bulkunitid'];

    $title = [];
    if ($search) $title[] = "Search: " . $search;
    if ($start_char) {
        $tData['start_char'] = $start_char;
    }
    if ($departmentid) $title[] = "Department: " . $Departments->get($departmentid)['name'];
    if ($brandid) $title[] = "Brand: " . $Models->get($brandid)['name'];
    if ($taxcategory) $title[] = "Tax Category: " . $Categories->get($taxcategory)['name'];
    if ($productcategoryid) $title[] = "Product Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Product Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($unitid) $title[] = "Unit: " . $Units->get($unitid)['name'];
    if ($bulkunitid) $title[] = "Bulk Unit: " . $BulkUnits->get($bulkunitid)['name'];
    if ($non_stock) $title[] = "Non-Stock: " . ucfirst($non_stock);


    $tData['product_count'] = $product_count = Products::$productClass->countWhere("1 = 1");


    $tData['title'] = implode(' | ', $title);
    if ($product_count <= 150 || $start_char || $search || $departmentid || $brandid || $taxcategory || $productcategoryid || $subcategoryid || $unitid || $bulkunitid || $non_stock)
        $tData['product'] = $Products->getList('', $search, $departmentid, $brandid, $taxcategory, $productcategoryid, $subcategoryid, $unitid,
            $bulkunitid, $non_stock, '', '', '', $start_char);
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $tData['units'] = $Units->getAllActive();
    $tData['bulkunits'] = $BulkUnits->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $data['content'] = loadTemplate('product_list.tpl.php', $tData);
}

if ($action == 'product_with_stock') {
    Users::isAllowed();
//    debug($_GET);
    $search = htmlspecialchars($_GET['search']);
    $non_stock = $_GET['non_stock'];
    $productid = $_GET['productid'];
    $departmentid = $_GET['departmentid'];
    $brandid = $_GET['brandid'];
    $taxcategory = $_GET['taxcategory'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $unitid = $_GET['unitid'];
    $bulkunitid = $_GET['bulkunitid'];

    $locationid = $_SESSION['member']['locationid'];
    $tData['location'] = $Locations->locationList($locationid)[0];

    $title = [];
    if ($search) $title[] = "Search: " . $search;
    if ($departmentid) $title[] = "Department: " . $Departments->get($departmentid)['name'];
    if ($brandid) $title[] = "Brand: " . $Models->get($brandid)['name'];
    if ($taxcategory) $title[] = "Tax Category: " . $Categories->get($taxcategory)['name'];
    if ($productcategoryid) $title[] = "Product Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Product Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($unitid) $title[] = "Unit: " . $Units->get($unitid)['name'];
    if ($bulkunitid) $title[] = "Bulk Unit: " . $BulkUnits->get($bulkunitid)['name'];
    if ($non_stock) $title[] = "Non-Stock: " . ucfirst($non_stock);

    $tData['title'] = implode(' | ', $title);

    $stocks = [];
    if ($search || $departmentid || $brandid || $taxcategory || $productcategoryid || $subcategoryid || $unitid || $bulkunitid || $non_stock) {
//        if (!$non_stock || $non_stock == 'no') {
//            $stocks = $Stocks->calcStock(
//                $locationid, "",
//                '', "", $productid, "", "", "",
//                "", $taxcategory, $brandid, $departmentid, "", "", "", "",
//                "", false, true, $productcategoryid, $subcategoryid,
//                true, true);
//        }

        $products = $Products->getList('', $search, $departmentid, $brandid, $taxcategory, $productcategoryid, $subcategoryid,
            $unitid, $bulkunitid, $non_stock, false, '', 'active');
        $branchid = $Locations->getBranch($locationid)['id'];
        $hierarchic_level = IS_ADMIN ? 0 : $Hierarchics->get($_SESSION['member']['hierachicid'])['level'];
        $bulk_stores_id = array_column(Locations::$locationClass->find(['bulk_store' => 1]), 'id');
        $tData['products'] = array_map(function ($p) use ($branchid, $locationid, $hierarchic_level, $bulk_stores_id) {
            if (!$p['non_stock']) {
                //stocks

                $current_stock = Stocks::$stockClass->calcStock(
                    $locationid, "",
                    '', "", $p['productid'], "", "", "",
                    "", '', '', '', "", "", "", "",
                    "", false, true, '', '',
                    true, true);
                $current_stock = array_values($current_stock)[0];
                $p['stock_qty'] = $current_stock['total'];

                //bulk stores stock
                $p['bulk_store_qty'] = 0;
                foreach ($bulk_stores_id as $bsid) {
                    $bulk_stock = Stocks::$stockClass->calcStock(
                        $bsid, "",
                        '', "", $p['productid'], "", "", "",
                        "", '', '', '', "", "", "", "",
                        "", false, true, '', '',
                        true, true);
                    $bulk_stock = array_values($bulk_stock)[0];
                    $p['bulk_store_qty'] += $bulk_stock['total'];
                }


                //prices
                $prices = HierarchicPrices::$hierarchicClass->getProductInfoWithPrices($branchid, $p['productid'], '', $hierarchic_level);
                $p['quick_price_inc'] = $prices[0]['inc_quicksale_price'];
                $p['quick_price_exc'] = round($p['quick_price_inc'] / (1 + $p['vatPercent'] / 100), 2);
                $p['prices'] = array_map(function ($hp) {
                    return [
                        'hierarchicname' => $hp['hierarchicname'],
                        'level' => $hp['level'],
                        'commission' => $hp['commission'],
                        'target' => $hp['target'],
                        'exc_price' => $hp['exc_price'],
                        'inc_price' => $hp['inc_price'],
                    ];
                }, $prices);

//                debug($p);
            }
//        debug($p);
            return $p;
        }, $products);
    }
//    debug($tData['products']);
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $tData['units'] = $Units->getAllActive();
    $tData['bulkunits'] = $BulkUnits->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $data['content'] = loadTemplate('product_list_with_stock.tpl.php', $tData);
}

if ($action == 'search_product') {
    $productid = $_GET['productid'];
    $brandid = $_GET['brandid'];
    $categoryid = $_GET['categoryid'];

    $title = [];

    if ($productid) {
        $tData['product'] = $Products->getList($productid);
        $title[] = "Product: " . $tData['product'][0]['name'];
    }

    if ($brandid) $title[] = "Brand: " . $Models->get($brandid)['name'];
    if ($categoryid) $title[] = "Category: " . $ProductCategories->get($categoryid)['name'];


    $tData['title'] = implode(' | ', $title);

    $tData['brands'] = $Models->getAllActive();
    $tData['categories'] = $ProductCategories->getAllActive();
    $data['content'] = loadTemplate('search_product.tpl.php', $tData);
}

if ($action == 'product_admin') {
    Users::can(OtherRights::admin_view, true);
    $productid = $_GET['productid'];
    if (!$product = $Products->get($productid)) {
        debug('Product not found!');
    }
    $fromdate = date('Y-m-d', strtotime('-3 months'));
    $productDetails = $Products->getProductDetails($productid);

    $locations = $Locations->locationList('', '', 'active', $_SESSION['member']['locationid']);
    foreach ($locations as $l) {
        $currentStock = $Stocks->calcStock(
            $l['id'], '', '', '', $productid, '',
            '', '', '', '', '', '',
            '', '', '', '', '', false,
            true, '', '', true, false
        );
        $currentStock = array_values($currentStock)[0];
        $tData['total_stock'] += $currentStock['total'];
    }

    $tData['productDetails'] = $productDetails;

    $tData['locations'] = $locations;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");

    $data['content'] = loadTemplate('product-admin.tpl.php', $tData);
}

if ($action == 'product_edit') {

    $action = 'product_add';
}

if ($action == 'product_add') {
    $tData['department'] = $Departments->get(CS_DEFAULT_DEPARTMENT);
    $tData['categoryList'] = $Categories->getAllActive();
    $tData['model'] = $Models->get(CS_DEFAULT_BRAND);
    $tData['unit'] = $Units->get(CS_DEFAULT_UNIT);
    $tData['bulk_unit'] = $BulkUnits->get(CS_DEFAULT_BULK);
    $tData['productCategory'] = $ProductCategories->get(CS_DEFAULT_CATEGORY);
    $tData['productSubCategory'] = $ProductSubCategories->get(CS_DEFAULT_SUBCATEGORY);
//    debug($tData['bulk_unit']);
    if (empty($_GET['id'])) {
        Users::can(OtherRights::add_product, true);
        $tData['hierarchicList'] = $Hierarchics->find(array('status' => 'active'), 'level');
    } else {
        Users::can(OtherRights::edit_product, true);
        $id = $_GET['id'];
        $tData['name'] = $_GET['name'];

        $product = $Products->get($id);
//        debug($product);
        $tData['product'] = $product;
        $tData['department'] = $Departments->get($product['departid']);
        $tData['model'] = $Models->get($product['modelid']);
        $tData['unit'] = $Units->get($product['unit']);
        $tData['bulk_unit'] = $BulkUnits->get($product['bulk_units']);
        $tData['productCategory'] = $ProductCategories->get($product['productcategoryid']);
        $tData['productSubCategory'] = $ProductSubCategories->get($product['subcategoryid']);
        $tData['edit'] = 1;
    }
    // debug($tData);
    $data['content'] = loadTemplate('product_edit.tpl.php', $tData);
}

if ($action == 'product_save') {
//    debug($_POST);
    $user = $_SESSION['member'];
    $id = intval($_POST['id']);
    $product = $_POST['product'];

    validate($product);
    if (CS_VFD_TYPE == VFD_TYPE_ZVFD) {
        if (isset($_POST['zvfd_exempted'])) {
            //find exempted category
            $exemptedCategory = Categories::$categoryClass->find(['vat_percent' => 0]);
            if (!$exemptedCategory) {
                $_SESSION['error'] = "No Exempted (0%) Tax category specified";
                redirectBack();
            }

            $product['categoryid'] = $exemptedCategory[0]['id'];
        } else {
            $product['categoryid'] = CS_ZVFD_TAXCATEGORYID;
            $product['taxcode'] = '';
        }
    }
//    debug($product);
    if (empty($id)) {
        Users::can(OtherRights::add_product, true);
        $product['barcode_office'] = $product['barcode_office'] ?: Products::generateBarcode();
        $product['doc'] = TIMESTAMP;
        $product['createdby'] = $user['id'];
        if (!empty($product['barcode_manufacture'])) {
            $barcodeexists = $Products->find(array('barcode_manufacture' => $product['barcode_manufacture']));
            if ($barcodeexists) {
                $_SESSION['error'] = "Barcode already exists!";
                redirect('products', 'product_add');
                die();
            }
        }
        $Products->Insert($product);
        $_SESSION['message'] = 'Product Added successfully';
    } else {
        Users::can(OtherRights::edit_product, true);
        $product['modifiedby'] = $user['id'];
        $product['dom'] = TIMESTAMP;
        $Products->update($id, $product);
        $_SESSION['message'] = 'Product Updated successfully';
    }
    $_POST['redirect'] ? header('Location: ' . base64_decode($_POST['redirect'])) : redirect('products', 'product_index');
}

if ($action == 'product_delete') {
    $id = intval($_GET['id']);
    $Products->delete($id);
    redirect('products', 'product_index');
}

if ($action == 'image_upload') {
    Users::can(OtherRights::upload_product_image, true);
    $id = $_GET['id'];
    if ($product = $Products->get($id)) {
        $tData['product'] = $product;
        $data['content'] = loadTemplate('product_image_upload.tpl.php', $tData);
    } else {
        $_SESSION['error'] = "Product not found!";
        redirectBack();
    }
}

if ($action == 'save_image') {
//    debug([$_POST, $_FILES]);
    $productid = $_POST['product_id'];

    validate($productid);
    validate($_FILES["image"]);
    if ($product = $Products->get($productid)) {

        $target_dir = "images/products/";
        $target_file = $target_dir . "{$productid}_" . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $_SESSION['error'] = "Invalid Image format";
            $_SESSION['delay'] = 5000;
            redirectBack();
            die();
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            if (file_exists($product['image_path'])) {
                unlink($product['image_path']);//clear previous image file
            }
            $Products->update($productid, [
                'image_path' => $target_file
            ]);
            $_SESSION['message'] = "Image uploaded successfully";
            $_SESSION['delay'] = 5000;
            redirect('products', 'product_index');
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }
    } else {
        $_SESSION['error'] = "Product Not found";
        $_SESSION['delay'] = 5000;
        redirectBack();
    }
}

if ($action == 'reorder_list') {
    Users::isAllowed();

    $productid = $_GET['productid'];
    $brandid = $_GET['brandid'];
    $categoryid = $_GET['categoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $stocklocation = $_GET['stocklocation'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$stocklocation) $stocklocation = $_SESSION['member']['locationid'];
        $branchLocations = $ALL_BRANCH ? $Locations->locationList() : $Locations->locationList('', $_SESSION['member']['branchid']);
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($stocklocation, array_column($branchLocations, 'id'))) $stocklocation = $_SESSION['member']['locationid'];
    } else {
        $stocklocation = $_SESSION['member']['locationid'];
    }

    $tData['location'] = $location = $Locations->locationList($stocklocation)[0];

    $title = [];
    $title[] = "Location: ".$location['name'].' - '.$location['branchname'];
    if ($productid) $title[] = "Product: " . Products::$productClass->get($productid)['name'];
    if ($brandid) $title[] = "Brand: " . Models::$staticClass->get($brandid)['name'];
    if ($categoryid) $title[] = "Category: " . ProductCategories::$class->get($categoryid)['name'];
    if ($subcategoryid) $title[] = "Category: " . ProductSubCategories::$class->get($subcategoryid)['name'];
    $tData['title'] = implode(' | ', $title);

    //filter by user location
    if (isset($_GET['stocklocation'])) {
        $products = $Products->reorderList('',$productid,$stocklocation,$categoryid,$subcategoryid,$brandid);
    }

    //arranging data
    $newData = [];
    foreach ($products as $index => $item) {
        $newData[$item['proid']]['id'] = $item['proid'];
        $newData[$item['proid']]['productname'] = $item['productname'];
        $newData[$item['proid']]['generic_name'] = $item['generic_name'];
        $newData[$item['proid']]['description'] = $item['description'];
        $newData[$item['proid']]['barcode_office'] = $item['barcode_office'];
        $newData[$item['proid']]['barcode_manufacture'] = $item['barcode_manufacture'];
        $newData[$item['proid']]['levels'][$item['stockid']]['stockid'] = $item['stockid'];
        $newData[$item['proid']]['levels'][$item['stockid']]['source'] = $item['source'];
        $newData[$item['proid']]['levels'][$item['stockid']]['locid'] = $item['locid'];
        $newData[$item['proid']]['levels'][$item['stockid']]['locname'] = $item['locname'];
        $newData[$item['proid']]['levels'][$item['stockid']]['minqty'] = $item['minqty'];
        $newData[$item['proid']]['levels'][$item['stockid']]['maxqty'] = $item['maxqty'];
    }
//    debug($newData);
    $tData['products'] = $newData;
    $tData['brands'] = Models::$staticClass->getAllActive();
    $tData['categories'] = ProductCategories::$class->getAllActive();
    $tData['subcategories'] = ProductSubCategories::$class->getAllActive();
    $data['content'] = loadTemplate('reorder_level_list.tpl.php', $tData);
}

if ($action == 'save_reorder_level') {
//    debug($_POST);
    $level = $_POST['level'];
    validate($level);

    $existingLevel = $ReorderLevels->find(['stockid' => $level['stockid']]);
    if (empty($existingLevel)) {
        //new
        $level['createdby'] = $_SESSION['member']['id'];
        $ReorderLevels->insert($level);
        $_SESSION['message'] = "Level created successfully";
    } else {
        //update
        $level['modifiedby'] = $_SESSION['member']['id'];
        $ReorderLevels->update($existingLevel[0]['id'], $level);
        $_SESSION['message'] = "Level updated successfully";
    }
    redirect('products', 'reorder_list');
}

if ($action == 'update_base_percentage') {
    if (isset($_POST['base_percent'])) {
        $percent = $_POST['base_percent'];
        $Products->updateWhere(
            ["'j'" => 'j'], //override
            ['baseprice' => $percent]
        );
        $Settings2->update(1, [
            'def_base' => $percent
        ]);
        $_SESSION['message'] = "Base percentage updated successfully";
        redirect('hierarchics', 'quick_price_list');

    }
    $data['content'] = loadTemplate('update_base_percentage.tpl.php', []);
}

if ($action == 'generate_barcode') {
    Users::can(OtherRights::print_barcode, true);
    $productid = $_GET['productid'];
    $locationid = $_GET['locationid'] ?: $_SESSION['member']['locationid'];
    if (!$product = $Products->get($productid)) debug('Product not found');

    if ($product['non_stock']) {
        $_SESSION['error'] = "Non-stock Item  dont support barcode printing";
        redirectBack();
    }

    if (!$location = $Locations->get($locationid)) debug('Location not found');
    $location = $Locations->locationList($locationid)[0];
    $product['barcode'] = $product['barcode_manufacture'] ?: $product['barcode_office'];

    $branchid = $Locations->getBranch($locationid)['id'];
    $priceList = $Products->getPrices($branchid, 0, $productid);
    $product['quick_price'] = $priceList['quick_price_inc'];

    $barcode_settings = $Settings2->get(1)['barcode_settings'];
    if ($barcode_settings) {
        $barcode_settings = base64_decode($barcode_settings);
        $barcode_settings = json_decode($barcode_settings, true);
    } else {
        debug('Barcode Settings not found, Provide barcode settings first');
    }
//    debug($barcode_settings);

    $stock = $Stocks->calcStock(
        $locationid, '', '', '', $productid,
        '', '', '', '', '', '',
        '', '', '', '', '', '',
        false, true, '', '',
        true
    );
    $stock = array_values($stock)[0];
    $product['stock_qty'] = $stock['total'] ?: 0;
    $product['nearby_expiry']='';
    if ($stock) {
        $batches = array_values($stock['batches']);
        $product['nearby_expiry'] = $batches[0]['expire_date'];
    }


    $tData['product'] = $product;
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['barcode_settings'] = $barcode_settings;
    $tData['location'] = $location;
    $data['content'] = loadTemplate('generate_barcode.tpl.php', $tData);
}

if ($action == 'print_barcode') {
    Users::can(OtherRights::print_barcode, true);
    $barcode_data = $_POST;
    validate($barcode_data);
//        debug($barcode_data);

    $barcode_data['product_name'] = $Products->get($barcode_data['productid'])['name'];
    $barcode_data['currencyname'] = $CurrenciesRates->getBaseCurrency()['name'];
    $barcode_settings = $Settings2->get(1)['barcode_settings'];
    if ($barcode_settings) {
        $barcode_settings = base64_decode($barcode_settings);
        $barcode_settings = json_decode($barcode_settings, true);
    }
    $data['barcode_settings'] = $barcode_settings;
    $data['barcode_data'] = $barcode_data;
    $data['layout'] = 'print_barcode.tpl.php';
}

if ($action == 'barcode_setting') {
    Users::isAllowed();
    $product = [
        'name' => 'Sample product number 12',
        'barcode' => '1000',
        'quick_price' => '5000',
    ];

    $barcode_settings = $Settings2->get(1)['barcode_settings'];
    if ($barcode_settings) {
        $barcode_settings = base64_decode($barcode_settings);
        $barcode_settings = json_decode($barcode_settings, true);
    }
//    debug($barcode_settings);


    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['product'] = $product;
    $tData['barcode_settings'] = $barcode_settings;
    $data['content'] = loadTemplate('barcode_setting.tpl.php', $tData);

}

if ($action == 'save_barcode_settings') {
    $barcode_settings = $_POST;
    validate($barcode_settings);
    $barcode_settings['show_company'] = isset($_POST['show_company']);
    $barcode_settings['show_name'] = isset($_POST['show_name']);
    $barcode_settings['show_price'] = isset($_POST['show_price']);
    $barcode_settings['show_expiry'] = isset($_POST['show_expiry']);
//    debug($barcode_settings,1);
    $Settings2->update(1, [
        'barcode_settings' => base64_encode(json_encode($barcode_settings))
    ]);

    $_SESSION['message'] = "Barcode settings saved";
    redirectBack();
}

if ($action == 'warranty_sticker_setting') {
    Users::isAllowed();
    $sample = [
        'header' => 'WARRANTY PROTECTION',
        'serialno' => '1234567890',
        'footer' => 'Warranty void if removed',
    ];

    $sticker_settings = $Settings2->get(1)['warranty_sticker_settings'];
    if ($sticker_settings) {
        $sticker_settings = base64_decode($sticker_settings);
        $sticker_settings = json_decode($sticker_settings, true);
    }
//    debug($sticker_settings);


    $tData['sample'] = $sample;
    $tData['sticker_settings'] = $sticker_settings;
    $data['content'] = loadTemplate('warranty_sticker_setting.tpl.php', $tData);

}

if ($action == 'save_warranty_sticker_setting') {
//    debug($_POST);
    $sticker_settings = $_POST['sticker'];
    validate($sticker_settings);
//    debug($barcode_settings,1);
    $Settings2->update(1, [
        'warranty_sticker_settings' => base64_encode(json_encode($sticker_settings))
    ]);

    $_SESSION['message'] = "Sticker settings saved";
    redirectBack();
}

if ($action == 'stock_split_patterns') {

}


if ($action == 'ajax_getProducts') {

    $locationid = $_GET['locationid'];
    $non_stock = $_GET['non_stock'];
    $expiring = $_GET['expiring'];
    $include_stock = isset($_GET['include_stock']) && $locationid; //include stock quantity in product label
    define('EXCEPT_PROFORMA', $_GET['except_proforma']); //proforma to be excluded from stock holding

    $icData = Products::$productClass->search($_GET['search']['term'], $non_stock, $expiring);
    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj = null;
            $obj->id = $ic['productid'];
            $obj->description = $ic['description'];
            $obj->barcode = $ic['barcode_office'] ?: $ic['barcode_manufacture'];

            if ($include_stock && !$ic['none_stock']) {
                $stock = $Stocks->calcStock(
                    $locationid, '', '', '', $ic['productid'],
                    '', '', '', '', '', '',
                    '', '', '', '', '', '',
                    false, true, '', '',
                    true, true
                );
                $stock = array_values($stock)[0];
                $total = $stock['total'] ?? 0;
                $obj->text = $ic['name'] . "      ({$total})";  //including stock qty
                $obj->stock_qty = $total;
            } else {
                $obj->text = $ic['name'];
            }

            if (!empty($_GET['notselect2'])) {
                $response[] = $obj;
            } else {
                $response['results'][] = $obj;
            }
        }
    } else {
        $obj = null;
        $obj->test = 'No results';
        $obj->id = 0;

        if (!empty($_GET['notselect2'])) {
            $response[] = $obj;
        } else {
            $response['results'][] = $obj;
        }
    }

    $data['content'] = $response;
}

if ($action == 'ajax_getStockProductDetails') {
//    debug($_GET);
    $user = $_SESSION['member'];
    $stockid = $_GET['stockid'];
    define('EXCEPT_PROFORMA', $_GET['except_proforma']); //proforma to be excluded from stock holding
    if (!empty($_GET['id'])) {
        $productId = $_GET['id'];
    }
    if (!empty($_GET['locationid'])) {
        $locationid = $_GET['locationid'];
    }
    $product = $Products->getStockProduct_details($stockid, $locationid, $productId);
    $hierarchic = $Hierarchics->get($_SESSION['member']['hierachicid']);
    $branchid = $Locations->getBranch($locationid)['id'];
//     debug($product);
    $list = null;
    if ($product) {
        $currentStock = $Stocks->calcStock($locationid, $stockid, "",
            "", "", "", "", "",
            "", "", "", "", "", "", "",
            "", "", false, true, '', '',
            true, true);
        $currentStock = array_values($currentStock)[0];
        $priceList = $Products->getPrices($branchid, $hierarchic['level'], $product['productid']);
        $priceList['inc_quicksale_price'] = $priceList['quick_price_inc'];

//         debug($currentStock);
        $list->found = 'yes';
        $list->productid = $product['productid'];
        $list->stockid = $stockid;
        $list->productname = $product['productname'];
        $list->description = $product['description'];
        $list->point = $product['point'];
        $list->unit = $product['unit'];
        $list->forquick_sale = $product['forquick_sale'];
        $list->barcode_manufacture = $product['barcode_manufacture'];
        $list->barcode_office = $product['barcode_office'];
        $list->categoryname = $product['categoryname'];
        $list->vat_rate = $product['vat_rate'];
        $list->locid = $product['locid'];
        $list->proid = $product['proid'];

        $list->costprice = $priceList['costprice'];
        $list->baseprice = $product['baseprice'];
        $list->minimum = IS_ADMIN ? 0 : $priceList['minimum'];
        $list->incminimum = addTAX($list->minimum, $product['vat_rate']);
        $list->maximum = $priceList['maximum'];
        $list->incmaximum = addTAX($list->maximum, $product['vat_rate']);
        $list->suggestedprice = $priceList['suggested'];
        $list->quicksale_price = getExclAmount($priceList['inc_quicksale_price'], $product['vat_rate']);
        $list->quicksale_price_VAT = $priceList['inc_quicksale_price'];

        //discounts
        $list->max_discount_percent = $priceList['max_discount_percent'];
        $list->max_quicksale_disc_percent = $priceList['max_quicksale_disc_percent'];

        foreach ($currentStock['batches'] as $bi => $stockBatch) {
            $currentStock['batches'] [$bi]['expire_description'] = fExpireDays($stockBatch['expire_remain_days']);
        }
//        debug($currentStock);
        $list->quantity = $currentStock['total'] ?? 0;
        $list->batch_stock = $currentStock['batches'];

        $list->trackserial = $product['trackserialno'];
        $list->validate_serialno = $product['validate_serialno'];
        $list->track_expire_date = $product['track_expire_date'];
        $list->prescription_required = $product['prescription_required'];
        $list->bulkname = $product['bulkname'];
        $list->bulk_rate = $product['bulk_rate'];
        $list->unitname = $product['unitname'];
        $list->unitabbr = $product['unitabbr'];
    } else {
        $list->found = 'no';
    }
    $response[] = $list;
//    debug($list);
    $data['content'] = $response;
}

if ($action == 'ajax_getProductDetailsForGRN') {
    $obj->status = "success";

    $productid = $_GET['productid'];
    $locationid = $_GET['locationid'];

    if (!$productid) {
        $obj->status = 'error';
        $obj->msg = 'Product is required';
    }
    if (!$locationid) {
        $obj->status = 'error';
        $obj->msg = 'Location is required';
    }
    $product = $Products->get($productid);
    if ($product = $Products->get($productid)) {
        $product['bulk_unit'] = $BulkUnits->get($product['bulk_units']);
        $product['single_unit'] = $Units->get($product['unit']);
        $product['category'] = $Categories->get($product['categoryid']);

        $branchid = $Locations->getBranch($locationid)['id'];
        $currentPrices = $CurrentPrices->quickPriceList($branchid, $productid)[0];
        $product['quicksale_price'] = $currentPrices['inc_quicksale_price'];
        $product['costprice'] = (double)$currentPrices['costprice'];
        $highest_percentage = $Hierarchics->highestLevel()['percentage'];
        $product['baseprice'] = $product['baseprice'] > $highest_percentage ? $product['baseprice'] : $highest_percentage;

        $obj->data = $product;
    } else {
        $obj->status = 'error';
        $obj->msg = 'Product not found';
    }

    $data['content'] = $obj;
}

if ($action == 'ajax_searchProduct') {
    $search = $_GET['search'];
    $non_stock = $_GET['non_stock'];
    $with_stock = isset($_GET['include_stock']);
    $with_expired = $_GET['with_expired']=='yes';
    $locationid = $_GET['locationid'];
    define('EXCEPT_PROFORMA', $_GET['except_proforma']); //proforma to be excluded from stock holding

    $stocks = [];
    if ($with_stock && $locationid) {
        $stocks = $Stocks->calcStock(
            $locationid, "",
            '', $search, '', "", "", "",
            "", $taxcategory, $brandid, $departmentid, "", "", "", "",
            "", $with_expired, true, $productcategoryid, $subcategoryid,
            true, true);
        $stocks = array_values($stocks);
        $not_in_ids = array_column($stocks, 'productid');
    }
    $products = $Products->getList('', $search, '', '', '',
        '', '', '', '', $non_stock, '', $not_in_ids, 'active');
    $response['result'] = array_merge($stocks, $products);
//    debug($response['result'] );
    $data['content'] = $response;
}

if ($action == 'ajax_getProductInfoAndClients') {
    $productid = $_GET['productid'];
    $currency_rateid = $_GET['currency_rateid'];

    $productDetails = $Products->getProductDetails($productid);

    $clientList = $Sales->getPurchasedProductClient($productid, Users::can(OtherRights::approve_other_credit_invoice) ? "" : $_SESSION['member']['id']);

    if ($productDetails) {
        $list->found = 'yes';
        $list->details = $productDetails;
        $list->clientList = $clientList;
        $list->total_stock = 0;
        if (isset($_GET['with_total_stock'])) {
            foreach ($Locations->getAllActive() as $l) {
                $currentStock = $Stocks->calcStock(
                    $l['id'], '', '', '', $productid, '',
                    '', '', '', '', '', '',
                    '', '', '', '', '', false,
                    true, '', '', true, false
                );
                $currentStock = array_values($currentStock)[0];
                $list->total_stock += $currentStock['total'];
            }
        }
    } else {
        $list->found = 'no';
    }
//    debug($list);
    $data['content'] = $list;
}

if ($action == 'ajax_getProductStockView') {
    $locationId = $_GET['locationId'];
    $productId = $_GET['productId'];
    $product = $Products->get($productId);
    if (!empty($product)) {
        $response['found'] = 'yes';
        $location = $Locations->get($locationId);
        $stock = $Stocks->find(['productid' => $productId, 'locid' => $locationId]);
        $stockId = $stock[0]['id'];

        $response['data']['productid'] = $product['id'];
        $response['data']['productname'] = $product['name'];
        $response['data']['track_expire_date'] = $product['track_expire_date'];
        $response['data']['locationname'] = $location['name'];

        //active pending order
        $orderDetails = $Orderdetails->getList('', $locationId, $productId, Orders::STATUS_PENDING);
        $response['data']['pending_order'] = array_sum(array_column($orderDetails, 'qty'));

        //pending sales
        $salesDetails = $Salesdetails->getList('', $locationId, $productId, "'0'");
        $response['data']['pending_sale'] = array_sum(array_column($salesDetails, 'quantity'));

        //expecting stock
        $expectingStock = $LPO->expectingStock($locationId, $productId);
        $expectingStock = array_sum(array_column($expectingStock, 'qty'));

        $response['data']['expecting_stock'] = $expectingStock;
        $response['data']['in_stock_qty'] = 0;
        $response['data']['held_stock'] = 0;
        $response['data']['available_stock'] = 0;
        $response['data']['batches'] = [];

        if (!empty($stockId)) {
            $currentStock = $Stocks->calcStock(
                $locationId, $stockId, '', '', '', '',
                '', '', '', '', '', '',
                '', '', '', '', '', false,
                true, '', '', true, true
            );
            $currentStock = array_values($currentStock)[0];
            if ($currentStock) {
                $response['data']['in_stock_qty'] = $currentStock['in_stock_qty'];
                $response['data']['held_stock'] = $currentStock['held_stock'];
                $response['data']['available_stock'] = $currentStock['total'];
                $response['data']['batches'] = $currentStock['batches'];
            }
        }
    } else {
        $response['found'] = 'no';
    }
//    debug($response);
    $data['content'] = $response;
}

if ($action == 'ajax_expectingStocks') {
    $productid = $_GET['productid'];
    $locationid = $_GET['locationid'];
    $obj->status = 'success';
    try {
        if (!$product = $Products->get($productid)) throw new Exception("product not found!");
        if (!$location = $Locations->get($locationid)) throw new Exception("location not found!");
        $expectingStocks = $LPO->expectingStock($locationid, $productid);
        $expectingStocks = array_map(function ($item) {
            $days = date_diff(date_create(fDate($item['expecting_in'], 'Y-m-d')), new DateTime())->days;
            $expect_in = fDate($item['expecting_in']);
            $expect_in = $item['time_passed'] && $days > 0 ? $expect_in . ", $days day(s) ago" : $days . " days";
            $item['expecting_in'] = $expect_in;
            return $item;
        }, $expectingStocks);
//        debug($expectingStocks);
        $obj->data = $expectingStocks;
    } catch (Exception $e) {
        $obj->status = 'error';
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_checkQuickSalePrice') {
//    debug($_GET);
    $productid = $_GET['productid'];
    $locationid = $_GET['locationid'];
    $newprice = $_GET['newprice'];
    $newprice = floatval($newprice);
    $results['status'] = "success";
    try {
        if (!$product = $Products->get($productid)) throw new Exception('Product not found!');
        if (!$branch = $Locations->getBranch($locationid)) throw new Exception('Location branch not found!');
        if ($newprice <= 0) throw new Exception('Invalid quick price!');

        $branchPrice = $CurrentPrices->quickPriceList($branch['id'], $product['id'])[0];
        if ($branchPrice['costprice']) {
            if ($newprice < $branchPrice['inc_base']) throw new Exception("Quick price is below base price " . formatN($branchPrice['inc_base']));
        }
    } catch (Exception $e) {
        $results['status'] = "error";
        $results['msg'] = $e->getMessage();
    }
    $data['content'] = $results;
}

if ($action == 'ajax_checkbarcode') {
    $barcode = $_GET['barcode'];
    $productid = $_GET['productid'];

    $result['status'] = 'new';
    try {
        $barcodeexists = $Products->byBarcode($barcode, false);
        if ($productid) {
            $barcodeexists = array_filter($barcodeexists, function ($p) use ($productid) {
                return $p['id'] != $productid;
            });
        }
        if (count($barcodeexists) > 0) throw new Exception("exists");
    } catch (Exception $e) {
        $result['status'] = 'exists';
    }
    $data['content'] = $result;
}

if ($action == 'ajax_checkProduct') {
    $productname = $_GET['productname'];
    $productid = $_GET['productid'];

    $result['status'] = 'new';
    try {
        $existing_products = $Products->find(['name' => $productname]);
        if ($productid) {
            $existing_products = array_filter($existing_products, function ($p) use ($productid) {
                return $p['id'] != $productid;
            });
        }
        if (count($existing_products) > 0) throw new Exception("exists");
    } catch (Exception $e) {
        $result['status'] = 'exists';
    }
    $data['content'] = $result;
}

if ($action == 'ajax_getSaleDetails') {
    $productid = $_GET['productid'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $fromdate = $_GET['fromdate'] ?: date('Y-m-d', strtotime('-3 months'));
    $obj->status = 'success';
    $details = $Sales->saleDetails(Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'], '', false, $fromdate, '',
        '', $locationid, $productid, '', '', false, $branchid);
    if (!empty($details)) {
        $obj->data = $details;
    } else {
        $obj->status = 'error';
        $obj->msg = 'Product sales not found';
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getPurchaseHistory') {
    $productid = $_GET['productid'];
    $fromdate = $_GET['fromdate'] ?: date('Y-m-d', strtotime('-3 months'));
    $obj->status = 'success';
    $details = $GRN->purchaseList($productid, $fromdate);
    if (!empty($details)) {
        $obj->data = $details;
    } else {
        $obj->status = 'error';
        $obj->msg = 'Purchase history not found';
    }
    $data['content'] = $obj;
}
