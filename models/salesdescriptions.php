<?

class Salesdescriptions extends model
{
    var $table = "salesdescriptions";
    public static $staticClass;

    public function __construct()
    {
        self::$staticClass = $this;
    }


}
