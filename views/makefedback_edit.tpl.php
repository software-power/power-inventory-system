<style>
.center-panel{
  margin: 0 auto;
  width: 85%;
  /* padding: 10px; */
}
.panel-actions a, .panel-actions .panel-action{
  font-size: 21px;
}
.widget-holder{
  height:147px;
}
.widget-box {
    background: #ffffff;
    width:100%;
    height: 95px;
    padding: 10px;
}
.widget-box h5 {
    font-size: 13px;
    font-weight: 700;
    padding-top: 8px;
    margin: 0;
}
.widget-box ul {
    padding: 0;
    list-style: none;
    margin: 0;
}
.nameCharacter {
  font-size: 20px;
  display: block;
  padding-top: 29px;
  text-transform: capitalize;
  text-align: center;
  border-radius: 100%;
  background: #ecedf0;
  height: 77px;
  width: 77px;
}
.role-tag {
  background: #138496;
  color: white;
  display: block;
  position: relative;
  height: 22px;
  padding: 1px;
  text-align: center;
  font-size: 14px;
}
span.rate-desc {
    text-align: center;
    display: block;
}
.inline-list li{
  display:inline-block;
}
.qn-holder h5 {
    font-size: 15px;
    font-weight: 700;
}
.qn-holder .qn-num {
  width:40px;
  height:40px;
  padding-top: 10px;
  background: #ffffff;
  border-radius: 100%;
  text-align: center;
  border: 2px solid #138496;
}
.qn-option{
  list-style:none;
}
.qn-option li {
    font-size: 16px;
    margin-top: 7px;
}
.qn-option-container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  text-transform:capitalize;
}
.qn-option-container:hover{
  /* background:black; */
}
.qn-option-container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: #fdfdfd;
  /* background-color: #eee; */
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.qn-option-container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.qn-option-container input:checked ~ .checkmark {
  background-color: #138496;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.qn-option-container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.qn-option-container .checkmark:after {
  top: 6px;
  left: 6px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
}
.qn-holder .qn {
  margin-top: 20px;
  background: #ecedf0;
  padding: 10px;
}
label.qn-option-container.opt-align {
    display: inline-block;
}
.qn-name h5{
  text-transform:capitalize;
}
.popup-model {
    position: absolute;
    top: 0;
    right: 123px;
    bottom: 0;
    left: 127px;
    padding: 71px;
    z-index: 20;
    background: rgba(236, 237, 240, 0.7294117647058823);
}
.popup-model p,h1 {
    color: red;
    position: relative;
    top: 104px;
    text-align: center;
}
.popup-model p{
  font-size: 16px;
}
.pro-input {
  padding: 20px;
  font-size: 21px;
  width: 50%;
  text-align: center;
  margin: 0 auto;
}
.pro-btn{
  margin-top:10px;
  display:none;
}
.widget-pro-name {
    font-size: 16px;
    margin-top: 6px;
    text-transform:capitalize
}
.panel-body{
  padding:0;
}
.row-btn-holder {
    margin-top: 10px;
}
input.contry-code {
  position: absolute;
  z-index: 9;
  font-size: 16px;
  font-weight: 600;
  width: 40px;
  height: 35px;
  border: none;
  left: 13px;
  padding: 3px;
  background: #ecedf0;
}
.hide-number {
  display: block;
  background: #ffffff;
  width: 28px;
  height: 32px;
  position: absolute;
  right: 20px;
  z-index: 9;
  top: 1px;
}
#mobilenumber{
  text-align:center;
}
#default-btn{
  display:none;
}
.form-control[readonly], fieldset[disabled] .form-control {
  background-color: #fdfdfd;
}
</style>

<header class="page-header">
	<h2><?if ($edit) echo 'View'; else echo 'Make';?> Feedback</h2>
</header>

<div class="row" style="width:85%;margin:0 auto">
  <section class="widget-container">
    <div class="widget-holder row">
      <div class="col-md-3">
        <div class="widget-box">
          <div class="col-md-5">
            <?if($tickets['image']){?>
              <img src="images/dp/<?=$tickets['image']?>" alt="Scott Stevens" class="img-responsive img-circle" />
            <?}else{?>
              <?$default_character = str_split($tickets['techname']);?>
              <span class="nameCharacter"><?=$default_character[0]?></span>
            <?}?>
          </div>
          <div class="col-md-7">
            <h5><?=$tickets['techname'];?></h5>
            <ul>
              <li><?=$tickets['udepart'];?></li>
            </ul>
          </div>
        </div>
        <span class="role-tag">TECHNICIAN / ASSIGNED</span>
      </div>
      <div class="col-md-4">
        <div class="widget-box">
          <div class="col-md-12">
            <h5><?=$client['name']?></h5>
            <ul class="inline-list">
              <li><?=$client['tinno']?></li>
              <li><i class="fa fa-mobile"></i> <?=$client['mobile']?></li>
              <li><i class="fa fa-at"></i> <?=$client['address']?></li>
            </ul>
          </div>
        </div>
        <span class="role-tag">CLIENT</span>
      </div>
      <div class="col-md-3">
        <div class="widget-box">
          <div class="col-md-12">
            <h5><?=$tickets['serialno']?></h5>
            <ul class="inline-list">
              <li class="widget-pro-name"><?=$tickets['productname']?></li>
            </ul>
            <ul class="inline-list">
              <li class="widget-pro-name"><?=$tickets['supportname']?></li>
            </ul>
          </div>
        </div>
        <span class="role-tag">PRODUCT</span>
      </div>
      <div class="col-md-2">
        <div class="widget-box">
          <span class="nameCharacter container"><?=$tickets['id']?></span>
          <!-- <span class="rate-desc">Poor</span> -->
        </div>
        <span class="role-tag">TICKET No.</span>
      </div>
    </div>
  </section>
  <section class="panel">
    <header class="panel-heading">
      <h2 class="panel-title">Questions</h2>
    </header>
    <div class="panel-body">

      <?php if ($popup){?>
        <div class="popup-model">

          <!-- no sample -->
          <?php if ($nosample){ ?>
            <h1><i class="fa fa-exclamation-triangle"></i></h1>
            <p>No Sample question for this support type, <strong style="text-transform:uppercase"><?=$tickets['supportname']?></strong> </p>
            <p><a href="?module=crms&action=feedback_index">Go Back</a> </p>
          <?php }; ?>
          <!-- no ticket specified -->
          <?php if ($noticket){ ?>
            <h1><i class="fa fa-exclamation-triangle"></i></h1>
            <p>Enter Ticket Number, to proceed</p>
            <p>
              <input id="ticketIdForFeedback" onblur="verifyTicket(this)" type="number" class="pro-input form-control" name="" value="">
              <button id="feedProceed" class="pro-btn btn btn-primary" type="button" name="button"><i class="fa fa-cog"></i> proceed</button>
            </p>
            <p><span id="feedError"></span></p>
            <p><a href="?module=crms&action=feedback_index">Go Back</a> </p>
          <?php }; ?>

        </div>
      <?php }; ?>

      <div class="center-panel">
        <form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('crms','save_fedback')?>" >
          <input type="hidden" name="ticketId" value="<?=$tickets['id']?>">
          <input type="hidden" name="clientmobile" value="<?=$client['mobile']?>">
          <div class="qn-holder">
            <div class="row qn">
              <div class="col-md-1">
                <h5 class="qn-num">1</h5>
              </div>
              <div class="col-md-11 qn-name">
                <h5>Client Email and Mobile Verification</h5>
                <ul class="qn-option">
                  <li><input name="clientid" type="hidden" value="<?=$client['id']?>"/></li>
                  <li>
                    <div class="row">
                      <div class="col-md-6">
                        <input onblur="emailverification(this);" id="emailid" required name="clientverify_email" class="form-control" type="text" value="<?=$client['email']?>"/>
                      </div>
                      <div class="col-md-6">
                        <input readonly class="contry-code" type="text" name="contrycode" value="255">
        								<span class="hide-number"></span>
                        <input onblur="checkmobilenumber(this)" placeholder="Mobile number" id="mobilenumber" required name="clientverify_mobile" class="form-control" type="number" value="<?=$client['mobile']?>"/>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <?php foreach ($questions as $key => $question){ ?>
              <div class="row qn">
                <div class="col-md-1">
                  <h5 class="qn-num"><?=$key + 2;?></h5>
                </div>
                <div class="col-md-11 qn-name">
                  <h5><?=$question['qname'];?></h5>
                  <ul class="qn-option">

                    <?php if ($question['possible_answers']){?>

                      <?php foreach ($question['possible_answers'] as $key => $answer){?>

                        <li>
                          <label class="qn-option-container"><?=$answer['name'];?>
                            <input type="radio" name="customerqn_reply[<?=$question['fqnid']?>]" data-question-id="<?=$question['fqnid']?>" value="<?=$answer['id']?>">
                            <span class="checkmark"></span>
                          </label>
                        </li>

                      <?php }; ?>

                    <?php }else{;?>
                      <li><textarea placeholder="Write here..." class="form-control" name="customerqn_reply[<?=$question['fqnid']?>]" rows="2" cols="80"></textarea>
                    <?php };?>

                  </ul>
                </div>
              </div>
            <?php }; ?>
            <div class="row qn">
              <div class="col-md-1">
                <h5 class="qn-num">#</h5>
              </div>
              <div class="col-md-11 qn-name">
                <h5>Notify client, SMS</h5>
                <ul class="qn-option">
                  <ul class="qn-option">
                    <li><textarea readonly id="feedbackSMS" required class="form-control" name="sms" rows="2" cols="80"></textarea> </li>
                    <li>
                      <label id="kisw" class="qn-option-container opt-align">Kisw
                        <input class="forKisw"  type="radio" name="lang" value="kisw">
                        <span class="checkmark"></span>
                      </label>
                      <label id="eng" class="qn-option-container opt-align">Eng
                        <input class="forEng" type="radio" name="lang" value="eng">
                        <span class="checkmark"></span>
                      </label>
                    </li>
                  </ul>
                </ul>
              </div>
            </div>
            <div id="default-btn" class="row row-btn-holder">
              <div class="col-md-12">
                <a href="?module=crms&action=feedback_index" class="btn btn-block btn-success">
                <i class="fa fa-list"></i> Ticket for feedback</a>
              </div>
            </div>
            <div id="forsave-btn" class="row row-btn-holder">
              <div class="col-md-6">
                <a href="?module=crms&action=feedback_index" class="btn btn-block btn-success">
                <i class="fa fa-list"></i> Ticket for feedback</a>
              </div>
              <div class="col-md-6">
                <button id="savemobile"  name="submit" class="btn btn-block btn-primary">
                <i class="fa fa-save"></i>  Save Fedback</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
<script>
//Kiswahile Message
$('#kisw').on('click', function(){
  if($('.forKisw').prop('checked')){
    $('#feedbackSMS').val('Mpendwa mteja, tunashukuru kwa maoni yako, wasiliana na sisi kupitia :- +<?=$department['mobile']?>');
  }
})
//English message
$('#eng').on('click', function(){
  if($('.forEng').prop('checked')){
    $('#feedbackSMS').val('Dear Customer, We thank you for giving us Feedback, Please contact Powercomputers Team for any issues :- +<?=$department['mobile']?>');
  }
})
function verifyTicket(obj){
  var ticketnumber = $(obj).val();
  $.get('?module=crms&action=verify_ticket&format=json&ticketId='+ticketnumber, null, function(data){
    var ticketStatus = eval(data);
    //console.log(ticketStatus[0]);
    if(ticketStatus[0].status == 'found'){
      if (ticketStatus[0].iscompleted == 3) {
        if (ticketStatus[0].isverified == 1) {
          if (ticketStatus[0].hasfeedback == 0) {
            $('#feedProceed').show('slow');
            $('#feedError').html(' ');
          }else{
            //has feedback
            $('#feedError').html('Ticket, has conducted feedback');
            $('#feedProceed').hide('slow');
          }
        }else{
          $('#feedError').html('Ticket Completed but Not Verified');
          $('#feedProceed').hide('slow');
        }
      }else{
        $('#feedError').html('Ticket Not Completed');
        $('#feedProceed').hide('slow');
      }
    }else{
      triggerError('not Found');
      $('#feedProceed').hide('slow');
    }
  })
}

$('#feedProceed').on('click', function(){
  var ticketId = $('#ticketIdForFeedback').val();
  window.location.href = "?id="+ticketId+"&module=crms&action=make_fedback";
})

/*function validateEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}*/

$('#form').on('submit', function(event){
  /*event.preventDefault();
	var email = $('#emailid').val();
	var number = $('#mobilenumber').val();
	var length = number.toString().length;
	if(length != 9){
		triggerError('Mobile is Min than 9 digits or Max than 9');
		$('#default-btn').show();
		$('#forsave-btn').hide();
	}else{
    $('#default-btn').hide();
		$('#forsave-btn').show();
    $(this).submit();
	}*/

})

function emailverification($obj){
  var input = $(obj);
  var email = input.val();
}

function checkmobilenumber(obj){
	var input = $(obj);
	var number = input.val();
	var length = number.toString().length;
	if(length != 9){
		triggerError('Mobile is Min than 9 digits or Max than 9');
		$('#default-btn').show();
		$('#forsave-btn').hide();
	}else{
    $('#default-btn').hide();
		$('#forsave-btn').show();
	}
}
</script>
