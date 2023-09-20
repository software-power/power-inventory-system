<?

class Salesdetails extends model
{
    var $table = 'salesdetails';

    static $saleDetailsClass = null;

    function __construct()
    {
        self::$saleDetailsClass = $this;
    }

    function getList($salesid = "", $locationid = "", $productid = "", $iscreditapproved = "", $sdi = "")
    {
        $sql = "select sd.*,
                       sales.receipt_no             as invoiceno,
                       if(stocks.id is null,1,0)    as sold_non_stock,
                       p.id                         as productid,
                       p.name                       as productname,
                       p.departid,
                       p.warrant_month
                from salesdetails sd
                         inner join sales on sd.salesid = sales.id
                         left join stocks on stocks.id = sd.stockid
                         inner join products p on stocks.productid = p.id or p.id = sd.productid
                         inner join locations l on sales.locationid = l.id
                where 1 = 1";
        if ($salesid) $sql .= " and sales.id = $salesid";
        if ($sdi) $sql .= " and sd.id = $sdi";
        if ($iscreditapproved) $sql .= " and sales.iscreditapproved = $iscreditapproved";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($productid) $sql .= " and p.id = $productid";
//         debug($sql);
        return fetchRows($sql);
    }

    function getDetailedList($productid = "", $clientid = "", $createdby = "", $productcategory = "", $productsubcategory = "", $brandid = "",
                             $departmentid = "", $fromdate = "", $todate = "", $iscreditapproved = "", $tra_only = "", $salesid = "", $locationid = "",
                             $branchid = "", $order_invoice_by = "",$from_support="")
    {
        $sql = "select sd.*,
                       sd.price - sd.discount                                                                                                         as sellingprice,
                       if(sd.sinc, round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2),
                          round((sd.price - sd.discount) * sd.quantity, 2))                                                                           as excamount,
                       if(sd.sinc, (sd.incprice * sd.quantity) - round((sd.incprice * sd.quantity) / (1 + sd.vat_rate / 100), 2),
                          round((sd.price - sd.discount) * sd.quantity * sd.vat_rate / 100, 2))                                                       as vatamount,
                       if(sd.sinc, sd.incprice * sd.quantity,
                          round((sd.price - sd.discount) * sd.quantity * (1 + sd.vat_rate / 100), 2))                                                 as incamount,
                       sales.receipt_no                                                                                                               as invoiceno,
                       sales.doc                                                                                                                      as invoicedate,
                       sales.orderid                                                                                                                  as orderno,
                       sales.full_amount,
                       ordercreator.name                                                                                                              as order_creator,
                       clients.id                                                                                                                     as clientid,
                       clients.name                                                                                                                   as clientname,
                       c.name                                                                                                                         as currencyname,
                       p.id                                                                                                                           as productid,
                       p.name                                                                                                                         as productname,
                       if(stocks.id is null, 1, 0)                                                                                                    as sold_non_stock,
                       model.name                                                                                                                     as brandname,
                       d.name                                                                                                                         as departmentname,
                       pc.name                                                                                                                        as productcategoryname,
                       psc.name                                                                                                                       as productsubcategoryname,
                       users.name                                                                                                                     as salesperson,
                       l.id                                                                                                                           as locationid,
                       l.name                                                                                                                         as locationname,
                       br.id                                                                                                                          as branchid,
                       br.name                                                                                                                        as branchname,
                       orders.foreign_orderid,
                       orders.order_source
                from salesdetails sd
                         inner join sales on sd.salesid = sales.id
                         left join orders on sales.orderid = orders.id
                         left join users ordercreator on orders.createdby = ordercreator.id
                         inner join users on users.id = sales.createdby
                         inner join clients on clients.id = sales.clientid
                         inner join currencies_rates cr on sales.currency_rateid = cr.id
                         inner join currencies c on c.id = cr.id
                         left join stocks on stocks.id = sd.stockid
                         inner join locations l on sales.locationid = l.id
                         inner join branches br on l.branchid = br.id
                         inner join products p on stocks.productid = p.id or p.id = sd.productid
                         left join model on p.modelid = model.id
                         left join product_categories pc on p.productcategoryid = pc.id
                         left join product_subcategories psc on p.subcategoryid = psc.id
                         left join departments d on p.departid = d.id
                where 1 = 1";
        if ($salesid) $sql .= " and sales.id = " . escapeChar($salesid);
        if ($clientid) $sql .= " and sales.clientid = ".escapeChar($clientid);
        if ($createdby) $sql .= " and sales.createdby = ".escapeChar($createdby);
        if ($order_invoice_by) $sql .= " and (ordercreator.id = " . escapeChar($order_invoice_by) . " or  sales.createdby = " . escapeChar($order_invoice_by) . ")";
        if ($iscreditapproved) $sql .= " and sales.iscreditapproved = $iscreditapproved";
        if ($tra_only) $sql .= " and sales.receipt_method != 'sr'";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($from_support=='yes') $sql .= " and (orders.order_source = 'support' and orders.foreign_orderid > 0)";
        if ($branchid) {
            if (is_array($branchid)) {
                $sql .= " and br.id in ('" . implode("','", escapeChar($branchid)) . "')";
            } else {
                $sql .= " and br.id = ".escapeChar($branchid);
            }
        }
        if ($productid) $sql .= " and p.id = ".escapeChar($productid);
        if ($brandid) $sql .= " and model.id = ".escapeChar($brandid);
        if ($productcategory) $sql .= " and pc.id = ".escapeChar($productcategory);
        if ($productsubcategory) $sql .= " and psc.id = ".escapeChar($productsubcategory);
        if ($departmentid) $sql .= " and d.id = ".escapeChar($departmentid);
        if ($fromdate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') >= '" . escapeChar($fromdate) . "'";
        if ($todate) $sql .= " and date_format(sales.doc,'%Y-%m-%d') <= '" . escapeChar($todate) . "'";
        $sql .= " order by sales.doc desc";
//         debug($sql);
        return fetchRows($sql);
    }
}
