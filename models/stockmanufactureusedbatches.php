<?php


class StockManufactureUsedBatches extends model
{
    var $table = "stock_manufacture_used_batches";

    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($smdi = '')
    {
        $sql = "";

        return fetchRows($sql);
    }
}