<?


class RoleRights extends model
{
    var $table = 'role_rights';

    function getList($roleid)
    {
        $sql = "select rr.submenuid, sub.menuid
                from role_rights rr
                         inner join submenus sub on rr.submenuid = sub.id
                         inner join menus on menus.id = sub.menuid
                where 1 = 1 and rr.roleid = $roleid";
        return fetchRows($sql);
    }

    function getUserMenus($roleid)
    {
        $sql = "select m.id     as mid,
                       m.label  as mlabel,
                       m.module as mmod,
                       m.action as mact,
                       m.icon   as micon,
                       s.id     as sid,
                       s.label  as slabel,
                       s.module as smod,
                       s.action as sact
                from role_rights as rr
                         inner join submenus as s on s.id = rr.submenuid and s.status = 1
                         inner join menus as m on m.id = s.menuid and m.status = 1
                where rr.roleid = $roleid order by m.sortno, s.sortno";
        $menus = [];
        foreach (fetchRows($sql) as $m) {
            $menus[$m['mlabel']]['module'] = $m['mmod'];
            $menus[$m['mlabel']]['action'] = $m['mact'];
            $menus[$m['mlabel']]['icon'] = $m['micon'];
            if ($m['slabel']) $menus[$m['mlabel']]['subs'][] = $m;
        }
        return $menus;
    }


    static function isAllowed($module,$action,$roleid): bool
    {
        $sql = "select rr.id
                from role_rights rr
                         inner join roles on rr.roleid = roles.id and roles.status = 'active'
                         inner join submenus s on rr.submenuid = s.id
                where s.module = '$module'  and s.action = '$action' and rr.roleid = $roleid";
        return count(fetchRows($sql)) > 0;
    }

    static function fromAction($other_right_action, $roleid): bool
    {
        $sql = "select ror.*
                from role_other_rights ror
                         inner join roles on ror.roleid = roles.id and roles.status = 'active'
                         inner join other_rights on other_rights.id = ror.orid
                where other_rights.action = '$other_right_action' and ror.roleid = $roleid";
//        debug($sql);
        return count(fetchRow($sql)) > 0;
    }
}