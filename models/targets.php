<?


class Targets extends model
{
    var $table = 'targets';

    function getList($userid = "", $departmentid = "", $group = true)
    {
        $sql = "select t.*,
                       users.name   as username,
                       d.name       as departmentname,
                       creator.name as issuedby
                from targets t
                         inner join users on t.userid = users.id
                         inner join departments d on t.departmentid = d.id
                         inner join users creator on t.createdby = creator.id
                where 1 = 1";
        if ($userid) $sql .= " and t.userid = $userid";
        if ($departmentid) $sql .= " and t.departmentid = $departmentid";

        $sql .= " order by t.doc";

        $results = fetchRows($sql);
        if (!$group) {
            return $results;
        } else {
            $new_array = [];
            foreach ($results as $r) {
                $new_array[$r['userid']]['userid'] = $r['userid'];
                $new_array[$r['userid']]['username'] = $r['username'];
                $new_array[$r['userid']]['departments'][$r['departmentid']]['departmentid'] = $r['departmentid'];
                $new_array[$r['userid']]['departments'][$r['departmentid']]['departmentname'] = $r['departmentname'];
                $new_array[$r['userid']]['departments'][$r['departmentid']]['amount'] = $r['amount'];
                $new_array[$r['userid']]['total_amount'] += $r['amount'];
            }
            return array_values($new_array);
        }
    }
}