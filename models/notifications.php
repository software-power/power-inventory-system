<?


class Notifications extends model
{
    public const NOTIFICATION_ABOUT_SUPPLIER = 'supplier';
    public const NOTIFICATION_ABOUT_STOCK = 'stock';
    public const NOTIFICATION_ABOUT_EXPIRE = 'expire';
    public const NOTIFICATION_ABOUT_OTHER = 'other';

    public const NOTIFICATION_TYPE_SUCCESS = 'success';
    public const NOTIFICATION_TYPE_DANGER = 'danger';
    public const NOTIFICATION_TYPE_WARNING = 'warning';

    var $table = "notifications";
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getNotifications($toid = "", $fromid = "", $state = "unread",$limit="",$offset="",$search="")
    {
        $sql = "select n.*,
                       u.name as toname,
                       IF(n.fromid = 0,'System',fromuser.name) as fromname
                from notifications n
                         inner join users u on u.id = n.toid
                         left join users fromuser on fromuser.id = n.fromid
                where 1=1";
        if ($fromid) $sql .= " and n.fromid = $fromid";
        if ($toid) $sql .= " and n.toid = $toid";
        if ($state) $sql .= " and n.state = '$state'";
        if ($search) $sql .= " and (n.about like '%$search%' or n.body like '%$search%')";
        $sql.=" order by n.doc desc";
        if (isset($limit) && isset($offset)) $sql .= " limit $limit,$offset";
        elseif ($limit) $sql .= " limit $limit";
//        debug($sql);
        return fetchRows($sql);
    }
}