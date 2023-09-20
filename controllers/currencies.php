<?
if ($action == 'currency_list') {
    Users::isAllowed();
    $tData['currency_list'] = $CurrenciesRates->getCurrency_rates();
    $data['content'] = loadTemplate('currency_list.tpl.php', $tData);
}

if ($action == 'save_currency') {
//    debug($_POST);
    $currency = $_POST['currency'];
    $rateid = $_POST['rateid'];
    $exchange_rate = $_POST['exchange_rate'];

//validate
    validate($currency);

    $id = $_POST['id'];
    if (!$currency['id']) {//new
        $exists = $Currencies->find(array('name' => $currency['name']));
        if ($exists) {
            $_SESSION['error'] = "Currency {$currency['name']} Already Exists";
            redirect('currencies', 'currency_list');
        }
        $currency['base'] = 'no';
        $currency['createdby'] = $_SESSION['member']['id'];
        $Currencies->insert($currency);
        $currencyid = $Currencies->lastId();

        $CurrenciesRates->insert([
            'currencyid' => $currencyid,
            'rate_amount' => $exchange_rate,
            'status' => $currency['status'],
            'createdby' => $_SESSION['member']['id'],
        ]);

    } else {//updating
        $currency['modifiedby'] = $_SESSION['member']['id'];
        $Currencies->update($currency['id'], $currency);

        $CurrenciesRates->update($rateid, [
            'currencyid' => $currency['id'],
            'rate_amount' => $exchange_rate,
            'status' => $currency['status'],
            'createdby' => $_SESSION['member']['id'],
        ]);
    }

    $_SESSION['message'] = 'Currency ' . ($currency['id'] ? 'Updated' : 'Added') . ' successfully';
    redirect('currencies', 'currency_list');
}

if ($action == 'ajax_getCurrentExchangeRate') {
    $currency_rateid = $_GET['rateid'];
    $obj->status = 'success';
    if ($rate = $CurrenciesRates->getCurrency_rates($currency_rateid)) {
        $obj->data = $rate;
    } else {
        $obj->status = 'error';
        $obj->msg = "Rate not found!";
    }
    $data['content'] = $obj;
}
