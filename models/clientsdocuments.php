<?php


class ClientsDocuments extends model
{
    var $table = "client_documents";
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($clientid = '')
    {
        $sql = "select doc.*,
                       c.name     as clientname,
                       users.name as issuedby,
                       d.name     as documentname
                from client_documents doc
                         inner join clients as c on c.id = doc.clientid
                         inner join documents d on doc.docid = d.id
                         inner join users on doc.createdby = users.id
                where 1 = 1";
        if ($clientid) $sql .= " and c.id = $clientid";
        return fetchRows($sql);
    }
}