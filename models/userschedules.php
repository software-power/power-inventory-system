<?php

class Userschedules extends model{
  var $table = "userschedules";

  function mySchedules($userId=""){
    $sql = "select u.name,cr.name as createdname,sc.id,sc.userid,sc.doc from userschedules as sc
    inner join users as u on u.id = sc.userid
    left join users as cr on cr.id = sc.createdby where 1=1 ";
    if($userId) $sql .=" and u.id = $userId";
    //echo $sql; die();
    return fetchRows($sql);
  }

  function myFullSchedules($scheduleid=""){
    $sql = "select ud.userschduleid, t.id as timeslotid, t.name,t.type,t.status as timestatus,
    ud.ticketid,ud.ticketslotno,u.name as createdby,usc.doc as createdon from timeslots as t
    left join userscheduledetails as ud on ud.timeslotid = t.id
    left join userschedules as usc on usc.id = ud.userschduleid
    left join users as u on u.id = usc.userid where 1=1 ";//group by ud.ticketslotno";
    if($scheduleid) $sql .=" and ud.userschduleid = $scheduleid or ud.userschduleid is null ";
    $sql .="order by t.sortno asc";
    //echo $sql; die();
    return fetchRows($sql);
  }

  function scheduleDetails($scheduleid){
    $sql = "select ud.*,usc.doc as createdon from userscheduledetails as ud
    inner join userschedules as usc on usc.id = ud.userschduleid
     where userschduleid = $scheduleid";
    //echo $sql; die();
    return fetchRows($sql);
  }

  function deleteDetails($scheduleid){
    $sql = "delete from userscheduledetails
    where userscheduledetails.userschduleid = $scheduleid ";
    //echo $sql; die();
    return executeQuery($sql);
  }
  function ticketTime(){
    $sql = "select t.id as ticketid, t.serialno,t.serialid,t.timespent,
    c.name clientname,u.name as assignedname,b.name as branchname from tickets as t
    inner join clients as c on c.id = t.clientid
    inner join branches as b on b.id = t.branchid
    inner join users as u on u.id = t.assignedto order by t.doc desc";
    //echo $sql;die();
    return fetchRows($sql);
  }
  function dayTime(){
    $sql = "select ticketid, sum(time) from ticketdetails group by ticketid";
    //echo $sql;die();
    return fetchRows($sql);
  }
  function scheduleTime(){
    $sql = "select count(ticketid) as noofslots, ticketid
    from userscheduledetails group by ticketid";
    //echo $sql;die();
    return fetchRows($sql);
  }
}
?>
