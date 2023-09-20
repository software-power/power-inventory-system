<?

class Locations extends model
{
    var $table = "locations";

    public static $locationClass = null;

    function __construct()
    {
        self::$locationClass = $this;
    }

    function search($name)
    {
        $sql = "select l.id as locationid, l.name as locationname, b.id as branchid, b.name as branchname
                from locations l
                         inner join branches b on l.branchid = b.id
                where l.status = 'active' and l.name like '%$name%'";
        return fetchRows($sql);
    }

    function locationList($locationid="",$branchid="",$location_status="",$first_locationid="")
    {
        $sql = "select l.*,
                       b.name as branchname
                from `locations` l
                         inner join branches b on l.branchid = b.id
                where 1 = 1";
        if($locationid)$sql.=" and l.id = $locationid";
        if($branchid)$sql.=" and b.id = $branchid";
        if($location_status)$sql.=" and l.status = '$location_status'";

        if($first_locationid)$sql .=" order by field(l.id, $first_locationid) desc, l.id";

        return fetchRows($sql);
    }

    function getBranch($locationid)
    {
        $sql = "select b.* from branches b inner join locations l on b.id = l.branchid where l.id = $locationid";
        return fetchRow($sql);
    }

    function defaultBranchLocation($branchid)
    {
        $sql = "select l.*,
                       b.name as branchname
                from locations l
                         inner join branches b on l.branchid = b.id
                where b.id = $branchid and l.default_load = 1";

        return fetchRow($sql);
    }
} 
