<?

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($action == "get_token") {
    required_method("POST");
    try {
        $usercode = removeSpecialCharacters($request_data['usercode']);
        if (!$usercode) throw new Exception("Invalid usercode!");
        $user = $Users->get($usercode);
        if (!$user) throw new Exception("user not found!");
        $payload = [
            'usercode' => $usercode,
            'name' => $user['name'],
            'time' => TIMESTAMP
        ];

        $token = JWT::encode($payload, SECRET_KEY, 'HS256');

        json_response(['status' => 'success', '_token' => $token]);
    } catch (Exception $e) {
        json_response(['status'=>'error','msg' => $e->getMessage()]);
    }
}
