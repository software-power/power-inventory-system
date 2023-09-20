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
</style>
<header class="page-header">
	<h2>Message Reports</h2>
</header>

<div class="col-md-12 col-lg-12">
  <form>
    <input type="hidden" name="module" value="messages">
    <input type="hidden" name="action" value="message_index">
    <h4><div id="open_filter" class="btn btn-primary"> <i class="fa fa-filter"></i> Search Filter</div> </h4>
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
			<div class="panel-actions">
				<a href="?module=home&action=index"> <i class="fa fa-home"></i> </a>
				<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
			</div>

			<h2 class="panel-title">List of Messages</h2>
		</header>

	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px">
		<thead>
			<tr>
				<th>No.</th>
				<th>JobCard</th>
				<th>Sender</th>
				<th>Receiver</th>
				<th>Destination1</th>
				<th>Destination2</th>
				<th>Email</th>
				<th>Text</th>
				<th>Language</th>
				<th>Media</th>
				<th>Message ID</th>
				<th>Message Status</th>
				<th>Email ID</th>
        <th>Email status</th>
        <th>Sent date</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($messagelist as $id=>$R) { ?>
				<tr>
					<td width="80px"><?=$id+1?></td>
					<td width="80px"><?=$R['jobcardid']?></td>
					<td><?=$R['sendername']?></td>
					<td><?=$R['receivername']?></td>
					<td><?=$R['destination1']?></td>
					<td><?=$R['destination2']?></td>
					<td><?=$R['email']?></td>
					<td><?=$R['text']?></td>
					<td><?=$R['language']?></td>
					<td><?=$R['media']?></td>
					<td><?=$R['messageid']?></td>
					<td><?=$R['messagestatus']?></td>
					<td><?=$R['email_id']?></td>
					<td><?=$R['emailstatus']?></td>
					<td><?=$R['doc']?></td>
				<?php } ?>
			</tbody>
		</table>
		</div>

		<script type="text/javascript">
			$('#open_filter').on('click', function(){
				$('#for-search-report').toggleClass('for-view-filter');
			})
		</script>
