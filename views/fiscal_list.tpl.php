<header class="page-header">
	<h2>Serials <?=$currentTitle;?></h2>
</header>

<div class="col-md-12">
	<div class="col-md-8">
		<form>
			<input type="hidden" name="module" value="serials">
			<input type="hidden" name="action" value="fiscal_requested_list">
			<div class="input-group input-search">
				<input type="text" class="form-control" placeholder="Enter serial no" name="serialno" value="<?=$serialno?>"/>
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				</span>
			</div>

		</form>
	</div>
	<div class="col-md-4"><!--<a href="<?=url('users','users_add')?>" class="mb-xs mt-xs mr-xs btn btn-success">Add User</a>-->
		<?if($activetab == 'fiscalized_serial'){?>

			<a href="?module=serials&action=fiscal_requested_list&activetab=fiscalized_serial" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn btn-success">Fiscalized Serial</a>
		<?}else if($activetab == 'fiscal_request'){?>

			<a href="?module=serials&action=fiscal_requested_list&activetab=fiscal_request" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn btn-success">Fiscalize Request</a>
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

			<h2 class="panel-title">List of Serials</h2>
		</header>

	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable">
		<thead>
			<tr>
				<th>No.</th>
				<th>Serial No</th>
				<th>Product Name</th>
				<th>Technician Name</th>
				<th>Status</th>
				<!-- <th>Assigned person</th> -->
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($serials as $id=>$R) { ?>
				<tr>

					<td width="80px"><?=$id+1?></td>
					<!-- <td><?=$R['serialno']?></td> -->
					<td><?=$R['serialno']?></td>
					<td><?=$R['productname']?></td>
					<td><?=$R['technician']?></td>
					<td>
            <?if($activetab == 'fiscal_request'){?>

              <?if($R['verify'] == 0){echo "Pending fiscalizing";}else if($R['verify'] == 1){echo "fiscalized";}?>

            <?}else if($activetab == 'fiscalized_serial'){?>

              <?if($R['verify'] == 0){echo "Pending Request";}else if($R['verify'] == 1){echo "Request Accepted";}?>

            <?}?>
          </td>

					<td class="actions-hover actions-fade">
					<?if ($R['clientid'] == 0 || $R['serialid'] == 0){?>
						<a href="<?=url('serials','fiscal_edit','id='.$R['id'])?>"  title="Edit"><i class="fa-pencil fa"></i></a>
					<?}?>

          <?if($R['fiscalized'] == 0){?>
            <a href="<?=url('serials','fiscal_verify','id='.$R['id'])?>"  title="Verify"><i class="fa-check fa"></i></a>
          <?}?>

					  <!-- <a href="<?=url('serials','fiscal_delete','id='.$R['id'])?>" onclick="return confirm('Are you sure you want to delete this ticket?')" title="Delete"><i class="fa-times fa"></i></a> -->
					</td>

				<?php } ?>
			</tbody>
		</table>
		</div>
