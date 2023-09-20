<?

use Firebase\JWT\JWT;

if ($action == "save_client") { //subsystem
    required_method("POST");
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (CS_MAIN_SYSTEM) throw new Exception("Not a sub system");
//        logData(json_encode($request_data), 'clients.log');
        //clear special characters
        $new_client = $request_data;
        $contacts = $new_client['contacts'];
        unset($new_client['contacts']);

        foreach ($new_client as $index => $item) {
            $new_client[$index] = str_replace(["'", '"'], '', $item);
        }

        //check account manager
        $acc_mng_code = $new_client['acc_mng_code'];
        unset($new_client['acc_mng_code']);
        if (!empty($acc_mng_code)) {
            $accmng = Users::$userClass->find(['code' => $acc_mng_code]);
            if (!empty($accmng)) {
                $new_client['acc_mng'] = $accmng[0]['id'];
            }
        }

        $existing_client = Clients::$clientClass->find(['code' => $new_client['code']]);
        if (!empty($existing_client)) {
            $clientid = $existing_client[0]['id'];
            Clients::$clientClass->update($clientid, $new_client);

            //clear old contacts
            Contacts::$contactClass->deleteWhere(['clientid' => $clientid]);
        } else {

            //check createdby
            $createdby = $new_client['createdby'];
            unset($new_client['createdby']);
            $user = Users::$userClass->find(['code' => $createdby]);
            $new_client['createdby'] = $user[0]['id'] ?: AUTH_USER['id'];

            Clients::$clientClass->insert($new_client);
            $clientid = Clients::$clientClass->lastId();
        }
        foreach ($contacts as $c) {
            Contacts::$contactClass->insert([
                'name' => $c['name'],
                'email' => $c['email'],
                'mobile' => $c['mobile'],
                'position' => $c['position'],
                'clientid' => $clientid,
            ]);
        }

        mysqli_commit($db_connection);
        json_response([
            'status' => 'success',
            'msg' => 'Client ' . (empty($existing_client) ? 'created' : 'updated') . ' successfully',
            'support_data' => [
                'clientcode' => $clientid,
                'support_name' => CS_SUPPORT_NAME,
            ]
        ]);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        json_response(['status' => 'error', 'msg' => $e->getMessage()]);
    }

}

if ($action == 'sub_send_support') { //subsystem request to send client to support

    //THIS IS FOR MAIN SYSTEM

    required_method("POST");
//    json_response($request_data);

    $client_maincode = removeSpecialCharacters($request_data['client_maincode']);
    $support_mapping = $request_data['support_mapping']; //from subsystem

    $result['status'] = 'success';
    try {
        if (!Clients::$clientClass->get($client_maincode)) throw new Exception("Client with main code $client_maincode not found!");

        if (!is_array($support_mapping)) throw new Exception("Invalid support mapping from subsystem!");

        $support_mapping[] = [ //from main system
            'clientcode' => $client_maincode,
            'support_name' => CS_SUPPORT_NAME,
        ];

        //support
        define('USERCODE', AUTH_USER['id']);
        $response = Clients::postToSupport($client_maincode, $support_mapping);
        if ($response['status'] == 'success') {
            $result['msg'] = $response['msg'];
        } else {
            throw new Exception("From main system: " . $response['msg']);
        }

        json_response($result);
    } catch (Exception $e) {
        json_response(['status' => 'error', 'msg' => $e->getMessage()]);
    }


}

if ($action == 'sub_return_support_code') { //subsystem return support mapping to main system
    //IN SUBSYSTEM
    required_method("POST");
//    json_response($request_data);

    $client_maincode = removeSpecialCharacters($request_data['client_maincode']);

    $result['status'] = 'success';
    try {
        $client = Clients::$clientClass->find(['code' => $client_maincode]);
        if (!$client) throw new Exception("Client with main code $client_maincode not found!");


        $result['support_mapping'] = [
            'clientcode' => $client[0]['id'],
            'support_name' => CS_SUPPORT_NAME,
        ];

        json_response($result);
    } catch (Exception $e) {
        json_response(['status' => 'error', 'msg' => $e->getMessage()]);
    }


}

if ($action == "get_screen_token") {
    required_method("POST");
//    json_response($request_data);
    $sub_token = REQUEST_HEADERS['sub-token'] ? base64_decode(REQUEST_HEADERS['sub-token']) : '';
    $sub_system = SystemTokens::$staticClass->find(['token' => $sub_token, 'status' => 'active']);
    if (empty($sub_system)) json_response(["status" => "error", "msg" => 'Sub system not found']);
    $payload = [
        'usercode' => AUTH_USER['id'],
        'request' => $request_data['request'],
        'time' => time()
    ];
    if (isset($_GET['mainclientcode'])) $payload['mainclientcode'] = $_GET['mainclientcode'];
    if (isset($_GET['for_support'])) $payload['for_support'] = 1;
    $token = JWT::encode($payload, CS_SYSTEM_TOKEN, 'HS256');
    json_response(["status" => "success", "_token" => $token]);
}

if ($action == "get_support_mapping_token") {
    required_method("POST");
    if (!CS_MAIN_SYSTEM) json_response(["status" => "error", "msg" => 'Not main system!']);
    if (empty($request_data['support_client'])) json_response(["status" => "error", "msg" => 'Support client is required!']);
    $payload = [
        'usercode' => AUTH_USER['id'],
        'request' => 'map_support_client',
        'data' => ['support_client' => $request_data['support_client']],
        'time' => time()
    ];
    $token = JWT::encode($payload, CS_SYSTEM_TOKEN, 'HS256');
    json_response(["status" => "success", "_token" => $token]);
}