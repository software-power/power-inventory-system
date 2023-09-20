<?php


class ProductCategories extends model
{
    var $table = "product_categories";
    public static $class = null;

    function __construct()
    {
        self::$class = $this;
    }
    function search($name)
    {
        $sql = "select * from product_categories where status = 'active' and name like '%$name%'";
        return fetchRows($sql);
    }
}