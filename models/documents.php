<?php


class Documents extends model
{
var $table = "documents";
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }
}