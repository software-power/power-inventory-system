<?php
if($action == 'edit_amc'){
	$tData['backto'] = $_SERVER['HTTP_REFERER'];//get back to where you from
	$action = 'add_amc';
	$tData['edit'] = true;
}

if ($action == 'add_amc') {
	$tData['backto'] = $_SERVER['HTTP_REFERER'];//get back to where you from
	$amcnumber = $_GET['amcnumber'];
	$tData['amcdetails'] = $Amcserials->getAMCdetails($amcnumber);
	$_SESSION['pagetitle'] = CS_COMPANY." - Add AMC";
	$data['content'] = loadTemplate('amc_edit.tpl.php',$tData);
}

if ($action == 'save_amc') {
		$id = $_POST['id'];
		$serialid = $_POST['serialid'];
		$amc = $_POST['amc'];
		//changing the date format for inserting into database
		$amcInput = explode('/', $amc['amcfrom']);
		$amc['amcfrom'] = $amcInput[2].'-'.$amcInput[1].'-'.$amcInput[0];

		$amcInput = explode('/', $amc['amcto']);
		$amc['amcto'] = $amcInput[2].'-'.$amcInput[1].'-'.$amcInput[0];
		/************END***************/
		$amc['doc'] = TIMESTAMP;
		$amc['createdby'] = $_SESSION['member']['id'];

		$serialdetails = array(
			'amcfrom' => $amc['amcfrom'],
			'amcto' => $amc['amcto']);

		if (empty($id)) {
			//new AMC
			$serialnumber = $Serials->find(array('status'=>'active','name'=>$amc['name']));
			$amc['serialid'] = $serialnumber[0]['id'];

			$Amcserials->Insert($amc);
			$amcId = $Amcserials->lastId();
			$Serials->update($amc['serialid'],$serialdetails);
			redirect('serials','add_amc','amcno='.$amcId);
		}else{
			//edit
			$Amcserials->update($id,$amc);
			$Serials->update($serialid,$serialdetails);
			$_SESSION['message'] = 'AMC edit successfully';
			redirect('amcs','edit_amc','amcnumber='.$id);
		}

}

if ($action == 'print_amc') {
		$amcid = $_GET['amcno'];
		//find the amc details
		if($amcid){
			$amcdetails = $Amcserials->getClientAMC($amcid);
			if ($amcdetails) {
				//changing the date format for inserting into database
				$amcInput = explode('-', $amcdetails['amcstart']);
				$amcdetails['amcstart'] = $amcInput[2].'/'.$amcInput[1].'/'.$amcInput[0];

				$amcInput = explode('-', $amcdetails['amcend']);
				$amcdetails['amcend'] = $amcInput[2].'/'.$amcInput[1].'/'.$amcInput[0];
				/************END***************/
				//print_r($amcdetails);
				$tData['amcnumber'] = $amcdetails['amcnumber'];
				$tData['serialid'] = $amcdetails['serialid'];
				$tData['serialname'] = $amcdetails['serialname'];
				$tData['amcstart'] = $amcdetails['amcstart'];
				$tData['amcend'] = $amcdetails['amcend'];
				$tData['clientname'] = $amcdetails['clientname'];
				$tData['amount'] = $amcdetails['amount'];
				//die();
				$data['layout'] = 'layout_blank.tpl.php';
				$data['content'] = loadTemplate('amc_doc.tpl.php',$tData);
			}else{
				echo "No AMC found, please contact powercomputers"; die();
			}
		}else{echo "No AMC number";die();}
}

if ($action == 'amc_list') {
	$user = $_SESSION['member'];

	if($user['role'] == 'Admin'){
		$getList = $Amcserials->getClientAMCs();
	}else{
		$getList = $Amcserials->getClientAMCs($user['deptid'],$clientId,$serialno,$invoiceno,$fromdate,$todate);
	}

	$tData['amclist'] = $getList;
	$_SESSION['pagetitle'] = CS_COMPANY." - AMC List";
	$data['content'] = loadTemplate('amc_list.tpl.php',$tData);
}

if ($action == 'amc_services') {
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
		$getList = $Amcserials->getClientAMCreport($depart,$clientid,$serialno,$invoiceno,$fromdate,$todate);
	}else{
		$getList = $Amcserials->getClientAMCreport($user['deptid'],$clientid,$serialno,$invoiceno,$fromdate,$todate);
	}

  $tData['braches'] = $Branches->find(array('status'=>'active'));
	$tData['amclist'] = $getList;
	$_SESSION['pagetitle'] = CS_COMPANY." - AMC Services";
	$data['content'] = loadTemplate('amcservices_list.tpl.php',$tData);
}

if ($action == 'amc_reports') {
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
		$getList = $Amcserials->getClientAMCreport($depart,$clientid,$serialno,$invoiceno,$fromdate,$todate);
	}else{
		$getList = $Amcserials->getClientAMCreport($user['deptid'],$clientid,$serialno,$invoiceno,$fromdate,$todate);
	}

  $tData['braches'] = $Branches->find(array('status'=>'active'));
	$tData['amclist'] = $getList;
	$_SESSION['pagetitle'] = CS_COMPANY." - AMC Reports";
	$data['content'] = loadTemplate('amc_report.tpl.php',$tData);
}

if ( $action == 'ajax_getAMCTickets' ) {
		$serialId = $_GET['serialId'];
		$amcid = $_GET['amcid'];
		$icData = $Tickets->getAMCTickets($serialId,$amcid);
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
