<style media="screen">
	h5{
		font-weight:700;
	}
	.action-holder{
		float:right;
	}
	.center-panel{
		width:71%;
		margin:0 auto;
	}
	.required{
		height:37px;
		font-size:14px;
	}
	input.contry-code {
    position: absolute;
    z-index: 9;
    font-size: 16px;
    font-weight: 600;
    width: 40px;
    border: none;
    left: 20px;
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
}
#mobile{
	text-align:center;
}
#savemobile{
  /* display:none; */
}
</style>
<header class="page-header">
	<h2>Ticket Transfer</h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="panel center-panel">
			<header class="panel-heading">
				<div class="action-holder">
					<a href="?module=home&action=index" class="btn btn-primary"><i class="fa fa-home"></i></a>
				</div>
				<h2 class="panel-title">Job Card details</h2>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('tickets','ticket_transfer_save')?>">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <h5>Ticket / Job card Number</h5>
                <input id="ticketid" type="hidden" name="id" value="<?=$ticket['jobid']?>">
                <input id="ticketno" onblur="getJobCardDetails(this)" type="text" placeholder="Ticket / Job card Number" class="required form-control" title="Job Card number is required" name="ticket[ticketid]" value="<?=$ticket['jobid']?>">
              </div>
              <div class="col-md-4">
                <h5>Serial Number</h5>
                <input id="serialno" readonly disabled type="text" placeholder="Serial Number" class="required form-control" title="Name is required" name="ticket[serialno]" value="<?=$ticket['serialno']?>">
              </div>
              <div class="col-md-4">
                <h5>Product Type</h5>
                <input id="productname" readonly disabled type="text" placeholder="Product" class="form-control" name="ticket[productname]" value="<?=$ticket['productname']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <h5>Client Name</h5>
                <input id="clientname" readonly disabled type="text" placeholder="Serial Number" class="required form-control" title="Name is required" name="ticket[clientname]" value="<?=$ticket['clientname']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <h5>Reported Problem</h5>
                <textarea id="problem" readonly disabled class="form-control" name="ticket[problem]" rows="3" cols="80"><?=$ticket['clientremark']?></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Transfer From <small>(Current Branch)</small> </h5>
                <input readonly type="hidden" id="branchid" name="ticket[branchid]" value="<?=$ticket['branchid']?>">
                <input readonly type="text" placeholder="Current branch" class="required form-control" id="transferfrom" title="Transfer From is required" name="ticket[transferfrom]" value="<?=$ticket['branchname']?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Transfer To <small>(To which branch)</small> </h5>
                <select class="required form-control" id="transferto" title="Transfer To is required" name="ticket[transferto]" >
                  <option selected disabled>-- Choose Branch --</option>
                  <?php foreach ($brachlist as $key => $branch){?>
                    <option <? if($userbranch == $branch['id']) echo 'disabled';?> value="<?=$branch['id']?>"><?=$branch['name']?></option>
                  <?php }; ?>
                </select>
              </div>
            </div>
          </div>
					<div class="form-group">
						<div class="" >
              <div class="col-md-12">
  							<div class="col-md-6">
  								<a href="?module=tickets&action=ticket_index&activetab=assigned_ticket" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Assigned Ticket</a>
  							</div>
  							<div class="col-md-6">
  								<button id="savemobile" type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-send"></i> Send Job card</button>
  							</div>
  						</div>
            </div>
					</div>
				</form>
			</div>
		</section>
	</div>
</div>
<script>
$(function(){
	$("#ticketno").focus();
})

function getJobCardDetails(obj){
  var ticketno=  $(obj).val();
	$.get('?module=tickets&action=getTicketDetails&format=json&jobid='+ticketno,null, function(data){
		var ticket = eval(data);
		if(ticket[0].status == 'found'){
			if(ticket[0].statustype != 'Pending'){
				triggerError('Ticket found, status not pending');
			}else{
				triggerMessage('Ticket found');
				$('#ticketid').val(ticket[0].jobid);
				$('#serialno').val(ticket[0].serialno);
				$('#clientname').val(ticket[0].clientname);
				$('#problem').val(ticket[0].clientremark);
				$('#productname').val(ticket[0].productname);
				$('#branchid').val(ticket[0].branchid);
				$('#transferfrom').val(ticket[0].branchname);
			}
		}else{
			triggerError('Ticket not found');
		}
	});
}
</script>
