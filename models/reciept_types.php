<?

class RecieptsTypes extends model
{
    var $table = "reciept_types";
    public static $staticClass;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    static function sizes()
    {
        return ['A4', 'small'];
    }
}

