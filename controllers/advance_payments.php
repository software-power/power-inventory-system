<?
if ($action == 'list') {
    Users::isAllowed();
    $branchid = $_GET['search']['branchid'];
    $clientid = $_GET['search']['clientid'];
    $receiver = $_GET['search']['receiver'];
    $currencyid = $_GET['search']['currencyid'];
    $methodid = $_GET['search']['methodid'];
    $eaccount = $_GET['search']['eaccount'];
    $fromdate = $_GET['search']['fromdate'] ?: TODAY;
    $todate = $_GET['search']['todate'];
    $apid = $_GET['apid'];

    $payment_method = $Paymentmethods->get($methodid);
    if (Users::can(OtherRights::approve_other_credit_invoice)) {
        $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    } else {
        $tData['branches'] = $Branches->find(['id' => $_SESSION['member']['branchid']]);
        $receiver = $_SESSION['member']['id'];
    }
    if ($apid) $fromdate = '';
    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($receiver) $title[] = "Receiver: " . $Users->get($receiver)['name'];
    if ($methodid) $title[] = "Payment Method: " . $payment_method['name'];
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($eaccount) $title[] = "E-account: " . $ElectronicAccounts->get($eaccount)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $tData['title'] = implode(' | ', $title);

    $tData['paymentList'] = $AdvancePayments->paymentList($apid, $clientid, $fromdate, $todate, $payment_method['name'], '', $receiver, $currencyid, $eaccount);


    $tData['paymentmethod'] = $Paymentmethods->getAllActive();
    $data['content'] = loadTemplate('advance_receipt_list.tpl.php', $tData);
}

if ($action == 'receive') {
    Users::can(OtherRights::receive_advance, true);
    $tData['paymentmethods'] = $Paymentmethods->getReceiving();
    $tData['branches'] = Users::can(OtherRights::approve_other_credit_invoice)
        ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id")
        : $Branches->find(['id' => $_SESSION['member']['branchid']]);

    $tData['currencies'] = $Currencies->getAllActive();
    $tData['banks'] = $Banks->getAllActive();
    $tData['eaccounts'] = $ElectronicAccounts->getAllActive();
    $data['content'] = loadTemplate('receive_advance_receipt.tpl.php', $tData);
}

if ($action == 'save') {
    Users::can(OtherRights::receive_advance, true);

    $payment = $_POST['payment'];
    $payment['amount'] = removeComma($payment['amount']);
    try {
        $payment['transfer_tally'] = CS_TALLY_TRANSFER; //todo what if updating
        if ($payment['id']) {
            debug("Advance receipt editing not support.\r\nCancel the receipt");
//            $receiptno = $payment['id'];
//            $payment['modifiedby'] = $_SESSION['member']['id'];
//            $AdvancePayments->update($payment['id'], $payment);
//            $_SESSION['message'] = "Advance payment updated successfully";
        } else {
            if ($AdvancePayments->find(['token' => $payment['token']])) throw new Exception("System found this form is already submitted, Transaction canceled to avoid duplicate receipt!");

            $payment['createdby'] = $_SESSION['member']['id'];
            $payment['branchid'] = $_SESSION['member']['branchid'];
            $AdvancePayments->insert($payment);
            $receiptno = $AdvancePayments->lastId();
            $_SESSION['message'] = "Advance payment received successfully";
        }

        //tally transfer direct
        if (CS_TALLY_DIRECT && $payment['transfer_tally']) {
            $ping = pingTally();
            if ($ping['status'] == 'error') {
                $_SESSION['error'] .= "\n" . $ping['msg'];
            } else {
                $result = AdvancePayments::tallyPost($receiptno);
                if ($result['status'] == 'success') $_SESSION['message'] .= "\n" . $result['msg'];
                if ($result['status'] == 'error') $_SESSION['error'] .= "\n" . $result['msg'];
            }
        }

        $payment['id'] ? redirect('advance_payments', 'list') : redirect('advance_payments', 'print&id=' . $receiptno);
    } catch (Exception $e) {
        $_SESSION['error'] .= "\n" . $e->getMessage();
        redirect('advance_payments', 'list');
    }
}

if ($action == 'print') {
    Users::can(OtherRights::receive_advance, true);
    if (!empty($id = intval($_GET['id']))) {
        $paymentInfo = $AdvancePayments->paymentList($id);
        if (!empty($paymentInfo[0])) {
//            debug($paymentInfo);
            $data['paymentInfo'] = $paymentInfo[0];
            $data['layout'] = "print_advance_receipt.tpl.php";
        } else {
            debug('Payment info not found');
        }
    } else {
        debug('Payment info not found');
    }
}

if ($action == 'tally_post') {
    $receiptno = $_GET['receiptno'];

    $ping = pingTally();
    if ($ping['status'] == 'error') {
        $_SESSION['error'] = $ping['msg'];
    } else {
        $result = AdvancePayments::tallyPost($receiptno);
        if ($result['status'] == 'success') $_SESSION['message'] = $result['msg'];
        if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}

if ($action == 'cancel_receipt') {
    Users::can(OtherRights::cancel_receipt,true);
    $apid = removeSpecialCharacters($_POST['apid']);
    $remarks = $_POST['remark'];
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $payment = AdvancePayments::$advancePaymentClass->get($apid);
        if (!$payment) throw new Exception("Advance receipt not found!");
        $payment = AdvancePayments::$advancePaymentClass->paymentList($apid)[0];
        if ($payment['used_advance'] > 0) throw new Exception("Some amount of this advance receipt already used.\nReceipt cant be canceled!");
        TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $payment['tally_trxno']]); //delete receipt tally transfer
        AdvancePayments::$advancePaymentClass->deleteWhere(['id' => $apid]);

        CanceledReceipts::$staticClass->insert([
            'receiptno' => $apid,
            'tally_voucherno' => "ADV-" . getTransNo($apid),
            'source' => 'advance_receipt',
            'currencyid' => $payment['currencyid'],
            'amount' => $payment['amount'],
            'remarks' => $remarks,
            'createdby' => $_SESSION['member']['id'],
            'payload' => base64_encode(json_encode($payment)),
        ]);

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Advance receipt canceled successfully";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}

if ($action == 'ajax_advanceReceiptTallyTransaction') {
    $apid = removeSpecialCharacters($_GET['apid']);
    try {
        $receipt = AdvancePayments::$advancePaymentClass->get($apid);
        if (!$receipt) throw new Exception("Receipt not found");
        $tally_transfers = [];
        if ($receipt['tally_post']) {
            $tally_transfers[] = [
                'voucher_type' => 'Receipt',
                'voucherno' => "ADV-" . getTransNo($receipt['id'])
            ];
        }
        $result = [
            'status' => 'success',
            'data' => $tally_transfers
        ];
    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
//    debug($receipt);
    $data['content'] = $result;
}