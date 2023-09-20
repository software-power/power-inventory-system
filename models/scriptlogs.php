<?

use Symfony\Component\Process\PhpExecutableFinder;

class ScriptLogs extends model
{
    var $table = "script_logs";

    static $class = null;

    function __construct()
    {
        self::$class = $this;
    }

    static function run($scripts = [])
    {
        $scripts = !empty($scripts) ? " --scripts=" . implode(',', $scripts) : "";
        $phpExc = (new PhpExecutableFinder())->find();
        if(empty($phpExc))return;

        $d = DIRECTORY_SEPARATOR;
        $cmd = $phpExc . " " . dirname(__DIR__, 1) . "{$d}controllers{$d}background{$d}script.php $scripts";

        if (substr(php_uname(), 0, 7) == "Windows"){
            $p = popen("start /B ". $cmd. " > NUL", "r"); sleep(1); pclose($p);
        }else{
            exec($cmd . " > /dev/null &");
        }
    }
}