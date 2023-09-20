<?
ob_start();
include '../cfg/database.php';
include '../functions.php';
include '../tally_functions.php';
include '../db.php';
include '../vendor/autoload.php';

ERROR_REPORTING(0);
// ERROR_REPORTING(E_ALL);

date_default_timezone_set($config['timezone_name']?:'Africa/Dar_es_Salaam');

$controllers = 'controllers/';
$models = '../models/';
$traits = '../traits/';
$default_module = 'home';
$default_action = 'index';

$module = isset($_GET['module']) ? $_GET['module'] : $default_module;
$action = isset($_GET['action']) ? $_GET['action'] : $default_action;

$currentTime = mktime(date("H"), date("i"), date("s"));
$currentTime = date('H:i:s', $currentTime);
$tomorrow = date("Y-m-d", strtotime('tomorrow'));
define('NOW', $currentTime);
define('TODAY', date('Y-m-d'));
define('TOMORROW', $tomorrow);
define('TIMESTAMP', date('Y-m-d G:i:s'));
define("SECRET_KEY", "0zlCNNqapU6x9lXPnEDv02z1nXwIxMLGAJbbHcbYAZOVOSAkHDDCeBJG10wj");
define("REQUEST_HEADERS", getallheaders());

/* Include all traits */
loadDir($traits);


/* Include all models */
loadDir($models);
//Instantiate the classes;
include '../instantiate.php';
include '../config.php';
include '../support_api.php';

include 'parse_request.php';

include 'routes.php';


if (file_exists($controllers . $module . '.php')) include "$controllers" . "$module.php";
ob_end_flush();



