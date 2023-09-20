<?php

class Suppliers extends model
{
    var $table = 'suppliers';

    function searchResults($name = "")
    {
        $sql = "select * from suppliers where name like '%" . $name . "%' order by name";

        // echo $sql;die();
        return fetchRows($sql);
    }

    function totalPaidAmount($supplierid = "", $branchid = "", $fromdate = "", $todate = "")
    {
        $sql = "select s.id,
                               s.name                          as suppliername,
                               b.id                            as branchid,
                               b.name                          as branchname,
                               sum(IFNULL(sp.total_amount, 0)) as amount
                        from suppliers s
                                 left join supplier_payments sp on s.id = sp.supplierid
                                 left join branches b on b.id = sp.branch_id
                        where 1 = 1";
        if ($supplierid) $sql .= " and s.id = $supplierid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($fromdate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " group by s.id, b.id";
        return fetchRows($sql);
    }

    function advancePaymentsBalance($supplierid = "", $branchid = "")
    {
        $supplier_filter = $supplierid ? " and s.id = $supplierid" : " and 1=1";
        $branch_filter = $branchid ? " and b.id = $branchid" : " and 1=1";
        $sql = "select s.id                                                                 as supplierid,
                       s.name                                                               as suppliername,
                       b.id                                                                 as branchid,
                       b.name                                                               as branchname,
                       sap.id                                                               as advance_id,
                       (sap.amount * sap.currency_amount) - IFNULL(used_advances.amount, 0) as remain_advance
                from supplier_advance_payments sap
                         inner join suppliers s on s.id = sap.supplierid
                         inner join branches b on b.id = sap.branchid
                         left join
                     (
                         select spua.advance_id, sum(spua.amount * sap2.currency_amount) as amount
                         from supplier_payment_used_advances spua
                         inner join supplier_advance_payments sap2 on sap2.id = spua.advance_id
                         group by spua.advance_id
                     ) as used_advances on sap.id = used_advances.advance_id
                where 1 = 1 $supplier_filter $branch_filter
                group by sap.id
                having remain_advance > 0 order by sap.id";
//        debug($sql);
        return fetchRows($sql);
    }

    function outstandingPayments($supplierid = "", $fromdate = "", $todate = "", $locationid = "", $branchid = "")
    {
        //filters

        $location = $locationid ? " and l.id = $locationid" : "";
        $branch = $branchid ? " and b.id = $branchid" : "";
        $supplier = $supplierid ? " and s.id = $supplierid" : "";
        $grnfrom = $fromdate ? " and date_format(grn.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $grnto = $todate ? " and date_format(grn.doc,'%Y-%m-%d') <= '$todate'" : "";
        $soufrom = $fromdate ? " and date_format(sou.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $souto = $todate ? " and date_format(sou.doc,'%Y-%m-%d') <= '$todate'" : "";

        $sql = "select supplierid,
                       suppliername,
                       locationname,
                       branchname,
                       sum(full_amount)        as full_amount,
                       sum(paid_amount)        as paid_amount,
                       sum(outstanding_amount) as outstanding_amount
                from (select s.id                                                                          as supplierid,
                             s.name                                                                        as suppliername,
                             l.name                                                                        as locationname,
                             b.name                                                                        as branchname,
                             sum(grn.full_amount * grn.currency_amount)                                    as full_amount,
                             sum(IFNULL(grn_payments.amount, 0))                                           as paid_amount,
                             sum((grn.full_amount * grn.currency_amount) - IFNULL(grn_payments.amount, 0)) as outstanding_amount
                      from suppliers s
                               inner join grn on grn.supplierid = s.id
                               inner join locations l on grn.locid = l.id
                               inner join branches b on l.branchid = b.id
                               left join
                           (
                               select spd.grnid, sum(spd.amount * sp.currency_amount) as amount
                               from supplier_payment_details spd
                                        inner join supplier_payments sp on spd.spid = sp.id
                               group by spd.grnid
                           ) as grn_payments on grn_payments.grnid = grn.id
                      where 1 = 1 and grn.supplier_payment = 1 and grn.approvedby is not null $location $branch $supplier  $grnfrom $grnto
                      group by supplierid
                      union all
                      select s.id                                                                        as supplierid,
                             s.name                                                                      as suppliername,
                             l.name                                                                      as locationname,
                             b.name                                                                      as branchname,
                             sum(sou.amount * sou.currency_amount)                                       as full_amount,
                             sum(IFNULL(opening_payment.amount, 0))                                      as paid_amount,
                             sum((sou.amount * sou.currency_amount) - IFNULL(opening_payment.amount, 0)) as outstanding_amount
                      from suppliers s
                               inner join supplier_opening_outstandings sou on sou.supplierid = s.id
                               inner join locations l on sou.locationid = l.id
                               inner join branches b on l.branchid = b.id
                               left join
                           (
                               select spd.openingid, sum(spd.amount * sp.currency_amount) as amount
                               from supplier_payment_details spd
                                        inner join supplier_payments sp on spd.spid = sp.id
                               group by spd.grnid
                           ) as opening_payment on opening_payment.openingid = sou.id
                      where 1 = 1  $location $branch $supplier $soufrom $souto
                      group by supplierid) as supplier_outstanding
                group by supplierid";

//        debug($sql);
        return fetchRows($sql);
    }

    function paymentHistory($supplierid, $branchid, $fromdate = "", $todate = "")
    {

    }

    function history($supplierid="",$createdby="")
    {
        $grncreatedby = $createdby?" and grn.createdby = $createdby":"";
        $lpocreatedby = $createdby?" and lpo.createdby = $createdby":"";
        $sql = "select s.id,
                       s.name,
                       s.contact_email  as email,
                       s.contact_mobile as mobile,
                       grns.count       as grncount,
                       lpos.count       as lpocount
                from suppliers s
                         left join (select supplierid, count(*) as count from grn where (grn.approvedby > 0 or grn.approvedby is not null) $grncreatedby group by supplierid) as grns on grns.supplierid = s.id
                        left join (select supplierid,  count(*) as count from lpo where (lpo.approvedby > 0 or lpo.approvedby is not null) $lpocreatedby group by supplierid) as lpos on lpos.supplierid = s.id
                where 1 = 1";
        //debug($sql);
        return fetchRows($sql);
    }

}
