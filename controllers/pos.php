<?

if ($action == 'quick_sales' || $action == 'quick_order') {
    Users::isAllowed();
    $data['pagetitle'] = "Quick Sale";
    if ($action == 'quick_order') {
        $tData['QUICK_ORDER_MODE'] = true;
        $data['pagetitle'] = "Quick Order";
    }

    $data['layout'] = 'layout_blank.tpl.php';
    $tData['plugins'] = true;
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['categories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive('name');
    $user_receipt = $Users->get($_SESSION['member']['id'])['receipt_type'];
    $default_print_size = $_SESSION['member']['default_print_size'];

    $tData['electronic_accounts'] = $ElectronicAccounts->getAllActive();
    $tData['receipts'] = $user_receipt == 'both' ?
        $RecieptsTypes->getAllActive()
        : $RecieptsTypes->find(['name' => $user_receipt, 'status' => 'active']);

    $print_sizes = RecieptsTypes::sizes();
    if ((!$default_print_size && CS_SR_TYPE != SR_TYPE_A4) || $default_print_size == 'small') $print_sizes = array_reverse($print_sizes);
    $tData['print_sizes'] = $print_sizes;

    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $tData['defaultClient'] = $Clients->get(1);

    $salesid = $_GET['salesid'];
    $orderid = $_GET['order_number'];
//    $proformaid = $_GET['proforma_no'];

    $tData['salesPerson'] = $_SESSION['member'];
    if ($orderid) {
        if ($order = $Orders->withDetails($orderid)[0]) {
            if (!$order) {
                $_SESSION['error'] = "Order not found!";
                redirectBack();
                die();
            }

            if ($order['sales_status'] == Orders::STATUS_CLOSED) {
                $_SESSION['error'] = "Order already closed";
                redirectBack();
            }
            if (!$tData['QUICK_ORDER_MODE'] && ($order['order_status'] == Orders::STATUS_CANCELED || $order['order_status'] == Orders::STATUS_INVALID)) {
                $_SESSION['error'] = "Order can not be processed";
                redirectBack();
            }

            $tData['defaultClient'] = $Clients->get($order['clientid']);
            $tData['defaultLocation'] = $Locations->get($order['locid']);
            $branchid = $Locations->getBranch($order['locid'])['id'];
            $currency_rate = $CurrenciesRates->find(['currencyid' => $order['currencyid']])[0];
            foreach ($order['details'] as $index => $detail) {
                $order['details'][$index]['unitname'] = $Products->getList($detail['productid'])[0]['unitname'];
                //find hierarchic prices
                $hierarchics = $Hierarchics->get($_SESSION['member']['hierachicid']);
                $priceList = $Products->getPrices($branchid, $hierarchics['level'], $detail['productid']);
                $priceList['costprice'] = $detail['non_stock'] ? 0 : $priceList['costprice']; //no cost for non-stock product
                $priceList['max_quicksale_disc_percent'] = $detail['non_stock'] ? 100 : $priceList['max_quicksale_disc_percent']; //no cost for non-stock product


                $order['details'][$index]['base_price'] = round($detail['price'] * $currency_rate['rate_amount'], 2);
                $order['details'][$index]['base_incprice'] = $detail['sinc']
                    ? round($detail['incprice'] * $currency_rate['rate_amount'], 2)
                    : round(addTAX($detail['price'], $detail['vat_rate']) * $currency_rate['rate_amount'], 2);
                $order['details'][$index]['hidden_cost'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
                $order['details'][$index]['base_hidden_cost'] = $priceList['costprice'];


                $order['details'][$index]['max_discount_percent'] = $priceList['max_quicksale_disc_percent'];
                $order['details'][$index]['max_discount'] = round($detail['price'] * $priceList['max_quicksale_disc_percent'] / 100, 2);
                $order['details'][$index]['discpercent'] = $detail['price'] <= 0 ? 0 : round($detail['discount'] / $detail['price'] * 100, 2);

                if ($detail['non_stock']) continue;

                $stockid = $Stocks->find(['productid' => $detail['productid'], 'locid' => $order['locid']])[0]['id'];
                $stockBatches = [];
                if ($stockid) {
                    $order['details'][$index]['stockid'] = $stockid;
                    $stockBatches = $Stocks->calcStock(
                        $order['locid'], $stockid, "",
                        "", "", "", "",
                        "", "", "", "", "", "",
                        "", "", "", "", false, true,
                        '', '', true, true
                    );
                    $stockBatches = array_values($stockBatches)[0];
//                    debug($stockBatches);
                }

                $order['details'][$index]['stock_qty'] = $stockBatches['total'] ?? 0;
                $order['details'][$index]['stock_batches'] = $stockBatches['batches'];
            }
            $tData['order'] = $order;
//            debug($order);
        }
//        debug($order);
    }
    if ($proformaid) {
        if (!$proforma = $Proformas->withDetails($proformaid)[0]) {
            $_SESSION['error'] = "Proforma not found!";
            redirectBack();
            die();
        }

        if ($proforma['sales_status'] == Orders::STATUS_CLOSED && !isset($_GET['reuse'])) {
            $_SESSION['error'] = "Proforma already closed";
            redirectBack();
        }
        define('EXCEPT_PROFORMA', $proformaid);
        $tData['defaultClient'] = $Clients->get($proforma['clientid']);
        $tData['defaultLocation'] = $Locations->get($proforma['locid']);
        $branchid = $Locations->getBranch($proforma['locid'])['id'];
        $currency_rate = $CurrenciesRates->find(['currencyid' => $proforma['currencyid']])[0];
        foreach ($proforma['details'] as $index => $detail) {
            $proforma['details'][$index]['unitname'] = $Products->getList($detail['productid'])[0]['unitname'];
            //find hierarchic prices
            $hierarchics = $Hierarchics->get($_SESSION['member']['hierachicid']);
            $priceList = $Products->getPrices($branchid, $hierarchics['level'], $detail['productid']);
            $priceList['costprice'] = $detail['non_stock'] ? 0 : $priceList['costprice']; //no cost for non-stock product
            $priceList['max_quicksale_disc_percent'] = $detail['non_stock'] ? 100 : $priceList['max_quicksale_disc_percent']; //no cost for non-stock product

            $proforma['details'][$index]['incprice'] = $detail['incprice'] ?: addTAX($detail['price'], $detail['vat_rate']);

            $proforma['details'][$index]['base_price'] = round($detail['price'] * $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['base_incprice'] = round($detail['incprice'] * $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['hidden_cost'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['base_hidden_cost'] = $priceList['costprice'];

            $proforma['details'][$index]['max_discount_percent'] = $priceList['max_quicksale_disc_percent'];
            $proforma['details'][$index]['max_discount'] = round($detail['price'] * $priceList['max_quicksale_disc_percent'] / 100, 2);
            $proforma['details'][$index]['discpercent'] = $detail['price'] <= 0 ? 0 : round($detail['discount'] / $detail['price'] * 100, 2);

            if ($detail['non_stock']) continue;

            $stockid = $Stocks->find(['productid' => $detail['productid'], 'locid' => $proforma['locid']])[0]['id'];
            $stockBatches = [];
            if ($stockid) {
                $proforma['details'][$index]['stockid'] = $stockid;
                $stockBatches = $Stocks->calcStock(
                    $proforma['locid'], $stockid, "",
                    "", "", "", "",
                    "", "", "", "", "", "",
                    "", "", "", "", false, true,
                    '', '', true, true
                );
                $stockBatches = array_values($stockBatches)[0];
            }


            $proforma['details'][$index]['stock_qty'] = $stockBatches['total'] ?? 0;
            $proforma['details'][$index]['stock_batches'] = $stockBatches['batches'];

//            debug($order['details'][$index]);
        }
        $tData['proforma'] = $proforma;
//            debug($proforma);
    }
    if ($salesid) {
        $sale = $Sales->get($salesid);
        if (!$sale) {
            $_SESSION['error'] = 'Sale not Found!';
            redirect('home', 'index');
        }

        if ($sale['iscreditapproved']) { //block if approved
            $_SESSION['error'] = 'Invoice already approved cant be edited';
            $_SESSION['delay'] = 5000;
            redirect('home', 'index');
        }

        if ($sale['has_combine']) { //block if has combination
            $_SESSION['error'] = 'Invoice has combination can not be edited';
            $_SESSION['delay'] = 5000;
            redirect('home', 'index');
        }

        if (!$sale['op_reuse']) define('EXCEPT_PROFORMA', $sale['proformaid']);  //release stock held by this proforma
        $tData['defaultClient'] = $Clients->get($sale['clientid']);
        $tData['salesPerson'] = $Users->get($sale['createdby']);
        $tData['defaultLocation'] = $Locations->get($sale['locationid']);
        $branchid = $Locations->getBranch($sale['locationid'])['id'];

        $currency_rate = $CurrenciesRates->getCurrency_rates($sale['currency_rateid']);
//        debug($currency_rate);
        $sale['currencyid'] = $currency_rate['currencyid'];
        $sale['details'] = $Salesdetails->find(['salesid' => $salesid]);
        foreach ($sale['details'] as $i => $detail) {
            $stock = $Stocks->get($detail['stockid']);
            $product = $Products->getList($stock['productid'] ?: $detail['productid'])[0];
            $sale['details'][$i]['productid'] = $product['id'];
            $sale['details'][$i]['productname'] = $product['name'];
            $sale['details'][$i]['description'] = $product['description'];
            $sale['details'][$i]['non_stock'] = $product['non_stock'];
            $sale['details'][$i]['trackserialno'] = $product['trackserialno'];
            $sale['details'][$i]['validate_serialno'] = $product['validate_serialno'];
            $sale['details'][$i]['track_expire_date'] = $product['track_expire_date'];
            $sale['details'][$i]['prescription_required'] = $product['prescription_required'];
            $sale['details'][$i]['unitname'] = $product['unitname'];

            $category = $Categories->get($product['categoryid']);
            $sale['details'][$i]['vat_rate'] = $category['vat_percent'];
            $sale['details'][$i]['qty'] = $detail['quantity'];

            //find hierarchic prices
            $hierarchics = $Hierarchics->get($_SESSION['member']['hierachicid']);
            $priceList = $Products->getPrices($branchid, $hierarchics['level'], $product['id']);
            $priceList['costprice'] = $product['non_stock'] ? 0 : $priceList['costprice']; //no cost for non-stock product
            $priceList['max_quicksale_disc_percent'] = $product['non_stock'] ? 100 : $priceList['max_quicksale_disc_percent']; //no cost for non-stock product
            $sale['details'][$i]['costprice'] = $priceList['costprice'];

            $sale['details'][$i]['base_price'] = round($detail['price'] * $sale['currency_amount'], 2);
            $sale['details'][$i]['base_incprice'] = $detail['sinc']
                ? round($detail['incprice'] * $currency_rate['rate_amount'], 2)
                : round(addTAX($detail['price'], $detail['vat_rate']) * $currency_rate['rate_amount'], 2);
            $sale['details'][$i]['hidden_cost'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
            $sale['details'][$i]['base_hidden_cost'] = $priceList['costprice'];
//            debug($priceList);
            $sale['details'][$i]['max_discount_percent'] = $priceList['max_quicksale_disc_percent'];
            $sale['details'][$i]['max_discount'] = round($detail['price'] * $priceList['max_quicksale_disc_percent'] / 100, 2);
            $sale['details'][$i]['discpercent'] = $detail['price'] <= 0 ? 0 : round($detail['discount'] / $detail['price'] * 100, 2);
            $sale['details'][$i]['sale_item'] = true;

            if ($product['non_stock']) continue;

            $stockBatches = [];
            $stockBatches = $Stocks->calcStock(
                $sale['locationid'], $detail['stockid'], "",
                "", "", "", "",
                "", "", "", "", "", "",
                "", "", "", "", false, true,
                '', '', true, true
            );
            $stockBatches = array_values($stockBatches)[0];
            $sale['details'][$i]['stock_qty'] = $stockBatches['total'] ?? 0;
            $sale['details'][$i]['stock_batches'] = $stockBatches['batches'];
            $sale['details'][$i]['prescription'] = $SalesPrescriptions->getInfo($detail['id']);
            $sale['details'][$i]['serialnos'] = $SerialNos->find(['sdi' => $detail['id']]);

            //release previous chosen serial no.
            $SerialNos->updateWhere(['sdi' => $detail['id']], [
                'sdi' => null,
                'salespersonid' => null,
                'dos' => null,
            ]);
        }
        $tData['sale'] = $sale;
//        debug($sale);
    }
    $data['content'] = loadTemplate('quick_sale.php', $tData);
}

if ($action == 'save_quick_order') {
    if (empty($_POST)) redirect('pos', 'quick_sales');

//    debug($_POST);

    //NB data came from quick sales
    $sales = $_POST['sales'];
    $productIds = $_POST['productid'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $incprice = $_POST['incprice'];
    $sinc = $_POST['sinc'];
    $required_price = $_POST['required_price'];
    $vat_rate = $_POST['vat_rate'];
    $order_print_size = $_POST['order_print_size'];

    validate($sales);
    validate($productIds);

    $currencyid = $CurrenciesRates->getCurrency_rates($sales['currency_rateid'])['currencyid'];
    $order = [
        'id' => $sales['orderid'],
        'clientid' => $sales['clientid'],
        'currencyid' => $currencyid,
        'proformaid' => $sales['proformaid'],
        'op_reuse' => $sales['op_reuse'],
        'order_value' => $sales['full_amount'],
        'locid' => $sales['locationid'],
        'type' => Orders::TYPE_QUICK,
        'print_size' => $order_print_size,
    ];

    if (!$order['id']) {//new
        $order['deptid'] = $_SESSION['member']['deptid'];
        $order['createdby'] = $_SESSION['member']['id'];

        //New
        $Orders->insert($order);
        $orderid = $Orders->lastId();

        //update proforma
        if ($order['proformaid']) {
            $Proformas->update($order['proformaid'], [
                'sales_status' => Orders::STATUS_CLOSED,
                'sales_closedby' => $_SESSION['member']['id']
            ]);
        }
    } else {//updating

        Users::can(OtherRights::edit_order, true);
        $orderid = $order['id'];
        $order['updated_at'] = TIMESTAMP;
        $order['updated_by'] = $_SESSION['member']['id'];
        $Orders->update($orderid, $order);

        //clear previous details
        $Orderdetails->deleteWhere(['orderid' => $orderid]);
    }

    //details
    foreach ($productIds as $index => $productid) {
        $Orderdetails->insert([
            'orderid' => $orderid,
            'productid' => $productid,
            'qty' => $qty[$index],
            'price' => removeComma($price[$index]),
            'sinc' => $sinc[$index],
            'incprice' => removeComma($incprice[$index]),
            'required_price' => removeComma($required_price[$index]),
            'vat_rate' => $vat_rate[$index],
            'createdby' => $_SESSION['member']['id'],
        ]);
    }

    $_SESSION['message'] = "Order No. $orderid " . ($order['id'] ? 'updated' : 'created') . " successfully";
    redirect('receipts', 'print_order', ['orderno' => $orderid, 'redirect' => base64_encode(url('pos', 'quick_order'))]);
}

if ($action == 'save_quick_sales_new') {
//    debug($_POST);
    $sales = $_POST['sales'];
    $sr_size = $_POST['sr_size'];
    $exceed_limit = $_POST['exceed_limit'];
    $paid_totalamount = $_POST['paid_totalamount'];
    $payment_method = $_POST['payment_method'];
    $electronic_account = $_POST['electronic_account'];
    $credit_card_no = $_POST['credit_card_no'];
    $stockIds = $_POST['stockid'];
    $productIds = $_POST['productid'];
    $product_description = $_POST['description'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $incprice = $_POST['incprice'];
    $sinc = $_POST['sinc'];
    $discount = $_POST['discount'];
    $hidden_cost = $_POST['hidden_cost'];
    $vat_rate = $_POST['vat_rate'];
    $batches = $_POST['batch'];
    $prescriptions = $_POST['prescription'];
    $serialnos = $_POST['serialno'];
    $keyboardonly = isset($_POST['keyboardonly']);

    validate($sales);
    validate($productIds);
    validate($stockIds);

    $paid_totalamount = removeComma($paid_totalamount);
//    debug($paid_totalamount);

    //if cash payment check if received amount is enough
    $CASH_APPROVED = $sales['paymenttype'] == PAYMENT_TYPE_CASH && $exceed_limit == '';
    if ($CASH_APPROVED) {
        if ($paid_totalamount < $sales['full_amount']) {
            $_SESSION['error'] = "Received Cash Not enough!, Sale not saved";
            redirectBack();
            die();
        }
    }

    global $db_connection;
    mysqli_begin_transaction($db_connection);//sales and payment
    try {
        //sales
        if ($sales['paymenttype'] == PAYMENT_TYPE_CREDIT) {
            $sales['credit_days'] = $sales['credit_days'] ?: 30;
            $sales['duedate'] = date('Y-m-d', strtotime(TODAY . " +" . $sales['credit_days'] . " days"));
        }
        $sales['transfer_tally'] = CS_TALLY_TRANSFER && $sales['receipt_method'] != 'sr'; //for tally transfer


        if (CS_VFD_TYPE == VFD_TYPE_ZVFD) {//set zvfd tax type
            if (empty(CS_ZVFD_TAXCATEGORYID)) throw new Exception("ZVFD TAX category not SET!");
            $taxCategory = Categories::$categoryClass->get(CS_ZVFD_TAXCATEGORYID);
            if (empty($taxCategory['zvfd_tax_type']) || $taxCategory['zvfd_tax_type'] <= 0) throw new Exception("ZVFD TAX Type not set!");
            $sales['zvfd_tax_type'] = $taxCategory['zvfd_tax_type'];
        }

        if (!$sales['id']) {//new
            if ($Sales->find(['token' => $sales['token']])) throw new Exception("System found this form is already submitted, Sales canceled to avoid duplicate invoices!");
            $sales['source'] = Sales::SOURCE_QUICK;
            $sales['salespersonid'] = $sales['createdby'] = $_SESSION['member']['id'];
            if ($exceed_limit == 1 && $sales['paymenttype'] == PAYMENT_TYPE_CASH) $sales['internal_remarks'] .= "\n\n Exceeded sale limit of " . formatN($_SESSION['member']['sale_limit']);
            if ($sales['receipt_method'] == 'sr') $sales['description'] .= ($sales['description'] ? "\r\n" : "") . CS_SR_EXTRA_REMARKS;
            $salesid = Sales::generateInvoiceNo($sales['locationid'], $sales['receipt_method']);
            if (empty($salesid)) throw new Exception("Invoice number failed to generate!");
            unset($sales['id']);
            Sales::$saleClass->update($salesid, $sales);
        } else {//updating
//            debug('old');
            $salesid = $sales['id'];
            if (!$oldSale = $Sales->get($salesid)) throw new Exception("Sale not found, cant update!");
            if ($oldSale['iscreditapproved']) throw new Exception("Sale already approved cant be edited!");

            // Clear old sale info
            //remove sales batches & sales prescriptions
            foreach ($Salesdetails->find(['salesid' => $salesid]) as $i => $oldSalesDetail) {
                $SalesBatches->deleteWhere(['sdi' => $oldSalesDetail['id']]);
                $SalesPrescriptions->deleteWhere(['sdi' => $oldSalesDetail['id']]);
                $SalesSerialnos->deleteWhere(['sdi' => $oldSalesDetail['id']]);
            }
            $Salesdetails->deleteWhere(['salesid' => $salesid]);
            $Salesdescriptions->deleteWhere(['salesid' => $salesid]);

            if ($exceed_limit == 1 && $sales['paymenttype'] == PAYMENT_TYPE_CASH) $sales['internal_remarks'] .= "\n\n Exceeded sale limit of " . formatN($Users->get($oldSale['createdby'])['sale_limit']);

            $sales['modifiedby'] = $_SESSION['member']['id'];
            $sales['dom'] = TIMESTAMP;
            $Sales->update($salesid, $sales);
        }

        //close order
        if ($sales['orderid']) {
            Orders::$staticClass->update($sales['orderid'], [
                'sales_status' => Orders::STATUS_CLOSED,
                'sales_closedby' => $_SESSION['member']['id']
            ]);

            //update bill
            $order = Orders::$staticClass->get($sales['orderid']);
            if ($order['billid']) Sales::$saleClass->update($salesid, ['billid' => $order['billid']]);
        }
        //close proforma
        if ($sales['proformaid']) {
            $Proformas->update($sales['proformaid'], [
                'sales_status' => Orders::STATUS_CLOSED,
                'sales_closedby' => $_SESSION['member']['id']
            ]);
            define('EXCEPT_PROFORMA', $sales['proformaid']);
        }

        //sale details
        $branchid = $Locations->getBranch($sales['locationid'])['id'];
        foreach ($productIds as $index => $productid) {
            $product = $Products->get($productid);
            if (!$stockIds[$index]) {//non-stock item
                if (!$product['non_stock']) throw new Exception("System found {$product['name']} marked as non-stock item while its not, Sale canceled!");
                $stockid = '';
                $detail = ['productid' => $productid];
            } else {
                $stockid = $stockIds[$index];
                $current_stock = $Stocks->calcStock(
                    $sales['locationid'], $stockid, "",
                    "", "", "", "",
                    "", "", "", "", "", "",
                    "", "", "", "", false, true,
                    '', '', true, true
                );
                $current_stock = array_values($current_stock)[0];

                if ($current_stock['total'] < $qty[$index]) throw new Exception("Product ({$product_description[$index]}), not enough stock");
                $detail = ['stockid' => $stockid];
            }

            $detail['salesid'] = $salesid;
            $detail['price'] = $price[$index];
            $detail['incprice'] = removeComma($incprice[$index]);
            $detail['sinc'] = $sinc[$index];
            $detail['price'] = removeComma($price[$index]);
            $detail['discount'] = removeComma($discount[$index]);
            $detail['hidden_cost'] = $hidden_cost[$index];
            $detail['quantity'] = $qty[$index];
            $detail['vat_rate'] = $vat_rate[$index];
            $detail['base_percentage'] = $product['baseprice'];
            $detail['points'] = $product['points'];
            $detail['createdby'] = $_SESSION['member']['id'];

            //commission
            $hierarchicPrices = $HierarchicPrices->getProductInfoWithPrices($branchid, $productid);
            $selectedHp = [];
            foreach ($hierarchicPrices as $hp) {
                $selectedHp = $hp;
                if ($hp['exc_price'] >= $detail['price']) break;
            }
            $detail['commission'] = $selectedHp['commission'] ?: 0;
            $detail['target'] = $selectedHp['target'] ?: 0;

            $Salesdetails->insert($detail);
            $sdi = $Salesdetails->lastId();

            //save prescriptions
            if ($prescriptions[$stockid]) {
                $SalesPrescriptions->insert([
                    'sdi' => $sdi,
                    'doctor_id' => $prescriptions[$stockid]['doctor'],
                    'hospital_id' => $prescriptions[$stockid]['hospital'],
                    'prescription' => $prescriptions[$stockid]['text'],
                    'referred' => $prescriptions[$stockid]['referred'] ? 1 : 0,
                ]);
            }


            //save batch details
            if ($batches[$stockid]) { //if track expire
                foreach ($batches[$stockid]['batchId'] as $bi => $batchId) {
                    $current_batch_stock = array_filter($current_stock['batches'], function ($b) use ($batchId) {
                        return $b['batchId'] == $batchId;
                    });
                    if (!$current_batch_stock) {
                        $batchno = $Batches->get($batchId)['batch_no'];
                        throw new Exception("System found batch no {$batchno} is out of stock");
                    }
                    $current_batch_stock = array_values($current_batch_stock)[0];
                    if ($current_batch_stock['total'] < $batches[$stockid]['qty_out'][$bi]) throw new Exception("System found batch {$current_batch_stock['batch_no']} has no enough qty");

                    $SalesBatches->insert([
                        'sdi' => $sdi,
                        'batch_id' => $batchId,
                        'qty' => $batches[$stockid]['qty_out'][$bi],
                    ]);
                }
            } else {
                //find batch id from available stocks
                $stockBatches = $current_stock['batches'];

                $remainTransferQty = $qty[$index]; //total product sell qty

                //insert stock until transfer qty is 0
                foreach ($stockBatches as $stb => $stockBatch) {
                    if ($stockBatch['total'] >= $remainTransferQty) { //single batch covers whole sale qty quantity
                        $SalesBatches->insert([
                            'sdi' => $sdi,
                            'batch_id' => $stockBatch['batchId'],
                            'qty' => $remainTransferQty,
                        ]);
                        break;
                    } else { //use another batch (qty and id)
                        $SalesBatches->insert([
                            'sdi' => $sdi,
                            'batch_id' => $stockBatch['batchId'],
                            'qty' => $stockBatch['total'],
                        ]);
                        $remainTransferQty -= $stockBatch['total']; //reduce remain qty
                    }
                    if ($remainTransferQty == 0) break; //no more sale qty left
                }
            }

            //save serialnos
            if ($serialnos[$stockid]) { // if product track serial no
                foreach ($serialnos[$stockid]['serial_number'] as $sindex => $number) {
                    $sno = $SerialNos->find(['number' => $number])[0];
                    if (empty($sno)) { //create new, it happens if serialnos entered manually from sale
                        $SerialNos->insert([
                            'number' => $number,
                            'initial_stockid' => $stockid,
                            'current_stock_id' => $stockid,
                            'source' => SerialNos::SOURCE_SALE,
                            'sdi' => $sdi,
                            'salespersonid' => $sales['salespersonid'],
                            'dos' => TIMESTAMP,
                            'createdby' => $sales['salespersonid']
                        ]);
                        $snoid = $SerialNos->lastId();
                    } else {
                        $SerialNos->update($sno['id'], [
                            'sdi' => $sdi,
                            'salespersonid' => $sales['salespersonid'],
                            'dos' => TIMESTAMP
                        ]);
                        $snoid = $sno['id'];
                    }
                    $SalesSerialnos->insert([
                        'sdi' => $sdi,
                        'snoid' => $snoid,
                    ]);
                }
            }
        }

        if ($CASH_APPROVED) {

            $methodId = $Paymentmethods->find(['name' => $payment_method])[0]['id'];
            $currencyid = $CurrenciesRates->getCurrency_rates($sales['currency_rateid'])['currencyid'];
            //save sales payment master
            $Salespayments->insert([
                'clientid' => $sales['clientid'],
                'currencyid' => $currencyid,
                'buying_rate' => 1,
                'paid_totalmount' => $sales['full_amount'],
                'handed_amount' => $paid_totalamount,
                'source' => SalesPayments::SOURCE_DIRECT,
                'pmethod_id' => $methodId,
                'eaccid' => $electronic_account,
                'credit_cardno' => $credit_card_no,
                'receipt_type' => $sales['receipt_method'] == 'sr' ? SalesPayments::RECEIPT_TYPE_SR : SalesPayments::RECEIPT_TYPE_TRA,
                'createdby' => $_SESSION['member']['id'],
                'approvalby' => $_SESSION['member']['id'],
                'approvedate' => TIMESTAMP,
            ]);
            $lastPaymentId = $Salespayments->lastId();

            //save sales payment details
            $SalespaymentDetails->insert([
                'salespaymentId' => $lastPaymentId,
                'salesid' => $salesid,
                'amount' => $sales['full_amount'],
                'base_selling' => $sales['currency_amount'],
                'createdby' => $_SESSION['member']['id'],
            ]);

            //update sales master
            $Sales->update($salesid, [
                'payment_status' => PAYMENT_STATUS_COMPLETE,
                'lastpaid_totalamount' => $sales['full_amount'],
                'iscreditapproved' => 1,
                'approvalby' => $_SESSION['member']['id'],
                'approvedate' => TIMESTAMP
            ]);

            //mark bill as billed
            RecurringBills::markAsBilled($salesid);
        }
//        debug(['okay']);
        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 10000;
        redirect('pos', 'quick_sales');
    }

    //tally transfer
    if (CS_TALLY_TRANSFER && CS_TALLY_DIRECT && $CASH_APPROVED && $sales['receipt_method'] != 'sr') {
        $ping = pingTally();
        if ($ping['status'] == 'error') {
            $_SESSION['error'] = $ping['msg'];
        } else {
            $result = Sales::tallyPost($salesid);
            if ($result['status'] == 'success') $_SESSION['message'] = $result['msg'];
            if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
        }
    }


    //receipt and fiscalization
    try {
        if ($CASH_APPROVED) {
            if ($sales['receipt_method'] == 'vfd') {
                $vfdResult = Sales::fiscalize($salesid);
//        debug($vfdResult);

                if ($vfdResult['status'] == 'success') {
                    $_SESSION['message'] = "\nInvoice created and fiscalized ";
                    $redirect_url = url('receipts', 'vfd', array('salesid' => $salesid, 'redirect' => base64_encode(url('pos', 'quick_sales'))));
                } else {
                    $_SESSION['error'] .= "\nInvoice created but not fiscalized due to" . $vfdResult['message'];
                    $redirect_url = url('sales', 'failed_fiscalization');
                }
            } elseif ($sales['receipt_method'] == 'efd') {
                $result = Sales::efdFiscalize($salesid);
                if ($result['status'] == 'success') {
                    $_SESSION['message'] .= "\n Invoice was created!, EFD Printed";
                } else {
                    $_SESSION['error'] = "\nEFD not Printed, " . $result['message'];
                }
                $redirect_url = url('pos', 'quick_sales');
            } elseif ($sales['receipt_method'] == 'sr') {
                $_SESSION['message'] .= "\n Invoice saved successfully!";
                $redirect_url = url('receipts', 'system_receipt', ['salesno' => $salesid, 'sr_size' => $sr_size, 'redirect' => base64_encode(url('pos', 'quick_sales'))]);
            }
        } else {
            $_SESSION['message'] .= "\n " . (!$sales['id'] ? 'Saved' : 'Updated') . ' successfully, waiting approval';
            $redirect_url = url('sales', 'sales_list', 'searchname=' . $salesid);
        }

        //update support order
        if (isset($order) && $order['foreign_orderid'] && $order['order_source'] == 'support') {
            $support_response = Orders::postToSupport($order['id']);
            if ($support_response['status'] == 'success') {
                $_SESSION['message'] .= "\nSupport: " . $support_response['msg'];
            } else {
                $_SESSION['error'] .= "\nSupport: " . $support_response['msg'];
            }
        }

        $_SESSION['delay'] = 10000;
        if ($keyboardonly && $sales['receipt_method'] == 'sr') {
            $invoiceno = Sales::$saleClass->get($salesid)['receipt_no'];
            $_SESSION['message'] .= "\n Invoice no $invoiceno";
            redirectBack();
        } else {
            header('Location: ' . $redirect_url);
        }
        die();
    } catch (Exception $e) {
        $_SESSION['error'] .= " \n" . $e->getMessage();
        $_SESSION['delay'] = 10000;
        redirect('sales', 'sales_list');
        die();
    }
}


if($action=='reprint'){
    $receiptno=$_GET['receiptno'];
    $sale = Sales::$saleClass->find(['receipt_no'=>$receiptno]);
    if(empty($sale)){
        $_SESSION['delay']=5000;
        $_SESSION['error']="Receipt no $receiptno not found!";
        redirectBack();
    }
    redirect('receipts','system_receipt',['salesno'=>$sale[0]['id'],'print_size'=>'']);
}