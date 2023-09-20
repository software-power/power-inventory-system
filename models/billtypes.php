<?php


class BillTypes extends model
{
    var $table = "bill_types";
    static $staticClass = null;

    public function period_types()
    {
        return [
            'month', 'year'
        ];
    }

    public function __construct()
    {
        self::$staticClass = $this;
    }
}