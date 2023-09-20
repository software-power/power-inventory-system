<?php


class SaleFiscalization extends model
{

    public const TYPE_EFD = 'efd';
    public const TYPE_VFD = 'vfd';
    public const TYPE_ZVFD = 'zvfd';

    var $table = "sales_fiscalization";
    static $saleFiscalizeClass = null;

    function __construct()
    {
        self::$saleFiscalizeClass = $this;
    }

    static function save($salesid,array $fiscData){
        $fisc = SaleFiscalization::$saleFiscalizeClass->find(['salesid' => $salesid])[0];
        if ($fisc) {
            SaleFiscalization::$saleFiscalizeClass->update($fisc['id'], $fiscData);
        } else {
            SaleFiscalization::$saleFiscalizeClass->insert($fiscData);
        }
    }
}