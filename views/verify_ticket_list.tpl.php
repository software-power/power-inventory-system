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
	height: auto;
	-webkit-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	-moz-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	}
.input-group-rounded input.form-control:first-child, .input-group-rounded input.form-control:last-child, .input-search input.form-control:first-child, .input-search input.form-control:last-child {
		border-radius: 0;
		height: 44px;
		font-size: 15px;
}
	/*.btn{
		padding: 4px 4px;
    font-size: 11px;
	}*/
	.table .actions-hover a{
		color:#ffffff;
	}
#model-container,#bg-model{display:none}
#model-container .mfp-container:before {
    content: '';
    display: inline-block;
    height: 100%;
    vertical-align: middle;
}
#model-container .mfp-content {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    margin: 0 auto;
    text-align: left;
    z-index: 10001;
		top:-247%;
}
@media (max-width: 575px) {
	.mfp-content{
	    top:-424px;
	}

}

#model-container .modal-block {
    background: transparent;
    padding: 0;
    text-align: left;
    max-width: 600px;
    margin: 40px auto;
    position: relative;
}
#model-container .card {
    position: relative;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background:transparent;
    /* background-clip: border-box; */
    border: none;
		box-shadow:none;
    border-radius: 0.25rem;
}
#model-container .card-header {
    background: #f6f6f6;
    border-radius: 5px 5px 0 0 !important;
    border-bottom: 1px solid #DADADA;
    padding: 18px;
    position: relative;
}
#model-container .card-header .card-title {
    color: #33353F;
    font-size: 20px;
    font-weight: 400;
    line-height: 20px;
    padding: 0;
    text-transform: none;
    margin: 0;
}
#model-container .card-body {
    background: #fdfdfd;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    /* border-radius: 5px; */
}
#model-container .card-body h4{margin-left: 19px;}
#model-container .modal-wrapper {
    position: relative;
    padding: 17px 0;
    border-bottom: 1px solid #9e9e9e75;
}
#model-container .card-footer {
	border-radius: 0 0 5px 5px;
	margin-top: -5px;
	height: 63px;
	padding: 17px;
	background: #FFF;
}
#model-container .modal-block-danger .fa {color:#ffffff;}
#model-container .modal-icon {
    float: left;
    width: 13%;
    text-align: center;
}
#model-container .modal-icon .fa{font-size: 38px;color:#cccccc;}
.hide-message{display:none}
.show-message{display:block}
.for-btn {
    padding: 10px;
    border-top: 1px solid #eeeeee;
    height: 42px;
}
button.btn.btn-default {
    font-size: 18px;
    padding-right: 15px;
}
.btn-home {
    padding: 8px;
    font-size: 24px;
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
.form-search{
	float:right;
}
</style>
<header class="page-header">
	<h2>Tickets for Verification</h2>
</header>
<!-- filter start here -->
<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="tickets">
		<input type="hidden" name="action" value="verify_ticket">
		<div id="filter_table">
      <div class="row">
        <div class="col-md-12">
					<select id="clientid" class="form-control" name="clientid">
						<?php if ($clientid){?>
							<option value="<?=$clientid?>"><?=$clientname?></option>
						<?php }else {?>
							<option value="" selected disabled>Client Name</option>
						<?php }?>
					</select>
				</div>
      </div>
			<div class="row">
				<div class="col-md-4">
					<select id="sales_status" class="form-control for-input" name="vstatus">
						<option value="" selected disabled>--Verify Status--</option>
						<option <?=selected($vstatus,'yes')?> value="yes">Verified</option>
						<option <?=selected($vstatus,'not')?> value="not">Not Verified</option>
					</select>
				</div>
				<div class="col-md-4">
					<input type="text" class="form-control for-input" name="ticketno" placeholder="Ticket Number" value="<?=$ticketno?>">
				</div>
				<div class="col-md-4">
					<input type="text" class="form-control for-input" name="serialno" placeholder="Serial Number" value="<?=$serialno?>">
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
					<a href="?module=tickets&action=verify_ticket" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET</a>
				</div>
        <div class="col-md-4">
					<button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i> SEARCH</button>
				</div>
			</div>
		</div>
	</form>
</div>
<!-- filter end here -->

<div id="bg-model" class="mfp-bg mfp-ready"></div>
<div id="model-container" class="mfp-container mfp-s-ready mfp-inline-holder">
	<div class="mfp-content">
		<div id="modalDanger" class="modal-block modal-block-danger">
			<section class="card">
				<header class="card-header">
					<h2 class="card-title">
						<div class="modal-icon">
							<i class="fa fa-envelope"></i>
						</div>
						<span>Notify the Client</span>
				</h2>
				</header>
				<div class="card-body">
					<div class="modal-wrapper">
						<div class="modal-text">
							<table class="crm_table" width="80%" style="margin:0 auto">
								<form id="message_form" action="?state_module=tickets&state_action=verify_ticket&module=messages&action=send_message" method="post">
									<input id="jobcard_id" type="hidden" name="message[jobcardid]" readonly class="form-control mb-md">
									<tr>
										<td width="100px">Name</td>
										<td width="200px">
											<input id="client_name" type="text" name="message[name]" readonly class="form-control mb-md">
										</td>
									</tr>
									<tr>
										<td width="100px">Mobile (<i>Collector</i>)</td>
										<td width="200px">
											<input onchange="showField(this)" id="client_mobile" type="text" name="message[mobile]" placeholder="255000000000" class="form-control mb-md">
										</td>
									</tr>
									<tr>
										<td width="100px">Email (<i>Client</i>)</td>
										<td width="200px">
											<input onchange="showField(this)" id="client_email" type="text" name="message[email]" class="form-control mb-md">
										</td>
									</tr>
									<tr>
										<td width="100px">Message (<i>Optional</i>)</td>
										<td width="200px">
												<textarea id="kiswahili"  readonly class="form-control mb-md" name="message[kiswahili]" rows="4" cols="80"></textarea>
												<textarea id="english"  readonly class="form-control mb-md" name="message[english]" rows="4" cols="80"></textarea>
										</td>
									</tr>
									<tr>
										<td width="100px">language</td>
										<td width="200px">
											<span><input type="radio" name="message[language]" value="eng"> English</span>
											<span><input type="radio" name="message[language]" value="ksw"> Kiswahili</span>
										</td>
									</tr>
									<tr>
										<td width="100px">Channel Option</td>
										<td width="200px">
											<span id="for_sms"><input id="sms" type="radio" name="message[media]" value="sms"> SMS</span>
											<span id="for_email"><input id="email" type="radio" name="message[media]" value="email"> EMAIL</span>
											<span id="for_sms_email"><input id="both" type="radio" name="message[media]" value="both"> Both</span>
											<!-- <span id="for_alert" class="hide-message"></span> -->
										</td>
									</tr>
									<tr class="for-btn">
										<td></td>
										<td style="float:right;padding:10px;">
											<button type="submit" class="btn btn-primary"> <i class="fa fa-send"></i> SEND MESSAGE</button>
											<span onclick="closeModel(this)" id="model-dismis" class="btn btn-danger modal-dismiss"> <i class="fa fa-close"></i> CLOSE</span>
										</div>
										</td>
									</tr>
								</form>
							</table>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading">
			<div class="row">
				<div class="col-md-3">
					<h2 class="panel-title">List of Completed Tickets</h2>
				</div>
				<div class="form-search">
					<button id="openModel" class="btn btn-success"> <i class="fa fa-search"></i> Open Filter</button>
					<a class="btn btn-primary btn-home" href="?module=home&action=index"> <i class="fa fa-home"></i> </a>
				</div>
			</div>
		</header>

	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px;">
		<thead>
			<tr>
				<th>No.</th>
				<th>Ticket</th>
				<th>Serial No</th>
				<th width="90px">Client</th>
				<th>Assigned</th>
				<th>Product</th>
				<th>Time Spent</th>
				<!-- <th>Invoice No</th> -->
				<!-- <th>Invoice Amount</th> -->
				<th>Status</th>
				<th>Closed Date</th>
				<th>Verified Date</th>
				<th>Verified</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($tickets as $id=>$R) { ?>
        <!-- <tr style='background-color: <?=$R['color']?>'> -->
				<tr class="activeclient_row">
					<td width="80px"><?=$id+1?></td>
					<td width="80px" class="activeclient_id"><strong><?=$R['id']?></strong></td>
					<td class="activeclient_serial"><?=$R['serialno']?></td>
					<td style="display:none;" class="activeclient_email"><?=$R['clientemail']?></td>
					<td style="display:none;" class='activeclient_mobile'><?=$R['contactmobile']?></td>
					<td width="90px" class="activeclient_name"><?=$R['clientname']?></td>
					<td><?=$R['technician']?></td>
					<td><?=$R['product']?></td>
					<td><?if($R['time'] == "") echo 'Null'; else echo date('H:i', mktime(0,$R['time']));?></td>
					<!-- <td><?=$R['invoicenumber']?></td> -->
					<!-- <td><?=$R['invoiceamount']?></td> -->
					<td><?=ucwords($R['status'])?></td>
					<td><?=$R['doclose']?></td>
					<td><?=$R['dov']?></td>
					<td><?if($R['verification'] == 0){echo "No"; }else if($R['verification'] == 1){ echo "Yes";}?></td>
					<!-- class="actions-hover actions-fade" -->
					<td class="dactions-hover dactions-fade"  style="width: 100px">
						<div class="btn-group dropleft">
							<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-list"></i>
							</button>
							<div class="dropdown-menu">
						<?if($R['isverified'] == 1 && $R['status'] == 'Completed'){?>
							<span class="dropdown-item"><i title="Job Closed" class="fa-ban fa"></i> Ticket Closed</span>
							<a class="dropdown-item" target="_blank" onclick="alert('You are about to print delivery note')" href="?module=tickets&action=delivery_note&jobcard=<?=$R['id']?>" title="delivery note"><i class="fa fa-file"></i> Delivery Note</a>

							<?php if ($head == 1){?>

								<?if($R['department'] == 'EFD'){?>
									<a class="dropdown-item" href="<?=url('tickets','jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
								<?}else if($R['department'] == 'POWERSECURE'){?>
									<a class="dropdown-item" href="<?=url('tickets','powersecure_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
								<?}else if($R['department'] == 'ICT'){?>
									<?if($R['supportedwith'] == 'inhouse'){?>
										<a class="dropdown-item" href="<?=url('tickets','inhouse_ict_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
									<?}else if($R['supportedwith'] == 'sitevisit'){?>
										<a class="dropdown-item" href="<?=url('tickets','sitevisit_ict_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
									<?}?>
								<?}else if($R['department'] == 'Tally'){?>
									<a class="dropdown-item" href="<?=url('tickets','tally_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
								<?}?>

							<?php }else if($role == 1){;?>

								<?if($R['department'] == 'EFD'){?>
									<a class="dropdown-item" href="<?=url('tickets','jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
								<?}else if($R['department'] == 'POWERSECURE'){?>
									<a class="dropdown-item" href="<?=url('tickets','powersecure_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
								<?}else if($R['department'] == 'ICT'){?>
									<?if($R['supportedwith'] == 'inhouse'){?>
										<a class="dropdown-item" href="<?=url('tickets','inhouse_ict_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
									<?}else if($R['supportedwith'] == 'sitevisit'){?>
										<a class="dropdown-item" href="<?=url('tickets','sitevisit_ict_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
									<?}?>
								<?}else if($R['department'] == 'Tally'){?>
									<a class="dropdown-item" href="<?=url('tickets','tally_jobcard','id='.$R['id'])?>" target="_blank" title="Job Card"><i class="fa fa-file"></i> Job Card</a>
								<?}?>

							<?php };?>

						<?}else{?>
							<a href="#" id="activate_sms" onclick="getClientDetails(this)" class="dropdown-item" title="Client Notify"><i class="fa fa-envelope"></i> Notify Client</a>
							<a class="dropdown-item" onclick="alert('You are about to verify the ticket?')" href="?module=tickets&action=verify_ticket_process&id=<?=$R['id']?>" title="verify"><i class="fa fa-check"></i> Verify Ticket</a>
							<a class="dropdown-item" href="<?=url('tickets','ticket_edit','id='.$R['id'])?>&state=verifying" title="Edit"><i class="fa-pencil fa"></i> Edit Ticket</a>
						<?}?>
					</div>
				</div>

					</td>
				<?php } ?>
			</tbody>
		</table>
		</div>
		<script type="text/javascript">
		$(function(){
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
			//get table row data
			function getClientDetails(obj) {
				var tableRow = $(obj).closest(".activeclient_row");
				var id = tableRow.find('.activeclient_id').html();
				var email = tableRow.find('.activeclient_email').html();
				var mobile = tableRow.find('.activeclient_mobile').html();
				var name = tableRow.find('.activeclient_name').html();
				var serial = tableRow.find('.activeclient_serial').html();

				var kiswahili = "Mpendwa mteja machine yako yenye number "+serial+" iko teyari, job card: "+id+" powercomputers";
				var english = "Our Dear customer your product with serial no "+serial+" is ready, job Card no: "+id+" powercomputers";

				//insert into message box
				$('#jobcard_id').val(id);
				$('#client_name').val(name);
				$('#client_mobile').val(mobile);
				$('#client_email').val(email);
				$('#kiswahili').val(kiswahili);
				$('#english').val(english);

				// show the model
				$(this).on('click',function(){
					$('#bg-model').show();
					$('#model-container').show();
				});

				if ($.trim($('#client_mobile').val()) === "" && $.trim($('#client_email').val()) === "") {
					alert('Please enter at least one option, the mobile or email this client "'+name+'" does not have this information');
				}

				/*if ($.trim($('#client_mobile').val()) === "" && $.trim($('#client_email').val()) === "") {
					$('#for_sms').hide();
					$('#for_email').hide();
					$('#for_sms_email').hide();

					alert('Please enter at least one option, the mobile or email this client "'+name+'" does not have this information');

				}else if ($.trim($('#client_mobile').val()) === "") {
					$('#for_sms').hide();
					$('#for_email').show();

				}else if ($.trim($('#client_email').val()) === "") {
					$('#for_email').hide();
						$('#for_sms').show();

				}else{
					$('#for_sms').show();
					$('#for_email').show();
					$('#for_sms_email').show();
				}*/
			}

			//close the model
			function closeModel() {
				$(this).on('click', function(){
					console.log('am coming');
					$('#bg-model').hide();
					$('#model-container').hide();
				})
			}

			function showField(obj) {
				$(obj).show();
			}
			function showEmail() {
					$('#for_email').show();
			}
			function validateEmail(email) {
			    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			    return regex.test(email);
			}

			function validMobile(number) {
				var regex = /([255])\d{1}\d{1}\d{1}\d{1}\d{1}\d{6}/;
				return regex.test(number);
			}

			$("#message_form").on('submit', function(e){
				e.preventDefault();
				var email = $('#client_email').val();
				var mobile = $('#client_mobile').val();
				// console.log(email);
				if ($('#sms').prop('checked')) {

					if (!validMobile(mobile)) {
						alert('Invalid mobile number, please add 255 instead of 0');
					}else{
						this.submit();
					}

				}else if ($('#email').prop('checked')) {

					if (!validateEmail(email)) {
						alert('Invalid email');
					}else{
							this.submit();
					}

				}else if ($('#both').prop('checked')) {

					if (!validateEmail(email)) {
						alert('Invalid email');
					}else	if (!validMobile(mobile)) {
							alert('Invalid mobile number, please add 255 instead of 0');
					}else{
						this.submit();
					}

				}else{
					alert('Please Choose a media for sending message')
				}

			})

		</script>
