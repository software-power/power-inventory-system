<?

class SalespaymentDetails extends model
{
    var $table = "salespayment_details";
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }
}
