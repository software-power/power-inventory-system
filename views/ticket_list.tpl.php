<style media="screen">
.dropleft .dropdown-menu {
		top: 0;
		right: 100%;
		left: auto;
		margin-top: 0;
		margin-right: .125rem;
}
a.dropdown-item{
	text-decoration:none;
}
.dropdown-item {
		display: block;
		width: 100%;
		padding: .25rem 1.5rem;
		clear: both;
		font-weight: 400;
		color: #212529;
		text-align: inherit;
		white-space: nowrap;
		background-color: transparent;
		border: 0;
}
.input-search input.form-control:first-child, .input-search input.form-control:last-child {
    border-radius: 0;
    height: 44px;
    font-size: 15px;
}
.search-holder {
    width: 60%;
    position: absolute;
    right: 0;
    top: 0;
		padding: 10px;
}
.table-responsive{
	min-height:154px;
}
span.transfstatus {
    font-size: 15px;
    font-weight: 600;
}
.transfstatus::before{
		content: '';
    display: block;
    background: #15d637;
    width: 12px;
    height: 12px;
    position: absolute;
    margin-left: -14px;
    border-radius: 100%;
}
.ticketholder {
    position: fixed;
    z-index: 99;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5803921568627451);
    display: none;
}
.table-holder {
    position: relative;
    top: 159px;
    background: white;
    width: 88%;
    margin: 0 auto;
    margin-left: 121px;
}
.title-model {
    padding-top: 4px;
    float: left;
    margin-left: 26px;
}
.close-btn-holder {
    padding-top: 4px;
    width: 90px;
    float: right;
}
</style>
<header class="page-header">
	<h2><?=$currentTitle;?> Tickets</h2>
</header>

<div class="ticketholder">
	<div class="table-holder">
		<div class="title-model">
			<h4><i class="fa fa-history"></i> Ticket transferred logs</h4>
		</div>
		<div class="close-btn-holder">
			<button class="btn btn-danger" onclick="closeTable()" type="button" name="button"><i class="fa fa-close"></i> CLOSE</button>
		</div>
		<table class="table table-hover mb-none" style="font-size:13px;" id="printing_area">
			<thead>
				<tr>
					<th>#</th>
					<th>Ticket</th>
					<th>Serial No</th>
					<th>Client Name</th>
					<th>From Branch</th>
					<th>Physical Location</th>
					<th>Product</th>
					<th>Depart</th>
					<th>Was Assigned To</th>
					<th>Was Assigned By</th>
					<th>Was Assigned Date</th>
					<th>Transferred Date</th>
				</tr>
			</thead>
			<tbody id="tbodyforticekt">
			</tbody>
		</table>
		</div>
</div>

<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-holder">
				<div class="row">
					<div class="col-md-3">
						<h2 class="panel-title"><i class="fa fa-file"></i> List of Tickets</h2>
					</div>
					<div class="col-md-2">
						<div class="transfstatus"> Ticket transferred</div>
					</div>
				</div>
			</div>
			<div class="search-holder">
				<form class="col-md-8">
					<input type="hidden" name="module" value="tickets">
					<input type="hidden" name="action" value="ticket_index">
					<div class="input-group input-search">
						<input type="text" class="form-control" placeholder="Enter serial no" name="serialno" value="<?=$serialno?>"/>
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
						</span>
					</div>
				</form>
				<div class="col-md-4">
					<?if($activetab == 'assigned_ticket'){?>
						<a href="?module=tickets&action=ticket_index&activetab=assigned_ticket" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa-tasks fa"></i> Assigned Ticket</a>
					<?}else if($activetab == 'unassigned_ticket'){?>
						<a href="?module=tickets&action=ticket_index&activetab=unassigned_ticket" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa-tasks fa"></i> Unassigned Ticket</a>
					<?}?>
				</div>
			</div>
		</header>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px;">
					<thead>
						<tr>
							<th>No.</th>
							<th>Ticket</th>
							<th>Serial No</th>
							<th>Client</th>
							<th>Branch</th>
							<th>Product</th>
							<th>Department</th>
							<th>Assigned To.</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($tickets as $id=>$R) { ?>
							<tr>
								<td width="80px"><?=$id+1?></td>
								<td width="80px"><span <? if($R['istransferred']) echo 'class="transfstatus"';?>><?=$R['id']?></span> </td>
								<td><?=$R['serialno']?></td>
								<td>
									<?=$R['client']?>
									<? if ($R['real_client']) { ?>
									-> <?=$R['real_client']?>
									<? } ?>
								</td>
								<td><?=$R['branch']?></td>
								<td><?=$R['product']?></td>
								<td><?=$R['department']?></td>
								<td><?if($R['assignedperson'] == "") echo "Not Yet"; else echo $R['assignedperson'];?></td>
								<td style="text-align:center">
									<div class="btn-group dropleft">
										<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="fa fa-list"></i>
										</button>
										<div class="dropdown-menu">
											<?if($_GET['activetab'] == 'assigned_ticket'){?>
												<a class="dropdown-item" title="JOBCARD" target="_blank" href="?module=tickets&action=jobcard&id=<?=$R['id']?>"> <i class="fa fa-file"></i> Print JobCard</a>
												<a class="dropdown-item" title="MIN-JOBCARD" target="_blank" href="?module=tickets&action=min_jobcard&id=<?=$R['id']?>"> <i class="fa fa-file"></i> Print Min-JobCard</a>
											<?}?>
											<?if ($R['clientid'] == 0 || $R['serialid'] == 0){?>
												<a class="dropdown-item" href="<?=url('tickets','ticket_edit','id='.$R['id'])?>"  title="Edit"><i class="fa-pencil fa"></i></a>
											<?}else{?>
												<a class="dropdown-item" href="<?=url('tickets','ticket_assign_edit','id='.$R['id'])?>"  title="<?if($_GET['activetab'] == 'assigned_ticket'){echo 'Re-Assign To';}else if($_GET['activetab'] == 'unassigned_ticket'){echo 'Assign To';}else{echo 'Assign To';}?>">
													<i class="fa-tasks fa"></i> <?if($_GET['activetab'] == 'assigned_ticket'){echo 'Re-Assign To';}else if($_GET['activetab'] == 'unassigned_ticket'){echo 'Assign To';}else{echo 'Assign To';}?>
												</a>
											<?}?>
												<a class="dropdown-item" href="<?=url('tickets','ticket_edit','id='.$R['id'])?>" title="Edit"><i class="fa-pencil fa"></i> Ticket Edit</a>
												<?php if ($role == 'Admin' || $head == 1){?>
													<a class="dropdown-item" href="<?=url('tickets','ticket_transfer','id='.$R['id'])?>" title="Ticket Transfer"><i class="fa-forward fa"></i> Ticket Transfer</a>
												<?php }; ?>
												<?php if ($role == 'Admin' || $head == 1){?>
													<?php if ($R['istransferred']){ ?>
														<a onclick="getTransferredLog(this)" data-ticketid="<?=$R['id']?>" class="dropdown-item" href="#" title="Transfer log"><i class="fa-history fa"></i> Transfer log</a>
													<?php }; ?>
												<?php }; ?>
										</div>
									</div>
								</td>

							<?php } ?>
						</tbody>
				</table>
			</div>
		</div>

<script type="text/javascript">
		function closeTable(){
			$('.ticketholder').hide('slow');
		}
		function getTransferredLog(obj){
			var ticketid = $(obj).attr('data-ticketid');

			$('.ticketholder').show('slow');
			$.get('?module=tickets&action=geticketransferredLog&format=json&ticketid='+ticketid,null,function(data){
				var logs = eval(data);
				console.log(logs);
				if(logs[0].message == "Found"){
					$('#tbodyforticekt').empty();
					$.each(logs[0].details, function(index, ticket){
						count = parseInt(index) + 1;
						var tableRow = "<tr>"+
							"<td>"+count+"</td><th>"+ticket.id+"</td><td>"+ticket.serialno+"</td>"+
							"<td>"+ticket.clientname+"</td><td>"+ticket.frombranch+"</td>"+
							"<td>"+ticket.ploc+"</td>"+
							"<td>"+ticket.productname+"</td><td>"+ticket.departname+"</td>"+
							"<td>"+ticket.wasassignedto+"</td><td>"+ticket.wasassignedby+"</td>"+
							"<td>"+ticket.wasassignedate+"</td><td>"+ticket.createdate+"</td></tr>";

							$('#tbodyforticekt').append(tableRow);
					})
				}

			});
		}
</script>
