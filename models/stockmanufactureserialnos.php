<?php


class StockManufactureSerialnos extends model
{
    var $table = "stock_manufacture_serialnos";

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