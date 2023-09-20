<?


class SupplierPayments extends model
{
    var $table = 'supplier_payments';

    function getList($supplierid = '', $branchid = '', $fromdate = '', $todate = '', $payment_method = '', $issuedby = '')
    {
        $sql = "select sp.*,
                       suppliers.name as suppliername,
                       users.name     as issuedby,
                       b.name         as branchname,
                       pm.name        as method,
                       cu.name        as currencyname,
                       spi.spid,
                       spi.chequename,
                       spi.chequetype,
                       spi.bankname,
                       spi.bankreference,
                       spi.credit_cardno,
                       spi.createdby
                from supplier_payments sp
                         inner join suppliers on sp.supplierid = suppliers.id
                         inner join users on users.id = sp.createdby
                         inner join branches b on b.id = sp.branch_id
                         inner join paymentmethods pm on sp.pmethod_id = pm.id
                         inner join currencies_rates cr on cr.id = sp.currency_rateid
                         inner join currencies cu on cu.id = cr.currencyid
                         left join supplier_payment_info spi on sp.id = spi.spid and spi.source = 'payment'
                where 1 = 1";
        if($supplierid)$sql.=" and suppliers.id = $supplierid";
        if($branchid)$sql.=" and b.id = $branchid";
        if($issuedby)$sql.=" and users.id = $issuedby";
        if($payment_method)$sql.=" and pm.name = '$payment_method'";
        if($fromdate)$sql.=" and date_format(sp.doc,'%Y-%m-%d') >= '$fromdate'";
        if($todate)$sql.=" and date_format(sp.doc,'%Y-%m-%d') <= '$todate'";

        $sql.=" order by sp.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function detailedPaymentInfo($paymentId = "", $supplierid = "", $fromDate = "", $toDate = "", $locationid = "", $branchid = "", $payment_method = "")
    {
        $sql = "select sp.*,
                       spi.chequename,
                       spi.chequetype,
                       spi.bankname,
                       spi.bankreference,
                       spi.credit_cardno,
                       banks.name                                             as bank_name,
                       banks.accno                                            as bank_accno,
                       banks.ledgername                                       as bank_ledgername,
                       e_account.name                                         as electronic_account,
                       e_account.ledgername                                   as electronic_account_ledgername,
                       suppliers.name                                                     as suppliername,
                       creator.name                                                       as creator,
                       pm.name                                                            as method,
                       spd.id                                                             as detailId,
                       (sp.currency_amount * spd.amount)                                  as amount,
                       IF(spd.grnid is null, sou.grnno, spd.grnid)                        as grnno,
                       IF(spd.grnid is null, sou.invoiceno, grn.invoiceno)                as invoiceno,
                       IF(spd.grnid is null, 'opening', 'grn')                            as source,
                       IF(spd.grnid is null, openingloc.name, grnloc.name)                as locationname,
                       IF(spd.grnid is null, openingbranch.name, grnbranch.name)          as branchname,
                       overpay.amount                                                     as overpay_amount,
                       used_advance.amount                                                as used_advance,
                       (sp.total_amount * sp.currency_amount) - IFNULL(used_advance.amount, 0) +
                       IFNULL(overpay.amount, 0)                                          as input_amount,
                       (sp.total_amount * sp.currency_amount) + IFNULL(overpay.amount, 0) as total_payment_amount
                from supplier_payments sp
                         inner join paymentmethods pm on pm.id = sp.pmethod_id
                         inner join supplier_payment_details spd on sp.id = spd.spid
                         left join supplier_payment_info spi on sp.id = spi.spid and spi.source = 'payment'
                         left join banks on banks.id = spi.bankid
                         left join electronic_accounts e_account on e_account.id = spi.eaccid
                         inner join suppliers on sp.supplierid = suppliers.id
                         inner join users creator on creator.id = sp.createdby
                         left join grn on spd.grnid = grn.id
                         left join locations grnloc on grn.locid = grnloc.id
                         left join branches grnbranch on grnloc.branchid = grnbranch.id
                         left join supplier_opening_outstandings sou on spd.openingid = sou.id
                         left join locations openingloc on sou.locationid = openingloc.id
                         left join branches openingbranch on openingloc.branchid = openingbranch.id
                         left join
                     (
                         select sap.spid, sum(sap.amount * sap.currency_amount) as amount
                         from supplier_advance_payments sap
                         group by sap.spid
                     ) as overpay on sp.id = overpay.spid
                         left join
                     (
                         select sap.spid, sum(sap.amount * sap.currency_amount) as amount
                         from supplier_payment_used_advances spua
                                  inner join supplier_advance_payments sap on sap.id = spua.advance_id
                         group by sap.spid
                     ) as used_advance on sp.id = used_advance.spid
                where 1 = 1";
        if ($paymentId) $sql .= " and sp.id = $paymentId";
        if ($supplierid) $sql .= " and suppliers.id = $supplierid";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        if ($locationid) $sql .= " and IF(spd.grnid is null, openingloc.id, grnloc.id) = $locationid";
        if ($branchid) $sql .= " and IF(spd.grnid is null, openingbranch.id, grnbranch.id) = $branchid";
        if ($fromDate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') >= '$fromDate'";
        if ($toDate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') <= '$toDate'";
        $sql .= " order by sp.id desc";
//        debug($sql);
        if ($payments = fetchRows($sql)) {
            $newArray = [];
//            debug($payments);
            foreach ($payments as $index => $item) {
                $newArray[$item['id']]['id'] = $item['id'];
                $newArray[$item['id']]['supplierid'] = $item['supplierid'];
                $newArray[$item['id']]['suppliername'] = $item['suppliername'];
                $newArray[$item['id']]['pmethod_id'] = $item['pmethod_id'];
                $newArray[$item['id']]['method'] = $item['method'];
                $newArray[$item['id']]['status'] = $item['status'];
                $newArray[$item['id']]['remark'] = $item['remark'];
                $newArray[$item['id']]['chequename'] = $item['chequename'];
                $newArray[$item['id']]['chequetype'] = $item['chequetype'];
                $newArray[$item['id']]['bankname'] = $item['bankname'];
                $newArray[$item['id']]['bankreference'] = $item['bankreference'];
                $newArray[$item['id']]['credit_cardno'] = $item['credit_cardno'];
                $newArray[$item['id']]['bank_name'] = $item['bank_name'];
                $newArray[$item['id']]['bank_accno'] = $item['bank_accno'];
                $newArray[$item['id']]['bank_ledgername'] = $item['bank_ledgername'];
                $newArray[$item['id']]['electronic_account'] = $item['electronic_account'];
                $newArray[$item['id']]['electronic_account_ledgername'] = $item['electronic_account_ledgername'];
                $newArray[$item['id']]['createdby'] = $item['createdby'];
                $newArray[$item['id']]['creator'] = $item['creator'];
                $newArray[$item['id']]['doc'] = $item['doc'];
                $newArray[$item['id']]['total_amount'] = $item['total_amount'];
                $newArray[$item['id']]['overpay_amount'] = $item['overpay_amount'];
                $newArray[$item['id']]['used_advance'] = $item['used_advance'];
                $newArray[$item['id']]['input_amount'] = $item['input_amount'];
                $newArray[$item['id']]['total_payment_amount'] = $item['total_payment_amount'];
                $newArray[$item['id']]['payments'][$item['detailId']]['detailId'] = $item['detailId'];
                $newArray[$item['id']]['payments'][$item['detailId']]['amount'] = $item['amount'];
                $newArray[$item['id']]['payments'][$item['detailId']]['grnno'] = $item['grnno'];
                $newArray[$item['id']]['payments'][$item['detailId']]['source'] = $item['source'];
                $newArray[$item['id']]['payments'][$item['detailId']]['invoiceno'] = $item['invoiceno'];
            }
//            debug($newArray);
            return array_values($newArray);
        } else {
            return false;
        }
    }

    function grnPayments($grnid = "", $payment_method = "")
    {
        $sql = "select sp.*,
                       c.name as currency_name,
                       spi.chequename,
                       spi.chequetype,
                       spi.bankname,
                       spi.bankreference,
                       spi.credit_cardno,
                       suppliers.name as suppliername,
                       creator.name   as creator,
                       pm.name        as method,
                       spd.id         as detailId,
                       spd.amount,
                       spd.grnid      as grnno
                from grn
                         inner join supplier_payment_details spd on grn.id = spd.grnid
                         inner join supplier_payments sp on spd.spid = sp.id
                        inner join currencies_rates cr on sp.currency_rateid = cr.id
                        inner join currencies c on cr.currencyid = c.id
                         left join supplier_payment_info spi on sp.id = spi.spid  and spi.source = 'payment'
                         inner join paymentmethods pm on pm.id = sp.pmethod_id
                         inner join suppliers on sp.supplierid = suppliers.id
                         inner join users creator on creator.id = sp.createdby
                where 1 = 1";

        if ($grnid) $sql .= " and spd.grnid = $grnid";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        $sql .= " order by sp.id desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function openingPayments($openingid = "", $payment_method = "")
    {
        $sql = "select sp.*,
                       spi.chequename,
                       spi.chequetype,
                       spi.bankname,
                       spi.bankreference,
                       spi.credit_cardno,
                       suppliers.name as suppliername,
                       creator.name   as creator,
                       pm.name        as method,
                       spd.id         as detailId,
                       spd.amount,
                       spd.openingid,
                       sou.invoiceno,
                       sou.grnno
                from supplier_opening_outstandings sou
                         inner join supplier_payment_details spd on sou.id = spd.openingid
                         inner join supplier_payments sp on spd.spid = sp.id
                         left join supplier_payment_info spi on sp.id = spi.spid and spi.source = 'payment'
                         inner join paymentmethods pm on pm.id = sp.pmethod_id
                         inner join suppliers on sp.supplierid = suppliers.id
                         inner join users creator on creator.id = sp.createdby
                where 1 = 1";

        if ($openingid) $sql .= " and spd.openingid = $openingid";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        $sql .= " order by sp.id desc";
//        debug($sql);
        return fetchRows($sql);
    }

}