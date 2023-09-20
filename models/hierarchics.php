<?

/**
 * model hierarchics
 */
class Hierarchics extends model
{
    var $table = 'hierarchics';


    function search($name)
    {
        $sql = "select name, id from hierarchics where status = 'active' and name like '%" . $name . "%'";
        return fetchRows($sql);
        // echo $sql; die();
    }

    function highestLevel()
    {
        $sql = "select * from hierarchics where level = (select max(level) from hierarchics)";
        return fetchRow($sql);
    }

    function getList($hierarchicid = "", $level = "")
    {
        $sql = "select * from hierarchics where 1=1";
        if($hierarchicid)$sql.=" and id = $hierarchicid";
        if($level)$sql.=" and level >= $level";
        return fetchRows($sql);
    }
}
