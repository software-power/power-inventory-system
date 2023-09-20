<?
if ($action == 'grn_list') {
    Users::isAllowed();

    $branchid = $_GET['search']['branchid'];
    $locationid = $_GET['search']['locationid'];
    $lpoid = removeSpecialCharacters($_GET['search']['lpo']);
    $grnid = removeSpecialCharacters($_GET['search']['grn']);
    $createdby = $_GET['search']['createdby'];
    $currencyid = $_GET['search']['currencyid'];
    $fromdate = $_GET['search']['from'] ?: date('Y-m-d', strtotime('-1 months'));
    $todate = $_GET['search']['to'];
    $supplierid = $_GET['search']['supplierid'];
    $paymenttype = $_GET['search']['paymenttype'];

    if (Users::cannot(OtherRights::approve_other_grn)) $createdby = $_SESSION['member']['id'];

    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($createdby) {
        $tData['creator'] = $creator = $Users->get($createdby);
        $title[] = "Issued by: " . $creator['name'];
    }
    if ($lpoid) $title[] = "LPO No: " . $lpoid;
    if ($grnid) $title[] = "GRN No: " . $grnid;

    if ($lpoid || $grnid) {
        $fromdate = $todate = '';
    }
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if ($paymenttype) $title[] = "Payment Type: " . strtoupper($paymenttype);
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);

    $tData['grnlist'] = $GRN->getList($lpoid, $grnid, $createdby, $fromdate, $todate, $supplierid, $paymenttype, $currencyid, $locationid, $branchid);
//debug($tData['grnlist']);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['currencies'] = $Currencies->getAllActive();
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('grn_list.tpl.php', $tData);
}

if ($action == 'save_grn_new') {
    Users::can(OtherRights::add_grn, true);

//    debug($_POST);
    $grn = $_POST['grn'];
    $productIds = $_POST['productid'];
    $rate = $_POST['rate'];
    $qty = $_POST['qty'];
    $billable_qty = $_POST['billable_qty'];
    $quick_sale_price = $_POST['quick_sale_price'];
    $vat_percentage = $_POST['vat_percentage'];
    $bulk = $_POST['bulk'];
    $bulk_rate = $_POST['bulk_rate'];
    $batches = $_POST['batch'];
//    debug($batches);
    //remove commas
    $grn['vat_registered'] = $grn['vat_registered'] ?: 0;
    $grn['supplier_payment'] = $grn['supplier_payment'] ?: 0;
    $grn['total_amount'] = removeComma($grn['total_amount']);
    $grn['full_amount'] = removeComma($grn['full_amount']);
    $grn['grand_vatamount'] = removeComma($grn['grand_vatamount']);

    validate($grn);
    validate($productIds);

    global $db_connection;
    try {
        mysqli_begin_transaction($db_connection);
        if (!$grn['id']) {//new
            if ($GRN->find(['token' => $grn['token']])) throw new Exception("System found this form is already submitted, operation canceled to avoid duplicate GRNS!");
            if (!CS_GRN_APPROVAL) {
                $grn['approvedby'] = $_SESSION['member']['id'];
                $grn['approval_date'] = TIMESTAMP;
                $grn['auto_approve'] = 1;
            }

            $grn['createdby'] = $_SESSION['member']['id'];

            $grn['transfer_tally'] = CS_TALLY_TRANSFER;  //tally transfer
            $GRN->insert($grn);
            $grnid = $GRN->lastId();
        } else { //editing
            $grnid = $grn['id'];
            if ($GRN->get($grnid)['approvedby']) throw new Exception('GRN has already been approved, cant be edited!');
            $grnInfo = $GRN->getGrnWithReturnQty($grnid);
//        debug($grnInfo);
            $GrnLogs->insert([
                'grnid' => $grnid,
                'payload' => base64_encode(json_encode($grnInfo)),
                'createdby' => $_SESSION['member']['id'],
            ]);

            //clear old info
            $details = $GRNDetails->find(['grnid' => $grnid]);
            foreach ($details as $detail) {
                $Batches->deleteWhere(['gdi' => $detail['id']]);
            }
            $GRNDetails->deleteWhere(['grnid' => $grnid]);

            $grn['modifiedby'] = $_SESSION['member']['id'];
            $grn['dom'] = TIMESTAMP;
            $GRN->update($grnid, $grn);
        }

        $branch = $Locations->getBranch($grn['locid']);
        foreach ($productIds as $index => $productId) {

            $stock = $Stocks->find(['productid' => $productId, 'locid' => $grn['locid']]);
            if ($stock) {//if there
                $stockid = $stock[0]['id'];
            } else {//if not there -> create the stock
                $Stocks->insert(['productid' => $productId, 'locid' => $grn['locid'], 'createdby' => $_SESSION['member']['id']]);
                $stockid = $Stocks->lastId();
            }

            $productQty = in_array($productId, $bulk)
                ? $qty[$index] * $bulk_rate[$index]
                : $qty[$index];
            $productBillableQty = in_array($productId, $bulk)
                ? $billable_qty[$index] * $bulk_rate[$index]
                : $billable_qty[$index];

            $GRNDetails->insert([
                'grnid' => $grnid,
                'stockid' => $stockid,
                'rate' => $rate[$index],
                'quick_sale_price' => $quick_sale_price[$index],
                'qty' => $productQty,
                'billable_qty' => $productBillableQty,
                'vat_percentage' => $vat_percentage[$index],
                'createdby' => $_SESSION['member']['id'],
            ]);
            $gid = $GRNDetails->lastId();

            if (!empty($batches[$productId])) {
                foreach ($batches[$productId]['batch_no'] as $i => $batch_no) {
                    $batch_no = trim($batch_no);
                    if (empty($batch_no)) $batch_no = Batches::generateBatchNo();
                    $batchQty = (in_array($productId, $bulk))
                        ? $batches[$productId]['qty'][$i] * $bulk_rate[$index]
                        : $batches[$productId]['qty'][$i];
                    $Batches->insert([
                        'batch_no' => $batch_no,
                        'qty' => $batchQty,
                        'expire_date' => $batches[$productId]['expire_date'][$i],
                        'gdi' => $gid,
                    ]);
                }
            } else {
                $batch_no = Batches::generateBatchNo(false);
                $Batches->insert([
                    'batch_no' => $batch_no,
                    'qty' => $productQty,
                    'gdi' => $gid,
                ]);
            }

            //NB product cost updating moved to grn approval
            if (!CS_GRN_APPROVAL) {
                $baseRate = $rate[$index] * $grn['currency_amount'];
//                $newProductCost = addTAX($baseRate, $vat_percentage[$index]);
                $newProductCost = round($baseRate, 2);

                CurrentPrices::updatePrice($branch['id'], $productId, $newProductCost,
                    $quick_sale_price[$index], $gid, "GRN {$grnid} entry", true);
            }
        }
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Goods Received Note (GRN) " . ($grn['id'] ? 'updated' : 'created') . " successfully";


        if (!CS_GRN_APPROVAL && $grn['transfer_tally'] && CS_TALLY_DIRECT) {
            //todo transfer direct tally
        }


        redirect('grns', 'grn_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = "Error, " . $e->getMessage();
        $_SESSION['delay'] = 5000;
        redirect('grns', 'grn_list');
    }
}

if ($action == 'grn_edit') {
    Users::can(OtherRights::edit_grn, true);
    $grnid = $_GET['grnid'];
    $grn = $GRN->get($grnid);
    if (empty($grn)) {
        $_SESSION['error'] = "GRN not found";
        redirectBack();
    }
//    $days = date_create($grn['doc'])->diff(new DateTime())->days;
    $grnPayment = $GRN->withPaymentAmount('', $grnid)[0];

//    if ($grn['paymenttype'] == PAYMENT_TYPE_CASH) {//cash grn
//        $_SESSION['error'] = "Cash GRN cant be edited!";
//        redirect('grns', 'grn_list');
//    } else { //credit grn
//        if ($grnPayment['outstanding_amount'] <= 0) {
//            $_SESSION['error'] = "GRN payment completed cant be edited!";
//            redirectBack();
//        }
//    }


    $grnInfo = $GRN::currentGrnState($grnid);
    $grnInfo['currency'] = $CurrenciesRates->getCurrency_rates($grnInfo['currency_rateid']);
    $tData['grn'] = $grnInfo;
//    debug($grnInfo);
    $tData['grnPayment'] = $grnPayment;
    $tData['suppliers'] = $Suppliers->getAll();
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['vatCategories'] = $Categories->getAll();
//    debug($tData['currencies']);
//    debug($grnPayment);


    $data['content'] = loadTemplate('grn_advance_edit.tpl.php', $tData);
}

if ($action == 'grn_full_edit') { //for non approved grn


    $grnid = $_GET['grnid'];
    $lpoid = $_GET['lpoid'];

    if (!$grnid && !$lpoid && CS_GRN_REQUIRE_LPO) {
        $_SESSION['error'] = "All GRN require LPO first";
        $_SESSION['delay'] = 5000;
        redirect('grns', 'lpo_list');
    }

    if ($grnid) {
        Users::can(OtherRights::edit_grn, true);
        $grn = $GRN->get($grnid);
        if (empty($grn)) {
            $_SESSION['error'] = "GRN not found";
            redirectBack();
        }

        if ($grn['approvedby']) {
            $_SESSION['error'] = "GRN has been approved cant be edited";
            redirectBack();
        }


        $currency = $CurrenciesRates->getCurrency_rates($grn['currency_rateid']);
        $grn['currencyname'] = $currency['currencyname'];
        $grn['currency_description'] = $currency['description'];
        $grn['locationname'] = $Locations->get($grn['locid'])['name'];
        $grn['suppliername'] = $Suppliers->get($grn['supplierid'])['name'];
        $grn['details'] = $GRNDetails->find(['grnid' => $grnid]);
        foreach ($grn['details'] as $index => $detail) {
            $stock = $Stocks->get($detail['stockid']);
            $product = $Products->get($stock['productid']);
            if ($product['non_stock']) {
                unset($grn['details'][$index]);
                continue;
            }
            $grn['details'][$index]['productid'] = $product['id'];
            $grn['details'][$index]['productname'] = $product['name'];
            $grn['details'][$index]['description'] = $product['description'];
            $grn['details'][$index]['track_expire_date'] = $product['track_expire_date'];
            $category = $Categories->get($product['categoryid']);
            $unit = $Units->get($product['unit']);
            $bulk_unit = $BulkUnits->get($product['bulk_units']);
            $grn['details'][$index]['unitname'] = $unit['name'];
            $grn['details'][$index]['bulk_rate'] = $bulk_unit['rate'];
            $grn['details'][$index]['base_rate'] = $detail['rate'] * $grn['currency_amount'];
            $grn['details'][$index]['vat_percentage'] = $category['vat_percent'];

            $highest_percentage = $Hierarchics->highestLevel()['percentage'];
            $product['baseprice'] = $product['baseprice'] > $highest_percentage ? $product['baseprice'] : $highest_percentage;

            $grn['details'][$index]['base_percentage'] = $product['baseprice'];
            if ($product['track_expire_date']) $grn['details'][$index]['batches'] = $Batches->find(['gdi' => $detail['id']]);
        }
        $tData['grn'] = $grn;
    } else {
        Users::can(OtherRights::add_grn, true);
    }

    if ($lpoid) {
        $lpo = $LPO->get($lpoid);
        if (!$lpo) {
            $_SESSION['error'] = "LPO not found!";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }
        if (!$lpo['approvedby']) {
            $_SESSION['error'] = "LPO not approved!";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }

        $grn = $GRN->find(['lpoid' => $_GET['lpoid']]);
        if ($grn) {
            $_SESSION['error'] = "LPO already processed!";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }
        $currency = $CurrenciesRates->getCurrency_rates($lpo['currency_rateid']);
        $lpo['currencyname'] = $currency['currencyname'];
        $lpo['currency_description'] = $currency['description'];
        $lpo['locationname'] = $Locations->get($lpo['locationid'])['name'];
        $lpo['suppliername'] = $Suppliers->get($lpo['supplierid'])['name'];
        $branchid = $Locations->getBranch($lpo['locationid'])['id'];
        $lpo['details'] = $LPODetails->find(['lpoid' => $lpoid]);
        foreach ($lpo['details'] as $index => $detail) {

            $product = $Products->get($detail['prodid']);
            $quick_sale_price = $CurrentPrices->quickPriceList($branchid, $product['id'])[0]['inc_quicksale_price'];
            $lpo['details'][$index]['productid'] = $product['id'];
            $lpo['details'][$index]['productname'] = $product['name'];
            $lpo['details'][$index]['description'] = $product['description'];
            $lpo['details'][$index]['track_expire_date'] = $product['track_expire_date'];
            $category = $Categories->get($product['categoryid']);
            $unit = $Units->get($product['unit']);
            $bulk_unit = $BulkUnits->get($product['bulk_units']);
            $lpo['details'][$index]['unitname'] = $unit['name'];
            $lpo['details'][$index]['bulk_rate'] = $bulk_unit['unit'];
            $lpo['details'][$index]['base_rate'] = $detail['rate'] * $lpo['currency_amount'];

            $highest_percentage = $Hierarchics->highestLevel()['percentage'];
            $product['baseprice'] = $product['baseprice'] > $highest_percentage ? $product['baseprice'] : $highest_percentage;

            $lpo['details'][$index]['base_percentage'] = $product['baseprice'];
            $lpo['details'][$index]['quick_sale_price'] = $quick_sale_price;
        }
//        debug($lpo);
        $tData['lpo'] = $lpo;
    }

    if (CS_DEFAULT_GRNLOC) {
        $tData['defaultLocation'] = $Locations->get(CS_DEFAULT_GRNLOC);
    } else {
        $tData['defaultLocation'] = $Locations->get($_SESSION['member']['id']);
    }
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
//    debug($tData['basecurrency']);
    $data['content'] = loadTemplate('grn_full_edit.tpl.php', $tData);
}

if ($action == 'add_serialno') {
    Users::isAllowed();
//    debug('add serial');
    if (empty($_GET['grnid'])) {
        $data['content'] = loadTemplate('add_serialno.tpl.php', []);
    } else {
        $grnid = $_GET['grnid'];
        if (empty($GRN->get($grnid))) {
            $_SESSION['error'] = "GRN not found!";
            redirectBack();
            die();
        }
        $grnInfo = $GRN::currentGrnState($grnid, '', 'yes', '', false);
        $tData['grnInfo'] = $grnInfo;
        $data['content'] = loadTemplate('add_serialno.tpl.php', $tData);
    }
}

if ($action == 'grn_save_serialno') {
    debug($_POST);

    $gdi = $_POST['gdi'];
    $current_stock_id = $_POST['current_stock_id'];
    $ids = $_POST['id'];
    $numbers = $_POST['number'];
    $state = $_POST['state'];

    validate($numbers);

    //find new numbers
    $newState = array_filter($state, function ($st) {
        return $st == 'new';
    });
    $newNos = array_filter($numbers, function ($key) use ($newState) {
        return in_array($key, array_keys($newState));
    }, ARRAY_FILTER_USE_KEY);

//    debug($newNos);

    //check existing
    $existing = $SerialNos->findMany(['number' => $newNos]);
    if (!empty($existing)) {
        $_SESSION['error'] = "Serial No (" . implode(', ', array_column($existing, 'number')) . ") already exists!";
        $_SESSION['delay'] = 20000;
        redirectBack();
        die();
    }

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        foreach ($numbers as $index => $sno) {
            if ($state[$index] == 'new') { //new numbers
                $SerialNos->insert([
                    'number' => $sno,
                    'initial_stockid' => $current_stock_id,
                    'current_stock_id' => $current_stock_id,
                    'gdi' => $gdi,
                    'createdby' => $_SESSION['member']['id'],
                ]);
            } elseif ($state[$index] == 'delete') {
                $serialno = $SerialNos->get($ids[$index]);
                if (!empty($serialno['sdi']) && $serialno != 0) throw new Exception("Serial no (" . $serialno['number'] . ") already been used cant delete!");
                $SerialNos->real_delete($ids[$index]);
            } else {
                $SerialNos->update($ids[$index], [
                    'number' => $sno
                ]);
            }
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Serial no saved successfully";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage() . " Serial nos not saved";
    }
    redirectBack();
}

if ($action == 'view_grn') {
    $can_view = Users::can(OtherRights::approve_other_grn) || Users::can(OtherRights::approve_grn) || Users::can(OtherRights::cancel_grn)
        || Users::can(OtherRights::add_grn) || Users::can(OtherRights::edit_grn);
    if (!$can_view) {
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('view grn')]);
    }
    $grnid = $_GET['grn'];
    if (!$grn = $GRN->get($grnid)) debug('GRN not found!');
    $grn = $GRN->getList('', $grnid)[0];
    $grn['details'] = $GRNDetails->getList($grnid);

    foreach ($grn['details'] as $index => $detail) {
        $grn['details'][$index]['batches'] = $Batches->find(['gdi' => $detail['id']]);
    }
//    debug($grn);
    $tData['grn'] = $grn;
    $data['content'] = loadTemplate('view_grn.tpl.php', $tData);
}

if ($action == 'print_grn') {
    $data['layout'] = 'layout_blank.tpl.php';
    $grnid = $_GET['grn'];
    $includestock = isset($_GET['includestock']);
    if (!$grn = $GRN->get($grnid)) debug('GRN not found!');
    $grn = $GRN->getList('', $grnid)[0];
    $grn['details'] = $GRNDetails->getList($grnid);
    foreach ($grn['details'] as $index => $detail) {
        if ($includestock) {
            $currentStock = $Stocks->calcStock(
                $grn['locationid'], $detail['stockid'], "", "", "", "",
                "", "", "", "", "", "", "",
                "", "", "", "", false, true,
                "", "", true
            );
            $currentStock = array_values($currentStock)[0];
            $grn['details'][$index]['currentstock'] = $currentStock['total']?:0;
        }
        $grn['details'][$index]['batches'] = $Batches->find(['gdi' => $detail['id']]);
    }
//    debug($grn);
    $data['grn'] = $grn;
    $data['layout'] = $includestock?'grn_print_with_stock.tpl.php':'grn_print.tpl.php';
}

if ($action == 'cancel_grn') {
    Users::can(OtherRights::cancel_grn, true);
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {


        $grnid = $_POST['grnid'];

        //if no transfer or sale or return
        $grn = $GRN->get($grnid);
        if (empty($grn)) throw new Exception("GRN not found");

        $grnInfo = $GRN->getGrnWithReturnQty($grnid, false);
        $grn_batches = array_column($grnInfo, 'batchId');
        //check if there is sale
        $sold_batches = $SalesBatches->findMany([
            'batch_id' => $grn_batches,
        ]);
        if (!empty($sold_batches)) throw new Exception("System found there are batches that have already been sold, Grn cannot be canceled!");

        //check if there is transfer
        $transferred_batches = $StockTransferBatches->findMany([
            'batch_id' => $grn_batches,
        ]);
        if (!empty($transfered_batches)) throw new Exception("System found there are batches that have already been transferred, Grn cannot be canceled!");
        //check if there is return
        $returned_batches = $GrnReturnBatches->findMany([
            'batch_id' => $grn_batches,
        ]);
        if (!empty($returned_batches)) throw new Exception("System found there are batches that have already been returned, Grn cannot be canceled!");

        //check if there is adjustment
        $adjusted_batches = $StockAdjustmentBatches->findMany([
            'batch_id' => $grn_batches,
        ]);
        if (!empty($adjusted_batches)) throw new Exception("System found there are batches that have already been adjusted, Grn cannot be canceled!");

        //check if used in manufacture
        $used_in_manufacture = StockManufactureUsedBatches::$staticClass->findMany([
            'batchid' => $grn_batches,
        ]);
        if (!empty($adjusted_batches)) throw new Exception("System found there are batches that have already been adjusted, Grn cannot be canceled!");

        $supplierPaymentDetails = $SupplierPaymentDetails->find(['grnid' => $grnid]);

        if ($grn['approvedby'] && $grn['paymenttype'] == PAYMENT_TYPE_CASH && CS_SUPPLIER_PAYMENT) {
            $spid = array_unique(array_column($supplierPaymentDetails, 'spid'));
            if (count($spid) > 1) throw new Exception("Grn exists in multiple supplier payments cant be cancelled!");

            //delete previous payment
            $SupplierPayments->deleteWhere(['id' => $supplierPaymentDetails[0]['spid']]);
            $SupplierPaymentDetails->deleteWhere(['id' => $supplierPaymentDetails[0]['id']]);

            //create supplier advance payment
            $branch = $Locations->getBranch($grn['locid']);
            $SupplierAdvancePayments->insert([
                'supplierid' => $grn['supplierid'],
                'branchid' => $branch['id'],
                'pmethod_id' => $Paymentmethods->find(['name' => PaymentMethods::CASH])[0]['id'],
                'currency_rateid' => $grn['currency_rateid'],
                'currency_amount' => $grn['currency_amount'],
                'amount' => $grn['full_amount'],
                'createdby' => $_SESSION['member']['id'],
                'remark' => "Canceled grn no: {$grnid}",
            ]);
        }

        if ($grn['paymenttype'] == PAYMENT_TYPE_CREDIT && $supplierPaymentDetails) throw new Exception("Grn has supplier payments cant be cancelled");

        GRN::cancelGrn($grnid);

        mysqli_commit($db_connection);

        $_SESSION['message'] = "GRN no $grnid canceled successfully";
        redirect('grns', 'grn_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['delay'] = 5000;
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }

}

if ($action == 'canceled_grn_list') {
    Users::isAllowed();

    $locationid = $_POST['search']['locationid'];
    $supplierid = $_POST['search']['supplierid'];
    $branchid = $_POST['search']['branchid'];
    $fromdate = $_POST['search']['fromdate'];
    $todate = $_POST['search']['todate'];
    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if (empty($fromdate)) $fromdate = date('Y-m-d', strtotime('-1 months'));
    if (empty($todate)) $todate = date('Y-m-d');
    $title[] = "From: " . fDate($fromdate);
    $tData['title'] = implode(' | ', $title);

    $canceled = $GrnCanceled->getList($locationid, $supplierid, $fromdate, $todate, $branchid);
    $canceled = array_map(function ($item) {
        $item['decoded'] = json_decode(base64_decode($item['payload']), true);
        return $item;
    }, $canceled);

//    debug($canceled);


    $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->get($_SESSION['member']['branchid']);
    $tData['location'] = IS_ADMIN ? [] : $Locations->get($_SESSION['member']['locationid']);
    $tData['suppliers'] = $Suppliers->getAll();
    $tData['canceled'] = $canceled;
    $data['content'] = loadTemplate("grn_canceled_list.tpl.php", $tData);
}

if ($action == 'approve_grn') {
    if (Users::cannot(OtherRights::approve_other_grn) && Users::cannot(OtherRights::approve_grn))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve GRN')]);

    $grnid = $_POST['grnid'];
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    $_SESSION['delay'] = 2000;
    try {
        if (!$grn = $GRN->get($grnid)) throw new Exception("GRN not found!");
        if ($grn['approvedby']) throw new Exception("GRN has already been approved!");
        if (!$branch = $Locations->getBranch($grn['locid'])) throw new Exception("Location branch not found!");

        $details = $GRNDetails->find(['grnid' => $grnid]);
        foreach ($details as $index => $detail) {
            $stock = $Stocks->get($detail['stockid']);
            $product = $Products->get($stock['productid']);

            //todo product cost must come from average product cost location/branch wise
            $baseRate = $detail['rate'] * $grn['currency_amount'];
//            $newProductCost = addTAX($baseRate, $detail['vat_percentage']);
            $newProductCost = round($baseRate, 2);

            CurrentPrices::updatePrice($branch['id'], $product['id'], $newProductCost,
                $detail['quick_sale_price'], $detail['id'], "GRN {$grn['id']} entry", true);
        }
        $GRN->update($grnid, ['approvedby' => $_SESSION['member']['id'], 'approval_date' => TIMESTAMP]);

        //for cash payment save supplier payment
        if ($grn['paymenttype'] == PAYMENT_TYPE_CASH && $grn['supplier_payment']) {
            $SupplierPayments->insert([
                'supplierid' => $grn['supplierid'],
                'branch_id' => $branch['id'],
                'pmethod_id' => $Paymentmethods->find(['name' => PaymentMethods::CASH])[0]['id'],
                'currency_rateid' => $grn['currency_rateid'],
                'currency_amount' => $grn['currency_amount'],
                'total_amount' => $grn['full_amount'],
                'createdby' => $_SESSION['member']['id'],
            ]);
            $spid = $SupplierPayments->lastId();

            $SupplierPaymentDetails->insert([
                'spid' => $spid,
                'grnid' => $grnid,
                'amount' => $grn['full_amount'],
                'createdby' => $_SESSION['member']['id'],
            ]);
        }

        $_SESSION['message'] = "GRN Approved";

        mysqli_commit($db_connection);

        if ($grn['transfer_tally'] && CS_TALLY_DIRECT) {
            $ping = pingTally();
            if ($ping['status'] == 'error') {
                $_SESSION['error'] = $ping['msg'];
            } else {
                $result = GRN::tallyPost($grn['id']);
                if ($result['status'] == 'success') $_SESSION['message'] .= "\n" . $result['msg'];
                if ($result['status'] == 'error') $_SESSION['error'] .= "\n" . $result['msg'];
            }
        }

        redirect('grns', 'grn_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirect('grns', 'grn_list');
    }
}

if ($action == 'tally_post') {
    $grnno = $_GET['grnno'];

    $ping = pingTally();
    if ($ping['status'] == 'error') {
        $_SESSION['error'] = $ping['msg'];
    } else {
        $result = GRN::tallyPost($grnno);
        if ($result['status'] == 'success') $_SESSION['message'] = $result['msg'];
        if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}


if ($action == 'grn_return') {
    Users::isAllowed();

//    debug($_POST);
    $grnid = $_POST['grnid'];
    $gdi = $_POST['gdi'];

    $grn = $GRN->get($grnid);
    if (empty($grn)) {
        $_SESSION['error'] = "GRN not found!";
        $_SESSION['delay'] = 4000;
        redirect('grns', 'grn_list');
        die();
    }

    if (!$grn['approvedby']) {
        $_SESSION['error'] = "GRN has not been approved!";
        $_SESSION['delay'] = 4000;
        redirect('grns', 'grn_list');
        die();
    }

    if (empty($gdi)) {
        $_SESSION['error'] = "Choose at least one product";
        $_SESSION['delay'] = 4000;
        redirect('grns', 'grn_list');
        die();
    }

    $det = $GRN->getGrnWithReturnQty($grnid);
    $grnInfo = array_values($det)[0];

    //filter selected
    $grnInfo['stock'] = array_filter($grnInfo['stock'], function ($item) use ($gdi) {
        return in_array($item['gdi'], $gdi);
    });

//    debug($grnInfo);
    //find remaining stock qty
    foreach ($grnInfo['stock'] as $index => $stock) {
        foreach ($stock['batches'] as $bkey => $batch) {
            $batchStock = $Stocks->calcStock(
                $grnInfo['st_locid'],
                $stock['stockid'],
                "", "", "",
                "", "", "",
                "", "", "", "",
                "", $batch['batchId'], "",
                "", "", true, false,
                "", "", false
            );
            $grnInfo['stock'][$index]['batches'][$bkey]['current_stock'] = $batchStock[0]['total'];
        }
        $grnInfo['stock'][$index]['current_stock_qty'] = array_sum(array_column($grnInfo['stock'][$index]['batches'], 'current_stock'));
    }
//    debug($grnInfo);
    $tData['grn'] = $grnInfo;
    $data['content'] = loadTemplate('grn_return_edit.tpl.php', $tData);
}

if ($action == 'grn_return_save') {
//    debug($_POST);
    $grn = $_POST['grn'];
    $stocks = $_POST['stockid'];
    $quantity = $_POST['quantity'];
    $gdi = $_POST['gdi'];
    $batch = $_POST['batch'];
    $serialno = $_POST['serialno'];

    validate($grn);
    validate($stocks);

    $grn['createdby'] = $_SESSION['member']['id'];
    $GrnReturns->insert($grn);
    $lastReturnId = $GrnReturns->lastId();

    foreach ($stocks as $index => $stockid) {

        $GrnReturnDetails->insert([
            'returnid' => $lastReturnId,
            'stockid' => $stockid,
            'quantity' => $quantity[$index],
            'createdby' => $_SESSION['member']['id'],
        ]);
        $grdi = $GrnReturnDetails->lastId();

        //return batch
        if (!empty($batch[$stockid])) {
            foreach ($batch[$stockid]['batchId'] as $i => $batch_id) {
                $GrnReturnBatches->insert([
                    'grdi' => $grdi,
                    'batch_id' => $batch_id,
                    'qty' => $batch[$stockid]['qty_out'][$i],
                ]);
            }
        } else { //product with no expiry has only one batch
            //finding batch batch
            $batches = $Batches->find([
                'gdi' => $gdi[$index]
            ]);
            $GrnReturnBatches->insert([
                'grdi' => $grdi,
                'batch_id' => $batches[0]['id'],
                'qty' => $quantity[$index],
            ]);
        }

        //return serialno
        if ($serialno[$stockid]) { //if tracking serialno
            foreach ($serialno[$stockid]['serial_number'] as $si => $number) {
                $sno = $SerialNos->find(['number' => $number]);
                if (empty($sno)) { //create new, it happens if serialnos entered manually
                    $SerialNos->insert([
                        'number' => $number,
                        'initial_stockid' => $stockid,
                        'current_stock_id' => $stockid,
                        'source' => SerialNos::SOURCE_RETURN,
                        'returned' => 1,
                        'createdby' => $_SESSION['member']['id']
                    ]);
                    $serialno_id = $SerialNos->lastId();
                } else {
                    $serialno_id = $sno[0]['id'];
                    $SerialNos->update($serialno_id, [
                        'returned' => 1,
                    ]);
                }

                $GrnReturnSerials->insert([
                    'grdi' => $grdi,
                    'serialno_id' => $serialno_id,
                ]);
            }
        }
    }
    $_SESSION['message'] = "Goods returned successfully";
    redirect('grns', 'grn_list');
}

if ($action == 'grn_return_list') {
    Users::isAllowed();
    $searchterms = $_POST['search'];

    $branchid = $searchterms['branchid'];
    $locationid = $searchterms['locationid'];
    $lpoid = $searchterms['lpo'];
    $grnid = $searchterms['grn'];
    $returnid = $searchterms['returnid'];
    $fromdate = $searchterms['from'];
    $todate = $searchterms['to'];
    $supplierid = $searchterms['supplierid'];
    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($returnid) $title[] = "Return No: " . $returnid;
    if ($lpoid) $title[] = "LPO No: " . $lpoid;
    if ($grnid) $title[] = "GRN No: " . $grnid;
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if (!$returnid && !$fromdate) $fromdate = date('Y-m-d', strtotime('-1 months'));
    $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);


    $tData['title'] = implode(' | ', $title);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $tData['returnList'] = $GrnReturns->getGrnReturns($returnid, $grnid, $supplierid, $fromdate, $todate, $locationid, $branchid);
//     debug($tData['returnList']);
    $tData['suppliers'] = $Suppliers->find(array('status' => 'active'));
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $data['content'] = loadTemplate('grn_return_list.tpl.php', $tData);
}

if ($action == 'grn_return_print') {
    $returnid = $_GET['returnid'];
    if ($return = $GrnReturns->get($returnid)) {
        $returnBatches = $GrnReturns->getGrnReturnBatchWise($returnid);
//        debug($returnBatches);
        $newArray = [];
        foreach ($returnBatches as $index => $item) {
            $newArray[$item['id']]['returnno'] = $item['id'];
            $newArray[$item['id']]['doc'] = $item['doc'];
            $newArray[$item['id']]['grnid'] = $item['grnid'];
            $newArray[$item['id']]['locationname'] = $item['locationname'];
            $newArray[$item['id']]['suppliername'] = $item['suppliername'];
            $newArray[$item['id']]['description'] = $item['description'];
            $newArray[$item['id']]['username'] = $item['username'];
            $newArray[$item['id']]['products'][$item['detailId']]['detailId'] = $item['detailId'];
            $newArray[$item['id']]['products'][$item['detailId']]['productname'] = $item['productname'];
            $newArray[$item['id']]['products'][$item['detailId']]['productdescription'] = $item['productdescription'];
            $newArray[$item['id']]['products'][$item['detailId']]['qty'] += $item['qty'];
            $newArray[$item['id']]['products'][$item['detailId']]['track_expire_date'] = $item['track_expire_date'];
            $newArray[$item['id']]['products'][$item['detailId']]['trackserialno'] = $item['trackserialno'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['batchId'] = $item['batchId'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['batch_no'] = $item['batch_no'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['qty'] = $item['qty'];
            $newArray[$item['id']]['products'][$item['detailId']]['batches'][$item['batchId']]['expire_date'] = $item['expire_date'];
        }
        $newArray = array_values($newArray)[0];
        foreach ($newArray['products'] as $index => $product) {
            $serialno_Ids = array_column($GrnReturnSerials->find(['grdi' => $product['detailId']]), 'serialno_id');
            $serialnos = $SerialNos->findMany(['id' => $serialno_Ids]);
//            debug($serialnos);
            $newArray['products'][$index]['serialnos'] = $serialnos;
        }
//        debug($newArray);
        $data['grnReturn'] = $newArray;
        $data['layout'] = 'grn_return_print.tpl.php';
    } else {
        debug("<h1>Return Number Not Found</h1>" . PHP_EOL .
            "<a href='?'>Home</a>");
    }
}


if ($action == 'lpo_list') {
    Users::isAllowed();

    $lpoid = $_GET['search']['lpo'];
    $createdby = $_GET['search']['createdby'];
    $branchid = $_GET['search']['branchid'];
    $locationid = $_GET['search']['locationid'];
    $fromdate = $_GET['search']['from'] ?: TODAY;
    $todate = $_GET['search']['to'];
    $supplierid = $_GET['search']['supplierid'];
    $status = $_GET['search']['status'];

    if (Users::cannot(OtherRights::approve_other_lpo)) $createdby = $_SESSION['member']['id'];

    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($lpoid) $title[] = "LPO No: " . $lpoid;
    if ($grnid) $title[] = "GRN No: " . $grnid;
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if ($lpoid || $grnid) $fromdate = $todate = '';
    if ($createdby) {
        $tData['creator'] = $creator = $Users->get($createdby);
        $title[] = "Issued by: " . $creator['name'];
    }
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

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

    $tData['lpolist'] = $LPO->getList($lpoid, $createdby, $fromdate, $todate, $supplierid, $locationid, $branchid, $status);

//     debug($tData['lpolist']);

    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('lpo_list.tpl.php', $tData);
}

if ($action == 'lpo_process') {
    $lpoid = $_GET['lpoid'];
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    if ($ALL_BRANCH || $USER_BRANCH) {
        $locations = $ALL_BRANCH ? $Locations->locationList() : $Locations->locationList('', $_SESSION['member']['branchid']);
        $tData['locations'] = $locations;
    } else {
        $tData['locations'] = $Locations->locationList($_SESSION['member']['locationid']);
    }

//   debug($tData);


    if ($lpoid) {
        Users::can(OtherRights::edit_lpo, true);
        $lpo = $LPO->get($lpoid);
        if (!$lpo) {
            $_SESSION['error'] = "LPO not found!";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }
        if ($lpo['approvedby']) {
            $_SESSION['error'] = "LPO has already been approved,cant edit";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }
        if ($lpo['status'] != 'active') {
            $_SESSION['error'] = "LPO is canceled!";
            $_SESSION['delay'] = 5000;
            redirectBack();
        }
        $lpo['suppliername'] = $Suppliers->get($lpo['supplierid'])['name'];
        $tData['defaultLocation'] = $Locations->get($lpo['locationid']);
        $lpo['details'] = $LPODetails->find(['lpoid' => $lpoid]);
        foreach ($lpo['details'] as $index => $detail) {
            $product = $Products->get($detail['prodid']);
            if ($product['non_stock']) unset($lpo['details'][$index]);
            $lpo['details'][$index]['productname'] = $product['name'];
            $lpo['details'][$index]['description'] = $product['description'];
            $category = $Categories->get($product['categoryid']);
            $lpo['details'][$index]['vat_rate'] = $category['vat_percent'];
            $lpo['details'][$index]['base_rate'] = $detail['rate'] * $lpo['currency_amount'];
        }
        $tData['lpo'] = $lpo;
//        debug($lpo);
    } else {
        Users::can(OtherRights::add_lpo, true);
    }
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('lpo_new_edit.tpl.php', $tData);
}

if ($action == 'save_lpo_new') {
//    debug($_POST);
    $lpo = $_POST['lpo'];
    $productIds = $_POST['productid'];
    $rate = $_POST['rate'];
    $qty = $_POST['qty'];
    $vat_percentage = $_POST['vat_percentage'];

    validate($lpo);
    validate($productIds);

    $lpo['total_amount'] = removeComma($lpo['total_amount']);
    $lpo['full_amount'] = removeComma($lpo['full_amount']);
    $lpo['grand_vatamount'] = removeComma($lpo['grand_vatamount']);

    global $db_connection;
    try {
        mysqli_begin_transaction($db_connection);
        if (!$lpo['id']) {//new
            $lpo['createdby'] = $_SESSION['member']['id'];

            if (!CS_LPO_APPROVAL) { //if approval not required
                $lpo['approvedby'] = $_SESSION['member']['id'];
                $lpo['approval_date'] = TIMESTAMP;
                $lpo['auto_approve'] = 1;
            }

            $LPO->insert($lpo);
            $lpoid = $LPO->lastId();
        } else {//editing
            $lpoid = $lpo['id'];

            $oldLpo = $LPO->get($lpoid);
            if ($oldLpo['approvedby']) throw new Exception('LPO has already been approved, cant be edited!');
            $oldLpo['details'] = $LPODetails->find(['lpoid' => $lpoId]);
            $LpoLogs->insert([
                'lpoid' => $lpoId,
                'payload' => base64_encode(json_encode($oldLpo)),
                'createdby' => $_SESSION['member']['id']
            ]);

            $lpo['modifiedby'] = $_SESSION['member']['id'];
            $LPO->update($lpoid, $lpo);

            //delete previous details
            $LPODetails->deleteWhere(['lpoid' => $lpoid]);
        }

        foreach ($productIds as $index => $productId) {
            $LPODetails->insert([
                'prodid' => $productId,
                'lpoid' => $lpoid,
                'rate' => $rate[$index],
                'qty' => $qty[$index],
                'vat_rate' => $vat_percentage[$index],
                'createdby' => $_SESSION['member']['id'],
            ]);
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "LPO " . ($lpo ? 'Updated' : 'Created') . " successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error, " . $e->getMessage();
        $_SESSION['delay'] = 5000;
    }
    redirect('grns', 'lpo_list');
}

if ($action == 'view_lpo') {
    $tData['company'] = $Settings->getAll();
    $tData['company'] = $tData['company'][0];
    $lpo = $LPO->getList($_GET['lpo'])[0];
    if (!$lpo) debug("LPO not found!");

    $lpo['details'] = $LPODetails->find(['lpoid' => $lpo['lponumber']]);
    foreach ($lpo['details'] as $index => $detail) {
        $product = $Products->get($detail['prodid']);
        $excamount = round($detail['rate'] * $detail['qty'], 2);
        $vatamount = round($excamount * ($detail['vat_rate'] / 100), 2);
        $lpo['details'][$index]['productname'] = $product['name'];
        $lpo['details'][$index]['excamount'] = $excamount;
        $lpo['details'][$index]['vatamount'] = $vatamount;
        $lpo['details'][$index]['incamount'] = $excamount + $vatamount;
    }
//    debug($lpo);
    $tData['lpo'] = $lpo;
    $data['content'] = loadTemplate('view_lpo.tpl.php', $tData);
}

if ($action == 'cancel_lpo') {
    if (!(Users::can(OtherRights::approve_other_lpo) || Users::can(OtherRights::approve_lpo)))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve LPO')]);
//    debug($_POST);
    $lpoid = $_POST['lpoid'];
    $revive = isset($_POST['revive']);
    if (!$lpo = $LPO->get($lpoid)) debug('LPO not found!');
    if ($lpo['approvedby']) {
        $_SESSION['error'] = "LPO already approved";
        redirectBack();
    }
    $LPO->update($lpoid, ['status' => $revive ? 'active' : 'inactive']);
    $_SESSION['message'] = "LPO no. $lpoid " . ($revive ? 'restored' : 'canceled') . " successfully";
    redirect('grns', 'lpo_list');
}

if ($action == 'disapprove_lpo') {
    if (!(Users::can(OtherRights::approve_other_lpo) || Users::can(OtherRights::approve_lpo)))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve LPO')]);
//    debug($_POST);
    $lpoid = removeSpecialCharacters($_POST['lpoid']);
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (!$lpo = $LPO->get($lpoid)) throw new Exception('LPO not found!');
        if ($GRN->find(['lpoid' => $lpoid])) throw new Exception('LPO already have GRN!');
        if ($lpo['status'] != 'active') throw new Exception('LPO is canceled!');

        $LPO->update($lpoid, ['approvedby' => '']);
        $_SESSION['message'] = "LPO disapproved successfully";

        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirect('grns', 'lpo_list');
}

if ($action == 'approve_lpo') {
    if (Users::cannot(OtherRights::approve_other_lpo) && Users::cannot(OtherRights::approve_lpo))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve LPO')]);

    $lpoid = $_POST['lpoid'];
    if ($lpo = $LPO->get($lpoid)) {
        if ($lpo['approvedby']) {
            $_SESSION['error'] = "LPO has already been approved!";
            redirectBack();
        }
        if ($lpo['status'] != 'active') {
            $_SESSION['error'] = "LPO is canceled!";
            redirectBack();
        }
        $LPO->update($lpoid, [
            'approvedby' => $_SESSION['member']['id'],
            'approval_date' => TIMESTAMP
        ]);
        $_SESSION['message'] = "LPO Approved";
    } else {
        $_SESSION['error'] = "LPO Not Found!";
    }
    $_SESSION['delay'] = 2000;
    redirect('grns', 'lpo_list');
}

if ($action == 'print_lpo') {
    $lpoid = removeSpecialCharacters($_GET['lpo']);
    $tData['company'] = $Settings->getAll();
    $tData['company'] = $tData['company'][0];
    $lpo = $LPO->get($lpoid);
    if (!$lpo) {
        debug("LPO not found!");
    }
    $lpo = LPO::$staticClass->getList($lpoid)[0];
    $lpo['details'] = $LPODetails->find(['lpoid' => $lpo['lponumber']]);
    foreach ($lpo['details'] as $index => $detail) {
        $product = $Products->get($detail['prodid']);
//        debug($lpo);
        $excamount = round($detail['rate'] * $detail['qty'], 2);
        $vatamount = round($excamount * ($detail['vat_rate'] / 100), 2);
        $lpo['details'][$index]['productname'] = $product['name'];
        $lpo['details'][$index]['productdescription'] = $product['description'];
        $lpo['details'][$index]['excamount'] = $excamount;
        $lpo['details'][$index]['vatamount'] = $vatamount;
        $lpo['details'][$index]['incamount'] = $excamount + $vatamount;
    }
//    debug($lpo);
//    debug('LPO print in progress');
    $tData['lpo'] = $lpo;
    $data['layout'] = 'layout_blank.tpl.php';
    $data['content'] = loadTemplate('lpo_print.tpl.php', $tData);
}

if ($action == 'ajax_getGrnProducts') {
    $grnid = $_GET['grnid'];
    $grn = $GRN->get($grnid);
    if ($grn) {
        $grn['details'] = $GRNDetails->find(['grnid' => $grnid]);
        foreach ($grn['details'] as $index => $detail) {
            $stock = $Stocks->get($detail['stockid']);
            $product = $Products->get($stock['productid']);
            $grn['details'][$index]['productname'] = $product['name'];
            $excamount = $detail['rate'] * $detail['qty'];
            $vatamount = $excamount * ($detail['vat_percentage'] / 100);
            $grn['details'][$index]['excamount'] = $excamount;
            $grn['details'][$index]['vatamount'] = $vatamount;
            $grn['details'][$index]['incamount'] = $excamount + $vatamount;
            if ($product['track_expire_date'])
                $grn['details'][$index]['batches'] = $Batches->find(['gdi' => $detail['id']]);
        }

        $obj->status = 'found';
        $obj->grn = $grn;
    } else {
        $obj->status = 'error';
        $obj->grn = null;
    }
//    debug($grn);
    $data['content'] = $obj;
}

if ($action == 'ajax_grn_return_details') {
    $grnid = $_GET['grnid'];
    $det = $GRN->getGrnWithReturnQty($grnid);
    $grnInfo = array_values($det)[0];
    //find remaining stock qty
    foreach ($grnInfo['stock'] as $index => $stock) {
        foreach ($stock['batches'] as $bkey => $batch) {
            $batchStock = $Stocks->calcStock(
                $grnInfo['st_locid'],
                $stock['stockid'],
                "", "", "",
                "", "", "",
                "", "", "", "",
                "", $batch['batchId'], "",
                "", "", true, false,
                "", "", false
            );
//            debug($batchStock);
            $grnInfo['stock'][$index]['batches'][$bkey]['current_stock'] = $batchStock[0]['total'];
        }
        $grnInfo['stock'][$index]['current_stock_qty'] = array_sum(array_column($grnInfo['stock'][$index]['batches'], 'current_stock'));
    }


//    debug($grnInfo);


    $response = array();
    $obj = null;
    if ($det) {
        $obj->status = 'found';
        $obj->details = $grnInfo;

    } else {
        $obj->status = 'not found';
    }
    $response[] = $obj;
    $data['content'] = $response;
}

if ($action == 'ajax_updateGRN') {
//    sleep(3);
    $obj->status = "success";
    $obj->msg = "Updated Successfully";

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (empty($_POST['data'])) throw new Exception("No data received");

        $data = json_decode($_POST['data'], true);
        $grn = $data['grn'];
        $currentGrn = $GRN->get($grn['id']);
        $currencyRate = $CurrenciesRates->get($grn['currency_rateid']);
        $exchange_rate = $currencyRate['rate_amount'];

        $grn['total_amount'] = removeComma($grn['total_amount']);
        $grn['full_amount'] = removeComma($grn['full_amount']);
        $grn['grand_vatamount'] = removeComma($grn['grand_vatamount']);

        $grn['currency_amount'] = $exchange_rate;
        $grn['modifiedby'] = $_SESSION['member']['id'];


        $GRN->update($grn['id'], $grn);

        if ($currentGrn['supplier_payment']) {
            //update payment for cash grn todo NB: assumption grn payment has only single detail
            if ($currentGrn['paymenttype'] == PAYMENT_TYPE_CASH) {
                $paymentDetail = $SupplierPaymentDetails->find(['grnid' => $grn['id']])[0];
                if ($paymentDetail) {
                    $paymentMaster = $SupplierPayments->find(['id' => $paymentDetail['spid']])[0];
                    $SupplierPaymentDetails->update($paymentDetail['id'], [
                        'amount' => $grn['full_amount']
                    ]);
                    $SupplierPayments->update($paymentMaster['id'], [
                        'total_amount' => array_sum(array_column($SupplierPaymentDetails->find(['spid' => $paymentMaster['id']]), 'amount'))
                    ]);
                }
            }
        }

        //update detail
        $detail = $data['detail'];
//        debug($detail);
        $detailBefore = $GRNDetails->get($detail['id']);
        $GRNDetails->update($detail['id'], [
            'rate' => $detail['rate'],
            'qty' => $detail['qty'],
            'billable_qty' => $detail['billable_qty'],
        ]);
        if (mysqli_affected_rows($db_connection) > 0) { //if any changes found after update create a log
            $detailAfter = $GRNDetails->get($detail['id']);
            $GrnDetailLogs->insert([
                'grnid' => $grn['id'],
                'gdi' => $detail['id'],
                'before_payload' => base64_encode(json_encode($detailBefore)),
                'after_payload' => base64_encode(json_encode($detailAfter)),
                'createdby' => $_SESSION['member']['id'],
            ]);

            //update price log if exists
            if ($priceLog = $PriceLogs->find(['gdi' => $detail['id']])[0]) {
                $baseRate = $detail['rate'] * $grn['currency_amount'];
                $costprice = round($baseRate, 2);
//                $costprice = addTAX($detail['rate'], $detail['vat_rate']);
                $PriceLogs->update($priceLog['id'], [
                    'costprice' => $costprice,
                    'modifiedby' => $_SESSION['member']['id'],
                    'dom' => TIMESTAMP,
                    'remarks' => $priceLog['remarks'] . PHP_EOL . "Update from GRN editing " . date('Y-m-d H:i')
                ]);

                //update current price if log in use
                if ($currentPrice = $CurrentPrices->find(['logid' => $priceLog['id']])[0]) {
                    $CurrentPrices->update($currentPrice['id'], [
                        'costprice' => $costprice
                    ]);
                }
            }
        }

        //update batches
        if ($detail['track_expire_date'] == 1) { //for tracking expire product
            foreach ($detail['batches'] as $index => $batch) {
                $batchBefore = $Batches->get($batch['id']);
                $Batches->update($batch['id'], $batch);
                if (mysqli_affected_rows($db_connection) > 0) { //if any changes found after update create a log
                    $batchAfter = $Batches->get($batch['id']);
                    $GrnBatchLogs->insert([
                        'gdi' => $detail['id'],
                        'batchid' => $batch['id'],
                        'before_payload' => base64_encode(json_encode($batchBefore)),
                        'after_payload' => base64_encode(json_encode($batchAfter)),
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                }
            }
        } else {
            //find detail batches
            $batches = $Batches->find(['gdi' => $detail['id']]);
            foreach ($batches as $bi => $batch) {
                $current_batch_stock = Stocks::$stockClass->calcStock(
                    $currentGrn['locid'],
                    $detailBefore['stockid'],
                    "", "", "",
                    "", "", "",
                    "", "", "", "",
                    "", $batch['id'], "",
                    "", "", true, false,
                    "", "", false
                );
                $current_batch_stock = array_values($current_batch_stock)[0];
                $batches[$bi]['total'] = $current_batch_stock['total'];
            }

            $qty_diff = $detail['qty'] - $detailBefore['qty'];


            //if new qty > detail qty
            if ($qty_diff > 0) {
                $batchBefore = $batches[0];
                $Batches->update($batches[0]['id'], [
                    'qty' => $batchBefore['qty'] + $qty_diff
                ]);
                $batchAfter = $Batches->get($batches[0]['id']);
                $GrnBatchLogs->insert([
                    'gdi' => $detail['id'],
                    'batchid' => $batches[0]['id'],
                    'before_payload' => base64_encode(json_encode($batchBefore)),
                    'after_payload' => base64_encode(json_encode($batchAfter)),
                    'createdby' => $_SESSION['member']['id'],
                ]);
            } elseif ($qty_diff < 0) {
                $qty_diff = abs($qty_diff);
                $batches = array_filter($batches, function ($b) {
                    return $b['total'] > 0;
                });
                foreach (array_reverse($batches) as $batch) {
                    if ($batch['total'] >= $qty_diff) {//covers whole qty
                        $Batches->update($batch['id'], [
                            'qty' => $batch['qty'] - $qty_diff
                        ]);
                        $qty_diff = 0;
                    } else {
                        $Batches->update($batch['id'], [
                            'qty' => $batch['qty'] - $batch['total']
                        ]);
                        $qty_diff -= $batch['total'];
                    }
                    $batchAfter = $Batches->get($batch['id']);
                    $GrnBatchLogs->insert([
                        'gdi' => $detail['id'],
                        'batchid' => $batch['id'],
                        'before_payload' => base64_encode(json_encode($batch)),
                        'after_payload' => base64_encode(json_encode($batchAfter)),
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    if ($qty_diff == 0) break;
                }
                if ($qty_diff > 0) throw new Exception("Remaining batch qty not enough!");
            }
        }
        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }

    $data['content'] = $obj;
}