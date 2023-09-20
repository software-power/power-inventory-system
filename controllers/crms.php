<?php
if($action == 'feedback_index'){
  $user = $_SESSION['member'];
  $clientid = $_GET['clientid'];
  $ticketno = $_GET['ticketno'];
  $feedbackstatus = $_GET['feedbackstatus'];
  $departmentid = $_GET['departmentid'];
  $tData['departid'] = $departmentid;
  $departdetail = $Departments->get($departmentid);
  $tData['departname'] = $departdetail['name'];
  $tData['clientid'] = $clientid;
  $clientdetails = $Clients->get($clientid);
  $tData['clientname'] = $clientdetails['name'];
  $tData['feedbackstatus'] = $feedbackstatus;
  $tData['ticketno'] = $ticketno;

  $tData['userole'] = $user['roleid'];

  if($user['role'] == 'Admin'){
    $tData['tickets'] = $Tickets->ticketForFeedback($ticketno,$clientid,$departmentid,$feedbackstatus);
  }else{
    $tData['tickets'] = $Tickets->ticketForFeedback($ticketno,$clientid,$user['deptid'],$feedbackstatus);
  }
  $data['content'] = loadTemplate('ticketForfeedback_list.tpl.php',$tData);
}

if($action == 'make_fedback'){
  $ticketId = $_GET['id'];
  if ($ticketId) {
    $tData['tickets'] = $Tickets->ticketForFeedback($ticketId,$fromdate="",$todate="");
    $tData['tickets'] = $tData['tickets'][0];
    $tData['department'] = $Departments->get($tData['tickets']['deptid']);
    $tData['client'] = $Clients->get($tData['tickets']['clientid']);
    $qnlist = $Feedbackqns->getFeedbackQns($tData['tickets']['supporttype']);
    if ($qnlist) {
      foreach ($qnlist as $key => $qn) {
        //formulating the possible answers
        $qn['possible_answers'] = $Feedbackqns->getFeedbackQnsReply($qn['fqnid']);
        $qnwithReply[] = $qn;
      }
    }else{
      $tData['popup'] = true;
      $tData['nosample'] = true;
    }
    $tData['questions'] = $qnwithReply;
  }else{
    $tData['popup'] = true;
    $tData['noticket'] = true;
  }
  $data['content'] = loadTemplate('makefedback_edit.tpl.php',$tData);
}

if($action == 'save_fedback'){
  $customerReply = $_POST['customerqn_reply'];
  $sms = $_POST['sms'];
  $ticketId = $_POST['ticketId'];
  $clientid = $_POST['clientid'];
  $clientMobile = $_POST['clientmobile'];
  $clientverify_email = $_POST['clientverify_email'];
  $user = $_SESSION['member'];

  //format mobile number
	if(!empty($_POST['clientverify_mobile'])){
		$mobileLength = strlen((string)$_POST['clientverify_mobile']);
		if($mobileLength == 9){
			$clientverify_mobile = '255'.$_POST['clientverify_mobile'];
		}else{
			$clientverify_mobile = 0;
		}
	}

  //email Verification
  $Clients->update($clientid,array('email'=>$clientverify_email,'mobile'=>$clientverify_mobile));
  //save replay
  $replydet = array('ticketId' =>$ticketId,'createdby'=>$user['id'],'sms'=>$sms);
  $CustomerReply->insert($replydet);
  $replyId = $CustomerReply->lastId();//last saved ID
  //customer reply details
  foreach ($customerReply as $key => $answerid) {
    $details['replyId'] = $replyId;
    $details['qnId'] = $key;
    if (is_numeric($answerid)){
      $details['resId'] = $answerid;
      $details['text'] = "";
    }else {
      $details['resId'] = 0;
      $details['text'] = $answerid;
    }
    $details['createdby'] = $user['id'];
    $CustomerReplyDet->insert($details);
  }
  //update the ticket, hasfeedback to 1
  $Tickets->update($ticketId,array('hasfeedback'=>1));
  //sending SMS to client
  //$sendSMS = sendSms($sms,$clientMobile);
  $_SESSION['message'] = 'Feedback submited successfully';
  redirect('crms','feedback_index');
}

if ($action == 'ajax_verify_ticket') {
  $ticketId = $_GET['ticketId'];
  // $ticketdetails = $Tickets->ticketForFeedback($ticketId);
  $ticketdetails = $Tickets->get($ticketId);
  $response = array();
  if ($ticketdetails) {
    $obj->status ='found';
  	$obj->id =$ticketdetails['id'];
  	$obj->iscompleted = $ticketdetails['statusid'];
  	$obj->isverified = $ticketdetails['isverified'];
  	$obj->hasfeedback = $ticketdetails['hasfeedback'];
  }else{
    $obj->status ='not found';
  }
  $response[]=$obj;
  $data['content'] = $response;
}

if($action == 'summary_report'){
  $summerylist = $Feedbackqns->crmSummaryReport();
  foreach ($summerylist as $key => $result) {
    $newlist[$result['question']][$result['actualanswer']]['customers'] = $result['replycount'];
    $newlist[$result['question']][$result['actualanswer']]['customereply'] = $result['customereply'];
    $newlist[$result['question']][$result['actualanswer']]['answerid'] = $result['answerid'];
  }
  $tData['report'] = $newlist;
  $data['content'] = loadTemplate('crmsummary_list.tpl.php',$tData);
}

if($action == 'details_report'){
  $summerylist = $Feedbackqns->crmSummaryReport();
  $tData['ticketWithFeedback'] = $Feedbackqns->totalIsFeedback('yes');
  $tData['ticketWithNoFeedback'] = $Feedbackqns->totalIsFeedback('no');

  foreach ($summerylist as $key => $result) {
    $newlist[$result['question']][$result['actualanswer']]['customers'] = $result['replycount'];
    $newlist[$result['question']][$result['actualanswer']]['customereply'] = $result['customereply'];
    $newlist[$result['question']][$result['actualanswer']]['questionid'] = $result['questionId'];
    $newlist[$result['question']][$result['actualanswer']]['answerid'] = $result['answerid'];
  }
  $tData['report'] = $newlist;
  $data['content'] = loadTemplate('crmdetails_list.tpl.php',$tData);
}

if($action == 'ajax_getCRMdetails'){
  $qnid = $_GET['qnid'];
  $resid = $_GET['resid'];
  //$replyid = $_GET['replyid'];
  $crmdetails = $Feedbackqns->crmDetailsReport($qnid,$resid);
  $response = array();
  if($crmdetails){
    $obj->status = 'found';
    $obj->details = $crmdetails;
  }else{
    $obj->status = 'not found';
  }
  $response[] = $obj;
  $data['content'] = $response;
}

if($action == 'ajax_getSuggestions'){
  $crmSuggestions = $Feedbackqns->crmSuggestions();
  /*$response = array();
  if($crmSuggestions){
    foreach ($crmSuggestions as $key => $suggestion) {
      $newsuggestion[$suggestion['replyId']]['ticketId'] = $suggestion['ticketId'];
      $newsuggestion[$suggestion['replyId']]['assignedname'] = $suggestion['assignedname'];
      $newsuggestion[$suggestion['replyId']]['assignedbyname'] = $suggestion['assignedbyname'];
      $newsuggestion[$suggestion['replyId']]['clientname'] = $suggestion['clientname'];
      $newsuggestion[$suggestion['replyId']]['departname'] = $suggestion['departname'];
      $newsuggestion[$suggestion['replyId']]['supportname'] = $suggestion['supportname'];
      $newsuggestion[$suggestion['replyId']]['branchname'] = $suggestion['branchname'];
      $newsuggestion[$suggestion['replyId']]['questionId'] = $suggestion['questionId'];
      $newsuggestion[$suggestion['replyId']]['suggestion'] = $suggestion['suggestion'];
    }
    $obj->status = 'found';
    $obj->details = $newsuggestion;
  }else{
    $obj->status = 'not found';
  }*/
  $response[] = $obj;
  $data['content'] = $response;
}

?>
