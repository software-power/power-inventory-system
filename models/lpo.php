<?

class LPO extends model
{
    var $table = "lpo";
    static public $staticClass = null;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($lpoid = "", $createdby = "", $fromdate = "", $todate = "", $supplierid = "", $locationid = "", $branchid = "", $status = "")
    {
        $sql = "select lpo.id                                      as lponumber,
                       g.id                                        as grnnumber,
                       lpo.doc                                     as issuedate,
                       s.name                                      as suppliername,
                       lpo.total_amount,
                       lpo.full_amount,
                       lpo.grand_vatamount,
                       (lpo.total_amount * lpo.currency_amount)    as base_total_amount,
                       (lpo.full_amount * lpo.currency_amount)     as base_full_amount,
                       (lpo.grand_vatamount * lpo.currency_amount) as base_grand_vatamount,
                       lpo.currency_amount,
                       lpo.currency_rateid,
                       locations.name                              as locationname,
                       branches.name                               as branchname,
                       cu.name                                     as currency_name,
                       cu.description                              as currency_description,
                       cu.base                                     as base_currency,
                       u.name                                      as issuedby,
                       approver.name                               as approver,
                       lpo.approval_date,
                       lpo.auto_approve,
                       lpo.status
                from lpo
                         inner join suppliers as s on s.id = lpo.supplierid
                         inner join locations on lpo.locationid = locations.id
                         inner join branches on locations.branchid = branches.id
                         left join currencies_rates as cur on cur.id = lpo.currency_rateid
                         left join currencies as cu on cu.id = cur.currencyid
                         left join grn as g on g.lpoid = lpo.id
                         inner join users as u on u.id = lpo.createdby
                         left join users as approver on approver.id = lpo.approvedby
                where 1 = 1";

        if ($lpoid) $sql .= " and lpo.id = $lpoid";
        if ($createdby) $sql .= " and u.id = $createdby";
        if ($supplierid) $sql .= " and lpo.supplierid = $supplierid";
        if ($locationid) $sql .= " and locations.id = $locationid";
        if ($branchid) $sql .= " and branches.id = $branchid";
        if ($fromdate) $sql .= " and date_format(lpo.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(lpo.doc,'%Y-%m-%d') <= '$todate'";

        if ($status == 'canceled') {
            $sql .= "  and lpo.status != 'active'";
        } elseif ($status == 'not-approved') {
            $sql .= "  and approver.id is null";
        } elseif ($status == 'approved') {
            $sql .= "  and approver.id is not null";
        }

        $sql .= " order by lpo.doc desc";
        // echo $sql; die();
        return fetchRows($sql);
    }

    function expectingStock($locationid = "", $productid = "")
    {
        $sql = "select lpo.id                                                                              as lpoid,
                       suppliers.name                                                                      as suppliername,
                       l.id                                                                                as locationid,
                       l.name                                                                              as locationname,
                       b.id                                                                                as branchid,
                       b.name                                                                              as branchname,
                       p.id                                                                                as productid,
                       p.name                                                                              as productname,
                       ld.qty,
                       lpo.expecting_days,
                       lpo.approval_date,
                       date_add(lpo.approval_date, INTERVAL lpo.expecting_days DAY)                        as expecting_in,
                       current_timestamp() >= date_add(lpo.approval_date, INTERVAL lpo.expecting_days DAY) as time_passed
                from lpodetails ld
                         inner join lpo on ld.lpoid = lpo.id
                         inner join suppliers on lpo.supplierid = suppliers.id
                         inner join locations l on lpo.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join products p on ld.prodid = p.id
                         left join grn on lpo.id = grn.lpoid
                         left join users as approver on lpo.approvedby = approver.id
                where lpo.status = 'active' and grn.id is null and approver.id is not null";
        if ($productid) $sql .= " and p.id = $productid";
        if ($locationid) $sql .= " and l.id = $locationid";
//        debug($sql);
        return fetchRows($sql);
    }

}
