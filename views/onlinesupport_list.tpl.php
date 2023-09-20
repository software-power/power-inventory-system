<header class="page-header">
	<h2><?=$currentTitle;?> Online Tickets</h2>
</header>

<div class="col-md-12">
	<div class="col-md-8">
		<form>
			<input type="hidden" name="module" value="tickets">
			<input type="hidden" name="action" value="online_ticket">
			<div class="input-group input-search">
				<input type="text" class="form-control" placeholder="Enter serial no" name="serialno" value="<?=$serialno?>"/>
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				</span>
			</div>

		</form>
	</div>
	<div class="col-md-4"><!--<a href="<?=url('users','users_add')?>" class="mb-xs mt-xs mr-xs btn btn-success">Add User</a>-->
		<?if($activetab == 'assigned_ticket'){?>

			<a href="?module=tickets&action=online_ticket&activetab=assigned_ticket" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn btn-success">Assigned Online Ticket</a>
		<?}else if($activetab == 'unassigned_ticket'){?>

			<a href="?module=tickets&action=online_ticket&activetab=unassigned_ticket" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn btn-success">Unassigned Online Ticket</a>
		<?}?>
	</div>
</div>


<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-actions">
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title"> <i class="fa fa-globe"></i> Online Support Tickets</h2>
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

				<?if($_GET['activetab'] == 'assigned_ticket'){?>
					<th>JobCard</th>
				<?}?>

				<th>Assigned person</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($tickets as $id=>$R) { ?>
				<tr>

					<td width="80px"><?=$id+1?></td>
					<td width="80px"><?=$R['id']?></td>
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

					<?if($_GET['activetab'] == 'assigned_ticket'){?>

						<?if($R['department'] == 'EFD'){?>
							<td> <a title="JOBCARD" target="_blank" href="?module=tickets&action=jobcard&id=<?=$R['id']?>"> <i class="fa fa-file"></i> </a> </td>
						<?}else if($R['department'] == 'Tally'){?>
							<td> <a title="JOBCARD" target="_blank" href="?module=tickets&action=tally_jobcard&id=<?=$R['id']?>"> <i class="fa fa-file"></i> </a> </td>
						<?}else if($R['department'] == 'POWERSECURE'){?>
							<td> <a title="JOBCARD" target="_blank" href="?module=tickets&action=powersecure_jobcard&id=<?=$R['id']?>"> <i class="fa fa-file"></i> </a> </td>
						<?}else if($R['department'] == 'ICT'){?>

							<?if($R['supportedwith'] == 'inhouse'){?>
								<td> <a title="JOBCARD" target="_blank" href="?module=tickets&action=inhouse_ict_jobcard&id=<?=$R['id']?>"> <i class="fa fa-file"></i> </a> </td>

							<?}else if($R['supportedwith'] == 'sitevisit'){?>

								<td> <a title="JOBCARD" target="_blank" href="?module=tickets&action=sitevisit_ict_jobcard&id=<?=$R['id']?>"> <i class="fa fa-file"></i> </a> </td>

							<?}?>

						<?}?>


					<?}?>

					<td><?if($R['assignedperson'] == "") echo "Not Yet"; else echo $R['assignedperson'];?></td>
					<td class="actions-hover actions-fade">
					<?if ($R['clientid'] == 0 || $R['serialid'] == 0){?>
						<a href="<?=url('tickets','ticket_edit','id='.$R['id'])?>"  title="Edit"><i class="fa-pencil fa"></i></a>
					<?}else{?>
						<a href="<?=url('tickets','ticket_assign_edit','id='.$R['id'])?>"  title="<?if($_GET['activetab'] == 'assigned_ticket'){echo "Re-Assign";}else if($_GET['activetab'] == 'unassigned_ticket'){echo "Assign";}else{echo "Assign";}?>"><i class="fa-tasks fa"></i></a>
					<?}?>
						<!-- <a href="<?=url('tickets','ticket_delete','id='.$R['id'])?>" onclick="return confirm('Are you sure you want to delete this ticket?')" title="Delete"><i class="fa-times fa"></i></a> -->
						<a href="<?=url('tickets','ticket_edit','id='.$R['id'])?>" title="Edit"><i class="fa-pencil fa"></i></a>
					</td>
				<?php } ?>
			</tbody>
		</table>
		</div>
