<?

class ProformaDetails extends model
{
    var $table = 'proformadetails';
    static $proformaDetailsClass = null;

    function __construct()
    {
        self::$proformaDetailsClass = $this;
    }

    function getList($productid = "", $clientid = "", $createdby = "", $categoryid = "", $subcategoryid = "", $brandid = "", $departmentid = "", $fromdate = "", $todate = "")
    {
        $sql = "select pd.*,
                       if(pd.sinc, round((pd.incprice * pd.qty) / (1 + pd.vat_rate / 100), 2), round(pd.price * pd.qty, 2)) as excamount,
                       if(pd.sinc, (pd.incprice * pd.qty) - round((pd.incprice * pd.qty) / (1 + pd.vat_rate / 100), 2),
                          round(pd.price * pd.qty * pd.vat_rate / 100, 2))                                                  as vatamount,
                       if(pd.sinc, pd.incprice * pd.qty, round(pd.price * pd.qty * (1 + pd.vat_rate / 100), 2))             as incamount,
                       if(p.id is null, pd.productname, p.name)                                                             as productname,
                       p.id is null                                                                                         as external,
                       pc.name                                                                                              as productcategoryname,
                       psc.name                                                                                             as subcategoryname,
                       m.name                                                                                               as brandname,
                       pr.clientid,
                       cu.name                                                                                              as currencyname,
                       date_add(pr.doc, INTERVAL pr.validity_days day)                                                      as valid_until,
                       date_add(pr.doc, INTERVAL pr.holding_days day)                                                       as hold_until,
                       current_timestamp() <= date_add(pr.doc, INTERVAL pr.holding_days day) and
                       current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_closedby = 0     as isholding,
                       users.name                                                                                           as issuedby,
                       case
                           when current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'pending'
                               then 'pending'
                           when (directsale.id is not null or ordersale.id is not null) and pr.sales_status = 'closed'
                               then 'closed'
                           when current_timestamp() <= date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'closed' and
                                orders.id is not null
                               then 'under order'
                           when current_timestamp() > date_add(pr.doc, INTERVAL pr.validity_days day) and pr.sales_status = 'pending'
                               then 'invalid'
                           end                                                                                              as proforma_status
                from proformadetails as pd
                         inner join proformas as pr on pr.id = pd.proformaid
                         inner join currencies cu on cu.id = pr.currencyid
                         inner join users on users.id = pr.createdby
                         left join sales directsale on directsale.proformaid = pr.id
                         left join orders on orders.proformaid = pr.id
                         left join sales ordersale on ordersale.orderid = orders.id
                         left join products p on p.id = pd.productid
                         left join model m on m.id = p.modelid
                         left join product_categories pc on pc.id = p.productcategoryid
                         left join product_subcategories psc on psc.id = p.subcategoryid
                where 1 = 1";
        if($productid)$sql.=" and pd.productid = ".escapeChar($productid);
        if($clientid)$sql.=" and pr.clientid = ".escapeChar($clientid);
        if($createdby)$sql.=" and pr.createdby = ".escapeChar($createdby);
        if($categoryid)$sql.=" and p.productcategoryid = ".escapeChar($categoryid);
        if($subcategoryid)$sql.=" and p.subcategoryid = ".escapeChar($subcategoryid);
        if($brandid)$sql.=" and p.modelid = ".escapeChar($brandid);
        if($departmentid)$sql.=" and p.departid = ".escapeChar($departmentid);
        if ($fromdate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') >= '" . escapeChar($fromdate) . "'";
        if ($todate) $sql .= " and date_format(pr.doc,'%Y-%m-%d') <= '" . escapeChar($todate) . "'";

        $sql .= " order by pr.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function preparedClosed($locationid, $productid = '', $fromdate = '', $todate = '')
    {
        $sql = "select p.id,
                       p.name,
                       ifnull(prepared.qty, 0)                         as prepared_qty,
                       ifnull(closed.qty, 0)                         as closed_qty,
                       ifnull(prepared.qty, 0) - ifnull(closed.qty, 0) as pending_qty
                from products p
                         left join
                     (
                         select pd.productid,
                                sum(pd.qty) as qty
                         from proformadetails pd
                                  inner join proformas pr on pd.proformaid = pr.id
                                  left join sales directsale on directsale.proformaid = pr.id
                                  left join orders on orders.proformaid = pr.id
                                  left join sales ordersale on ordersale.orderid = orders.id
                         where 1 = 1
                           and pr.locid = $locationid
                           and date_format(pr.doc, '%Y-%m-%d') >= '$fromdate'
                           and date_format(pr.doc, '%Y-%m-%d') <= '$todate'
                         group by pd.productid
                     ) as prepared on p.id = prepared.productid
                         left join
                     (
                         select pd.productid,
                                sum(pd.qty) as qty
                         from proformadetails pd
                                  inner join proformas pr on pd.proformaid = pr.id
                                  left join sales directsale on directsale.proformaid = pr.id
                                  left join orders on orders.proformaid = pr.id
                                  left join sales ordersale on ordersale.orderid = orders.id
                         where 1 = 1
                           and (directsale.id is not null or ordersale.id is not null)
                           and pr.locid = $locationid
                           and date_format(pr.doc, '%Y-%m-%d') >= '$fromdate'
                           and date_format(pr.doc, '%Y-%m-%d') <= '$todate'
                         group by pd.productid
                     ) as closed on p.id = closed.productid
                where 1 = 1";
        if ($productid) $sql .= " and p.id = $productid";
        return fetchRows($sql);
    }
}
