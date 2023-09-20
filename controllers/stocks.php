<?
if ($action == "transfer_list") {
    Users::isAllowed();
    $fromdate = $_GET['search']['fromdate'] ?: TODAY;
    $todate = $_GET['search']['todate'];
    $createdby = $_GET['search']['createdby'];
    $transferno = $_GET['search']['transferno'];
    $fromlocation = $_GET['search']['fromlocation'];
    $tolocation = $_GET['search']['tolocation'];
    $status = $_GET['search']['status'];

    if (Users::cannot(OtherRights::approve_other_transfer) && Users::cannot(OtherRights::view_all_transfer)) $createdby = $_SESSION['member']['id'];

    $title = [];
    if ($transferno) $title[] = "Transfer No: " . $transferno;
    if ($createdby) {
        $tData['creator'] = $creator = $Users->get($createdby);
        $title[] = "Issued by: " . $creator['name'];
    }
    if ($fromlocation) $title[] = "From " . $Locations->get($fromlocation)['name'];
    if ($tolocation) $title[] = "To " . $Locations->get($tolocation)['name'];

    if ($transferno) $fromdate = $todate = '';

    if ($fromdate) $title[] = "From date: " . fDate($fromdate);
    if ($todate) $title[] = "To date: " . fDate($todate);

    if ($status == 'approved') {
        $title[] = "Status: Approved";
    } elseif ($status == 'not-approved') {
        $title[] = "Status: Not approved";
    } elseif ($status == 'canceled') {
        $title[] = "Status: Canceled";
    }

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $tData['transfers'] = $StockTransfers->getList($transferno, $createdby, $fromlocation, $tolocation, $fromdate, $todate, $status);
    $tData['basecurrency'] = $Currencies->find(['base' => 'yes'])[0];
    $data['content'] = loadTemplate('transfer_list.tpl.php', $tData);
}

if ($action == 'issue_transfer') {
    $transferno = $_GET['transferno'];
    $reqno = $_GET['reqno'];
    if (!$transferno && !$reqno && CS_TRANSFER_REQUIRE_REQUISITION) {
        $_SESSION['error'] = "All transfer require Requisition first!";
        $_SESSION['delay'] = 5000;
        redirect('stocks', 'transfer_requisition_list');
    }
    if ($transferno) {
        debug('Transfer Editing is under development');
        Users::can(OtherRights::edit_transfer, true);
    } else {
        Users::can(OtherRights::transfer_stock, true);
    }
    if ($reqno) {
        $requisition = $TransferRequisitions->get($reqno);
        if (!$requisition) {
            $_SESSION['error'] = "Requisition not found!";
            redirectBack();
        }
        if (!$requisition['approvedby']) {
            $_SESSION['error'] = "Requisition has not been approved!";
            redirectBack();
        }

        if ($StockTransfers->find(['reqid' => $reqno])) {
            $_SESSION['error'] = "Requisition has already been processed to transfer!";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }
        $requisition['fromlocation'] = $Locations->get($requisition['location_from'])['name'];
        $requisition['tolocation'] = $Locations->get($requisition['location_to'])['name'];
        $requisition['details'] = $TransferRequisitionDetails->find(['reqid' => $reqno]);
        foreach ($requisition['details'] as $index => $detail) {
            //product
            $product = $Products->get($detail['productid']);
            $requisition['details'][$index]['productname'] = $product['name'];
            $requisition['details'][$index]['product_description'] = $product['description'];
            $requisition['details'][$index]['track_expire_date'] = $product['track_expire_date'];
            $requisition['details'][$index]['trackserialno'] = $product['trackserialno'];
            //check stock id
            $stockid = $Stocks->find(['productid' => $detail['productid'], 'locid' => $requisition['location_to']])[0]['id'];
            if (!$stockid) {
                $Stocks->insert([
                    'productid' => $detail['productid'],
                    'locid' => $requisition['location_to'],
                    'createdby' => $_SESSION['member']['id']
                ]);
                $stockid = $Stocks->lastId();
            }
            $requisition['details'][$index]['stockid'] = $stockid;

            $currentStock = $Stocks->calcStock(
                $requisition['location_to'], $stockid, "", "", "", "",
                "", "", "", "", "", "", "",
                "", "", "", "", false, true,
                "", "", true, true
            );
            $currentStock = array_values($currentStock)[0];
            $requisition['details'][$index]['current_stock'] = $currentStock['total'] ?? 0;
            $requisition['details'][$index]['batches'] = $currentStock['batches'];
        }
//        debug($requisition);
        $tData['requisition'] = $requisition;
    }
    $data['content'] = loadTemplate('stock_transfer_edit.tpl.php', $tData);
}

if ($action == 'save_transfer') {
//    debug($_POST);
    $transfer = $_POST['transfer'];
    $transfer['createdby'] = $_SESSION['member']['id'];

    $stockIds = $_POST['stockid'];
    $quantities = $_POST['quantity'];
    $serialnos = $_POST['serialno'];
    $batches = $_POST['batch'];

    validate($transfer);
    validate($stockIds);
    if (!CS_TRANSFER_APPROVAL) { //if approval not required
        $transfer['approvedby'] = $_SESSION['member']['id'];
        $transfer['doa'] = TIMESTAMP;
        $transfer['auto_approve'] = 1;
    }
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if ($StockTransfers->find(['token' => $transfer['token']]))
            throw new Exception("System found this form is already submitted, Transfer canceled to avoid duplicate transfers!");
        $StockTransfers->insert($transfer);
        $lastTransferId = $StockTransfers->lastId();
        $tobranchid = $Locations->getBranch($transfer['location_to'])['id'];
        if (!$tobranchid) throw new Exception('Stock to-Branch not found!');
        $frombranchid = $Locations->getBranch($transfer['location_from'])['id'];
        if (!$frombranchid) throw new Exception('Stock from-Branch not found!');
        foreach ($stockIds as $index => $stockid) {

            $current_stock = $Stocks->calcStock(
                $transfer['location_from'], $stockid, "",
                "", "", "", "",
                "", "", "", "", "", "",
                "", "", "", "", false, true,
                '', '', true, true
            );
            $current_stock = array_values($current_stock)[0];
            if ($current_stock['total'] < $quantities[$index]) throw new Exception("Product ({$current_stock['name']}), not enough stock for transfer");

            //check 'stock to' -> if it's available
            $stock_det = $Stocks->get($stockid);
            $checkStock = $Stocks->find([
                'productid' => $stock_det['productid'],
                'locid' => $transfer['location_to']
            ]);
            if ($checkStock) {
                $stockto = $checkStock[0]['id'];
            } else {
                //create new stock
                $Stocks->insert([
                    'locid' => $transfer['location_to'],
                    'productid' => $stock_det['productid'],
                    'createdby' => $_SESSION['member']['id']
                ]);
                $stockto = $Stocks->lastId();
            }

            $StockTransferDetails->insert([
                'transferid' => $lastTransferId,
                'stock_from' => $stockid,
                'stock_to' => $stockto,
                'quantity' => $quantities[$index], //overall qty
                'createdby' => $_SESSION['member']['id'],
            ]);

            $stdi = $StockTransferDetails->lastId(); //stock transfer detail id
            $product = Products::$productClass->get($stock_det['productid']);

            if ($batches[$stockid] && $product['track_expire_date'] == 1) { //if tracking expire date
                foreach ($batches[$stockid]['batchId'] as $bi => $batchId) {
                    $batch_trans_qty = $batches[$stockid]['qty_out'][$bi];
                    $current_batch_stock = array_filter($current_stock['batches'], function ($b) use ($batchId, $batch_trans_qty) {
                        return $b['batchId'] == $batchId && $b['total'] >= $batch_trans_qty;
                    });
                    if (!$current_batch_stock) {
                        $batchno = $Batches->get($batchId)['batch_no'];
                        throw new Exception("System found product {$current_stock['name']}, batch no {$batchno} does not have enough qty for transfer");
                    }

                    $StockTransferBatches->insert([
                        'stdi' => $stdi,
                        'batch_id' => $batchId,
                        'qty' => $batch_trans_qty, //single batch qty
                    ]);
                }
            } else {
                //find batch id from available stocks
                $stockBatches = $current_stock['batches'];
                $remainTransferQty = $quantities[$index]; //total product transfer qty
                //insert stock until transfer qty is 0
                foreach ($stockBatches as $stb => $stockBatch) {
                    if ($stockBatch['total'] >= $remainTransferQty) { //single batch covers whole transfer qty
                        $StockTransferBatches->insert([
                            'stdi' => $stdi,
                            'batch_id' => $stockBatch['batchId'],
                            'qty' => $remainTransferQty, //single batch qty
                        ]);
                        break;
                    } else { //use another batch (qty and id)
                        $StockTransferBatches->insert([
                            'stdi' => $stdi,
                            'batch_id' => $stockBatch['batchId'],
                            'qty' => $stockBatch['total'], //single batch qty
                        ]);
                        $remainTransferQty -= $stockBatch['total']; //reduce remain qty
                    }
                    if ($remainTransferQty == 0) break; //no more transfer qty left
                }
            }

            if ($serialnos[$stockid] && $product['trackserialno'] == 1) { //if tracking serialno
                foreach ($serialnos[$stockid]['serial_number'] as $si => $number) {
                    $sno = $SerialNos->find(['number' => $number]); //todo if more validation is required check here
                    if (empty($sno)) { //create new, it happens if serialnos entered manually
                        if ($product['validate_serialno'] == 1) throw new Exception("Product {$product['name']} validates serial no from stock, serialno $number not found!");
                        $SerialNos->insert([
                            'number' => $number,
                            'initial_stockid' => $stockid,
                            'current_stock_id' => $transfer['approvedby'] ? $stockto : $stockid, //change location if already approved
                            'source' => SerialNos::SOURCE_TRANSFER,
                            'createdby' => $_SESSION['member']['id']
                        ]);
                        $serialno_id = $SerialNos->lastId();
                    } else {
                        $serialno_id = $sno[0]['id'];
                        if ($transfer['approvedby']) $SerialNos->update($serialno_id, ['current_stock_id' => $stockto]); //update current serial stock id
                    }

                    $StockTransferSerials->insert([
                        'stdi' => $stdi,
                        'serialno_id' => $serialno_id,
                    ]);

                }
            }

            //price updating
            if ($transfer['approvedby']) {
//            if ($frombranchid != $tobranchid) {
                $currentBranchPrice = $CurrentPrices->find(['branchid' => $frombranchid, 'productid' => $stock_det['productid']])[0];
                $destinationBranchPrice = $CurrentPrices->find(['branchid' => $tobranchid, 'productid' => $stock_det['productid']])[0];
                if (!$destinationBranchPrice || $currentBranchPrice['costprice'] > $destinationBranchPrice['costprice']
                    || $currentBranchPrice['quicksale_price'] > $destinationBranchPrice['quicksale_price']) {
                    CurrentPrices::updatePrice($tobranchid, $stock_det['productid'], $currentBranchPrice['costprice'],
                        $currentBranchPrice['quicksale_price'], 0, "Transfer No $lastTransferId", true);
                }
//            }
            }
        }
        $_SESSION['message'] = "Goods transferred successfully";

        mysqli_commit($db_connection);
        redirect('stocks', 'transfer_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 10000;
        redirectBack();
    }
}

if ($action == 'transfer_view') {
    $can_view = Users::can(OtherRights::view_all_transfer) || Users::can(OtherRights::approve_other_transfer)
        || Users::can(OtherRights::approve_transfer) || Users::can(OtherRights::transfer_stock)
        || Users::can(OtherRights::edit_transfer);
    if (!$can_view) redirect('authenticate', 'access_page', ['right_action' => base64_encode('view stock transfer')]);

    $transferno = $_GET['transferno'];
    if (!($transfer = $StockTransfers->get($transferno))) debug("Transfer Not Found");
    $tData['transfer'] = StockTransfers::transferInfo($transferno);
    $tData['basecurrency'] = $Currencies->find(['base' => 'yes'])[0];
//    debug($tData['transfer']);
    $data['content'] = loadTemplate('view_transfer.tpl.php', $tData);
}

if ($action == 'approve_transfer') {
    if (Users::cannot(OtherRights::approve_other_transfer) && Users::cannot(OtherRights::approve_transfer))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Stock Transfer')]);

    $transferno = removeSpecialCharacters($_GET['transferno']);
    if (!($transfer = StockTransfers::transferInfo($transferno))) debug("Transfer not found!");

    if (!empty($transfer['approver'])) {
        $_SESSION['error'] = "Transfer already approved";
        redirectBack();
    }
    if ($transfer['status'] != 'active') {
        $_SESSION['error'] = "Transfer already canceled!";
        redirectBack();
    }
    foreach ($transfer['products'] as $index => $detail) {
        $currentBranchPrice = $CurrentPrices->quickPriceList($transfer['frombranchid'], $detail['productid'])[0];
//        debug($currentBranchPrice);
        $transfer['products'][$index]['vat_rate'] = $currentBranchPrice['vat_rate'];
        $transfer['products'][$index]['exc_base'] = $currentBranchPrice['exc_base'];
        $transfer['products'][$index]['inc_base'] = $currentBranchPrice['inc_base'];

        $transfer['products'][$index]['from']['costprice'] = $currentBranchPrice['costprice'];
        $transfer['products'][$index]['from']['inc_quicksale_price'] = $currentBranchPrice['inc_quicksale_price'];

        $destinationBranchPrice = $CurrentPrices->quickPriceList($transfer['tobranchid'], $detail['productid'])[0];
        $transfer['products'][$index]['to']['costprice'] = $destinationBranchPrice['costprice'];
        $transfer['products'][$index]['to']['inc_quicksale_price'] = $destinationBranchPrice['inc_quicksale_price'];
    }
//        debug($transfer);
    $tData['transfer'] = $transfer;
    $tData['basecurrency'] = $Currencies->find(['base' => 'yes'])[0];
    $data['content'] = loadTemplate("approve_transfer.tpl.php", $tData);
}

if ($action == 'confirm_approval') {
    if (Users::cannot(OtherRights::approve_other_transfer) && Users::cannot(OtherRights::approve_transfer))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Stock Transfer')]);
//    debug($_POST);
    $transferno = $_POST['transferno'];
    $transfer_cost = $_POST['transfer_cost'];
    $prices = $_POST['prices'];

    validate($transferno);
//    validate($transfer_cost);
    validate($prices);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (!$transfer = StockTransfers::transferInfo($transferno)) throw new Exception("Transfer not found!");
        foreach ($transfer['products'] as $detail) {
            $current_stock = $Stocks->calcStock(
                $transfer['location_from'], '', "",
                "", $detail['productid'], "", "",
                "", "", "", "", "", "",
                "", "", "", "", false, true,
                '', '', true, true
            );
            $current_stock = array_values($current_stock)[0];
//            debug($current_stock);
            if ($current_stock['total'] < $detail['qty']) throw new Exception("Product ({$current_stock['name']}), not enough stock for transfer");

            foreach ($detail['batches'] as $bi => $batch) {
                $current_batch_stock = array_filter($current_stock['batches'], function ($b) use ($batch) {
                    return $b['batchId'] == $batch['batchId'] && $b['total'] >= $batch['qty'];
                });
                if (!$current_batch_stock) {
                    $batchno = $Batches->get($batch['batchId'])['batch_no'];
                    throw new Exception("System found batch no {$batchno} is out of stock,\nTry editing Transfer");
                }
            }
            if ($detail['trackserialno']) {
                if (count($detail['serialnos']) != $detail['qty']) throw new Exception("Product {$detail['productname']} missing serial no info!");
                foreach ($detail['serialnos'] as $sno) {
                    if ($sno['current_stock_id'] != $detail['fromstockid']) throw new Exception("Serial no {$sno['number']} does not exist in the transfer source!");
                    if ($sno['sdi'] || $sno['smdi']) throw new Exception("Product {$detail['productname']},\nSerial no {$sno['number']} already been used!");

                    SerialNos::$serialNoClass->update($sno['id'], ['current_stock_id' => $detail['tostockid']]); //change serialno location
                }
            }


            //if ($transfer['tobranchid'] != $transfer['frombranchid'])
            //update prices
            CurrentPrices::updatePrice($transfer['tobranchid'],
                $detail['productid'],
                $prices[$detail['detailId']]['costprice'],
                $prices[$detail['detailId']]['quicksale_price'],
                0,
                "Transfer No " . $transfer['transferno'],
                true
            );

        }

        $StockTransfers->update($transferno, [
            'transfer_cost' => $transfer_cost,
            'approvedby' => $_SESSION['member']['id'],
            'doa' => TIMESTAMP
        ]);
//        debug($transfer);
        $_SESSION['message'] = "Transfer Approved";
        mysqli_commit($db_connection);
        redirect('stocks', 'transfer_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 10000;
        redirectBack();
    }
}

if ($action == 'cancel_transfer') {
    if (Users::cannot(OtherRights::approve_other_transfer) && Users::cannot(OtherRights::approve_transfer))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Stock Transfer')]);
    $transferno = $_POST['transferno'];
    $revive = isset($_POST['revive']);

    if (!($transfer = StockTransfers::transferInfo($transferno))) debug("Transfer not found!");

    if (!empty($transfer['approver'])) {
        $_SESSION['error'] = "Transfer already approved";
        redirectBack();
    }
//    debug($transfer);
    $StockTransfers->update($transferno, ['status' => $revive ? 'active' : 'inactive']);
    $_SESSION['message'] = "Transfer no. $transferno " . ($revive ? 'restored' : 'canceled') . " successfully";
    redirect('stocks', 'transfer_list');
}

if ($action == 'transfer_print') {
    $can_view = Users::can(OtherRights::view_all_transfer) || Users::can(OtherRights::approve_other_transfer)
        || Users::can(OtherRights::approve_transfer) || Users::can(OtherRights::transfer_stock)
        || Users::can(OtherRights::edit_transfer);
    if (!$can_view) redirect('authenticate', 'access_page', ['right_action' => base64_encode('view print stock transfer')]);

    $transferno = $_GET['transferno'];
    if ($transfer = $StockTransfers->get($transferno)) {
        $data['transfer'] = StockTransfers::transferInfo($transferno);
        $data['layout'] = 'stock_transfer_print.tpl.php';
    } else {
        debug("<h1>Transfer Not Found</h1>" . PHP_EOL .
            "<a href='?'>Home</a>");
    }
}


if ($action == 'transfer_requisition_list') {
    Users::isAllowed();
    $fromdate = $_GET['search']['fromdate'] ?: TODAY;
    $todate = $_GET['search']['todate'];
    $transferno = $_GET['search']['reqno'];
    $createdby = $_GET['search']['createdby'];
    $fromlocation = $_GET['search']['fromlocation'];
    $tolocation = $_GET['search']['tolocation'];

    if (Users::cannot(OtherRights::approve_other_requisition)) $createdby = $_SESSION['member']['id'];

    $title = [];
    if ($reqno) $title[] = "Requisition No: " . $reqno;
    if ($createdby) {
        $tData['creator'] = $creator = $Users->get($createdby);
        $title[] = "Issued by: " . $creator['name'];
    }
    if ($fromlocation) $title[] = "From " . $Locations->get($fromlocation)['name'];
    if ($tolocation) $title[] = "To " . $Locations->get($tolocation)['name'];
    if ($fromdate) $title[] = "From date: " . fDate($fromdate);
    if ($todate) $title[] = "To date: " . fDate($todate);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);
    $requisitionList = $TransferRequisitions->getList($reqno, $createdby, $fromlocation, $tolocation, $fromdate, $todate);
    $tData['requisitions'] = $requisitionList;
    $data['content'] = loadTemplate('transfer_requisition_list.tpl.php', $tData);
}

if ($action == 'issue_transfer_requisition') {
    Users::can(OtherRights::add_requisition, true);
//    debug($_POST);
    $reqno = $_POST['reqno'];
    if ($reqno) {
        $requisition = $TransferRequisitions->get($reqno);
        if (!$requisition) {
            $_SESSION['error'] = "Requisition not found!";
            redirectBack();
        }
        if ($requisition['approvedby']) {
            $_SESSION['error'] = "Requisition has already been approved!";
            redirectBack();
        }
        $requisition = $TransferRequisitions->withDetails($reqno)[0];
//        debug($requisition);
        $tData['requisition'] = $requisition;
    }
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $data['content'] = loadTemplate('transfer_requisition_edit.tpl.php', $tData);
}

if ($action == 'save_requisition') {
//    debug($_POST);
    $requisition = $_POST['requisition'];
    $productids = $_POST['productid'];
    $qty = $_POST['qty'];

    validate($requisition);
    validate($productids);

    if (!$requisition['id']) {
        $requisition['createdby'] = $_SESSION['member']['id'];
        if (!CS_REQUISITION_APPROVAL) {
            $requisition['approvedby'] = $_SESSION['member']['id'];
            $requisition['approve_date'] = TIMESTAMP;
            $requisition['auto_approve'] = 1;
        }

        $TransferRequisitions->insert($requisition);
        $reqid = $TransferRequisitions->lastId();
    } else {
        $reqid = $requisition['id'];
        $currentRequisition = $TransferRequisitions->get($reqid);
        if ($currentRequisition['approvedby']) {
            $_SESSION['error'] = "Requisition has already been approved!";
            redirect('stocks', 'transfer_requisition_list');
        }

        if ($StockTransfers->find(['reqid' => $reqid])) {
            $_SESSION['error'] = "Transfer have already been made!";
            redirect('stocks', 'transfer_requisition_list');
        }

        $requisition['modifiedby'] = $_SESSION['member']['id'];
        $TransferRequisitions->update($reqid, $requisition);
        //clear previous details
        $TransferRequisitionDetails->deleteWhere(['reqid' => $reqid]);
//        debug('Editing under development');
    }

    foreach ($productids as $index => $productid) {
        $TransferRequisitionDetails->insert([
            'reqid' => $reqid,
            'productid' => $productid,
            'qty' => $qty[$index],
            'createdby' => $_SESSION['member']['id'],
        ]);
    }

    $_SESSION['message'] = "Transfer requisition " . ($reqid ? 'updated' : 'created') . " successfully";
    redirect('stocks', 'transfer_requisition_list');
}

if ($action == 'approve_requisition') {
    if (Users::cannot(OtherRights::approve_other_requisition) && Users::cannot(OtherRights::approve_requisition))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Transfer Requisition')]);


//    debug($_POST);
    $reqno = $_POST['reqno'];
    if ($requisition = $TransferRequisitions->get($reqno)) {
        if ($requisition['approvedby']) {
            $_SESSION['error'] = 'Requisition has already been approved!';
        }

        $TransferRequisitions->update($reqno, [
            'approvedby' => $_SESSION['member']['id'],
            'approve_date' => TIMESTAMP,
        ]);
        $_SESSION['message'] = 'Requisition approved successfully';
    } else {
        $_SESSION['error'] = 'Requisition not found!';
    }
    $_SESSION['delay'] = 5000;
    redirect('stocks', 'transfer_requisition_list');
}

if ($action == 'requisition_print') {
    $reqno = $_GET['reqno'];
    if ($requisition = $TransferRequisitions->withDetails($reqno)[0]) {
        //fetch products
//        debug($requisition);
        $data['requisition'] = $requisition;
        $data['layout'] = 'requisition_print.tpl.php';
    } else {
        debug("<h1>Transfer Requisition Not Found</h1>" . PHP_EOL .
            "<a href='?'>Home</a>");
    }
}


if ($action == 'stock_adjustment_list') {
    Users::isAllowed();
    $adjustmentno = $_POST['search']['adjustmentno'];
    $branchid = $_POST['search']['branchid'];
    $locationid = $_POST['search']['locationid'];
    $fromdate = $_POST['search']['fromdate'];
    $todate = $_POST['search']['todate'];

    $title = [];
    if ($adjustmentno) $title[] = "Adjustment No: " . $adjustmentno;
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);


    $adjustments = $StockAdjustments->getList($adjustmentno, $locationid, $branchid, $fromdate, $todate);
//    debug($adjustments);
    $tData['adjustments'] = $adjustments;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $data['content'] = loadTemplate('stock_adjustment_list.tpl.php', $tData);
}

if ($action == 'make_adjustment') {
    Users::can(OtherRights::adjust_stock, true);
//    debug('list');
    $data['content'] = loadTemplate('make_adjustment.tpl.php', $tData);
}

if ($action == 'save_adjustment') {
    Users::can(OtherRights::adjust_stock, true);
//    debug($_POST);
    $adjustment = $_POST['adj'];
    $stockIds = $_POST['stockid'];
    $current_stock = $_POST['current_stock'];
    $qty = $_POST['qty'];
    $product_actions = $_POST['product_action'];
    $batch_actions = $_POST['batch_actions'];
    $remarks = $_POST['remarks'];
    $batches = $_POST['batches'];

    validate($adjustment);
    validate($stockIds);

    $adjustment['createdby'] = $_SESSION['member']['id'];

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if ($StockAdjustments->find(['token' => $adjustment['token']])) throw new Exception("System found this form is already submitted, Adjustment canceled to avoid duplicate adjustments!");
        $StockAdjustments->insert($adjustment);
        $adjustmentId = $StockAdjustments->lastId();

        foreach ($stockIds as $index => $stockid) {
            $StockAdjustmentDetails->insert([
                'adjustment_id' => $adjustmentId,
                'stockid' => $stockid,
                'current_stock' => $current_stock[$index],
                'remarks' => $remarks[$index],
                'createdby' => $_SESSION['member']['id']
            ]);
            $adid = $StockAdjustmentDetails->lastId();

            if ($batches[$stockid]) {
                foreach ($batches[$stockid]['batchId'] as $bi => $batchId) {
                    $StockAdjustmentBatches->insert([
                        'adid' => $adid,
                        'batch_id' => $batchId,
                        'qty' => $batches[$stockid]['qty'][$bi],
                        'action' => in_array($batchId, $batch_actions)
                            ? StockAdjustmentBatches::ACTION_ADD
                            : StockAdjustmentBatches::ACTION_REDUCE,
                        'before_qty' => $batches[$stockid]['current_stock'][$bi],
                    ]);
                }
            } else {
                $stock = $Stocks->calcStock(
                    $adjustment['locationid'], $stockid,
                    "", "", "", "", "",
                    "", "", "", "", "", "", "",
                    "", "", "", $with_expired = true, $group_batch = true,
                    "", "", $with_stock = false);
                $stock = array_values($stock)[0];

                //take latest batch
                $stockBatches = array_values($stock['batches']);
                if (empty($stockBatches)) throw new Exception("No Stock Found for Adjustment, ERROR S001");
                //sort desc depending on total
                usort($stockBatches, function ($a, $b) {
                    if ($a['total'] == $b['total']) {
                        return 0;
                    }
                    return ($a['total'] < $b['total']) ? -1 : 1;
                });
                $stockBatches = array_reverse($stockBatches);
                if (in_array($stockid, $product_actions)) {//if adding
                    $latestBatch = $stockBatches[0];
                    $StockAdjustmentBatches->insert([
                        'adid' => $adid,
                        'batch_id' => $latestBatch['batchId'],
                        'qty' => $qty[$index],  //product qty
                        'action' => StockAdjustmentBatches::ACTION_ADD,
                        'before_qty' => $latestBatch['total'],
                    ]);
                } else {
                    $remainReduceQty = $qty[$index];
                    foreach ($stockBatches as $stb => $stockBatch) {
                        if ($stockBatch['total'] >= $remainReduceQty) { //single batch covers whole reduce qty quantity
                            $StockAdjustmentBatches->insert([
                                'adid' => $adid,
                                'batch_id' => $stockBatch['batchId'],
                                'qty' => $remainReduceQty,  //product qty
                                'action' => StockAdjustmentBatches::ACTION_REDUCE,
                                'before_qty' => $stockBatch['total'],
                            ]);
                            break;
                        } else { //use another batch (qty and id)
                            $StockAdjustmentBatches->insert([
                                'adid' => $adid,
                                'batch_id' => $stockBatch['batchId'],
                                'qty' => $stockBatch['total'],  //single batch qty
                                'action' => StockAdjustmentBatches::ACTION_REDUCE,
                                'before_qty' => $stockBatch['total'],
                            ]);
                            $remainReduceQty -= $stockBatch['total']; //reduce remain qty
                        }
                        if ($remainReduceQty == 0) break; //no more transfer qty left
                    }
                }
            }
        }
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Stock Adjusted successfully";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 5000;
    }

    redirect('stocks', 'stock_adjustment_list');
}

if ($action == 'adjustment_print') {
    $can_view = Users::can(OtherRights::adjust_stock) || Users::isAllowed('stocks', 'stock_adjustment_list', false);
    if (!$can_view) redirect('authenticate', 'access_page', ['right_action' => base64_encode('view stock adjustment')]);

    $adjustmentno = $_GET['adjustmentno'];
    if ($adjustment = $StockAdjustments->get($adjustmentno)) {
        $adjustmentBatches = $Stocks->stockAdjustmentBatchWise($adjustmentno);
//        debug($adjustmentBatches);
        $newArray = [];
        foreach ($adjustmentBatches as $index => $item) {
            $newArray[$item['id']]['adjustmentno'] = $item['id'];
            $newArray[$item['id']]['doc'] = $item['doc'];
            $newArray[$item['id']]['locationname'] = $item['locationname'];
            $newArray[$item['id']]['issuedby'] = $item['issuedby'];
            $newArray[$item['id']]['description'] = $item['remarks'];
            $newArray[$item['id']]['products'][$item['detailId']]['detailId'] = $item['detailId'];
            $newArray[$item['id']]['products'][$item['detailId']]['productname'] = $item['productname'];
            $newArray[$item['id']]['products'][$item['detailId']]['productdescription'] = $item['productdescription'];
            $newArray[$item['id']]['products'][$item['detailId']]['barcode_office'] = $item['barcode_office'];
            $newArray[$item['id']]['products'][$item['detailId']]['action'] = $item['action'];
            $newArray[$item['id']]['products'][$item['detailId']]['current_stock'] = $item['current_stock'];
            $newArray[$item['id']]['products'][$item['detailId']]['qty'] += $item['qty'];

            $newArray[$item['id']]['products'][$item['detailId']]['batch_variation'] += $item['action'] == StockAdjustmentBatches::ACTION_ADD
                ? $item['qty']
                : (-$item['qty']);

            $newArray[$item['id']]['products'][$item['detailId']]['after_qty'] = $item['current_stock'] + $newArray[$item['id']]['products'][$item['detailId']]['batch_variation'];

            $newArray[$item['id']]['products'][$item['detailId']]['track_expire_date'] = $item['track_expire_date'];
            $newArray[$item['id']]['products'][$item['detailId']]['trackserialno'] = $item['trackserialno'];
            $newArray[$item['id']]['products'][$item['detailId']]['detail_remarks'] = $item['detail_remarks'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['batchId'] = $item['batchId'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['batch_no'] = $item['batch_no'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['qty'] = $item['qty'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['action'] = $item['action'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['before_qty'] = $item['before_qty'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['after_qty'] = $item['after_qty'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['expire_date'] = $item['expire_date'];
        }
        $newArray = array_values($newArray)[0];
//        debug($newArray);

        //todo include serial no
        $data['adjustment'] = $newArray;
        $data['layout'] = 'stock_adjustment_print.tpl.php';
    } else {
        debug("<h1>Adjustments Not Found</h1>" . PHP_EOL .
            "<a href='?'>Home</a>");
    }
}


if ($action == 'stock_manufacture_list') {
    Users::isAllowed();

    $manufactureno = removeSpecialCharacters($_POST['manufactureno']);
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $tData['fromdate'] = $fromdate = $_GET['fromdate'] ?: TODAY;
    $tData['todate'] = $todate = $_GET['todate'];

    $title = [];
    if ($manufactureno) $title[] = "Manufacture No: " . $manufactureno;
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($createdby) $title[] = "Issued by: " . Users::$userClass->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);

    $tData['manufactures'] = StockManufactures::$staticClass->getList($manufactureno, $locationid, '');

    $tData['locations'] = Locations::$locationClass->locationList('', '', '', $_SESSION['member']['locationid']);
    $data['content'] = loadTemplate('stock_manufacture_list.tpl.php', $tData);
}

if ($action == 'manufacture_stock') {
    Users::can(OtherRights::manufacture_stock, true);
    $manufactureid = removeSpecialCharacters($_GET['manufactureno']);
    if ($manufactureid) {
        $manufacture = StockManufactures::$staticClass->get($manufactureid);
        if (!$manufacture) {
            $_SESSION['error'] = "Stock manufacture not found!";
            redirectBack();
        }
        if ($manufacture['approvedby']) {
            $_SESSION['error'] = "Stock manufacture already approved cant be edited!";
            redirectBack();
        }
        $manufacture = StockManufactures::$staticClass->getList($manufactureid)[0];
        $details = [];
        foreach (StockManufactureDetails::$staticClass->getList($manufactureid) as $item) {
//        debug($item);
            if (empty($item['smdi'])) {
                $details[$item['id']]['id'] = $item['id'];
                $details[$item['id']]['stockid'] = $item['stockid'];
                $details[$item['id']]['costprice'] = $item['costprice'];
                $details[$item['id']]['qty'] = $item['qty'];
                $details[$item['id']]['stock_qty'] = $item['stock_qty'];
                $details[$item['id']]['productid'] = $item['productid'];
                $details[$item['id']]['productname'] = $item['productname'];
                $details[$item['id']]['description'] = $item['description'];
                $details[$item['id']]['track_expire_date'] = $item['track_expire_date'];
                $details[$item['id']]['trackserialno'] = $item['trackserialno'];
                $details[$item['id']]['track_expire_date'] = $item['track_expire_date'];
                $details[$item['id']]['barcode_office'] = $item['barcode_office'];
                $details[$item['id']]['barcode_manufacture'] = $item['barcode_manufacture'];
                if (!isset($details[$item['id']]['current_stock'])) {
                    $current_stock = $Stocks->calcStock(
                        $manufacture['locationid'], $item['stockid'], "",
                        "", "", "", "",
                        "", "", "", "", "", "",
                        "", "", "", "", false, true,
                        '', '', true, true
                    );
                    $current_stock = array_values($current_stock)[0];
                    $details[$item['id']]['current_stock'] = $current_stock['total'] ?: 0;
                }
                $details[$item['id']]['total_costprice'] = round($item['costprice'] * $item['qty'], 2);
                //todo for tracking items add batches
            } else {
                $details[$item['smdi']]['end_products'][$item['id']]['smei'] = $item['id'];
                $details[$item['smdi']]['end_products'][$item['id']]['stockid'] = $item['stockid'];
                $details[$item['smdi']]['end_products'][$item['id']]['costprice'] = $item['costprice'];
                $details[$item['smdi']]['end_products'][$item['id']]['quickprice'] = $item['quickprice'];
                $details[$item['smdi']]['end_products'][$item['id']]['qty'] = $item['qty'];
                $details[$item['smdi']]['end_products'][$item['id']]['stock_qty'] = $item['stock_qty'];
                $details[$item['smdi']]['end_products'][$item['id']]['productid'] = $item['productid'];
                $details[$item['smdi']]['end_products'][$item['id']]['productname'] = $item['productname'];
                $details[$item['smdi']]['end_products'][$item['id']]['description'] = $item['description'];
                $details[$item['smdi']]['end_products'][$item['id']]['track_expire_date'] = $item['track_expire_date'];
                $details[$item['smdi']]['end_products'][$item['id']]['barcode_office'] = $item['barcode_office'];
                $details[$item['smdi']]['end_products'][$item['id']]['barcode_manufacture'] = $item['barcode_manufacture'];
                $details[$item['smdi']]['end_products'][$item['id']]['baseprice'] = $item['baseprice'];
                $details[$item['smdi']]['end_products'][$item['id']]['vat_percent'] = $item['vat_percent'];
                $details[$item['smdi']]['end_products'][$item['id']]['end_total_costprice'] = round($item['costprice'] * $item['qty'], 2);

                if (!isset($details[$item['smdi']]['overall_end_products_costprice'])) $details[$item['smdi']]['overall_end_products_costprice'] = 0;
                $details[$item['smdi']]['overall_end_products_costprice'] += $details[$item['smdi']]['end_products'][$item['id']]['end_total_costprice'];

                //current costprice
                $currentPrice = CurrentPrices::$currentPricesClass->find(['branchid' => $manufacture['branchid'], 'productid' => $item['productid']])[0];
//                debug($currentPrice);
                $details[$item['smdi']]['end_products'][$item['id']]['current_costprice'] = $currentPrice['costprice'];
                $details[$item['smdi']]['end_products'][$item['id']]['current_quickprice'] = $currentPrice['quick_price_inc'];
                //todo for tracking items add batches
            }
        }
        $manufacture['details'] = $details;
//        debug($manufacture);
        $tData['manufacture'] = $manufacture;
    }

    $data['content'] = loadTemplate('manufacture_stock.tpl.php', $tData);
}

if ($action == 'save_manufacture') {
    Users::can(OtherRights::manufacture_stock, true);

//    debug($_POST);
    $manufacture = $_POST['manufacture'];
    $raw_materials = $_POST['raw_materials'];
    $end_products = $_POST['end_products'];
    $serialnos = $_POST['serialno'];

    validate($raw_materials['stockid']);
    validate($end_products);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//        debug($raw_materials);

        if (!Locations::$locationClass->get($manufacture['locationid'])) throw new Exception("Location not found!");
        if (empty($manufacture['id'])) {  //new manufacture
            if (StockManufactures::$staticClass->find(['token' => $manufacture['token']])) throw new Exception("System found this form already submitted,\ntransaction stopped to avoid duplicate inputs");
            $manufacture['createdby'] = $_SESSION['member']['id'];
            StockManufactures::$staticClass->insert($manufacture);
            $manufactureid = StockManufactures::$staticClass->lastId();
        } else {
            unset($manufacture['token']);
            $manufactureid = $manufacture['id'];
            if (!StockManufactures::$staticClass->get($manufactureid)) throw new Exception("Manufacture info not found for update");
            $manufacture['modifiedby'] = $_SESSION['member']['id'];
            $manufacture['dom'] = TIMESTAMP;
            StockManufactures::$staticClass->update($manufactureid, $manufacture);

            //clear old records
            $details = StockManufactureDetails::$staticClass->find(['manufactureid' => $manufactureid]);
            $smdis = array_column($details, 'id');
            StockManufactureUsedBatches::$staticClass->deleteWhereMany(['smdi' => $smdis]); //clear used batches
            Batches::$batchesClass->deleteWhereMany(['smei' => $smdis]);//clear batches
            StockManufactureSerialnos::$staticClass->deleteWhereMany(['smdi' => $smdis]);//clear used serial nos
            StockManufactureDetails::$staticClass->deleteWhere(['manufactureid' => $manufactureid]); //clear old details
        }

        foreach ($raw_materials['stockid'] as $index => $stockid) {
//            debug($raw_materials);
            $product = Stocks::$stockClass->getProduct($stockid);
            //validate stock
            $current_stock = $Stocks->calcStock(
                $manufacture['locationid'], $stockid, "",
                "", "", "", "",
                "", "", "", "", "", "",
                "", "", "", "", false, true,
                '', '', true, true
            );

            $raw_material_qty = $raw_materials['qty'][$index]; //raw material qty
            $current_stock = array_values($current_stock)[0];
            if ($current_stock['total'] < $raw_material_qty) {
                throw new Exception("Product ({$product['name']}), does not have enough stock");
            }

            StockManufactureDetails::$staticClass->insert([
                'manufactureid' => $manufactureid,
                'stockid' => $stockid,
                'costprice' => removeComma($raw_materials['costprice'][$index]),
                'qty' => $raw_material_qty,
                'stock_qty' => $raw_materials['current_stock'][$index],
                'createdby' => $_SESSION['member']['id'],
            ]);
            $smdi = StockManufactureDetails::$staticClass->lastId();

            $stockBatches = $current_stock['batches'];
            $remainQty = $raw_material_qty; //total product raw material qty
            //distribute used batch
            foreach ($stockBatches as $stb => $stockBatch) {
                if ($stockBatch['total'] >= $remainQty) { //single batch covers whole qty
                    StockManufactureUsedBatches::$staticClass->insert([
                        'smdi' => $smdi,
                        'batchid' => $stockBatch['batchId'],
                        'qty' => $remainQty, //single batch qty
                    ]);
                    break;
                } else { //use another batch (qty and id)
                    $StockTransferBatches->insert([
                        'smdi' => $smdi,
                        'batchid' => $stockBatch['batchId'],
                        'qty' => $stockBatch['total'], //single batch qty
                    ]);
                    $remainQty -= $stockBatch['total']; //reduce remain qty
                }
                if ($remainQty == 0) break; //no more transfer qty left
            }

            //insert serial nos
            if ($product['trackserialno'] && empty($serialnos[$stockid]['serial_number']))
                throw new Exception("Product {$product['name']} require serial no");
            if (!empty($serialnos[$stockid]['serial_number'])) {
                foreach ($serialnos[$stockid]['serial_number'] as $si => $number) {
                    $sno = $SerialNos->find(['number' => $number]); //todo if more validation is required check here
                    if (empty($sno)) { //create new, it happens if serialnos entered manually
                        if ($product['validate_serialno'] == 1) throw new Exception("Product {$product['name']} validates serial no from stock, serialno $number not found!");
                        SerialNos::$serialNoClass->insert([
                            'number' => $number,
                            'initial_stockid' => $stockid,
                            'current_stock_id' => $stockid, //change location if already approved
                            'source' => SerialNos::SOURCE_MANUFACTURE,
                            'createdby' => $_SESSION['member']['id']
                        ]);
                        $serialno_id = SerialNos::$serialNoClass->lastId();
                    } else {
                        $serialno_id = $sno[0]['id'];
                    }

                    StockManufactureSerialnos::$staticClass->insert([
                        'smdi' => $smdi,
                        'snoid' => $serialno_id,
                    ]);
                }
            }

            //end products
            if (empty($end_products[$stockid])) throw new Exception("End products not found for product {$product['name']}");
            foreach ($end_products[$stockid]['productid'] as $eindex => $end_productid) {
                $end_stockid = Stocks::$stockClass->find(['locid' => $manufacture['locationid'], 'productid' => $end_productid])[0]['id'];
                if (empty($end_stockid)) {
                    Stocks::$stockClass->insert([
                        'locid' => $manufacture['locationid'],
                        'productid' => $end_productid,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $end_stockid = Stocks::$stockClass->lastId();
                }


                $end_costprice = removeComma($end_products[$stockid]['costprice'][$eindex]);
                $end_quickprice = removeComma($end_products[$stockid]['quickprice'][$eindex]);
                $end_qty = $end_products[$stockid]['qty'][$eindex];
                StockManufactureDetails::$staticClass->insert([
                    'manufactureid' => $manufactureid,
                    'smdi' => $smdi, //raw material id
                    'stockid' => $end_stockid,
                    'costprice' => $end_costprice,
                    'quickprice' => $end_quickprice,
                    'qty' => $end_qty,
                    'createdby' => $_SESSION['member']['id'],
                ]);
                $smei = StockManufactureDetails::$staticClass->lastId();

                //create new batch
                Batches::$batchesClass->insert([
                    'batch_no' => Batches::generateBatchNo(false, true),
                    'qty' => $end_qty,
                    'smei' => $smei,
                ]);

                //update cost price after approval
            }
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Manufacture saved successfully";
        redirect('stocks', 'stock_manufacture_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        debug($e);
        redirectBack();
    }
}

if ($action == 'view_manufacture') {
    $manufactureid = removeComma($_GET['manufactureno']);

    $manufacture = StockManufactures::$staticClass->get($manufactureid);
    if (!$manufacture) {
        $_SESSION['error'] = "Stock manufacture not found!";
        redirectBack();
    }
    $manufacture = StockManufactures::$staticClass->getList($manufactureid)[0];
    $details = [];
    foreach (StockManufactureDetails::$staticClass->getList($manufactureid) as $item) {
//        debug($item);
        if (empty($item['smdi'])) {
            $details[$item['id']]['id'] = $item['id'];
            $details[$item['id']]['stockid'] = $item['stockid'];
            $details[$item['id']]['costprice'] = $item['costprice'];
            $details[$item['id']]['qty'] = $item['qty'];
            $details[$item['id']]['total_raw_material_costprice'] = round($item['qty'] * $item['costprice'], 2);
            $details[$item['id']]['stock_qty'] = $item['stock_qty'];
            $details[$item['id']]['productid'] = $item['productid'];
            $details[$item['id']]['productname'] = $item['productname'];
            $details[$item['id']]['description'] = $item['description'];
            $details[$item['id']]['track_expire_date'] = $item['track_expire_date'];
            $details[$item['id']]['trackserialno'] = $item['trackserialno'];
            $details[$item['id']]['barcode_office'] = $item['barcode_office'];
            $details[$item['id']]['barcode_manufacture'] = $item['barcode_manufacture'];
            //todo for tracking items add batches
            if ($item['trackserialno']) {
                $snos = StockManufactureSerialnos::$staticClass->find(['smdi' => $item['id']]);
                $snoids = array_column($snos, 'snoid');
                $snos = SerialNos::$serialNoClass->findMany(['id' => $snoids]);
                $details[$item['id']]['serialnos'] = $snos;
            }
        } else {
            $details[$item['smdi']]['end_products'][$item['id']]['smei'] = $item['id'];
            $details[$item['smdi']]['end_products'][$item['id']]['stockid'] = $item['stockid'];
            $details[$item['smdi']]['end_products'][$item['id']]['costprice'] = $item['costprice'];
            $details[$item['smdi']]['end_products'][$item['id']]['quickprice'] = $item['quickprice'];
            $details[$item['smdi']]['end_products'][$item['id']]['qty'] = $item['qty'];
            $details[$item['smdi']]['end_products'][$item['id']]['stock_qty'] = $item['stock_qty'];
            $details[$item['smdi']]['end_products'][$item['id']]['productid'] = $item['productid'];
            $details[$item['smdi']]['end_products'][$item['id']]['productname'] = $item['productname'];
            $details[$item['smdi']]['end_products'][$item['id']]['description'] = $item['description'];
            $details[$item['smdi']]['end_products'][$item['id']]['track_expire_date'] = $item['track_expire_date'];
            $details[$item['smdi']]['end_products'][$item['id']]['barcode_office'] = $item['barcode_office'];
            $details[$item['smdi']]['end_products'][$item['id']]['barcode_manufacture'] = $item['barcode_manufacture'];
            $details[$item['smdi']]['end_products'][$item['id']]['end_total_costprice'] = round($item['costprice'] * $item['qty'], 2);

            if (!isset($details[$item['smdi']]['overall_end_products_costprice'])) $details[$item['smdi']]['overall_end_products_costprice'] = 0;
            $details[$item['smdi']]['overall_end_products_costprice'] += $details[$item['smdi']]['end_products'][$item['id']]['end_total_costprice'];
            //todo for tracking items add batches
        }
    }
    $manufacture['details'] = $details;
    $tData['manufacture'] = $manufacture;
//    debug($manufacture);
    $data['content'] = loadTemplate('view_manufacture.tpl.php', $tData);
}

if ($action == 'approve_manufacture') {
    Users::can(OtherRights::approve_manufacture, true);
//    debug($_GET);
    $manufactureid = removeSpecialCharacters($_GET['manufactureno']);
    $manufacture = StockManufactures::$staticClass->get($manufactureid);
    if (!$manufacture) {
        $_SESSION['error'] = "Stock manufacture info not found!";
        redirectBack();
    }
    if ($manufacture['approvedby']) {
        $_SESSION['error'] = "Stock manufacture already approved!";
        redirectBack();
    }
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $manufacture = StockManufactures::$staticClass->getList($manufactureid)[0];
        $details = [];
        foreach (StockManufactureDetails::$staticClass->getList($manufactureid) as $item) {
            if (empty($item['smdi'])) { //raw material validate stock
                $current_stock = $Stocks->calcStock(
                    $manufacture['locationid'], $item['stockid'], "",
                    "", "", "", "",
                    "", "", "", "", "", "",
                    "", "", "", "", false, true,
                    '', '', true, true
                );

                $current_stock = array_values($current_stock)[0];
                if ($current_stock['total'] < $item['qty']) throw new Exception("Product ({$item['productname']}), does not have enough stock.\nTry editing the manufacture");
                //check batches
                $stockBatches = $current_stock['batches'];
                foreach (StockManufactureUsedBatches::$staticClass->find(['smdi' => $item['id']]) as $usedBatch) {
                    $batch = array_filter($stockBatches, function ($b) use ($usedBatch) {
                        return $usedBatch['batchid'] == $b['batchId'] && $b['total'] >= $usedBatch['qty'];
                    });
                    if (empty($batch)) throw new Exception("System found Product ({$item['productname']}) have used batch that does not have enough stock.\nTry editing the manufacture");
                }

                //validate serialnos
                if ($item['trackserialno']) {
                    $used_serialnos = StockManufactureSerialnos::$staticClass->find(['smdi' => $item['id']]);
                    if (count($used_serialnos) != $item['qty']) throw new Exception("Product {$item['productname']} missing serial no info,\nUsed raw material qty does not match serial no qty!");
                    foreach ($used_serialnos as $smsno) {
                        $sno = SerialNos::$serialNoClass->get($smsno['snoid']);
                        if ($sno['current_stock_id'] != $item['stockid']) throw new Exception("Serial no {$sno['number']} does not exist in the manufacture location!");
                        if ($sno['sdi'] || $sno['smdi']) throw new Exception("Product {$item['productname']},\nSerial no {$sno['number']} already been used!");
                        SerialNos::$serialNoClass->update($sno['id'], ['smdi' => $item['id']]); //use for manufacture
                    }
                }

            } else { //end product update costprice
                $currentPrice = CurrentPrices::$currentPricesClass->find(['branchid' => $manufacture['branchid'], 'productid' => $item['productid']])[0];
                if (empty($currentPrice) || ($item['costprice'] > $currentPrice['costprice'])) { //update only if new item has bigger costprice or not cost existed
                    CurrentPrices::updatePrice(
                        $manufacture['branchid'],
                        $item['productid'],
                        $item['costprice'],
                        $item['quickprice'],
                        '',
                        "Stock manufacture no $manufactureid",
                        true //force creating
                    );
                }
            }
        }

        StockManufactures::$staticClass->update($manufactureid, [
            'approvedby' => $_SESSION['member']['id'],
            'approvedate' => TIMESTAMP,
        ]);
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Approved successfully";
        redirect('stocks', 'stock_manufacture_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}


//reports

if ($action == 'stock_report') {
    Users::isAllowed();
//    debug($_GET);

    $stocklocation = $_GET['stocklocation'];
    $stockdate = $_GET['stockdate'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $print_pdf = isset($_GET['print_pdf']);
    $tData['pdf_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&print_pdf";
    $title = [];
    if ($depart) {
        $department = $Departments->get($depart);
        $title[] = "Department: " . $department['name'];
    }
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) {
        $brand = $Models->get($brands);
        $title[] = "Brand: " . $brand['name'];
    }
    if ($productid) {
        $product = $Products->get($productid);
        $title[] = "Product: " . $product['name'];
    }
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($stockdate) $title[] = "Stock Date: " . fDate($stockdate);
    $tData['stockdate'] = $stockdate;
    $tData['title'] = implode(' | ', $title);

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
    $stockList = $Stocks->calcStock(
        $location['id'], "",
        $stockdate, "", $productid, "", "", "",
        "", $categories, $brands, $depart, "", "", "", "",
        "", $with_expired = true, $group_batch = true, $productcategoryid, $subcategoryid,
        $with_stock = false);

    foreach ($stockList as $key => $s) {
        $product = $Products->get($s['productid']);
        $bulk = $BulkUnits->get($product['bulk_units']);


        $stockList[$key]['level'] = 'normal';
        if ($product['reorder_level'] == 1) {
            $level = $Products->reorderList($s['id'])[0];
            if ($s['total'] <= $level['minqty']) {
                $stockList[$key]['level'] = 'below';
                $stockList[$key]['minqty'] = $level['minqty'];
            }
        }
        if ($bulk['rate']) {
            $bulkQty = floor(($s['total']) / $bulk['rate']);
            $unitQty = floor(($s['total']) % $bulk['rate']);

            $remain = '';
            if ($bulkQty) $remain .= $bulkQty . ' ' . $bulk['name'];
            if ($unitQty) {
                if ($bulkQty) $remain .= ', ';
                $remain .= $unitQty . ' ' . $s['unitName'];
            }
        }

        $stockList[$key]['inunit'] = $remain;
        $stockList[$key]['bulkUnit'] = $bulk['name'];
        $stockList[$key]['bulkRate'] = $bulk['rate'];
    }

    if ($print_pdf) {
        $data['stockdate'] = $stockdate ?: TODAY;
        $data['stocklist'] = $stockList;
        $data['layout'] = 'stock_report_pdf_print.tpl.php';
    } else {
        $tData['stocklist'] = $stockList;
        // debug($tData['stocklist']);
        $tData['depart'] = $Departments->getAllActive();
        $tData['categories'] = $Categories->getAllActive();
        $tData['brands'] = $Models->getAllActive();
        $tData['productCategories'] = $ProductCategories->getAllActive('name');
        $tData['productSubcategories'] = $ProductSubCategories->getAllActive('name');
        // debug($tData['stocklist']);
        $data['content'] = loadTemplate('stock_report.tpl.php', $tData);
    }
}

if ($action == 'quantitywise_stock_report') {
    Users::isAllowed();
//    debug($_GET);

    $stocklocation = $_GET['stocklocation'];
    $stockdate = $_GET['stockdate'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $tData['quantity'] = $quantity = $_GET['quantity'];
    $tData['qtycategory'] = $qtycategory = $_GET['qtycategory'];


    $title = [];
    if ($depart) $title[] = "Department: " . $Departments->get($depart)['name'];
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) $title[] = "Brand: " . $Models->get($brands)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($stockdate) $title[] = "Stock Date: " . fDate($stockdate);
    if ($qtycategory) $title[] = ucfirst($qtycategory) . ": " . $quantity;
    $tData['stockdate'] = $stockdate;
    $tData['title'] = implode(' | ', $title);
    $_SESSION['pagetitle'] = CS_COMPANY . " " . $tData['title'];
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


    if (isset($_GET['quantity']) && $qtycategory)
        $stockList = $Stocks->calcStock(
            $location['id'], "",
            $stockdate, "", $productid, "", "", "",
            "", $categories, $brands, $depart, "", "", "", "",
            "", $with_expired = true, $group_batch = true, $productcategoryid, $subcategoryid,
            $with_stock = false);

    foreach ($stockList as $key => $s) {

        if ($qtycategory == "equalto" && $s['total'] != $quantity) {
            unset($stockList[$key]);
            continue;
        } elseif ($qtycategory == "lessthan" && $s['total'] >= $quantity) {
            unset($stockList[$key]);
            continue;
        } elseif ($qtycategory == "morethan" && $s['total'] <= $quantity) {
            unset($stockList[$key]);
            continue;
        }

        $product = $Products->get($s['productid']);
        $bulk = $BulkUnits->get($product['bulk_units']);

        if ($bulk['rate']) {
            $bulkQty = floor(($s['total']) / $bulk['rate']);
            $unitQty = floor(($s['total']) % $bulk['rate']);

            $remain = '';
            if ($bulkQty) $remain .= $bulkQty . ' ' . $bulk['name'];
            if ($unitQty) {
                if ($bulkQty) $remain .= ', ';
                $remain .= $unitQty . ' ' . $s['unitName'];
            }
        }

        $stockList[$key]['inunit'] = $remain;
        $stockList[$key]['bulkUnit'] = $bulk['name'];
        $stockList[$key]['bulkRate'] = $bulk['rate'];
    }

    $tData['stocklist'] = $stockList;
    // debug($tData['stocklist']);
    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive('name');
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive('name');
    // debug($tData['stocklist']);
    $data['content'] = loadTemplate('stock_report_quantitywise.tpl.php', $tData);
}

if ($action == 'advanced_stock_report') {
    Users::isAllowed();
//    debug($_GET);

    $stocklocation = $_GET['stocklocation'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $title = [];
    if ($depart) {
        $department = $Departments->get($depart);
        $title[] = "Department: " . $department['name'];
    }
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) {
        $brand = $Models->get($brands);
        $title[] = "Brand: " . $brand['name'];
    }
    if ($productid) {
        $product = $Products->get($productid);
        $title[] = "Product: " . $product['name'];
    }
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    $tData['title'] = implode(' | ', $title);

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
    $tData['stocklist'] = $Stocks->calcStock(
        $location['id'], "",
        "", "", $productid, "", "", "",
        "", $categories, $brands, $depart, "", "", "", "",
        "", $with_expired = true, $group_batch = true, $productcategoryid, $subcategoryid,
        $with_stock = false, true);

    foreach ($tData['stocklist'] as $key => $s) {
        $product = $Products->get($s['productid']);
        $bulk = $BulkUnits->get($product['bulk_units']);


        $tData['stocklist'][$key]['level'] = 'normal';
        if ($product['reorder_level'] == 1) {
            $level = $Products->reorderList($s['id'])[0];
            if ($s['in_stock_qty'] <= $level['minqty']) {
                $tData['stocklist'][$key]['level'] = 'below';
                $tData['stocklist'][$key]['minqty'] = $level['minqty'];
            }
        }
        if ($bulk['rate']) {
            $bulkQty = floor(($s['in_stock_qty']) / $bulk['rate']);
            $unitQty = floor(($s['in_stock_qty']) % $bulk['rate']);

            $remain = '';
            if ($bulkQty) $remain .= $bulkQty . ' ' . $bulk['name'];
            if ($unitQty) {
                if ($bulkQty) $remain .= ', ';
                $remain .= $unitQty . ' ' . $s['unitName'];
            }
        }

        $tData['stocklist'][$key]['inunit'] = $remain;
        $tData['stocklist'][$key]['bulkUnit'] = $bulk['name'];
        $tData['stocklist'][$key]['bulkRate'] = $bulk['rate'];


        //active pending order
        $orderDetails = $Orderdetails->getList('', $location['id'], $product['id'], Orders::STATUS_PENDING);
        $tData['stocklist'][$key]['pending_order'] = array_sum(array_column($orderDetails, 'qty'));

        //pending sales
        $salesDetails = $Salesdetails->getList('', $location['id'], $product['id'], "'0'");
        $tData['stocklist'][$key]['pending_sale'] = array_sum(array_column($salesDetails, 'quantity'));

        //expecting stock
        $expectingStocks = $LPO->expectingStock($location['id'], $product['id']);
        $tData['stocklist'][$key]['expecting_stock'] = array_sum(array_column($expectingStocks, 'qty'));
    }

    // debug($tData['stocklist']);
    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive('name');
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive('name');
    // debug($tData['stocklist']);
    $data['content'] = loadTemplate('stock_report_advanced.tpl.php', $tData);
}

if ($action == 'stock_aging_report') {
//    debug($_GET);
    $locationid = $_GET['locationid'];
    $productid = $_GET['productid'];
    $age_range = $_GET['age_range'];

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

    $location = $Locations->get($locationid);
    $branch = $Locations->getBranch($locationid);
    $location['branchname'] = $branch['name'];
    $title = [];
    $title[] = "Location: " . $location['name'] . " - " . $branch['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    $tData['title'] = implode(' | ', $title);

    $stocks = $Stocks->calcStock(
        $locationid, '', '', '', $productid, '', '', '', '',
        '', '', '', '', '', '', '', '', false,
        true, '', ''
    );

    if (!is_array($age_range)) $age_range = [30, 60, 100];

    $totals = [];
    foreach ($stocks as $index => $s) {
        $currentBranchPrice = $CurrentPrices->find(['branchid' => $branch['id'], 'productid' => $s['productid']])[0];
        $stocks[$index]['costprice'] = $currentBranchPrice['costprice'];
        $stocks[$index]['stock_value'] = $currentBranchPrice['costprice'] * $s['total'];
        foreach ($s['batches'] as $bi => $b) {
            $age = $Batches->batchReceiveHistory($locationid, $b['batchId'], $age_range)[0]['in_stock'];
            $stock_value = $currentBranchPrice['costprice'] * $b['total'];
            $stocks[$index]['aging'][$age]['qty'] += $b['total'];
            $stocks[$index]['aging'][$age]['stock_value'] += $stock_value;
            $totals['ranges'][$age] += $stock_value;
            $totals['total'] += $stock_value;
        }
        unset($stocks[$index]['batches']);
//        if (count($s['batches']) > 2) debug($stocks[$index]);
    }
    //transform days into ranges
    $ranges = array_unique($age_range);
    sort($ranges);
    $lastElement = end($ranges);
    $ranges = [];
    foreach ($age_range as $index => $day) {
        if ($index == 0) {
            $ranges[] = "<= $age_range[0]";
        }
        if ($lastElement == $day) {
            $ranges[] = "$day >";
        }
        if (($index == 0 && $lastElement != $day) or ($index != 0 && $lastElement != $day)) {
            $next_day = $age_range[$index + 1];
            $ranges[] = "$day - $next_day";
        }
    }
    $tData['ranges'] = $ranges;
    $tData['stocks'] = $stocks;
    $tData['location'] = $location;
    $tData['totals'] = $totals;
    $data['content'] = loadTemplate('stock_aging_report.tpl.php', $tData);
}

if ($action == 'stock_planning_report') {
    Users::isAllowed();

//    debug($_GET);
    $productid = $_GET['productid'];
    $tData['modelid'] = $modelid = $_GET['modelid'];
    $catid = $_GET['catid'];
    $tData['selected_months'] = $months = $_GET['months'];
    $branchids = $_GET['branchids'];
    $locationid = $_GET['locationid'];
    $userid = $_GET['userid'];
    $tData['productcategoryid'] = $productcategoryid = $_GET['productcategoryid'];
    $tData['subcategoryid'] = $subcategoryid = $_GET['subcategoryid'];
    $tData['deptid'] = $deptid = $_GET['deptid'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];

    //creating title
    $title = [];
    $selectedBranches = [];
    if (!empty($branchids)) {
        $selectedBranches = Branches::$branchClass->findMany(['id' => $branchids]);
        $title[] = "Branches: " . implode(", ", array_column($selectedBranches, 'name'));
        $branchids = array_column($selectedBranches, 'id');
    }

    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($modelid) $title[] = "Brand: " . $Models->get($modelid)['name'];
    if ($productcategoryid) $title[] = "Product category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($deptid) $title[] = "Department: " . $Departments->get($deptid)['name'];


    $tData['title'] = implode(' | ', $title);
    //arrange products
    $monthNames = [];
    $productArray = [];

    //arrange months ascending
    usort($months, function ($a, $b) { //asc
        if (strtotime('01-' . $a) == strtotime('01-' . $b)) {
            return 0;
        }
        return (strtotime('01-' . $a) < strtotime('01-' . $b)) ? -1 : 1;
    });
    $monthNames = array_map(function ($m) {
        return fDate('01-' . $m, 'M Y');
    }, $months);
//    debug($monthNames);
    if (!empty($branchids)) {
        foreach ($branchids as $branchid) {
            $locationids = array_column(Locations::$locationClass->find(['branchid' => $branchid]), 'id');
            foreach ($locationids as $locationid) {
                $stocks = $Stocks->calcStock(
                    $locationid, '', '', '', $productid, '', '', '', '',
                    '', $modelid, $deptid, '', '', '', '', '', false,
                    true, $productcategoryid, $subcategoryid, false
                );
                foreach ($stocks as $s) {
                    if (in_array($s['productid'], array_column($productArray, 'productid'))) {
                        $productArray[$s['productid']]['stock_qty'] += $s['total'];
                    } else {
                        $productArray[$s['productid']]['productid'] = $s['productid'];
                        $productArray[$s['productid']]['productname'] = $s['name'];
                        $productArray[$s['productid']]['barcode'] = $s['barcode_office'];
                        $productArray[$s['productid']]['description'] = $s['productdescription'];
                        $productArray[$s['productid']]['unitname'] = $s['unitName'];
                        $productArray[$s['productid']]['brandname'] = $s['brandName'];
                        $productArray[$s['productid']]['stock_qty'] += $s['total'];

                        $monthWiseSales = Sales::$saleClass->monthWiseSales($months, '', $s['productid'], '', '', '', '', '', '', '', $branchids);
                        foreach ($monthWiseSales as $m) {
                            $productArray[$s['productid']]['months'][$m['salesMonth']] = $m['quantity'];
                            $productArray[$s['productid']]['total_qty'] += $m['quantity'];
                        }
                        $total_qty = $productArray[$s['productid']]['total_qty'] ?: 0;
                        $month_count = count($months);
                        $avg = $month_count == 0 ? '' : round($total_qty / $month_count, 1);
                        $productArray[$s['productid']]['avg_qty'] = $avg;
                    }
                }
            }
        }
    }
//    debug($productArray);

    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['months'] = $monthNames;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['models'] = $Models->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $tData['branchids'] = $branchids;
    $tData['salesSummary'] = $productArray;
    $_SESSION['pagetitle'] = "Stock Planning Report";
    $data['content'] = loadTemplate('stock_planning_report.tpl.php', $tData);
}

if ($action == 'branches_stock' || $action == 'branches_stock_without_cost') {
    Users::isAllowed();

    $WITHOUT_COST = $action == 'branches_stock_without_cost';

    $tData['stockdate'] = $stockdate = $_GET['stockdate'] ?: TODAY;
    $productid = $_GET['productid'];
    $tData['branchids'] = $branchids = removeSpecialCharacters($_GET['branchids']);
    $tData['productcategoryid'] = $productcategoryid = removeSpecialCharacters($_GET['productcategoryid']);
    $tData['subcategoryid'] = $subcategoryid = removeSpecialCharacters($_GET['subcategoryid']);
    $tData['brandid'] = $brandid = removeSpecialCharacters($_GET['brandid']);
    $title = [];
    if ($stockdate) $title[] = "Stock Date: " . fDate($stockdate);
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($brandid) $title[] = "Brand: " . $Models->get($brandid)['name'];
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "SubCategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    $tData['title'] = implode(' | ', $title);

    $branches = Users::can(OtherRights::view_all_branch_stock)
        ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id")
        : $Branches->find(['id' => $_SESSION['member']['branchid']]);

    $selectedBranches = array_filter($branches, function ($b) use ($branchids) {
        return in_array($b['id'], $branchids);
    });


    $products = [];
    $total_stock_value = 0;
    foreach ($selectedBranches as $index => $b) {
        foreach ($Locations->find(['branchid' => $b['id'], 'status' => 'active']) as $l) {
            $stocks = $Stocks->calcStock(
                $l['id'], '', $stockdate, '', $productid, '', '', '', '',
                '', $brandid, '', '', '', '', '', '', false,
                true, $productcategoryid, $subcategoryid
            );

            foreach ($stocks as $s) {
                $currentBranchPrice = $CurrentPrices->find(['branchid' => $b['id'], 'productid' => $s['productid']])[0];
                $stock_value = ($currentBranchPrice['costprice'] * $s['total']);
                $total_stock_value += $stock_value;
                $selectedBranches[$index]['stock_value'] += $stock_value;
                if (in_array($s['productid'], array_column($products, 'productid'))) {
                    $products[$s['productid']]['qty'] += $s['total'];
                    $products[$s['productid']]['stock_value'] += $stock_value;
                    $products[$s['productid']]['branches'][$b['id']]['name'] = $b['name'];
                    $products[$s['productid']]['branches'][$b['id']]['costprice'] = $currentBranchPrice['costprice'];
                    $products[$s['productid']]['branches'][$b['id']]['qty'] += $s['total'];
                    $products[$s['productid']]['branches'][$b['id']]['stock_value'] += $stock_value;
                } else {
                    $products[$s['productid']]['productid'] = $s['productid'];
                    $products[$s['productid']]['productname'] = $s['name'];
                    $products[$s['productid']]['barcode'] = $s['barcode_office'];
                    $products[$s['productid']]['generic_name'] = $s['generic_name'];
                    $products[$s['productid']]['description'] = $s['productdescription'];
                    $products[$s['productid']]['unitname'] = $s['unitName'];
                    $products[$s['productid']]['qty'] += $s['total'];
                    $products[$s['productid']]['stock_value'] += $stock_value;
                    $products[$s['productid']]['branches'][$b['id']]['name'] = $b['name'];
                    $products[$s['productid']]['branches'][$b['id']]['costprice'] = $currentBranchPrice['costprice'];
                    $products[$s['productid']]['branches'][$b['id']]['qty'] += $s['total'];
                    $products[$s['productid']]['branches'][$b['id']]['stock_value'] += $stock_value;
                }
            }
        }
    }
    $tData['total_stock_value'] = $total_stock_value;
    $tData['products'] = $products;
    $tData['branches'] = $selectedBranches;
    $tData['branchlist'] = $branches;
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive();
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive();
    $data['content'] = $WITHOUT_COST ? loadTemplate('branches_stock_without_cost_report.tpl.php', $tData) : loadTemplate('branches_stock_report.tpl.php', $tData);
}

if ($action == 'branch_locationwise_stock_report') {
    $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    $productid = $_GET['productid'];
    $stockdate = $_GET['stockdate'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    if (Users::cannot(OtherRights::view_all_branch_stock)) $branchid = $_SESSION['member']['branchid'];
    $branch = $Branches->get($branchid);
    $title = [];
    if ($branchid) $title[] = "Branch: " . $branch['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "SubCategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($stockdate) $title[] = "Stock Date: " . fDate($stockdate);
    $tData['branch'] = $branch;
    $tData['stockdate'] = $stockdate;
    $tData['title'] = implode(' | ', $title);

    $locations = $Locations->find(['branchid' => $branchid, 'status' => 'active']);
    $products = [];
    $total_stock_value = 0;
    foreach ($locations as $index => $l) {
        $stocks = $Stocks->calcStock(
            $l['id'], '', $stockdate, '', $productid, '', '', '', '',
            '', '', '', '', '', '', '', '', false,
            true, $productcategoryid, $subcategoryid
        );

        foreach ($stocks as $s) {
            $currentBranchPrice = $CurrentPrices->find(['branchid' => $branchid, 'productid' => $s['productid']])[0];
            $stock_value = ($currentBranchPrice['costprice'] * $s['total']);
            $total_stock_value += $stock_value;
            $locations[$index]['stock_value'] += $stock_value;
//                debug($s);
            if (in_array($s['productid'], array_column($products, 'productid'))) {
                $products[$s['productid']]['total_qty'] += $s['total'];
                $products[$s['productid']]['stock_value'] += $stock_value;
                $products[$s['productid']]['locations'][$l['id']]['name'] = $l['name'];
                $products[$s['productid']]['locations'][$l['id']]['qty'] += $s['total'];
                $products[$s['productid']]['locations'][$l['id']]['stock_value'] += $stock_value;
            } else {
                $products[$s['productid']]['productid'] = $s['productid'];
                $products[$s['productid']]['productname'] = $s['name'];
                $products[$s['productid']]['generic_name'] = $s['generic_name'];
                $products[$s['productid']]['unitname'] = $s['unitName'];
                $products[$s['productid']]['costprice'] = $currentBranchPrice['costprice'];
                $products[$s['productid']]['total_qty'] += $s['total'];
                $products[$s['productid']]['stock_value'] += $stock_value;
                $products[$s['productid']]['locations'][$l['id']]['name'] = $l['name'];
                $products[$s['productid']]['locations'][$l['id']]['qty'] += $s['total'];
                $products[$s['productid']]['locations'][$l['id']]['stock_value'] += $stock_value;
            }
        }
    }
//    debug($locations);
    $tData['products'] = $products;
    $tData['locations'] = $locations;
    $tData['total_stock_value'] = $total_stock_value;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['productCategories'] = $ProductCategories->getAllActive();
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive();
    $data['content'] = loadTemplate('branch_locationwise_stock_report.tpl.php', $tData);
}

if ($action == 'stock_report_with_reorder') {
    Users::isAllowed();

    $stocklocation = $_GET['stocklocation'];
    $productid = $_GET['productid'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $variance = empty($_GET['variance']) ? 0 : $_GET['variance'];

    $title = [];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($depart) $title[] = "Department: " . $Departments->get($depart)['name'];

    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) {
        $brand = $Models->get($brands);
        $title[] = "Brand: " . $brand['name'];
    }
    if ($productcategoryid) {
        $pCategory = $ProductCategories->get($productcategoryid);
        $title[] = "Category: " . $pCategory['name'];
    }
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];

    $title[] = "Variance: " . $variance . "%";

    $tData['title'] = implode(' | ', $title);

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
    // debug($tData);
    // debug($tData['location']);


    $tData['stocklist'] = $Stocks->calcStock(
        $location['id'], '', '', '', $productid, '',
        '', '', '', $categories, $brands, $depart, '',
        '', '', '', '', $with_expired = true, $group_batch = true,
        $productcategoryid, $subcategoryid, $with_stock = false
    );


    foreach ($tData['stocklist'] as $key => $s) {
        $product = $Products->get($s['productid']);
        $bulk = $BulkUnits->get($product['bulk_units']);

        if ($product['reorder_level'] == 1) {
            $level = $Products->reorderList($s['id']);
            $tData['stocklist'][$key]['minLevel'] = $level[0]['minqty'];
            $tData['stocklist'][$key]['varianceLevel'] = $level[0]['minqty'] * (1 + ($variance / 100));
        }

        $tData['stocklist'][$key]['bulkUnit'] = $bulk['name'];
        $tData['stocklist'][$key]['bulkRate'] = $bulk['rate'];
    }

    // debug($tData['stocklist']);
    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive();
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive();
    // debug($tData['stocklist']);
    $data['content'] = loadTemplate('stock_report_with_reorder.tpl.php', $tData);
}

if ($action == 'bacthwise_stock_report') {
    $stocklocation = $_GET['stocklocation'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $batchno = $_GET['batchno'];
    $productid = $_GET['productid'];
    $expirebefore = $_GET['expirebefore'];
    $expireafter = $_GET['expireafter'];

    $title = [];

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


    $tData['location'] = $location = $Locations->get($stocklocation);
    $title[] = "Location: " . $tData['location']['name'];
    // debug($tData['location']);


    if ($depart) $title[] = "Department: " . $Departments->get($depart)['name'];
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) $title[] = "Brand: " . $Models->get($brands)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($batchno) $title[] = 'Batch No: ' . $batchno;
    if ($expirebefore) $title[] = 'Expire Before: ' . fDate($expirebefore);
    if ($expireafter) $title[] = 'Expire After: ' . fDate($expireafter);


    $title[] = isset($_GET['with_expired']) ? 'Include expired batches' : 'Without expired batches';
    $title[] = isset($_GET['with_stock']) ? 'Only batch with stock' : 'Include out of stock batch';
    $tData['title'] = implode(' | ', $title);

    $tData['stocklist'] = $Stocks->calcStock(
        $location['id'], '', '', '', $productid, '', '',
        '', '', $categories, $brands, $depart, '', '', $batchno, $expirebefore,
        $expireafter, $with_expired = isset($_GET['with_expired']), $group_batch = true, $productcategoryid, '',
        $with_stock = isset($_GET['with_stock']));

    foreach ($tData['stocklist'] as $key => $s) {
        $product = $Products->get($s['productid']);
        $bulk = $BulkUnits->get($product['bulk_units']);

        $tData['stocklist'][$key]['bulkUnit'] = $bulk['name'];
        $tData['stocklist'][$key]['bulkRate'] = $bulk['rate'];
    }

    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $data['content'] = loadTemplate('stock_report_detailed.tpl.php', $tData);
}

if ($action == 'expired_report') {
    Users::isAllowed();
    $stocklocation = $_GET['stocklocation'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productcategoryid = $_GET['productcategoryid'];
    $batchno = $_GET['batchno'];
    $productid = $_GET['productid'];

    $title = [];
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


    $tData['location'] = $location = $Locations->get($stocklocation);
    $title[] = "Location: " . $tData['location']['name'];

    if ($depart) {
        $department = $Departments->get($depart);
        $title[] = "Department: " . $department['name'];
    }
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) {
        $brand = $Models->get($brands);
        $title[] = "Brand: " . $brand['name'];
    }
    if ($productid) {
        $product = $Products->get($productid);
        $title[] = "Product: " . $product['name'];
    }
    if ($productcategoryid) {
        $pCategory = $ProductCategories->get($productcategoryid);
        $title[] = "Category: " . $pCategory['name'];
    }
    if ($batchno) {
        $title[] = 'Batch No: ' . $batchno;
    }

    $tData['title'] = implode(' | ', $title);

    $tData['stocklist'] = $Stocks->calcStock(
        $location['id'], '', '', '', $productid, '', '',
        '', '', $categories, $brands, $depart, '', '', $batchno, TODAY,
        '', $with_expired = true, $group_batch = true, $productcategoryid, '', $with_stock = true
    );

//debug($tData['stocklist']);
    foreach ($tData['stocklist'] as $key => $s) {
        $product = $Products->get($s['productid']);
        $bulk = $BulkUnits->get($product['bulk_units']);

        $tData['stocklist'][$key]['bulkUnit'] = $bulk['name'];
        $tData['stocklist'][$key]['bulkRate'] = $bulk['rate'];

        //arrange products
    }

//     debug($tData['stocklist']);
    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive();
    // debug($tData['stocklist']);
    $data['content'] = loadTemplate('stock_report_expired.tpl.php', $tData);
}

if ($action == 'stock_report_admin') {
    Users::isAllowed();

    $stocklocation = $_GET['stocklocation'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $title = [];
    if ($depart) {
        $department = $Departments->get($depart);
        $title[] = "Department: " . $department['name'];
    }
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) {
        $brand = $Models->get($brands);
        $title[] = "Brand: " . $brand['name'];
    }
    if ($productid) {
        $product = $Products->get($productid);
        $title[] = "Product: " . $product['name'];
    }
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    $tData['title'] = implode(' | ', $title);

    if (empty($stocklocation)) $stocklocation = $Locations->defaultBranchLocation($_SESSION['member']['branchid'])['id'];


    $tData['location'] = $location = $Locations->locationList($stocklocation)[0];

    $tData['stocklist'] = $Stocks->calcStock(
        $location['id'], '', '', '', '', '', '', '', '', $categories,
        $brands, $depart, '', '', '', '', '', $with_expired = false,
        $group_batch = true, $productcategoryid, $subcategoryid
    );

    $branchid = $Locations->getBranch($location['id'])['id'];
    $totalStockValue = 0;
    foreach ($tData['stocklist'] as $key => $s) {
        $product = $Products->get($s['productid']);

        $currentBranchPrice = $CurrentPrices->find(['branchid' => $branchid, 'productid' => $s['productid']])[0];
        $tData['stocklist'][$key]['costprice'] = $currentBranchPrice['costprice'];
        $tData['stocklist'][$key]['stock_value'] = $s['total'] * $currentBranchPrice['costprice'];

        $totalStockValue += $tData['stocklist'][$key]['stock_value'];

        $tData['stocklist'][$key]['level'] = 'normal';
        if ($product['reorder_level'] == 1) {
            $level = $Products->reorderList($s['id']);
            if ($s['total'] <= $level[0]['minqty']) {
                $tData['stocklist'][$key]['level'] = 'below';
            }
        }
    }

    $tData['totalStockValue'] = $totalStockValue;
    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive('name');
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive('name');

    $data['content'] = loadTemplate('stockreport_list_admin.tpl.php', $tData);
}

if ($action == 'stock_report_admin_with_supplier') {
    Users::isAllowed();

    $stocklocation = $_GET['stocklocation'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $title = [];
    if ($depart) {
        $department = $Departments->get($depart);
        $title[] = "Department: " . $department['name'];
    }
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) {
        $brand = $Models->get($brands);
        $title[] = "Brand: " . $brand['name'];
    }
    if ($productid) {
        $product = $Products->get($productid);
        $title[] = "Product: " . $product['name'];
    }
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    $tData['title'] = implode(' | ', $title);

    if (empty($stocklocation)) $stocklocation = $Locations->defaultBranchLocation($_SESSION['member']['branchid'])['id'];


    $tData['location'] = $location = $Locations->locationList($stocklocation)[0];

    $tData['stocklist'] = $Stocks->calcStock(
        $location['id'], '', '', '', '', '', '', '', '', $categories,
        $brands, $depart, '', '', '', '', '', $with_expired = false,
        $group_batch = true, $productcategoryid, $subcategoryid
    );

    $branchid = $Locations->getBranch($location['id'])['id'];
    $totalStockValue = 0;
    foreach ($tData['stocklist'] as $key => $s) {
        $product = $Products->get($s['productid']);

        $currentBranchPrice = $CurrentPrices->find(['branchid' => $branchid, 'productid' => $s['productid']])[0];
        $tData['stocklist'][$key]['costprice'] = $currentBranchPrice['costprice'];
        $tData['stocklist'][$key]['stock_value'] = $s['total'] * $currentBranchPrice['costprice'];

        $totalStockValue += $tData['stocklist'][$key]['stock_value'];

        $tData['stocklist'][$key]['suppliers'] = $Stocks->withSuppliers(array_column($s['batches'], 'batchId'));
//        debug( $tData['stocklist'][$key]);
    }

    $tData['totalStockValue'] = $totalStockValue;
    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive('name');
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive('name');

    $data['content'] = loadTemplate('stockreport_with_supplier.tpl.php', $tData);
}

if ($action == 'stock_report_admin_detailed') {
    if (!IS_ADMIN) redirect('authenticate', 'access_page');

    $stocklocation = $_GET['stocklocation'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productcategoryid = $_GET['productcategoryid'];
    $batchno = $_GET['batchno'];
    $expirebefore = $_GET['expirebefore'];
    $expireafter = $_GET['expireafter'];

    $tData['title'] = '';
    $tData['title'] .= !empty($batchno) ? 'Batch No: ' . $batchno . ' | ' : '';
    $tData['title'] .= !empty($expirebefore) ? 'Expire Before: ' . fDate($expirebefore) . ' | ' : '';
    $tData['title'] .= !empty($expireafter) ? 'Expire After: ' . fDate($expireafter) . ' | ' : '';

    if (empty($stocklocation)) $stocklocation = $Locations->defaultBranchLocation($_SESSION['member']['branchid'])['id'];


    $tData['location'] = $location = $Locations->get($stocklocation);


    $tData['stocklist'] = $Stocks->calcStock(
        $location['id'], '', '', '', '', '', '',
        '', '', $categories, $brands, $depart, '', '', $batchno,
        $expirebefore, $expireafter, $with_expired = true, $group_batch = true, $productcategoryid
    );

    $branchid = $Locations->getBranch($location['id'])['id'];
    foreach ($tData['stocklist'] as $key => $s) {
        $product = $Products->get($s['productid']);

        $currentBranchPrice = $CurrentPrices->find(['branchid' => $branchid, 'productid' => $s['productid']])[0];
        $tData['stocklist'][$key]['costprice'] = $currentBranchPrice['costprice'];
        $bulk = $BulkUnits->get($product['bulk_units']);

        $tData['stocklist'][$key]['bulkUnit'] = $bulk['name'];
        $tData['stocklist'][$key]['bulkRate'] = $bulk['rate'];

    }

    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive();

    $data['content'] = loadTemplate('stockreport_admin_detailed.tpl.php', $tData);
}

if ($action == 'proforma_projection_report') {
    Users::isAllowed();
//     debug($_GET);
    $stocklocation = $_GET['stocklocation'];
    $search = htmlspecialchars($_GET['search']);
    $fromdate = $_GET['fromdate'] ?: date('Y-m-d', strtotime('-1 month'));
    $todate = $_GET['todate'] ?: TODAY;
    $departmentid = $_GET['departmentid'];
    $brandid = $_GET['brandid'];
    $productcategoryid = $_GET['productcategoryid'];

    $title = [];
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

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

    if ($search) $title[] = "Search: " . $search;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($departmentid) $title[] = "Department: " . $Departments->get($departmentid)['name'];
    if ($brandid) $title[] = "Brand: " . $Models->get($brandid)['name'];
    if ($productcategoryid) $title[] = "Product Category: " . $ProductCategories->get($productcategoryid)['name'];

    $tData['title'] = implode(' | ', $title);

    if ($search || $departmentid || $brandid || $productcategoryid || $subcategoryid) {
        $products = $Products->getList('', $search, $departmentid, $brandid, '', $productcategoryid);

        foreach ($products as $index => $p) {
            $detail = ProformaDetails::$proformaDetailsClass->preparedClosed($stocklocation, $p['productid'], $fromdate, $todate)[0];
            $products[$index]['prepared'] = $detail['prepared_qty'];
            $products[$index]['closed'] = $detail['closed_qty'];
            $products[$index]['pending'] = $detail['pending_qty'];

            $stock = $Stocks->calcStock(
                $stocklocation, '', '', '', $p['productid'], '', '', '', '', '',
                '', '', '', '', '', '', '', false,
                true, '', '', true, false
            );
            $stock = array_values($stock)[0];
            $products[$index]['stock_qty'] = $stock['total'];

//            debug($products[$index]);
        }
//    debug($products);
    }


    $tData['products'] = $products;
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $tData['units'] = $Units->getAllActive();
    $tData['bulkunits'] = $BulkUnits->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Proforma Audit Reports";
    $data['content'] = loadTemplate('proforma_projection_report.tpl.php', $tData);
}

if ($action == 'location_stock_inout_report') {
    Users::isAllowed();

    $locationid = removeSpecialCharacters($_GET['locationid']);
    $productid = removeSpecialCharacters($_GET['productid']);
    $tData['brandid'] = $brandid = removeSpecialCharacters($_GET['brandid']);
    $tData['pcategoryid'] = $pcategoryid = removeSpecialCharacters($_GET['pcategoryid']);
    $tData['subcategoryid'] = $subcategoryid = removeSpecialCharacters($_GET['subcategoryid']);
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];

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
//    debug($locationid);
    $tData['location'] = $Locations->get($locationid);

    $title = [];
    $title[] = "Location: " . $tData['location']['name'];
    if ($productid) {
        $tData['product'] = $Products->get($productid);
        $title[] = "Product: " . $tData['product']['name'];
    }
    if ($brandid) $title[] = "Brand: " . Models::$staticClass->get($brandid)['name'];
    if ($pcategoryid) $title[] = "Category: " . ProductCategories::$class->get($pcategoryid)['name'];
    if ($subcategoryid) $title[] = "Sub-category: " . ProductSubCategories::$class->get($subcategoryid)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    if ($fromdate) {
        $current_stock = Stocks::$stockClass->calcStock($locationid, "", "",
            "", $productid, "", "",
            "", "", "", $brandid, "",
            "", "", "", "", "",
            true, true, $pcategoryid, $subcategoryid, false);

        $opening_stock_date = date('Y-m-d', strtotime($fromdate . ' -1 day'));
        $opening_stock = Stocks::$stockClass->calcStock($locationid, "", $opening_stock_date,
            "", $productid, "", "",
            "", "", "", $brandid, "",
            "", "", "", "", "",
            true, true, $pcategoryid, $subcategoryid, false);
//    debug($opening_stock);

        foreach ($current_stock as $i => $item) {
            $current_stock[$i]['opening_balance'] = $opening_stock[$i]['total'] ?: 0;
            $history = Stocks::$stockClass->productsHistory($locationid, $item['productid'], $fromdate, $todate);
//        if($i==3809)debug($history);
            $summary = [];
            foreach ($history as $h) {
                switch ($h['voucher']) {
                    case 'grn':
                        $summary['in']['purchase'] += $h['qty'];
                        break;
                    case 'adjustment':
                        $h['action'] == 'in'
                            ? $summary['in']['adj_in'] += $h['qty']
                            : $summary['out']['adj_out'] += $h['qty'];
                        break;
                    case 'transfer out':
                        $summary['out']['trans_out'] += $h['qty'];
                        break;
                    case 'transfer in':
                        $summary['in']['trans_in'] += $h['qty'];
                        break;
                    case 'return':
                        $summary['out']['grn_return'] += $h['qty'];
                        break;
                    case 'sale':
                        $summary['out']['sale'] += $h['qty'];
                        break;
                    case 'sale return':
                        $summary['in']['sale_return'] += $h['qty'];
                        break;
                    case 'manufacture raw material':
                        $summary['out']['man_raw'] += $h['qty'];
                        break;
                    case 'manufacture end product':
                        $summary['in']['man_end'] += $h['qty'];
                        break;
                }
            }
            $current_stock[$i]['history'] = $summary;
        }
    }
//    debug($current_stock);
//    debug($tData);
    $tData['products'] = $current_stock;
    $tData['brands'] = $Models->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $data['content'] = loadTemplate('location_stock_inout_report.tpl.php', $tData);
}

if ($action == 'ajax_getBarcodeStockId') {
    $barcode = $_GET['barcode'];
    $locationid = $_GET['locationid'];

    $obj->status = 'success';

    $product = $Products->byBarcode($barcode);
    if (!$product) {
        $obj->status = "error";
        $obj->msg = "Product not found";
    } else {
        $stockid = '';
        $product = $Products->getList($product['id'])[0];
        if (!$product['non_stock']) {
            $stockid = $Stocks->find(['productid' => $product['id'], 'locid' => $locationid])[0]['id'];
        }
        if (!$stockid && !$product['non_stock']) {
            $obj->status = "error";
            $obj->msg = "Stock not found";
        } else {
            $obj->data = [
                'stockid' => $stockid,
                'productid' => $product['productid'],
                'productname' => $product['name'],
                'vat_rate' => $product['vatPercent'],
                'description' => $product['description'],
                'unitname' => $product['description'],
                'non_stock' => $product['non_stock'],
            ];
        }
    }
//    debug($obj);
    $data['content'] = $obj;

}

if ($action == 'ajax_getLocationStocks') {
//    debug($_GET);
    $locationid = $_GET['locationid'];
    $productcategoryid = $_GET['categoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $purpose = $_GET['quicksale'];
    $non_stock = $_GET['non_stock'];


    $search_barcode = $_GET['barcode'];
    $onlybarcode = $_GET['onlybarcode'];
    $productname = $_GET['search']['term'];
    $search_barcode = $search_barcode ?: $productname; //if searching barcode directly
    define('EXCEPT_PROFORMA', $_GET['except_proforma']); //proforma to be excluded from stock holding

    if ($search_barcode) {
        $barcode_office = $Products->find(array('barcode_office' => $search_barcode));
        $barcode_manufacture = $Products->find(array('barcode_manufacture' => $search_barcode));

        if (!empty($barcode_manufacture)) {
            $manufacture_barcode = $barcode_manufacture['0']['barcode_manufacture'];
        } elseif (!empty($barcode_office)) {
            $office_barcode = $barcode_office['0']['barcode_office'];
        }
        if ($manufacture_barcode || $office_barcode) $productname = '';
    }

    // echo "<pre>";
    // print_r($_GET);
    // print_r($barcode_office);
    // die();

    $currentStock = $Stocks->calcStock(
        $locationid, $stockid = "", "", $productname, $prodid = 0,
        $office_barcode, $manufacture_barcode, $purpose, "", "",
        "", "", "", "", "", "", "",
        $with_expired = false, $group_batch = true, $productcategoryid, $subcategoryid,
        true, true);


    $response = [];
//    debug($currentStock);
    if ($non_stock == 'yes') {
        $non_stock_products = $Products->getList('', $productname, '', '', '',
            '', '', '', '', 'yes', $productname ? false : true);
//        debug($non_stock_products);
    }
    if ($currentStock || $non_stock_products) {
        $currentStock = array_values($currentStock);

        foreach ((array)$currentStock as $ic) {
            $obj = null;
            if ($ic['total'] != 0 && isNegative($ic['total']) != 1) {
                $obj->text = $purpose == 1 || $productname ? $ic['name'] . ' (' . $ic['total'] . ')' : $ic['name'];
                $obj->qty = $ic['total'];
                $obj->id = $ic['id'];
                $obj->productid = $ic['productid'];
                $obj->barcode = $ic['barcode_office'] ?: $ic['barcode_manufacture'];
                $obj->stock_qty = $ic['total'];
                $obj->description = $ic['productdescription'];
                $obj->non_stock = 0;
                $response['results'][] = $obj;

            }
        }

        foreach ($non_stock_products as $item) {
            $obj = null;
            $obj->text = $item['name'];
            $obj->qty = 1;
            $obj->id = '';
            $obj->productid = $item['productid'];
            $obj->description = $item['description'];
            $obj->unitname = $item['unitname'];
            $obj->vat_rate = $item['vatPercent'];
            $obj->description = $item['description'];
            $obj->barcode = $item['barcode_office'] ?: $item['barcode_manufacture'];
            $obj->non_stock = 1;
            $response['results'][] = $obj;
        }
    } else {
        $obj = null;
        $obj->test = 'No results';
        $obj->id = 0;
        $response['results'][] = $obj;

    }
//    debug($response);
    $data['content'] = $response;
}


if ($action == 'ajax_locationStockSearch') {
    $locationid = $_GET['locationid'];
    $productname = $_GET['term'];


    define('EXCEPT_PROFORMA', $_GET['except_proforma']);

    $current_stock = $Stocks->calcStock(
        $locationid, "", "",
        $productname, "", "",
        "", "", "",
        "", "", "", "", "", "",
        "", "", false, true,
        '', '', true, true);
    $current_stock = array_filter($current_stock, function ($s) {
        return $s['total'] > 0;
    });
    $current_stock = array_values($current_stock);

    //appending non-stock item
    $non_stock = $Products->getList('', $productname, '', '', '', '', '',
        '', '', 'yes', '', '', 'active');
    $current_stock = array_merge($current_stock, $non_stock);
//    debug($current_stock);
    $data['content'] = $current_stock;
}

if ($action == 'ajax_getLPODetails') {

    $obj->status = 'success';

    $lpoid = $_GET['lponumber'];
    if ($lpo = $LPO->get($lpoid)) {
        $lpo['details'] = $LPODetails->find(['lpoid' => $lpoid]);
        foreach ($lpo['details'] as $index => $detail) {
            $product = $Products->get($detail['prodid']);
            $lpo['details'][$index]['productname'] = $product['name'];
            $excamount = round($detail['rate'] * $detail['qty'], 2);
            $vatamount = round($excamount * ($detail['vat_rate'] / 100), 2);

            $lpo['details'][$index]['excamount'] = $excamount;
            $lpo['details'][$index]['vatamount'] = $vatamount;
            $lpo['details'][$index]['incamount'] = ($excamount + $vatamount);
        }
        $obj->data = $lpo;
    } else {
        $obj->status = 'error';
        $obj->msg = 'LPO not found!';
    }

    $data['content'] = $obj;
}


if ($action == 'ajax_getAdjustmentStock') {
    $locationId = $_GET['locationId'];
    $productId = $_GET['productId'];
    $with_cost = isset($_GET['with_cost']);

    $stockId = $Stocks->find([
        'productid' => $productId,
        'locid' => $locationId
    ])[0]['id'];


    if ($stockId) {
        $detail = $Stocks->calcStock(
            $locationId,
            $stockId,
            "", "", $productId, "", "",
            "", "", "", "", "", "", "",
            "", "", "", $with_expired = true, $group_batch = true,
            "", "", $with_stock = false);
        $stock = array_values($detail)[0];

        if ($stock['track_expire_date']) {//filter out expired and out of stock
            $stock['batches'] = array_filter($stock['batches'], function ($batch) {
                return !($batch['expire_remain_days'] < 1 && $batch['total'] <= 0);
            });
            $stock['total'] = array_sum(array_column($stock['batches'], 'total'));
        }

        if ($with_cost) {
            $branchid = Locations::$locationClass->get($locationId)['branchid'];
            $currentPrice = CurrentPrices::$currentPricesClass->find(['branchid' => $branchid, 'productid' => $productId])[0];
        }
        $stock['costprice'] = $currentPrice['costprice'] ?: 0;
        $product = Stocks::$stockClass->getProduct($stockId);
        $stock['trackserialno'] = $product['trackserialno'];
//            debug($stock);

        $response[] = $stock;
//        debug($response);
        $data['content'] = $response;
    } else {
        $response = [];
    }
    $data['content'] = $response;
}

if ($action == 'ajax_getTransferDetails') {
    $transferno = $_GET['transferno'];
    if ($transfer = $StockTransfers->get($transferno)) {
        $obj->found = 'yes';
        $obj->data = StockTransfers::transferInfo($transferno);
    } else {
        $obj->found = 'no';
        $obj->data = [];
    }

    $data['content'] = $obj;
}

if ($action == 'ajax_getRequisitionDetails') {
    $reqno = $_GET['reqno'];
    $obj->status = 'success';
    if ($requisition = $TransferRequisitions->get($reqno)) {
        $obj->data = $TransferRequisitions->withDetails($reqno)[0];
    } else {
        $obj->status = 'error';
        $obj->msg = 'Transfer requisition not found';
    }
    $data['content'] = $obj;
}
