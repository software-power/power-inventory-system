<?php
/**
 * ticketdetails
 */
class Ticketdetails extends model{
  var $table = "ticketdetails";
  function getTicketDet($ticketId){
    $sql = "select td.id as ticketdetId,td.date,td.time,td.remark,td.doc,u.name from ticketdetails as td
    inner join ticketdetailtech as tec on tec.ticketdetid = td.id
    inner join users as u on u.id = tec.technicianId where td.ticketid = $ticketId ";
    //echo $sql;die();
    return fetchRows($sql);
  }
  function deleteDetails($ticketDetId){
    $sql = "delete ticketdetails ,ticketdetailtech from ticketdetails
    inner join ticketdetailtech where ticketdetails.id = ticketdetailtech.ticketdetid
    and ticketdetails.id = $ticketDetId ";
    return executeQuery($sql);
  }
}
 ?>
