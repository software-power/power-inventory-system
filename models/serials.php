<?php

	class Serials extends model{
		var $table = "serials";

		function getClientSerials($clientid,$userdept="") {
			$sql = "select s.*,p.name as productname,d.name as departname,
			u.name as createdbyname from serials as s
			inner join departments as d on d.id = s.deptid
			inner join products as p on p.id = s.prodid
			inner join users as u on u.id = s.createdby
			where s.status = 'active'";
			if($clientid) $sql.=" and clientid = $clientid";
			if($userdept) $sql.=" and deptid = $userdept";
			//echo $sql;die();
			return fetchRows($sql);
		}

		function getSerialDetails($id){
			$sql = 'select s.*,c.name as clientname, d.name as departname,p.name as productname from serials as s
			inner join departments as d on d.id = s.deptid
			inner join clients as c on c.id = s.clientid
			inner join products as p on p.id = s.prodid where s.id = '.$id;
			return fetchRow($sql);
		}

		function getSerialNumber($serialno){
			$sql = "select s.*,c.name as clientname, d.name as departname,p.name as productname
			from serials as s
			inner join departments as d on d.id = s.deptid
			inner join clients as c on c.id = s.clientid
			inner join products as p on p.id = s.prodid where s.status = 'active' and s.name = '".$serialno."' ";
			return fetchRow($sql);
		}

		function search($name){
			$sql = "select name, id from serials where status = 'active' and name like '%".$name."%'";
			return fetchRows($sql);
		}

		function diagnoseSn($serialno){
			$sql = "select s.*,c.name as clientname, d.name as departname,p.name as productname
			from serials as s
			left join departments as d on d.id = s.deptid
			left join clients as c on c.id = s.clientid
			left join products as p on p.id = s.prodid
			where s.name = '".$serialno."' ";
			//echo $sql;die();
			return fetchRow($sql);
		}

		function searchResults($name="",$brach,$department="",$orderby="",$limit="") {
			$sql = "select s.*,u.name as technician,us.name as createdbyname from serials as s
			left join users as us on us.id = s.createdby
			left join users as u on u.id = s.fiscalrequestby
			where s.status = 'active'";
			// serialno
			if($name) $sql .=" and s.name like '%".$name."%'";
			if($brach)$sql .=" and s.locid = ".$brach;
			if($department)$sql .=" and s.deptid = ".$department;
			if($orderby) $sql .=" order by s.".$orderby." desc";
			if($limit) $sql .=" limit ".$limit;
			//echo $sql;die();
			return fetchRows($sql);
		}

		function myAssignedFiscale($user="",$serialno="",$fstatus="",$clientid="",$fromdate="",$todate=""){
			$sql = "select s.id as serialid,s.name,s.doc,s.warrantydatefrom,
			s.warrantydateto,s.isfiscal,s.fiscalby,s.fiscaldate,s.status,
			c.name as clientname from serials as s
			inner join clients as c on c.id = s.clientid
			inner join users as u on u.id = s.fiscalrequestby where 1=1";

			if($user) $sql.=" and s.fiscalrequestby = $user";
			if($serialno) $sql.=" and s.name = '$serialno'";
			if($fstatus == 'yes') {
				$sql.=" and s.isfiscal = 1";
			}else if($fstatus == 'not'){
				$sql.=" and s.isfiscal = 0";
			}
			if($clientid) $sql.=" and c.id = $clientid";
			//if ($fromdate) $sql .=" and s.doc >= '".$fromdate."'";
			//if ($todate) $sql .=" and s.doc <= '".$todate."'";
			$sql .=" order by doc desc";
			//echo $sql;die();
			return fetchRows($sql);
		}

		function getFiscalizedlist($user="",$serialno="",$depart="",$branch="",$clientid="",$fromdate="",$todate=""){
			$sql = "select s.id as serialid,s.name,s.doc,s.fiscaldate,s.warrantydatefrom,
			s.warrantydateto,s.isfiscal,s.fiscalby,s.fiscaldate,s.status,
			c.name as clientname,u.name as fiscalizeby from serials as s
			inner join clients as c on c.id = s.clientid
			inner join users as u on u.id = s.fiscalby where 1=1 and s.isfiscal = 1 ";

			if($user) $sql.=" and s.fiscalby = $user";
			if($serialno) $sql.=" and s.name = '$serialno'";
			if($depart) $sql.=" and s.deptid = '$depart'";
			if($branch) $sql.=" and s.locid = '$branch'";
			if($clientid) $sql.=" and c.id = $clientid";
			if ($fromdate) $sql .=" and s.doc >= '".$fromdate."'";
			if ($todate) $sql .=" and s.doc <= '".$todate."'";
			$sql .=" order by doc desc";
			//echo $sql;die();
			return fetchRows($sql);
		}

}

?>
