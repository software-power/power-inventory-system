<?
if ($action == 'index') {
    Users::isAllowed();
    $name = $_GET['name'];
    $tData['hierarchic_list'] = $Hierarchics->getAll();
    $data['content'] = loadTemplate('hierarchics_list.tpl.php', $tData);
}

if ($action == 'hierarchics_save') {
//    debug($_POST);
    $id = $_POST['hierarchic']['id'];
    $hierarchic = $_POST['hierarchic'];

    validate($hierarchic);

    if (!empty($id)) {
        // editing part
        $hierarchic['modifiedby'] = $_SESSION['member']['id'];
        $Hierarchics->update($id, $hierarchic);
        $_SESSION['message'] = 'Hierarchic Updated successfully';
    } else {
        // new hierarchic
        $exists = $Hierarchics->search($hierarchic['name']);
        if (empty($exists)) {
            $hierarchic['createdby'] = $_SESSION['member']['id'];
            $Hierarchics->insert($hierarchic);
            $lastSaved = $Hierarchics->lastid();
            $_SESSION['message'] = 'Hierarchic Added successfully';
        } else {
            $_SESSION['error'] = 'Hierarchic Already Exists';
        }
    }
    redirect('hierarchics', 'index');
}

if ($action == 'delete') {
    $id = $_POST['id'];
    if ($id) {
        $Hierarchics->delete($id);
        $_SESSION['message'] = 'Hierarchic Deleted successfully';
    }
    redirect('hierarchics', 'index');
}


if ($action == 'product_hierarchs_list') {
    Users::isAllowed();
    $tData['hierarchicList'] = $Hierarchics->getAllActive();
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");

    $query_items = false;

    $productid = $_GET['productid'];
    $brandid = $_GET['brandid'];
    $categoryid = $_GET['categoryid'];
    if (IS_ADMIN) {
        $hierarchicid = $_GET['hierarchicId'];
        $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    } elseif (Users::can(OtherRights::approve_other_credit_invoice)) {
        $hierarchicid = $_SESSION['member']['hierachicid'];
        $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    } else {
        $hierarchicid = $_SESSION['member']['hierachicid'];
        $branchid = $_SESSION['member']['branchid'];
    }

    $title = [];
    if ($branchid) $title[] = "Branch :" . $Branches->get($branchid)['name'];
    if ($productid) {
        $query_items = true;
        $title[] = "Product :" . $Products->get($productid)['name'];
    }
    if ($hierarchicid) {
        $hierarchic = $Hierarchics->get($hierarchicid);
        $title[] = "Starting from Hierarchic :" . $hierarchic['name'] . " Level " . $hierarchic['level'];
        $level = $hierarchic['level'];
    }
    if ($brandid) {
        $query_items = true;
        $title[] = "Brand: " . $Models->get($brandid)['name'];
    }
    if ($categoryid) {
        $query_items = true;
        $title[] = "Category: " . $ProductCategories->get($categoryid)['name'];
    }

    $tData['title'] = implode(' | ', $title);

    if ($query_items) {
        $products = $HierarchicPrices->getProductInfoWithPrices($branchid, $productid, '', $level, $brandid, $categoryid);
    }
//    debug($products);
    $newArray = [];
    foreach ($products as $key => $product) {
        $newArray[$product['productid']]['productid'] = $product['productid'];
        $newArray[$product['productid']]['productname'] = $product['productname'];
        $newArray[$product['productid']]['baseprice'] = $product['baseprice'];
        $newArray[$product['productid']]['costprice'] = $product['costprice'];
        $newArray[$product['productid']]['quicksale_price'] = $product['quicksale_price'];
        $newArray[$product['productid']]['branchid'] = $product['branchid'];
        $newArray[$product['productid']]['branchname'] = $product['branchname'];
        $newArray[$product['productid']]['brandName'] = $product['brandName'];
        $newArray[$product['productid']]['taxCategory'] = $product['taxCategory'];
        $newArray[$product['productid']]['vat_percent'] = $product['vat_percent'];
        $newArray[$product['productid']]['departmentName'] = $product['departmentName'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['hierarchicId'] = $product['hierarchicId'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['hpi'] = $product['hpi'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['hierarchicname'] = $product['hierarchicname'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['level'] = $product['level'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['percentage'] = $product['percentage'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['commission'] = $product['commission'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['target'] = $product['target'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['source'] = $product['source'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['below_base'] = $product['below_base'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['exc_price'] = $product['exc_price'];
        $newArray[$product['productid']]['hierarchics'][$product['hierarchicId']]['inc_price'] = $product['inc_price'];
    }
//    debug($newArray);

    $tData['pricelist'] = array_values($newArray);
    $tData['brands'] = $Models->getAllActive();
    $tData['categories'] = $ProductCategories->getAllActive();
    $data['content'] = loadTemplate('price_list.tpl.php', $tData);
}

if ($action == 'export_price_list' || $action == 'export_price_with_stock') {
    Users::isAllowed();

    if ($action == 'export_price_with_stock') {
        $tData['with_stock'] = $data['with_stock'] = $with_stock = true;
    }

    $productid = $_GET['productid'];
    $price_hierarchic = $_GET['price_hierarchic'] ?: $_SESSION['member']['hierachicid'];
    $modelid = $_GET['modelid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $deptid = $_GET['deptid'];
    $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    $print_pdf = isset($_GET['print_pdf']);
    $tData['pdf_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&print_pdf";

    $title = [];
    if ($branchid) $title[] = "Branch :" . $Branches->get($branchid)['name'];
    if ($productid) $title[] = "Product :" . $Products->get($productid)['name'];
    if ($modelid) $title[] = "Brand :" . $Models->get($modelid)['name'];
    if ($productcategoryid) $title[] = "Category :" . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory :" . $ProductSubCategories->get($subcategoryid)['name'];
    if ($deptid) $title[] = "Department :" . $Departments->get($deptid)['name'];
    if ($price_hierarchic == 'quick_sale') {
        $title[] = "Level: Quick Price";
        $level = 1;
        $quick_price = true;
    } else {
        $hierarchic = $Hierarchics->get($price_hierarchic);
        $level = $hierarchic['level'];
        $title[] = "Level: " . $hierarchic['name'] . " hierarchic";
    }
    $tData['title'] = implode(' | ', $title);

    $products = $HierarchicPrices->getProductPriceExport($branchid, $productid, $level, $modelid, $productcategoryid, $subcategoryid, $deptid);


    $branch_locations = array_column($Locations->find(['branchid' => $branchid]), 'id');
    foreach ($products as $index => $p) {
        if ($quick_price) {
            $export_incprice = $p['inc_quicksale_price'];
            $export_excprice = $p['exc_quicksale_price'];
        } else {
            $export_incprice = $p['below_base'] ? $p['inc_base'] : $p['inc_price'];
            $export_excprice = $p['below_base'] ? $p['exc_base'] : $p['exc_price'];
        }

        if ($export_incprice <= 0) {
            unset($products[$index]);
            continue;
        }
        $products[$index]['export_incprice'] = $export_incprice;
        $products[$index]['export_excprice'] = $export_excprice;

        if ($with_stock) {
            foreach ($branch_locations as $locationid) {
                $current_stock = $Stocks->calcStock(
                    $locationid, '', "",
                    "", $p['productid'], "", "",
                    "", "", "", "", "", "",
                    "", "", "", "", false, true,
                    '', '', true
                );
                $current_stock = array_values($current_stock)[0];
                $products[$index]['branch_stock_qty'] += $current_stock['total'];
            }
        }

//        debug($products[$index]);
    }

    if ($print_pdf) {
        $data['products'] = $products;
        $data['basecurrency'] = $Currencies->find(['base' => 'yes'])[0];
        $data['layout'] = 'product_price_export_print.tpl.php';
    } else {
        $tData['products'] = $products;
        $user_level = $Hierarchics->get($_SESSION['member']['hierachicid'])['level'];
        $tData['hierarchicList'] = IS_ADMIN ? $Hierarchics->getAllActive() : $Hierarchics->getList('', $user_level);
        $tData['basecurrency'] = $Currencies->find(['base' => 'yes'])[0];
        $tData['departments'] = $Departments->getAllActive();
        $tData['models'] = $Models->getAllActive();
        $tData['productcategories'] = $ProductCategories->getAllActive();
        $tData['subcategories'] = $ProductSubCategories->getAllActive();
        $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id")
            : $Branches->find(['id' => $_SESSION['member']['branchid']]);
        $_SESSION['pagetitle'] = CS_COMPANY . " " . $tData['title'];
        $data['content'] = loadTemplate('export_price_list.tpl.php', $tData);
    }
}

if ($action == 'product_hierarchics') {
    Users::can(OtherRights::edit_price, true);

    $productid = $_GET['productid'];
    $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];

    if (!$product = $Products->get($productid)) {
        $_SESSION['error'] = "Product Not found";
        redirectBack();
    }

    $productPrices = $HierarchicPrices->getProductInfoWithPrices($branchid, $productid);
//    debug($productPrices);
    $tData['branches'] = Users::can(OtherRights::view_all_branch_stock) ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $tData['currentBranch'] = $Branches->get($branchid);
    $tData['productPrices'] = $productPrices;
    $data['content'] = loadTemplate('hierarchics_product_edit.tpl.php', $tData);
}

if ($action == 'product_hierarchics_save') {
    Users::can(OtherRights::edit_price, true);
//    debug($_POST);
    $productid = $_POST['productid'];
    $branchid = $_POST['branchid'];
    $hierarchicid = $_POST['hierarchicid'];
    $percentage = $_POST['percentage'];
    $commission = $_POST['commission'];
    $target = $_POST['target'];
    $quicksale_price = $_POST['quicksale_price'];

    //todo current price update mechanism

    validate($productid);
    validate($branchid);
    validate($percentage);

    if ($product = $Products->get($productid)) {
        foreach ($hierarchicid as $index => $hid) {
            $hp = $HierarchicPrices->find([
                'hierachicid' => $hid,
                'productid' => $productid,
                'branchid' => $branchid,
            ]);
            if ($hp) {
                $HierarchicPrices->update($hp[0]['id'], [
                    'hierachicid' => $hid,
                    'productid' => $productid,
                    'branchid' => $branchid,
                    'percentage' => $percentage[$index],
                    'commission' => $commission[$index],
                    'target' => $target[$index],
                    'modifiedby' => $_SESSION['member']['id']
                ]);
            } else {
                $HierarchicPrices->insert([
                    'hierachicid' => $hid,
                    'productid' => $productid,
                    'branchid' => $branchid,
                    'percentage' => $percentage[$index],
                    'commission' => $commission[$index],
                    'target' => $target[$index],
                    'createdby' => $_SESSION['member']['id']
                ]);
            }
        }

        if ($quicksale_price) CurrentPrices::updatePrice($branchid, $productid, 0, $quicksale_price, 0, "Quick Price");

        $_SESSION['message'] = "Price updated successfully";
        $_SESSION['delay'] = 3000;
    } else {
        $_SESSION['error'] = "Product Not found";
        $_SESSION['delay'] = 3000;
    }

    if ($_POST['redirect']) {
        header('Location: ' . base64_decode($_POST['redirect']));
        die();
    } else {
        redirectBack();
    }
}

if ($action == 'delete_price_hierarchic') {
    Users::can(OtherRights::edit_price, true);
    $id = $_POST['hpi'];
    if ($id) {
        $HierarchicPrices->real_delete($id);
        $_SESSION['message'] = "Price removed successfully";
    }

    redirectBack();
}

if ($action == 'quick_price_list' || $action == 'quick_price_list_with_last_purchase') {
    Users::isAllowed();

    $tData['WITH_PURCHASE'] = $WITH_PURCHASE = $action == 'quick_price_list_with_last_purchase';

    $productid = $_GET['productid'];
    $brandid = $_GET['brandid'];
    $categoryid = $_GET['categoryid'];
    $subcategoryid = $_GET['subcategoryid'];


    $branchid = IS_ADMIN ? ($_GET['branchid'] ?: $_SESSION['member']['branchid']) : $_SESSION['member']['branchid'];
    $title = [];
    if ($branchid) $title[] = "Branch: " . Branches::$branchClass->get($branchid)['name'];
    if ($productid) $title[] = "Product: " . Products::$productClass->get($productid)['name'];
    if ($brandid) $title[] = "Brand: " . Models::$staticClass->get($brandid)['name'];
    if ($categoryid) $title[] = "Category: " . ProductCategories::$class->get($categoryid)['name'];
    if ($subcategoryid) $title[] = "Category: " . ProductSubCategories::$class->get($subcategoryid)['name'];
    $tData['title'] = implode(' | ', $title);


    if (isset($_GET['branchid'])) $products = $CurrentPrices->quickPriceList($branchid, $productid, $brandid, $categoryid, $subcategoryid);
    $below_base = 0;
    foreach ($products as $i => $product) {
        if ($product['inc_base'] > $product['inc_quicksale_price']) $below_base++;
        if ($WITH_PURCHASE) {
            $last_purchase = GRNDetails::$grnDetailsClass->getBranchLastPurchase($branchid, $product['productid']);
            if ($last_purchase) $products[$i]['last_purchasedate'] = $last_purchase[0]['last_purchasedate'];
        }
    }
//    debug($products);
    $tData['products'] = $products;
    $tData['below_base'] = $below_base;
    $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $tData['brands'] = Models::$staticClass->getAllActive();
    $tData['categories'] = ProductCategories::$class->getAllActive();
    $tData['subcategories'] = ProductSubCategories::$class->getAllActive();

    $data['content'] = loadTemplate('quick_price_list.tpl.php', $tData);
}

if ($action == 'update_quick_price') {
    Users::can(OtherRights::edit_price, true);
    $productid = $_POST['productid'];
    $branchid = $_POST['branchid'];
    $newPrice = $_POST['newPrice'];

//    debug($_POST);

    validate($productid);
    validate($branchid);
    validate($newPrice);

    $result = CurrentPrices::updatePrice($branchid, $productid, 0, $newPrice, 0, "Quick Price");
    if ($result['status'] == 'success') {
        $_SESSION['message'] = "Quick price updated successfully";
    } else {
        $_SESSION['error'] = $result['msg'];
    }
    redirect('hierarchics', 'quick_price_list');
}

if ($action == 'update_costprice') {
    Users::can(OtherRights::edit_costprice, true);
    $branchid = $_POST['branchid'];
    $productid = $_POST['productid'];
    $costprice = $_POST['costprice'];

    validate($branchid);
    validate($productid);
    validate($costprice);


    try {
        if (!$product = $Products->get($productid)) throw new Exception("product not found!");
        if (!$branch = $Branches->get($branchid)) throw new Exception("branch not found!");
//        debug($_POST);

        $result = CurrentPrices::updatePrice($branchid, $productid, $costprice, 0, 0);
        if ($result['status'] == 'error') throw new Exception($result['msg']);

        $_SESSION['message'] = "Cost price updated successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage() . ", Costprice not updated";
    }

    redirectBack();
}

if ($action == 'price_history') {
    Users::isAllowed();
    $productid = $_GET['productid'];
    if (IS_ADMIN) {
        $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    } else {
        $branchid = $_SESSION['member']['branchid'];
    }

    $product = $Products->get($productid);
    $branch = $Branches->get($branchid);

    $title = [];
    $title[] = "Branch: " . $branch['name'];
    if ($product) $title[] = "Product: " . $product['name'];
    $tData['title'] = implode(' | ', $title);

    if ($branch && $product) {
        $logs = $PriceLogs->getList($branchid, $productid);
        $lines = explode(PHP_EOL, $logs[0]['remarks']);
//        debug($lines);
        $tData['logs'] = $logs;
    }

    $tData['currentBranch'] = $branch;
    $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $data['content'] = loadTemplate('price_history.tpl.php', $tData);
}

if ($action == 'ajax_getProductHierarchics') {
    $productid = $_GET['productid'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $currency_rateid = $_GET['currency_rateid'];
    if ($locationid) $branchid = $Locations->getBranch($locationid)['id'];
    $obj->status = 'success';
    try {
        if (!$branchid) throw new Exception('Invalid branch!');

        if (IS_ADMIN) {
            $level = '';
        } elseif ($_SESSION['member']['branchid'] != $branchid) {
            $level = $Hierarchics->highestLevel()['level'];
        } else {
            $level = $Hierarchics->get($_SESSION['member']['hierachicid'])['level'];
        }
//        debug($level);
        $productPrices = $HierarchicPrices->getProductInfoWithPrices($branchid, $productid, '', $level);
        if (!($productPrices[0]['costprice'])) throw new Exception('Product prices not found in this branch!');
        $productPrices = array_filter($productPrices, function ($p) {
            return !$p['below_base'];
        });
        $productPrices = array_values($productPrices);
        if (!$productPrices) {
            $priceList = $Products->getPrices($branchid, $level, $productid);
            $productInfo = $Products->getList($productid)[0];
            $productPrices[] = [
                'hierarchicname' => 'Base price',
                'level' => '',
                'commission' => '',
                'target' => '',
                'costprice' => $priceList['costprice'],
                'inc_quicksale_price' => $priceList['quick_price_inc'],
                'exc_price' => $priceList['maximum'],
                'vat_percent' => $productInfo['vatPercent'],
                'inc_price' => addTAX($priceList['maximum'], $productInfo['vatPercent']),
            ];
        }
//        debug($productPrices);

        if ($currency_rate = $CurrenciesRates->get($currency_rateid)) {
            foreach ($productPrices as $index => $item) {
                $productPrices[$index]['foreign_inc_price'] = round($item['inc_price'] / $currency_rate['rate_amount'], 2);
            }
        }
        $obj->data = $productPrices;
    } catch (Exception $e) {
        $obj->status = 'error';
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getProductPrice') {
//    debug($_GET);
    $productid = $_GET['productid'];
    $from_proforma = isset($_GET['for_proforma']);
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    if ($locationid) $branchid = $Locations->getBranch($locationid)['id'];
    $hierarchic = $Hierarchics->get($_SESSION['member']['hierachicid']);

    $obj->status = 'success';

    $product = $Products->getProductDetails($productid);
    $prices = $Products->getPrices($branchid, $hierarchic['level'], $productid);
//    debug($prices);
    $product['suggested'] = $prices['suggested'];
    $product['minimum'] = IS_ADMIN || $from_proforma ? 0 : $prices['minimum'];

    if (!$product) $obj->status = 'error';

    $obj->data = $product;
    $data['content'] = $obj;
}
