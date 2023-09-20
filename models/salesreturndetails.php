<?


class SalesReturnDetails extends model
{
    var $table = "sales_return_details";
    static $saleReturnDetailsClass = null;

    function __construct()
    {
        self::$saleReturnDetailsClass = $this;
    }

    function getList($returnid = '', $sdi = '', $check_returnable = false)
    {
        $sql = "select srd.*,
                       IF(srd.sinc, round(srd.return_amount / (1 + srd.vat_rate / 100), 2), round(srd.rate * srd.qty, 2)) as excamount,
                       IF(srd.sinc, srd.return_amount - round(srd.return_amount / (1 + srd.vat_rate / 100), 2),
                          round(srd.rate * srd.qty * (srd.vat_rate / 100), 2))                                            as vatamount,
                       IF(srd.sinc, srd.return_amount,
                          round(srd.rate * srd.qty * (1 + srd.vat_rate / 100), 2))                                        as incamount,
                       round(IF(srd.sinc, round(srd.return_amount / (1 + srd.vat_rate / 100), 2), round(srd.rate * srd.qty, 2)) * sr.currency_amount,
                             2)                                                                                           as base_excamount,
                       round(IF(srd.sinc, srd.return_amount - round(srd.return_amount / (1 + srd.vat_rate / 100), 2),
                                round(srd.rate * srd.qty * (srd.vat_rate / 100), 2)) * sr.currency_amount, 2)             as base_vatamount,
                       round(IF(srd.sinc, srd.return_amount,
                                round(srd.rate * srd.qty * (1 + srd.vat_rate / 100), 2)) * sr.currency_amount, 2)         as base_incamount,
                       sd.stockid,
                       sd.quantity                                                                                        as sold_qty,
                       p.id                                                                                               as productid,
                       if(stocks.id is null, 1, 0)                                                                        as sold_non_stock,
                       p.name                                                                                             as productname,
                       p.description                                                                                      as productdescription,
                       p.track_expire_date,
                       p.trackserialno,
                       departments.name                                                                                   as departmentname,
                       departments.tally_sales_account
                from sales_return_details srd
                         inner join sales_returns sr on srd.srid = sr.id
                         inner join salesdetails sd on sd.id = srd.sdi
                         left join stocks on sd.stockid = stocks.id
                         inner join products p on stocks.productid = p.id or p.id = sd.productid
                         inner join departments on p.departid = departments.id
                where 1 = 1";
        if ($returnid) $sql .= " and sr.id = $returnid";
        if ($sdi) $sql .= " and sd.id = $sdi";

        $results = fetchRows($sql);
        if (!$check_returnable) {
            return $results;
        } else {
            foreach ($results as $index => $item) {
                $previous_returns = self::previousReturns($item['sdi']);
                $previous_returns = array_sum(array_column($previous_returns, 'qty'));
                $results[$index]['previous_returns'] = $previous_returns;
                $results[$index]['returnable'] = ($item['sold_qty'] - $previous_returns - $item['qty']) >= 0;
            }
            return $results;
        }
    }

    function previousReturns($sdi)
    {
        $sql = "select srd.*
                from sales_return_details srd
                         inner join sales_returns sr on sr.id = srd.srid
                where sr.approvedby > 0 and (sr.type = 'full' or sr.type = 'item') and srd.sdi = $sdi";
        return fetchRows($sql);
    }
    function previousPriceChange($sdi)
    {
        $sql = "select srd.*
                from sales_return_details srd
                         inner join sales_returns sr on sr.id = srd.srid
                where sr.approvedby > 0 and sr.type = 'price' and srd.sdi = $sdi";
        return fetchRows($sql);
    }
}