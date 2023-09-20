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
<div class="center-panel">
	<section class="panel">
		<header class="panel-heading">
			<div class="row">
				<div class="col-md-3">
					<h2 class="panel-title">List of Brand</h2>
					<p>Recently Added</p>
				</div> 
				<div class="col-md-9 form-search">
					<form method="post" action="<?=url('model','search')?>" class="col-md-8">
						<input type="hidden" name="module" value="model">
						<input type="hidden" name="action" value="search">
						<div class="input-group input-search">
							<input type="text" class="form-control" placeholder="Enter search term" name="name" value="<?=$name?>"/>
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</form>
					<a href="?module=model&action=model_add" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn"><i class="fa fa-plus"></i> Add Brand</a>
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
	        <th>Name</th>
	        <th>Status</th>
	        <th></th>
				</tr>
			</tr>
		</thead>
		<tbody>
			<?php foreach($model_list as $id=>$R) { ?>

				<tr>
					<td width="80px"><?=$id+1?></td>
					<td><?=$R['name']?></td>
					<td><?=$R['status']?></td>
					<td>
						<div class="btn-group dropleft">
							<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <i class="fa fa-list"></i>
						  </button>
							<div class="dropdown-menu">
								<a class="dropdown-item" href="<?=url('model','model_edit','id='.$R['id'])?>"  title="Edit"><i class="fa-pencil fa"></i> Edit</a>
								<a class="dropdown-item" href="<?=url('model','model_delete','id='.$R['id'])?>"  title="Edit"><i class="fa-trash fa"></i> Delte</a>
							</div>
						</div>
					</td>
				<?php } ?>
			</tbody>
		</table>
		</div>
