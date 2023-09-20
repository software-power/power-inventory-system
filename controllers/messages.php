<?php
if ($action == 'message_index') {

  $tData['jobcard'] = $_GET['jobcard'];
  $tData['user'] = $_GET['user'];
  $tData['fromdate'] = $_GET['fromdate'];
  $tData['todate'] = $_GET['todate'];

  $jobcard = $_GET['jobcard'];
  $user = $_GET['user'];


  if ($tData['fromdate']){
  $dateInput = explode('/',$tData['fromdate']);
  $fromdate  = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];

  }else{
    //one month date
    $tData['fromdate'] = date('d/m/Y',strtotime('1 month ago'));
    $fromdate = date('Y-m-d',strtotime('1 month ago'));
  }

  if ($tData['todate']){
  $dateInput = explode('/',$tData['todate']);
  $todate  = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
  }else{
    //today date
  $tData['todate'] = date('d/m/Y');
  $todate = date('Y-m-d');
  }

  $tData['messagelist'] = $Messages->getMessages(
    $jobcard,
    $user,
    $fromdate,
    $todate
  );
  //echo "<pre>";print_r($tData['messagelist']);print_r(TODAY);die();
  $tData['clients'] = $Clients->find(array('status'=>'active'));
  $tData['users'] = $Users->find(array('status'=>'active'));
  $data['content'] = loadTemplate('message_list.tpl.php',$tData);
}

if ($action == 'compose_message') {
  $data['content'] = loadTemplate('message_edit.tpl.php');
}

if ($action == 'send_message') {
	//$senderid =
	$senderid = $_SESSION['member']['id'];
	$message_details = $_POST['message'];
  $current_state = $_GET['state_action'];
  $module = $_GET['state_module'];
	//echo "<pre>";
	//print_r($message_details);
	//print_r($current_state);
	//die();
	if ($message_details['language'] == 'eng') {
		//english message
    $subject = "PCTLSUPPORT";
		$choosemedia = $message_details['media'];
		switch ($choosemedia) {
			case 'sms':
				$sendSMS = sendSms($message_details['english'],$message_details['mobile']);
				if ($sendSMS) {
					$message['messageid'] = $sendSMS['messageid'];
					$message['destination1'] = $sendSMS['destination'];
					$message['messagestatus'] = $sendSMS['status'];
					$message['media'] = $message_details['media'];
					$message['language'] = 'eng';
					$message['text'] = $message_details['english'];
					$message['jobcardid'] = $message_details['jobcardid'];
					$message['name'] = $message_details['name'];
					$message['createdby'] = $senderid;
					//print_r( $message);
					$Messages->insert($message);
					$_SESSION['message'] = 'Your Message was sent successfully';
				}
				break;
			case 'email':
        $usermessage = tumaMail($message_details['email'],$message_details['name'],$subject,$message_details['english']);

        if ($usermessage) {
          $message['email_id'] = 0;
          $message['messageid'] = 'null';
          $message['messagestatus'] = 'null';
					$message['destination2'] = $message_details['email'];
					$message['emailstatus'] = 0;
					$message['media'] = $message_details['media'];
					$message['language'] = 'eng';
					$message['text'] = $message_details['english'];
					$message['jobcardid'] = $message_details['jobcardid'];
					$message['name'] = $message_details['name'];
					$message['createdby'] = $senderid;
					//print_r( $message);
					$Messages->insert($message);
					$_SESSION['message'] = 'Your Email was sent successfully';
        }

				break;
			case 'both':
        //sending the SMS to the client
        $sendSMS = sendSms($message_details['english'],$message_details['mobile']);
        //sending the EMAIL to the client
        $usermessage = tumaMail($message_details['email'],$message_details['name'],$subject,$message_details['english']);

        $message['email_id'] = 0;
        $message['messageid'] = $sendSMS['messageid'];
        $message['messagestatus'] = $sendSMS['status'];
        $message['destination1'] = $sendSMS['destination'];
        $message['destination2'] = $message_details['email'];
        $message['emailstatus'] = 0;
        $message['media'] = $message_details['media'];
        $message['language'] = 'eng';
        $message['text'] = $message_details['english'];
        $message['jobcardid'] = $message_details['jobcardid'];
        $message['name'] = $message_details['name'];
        $message['createdby'] = $senderid;
        //print_r( $message);
        $Messages->insert($message);
        $_SESSION['message'] = 'Your Email and SMS was sent successfully';

				break;
			default:
				$_SESSION['error'] = "Please choose media for sending notification";
				break;
		}

	}else if($message_details['language'] == 'ksw'){
		//kiswahili message
		$text = $message_details['kiswahili'];
		$choosemedia = $message_details['media'];
		switch ($choosemedia) {
			case 'sms':
			$sendSMS = sendSms($message_details['kiswahili'],$message_details['mobile']);
			if ($sendSMS) {
				$message['messageid'] = $sendSMS['messageid'];
				$message['destination1'] = $sendSMS['destination'];
				$message['destination2'] = 'null';
				$message['messagestatus'] = $sendSMS['status'];
				$message['media'] = $message_details['media'];
				$message['language'] = 'ksw';
				$message['text'] = $message_details['kiswahili'];
				$message['jobcardid'] = $message_details['jobcardid'];
				$message['name'] = $message_details['name'];
				$message['createdby'] = $senderid;
				//print_r( $message);
				$Messages->insert($message);
				$_SESSION['message'] = 'Your Message was sent successfully';
			}
				break;
			case 'email':
      $usermessage = tumaMail($message_details['email'],$message_details['name'],$subject,$message_details['kiswahili']);

      if ($usermessage) {
        $message['email_id'] = 0;
        $message['messageid'] = 'null';
        $message['messagestatus'] = 'null';
        $message['destination1'] = 'null';
        $message['destination2'] = $message_details['email'];
        $message['emailstatus'] = 0;
        $message['media'] = $message_details['media'];
        $message['language'] = 'ksw';
        $message['text'] = $message_details['kiswahili'];
        $message['jobcardid'] = $message_details['jobcardid'];
        $message['name'] = $message_details['name'];
        $message['createdby'] = $senderid;
        //print_r( $message);
        $Messages->insert($message);
        $_SESSION['message'] = 'Your Email was sent successfully';
      }

				break;
			case 'both':
      //sending the SMS to the client
      $sendSMS = sendSms($message_details['kiswahili'],$message_details['mobile']);
      //sending the EMAIL to the client
      $usermessage = tumaMail($message_details['email'],$message_details['name'],$subject,$message_details['kiswahili']);

      $message['email_id'] = 0;
      $message['messageid'] = $sendSMS['messageid'];
      $message['messagestatus'] = $sendSMS['status'];
      $message['destination1'] = $sendSMS['destination'];
      $message['destination2'] = $message_details['email'];
      $message['emailstatus'] = 0;
      $message['media'] = $message_details['media'];
      $message['language'] = 'ksw';
      $message['text'] = $message_details['kiswahili'];
      $message['jobcardid'] = $message_details['jobcardid'];
      $message['name'] = $message_details['name'];
      $message['createdby'] = $senderid;
      //print_r( $message);
      $Messages->insert($message);
      $_SESSION['message'] = 'Your Email and SMS was sent successfully';

				break;
			default:
				$_SESSION['error'] = "Please choose media for sending notification";
				break;
		}

	}
	//die();
	//redirect('tickets','verify_ticket');
	redirect($module,$current_state);
}
if ($action == 'ajax_fetchjobcard') {
  $jobid = $_GET['jobcard'];

  $aData = $Tickets->getJobCard($jobid);

  $response = array();
  $obj=null;

  if ($aData) {

    if ($aData['statusname'] == 'Completed') {
      $obj->jobid = $aData['jobid'];
      $obj->serialno = $aData['serialno'];
      $obj->name = $aData['clientname'];
      $obj->mobile = $aData['contactmobile'];
      $obj->email = $aData['clientemail'];
      $obj->statusid = $aData['statusname'];
      $obj->status = 'found';
    }else{
      $obj->status = 'found';
      $obj->serialno = $aData['serialno'];
      $obj->statusid = 'not complete';
    }
  }else{
    $obj->status = 'not found';
  }

  $response[] = $obj;
  $data['content']=$response;



}
