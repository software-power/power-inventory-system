<?

define("SUPPORT_ENDPOINT", [
    /*
     * GET AUTH TOKEN
     * */
    "get_token" => CS_SUPPORT_SERVER . "/apis/web/auth/webtoken",

    /*
     * clients CRUD
     * */
    "clients" => CS_SUPPORT_SERVER . "/apis/web/auth/clients",

    /*
     * clients Mapping
     * */
    "clients_mapping" => CS_SUPPORT_SERVER . "/apis/web/auth/clients_mapping",

    /*
     * order CRUD
     * */
    "order" => CS_SUPPORT_SERVER . "/apis/web/auth/orders",

    /*
     * serialno CRUD
     * */
    "serials" => CS_SUPPORT_SERVER . "/apis/web/auth/serials",
]);


function sendSupportRequest($url, $data, $method = "POST", $auth = false, $headers = ["Content-Type: application/json"])
{
    try {
        $curl = curl_init($url);
        if ($auth) {
            $token_result = getSupportAuthToken();
            if ($token_result['status'] != 'success') throw new Exception($token_result['msg']);
            $headers[] = "Authorization: " . $token_result['token'];
        }

        if ($headers) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if ($method == "POST") curl_setopt($curl, CURLOPT_POST, true);
        if ($method == "PUT") curl_setopt($curl, CURLOPT_PUT, true);
        if ($method == "PATCH") curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result['status'] = 'success';
        $response = curl_exec($curl);
        $result['data'] = json_decode($response, true);

        if (curl_errno($curl)) throw new Exception(curl_error($curl));
        curl_close($curl);

    } catch (Exception $e) {
        $result = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
    return $result;
}


function getSupportAuthToken()
{
    $url = SUPPORT_ENDPOINT['get_token'];
    $data = json_encode([
        'usercode' => defined('USERCODE') ? USERCODE : $_SESSION['member']['id'],
        'support_name' => CS_SUPPORT_NAME
    ]);
    $headers = ["Content-Type: application/json"];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $result = json_decode($response, true);
    curl_close($curl);
    try {
        if (curl_errno($curl)) throw new Exception(curl_error($curl));
        if ($result['status'] == 'success') {
            return [
                'status' => 'success',
                'token' => $result['data']['token']
            ];
        } else {
            throw new Exception($result['msg'] ?: "Error getting token");
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
}