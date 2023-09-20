<?


class Users extends model
{
    use Authorization;
    var $table = "users";

    public static $userClass = null;

    function __construct()
    {
        self::$userClass = $this;
    }

    function search($name)
    {
        $sql = "select name, id from users where status = 'active' and name like '%" . $name . "%'";
        return fetchRows($sql);
    }

    function searchResults($name = "")
    {
        $sql = "SELECT users.*, users.id AS userid, users.name AS staff, departments.name AS depart FROM `users`
					INNER JOIN departments ON
					users.deptid = departments.id
					WHERE users.status = 'active'
					AND users.name LIKE '%$name%'";
        return fetchRows($sql);
    }

    function userList($name = "", $userid = "", $status = "", $locationid = "", $branchid = "")
    {
        $sql = "select users.*,
                       b.name       as branchname,
                       l.name       as locationname,
                       d.name       as departmentname,
                       h.name       as hierarchicname,
                       roles.name   as rolename,
                       creator.name as createdby
                from users
                         inner join branches b on users.branchid = b.id
                         inner join locations l on users.locationid = l.id
                         inner join departments d on users.deptid = d.id
                         inner join hierarchics h on users.hierachicid = h.id
                         inner join roles on users.roleid = roles.id
                         inner join users creator on users.createdby = creator.id
                where 1=1";
        if ($userid) {
            $sql .= " and users.id = $userid";
            return fetchRow($sql);
        }
        if ($name) $sql .= " and users.name like '%$name%'";
        if ($status) $sql .= " and users.status = '$status'";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        return fetchRows($sql);
    }

    function userDetails($userid = '')
    {
        $sql = "select id,name,roleid,deptid,branchid,head,status from users where id= $userid";
        //echo $sql;die();
        return fetchRow($sql);
    }

    function getDp($user = '')
    {
        $sql = "select image from users where id =" . $user;
        return fetchRow($sql);
    }

    function locationUserForExpireNotification($locationId)
    {
        $sql = "select id,username,name,locationid from users where status = 'active' and locationid = $locationId and expire_notification = 1";
//        debug($sql);
        return fetchRows($sql);
    }

    function locationUserForStockLevelNotification($locationId)
    {
        $sql = "select id,username,name,locationid from users where status = 'active' and locationid = $locationId and stock_notification = 1";
//        debug($sql);
        return fetchRows($sql);
    }

    function branchUserForSupplierNotification($branchid)
    {
        $sql = "select id,username,name,branchid from users where status = 'active' and branchid = $branchid and supplier_notification = 1";
//        debug($sql);
        return fetchRows($sql);
    }

}
