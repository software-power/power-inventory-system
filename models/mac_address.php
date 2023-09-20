<?php

class Mac_address extends model
{

    public const DEVICE_STATUS_ACTIVE = 'active';
    public const DEVICE_STATUS_BLOCKED = 'blocked';


    var $table = "mac_address";

    function getMacAddress($device_status="",$deleted = false)
    {
        $sql = "select mac_address.*,u.id as userid, u.name as username from mac_address left join users as u on mac_address.id = u.mac_address_id where 1=1";
        if ($device_status) $sql .= " and mac_address.device_status = '$device_status'";
        if($deleted)$sql.=" and mac_address.deleted_at is not null";
        else $sql.=" and mac_address.deleted_at is null";
        return fetchRows($sql);
    }

    function checkMacAddress($macAddress)
    {
        $sql = "select * from mac_address where mac_address = '" . $macAddress . "'";

        return fetchRows($sql);

    }

}


?>