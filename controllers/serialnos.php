<?
if ($action == 'index') {
    debug('index');
}

if ($action == 'add_product_serialno') {
//    debug('add serial no');
    Users::isAllowed();
    $productid = $_GET['productid'];
    $locationid = $_GET['locationid'];
    $grnid = $_GET['grnid'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$locationid) $locationid = $_SESSION['member']['locationid'];
        $branchLocations = $ALL_BRANCH ? $Locations->locationList() : $Locations->locationList('', $_SESSION['member']['branchid']);
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($locationid, array_column($branchLocations, 'id'))) $locationid = $_SESSION['member']['locationid'];
    } else {
        $locationid = $_SESSION['member']['locationid'];
    }

    if ($productid) {
        try {
            $product = $Products->getList($productid)[0];
            if (!$product['trackserialno']) throw new Exception("Product {$product['name']} dont track serial no!");

            if ($grnid) {
                $grnInfo = $GRN::currentGrnState($grnid, $productid, '', '', false, 'in_stock');
                if (!$grnInfo) throw new Exception('No GRN found!');
                $tData['grnInfo'] = $grnInfo;
                $grnitem = array_values($grnInfo['stock'])[0];
                $product['gdi'] = $grnitem['gdi'];
                $product['stockid'] = $grnitem['stockid'];
                $product['locationid'] = $grnInfo['st_locid'];
                $location = $Locations->locationList($grnInfo['st_locid'])[0];
                $product['locationname'] = $location['name'] . " - " . $location['branchname'];
                $product['purchased_qty'] = $grnitem['qty'];
                $product['stock_qty'] = $grnitem['current_stock_qty'];
                $product['serial_in_stock'] = $grnitem['serialnos_count'];
                $product['stock_qty_without_serial'] = $product['stock_qty'] - $product['serial_in_stock'];
                $product['stock_qty_without_serial'] = $product['stock_qty_without_serial'] < 0 ? 0 : $product['stock_qty_without_serial'];
//                debug($product);
            } else {

                $stocks = $Stocks->calcStock(
                    $locationid, '', '', '', $productid, '', '', '', '',
                    '', '', '', '', '', '', '', '', false,
                    true, '', ''
                );
                $stock = array_values($stocks)[0];
                if (!$stock) throw new Exception('No Stock found!');
                $product['stockid'] = $stock['id'];
                $product['locationid'] = $locationid;
                $location = $Locations->locationList($locationid)[0];
                $product['locationname'] = $location['name'] . " - " . $location['branchname'];
                $product['stock_qty'] = $stock['total'];
                $product['serial_in_stock'] = count($SerialNos->getList($locationid, $productid, '', '', '', 'in_stock'));
                $product['stock_qty_without_serial'] = $product['stock_qty'] - $product['serial_in_stock'];
                $product['stock_qty_without_serial'] = $product['stock_qty_without_serial'] < 0 ? 0 : $product['stock_qty_without_serial'];
            }

            $tData['product'] = $product;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $grnid ? redirectBack() : redirect('serialnos', 'add_product_serialno');
        }
    }

    $data['content'] = loadTemplate('add_product_serialno.tpl.php', $tData);
}

if ($action == 'upload_serialno') {
    $stockid = $_POST['stockid'];
    $gdi = $_POST['gdi'];
    $serialnos = $_POST['serialno'];
    validate($stockid);
    validate($serialnos);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        foreach ($serialnos as $no) {
            if ($SerialNos->find(['number' => $no])) throw new Exception("System found serial number {$no} already exists,transaction canceled!");
            $SerialNos->insert([
                'number' => $no,
                'initial_stockid' => $stockid,
                'current_stock_id' => $stockid,
                'gdi' => $gdi ?: '',
                'source' => $gdi ? SerialNos::SOURCE_GRN : SerialNos::SOURCE_UPLOAD,
                'createdby' => $_SESSION['member']['id'],
            ]);
        }
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Serial number uploaded successfully";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 5000;
    }


    $gdi ? redirect('grns', 'add_serialno') : redirectBack();
}

if ($action == 'print_warranty_sticker') {
//    debug($_GET);
    $snoid = removeSpecialCharacters($_GET['snoid']);
    $serialno = SerialNos::$serialNoClass->get($snoid);
    if (!$serialno) debug("Serial No not found!");

    $serialno = SerialNos::$serialNoClass->getList('', '', '', '', '', 'sold', '',
        '', '', $snoid)[0];
    if (!$serialno) debug("Serial No not Sold");

    $sticker_settings = $Settings2->get(1)['warranty_sticker_settings'];
    if ($sticker_settings) {
        $sticker_settings = base64_decode($sticker_settings);
        $sticker_settings = json_decode($sticker_settings, true);
    } else {
        debug("Warranty sticker settings not found");
    }

//    debug($sticker_settings);
    $data['sno'] = $serialno;
    $data['sticker_settings'] = $sticker_settings;
    $data['layout'] = 'print_warranty_sticker.tpl.php';
}

if ($action == 'send_serialno_to_support') {
    $salesid = $_GET['salesid'];
    $result = SerialNos::sendSerialnoToSupport($salesid);
    if ($result['status'] == 'success') {
        $_SESSION['message'] = $result['msg'];
    } else {
        $_SESSION['error'] = $result['msg'];
    }
    $_SESSION['delay'] = 4000;
    redirectBack();
}

if ($action == 'ajax_validateSerialno') {
//    debug($_GET);
    $number = $_GET['number'];
    $stockid = $_GET['stockid'];
//    debug($product);
    $obj->status = 'success';
    $obj->message = 'validated';
    try {

        $stock = $Stocks->get($stockid);
        if (empty($stock)) throw new Exception('Stock not found');
        $product = $Products->get($stock['productid']);
        $serial = $SerialNos->find(['number' => $number,]);
        if (!empty($serial)) {
            if (!empty($serial[0]['sdi']) || !empty($serial[0]['smdi'])) throw new Exception('Serial number already been used!');
            if ($serial[0]['current_stock_id'] != $stockid) {
                $serialstock = Stocks::$stockClass->get($serial[0]['current_stock_id']);
                $seriallocation = Locations::$locationClass->locationList($serialstock['locid'])[0];
                throw new Exception("Serial number found in ({$seriallocation['name']} - {$seriallocation['branchname']}), different location from your transaction source");
            }
        }
        if ($product['validate_serialno'] && empty($serial)) throw new Exception('Serial number not found in stock');

    } catch (Exception $e) {
        $obj->status = 'error';
        $obj->message = $e->getMessage();
    }

    $data['content'] = $obj;
}

if ($action == 'ajax_validateSerialnoBundle') { //for upload
    $stockid = $_POST['stockid'];
    $serialnos = $_POST['serialnos'];
//    debug($product);
    $result['status'] = 'success';
    try {
        if (!$stockid) throw new Exception('Invalid stockid');
        if (!$serialnos) throw new Exception('Invalid serial numbers');

        $stock = $Stocks->get($stockid);
        if (!$stock) throw new Exception('Stock not found');
        $product = $Products->get($stock['productid']);
        if (!$product) throw new Exception('Product not found');
        if (!$product['trackserialno']) throw new Exception('Product does not track serial number');

        $serialno_result = [];
        foreach ($serialnos as $no) {
            $validate['number'] = $no;
            $validate['status'] = 1;
            if ($SerialNos->find(['number' => $no])) $validate['status'] = 0;
            $serialno_result[] = $validate;
        }

        $result['data'] = $serialno_result;
    } catch (Exception $e) {
        $result['status'] = 'error';
        $result['msg'] = $e->getMessage();
    }
    $data['content'] = $result;
}

if ($action == 'ajax_validateSerialnoBundleForTransfer') {
    $stockid = $_POST['stockid'];
    $serialnos = $_POST['serialnos'];
//    debug($product);
    $result['status'] = 'success';
    try {
        if (!$stockid) throw new Exception('Invalid stockid');
        if (!$serialnos) throw new Exception('Invalid serial numbers');

        $stock = $Stocks->get($stockid);
        if (!$stock) throw new Exception('Stock not found');
        $product = $Products->get($stock['productid']);
        if (!$product) throw new Exception('Product not found');
        if (!$product['trackserialno']) throw new Exception('Product does not track serial number');

        $serialno_result = [];
        foreach ($serialnos as $no) {
            $validate['number'] = $no;
            $validate['status'] = 1;

            $serial = $SerialNos->find(['number' => $no,]);
            try {
                if (!empty($serial)) {
                    if (!empty($serial[0]['sdi']) || !empty($serial[0]['smdi'])) throw new Exception('Serial number already been used!');
                    if ($serial[0]['current_stock_id'] != $stockid) {
                        $serialstock = Stocks::$stockClass->get($serial[0]['current_stock_id']);
                        $seriallocation = Locations::$locationClass->locationList($serialstock['locid'])[0];
                        throw new Exception("Serial number found in ({$seriallocation['name']} - {$seriallocation['branchname']}), different location from your transaction source");
                    }
                }
                if ($product['validate_serialno'] && empty($serial)) throw new Exception('Serial number not found in stock');
            } catch (Exception $e) {
                $validate['status'] = 0;
                $validate['msg'] = $e->getMessage();
            }
            $serialno_result[] = $validate;
        }

//debug($serialno_result);
        $result['data'] = $serialno_result;
    } catch (Exception $e) {
        $result['status'] = 'error';
        $result['msg'] = $e->getMessage();
    }
    $data['content'] = $result;
}

if ($action == 'ajax_getSerialnoHistory') {
    $snoid = $_GET['snoid'];
    $obj->status = 'success';

    if ($sno = $SerialNos->get($snoid)) {
        $history = $SerialNos->history($snoid);
        foreach ($history as $index => $h) {
            $history[$index]['issue_date'] = fDate($h['doc'], 'd M Y H:i');
            switch ($h['voucher_type']) {
                case 'grn':
                    $history[$index]['url'] = url('grns', 'view_grn', ['grn' => $h['voucher_no']]);
                    break;
                case 'GRN Return':
                    $history[$index]['url'] = url('grns', 'grn_return_print', ['returnid' => $h['voucher_no']]);
                    break;
                case 'transfer':
                    $history[$index]['url'] = url('stocks', 'transfer_view', ['transferno' => $h['voucher_no']]);
                    break;
                case 'Sales Return':
                    $history[$index]['url'] = url('sales_returns', 'view', ['returnno' => $h['voucher_no']]);
                    $history[$index]['voucher_no'] = getCreditNoteNo($h['voucher_no']);
                    break;
                case 'sales':
                    $history[$index]['url'] = url('sales', 'view_invoice', ['salesid' => $h['voucher_no']]);
                    $history[$index]['voucher_no'] = $Sales->get($h['voucher_no'])['receipt_no'];
                    break;
                case 'manufacture':
                    $history[$index]['url'] = url('stocks', 'view_manufacture', ['manufactureno' => $h['voucher_no']]);
                    break;
            }
        }
//        debug($history);
        $obj->data = $history;
    } else {
        $obj->status = 'error';
        $obj->msg = 'Serialno not found!';
    }
    $data['content'] = $obj;
}