<?

class LPODetails extends model
{
    var $table = "lpodetails";

    function getList($lpoid = "",$supplierid="",$createdby="",$productid="",$productcategoryid="",$brandid="",$fromdate="",$todate="",$status="",$locationid="",$branchid="")
    {
        $sql = "select ld.*,
                       round(ld.rate * ld.qty * (1 + ld.vat_rate / 100), 2) as incamount,
                       currencies.name                                      as currencyname,
                       p.name                                               as productname,
                       pc.name                                              as productcategoryname,
                       model.name                                           as brandname,
                       lpo.doc                                              as issuedate,
                       suppliers.name                                       as suppliername,
                       users.name                                           as issuedby,
                       l.name                                               as locationnname,
                       b.name                                               as branchname
                from lpodetails ld
                         inner join lpo on ld.lpoid = lpo.id
                         inner join suppliers on suppliers.id = lpo.supplierid
                         inner join users on lpo.createdby = users.id
                         inner join locations l on lpo.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join currencies_rates cr on cr.id = lpo.currency_rateid
                         inner join currencies on cr.currencyid = currencies.id
                         inner join products p on ld.prodid = p.id
                         inner join product_categories pc on pc.id = p.productcategoryid
                         inner join model on model.id = p.modelid
                where 1 = 1";
        if ($lpoid) $sql .= " and lpo.id = $lpoid";
        if ($supplierid) $sql .= " and suppliers.id = $supplierid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($createdby) $sql .= " and users.id = $createdby";
        if ($productcategoryid) $sql .= " and pc.id = $productcategoryid";
        if ($brandid) $sql .= " and model.id = $brandid";
        if ($brandid) $sql .= " and model.id = $brandid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($fromdate) $sql .= " and date_format(lpo.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(lpo.doc,'%Y-%m-%d') <= '$todate'";

        if ($status == 'canceled') {
            $sql .= "  and lpo.status != 'active'";
        } elseif ($status == 'not-approved') {
            $sql .= "  and lpo.approvedby <= 0";
        } elseif ($status == 'approved') {
            $sql .= "  and lpo.approvedby > 0";
        }

        $sql .= " order by lpo.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }
}
