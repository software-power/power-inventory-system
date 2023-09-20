<?php
if($action == 'time_list'){
  $tData['timelist'] = $Timeslots->find(array('status'=>'active'));
  $data['content'] = loadTemplate('worktime_list.tpl.php',$tData);
}

if ($action == 'edit_time') {
  $tData['edit'] = true;
  $action = "add_time";
}

if($action == 'add_time'){
  $id = $_GET['id'];
  $tData['timeslot'] = $Timeslots->get($id);
  $data['content'] = loadTemplate('worktime_edit.tpl.php',$tData);
}

if($action == 'timeslot_save'){
  $user = $_SESSION['member'];
  $timeslot = $_POST['timeslot'];
  $id = $_POST['id'];
  if (!empty($id)) {
    //edit
    $timeslot['modifiedby'] = $user['id'];
    $_SESSION['message'] = 'Times is updated successfully';
    $Timeslots->update($id,$timeslot);
    redirect('schedules','time_list');
  }else{
    //new
    $timeslot['createdby'] = $user['id'];
    $Timeslots->insert($timeslot);
    $lastId = $Timeslots->lastId();
    $_SESSION['message'] = 'Times is added successfully';
    redirect('schedules','add_time','id='.$lastId);
  }
}

if($action == 'add_schedules'){
  $scheduleid = $_GET['scheduleid'];
  $tData['scheduleid'] = $scheduleid;
  $scheduleDetails = $Userschedules->scheduleDetails($scheduleid);
  foreach ($scheduleDetails as $key => $details) {
    //full time day time slot with it's Ids
    $newDetails[$details['timeslotid']][$details['ticketslotno']] = $details['ticketid'];
    $newDetails[$details['timeslotid']]['date'] = $details['createdon'];
  }
  $tData['scheduleDetails'] = $newDetails;
  $tData['timeslots'] = $Timeslots->find(array('status'=>'active'),$sortby="sortno asc");
  $data['content'] = loadTemplate('myschedule_edit.tpl.php',$tData);
}

if($action == 'my_schedules'){
  $user = $_SESSION['member'];
  $tData['fromdate'] = $_GET['fromdate'];
  $tData['todate'] = $_GET['todate'];

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
  $tData['schedulelist'] = $Userschedules->mySchedules($user['id']);
  $data['content'] = loadTemplate('myschedule_list.tpl.php',$tData);
}

if($action == 'save_schedule'){
  $scheduleid = $_POST['scheduleid'];
  $ticketime = $_POST['ticketime'];
  $user = $_SESSION['member'];
  $datatimeslotid = $_POST['timeslotid'];//for now not used any where
  if(!empty($scheduleid)){
    //edit
    $Userschedules->deleteDetails($scheduleid);
    foreach ($ticketime as $timeslotId => $tickets) {
      foreach ($tickets as $ticketslotno => $ticketId) {
        if(!empty($ticketId)){
          $scheduledetails['ticketid'] = $ticketId;
          $scheduledetails['userschduleid '] = $scheduleid;
          $scheduledetails['ticketslotno '] = $ticketslotno;
          $scheduledetails['timeslotid  '] = $timeslotId;
          $scheduledetails['dom'] = TIMESTAMP;
          $scheduledetails['createdby  '] = $user['id'];
          $scheduledetails['modifiedby  '] = $user['id'];
          $Userscheduledetails->insert($scheduledetails);//saving the user schedule details
        }
      }
    }
    $_SESSION['message'] = "Your schedule is updated succeffully";
    redirect('schedules','add_schedules&scheduleid='.$scheduleid);
  }else{
    //new
    $userschedule = array('userid' =>$user['id'] ,'createdby'=>$user['id']);
    $check = $Userschedules->find(array('userid'=>$userschedule['userid']),$sortby = 'doc desc');
    $lastScheduledate = $check[0]['doc'];
    //Checking if there is any schedule created for current day
    if (fDate($lastScheduledate) == fDate(TODAY)) {
      //Today there is schedule already
      $lastScheduleid = $check[0]['id'];
      $_SESSION['error'] = "Today there is schedule already";
      redirect('schedules','add_schedules','scheduleid='.$lastScheduleid);
    }else{
      //Today no schedule created
      $Userschedules->insert($userschedule);//saving the user schedule
      $userschedulesId = $Userschedules->lastId();
      foreach ($ticketime as $timeslotId => $tickets) {
        foreach ($tickets as $ticketslotno => $ticketId) {
          if(!empty($ticketId)){
            $scheduledetails['ticketid'] = $ticketId;
            $scheduledetails['userschduleid '] = $userschedulesId;
            $scheduledetails['ticketslotno '] = $ticketslotno;
            $scheduledetails['timeslotid  '] = $timeslotId;
            $scheduledetails['createdby  '] = $user['id'];
            $Userscheduledetails->insert($scheduledetails);//saving the user schedule details
          }
        }
      }
      $_SESSION['message'] = "Your schedule is created succeffully";
    }
    redirect('schedules','my_schedules');
  }
}

if($action == 'printSchedule'){
  $data['layout'] = 'layout_blank.tpl.php';
  $scheduleid = $_GET['scheduleid'];
  $schedulelist = $Userschedules->myFullSchedules($scheduleid);
  if(!empty($schedulelist)){
    foreach ($schedulelist as $key => $schedule) {
      $newList[$schedule['timeslotid']]['slot_'.$schedule['ticketslotno']] = $schedule['ticketid'];
      $newList[$schedule['timeslotid']]['time'] = $schedule['name'];
      $newList[$schedule['timeslotid']]['timeid'] = $schedule['timeslotid'];
      $newList[$schedule['timeslotid']]['type'] = $schedule['type'];
    }
  }
  $tData['schedulelist'] = $newList;
  $tData['createdby'] = $schedulelist[0]['createdby'];
  $tData['createdon'] = $schedulelist[0]['createdon'];
  $data['content'] = loadTemplate('printschedule_list.tpl.php',$tData);
}

if($action == 'ajax_myfullschedules'){
  $user = $_SESSION['member'];
  $scheduleid = $_GET['scheduleid'];
  //$tData['schedulelist'] = $Userschedules->mySchedules($user['id']);
  $schedulelist = $Userschedules->myFullSchedules($scheduleid);
  //echo '<pre>';
  $response = array();
  $obj = null;
  if($schedulelist){
    foreach ($schedulelist as $key => $schedule) {
      $newList[$schedule['timeslotid']]['slot_'.$schedule['ticketslotno']] = $schedule['ticketid'];
      $newList[$schedule['timeslotid']]['time'] = $schedule['name'];
      $newList[$schedule['timeslotid']]['timeid'] = $schedule['timeslotid'];
      $newList[$schedule['timeslotid']]['type'] = $schedule['type'];
    }
    $obj->results = $newList;
    $obj->status = "found";

  }else{
    $obj->status = "not found";
  }

  $response[] =$obj;
  $data['content'] = $response;
}

//verify ticket
if($action == 'ajax_verifyticketForSchedule'){
  $user = $_SESSION['member'];
  $ticketId = $_GET['ticketnumber'];
  $ticketDetails = $Tickets->verifyticketForSchedule($ticketId);

  $response = array();
  $obj = null;
  if($ticketDetails){
    $obj->id = $ticketDetails['id'];
    $obj->isverified = ($ticketDetails['isverified'] == 1 ? 'yes':'no');
    $obj->problem = $ticketDetails['problem'];
    $obj->isInmyDepartment = ($ticketDetails['deptid'] == $user['deptid'] ? 'yes':'no');
    $obj->deptname = $ticketDetails['deptname'];
    $obj->serialid = $ticketDetails['serialid'];
    $obj->product = $ticketDetails['product'];
    $obj->statusname = $ticketDetails['statusname'];
    $obj->isAssignedTome = ($ticketDetails['assignedtoId'] == $user['id'] ? 'yes':'no');
    $obj->assignedtoName = $ticketDetails['assignedtoName'];
    $obj->result = 'found';
  }else{
    $obj->result = "not found";
  }
  $response[] = $obj;
  $data['content'] = $response;
}

if($action == 'time_reconciliation'){
  $tData['fromdate'] = $_GET['fromdate'];
  $tData['todate'] = $_GET['todate'];

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
  $ticketList = $Userschedules->ticketTime();
  $ticketDayTime = $Userschedules->dayTime();
  $ticketScheduleTime = $Userschedules->scheduleTime();

  foreach ($ticketDayTime as $key => $daytime) {
    $newdaytime[$daytime['ticketid']] = $daytime;
  }
  foreach ($ticketScheduleTime as $key => $scheduleTime) {
    $newScheduleTime[$scheduleTime['ticketid']] = $scheduleTime;
  }

  foreach ($ticketList as $index => $ticket) {
    $ticket['daytime'] = $newdaytime[$ticket['ticketid']]['sum(time)'];
    $ticket['schedultime'] = $newScheduleTime[$ticket['ticketid']]['noofslots'];
    $reconciliationList[] = $ticket;
  }
  $tData['reconciliationList'] = $reconciliationList;
  $data['content'] = loadTemplate('timereconciliation_list.tpl.php',$tData);
}

?>
