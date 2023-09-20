<?php

class SalesPrescriptions extends model
{
    var $table = "sales_prescription_details";
    static $salesPrescriptionClass = null;
    function __construct()
    {
        self::$salesPrescriptionClass = $this;
    }

    function getReferrals($productId = "", $doctorId = "", $hospitalId = "", $fromdate = "", $todate = "")
    {
        $sql = "select spd.*,
                       d.name                                             as doctorname,
                       h.name                                             as hospitalname,
                       if(stocks.id is null,1,0)                          as sold_non_stock,
                       p.name                                             as productname,
                       sd.amount,
                       round(sd.price * sd.quantity * (sd.vat_rate / 100),2) as vat_amount,
                       round(sd.price * sd.quantity * (1 + sd.vat_rate / 100),2) as sellAmount,
                       sd.doc                                             as selldate
                from sales_prescription_details spd
                         inner join doctors d on spd.doctor_id = d.id
                         inner join hospitals h on spd.hospital_id = h.id
                         inner join salesdetails sd on spd.sdi = sd.id
                         left join stocks on sd.stockid = stocks.id
                         inner join products p on stocks.productid = p.id or p.id = sd.productid
                where spd.referred = 1";
        if ($productId) $sql .= " and p.id = $productId";
        if ($doctorId) $sql .= " and spd.doctor_id = $doctorId";
        if ($hospitalId) $sql .= " and spd.hospital_id = $hospitalId";
        if ($fromdate) $sql .= " and date_format(sd.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sd.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by sd.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function getInfo($sdi)
    {
        $sql = "select spd.*,
                       d.name as doctorname,
                       h.name as hospitalname
                from sales_prescription_details spd
                         inner join hospitals h on spd.hospital_id = h.id
                         inner join doctors d on spd.doctor_id = d.id
                where 1 = 1 and spd.sdi = $sdi";
//        debug($sql);
        return fetchRow($sql);
    }

}
