<?


class SalesSerialnos extends model
{
    var $table = "sales_serialnos";
    static $saleSerialnosClass = null;

    function __construct()
    {
        self::$saleSerialnosClass = $this;
    }
}