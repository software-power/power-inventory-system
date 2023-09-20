<?
if ($action == 'invoice_list') {
    Users::isAllowed();

    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $tData['userid'] = $userid = removeSpecialCharacters($_GET['userid']);
    $clientid = removeSpecialCharacters($_GET['clientid']);
    $tData['branchid'] = $branchid = removeSpecialCharacters($_GET['branchid']);
    $locationid = removeSpecialCharacters($_GET['locationid']);
    $tData['paymenttype'] = $paymenttype = removeSpecialCharacters($_GET['paymenttype']);
    $tData['payment_status'] = $payment_status = removeSpecialCharacters($_GET['payment_status']);
    $tData['approval_status'] = $approval_status = removeSpecialCharacters($_GET['approval_status']);
    $invoiceno = htmlspecialchars($_GET['invoiceno']);
    $salesid = removeSpecialCharacters($_GET['salesid']);
    if ($salesid || $invoiceno) $fromdate = $todate = $branchid = $locationid = $userid = '';
//     debug($_GET);
    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];


    $title = [];
    if ($invoiceno) $title[] = "Invoice No: " . $invoiceno;
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) {
        $tData['location'] = $Locations->get($locationid);
        $title[] = "Location: " . $tData['location']['name'];
    }
    if ($userid) {
        $tData['creator'] = $creator = $Users->get($userid);
        $title[] = "Sales Person: " . $creator['name'];
    }
    if ($clientid) {
        $tData['client'] = $Clients->get($clientid);
        $title[] = "Client: " . $tData['client']['name'];
    }
    if ($payment_status) $title[] = "Payment Status: " . $payment_status;
    if (strlen($approval_status)) $title[] = "Approval Status: " . ($approval_status == 0 ? 'Not approved' : 'Approved');
    if ($paymenttype) $title[] = "Invoice type: " . $paymenttype;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $tData['sales_list'] = $Sales->salesInvoiceList($salesid, $userid, $clientid, false, false, $approval_status, $fromdate, $todate,
        $paymenttype, $payment_status, $locationid, $branchid, $invoiceno);
//debug($tData['sales_list']);
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('invoice_list.tpl.php', $tData);
}

if ($action == 'client_receipts') {
    Users::isAllowed();
    $_POST['invoicenum'] = $_POST['opening'] = '';
    if ($client = $Clients->get($_GET['clientid'])) {
        $tData['client'] = $client;
        $clientOutstandingSales = $Sales->salesInvoiceList("", "", $client['id'], false, true);
        $clientOutstandingSales = array_filter($clientOutstandingSales, function ($sale) { //filter only pending approved credit sales with tra receipt only
            return $sale['paymenttype'] == PAYMENT_TYPE_CREDIT && $sale['iscreditapproved'] == 1 && $sale['receipt_method'] != 'sr';
        });
        $openingOutstanding = $ClientOpeningOutstandings->getList('', $client['id'], '', '', '', '', '', '', '', true);
        $tData['sales_list'] = $clientOutstandingSales;
        $tData['openingOutstanding'] = $openingOutstanding;
        $tData['advanceBalances'] = $AdvancePayments->clientAdvanceBalances($client['id']);
//        debug($advanceBalances);

        //group total outstanding by currency
        $totalOutstandingAmounts = [];
        foreach ($clientOutstandingSales as $index => $sale) {
            $totalOutstandingAmounts[$sale['currencyid']]['currencyid'] = $sale['currencyid'];
            $totalOutstandingAmounts[$sale['currencyid']]['currencyname'] = $sale['currencyname'];
            $totalOutstandingAmounts[$sale['currencyid']]['pending_amount'] += $sale['pending_amount'];
        }
        foreach ($openingOutstanding as $index => $coo) {
            $totalOutstandingAmounts[$coo['currencyid']]['currencyid'] = $coo['currencyid'];
            $totalOutstandingAmounts[$coo['currencyid']]['currencyname'] = $coo['currencyname'];
            $totalOutstandingAmounts[$coo['currencyid']]['pending_amount'] += $coo['pending_amount'];
        }
        $tData['totalOutstandingAmounts'] = $totalOutstandingAmounts;
    }
    $data['content'] = loadTemplate('client_receipts.tpl.php', $tData);
}

if ($action == 'client_receipts_sr') {
    Users::isAllowed();
    $_POST['invoicenum'] = $_POST['opening'] = '';
    if ($client = $Clients->get($_GET['clientid'])) {
        $tData['client'] = $client;
        $clientOutstandingSales = $Sales->salesInvoiceList("", "", $client['id'], false, true);
        $clientOutstandingSales = array_filter($clientOutstandingSales, function ($sale) { //filter only pending approved credit sales with sr receipt only
            return $sale['paymenttype'] == PAYMENT_TYPE_CREDIT && $sale['iscreditapproved'] == 1 && $sale['receipt_method'] == 'sr';
        });
        $tData['sales_list'] = $clientOutstandingSales;
        $tData['advanceBalances'] = $AdvancePayments->clientAdvanceBalances($client['id']);

        //group total outstanding by currency
        $totalOutstandingAmounts = [];
        foreach ($clientOutstandingSales as $index => $sale) {
            $totalOutstandingAmounts[$sale['currencyid']]['currencyid'] = $sale['currencyid'];
            $totalOutstandingAmounts[$sale['currencyid']]['currencyname'] = $sale['currencyname'];
            $totalOutstandingAmounts[$sale['currencyid']]['pending_amount'] += $sale['pending_amount'];
        }
//        debug($totalOutstandingAmounts);
        $tData['totalOutstandingAmounts'] = $totalOutstandingAmounts;
    }
    $data['content'] = loadTemplate('client_receipts_sr.tpl.php', $tData);
}

if ($action == 'credit_payment') {
    Users::can(OtherRights::receive_credit_payment, true);
//    debug($_POST);
    $user = $_SESSION['member'];
    $invoicenum = $_POST['invoicenum'];
    $opening = $_POST['opening'];
    $tData['redirect'] = $_POST['redirect'];
    $tData['receipt_type'] = $_POST['receipt_type'];  //sales payment receipt_type from view

    if (empty($invoicenum) && empty($opening)) {
        $_SESSION['error'] = 'PLease select at least 1 to proceed payment';
        redirectBack();
    }

    $tData['client'] = $Clients->get($_POST['selectedclient']);
    if (!empty($invoicenum)) {
        $sales = $Sales->salesList($invoicenum);
    }
    if (!empty($opening)) {
        $opening_outstandings = $ClientOpeningOutstandings->getList($opening, $client['id'], '', '', '', '', '', '', '', true);
    }
//        debug($opening_outstandings);
    $tData['advanceBalances'] = $AdvancePayments->clientAdvanceBalances($_POST['selectedclient']);

    $tData['paymentmethod'] = $Paymentmethods->getReceiving();
    $tData['currencies'] = $Currencies->getAllActive();
    $tData['banks'] = $Banks->getAllActive();
    $tData['eaccounts'] = $ElectronicAccounts->getAllActive();
//        debug($tData['currencies']);
    $tData['baseCurrency'] = $Currencies->find(['base' => 'yes'])[0];
    $tData['sales'] = $sales;
    $tData['opening_outstandings'] = $opening_outstandings;
    $data['content'] = loadTemplate('credit_payment.tpl.php', $tData);
}

if ($action == 'credit_payment_save') {
//    debug($_POST);

    $payment = $_POST['payment'];
    $grandAmountToPay = $_POST['grandAmountToPay'];
    $offset_amount = (float)$_POST['offset_amount'];
    $received_amount = removeComma($_POST['received_amount']);
    $sales = $_POST['sales'];
    $opening = $_POST['opening'];

    validate($payment);

    //for main payment
    $payment['paid_totalmount'] = $received_amount + $offset_amount;
    $payment['source'] = SalesPayments::SOURCE_RECEIPT;
    $payment['createdby'] = $_SESSION['member']['id'];
//    debug([$_POST,$payment]);
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    $_SESSION['delay'] = 5000;
    try {
        if (empty($sales) && empty($opening)) throw new Exception('Choose at least one invoice is required for payment!');
        if (!$grandAmountToPay) throw new Exception('At least one invoice is required for payment!');

        if ($Salespayments->find(['token' => $payment['token']])) throw new Exception("System found this form is already submitted, Payments canceled to avoid duplicate payments!");

        $payment['transfer_tally'] = CS_TALLY_TRANSFER && $payment['receipt_type'] == SalesPayments::RECEIPT_TYPE_TRA;
//        debug($payment);
        $Salespayments->insert($payment);
        $salespaymentId = $Salespayments->lastId();

        //if offsetting from advance payment
        if (isset($_POST['offset_advance'])) {
            $advanceBalances = $AdvancePayments->advancePaymentBalances($payment['clientid'], $payment['currencyid']);
            $totalBalance = array_sum(array_column($advanceBalances, 'remaining_advance'));

            if ($offset_amount > $totalBalance || $totalBalance <= 0) throw new Exception('Payment cant be made, Offset amount not found!, Code P0003');

            //distribute used advances
            foreach ($advanceBalances as $a) {
                if ($a['remaining_advance'] >= $offset_amount) { //single advance covers whole offset amount
                    $SalePaymentUsedAdvances->insert([
                        'spid' => $salespaymentId,
                        'advance_id' => $a['id'],
                        'amount' => $offset_amount,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    break;
                } else {
                    $SalePaymentUsedAdvances->insert([
                        'spid' => $salespaymentId,
                        'advance_id' => $a['id'],
                        'amount' => $a['remaining_advance'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $offset_amount -= $a['remaining_advance'];
                }
                if ($offset_amount == 0) break;
            }
        }

        // normal sales
        if ($sales) {
            foreach ($sales as $s) {
                $amount = removeComma($s['amount']);
                $SalespaymentDetails->insert([
                    'salespaymentId' => $salespaymentId,
                    'salesid' => $s['salesid'],
                    'amount' => $amount,
                    'base_selling' => $s['base_selling'],
                    'createdby' => $_SESSION['member']['id']
                ]);

                /*
                 * FORMULA
                 * amount paid in sales currency = foreign amount * payment buying rate / selling price to base currency
                 */
                $paidAmountInSaleCurrency = round($amount * $payment['buying_rate'] / $s['base_selling'], 2);

                $sale = $Sales->salesList($s['salesid'])[0];

                if ($paidAmountInSaleCurrency >= $sale['pending_amount']) {
                    $payment_status = [
                        'payment_status' => PAYMENT_STATUS_COMPLETE,
//                        'lastpaid_totalamount' => $sale['full_amount'], //todo check which amount to put, suggestion (last_paid + pending_amt)
                        'lastpaid_totalamount' => ($sale['lastpaid_totalamount'] + $sale['pending_amount']) //todo check which amount to put, suggestion (last_paid + pending_amt)
                    ];
                } else {
                    $payment_status = [
                        'payment_status' => PAYMENT_STATUS_PARTIAL,
                        'lastpaid_totalamount' => $sale['lastpaid_totalamount'] + $paidAmountInSaleCurrency
                    ];
                }
                $Sales->update($s['salesid'], $payment_status);
            }
        }

        // opening outstanding
        if ($opening) {
            foreach ($opening as $op) {
                $amount = removeComma($op['amount']);
                $SalespaymentDetails->insert([
                    'salespaymentId' => $salespaymentId,
                    'salesid' => $op['id'],
                    'amount' => $amount,
                    'base_selling' => $op['base_selling'],
                    'opening' => 1,
                    'createdby' => $_SESSION['member']['id']
                ]);

                /*
                 * FORMULA
                 * amount paid in sales currency = foreign amount * payment buying rate / selling price to base currency
                 */
                $paidAmountInSaleCurrency = round($amount * $payment['buying_rate'] / $op['base_selling'], 2);

                $opening_outstanding = $ClientOpeningOutstandings->getList($op['id'])[0];

                if ($paidAmountInSaleCurrency >= $opening_outstanding['pending_amount']) {
                    $payment_status = [
                        'payment_status' => PAYMENT_STATUS_COMPLETE,
                        'paid_amount' => $opening_outstanding['outstanding_amount']
                    ];
                } else {
                    $payment_status = [
                        'payment_status' => PAYMENT_STATUS_PARTIAL,
                        'paid_amount' => $opening_outstanding['paid_amount'] + $paidAmountInSaleCurrency
                    ];
                }
                $ClientOpeningOutstandings->update($op['id'], $payment_status);
            }
        }

        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirect('payments', 'client_receipts_sr');
    }

    //tally transfer
    if (CS_TALLY_DIRECT && $payment['transfer_tally']) {
        $ping = pingTally();
        if ($ping['status'] == 'error') {
            $_SESSION['error'] = $ping['msg'];
        } else {
            $result = SalesPayments::tallyPost($salespaymentId);
            if ($result['status'] == 'error') $_SESSION['error'] .= "\n" . $result['msg'];
            if ($result['status'] == 'success') $_SESSION['message'] .= "\n" . $result['msg'];
        }
    }


    redirect('payments', 'payment_receipt&id=' . $salespaymentId, ['redirect' => $_POST['redirect']]);

    /*
     * REVERSING todo check tally effect
     *
     * 1. Find sales payment master
     * 2. if has used advance delete theme
     * 3. Recalculate paid amt for each payment detail sales & opening outstanding
     * 4. Remove tally transfer
     * 5. Save canceled receipt
     *
     */
}

if ($action == 'payment_list') {
    $salesid = $_GET['id'];
    $openingid = $_GET['openingid'];
    if ($sale = $Sales->get($salesid)) {
        $sale = $Sales->salesList($salesid)[0];
        $tData['sale'] = $sale;
        $tData['payments'] = $Salespayments->getSalesPayment($salesid);
        $data['content'] = loadTemplate('payment_list.tpl.php', $tData);
    } elseif ($opening_outstanding = $ClientOpeningOutstandings->get($openingid)) {
        $opening_outstanding = $ClientOpeningOutstandings->getList($openingid)[0];
        $tData['opening_outstanding'] = $opening_outstanding;
        $tData['payments'] = $Salespayments->getSalesPayment('', $openingid);
//        debug($tData['payments']);
        $data['content'] = loadTemplate('client_opening_outstanding_payment_list.tpl.php', $tData);
    } else {
        $_SESSION['error'] = "Invoice Not Found";
        redirectBack();
    }
}

if ($action == 'client_outstanding') {
    if (Users::cannot(OtherRights::approve_other_credit_invoice) && Users::cannot(OtherRights::approve_credit))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Credit Invoice')]);

    $salesid = $_GET['salesid'];
    if (!$salesid) debug('Sale not found!');
    $sale = $Sales->salesList($salesid)[0];
    if (!$sale) debug('Sale not found!');

    if ($sale['iscreditapproved']) {
        $_SESSION['error'] = "Invoice already approved!";
        redirectBack();
    }

    $outstandingSales = $Sales->getSalesOutstanding("", $sale['clientid']);
    $openingOutstandings = $ClientOpeningOutstandings->getList('', $sale['clientid'], '', '', '', '', '', '', '', true);
    $advanceBalances = $AdvancePayments->clientAdvanceBalances($sale['clientid']);
    $outstandingApproved = array_filter($outstandingSales, function ($sale) {
        return $sale['iscreditapproved'];
    });
//    debug($advanceBalances);

    //group by currency
    $tData['totalOutstandingAmounts'] = [];
    foreach ($outstandingApproved as $item) {
        $tData['totalOutstandingAmounts'][$item['currencyname']]['amount'] += $item['pending_amount'];
        $tData['totalOutstandingAmounts'][$item['currencyname']]['base_amount'] += $item['base_pending_amount'];
        $tData['totalOutstandingAmounts'][$item['currencyname']]['base_currency'] = $item['base_currency'];
    }
    foreach ($openingOutstandings as $item) {
        $tData['totalOutstandingAmounts'][$item['currencyname']]['amount'] += $item['pending_amount'];
        $tData['totalOutstandingAmounts'][$item['currencyname']]['base_amount'] += $item['base_pending_amount'];
        $tData['totalOutstandingAmounts'][$item['currencyname']]['base_currency'] = $item['base_currency'];
    }


    $tData['pending_after_approve'] = $sale['base_pending_amount'] + array_sum(array_column($tData['totalOutstandingAmounts'], 'base_amount'));
    $tData['sale'] = $sale;
    $tData['client'] = $Clients->get($sale['clientid']);
    $tData['contacts'] = $Contacts->find(['clientid' => $sale['clientid']]);
    $tData['opening_outstandings'] = $openingOutstandings;
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['outstandingSales'] = $outstandingSales;
    $tData['advanceBalances'] = $advanceBalances;
    $data['content'] = loadTemplate('outstanding_before_invoice_approve.tpl.php', $tData);
}

if ($action == 'approve_invoice') {
    if (Users::cannot(OtherRights::approve_other_credit_invoice) && Users::cannot(OtherRights::approve_credit))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Credit Invoice')]);

    $salesid = $_GET['salesid'];

    if (!$salesid) {
        $_SESSION['error'] = "Invoice not found";
        redirectBack();
    }
    $sale = $Sales->detailedSalesList('', $salesid)[0];
    if ($sale['iscreditapproved']) {
        $_SESSION['error'] = "Invoice already approved";
        redirectBack();
    }

    foreach ($sale['products'] as $index => $product) {
        $costamount = $product['costprice'] * $product['quantity'];
        $sale['products'][$index]['margin'] = ($sale['products'][$index]['amount'] - $costamount) * 100 / $costamount;
        $sale['total_costamount'] += $costamount;
    }
    $sale['total_margin'] = bcdiv(($sale['grand_amount'] - $sale['total_costamount']) * 100 / $sale['total_costamount'], 1, 2);

    $sale['installments'] = $SalesInstallmentPlans->find(['salesid' => $salesid]);

    $outstandingSales = $Sales->getSalesOutstanding("", $sale['clientid'], '', true);
    $openingOutstandings = $ClientOpeningOutstandings->getList('', $sale['clientid'], '', '', '', '', '', '', '', true);

    $total_outstanding = array_sum(array_column($outstandingSales, 'base_pending_amount')) + array_sum(array_column($openingOutstandings, 'base_pending_amount'));

    $tData['pending_after_approve'] = $total_outstanding + $Sales->salesList($salesid)[0]['base_pending_amount'];
    $tData['advanceBalances'] = $AdvancePayments->clientAdvanceBalances($sale['clientid']);
    $tData['electronic_accounts'] = $ElectronicAccounts->getAllActive();
    $default_print_size = $_SESSION['member']['default_print_size'];
    $print_sizes = RecieptsTypes::sizes();
    if ($default_print_size == 'small') $print_sizes = array_reverse($print_sizes);
    $tData['print_sizes'] = $print_sizes;

    $tData['client'] = $Clients->get($sale['clientid']);
    $tData['contacts'] = $Contacts->find(['clientid' => $sale['clientid']]);
    $tData['invoice'] = $sale;
    $data['content'] = loadTemplate('approve_invoice.tpl.php', $tData);
}

if ($action == 'confirm_invoice_approval') {
    if (Users::cannot(OtherRights::approve_other_credit_invoice) && Users::cannot(OtherRights::approve_credit))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Credit Invoice')]);

    $_SESSION['delay'] = 6000;
    $salesid = $_POST['salesid'];
    $print_size = $_POST['print_size'];
    $invoice = $_POST['sale'];
    $dist_plan = $_POST['dist_plan'];
    $installment_plans = $_POST['installment_plans'];
    $items = $_POST['item'];
    $receivedCash = $_POST['receivedCash'];
    $payment_method = $_POST['payment_method'];
    $electronic_account = $_POST['electronic_account'];
    $credit_card_no = $_POST['credit_card_no'];


    $sale = $Sales->get($salesid);
    if (empty($sale)) {
        $_SESSION['error'] = "Invoice not found";
        redirect('payments', 'invoice_list');
        die();
    }
    if ($sale['iscreditapproved']) {
        $_SESSION['error'] = "Invoice already approved";
        redirect('payments', 'invoice_list');
        die();
    }

    $sale = $Sales->salesList($salesid)[0];
    //check stock before approving
    $result = Sales::verifyStock($salesid);

    if ($result['status'] != 'success') {
        $_SESSION['error'] = $result['message'] . ", \nInvoice cant be approved, Try editing!";
        redirect('payments', 'invoice_list');
        die();
    }

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//        debug($_POST);
        $branchid = $Locations->getBranch($sale['locationid'])['id'];
        foreach ($items as $sdi => $item) {
            $detail = $Salesdetails->getList('', '', '', '', $sdi)[0];
            if (!$detail) throw new Exception("Item details not found!");
            $item['price'] = removeComma($item['price']);
            $item['discount'] = removeComma($item['discount']);
            $item['incprice'] = removeComma($item['incprice']);

            //commission
            $hierarchicPrices = $HierarchicPrices->getProductInfoWithPrices($branchid, $detail['productid']);
            $selectedHp = [];
            foreach ($hierarchicPrices as $hp) {
                $selectedHp = $hp;
                if ($hp['exc_price'] >= $item['price']) break;
            }
            $item['commission'] = $selectedHp['commission'] ?: 0;
            $item['target'] = $selectedHp['target'] ?: 0;
            $Salesdetails->update($sdi, $item);
        }

        $Sales->update($salesid, [
            'grand_vatamount' => removeComma($invoice['grand_vatamount']),
            'grand_amount' => removeComma($invoice['grand_amount']),
            'full_amount' => removeComma($invoice['full_amount']),
            'print_size' => $print_size ? $print_size : $invoice['print_size'],
            'iscreditapproved' => 1,
            'dist_plan' => $sale['has_installment'] ? $dist_plan : '',
            'approvedate' => TIMESTAMP,
            'approvalby' => $_SESSION['member']['id']
        ]);

        RecurringBills::markAsBilled($salesid); //if invoice is from bills

        $fullAmount = removeComma($invoice['full_amount']);

        //installment plans
        $SalesInstallmentPlans->deleteWhere(['salesid' => $salesid]);
        if ($sale['has_installment']) {
            $total_plan_amount = 0;
            foreach ($installment_plans['time'] as $index => $time) {
                $time = $dist_plan == 'monthly' ? $time .= "-01" : $time;
                $amt = removeComma($installment_plans['amount'][$index]);
                $total_plan_amount += $amt;
                $SalesInstallmentPlans->insert([
                    'salesid' => $salesid,
                    'time' => $time,
                    'amount' => $amt,
                    'createdby' => $_SESSION['member']['id'],
                ]);
            }
            if ($total_plan_amount != $fullAmount) throw new Exception("Total installment amount does not match invoice amount!");

        }

        if (isset($_POST['offset_advance']) && $sale['paymenttype'] == PAYMENT_TYPE_CREDIT) { //using advance payment
            $advanceBalances = $AdvancePayments->advancePaymentBalances($sale['clientid'], $sale['currencyid']);
            $totalAdvance = array_sum(array_column($advanceBalances, 'remaining_advance'));
            if ($totalAdvance > 0) {
                $methodId = $Paymentmethods->find(['name' => PaymentMethods::CASH])[0]['id'];
                if ($totalAdvance >= $fullAmount) { //remaining advance fulfills total invoice amount
                    $usedAdvance = $fullAmount;
                    $Salespayments->insert([
                        'clientid' => $sale['clientid'],
                        'pmethod_id' => $methodId,
                        'receipt_type' => $sale['receipt_method'] == 'sr' ? SalesPayments::RECEIPT_TYPE_SR : SalesPayments::RECEIPT_TYPE_TRA,
                        'currencyid' => $sale['currencyid'],
                        'buying_rate' => 1,
                        'paid_totalmount' => $usedAdvance,
                        'source' => SalesPayments::SOURCE_RECEIPT,
                        'createdby' => $_SESSION['member']['id'],
                        'remark' => 'Advance offset from sale approval',
                        'transfer_tally' => CS_TALLY_TRANSFER && $sale['receipt_method'] != 'sr',
                    ]);
                    $salespaymentId = $Salespayments->lastId();
                    $SalespaymentDetails->insert([
                        'salespaymentId' => $salespaymentId,
                        'salesid' => $salesid,
                        'amount' => $usedAdvance,
                        'base_selling' => $sale['currency_amount'],
                        'createdby' => $_SESSION['member']['id']
                    ]);

                    $Sales->update($salesid, [
                        'payment_status' => PAYMENT_STATUS_COMPLETE,
                        'lastpaid_totalamount' => $fullAmount
                    ]);
                } else {
                    $usedAdvance = $totalAdvance;
                    $Salespayments->insert([
                        'clientid' => $sale['clientid'],
                        'pmethod_id' => $methodId,
                        'receipt_type' => $sale['receipt_method'] == 'sr' ? SalesPayments::RECEIPT_TYPE_SR : SalesPayments::RECEIPT_TYPE_TRA,
                        'currencyid' => $sale['currencyid'],
                        'buying_rate' => 1,
                        'paid_totalmount' => $usedAdvance,
                        'source' => SalesPayments::SOURCE_RECEIPT,
                        'createdby' => $_SESSION['member']['id'],
                        'remark' => 'Advance offset from sale approval',
                        'transfer_tally' => CS_TALLY_TRANSFER && $sale['receipt_method'] != 'sr',
                    ]);

                    $salespaymentId = $Salespayments->lastId();
                    $SalespaymentDetails->insert([
                        'salespaymentId' => $salespaymentId,
                        'salesid' => $salesid,
                        'amount' => $usedAdvance,
                        'base_selling' => $sale['currency_amount'],
                        'createdby' => $_SESSION['member']['id']
                    ]);
                    $Sales->update($salesid, [
                        'payment_status' => PAYMENT_STATUS_PARTIAL,
                        'lastpaid_totalamount' => $totalAdvance
                    ]);
                }

                //distribute used advances
                foreach ($advanceBalances as $a) {
                    if ($a['remaining_advance'] >= $usedAdvance) { //single advance covers whole offset amount
                        $SalePaymentUsedAdvances->insert([
                            'spid' => $salespaymentId,
                            'advance_id' => $a['id'],
                            'amount' => $usedAdvance,
                            'createdby' => $_SESSION['member']['id'],
                        ]);
                        break;
                    } else {
                        $SalePaymentUsedAdvances->insert([
                            'spid' => $salespaymentId,
                            'advance_id' => $a['id'],
                            'amount' => $a['remaining_advance'],
                            'createdby' => $_SESSION['member']['id'],
                        ]);
                        $usedAdvance -= $a['remaining_advance'];
                    }
                    if ($usedAdvance == 0) break;
                }
            }

        }

        if ($sale['paymenttype'] == PAYMENT_TYPE_CASH) {
            $receivedCash = removeComma($receivedCash);
            if ($receivedCash < $fullAmount) throw new Exception("Received Cash Not enough!, Invoice not Saved");
            $methodId = $Paymentmethods->find(['name' => $payment_method])[0]['id'];
            $currencyid = $CurrenciesRates->getCurrency_rates($sale['currency_rateid'])['currencyid'];
            //save sales payment master
            $Salespayments->insert([
                'clientid' => $sale['clientid'],
                'currencyid' => $currencyid,
                'buying_rate' => 1,
                'paid_totalmount' => $fullAmount,
                'handed_amount' => $receivedCash,
                'source' => SalesPayments::SOURCE_DIRECT,
                'pmethod_id' => $methodId,
                'eaccid' => $electronic_account,
                'credit_cardno' => $credit_card_no,
                'receipt_type' => $sale['receipt_method'] == 'sr' ? SalesPayments::RECEIPT_TYPE_SR : SalesPayments::RECEIPT_TYPE_TRA,
                'createdby' => $_SESSION['member']['id'],
                'approvalby' => $_SESSION['member']['id'],
                'approvedate' => TIMESTAMP,
            ]);
            $lastPaymentId = $Salespayments->lastId();

            //save sales payment details
            $SalespaymentDetails->insert([
                'salespaymentId' => $lastPaymentId,
                'salesid' => $salesid,
                'amount' => $fullAmount,
                'base_selling' => $sale['currency_amount'],
                'createdby' => $_SESSION['member']['id'],
            ]);

            //update sales master
            $Sales->update($salesid, [
                'payment_status' => PAYMENT_STATUS_COMPLETE,
                'lastpaid_totalamount' => $fullAmount,
            ]);
        }

        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = "Error, " . $e->getMessage();
        redirectBack();
        die();
    }


    //tally transfer
    if (CS_TALLY_DIRECT && $sale['transfer_tally']) {
        $ping = pingTally();
        if ($ping['status'] == 'error') {
            $_SESSION['error'] = $ping['msg'];
        } else {
            $result = Sales::tallyPost($salesid);
            if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
            if ($result['status'] == 'success') {
                $_SESSION['message'] .= "Invoice " . $result['msg'];
                if (isset($_POST['offset_advance'])) {
                    $receipt_result = SalesPayments::tallyPost($salespaymentId);
                    if ($receipt_result['status'] == 'error') $_SESSION['error'] .= "\n" . $receipt_result['msg'];
                    if ($receipt_result['status'] == 'success') $_SESSION['message'] .= "\n Offset " . $receipt_result['msg'];
                }

            }
        }
    }

    if ($sale['receipt_method'] == 'vfd') {
        $vfdResult = Sales::fiscalize($salesid);

        if ($vfdResult['status'] == 'success') {
            $_SESSION['message'] .= 'Invoice created and fiscalized ';
            $redirect_url = url('receipts', 'vfd', ['salesid' => $salesid, 'redirect' => base64_encode(url('payments', 'invoice_list'))]);
        } else {
            $_SESSION['error'] .= "\nInvoice Approved but not fiscalized due to " . $vfdResult['message'];
            $redirect_url = url('sales', 'failed_fiscalization');
        }
    } elseif ($sale['receipt_method'] == 'efd') {
        $result = Sales::efdFiscalize($salesid);
        if ($result['status'] == 'success') {
            $_SESSION['message'] .= "\nInvoice Approved and EFD Printed";
            $redirect_url = url('payments', 'invoice_list');
        } else {
            $_SESSION['error'] .= "\nInvoice Approved but EFD not Printed, " . $result['message'];
            $redirect_url = url('sales', 'failed_fiscalization');
        }
    } elseif ($sale['receipt_method'] == 'sr') {
        $_SESSION['message'] .= "\nInvoice Approved";
        $redirect_url = url('receipts', 'system_receipt', ['salesno' => $salesid, 'redirect' => base64_encode(url('payments', 'invoice_list'))]);
    }

    //update support order
    if ($sale['orderid']) {
        $order = Orders::$staticClass->get($sale['orderid']);
        if ($order['foreign_orderid'] && $order['order_source'] == 'support') {
            $support_response = Orders::postToSupport($sale['orderid']);
            if ($support_response['status'] == 'success') {
                $_SESSION['message'] .= "\nSupport: " . $support_response['msg'];
            } else {
                $_SESSION['error'] .= "\nSupport: " . $support_response['msg'];
            }
        }
    }

    header("Location: $redirect_url");
    die();
}

if ($action == 'payment_receipt') {
    if (!empty($paymentId = intval($_GET['id']))) {
        $paymentInfo = $Salespayments->detailedPaymentInfo($paymentId)[0];
//        debug($paymentInfo);
        if (empty($paymentInfo)) {
            debug('Payment Receipt Not Found!');
            die();
        }
        if (!($paymentInfo['received_amount'] > 0)) {
            if ($paymentInfo['advance_amount']) {
                $_SESSION['message'] = "Payment Offset";
            } else {
                $_SESSION['message'] = "No Receipt amount received";
            }
            redirect('home', 'index');
        }
//        debug($paymentInfo);
        $data['baseCurrency'] = $Currencies->find(['base' => 'yes'])[0];
        $data['paymentInfo'] = $paymentInfo;
//        debug($paymentInfo);
        $data['layout'] = 'print_sales_payment_receipt.tpl.php';
    } else {
        debug('Payment Receipt Not Found!');
    }

}

if ($action == 'save_invoice_remarks') {
    $salesid = removeSpecialCharacters($_POST['salesid']);
    $openingid = removeSpecialCharacters($_POST['openingid']);
    $remarkid = removeSpecialCharacters($_POST['remarkid']);

    //sales
    if ($salesid) {
        if (!$Sales->get($salesid)) {
            $_SESSION['error'] = "Invoice not found";
            redirectBack();
        }
        if (!$Sales->getSalesOutstanding($salesid)[0]) {
            $_SESSION['error'] = "No Outstanding found";
            redirectBack();
        }
        $Sales->update($salesid, ['invoiceremarkid' => $remarkid]);
    }

    //opening outstanding
    if ($openingid) {
        if (!$ClientOpeningOutstandings->get($openingid)) {
            $_SESSION['error'] = "Invoice not found";
            redirectBack();
        }
        $outstanding = $ClientOpeningOutstandings->getList($openingid, '', '', '',
            '', '', '', '', '', true)[0];
        if (!$outstanding) {
            $_SESSION['error'] = "No Outstanding found";
            redirectBack();
        }
        $ClientOpeningOutstandings->update($openingid, ['invoiceremarkid' => $remarkid]);
    }
    $_SESSION['message'] = "Remarks saved successfully";
    redirectBack();
}

if ($action == 'cancel_receipt') {
    Users::can(OtherRights::cancel_receipt, true);
    $receiptid = removeSpecialCharacters($_POST['receiptid']);
    $remarks = removeSpecialCharacters($_POST['remark']);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $receipt = SalesPayments::$salePaymentClass->get($receiptid);
        if (!$receipt) throw new Exception("Receipt not found");
        if ($receipt['source'] != SalesPayments::SOURCE_RECEIPT) throw new Exception("Receipt from cash invoice cant be canceled!");
        $payload = [];
        $payload = SalesPayments::$salePaymentClass->detailedPaymentInfo($receiptid)[0];
        $payload ['receipt_tally_voucherno'] = "R-" . getTransNo($receipt['id']);

        $payment_used_advances = SalePaymentUsedAdvances::$salePaymentUsedAdvanceClass->getList($receipt['id']);

        foreach ($payment_used_advances as $a) {
            $payload['usedadvance'][] = [
                'id' => $a['id'],
                'advanceid' => $a['advance_id'],
                'amount' => $a['amount'],
                'tally_voucherno' => "PA-" . getTransNo($a['id'])
            ];
        }

        //recalculating invoice paid amount
        foreach (SalespaymentDetails::$staticClass->find(['salespaymentId' => $receipt['id']]) as $d) {
            $paidAmountInSaleCurrency = round($d['amount'] * $receipt['buying_rate'] / $d['base_selling'], 2);
            $previous_payments = !$d['opening']
                ? SalesPayments::$salePaymentClass->getSalesPayment($d['salesid'])
                : SalesPayments::$salePaymentClass->getSalesPayment('', $d['salesid']);
            $previous_payments = array_filter($previous_payments, function ($p) use ($d) {
                return $p['detailid'] != $d['id'];
            });

            $previous_paid_amounts = array_map(function ($p) {
                return round($p['amount'] * $p['buying_rate'] / $p['base_selling'], 2); //convert to invoice currency
            }, $previous_payments);
            $total_previous_paid = array_sum($previous_paid_amounts);
            $total_previous_paid = $total_previous_paid < 0 ? 0 : $total_previous_paid;
            if ($d['opening']) {//opening outstanding
                $ClientOpeningOutstandings->update($d['salesid'], [
                    'payment_status' => $total_previous_paid > 0 ? PAYMENT_STATUS_PARTIAL : PAYMENT_STATUS_PENDING,
                    'paid_amount' => $total_previous_paid
                ]);
            } else {//sales
                $Sales->update($d['salesid'], [
                    'lastpaid_totalamount' => $total_previous_paid
                ]);

                $sale = $Sales->salesList($d['salesid'])[0];
                $Sales->update($d['salesid'], [
                    'payment_status' => $sale['pending_amount'] > 0 && $total_previous_paid > 0 ? PAYMENT_STATUS_PARTIAL : PAYMENT_STATUS_PENDING
                ]);
            }
        }

        $trxnos = array_column($payment_used_advances, 'tally_trxno');
        TallyTransfers::$tallyTransferclass->deleteWhereMany(['trxno' => $trxnos]);//delete previous used advances tally transfer
        SalePaymentUsedAdvances::$salePaymentUsedAdvanceClass->deleteWhere(['spid' => $receipt['id']]);

        TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $receipt['tally_trxno']]); //delete receipt tally transfer
        SalespaymentDetails::$staticClass->deleteWhere(['salespaymentId' => $receipt['id']]); //delete payment details
        SalesPayments::$salePaymentClass->deleteWhere(['id' => $receipt['id']]); //delete payment master

        CanceledReceipts::$staticClass->insert([
            'receiptno' => $receipt['id'],
            'tally_voucherno' => $payload ['receipt_tally_voucherno'],
            'source' => 'receipt',
            'currencyid' => $receipt['currencyid'],
            'amount' => $receipt['paid_totalmount'],
            'remarks' => $remarks,
            'createdby' => $_SESSION['member']['id'],
            'payload' => base64_encode(json_encode($payload)),
        ]);
//        debug([$payload, base64_encode(json_encode($payload))], 1);

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Receipt canceled successfully";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}

if ($action == 'ajax_receiptTallyTransaction') {
    $receiptid = removeSpecialCharacters($_GET['receiptid']);
    try {
        $receipt = SalesPayments::$salePaymentClass->get($receiptid);
        if (!$receipt) throw new Exception("Receipt not found");
        $tally_transfers = [];
        if ($receipt['tally_post']) {
            $tally_transfers[] = [
                'voucher_type' => 'Receipt',
                'voucherno' => "R-" . getTransNo($receipt['id'])
            ];
            foreach (SalePaymentUsedAdvances::$salePaymentUsedAdvanceClass->getList($receipt['id']) as $a) {
                $tally_transfers[] = [
                    'voucher_type' => 'Payment',
                    'voucherno' => "PA-" . getTransNo($a['id'])
                ];
            }
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
    $data['content'] = $result;
}