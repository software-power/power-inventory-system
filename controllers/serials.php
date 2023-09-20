<?php
if ($action == 'client_serials') {
	$user = $_SESSION['member'];
	$id = $_GET['id'];
	if ($user['role'] == 'Admin') {
		$tData['serial'] = $Serials->getClientSerials($id);
	}else{
		$tData['serial'] = $Serials->getClientSerials($id,$user['deptid']);
	}
	$data['content'] = loadTemplate('client_serials_list.tpl.php',$tData);
}
if ($action == 'serial_index' ) {
	$user = $_SESSION['member'];
	$tData['name'] = $_GET['name'];
	$dept=$_SESSION['member']['deptid'];
	$tData['head'] = $user['head'];
	$tData['role'] = $user['roleid'];
	$brach = $user['branchid'];

	if ($tData['name']) {
		$tData['serial'] = $Serials->searchResults($tData['name'],$brach,$dept,$order="name",$limit="");
	}else{
		$tData['serial'] = $Serials->searchResults($name="",$brach,$dept,$order="doc",$limit="10");
	}
	$data['content'] = loadTemplate('serial_list.tpl.php',$tData);
}

if ($action == 'myassigned_fiscal' ) {
	$user = $_SESSION['member'];
	$tData['fromdate'] = $_GET['fromdate'];
  $tData['todate'] = $_GET['todate'];
	$serialno = $_GET['serialno'];
	$fstatus = $_GET['fstatus'];
	$clientid = $_GET['clientid'];
	$dept=$user['deptid'];
	$brach=$user['branchid'];
	$head = $_SESSION['member'];

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
	$clientdet = $Clients->get($clientid);
	$tData['serialno'] = $serialno;
	$tData['clientid'] = $clientid;
	$tData['clientname'] = $clientdet['name'];
	$tData['fstatus'] = $fstatus;
	$tData['serials'] = $Serials->myAssignedFiscale($user['id'],$serialno,$fstatus,$clientid,$fromdate,$todate);
	$data['content'] = loadTemplate('myassignedfiscal_list.tpl.php',$tData);
}

if ($action == 'fiscalized_list' ) {
	$user = $_SESSION['member'];
	$tData['fromdate'] = $_GET['fromdate'];
  $tData['todate'] = $_GET['todate'];
	$serialno = $_GET['serialno'];
	$fstatus = $_GET['fstatus'];
	$clientid = $_GET['clientid'];
	$fortech = $_GET['userid'];
	$dept=$user['deptid'];
	$brach=$user['branchid'];

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

	$clientdet = $Clients->get($clientid);
	$tData['serialno'] = $serialno;
	$tData['clientid'] = $clientid;
	$tData['clientname'] = $clientdet['name'];
	$tData['selectedtech'] = $fortech;

	if($user['roleid'] == 1){
		$tData['tech'] = $Users->getAll();
	}else{
		$tData['tech'] = $Users->find(array('deptid' =>$user['deptid'],'branchid'=>$user['branchid']));
	}

	$tData['serials'] = $Serials->getFiscalizedlist($fortech,$serialno,$depart,$branch,$clientid,$fromdate="",$todate="");
	$data['content'] = loadTemplate('fiscalized_list.tpl.php',$tData);
}

if ($action == 'send_sms') {
	//$msg = sendSms('test sms','255719525658');
	//echo "<pre>";
	$option = ['cost'=>12];
	$new = 'Aminally';
	$newPassword = password_hash($new,PASSWORD_BCRYPT,$option);
	print_r($newPassword);
	die();
}

if ($action == 'serial_diagnose') {
	$tData['serialno'] = $_GET['serialno'];
	$data['content'] = loadTemplate('serialdiagnose_edit.tpl.php',$tData);
}

if ($action == 'serial_diagnose_process') {
	$serialno = $_POST['serialnumber'];
	$serialDetails = $Serials->diagnoseSn($serialno);
	if ($serialDetails) {
		$tData['serial'] = $serialDetails;
		$tData['diagnose'] = true;
		$action = 'serial_add';
	}else{
		$_SESSION['error'] = "Serial number not found in the system";
		redirect('serials','serial_diagnose','serialno='.$serialno);
	}
}

if ( $action == 'serial_edit') {
	$id = $_GET['id'];
	$tData['name'] = $_GET['name'];
	$tData['serial'] = $Serials->getSerialDetails($id);
	$tData['edit'] = 1;
	$action = 'serial_add';
}

if ( $action == 'serial_add') {
	$serialId = $_GET['id'];
	$ticketid = $_GET['ticketid'];
	if ($ticketid) {
		$ticket = $Tickets->getDetails($ticketid);
		$tData['serial']['name'] = $ticket['serialno'];
		$tData['serial']['prodid'] = $ticket['prodid'];
		$tData['serial']['deptid'] = $ticket['deptid'];
		$tData['serial']['clientid'] = $ticket['clientid'];
	}
	$tData['prods'] = $Products->find(array('status'=>'active'),$sortby = "name");
	$tData['depts'] = $Departments->find(array('status'=>'active'));
	$tData['locs'] = $Branches->find(array('status'=>'active'));
	$tData['clients'] = $Clients->find(array('status'=>'active'),$sortby ="name");

	if(empty($tData['diagnose'])){
		$tData['serial'] = $Serials->getSerialDetails($serialId);
	}
	$data['content'] = loadTemplate('serial_edit.tpl.php',$tData);
}

if ($action == 'replace_serial') {
	$data['content'] = loadTemplate('replaceserial_edit.tpl.php',$tData);
}

if ($action == 'save_replacement') {
	$serialname = $_POST['serial'];

	$findOld = $Serials->find(array('status'=>'active','name'=>$serialname['oldname']));

	$NewSerial = array(
		'name' => $serialname['newserial'],
	 	'status'=>$serialname['status'],
	 	'clientid'=>$findOld[0]['clientid'],
	 	'prodid'=>$findOld[0]['prodid'],
	 	'deptid'=>$findOld[0]['deptid'],
	 	'locid'=>$findOld[0]['locid'],
	 	'warrantydatefrom'=>$findOld[0]['warrantydatefrom'],
	 	'warrantydateto'=>$findOld[0]['warrantydateto'],
	 	'amcfrom'=>$findOld[0]['amcfrom'],
	 	'amcto'=>$findOld[0]['amcto'],
	 	'invoiceno'=>$findOld[0]['invoiceno'],
	 	'isfiscal'=>0,
	 	'contactperson'=>$findOld[0]['contactperson'],
		'isreplaced'=>1,
		'replacedfrom'=>$serialname['oldname'],
		'doc'=>TIMESTAMP,
		'createdby'=>$_SESSION['member']['id']
	);

	if ($findOld) {
			$Serials->Insert($NewSerial);
			$Serials->update($findOld[0]['id'],array('status'=>'inactive'));
			$_SESSION['message'] = 'Your serial is replaced from '.$findOld[0]['name'].' to'. $serialname['newserial'].' successfully';

	}else{
		$_SESSION['error'] = 'Old serial does not exists';
	}
	//redirect('serials','replace_serial');
	redirect('serials','serial_index');
}

if ( $action == 'serial_save') {
	$returnpoint = $_GET['current'];
	$id = intval($_POST['id']);
	$serial = $_POST['serial'];

	$amcInput = explode('/', $serial['amcfrom']);
	$serial['amcfrom'] = $amcInput[2].'-'.$amcInput[1].'-'.$amcInput[0];

	$amcInput = explode('/', $serial['amcto']);
	$serial['amcto'] = $amcInput[2].'-'.$amcInput[1].'-'.$amcInput[0];

	$dateInput = explode('/',$serial['warrantydatefrom']);
	$serial['warrantydatefrom'] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];

	$dateInput = explode('/',$serial['warrantydateto']);
	$serial['warrantydateto'] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];

	if ( empty($id) )  {
		$serial['doc'] = TIMESTAMP;
		$serial['createdby'] = $_SESSION['member']['id'];
		$serialVerify = $Serials->find(array('name' =>$serial['name']));

		if ($serialVerify) {
			// code...
			$_SESSION['error'] = 'Serial is used';
			$serialnumber = $serial['name'];
		}else{

			$Serials->Insert($serial);
			$serialnumber = $serial['name'];
			$serialId = $Serials->lastId();

			$_SESSION['message'] = 'Serial Added';
		}

		if ($returnpoint == 'nonefd_serial') {
			redirect('tickets','direct_assign','checker=nonEfd_support&serialid='.$serialId.'&clientid='.$serial['clientid'].'&productid='.$serial['prodid']);
		}else{
			redirect('serials','serial_add','id='.$serialId.'&serial='.$serialnumber);
		}

	}else {
		$serial['modifiedby'] = $_SESSION['member']['id'];
		$serial['dom'] = TIMESTAMP;
		$Serials->update($id,$serial);
		$serialId = $id;

		$_SESSION['message'] = 'Serial Updated';
		redirect('serials','serial_edit','id='.$id);
	}

}

if ( $action == 'serial_delete') {
	$id = intval($_GET['id']);
	$data = array('deletedby' => $_SESSION['member']['id'],'deledate'=>TIMESTAMP);
	$Serials->delete($id);
	$Serials->update($id,$data);
	redirect('serials','serial_index');
}

if ($action == 'fiscal_edit') {
	$user = $_SESSION['member'];
	$serialId = $_GET['id'];
	$departId = $_SESSION['member']['deptid'];


	$serialdet = $Serials->get($serialId);
	$tData['usersid'] = $serialdet['fiscalrequestby'];

	if($user['role'] == 'Admin'){
		$tData['users'] = $Users->find(array('status'=>'active'));
	}else{
		$tData['users'] = $Users->find(array('status'=>'active','deptid'=>$user['deptid']));
	}

	$tData['serialId'] = $serialId;
	$data['content'] = loadTemplate('fiscal_assign.tpl.php',$tData);
}

if ($action == 'assignfiscal') {
	$serialno = $_GET['id'];
	$user = $_SESSION['member'];
	$tData['serialId'] = $serialno;

	if($user['role'] == Admin){
		$technicians = $Users->find(array('status'=>'active'));
	}else{
		$technicians = $Users->find(array('status'=>'active','deptid'=>$user['deptid']));
	}
	$tData['users'] = $technicians;
	$data['content'] = loadTemplate('assignfiscal_edit.tpl.php',$tData);
}

if ($action == 'saveAssign_fiscal') {
	$loginUser = $_SESSION['member'];
	$fiscal = $_POST['fiscal'];
	$fiscaldata['isfiscalrequested'] = 1;
	$fiscaldata['fiscalrequestby'] = $fiscal['fiscalrequestby'];
	$fiscaldata['fiscalrequestverify'] = 1;
	$fiscaldata['fiscalrequestverifyby'] = $loginUser['id'];

	if ($loginUser['head'] || $loginUser['roleid'] == 1) {
		$Serials->update($fiscal['serialId'],$fiscaldata);
		$_SESSION['message'] = 'Fiscal Assigned successfully';
	}else{
		$_SESSION['error'] = 'Not sent, Contact HOD';
	}
	redirect('serials','serial_index');
}

if ($action == 'fiscal_reassign_save') {
		$serial = $_POST['fiscal'];
		$hodDetails = $_SESSION['member'];

		if ($hodDetails['deptid']) {
				$department = $Departments->find(array('id'=>$hodDetails['deptid'],'status'=>'active'));

				if ($department[0]['name'] == 'EFD') {

					if ($hodDetails['head']) {

							$Technician = $Users->find(array('id'=>$serial['assignedto'],'status'=>'active'));
							$Serialdetails = $Serials->find(array('id'=>$serial['serialId'],'status'=>'active'));

							if ($Technician[0]['deptid'] == $department[0]['id'] ) {

									$fiscaldata = array(
										'fiscalrequestverify' => 1,
										'fiscalrequestby'=>$Technician[0]['id']
									);
									//Validating for makesure the device is requested for fiscal and did not fiscal
									if ($Serialdetails[0]['isfiscalrequested'] == 1 && $Serialdetails[0]['isfiscal'] == 0) {
										$Serials->update($serial['serialId'],$fiscaldata);
										$_SESSION['message'] = 'Fiscal is re-assign successfully';
									}
							}else{$_SESSION['error'] = 'Choose another technician';}
					}
				}else{$_SESSION['error'] = "Contact with HOD with ".$department[0]['name']." department";}
		}else{$_SESSION['error'] = 'Not Allowed for this service';}
		//die();
		redirect('serials','fiscal_edit');
}

if ($action == 'fiscalizing') {
	$user = $_SESSION['member'];
	$serialid = $_GET['id'];
	$techid = $user['id'];
	//search serial number for fiscalizing
	$serialdata = $Serials->find(array(
			'fiscalrequestby'=>$techid,
			'fiscalrequestverify'=>1,
			'isfiscal'=>0,
			'id'=>$serialid
		));

	if ($serialdata) {
			$fiscalizing = array('isfiscal' => 1,'fiscalby' =>$techid,'fiscaldate' =>TIMESTAMP);
			//makesure the replaced serials does not change the warranty date, it will remain the same (old serial)
			if ($serialdata[0]['isreplaced'] == 1) {
				$fiscalizing['warrantydateto'] = $serialdata[0]['warrantydateto'];
			}else{
				$fiscalizing['warrantydateto'] = date('Y-m-d',strtotime('1 year'));
			}
			$Serials->update($serialid,$fiscalizing);
			$_SESSION['message'] = 'Product with serial No '.$serialdata[0]['name'].' is fiscalized successfully';
	}else{
		$_SESSION['error'] = 'Serial number not sent to you or not Verified';
	}
	redirect('serials','myassigned_fiscal');
}

if ($action == 'ajax_getSerialNumbers') {
		$productid = $_GET['productid'];
		$clientid = $_GET['clientid'];
		$response = array();
		$obj=null;

		$serialnumber = $Serials->find(
			array(
				'status'=>'active','prodid' =>$productid,
				'clientid' =>$clientid)
			);
			$departInfo = $Products->productDepartment($productid);
			$clientDetails = $Clients->find(array('status'=>'active','id'=>$clientid));
			//check the serial number
		if ($serialnumber) {
				$obj->serialdata = $serialnumber;
				$obj->departdata = $departInfo;
				$obj->clientName = $clientDetails;
				$obj->status = "found";
			}else{
				$obj->status = "no";
		}
		$response[] =$obj;
		$data['content'] = $response;
}

if ( $action == 'ajax_getAllSerials' ) {
		$icData = $Serials->search($_GET['search']['term']);
	//	$locId = $_GET['locId'];
		$response = array();
		if ($icData) {
			foreach ((array)$icData as $ic) {
				$obj=null;
				$obj->text=$ic['name'];
				$obj->id=$ic['id'];
				$response['results'][]=$obj;
			}
		}else {
			$obj=null;
			$obj->test='No results';
			$obj->id=0;
			$response['results'][]=$obj;
		}
		$data['content']=$response;
}

if ($action == 'ajax_generateSerial') {
		$productid = $_GET['productid'];
		$clientid = $_GET['clientid'];
		$clientname = $Clients->find(array('id'=>$clientid,'status'=>'active'));
		$productname = $Products->find(array('id'=>$productid,'status'=>'active'));
		#2. get first name for client and product
		//$clientarrayName = explode(' ',$clientname[0]['name']);// for now it changed from name to id coz serial no it will be long
		$receivedClientid = $clientname[0]['id'];
		$productarrayName = explode(' ',$productname[0]['name']);
		#3. generating unique value -> increment value, i suggest to use timestamp coz it change every time
		$fullDate = explode(" ",TIMESTAMP);
		$onlyTime = explode(':',$fullDate[1]);
		//get Unique value or increment value
		$uniquevalue = $onlyTime[0].$onlyTime[1].$onlyTime[2];
		#4. suggest the serial number by combine client name, increment value, product name and product id
		//suggested serial number
		$suggestedSerialNumber = $receivedClientid."/".$uniquevalue."/".$productarrayName[0]."/".$productid;
		#5. confirm the suggested serial number if it's exisit or not.
		$confirmSuggestedSerialno = $Serials->find(array('name'=>$suggestedSerialNumber));
		$response = array();
		$obj=null;
		if ($confirmSuggestedSerialno) {
				$obj->status = "used";
			}else{
				$obj->generatedserial = $suggestedSerialNumber;
				$obj->clientid = $clientid;
				$obj->productid = $productid;
				$obj->status = "new";
		}
		$response[] =$obj;
		$data['content'] = $response;
}

if ($action == 'ajax_getSerialDetails') {
	$checkingSerialNumber = $_GET['serialnumber'];
	$serialnumber = $Serials->getSerialNumber($checkingSerialNumber);
	$contactPerson = $Tickets->getLastContactPerson($checkingSerialNumber);

	$response = array();
	$obj=null;
	if ($serialnumber) {
			$clientData = $Clients->get($serialnumber['clientid']);

			if ($serialnumber['warrantydateto'] >= TODAY){
				$serialservicetype = 'warranty';
				$warrantyId = $Warranties->getWarrantyId($serialnumber['name'],$serialnumber['warrantydateto']);
			}else{
					//check for AMC
				if($serialnumber['amcto'] >= TODAY){
					$amcId = $Amcserials->getAMCId($serialnumber['name'],$serialnumber['amcto']);
					if(empty($amcId)){
						$serialservicetype = 'chargeable';
					}else{
						$serialservicetype = 'amc';
					}

				}else{
					//client need to pay for service
					$warrantyId['id'] = 0;
					$serialservicetype = 'chargeable';
				}
			}

			$obj->deptid = $serialnumber['deptid'];
			$obj->departname = $serialnumber['departname'];
			$obj->prodid = $serialnumber['prodid'];
			$obj->productname = $serialnumber['productname'];
			$obj->amcid = $amcId['id'];
			$obj->warrantyId = $warrantyId['id'];
			//$obj->amc = $serialnumber['amc'];
			$obj->clientid = $serialnumber['clientid'];
			$obj->clientname = $serialnumber['clientname'];
			$obj->serialid = $serialnumber['id'];
			$obj->serialname = $serialnumber['name'];
			$obj->branchid = $serialnumber['locid'];
			$obj->plocation = $serialnumber['ploc'];
			$obj->warrantydateto = $serialnumber['warrantydateto'];
			$obj->amcdateto = $serialnumber['amcto'];
			$obj->client = $clientData['name'];
			//$obj->warrantydateto = formatD($serialnumber['warrantydateto']);
			$obj->servicestatus = $serialservicetype;
			$obj->contactname = $contactPerson['contactname'];
			$obj->contactmobile = $contactPerson['contactmobile'];
			$obj->status = 'found';
	}else{
		$obj->status = 'no';
	}

	$response[]=$obj;
	$data['content']=$response;
}
