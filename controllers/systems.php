<?

if ($action == 'ajax_getMainSystemClientScreen') {
    $result['status'] = 'success';
    try {
        $access_token = SystemTokens::getAccessToken($_SESSION['member']['code']);
        if (empty($access_token)) throw new Exception("Invalid user access token");

        $sub_token = base64_encode(CS_SYSTEM_TOKEN);
        $headers = [
            "Content-Type: application/json",
            "Authorization: $access_token",
            "sub-token: $sub_token",
        ];

        $url = CS_MAIN_SYSTEM_URL . SystemTokens::GET_CLIENT_SCREEN_ACCESS_TOKEN;
        if (isset($_GET['mainclientcode'])) {
            $mainclientcode = removeSpecialCharacters($_GET['mainclientcode']);
            if (!$mainclientcode) throw new Exception("Client code not set");
            $url .= "&mainclientcode=$mainclientcode";
        }
        if (isset($_GET['for_support'])) $url .= '&for_support';
        $response = sendHttpRequest($url, json_encode([
            'request' => OtherRights::add_client
        ]), 'POST', $headers);
        if (empty($access_token)) throw new Exception("Invalid access token");
        if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
            $result['token'] = $response['data']['_token'];
        } else {
            throw new Exception('Invalid system access token');
        }
    } catch (Exception $e) {
        $result = ['status' => 'error', 'msg' => $e->getMessage()];
    }
//    debug($result);
    $data['content'] = $result;
}
