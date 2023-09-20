<?


class Industries extends model
{
    var $table = 'industries';
    public static $industryClass = null;

    function __construct()
    {
        self::$industryClass = $this;
    }
}