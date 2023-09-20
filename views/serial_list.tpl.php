<style>
	.show-fiscal-btn{display:'block';}
	.hide-fiscal-btn{display:'none';}
	.panel-actions a, .panel-actions .panel-action{
		font-size: 21px;
	}
.input-group-rounded input.form-control:first-child, .input-group-rounded input.form-control:last-child, .input-search input.form-control:first-child, .input-search input.form-control:last-child {
    border-radius: 0;
    height: 44px;
    font-size: 15px;
}
.table {
    width: 100%;
    font-size: 14px;
}
.table .actions a:hover, .table .actions-hover a {
    color: #ffffff;
}
.table .actions a:hover, .table .actions-hover a:hover {
    color: #ffffff;
}
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
.badge-orange{
	background-color:#47a447;
}
.badge-red{
	background-color:#d2322d;
}
.center-panel{
	width:80%;
	margin:0 auto;
}
.table-responsive {
  min-height: 150px;
}
</style>

<header class="page-header">
	<h2>Serial Numbers</h2>
</header>
<div class="center-panel">
	<section class="panel">
		<header class="panel-heading">
			<div class="row">
				<div class="col-md-3">
					<h2 class="panel-title">List of Serial Numbers</h2>
					<p>Recently Added</p>
				</div>
				<div class="col-md-9 form-search">
					<form class="col-md-8">
						<input type="hidden" name="module" value="serials">
						<input type="hidden" name="action" value="serial_index">
						<div class="input-group input-search">
							<input type="text" class="form-control" placeholder="Enter search term" name="name" value="<?=$name?>"/>
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</form>
					<a href="?module=serials&action=serial_add" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn"><i class="fa fa-plus"></i> Add Serial</a>
					<a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
				</div>
			</div>
		</header>
	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable">
		<thead>
			<tr>
				<tr>
					<th>No.</th>
	        <th>Serial Number</th>
	        <th>Device Status</th>
	        <th>Created by</th>
	        <th>Fiscalize Status</th>
	        <th>&nbsp;</th>
				</tr>
			</tr>
		</thead>
		<tbody>
			<?php foreach($serial as $id=>$R) { ?>

				<tr>
					<td width="80px"><?=$id+1?></td>
					<td><?=$R['name']?></td>
					<td><?=$R['status']?></td>
					<td><?=$R['createdbyname']?></td>
					<td>
						<span class="badge <?if($R['isfiscal'] == 1) echo 'badge-orange'; else echo 'badge-red';?>">
							<?=($R['isfiscal']? 'Fiscalized':'Not Fiscalized')?>
						</span>
          </td>
					<td>

						<div class="btn-group dropleft">
							<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <i class="fa fa-list"></i>
						  </button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="<?=url('serials','serial_edit','id='.$R['id'])?>"  title="Edit"><i class="fa-pencil fa"></i> Edit Serial</a>
								<?php if($role == 1){?>
									<?if($R['isfiscal'] == 0 && $R['isfiscalrequested'] == 0){?>
										<a class="dropdown-item" href="<?=url('serials','assignfiscal','id='.$R['id'])?>"  title="Send To Fiscalize"><i class="fa-share-square fa"></i> Send to Fiscalize</a>
									<?}else{?>
										<?php if ($R['isfiscal'] != 1){ ?>
											<a class="dropdown-item" href="<?=url('serials','fiscal_edit','id='.$R['id'])?>"  title="Re-Assign Fiscalize"><i class="fa-share-square fa"></i> Re-Assign Fiscalize</a>
										<?php }; ?>
									<?}?>
									<a class="dropdown-item" href="<?=url('serials','serial_delete','id='.$R['id'])?>" onclick="return confirm('Are you sure you want to delete this serial number?')" title="Delete"><i class="fa-times fa"></i> Delete Serial</a>
								<?php }else{?>
									<?php if($head){?>
										<?if($R['isfiscal'] == 0 && $R['isfiscalrequested'] == 0){?>
											<a class="dropdown-item" href="<?=url('serials','assignfiscal','id='.$R['id'])?>"  title="Send To Fiscalize"><i class="fa-share-square fa"></i> Send to Fiscalize</a>
										<?}else{?>
											<?php if ($R['isfiscal'] != 1){ ?>
												<a class="dropdown-item" href="<?=url('serials','fiscal_edit','id='.$R['id'])?>"  title="Re-Assign Fiscalize"><i class="fa-share-square fa"></i> Re-Assign Fiscalize</a>
											<?php }; ?>
										<?}?>
										<a class="dropdown-item" href="<?=url('serials','serial_delete','id='.$R['id'])?>" onclick="return confirm('Are you sure you want to delete this serial number?')" title="Delete"><i class="fa-times fa"></i> Delete Serial</a>
									<?php }; ?>
								<?php }?>
							</div>
						</div>
					</td>
				<?php } ?>
			</tbody>
		</table>
		</div>
