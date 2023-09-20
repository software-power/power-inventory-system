<?
if ($action == 'sales_list') {
    Users::isAllowed();
    $invoiceno = $_GET['invoiceno'];
    $salesid = $_GET['searchname'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $userid = $_GET['userid'];
    $clientid = $_GET['clientid'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $paymenttype = $_GET['paymenttype'];
    $payment_status = $_GET['payment_status'];
//     debug($_GET);
    if ($invoiceno || $salesid) $fromdate = $todate = $locationid = $branchid = $userid = '';

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];

    $title = [];
    if ($invoiceno) $title[] = "Invoice No: " . $invoiceno;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($userid) $title[] = "Order/Invoice by: " . $Users->get($userid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($payment_status) $title[] = "Payment Status: " . $payment_status;
    if ($paymenttype) $title[] = "Invoice type: " . $paymenttype;
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $tData['sales_list'] = $Sales->salesList($salesid, '', $fromdate, $todate, $clientid, $locationid, $branchid, $paymenttype,
        '', '', '', '', $payment_status, $userid, '', '', $invoiceno);


//    debug($tData['sales_list']);
    $tData['searchname'] = $salesid;
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('sales_list.tpl.php', $tData);
}

if ($action == 'add_sales_new') {
    Users::isAllowed();
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $user_receipt = $Users->get($_SESSION['member']['id'])['receipt_type'];
    $default_print_size = $_SESSION['member']['default_print_size'];

    $tData['electronic_accounts'] = $ElectronicAccounts->getAllActive();
    $tData['reciepts'] = $user_receipt == 'both' ?
        $RecieptsTypes->getAllActive()
        : $RecieptsTypes->find(['name' => $user_receipt, 'status' => 'active']);
    $print_sizes = RecieptsTypes::sizes();
    if ((!$default_print_size && CS_SR_TYPE != SR_TYPE_A4) || $default_print_size == 'small') $print_sizes = array_reverse($print_sizes);
    $tData['print_sizes'] = $print_sizes;

//    debug($_SESSION['member']);
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $tData['salesPerson'] = $_SESSION['member'];

    $salesid = $_GET['id'];
    $orderid = $_GET['order_number'];
    $proformaid = $_GET['proforma_no'];


    if ($orderid) {
        if (!$order = $Orders->withDetails($orderid)[0]) {
            $_SESSION['error'] = "order not found!";
            redirectBack();
        }

        if ($order['sales_status'] != Orders::STATUS_PENDING) {
            $_SESSION['error'] = "Order can not be processed";
            redirectBack();
        }

        //check permission to sale the order
        $SALE_IN_BRANCH = Users::can(OtherRights::approve_other_credit_invoice) || (Users::can(OtherRights::sale_other_order) && $order['branchid'] == $_SESSION['member']['branchid']);
        if (!$SALE_IN_BRANCH) {
            $_SESSION['error'] = "You are not allowed to sale orders from other branches";
            redirectBack();
        }

        if (!$order['op_reuse']) define('EXCEPT_PROFORMA', $order['proformaid']);  //release stock held by this proforma
        $tData['client'] = $Clients->get($order['clientid']);
        $tData['defaultLocation'] = $Locations->get($order['locid']);
        $branchid = $Locations->getBranch($order['locid'])['id'];
        $tData['salesPerson'] = $_SESSION['member'];

        $currency_rate = $CurrenciesRates->getCurrency_rates($order['currency_rateid']);
        $order['currency_amount'] = $currency_rate['rate_amount'];
        foreach ($order['details'] as $index => $detail) {
            //find hierarchic prices
            $hierarchics = $Hierarchics->get($_SESSION['member']['hierachicid']);
            $priceList = $Products->getPrices($branchid, $hierarchics['level'], $detail['productid']);
            $priceList['costprice'] = $detail['non_stock'] ? 0 : $priceList['costprice']; //no cost for non-stock product
            $priceList['minimum'] = IS_ADMIN || $detail['non_stock'] ? 0 : $priceList['minimum'];

            $order['details'][$index]['costprice'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
            $order['details'][$index]['incprice'] = $detail['incprice'] ?: addTAX($detail['price'], $detail['vat_rate']);
            $order['details'][$index]['base_min_price'] = $priceList['minimum'];
            $order['details'][$index]['base_min_incprice'] = addTAX($priceList['minimum'], $detail['vat_rate']);
            $order['details'][$index]['base_suggested_price'] = $priceList['maximum'];
            $order['details'][$index]['base_suggested_incprice'] = addTAX($priceList['maximum'], $detail['vat_rate']);
            $order['details'][$index]['min_price'] = round($priceList['minimum'] / $currency_rate['rate_amount'], 2);
            $order['details'][$index]['min_incprice'] = addTAX($order['details'][$index]['min_price'], $detail['vat_rate']);
            $order['details'][$index]['suggested_price'] = round($priceList['maximum'] / $currency_rate['rate_amount'], 2);
            $order['details'][$index]['suggested_incprice'] = addTAX($order['details'][$index]['suggested_price'], $detail['vat_rate']);
            $order['details'][$index]['base_price'] = round($detail['price'] * $currency_rate['rate_amount'], 2);
            $order['details'][$index]['base_incprice'] = $detail['sinc']
                ? round($detail['incprice'] * $currency_rate['rate_amount'], 2)
                : round(addTAX($detail['price'], $detail['vat_rate']) * $currency_rate['rate_amount'], 2);
            $order['details'][$index]['hidden_cost'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
            $order['details'][$index]['base_hidden_cost'] = $priceList['costprice'];

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
            }
            $order['details'][$index]['stock_qty'] = $stockBatches['total'] ?? 0;
            $order['details'][$index]['stock_batches'] = $stockBatches['batches'];

        }
//            debug($order);
        $tData['order'] = $order;
    }
    if ($proformaid) {
        if (!$proforma = $Proformas->withDetails($proformaid)[0]) {
            $_SESSION['error'] = "Proforma not found!";
            redirectBack();
        }

        if ($proforma['sales_status'] == Orders::STATUS_CLOSED && !isset($_GET['reuse'])) {
            $_SESSION['error'] = "Order already closed";
            redirectBack();
        }

        define('EXCEPT_PROFORMA', $proformaid);
        $tData['client'] = $Clients->get($proforma['clientid']);
        $tData['defaultLocation'] = $Locations->get($proforma['locid']);
        $branchid = $Locations->getBranch($proforma['locid'])['id'];
        $tData['salesPerson'] = $_SESSION['member'];

        $currency_rate = $CurrenciesRates->getCurrency_rates($proforma['currency_rateid']);
        $proforma['currency_amount'] = $currency_rate['rate_amount'];
        foreach ($proforma['details'] as $index => $detail) {
            //find hierarchic prices
            $hierarchics = $Hierarchics->get($_SESSION['member']['hierachicid']);
            $priceList = $Products->getPrices($branchid, $hierarchics['level'], $detail['productid']);
            $priceList['costprice'] = $detail['non_stock'] ? 0 : $priceList['costprice']; //no cost for non-stock product
            $priceList['minimum'] = IS_ADMIN || $detail['non_stock'] ? 0 : $priceList['minimum'];
//            debug($priceList);
            $proforma['details'][$index]['costprice'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['incprice'] = $detail['incprice'] ?: addTAX($detail['price'], $detail['vat_rate']);
            $proforma['details'][$index]['base_min_price'] = $priceList['minimum'];
            $proforma['details'][$index]['base_min_incprice'] = addTAX($priceList['minimum'], $detail['vat_rate']);
            $proforma['details'][$index]['base_suggested_price'] = $priceList['maximum'];
            $proforma['details'][$index]['base_suggested_incprice'] = addTAX($priceList['maximum'], $detail['vat_rate']);
            $proforma['details'][$index]['min_price'] = round($priceList['minimum'] / $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['min_incprice'] = addTAX($proforma['details'][$index]['min_price'], $detail['vat_rate']);
            $proforma['details'][$index]['suggested_price'] = round($priceList['maximum'] / $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['suggested_incprice'] = addTAX($proforma['details'][$index]['suggested_price'], $detail['vat_rate']);
            $proforma['details'][$index]['base_price'] = round($detail['price'] * $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['hidden_cost'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
            $proforma['details'][$index]['base_hidden_cost'] = $priceList['costprice'];
            $proforma['details'][$index]['proforma_item'] = true;

            if ($detail['non_stock']) continue;

            $stockid = $Stocks->find(['productid' => $detail['productid'], 'locid' => $proforma['locid']])[0]['id'];
            $stockBatches = [];
//            debug($proforma);
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

        }
//        debug($proforma);
        $tData['proforma'] = $proforma;
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

        //todo include sale description for combined description

        if (!$sale['op_reuse']) define('EXCEPT_PROFORMA', $sale['proformaid']);  //release stock held by this proforma
        $tData['client'] = $Clients->get($sale['clientid']);
        $tData['defaultLocation'] = $Locations->get($sale['locationid']);
        $branchid = $Locations->getBranch($sale['locationid'])['id'];
        $tData['salesPerson'] = $Users->get($sale['createdby']);
        $sale['installments'] = $SalesInstallmentPlans->find(['salesid' => $salesid]);
        $sale['details'] = $Salesdetails->find(['salesid' => $salesid]);

        $currency_rate = $CurrenciesRates->getCurrency_rates($sale['currency_rateid']);

        foreach ($sale['details'] as $i => $detail) {
            $stock = $Stocks->get($detail['stockid']);
            $product = $Products->get($stock ? $stock['productid'] : $detail['productid']);
            $sale['details'][$i]['productid'] = $product['id'];
            $sale['details'][$i]['productname'] = $product['name'];
            $sale['details'][$i]['description'] = $product['description'];
            if ($detail['print_extra']) $sale['details'][$i]['description'] = $Salesdescriptions->find(['sdi' => $detail['id']])[0]['description'];
            $sale['details'][$i]['show_print'] = $detail['show_print'] ? 'yes' : 'no';
            $sale['details'][$i]['non_stock'] = $product['non_stock'];
            $sale['details'][$i]['trackserialno'] = $product['trackserialno'];
            $sale['details'][$i]['validate_serialno'] = $product['validate_serialno'];
            $sale['details'][$i]['track_expire_date'] = $product['track_expire_date'];
            $sale['details'][$i]['prescription_required'] = $product['prescription_required'];

            $category = $Categories->get($product['categoryid']);
            $sale['details'][$i]['vat_rate'] = $category['vat_percent'];

            //find hierarchic prices
            $hierarchics = $Hierarchics->get($_SESSION['member']['hierachicid']);
            $priceList = $Products->getPrices($branchid, $hierarchics['level'], $product['id']);
//            debug($priceList);
            $priceList['costprice'] = $product['non_stock'] ? 0 : $priceList['costprice']; //no cost for non-stock product
            $sale['details'][$i]['costprice'] = $priceList['costprice'];
            $priceList['minimum'] = IS_ADMIN || $product['non_stock'] ? 0 : $priceList['minimum'];

            $sale['details'][$i]['incprice'] = $detail['incprice'] ?: addTAX($detail['price'], $category['vat_percent']);
            $sale['details'][$i]['base_min_price'] = $priceList['minimum'];
            $sale['details'][$i]['base_min_incprice'] = addTAX($priceList['minimum'], $category['vat_percent']);
            $sale['details'][$i]['base_suggested_price'] = $priceList['maximum'];
            $sale['details'][$i]['base_suggested_incprice'] = addTAX($priceList['maximum'], $category['vat_percent']);
            $sale['details'][$i]['min_price'] = round($priceList['minimum'] / $currency_rate['rate_amount'], 2);
            $sale['details'][$i]['min_incprice'] = addTAX($sale['details'][$i]['min_price'], $category['vat_percent']);
            $sale['details'][$i]['suggested_price'] = round($priceList['maximum'] / $currency_rate['rate_amount'], 2);
            $sale['details'][$i]['suggested_incprice'] = addTAX($sale['details'][$i]['suggested_price'], $category['vat_percent']);
            $sale['details'][$i]['base_price'] = round($detail['price'] * $sale['currency_amount'], 2);
            $sale['details'][$i]['base_incprice'] = $detail['sinc']
                ? round($detail['incprice'] * $currency_rate['rate_amount'], 2)
                : round(addTAX($detail['price'], $detail['vat_rate']) * $currency_rate['rate_amount'], 2);
            $sale['details'][$i]['hidden_cost'] = round($priceList['costprice'] / $currency_rate['rate_amount'], 2);
            $sale['details'][$i]['base_hidden_cost'] = $priceList['costprice'];
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
//        debug($sale);
        $tData['sale'] = $sale;
    }
    $data['content'] = loadTemplate('make_sale.tpl.php', $tData);

}

if ($action == 'save_sales_new') {
    $sales = $_POST['sales'];

    validate($sales);
//    debug($_POST);
    $sales['vat_exempted'] = !isset($_POST['with_vat']);
    $receivedCash = $_POST['receivedCash'];
    $exceed_limit = $_POST['exceed_limit'];
    $extra_desc_approval = $_POST['extra_desc_approval'];
    $payment_method = $_POST['payment_method'];
    $electronic_account = $_POST['electronic_account'];
    $credit_card_no = $_POST['credit_card_no'];
    $dist_plan = $_POST['dist_plan'];
    $stockIds = $_POST['stockid'];
    $productIds = $_POST['productid'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $hidden_cost = $_POST['hidden_cost'];
    $vat_rate = $_POST['vat_rate'];
    $incprice = $_POST['incprice'];
    $sinc = $_POST['sinc'];
    $bulk_rate = $_POST['bulk_rate'];
    $product_description = $_POST['product_description'];
    $batches = $_POST['batch'];
    $serialnos = $_POST['serialno'];
    $prescriptions = $_POST['prescription'];
    $show_print = $_POST['show_print'];
    $print_extra = $_POST['print_extra'];
    $installment_plans = $_POST['installment_plans'];

    validate($productIds);

    global $db_connection;
    mysqli_begin_transaction($db_connection); //sales and payment transaction
    try {
        $sales['has_combine'] = isset($_POST['has_combine']);
        $sales['source'] = Sales::SOURCE_DETAILED;

        if (isset($sales['has_installment'])) {
            $sales['has_installment'] = 1;
            $sales['dist_plan'] = $dist_plan;
        }
//        debug($sales);

        $CASH_APPROVED = $sales['paymenttype'] == PAYMENT_TYPE_CASH && !isset($_POST['has_combine']) && !isset($_POST['need_approval']) && $exceed_limit == '' && $extra_desc_approval == '' && !$sales['vat_exempted'];

        //check receivedCash if cash payment
        if ($CASH_APPROVED) {
            if ($receivedCash < $sales['full_amount']) {
                $_SESSION['error'] = "Received Cash Not enough!, Invoice not Saved";
                redirectBack();
                die();
            }
        }

        if ($sales['paymenttype'] == PAYMENT_TYPE_CREDIT) {
            if (!$sales['credit_days']) $sales['credit_days'] = 30;
            $sales['duedate'] = date('Y-m-d', strtotime(TODAY . " +" . $sales['credit_days'] . " days"));
        }

        $sales['transfer_tally'] = CS_TALLY_TRANSFER && $sales['receipt_method'] != 'sr'; //for tally transfer

        if (CS_VFD_TYPE == VFD_TYPE_ZVFD) {//set zvfd tax type
            if (empty(CS_ZVFD_TAXCATEGORYID)) throw new Exception("ZVFD TAX category not SET!");
            $taxCategory = Categories::$categoryClass->get(CS_ZVFD_TAXCATEGORYID);
            if (empty($taxCategory['zvfd_tax_type']) || $taxCategory['zvfd_tax_type'] <= 0) throw new Exception("ZVFD TAX Type not set!");
            $sales['zvfd_tax_type'] = $taxCategory['zvfd_tax_type'];
        }

        if ($extra_desc_approval == 1) $sales['internal_remarks'] .= "\n\n Has extra description";

        if (!$sales['id']) {//new
            if ($Sales->find(['token' => $sales['token']])) throw new Exception("System found this form is already submitted, Sales canceled to avoid duplicate invoices!");
            $sales['createdby'] = $_SESSION['member']['id'];
            if ($exceed_limit == 1 && $sales['paymenttype'] == PAYMENT_TYPE_CASH) $sales['internal_remarks'] .= "\n\n Exceeded sale limit of " . formatN($_SESSION['member']['sale_limit']);
            if ($sales['receipt_method'] == 'sr') $sales['description'] .= ($sales['description'] ? "\r\n" : "") . CS_SR_EXTRA_REMARKS;
            $salesid = Sales::generateInvoiceNo($sales['locationid'], $sales['receipt_method']);
            if (empty($salesid)) throw new Exception("Invoice number failed to generate!");
            unset($sales['id']);
            Sales::$saleClass->update($salesid, $sales);
        } else {//updating
            $salesid = $sales['id'];
            if (!$oldSale = $Sales->get($salesid)) {
                $_SESSION['error'] = "Sale not found, cant update!";
                $_SESSION['delay'] = 5000;
                redirectBack();
            }
            if ($oldSale['iscreditapproved']) {
                $_SESSION['error'] = "Sale already approved cant be edited!";
                $_SESSION['delay'] = 5000;
                redirect('payments', 'invoice_list');
            }

            // Clear old sale info
            //remove sales batches & sales prescriptions
            $sdis = array_column($Salesdetails->find(['salesid' => $salesid]), 'id');
            $SalesBatches->deleteWhereMany(['sdi' => $sdis]);
            $SalesPrescriptions->deleteWhereMany(['sdi' => $sdis]);
            $SalesSerialnos->deleteWhereMany(['sdi' => $sdis]);
            $Salesdescriptions->deleteWhereMany(['sdi' => $sdis]);
            $Salesdetails->deleteWhere(['salesid' => $salesid]);
            $SalesInstallmentPlans->deleteWhere(['salesid' => $salesid]);

            if ($exceed_limit == 1 && $sales['paymenttype'] == PAYMENT_TYPE_CASH) $sales['internal_remarks'] .= "\n\n Exceeded sale limit of " . formatN($Users->get($oldSale['createdby'])['sale_limit']);
            $sales['modifiedby'] = $_SESSION['member']['id'];
            $sales['dom'] = TIMESTAMP;
            $Sales->update($salesid, $sales);
        }
        //update order
        if ($sales['orderid']) {
            Orders::$staticClass->update($sales['orderid'], [
                'sales_status' => Orders::STATUS_CLOSED,
                'sales_closedby' => $_SESSION['member']['id']
            ]);

            //update bill
            $order = Orders::$staticClass->get($sales['orderid']);
            if ($order['billid']) Sales::$saleClass->update($salesid, ['billid' => $order['billid']]);
        }
        //update proforma
        if ($sales['proformaid']) {
            $Proformas->update($sales['proformaid'], [
                'sales_status' => Orders::STATUS_CLOSED,
                'sales_closedby' => $_SESSION['member']['id']
            ]);
            define('EXCEPT_PROFORMA', $sales['proformaid']);
        }
        //installments
        if ($sales['has_installment']) {
            $total_plan_amount = 0;
//            debug($installment_plans);
            foreach ($installment_plans['time'] as $index => $time) {
                $time = $sales['dist_plan'] == 'monthly' ? $time .= "-01" : $time;
                $amt = removeComma($installment_plans['amount'][$index]);
                $total_plan_amount += $amt;
                $SalesInstallmentPlans->insert([
                    'salesid' => $salesid,
                    'time' => $time,
                    'amount' => $amt,
                    'createdby' => $_SESSION['member']['id'],
                ]);
            }
            if ($total_plan_amount != $sales['full_amount']) throw new Exception("Total installment amount does not match invoice amount!");
        }


        //Sales details
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
            $detail['price'] = removeComma($price[$index]);
            $detail['incprice'] = removeComma($incprice[$index]);
            $detail['sinc'] = $sinc[$index];
            $detail['hidden_cost'] = $hidden_cost[$index];
            $detail['quantity'] = $qty[$index];
            $detail['vat_rate'] = $vat_rate[$index];
            $detail['show_print'] = $show_print[$index];
            $detail['print_extra'] = in_array($productid, $print_extra);
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

            //extra description
            if ($detail['print_extra']) {
                $Salesdescriptions->insert([
                    'sdi' => $sdi,
                    'description' => $product_description[$index],
                ]);
            }

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
                $remainSellQty = $qty[$index]; //total product sell qty
                //insert stock until transfer qty is 0
                foreach ($stockBatches as $stb => $stockBatch) {
                    if ($stockBatch['total'] >= $remainSellQty) { //single batch covers whole sale qty quantity
                        $SalesBatches->insert([
                            'sdi' => $sdi,
                            'batch_id' => $stockBatch['batchId'],
                            'qty' => $remainSellQty,
                        ]);
                        break;
                    } else { //use another batch (qty and id)
                        $SalesBatches->insert([
                            'sdi' => $sdi,
                            'batch_id' => $stockBatch['batchId'],
                            'qty' => $stockBatch['total'],
                        ]);
                        $remainSellQty -= $stockBatch['total']; //reduce remain qty
                    }
                    if ($remainSellQty == 0) break; //no more sale qty left
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
                'handed_amount' => $receivedCash,
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
        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 10000;
        redirect('sales', 'add_sales_new');
        die();
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

    try {
        if ($CASH_APPROVED) {
            if ($sales['receipt_method'] == 'vfd') {
                $vfdResult = Sales::fiscalize($salesid);
                if ($vfdResult['status'] == 'success') {
                    $_SESSION['message'] .= "\nInvoice created and fiscalized ";
                    $redirect_url = url('receipts', 'vfd', array('salesid' => $salesid, 'redirect' => base64_encode(url('sales', 'add_sales_new'))));
                } else {
                    $_SESSION['error'] .= "\nInvoice created but not fiscalized due to " . $vfdResult['message'];
                    $redirect_url = url('sales', 'failed_fiscalization');
                }
            } elseif ($sales['receipt_method'] == 'efd') {
                $result = Sales::efdFiscalize($salesid);
                if ($result['status'] == 'success') {
                    $_SESSION['message'] .= "\n Invoice was created!, EFD Printed";
                } else {
                    $_SESSION['error'] .= "\nEFD not Printed, " . $result['message'];
                }
                $redirect_url = url('sales', 'add_sales_new');
            } elseif ($sales['receipt_method'] == 'sr') {
                $_SESSION['message'] .= "\n Invoice saved successfully!";
                $redirect_url = url('receipts', 'system_receipt', ['salesno' => $salesid, 'redirect' => base64_encode(url('sales', 'add_sales_new'))]);
            }
        } else {
            $_SESSION['message'] .= "\n" . (!$sales['id'] ? 'Saved' : 'Updated') . ' successfully, waiting approval';
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


        header('Location: ' . $redirect_url);
        die();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 10000;
        redirect('sales', 'sales_list');
        die();
    }


}

if ($action == 'cancel_sale') {
    Users::can(OtherRights::cancel_sale, true);
    $salesid = $_POST['salesno'];
    $sale = $Sales->get($salesid);
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {

        if (empty($sale)) {
            $_SESSION['error'] = "Invoice not Found";
            $_SESSION['delay'] = 5000;
            redirect('payments', 'invoice_list');
            die();
        }

        if ($sale['iscreditapproved']) {
            $_SESSION['error'] = "Invoice already approved cant be canceled";
            $_SESSION['delay'] = 5000;
            redirect('payments', 'invoice_list');
            die();
        }

        $currency = $CurrenciesRates->getCurrency_rates($sale['currency_rateid']);
        $sale['currencyname'] = $currency ['currencyname'] . " - " . $currency['description'];
        $sale['locationname'] = $Locations->get($sale['locationid'])['name'];
        $sale['creator'] = $Users->get($sale['createdby'])['name'];
        $sale['salesperson'] = $Users->get($sale['salespersonid'])['name'];
        $sale['clientname'] = $Clients->get($sale['clientid'])['name'];
        $sale['details'] = $Salesdetails->find(['salesid' => $salesid]);
        foreach ($sale['details'] as $index => $detail) {
            $stock = $Stocks->get($detail['stockid']);
            $sale['details'][$index]['productname'] = $Products->get($stock['productid'])['name'];
            $sale['details'][$index]['batches'] = $SalesBatches->find(['sdi' => $detail['id']]);
            $sale['details'][$index]['prescriptions'] = $SalesPrescriptions->find(['sdi' => $detail['id']]);
            $sale['details'][$index]['serialnos'] = $SalesSerialnos->find(['sdi' => $detail['id']]);
        }
        $sale['descriptions'] = $Salesdescriptions->find(['salesid' => $salesid]);
        $SalesCanceled->insert([
            'saleid' => $salesid,
            'invoiceno' => $sale['receipt_no'],
            'clientid' => $sale['clientid'],
            'locationid' => $sale['locationid'],
            'payload' => base64_encode(json_encode($sale)),
            'createdby' => $_SESSION['member']['id']
        ]);
        //release order
        if ($sale['orderid']) {
            $order = Orders::$staticClass->get($sale['orderid']);
            if ($order['foreign_orderid']) { //external order
                Orders::$staticClass->update($sale['orderid'], ['status' => 'inactive']);
            } else {
                Orders::$staticClass->update($sale['orderid'], [
                    'sales_status' => Orders::STATUS_PENDING,
                    'sales_closedby' => 0
                ]);
            }
        }
        //release proforma
        if ($sale['proformaid'] && !$sale['op_reuse']) {
            $Proformas->update($sale['proformaid'], [
                'sales_status' => 'pending',
                'sales_closedby' => 0
            ]);
        }

        //clear sale related info
        foreach ($sale['details'] as $i => $detail) {
            $SalesBatches->deleteWhere(['sdi' => $detail['id']]);
            $SalesPrescriptions->deleteWhere(['sdi' => $detail['id']]);
            $SalesSerialnos->deleteWhere(['sdi' => $detail['id']]);

            //release serialnos
            $SerialNos->updateWhere(['sdi' => $detail['id']], [
                'sdi' => null,
                'salespersonid' => null,
                'dos' => null,
            ]);
        }
        $Salesdetails->deleteWhere(['salesid' => $salesid]);
        $Salesdescriptions->deleteWhere(['salesid' => $salesid]);
        $Sales->real_delete($salesid);
        //debug($sale);

        $_SESSION['message'] = "Sale canceled successfully";
        mysqli_commit($db_connection);

        //update support order
        if (isset($order) && $order['foreign_orderid'] && $order['order_source'] == 'support') {
            $support_response = Orders::postToSupport($order['id']);
            if ($support_response['status'] == 'success') {
                $_SESSION['message'] .= "\nSupport: " . $support_response['msg'];
            } else {
                $_SESSION['error'] .= "\nSupport: " . $support_response['msg'];
            }
        }

        redirect('payments', 'invoice_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = 'Error ' . $e->getMessage();
        $_SESSION['delay'] = 5000;
        redirect('payments', 'invoice_list');
    }

}

if ($action == 'canceled_list') {
    Users::isAllowed();
    $locationid = $_GET['locationid'];
    $clientid = $_GET['clientid'];
    $branchid = $_GET['branchid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if (empty($fromdate)) $fromdate = date('Y-m-d', strtotime('-1 months'));
    if (empty($todate)) $todate = date('Y-m-d');
    $title[] = "From: " . fDate($fromdate);
    $tData['title'] = implode(' | ', $title);

    $canceled = $SalesCanceled->getList($locationid, $clientid, $fromdate, $todate, $branchid);
    $canceled = array_map(function ($item) {
        $item['decoded'] = json_decode(base64_decode($item['payload']), true);
        return $item;
    }, $canceled);

//    debug($canceled);


    $tData['branches'] = Users::can(OtherRights::approve_other_credit_invoice) ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->get($_SESSION['member']['branchid']);
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $tData['canceled'] = $canceled;
    $data['content'] = loadTemplate('sales_canceled_list.tpl.php', $tData);
}

if ($action == 'view_invoice') {
    $salesid = $_GET['salesid'];
    if (!$sales = $Sales->get($salesid)) debug('Invoice not found!');
    $invoice = $Sales->detailedSalesList($user = '', $salesid, $group = true)[0];
    $saleExpense = $Expenses->find(['saleid' => $salesid]);
    $totalExpense = 0;
    $totalExpense = array_sum(array_column($saleExpense, 'total_amount'));
    $invoice['expense_amount'] = $totalExpense;
    $has_payments = SalespaymentDetails::$staticClass->countWhere(['salesid' => $salesid, 'opening' => 0]) > 0;
    $converted = Sales::$saleClass->countWhere(['previd' => $salesid]) > 0;
    $invoice['can_fiscalize'] = (!$has_payments || CS_FISCALIZE_PAID) && !$converted && $invoice['iscreditapproved'] && $invoice['receipt_method'] == 'sr';
    $salesreturns = $SalesReturns->getList('', $salesid, '', '');
    $documents = SalesDocuments::$staticClass->getList($salesid);

//    debug($invoice,1);
    //fetch serialnos
    foreach ($invoice['products'] as $index => $product) {
        if ($product['trackserialno']) {
            $invoice['has_serialno'] = true;
            $invoice['products'][$index]['serialnos'] = $SerialNos->find(['sdi' => $product['sdi']]);
        }
    }
//    debug($invoice);
    $tData['invoice'] = $invoice;
    $tData['returns'] = $salesreturns;
    $tData['documents'] = $documents;
    $data['content'] = loadTemplate('view_invoice.tpl.php', $tData);
}

if ($action == 'fiscalize_sr_invoice') {
    Users::can(OtherRights::approve_other_credit_invoice, true);
    $salesid = removeSpecialCharacters($_POST['salesid']);
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $oldsale = Sales::$saleClass->get($salesid);
        if (empty($oldsale)) throw new Exception("Invoice not found");
        if (!$oldsale['iscreditapproved']) throw new Exception("Invoice is not approved");
        $has_payments = SalespaymentDetails::$staticClass->countWhere(['salesid' => $salesid, 'opening' => 0]) > 0;
        if ($has_payments && !CS_FISCALIZE_PAID) throw new Exception("Invoice has payments cant be converted");
        if ($oldsale['receipt_method'] != 'sr') throw new Exception("Invoice is not SR");
        $has_returns = SalesReturns::$saleReturnClass->countWhere(['salesid' => $salesid]) > 0;
        if ($has_returns) throw new Exception("Invoice has credit notes cant be converted");
        $converted = Sales::$saleClass->countWhere(['previd' => $salesid]) > 0;
        if ($converted) throw new Exception("Invoice already converted");

        $receipts = RecieptsTypes::$staticClass->getAllActive();
        $receipts = array_filter($receipts, function ($r) {
            return strtolower($r['name']) != 'sr';
        });
        if (empty($receipts)) throw new Exception("TRA integration not allowed!,\nCheck company settings");
        $receipts = array_values($receipts);
        $new_sale = $oldsale;
//        debug($receipts);

        unset(
            $new_sale['id'],
            $new_sale['orderid'], $new_sale['billid'], $new_sale['proformaid'], $new_sale['op_reuse'],
            $new_sale['tally_post'], $new_sale['doc'], $new_sale['modifiedby'], $new_sale['dom'], $new_sale['tally_invoiceno'],
            $new_sale['tally_trxno'], $new_sale['tally_message']
        );


        if ($has_payments) {
            $new_sale['approvalby'] = $_SESSION['member']['id'];
            $new_sale['approvedate'] = TIMESTAMP;
        } else {
            unset($new_sale['approvalby'], $new_sale['approvedate'], $new_sale['iscreditapproved']);
            $new_sale['payment_status'] = PAYMENT_STATUS_PENDING;
            $new_sale['paymenttype'] = PAYMENT_TYPE_CREDIT;
            $new_sale['credit_days'] = 30;
            $new_sale['source'] = Sales::SOURCE_DETAILED;
            $new_sale['print_size'] = 'A4';
            $new_sale['duedate'] = date('Y-m-d', strtotime(TODAY . " +30 days"));
        }

        $newsalesid = Sales::generateInvoiceNo($new_sale['locationid'], $receipts[0]['name']);
        if (empty($newsalesid)) throw new Exception("Invoice number failed to generate!");

        unset($new_sale['receipt_no']);
        $new_sale['previd'] = $salesid;
        $new_sale['receipt_method'] = $receipts[0]['name'];
        $new_sale['transfer_tally'] = CS_TALLY_TRANSFER;

        //new sale
        if (!Sales::$saleClass->update($newsalesid, $new_sale)) throw new Exception("Failed to create new invoice");
        Sales::$saleClass->update($salesid, [
            'payment_status' => PAYMENT_STATUS_COMPLETE,
            'grand_amount' => 0,
            'grand_vatamount' => 0,
            'full_amount' => 0,
            'lastpaid_totalamount' => 0,
            'fisc_convertedby' => $_SESSION['member']['id'],
            'fisc_convertdate' => TIMESTAMP,
        ]);

        //copy installment plan
        if ($oldsale['has_installment']) SalesInstallmentPlans::$staticClass->updateWhere(['salesid' => $salesid], ['salesid' => $newsalesid]);

        //shift old payments
        if ($has_payments) SalespaymentDetails::$staticClass->updateWhere(['salesid' => $salesid, 'opening' => 0], ['salesid' => $newsalesid]);

        //copy details
        foreach (Salesdetails::$saleDetailsClass->find(['salesid' => $salesid]) as $detail) {
            $new_detail = $detail;
            unset($new_detail['id'], $new_detail['doc']);
            $new_detail['salesid'] = $newsalesid;
            Salesdetails::$saleDetailsClass->insert($new_detail);
            $newsdi = Salesdetails::$saleDetailsClass->lastId();

            //set old detail qty = 0
            Salesdetails::$saleDetailsClass->update($detail['id'], ['quantity' => 0]);

            //copy batches
            foreach (SalesBatches::$salesBatchesClass->find(['sdi' => $detail['id']]) as $salebatch) {
                $new_batch = $salebatch;
                unset($new_batch['id']);
                $new_batch['sdi'] = $newsdi;
                SalesBatches::$salesBatchesClass->insert($new_batch);
                SalesBatches::$salesBatchesClass->update($salebatch['id'], ['qty' => 0]);
            }

            //copy prescription
            if ($prescription = SalesPrescriptions::$salesPrescriptionClass->find(['sdi' => $detail['id']])[0]) {
                $new_prescription = $prescription;
                unset($new_prescription['id']);
                $new_prescription['sdi'] = $newsdi;
                SalesPrescriptions::$salesPrescriptionClass->insert($new_prescription);
            }

            //copy description
            if ($description = Salesdescriptions::$staticClass->find(['sdi' => $detail['id']])[0]) {
                $new_description = $description;
                unset($new_description['id']);
                $new_description['sdi'] = $newsdi;
                Salesdescriptions::$staticClass->insert($new_description);
            }

            //copy serialnos
            foreach (SerialNos::$serialNoClass->find(['sdi' => $detail['id']]) as $sno) {
                SalesSerialnos::$saleSerialnosClass->insert(['sdi' => $newsdi, 'snoid' => $sno['id']]);
                SerialNos::$serialNoClass->update($sno['id'], ['sdi' => $newsdi]);
            }
        }

        mysqli_commit($db_connection);
        if ($has_payments) {
            $_SESSION['message'] = "Invoice converted,\n fiscalize the invoice";
            redirect('sales', 'failed_fiscalization', ['convertedid' => $newsalesid]);
        } else {
            $_SESSION['message'] = "Invoice converted successfully,\nwaiting approval";
            redirect('payments', 'invoice_list');
        }
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'convert_to_credit_invoice') {
    if (!IS_ADMIN) redirect('authenticate', 'access_page', ['right_action' => base64_encode('convert invoice')]);
//    debug($_POST);
    $salesid = removeSpecialCharacters($_POST['salesid']);
    $remarks = $_POST['remarks'];
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $sale = Sales::$saleClass->get($salesid);
        if (!$sale) throw new Exception("Invoice not found!");
        if (!$sale['iscreditapproved']) throw new Exception("Invoice is not approved!");
        if ($sale['paymenttype'] != PAYMENT_TYPE_CASH) throw new Exception("Invoice is not cash invoice!");
        if ($sale['clientid'] == 1) throw new Exception("Cash client cant have credit invoice!");
        $paymentdetails = SalespaymentDetails::$staticClass->find(['salesid' => $salesid, 'opening' => 0])[0];
        $payment = SalesPayments::$salePaymentClass->find(['id' => $paymentdetails['salespaymentId'], 'source' => SalesPayments::SOURCE_DIRECT]);
        if (!$payment) throw new Exception("Invoice payment info not found!");
        Sales::$saleClass->update($salesid, [
            'paymenttype' => PAYMENT_TYPE_CREDIT,
            'payment_status' => PAYMENT_STATUS_PENDING,
            'lastpaid_totalamount' => 0,
            'credit_convertedby' => $_SESSION['member']['id'],
            'credit_convertedat' => TIMESTAMP,
            'credit_convert_remarks' => $remarks,
        ]);
        SalesPayments::$salePaymentClass->deleteWhere(['id' => $paymentdetails['salespaymentId']]);//delete payment master
        SalespaymentDetails::$staticClass->deleteWhere(['salespaymentId' => $paymentdetails['salespaymentId']]);//delete payment details
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Invoice converted successfully";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}

if ($action == 'opening_outstanding') {
    Users::isAllowed();
//    debug('opening');
    $openingid = $_GET['openingid'];
    $clientid = $_GET['clientid'];
    $currencyid = $_GET['currencyid'];
    $userid = $_GET['userid'];
    $payment_status = $_GET['payment_status'];
    $locationid = $_GET['locationid'];
    $branchid = $_GET['branchid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];

    $title = [];

    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($userid) $title[] = "Issued by: " . $Users->get($userid)['name'];
    if ($payment_status) $title[] = "Payment status: " . $payment_status;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);

    $outstandings = $ClientOpeningOutstandings->getList($openingid, $clientid, $currencyid, $userid, $payment_status, $fromdate, $todate, $locationid, $branchid);
//    debug($outstandings);
    $tData['outstandings'] = $outstandings;
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['branches'] = Users::can(OtherRights::approve_other_credit_invoice) ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $data['content'] = loadTemplate('client_opening_outstanding_list.tpl.php', $tData);
}

if ($action == 'create_opening_outstanding') {
    Users::can(OtherRights::approve_other_credit_invoice, true);
    $tData['currencies'] = $Currencies->getAllActive();
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('create_client_opening_outstanding.tpl.php', $tData);
}

if ($action == 'save_client_opening_outstanding') {
    Users::can(OtherRights::approve_other_credit_invoice, true);
//    debug($_POST);
    $invoiceno = $_POST['invoiceno'];
    $clientid = $_POST['clientid'];
    $locationid = $_POST['locationid'];
    $currencyid = $_POST['currencyid'];
    $currency_amount = $_POST['currency_amount'];
    $outstanding_amount = $_POST['outstanding_amount'];
    $invoicedate = $_POST['invoicedate'];
    $credit_days = $_POST['credit_days'];
    $description = $_POST['description'];

    validate($invoiceno);
    validate($clientid);
    validate($locationid);
    validate($currencyid);
    validate($currency_amount);
    validate($outstanding_amount);
    validate($invoicedate);
    validate($credit_days);


    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        foreach ($invoiceno as $index => $invno) {
            if (ClientOpeningOutstandings::$staticClass->countWhere(['invoiceno' => $invno]) > 0) throw new Exception("Invoice no '$invno' already exists");
            ClientOpeningOutstandings::$staticClass->insert([
                'invoiceno' => $invno,
                'currencyid' => $currencyid[$index],
                'currency_amount' => $currency_amount[$index],
                'clientid' => $clientid[$index],
                'locationid' => $locationid[$index],
                'description' => $description[$index],
                'outstanding_amount' => $outstanding_amount[$index],
                'invoicedate' => $invoicedate[$index],
                'credit_days' => $credit_days[$index],
                'createdby' => $_SESSION['member']['id'],
            ]);
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Outstanding saved successfully!";
        redirect('sales', 'opening_outstanding');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }

}


if ($action == 'invoice_remarks') {
    $tData['remarks'] = $InvoiceRemarks->getAll();
    $data['content'] = loadTemplate('invoice_remarks_list.tpl.php', $tData);
}

if ($action == 'save_invoice_remark') {
    $remark = $_POST['remark'];
    validate($remark);
    if (!$remark['id']) {
        if ($InvoiceRemarks->find(['name' => $remark['name']])) {
            $_SESSION['error'] = "Name already exists";
            redirectBack();
        }
        $remark['createdby'] = $_SESSION['member']['id'];
        $InvoiceRemarks->insert($remark);
    } else {
        $InvoiceRemarks->update($remark['id'], $remark);
    }
    $_SESSION['message'] = "Remark " . ($remark['id'] ? 'updated' : 'created');
    redirectBack();
}

if ($action == 'save_document') {
    $document = $_POST['document'];
    validate($document);
    if (!$document['id']) {
        if (Documents::$staticClass->find(['name' => $document['name']])) {
            $_SESSION['error'] = "Name already exists";
            redirectBack();
        }
        $document['createdby'] = $_SESSION['member']['id'];
        Documents::$staticClass->insert($document);
    } else {
        Documents::$staticClass->update($document['id'], $document);
    }
    $_SESSION['message'] = "Document " . ($document['id'] ? 'updated' : 'created');
    redirectBack();
}

if ($action == 'attach_document') {
//    debug([$_POST, $_FILES]);
    $salesid = removeSpecialCharacters($_POST['salesid']);
    $docids = $_POST['docid']; //document master id
    $sdocid = $_POST['sdocid']; //existing doc id
    $document_action = $_POST['document_action'];
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $sale = Sales::$saleClass->get($salesid);
        if (!$sale) throw new Exception("Invoice not found");
        $file_to_remove = [];
        foreach ($docids as $index => $docid) {
            if ($document_action[$index] == 'new') {
                if ($sdocid[$index]) { //had document
                    $documentid = $sdocid[$index];
                    $doc = SalesDocuments::$staticClass->get($sdocid[$index]);
                    if ($doc && file_exists($doc['path'])) unlink($doc['path']);
                } else {
                    SalesDocuments::$staticClass->insert([
                        'salesid' => $salesid,
                        'docid' => $docid,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $documentid = SalesDocuments::$staticClass->lastId();
                }
                $file = $_FILES["file$docid"];
                $allowed_files = ['jpg', 'jpeg', 'png', 'pdf'];
                if ($file['error']) throw new Exception("Invalid file");
                $target_dir = "documents/sales/$salesid/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
                $target_file = $target_dir . $docid . "_" . str_replace([' ', '-'], '_', basename($file["name"]));
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if (!in_array($file_type, $allowed_files)) throw new Exception("Allowed files extensions are " . implode(',', $allowed_files) . " only");
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    SalesDocuments::$staticClass->update($documentid, ['path' => $target_file]);
                } else {
                    throw new Exception("Failed to upload the file");
                }
            }
            if ($document_action[$index] == 'remove') {
                if ($sdocid[$index]) { //had document
                    $doc = SalesDocuments::$staticClass->get($sdocid[$index]);
                    if ($doc && file_exists($doc['path'])) unlink($doc['path']);
                    SalesDocuments::$staticClass->deleteWhere(['id' => $sdocid[$index]]);
                }
            }

        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Document saved";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}


if ($action == 'post_tally') {
    $salesid = $_GET['salesid'];
    $ping = pingTally();
    if ($ping['status'] == 'error') {
        $_SESSION['error'] = $ping['msg'];
    } else {
        $result = Sales::tallyPost($salesid);
        if ($result['status'] == 'success') $_SESSION['message'] = $result['msg'];
        if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}

if ($action == 'failed_fiscalization') {
    Users::can(OtherRights::refiscalize_invoice, true);
    if (!empty($_POST['id'])) {
        $salesid = $_POST['id'];
        $sales = $Sales->get($salesid);
        if (empty($sales)) {
            $_SESSION['error'] = "Invoice not found";
            redirectBack();
            die();
        }
        if ($sales['receipt_method'] == 'vfd') {
            $vfdResult = Sales::fiscalize($salesid, isset($_POST['refiscalize']));  // override
            if ($vfdResult['status'] == 'success') {
                $_SESSION['message'] = 'Invoice created and fiscalized ';
                redirect('receipts', 'vfd', array('salesid' => $salesid, 'redirect' => base64_encode(url('sales', 'sales_list'))));
            } else {
                $_SESSION['error'] = 'Invoice created but not fiscalized due to ' . $vfdResult['message'];
                redirect('sales', 'failed_fiscalization');
            }
        } elseif ($sales['receipt_method'] == 'efd') {
            if (isset($_POST['state'])) Users::can(OtherRights::resend_efd_receipt, true);
            $result = Sales::efdFiscalize($salesid, isset($_POST['refiscalize']), $_POST['state'] == 'duplicate');// override
            if ($result['status'] == 'success') {
                $_SESSION['message'] = 'Invoice was created!, EFD Printed';
            } else {
                $_SESSION['error'] = 'EFD not Printed, ' . $result['message'];
            }
            isset($_POST['state']) ? redirectBack() : redirect('sales', 'failed_fiscalization');
        }
    } else {
        $convertedid = removeSpecialCharacters($_GET['convertedid']);
        $tData['failedFiscalization'] = $Sales->failedFiscalization($convertedid);
        $tData['basecurrency'] = $Currencies->find(['base' => 'yes'])[0];

        $data['content'] = loadTemplate('refiscalize.tpl.php', $tData);
    }
}


if ($action == 'ajax_getClientDetails') {
    $id = $_GET['clientId'];
    $cData = $Clients->find(array('id' => $id, 'status' => 'active'));
    $cData = $cData[0];
    $response = array();
    $obj = null;

    if ($cData) {
        $obj->name = $cData['name'];
        $obj->id = $cData['id'];
        $obj->mobile = $cData['mobile'];
        $obj->address = $cData['address'];
        $obj->email = $cData['email'];
        $obj->tinno = $cData['tinno'];
        $obj->vatno = $cData['vatno'];
        $obj->telephone = $cData['tel'];
        $obj->district = $cData['district'];
        $obj->street = $cData['street'];
        $obj->plotnumber = $cData['plotno'];
        $obj->location = $cData['city'];
        $obj->status = $cData['status'];
    } else {
        $obj = null;
    }
    $response[] = $obj;
    $data['content'] = $response;
}

if ($action == 'ajax_getSerials') {
    $data['layout'] = '../layout_blank.tpl.php';
    $icData = $Serials->search($_GET['search']['term']);
    //  $locId = $_GET['locId'];
    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj = null;
            $obj->text = $ic['name'];
            $obj->id = $ic['id'];
            $response['results'][] = $obj;
        }
    } else {
        $obj = null;
        $obj->test = 'No results';
        $obj->id = 0;
        $response['results'][] = $obj;
    }
    $data['content'] = $response;
}

if ($action == 'ajax_getSalesDetails') {
    $salesid = $_GET['salesid'];
    $icData = $Sales->detailedSalesList($user = '', $salesid, $group = true)[0];
    //fetch serialnos
    if ($icData) {
        $icData['issue_date'] = fDate($icData['doc'], 'd M Y H:i');
        $icData['vat_exempted'] = $icData['vat_exempted'] ? 'Yes' : 'No';
        $icData['has_serialno'] = 0;
        foreach ($icData['products'] as $index => $product) {
            if ($icData['approver'] && $product['trackserialno']) $icData['has_serialno'] = 1;
            $icData['products'][$index]['serialnos'] = $SerialNos->find(['sdi' => $product['sdi']]);
        }
        $saleExpense = $Expenses->find(['saleid' => $icData['salesid']]);
        $totalExpense = 0;
        $totalExpense = array_sum(array_column($saleExpense, 'total_amount'));
        $icData['expense_amount'] = $totalExpense;
        $obj->status = 'success';
        $obj->data = $icData;
    } else {
        $obj->status = 'error';
        $obj->msd = 'Invoice not found!';
    }

//    debug($obj);
    $data['content'] = $obj;
}

if ($action == 'ajax_changeSerialNo') {
    $result['status'] = 'success';
    try {
        if (Users::cannot(OtherRights::approve_other_credit_invoice)) throw new Exception("Permission denied");
        $number = $_GET['serial_number'];
        $snoid = $_GET['snoid'];
        $sdi = $_GET['sdi'];
        $sd = $Salesdetails->get($sdi);
        if (!$sd) throw new Exception("Sales info not found!");
        $sd = $Salesdetails->getList('', '', '', '', $sdi)[0];
        $product = $Products->get($sd['productid']);

//        debug($product);
        $serialno = $SerialNos->find(['number' => $number, 'current_stock_id' => $sd['stockid']])[0];

        if ($serialno['id'] == $snoid) {
            $result['status'] = 'same';
        } elseif ($serialno['sdi']) {
            throw new Exception("New serial number already sold");
        } else {
            $oldsno = $SerialNos->get($snoid);
            if ($product['validate_serialno']) {//validates from stock
                if (!$serialno['id']) {
                    throw new Exception("New serial number not found in stock, This product validate serial number from stock");
                } else {
                    //changing serial number
                    $SerialNos->update($serialno['id'], [ //update new serialno
                        'sdi' => $oldsno['sdi'],
                        'salespersonid' => $oldsno['salespersonid'],
                        'dos' => TIMESTAMP,
                    ]);
                    //release old serialno
                    $SerialNos->update($snoid, [
                        'sdi' => null,
                        'salespersonid' => null,
                        'dos' => null,
                    ]);
                }
            } else {//dont validate from stock
                if (!$serialno['id']) { //create new
                    $SerialNos->insert([
                        'number' => $number,
                        'initial_stockid' => $sd['stockid'],
                        'current_stock_id' => $sd['stockid'],
                        'source' => SerialNos::SOURCE_SALE,
                        'createdby' => $_SESSION['member']['id'],
                        'sdi' => $oldsno['sdi'],
                        'salespersonid' => $oldsno['salespersonid'],
                        'dos' => TIMESTAMP,
                    ]);
                } else {
                    //changing serial number
                    $SerialNos->update($serialno['id'], [ //update new serialno
                        'sdi' => $oldsno['sdi'],
                        'salespersonid' => $oldsno['salespersonid'],
                        'dos' => TIMESTAMP,
                    ]);
                }

                //todo how to release old serialno for non validating products
                $SerialNos->update($snoid, [
                    'sdi' => null,
                    'salespersonid' => null,
                    'dos' => null,
                ]);
            }
        }
    } catch (Exception $e) {
        $result['status'] = 'error';
        $result['msg'] = $e->getMessage();
    }
//    debug($result);
    $data['content'] = $result;
}

if ($action == 'ajax_getSalesDocument') {
    $result['status'] = 'success';
    try {
        $salesid = removeSpecialCharacters($_GET['salesid']);
        if (!$salesid) throw new Exception("Invalid sales number");
        $result['data'] = SalesDocuments::$staticClass->getList($salesid);
    } catch (Exception $e) {
        $result['status'] = 'error';
        $result['msg'] = $e->getMessage();
    }
    $data['content'] = $result;
}

if ($action == 'ajax_getInvoiceInstallmentPlans') {
    $salesid = removeSpecialCharacters($_GET['salesid']);
    $result['status'] = 'success';

    $sale = Sales::$saleClass->get($salesid);
    try {
        if (!$sale) throw new Exception("Invoice not found");
        if (!$sale['has_installment']) throw new Exception("Invoice does not have installment plans");

        $sale = Sales::$saleClass->getSalesOutstanding($salesid)[0];

        $sale['paid_amount'] = $sale['full_amount'] - $sale['pending_amount'];
        $installments = SalesInstallmentPlans::$staticClass->withStatus($salesid);

        $result['data'] = [
            'sale' => $sale,
            'installments' => $installments,
        ];
    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
//    debug($result);
    $data['content'] = $result;
}
