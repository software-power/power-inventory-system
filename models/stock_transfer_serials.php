<?

class StockTransferSerials extends model
{
    var $table = "stock_transfer_serials";
    static $transferSerialClass = null;

    function __construct()
    {
        self::$transferSerialClass = $this;
    }
}
