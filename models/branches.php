<?php

class Branches extends model
{
    var $table = "branches";

    public static $branchClass = null;

    function __construct()
    {
        self::$branchClass = $this;
    }

    function search($name)
    {
        $sql = "select name, id from branches where status = 'active' and name like '%" . $name . "%'";
        return fetchRows($sql);
    }

    function searchResults($name = "")
    {
        $sql = "select * from branches where status = 'active' and name like '%" . $name . "%' order by name";

        // echo $sql;die();
        return fetchRows($sql);
    }

    function receiptSalesCount($branchId, $system_receipt = true)
    {
        $sql = "select count(sales.id) as salesCount from sales
				inner join locations l on l.id=sales.locationid
				inner join branches b on l.branchid=b.id
				where b.id = $branchId";
        if ($system_receipt) {
            $sql .= " and sales.receipt_method = 'sr'";
        } else {
            $sql .= " and sales.receipt_method != 'sr'";
        }
//        debug($sql);
        return fetchRow($sql);
    }
}
