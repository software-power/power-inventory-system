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
.badge-block{
	width:100%;
	border-radius:unset;
}
</style>

<header class="page-header">
	<h2>Serial Numbers</h2>
</header>
<div class="center-panel">
	<section class="panel">
		<header class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<h2 class="panel-title">List of Serial Numbers</h2>
					<p>Recently Added</p>
				</div>
				<div class="col-md-3">
					<a href="?module=clients&action=client_index" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn"><i class="fa fa-list"></i> Client list</a>
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
	        <th>Product</th>
	        <th>Department</th>
	        <th>Created by</th>
	        <th>Fiscalize Status</th>
				</tr>
			</tr>
		</thead>
		<tbody>
			<?php foreach($serial as $id=>$R) { ?>
				<tr>
					<td width="10px"><?=$id+1?></td>
					<td><?=$R['name']?></td>
					<td><?=$R['productname']?></td>
					<td><?=$R['departname']?></td>
					<td><?=$R['createdbyname']?></td>
					<td>
						<span class="badge-block badge <?if($R['isfiscal'] == 1) echo 'badge-orange'; else echo 'badge-red';?>">
							<?=($R['isfiscal']? 'Fiscalized':'Not Fiscalized')?>
						</span>
          </td>
        </tr>
				<?php } ?>
			</tbody>
		</table>
		</div>
