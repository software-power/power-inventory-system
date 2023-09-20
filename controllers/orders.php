<?

if ($action == 'order_list') {
//    debug($_GET);
    Users::isAllowed();
    $user = $_SESSION['member'];
    $branchid = $_GET['branchid'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $issuedby = $_GET['issuedby'];
    $clientid = $_GET['clientid'];
    $status = $_GET['order_status'];
    $order_type = $_GET['order_type'];
    $ordernumber = removeSpecialCharacters($_GET['ordernumber']);


    if (Users::can(OtherRights::approve_other_credit_invoice)) {
        $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    } elseif (Users::can(OtherRights::sale_other_order)) { //user branch
        $branchid = $_SESSION['member']['branchid'];
        $tData['branches'] = $Branches->find(['id' => $_SESSION['member']['branchid']]);
    } else {
        $issuedby = $_SESSION['member']['id'];
        $branchid = $_SESSION['member']['branchid'];
    }

    if ($ordernumber) $fromdate = '';

    $title = [];
    if ($ordernumber) $title[] = "Order no: " . $ordernumber;
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($issuedby) $title[] = "Issued by: " . $Users->get($issuedby)['name'];
    if ($fromdate) $title[] = "From " . fDate($fromdate);
    if ($todate) $title[] = "To " . fDate($todate);
    if ($status) $title[] = "Order status: " . $status;
    if ($order_type) $title[] = "Order type: " . ($order_type == Orders::TYPE_NORMAL ? 'Normal Order' : 'Quick Order');
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);


    $tData['orderList'] = $Orders->getAllOrders($ordernumber, $issuedby, $status, $clientid, $fromdate, $todate, $order_type, '', $branchid);
//debug($tData['orderList']);
    $tData['orderid'] = $_GET['orderid'];
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('order_list.tpl.php', $tData);
}

if ($action == 'add_order') {
    Users::isAllowed();
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $_SESSION['delay'] = 5000;
    $orderid = $_GET['orderid'];
    $olcid = $_GET['olcid']; //if location changed in order maintain product
    $proformaid = $_GET['proforma_no'];
    if ($proformaid) {
        if (!$proforma = $Proformas->withDetails($proformaid)[0]) {
            $_SESSION['error'] = "Proforma not found";
            redirectBack();
        }
        if ($proforma['sales_status'] == Orders::STATUS_CLOSED && !isset($_GET['reuse'])) {
            $_SESSION['error'] = "Order is closed cant be edited";
            redirectBack();
        }

        define('EXCEPT_PROFORMA', $proformaid);
        $tData['defaultLocation'] = $Locations->get($proforma['locid']);
        $branchid = $Locations->getBranch($proforma['locid'])['id'];
        $hierarchic = $Hierarchics->get($_SESSION['member']['hierachicid']);
        $exchange_rate = $CurrenciesRates->find(['currencyid' => $proforma['currencyid']])[0]['rate_amount'];  //current exchange rate
        foreach ($proforma['details'] as $index => $detail) {
            $prices = $Products->getPrices($branchid, $hierarchic['level'], $detail['productid']);
            $prices['minimum'] = IS_ADMIN || $detail['non_stock'] ? 0 : $prices['minimum'];
            $proforma['details'][$index]['base_price'] = round($detail['price'] * $exchange_rate, 2);
            $proforma['details'][$index]['min_base_price'] = $prices['minimum'];
            $proforma['details'][$index]['min_price'] = round($prices['minimum'] / $exchange_rate, 2);
            $proforma['details'][$index]['min_incprice'] = round($prices['minimum'] * (1 + $detail['vat_rate'] / 100) / $exchange_rate, 2);

            $stockid = $Stocks->find(['productid' => $detail['productid'], 'locid' => $proforma['locid']])[0]['id'];
            $stockBatches = [];
            if ($stockid) {
                $stockBatches = $Stocks->calcStock(
                    $proforma['locid'], $stockid, "",
                    "", "", "", "",
                    "", "", "", "", "", "",
                    "", "", "", "", false, true,
                    '', '', true, true
                );
                $stockBatches = array_values($stockBatches)[0];
            }
            $proforma['details'][$index]['stockqty'] = $stockBatches['total'] ?: 0;
        }
        $tData['proforma'] = $proforma;
//        debug($proforma);
    }

    if ($orderid) {
        if (!$order = $Orders->withDetails($orderid)[0]) {
            $_SESSION['error'] = "Order not found";
            redirectBack();
        }

        if ($order['sales_status'] == Orders::STATUS_CLOSED) {
            $_SESSION['error'] = "Order is closed cant be edited";
            redirectBack();
        }

        if (!$order['op_reuse']) define('EXCEPT_PROFORMA', $order['proformaid']);  //release stock held by this proforma
        $orderlocationid = $olcid ?: $order['locid'];
        $tData['defaultLocation'] = $Locations->get($orderlocationid);
        $branchid = $Locations->getBranch($orderlocationid)['id'];
        $hierarchic = $Hierarchics->get($_SESSION['member']['hierachicid']);
        $exchange_rate = $CurrenciesRates->find(['currencyid' => $order['currencyid']])[0]['rate_amount'];  //current exchange rate
        foreach ($order['details'] as $index => $detail) {
            $prices = $Products->getPrices($branchid, $hierarchic['level'], $detail['productid']);
            $prices['minimum'] = IS_ADMIN || $detail['non_stock'] ? 0 : $prices['minimum'];
            $order['details'][$index]['base_price'] = round($detail['price'] * $exchange_rate, 2);
            $order['details'][$index]['min_base_price'] = $prices['minimum'];
            $order['details'][$index]['min_price'] = round($prices['minimum'] / $exchange_rate, 2);
            $order['details'][$index]['min_incprice'] = round($prices['minimum'] * (1 + $detail['vat_rate'] / 100) / $exchange_rate, 2);

            $stockid = $Stocks->find(['productid' => $detail['productid'], 'locid' => $orderlocationid])[0]['id'];
            $stockBatches = [];
            if ($stockid) {
                $stockBatches = $Stocks->calcStock(
                    $orderlocationid, $stockid, "",
                    "", "", "", "",
                    "", "", "", "", "", "",
                    "", "", "", "", false, true,
                    '', '', true, true
                );
                $stockBatches = array_values($stockBatches)[0];
            }
            $order['details'][$index]['stockqty'] = $stockBatches['total'] ?: 0;

        }
        $tData['order'] = $order;
//        debug($order);
    }

    $data['content'] = loadTemplate('order_edit_new.tpl.php', $tData);
}

if ($action == 'order_save') {
    $order = $_POST['order'];

    //validate
//    debug($_POST);
    $productIds = $_POST['productid'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $incprice = $_POST['incprice'];
    $sinc = $_POST['sinc'];
    $vat_rate = $_POST['vat_rate'];
    $print_extra = $_POST['print_extra'];
    $product_description = $_POST['product_description'];
//        debug($incprice);

    validate($order);
    validate($productIds);

    if (empty($order['validity_days'])) $order['validity_days'] = CS_ORDER_VALID_DAYS;

    if (!$order['id']) {//new
        $order['deptid'] = $_SESSION['member']['deptid'];
        $order['createdby'] = $_SESSION['member']['id'];

        //New
        $Orders->insert($order);
        $orderid = $Orders->lastId();
        if ($order['proformaid']) {
            $Proformas->update($order['proformaid'], [
                'sales_status' => Orders::STATUS_CLOSED,
                'sales_closedby' => $_SESSION['member']['id']
            ]);
        }
    } else {//updating
        $orderid = $order['id'];
        $order['updated_at'] = TIMESTAMP;
        $order['updated_by'] = $_SESSION['member']['id'];
        $Orders->update($orderid, $order);

        //clear previous details
        $Salesdescriptions->deleteWhereMany(['odi' => array_column($Orderdetails->find(['orderid' => $orderid]), 'id')]);
        $Orderdetails->deleteWhere(['orderid' => $orderid]);
    }

    foreach ($productIds as $index => $productid) {
        $Orderdetails->insert([
            'orderid' => $orderid,
            'productid' => $productid,
            'qty' => $qty[$index],
            'price' => removeComma($price[$index]),
            'incprice' => removeComma($incprice[$index]),
            'sinc' => $sinc[$index],
            'vat_rate' => $vat_rate[$index],
            'print_extra' => in_array($productid, $print_extra),
            'createdby' => $_SESSION['member']['id'],
        ]);
        $odi = $Orderdetails->lastId();

        if (in_array($productid, $print_extra)) {
            $Salesdescriptions->insert([
                'odi' => $odi,
                'description' => $product_description[$index],
            ]);
        }
    }

    $_SESSION['message'] = "Order " . ($order['id'] ? 'updated' : 'created') . " successfully";

    redirect('orders', 'order_list&ordernumber=' . $orderid);
}

if ($action == 'extend_days') {
    $orderid = $_POST['orderid'];
    $extend_days = $_POST['extend_days'];
    validate($orderid);
    validate($extend_days);
    $order = $Orders->getAllOrders($orderid)[0];
    if (empty($order)) {
        $_SESSION['error'] = "Order not found!";
        redirectBack();
    }
    if ($order['order_status'] == Orders::STATUS_INVALID) {
        $until = new DateTime("now +$extend_days day");
    } else {
        $until = new DateTime("{$order['valid_until']} +$extend_days day");
    }
    $newDays = $until->diff(new DateTime("{$order['issueddate']}"))->days;
    $Orders->update($orderid, ['validity_days' => $newDays]);

    $_SESSION['message'] = "Order updated";
    redirect('orders', 'order_list');
}

if ($action == 'cancel_order') {
//    debug($_POST);
    $orderid = $_POST['orderid'];
    $revive = $_POST['revive'] == 1;
    validate($orderid);
    $order = $Orders->getAllOrders($orderid)[0];
    if (empty($order)) {
        $_SESSION['error'] = "Order not found!";
        redirectBack();
    }
    if ($order['order_status'] == Orders::STATUS_CLOSED) {
        $_SESSION['error'] = "Order already closed cant be canceled!";
        redirectBack();
    }
    if ($revive) {
        if ($order['foreign_orderid']) {
            $_SESSION['error'] = "External orders cant be continued after canceled!";
            redirectBack();
        }
        $Orders->update($orderid, ['status' => 'active']);
    } else {
        $Orders->update($orderid, ['status' => 'inactive']);
    }
    $_SESSION['message'] = "Order updated";

    //update support order
    if ($order['foreign_orderid'] && $order['order_source'] == 'support') {
        $support_response = Orders::postToSupport($orderid);
        if ($support_response['status'] == 'success') {
            $_SESSION['message'] .= "\nSupport: " . $support_response['msg'];
        } else {
            $_SESSION['error'] .= "\nSupport: " . $support_response['msg'];
        }
    }

    redirect('orders', 'order_list');
}

if ($action == 'checklist') {
    $orderid = $tData['orderno'] = removeSpecialCharacters($_GET['orderno']);
    $tData['checklists'] = $OrderChecklists->getList($orderid);
    $data['content'] = loadTemplate('order_checklist_form.tpl.php', $tData);
}

if ($action == "save_orderchecklist") {
    $orderid = removeSpecialCharacters($_POST['orderid']);
    $list = $_POST['list'];
//    debug($_POST);
    validate($orderid);
    validate($list);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        foreach ($list as $cid => $item) {
            $oc = $OrderChecklists->find(['cid' => $cid, 'orderid' => $orderid])[0];
            if ($oc) {
                $ocid = $oc['id'];
                $OrderChecklists->update($ocid, [
                    'remark' => $item['remark'],
                    'createdby' => $_SESSION['member']['id'],
                    'doc' => TIMESTAMP
                ]);
            } else {
                $OrderChecklists->insert([
                    'orderid' => $orderid,
                    'cid' => $cid,
                    'remark' => $item['remark'],
                    'createdby' => $_SESSION['member']['id'],
                    'doc' => TIMESTAMP
                ]);
                $ocid = $OrderChecklists->lastId();
            }

            $file = $_FILES["list$cid"];
            $allowed_files = ['jpg', 'jpeg', 'png', 'pdf'];
            if ($file['error']) continue;
            $target_dir = "documents/order_checklists/$orderid/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            $target_file = $target_dir . $cid . "_" . str_replace([' ', '-'], '_', basename($file["name"]));
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (!in_array($file_type, $allowed_files)) throw new Exception("Allowed files extensions are " . implode(',', $allowed_files) . " only");
            if ($oc['file_path']) unlink($oc['file_path']);
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $OrderChecklists->update($ocid, ['file_path' => $target_file,]);
            } else {
                throw new Exception("Failed to upload the file");
            }


        }
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Check list saved";
        redirect('orders', 'order_list', ['ordernumber' => $orderid]);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'closing_order') {
    $orderId = $_GET['orderid'];
    $userid = $_SESSION['member']['id'];
    $data = array('sales_status' => 'closed', 'closedby' => $userid, 'closedate' => TIMESTAMP);
    $Orders->update($orderId, $data);
    redirect('orders', 'order_list');
}

if ($action == 'print_checklist') {

    $data['layout'] = 'order_checklist_print.tpl.php';
}

if ($action == 'change_order_person') {
    Users::can(OtherRights::approve_other_credit_invoice, true);
    $orderid = removeSpecialCharacters($_POST['orderid']);
    $userid = removeSpecialCharacters($_POST['personid']);

    $order = Orders::$staticClass->get($orderid);
    if (!$order) {
        $_SESSION['error'] = "Order not found";
        redirectBack();
    }

    if (Users::$userClass->countWhere(['id' => $userid]) == 0) {
        $_SESSION['error'] = "Sales person not found";
        redirectBack();
    }

    if ($order['createdby'] == $userid) {
        $_SESSION['error'] = "Nothing to change";
        redirectBack();
    }

    Orders::$staticClass->update($orderid, [
        'createdby' => $userid,
        'person_changedby' => $_SESSION['member']['id'],
        'person_changedat' => TIMESTAMP,
    ]);

    $_SESSION['message'] = "Order person changed successfully";
    redirectBack();
}

if ($action == 'post_support') {
    $orderid = removeSpecialCharacters($_GET['orderid']);
    $result = Orders::postToSupport($orderid);
    if ($result['status'] == 'success') {
        $_SESSION['message'] = $result['msg'];
    } else {
        $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}

if ($action == 'ajax_getOrderDetails') {
    $orderId = $_GET['orderId'];

    $obj->status = 'success';
    if ($order = $Orders->withDetails($orderId)[0]) {
        //arrange details
//        if (!$order['op_reuse']) define('EXCEPT_PROFORMA', $order['proformaid']);  //release stock held by this proforma

        foreach ($order['details'] as $index => $item) {
            $order['details'][$index]['total'] = $item['incamount'];

            //find location stock
            $stock = $Stocks->find(array('productid' => $item['productid'], 'locid' => $order['locid']))[0];
            if ($item['non_stock']) {
                $order['details'][$index]['stock_state'] = 'exists';
            } else if (empty($stock)) {
                $order['details'][$index]['stock_state'] = 'no-stock';
            } else {
                //checking stock qty
                $currentStock = $Stocks->calcStock(
                    $order['locid'], $stock['id'], "", "", "",
                    "", "", "", "", "",
                    "", "", "", "", "", "",
                    "", false, true, '',
                    '', true, true
                );
                $currentStock = array_values($currentStock)[0];
//                debug($currentStock);
                $stock_qty = $currentStock['total'] ?? 0;
                $held_qty = $currentStock['held_stock'] ?? 0;
                $order['details'][$index]['stock_qty'] = $stock_qty;
                $order['details'][$index]['held_qty'] = $held_qty;
                if ($stock_qty >= $item['qty']) {
                    $order['details'][$index]['stock_state'] = 'exists';
                } elseif ($stock_qty < $item['qty'] && ($stock_qty + $held_qty) >= $item['qty']) {
                    $order['details'][$index]['stock_state'] = 'held';
                } elseif ($stock_qty < $item['qty'] && $stock_qty > 0) {
                    $order['details'][$index]['stock_state'] = 'not-enough';
                } else {
                    $order['details'][$index]['stock_state'] = 'no-stock';
                }
            }
        }
//        debug($order);
        $obj->data = $order;
    } else {
        $obj->status = 'error';
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_orderFirstContact') {
    $orderno = $_GET['orderno'];
    $order = $Orders->get($orderno);
    $result['status'] = 'success';
    if ($order) {
        $first_contact = $Contacts->find(['clientid' => $order['clientid']])[0];
        $result['data'] = [
            'name' => $first_contact['name'] ?: '',
            'mobile' => $first_contact['mobile'] ?: '',
        ];
    } else {
        $result['status'] = 'error';
        $result['msg'] = 'Order not found';
    }

    $data['content'] = $result;
}
