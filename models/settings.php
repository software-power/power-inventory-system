<?php

class Settings extends model
{
    var $table = "settings";
    public static $staticClass = null;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    function getSettings(){
        $sql = "select *
                from settings
                         inner join settings2 on settings2.id = 1";
//        debug($sql);
        return fetchRow($sql);
    }
}
