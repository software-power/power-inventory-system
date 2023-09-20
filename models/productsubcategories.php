<?php


class ProductSubCategories extends model
{
    var $table = "product_subcategories";
    public static $class = null;

    function __construct()
    {
        self::$class = $this;
    }

    function search($name)
    {
        $sql = "select * from product_subcategories where name like '%$name%'";
        return fetchRows($sql);
    }

    function getAllSubcategories($categoryId = '')
    {
        $sql = "select sub.*, c.name as categoryName
                from product_subcategories sub
                         inner join product_categories c on c.id = sub.category_id
                where 1=1";
        if ($categoryId) $sql .= " and sub.category_id = $categoryId";
        return fetchRows($sql);
    }
}