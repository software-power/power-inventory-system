<?php /**
 * currencies_rates
 */
class Currencies_rates extends model
{
    var $table = "currencies_rates";
    public static $staticClass;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    function getCurrency_rates($id = "")
    {
        $sql = "select c.name as currencyname,
                   c.base,
                   c.description,
                   r.id   as rateid,
                   r.rate_amount,
                   r.currencyid,
                   r.doc,
                   r.status,
                   u.name as createdbyname
            from currencies_rates as r
                     inner join currencies as c on c.id = r.currencyid
                     inner join users as u on u.id = r.createdby
            where r.status = 'active'";
        if ($id) {
            $sql .= " and r.id = $id";
            return fetchRow($sql);
        } else {
            return fetchRows($sql);
        }
    }

    function getBaseCurrency()
    {
        $sql = "select c.name,c.base,c.description from currencies as c where c.base = 'yes'";
        //echo $sql;die();
        return fetchRow($sql);
    }

    function getRate_logs($currencyid = "")
    {
        $sql = "select c.name as currencyname,r.id as rateid,r.rate_amount,
    r.currencyid,r.doc,r.status,
    u.name as createdbyname from  currencies_rates as r
    inner join currencies as c on c.id = r.currencyid
    inner join users as u on u.id = r.createdby where 1=1 and r.currencyid=$currencyid
    order by r.doc desc";

        //echo $sql;die();
        return fetchRows($sql);
    }
}

?>
