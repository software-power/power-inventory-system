<?

if ($action == 'bill_types') {
    $tData['bill_types'] = BillTypes::$staticClass->getAll();
    $tData['period_types'] = BillTypes::$staticClass->period_types();
    $data['content'] = loadTemplate('bill_types_list.tpl.php', $tData);
}

if ($action == 'bill_type_save') {
    $billtype = $_POST['billtype'];
    validate($billtype);

    if (!$billtype['id']) {
        if (BillTypes::$staticClass->find(['name' => $billtype['name']])) {
            $_SESSION['error'] = "Bill type name already exists";
            redirectBack();
        }
        BillTypes::$staticClass->insert($billtype);
    } else {
        BillTypes::$staticClass->update($billtype['id'], $billtype);
    }

    $_SESSION['message'] = "Bill type saved";
    redirectBack();
}

if ($action == 'list') {
    Users::isAllowed();
    $clientid = removeSpecialCharacters($_GET['clientid']);
    $issuedby = removeSpecialCharacters($_GET['issuedby']);
    $billmonth = $_GET['billmonth'];

    $issuedby = IS_ADMIN ? $issuedby : $_SESSION['member']['id'];

    $title = [];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($issuedby) $title[] = "Issued by: " . $Users->get($issuedby)['name'];
    if ($billmonth == "all") {
        $tData['billmonth'] = "all";
        $title[] = "All bills";
        $billmonth = "";
    } elseif ($billmonth == "nextmonth") {
        $tData['billmonth'] = "nextmonth";
        $title[] = "Next Month bills";
        $billmonth = date('Y-m', strtotime("+1 month"));
    } elseif ($billmonth == "stopped") {
        $tData['billmonth'] = "stopped";
        $title[] = "Stopped bills";
        $billmonth = "";
        $status = "inactive";
    } else {
        $tData['billmonth'] = "thismonth";
        $title[] = "This Month bills";
        $billmonth = date('Y-m');
    }


    $tData['title'] = implode(' | ', $title);

    $tData['bills'] = RecurringBills::$staticClass->getList('', $clientid, $issuedby, $billmonth, $status);
    $data['content'] = loadTemplate('recurring_bills_list.tpl.php', $tData);
}

if ($action == 'create_bill') {
    $billid = removeSpecialCharacters($_GET['billid']);
    $tData['bill_types'] = BillTypes::$staticClass->getAllActive();
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);

    if ($billid) {
        Users::can(OtherRights::edit_bill, true);

        $bill = RecurringBills::$staticClass->getList($billid);
        if (!$bill) {
            $_SESSION['error'] = "Bill not found!";
            redirectBack();
        }

        if ($bill['createdby'] != $_SESSION['member']['id'] && !IS_ADMIN) {
            $_SESSION['error'] = "You cant edit this bill";
            redirectBack();
        }

        $bill['has_sales'] = Sales::$saleClass->countWhere(['billid' => $billid, 'iscreditapproved' => 1]) > 0;
        $tData['defaultLocation'] = $Locations->get($bill['locationid']);
        $branchid = $Locations->getBranch($bill['locationid'])['id'];
        $hierarchic = $Hierarchics->get($_SESSION['member']['hierachicid']);
        $exchange_rate = $CurrenciesRates->find(['currencyid' => $bill['currencyid']])[0]['rate_amount'];  //current exchange rate

        $bill['details'] = RecurringBillDetails::$staticClass->getList($billid);
        foreach ($bill['details'] as $index => $detail) {
            $prices = $Products->getPrices($branchid, $hierarchic['level'], $detail['productid']);
            $prices['minimum'] = IS_ADMIN || $detail['non_stock'] ? 0 : $prices['minimum'];
            $bill['details'][$index]['base_price'] = round($detail['price'] * $exchange_rate, 2);
            $bill['details'][$index]['min_base_price'] = $prices['minimum'];
            $bill['details'][$index]['min_price'] = round($prices['minimum'] / $exchange_rate, 2);
            $bill['details'][$index]['min_incprice'] = round($prices['minimum'] * (1 + $detail['vat_rate'] / 100) / $exchange_rate, 2);
        }
        $tData['bill'] = $bill;
//        debug($bill);
    } else {
        Users::can(OtherRights::create_bill, true);
    }

    $data['content'] = loadTemplate('create_bill.tpl.php', $tData);
}

if ($action == 'save_bill') {
    if (Users::cannot(OtherRights::create_bill) && Users::cannot(OtherRights::edit_bill)) {
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Create or edit bill')]);
    }
//    debug($_POST);
    $bill = $_POST['bill'];
    $productIds = $_POST['productid'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $incprice = $_POST['incprice'];
    $sinc = $_POST['sinc'];
    $vat_rate = $_POST['vat_rate'];
    $print_extra = $_POST['print_extra'];
    $product_description = $_POST['product_description'];

    validate($bill);
    validate($productIds);

    $bill['total_excamount'] = removeComma($bill['total_excamount']);
    $bill['total_vatamount'] = removeComma($bill['total_vatamount']);
    $bill['total_amount'] = removeComma($bill['total_amount']);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (!$bill['id']) {
            $bill['createdby'] = $_SESSION['member']['id'];
            RecurringBills::$staticClass->insert($bill);
            $billid = RecurringBills::$staticClass->lastId();
        } else {
            $bill['id'] = removeSpecialCharacters($bill['id']);
            if (!RecurringBills::$staticClass->get($bill['id'])) throw new Exception("Bill not found for update!");

            $billid = $bill['id'];

            //create edit log
            $oldbill = RecurringBills::$staticClass->getList($billid);
            $oldbill['details'] = RecurringBillDetails::$staticClass->getList($billid);
            RecurringBillEditLogs::$staticClass->insert([
                'billid' => $billid,
                'total_amount' => $oldbill['total_amount'],
                'createdby' => $_SESSION['member']['id'],
                'payload' => base64_encode(json_encode($oldbill))
            ]);

            //update
            $bill['modifiedby'] = $_SESSION['member']['id'];
            $bill['dom'] = TIMESTAMP;
            RecurringBills::$staticClass->update($bill['id'], $bill);
            RecurringBillDetails::$staticClass->deleteWhere(['billid' => $billid]); //clear details
        }

        foreach ($productIds as $index => $productid) {
            RecurringBillDetails::$staticClass->insert([
                'billid' => $billid,
                'productid' => $productid,
                'qty' => $qty[$index],
                'vat_rate' => $vat_rate[$index],
                'price' => removeComma($price[$index]),
                'sinc' => $sinc[$index],
                'incprice' => removeComma($incprice[$index]),
                'print_extra' => in_array($productid, $print_extra),
                'extra_desc' => $product_description[$index],
            ]);
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Bill saved";
        redirect('recurring_bills', 'list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'view_bill') {
    $billid = removeSpecialCharacters($_GET['billid']);
    $bill = RecurringBills::$staticClass->get($billid);
    if (empty($bill)) debug("Bill info not found");

    $bill = RecurringBills::$staticClass->getList($billid);
    $bill['details'] = RecurringBillDetails::$staticClass->getList($billid);
    $tData['bill'] = $bill;
    $tData['billing_logs'] = RecurringBillsLogs::$staticClass->getList($billid);
//    debug( $tData['billing_logs']);
    $data['content'] = loadTemplate('view_bill.tpl.php', $tData);
}

if ($action == 'bill_clients') {
    $billids = $_POST['billid'];
    validate($billids);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $bills = RecurringBills::$staticClass->getList($billids);
        foreach ($bills as $bill) {
            if ($bill['status'] != 'active') continue;

            if ($bill['billtype'] == RecurringBills::bill_type_month) {
                $nextbilldate = date('Y-m-d', strtotime($bill['nextbilldate'] . " +{$bill['bill_interval']} month"));
            } elseif ($bill['billtype'] == RecurringBills::bill_type_year) {
                $nextbilldate = date('Y-m-d', strtotime($bill['nextbilldate'] . " +{$bill['bill_interval']} year"));
            }
            $lastbilldate = fDate($bill['nextbilldate'], 'Y-m-d');
            $billmonth = fDate($lastbilldate, 'F Y');

            //todo check if billable this month
            $currencyrate = Currencies_rates::$staticClass->find(['currencyid' => $bill['currencyid']])[0];
            if ($bill['non_stock']) {
                $salesid = Sales::generateInvoiceNo($bill['locationid'], 'sr');
                if(empty($salesid)) throw new Exception("Invoice number failed to generate!");

                Sales::$saleClass->update($salesid,[
                    'currency_rateid' => $currencyrate['id'],
                    'currency_amount' => $currencyrate['rate_amount'],
                    'clientid' => $bill['clientid'],
                    'locationid' => $bill['locationid'],
                    'billid' => $bill['billid'],
                    'payment_status' => PAYMENT_STATUS_PENDING,
                    'paymenttype' => PAYMENT_TYPE_CREDIT,
                    'source' => Sales::SOURCE_DETAILED,
                    'receipt_method' => 'sr',
                    'print_size' => 'A4',
                    'iscreditapproved' => 1,
                    'approvalby' => $_SESSION['member']['id'],
                    'approvedate' => TIMESTAMP,
                    'grand_amount' => $bill['total_excamount'],
                    'grand_vatamount' => $bill['total_vatamount'],
                    'full_amount' => $bill['total_amount'],
                    'credit_days' => 30,
                    'salespersonid' => $_SESSION['member']['id'],
                    'createdby' => $_SESSION['member']['id'],
                    'duedate' => date('Y-m-d', strtotime(TODAY . " +30 days")),
                    'token' => unique_token(),
                    'description' => $bill['remarks'] . "Bill for month $billmonth\r\n\r\n{$bill['billtypename']}",
                    'internal_remarks' => $bill['internal_remarks'] . "Bill for month $billmonth\r\n\r\n{$bill['billtypename']}",
                ]);

                $billdetails = RecurringBillDetails::$staticClass->getList($bill['billid']);
                foreach ($billdetails as $item) {
                    $product = Products::$productClass->get($item['productid']);
                    if (!$product['non_stock']) throw new Exception("Bill no {$bill['billid']},\n{$product['name']} is not non stock item");
                    $hierarchicPrices = $HierarchicPrices->getProductInfoWithPrices($bill['branchid'], $item['productid']);
                    $selectedHp = end($hierarchicPrices);
                    Salesdetails::$saleDetailsClass->insert([
                        'salesid' => $salesid,
                        'productid' => $item['productid'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                        'sinc' => $item['sinc'],
                        'incprice' => $item['incprice'],
                        'hidden_cost' => 0,
                        'vat_rate' => $item['vat_rate'],
                        'print_extra' => $item['print_extra'],
                        'base_percentage' => $product['baseprice'],
                        'points' => $product['points'],
                        'commission' => $selectedHp['commission'] ?: 0,
                        'target' => $selectedHp['target'] ?: 0,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $sdi = Salesdetails::$saleDetailsClass->lastId();

                    if ($item['print_extra']) {
                        Salesdescriptions::$staticClass->insert([
                            'sdi' => $sdi,
                            'description' => $item['extra_desc'],
                        ]);
                    }
                }

                RecurringBills::$staticClass->update($bill['billid'], [
                    'lastbilldate' => $lastbilldate,
                    'nextbilldate' => $nextbilldate
                ]);

                RecurringBillsLogs::$staticClass->insert([
                    'billid' => $bill['billid'],
                    'salesid' => $salesid,
                    'billmonth' => $lastbilldate,
                    'createdby' => $_SESSION['member']['id'],
                    'remarks' => "Bill for month $billmonth\r\n\r\n{$bill['billtypename']}, {$bill['bill_interval']} {$bill['billtype']}"
                ]);
            } else {
                Orders::$staticClass->insert([
                    'billid' => $bill['billid'],
                    'currencyid' => $bill['currencyid'],
                    'order_value' => $bill['total_amount'],
                    'print_size' => 'A4',
                    'validity_days' => CS_ORDER_VALID_DAYS,
                    'createdby' => $_SESSION['member']['id'],
                    'clientid' => $bill['clientid'],
                    'locid' => $bill['locationid'],
                    'remarks' => $bill['remarks'] . "Bill for month $billmonth\r\n\r\n{$bill['billtypename']}",
                    'internal_remarks' => $bill['internal_remarks'] . "Bill for month $billmonth\r\n\r\n{$bill['billtypename']}",
                ]);
                $orderid = Orders::$staticClass->lastId();

                $billdetails = RecurringBillDetails::$staticClass->getList($bill['billid']);
                foreach ($billdetails as $item) {
                    $Orderdetails->insert([
                        'orderid' => $orderid,
                        'productid' => $item['productid'],
                        'qty' => $item['qty'],
                        'price' => $item['price'],
                        'incprice' => $item['incprice'],
                        'sinc' => $item['sinc'],
                        'vat_rate' => $item['vat_rate'],
                        'print_extra' => $item['print_extra'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $odi = $Orderdetails->lastId();

                    if ($item['print_extra']) {
                        $Salesdescriptions->insert([
                            'odi' => $odi,
                            'description' => $item['extra_desc'],
                        ]);
                    }
                }
            }
        }
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Clients billed successfully";
        redirect('recurring_bills', 'list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'stop_billing') {
    $billid = removeSpecialCharacters($_POST['billid']);
    $remarks = $_POST['remarks'];
    $bill = RecurringBills::$staticClass->get($billid);
    if (!$bill) {
        $_SESSION['error'] = "Bill not found!";
        redirectBack();
    }
    if ($bill['status'] != 'active') {
        $_SESSION['error'] = "Billing was already stopped!";
        redirectBack();
    }

    $bill = RecurringBills::$staticClass->getList($billid);
    $nextbilldate = fDate($bill['nextbilldate'], 'Y-m-d');
    $billmonth = fDate($nextbilldate, 'F Y');

    RecurringBillsLogs::$staticClass->insert([
        'billid' => $bill['billid'],
        'billmonth' => $nextbilldate,
        'createdby' => $_SESSION['member']['id'],
        'remarks' => "Billing stopped for month $billmonth\r\n\r\nReason: $remarks\r\n\r\n{$bill['billtypename']}, {$bill['bill_interval']} {$bill['billtype']}"
    ]);

    RecurringBills::$staticClass->update($billid, ['status' => 'inactive']);
    $_SESSION['message'] = "Billing stopped";
    redirectBack();
}

if ($action == 'enable_billing') {
    $billid = removeSpecialCharacters($_POST['billid']);
    $bill = RecurringBills::$staticClass->get($billid);
    if (!$bill) {
        $_SESSION['error'] = "Bill not found!";
        redirectBack();
    }
    RecurringBills::$staticClass->update($billid, ['status' => 'active']);
    $_SESSION['message'] = "Billing enabled";
    redirectBack();
}


//ajax
if ($action == 'ajax_getBillDetails') {
    $billid = removeSpecialCharacters($_GET['billid']);
    $result['status'] = 'success';
    try {
        $bill = RecurringBills::$staticClass->get($billid);
        if (empty($bill)) throw new Exception("Bill info not found");
        $details = RecurringBillDetails::$staticClass->getList($billid);
        $details = array_map(function ($d){
            $d['price']=formatN($d['price']);
            $d['incprice']=formatN($d['incprice']);
            $d['incamount']=formatN($d['incamount']);
            $d['excamount']=formatN($d['excamount']);
            $d['vatamount']=formatN($d['vatamount']);
            return $d;
        }, $details);
        $result['data']=$details;
    } catch (Exception $e) {
        $result = ['status' => 'error', 'msg' => $e->getMessage()];
    }
//    debug($result);
    $data['content'] = $result;
}
