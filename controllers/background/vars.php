<?

//print_r($appFolder);

include __DIR__.'/../../cfg/database.php';
include __DIR__.'/../../functions.php';
include __DIR__.'/../../tally_functions.php';
include __DIR__.'/../../db.php';
include __DIR__.'/../../vendor/autoload.php';

ERROR_REPORTING(0);
// ERROR_REPORTING(E_ALL);
date_default_timezone_set($config['timezone_name']?:'Africa/Dar_es_Salaam');

define('TODAY', date('Y-m-d'));

//debug($config);
$models = __DIR__.'/../../models/';
$traits = __DIR__.'/../../traits/';

/* Include all traits */
loadDir($traits);

/* Include all models */
loadDir($models);
//Instantiate the classes;
include __DIR__.'/../../instantiate.php';
include __DIR__.'/../../config.php';
include __DIR__.'/../../support_api.php';
require __DIR__.'/../../cfg/cache.php';

ignore_user_abort(true);
set_time_limit(0);
