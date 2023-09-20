<?


class PriceLogs extends model
{
    var $table = 'price_logs';

    static $priceLogsClass = null;

    function __construct()
    {
        self::$priceLogsClass = $this;
    }

    function getList($branchid = "", $productid = "")
    {
        $sql = "select pl.*,
                       p.name              as productname,
                       b.name              as branchname,
                       users.name          as issuedby,
                       cp.id is not null   as current_price
                from price_logs pl
                         inner join branches b on b.id = pl.branchid
                         inner join products p on pl.productid = p.id
                         inner join users on pl.createdby = users.id
                         left join current_prices cp on pl.id = cp.logid
                where 1 = 1";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($productid) $sql .= " and p.id = $productid";

        $sql .= " order by pl.doc desc";
        return fetchRows($sql);
    }

}