<?

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//authorization
$headers = REQUEST_HEADERS;

try {
    if (isset($headers['main-token'])) { //main system token authorization => request from main system
        $main_token = base64_decode($headers['main-token']);
//        json_response($main_token);
//        if (CS_MAIN_SYSTEM) json_response(['status' => 'error', 'msg' => 'Client sent to non subsystem']);
        if (CS_MAIN_SYSTEM_TOKEN !== $main_token) json_response(['status' => 'error', 'msg' => 'Main token mismatch']);
        $auth_user = $Users->get(1);
    } elseif (isset($headers['sub-token'])) { //sub system token authorization =>request from subsystem
        $sub_token = base64_decode($headers['sub-token']);
        if (!CS_MAIN_SYSTEM) json_response(['status' => 'error', 'msg' => 'Not main system']);
        if (!SystemTokens::$staticClass->find(['token' => $sub_token])) json_response(['status' => 'error', 'msg' => 'Sub system not found']);
        $auth_user = $Users->get(1);
    } else { //user code authorization
        if (!isset($headers['Authorization']) && !isset($_GET['access_token'])) throw new Exception("");

        if (isset($headers['Authorization'])) {
            $token = $headers['Authorization'];
            $decoded = JWT::decode($token, new Key(SECRET_KEY, 'HS256'));
        } else {
            $access_token = $_GET['access_token'];
            $decoded = JWT::decode($access_token, new Key(CS_SYSTEM_TOKEN, 'HS256'));
        }
        $decoded = json_decode(json_encode($decoded), 1);
        $decoded['usercode'] = removeSpecialCharacters($decoded['usercode']);
        if (!isset($decoded['usercode'])) throw new Exception("");
        if (isset($decoded['for_support'])) $request_data['for_support'] = 1;
        if (isset($decoded['mainclientcode'])) $request_data['clientid'] = $decoded['mainclientcode'];
        $request_data['data'] = $decoded['data'];
        $auth_user = $Users->get($decoded['usercode']);
    }
    if (!$auth_user) throw new Exception("");

    define("AUTH_USER", $auth_user);
} catch (LogicException $e) {
    json_response(['status' => 'error', 'msg' => 'unauthorized']);
} catch (UnexpectedValueException $e) {
    json_response(['status' => 'error', 'msg' => 'unauthorized']);
} catch (Exception $e) {
    json_response(['status' => 'error', 'msg' => 'unauthorized']);
}

