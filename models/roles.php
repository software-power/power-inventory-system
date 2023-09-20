<?

class Roles extends model
{
    var $table = "roles";

    function getList($roleid="")
    {
        $sql = "select roles.*, creator.name as creator, count(users.id) as usercount
                from roles
                         left join users on users.roleid = roles.id
                         inner join users creator on creator.id = roles.createdby
                where 1 = 1";
        if($roleid)$sql.=" and roles.id = $roleid";

        $sql.=" group by roles.id";
        return fetchRows($sql);
    }

}