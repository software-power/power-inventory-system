<?php
if ($action == 'warranty_list') {
  $tData['warrantyList'] = $Warranties->getSerialWarranties($serial);
  $data['content'] = loadTemplate('warranty_list.tpl.php',$tData);
}

if ($action == 'warranty_services') {
	$user = $_SESSION['member'];
  $branch = $_GET['branch'];
  $departId = $_GET['department'];
  $serialno = $_GET['serialno'];
  $clientid = $_GET['clientid'];
  $invoiceno = $_GET['invoiceno'];

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

	if($user['role'] == 'Admin'){
		$getList = $Warranties->getClientWarrantyhReport($depart,$clientid,$serialno,$invoiceno,$fromdate,$todate);
	}else{
		$getList = $Warranties->getClientWarrantyhReport($user['deptid'],$clientid,$serialno,$invoiceno,$fromdate,$todate);
	}

  $tData['braches'] = $Branches->find(array('status'=>'active'));
	$tData['warrantylist'] = $getList;
	$data['content'] = loadTemplate('warrantyservices_list.tpl.php',$tData);
}

if ($action == 'add_warranty') {
  $data['content'] = loadTemplate('warranty_edit.tpl.php');
}

if ($action == 'save_warranty') {
  $id = $_POST['warrantyid'];
  $user = $_SESSION['member'];
  $warranty = $_POST['warranty'];

  //changing the date format for inserting into database
  $warrantyInput = explode('/', $warranty['warrantydatefrom']);
  $warranty['warrantydatefrom'] = $warrantyInput[2].'-'.$warrantyInput[1].'-'.$warrantyInput[0];

  $warrantyInputTo = explode('/', $warranty['warrantydateto']);
  $warranty['warrantydateto'] = $warrantyInputTo[2].'-'.$warrantyInputTo[1].'-'.$warrantyInputTo[0];
  /************END***************/
$serialdetails = array(
  'warrantydatefrom' => $warranty['warrantydatefrom'],
  'warrantydateto' => $warranty['warrantydateto']);

  if(empty($id)){
    //new
    $warranty['createdby'] = $user['id'];
    $serialnumber = $Serials->find(array('status'=>'active','name'=>$warranty['name']));
    if($serialnumber){
      $warranty['serialid'] = $serialnumber[0]['id'];
      $Warranties->insert($warranty);
      //update warranty status to serial number
      $Serials->update($warranty['serialid'],$serialdetails);
      $_SESSION['message'] = "Warranty is updated";
    }else{
      $_SESSION['error'] = "Serial Number not found";
    }
  }
  redirect('warranties','warranty_list');
}

if ( $action == 'ajax_getWarrantyTickets' ) {
		$serialId = $_GET['serialId'];
		$warrantyid = $_GET['warrantyid'];
		$icData = $Tickets->getWarrantyTickets($serialId,$warrantyid);
		$response = array();

		if($icData){
			$obj->details = $icData;
			$obj->message ='Found';
		}else{
			$obj->message ='Not found';
		}

		$response[]=$obj;
		$data['content']=$response;
}
?>
