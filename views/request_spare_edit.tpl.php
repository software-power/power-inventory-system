<style media="screen">
h5 {
	font-size: 16px;
	font-weight: 600;
}
.btn-holder {
  float: right;
}
#saveBtn{
	display:none;
}
.spare_row {
    margin-top: 10px;
}
.rowtech {
    padding: 18px;
    background: #fdfdfd;
    border-radius: 5px;
    border: 2px solid #ecedf0;
}
.select2-container .select2-selection--single {
    height: 36px;
}
div#spare_row_drop {
    margin-top: 17px;
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
.num-hide-ov {
    width: 24px;
    top: 3px;
}
</style>
<header class="page-header">
	<h2><?if($filter) echo 'Issue Spare'; else echo 'Request Spare'?></h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="panel" style="width:70%;margin:0 auto">
			<header class="panel-heading">
				<div class="btn-holder">
	  			<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
	      </div>
				<h2 class="panel-title">Spare Details</h2>
			</header>
			<div class="panel-body">
				<form  id="form" class="form-horizontal form-bordered" method="post" action="<?=url('spares','save_request')?>">
					<div class="row">
						<div class="col-md-3">
							<div class="col-md-12">
								<h5>Ticket/Jobcard No.</h5>
							</div>
							<div class="col-md-12">
								<input type="hidden" name="filter" value="<?=$filter?>">
								<input type="hidden" name="id" value="<?=$ticket['id']?>">
								<input type="hidden" name="serialid" value="<?=$ticket['ticketid']?>">
								<input type="hidden" id="clientid" name="ticket[clientid]" value="<?=$ticket['clientid']?>">
								<input onblur="getJobCardDetails(this);" placeholder="Ticket/Jobcard No" type="text" required class="form-control" id="ticketid" title="Name is required" name="ticket[ticketid]" value="<?=$ticket['ticketid'];?>">
							</div>
						</div>
            <div class="col-md-5">
							<div class="col-md-12">
								<h5>product Type</h5>
							</div>
							<div class="col-md-12">
								<input id="protype" class="form-control" readonly type="text" name="" value="<?=$ticket['productname'];?>">
							</div>
						</div>
						<div class="col-md-4">
							<div class="col-md-12">
								<h5>Serial Number</h5>
							</div>
							<div class="col-md-12">
								<input id="serialid" class="form-control" readonly type="text" name="" value="<?=$ticket['serialno'];?>">
							</div>
						</div>
					</div>
					<div id="spare_row_drop" class="rowtech col-md-12">
            <div class="row">
              <div class="col-md-7">
                <h5>Spare Part</h5>
              </div>
              <div class="col-md-4">
                <h5>Spare Qty</h5>
              </div>
              <div class="col-md-1">
                <button onclick="addNewSpareRow();" type="button" name="button" class="btn"><i class="fa fa-plus"></i></button>
              </div>
            </div>
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
              <div class="col-md-4">
                <span class="num-hide num-hide-ov"></span>
                <input placeholder="Quantity " title="Spare quantity" type="number" class="spare_qty form-control" name='spare_qty[]' value="<?=$spare_order['qty']?>"/>
              </div>
              <div class="col-md-1">
                <button onclick="removeSpareRow(this);" type="button" name="button" class="btn"><i class="fa fa-minus"></i> </button>
              </div>
            </div>

          </div>
					<div class="form-group">
				  	<div id="blockBtn" class="col-md-12">
					    <div class="col-md-12">
					      <a href="<?=$backto?>" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back</a>
					    </div>
				  	</div>
				  	<div id="saveBtn" class="col-md-12">
					    <div class="col-md-6">
					      <a href="<?=$backto?>" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back</a>
					    </div>
					    <div class="col-md-6">
					      <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save</button>
					    </div>
				  	</div>
					</div>
				</form>
			</div>
		</section>
	</div>
</div>
<script type="text/javascript">
$(function(){
  $('.spareid').select2({width:'100%',minimumInputLength:3,
    ajax:{
      url:"?module=products&action=getProducts&format=json",dataType:'json',delay:250,quietMillis:200,
      data: function (term) {return {search:term};},
      results:function (data,page){return{result:data};}
    }
  });
})
$('#serialno').focus();
	function getJobCardDetails(obj){
		var ticketid = $(obj).val();
    if(ticketid == '' || ticketid == null){

    }else{
      $.get('?module=tickets&action=getTicketDetails&format=json&jobid='+ticketid, function(data){
        var ticket = eval(data);
        console.log(ticket);
        if(ticket[0].status == "found"){
          if(ticket[0].statustype == 'Pending'){
            $('#protype').val(ticket[0].productname);
            $('#serialid').val(ticket[0].serialno);
            $('#clientid').val(ticket[0].clientid);
            triggerMessage('JobCard/Ticket is found');
            $('#blockBtn').hide();
            $('#saveBtn').show();
          }else {
            triggerError('Jobcard/ticket found but is completed');
            $('#blockBtn').show();
  					$('#saveBtn').hide();
  					$('#protype').val('');
  					$('#serialid').val('');
          }
        }else{
          triggerError('Jobcard/ticket Not found or completed');
          $('#blockBtn').show();
					$('#saveBtn').hide();
					$('#protype').val('');
					$('#serialid').val('');
        }
      });
    }
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

  function addNewSpareRow(){
		var row = '<div class="row spare_row">'+
		'<div class="col-md-7">'+
		'<select onchange="checkSpare(this)" class="form-control spareid" name="spare_spareid[]">'+
		'<option selected disabled>--Choose Spare--</option>'+
		'</select></div>'+
		'<div class="col-md-4"><span class="num-hide num-hide-ov"></span>'+
		'<input placeholder="Quantity" title="Spare Quantity" type="number" class="spare_qty form-control" name="spare_qty[]" value=""/>'+
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
</script>
