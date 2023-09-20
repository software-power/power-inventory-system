<?php


class RecurringBillDetails extends model
{
    var $table = "recurring_bill_details";
    public static $staticClass;

    public function __construct()
    {
        self::$staticClass = $this;
    }

    public function getList($billid = "")
    {
        $sql = "select rbd.*,
                       p.name                                                                                                   as productname,
                       p.description                                                                                            as productdescription,
                       p.non_stock,
                       if(rbd.sinc, rbd.incprice, round(rbd.price * (1 + rbd.vat_rate / 100), 2))                               as incprice,
                       if(rbd.sinc, rbd.incprice * rbd.qty, round(rbd.price * rbd.qty * (1 + rbd.vat_rate / 100), 2))           as incamount,
                       if(rbd.sinc, round(rbd.incprice * rbd.qty / (1 + rbd.vat_rate / 100), 2), round(rbd.price * rbd.qty, 2)) as excamount,
                       if(rbd.sinc, (rbd.incprice * rbd.qty) - round(rbd.incprice * rbd.qty / (1 + rbd.vat_rate / 100), 2),
                          round(rbd.price * rbd.qty * (rbd.vat_rate / 100), 2))                                                 as vatamount
                from recurring_bill_details rbd
                         inner join products p on rbd.productid = p.id
                where 1 = 1";

        if ($billid) $sql .= " and rbd.billid = $billid";
        return fetchRows($sql);
    }
}