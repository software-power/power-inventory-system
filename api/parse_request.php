<?


$headers = REQUEST_HEADERS;

if (!isset($_GET['access_token']))
    if (!isset($headers['Content-Type']) || $headers['Content-Type'] != 'application/json') {
        json_response(['error' => 'invalid headers']);
    }

$raw_data = file_get_contents("php://input");
$request_data = json_decode($raw_data, 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (json_last_error() != JSON_ERROR_NONE) json_response(['status' => 'error', 'msg' => "Invalid json format"]);
}

