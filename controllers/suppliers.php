<?
if ($action == 'supplier_list') {
    Users::isAllowed();
    $tData['suppliers'] = $Suppliers->getAll();
    $data['content'] = loadTemplate('supplier_list.tpl.php', $tData);
}

if ($action == 'supplier_add') {
    $supplierid = $_GET['supplierid'];
    if ($supplierid) {
        Users::can(OtherRights::edit_supplier, true);
        $tData['supplier'] = $Suppliers->get($supplierid);
    } else {
        Users::can(OtherRights::add_supplier, true);
    }
    $data['content'] = loadTemplate('supplier_edit.tpl.php', $tData);
}

if ($action == 'supplier_save' || $action == 'quick_add') {
//    debug($_POST);
    $supplier = $_POST['supplier'];

    validate($supplier);
    try {
        if ($supplier['id']) {
            $old_supplier = $Suppliers->get($supplier['id']);
            // editing
            $Suppliers->update($supplier['id'], $supplier);
        } else {
            if ($Suppliers->find(['name' => $supplier['name']])) throw new Exception('Supplier Already Exists');
            $supplier['createdby'] = $_SESSION['member']['id'];;
            $Suppliers->Insert($supplier);
        }

        if (CS_TALLY_TRANSFER) {
            $tally_result = createTallyLedger(TallyGroups::SUNDRY_CREDITORS, $supplier['name'], $old_supplier['ledgername']);
            if ($tally_result['status']=='success') {
                $Suppliers->update($supplier['id'], ['ledgername' => $supplier['name']]);
                $tally_message = $tally_result['msg'];
            }else {
                $_SESSION['error'] = $tally_result['msg'];
            }
        }


        $_SESSION['message'] = "Supplier " . ($supplier['id'] ? 'updated' : 'added'). " \n $tally_message";
        $action == 'quick_add' ? redirectBack() : redirect('suppliers', 'supplier_list');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 3000;
        redirectBack();
    }

}


if ($action == 'update_tally_ledger') {
    $supplierid = $_GET['supplierid'];

    $supplier = $Suppliers->get($supplierid);
    $tally_result = createTallyLedger(TallyGroups::SUNDRY_CREDITORS, $supplier['name'], $supplier['ledgername']);
    if ($tally_result['status']=='success') {
        $Suppliers->update($supplier['id'], ['ledgername' => $supplier['name']]);
        $tally_message = $tally_result['msg'];
    }else {
        $_SESSION['error'] = $tally_result['msg'];
    }

    redirectBack();
}

//payments
if ($action == 'payment_list') {
    Users::isAllowed();
    $branchid = $_GET['search']['branchid'] ?? $_SESSION['member']['branchid'];
    $locationid = $_GET['search']['locationid'];
    $supplierid = $_GET['search']['supplierid'];
    $fromdate = $_GET['search']['fromdate'];
    $todate = $_GET['search']['todate'];

    $title = [];
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if (isset($_GET['search']['outstanding_only'])) $title[] = "With outstanding only: ";

    $tData['title'] = implode(' | ', $title);

    $supplierOutstanding = $Suppliers->outstandingPayments($supplierid, $fromdate, $todate, $locationid, $branchid);

    foreach ($supplierOutstanding as $index => $item) {
        $advancePayments = $Suppliers->advancePaymentsBalance($item['supplierid']);
        $supplierOutstanding[$index]['advance_balance'] = array_sum(array_column($advancePayments, 'remain_advance'));
    }
    if (isset($_GET['search']['outstanding_only'])) {
        $supplierOutstanding = array_filter($supplierOutstanding, function ($R) {
            return $R['outstanding_amount'] > 0;
        });
    }
//    debug($supplierOutstanding);
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['currentBranch'] = $Branches->get($branchid);
    $tData['supplierOutstanding'] = $supplierOutstanding;
    $tData['total_outstanding'] = array_sum(array_column($supplierOutstanding, 'outstanding_amount'));
    $tData['base_currency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('supplier_payment_list.tpl.php', $tData);
}

if ($action == 'outstanding_detailed') {
    Users::can(OtherRights::pay_supplier, true);
//    debug($_GET);
    $supplierid = $_GET['supplierid'];
    $branchid = $_GET['branchid'];
    if ($supplier = $Suppliers->get($supplierid)) {
        $outstandingGrns = $GRN->withPaymentAmount($supplierid, "", "", $branchid,
            "", "", "", true);

        $advancePayment = $Suppliers->advancePaymentsBalance($supplierid, $branchid);
        $totalOutstanding = array_sum(array_column($outstandingGrns, 'outstanding_amount'));
        $tData['supplier'] = $supplier;
        $tData['currentBranch'] = $Branches->get($branchid);
        $tData['outstandingGrns'] = $outstandingGrns;
        $tData['advanceAmount'] = array_sum(array_column($advancePayment, 'remain_advance'));
        $tData['totalOutstanding'] = $totalOutstanding;
    }
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['base_currency'] = $CurrenciesRates->getBaseCurrency();

    $data['content'] = loadTemplate('supplier_outstanding_detailed.tpl.php', $tData);
}

if ($action == 'make_payment') {
    Users::can(OtherRights::pay_supplier, true);

//    debug($_POST);
    $supplierid = $_POST['supplierid'];
    $branchid = $_POST['branchid'];
    $grnids = $_POST['grnid'];
    $openings = $_POST['openid'];
    if (empty($grnids) && empty($openings)) {
        $_SESSION['error'] = "Please select at least one item!";
        $_SESSION['delay'] = 3000;
        redirectBack();
        die();
    }
    if ($supplier = $Suppliers->get($supplierid)) {
        $outstandingGrns = $GRN->withPaymentAmount($supplierid, $grnids, $openings, $branchid,
            "", "", "", true, !empty($openings), !empty($grnids));
        $tData['paymentmethod'] = $Paymentmethods->getReceiving();
        $totalamount = array_sum(array_column($outstandingGrns, 'outstanding_amount'));
        $tData['totalamount'] = $totalamount;
        $advancePayment = $Suppliers->advancePaymentsBalance($supplierid, $branchid);
//        debug($outstandingGrns);
        $tData['advanceAmount'] = array_sum(array_column($advancePayment, 'remain_advance'));


        $tData['supplier'] = $supplier;
        $tData['branch'] = $Branches->get($branchid);
        $tData['grns'] = $outstandingGrns;
        $tData['banks'] = $Banks->getAllActive();
        $tData['eaccounts'] = $ElectronicAccounts->getAllActive();
        $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
        $tData['base_currency'] = $CurrenciesRates->getBaseCurrency();
        $tData['currentCurrency'] = array_filter($tData['currencies'], function ($c) {
            return $c['base'] == 'yes';
        })[0];

//        debug($tData['currentCurrency']);
        $data['content'] = loadTemplate('make_supplier_payment.tpl.php', $tData);
    } else {
        debug("Supplier Not Found!");
    }
}

if ($action == 'save_payment') {

//    debug($_POST);
    $payment = $_POST['payment'];
    $payment_info = $_POST['payment_info'];
    $grnids = $_POST['grnid'];
    $openingid = $_POST['openingid'];
    $receipt_no = $_POST['receipt_no'];
    $pay_amount = $_POST['pay_amount'];
    $total_outstanding = $_POST['total_outstanding'];
    $input_amount = $_POST['input_amount'];
    $offset_amount = $_POST['offset_amount'];

    validate($payment);
    validate($pay_amount);

    $payment['total_amount'] = array_sum($pay_amount);
    $payment['createdby'] = $_SESSION['member']['id'];

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {

//    debug($payment, true);
        $SupplierPayments->insert($payment);
        $spid = $SupplierPayments->lastId();

        //payment info
        $insert = false;
        foreach ($payment_info as $item) {
            if (!empty($item)) $insert = true;
        }
        if ($insert) {
            $payment_info['spid'] = $spid;
            $payment_info['createdby'] = $_SESSION['member']['id'];
            $SupplierPaymentInfo->insert($payment_info);
        }

        $new_advance = 0;
        if (isset($_POST['offset_advance'])) {
            $advancePayments = $Suppliers->advancePaymentsBalance($supplierid, $branchid);
            $advance_amount = array_sum(array_column($advancePayments, 'remain_advance'));

            if ($offset_amount != $advance_amount) throw new Exception('System found you are trying to offset the non existing advance amount!');
            //used advance amount
            $used_advance = $total_outstanding > $advance_amount ? $advance_amount : $total_outstanding;

            //distribute advance payment
            $used_remain = $used_advance;
            foreach ($advancePayments as $index => $advance) {
                if ($advance['remain_advance'] > $used_remain) {//covers whole amount
                    $SupplierUsedAdvancePayments->insert([
                        'spid' => $spid,
                        'advance_id' => $advance['advance_id'],
                        'amount' => $used_remain,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $used_remain -= $used_remain;
                    break;
                } else {
                    $SupplierUsedAdvancePayments->insert([
                        'spid' => $spid,
                        'advance_id' => $advance['advance_id'],
                        'amount' => $advance['remain_advance'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $used_remain -= $advance['remain_advance'];
                }
                if ($used_remain == 0) break;
            }

            //finding if there is new advance
            $remain_after_offset = $total_outstanding - $advance_amount;
            if ($remain_after_offset > 0) {
                $new_advance = $input_amount - $remain_after_offset;
            } else {
                $new_advance = $input_amount;
            }
        } else {
            $new_advance = $input_amount - $total_outstanding;
        }

        if ($new_advance > 0) {
            $SupplierAdvancePayments->insert([
                'supplierid' => $payment['supplierid'],
                'branchid' => $payment['branch_id'],
                'pmethod_id' => $payment['pmethod_id'],
                'currency_rateid' => $payment['currency_rateid'],
                'currency_amount' => $payment['currency_amount'],
                'spid' => $spid,
                'amount' => $new_advance,
                'createdby' => $_SESSION['member']['id'],
                'remark' => "From over payment",
            ]);
        }

        //details
        foreach ($grnids as $index => $grnid) {
            if ($grnid) { //for grn
                $detail = [
                    'spid' => $spid,
                    'grnid' => $grnid,
                    'amount' => $pay_amount[$index],
                    'supplier_receiptno' => $receipt_no[$index],
                    'createdby' => $_SESSION['member']['id'],
                ];
            } else { //for opening
                $detail = [
                    'spid' => $spid,
                    'openingid' => $openingid[$index],
                    'amount' => $pay_amount[$index],
                    'supplier_receiptno' => $receipt_no[$index],
                    'createdby' => $_SESSION['member']['id'],
                ];
            }

            $SupplierPaymentDetails->insert($detail);
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Payment saved successfully";
        redirect('suppliers', 'payment_slip', ['id' => $spid]);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = "Error " . $e->getMessage();
        $_SESSION['delay'] = 4000;
        redirect('suppliers', 'payment_list');
    }


}

if ($action == 'payment_slip') {
    if (!empty($paymentId = intval($_GET['id']))) {
        $paymentInfo = $SupplierPayments->detailedPaymentInfo($paymentId)[0];
//        debug($paymentInfo);
        if (empty($paymentInfo)) {
            debug('Payment Slip Not Found!');
            die();
        }
//        debug($paymentInfo);
        $data['baseCurrency'] = $Currencies->find(['base' => 'yes'])[0];
        $data['paymentInfo'] = $paymentInfo;
//        debug($paymentInfo);
        $data['layout'] = 'print_supplier_payment_slip.tpl.php';
    } else {
        debug('Payment Receipt Not Found!');
    }

}

if ($action == 'grn_payments') {
    $grnid = $_GET['grnid'];
//    debug($grnid);
    if ($grn = $GRN->get($grnid)) {
        $supplierPayments = $SupplierPayments->grnPayments($grnid);
        $grn['total_paid'] = array_sum(array_column($supplierPayments, 'amount'));
        if ($grn['total_paid'] >= $grn['full_amount']) {
            $grn['payment_status'] = PAYMENT_STATUS_COMPLETE;
        } elseif ($grn['total_paid'] > 0) {
            $grn['payment_status'] = PAYMENT_STATUS_PARTIAL;
        } else {
            $grn['payment_status'] = PAYMENT_STATUS_PENDING;
        }
        $grn['outstanding_amount'] = $grn['full_amount'] - $grn['total_paid'];
        $grn['creator'] = $Users->get($grn['createdby'])['name'];
        $grn['suppliername'] = $Suppliers->get($grn['supplierid'])['name'];
        $grn['currency_name'] = $CurrenciesRates->getCurrency_rates($grn['currency_rateid'])['currencyname'];
        $tData['grn'] = $grn;
        $tData['payments'] = $supplierPayments;
        $data['content'] = loadTemplate('grn_payment_list.tpl.php', $tData);
    } else {
        debug('GRN info Not Found!');
    }
}

if ($action == 'opening_outstanding_payments') {
    $openingid = $_GET['openingid'];
    if ($opening = $SupplierOpeningOutstandings->get($openingid)) {
        $supplierPayments = $SupplierPayments->openingPayments($openingid);
        $opening['total_paid'] = array_sum(array_column($supplierPayments, 'amount'));
        if ($opening['total_paid'] >= $opening['amount']) {
            $opening['payment_status'] = PAYMENT_STATUS_COMPLETE;
        } elseif ($opening['total_paid'] > 0) {
            $opening['payment_status'] = PAYMENT_STATUS_PARTIAL;
        } else {
            $opening['payment_status'] = PAYMENT_STATUS_PENDING;
        }
        $opening['outstanding_amount'] = $opening['amount'] - $opening['total_paid'];
        $opening['creator'] = $Users->get($opening['createdby'])['name'];
        $opening['suppliername'] = $Suppliers->get($opening['supplierid'])['name'];
//        debug($supplierPayments);
        $tData['opening'] = $opening;
        $tData['payments'] = $supplierPayments;
        $data['content'] = loadTemplate('supplier_opening_outstanding_payment_list.tpl.php', $tData);
    } else {
        debug('Opening outstanding Info Not Found!');
    }
}

if ($action == 'opening_outstanding_list') {
    Users::isAllowed();
//    debug('opening');
    $locationid = $_GET['locationid'];
    $branchid = $_GET['branchid'];
    $title = [];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];

    $tData['title'] = implode(' | ', $title);
    $tData['list'] = $SupplierOpeningOutstandings->getList($locationid, $branchid);

    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('supplier_opening_outstanding_list.tpl.php', $tData);
}

if ($action == 'create_opening_outstanding') {
    Users::isAllowed();
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['id']);
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('create_opening_supplier_outstanding.tpl.php', $tData);
}

if ($action == 'save_opening_outstanding') {
//    debug($_POST);
    $openid = $_POST['openid'];
    $grnnos = $_POST['grnno'];
    $supplierid = $_POST['supplierid'];
    $invoiceno = $_POST['invoiceno'];
    $locationid = $_POST['locationid'];
    $currency_amount = $_POST['currency_amount'];
    $currency_rateid = $_POST['currency_rateid'];
    $amount = $_POST['amount'];

    validate($openid);
    validate($grnnos);

    foreach ($grnnos as $index => $grnno) {
        if (!$openid[$index]) {//new
            $SupplierOpeningOutstandings->insert([
                'grnno' => $grnno,
                'supplierid' => $supplierid[$index],
                'invoiceno' => $invoiceno[$index],
                'locationid' => $locationid[$index],
                'currency_rateid' => $currency_rateid[$index],
                'currency_amount' => $currency_amount[$index],
                'amount' => $amount[$index],
                'createdby' => $_SESSION['member']['id'],
            ]);
        } else {//updating
            $SupplierOpeningOutstandings->update($openid[$index], [
                'grnno' => $grnno,
                'supplierid' => $supplierid[$index],
                'invoiceno' => $invoiceno[$index],
                'locationid' => $locationid[$index],
                'currency_rateid' => $currency_rateid[$index],
                'currency_amount' => $currency_amount[$index],
                'amount' => $amount[$index],
                'modifiedby' => $_SESSION['member']['id'],
                'dom' => TIMESTAMP,
            ]);
        }
    }
    $_SESSION['message'] = "Record saved successfully";
    redirect('suppliers', 'opening_outstanding_list');
}

if ($action == 'ajax_getSuppliers') {
//    debug($_GET);
    $icData = $Suppliers->searchResults($_GET['search']['term']);

    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj = null;
            $obj->id = $ic['id'];
            $obj->text = $ic['name'];
            $obj->vat_registered = $ic['vat_registered'];
            $response['results'][] = $obj;
        }
    } else {
        $obj = null;
        $obj->text = 'No results';
        $obj->id = 0;
        $response['results'][] = $obj;
    }

    $data['content'] = $response;
}
