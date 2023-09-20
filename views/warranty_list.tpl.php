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
	<h2>List of Warranty</h2>
</header>

<div class="col-md-12 col-lg-12">
  <form>
    <input type="hidden" name="module" value="messages">
    <input type="hidden" name="action" value="message_index">
    <!-- <h4><div id="open_filter" class="btn btn-primary"> <i class="fa fa-filter"></i> Search Filter</div> </h4> -->
  </form>
</div>
<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading for-heading">
			<div class="btn-holder">
        <a class="btn btn-success" href="?module=warranties&action=add_warranty"> <i class="fa fa-plus"></i> Create Warranty</a>
        <a class="btn btn-success" href="?module=warranties&action=warranty_services"> <i class="fa fa-list"></i> Warranty Services</a>
  			<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
      </div>
			<h2 class="panel-title">List of Warranty</h2>
		</header>
	  <div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px">
      		<thead>
      			<tr>
      				<th>No.</th>
      				<th>Warranty number</th>
      				<th>Serial Number</th>
      				<th>Warranty Start</th>
      				<th>Warranty End</th>
      				<th>Client Name</th>
      				<th>Created by</th>
      				<th>Created Date</th>
      			</tr>
      		</thead>
    		  <tbody>
    			<?php foreach($warrantyList as $id=>$R) { ?>
    				<tr>
    					<td width="50px"><?=$id+1?></td>
    					<td width="80px" style="text-align:center"><?=$R['id']?></td>
    					<td><?=$R['serialname']?></td>
    					<td><?=fDate($R['warrantydatefrom'])?></td>
    					<td><?=fDate($R['warrantydateto'])?></td>
    					<td><?=$R['clientname']?></td>
    					<td><?=$R['createdbyname']?></td>
    					<td><?=fDate($R['doc'])?></td>
    				<?php } ?>
    			</tbody>
        </table>
		</div>
  </div>
  </section>
</div>
<script type="text/javascript">
  $('#open_filter').on('click', function(){
    $('#for-search-report').toggleClass('for-view-filter');
  })
</script>
