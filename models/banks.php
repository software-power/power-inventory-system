<?


class Banks extends model
{
    var $table = "banks";

    static $banksClass = null;

    function __construct()
    {
        self::$banksClass = $this;
    }
}