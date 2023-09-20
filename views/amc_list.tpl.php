<style media="screen">
	.panel-actions a, .panel-actions .panel-action{
		font-size: 21px;
	}
  form table tr td{
    padding-left: 10px;
  }
  .for-column{
    width:100px;
  }
  .for-btn {
    padding: 16px;
    display: block;
}
.for-holder{
	height:0px;
	overflow: hidden;
	transition: .3s;
	background: white;
}
.for-view-filter{
height: 165px;
padding:10px;
}
.btn-holder {
  float: right;
}
</style>
<header class="page-header">
	<h2>List of AMC</h2>
</header>

<div class="col-md-12 col-lg-12">
  <form>
    <input type="hidden" name="module" value="messages">
    <input type="hidden" name="action" value="message_index">
    <!-- <h4><div id="open_filter" class="btn btn-primary"> <i class="fa fa-filter"></i> Search Filter</div> </h4> -->
    <div id="for-search-report" class="for-holder">
			<div class="col-md-12">
				<div class="col-md-4">
					<span>JobCard</span>
					<span><input type="text" class="form-control" placeholder="Enter Job Card number" name="jobcard" value="<?=$jobcard?>"/></span>
				</div>
				<div class="col-md-4">
					<span>Sender</span>
					<span>
						<select class="form-control" name="user">
								<option value=""></option>
										<? foreach ($users as $u){?>
											<option <?=selected($u['id'],$user)?> value="<?=$u['id']?>"><?=$u['name']?></option>
											<!-- <?//if ($r['id'] == $statusid) echo 'selected';?> -->
										<?}?>
							</select>
					</span>
				</div>
				<div class="col-md-4">
					<div class="col-md-6">
						<span class="for-btn"><button type="button" class="mb-xs mt-xs mr-xs btn btn-default" onclick="location.href='<?=url('messages','message_index')?>'"><i class="fa fa-refresh"></i> Reset</button></span>
					</div>
					<!--<span>Clients</span>
					<span>
						<select class="form-control" name="client">
								<option value=""></option>
										<? //foreach ($clients as $c){?>
											<option <?//selected($c['id'],$clients)?> value="<?//$c['id']?>"><?//$c['name']?></option>
											<!- <?//if ($r['id'] == $statusid) echo 'selected';?> -->
										<?//}?>
							</select>
					</span>
				</div>
			</div>
			<div class="col-md-12">
				<div class="col-md-4">
					<span>From Date</span>
					<span>
						<input type="text" readonly name="fromdate"  class="datepicker form-control" value="<?=$fromdate?>">
					</span>
				</div>
				<div class="col-md-4">
					<span>To Date</span>
					<span>
						<input type="text" readonly name="todate"  class="datepicker form-control" value="<?=$todate?>">
					</span>
				</div>
				<div class="col-md-4">
					<div class="col-md-6">
						<span class="for-btn"><button class="mb-xs mt-xs mr-xs btn btn-success" type="submit"> <i class="fa fa-search"></i> SEARCH</button></span>
					</div>

				</div>
			</div>
    </div>

  </form>
</div>


<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading for-heading">
			<div class="btn-holder">
        <a class="btn btn-success" href="?module=amcs&action=add_amc"> <i class="fa fa-plus"></i> Create AMC</a>
        <a class="btn btn-success" href="?module=amcs&action=amc_services"> <i class="fa fa-list"></i> AMC Services</a>
  			<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
      </div>
			<h2 class="panel-title">List of AMC</h2>
		</header>

	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px">
		<thead>
			<tr>
				<th>No.</th>
				<th>AMC number</th>
				<th>Serial Ref.</th>
				<th>AMC Start</th>
				<th>AMC End</th>
				<th>Serial Number</th>
				<th>Client Name</th>
				<th>Created by</th>
				<th>Created Date</th>
				<th>AMC status</th>
				<th>AMC Doc</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($amclist as $id=>$R) { ?>
				<tr>
					<td width="80px"><?=$id+1?></td>
					<td width="80px"><?=$R['amcnumber']?></td>
					<td><?=$R['serialid']?></td>
					<td><?=fDate($R['amcstart'])?></td>
					<td><?=fDate($R['amcend'])?></td>
					<td><?=$R['serialname']?></td>
					<td><?=$R['clientname']?></td>
					<td><?=$R['createdbyname']?></td>
					<td><?=fDate($R['createddate'])?></td>
					<td><?=$R['amcstatus']?></td>
          <td>
            <a class="btn btn-success" target="_blank" href="?module=amcs&action=print_amc&amcno=<?=$R['amcnumber']?>" title="Print AMC"><i class="fa fa-file"></i></a
          </td>
				<?php } ?>
			</tbody>
		</table>
		</div>

		<script type="text/javascript">
			$('#open_filter').on('click', function(){
				$('#for-search-report').toggleClass('for-view-filter');
			})
		</script>
