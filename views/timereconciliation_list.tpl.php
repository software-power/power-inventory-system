<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
div.dataTables_wrapper div.dataTables_filter input {
    width: 100%;
}
.panel-actions a, .panel-actions .panel-action{
	font-size: 21px;
}
.formholder h5 {
    font-size: 15px;
    font-weight: 600;
}
.for-input {
  padding: 8px;
  height: 40px;
  font-size: 14px;
  border: none;
  outline: none;
	margin-top: 2px;
}
.select2-container--default .select2-selection--single{
  padding: 8px;
  height: 40px;
  font-size: 14px;
	border: none;
	outline: none;
	margin-top: 2px;
}
.formModel {
	display:none;
	position: fixed;
	width: 100%;
	z-index: 14;
	background: rgba(238, 238, 238, 0.6196078431372549);
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	height: 100%;
}
.formholder {
	position: relative;
	display:none;
	z-index: 26;
	border-radius: 5px;
	padding: 24px;
	width: 100%;
	background: #ededee;
	height: 240px;
	-webkit-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	-moz-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	}
.panelControl {
    float: right;
}
.for-formanage {
    border: 1px solid #47a447;
    padding: 9px;
    height: auto;
    border-radius: 5px;
}
div.dataTables_wrapper div.dataTables_filter input{
  margin-left:0;
}
</style>
<header class="page-header">
	<h2>Time Reconcilition Report</h2>
</header>
<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="schedules">
		<input type="hidden" name="action" value="index">
		<div id="filter_table">
			<div class="row">
				<div class="col-md-6">
					<input class="form-control for-input" type="text" name="serialno" value="" placeholder="Serial Number">
				</div>
				<div class="col-md-6">
					<input class="form-control for-input" type="text" name="ticketid" value="<?=$ticketid?>" placeholder="Ticket Number">
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<select id="clientid" class="form-control" name="client">
						<?php if ($client){?>
							<option value="<?=$client?>"><?=$clientname?></option>
						<?php }else {?>
							<option value="" selected disabled>--Client Name--</option>
						<?php }?>
					</select>
				</div>
				<div class="col-md-6">
					<select class="form-control for-input" name="user">
							<option value="" selected disabled>--Staff--</option>
							<? foreach ($users as $r){?>
								<option <?=selected($r['id'],$user)?> value="<?=$r['id']?>"><?=$r['name']?></option>
							<?}?>
						</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-3">
							<h5>From Date</h5>
						</div>
						<div class="col-md-9">
							<input type="text" readonly name="fromdate"  class="datepicker form-control for-input" value="<?=$fromdate?>">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-3">
							<h5>To Date</h5>
						</div>
						<div class="col-md-9">
							<input type="text" readonly name="todate"  class="datepicker form-control for-input" value="<?=$todate?>">
						</div>
					</div>
				</div>
			</div>
			<div class="row">
        <div class="col-md-4">
					<div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
				</div>
				<div class="col-md-4">
					<a href="?module=reports&action=index" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET</a>
				</div>
        <div class="col-md-4">
					<button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i> SEARCH</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading">
			<div class="panelControl">
				<button id="openModel" class="btn btn-primary" href="?module=home&action=index" title="Home"> <i class="fa fa-search"></i> Open Search </button>
				<a class="btn btn-primary" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> </a>
			</div>
			<h2 class="panel-title">Ticket Time reconciliation list</h2>
		</header>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" style="font-size:13px;" id="printing_area">
					<thead>
						<tr>
							<th>#</th>
							<th>Ticket</th>
							<th>Serial No</th>
							<th>Client Name</th>
							<th>Branch</th>
              <th>Assigned To</th>
							<th>Time (Time Spent)</th>
							<th>Time (Day Time)</th>
							<th>Time (Schedule Time)</th>
						</tr>
					</thead>
					<tbody>

						<?php
							foreach($reconciliationList as $index=>$list) {?>

							<tr>
								<td><?=$index+1;?></td>
								<td><?=$list['ticketid']?></td>
								<td><?=$list['serialno']?></td>
								<td><?=$list['clientname']?></td>
								<td><?=$list['branchname']?></td>
								<td><?=$list['assignedname']?></td>
								<td style="text-align:center">
                  <?=($list['timespent']? $list['timespent']:0)?></td>
								<td style="text-align:center">
                  <?=($list['daytime']? $list['daytime']:0)?></td>
								<td style="text-align:center">
                  <?=($list['schedultime']? $list['schedultime'] * 30: 0)?>
                </td>
							</tr>

							<?php }?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>
<script type="text/javascript">
$(function(){
	$('#departId').select2({width:'100%',minimumInputLength:3,
		ajax:{
			url:"?module=departments&action=getDepartments&format=json",dataType:'json',delay:250,quietMillis:200,
			data: function (term) {return {search:term};},
			results:function (data,page){return{result:data};}
		}
	});

	$("#clientid").select2({ width: '100%', minimumInputLength: 3,
		ajax: {
			url: "?module=clients&action=getClients&format=json", dataType: 'json', delay: 250, quietMillis: 200,
			data: function (term) {	return { search : term }; },
			results: function (data, page) { return { results: data }; }
		}
	 });
})
$('#openModel').on('click', function (){
	$('#formHolder').show('slow');
	$('#formModel').show('slow');
})

$('#closeSearchModel').on('click', function (){
	$('#formHolder').hide('slow');
	$('#formModel').hide('slow');
})

// $(document).ready(function(){
//  $('#printing_area').DataTable({
// 	 dom: '<"top"fB>t<"bottom"ip>',
// 	 colReorder:true,
// 	 keys:true,
// 	 buttons: [
// 		 'copyHtml5', 'excelHtml5', 'pdfHtml5','csvHtml5','print'],
// 	 <?if($_GET['status']){?>
// 	 title:'<?=$_GET['status']?>',
// 	 <?}?>
//  });
// })

</script>
