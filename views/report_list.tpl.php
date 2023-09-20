<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
div.dataTables_wrapper div.dataTables_filter input {
    width: 100%;
}
div.dataTables_wrapper div.dataTables_filter input{
  margin-left:0;
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
	height: 402px;
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
</style>
<header class="page-header">
	<h2>Ticket Report</h2>
</header>
<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="reports">
		<input type="hidden" name="action" value="index">
		<div id="filter_table">
			<?if($_SESSION['member']['roleid'] == R_MASTER){?>
				<div class="row">
					<div class="col-md-12">
						<!-- <h5></h5> -->
						<select class="form-control for-input" name="brach">
								<option value="" select disabled>Brach</option>
								<? foreach ($braches as $b){?>
									<option <?=selected($b['id'],$brach)?> value="<?=$b['id']?>"><?=$b['name']?></option>
									<!-- <?//if ($r['id'] == $statusid) echo 'selected';?> -->
								<?}?>
							</select>
					</div>
				</div>
			<?}?>
			<?if(IS_ADMIN){?>
        <!-- for management -->
        <p>For Management</p>
        <div class="col-md-12 for-formanage">
          <div class="row">
            <div class="col-md-6">
              <select class="form-control for-input" name="brach">
                  <option value="" selected disabled>Branch</option>
                  <? foreach ($braches as $b){?>
                    <option <?=selected($b['id'],$brach)?> value="<?=$b['id']?>"><?=$b['name']?></option>
                  <?}?>
              </select>
            </div>
            <div class="col-md-6">
              <select id="departId" class="form-control for-input" name="department">
                  <option value="">Department</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <h5>AMC Status</h5>
              <select id="amcstatus" class="form-control for-input" name="amcstatus">
                  <option value="" selected disabled>--Choose Status--</option>
                  <option <?=selected($amcstatus,'yes')?> value="yes">Yes AMC</option>
                  <option <?=selected($amcstatus,'no')?> value="no">No AMC</option>
                  <option <?=selected($amcstatus,'expired')?> value="expired">AMC Expired</option>
              </select>
            </div>
            <div class="col-md-4">
              <h5>Warranty Status</h5>
              <select id="amcstatus" class="form-control for-input" name="warrantystatus">
                  <option value="" selected disabled>--Choose Status--</option>
                  <option <?=selected($warrantystatus,'yes')?> value="yes">Yes Warranty</option>
                  <option <?=selected($warrantystatus,'no')?> value="no">No Warranty</option>
                  <option <?=selected($warrantystatus,'expired')?> value="expired">Expired Warranty</option>
              </select>
            </div>
            <div class="col-md-4">
              <h5>Invoice Status</h5>
              <select id="invoicestatus" class="form-control for-input" name="invoicestatus">
                  <option value="" selected disabled>--Choose Status--</option>
                  <option <?=selected($invoicestatus,'yes')?> value="yes">Yes</option>
                  <option <?=selected($invoicestatus,'no')?> value="no">No</option>
              </select>
            </div>
          </div>
        </div>
        <!-- end for management -->
			<?}?>
			<div class="row">
				<div class="col-md-3">
					<!-- <h5></h5> -->
					<select class="form-control for-input" name="supportype">
							<option value="" selected disabled>Support Type</option>
							<? foreach ($supportypes as $r){?>
								<option <?=selected($r['id'],$supportype)?> value="<?=$r['id']?>"><?=$r['name']?></option>
								<!-- <?//if ($r['id'] == $statusid) echo 'selected';?> -->
							<?}?>
						</select>
				</div>
				<div class="col-md-3">
					<input class="form-control for-input" type="text" name="serialno" value="" placeholder="Serial Number">
				</div>
				<div class="col-md-3">
					<input class="form-control for-input" type="text" name="ticketid" value="<?=$ticketid?>" placeholder="Ticket Number">
				</div>
				<div class="col-md-3">
					<!-- <h5></h5> -->
					<select class="form-control for-input" name="type">
							<option value="" selected disabled>Status</option>
							<? foreach ($statuses as $r){?>
								<option <?=selected($r['id'],$type)?> value="<?=$r['id']?>"><?=$r['name']?></option>
								<!-- <?//if ($r['id'] == $statusid) echo 'selected';?> -->
							<?}?>
						</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<!-- <h5>Client</h5> -->
					<select id="clientid" class="form-control" name="client">
						<?php if ($client){?>
							<option value="<?=$client?>"><?=$clientname?></option>
						<?php }else {?>
							<option value="" selected disabled>Client Name</option>
						<?php }?>
					</select>
				</div>
				<div class="col-md-6">
					<!-- <h5></h5> -->
					<select class="form-control for-input" name="user">
							<option value="" selected disabled>Users</option>
							<? foreach ($users as $r){?>
								<option <?=selected($r['id'],$user)?> value="<?=$r['id']?>"><?=$r['name']?></option>
								<!-- <?//if ($r['id'] == $statusid) echo 'selected';?> -->
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
					<button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i> SEARCH</button>
				</div>
				<div class="col-md-4">
					<a href="?module=reports&action=index" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET</a>
				</div>
				<div class="col-md-4">
					<div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
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
			<h2 class="panel-title">Ticket Reports</h2>
		</header>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
					<thead>
						<tr>
							<th>#</th>
							<th>Ticket</th>
							<th>Serial No</th>
							<th>Client Name</th>
							<th>Branch</th>
							<th>Product</th>
							<th>Depart</th>
              <?if(IS_ADMIN){ ?>
                <!-- <th>Invoice No.</th> -->
                <th>Invoice Ammount</th>
                <th>Type</th>
                <th>Assigned To</th>
              <?php }else{ ?>
                <th>Assigned By</th>
                <th>Assigned To</th>
              <?php }?>
							<th>Created On</th>
							<th>Support Type</th>

              <?if(IS_ADMIN){ ?>
                <th>Verify Status</th>
              <?php }?>

							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

						<?php $count=1;
							foreach($listData as $ins=>$list) {?>

							<tr style="background-color:<?=$R['color']?>">
								<td><?=$count?></td>
								<td><?=$list['id']?></td>
								<td><?=$list['serialno']?></td>
								<td><?=$list['client']?></td>
								<td><?=$list['branchname']?></td>
								<td><?=$list['productname']?></td>
								<td><?=$list['deptname']?></td>

                <?if(IS_ADMIN){ ?>

                  <!-- <td><?if($list['invoiceno'])echo $list['invoiceno']; else echo 'Null';?></td> -->
                  <td><?=formatN($list['amount'])?></td>
                  <td style="text-transform:capitalize"><?=$list['type']?></td>
                  <td><?=$list['assigname']?></td>

                <?php }else{ ?>

                  <td><?=$list['assigname']?></td>
  								<td><?=$list['assignby']?></td>

                <?php }?>
								<td><?=fDate($list['doc'])?></td>
								<td style="text-transform:capitalize"><?=$list['supportype']?></td>
                <?if(IS_ADMIN){ ?>
                  <td style="text-transform:capitalize"><?if($list['isverified']) echo 'verified'; else echo 'Not verified';?></td>
                <?php }?>

								<td><?=$list['statusname']?></td>
								<td><a title="Full Report" href="?module=reports&action=full_report&id=<?=$list['id']?>" target="_blank"> <i class="fa fa-file"></i> </a> </td>
							</tr>
							<?php $count++;}?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>
<script type="text/javascript">
$(function(){
	//$('#clientid').select2({minimumInputLength:3});
	//$('#productsid').select2({minimumInputLength:3});
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

function printData()
{
   var tabletoprint=document.getElementById("printing_area");
   newWin= window.open("");
   newWin.document.write(tabletoprint.outerHTML);
   newWin.print();
   newWin.close();
}

$('#printBtn').on('click',function(){
printData();
})
$('#openModel').on('click', function (){
	$('#formHolder').show('slow');
	$('#formModel').show('slow');
	/*$('html, body').css({
    overflow: 'hidden',
    height: '100%'
	});*/
})

$('#closeSearchModel').on('click', function (){
	$('#formHolder').hide('slow');
	$('#formModel').hide('slow');
	/*$('html, body').css({
    overflow: 'auto',
    height: 'auto'
	});*/
})
$(document).ready(function(){
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
})

</script>
