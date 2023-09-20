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
.btn-align{
	float:right;
	position:relative;
	top:-25px;
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
	height: 213px;
	-webkit-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	-moz-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	}
.row.top-row {
    margin-top: 10px;
}
</style>
<header class="page-header">
	<h2>Tickets for Feedback</h2>
</header>

<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="crms">
		<input type="hidden" name="action" value="feedback_index">
		<div id="filter_table">
			<?php if ($userole == R_ADMIN){?>
				<div class="row">
					<div class="col-md-12">
						<select id="departId" class="form-control for-input" name="departmentid">
							<?php if ($departid){?>
								<option value="<?=$departid?>"><?=$departname?></option>
							<?php }else {?>
								<option value="" selected disabled>--Department--</option>
							<?php }?>
						</select>
					</div>
				</div>
			<?php }; ?>
			<div class="row">
				<div class="col-md-4">
					<select id="clientid" class="form-control for-input" name="clientid">
						<?php if ($clientid){?>
							<option value="<?=$clientid?>"><?=$clientname?></option>
						<?php }else {?>
							<option value="" selected disabled>--Client Name--</option>
						<?php }?>
					</select>
				</div>
				<div class="col-md-4">
					<input type="text" placeholder="Ticket Number" name="ticketno"  class="form-control for-input" value="<?=$ticketno?>">
				</div>
				<div class="col-md-4">
					<select class="form-control for-input" name="feedbackstatus">
							<option value="" selected disabled>--Feedback Status--</option>
							<option value="yes" <?=selected($feedbackstatus,'yes')?>>Yes</option>
							<option value="no" <?=selected($feedbackstatus,'no')?>>No</option>
						</select>
				</div>
			</div>
			<div class="row top-row">
				<div class="col-md-4">
					<div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
				</div>
				<div class="col-md-4">
					<a href="?module=crms&action=feedback_index" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET</a>
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
		<header class="panel-heading for-heading">
			<h2 class="panel-title">
        <span><i class="fa fa-file"></i> List of Ticket</span>
      </h2>
			<div class="btn-align">
				<button id="openModel" class="btn btn-success"> <i class="fa fa-search"></i> Search</button>
				<a href="?module=crms&action=make_fedback" class=" btn btn-primary">Make Fedback</a>
			</div>
		</header>
	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px">
		<thead>
			<tr>
				<th>SN.</th>
				<th>Ticket No.</th>
				<th>Serial No.</th>
				<th>Product</th>
				<th>Department</th>
				<th>Branch</th>
				<th>Client Name</th>
				<th>Support Type</th>
				<th>Closed Date</th>
				<th>Verified Date</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($tickets as $id=>$T) { ?>
				<tr>
					<td><?=$id+1?></td>
					<td style="text-align:center;font-size:15px;font-weight:600"><?=$T['ticketId']?></td>
					<td><?=$T['serialno']?></td>
					<td><?=$T['productname']?></td>
					<td><?=$T['departname']?></td>
					<td><?=$T['branchname']?></td>
					<td><?=$T['client']?></td>
					<td><?=$T['supportname']?></td>
					<td><?=fDate($T['doclose'])?></td>
					<td><?=fDate($T['dov'])?></td>
          <td>
						<?php if ($T['hasfeedback']){?>
							<span>Has Feeback</span>
						<?php }else{?>
							<a class="btn btn-success" href="?id=<?=$T['ticketId']?>&module=crms&action=make_fedback" title="Make feedback"><i class="fa fa-edit"></i></a>
						<?php }?>
						<!-- <a class="btn btn-primary" target="_blank" href="?module=crms&action=print_feedback&id=<?=$T['ticketId']?>" title="Print Feedback report"><i class="fa fa-print"></i></a> -->
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
		$('#openModel').on('click', function (){
			$('#formHolder').show('slow');
			$('#formModel').show('slow');
		});
		$('#closeSearchModel').on('click', function (){
			$('#formHolder').hide('slow');
			$('#formModel').hide('slow');
		});

		$(document).ready(function(){
		 $('#userTable').DataTable({
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
