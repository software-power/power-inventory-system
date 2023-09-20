<?php

	class Tickets extends model
	{
		var $table = "tickets";

		function search($serialno="",$assignedto="") {
					$sql = "select t.*, t.client as client, b.name as branch, d.name as department, p.name as product, c.name as real_client, s.name as status,  s.color from tickets as t
					inner join branches as b on b.id = t.branchid
					inner join departments as d on d.id = t.deptid
					inner join products as p on p.id = t.prodid
					inner join statuses as s on s.id = t.statusid
					left join clients as c on c.id = t.clientid
					left join serials as r on r.id = t.serialid
					where 1=1 ";

				if ($serialno) $sql .= " and t.serialno = '$serialno' ";
				if ($assignedto) $sql .= " and t.assignedto = $assignedto ";

				$sql .= " order by t.doc desc";
				// echo $sql;
				return fetchRows($sql);
		}
		//ACTIONS
		//assigned_ticket
		//unassigned_ticket
		//Was -> getUnassignedTickets
		//Now -> getGeneralTickets
		function getGeneralTickets($serialno="",$deptid="",$branchid="",$tickeAction="", $from="") {
			$sql = "select t.*, t.client as client,u.name as assignedperson,
			b.name as branch, d.name as department,
			p.name as product, c.name as real_client from tickets as t
			inner join branches as b on b.id = t.branchid
			inner join departments as d on d.id = t.deptid
			inner join products as p on p.id = t.prodid
			left join clients as c on c.id = t.clientid
			left join users as u on u.id = t.assignedto
			where t.statusid <> 3 ";

			if ($serialno) $sql .= " and t.serialno = '$serialno' ";
			if ($deptid) $sql .= " and t.deptid = '$deptid' ";
			if ($branchid) $sql .= " and t.branchid = '$branchid' ";
			//checking the ticket action for filtering ticket in terms of assigned or unassigned
			if ($tickeAction == "assigned_ticket"){
				$sql .= " and t.assignedto > 0";
			}else if ($tickeAction == "unassigned_ticket") {
				$sql .= " and t.assignedto is null or t.assignedto = 0";
			};

			if($from == 'online') $sql .=" and t.raisedfrom = 'web'"; else $sql .=" and (t.raisedfrom = 'direct' || t.raisedfrom = 'mobile') ";

			$sql .= " order by t.doc desc";
			 //echo $sql;die();
			return fetchRows($sql);
		}

		function getDetails($id="") {
			$sql = "select t.*,r.id as serialid, c.id as cid,
			b.name as branch, d.name as department, p.name as product, s.name as status, c.name as real_client,
			su.name as supporttypename from tickets as t
			inner join branches as b on b.id = t.branchid
			inner join departments as d on d.id = t.deptid
			inner join products as p on p.id = t.prodid
			inner join statuses as s on s.id = t.statusid
			inner join supporttype as su on su.id = t.supporttype
			left join clients as c on c.id = t.clientid
			left join serials as r on r.id = t.serialid
			where 1=1 ";
			//as supporttypename
			if ($id) $sql .= " and t.id = $id ";
			// echo $sql;
			return fetchRow($sql);
}

		function getCompleteTicket($ticketid="",$serialno="",$clientid="",$brachid="",$deptid="",$verifyStatus="",$fromdate="",$todate=""){
				$sql = "select t.*,t.timespent as time,t.invoiceno as invoicenumber,
				t.amount as invoiceamount,t.isverified as verification,
				r.id as serialid, c.id as cid, b.name as branch,
				d.name as department, p.name as product, s.name as status,c.email as clientemail ,c.name as clientname, u.name as technician
				from tickets as t
				inner join branches as b on b.id = t.branchid
				inner join departments as d on d.id = t.deptid
				inner join products as p on p.id = t.prodid
				inner join statuses as s on s.id = t.statusid
				inner join users as u on u.id = t.	assignedto
				left join clients as c on c.id = t.clientid
				left join serials as r on r.id = t.serialid
				where 1=1 and t.statusid = 3";

				if ($ticketid) $sql .= " and t.id = '$ticketid' ";
				if ($serialno) $sql .= " and t.serialno = '$serialno' ";
				if ($clientid) $sql .= " and t.clientid = '$clientid' ";
				if ($brachid) $sql .=" and t.branchid = $brachid ";
				if ($deptid) $sql .=" and t.deptid = $deptid ";

				if ($verifyStatus == 'yes'){
					$sql .=" and t.isverified = 1 ";
				}else if($verifyStatus == 'not'){
					$sql .=" and t.isverified = 0 ";
				}else{
					$sql .=" and t.isverified = 0 ";
				}
				//if ($fromdate) $sql .=" and t.doc >= '".$fromdate."'";
				//if ($todate) $sql .=" and t.doc <= '".$todate."'";
				$sql .= " order by t.doclose desc";
				//echo $sql;die();
				return fetchRows($sql);
		}

		function getJobCard($ticketid){
			$sql = "select t.id as jobid,t.type as prostatus,t.raisedfrom,t.contactname,t.contactmobile,t.ploc as physicalloc,
			t.dov as verifydate,t.doc as createdate,t.supportedwith, serialno, client,c.name as clientname,
			b.id as branchid,b.name as branchname, p.name as productname, d.name as deptname,
			st.name as statusname,st.color as statuscolor,
			u.name as assigname,us.name as assignby,assignedon,clientremark, sut.name as supporttype,
			c.id as clientid,c.tinno as tinnumber,c.mobile as mobilenumber, c.address as clientaddress,
			c.email as clientemail,remark, type, invoiceno, amount from tickets as t
			inner join clients as c on c.id = t.clientid
			inner join branches as b on b.id = t.branchid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join supporttype as sut on sut.id = t.supporttype
			left join users as u on u.id = t.assignedto
			left join users as us on us.id = t.assignedby
			inner join statuses as st on st.id = t.statusid where t.id = $ticketid";

			// if ($statusid) $sql .=" and t.statusid = $statusid ";
			// if ($fromdate) $sql .=" and t.doc >= '".$fromdate."'";
			// if ($todate) $sql .=" and t.doc <= '".$todate."'";

				//echo $sql;die();
				//clients//assignedto//todate will today // fromdate will 1 month ago
				return fetchRow($sql);
		}

		function getTicketReport($ticketid){

			$sql = "select t.id,t.timespent,t.isverified,t.spareamount,t.invoiceno,t.amount as invoiceamount,t.sparepart,t.type as prostatus,t.raisedfrom,t.contactname,t.contactmobile, serialno, client,c.name as clientname,b.name as branchname, p.name as productname, d.name as deptname,
			st.name as statusname,st.color as statuscolor,
			u.name as assigname,us.name as assignby,assignedon,clientremark, sut.name as supporttype,
			c.tinno as tinnumber,c.mobile as mobilenumber, c.address as clientaddress,
			c.email as clientemail,remark, type, invoiceno, amount from tickets as t
			inner join clients as c on c.id = t.clientid
			inner join branches as b on b.id = t.branchid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join supporttype as sut on sut.id = t.supporttype
			inner join users as u on u.id = t.assignedto
			inner join users as us on us.id = t.assignedby
			inner join statuses as st on st.id = t.statusid where t.id = $ticketid";

			// if ($statusid) $sql .=" and t.statusid = $statusid ";
			// if ($fromdate) $sql .=" and t.doc >= '".$fromdate."'";
			// if ($todate) $sql .=" and t.doc <= '".$todate."'";

				//echo $sql;
				//die();
				//clients//assignedto//todate will today // fromdate will 1 month ago
				return fetchRow($sql);
		}
		function getTicketReportsAdmin($department="",$branch = "",$statusid=""){

			$sql = "select t.id, serialno, client,c.name as clientname,b.name as branchname, p.name as productname, d.name as deptname, st.name as statusname,
			 u.name as assigname,us.name as assignby, su.name as supportype ,t.assignedon, t.clientremark, t.remark, t.type, t.invoiceno, t.amount,t.sparepart,t.spareamount,t.timespent
			 from tickets as t
			 left join clients as c on c.id = t.clientid
			 inner join branches as b on b.id = t.branchid
			 inner join products as p on p.id = t.prodid
			 inner join departments as d on d.id = t.deptid
			 left join users as u on u.id = t.assignedto
			 left join users as us on us.id = t.assignedby
			 left join supporttype as su on su.id = t.supporttype
			 inner join statuses as st on st.id = t.statusid
			 where 1=1 and t.isverified = 0";

			//filtering the data according the needs
			if($department) $sql .=" and t.deptid = $department";
			//if($branch) $sql .=" and t.branchid = $branch";
			if ($statusid) $sql .=" and t.statusid = $statusid ";
			//order the
			$sql .=" order by t.id desc";
				//echo $sql;die();
				return fetchRows($sql);
		}

		function getTicketReports(
			$department="",
			$branch = "",$statusid="",$clientid="",
			$userid="",$serialno="",$ticketid="",
			$supportype="",$amc="",$invoice="",
			$warranty="",$fromdate="",$todate=""){

			$sql = "select t.id, serialno, client,c.name as clientname,b.name as branchname,
			p.name as productname, d.name as deptname, st.name as statusname,
			u.name as assigname,us.name as assignby, su.name as supportype ,
			t.assignedon, t.clientremark, t.remark, t.type,
			t.isverified,t.invoiceno, t.amount,t.sparepart,t.doc,
			t.spareamount,t.timespent,t.warrantydateto,t.amcdateto from tickets as t
			inner join clients as c on c.id = t.clientid
			inner join branches as b on b.id = t.branchid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join users as u on u.id = t.assignedto
			inner join users as us on us.id = t.assignedby
			inner join supporttype as su on su.id = t.supporttype
			inner join statuses as st on st.id = t.statusid where 1=1";

			//filtering the data according the needs
			if($department) $sql .=" and t.deptid = $department";
			if($branch) $sql .=" and t.branchid = $branch";
			if($statusid) $sql .=" and t.statusid = $statusid ";
			if($clientid) $sql .=" and t.clientid = $clientid";
			if($userid) $sql .=" and t.assignedto = $userid";
			if($serialno) $sql .=" and t.serialno = '".$serialno."'";
			if($ticketid) $sql .=" and t.id = '".$ticketid."'";
			if($supportype) $sql .=" and t.supporttype = $supportype";
			/**admin**/
			//for AMC
			if($amc){
				if($amc == 'yes'){
					$sql .=" and t.amcdateto >= t.doc ";
				}else if($amc == 'no'){
					$sql .=" and t.amcdateto is null ";
				}else if($amc == 'expired'){
					$sql .=" and t.amcdateto < t.doc ";
				}
			}
			//for warranty
			if($warranty){
				//$sql .=" and warranty = $warranty ";
				if($warranty == 'yes'){
					$sql .=" and t.warrantydateto >= t.doc ";
				}else if($warranty == 'no'){
					$sql .=" and t.warrantydateto is null ";
				}else if($warranty == 'expired'){
					$sql .=" and t.warrantydateto < t.doc ";
				}

			}
			//for invoice number
			if($invoice){
				if($invoice == 'yes'){
					$sql .=" and t.invoiceno <> '' ";
				}else if($invoice == 'no'){
					$sql .=" and t.invoiceno = '' ";
				}
			}
			/**admin**/
			if($fromdate) $sql .=" and t.doc >= '".$fromdate."'";
			if($todate) $sql .=" and t.doc <= '".$todate." 23:59'";
			//order the
			$sql .=" order by t.id desc";
			//echo $sql;die();
			return fetchRows($sql);
		}

		function getAMCTickets($serialId,$amcid){

			$sql = "select t.id, serialno, client,c.name as clientname,b.name as branchname, p.name as productname,
			d.name as deptname, st.name as statusname,
			u.name as assigname,us.name as assignby, su.name as supportype ,
			t.assignedon, t.clientremark, t.remark, t.type,t.amcid,
			t.invoiceno, t.amount,t.sparepart,t.spareamount,t.timespent,t.warrantydateto,t.amcdateto from tickets as t
			inner join clients as c on c.id = t.clientid
			inner join branches as b on b.id = t.branchid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join users as u on u.id = t.assignedto
			inner join users as us on us.id = t.assignedby
			inner join supporttype as su on su.id = t.supporttype
			inner join statuses as st on st.id = t.statusid where 1=1 ";

			if($serialId) $sql.=" and t.serialid = '$serialId'";
			if($amcid) $sql.=" and t.amcid = '$amcid'";

			$sql .=" order by t.id desc";
			//echo $sql;die();
			return fetchRows($sql);
		}

		function getWarrantyTickets($serialId,$warrantyid){

			$sql = "select t.id, serialno, client,c.name as clientname,b.name as branchname, p.name as productname,
			d.name as deptname, st.name as statusname,
			u.name as assigname,us.name as assignby, su.name as supportype ,
			t.assignedon, t.clientremark, t.remark, t.type,t.amcid,
			t.invoiceno, t.amount,t.sparepart,t.spareamount,
			t.timespent,t.warrantydateto,t.amcdateto from tickets as t
			inner join clients as c on c.id = t.clientid
			inner join branches as b on b.id = t.branchid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join users as u on u.id = t.assignedto
			inner join users as us on us.id = t.assignedby
			inner join supporttype as su on su.id = t.supporttype
			inner join statuses as st on st.id = t.statusid where 1=1 ";

			if($serialId) $sql.=" and t.serialid = '$serialId'";
			if($warrantyid) $sql.=" and t.warrantyid	 = '$warrantyid'";

			$sql .=" order by t.id desc";
			//echo $sql;die();
			return fetchRows($sql);
		}

		//statics base
		//Weekly
		/*

		* @param string $userid
		* @param string $deptid
		*/
		function ticketStatics($userid="",$brachid="",$deptid="",$status="",$isverified="",$fromdate="",$todate=""){
			//$sql = "select count(*) from tickets as t where 1=1";
			$sql = "select count(*) from tickets as t
			inner join clients as c on c.id = t.clientid
			inner join branches as b on b.id = t.branchid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join users as u on u.id = t.assignedto
			inner join users as us on us.id = t.assignedby
			inner join supporttype as su on su.id = t.supporttype
			inner join statuses as st on st.id = t.statusid where 1=1";
			//select count(*) from tickets WHERE deptid = 1 and statusid = 3 and isverified = 0

			if($userid) $sql .=" and t.assignedto = $userid";
			if($brachid) $sql .=" and t.branchid = $brachid";
			if($deptid) $sql .=" and t.deptid = $deptid";
			if($status) $sql .=" and t.statusid = $status";

						if($isverified === 0) {

							if($isverified === "")$sql .=""; else $sql .=" and t.isverified = ".$isverified;

						}else if($isverified === 1) $sql .=" and t.isverified = ".$isverified;

			if($fromdate) $sql .=" and t.assignedon >= '".$fromdate."'";
			if($todate) $sql .=" and t.assignedon <= '".$todate."'";
			//echo $sql; die();
			return fetchRow($sql);
		}

		function getTicketStatics_summery($type=false,$depart="",$branch="",$forall = false){
			if($forall){
				$sql .= "select count(*) as total,d.name as department";
			}else{
				if($type){
					$sql .="select count(*) as total,t.type";
				}else{
					$sql .="select count(*) as total";
				}
			}

			$sql .= " from tickets as t
			inner join branches as b on b.id = t.branchid
			inner join departments as d on d.id = t.deptid where 1=1";

			//if($type) $sql.=" and t.type = '$type'";
			if($depart) $sql.=" and t.deptid = $depart";
			if($branch) $sql.=" and t.branchid = $branch";

			if($forall){
				$sql .=" group by t.deptid";
			}else{
				if($type){
					$sql .=" group by t.type";
				}
			}
			//echo $sql;die();
			return fetchRows($sql);
		}

		function getPendingLeader($depart="", $branch=""){
			$sql = "select count(*) as total,u.image,u.name as techname,
			d.name as department, b.name as branch from tickets as t
			inner join branches as b on b.id = t.branchid
			inner join departments as d on d.id = t.deptid
			inner join users as u on u.id = t.assignedto where t.statusid = 1";

			if($depart) $sql .=" and t.deptid = $depart";
			if($branch) $sql .=" and t.branchid = $branch";

			$sql .=" group by t.assignedto order by total desc";
			return fetchRows($sql);
		}

		function staticsMaster($deptid="",$branchid="",$status=""){
			$sql = "select count(*) from tickets WHERE 1=1 ";

			if ($deptid) $sql .=" and deptid = $deptid";
			if ($branchid) $sql .=" and branchid = $branchid";
			if ($status) $sql .=" and statusid = $status";
			//echo $sql;die();
			return fetchRow($sql);
		}

		function ticketStaticsAdmin($userid="",$deptid="",$status="",$isverified="",$fromdate="",$todate=""){
			$sql = "select count(*) from tickets as t where 1=1";
			//select count(*) from tickets WHERE deptid = 1 and statusid = 3 and isverified = 0

			if($userid) $sql .=" and t.assignedto = $userid";
			if($deptid) $sql .=" and t.deptid = $deptid";
			if($status) $sql .=" and t.statusid = $status";

						if($isverified === 0) {

							if($isverified === "")$sql .=""; else $sql .=" and t.isverified = ".$isverified;

						}else if($isverified === 1) $sql .=" and t.isverified = ".$isverified;

			if($fromdate) $sql .=" and t.assignedon >= '".$fromdate."'";
			if($todate) $sql .=" and t.assignedon <= '".$todate."'";
			//echo $sql; die();
			return fetchRow($sql);
		}

		/*
		function for get all ticket for feedback
		col - hasfeedback = 0
		col - isverified > 1
		col - statusId = 3 -> for completed
		@param id
		*/
		//$fromdate="",$todate=""
		function ticketForFeedback($ticketid = "",$clientid= "",$departmentid="",$feedbackstatus=""){
			$sql = "select t.*,t.id as ticketId,p.name as productname,
			b.name as branchname,d.name as departname,u.name as techname,
			su.name as supportname,u.image,ud.name as udepart from tickets as t
			inner join clients as c on c.id = t.clientid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join branches as b on b.id = t.branchid
			inner join users as u on u.id = t.assignedto
			inner join departments as ud on ud.id = u.deptid
			left join supporttype as su on su.id = t.supporttype
			where t.statusid = 3 and t.isverified = 1 ";

			//if ($fromdate) $sql .=" and t.doc >= '".$fromdate."'";
			//if ($todate) $sql .=" and t.doc <= '".$todate." 23:59'";
			if($ticketid) $sql .=" and t.id = $ticketid ";
			if($clientid) $sql .= " and c.id = $clientid";
			if($departmentid) $sql .=" and d.id = $departmentid";

			if($feedbackstatus){
				if($feedbackstatus == 'yes'){
					$sql .=" and t.hasfeedback = 1 ";
				}else if($feedbackstatus == 'no'){
					$sql .=" and t.hasfeedback = 0 ";
				}
			}else{
				$sql .=" and t.hasfeedback = 0 ";
			}
			$sql .= " order by t.doc desc";
			//echo $sql; die();
			return fetchRows($sql);
		}

		function verifyticketForSchedule($ticketId){
			$sql = "select t.id,t.isverified,t.clientremark as problem,d.id as deptid,d.name as deptname,
			t.serialid,p.name as product,s.name as statusname,u.id as assignedtoId,
			u.name as assignedtoName from tickets as t
			inner join statuses as s on s.id = t.statusid
			inner join products as p on p.id = t.prodid
			inner join departments as d on d.id = t.deptid
			inner join users as u on u.id = t.assignedto where t.id = $ticketId";
			//echo $sql;die();
			return fetchRow($sql);
		}

		function getLastContactPerson($serialno=""){
			$sql = "select t.id as ticketid, t.contactname, t.contactmobile,t.doc
			from tickets as t where 1=1 ";
			if($serialno) $sql.=" and t.serialno ='$serialno'";
			$sql .=" order by t.doc desc";
			//echo $sql;die();
			$details = fetchRows($sql);
			return $details[0];
		}
}

/*
where your_expiry_field >= cast( getdate() as date()) + 30
  and your_expiry_field < cast( getdate() as date()) + 31
	*/
?>
