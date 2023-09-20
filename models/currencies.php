<?

class Currencies extends model
{
    var $table = "currencies";
    static $currencyClass = null;

    function __construct()
    {
        self::$currencyClass = $this;
    }
}
