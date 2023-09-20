<style media="screen">
	.center-panel{
		width:70%;
		margin:0 auto;
	}
	.hierarchic-holder {
    padding: 10px;
    border-bottom: 1px solid;
    background: #ecedf0;
    margin-top: 10px;
}
.hierarchic-header{
	margin-top: 10px;
}
.for-product-save {
    margin-top: 11px;
}
.hierarchic-header label{
	font-size:16px;
}
.for-row{
	padding:5px;
}
.btn-holder{
	float:right;
	margin-top:-20px;
}
.tb-header h5{
	font-weight:600;
	text-align:center;
}
.slot {
	margin-top: 6px;
}
.scheduledate {
    position: absolute;
    top: 3%;
    right: 22%;
    font-weight: 700;
}
.num-hide {
    display: block;
    background: white;
    width: 32px;
    height: 28px;
    position: absolute;
    right: 21px;
    margin-top: 5px;
}
span.error-display {
    text-align: center;
    display: inline-block;
    color: red;
}
#forsave-btn{
	display:none;
}
</style>
<header class="page-header">
	<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> Schedule</h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="panel center-panel">
			<header class="panel-heading">
				<h2 class="panel-title"><i class="fa fa-calendar"></i> Schedule</h2>
				<div class="btn-holder">
					<a href="?module=home&action=index" class="btn btn-primary"><i class="fa fa-home"></i></a>
				</div>
			</header>
			<div class="panel-body">
				<form autocomplete="off" id="form" class="form-horizontal form-bordered" method="post" action="<?=url('schedules','save_schedule')?>">
					<input type="hidden" autocomplete="false">
					<div class="row tb-header">
						<div class="col-md-3">
							<h5>Time Slot</h5>
						</div>
						<div class="col-md-3">
							<h5>Ticket Slot 1</h5>
						</div>
						<div class="col-md-3">
							<h5>Ticket Slot 2</h5>
						</div>
						<div class="col-md-3">
							<h5>Ticket Slot 3</h5>
						</div>
					</div>

					<?php if ($scheduleDetails){?>
						<input type="hidden" name="scheduleid" value="<?=$scheduleid?>">
						<h5 class="scheduledate">Created On: <?=fDate($scheduleDetails[1]['date']);?></h5>
						<?php foreach ($timeslots as $index => $time){ ?>

							<?php if ($time['type'] == 'lunch'){ ?>

								<div class="row slot">
									<div class="col-md-12">
										<input style="text-align:center" disabled readonly type="text" placeholder="Time" class="form-control" name="" value="<?=$time['name']?> LUNCH TIME">
										<input type="hidden" readonly name="timeslotid[]" value="<?=$time['id']?>">
									</div>
								</div>

							<?php }else{ ?>

								<div class="row slot">
									<div class="col-md-3">
										<input type="text" readonly placeholder="Time" class="form-control" name="time[]" value="<?=$time['name']?>">
										<input type="hidden" readonly name="timeslotid[]" value="<?=$time['id']?>">
									</div>
									<div class="col-md-3 slotinput">
										<span class="num-hide"></span>
										<input onblur="verifyTicketNumber(this);" type="number" placeholder="Ticket 1" class="form-control" data-timeslotid="<?=$time['id']?>" name="ticketime[<?=$time['id']?>][1]" value="<?=$scheduleDetails[$time['id']][1]?>">
										<span class="error-display"></span>
									</div>
									<div class="col-md-3 slotinput">
										<span class="num-hide"></span>
										<input onblur="verifyTicketNumber(this);" type="number" placeholder="Ticket 2" class="form-control" data-timeslotid="<?=$time['id']?>" name="ticketime[<?=$time['id']?>][2]" value="<?=$scheduleDetails[$time['id']][2]?>">
										<span class="error-display"></span>
									</div>
									<div class="col-md-3 slotinput">
										<span class="num-hide"></span>
										<input onblur="verifyTicketNumber(this);" type="number" placeholder="Ticket 3" class="form-control" data-timeslotid="<?=$time['id']?>" name="ticketime[<?=$time['id']?>][3]" value="<?=$scheduleDetails[$time['id']][3]?>">
										<span class="error-display"></span>
									</div>
								</div>

							<?php }; ?>

						<?php }; ?>

					<?php }else{ ?>

						<?php foreach ($timeslots as $index => $time){ ?>

							<?php if ($time['type'] == 'lunch'){ ?>

								<div class="row slot">
									<div class="col-md-12">
										<input style="text-align:center" disabled readonly type="text" placeholder="Time" class="form-control" name="" value="<?=$time['name']?> LUNCH TIME">
										<input type="hidden" readonly name="timeslotid[]" value="<?=$time['id']?>">
									</div>
								</div>

							<?php }else{ ?>

								<div class="row slot">
									<div class="col-md-3">
										<input type="text" readonly placeholder="Time" class="form-control" name="time[]" value="<?=$time['name']?>">
										<input type="hidden" readonly name="timeslotid[]" value="<?=$time['id']?>">
									</div>
									<div class="col-md-3 slotinput">
										<span class="num-hide"></span>
										<input onblur="verifyTicketNumber(this);" type="number" placeholder="Ticket 1" class="form-control" data-timeslotid="<?=$time['id']?>" name="ticketime[<?=$time['id']?>][1]" value="">
										<span class="error-display"></span>
									</div>
									<div class="col-md-3 slotinput">
										<span class="num-hide"></span>
										<input onblur="verifyTicketNumber(this);" type="number" placeholder="Ticket 2" class="form-control" data-timeslotid="<?=$time['id']?>" name="ticketime[<?=$time['id']?>][2]" value="">
										<span class="error-display"></span>
									</div>
									<div class="col-md-3 slotinput">
										<span class="num-hide"></span>
										<input onblur="verifyTicketNumber(this);" type="number" placeholder="Ticket 3" class="form-control" data-timeslotid="<?=$time['id']?>" name="ticketime[<?=$time['id']?>][3]" value="">
										<span class="error-display"></span>
									</div>
								</div>

							<?php }; ?>

						<?php }; ?>

					<?php }; ?>

					<div class="row for-product-save" id="default-btn">
						<div class="col-md-12">
							<a href="?module=schedules&action=my_schedules" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-calendar"></i> Back to list</a>
						</div>
					</div>

					<div class="row for-product-save" id="forsave-btn">
						<div class="col-md-6">
							<a href="?module=schedules&action=my_schedules" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-calendar"></i> Back to list</a>
						</div>
						<div class="col-md-6">
							<button id="saveSchedule" type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save Schedule</button>
						</div>
					</div>

				</form>
			</div>
		</section>
	</div>
</div>
<script>
	$(function(){
		$("#name").focus();
		$('#ticketnumber').multipleInput();
		//format the time schedule
		$('input.timepicker').timepicker({
			timeFormat:'hh:mm',
			dropdown:false,
			scrollbar:false
		});
	})

	function showError(input,isfocus,element,message){
		if(isfocus == 'yes'){
			$(input).focus();
		}
		element.text(message);
		setTimeout(function(){
			element.empty();
		},3000);
		triggerError(message);
		$("#forsave-btn").hide();
		$("#default-btn").show();
	}

	function successTicket(input,title,element,message){
		$(input).attr('title',title);
		element.text(message);
		setTimeout(function(){
			element.empty();
		},3000);
		triggerMessage(message)
		$("#forsave-btn").show();
		$("#default-btn").hide();
	}

	function verifyTicketNumber(obj){
		var ticketNumber = $(obj).val();
		var errorspan = $(obj).closest('.slotinput').find('.error-display');
		$.get("?module=schedules&action=verifyticketForSchedule&format=json&ticketnumber="+ticketNumber, null, function(result){
			var report = eval(result);
			report = report[0];
			//console.log(report);
			 if(report.result == 'found'){
				 if(report.statusname == "Completed"){
					 showError(obj,'yes',errorspan,'Ticket Number is completed');
				 }else{
					 if(report.isverified == 'yes'){
						 showError(obj,'yes',errorspan,'Ticket Number verified or closed');
					 }else{
						 if(report.isInmyDepartment == "yes"){
							 if(report.isAssignedTome == "yes"){
								 //console.log(report);
								 successTicket(obj,report.problem,errorspan,"Ticket number is pending, product "+report.product);
							 }else{
								 showError(obj,'yes',errorspan,'Ticket Number is not assigned to you, assigned to '+report.assignedtoName);
							 }
						 }else{
							 showError(obj,'yes',errorspan,'Ticket Number is not in your department > '+report.deptname);
						 }
					 }
				 }
			}else if(report.result == 'not found'){
				showError(obj,'no',errorspan,'Ticket Number not found');
			}

		});
	}
</script>
