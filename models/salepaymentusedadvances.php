<?


class SalePaymentUsedAdvances extends model
{
    var $table = "sales_payment_used_advances";
    static $salePaymentUsedAdvanceClass = null;

    function __construct()
    {
        self::$salePaymentUsedAdvanceClass = $this;
    }

    function getList($paymentid = "", $advanceid = "", $currencyid = "", $fromdate = "", $todate = "")
    {
        $sql = "select spua.*,
                       c.id   as currencyid,
                       c.name as currencyname
                from sales_payment_used_advances spua
                         inner join advance_payments ap on spua.advance_id = ap.id
                         inner join salespayments sp on spua.spid = sp.id
                         inner join currencies c on c.id = ap.currencyid
                where 1 = 1";

        if ($paymentid) $sql .= " and sp.id = $paymentid";
        if ($advanceid) $sql .= " and ap.id = $advanceid";
        if ($currencyid) $sql .= " and c.id = $currencyid";
        if ($currencyid) $sql .= " and c.id = $currencyid";
        if ($fromdate) $sql .= " and date_format(spua.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(spua.doc,'%Y-%m-%d') <= '$todate'";
//        debug($sql);
        return fetchRows($sql);
    }
}