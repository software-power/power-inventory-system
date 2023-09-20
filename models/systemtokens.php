<?php


class SystemTokens extends model
{
    public const TEST_MAIN_SYSTEM_URL = "api/?module=testing&action=main_system_connection";
    public const TEST_SUB_SYSTEM_URL = "api/?module=testing&action=sub_system_connection";

    public const SEND_CLIENT_URL = "api/?module=clients&action=save_client";


    public const GET_CLIENT_SCREEN_ACCESS_TOKEN = "api/?module=clients&action=get_screen_token";
    public const GET_ACCESS_TOKEN = "api/?module=authenticate&action=get_token";

    public const GET_CLIENT_SCREEN = "api/?module=endpoints&action=clients";


    public const REQUEST_MAIN_SEND_TO_SUPPORT = "api/?module=clients&action=sub_send_support";

    public const REQUEST_SUB_SUPPORT_CODE = "api/?module=clients&action=sub_return_support_code";

    var $table = 'system_tokens';
    public static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }


    static function getAccessToken($usercode)
    {
        try {
            $data = [
                "usercode" => $usercode
            ];
            $url = CS_MAIN_SYSTEM_URL . SystemTokens::GET_ACCESS_TOKEN;
            $data = json_encode($data);
            $response = sendHttpRequest($url, $data, "POST");
            if ($response['status'] == 'success' && $response['data']['status'] == 'success') {
                return $response['data']['_token'];
            } else {
                throw new Exception('');
            }
        } catch (Exception $e) {
            return "";
        }
    }
}