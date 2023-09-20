<style media="screen">
	.btn-holder{
		float:right;
	}
	.center-panel{
		width:60%;
		margin:0 auto;
	}
	.required{
		font-size:15px;
	}
	h5 {
    font-weight: 700;
}
</style>
<header class="page-header">
	<h2>Assign Ticket</h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="center-panel">
			<header class="panel-heading">
				<div class="btn-holder">
					<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i></a>
				</div>
				<h2 class="panel-title">Ticket Details</h2>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('tickets','ticket_assign_save')?>">
					<div class="row">
						<div class="col-md-6">
							<h5>Serial Number</h5>
							<input type="hidden" name="id" value="<?=$ticket['id']?>">
							<input type="text" class="required form-control" id="serialno" title="Serial no is required" style="width:100%" value="<?=$ticket['serialno']?>" readonly>
						</div>
						<div class="col-md-6">
							<h5>Product</h5>
							<input type="text" class="required form-control" id="product" title="Product is required" style="width:100%" value="<?=$ticket['product']?>" readonly>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<h5>Department</h5>
							<input type="text" class="required form-control" id="department" title="Department is required" style="width:100%" value="<?=$ticket['department']?>" readonly>
						</div>
						<div class="col-md-6">
							<h5>Client</h5>
							<input type="text" class=" form-control" id="client" title="Client is required" style="width:100%" value="<?=$ticket['client']?>" readonly>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h5>Assign to User</h5>
							<select name="ticket[assignedto]" required class="form-control mb-md">
								<option selected disabled>--Choose Staff--</option>
								<? foreach ($users as $r){?>
									<option <?=selected($ticket['assignedto'],$r['id'])?> value="<?=$r['id']?>"><?=$r['name']?></option>
								<?}?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6">
							<a href="<?=$fromurl?>" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-arrow-left"></i> Back</a>
						</div>
						<div class="col-md-6">
							<button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Assign</button>
						</div>
					</div>
				</form>
			</div>
		</section>
	</div>
</div>
<script>
	$(function(){
		$("#name").focus();
	})
</script>
