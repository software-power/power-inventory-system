<?
if ($action == 'proforma_list') {
    Users::isAllowed();
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $clientid = $_GET['clientid'];
    $issuedby = $_GET['issuedby'];
    $currencyid = $_GET['currencyid'];
    $locationid = $_GET['locationid'];
    $branchid = $_GET['branchid'];
    $proforma_status = $_GET['proforma_status'];
    $stock_holding = $_GET['stock_holding'];
    $proforma_no = $_GET['proforma_no'];


    if (Users::can(OtherRights::approve_other_credit_invoice)) {
        $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    } elseif (Users::can(OtherRights::sale_other_order)) { //user branch
        $branchid = $_SESSION['member']['branchid'];
        $tData['branches'] = $Branches->find(['id' => $_SESSION['member']['branchid']]);
    } else {
        $issuedby = $_SESSION['member']['id'];
        $branchid = $_SESSION['member']['branchid'];
    }


    if ($proforma_no) $fromdate = '';

    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($issuedby) $title[] = "Issued by: " . $Users->get($issuedby)['name'];
    if ($fromdate) $title[] = "From " . fDate($fromdate);
    if ($todate) $title[] = "To " . fDate($todate);

    if ($proforma_status) $title[] = "Proforma status: " . $proforma_status;
    if ($stock_holding == 'yes') $title[] = "Holding Stock";
    if ($stock_holding == 'no') $title[] = "Not holding Stock";
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $tData['proformaList'] = $Proformas->proformaList($proforma_no, $issuedby, $proforma_status, $clientid, $stock_holding, $fromdate, $todate, $locationid, $branchid, $currencyid);
//    debug($tData['proformaList']);
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('proforma_list.tpl.php', $tData);
}

if ($action == 'hold_stock') {
    Users::can(OtherRights::hold_stock, true);
//    debug($_POST);
    $proformaid = $_POST['proforma_no'];
    $holding_days = $_POST['hold_days'];
    $extending = $_POST['extending'] == 1;

    if (!$proforma = $Proformas->withDetails($proformaid)[0]) {
        $_SESSION['error'] = "Proforma not found!";
        redirectBack();
    }
    if ($proforma['sales_status'] == Orders::STATUS_CLOSED) {
        $_SESSION['error'] = "Proforma already closed cant hold stock!";
        redirectBack();
    }

    try {
        //check stock
        define('EXCEPT_PROFORMA', $proformaid);
        foreach ($proforma['details'] as $detail) {
            if ($detail['source'] == 'external' || $detail['non_stock']) continue;
            $current_stock = $Stocks->calcStock(
                $proforma['locid'], "", "",
                "", $detail['productid'], "", "",
                "", "", "", "", "", "",
                "", "", "", "", false, true,
                '', '', true, true
            );
            $current_stock = array_values($current_stock)[0];
            if ($detail['qty'] > $current_stock['total'])
                throw new Exception("Product ({$detail['productname']}) has no enough stock," . PHP_EOL . PHP_EOL . " Proforma cant hold stock");
        }

        $Proformas->update($proformaid, [
            'holding_days' => $extending ? ($proforma['holding_days'] + $holding_days) : $holding_days
        ]);
        $_SESSION['message'] = "Proforma updated successfully";
    } catch (Exception $e) {
        $Proformas->update($proformaid, ['holding_days' => 0]); //cancel holding
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 10000;
    }
    redirect('proformas', 'proforma_list');
}

if ($action == 'cancel_holding') {
    $proformaid = $_POST['proforma_no'];
    if (!$proforma = $Proformas->get($proformaid)) {
        $_SESSION['error'] = "Proforma not found!";
        redirectBack();
    }
    $Proformas->update($proformaid, ['holding_days' => 0]);
    $_SESSION['message'] = "Proforma updated successfully";
    redirect('proformas', 'proforma_list');
}

if ($action == 'create_proforma') {
    $tData['currencies'] = $CurrenciesRates->getCurrency_rates();
    $tData['categories'] = $Categories->getAllActive();
    $tData['defaultLocation'] = $Locations->get($_SESSION['member']['locationid']);
    $_SESSION['delay'] = 5000;

    $proformaid = $_GET['proformaid'];
    $copy = isset($_GET['copy']);
    $tData['COPY'] = $copy;

    if (!$proformaid) {
        Users::can(OtherRights::create_proforma, true);
    } else {
        Users::can(OtherRights::edit_proforma, true);
//        debug('Proforma editing under development');
        if (!$proforma = $Proformas->withDetails($proformaid)[0]) {
            $_SESSION['error'] = "Proforma not found!";
            redirectBack();
        }

        if ($proforma['sales_status'] == Orders::STATUS_CLOSED && !$copy) {
            $_SESSION['error'] = "Proforma already closed!";
            redirectBack();
        }
        $tData['defaultLocation'] = $Locations->get($proforma['locid']);
        $branchid = $Locations->getBranch($proforma['locid'])['id'];
        $hierarchic = $Hierarchics->get($_SESSION['member']['hierachicid']);
        $exchange_rate = $CurrenciesRates->find(['currencyid' => $proforma['currencyid']])[0]['rate_amount'];  //current exchange rate
        foreach ($proforma['details'] as $index => $detail) {
            $prices = $Products->getPrices($branchid, $hierarchic['level'], $detail['productid']);
//            $prices['minimum'] = IS_ADMIN || $detail['non_stock'] ? 0 : $prices['minimum'];
            $prices['minimum'] = 0;  //proforma no hierarchic restriction
            $proforma['details'][$index]['base_price'] = round($detail['price'] * $exchange_rate, 2);
            $proforma['details'][$index]['min_base_price'] = $prices['minimum'];
            $proforma['details'][$index]['min_price'] = round($prices['minimum'] / $exchange_rate, 2);
            $proforma['details'][$index]['min_incprice'] = round($prices['minimum'] * (1 + $detail['vat_rate'] / 100) / $exchange_rate, 2);
        }
//        debug($proforma);
        $tData['proforma'] = $proforma;
    }

    $data['content'] = loadTemplate('proforma_create.tpl.php', $tData);
}

if ($action == 'save_proforma') {
    $proforma = $_POST['proforma'];
    $productIds = $_POST['productid'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $incprice = $_POST['incprice'];
    $sinc = $_POST['sinc'];
    $vat_rate = $_POST['vat_rate'];
    $print_extra = $_POST['print_extra'];
    $product_description = $_POST['product_description'];
    $external_product = $_POST['external'];

    validate($proforma);

//    debug($_POST);
    if ($proforma['paymentterms'] == PAYMENT_TYPE_CREDIT && (!$proforma['payment_days'] || $proforma['payment_days'] <= 0)) {
        $proforma['payment_days'] = 30;
    }

    if (!$proforma['id']) {//new
        if ($Proformas->find(['token' => $proforma['token']])) {
            $_SESSION['error'] = "System found this form has already been sent, transaction canceled to avoid duplicate proformas!";
            redirect('proformas', 'proforma_list');
        }
        $proforma['createdby'] = $_SESSION['member']['id'];
        $Proformas->insert($proforma);
        $proformaid = $Proformas->lastId();
    } else {
        $proformaid = $proforma['id'];

        if (!$current = $Proformas->get($proformaid)) {
            $_SESSION['error'] = "Proforma not found";
            redirectBack();
        }
        if ($current['sales_status'] != Orders::STATUS_PENDING) {
            $_SESSION['error'] = "Proforma already closed";
            redirectBack();
        }
//        debug('editing under development');
        $proforma['modifiedby'] = $_SESSION['member']['id'];
        $proforma['dom'] = TIMESTAMP;
        $Proformas->update($proformaid, $proforma);

        //clear previous details
        $Salesdescriptions->deleteWhereMany(['pdi' => array_column($Proformadetails->find(['proformaid' => $proformaid]), 'id')]);
        $Proformadetails->deleteWhere(['proformaid' => $proformaid]);
    }

    foreach ($productIds as $index => $productid) {
        $Proformadetails->insert([
            'proformaid' => $proformaid,
            'productid' => $productid,
            'qty' => $qty[$index],
            'price' => removeComma($price[$index]),
            'sinc' => $sinc[$index],
            'incprice' => removeComma($incprice[$index]),
            'vat_rate' => $vat_rate[$index],
            'print_extra' => in_array($productid, $print_extra),
            'createdby' => $_SESSION['member']['id'],
        ]);
        $pdi = $Proformadetails->lastId();

        if (in_array($productid, $print_extra)) {
            $Salesdescriptions->insert([
                'pdi' => $pdi,
                'description' => $product_description[$index],
            ]);
        }
    }

    foreach ($external_product['productname'] as $index => $productname) {
        $Proformadetails->insert([
            'proformaid' => $proformaid,
            'productname' => $productname,
            'qty' => $external_product['qty'][$index],
            'price' => removeComma($external_product['price'][$index]),
            'sinc' => $external_product['sinc'][$index],
            'incprice' => removeComma($external_product['incprice'][$index]),
            'vat_rate' => $external_product['vat_rate'][$index],
            'taxcategory' => $external_product['tax_category'][$index],
            'createdby' => $_SESSION['member']['id'],
        ]);
    }

    $_SESSION['message'] = "Proforma " . ($proforma['id'] ? 'updated' : 'saved') . " successfully";
    redirect('proformas', 'print_proforma', ['proforma_number' => $proformaid, 'redirect' => base64_encode(url('proformas', 'proforma_list'))]);
}

if ($action == 'print_proforma') {
    $proformaid = $_GET['proforma_number'];
    if ($proforma = $Proformas->withDetails($proformaid)[0]) {
        $proforma['total_excamount'] = array_sum(array_column($proforma['details'], 'excamount'));
        $proforma['total_vatamount'] = array_sum(array_column($proforma['details'], 'vatamount'));
        $proforma['total_incamount'] = array_sum(array_column($proforma['details'], 'incamount'));

        $location = $Locations->get($proforma['locid']);
        $address = $location['address'];
        $address = explode(PHP_EOL, $address);
        if (count($address) > 1) {
            define("LOCATION_ADDRESS", $address);
        }

        $data['proforma'] = $proforma;


        if (isset($_GET['with_bank_info'])) {
            $bankids = explode(',', $location['bankids']);
            $banks = Banks::$banksClass->findMany(['id' => $bankids]);
            $data['banks'] = $banks;
        }
        $data['layout'] = isset($_GET['with_image']) ? 'proforma_invoice_print_image.tpl.php' : 'proforma_invoice_print.tpl.php';
    } else {
        debug('Proforma not found!');
    }
}
