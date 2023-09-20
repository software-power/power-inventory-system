<style media="screen">
.centre-panel {
	width: 73%;
	margin: 0 auto;
}
</style>
<header class="page-header">
	<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> Model</h2>
</header>
<div class="centre-panel">
	<div class="col-lg-12">
		<section class="panel">
			<header class="panel-heading">
				<h2 class="panel-title">Brand Details</h2>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('model','model_save')?>" >
					<div class="row">
						<div class="col-md-6">
							<input type="hidden" name="id" value="<?=$model['id']?>">
							<h5>Brand Name</h5>
							<input type="hidden" name="id" value="<?=$model['id']?>">
							<input id="name" title="Name is required" placeholder="Brand Name" type="text" class="form-control" name="model[name]" value="<?=$model['name']?>">
						</div>
						<div class="col-md-6">
							<h5>Status</h5>
							<select name="model[status]" class="form-control">
								<option value="active" <?=selected($model['status'],'active')?>>Active</option>
								<option value="inactive" <?=selected($model['status'],'inactive')?>>In-Active</option>
							</select>
						</div>
					</div>
	        <div class="form-group">
						<div class="col-md-6">
							<a href="?module=model&action=model_list" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back To List</a>
						</div>
						<div class="col-md-6">
							<button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save Brand</button>
						</div>
	        </div>
	      </form>
			</div>
		</section>
	</div>
</div>
