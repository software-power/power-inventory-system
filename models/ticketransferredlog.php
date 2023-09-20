<?php
/**
 * ticketransferredlog
 */
class Ticketransferredlogs extends model{
  var $table = 'ticketransferredlog';

  function geticketLog($ticketid){
    $sql = "select t.id, t.serialno,c.name as clientname,
    p.name as productname,d.name as departname,
    b.name as frombranch,tec.name as wasassignedto,
    hd.name as wasassignedby,date_format(tf.wasassignedate,'%W,%M %Y') as wasassignedate,
    tf.ploc,date_format(tf.doc, '%W,%M %Y') as createdate from ticketransferredlog as tf
    inner join tickets as t on t.id = tf.ticketid
    inner join products as p on p.id = t.prodid
    inner join departments as d on d.id = t.deptid
    inner join clients as c on c.id = t.clientid
    inner join branches as b on b.id = tf.frombranch
    left join users as tec on tec.id = tf.wasassignedto
    left join users as hd on hd.id = tf.wasassignedby where tf.ticketid = $ticketid";

    $sql .=" order by tf.doc";
    //echo $sql; die();
    return fetchRows($sql);
  }
}
 ?>
