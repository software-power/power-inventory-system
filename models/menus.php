<?

class Menus extends model
{
    var $table = "menus";

    function getAllMenus()
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
                    from menus as m
                             left join submenus as s on s.menuid = m.id and s.status = 1
                    where m.status = 1
                    order by m.sortno, s.sortno";
        $menus = [];
        foreach (fetchRows($sql) as $m) {
            $menus[$m['mlabel']]['module'] = $m['mmod'];
            $menus[$m['mlabel']]['action'] = $m['mact'];
            $menus[$m['mlabel']]['icon'] = $m['micon'];
            if ($m['slabel']) $menus[$m['mlabel']]['subs'][] = $m;
        }
        return $menus;
    }
}
