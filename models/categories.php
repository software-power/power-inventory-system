<?

class Categories extends model
{
    var $table = 'categories';
    static $categoryClass = null;

    function __construct()
    {
        self::$categoryClass = $this;
    }

    function getList($categoryid = '')
    {
        $sql = "select c.*,
                   taxcode.name as taxcodename,
                   count(p.id)  as product_count
            from categories c
                     inner join taxcode on c.taxcode = taxcode.id
                     left join products as p on p.categoryid = c.id
            where 1 = 1";
        if ($categoryid) $sql .= " and c.id = $categoryid";
        $sql .= " group by c.id";
        return fetchRows($sql);
    }
}
