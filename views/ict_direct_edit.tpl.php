<style>
.popup_container {
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 1042;
	overflow: hidden;
  overflow-y: scroll;
	position: fixed;
	background: rgba(11, 11, 11, 0.71);
  display:none;
	filter: alpha(opacity=80);
}
.pop-col-holder {
    background: white;
    width: 50%;
    margin: 0 auto;
    position: relative;
    top: 117px;
    border-radius: 5px;
    padding: 16px;
}
.popup_info{
	width:50%;
}
.popup_info h4{
	border-bottom: 1px solid rgba(158, 158, 158, 0.5215686274509804);
  width: 100%;
  padding-bottom: 10px;
}
.control-btn ul{
	padding:0;
	list-style:none;
}
.control-btn{
	float:right;
  padding-top: 9px;
}
.control-btn ul li{
	display:inline-block;
	margin-left:10px;
}

.center-panel{
  margin: 0 auto;
  width: 96%;
  padding: 10px;
}
.btn-client,.btn-serial {
    position: absolute;
}
.btn-client{
  right: 20%;
  top: 34%;
}
.btn-serial{
  right: 20%;
  top: 28%;
}
.panel-actions a, .panel-actions .panel-action{
  font-size: 21px;
}
#generate_serial {
    display:none;
}
.panel-actions a, .panel-actions .panel-action{
  font-size: 21px;
}
.select2-container .select2-selection--single {
    height: 39px;
}
h5 {
    font-weight: 900;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered{
	padding:0 5px 5px;
}
.num-hide {
    display: block;
    background: white;
    width: 32px;
    height: 28px;
    position: absolute;
    right: 21px;
    top: 40px;
}
.forChargeable{
  background:#d64742;
}
.forChargeable {
    background: #d64742;
}
.forChargeable h2{
  color:#ffffff;
}
.forValid{
  background:#51b451;
}
.forValid h2{
  color:#ffffff;
}
#showServiceStatus {
    position: absolute;
    right: 23px;
    top: 19px;
    font-size: 16px;
}
.panel-heading p{
  color:#ffffff;
  text-transform:uppercase;
}
.container-checkbox {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 14px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.container-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}
.container-checkbox .checkmark {
    position: absolute;
    top: 3px;
    left: 0;
    height: 19px;
    width: 23px;
    background-color: #b7b9c1;
}
.container-checkbox:hover input ~ .checkmark {
    background-color: #ccc;
}
.container-checkbox input:checked ~ .checkmark {
    background-color: #2196F3;
}
.container-checkbox .checkmark:after {
    content: "";
    position: absolute;
    display: none;
}
.container-checkbox input:checked ~ .checkmark:after {
    display: block;
}
.container-checkbox .checkmark:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
#forerror{
	display:none;
}
.forNotChargeable{
  background:#0099e6;
}
.forNotChargeable h2{
  color:#ffffff;
}
</style>

<!---POPUP START HERE--------->
<div id="popup_container" class="popup_container">
		<div class="pop-col-holder">
			<div class="container">
				<div class="popup_info">
					<h4><span>Generated Serial Number</span></h4>
            <form class="" action="<?=url('serials','serial_save','current=nonefd_serial')?>" method="post">
              <div id="buildingRow" class="build-container" width="100%">
                <div class="build-row-main">
                    <div class="rows-holder">
                      <span width="100px">Suggested Serial</span>
                      <div width="200px">
                        <input type="text" id="generated_serial" name="serial[name]" placeholder="Serial Number" class="form-control" value="<?=$building['name']?>"/>
                        <input type="hidden" id="client_id" name="serial[clientid]" value=""/>
                        <input type="hidden" id="product_id" name="serial[prodid]" value=""/>
                        <input type="hidden" name="serial[isfiscal]" value="1"/>
                        <input type="hidden" name="serial[status]" value="active"/>
                      </div>
                    </div>
                    <div class="rows-holder">
                      <span width="100px">Department</span>
                      <div width="200px">
                        <select id="deptid" name="serial[deptid]" required class="form-control mb-md">
													<option selected disabled>--Choose Department--</option>
                        </select>
                      </div>
                    </div>
                    <div class="rows-holder">
                      <span width="100px">Location</span>
                      <div width="200px">
                        <select name="serial[locid]" required class="form-control mb-md">
                          <option selected disabled>--Choose Location--</option>
                          <? foreach ($locs as $p){?>
                            <option <?if ($optionselect == 'yes') echo "selected";?> <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
                          <?}?>
                        </select>
                      </div>
                    </div>
                </div>
              </div>
              <div class="col-md-12">
								<div class="row">
									<div class="col-md-6">
										<button id="close-popup-module" type="button" class="btn btn-block btn-danger"> <i class="fa fa-close"></i> Cancel</button>
									</div>
									<div class="col-md-6">
										<button type="submit" class="btn btn-block btn-primary"><i class="fa fa-save"></i> Save Serial</button>
									</div>
								</div>
  					</div>
            </form>
				</div>
			</div>
		</div>
	</div>
<!--POPUP END HERE--->

<header class="page-header">
	<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> Non-EFD Support</h2>
</header>

<div class="row">
	<div class="col-lg-12">
		<section class="panel" style="width:70%;margin:0 auto">
      <header id="showStatus" class="panel-heading">
				<h2 class="panel-title">Direct Non-Serial Support - <strong>Jobcard</strong> (DNSS)</h2>
				<p id="showServiceStatus"></p>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('tickets','save_direct_support')?>" >
					<fieldset class="row-panel">
						<legend style="width:40%">It indicates how you received query from the client</legend>
						<div class="row">
							<div class="col-md-12">
								<h5>Raised From</h5>
								<select id="raisedfrom" name="support[raisedfrom]" required class="form-control mb-md">
										<option selected disabled>--Choose Type--</option>
										<option value="direct">direct</option>
										<option value="mobile">mobile</option>
								</select>
							</div>
						</div>
					</fieldset>
					<fieldset class="row-panel">
						<legend style="width:70%">Section for select the client and product in order to get serials list or generate serial</legend>
						<div class="row">
							<div class="col-md-6">
								<h5>Client</h5>
								<input type="hidden" id="client" name="support[client]"/>
								<select id="clientid" name="support[clientid]" class="form-control mb-md">
									<option selected disabled>--Choose Client--</option>
									<?php if ($forserialdetails){?>
										<option selected value="<?=$forserialdetails['clientid']?>"><?=$forserialdetails['clientname']?></option>
									<?php }; ?>
								</select>
							</div>
							<div class="col-md-6">
								<h5>Product</h5>
								<select onchange="getSerialNumber();" id="prodid" name="support[prodid]" required class="form-control mb-md">
									<option selected disabled>--Choose Product--</option>
									<?php if ($forserialdetails){?>
										<option selected value="<?=$forserialdetails['prodid']?>"><?=$forserialdetails['productname']?></option>
									<?php }; ?>
								</select>
							</div>
						</div>
					</fieldset>
					<fieldset class="row-panel">
						<legend style="width:50%">Section for serial details (Department and location)</legend>
						<div class="row">
							<div class="col-md-12">
								<h5>Serial Number</h5>
								<input id="ticketwarrantyId" type="hidden" name="support[warrantyid]" value="">
								<input id="ticketamcid" type="hidden" name="support[amcid]" value="">
								<input id="serialnumber" type="hidden" name="support[serialno]" value="<?=$serial['name']?>">
								<button id="generate_serial" class="btn btn-primary" type="button" name="button"> <i class="fa fa-cog"></i> Generate Serial Number</button>
								<input type="hidden" name="support[type]" id="type"/>
								<select onblur="getSerialDetails(this);" id="serialid" required class="form-control mb-md" title="Serial number is required" style="width:100%" name="support[serialid]">
									<option selected disabled>--Choose Serial No--</option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<h5>Department</h5>
								<select id="forgetDepartment" name="support[deptid]" required class="form-control mb-md">
									<option selected disabled>--Choose Department--</option>
									<?php if ($forserialdetails){?>
										<option selected value="<?=$forserialdetails['deptid']?>"><?=$forserialdetails['departname']?></option>
									<?php }; ?>
								</select>
								<!--<select id="deptId" name="support[deptid]" required class="form-control mb-md">
									<option selected disabled>-Choose Department-</option>
								</select>-->
							</div>
							<div class="col-md-6">
								<h5>Location</h5>
								<select id="branchid" name="support[branchid]" required class="form-control mb-md">
									<option selected disabled>--Choose Location--</option>
									<? foreach ($locs as $p){?>
										<option <?if ($p['id'] == $forserialdetails['locid']) echo "selected";?> <?if ($optionselect == 'yes') echo "selected";?> <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
									<?}?>
								</select>
							</div>
						</div>
					</fieldset>
					<fieldset class="row-panel">
						<legend style="width:70%;">The person who reports the query or problem and what the support he/she require</legend>
						<div class="row">
							<div class="col-md-4">
								<h5>Contact Person (Name)</h5>
								<input id="contactName" placeholder = "Eg. juma rashid" type="text" name="support[contactname]" class="form-control mb-md">
							</div>
							<div class="col-md-4">
								<h5>Contact Person (Mobile)</h5>
								<span class="num-hide"></span>
								<input onblur="checkmobilenumber(this)" id="contactMobile" placeholder = "Eg. 782769370"  type="number" name="support[contactmobile]" class="form-control mb-md">
								<span id="mobileChecker"></span>
							</div>
							<div class="col-md-4">
								<h5>Support Type</h5>
								<select onblur="getsupportTYpe(this)" id="supporttype" name="support[supporttype]" required class="form-control mb-md">
									<option selected disabled>--Choose Type--</option>
									<? foreach ($supporttype as $p){?>
										<option <?=selected($serial['supporttype'],$p['id'])?> data-chargeable="<?=$p['chargeable_status']?>" value="<?=$p['id']?>"><?=$p['name']?></option>
									<?}?>
									</select>
							</div>
						</div>
					</fieldset>
					<fieldset class="row-panel">
						<legend style="width:50%">Section that indicates how you will support or solve client query</legend>
						<div class="row">
							<div class="col-md-12">
								<h5>Mode Of Support</h5>
								<select name="support[supportedwith]" required class="form-control mb-md">
									<option selected disabled>--Choose Mode--</option>
									<option value="inhouse" <?=selected($ticket['supportedwith'],'inhouse')?>>In house</option>
									<option value="remote" <?=selected($ticket['supportedwith'],'remote')?>>Remote</option>
									<option value="sitevisit" <?=selected($ticket['supportedwith'],'sitevisit')?>>Site Visit</option>
								</select>
							</div>
						</div>
					</fieldset>
					<fieldset class="row-panel">
						<legend style="width:50%">Section for problem details, jobcard type</legend>
						<div class="row">
							<div class="col-md-12">
								<h5>State the Problem</h5>
								<textarea required title="Describe the problem" class="form-control" name="support[clientremark]" rows="4" cols="50" placeholder="Describe the problem"></textarea>
								<div class="row">
									<div class="col-md-11">
										<label class="container-checkbox">Printing Min Job Card. (58mm)
											<input type="checkbox" name="minjobcard" value="yes">
											<span class="checkmark"></span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
					<fieldset class="row-panel">
						<legend style="width:50%;">Section for assigning the person who will work for it</legend>
						<div class="row">
							<div class="col-md-12">
								<h5>Assign Task To</h5>
								<small>Lead Technician for this Job</small>
								<select name="support[assignedto]" required class="form-control">
									<option selected disabled>--Choose Staff--</option>
									<?if($userhead == 'admin'){?>
										<option value=""></option>
										<? foreach ($users as $p){?>
											<option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
										<?}?>
									<?}else if($userhead == 'yes'){?>
											<option value=""></option>
											<? foreach ($users as $p){?>
												<option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
											<?}?>
									<?}else{?>
										<option value="<?=$users['id']?>"><?=$users['name']?></option>
									<?}?>
								</select>
								<input type="hidden" name="support[confirm]" value="NonEFD" id="confirmtype"/>
							</div>
							<!--<div class="col-md-7">
								<h5>Sub Technicians</h5>
								<small>Supportive Technician for this Job</small>
								<select multiple="multiple" id="multech" name="subtechnician[]" class="form-control multselect">
									<?if($userhead == 'admin'){?>
										<option value=""></option>
										<? foreach ($users as $p){?>
											<option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
										<?}?>
									<?}else if($userhead == 'yes'){?>
											<option value=""></option>
											<? foreach ($users as $p){?>
												<option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
											<?}?>
									<?}else{?>
										<option value="<?=$users['id']?>"><?=$users['name']?></option>
									<?}?>
								</select>
							</div>-->
						</div>
					</fieldset>
					<div class="form-group">
						<div id="forsavebtn">
							<div class="col-md-6">
								<a href="?module=tickets&action=my_ticket_index" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-arrow-left"></i> Back</a>
							</div>
							<div class="col-md-6">
								<button id="forsave" type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save</button>
							</div>
						</div>
						<div id="forerror">
							<div class="col-md-12">
								<a href="?module=tickets&action=my_ticket_index" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-arrow-left"></i> Back</a>
							</div>
						</div>
					</div>
				</form>
      </div>
<script>

	$(function(){
    //$('#clientid').select2({minimumInputLength:3});
		$(':input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
    $('#multech').select2({minimumInputLength:3});
		//ajax for department
		$('#deptid').select2({width:'100%',minimumInputLength:3,
    ajax:{
      url:"?module=departments&action=getDepartments&format=json",dataType:'json',delay:250,quietMillis:200,
      data: function (term) {return {search:term};},
      results:function (data,page){return{result:data};}
    }});
		//ajax for department
		$('#deptId').select2({width:'100%',minimumInputLength:3,
    ajax:{
      url:"?module=departments&action=getDepartments&format=json",dataType:'json',delay:250,quietMillis:200,
      data: function (term) {return {search:term};},
      results:function (data,page){return{result:data};}
    }});
		//ajax for client
		$("#clientid").select2({ width: '100%', minimumInputLength: 3,
			ajax: {
				url: "?module=clients&action=getClients&format=json", dataType: 'json', delay: 250, quietMillis: 200,
				data: function (term) {	return { search : term }; },
				results: function (data, page) { return { results: data }; }
			}
		});
		//ajax for products
		$('#prodid').select2({width:'100%',minimumInputLength:3,
    ajax:{
      url:"?module=products&action=getProducts&format=json",dataType:'json',delay:250,quietMillis:200,
      data: function (term) {return {search:term};},
      results:function (data,page){return{result:data};}
    }});

		$("#name").focus();
		toggleAmc();
	})

	function toggleAmc(){
		var amc = $("#amc").val();
		if (amc == 'no') $(".amcDet").hide();
		if (amc == 'yes') $(".amcDet").show();
	}

  <?if($_GET['productid'] && $_GET['serialid']){?>
    //run the function for getting serial number
    getSerialNumber();
  <?}?>

  function getSerialNumber(){
    var clienName = $('#client');
    var clientid = $('#clientid').val();
    var productid = $('#prodid').val();
    var serialid = $('#serialid');
    var serialnumber = $('#serialnumber');

    $.get("?module=serials&action=getSerialNumbers&format=json&clientid="+clientid+"&productid="+productid, null, function(d){
      var serialList = eval(d);
      if (serialList[0].status == 'found') {
				$('#deptId').append("<option value='"+serialList[0].departdata.departId+"'>"+serialList[0].departdata.departname+"</option>");
        clienName.val(serialList[0].clientName[0].name);
        $.each(serialList[0].serialdata, function(index,serial){
          var optionElement = "<option value='"+serial.id+"'>"+serial.name+"</option>";
          serialnumber.val(serial.name);
          serialid.append(optionElement).show();
          $('#generate_serial').hide();
        })

        triggerMessage('All serial number are populated successfully');

      }else{
        triggerError('Client with product has no serial number');
        serialid.empty().hide();
        $('#generate_serial').show();
      }

    });
  }

  $('#generate_serial').on('click', function(event){
    event.preventDefault();
    var clientid = $('#clientid').val();
    var productid = $('#prodid').val();
    var serialpopup = $('#popup_container');
    var serialfielView = $('#generated_serial');

    $.get("?module=serials&action=generateSerial&format=json&clientid="+clientid+"&productid="+productid, null, function(serials){
      var predicted_serial = eval(serials);
      if (predicted_serial[0].status == 'new') {
          var productId = predicted_serial[0].productid;
          var clientId = predicted_serial[0].clientid;

          serialfielView.val(predicted_serial[0].generatedserial);
          serialpopup.show();

          $('#client_id').val(clientId);
          $('#product_id').val(productId);
      }

    });
  })

	function getsupportTYpe(obj){
	  var chargeable_status = $(obj).children('option:selected').data('chargeable');
		var serialnumber = $('#serialid');
	  if(chargeable_status != 1){
	    triggerMessage('Not Chargeable');
	    $("#type").val('not chargeable');
	    $('#showStatus').addClass('forNotChargeable');
	    $('#showServiceStatus').html('not chargeable');
	  }else{
	    $('#showStatus').removeClass('forNotChargeable');
			getSerialDetails(serialnumber);//update the initial serial number status
	  }
	}

  function getSerialDetails(obj) {
    var serialnumber = $(obj).children("option:selected").text();
    $.get("?module=serials&action=getSerialDetails&format=json&serialnumber="+serialnumber, null, function(d){
      var CC = eval(d);
			var status = CC[0].status;
      if (status == 'found') {
       	$("#deptid").val(CC[0].deptid);
				$("#prodid").val(CC[0].prodid);
				$("#client").val(CC[0].client);
        $("#clientid").val(CC[0].clientid);
				//$("#serialid").val(CC[0].serialid);
				$('#serialnumber').val(CC[0].serialname);
				$("#branchid").val(CC[0].branchid);
				$("#contactName").val(CC[0].contactname);
				$("#ticketamcid").val(CC[0].amcid);
				$("#ticketwarrantyId").val(CC[0].warrantyId);
        var mNumber  = CC[0].contactmobile;

			if(mNumber == '' || mNumber == null){
				}else{$("#contactMobile").val(mNumber.replace(/^255+/i, ''));}

				if(CC[0].departname !== '' || CC[0].departname !== null){
          $("#forgetDepartment").append("<option selected value="+CC[0].deptid+">"+CC[0].departname+"</option>");
        }

        //making the field to readonly, user cant edit the text inside it
        if(CC[0].client){
           $('#client').prop('readonly', true);
           $('#pass_client').hide();
         }else{
           $('#client').removeAttr('readonly');
           $('#pass_client').show();
         };

				var servicestatus = CC[0].servicestatus;
        //flag the ticket for client to pay
        if(servicestatus == 'chargeable'){
          triggerError('No Warranty Or AMC, need to pay');
          $("#type").val(servicestatus);
          $('#showStatus').removeClass('forValid');
          $('#showStatus').addClass('forChargeable');
          $('#showServiceStatus').html(servicestatus);
        }else{
          $("#type").val(servicestatus);
          $('#showStatus').removeClass('forChargeable');
          $('#showStatus').addClass('forValid');
          $('#showServiceStatus').html(servicestatus);
        }

				$('#forerror').hide();
        $('#forsavebtn').show();
      }else{
        triggerError('Serial number not found, please fill in your details');
				$('#forerror').show();
        $('#forsavebtn').hide();
      }
    });
  }
  //button for serial number
  $('#pass_serial').on('click', function (event) {
    event.preventDefault();
    var new_serial = $('#serialnumber').val();
    var serial_Link = $(this).attr('href');
    window.location = serial_Link+"&newserial="+new_serial;
  })

  $('#pass_client').on('click', function (event) {
    event.preventDefault();
    var new_serial = $('#client').val();
    var serial_Link = $(this).attr('href');
    window.location = serial_Link+"&newclient="+new_serial;
  })

  $("#close-popup-module").on("click", function(e){
    e.preventDefault();
    $('.popup_container').hide();
  });
//Checking for printing the jobcard
<?if($ticketid){?>
	$(window).on("load",function(){

		<?if($_GET['minjob'] == 'yes'){?>
      window.open('?module=tickets&action=min_jobcard&id=<?=$ticketid;?>', '_blank');
    <?}else{?>
      window.open('?module=tickets&action=jobcard&id=<?=$ticketid;?>', '_blank');
    <?}?>

  });
<?}?>

function checkmobilenumber(obj){
	var check = $('#mobileChecker');
	var input = $(obj);
	var number = input.val();
	var length = number.toString().length;
	if(length != 9){
		triggerError('Mobile is Min than 9 digits or Max than 9');
		input.focus();
		$('#forsave').hide();
		check.html("Don't begin with 255 or 0 on your mobile number");
	}else{
		$('#forsave').show();
		check.empty();
	}
}

</script>
