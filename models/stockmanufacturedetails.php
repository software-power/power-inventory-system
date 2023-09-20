<?php


class StockManufactureDetails extends model
{
    var $table = "stock_manufacture_details";

    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($manufactureid = '')
    {
        $sql = "select smd.*,
                       p.id                 as productid,
                       p.name               as productname,
                       p.description,
                       p.baseprice,
                       p.track_expire_date,
                       p.trackserialno,
                       p.barcode_office,
                       p.barcode_manufacture,
                       categories.vat_percent
                from stock_manufacture_details smd
                         inner join stocks on stocks.id = smd.stockid
                         inner join products p on p.id = stocks.productid
                         inner join categories on p.categoryid = categories.id
                where 1 = 1";
        if ($manufactureid) $sql .= " and smd.manufactureid = $manufactureid";
        return fetchRows($sql);
    }
}