<?php
/**
 * warranties table
 */
class Warranties extends model{
  var $table = "warranties";
  //function getCurrentWarranty($serialno="")
  function getSerialWarranties($serialid=""){
    $sql = "select w.id, w.name, w.invoiceno,w.invoiceamount,w.warrantydatefrom,
    w.warrantydateto,w.status,w.serialid,w.doc,s.name as serialname,
    c.name as clientname,u.name as createdbyname from warranties as w
    inner join serials as s on s.id = w.serialid
    inner join clients as c on c.id = s.clientid
    inner join users as u on u.id = w.createdby where 1=1 ";
    //echo $sql;die();
    if($serialid) $sql.=" and w.serialid = '$serialid'";
    $sql .=" order by w.doc desc";
    return fetchRows($sql);
  }

  function getClientWarrantyhReport($depart="",$clientId="",$serialno="",$invoiceno="",$fromdate="",$todate=""){
    $sql = "select w.id as warrantynumber,w.invoiceno,w.serialid,
    w.warrantydatefrom as warrantystart,
    w.warrantydateto as warrantyend,w.doc as createddate,w.status as warrantystatus,
    s.name as serialname,c.name as clientname,u.name as createdbyname,
    (select count(serialid) from tickets where serialid = w.serialid) as totalservices,
    (select count(warrantyid) from tickets where warrantyid = warrantynumber) as warrantyservices
    from warranties as w
    inner join serials as s on s.id = w.serialid
    inner join clients as c on c.id = s.clientid
    inner join users as u on u.id = w.createdby where 1=1 ";
    //where t.doc <= a.doc
    //count(t.serialid) as totalservices,
    if($depart) $sql .=" and s.deptid = $depart";
    if ($serialno) $sql .=" and w.name = '$serialno'";
    if ($clientId) $sql .=" and s.clientid = $clientId";
    if ($invoiceno) $sql .=" and w.invoiceno = $invoiceno";
    $sql .=" group by w.id order by w.doc desc";
    //echo $sql;die();
    return fetchRows($sql);
  }
  function getWarrantyId($serialno="",$warrantydateto=""){
    $sql = "select w.id from warranties as w where w.status = 'active' and
     w.name = '$serialno' and w.warrantydateto = '$warrantydateto'";
    //echo $sql;die();
    return fetchRow($sql);
  }
}
 ?>
