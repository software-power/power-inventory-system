<script>
	function addNewItem(btnObj) {
		objRow = document.getElementById("firstrow").cloneNode(true);


		inputTags = objRow.getElementsByTagName("INPUT");
		inputTags[0].value = '';
		inputTags[1].value = '';
		inputTags[2].style.display = 'block';

		document.getElementById("firstrow").parentNode.appendChild(objRow);
}


function removeItemRow(btnObj) {
		objRow = btnObj.parentNode.parentNode;
		objRow.parentNode.removeChild(objRow);
}
</script>
<header class="page-header">
	<h2><?//if ($edit) //echo 'Edit'; else echo 'Add';?> Message</h2>
</header>

<div class="row">
	<div class="col-lg-12">
		<section class="panel" style="margin:0 auto; width:70%">
			<header class="panel-heading">
				<div class="panel-actions">
					<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div>

				<h2 class="panel-title"> Compose Message</h2>
        <p style="font-size:14px;font-style:italic;">*NOTE: make sure the job card status is completed, in order to send message to the client.</p>

			</header>
			<div class="panel-body">
        <div class="">
          <table class="crm_table" width="60%" style="margin:0 auto">
            <!-- action="?module=messages&action=send_message" -->
            <form id="message_form" action="?state_module=messages&state_action=compose_message&module=messages&action=send_message" method="post">
              <input id="jobcard_id" type="hidden" name="message[jobcardid]" readonly class="form-control mb-md">
              <tr>
                <td width="100px">JobCard Number</td>
                <td width="200px">
                  <input onblur="fetchdetails()" placeholder="Jobcard number" id="jobcardnumber" type="text" name="message[jobcardid]" class="form-control mb-md">
                </td>
              </tr>
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
                  <!-- <span onclick="closeModel(this)" id="model-dismis" class="btn btn-danger modal-dismiss"> <i class="fa fa-close"></i> CLOSE</span> -->
                </div>
                </td>
              </tr>
            </form>
          </table>
        </div>
      </div>
    </section>
  </div>
</div>


<script>

function fetchdetails(){
  var jobcard = $('#jobcardnumber').val();
  var email = $('#client_email')//.val();
  var mobile = $('#client_mobile')//.val();
  var name = $('#client_name')//.val();
  //console.log(jobcard)

  $.get("?module=messages&action=fetchjobcard&format=json&jobcard="+jobcard,null,function(d){
    var CC = eval(d);

    var status = CC[0].status;
    var ticketstatus = CC[0].statusid;
    var serialnumber = CC[0].serialno;

    if (status == 'found') {

      if (ticketstatus == 'Completed') {
            triggerMessage('Good job, your jobcard is completed and you can notify the client');
            email.val(CC[0].email);
            mobile.val(CC[0].mobile);
            name.val(CC[0].name);
            var serial = CC[0].serialno;
            var id = CC[0].jobid;
            console.log(CC[0].statusid);

            var kiswahili = "Mpendwa mteja mashine yako yenye number "+serial+" iko tiyari, job card: "+id+" powercomputers";
            var english = "Our Dear customer your product with serial no "+serial+" is ready, job Card no: "+id+" powercomputers";

            $('#kiswahili').val(kiswahili);
            $('#english').val(english);

      }else{
        triggerError('Please this jobCard with serial '+serialnumber+' is not Completed, dont run away to do your job');
      }

    }else{
      triggerError('The Jobcard number is not exists');
    }

  });
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
  var jobcardnumber = $('#jobcardnumber').val();
  // console.log(email);

	if (jobcardnumber == "" || jobcardnumber == null) {
		alert('JobCard number cant be empty')
	}else{
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
	}

})


</script>
