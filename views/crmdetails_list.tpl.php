<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
.ticketholder,.ticketholder-suggestion {
    position: fixed;
    z-index: 99;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5803921568627451);
    display: none;
    overflow-y:scroll;
}
.table-holder {
    position: relative;
    top: 159px;
    background: white;
    width: 88%;
    margin: 0 auto;
    margin-left: 121px;
}
.title-model {
    padding-top: 45px;
    float: left;
    margin-left: 9px;
}
.close-btn-holder {
    padding-top: 10px;
    float: right;
    margin-right: 7px;
}
.panelControl {
    float: right;
}
.qnname {
  font-size: 15px;
  text-transform: capitalize;
  font-weight: 700;
}
.report-th {
  font-size: 15px;
  padding: 10px;
  border-bottom: 1px solid #ecedf0;
}
.report-td {
  padding-left: 25px;
  text-transform: capitalize;
  margin-top: 6px;
  display: inline-block;
  background: #ecedf0;
  width: 100%;
  height: 35px;
  font-weight: 500;
}
.align-box {
  width: 77%;
  margin: 0 auto;
}
.qname{
  text-transform:capitalize;
}
div.dataTables_wrapper div.dataTables_info{
  padding-left:10px;
}
div.dataTables_wrapper div.dataTables_filter input {
    width: 91%;
    padding: 10px;
    height: 39px;
    font-size: 15px;
    margin-top: 10px;
}
.dt-buttons {
    position: absolute;
    display: block;
    padding: 10px;
    z-index:10
}
.control-datatable{
  position: relative;
  z-index: 10;
}
.center-index{
  text-align:center;
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
.widget-box {
  background: #ffffff;
  width: 100%;
  height: 95px;
  padding: 10px;
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
.widget-box-holder {
    width: 20%;
    margin: 0 auto;
}
</style>
<header class="page-header">
	<h2>CRM Details Report</h2>
</header>

<!-- suggestion view -->
<div class="ticketholder-suggestion">
	<div class="table-holder">
		<div class="control-datatable">
      <div class="title-model">
  			<h4><i class="fa fa-file"></i> Suggestions Box</h4>
  		</div>
  		<div class="close-btn-holder">
  			<button class="btn btn-danger" onclick="closeTableSuggestion()" type="button" name="button">
        <i class="fa fa-close"></i>  CLOSE</button>
  		</div>
    </div>
		<table class="table table-hover mb-none" style="font-size:13px;" id="crmTableSuggestion">
			<thead>
				<tr>
					<th>#</th>
					<th>Ticket</th>
					<th>Client Name</th>
					<th>Branch</th>
					<th>Depart</th>
					<th>Assigned To</th>
					<th>Suggestion</th>
					<th>Support Type</th>
					<th>Job Card</th>
				</tr>
			</thead>
			<tbody id="suggestionList">
			</tbody>
		</table>
		</div>
</div>
<!-- suggestion end here -->


<!-- details view -->
<div class="ticketholder">
	<div class="table-holder">
		<div class="control-datatable">
      <div class="title-model">
  			<h4><i class="fa fa-file"></i> Tickets/Clients CRM Detail Report</h4>
  		</div>
  		<div class="close-btn-holder">
  			<button class="btn btn-danger" onclick="closeTable()" type="button" name="button">
        <i class="fa fa-close"></i>  CLOSE</button>
  		</div>
    </div>
		<table class="table table-hover mb-none" style="font-size:13px;" id="crmTable">
			<thead>
				<tr>
					<th>#</th>
					<th>Ticket</th>
					<th>Serial No</th>
					<th>Client Name</th>
					<th>Branch</th>
					<th>Product</th>
					<th>Depart</th>
					<th>Assigned To</th>
					<th>Question</th>
					<th>Answer</th>
					<th>Support Type</th>
					<th>Job Card</th>
				</tr>
			</thead>
			<tbody id="tbodyforticekt">
			</tbody>
		</table>
		</div>
</div>
<!-- details end here -->

<div class="align-box">
  <div class="col-md-12">
    <div class="row widget-holder">
      <div class="col-md-6">
        <div class="widget-box">
          <div class="widget-box-holder">
            <span class="nameCharacter"><?=$ticketWithFeedback['numberOfticket'];?></span>
          </div>
        </div>
        <span class="role-tag">Total number of Ticket (s) which has feedback</span>
      </div>
      <div class="col-md-6">
        <div class="widget-box">
          <div class="widget-box-holder">
            <span class="nameCharacter"><?=$ticketWithNoFeedback['numberOfticket'];?></span>
          </div>
        </div>
        <span class="role-tag">Total number of Ticket (s) which has no feedback</span>
      </div>
    </div>
  	<section class="panel">
  		<header class="panel-heading">
  			<div class="panelControl">
  				<button class="btn btn-primary" onclick="openSuggestionBox()"> <i class="fa fa-archive"></i> Suggestions Box </button>
  				<a class="btn btn-primary" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> </a>
  			</div>
  			<h2 class="panel-title">Question (s) Summary Report</h2>
  		</header>
  		<div class="panel-body">
  			<div class="">
          <div class="report-holder">

            <?php foreach ($report as $question_name => $qn_answers){ ?>

              <div class="qn-name col-md-12"><h5 class="qnname"><?=$question_name?></h5> </div>
              <div class="col-md-12">
                <div class="row report-row-th">
                  <div class="col-md-6">
                    <p class="report-th">Answer</p>
                  </div>
                  <div class="col-md-6">
                    <p class="report-th">Ticket / Client Total</p>
                  </div>
                </div>

                <?php foreach ($qn_answers as $possibleAnswer => $details){?>

                  <div class="row report-row-td">
                    <div class="col-md-6">
                      <span class="report-td"><?=$possibleAnswer?></span>
                    </div>
                    <div class="col-md-6">
                      <span class="report-td">
                        <button onclick="getCRMdetails(<?=$details['questionid']?>,<?=$details['answerid']?>,<?=$details['customereply']?>);" class="btn btn-primary" name="button">
                          <i class="fa fa-user"></i> <?=$details['customers']?>
                        </button>
                      </span>
                    </div>
                  </div>

                <?php };?>

              </div>
            <?php }; ?>

          </div>
  			</div>
  		</div>
  	</section>
  </div>
</div>
<script type="text/javascript">

function openSuggestionBox(){
  $('.ticketholder-suggestion').show('slow');
  $.get('?module=crms&action=getSuggestions&format=json',null,function(data){
    var tickets = eval(data);
    if (tickets[0].status == 'found') {
      $('#suggestionList').empty();
      $.each(tickets[0].details, function(index, ticket){
        count = parseInt(index) + 1;
        var tableRow = "<tr>"+
          "<td class='center-index'>"+count+"</td><th class='center-index'>"+ticket.ticketId+"</td>"+
          "<td>"+ticket.clientname+"</td><td>"+ticket.branchname+"</td>"+
          "</td><td>"+ticket.departname+"</td>"+
          "<td>"+ticket.assignedname+"</td><td class='qname'>"+ticket.suggestion+"</td>"+
          "</td><td>"+ticket.supportname+"</td>"+
          "<td style='text-align:center'><a title='JOB CARD' target='_blank' href='?module=tickets&action=jobcard&id="+ticket.ticketId+"'><i class='fa fa-file'></i></a></td></tr>";

          $('#suggestionList').append(tableRow);
      })
      printSettings('crmTableSuggestion');
    }
  })
}

function getCRMdetails(questionId,answerid){
  var qnId = questionId;
  var resId = answerid;//var replyId = customereplyid;
  $('.ticketholder').show('slow');
  $.get('?module=crms&action=getCRMdetails&format=json&qnid='+qnId+'&resid='+resId,null,function(data){
    var tickets = eval(data);
    if (tickets[0].status == 'found') {
      $('#tbodyforticekt').empty();
      $.each(tickets[0].details, function(index, ticket){
        count = parseInt(index) + 1;
        var tableRow = "<tr>"+
          "<td class='center-index'>"+count+"</td><th class='center-index'>"+ticket.ticketId+"</td><td>"+ticket.serialno+"</td>"+
          "<td>"+ticket.clientname+"</td><td>"+ticket.branchname+"</td>"+
          "<td>"+ticket.productname+"</td><td>"+ticket.departname+"</td>"+
          "<td>"+ticket.assignedname+"</td><td class='qname'>"+ticket.question+"</td>"+
          "<td>"+ticket.actualanswer+"</td><td>"+ticket.supportname+"</td>"+
          "<td style='text-align:center'><a title='JOB CARD' target='_blank' href='?module=tickets&action=jobcard&id="+ticket.ticketId+"'><i class='fa fa-file'></i></a></td></tr>";

          $('#tbodyforticekt').append(tableRow);
      })
      printSettings('crmTable');
    }
  })
}

function closeTableSuggestion(){
  table = $('#crmTableSuggestion').DataTable();
  table.destroy();//destory the datatable
  $('.ticketholder-suggestion').hide('slow');
}

function closeTable(){
  table = $('#crmTable').DataTable();
  table.destroy();//destory the datatable
  $('.ticketholder').hide('slow');
}

function printSettings(tableName){
  $('#'+tableName).DataTable({
    dom: '<"top"fB>t<"bottom"ip>',
    colReorder:true,
    keys:true,
    buttons: [
      'copyHtml5', 'excelHtml5', 'pdfHtml5','csvHtml5','print']
  });
}

</script>
