<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
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
.select2-container--default .select2-selection--single {
    padding: 9px;
    height: 52px;
    font-size: 16px;
    border: none;
    outline: none;
    margin-top: 2px;
}
.for-input {
    padding: 9px;
    height: 52px;
    font-size: 16px;
    border: none;
    outline: none;
		margin-top: 2px;
}
.formholder {
	position: relative;
	display:none;
	z-index: 26;
	border-radius: 5px;
	padding: 16px;
	width: 100%;
	background: #ededee;
	height: 278px;
	-webkit-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	-moz-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
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
.ticketholder {
    position: fixed;
    z-index: 99;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5803921568627451);
		display:none;
}
.table-holder {
    position: relative;
    top: 159px;
    background: white;
    width: 88%;
    margin: 0 auto;
    margin-left: 121px;
}
.badge-btn{
	cursor:pointer;
}
.title-model {
    padding-top: 4px;
    float: left;
    margin-left: 26px;
}
.close-btn-holder {
    padding-top: 4px;
    width: 73px;
    float: right;
}
div.dataTables_wrapper div.dataTables_filter input {
    width: 91%;
    padding: 10px;
    height: 41px;
    font-size: 15px;
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
</style>
<header class="page-header">
	<h2>AMC Report</h2>
</header>

<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="amcs">
		<input type="hidden" name="action" value="amc_reports">
		<div id="filter_table">
			<div class="row">
				<div class="col-md-12">
					<input class="form-control for-input" type="text" name="invoiceno" value="" placeholder="Invoice Number">
				</div>
			</div>
			<!-- <div class="row">
				<div class="col-md-6">
					<select class="form-control for-input" name="branch">
							<option value="" selected disabled>Branch</option>
							<? foreach ($braches as $b){?>
								<option <?=selected($b['id'],$branch)?> value="<?=$b['id']?>"><?=$b['name']?></option>
							<?}?>
						</select>
				</div>
				<div class="col-md-6">
					<select id="departId" class="form-control for-input" name="department">
							<option value="">Department</option>
					</select>
				</div>
			</div> -->
			<div class="row">
				<div class="col-md-6">
					<input class="form-control for-input" type="text" name="serialno" value="" placeholder="Serial Number">
				</div>
				<div class="col-md-6">
					<select id="clientid" class="form-control" name="clientid">
						<?php if ($client){?>
							<option value="<?=$client?>"><?=$clientname?></option>
						<?php }else {?>
							<option value="" selected disabled>Client Name</option>
						<?php }?>
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
					<button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i> SEARCH</button>
				</div>
				<div class="col-md-4">
					<a href="?module=amcs&action=amc_reports" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET</a>
				</div>
				<div class="col-md-4">
					<div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="ticketholder">
	<div class="table-holder">
		<div class="title-model">
			<h4><i class="fa fa-file"></i> Ticket/Services Report</h4>
		</div>
		<div class="close-btn-holder">
			<button class="btn btn-danger" onclick="closeTable()" type="button" name="button">CLOSE</button>
		</div>
		<table class="table table-hover mb-none" style="font-size:13px;" id="printing_area">
			<thead>
				<tr>
					<th></th>
					<th>#</th>
					<th>Ticket</th>
					<th>Serial No</th>
					<th>Client Name</th>
					<th>Branch</th>
					<th>Product</th>
					<th>Depart</th>
					<th>Assigned To</th>
					<th>Assigned By</th>
					<th>Assigned Date</th>
					<th>Support Type</th>
					<th>Job Card</th>
				</tr>
			</thead>
			<tbody id="tbodyforticekt">
			</tbody>
		</table>
		</div>
</div>

<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading for-heading">
			<div class="btn-holder">
        <button id="openModel" class="btn btn-success"> <i class="fa fa-search"></i> Search</button>
  			<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
      </div>
			<h2 class="panel-title">List of AMC</h2>
		</header>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-hover mb-none" id="amcTable" style="font-size:13px">
				<thead>
					<tr>
						<th style="width:18px;">No.</th>
						<th>AMC No.</th>
						<th>invoiceno</th>
						<!-- <th>Serial Ref.</th> -->
						<th>AMC Start</th>
						<th>AMC End</th>
						<th style="text-align:center">Total Services</th>
						<th style="text-align:center">AMC Services</th>
						<th>Serial No.</th>
						<th>Client Name</th>
						<th>Created by</th>
						<th>Created Date</th>
						<th>AMC Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($amclist as $id=>$R) { ?>
						<tr>
							<td width="18px"><?=$id+1?></td>
							<td width="80px" style="text-align:center"><?=$R['amcnumber']?></td>
							<td><?=$R['invoiceno']?></td>
							<td><?=fDate($R['amcstart'])?></td>
							<td><?=fDate($R['amcend'])?></td>

							<td style="text-align:center;">
								<?php if ($R['totalservices']) {?>
									<span onclick="getTikcetDetails(<?=$R['serialid']?>,0);" class="btn btn-primary"><?=$R['totalservices']?></span>
								<?php }else{ ?>
									<span><?=$R['totalservices']?></span>
								<?php } ?>
							</td>

							<td style="text-align:center;font-weight:600">
								<?php if ($R['amcservices']) {?>
									<span onclick="getTikcetDetails(0,<?=$R['amcnumber']?>);" class="btn btn-success"><?=$R['amcservices']?></span>
								<?php }else{ ?>
									<span><?=$R['amcservices']?></span>
								<?php } ?>
							</td>

							<td><?=$R['serialname']?></td>
							<td><?=$R['clientname']?></td>
							<td><?=$R['createdbyname']?></td>
							<td><?=fDate($R['createddate'])?></td>
							<td><?=$R['amcstatus']?></td>
		          <td>
								<div class="btn-group dropleft">
								  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    <i class="fa fa-list"></i>
								  </button>
								  <div class="dropdown-menu">
										<a class="dropdown-item" href="?module=amcs&action=edit_amc&amcnumber=<?=$R['amcnumber']?>" title="AMC Edit"><i class="fa fa-pencil"></i> AMC edit</a>
				            <a class="dropdown-item" target="_blank" href="?module=amcs&action=print_amc&amcno=<?=$R['amcnumber']?>" title="Print AMC"><i class="fa fa-file"></i> Print AMC</a>
								  </div>
								</div>
		          </td>
						<?php } ?>
					</tbody>
				</table>
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
			$('#open_filter').on('click', function(){
				$('#for-search-report').toggleClass('for-view-filter');
			});

			$('#openModel').on('click', function (){
				$('#formHolder').show('slow');
				$('#formModel').show('slow');
			});

			$('#closeSearchModel').on('click', function (){
				$('#formHolder').hide('slow');
				$('#formModel').hide('slow');
			});
			function getTikcetDetails(serialId,amcid){
				$('.ticketholder').show('slow')

				$.get("?module=amcs&action=getAMCTickets&format=json&serialId="+serialId+"&amcid="+amcid, null, function(d){
		      var data = eval(d);

					if(data[0].message == "Found"){
						$('#tbodyforticekt').empty();
						$.each(data[0].details, function(index, ticket){
							count = parseInt(index) + 1;
							var tableRow = "<tr><td></td>"+
								"<td>"+count+"</td><th>"+ticket.id+"</td><td>"+ticket.serialno+"</td>"+
								"<td>"+ticket.clientname+"</td><td>"+ticket.branchname+"</td>"+
								"<td>"+ticket.productname+"</td><td>"+ticket.deptname+"</td>"+
								"<td>"+ticket.assigname+"</td><td>"+ticket.assignby+"</td>"+
								"<td>"+ticket.assignedon+"</td><td>"+ticket.supportype+"</td>"+
								"<td style='text-align:center'><a title='JOB CARD' target='_blank' href='?module=tickets&action=jobcard&id="+ticket.id+"'><i class='fa fa-file'></i></a></td></tr>";

								$('#tbodyforticekt').append(tableRow);
						})
					}

		    });
			}
			function closeTable(){
				$('.ticketholder').hide('slow');
			}

			$(document).ready(function(){
			 $('#amcTable').DataTable({
				 dom: '<"top"fB>t<"bottom"ip>',
				 colReorder:true,
				 keys:true,
				 buttons: [
					 'copyHtml5', 'excelHtml5', 'pdfHtml5','csvHtml5','print'],
				 <?if($_GET['status']){?>
				 title:'<?=$_GET['status']?>',
				 <?}?>
			 });
			})

		</script>
