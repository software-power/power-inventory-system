<?

if ($action == 'list') {
    Users::isAllowed();
    $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    $locationid = $_GET['locationid'];
    $clientid = $_GET['clientid'];
    $invoiceno = $_GET['invoiceno'];
    $userid = $_GET['userid'];
    $return_type = $_GET['return_type'];
    $return_status = $_GET['return_status'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $returnno = $_GET['returnno'];

    $userid = Users::can(OtherRights::approve_other_credit_note) ? $userid : $_SESSION['member']['id'];

    if ($returnno) {
        $fromdate = $todate = '';
    };
    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($userid) {
        $tData['creator'] = $creator = $Users->get($userid);
        $title[] = "Issued by: " . $creator['name'];
    }
    if ($return_type) $title[] = "Return type: " . $return_type;
    if ($return_status) $title[] = "Status: " . $return_status;

    if ($invoiceno) {
        $fromdate = $todate = '';
        $title[] = "Invoice no: " . $invoiceno;
    }

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;


    $list = $SalesReturns->getList($returnno, '', $invoiceno, $return_type, $return_status, $clientid, $userid, $fromdate, $todate, $locationid, $branchid);
//    debug($list);
    $tData['list'] = $list;
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['branches'] = Users::can(OtherRights::approve_other_credit_note) ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $data['content'] = loadTemplate('credit_notes_list.tpl.php', $tData);
}

if ($action == 'issue_credit_note') {
    Users::can(OtherRights::issue_credit_note, true);
    $salesid = $_POST['salesid'];
    $returnid = $_POST['returnid'];
    $return_type = $_POST['return_type'];
    $description = $_POST['description'];

    try {
        if (!$sale = $Sales->get($salesid)) throw new Exception("Invoice not found!");
        if (!$sale['iscreditapproved']) throw new Exception("Invoice not approved!");
        $converted = $Sales->countWhere(['previd' => $salesid]) > 0;
        if ($converted) throw new Exception("Invoice converted cant issue credit note");

        if ($return_type == SalesReturns::TYPE_FULL) {
            if ($previous_returns = $SalesReturns->getList('', $salesid, '', '', 'approved')) throw new Exception("Invoice already have credit notes, cant do full return!");

            SalesReturns::fullInvoiceReturn($salesid, $description);
            $_SESSION['message'] = "Credit note created successfully, waiting approval!";
            redirect('sales', 'view_invoice', ['salesid' => $salesid]);
        }

        if ($returnid) {
            $salereturn = $SalesReturns->getList($returnid)[0];
            if ($salereturn['return_status'] == 'approved') {
                $_SESSION['error'] = "Credit note already approved cant be edited!";
                redirectBack();
            }
            if ($salereturn['return_status'] == 'canceled') {
                $_SESSION['error'] = "Credit note canceled cant be edited!";
                redirectBack();
            }
//            debug($salereturn);
            $return_type = $salereturn['type'];
            $tData['salereturn'] = $salereturn;
        }

        $invoice = $Sales->detailedSalesList($user = '', $salesid, $group = true)[0];
        $invoice['client'] = $Clients->get($invoice['clientid']);
        //fetch serialnos
        foreach ($invoice['products'] as $index => $product) {
            //consider previous returns
            $total_previous_return = array_sum(array_column($SalesReturnDetails->previousReturns($product['sdi']), 'qty'));
            $invoice['products'][$index]['quantity'] -= $total_previous_return;
            $invoice['products'][$index]['serialnos'] = $SerialNos->find(['sdi' => $product['sdi']]);
            $invoice['products'][$index]['prev_return'] = $total_previous_return;

            $qty = $invoice['products'][$index]['quantity'];
            $vat_rate = $invoice['products'][$index]['vat_rate'];

            if ($product['sinc']) { //selling price from inc price
                //previous return amounts
                $previous_return_amount = array_sum(array_column($SalesReturnDetails->previousPriceChange($product['sdi']), 'return_amount'));
                $invoice['products'][$index]['total_amount'] -= $previous_return_amount;
                $invoice['products'][$index]['incprice'] = $invoice['products'][$index]['total_amount'] / $qty;
                $invoice['products'][$index]['selling_price'] = round($invoice['products'][$index]['incprice'] / (1 + $vat_rate / 100), 2);

            } else {
                //previous price change
                $previous_price_change = array_sum(array_column($SalesReturnDetails->previousPriceChange($product['sdi']), 'rate'));
                $invoice['products'][$index]['selling_price'] -= $previous_price_change;

                //recalculate amounts
                $price = $invoice['products'][$index]['selling_price'];
                $invoice['products'][$index]['total_amount'] = round($price * $qty * (1 + $vat_rate / 100), 2);
            }


//            debug($invoice['products'][$index]);
//            debug($previous_price_change);


            foreach ($product['batches'] as $bi => $batch) {
                $total_prev_batch_return = array_sum(array_column($SalesReturnBatches->previousReturn($batch['batchId'], $product['sdi']), 'qty'));
                $invoice['products'][$index]['batches'][$bi]['batchSoldQty'] -= $total_prev_batch_return;
                $invoice['products'][$index]['batches'][$bi]['prev_return'] = $total_prev_batch_return;
            }
        }
        $tData['invoice'] = $invoice;
        if ($return_type == SalesReturns::TYPE_ITEM) {
            $data['content'] = loadTemplate('issue_item_credit_note.tpl.php', $tData);
        } else {
            $data['content'] = loadTemplate('issue_price_credit_note.tpl.php', $tData);
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        redirect('sales', 'view_invoice', ['salesid' => $salesid]);
    }
}

if ($action == "save_credit_note") {
    Users::can(OtherRights::issue_credit_note, true);
//    debug($_POST);
    $salereturn = $_POST['salereturn'];
    $sdis = $_POST['sdis'];
    $details = $_POST['details'];
    $serialnos = $_POST['serialnos'];
    $batches = $_POST['batches'];

    validate($salereturn);
    validate($sdis);
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $sales = $Sales->get($salereturn['salesid']);
        if (!$sales) throw new Exception("Sales no " . $salereturn['salesid'] . " not found");
        $sales = $Sales->salesList($salereturn['salesid'])[0];

        $salereturn['transfer_tally'] = CS_TALLY_TRANSFER && $sale['receipt_method'] != 'sr';

        if (!$salereturn['id']) {//new
            if ($SalesReturns->find(['token' => $salereturn['token']])) throw new Exception("System found this form is already submitted, Return canceled to avoid duplicate returns!");
            $salereturn['createdby'] = $_SESSION['member']['id'];

            $SalesReturns->insert($salereturn);
            $srid = $SalesReturns->lastId();

        } else { //updating

//            debug('editing');
            $srid = $salereturn['id'];
            if (!$SalesReturns->get($srid)) {
                $_SESSION['error'] = "Credit note not found cant be updated";
                redirect('sales_returns', 'list');
            }

            //clear old records
            foreach ($SalesReturnDetails->find(['srid' => $srid]) as $detail) {
                //clear serialnos
                $SalesReturnSerialnos->deleteWhere(['srdid' => $detail['id']]);
                //clear batches
                $SalesReturnBatches->deleteWhere(['srdid' => $detail['id']]);
            }
            //clear details
            $SalesReturnDetails->deleteWhere(['srid' => $srid]);

            $SalesReturns->update($srid, $salereturn);
        }

        foreach ($sdis as $index => $sdi) {
            $SalesReturnDetails->insert([
                'srid' => $srid,
                'sdi' => $sdi,
                'rate' => $details[$sdi]['rate'],
                'qty' => $details[$sdi]['return_qty'],
                'vat_rate' => $details[$sdi]['vat_rate'],
                'sinc' => $details[$sdi]['sinc'],
                'return_amount' => $details[$sdi]['return_amount'],
                'remarks' => $details[$sdi]['remarks'],
            ]);
            $srdid = $SalesReturnDetails->lastId();

            if ($salereturn['type'] == SalesReturns::TYPE_ITEM) { //if stock return
                //serialno
                if ($serialnos[$sdi]) {
                    foreach ($serialnos[$sdi]['snoid'] as $sindex => $snoid) {
                        $SalesReturnSerialnos->insert([
                            'srdid' => $srdid,
                            'snoid' => $snoid,
                        ]);
                    }
                }

                //batches
                if ($batches[$sdi]) {//track expire date
                    //compare batch_qty with return_qty
                    $batch_qty = array_filter($batches[$sdi]['batch_qty'], function ($qty) {
                        return $qty > 0;
                    });
                    $total_batch_qty = array_sum($batch_qty);
                    if ($details[$sdi]['return_qty'] != $total_batch_qty) throw new Exception("Total batch quantity dont match return quantity!");

                    foreach ($batches[$sdi]['batchid'] as $bi => $batchid) {
                        if ($batches[$sdi]['batch_qty'][$bi] <= 0) continue;
                        $SalesReturnBatches->insert([
                            'srdid' => $srdid,
                            'batchid' => $batchid,
                            'qty' => $batches[$sdi]['batch_qty'][$bi],
                        ]);
                    }
                } else { //not tracking
                    $soldBatches = $SalesBatches->find(['sdi' => $sdi]);
                    if (!$soldBatches) throw new Exception("CODE00B1, batches not found");
                    $remainQty = $details[$sdi]['return_qty'];
                    foreach ($soldBatches as $saleBatch) {
                        if ($saleBatch['qty'] >= $remainQty) { //single batch covers whole return qty
                            $SalesReturnBatches->insert([
                                'srdid' => $srdid,
                                'batchid' => $saleBatch['batch_id'],
                                'qty' => $remainQty,
                            ]);
                            $remainQty = 0;
                            break;
                        } else { //use another batch (qty and id)
                            $SalesReturnBatches->insert([
                                'srdid' => $srdid,
                                'batchid' => $saleBatch['batch_id'],
                                'qty' => $saleBatch['qty'],
                            ]);
                            $remainQty -= $saleBatch['qty']; //reduce remain qty
                        }
                        if ($remainQty == 0) break; //no more sale qty left
                    }
                    if ($remainQty > 0) throw new Exception("CODE00B2, not enough sold batch quantity");
                }
            }
        }
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Credit note " . ($salereturn['id'] ? 'updated' : 'saved') . " successfully, waiting approval";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirect('sales', 'view_invoice', ['salesid' => $salereturn['salesid']]);
}

if ($action == 'view') {
    $returnid = $_GET['returnno'];

    if (!$return = $SalesReturns->get($returnid)) {
        $_SESSION['error'] = "Credit note not found!";
        redirectBack();
    }
    $salereturn = $SalesReturns->getList($returnid)[0];
    $salereturn['details'] = $SalesReturnDetails->getList($returnid);
    if ($salereturn['type'] != SalesReturns::TYPE_PRICE)
        foreach ($salereturn['details'] as $index => $detail) {
            $salereturn['details'][$index]['batches'] = $SalesReturnBatches->getList($detail['id']);
            $salereturn['details'][$index]['serialnos'] = $SalesReturnSerialnos->getList($detail['id']);
        }

    //check if approval cause amount return to client
    if ($salereturn['return_status'] == 'not_approved') {
        $invoice = $Sales->salesList($salereturn['salesid'])[0];
        $salereturn['not_cash_client'] = $invoice['clientid'] != 1;
        if ($invoice['paymenttype'] == PAYMENT_TYPE_CASH || $invoice['pending_amount'] < $salereturn['total_incamount']) {
            $salereturn['require_client_payment_return'] = true;
            $invoice['pending_amount'] = $invoice['pending_amount'] >= 0 ? $invoice['pending_amount'] : 0;
            $salereturn['return_amount'] = $invoice['paymenttype'] == PAYMENT_TYPE_CASH
                ? $salereturn['total_incamount']
                : ($salereturn['total_incamount'] - $invoice['pending_amount']);
            $tData['payment_methods'] = $Paymentmethods->getReceiving();
            $tData['banks'] = $Banks->getAllActive();
            $tData['eaccounts'] = $ElectronicAccounts->getAllActive();
        }
    }


//    debug($salereturn);
    $tData['salereturn'] = $salereturn;
    $data['content'] = loadTemplate('view_credit_note.tpl.php', $tData);
}

if ($action == 'approve_credit_note') {
    if (Users::cannot(OtherRights::approve_other_credit_note) && Users::cannot(OtherRights::approve_credit_note))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Credit Note')]);

//    debug($_POST);
    $returnid = $_POST['returnid'];

    $payment = $_POST['payment'];
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (!$return = $SalesReturns->get($returnid)) throw new Exception("Credit note not found!");
        $salereturn = $SalesReturns->getList($returnid)[0];
        if ($salereturn['return_status'] == 'approved') throw new Exception("Credit note already approved!");
        if ($salereturn['return_status'] == 'canceled') throw new Exception("Credit note is canceled cant be approved!");
//        debug($salereturn);
        if ($salereturn['type'] == SalesReturns::TYPE_FULL || $salereturn['type'] == SalesReturns::TYPE_ITEM) {
            foreach ($SalesReturnDetails->getList($returnid, '', true) as $index => $detail) {
                if (!$detail['returnable']) throw new Exception("Item ({$detail['productname']}) cant return more than sold quantity!");

                //release previous chosen serial no.
                if ($serialnos = $SalesReturnSerialnos->find(['srdid' => $detail['id']])) {
                    foreach ($serialnos as $sno) {
                        $SerialNos->update($sno['snoid'], [
                            'sdi' => null,
                            'salespersonid' => null,
                            'dos' => null,
                        ]);
                    }
                }
            }
        }

        $sale = $Sales->get($salereturn['salesid']);
        //if advance overshoot
        $msg = '';
        $currencyname = $Currencies->get($salereturn['currencyid'])['name'];

        //check return_action
        if (isset($_POST['return_action'])) {
            $return_action = $_POST['return_action'];
            if ($return_action == SalesReturns::ACTION_ADVANCE) {
                $payment['createdby'] = $_SESSION['member']['id'];
                $payment['branchid'] = $Locations->getBranch($sale['locationid'])['id'];
                $payment['clientid'] = $sale['clientid'];
                $payment['currencyid'] = $salereturn['currencyid'];
                $payment['remark'] = "from sales return";
                $payment['srid'] = $returnid;
                $payment['pmethod_id'] = $Paymentmethods->find(['name' => PaymentMethods::FROM_CREDIT_NOTE])[0]['id'];
                $payment['transfer_tally'] = $salereturn['transfer_tally'];

                $AdvancePayments->insert($payment);
                $msg = "and advance amount of $currencyname " . formatN($payment['amount']) . " created";
            } elseif ($return_action == SalesReturns::ACTION_MANUAL) {
                $payment['return_amount'] = $payment['amount'];
                unset($payment['amount']);
                $SalesReturns->update($returnid, $payment);
                $msg = "and amount of $currencyname " . formatN($payment['return_amount']) . " returned to client";
            }
        }

        $SalesReturns->update($returnid, [
            'approvedby' => $_SESSION['member']['id'],
            'approval_date' => TIMESTAMP,
        ]);

        $Sales->update($salereturn['salesid'], ['total_increturn' => $sale['total_increturn'] + $salereturn['total_incamount']]);

        //mark invoice payment as complete if no sale pending amount
        $sale = $Sales->salesList($salereturn['salesid'])[0];
        if ($sale['pending_amount'] <= 0) {
            $Sales->update($salereturn['salesid'], ['payment_status' => PAYMENT_STATUS_COMPLETE]);
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Credit note approved successfully " . $msg;

        //tally transfer
        if ($salereturn['transfer_tally'] && CS_TALLY_DIRECT) {
            $ping = pingTally();
            if ($ping['status'] == 'error') {
                $_SESSION['error'] = $ping['msg'];
            } else {
                $result = SalesReturns::tallyPost($returnid);
                if ($result['status'] == 'success') $_SESSION['message'] .= $result['msg'];
                if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
            }
        }


        redirect('sales_returns', 'list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'cancel_credit_note') {
    $returnid = $_POST['returnid'];

    try {
        if (!$salereturn = $SalesReturns->get($returnid)) throw new Exception("Credit note not found!");
        if ($return['approvedby']) throw new Exception("Credit note already approved cant be canceled!");

        $SalesReturns->update($returnid, ['status' => 'inactive']);
        $_SESSION['message'] = "Credit note canceled";
        redirect('sales_returns', 'list');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'print_credit_note') {
    $returnid = $_GET['returnno'];
    if (!$return = $SalesReturns->get($returnid)) {
        $_SESSION['error'] = "Credit note not found!";
        $_SESSION['delay'] = 5000;
        redirectBack();
    }
    $salereturn = $SalesReturns->getList($returnid)[0];
    if ($salereturn['return_status'] != 'approved') {
        $_SESSION['error'] = "Credit note not approved!";
        $_SESSION['delay'] = 5000;
        redirectBack();
    }
    $salereturn['details'] = $SalesReturnDetails->getList($returnid);
//    debug($salereturn);
    $data['salereturn'] = $salereturn;
    $data['layout'] = "credit_note_print.tpl.php";
}

if ($action == 'post_tally') {
    $returnno = $_GET['returnno'];
    $ping = pingTally();
    if ($ping['status'] == 'error') {
        $_SESSION['error'] = $ping['msg'];
    } else {
        $result = SalesReturns::tallyPost($returnno);
        if ($result['status'] == 'success') $_SESSION['message'] = $result['msg'];
        if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}

