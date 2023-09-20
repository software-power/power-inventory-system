<?

class BulkUnits extends model
{
    var $table = 'bulk_units';

    function search($name){
        $sql = "select * from bulk_units where status = 'active' and (name like '%".$name."%' or abbr like '%".$name."%')";
//     debug($sql);
        return fetchRows($sql);
    }

    function getBulkUnits()
    {
        $sql = "select bulk_units.*,
                       units.name as singleUnit,
                       units.abbr as singleUnitAbbr
                from `bulk_units`
                         inner join units on units.id = bulk_units.unit
                where bulk_units.status = 'active'";
        return fetchRows($sql);
    }
}
