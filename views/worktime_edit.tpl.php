<style media="screen">
#form h5 {
    font-size: 16px;
    font-weight: 600;
}
</style>
<header class="page-header">
<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> Work Time</h2>
</header>

<div class="row">
<div class="col-lg-12">
	<section class="panel" style="width: 46%;margin:0 auto">
		<header class="panel-heading">
			<h2 class="panel-title">TIme Details</h2>
		</header>
		<div class="panel-body">
			<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('schedules','timeslot_save')?>">
        <input type="hidden" name="id" value="<?=$timeslot['id']?>">
				<div class="row">
					<div class="col-md-6">
						<h5>Time</h5>
						<input type="text" required class="timepicker form-control" id="name" title="Name is required" style="width:100%" name="timeslot[name]" value="<?=$timeslot['name']?>">
					</div>
          <div class="col-md-6">
						<h5>Type</h5>
						<select name="timeslot[type]" class="form-control">
              <option value="" selected disabled>--Choose Type--</option>
              <option value="on" <?=selected($timeslot['type'],'on')?>>On</option>
							<option value="work" <?=selected($timeslot['type'],'work')?>>Work</option>
							<option value="lunch" <?=selected($timeslot['type'],'lunch')?>>Lunch</option>
							<option value="Off" <?=selected($timeslot['type'],'off')?>>Off</option>
						</select>
					</div>
				</div>
        <div class="row">
          <div class="col-md-12">
            <h5>Sort Number</h5>
            <input type="text" placeholder="Sort Number" class="form-control" name="timeslot[sortno]" value="<?=$timeslot['sortno']?>">
          </div>
        </div>
				<div class="row">
					<div class="col-md-12">
						<h5>Status</h5>
						<select name="timeslot[status]" class="form-control">
              <option value="" selected disabled>--Choose Status--</option>
							<option value="active" <?=selected($timeslot['status'],'active')?>>Active</option>
							<option value="inactive" <?=selected($timeslot['status'],'inactive')?>>In-Active</option>
						</select>
					</div>
				</div>
        <div class="form-group">
          <div class="col-md-12">
            <div class="col-md-6">
              <a href="?module=schedules&action=time_list" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back to list</a>
            </div>
            <div class="col-md-6">
              <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save</button>
            </div>
          </div>
        </div>
      </form>
      <script>
      $(function(){
        $('input.timepicker').timepicker({
    			timeFormat:'hh:mm',
    			dropdown:false,
    			scrollbar:false
    		});

        $("#name").focus();
      })
    </script>
