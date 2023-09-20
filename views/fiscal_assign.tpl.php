<style media="screen">
	h5{
		font-weight:600;
	}
	.align-right{
		float:right;
		position: relative;
    top: -20px;
	}
	.center-panel{
		width:50%;
		margin:0 auto;
	}
</style>
<header class="page-header">
	<h2>Re-Assign To Fiscalize</h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="panel center-panel">
			<header class="panel-heading">
				<h2 class="panel-title">Assign To</h2>
				<a href="?module=home&action=index" class="align-right btn btn-primary"><i class="fa fa-home"></i> </a>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('serials','fiscal_reassign_save')?>">
					<input type="hidden" name="fiscal[serialId]" value="<?=$serialId?>" />
					<div class="row">
						<div class="col-md-12">
							<h5>Technician name</h5>
							<select name="fiscal[fiscalrequestby]" required class="form-control mb-md">
								<option value=""></option>
								<? foreach ($users as $r){?>
									<option <?=selected($usersid,$r['id'])?> value="<?=$r['id']?>"><?=$r['name']?></option>
								<?}?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<div class="col-md-6">
								<a href="?module=serials&action=serial_index" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back</a>
							</div>
							<div class="col-md-6">
								<button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Send</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</section>
	</div>
<script>
	$(function(){
		$("#name").focus();
	})
</script>
