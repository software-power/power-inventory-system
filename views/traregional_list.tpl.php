<style media="screen">
	.panel-actions a, .panel-actions .panel-action{
		font-size: 21px;
	}
  form table tr td{
    padding-left: 10px;
  }
  .for-column{
    width:100px;
  }
  .for-btn {
    padding: 16px;
    display: block;
}
.for-holder{
	height:0px;
	overflow: hidden;
	transition: .3s;
	background: white;
}
.for-view-filter{
height: 165px;
padding:10px;
}
.btn-holder {
  float: right;
}
</style>
<header class="page-header">
	<h2>TRA Regional</h2>
</header>
<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading for-heading">
			<div class="btn-holder">
        <a class="btn" href="?module=tra&action=vfd_settings"> <i class="fa fa-cog"></i> VFD settings</a>
        <a class="btn" href="?module=tra&action=add_regional"> <i class="fa fa-plus"></i> Add Regional</a>
  			<a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
      </div>
			<h2 class="panel-title">Regional List</h2>
		</header>

	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:14px">
		<thead>
			<tr>
				<th>No.</th>
				<th>Manager Name</th>
				<th>Mobile</th>
				<th>Email (s)</th>
				<th>Location (Regional)</th>
				<th>Created by</th>
				<th>Created Date</th>
        <th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($tra_list as $id=>$R) { ?>
				<tr>
					<td width="18px"><?=$id+1?></td>
					<td><?=$R['mname']?></td>
					<td><?=$R['mobile']?></td>
					<td><?=$R['email'].",".$R['email2'].",<br>".$R['email3']?></td>
					<td><?=$R['location']?></td>
					<td><?=$R['createdbyname']?></td>
					<td><?=fDate($R['doc'])?></td>
          <td><a href="<?=url('tra','edit_regional','id='.$R['id'])?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
				<?php } ?>
			</tbody>
		</table>
		</div>

		<script type="text/javascript">
			$('#open_filter').on('click', function(){
				$('#for-search-report').toggleClass('for-view-filter');
			})
		</script>
