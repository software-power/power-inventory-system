<?php
/**
 * feedbackqns -> module for feedback questions
 */
class Feedbackqns  extends model{
  var $table = 'feedbackqns';
  function getFeedbackQns($supportId=""){
    $sql = "select fq.id as fqnid, fq.name qname, fq.status from feedbackqns as fq where 1=1";
    if($supportId) $sql .=" and fq.supportyId = $supportId";
    //echo $sql; die();
    return fetchRows($sql);
  }
  function getFeedbackQnsReply($qnId){
    $sql = "select r.id, r.name,r.status from feedbackqnsreply as r
    where r.feedbackqnsId = $qnId";
    //echo $sql; die();
    return fetchRows($sql);
  }
  function totalIsFeedback($yesOrNo=""){
    $sql = "select count(*) as numberOfticket FROM tickets where 1=1 ";
    if($yesOrNo == 'yes'){
      $sql .=" and hasfeedback = 1";
    }else if($yesOrNo == 'no'){
      $sql .=" and hasfeedback = 0";
    }
    //echo $sql;die();
    return fetchRow($sql);
  }

  function crmSummaryReport(){
    $sql = "select fq.id as questionId,fq.name as question,fr.name as actualanswer,
    cr.replyId as customereply,cr.resId as answerid,
    count(fr.name) as replycount from feedbackqns as fq
    inner join feedbackqnsreply as fr on fr.feedbackqnsId = fq.id
    inner join customer_replydetails as cr on cr.qnId = fq.id and cr.resId = fr.id
    group by fq.name, fr.name";
    //echo $sql;die();
    return fetchRows($sql);
  }
  function crmDetailsReport($qnid="",$answerid=""){
    $sql = "select cl.ticketId,u.name as assignedname,us.name as assignedbyname,
    c.name as clientname,s.name as serialno,p.name as productname,d.name as departname,
    st.name as statusname,su.name as supportname,b.name as branchname,fq.id as questionId,
    fq.name as question,fr.name as actualanswer
    from feedbackqns as fq
    inner join feedbackqnsreply as fr on fr.feedbackqnsId = fq.id
    inner join customer_replydetails as cr on cr.qnId = fq.id and cr.resId = fr.id
    inner join customer_reply as cl on cl.id = cr.replyId
    inner join tickets as t on t.id = cl.ticketId
    inner join clients as c on c.id = t.clientid
    inner join users as u on u.id = t.assignedto
    inner join users as us on us.id = t.assignedby
    inner join serials as s on s.id = t.serialid
    inner join products as p on p.id = t.prodid
    inner join departments as d on d.id = t.deptid
    inner join statuses as st on st.id = t.statusid
    inner join supporttype as su on su.id = t.supporttype
    inner join branches as b on b.id = t.branchid where 1=1 ";

    if($qnid) $sql.=" and cr.qnId = $qnid";
    if($answerid) $sql.=" and cr.resId = $answerid";
    //if($replyid) $sql.=" and cr.replyId	= $replyid";
    //echo $sql;die();
    return fetchRows($sql);
  }

  function crmSuggestions(){
    $sql = "select cl.ticketId,u.name as assignedname,us.name as assignedbyname,
    c.name as clientname,s.name as serialno,p.name as productname,d.name as departname,
    su.name as supportname,b.name as branchname,fq.id as questionId,
    fq.name as question,cr.replyId, cr.text as suggestion
    from feedbackqns as fq
    inner join customer_replydetails as cr on cr.qnId = fq.id
    inner join customer_reply as cl on cl.id = cr.replyId
    inner join tickets as t on t.id = cl.ticketId
    inner join clients as c on c.id = t.clientid
    inner join users as u on u.id = t.assignedto
    inner join users as us on us.id = t.assignedby
    inner join serials as s on s.id = t.serialid
    inner join products as p on p.id = t.prodid
    inner join departments as d on d.id = t.deptid
    inner join supporttype as su on su.id = t.supporttype
    inner join branches as b on b.id = t.branchid";

    //echo $sql;die();
    return fetchRows($sql);
  }
}
 ?>
