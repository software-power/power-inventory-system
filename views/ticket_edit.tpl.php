<style media="screen">
	.panel-align{
		margin:0 auto;
		width: 96%;
	}
	.panel-align table tr td{
		padding:3px;
	}
	.sirinumber{
		border: 1px solid #b0b4c1;
		padding: 15px;
		display:none;
		-webkit-box-shadow: 7px -3px 60px -15px rgba(0,0,0,0.65);
-moz-box-shadow: 7px -3px 60px -15px rgba(0,0,0,0.65);
box-shadow: 7px -3px 60px -15px rgba(0,0,0,0.65);
	}
	#form h5 {
		font-size: 16px;
		font-weight: 600;
	}
	.select2-container .select2-selection--single {
	    height: 36px;
	}
	.rowtech {
		padding: 18px;
    background: #fdfdfd;
    border-radius: 5px;
    border: 2px solid #ecedf0;
}
select#msultech {
    height: 74px;
}
.rowInputTech {
    margin-top: 8px;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered{
	padding:0 5px 5px;
}
.num-hide,.num-hide-day {
    display: block;
    background: white;
    width: 32px;
    height: 28px;
    position: absolute;
    right: 21px;
    top: 40px;
}
.num-hide-day{
	top:3px;
}
.home-key{
	float:right;
}
#spare_row {
    margin-top: 10px;
}
.row.spare_row {
    margin-top: 8px;
}
.num-hide-ov{
	width: 24px;
	top: 3px;
}
.badge-danger {
    background-color: #d2322d;
}
.badge-success {
    background-color: #47a447;
}
.badge-primary {
    background-color: #0088cc;
}
</style>
<header class="page-header">
	<h2>Edit Ticket</h2>
</header>

<div class="row">
	<div class="col-lg-12">
		<section class="panel" style="width:76%;margin:0 auto">
			<header class="panel-heading">
				<div class="home-key">
					<a class="btn btn-primary" href="?module=home&action=index"><i class="fa fa-home"></i> </a>
				</div>
				<h2 class="panel-title">Ticket Details</h2>
			</header>
			<div class="panel-body">

				<div class="panel-container col-md-12">
					<div class="panel-align">
						<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('tickets','ticket_save')?>">
							<input type="hidden" name="id" value="<?=$ticket['id']?>" />
							<div class="row">
								<div class="col-md-12">
									<h5>Raised From</h5>
									<input type="text" name="ticket[raisedfrom]" value="<?=$ticket['raisedfrom']?>" readonly class="form-control mb-md">
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<h5>Contact (person) name</h5>
									<input type="text" name="ticket[contactname]" value="<?=$ticket['contactname']?>" class="form-control mb-md">
								</div>
								<div class="col-md-6">
									<h5>Contact (person) mobile</h5>
									<span class="num-hide"></span>
									<input onblur="checkmobilenumber(this)" type="number" name="ticket[contactmobile]" value="<?=$ticket['contactmobile']?>" class="form-control mb-md">
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h5>Client Name</h5>
								</div>
								<div class="col-md-6">
									<input type="text" class=" form-control" id="client" title="Client is required" style="width:100%" value="<?=$ticket['client']?>" readonly>
								</div>
								<div class="col-md-6">
									<? if (!$ticket['clientid']) { ?>
									<a target="_blank" style='position:absolute' href="<?=url('clients','client_add','ticketid='.$ticket['id'])?>" title="Add"><i class='fa fa-plus'></i></a>
									<? } ?>
									<select name="ticket[clientid]" id="clientid" class="form-control mb-md">
										<option value="<?=$clients['id']?>"><?=$clients['name']?></option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h5>Serial Number</h5>
								</div>
								<div class="col-md-6">
									<input type="text" class=" form-control" id="serialno" title="Serial no is required" style="width:100%" value="<?=$ticket['serialno']?>" readonly>
								</div>
								<div class="col-md-6">
									<? if (!$ticket['serialid']) { ?>
									<a target="_blank" style='position:absolute' href="<?=url('serials','serial_add','ticketid='.$ticket['id'])?>" title="Add"><i class='fa fa-plus'></i></a>
									<? } ?>
									<select id="getSerial" name="ticket[serialid]" class="form-control mb-md">
										<option value="<?=$serials['id']?>"><?=$serials['name']?></option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<h5>Product</h5>
									<select name="ticket[prodid]" id="productid" required class="form-control mb-md">
										<option value="<?=$ticket['prodid']?>"><?=$ticket['product']?></option>
									</select>
								</div>
								<div class="col-md-6">
									<h5>Department</h5>
									<select id="departId" name="ticket[deptid]" required class="form-control mb-md">
										<option value="<?=$ticket['deptid']?>"><?=$ticket['department']?></option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h5>Support Type</h5>
									<input type="text" class=" form-control mb-md" id="client" title="Support is required" style="width:100%" value="<?=$ticket['supporttypename']?>" readonly>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h5>Problem</h5>
									<textarea rows="4" class=" form-control" disabled ><?=$ticket['clientremark']?></textarea>
								</div>
							</div>


							<?php if ($ticket['clientid'] && $ticket['serialid']){?>

								<div class="row">
									<div class="col-md-6">
										<h5>Ticket Status</h5>
										<select onblur="getTicketStatusForSIRI(this)" name="ticket[statusid]" required class="form-control mb-md">
											<option selected disabled>--Choose Status--</option>
											<? foreach ($statuses as $r){?>
												<option <?if ($r['id'] == $ticket['statusid']) echo 'selected';?> value="<?=$r['id']?>"><?=$r['name']?></option>
											<?}?>
										</select>
										<?if($addsirinumber){?>
											<div class="sirinumber">
												<label for="sirinumber">Seal Number</label>
												<input id="sirinumber" placeholder="Seal number" required title="Please Add SIRI number" type="text" name="ticket[sirinumber]" value="" class=" form-control">
											</div>
										<?}?>
										<?if($forVerifying){?>
											<!-- <div class="sirinumber"> -->
											<label for="sirinumber">Seal Number</label>
											<input id="sirinumber" placeholder="Seal number" required title="Please Add SIRI number" type="text" name="ticket[sirinumber]" value="<?=$ticket['sirinumber']?>" class=" form-control">
										<!-- </div> -->
										<?}?>
									</div>
									<div class="col-md-6">
										<h5>Mode of Support</h5>
										<select name="ticket[supportedwith]" required class="form-control mb-md">
											<option selected disabled>--Choose Mode--</option>
											<option value="inhouse" <?=selected($ticket['supportedwith'],'inhouse')?>>In house</option>
											<option value="remote" <?=selected($ticket['supportedwith'],'remote')?>>Remote</option>
											<option value="sitevisit" <?=selected($ticket['supportedwith'],'sitevisit')?>>Site Visit</option>
										</select>
									</div>
								</div>

								<!-- ticket report start -->
								<?php if ($ticket['confirm'] == 'NonEFD'){?>

									<div id="rowtech" class="rowtech">
										<h5>Day Report / Mult-technician report</h5>
										<div class="row">
											<div class="col-md-2">
												<h5 style="text-align:center">Day</h5>
											</div>
											<div class="col-md-4">
												<h5 style="text-align:center">Description</h5>
											</div>
											<div class="col-md-2">
												<h5 style="text-align:center">Time Spent</h5>
											</div>
											<div class="col-md-3">
												<h5 style="text-align:center">Technician (s)</h5>
											</div>
											<div class="col-md-1">
												<button onclick="addNewDay();" type="button" name="button" class="btn"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div id="rowInputTechDrop">
											<?php if ($tecDetails){?>
												<!-- index we start from the last index element of the list -->
												<input id="nameindex" type="hidden" name="" value="<?=$lastIndex + 1;?>">

												<?php foreach ($tecDetails as $key => $tDetail){?>

													<input type="hidden" name="ticketdetId[]" value="<?=$tDetail['ticketdetId']?>">
													<div class="row rowInputTech">
														<div class="col-md-2">
															<input value="<?=$tDetail['date']?>" readonly type="text" placeholder="Date" class="datepicker form-control" name='date[<?=$key?>]'/>
														</div>
														<div class="col-md-4">
															<textarea placeholder="Write description.." name="remark[<?=$key?>]" rows="3" class="form-control"><?=$tDetail['remark']?></textarea>
														</div>
														<div class="col-md-2">
															<span class="num-hide-day"></span>
															<input value="<?=$tDetail['time']?>" type="number" placeholder="Minutes" class="form-control" name='time[<?=$key?>]'/>
														</div>
														<div class="col-md-3">
															<select multiple="multiple" title="Technician" id="multech" name="technicians[<?=$key?>][]" class="required multech form-control">
						                    <? foreach ($technicians as $index =>  $p){?>
																	<?php foreach ($tDetail['name'] as $i => $name){ ?>
																		<option <?if ($p['name'] == $name) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
																	<?php }; ?>
						                    <?}?>
															</select>
														</div>
														<div class="col-md-1">
															<button onclick="removeRowDay(this);" type="button" name="button" class="btn"><i class="fa fa-minus"></i> </button>
														</div>
													</div>

												<?php }?>

											<?php }else{?>
												<!-- incase no details so index we start from the one -->
												<input id="nameindex" type="hidden" name="" value="1">
											<?php }?>
											<div class="row rowInputTech">
												<div class="col-md-2">
													<input readonly type="text" placeholder="Date" class="datepicker form-control" value="<?=$todasydate?>" name='date[0]'/>
												</div>
												<div class="col-md-4">
													<textarea placeholder="Write description.." name="remark[0]" rows="3" class="form-control" value=""></textarea>
												</div>
												<div class="col-md-2">
													<span class="num-hide-day"></span>
													<input value="<?=$tDetail['time']?>" type="number" placeholder="Minutes" class="form-control" name='time[<?=$key?>]'/>
												</div>
												<div class="col-md-3">
													<select multiple="multiple" title="Technician" id="multech" name="technicians[0][]" class="required multech form-control">
				                    <? foreach ($technicians as $p){?>
				                      <option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
				                    <?}?>
													</select>
												</div>
												<div class="col-md-1">
													<button onclick="removeRowDay(this);" type="button" name="button" class="btn"><i class="fa fa-minus"></i> </button>
												</div>
											</div>
										</div>
									</div>

								<?php }; ?>
								<!-- report for ticket -->

								<div class="row">
									<div class="col-md-12">
										<h5>Remark / Work done</h5>
										<textarea rows="5" class=" form-control" name='ticket[remark]' id="remark" title="Remark is required" style="width:100%"><?=$ticket['remark']?></textarea>
									</div>
								</div>

								<div id="spare_row" class="spare_row rowtech">
									<div class="row">
										<div class="col-md-12">
											<small>You can request spare part(S) for this ticket/Jobcard if it required. Once it succeffully please wait for <strong>HOD/Manager</strong> approval</small>
										</div>
										<div class="col-md-7">
											<h5>Spare Part</h5>
										</div>
										<div class="col-md-2">
											<h5>Spare Qty</h5>
										</div>
										<div class="col-md-2">
											<h5>Order Status</h5>
										</div>
										<div class="col-md-1">
											<button onclick="addNewSpareRow();" type="button" name="button" class="btn"><i class="fa fa-plus"></i></button>
										</div>
									</div>
									<div id="spare_row_drop">
										<?php if ($spare_orders){?>

											<?php foreach ($spare_orders as $key => $list){?>

												<div class="row spare_row">
													<div class="col-md-7">
														<?php if($list['pro_approval'] || $list['pro_issued']){ ?>
															<input readonly class="form-control" type="text" name="" value="<?=$list['productname']?>">
														<?php }else{?>
															<select onchange="checkSpare(this)" class="form-control spareid" name="spare_spareid[]">
																<option selected value="<?=$list['prodid']?>"><?=$list['productname']?></option>
															</select>
														<?php }?>
													</div>
													<div class="col-md-2">
														<span class="num-hide num-hide-ov"></span>

														<input <?if($list['pro_approval'] || $list['pro_issued']) echo 'readonly';?> placeholder="Quantity"
														title="Spare quantity" type="number" class="spare_qty form-control"
														<?if($list['pro_approval'] !=1 || $list['pro_issued'] !=1) echo "name='spare_qty[]'";?> value="<?=$list['qty']?>"/>

													</div>
													<div class="col-md-2">
														<?php if ($list['isfrom_jobcard']){?>

															<?php if ($list['pro_approval']){?>

																<?php if ($list['pro_issued']){ ?>
																	<span class="badge badge-success">Issued</span>
																<?php }else{?>
																	<span class="badge badge-primary">Not Issued</span>
																<?php }; ?>

															<?php }else{; ?>
																<span class="badge badge-danger">Not Approved</span>
															<?php }; ?>

														<?php }; ?>
													</div>
													<div class="col-md-1">
														<?php if ($list['pro_approval'] || $list['pro_issued']){?>
															<button type="button" class="btn"><i class="fa fa-ban"></i> </button>
														<?php }else{?>
															<button onclick="removeSpareRow(this);" type="button" name="button" class="btn"><i class="fa fa-minus"></i> </button>
														<?php }?>
													</div>
												</div>

											<?php }; ?>

										<?php }else{?>

											<div class="row spare_row">
												<div class="col-md-7">
													<select onchange="checkSpare(this)" class="form-control spareid" name="spare_spareid[]">
														<?php if ($spare_order['spareid']){?>
															<option value=""><?=$spare_order['spareid']?></option>
														<?php }else{ ?>
															<option selected disabled>--Choose Spare--</option>
														<?php }; ?>
													</select>
												</div>
												<div class="col-md-2">
													<span class="num-hide num-hide-ov"></span>
													<input placeholder="Quantity " title="Spare quantity" type="number" class="spare_qty form-control" name='spare_qty[]' value="<?=$spare_order['qty']?>"/>
												</div>
												<div class="col-md-2">
													<span class="badge badge-danger">Not Approved</span>
												</div>
												<div class="col-md-1">
													<button onclick="removeSpareRow(this);" type="button" name="button" class="btn"><i class="fa fa-minus"></i> </button>
												</div>
											</div>

										<?php }; ?>
									</div>
								</div>
								<!-- <div class="row">
									<div class="col-md-6">
										<h5>Spare/ Part</h5>
										<input type="text" class=" form-control" name='ticket[sparepart]' value="<?=$ticket['sparepart']?>"/>
									</div>
									<div class="col-md-6">
										<h5>Spare / Part Amount</h5>
										<span class="num-hide"></span>
										<input type="number" class=" form-control" name='ticket[spareamount]' value="<?=$ticket['spareamount']?>"/>
									</div>
								</div> -->
								<?php //if ($ticket['type'] == 'paid'){?>
									<!-- <div class="row">
										<div class="col-md-6">
											<h5>Invoice Amount</h5>
											<span class="num-hide"></span>
											<input type="number" class=" form-control" name='ticket[amount]' value="<?=$ticket['amount']?>"/>
										</div>
										<div class="col-md-6">
											<h5>Invoice Number</h5>
											<input type="text" class=" form-control" name='ticket[invoiceno]' value="<?=$ticket['invoiceno']?>"/>
										</div>
									</div> -->
								<?php //}; ?>

							<?php }; ?>


							<div class="row">
								<div class="col-md-12">
									<h5>Time Spent</h5>
									<span class="num-hide"></span>
									<input placeholder="Enter Minutes only, Example: 30" type="number" class=" form-control" name='ticket[timespent]' value="<?=$ticket['timespent']?>"/>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<?if($_GET['state'] == 'verifying'){?>
										<div class="col-md-6">
											<a href="?module=tickets&action=verify_ticket" class="mb-xs mt-xs mr-xs btn btn-success btn-block">
												<i class="fa fa-list"></i> Back To list</a>
										</div>
									<?}else{?>
										<div class="col-md-6">
											<a href="?module=tickets&action=my_ticket_index" class="mb-xs mt-xs mr-xs btn btn-success btn-block">
												<i class="fa fa-list"></i> Back To list</a>
										</div>
									<?}?>
									<div class="col-md-6">
										<? if ($ticket['clientid'] && $ticket['serialid']) { ?>
										<button id="forsave" type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block">
											<i class="fa fa-save"></i> Save</button>
										<? } else { ?>
										<button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block">Rectify New Details</button>
										<? } ?>
									</div>
									<?if($_GET['state'] == 'verifying'){?>
										<div class="col-md-12">
											<a onclick="alert('You are about to verify the ticket?')" href="?module=tickets&action=verify_ticket_process&id=<?=$ticket['id']?>" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-check"></i> Verify Job</a>
										</div>
									<?}?>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>

<script>
	$(function(){
		//$('#clientid').select2({minimumInputLength:3});
		//$('#productid').select2({minimumInputLength:3});
		$(':input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
		$('input.timepicker').timepicker({
			timeFormat:'hh:mm',
			dropdown:false,
			scrollbar:false
		});
		$('.multech').select2({minimumInputLength:3});
		//getting department
		$('#departId').select2({width:'100%',minimumInputLength:3,
			ajax:{
				url:"?module=departments&action=getDepartments&format=json",dataType:'json',delay:250,quietMillis:200,
				data: function (term) {return {search:term};},
				results:function (data,page){return{result:data};}
			}
		});
		//getting product
		$('.spareid').select2({width:'100%',minimumInputLength:3,
			ajax:{
				url:"?module=products&action=getProducts&format=json",dataType:'json',delay:250,quietMillis:200,
				data: function (term) {return {search:term};},
				results:function (data,page){return{result:data};}
			}
		});

		//getting product
		$('#productid').select2({width:'100%',minimumInputLength:3,
			ajax:{
				url:"?module=products&action=getProducts&format=json",dataType:'json',delay:250,quietMillis:200,
				data: function (term) {return {search:term};},
				results:function (data,page){return{result:data};}
			}
		});
		//getting client
		$("#clientid").select2({ width: '100%', minimumInputLength: 3,
			ajax: {
				url: "?module=clients&action=getClients&format=json", dataType: 'json', delay: 250, quietMillis: 200,
				data: function (term) {	return { search : term }; },
				results: function (data, page) { return { results: data }; }
			}
		 });
		 //getting serial number
		 $("#getSerial").select2({ width: '100%', minimumInputLength: 3,
 			ajax: {
 				url: "?module=serials&action=getAllSerials&format=json", dataType: 'json', delay: 250, quietMillis: 200,
 				data: function (term) {	return { search : term }; },
 				results: function (data, page) { return { results: data }; }
 			}
 		 });

		$("#name").focus();
	})

	function getTicketStatusForSIRI(obj){
		if (obj.value == 3) {
			$('.sirinumber').show();
			triggerError('Please Add Seal Number');
		}else{
			$('.sirinumber').hide();
		}
	}

	function addNewDay(){
		var index = $('#nameindex').val();
		$('#nameindex').val(parseInt(index) + 1);
		var row = "<div class='row rowInputTech'><div class='col-md-2'>"+
			"<input readonly type='text' placeholder='Date' name='date["+index+"]' class='datepicker form-control' value='<?=$toddaydate?>'/></div>"+
			"<div class='col-md-4'><textarea name='remark["+index+"]' placeholder='Write description..'' rows='3' class='form-control'></textarea></div>"+
			"<div class='col-md-2'><span class='num-hide-day'></span><input type='number' placeholder='Minutes' class='form-control' name='time["+index+"]'/></div>"+
			"<div class='col-md-3'>"+
			"<select multiple='multiple' name='technicians["+index+"][]' id='multech' title='Technician' class='required multech form-control'>"+
			<? foreach ($technicians as $p){?>
			"<option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value='<?=$p['id']?>'><?=$p['name']?></option>"+
			<?}?>"</select></div><div class='col-md-1'><button onclick='removeRowDay(this)' type='button' name='button' class='btn'><i class='fa fa-minus'></i> </button></div></div>";
			$('#rowInputTechDrop').append(row);
			$('.multech').select2({minimumInputLength:3});
			$('input.timepicker').timepicker({
				timeFormat:'hh:mm',
				dropdown:false,
				scrollbar:false
			});
			date()
			$(':input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
	}

	function addNewSpareRow(){
		var row = '<div class="row spare_row">'+
		'<div class="col-md-7">'+
		'<select onchange="checkSpare(this)" class="form-control spareid" name="spare_spareid[]">'+
		'<option selected disabled>--Choose Spare--</option>'+
		'</select></div>'+
		'<div class="col-md-2"><span class="num-hide num-hide-ov"></span>'+
		'<input placeholder="Quantity" title="Spare Quantity" type="number" class="spare_qty form-control" name="spare_qty[]" value=""/>'+
		'</div><div class="col-md-2"><span class="num-hide"></span>'+
		'<span class="badge badge-danger">Not Approved</span>'+
		'</div><div class="col-md-1">'+
		'<button onclick="removeSpareRow(this);" type="button" name="button" class="btn"><i class="fa fa-minus"></i> </button></div></div>';

			$('#spare_row_drop').append(row);
			$('.spareid').select2({width:'100%',minimumInputLength:3,
				ajax:{
					url:"?module=products&action=getProducts&format=json",dataType:'json',delay:250,quietMillis:200,
					data: function (term) {return {search:term};},
					results:function (data,page){return{result:data};}
				}
			});
			$(':input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
	}

	function checkSpare(obj){
		var spareid = $(obj).val();
		var spare_qty = $(obj).closest('.spare_row').find('.spare_qty');
		if(spareid != '' || spareid != null){
			spare_qty.attr('required','required');
		}
	}

	function removeSpareRow(obj){
		$(obj).closest('.spare_row').remove();
	}

	function removeRowDay(obj){
		$(obj).closest('.rowInputTech').remove();
	}
	function checkmobilenumber(obj){
		var input = $(obj);
		var number = input.val();
		var length = number.toString().length;
		if(length != 9){
			triggerError('Mobile is Min than 9 digits or Max than 9');
			$('#forsave').hide();
			//$('#forsave-btn').hide();
		}else{
	    $('#forsave').show();
			//$('#forsave-btn').show();
		}
	}
</script>
