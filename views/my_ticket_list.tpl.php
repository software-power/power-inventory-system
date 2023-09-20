<style media="screen">
	.panel-actions a, .panel-actions .panel-action{
		font-size: 21px;
	}
	.dropleft .dropdown-menu {
		top:0;
			right: 100%;
			left: auto;
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
	.table-responsive {
    min-height: 174px;
}
</style>
<header class="page-header">
	<h2>My Tickets</h2>
</header>
<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading for-heading">
			<div class="panel-holder">
				<h2 class="panel-title">List of Tickets</h2>
			</div>
			<div class="search-holder">
				<div class="col-md-12">
					<form>
						<input type="hidden" name="module" value="tickets">
						<input type="hidden" name="action" value="my_ticket_index">
						<div class="input-group input-search">
							<input type="text" class="form-control" placeholder="Enter serial number" name="serialno" value="<?=$serialno?>"/>
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</form>
				</div>
			</div>
		</header>

	<div style="padding:0" class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px">
		<thead>
			<tr>
				<th>No.</th>
				<th>Ticket</th>
				<th>Serial No</th>
				<th>Client</th>
				<th>Branch</th>
				<th>Product</th>
				<th>Status</th>
				<th>Closed Date</th>
				<th>Verified Date</th>
				<th>Verify Status</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($tickets as $id=>$R) { ?>
				<tr style='color:#ffffff;background-color: <?=$R['color']?>'>
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
					<td><?=ucwords($R['status'])?></td>
					<td><?=$R['doclose']?></td>
					<td><?=$R['dov']?></td>
					<td><?if($R['isverified'] == 0){echo "No"; }else if($R['isverified'] == 1){ echo "Yes";}?></td>

					<td style="text-align:center">
						<div class="btn-group dropleft">
							<button style="color:black" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-list"></i>
							</button>
							<div class="dropdown-menu">
								<?if($R['isverified'] == 1 && $R['status'] == 'Completed'){?>
									<i title="Job Closed" class="fa-ban fa"></i>

								<?} else if($R['isverified'] == 0){?>
									<a class="dropdown-item" href="<?=url('tickets','jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Print JobCard</a>
									<a class="dropdown-item" href="<?=url('tickets','min_jobcard','id='.$R['id'])?>" target="_blank" title="Min Job Card"><i class="fa fa-file"></i> Print Min-JobCard</a>
									<a class="dropdown-item" href="<?=url('tickets','ticket_edit','id='.$R['id'])?>" title="Edit"><i class="fa-pencil fa"></i> Ticket Edit</a>

								<?}?>
							</div>
						</div>
					</td>
				<?php } ?>
			</tbody>
		</table>
		</div>
