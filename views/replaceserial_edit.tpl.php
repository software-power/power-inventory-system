<div class="row">
	<div class="col-lg-12">
		<section class="panel" style="width:70%; margin:0 auto">
			<header class="panel-heading">
				<h2 class="panel-title">Serial Details</h2>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('serials','save_replacement')?>">
					<div class="row">
						<div class="col-md-4">
							<h5>Old Serial Number</h5>
							<input placeholder="Old Serial Number" type="text" class="form-control" id="name" title="Old serial is required" style="width:100%" name="serial[oldname]" value="<?if($_GET['newserial']){echo $_GET['newserial'];}else{echo $serial['name'];}?>">
							<input type="hidden" name="id" value="<?=$serial['id']?>">
						</div>
						<div class="col-md-4">
							<h5>New Serial Number</h5>
							<input placeholder="New Serial Number" type="text" class="form-control" id="name" title="New serial is required" style="width:100%" name="serial[newserial]" value="<?if($_GET['newserial']){echo $_GET['newserial'];}else{echo $serial['name'];}?>">
							<input type="hidden" name="id" value="<?=$serial['id']?>">
						</div>
						<div class="col-md-4">
							<h5>Status</h5>
							<select name="serial[status]" class="form-control">
								<option value="active" <?=selected($serial['status'],'active')?>>Active</option>
								<option value="inactive" <?=selected($serial['status'],'inactive')?>>In-Active</option>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<a href="?module=serials&action=serial_index" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back</a>
						</div>
						<div class="col-md-6">
							<button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Replace Serial</button>
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

		toggleAmc();

	})


	function toggleAmc(){

		var amc = $("#amc").val();

		if (amc == 'no') $(".amcDet").hide();
		if (amc == 'yes') $(".amcDet").show();
	}
</script>
