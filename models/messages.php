<?
/**
 * support type
 */
class Messages extends model
{
  var $table = "messages";

  function getMessages($jobcard="",$userid="",$fromdate="",$todate="")
  {
    $sql = "select m.*, m.name as receivername, u.name as sendername from messages as m
    inner join users as u on u.id = m.createdby";

    if($jobcard) $sql .=" and m.jobcardid = $jobcard";
    if($userid) $sql .=" and m.createdby = $userid";
    //if($clientid) $sql .=" and m.createdby = $clientid";
    if ($fromdate) $sql .=" and m.doc >= '".$fromdate."'";
    if ($todate) $sql .=" and m.doc <= '".$todate." 23:59'";

    //echo $sql; die();

    return fetchRows($sql);
  }

}
?>
