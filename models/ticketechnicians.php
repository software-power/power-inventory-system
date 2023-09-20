<?php
/**
 *ticketechnicians
 */
class Ticketechnicians extends model{
  var $table = "ticketechnicians";
  function ticketParticipants($ticketId){
    $sql = "select u.name,u.id as userId,t.ticketid,t.assignby from ticketechnicians as t
    inner join users as u on u.id = t.userid where t.ticketid = $ticketId";
    //echo $sql;die();
    return fetchRows($sql);
  }
}
 ?>
