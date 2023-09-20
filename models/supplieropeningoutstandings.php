<?


class SupplierOpeningOutstandings extends model
{
    var $table = 'supplier_opening_outstandings';

    public function getList($locationid="",$branchid="")
    {
        $sql = "select sou.*,
                       (sou.amount * sou.currency_amount)                                      as base_amount,
                       opening_payments.amount                                                 as paid_amount,
                       (sou.amount * sou.currency_amount) - IFNULL(opening_payments.amount, 0) as outstanding_amount,
                       s.name                                                                  as suppliername,
                       l.name                                                                  as locationname,
                       b.name                                                                  as branchname,
                       c.name                                                                  as currency,
                       c.base                                                                  as base_currency,
                       users.name                                                              as username
                from supplier_opening_outstandings sou
                         inner join suppliers s on sou.supplierid = s.id
                         inner join locations l on sou.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join currencies_rates cr on cr.id = sou.currency_rateid
                         inner join currencies c on c.id = cr.currencyid
                         inner join users on users.id = sou.createdby
                         left join
                     (
                         select spd.openingid, sum(spd.amount * sp.currency_amount) as amount
                         from supplier_payment_details spd
                                  inner join supplier_payments sp on sp.id = spd.spid
                         group by spd.openingid
                     ) as opening_payments on opening_payments.openingid = sou.id
                where 1 = 1";
        if($locationid)$sql.=" and l.id = $locationid";
        if($branchid)$sql.=" and b.id = $branchid";
//        debug($sql);
        return fetchRows($sql);
    }
}