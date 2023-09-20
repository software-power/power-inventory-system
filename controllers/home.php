<?

if ($action == 'index') {
    if (ScriptLogs::$class->countWhere(['date' => TODAY]) == 0) {
        //run script in background
        ScriptLogs::run();
    }


    $userdata = $_SESSION['member'];
    $tData['username'] = $userdata['username'];
    $tData['name'] = $userdata['name'];
    $tData['mobile'] = $userdata['mobile'];
    $tData['email'] = $userdata['email'];
    $tData['head'] = $userdata['head'];

    $tData['branch'] = $Branches->get($_SESSION['member']['branchid']);
    $tData['location'] = $Locations->get($_SESSION['member']['locationid']);
    $tData['department'] = $Departments->get($_SESSION['member']['deptid']);
    $tData['role'] = $Roles->get($_SESSION['member']['roleid']);


    $weektodate = date('Y-m-d');
    $weekfromdate = date('Y-m-d', strtotime('1 week ago'));
    $tData['weektodate'] = $weektodate;
    $tData['weekfromdate'] = $weekfromdate;

    $monthtodate = date('Y-m-d');
    $monthfromdate = date('Y-m-d', strtotime('1 month ago'));
    $tData['monthtodate'] = $monthtodate;
    $tData['monthfromdate'] = $monthfromdate;
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();

    if (IS_ADMIN) {
        $fromdate = $todate = TODAY;

        $credit_sales = $Sales->dashboardSales("", "", $fromdate, $todate, PAYMENT_TYPE_CREDIT);
        $cash_sales = $Sales->dashboardSales("", "", $fromdate, $todate, PAYMENT_TYPE_CASH);

        $tData['credit_sales'] = $credit_sales;

        //get total amounts by currency
        $tData['total_credits'] = [];
        foreach ($credit_sales as $item) {
            $tData['total_credit'][$item['currency_name']] += $item['full_amount'];
        }

        $tData['cash_sales'] = $cash_sales;
        //get total amounts by currency
        $tData['total_credits'] = [];
        foreach ($cash_sales as $item) {
            $tData['total_cash'][$item['currency_name']] += $item['full_amount'];
        }

        // Most Sold product
        $fromdate = date('Y-m-d', strtotime(TODAY . ' -1 month'));

//        debug(Dashboard::cachingItems());
        try {
            if ($CachePool->hasItem(CACHE_FILES['dashboard'])) {
                $dashboardCache = $CachePool->getItem(CACHE_FILES['dashboard']);
                $cache_data=$dashboardCache->get();
            } else {
                $dashboardCache = $CachePool->getItem(CACHE_FILES['dashboard']);
                $cache_data = Dashboard::cachingItems();

                $dashboardCache->set($cache_data);
                $dashboardCache->expiresAfter(60*3); //3 minutes
                $CachePool->save($dashboardCache);
            }

        } catch (\Psr\SimpleCache\InvalidArgumentException $e) {
            logData("Cache error: " . $e->getMessage());
        }catch (\Psr\Cache\InvalidArgumentException $e) {
            logData("Cache error: " . $e->getMessage());
        } catch (Exception $e) {
            logData("Error: " . $e->getMessage());
        }
//        debug("done");
        $tData['topsales'] = $cache_data['topsales'];
        $tData['topProducts'] = $cache_data['topProducts'];
        $tData['topProductsOut'] = $cache_data['topProductsOut'];
//         debug($topsales);
        $tData['totalOrders'] = Orders::$staticClass->countWhere(['sales_status'=>Orders::STATUS_PENDING,'status'=>'active']);
    } else {
        $todaySales = $Sales->salesList('', $userdata['id'], TODAY);

        $tData['todayTotalSales'] = array_sum(array_column($todaySales, 'base_full_amount'));
    }
    $_SESSION['pagetitle'] = CS_COMPANY . " - Dashboard";
    $data['content'] = loadTemplate('dashboard.tpl.php', $tData);
}

if ($action == 'cs_index') {
    $data['help'] = 'The company settings are given on this screen.<br/>';
    $tData['reciepts'] = $RecieptsTypes->getAll();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Company Settings";

    //for default values
    $tData['taxes'] = $Categories->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive();
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive();
    $tData['unit'] = $Units->get(CS_DEFAULT_UNIT);
    $tData['bulkUnits'] = $BulkUnits->find(['unit' => CS_DEFAULT_UNIT]);
    $tData['locations'] = $Locations->getAllActive();
    $tData['basePrice'] = $Currencies->find(['base' => 'yes'])[0];

    $data['content'] = loadTemplate('home.tpl.php', $tData);
}

if ($action == 'settings_save') {
//    debug([$_POST,$_FILES]);
    $reciepttypes = $RecieptsTypes->getAll();

    foreach ($reciepttypes as $key => $rt) {
        $RecieptsTypes->update($rt['id'], array('status' => 'inactive'));
    }

    $reciepts = $_POST['reciepts'];
    foreach ($reciepts as $key => $r) {
        $RecieptsTypes->update($r, array('status' => 'active'));
    }

    $miniData = $_POST['cs'];
    $cs2 = $_POST['cs2'];
    $cs2['efd_shared_dir'] = str_replace('\\', '\\\\', $cs2['efd_shared_dir']);
    $cs2['sales_agreement'] = base64_encode($cs2['sales_agreement']);
//    debug($cs2);
    $cs2['tra_rc'] = $_POST['cs2']['tra_rc'] ? 1 : 0;
    $cs2['show_generic_name'] = $cs2['show_generic_name'] ? 1 : 0;
    $cs2['show_category'] = $cs2['show_category'] ? 1 : 0;
    $cs2['show_brand'] = $cs2['show_brand'] ? 1 : 0;
    $cs2['show_department'] = $cs2['show_department'] ? 1 : 0;

    $uploaddir = 'images/company/';

    if (!is_dir($uploaddir)) mkdir($uploaddir, 755, true);
    //logo img
    if (!empty ($_FILES["clogo"]["name"])) {
        $logo_filename = $uploaddir . $_FILES["clogo"]["name"];
        move_uploaded_file($_FILES["clogo"]["tmp_name"], $logo_filename);
        $miniData['logo'] = $logo_filename;
    }
    //footer img
    if (!empty ($_FILES["footer_img"]["name"])) {
        $footer_filename = $uploaddir . $_FILES["footer_img"]["name"];
        move_uploaded_file($_FILES["footer_img"]["tmp_name"], $footer_filename);
        $miniData['footer_img'] = $footer_filename;
    }

    $Settings->update(1, $miniData);
    $Settings2->update(1, $cs2);

    if ($miniData['vfd_type'] == VFD_TYPE_ZVFD) { //update all product tax categoryid
        $categoryid = escapeChar($miniData['zvfd_taxcategoryid']);
        $sql = "update products set categoryid = $categoryid where  taxcode = '' or taxcode is null";
        if (executeQuery($sql)) $msg = "\n\nAll product tax category updated";
    }


    $_SESSION['message'] = "Company Settings Changed $msg";
    redirect('home', 'cs_index', ['tab' => $_POST['tabname']]);
}

//system apis
if ($action == 'system_tokens') {
    Users::isAllowed();
    $tData['system_tokens'] = SystemTokens::$staticClass->getAll();
    $data['content'] = loadTemplate('system_tokens.tpl.php', $tData);
}

if ($action == 'save_system_tokens') {
    Users::isAllowed();
//    debug($_POST);
    $subid = $_POST['subid'];
    $do = $_POST['do'];
    $names = $_POST['name'];
    $endpoint = $_POST['endpoint'];
    $token = $_POST['token'];

    validate($names);
    validate($endpoint);
    validate($token);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        foreach ($names as $index => $name) {
            if (empty($subid[$index])) {
                SystemTokens::$staticClass->insert([
                    'name' => $name,
                    'endpoint' => $endpoint[$index],
                    'token' => removeSpecialCharacters($token[$index]),
                    'createdby' => $_SESSION['member']['id'],
                ]);
            } else {
                if ($do[$index] == 'delete') {
                    SystemTokens::$staticClass->update($subid[$index], [
                        'modifiedby' => $_SESSION['member']['id'],
                        'dom' => TIMESTAMP,
                        'status' => 'inactive'
                    ]);
                } else {
                    SystemTokens::$staticClass->update($subid[$index], [
                        'name' => $name,
                        'endpoint' => $endpoint[$index],
                        'token' => removeSpecialCharacters($token[$index]),
                        'modifiedby' => $_SESSION['member']['id'],
                        'dom' => TIMESTAMP,
                        'status' => 'active'
                    ]);
                }
            }
        }
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Saved successfully";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }

    redirectBack();
}

if ($action == 'save_main_system_info') {
    Users::isAllowed();
    $token = $_POST['token'];
    $url = $_POST['main_url'];
    $public_url = $_POST['main_public_url'];
    validate($token);
    validate($url);

    try {
        if (!$Settings2->update(1, ['main_system_token' => $token, 'main_system_url' => $url, 'main_system_public_url' => $public_url])) throw new Exception('Failed to update main system info');
        $_SESSION['message'] = 'Saved successfully';
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}

//inactive user
if ($action == 'inactive') {
    $data['layout'] = 'layout_inactive.tpl.php';
}


if ($action == 'ajax_generateSystemToken') {
    Users::isAllowed();
    $request = $_POST['request'];
    $result['status'] = 'success';
    try {
        if ($request == 'generate') {
            $system_token = unique_token(200);
            if (!$Settings2->update(1, [
                'system_token' => $system_token
            ])) throw new Exception("Failed to generate system token");

            $result['data'] = $system_token;

        }
    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
    $data['content'] = $result;
}

if ($action == 'ajax_testMainSystemConnection') {
    Users::isAllowed();
    $main_token = $_POST['main'];
    $main_url = $_POST['url'];
    $result['status'] = 'success';
    $main_token = base64_encode($main_token);
    $system_token = base64_encode(CS_SYSTEM_TOKEN);

    $main_url .= SystemTokens::TEST_MAIN_SYSTEM_URL;
    try {
        $response = sendHttpRequest($main_url, json_encode(['main' => $main_token, 'system' => $system_token]), 'POST');
        if ($response['status'] != 'success') throw new Exception($response['msg']);
        if ($response['data']['status'] != 'success') throw new Exception($response['data']['msg']);
        $result['msg'] = $response['data']['msg'];
    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
    $data['content'] = $result;
}

if ($action == 'ajax_testSubSystemConnection') {
    Users::isAllowed();
    $sub_token = $_POST['sub_token'];
    $sub_url = $_POST['sub_url'];
    $result['status'] = 'success';
    $main_token = base64_encode(CS_SYSTEM_TOKEN);
    $sub_token = base64_encode($sub_token);

    $sub_url .= SystemTokens::TEST_SUB_SYSTEM_URL;
    try {
        $response = sendHttpRequest($sub_url, json_encode(['main' => $main_token, 'sub_token' => $sub_token]), 'POST');
        if ($response['status'] != 'success') throw new Exception($response['msg']);
        if ($response['data']['status'] != 'success') throw new Exception($response['data']['msg']);
        $result['msg'] = $response['data']['msg'];
    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
    $data['content'] = $result;
}

if ($action == 'ajax_hidehelp') {

    $upData['help'] = 0;
    $Users->update($_SESSION['member']['id'], $upData);

    $obj = null;
    $obj->msg = 'done';

    $response[] = $obj;
    $data['content'] = $response;

}

if ($action == 'ajax_sendMsg') {

    $msg['msgto'] = $_GET['to'];
    $msg['message'] = $_GET['message'];
    $msg['msgfrom'] = $_SESSION['member']['id'];
    $Messages->insert($msg);

    $obj = null;
    $obj->msg = 'done';

    $response[] = $obj;
    $data['content'] = $response;
}


if ($action == 'ajax_markRead') {

    $id = $_GET['id'];
    $Messages->delete($id);

    $obj = null;
    $obj->msg = 'done';

    $response[] = $obj;
    $data['content'] = $response;
}