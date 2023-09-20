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
	height: 154px;
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
    top: 136px;
    background: white;
    width: 63%;
    margin: 0 auto;
    margin-left: 283px;
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
    width: 86px;
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
.center-panel{
  width:70%;
  margin:0 auto;
}
tr.trheader th{
  text-align:center;
}
tr.lunch{
  background:#d64742;
  color:#ffffff;
}
tr.scheduletr {
    font-size: 15px;
    font-weight: 500;
    text-align: center;
}
</style>
<header class="page-header">
	<h2>My Schedule</h2>
</header>

<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="amcs">
		<input type="hidden" name="action" value="amc_reports">
		<div id="filter_table">
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
			<h4><i class="fa fa-calendar"></i> Day Schedule</h4>
		</div>
		<div class="close-btn-holder">
			<button class="btn btn-danger" onclick="closeTable()" type="button" name="button">CLOSE</button>
		</div>
		<table class="table mb-none" style="font-size:13px;" id="printing_area">
			<thead>
				<tr class="trheader">
					<th>#</th>
					<th>Time Slot</th>
					<th>Ticket Slot 1</th>
					<th>Ticket Slot 2</th>
					<th>Ticket Slot 3</th>
				</tr>
			</thead>
			<tbody id="tbodyforticekt"></tbody>
		</table>
		</div>
</div>

<div class="center-panel">
	<section class="panel">
		<header class="panel-heading for-heading">
			<div class="btn-holder">
        <button id="openModel" class="btn btn-success"> <i class="fa fa-search"></i> Search</button>
  			<a class="btn btn-primary" href="?module=schedules&action=add_schedules"> <i class="fa fa-calendar"></i> Create Schedule</a>
  			<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
      </div>
			<h2 class="panel-title">List of Schedule</h2>
		</header>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-hover mb-none" id="amcTable" style="font-size:13px">
				<thead>
					<tr>
						<th>No.</th>
						<th>Day</th>
						<!-- <th>Created by</th> -->
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($schedulelist as $id=>$R) { ?>
						<tr>
							<td width="80px"><?=$id+1?></td>
							<td><?=fDate($R['doc'])?></td>
              <!-- <td><?=$R['name']?></td> -->
              <td style="text-align:center">
                <div class="btn-group dropleft">
    						  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    						    <i class="fa fa-list"></i>
    						  </button>
                  <div class="dropdown-menu">
                    <a href="#" onclick="getMyFullschedule(<?=$R['id']?>);" class="dropdown-item"><i class="fa fa-calendar"></i> View Schedule</a>
    								<a class="dropdown-item" target="_blank" href="?module=schedules&action=printSchedule&scheduleid=<?=$R['id']?>" title="Print Schedule"><i class="fa fa-print"></i> Print Schedule</a>
    								<a class="dropdown-item" href="?module=schedules&action=add_schedules&scheduleid=<?=$R['id']?>" title="Edit Schedule"><i class="fa fa-pencil"></i> Edit Schedule</a>
                  </div>
                </div>
              </td>
            </tr>

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
			function getMyFullschedule(scheduleid){
				$('.ticketholder').show('slow');
        var slot1 = "",slot2="",slot3="";
        //console.log(scheduleid);

				$.get("?module=schedules&action=myfullschedules&format=json&scheduleid="+scheduleid, null, function(d){
		      var data = eval(d);
					if(data[0].status == "found"){
						$('#tbodyforticekt').empty();
						$.each(data[0].results, function(index, schedules){
							count = parseInt(index) + 1;
              if(schedules.slot_1 == "" || schedules.slot_1 == null){
                slot1 = "<i class='fa fa-bed'></i>";
              }else{
                slot1 = schedules.slot_1;
              }

              if(schedules.slot_2 == "" || schedules.slot_2 == null){
                slot2 = "<i class='fa fa-bed'></i>";
              }else{
                slot2 = schedules.slot_2;
              }

              if(schedules.slot_3 == "" || schedules.slot_3 == null){
                slot3 = "<i class='fa fa-bed'></i>";
              }else{
                slot3 = schedules.slot_3;
              }

              var tableRow = "";

              if(schedules.type == "lunch"){
                tableRow ="<tr class='scheduletr lunch'><td>"+index+"</td>"+
        					"<td>"+schedules.time+" LUNCH TIME <i class='fa fa-coffee'></i></td>"+
                  "<td>"+schedules.time+" LUNCH TIME <i class='fa fa-coffee'></i></td>"+
                  "<td>"+schedules.time+" LUNCH TIME <i class='fa fa-coffee'></i></td>"+
        					"<td>"+schedules.time+" LUNCH TIME <i class='fa fa-coffee'></i></td></tr>";
              }else{
                tableRow ="<tr class='scheduletr'><td>"+index+"</td>"+
        					"<td>"+schedules.time+" <i class='fa fa-bell'></i></td>"+
                  "<td>"+slot1+"</td>"+
                  "<td>"+slot2+"</td>"+
        					"<td>"+slot3+"</td></tr>";
              }

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
				 dom: '<"top"f>t<"bottom"ip>',
				 colReorder:true,
				 keys:true,
				 // buttons: [
					//  'copyHtml5', 'excelHtml5', 'pdfHtml5','csvHtml5','print'],
				 <?if($_GET['status']){?>
				 title:'<?=$_GET['status']?>',
				 <?}?>
			 });
     })

		</script>
