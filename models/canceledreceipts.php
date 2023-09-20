<?


class CanceledReceipts extends model
{
    var $table = 'canceled_receipts';
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($createdby = "", $fromdate = "", $todate = "")
    {
        $sql = "select cr.*,
                       cu.name as currencyname,
                       cu.description as currency_description,
                       users.name as issuedby
                from canceled_receipts cr
                         inner join currencies cu on cr.currencyid = cu.id
                         inner join users on cr.createdby = users.id
                where 1 = 1";
        if ($createdby) $sql .= " and cr.createdby = $createdby";
        if ($fromdate) $sql .= " and date_format(cr.doc, '%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(cr.doc, '%Y-%m-%d') <= '$todate'";

        $sql.=" order by cr.doc desc";
        return fetchRows($sql);
    }
}