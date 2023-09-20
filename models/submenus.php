<?

class Submenus extends model
{
    var $table = "submenus";
    static $submenuClass = null;

    function __construct()
    {
        self::$submenuClass = $this;
    }

}
