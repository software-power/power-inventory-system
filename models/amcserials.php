<?
/**
 *
 */
class Amcserials extends model
{

  var $table = "amc";

  function getAMCdetails($amcno=""){
    $sql = "select a.id,a.name as serialnumber,a.invoiceno,a.invoiceamount,
    a.amcfrom,a.amcto,a.status,a.serialid,c.name as clientname,p.name productname from amc as a
    inner join serials as s on s.id = a.serialid
    inner join clients as c on c.id = s.clientid
    inner join products as p on p.id = s.prodid where a.id = $amcno";
    //echo $sql;die();
    return fetchRow($sql);
  }

  function getClientAMC($amcid=""){
    $sql = "select
    a.id as amcnumber,a.serialid,a.invoiceamount as amount, a.amcfrom as amcstart,a.amcto as amcend,
    s.name as serialname,c.name as clientname
    from amc as a inner join serials as s on s.id = a.serialid
    inner join clients as c on c.id = s.clientid where 1 = 1";

    if ($amcid) $sql .=" and a.id = $amcid";

    //echo $sql;die();
    return fetchRow($sql);
  }

  function getClientAMCs($depart="",$clientId="",$serialno="",$invoiceno="",$fromdate="",$todate=""){
    $sql = "select a.id as amcnumber,a.invoiceno,a.serialid,
    a.amcfrom as amcstart,a.amcto as amcend,a.status as amcstatus,a.doc as createddate,
    s.name as serialname,c.name as clientname,u.name as createdbyname
    from amc as a
    inner join serials as s on s.id = a.serialid
    inner join clients as c on c.id = s.clientid
    inner join users as u on u.id = a.createdby where 1=1";

    if($depart) $sql .=" and s.deptid = $depart";
    if ($serialno) $sql .=" and a.name = '$serialno'";
    if ($clientId) $sql .=" and s.clientid = $clientId";
    if ($invoiceno) $sql .=" and a.invoiceno = $invoiceno";
    $sql .=" order by a.doc desc";
    //echo $sql;die();
    return fetchRows($sql);
  }

  function getClientAMCreport($depart="",$clientId="",$serialno="",$invoiceno="",$fromdate="",$todate=""){
    $sql = "select a.id as amcnumber,a.invoiceno,a.serialid, a.amcfrom as amcstart,
    a.amcto as amcend,a.doc as createddate,a.status as amcstatus,
    s.name as serialname,c.name as clientname,u.name as createdbyname,
    (select count(serialid) from tickets where serialid = a.serialid) as totalservices,
    (select count(amcid) from tickets where amcid = amcnumber) as amcservices
    from amc as a
    inner join serials as s on s.id = a.serialid
    inner join clients as c on c.id = s.clientid
    inner join users as u on u.id = a.createdby where 1=1 ";
    //where t.doc <= a.doc
    //count(t.serialid) as totalservices,
    if($depart) $sql .=" and s.deptid = $depart";
    if ($serialno) $sql .=" and a.name = '$serialno'";
    if ($clientId) $sql .=" and s.clientid = $clientId";
    if ($invoiceno) $sql .=" and a.invoiceno = $invoiceno";
    $sql .=" group by a.id order by a.doc desc";
    //echo $sql;die();
    return fetchRows($sql);
  }

  function getAMCId($serialno="",$amcto=""){
    $sql = "select a.id from amc as a where a.status = 'active' and
     a.name = '$serialno' and a.amcto = '$amcto'";
    //echo $sql;die();
    return fetchRow($sql);
  }
}
