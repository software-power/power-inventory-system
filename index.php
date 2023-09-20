<?

include 'cfg/EncSessionHandler.php';
$key = 'sifusodihosdighodihgosidghois';
$handler = new EncSessionHandler($key);
session_set_save_handler($handler, true);

$session_path = __DIR__ . DIRECTORY_SEPARATOR . "sessions" . DIRECTORY_SEPARATOR;
if (!is_dir($session_path)) mkdir($session_path);
session_save_path($session_path);
ob_start();
session_start();
include 'cfg/database.php';
include 'functions.php';
include 'tally_functions.php';
include 'db.php';
include 'vendor/autoload.php';

ERROR_REPORTING(0);
// ERROR_REPORTING(E_ALL);

date_default_timezone_set($config['timezone_name']?:'Africa/Dar_es_Salaam');

include 'lib/PHPMailer/PHPMailerAutoload.php';

$controllers = 'controllers/';
$models = 'models/';
$traits = 'traits/';
$default_module = 'home';
$default_action = 'index';
$layout = 'layout.tpl.php';

$module = $_GET['module'];
$action = $_GET['action'];
$action = str_replace('.html', '', $action);
$format = $_GET['format'] ?? '';

$currentTime = mktime(date("H"), date("i"), date("s"));
$currentTime = date('H:i:s', $currentTime);
$tomorrow = date("Y-m-d", strtotime('tomorrow'));
define('NOW', $currentTime);
define('TODAY', date('Y-m-d'));
define('TOMORROW', $tomorrow);
define('TIMESTAMP', date('Y-m-d G:i:s'));
define("REQUEST_HEADERS", getallheaders());


/* Include all traits */
loadDir($traits);

/* Include all models */
loadDir($models);
//Instantiate the classes;
require 'instantiate.php';
require 'config.php';
require 'licefunc.php';
include 'support_api.php';


if (empty($module)) $module = $default_module;
if (empty($action)) $action = $default_action;
$testinggg = true;
//if($testinggg){
try {
    if (!defined('CS_LICE_TOKEN') || empty(CS_LICE_TOKEN)) throw new Exception("Missing token");
    if (!defined('CS_TIN') || empty(CS_TIN)) throw new Exception("Missing TIN");
    if (!defined('CS_SOFTWARE_ID') || empty(CS_SOFTWARE_ID)) throw new Exception("Missing Software ID");
//    powerLicenInfo();

    define('ISDEV', 'no');
    $license = isKeyValid(CS_LICE_TOKEN, CS_TIN, CS_SOFTWARE_ID);
//    debug($license);
    if ($license['status'] == 'invalid') throw new Exception($license['msg']);

    if ($license['reason'] == 'not register') throw new Exception($license['msg']);

    define('LICENSE_REMAIN_DAYS', $license['remains']);
    $license_modules = [];
    foreach (explode(',', $license['modules']) as $m) {
        list($k, $val) = explode('@', $m);
        $license_modules[$k] = $val;
    }
    define('LICENSE_MODULES', $license_modules);

} catch (Exception $e) {
    unset($_SESSION['member']);
    $_SESSION['license_error_message'] = $e->getMessage();
}
//}

// validate login of user
$member = $_SESSION['member'];
if ((empty($member)) and $module != 'authenticate') {
    $module = 'authenticate';
    $action = 'login';
}

require 'cfg/active_middlewares.php';
require 'cfg/cache.php';


$_SESSION['pagetitle'] = CS_COMPANY; // Used for default Page Titled

$user = $_SESSION['member'];
if ($_SESSION['member']['status'] != 'active' and $module != 'authenticate') {
    $module = 'home';
    $action = 'inactive';
}

if ($user) define('IS_ADMIN', $_SESSION['member']['roleid'] == 1);

if ($_SESSION['member']) {
    $_SESSION['member'] = $Users->get($_SESSION['member']['id']);
    $_SESSION['member']['rolename'] = $Roles->get($_SESSION['member']['roleid'])['name'];
}

if ($format == 'json') $action = 'ajax_' . $action;

if (empty($data['message'])) $data['message'] = $_SESSION['message'];
if (empty($data['error'])) $data['error'] = $_SESSION['error'];
if (empty($data['delay'])) $data['delay'] = intval($_SESSION['delay']) ?? 2500;


if (file_exists($controllers . $module . '.php')) {
    include $controllers . $module . '.php';
}

$data['module'] = $module;
$data['action'] = $action;

//debug($_SESSION);
$data['realmenus'] = IS_ADMIN ? $Menus->getAllMenus() : $RoleRights->getUserMenus($_SESSION['member']['roleid']);
$data['user_notifications'] = $Notifications->countWhere(['toid' => $_SESSION['member']['id'], 'state' => 'unread']);
$data['unreadNotifications'] = $Notifications->getNotifications($_SESSION['member']['id'], "", 'unread', 5);
$data['menu'] = loadTemplate('menu.tpl.php', $data);
$data['control_panel'] = loadTemplate('control_panel.tpl.php', $data);

if (empty($data['pagetitle'])) $data['pagetitle'] = $_SESSION['pagetitle'];
if (empty($data['layout'])) $data['layout'] = $layout;

if ($format == 'none') {
    $data['layout'] = 'layout_iframe.tpl.php';

    global $templateData;
    $data['content'] .= '<script>window.print();</script>';
    $templateData['content'] = $data['content'];
}

if ($format == 'json') echo json_encode($data['content'],JSON_INVALID_UTF8_IGNORE);
else echo loadTemplate($data['layout'], $data);

if ($_SESSION['message']) $_SESSION['message'] = '';
if ($_SESSION['error']) $_SESSION['error'] = '';
if ($_SESSION['delay']) $_SESSION['delay'] = '';
if ($_SESSION['license_error_message']) $_SESSION['license_error_message'] = '';

//AL HAMDU LILLAH;;
ob_end_flush();

if (isset($_SESSION['download_efd'])) {
    echo "<script>
                let a = document.createElement('a');
                a.href = `?module=download&action=efd_file&download={$_SESSION['download_efd']}`;
                a.setAttribute('target','_blank');
                a.click();
          </script>";
}