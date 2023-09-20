<?php


class SalesDocuments extends model
{
var $table = "sales_documents";
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function  getList($salesid = ''){
        $sql = "select doc.*,
                       sales.receipt_no as invoiceno,
                       users.name       as issuedby,
                       d.name           as documentname
                from sales_documents doc
                         inner join sales on sales.id = doc.salesid
                         inner join documents d on doc.docid = d.id
                         inner join users on doc.createdby = users.id
                where 1 = 1";
        if($salesid)  $sql.=" and sales.id = $salesid";
        return fetchRows($sql);
    }
}