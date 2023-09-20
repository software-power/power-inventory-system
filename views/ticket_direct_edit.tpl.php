<style>
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
.select2-container .select2-selection--single {
  height: 39px;
}
h5 {
  font-weight: 900;
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
.forChargeable h2{
  color:#ffffff;
}
.forNotChargeable{
  background:#0099e6;
}
.forNotChargeable h2{
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
#forTRA{
  display:none;
}
#forerror{
  display:none;
}
</style>

<header class="page-header">
	<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> Support</h2>
</header>

<div class="row">
	<div class="col-lg-12">
		<section class="panel" style="width:70%;margin:0 auto">
      <header id="showStatus" class="panel-heading">
				<h2 class="panel-title">Direct Serial Support - <strong>Jobcard</strong> (DSS)</h2>
        <p id="showServiceStatus"></p>
			</header>
			<div class="panel-body">
        <form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('tickets','save_direct_support')?>" >
          <fieldset class="row-panel">
            <legend>It indicates how you received query from the client</legend>
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
            <legend>Information that related or indicates the product</legend>
            <div class="row">
              <div class="col-md-12">
                <h5>Serial Number</h5>
                <input id="ticketwarrantyId" type="hidden" name="support[warrantyid]" value="">
                <input id="ticketamcid" type="hidden" name="support[amcid]" value="">
                <input id="serialid" type="hidden" name="support[serialid]" value="<?=$serial['id']?>">
                <input placeholder="Serial Number" id="serialnumber" onblur="getSerialDetails();" type="text" class="required form-control" id="name" title="Name is required" style="width:100%;font-size: 15px;" name="support[serialno]" value="<?=$serial['name']?>">
                <input id="warrantydateto" type="hidden" name="support[warrantydateto]" value="<?=$serial['warrantydateto']?>">
                <input id="amcdateto" type="hidden" name="support[amcdateto]" value="<?=$serial['amcdateto']?>">
                <input type="hidden" name="support[type]" id="type"/>
                <!--TODO need to consider security for this->
                <a id="pass_serial" href="?module=serials&action=serial_add" class="btn btn-serial"> <i class="fa fa-plus"></i> </a> -->
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <h5>Product</h5>
                <select id="forgetProduct" name="support[prodid]" required class="form-control mb-md">
                  <option selected disabled>--Choose Product--</option>
                </select>
              </div>
              <div class="col-md-6">
                <h5>Department</h5>
                <select id="forgetDepartment" name="support[deptid]" required class="form-control mb-md">
                  <option selected disabled>--Choose Department--</option>
                </select>
              </div>
            </div>
          </fieldset>
          <fieldset class="row-panel">
            <legend>Section for client information</legend>
            <div class="row">
              <div class="col-md-6">
                <h5>Client</h5>
                <input placeholder="Client" type="text" id="client" name="support[client]" placeholder="Enter client name" class="form-control"/>
                <input type="hidden" id="clientid" name="support[clientid]"/>
              </div>
              <div class="col-md-6">
                <h5>Location</h5>
                <select id="branchid" name="support[branchid]" required class="form-control mb-md">
                  <option selected disabled>--Choose Location--</option>
                    <? foreach ($locs as $p){?>
                      <option <?if ($optionselect == 'yes') echo "selected";?> <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
                    <?}?>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <h5>Physical Location</h5>
                <input placeholder="Tabat, Ilala" id="ploc" type="text" class="form-control" name="support[ploc]" value="<?=$serial['ploc']?>">
              </div>
            </div>
          </fieldset>
          <fieldset class="row-panel">
            <legend style="width:100%;">The person who reports the query or problem and what the support he/she require</legend>
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
                  <option value="" selected disabled>--Choose Type--</option>
                    <? foreach ($supporttype as $p){?>
                      <option <?=selected($serial['supporttype'],$p['id'])?> data-chargeable="<?=$p['chargeable_status']?>" value="<?=$p['id']?>"><?=$p['name']?></option>
                    <?}?>
                </select>
              </div>
            </div>
          </fieldset>
          <fieldset class="row-panel">
            <legend style="width:100%">Section for problem details, jobcard type and Email to TRA</legend>
            <div class="row">
              <div class="col-md-12">
                <h5>State the Problem</h5>
                <textarea class="form-control" name="support[clientremark]" rows="4" cols="50" placeholder="Describe the problem"></textarea>
                <div class="row">
                  <div class="col-md-6">
                    <label class="container-checkbox">Printing Min Job Card. (58mm)
                      <input type="checkbox" name="minjobcard" value="yes">
                      <span class="checkmark"></span>
                    </label>
                  </div>
                  <div class="col-md-6">
                    <label class="container-checkbox">
                      Send <strong>Job Card</strong> details to TRA regional office
                      <input id="tra_checkbox" type="checkbox" checked name="emailto_tra" value="yes">
                      <span class="checkmark"></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row" id="forTRA">
              <div class="col-md-12">
                <select required name="tra_regional" class="form-control mb-md">
                  <option selected disabled>--Choose TRA Regional--</option>
                  <?php foreach ($tra_list as $key => $tra){ ?>
                    <option value="<?=$tra['id']?>"><?=$tra['location']?></option>
                  <?php }; ?>
                </select>
              </div>
            </div>
          </fieldset>
          <fieldset class="row-panel">
            <legend style="width:100%;">Section for assigning the person who will work for it</legend>
            <div class="row">
              <div class="col-md-12">
                <h5>Assign Task To</h5>
                <small>Lead Technician for this Job</small>
                <select name="support[assignedto]" required class="form-control mb-md">
                  <option selected disabled>--Choose Staff--</option>
                <?if($userhead == 'admin'){?>
                  <? foreach ($users as $p){?>
                    <option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
                  <?}?>
                <?}else if($userhead == 'yes'){?>
                    <? foreach ($users as $p){?>
                      <option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
                    <?}?>
                <?}else{?>
                  <option value="<?=$users['id']?>"><?=$users['name']?></option>
                <?}?>
                </select>
              </div>
              <input type="hidden" name="support[confirm]" value="EFD" id="confirmtype"/>
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
    $(':input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
    $('#deptid').select2({width:'100%',minimumInputLength:3,
    ajax:{
      url:"?module=departments&action=getDepartments&format=json",dataType:'json',delay:250,quietMillis:200,
      data: function (term) {return {search:term};},
      results:function (data,page){return{result:data};}
    }});

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

  if($('#tra_checkbox').is(':checked')){
    $('#forTRA').show();
  }else{
    $('#forTRA').hide();
  }

$('#tra_checkbox').on('click', function(){
  if($(this).is(':checked')){
    $('#forTRA').show();
  }else{
    $('#forTRA').hide();
  }
})

function getsupportTYpe(obj){
  var chargeable_status = $(obj).children('option:selected').data('chargeable');
  if(chargeable_status != 1){
    triggerMessage('Not Chargeable');
    $("#type").val('not chargeable');
    $('#showStatus').addClass('forNotChargeable');
    $('#showServiceStatus').html('not chargeable');
  }else{
    $('#showStatus').removeClass('forNotChargeable');
    getSerialDetails();//run again the function for get serial number details
  }
}

  function getSerialDetails() {
    var serialnumber = $('#serialnumber').val();
    $.get("?module=serials&action=getSerialDetails&format=json&serialnumber="+serialnumber, null, function(d){
      var CC = eval(d);
      if (CC[0].status == 'found') {

				$("#client").val(CC[0].client);
        $("#clientid").val(CC[0].clientid);
				$("#serialid").val(CC[0].serialid);
				$("#branchid").val(CC[0].branchid);
				$("#ploc").val(CC[0].plocation);
				$("#warrantydateto").val(CC[0].warrantydateto);
				$("#amcdateto").val(CC[0].amcdateto);
				$("#ticketamcid").val(CC[0].amcid);
				$("#ticketwarrantyId").val(CC[0].warrantyId);
				$("#contactName").val(CC[0].contactname);
        var mNumber  = CC[0].contactmobile;

        if(mNumber == '' || mNumber == null){
  				}else{$("#contactMobile").val(mNumber.replace(/^255+/i, ''));}

        if(CC[0].departname !== '' || CC[0].departname !== null){
          $("#forgetDepartment").append("<option selected value="+CC[0].deptid+">"+CC[0].departname+"</option>");
        }

        if(CC[0].productname !== '' || CC[0].productname !== null){
          $("#forgetProduct").append("<option selected value="+CC[0].prodid+">"+CC[0].productname+"</option>");
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

//validation for printing the jobcard
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
