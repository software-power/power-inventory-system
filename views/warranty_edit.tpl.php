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
</style>
<header class="page-header">
	<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> Warranty</h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="panel" style="width:70%;margin:0 auto">
			<header class="panel-heading">
				<div class="btn-holder">
	  			<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
	      </div>
				<h2 class="panel-title">Warranty Details</h2>
			</header>
			<div class="panel-body">
				<form  id="form" class="form-horizontal form-bordered" method="post" action="<?=url('warranties','save_warranty')?>">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12">
								<h5>Serial Number</h5>
							</div>
							<div class="col-md-12">
								<input type="hidden" name="warrantyid" value="<?=$warranty['id']?>">
								<input onblur="getSerialDetails(this);" placeholder="Serial Number" type="text" required class="form-control" id="serialno" title="Name is required" style="width:100%" name="warranty[name]" value="<?=$warranty['serial'];?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-12">
								<h5>Client Name</h5>
							</div>
							<div class="col-md-12">
								<input id="clientid" class="form-control" readonly type="text" name="clientname" value="">
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-12">
								<h5>Product Type</h5>
							</div>
							<div class="col-md-12">
								<input id="productid" class="form-control" readonly type="text" name="clientname" value="">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-12">
								<h5>Invoice Number</h5>
							</div>
							<div class="col-md-12">
								<input required title="Invoice Number" placeholder="Invoice Number" type="text" class="form-control" name="warranty[invoiceno]" value="<?=$warranty['invoiceno']?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-12">
								<h5>Invoice Amount</h5>
							</div>
							<div class="col-md-12">
								<input required title="Invoice Amount" placeholder="Amount" type="text" class="form-control" name="warranty[invoiceamount]" value="<?=$warranty['invoiceno']?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-12">
								<h5>Warranty From</h5>
							</div>
							<div class="col-md-12">
								<small>Day of starting Warranty</small>
								<input type="text" readonly class="form-control datepicker" name="warranty[warrantydatefrom]" value="<?if ($warranty['warrantydatefrom']>0) echo fDate($warranty['warrantydatefrom'],'d/m/Y'); else echo fDate(TODAY,'d/m/Y')?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-12">
								<h5>Warranty To</h5>
							</div>
							<div class="col-md-12">
								<small>Day of Ending Warranty</small>
								<input type="text" readonly class="form-control datepicker" name="warranty[warrantydateto]" value="<?if ($warranty['warrantydateto']>0) echo fDate($warranty['warrantydateto'],'d/m/Y'); else echo fDate(TODAY,'d/m/Y')?>">
							</div>
						</div>
					</div>
					<div class="form-group">
				  	<div id="blockBtn" class="col-md-12">
					    <div class="col-md-12">
					      <a href="?module=warranties&action=warranty_list" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back</a>
					    </div>
				  	</div>
				  	<div id="saveBtn" class="col-md-12">
					    <div class="col-md-6">
					      <a href="?module=warranties&action=warranty_list" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back</a>
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
$('#serialno').focus();
	<?if($_GET['amcno']){?>
		$(window).on("load",function(){
			window.open('?module=serials&action=print_amc&amcno=<?=$_GET['amcno'];?>', '_blank');
		});
	<?}?>

	function getSerialDetails(obj){
		var serial = $(obj).val();

		if(serial == '' || serial == null ){
			console.log(serial);
		}else{
			$.get("?module=serials&action=getSerialDetails&format=json&serialnumber="+serial,null, function(data){
				var serialdetails = eval(data);
				if (serialdetails[0].status == 'found') {
					$('#clientid').val(serialdetails[0].client);
					$('#productid').val(serialdetails[0].productname);
					triggerMessage('Serial Number Found');
					$('#blockBtn').hide();
					$('#saveBtn').show();
				}else{
					triggerError('Serial Number Not Found');
					$('#blockBtn').show();
					$('#saveBtn').hide();
					$('#clientid').val('');
					$('#productid').val('');
				}
			});
		}
	}
</script>
