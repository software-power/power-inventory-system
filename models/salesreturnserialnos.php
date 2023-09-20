<?


class SalesReturnSerialnos extends model
{
    var $table = "sales_return_serialnos";
    static $saleReturnSerialnosClass = null;

    function __construct()
    {
        self::$saleReturnSerialnosClass = $this;
    }
    function getList($srdid)
    {
        $sql = "select srs.*,
                       serialnos.number
                from sales_return_serialnos srs
                         inner join serialnos on srs.snoid = serialnos.id
                where 1 = 1 and srs.srdid = $srdid";

        return fetchRows($sql);
    }
}