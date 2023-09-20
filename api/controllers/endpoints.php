<?

if ($action == 'clients') {
    if (isset($request_data['for_support'])) $tData['for_support'] = 1;
    $clientid = $request_data['clientid'] ?: removeSpecialCharacters($_GET['clientid']);
    $client = Clients::$clientClass->get($clientid);
    $acc_mng = Users::$userClass->get($client['acc_mng']);
    $client['account_manager'] = $acc_mng['name'];
//    debug($client);
    $tData['client'] = $client;
    echo loadTemplate('client-add.tpl.php', $tData);
}

if ($action == 'ajax_getClients') {
    $clientname = escapeChar($_GET['clientname']);
    $tinno = escapeChar($_GET['tinno']);
    $vatno = escapeChar($_GET['vatno']);


    $result['status'] = 'success';
    try {
        if ($tinno == '999999999' || $tinno == '000000000') $tinno = '';

        $search = $clientname ?: $tinno ?: $vatno;
        $found = [];
        if (!empty($search))
            $found = Clients::$clientClass->withRoyaltyCardInfo('', $search);
        $result['data'] = $found;
    } catch (Exception $e) {
        $result = ['status' => 'error', 'msg' => $e->getMessage()];
    }
    json_response($result);

}

if ($action == 'ajax_getUsers') {
    $search = escapeChar($_GET['search']['term']);


    $result['status'] = 'success';
    try {
        $found = $Users->search($search);
        if ($found) {
            $result['results'] = array_map(function ($u) {
                return ['id' => $u['id'], 'text' => $u['name']];
            }, $found);
        } else {
            throw new Exception('No result');
        }
    } catch (Exception $e) {
        $result['results'] = ['id' => 0, 'text' => $e->getMessage()];
    }
    json_response($result);

}

if ($action == 'ajax_saveClients') {
    required_method('POST');
    $for_support = isset($request_data['for_support']);
    $client = $request_data['client'];
    $data = $request_data['data'];
    $sclient = $data['support_client'];


    $result['status'] = 'success';
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        //from support mapping
        if (!empty($sclient)) {
            $client = [
                'name' => $sclient['name'],
                'tinno' => $sclient['tinno'],
                'vatno' => $sclient['vatno'],
                'mobile' => $sclient['mobile'],
                'tel' => $sclient['tel'],
                'plotno' => $sclient['plotno'],
                'district' => $sclient['district'],
                'street' => $sclient['street'],
                'address' => $sclient['address'],
                'city' => $sclient['city'],
                'country' => $sclient['country'],
                'email' => $sclient['email'],
            ];
        }


        if (!CS_MAIN_SYSTEM) throw new Exception("Not a main system!");
        if (empty($client)) throw new Exception('Invalid inputs');
//        debug($client);
        $contacts = $client['contacts'];
        unset($client['contacts']);
        if (empty($client['id'])) {

            if (Clients::$clientClass->countWhere(['name' => $client['name']]) > 0) throw new Exception("Client name already exists");

            $client['acc_mng'] = AUTH_USER['id'];
            $client['createdby'] = AUTH_USER['id'];
            if (!Clients::$clientClass->insert($client)) throw new Exception("Failed to save client!");
            $clientid = Clients::$clientClass->lastId();

            foreach ($contacts as $c) {
                Contacts::$contactClass->insert([
                    'clientid' => $clientid,
                    'name' => $c['fullname'],
                    'email' => $c['fullemail'],
                    'mobile' => $c['fullmobile'],
                    'position' => $c['fullposition'],
                ]);
            }
            $result['msg'] = "Client created";
        } else {
            $current_client = Clients::$clientClass->get($client['id']);
            if (!$current_client) throw new Exception("Client not found for update!");
            if (!Clients::$clientClass->update($client['id'], $client)) throw new Exception("Failed to update client!");
            Contacts::$contactClass->deleteWhere(['clientid' => $client['id']]);//clear old contacts
            $clientid = $client['id'];
            $result['msg'] = "Client updated";
        }
        mysqli_commit($db_connection);

        $result['data']['clientid'] = $clientid;
        $response = Clients::postToSubSystem($clientid);

        $support_mapping[] = [ //from main system
            'clientcode' => $clientid,
            'support_name' => CS_SUPPORT_NAME,
        ];
        foreach ($response['responses'] as $r) {
            if ($r['status'] === 'success') {
                $support_mapping[] = $r['support_data'];
                $result['msg'] .= "\r\n\r\n" . $r['msg'];
            } else {
                $result['response_error'] .= $r['msg'] . "\n\n";
            }
        }

        define('USERCODE', AUTH_USER['id']);
        if (!empty($sclient)) { //from support mapping screen
            //support
            $response = Clients::mapToSupport($sclient['id'], $support_mapping);
            if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
                $result['msg'] .= "\n\r" . ($response['data']['message'] ?: $response['msg']);
            } else {
                $result['response_error'] .= "Support:  " . $response['data']['message'] ?: $response['msg'] . "\n\n";
            }
        } else {
            if (!$for_support) { //no send to support
                //support
                $response = Clients::postToSupport($clientid, $support_mapping);
                if ($response['status'] == 'success') {
                    $result['msg'] .= "\r\n\r\n" . $response['msg'];
                } else {
                    $result['response_error'] .= "Support:  " . $response['msg'] . "\n\n";
                }
            }
        }
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $result = ['status' => 'error', 'msg' => $e->getMessage()];
    }
    json_response($result);

}


//support mapping
if ($action == 'support_mapping_screen') {
    $data = $request_data['data'];
    $sclient = $data['support_client'];
    if (empty($sclient)) debug("<h3>NO CLIENT DATA FOUND!</h3>");
//    debug($sclient);
    $matches = [];
    $metadata = [];

    //TIN
    $tinno = removeSpecialCharacters($sclient['tinno']);
    $tinno = trim($tinno);
    if (!empty($tinno) && $sclient['tinno'] != '000000000' && $tinno != '999999999') {
        $byTIN = Clients::$clientClass->withRoyaltyCardInfo('', $tinno, true);
        if (is_array($byTIN)) {
            $matches = array_merge($matches, $byTIN);
            $metadata['tin'] = count($byTIN);
        }
    }

    //VRN
    $vatno = removeSpecialCharacters($sclient['vatno']);
    $vatno = trim($vatno);
    if (!empty($sclient['vatno'])) {
        $byVATNo = Clients::$clientClass->withRoyaltyCardInfo('', $vatno, true);
        $byVATNo = array_filter($byVATNo, function ($c) use ($matches) {
            return !in_array($c['id'], array_column($matches, 'id'));
        });
        if (is_array($byVATNo)) {
            $matches = array_merge($matches, $byVATNo);
            $metadata['vrn'] = count($byVATNo);
        }
    }


    //mobile no
    $mobile = removeSpecialCharacters($sclient['mobile']);
    $mobile = str_split(trim($mobile));
    $mobile = array_slice($mobile, -9);
    $mobile = implode('', $mobile);
    if (!empty($mobile)) {
        $byMobile = Clients::$clientClass->withRoyaltyCardInfo('', "", true, '', $mobile);
        $byMobile = array_filter($byMobile, function ($c) use ($matches) {
            return !in_array($c['id'], array_column($matches, 'id'));
        });
        if (is_array($byMobile)) {
            $matches = array_merge($matches, $byMobile);
            $metadata['mobile'] = count($byMobile);
        }
    }


    //Name
    $SKIP_WORDS = ["GENERAL", "SUPPLY", "SUPPLIES", "LIMITED", "LTD", "L.T.D", "CO", "CO.", "T/A"];
    $SKIP_WORDS = [];

    $names = explode(' ', trim($sclient['name']));

    $names = array_filter($names, function ($n) use ($SKIP_WORDS) {
        return !in_array($n, $SKIP_WORDS) && !empty($n) && strlen($n) > 1;
    });


    $name_chunks = array_chunk($names, 2);
//    debug($name_chunks);
    global $db_connection;
    foreach ($name_chunks as $index => $n) {
        if (count($n) <= 1) break;
        $n = implode(' ', $n);
        $n = mysqli_real_escape_string($db_connection, $n);
        if (!empty($n)) {
            $byName = Clients::$clientClass->withRoyaltyCardInfo('', $n, true);

            $byName = array_filter($byName, function ($c) use ($matches) {
                return !in_array($c['id'], array_column($matches, 'id'));
            });
            if (is_array($byName)) {
                $matches = array_merge($matches, $byName);
                $metadata['name'] += count($byName);
            }
        }
    }

//    debug($metadata);
    $tData['sclient'] = $sclient;
    $tData['possible_matches'] = $matches;
    echo loadTemplate('client-code-mapping.tpl.php', $tData);
}

if ($action == 'ajax_mapSupportClient') {
    //IN MAIN SYSTEM
    required_method('POST');
    $client_maincode = $request_data['client_maincode'];
    $data = $request_data['data'];
    $sclient = $data['support_client'];

    $result['status'] = 'success';
    try {
        if (empty($sclient['id'])) throw new Exception("Invalid support client ID");
        if (empty($client_maincode)) throw new Exception("Invalid mainclient ID");
        $response = Clients::getSubSupportCode($client_maincode);
//        debug($response);
        $support_mapping[] = [ //from main system
            'clientcode' => $client_maincode,
            'support_name' => CS_SUPPORT_NAME,
        ];
        foreach ($response['responses'] as $r) {
            if ($r['status'] === 'success') {
                $support_mapping[] = $r['support_mapping'];
            }
        }
        //support
        define('USERCODE', AUTH_USER['id']);
        $response = Clients::mapToSupport($sclient['id'], $support_mapping);
//        debug($response);
        if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
            $result['msg'] = ($response['data']['message'] ?: $response['msg']);
        } else {
            throw new Exception($response['data']['message'] ?: $response['msg']);
        }
    } catch (Exception $e) {
        $result = ['status' => 'error', 'msg' => $e->getMessage()];
    }
    json_response($result);

}

//pos screen
if ($action == 'pos_display') {

    $location = Locations::$locationClass->get(CS_POS_DISPLAY_LOCATION);
    if (!$location) debug("<h2>POS SCREEN DISPLAY LOCATION NOT SET</h2>");


//    debug($_GET);
    $barcode = removeSpecialCharacters($_GET['barcode']);
    $productcode = removeSpecialCharacters($_GET['productcode']);

    try {
        if ($barcode || $productcode) {
            if ($barcode) $product = Products::$productClass->byBarcode($barcode);
            if ($productcode) $product = Products::$productClass->get($productcode);
            if (empty($product)) throw new Exception("Product Not found");
            $product = Products::$productClass->getList($product['id'])[0];

            //currency
            $product['currency'] = Currencies::$currencyClass->find(['base' => 'yes'])[0]['name'];

            //price
            $quick_price = CurrentPrices::$currentPricesClass->quickPriceList($location['branchid'], $product['id']);
            $product['inc_quicksale_price'] = $quick_price[0]['inc_quicksale_price'];
            //stock
            $stock = $Stocks->calcStock(
                $location['id'], '', '', '', $product['id'], '', '',
                '', '', '', '', '', '', '', '', '',
                '', true, $group_batch = true, '', '', true);
            $stock = $stock ? array_values($stock)[0] : [];
            $product['stock_qty'] = $stock['total'] ?: 0;
            $product['batches'] = $stock['batches'] ?: [];
//            debug($product);
            $tData['product'] = $product;

        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }


    echo loadTemplate('pos-display-screen.tpl.php', $tData);
}

if ($action == 'print_barcode') {

    try {
        $location = Locations::$locationClass->get(CS_POS_DISPLAY_LOCATION);
        if (!$location) throw new  Exception("<h2>POS SCREEN DISPLAY LOCATION NOT SET</h2>");

        $productcode = removeSpecialCharacters($_GET['productcode']);
        $product = Products::$productClass->get($productcode);
        if (empty($product)) throw new Exception("Product Not found");


        $barcode_settings = $Settings2->get(1)['barcode_settings'];
        if (!$barcode_settings) throw new Exception("Barcode setting not found");
        $barcode_settings = base64_decode($barcode_settings);
        $barcode_settings = json_decode($barcode_settings, true);
//debug($barcode_settings);
        //currency
        //price
        $quick_price = CurrentPrices::$currentPricesClass->quickPriceList($location['branchid'], $product['id']);

        $barcode_data['barcode_text'] = $product['barcode_office'] ?: $product['barcode_manufacture'];
        $barcode_data['product_name'] = $product['name'];
        $barcode_data['quick_price'] = $quick_price[0]['inc_quicksale_price'];
        $barcode_data['qty'] = 1;
        $barcode_data['currencyname'] = Currencies::$currencyClass->find(['base' => 'yes'])[0]['name'];


        $stock = $Stocks->calcStock(
            $location['id'], '', '', '', $product['id'],
            '', '', '', '', '', '',
            '', '', '', '', '', '',
            false, true, '', '', true
        );
        $stock = array_values($stock)[0];
        $barcode_data['nearby_expiry'] = '';
        if ($stock) {
            $batches = array_values($stock['batches']);
            $barcode_data['nearby_expiry'] = $batches[0]['expire_date'];
        }

//debug($barcode_data);
        $tData['barcode_settings'] = $barcode_settings;
        $tData['barcode_data'] = $barcode_data;
    } catch (Exception $e) {
        debug($e->getMessage());
    }
    echo loadTemplate('print_api_barcode.tpl.php', $tData);
}


if ($action == 'ajax_searchProduct') {
    $search = $_GET['search'];
    $non_stock = $_GET['non_stock'];
    $products = Products::$productClass->getList('', $search, '', '', '',
        '', '', '', '', $non_stock, '', '', 'active');
    $response['result'] = $products;
    json_response($response);
}