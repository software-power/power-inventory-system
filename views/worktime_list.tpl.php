<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
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
.btn-holder {
  float: right;
}
.center-panel{
  width:70%;
  margin:0 auto;
}
.table .actions a:hover{
  color:#ffffff;
}
.table .actions a, .table .actions-hover a{
  color:#ffffff;
}
.type-indicate{
  width: 14px;
    height: 14px;
    border-radius: 100%;
    display: inline-block;
    position: absolute;
    right: 62%;
}
.type-indicate.color-on{
  background:#0088cc;
}
.type-indicate.color-off{
  background:#d64742;
}
.type-indicate.color-lunch{
  background:#d64742
}
.type-indicate.color-work{
  background:#47a447;
}
</style>
<header class="page-header">
	<h2>Time Slot</h2>
</header>
<div class="center-panel">
	<section class="panel">
		<header class="panel-heading for-heading">
			<div class="btn-holder">
  			<a class="btn btn-primary" href="?module=schedules&action=add_time"> <i class="fa fa-calendar"></i> Add Time</a>
  			<a class="btn btn-primary" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
      </div>
			<h2 class="panel-title">List of Time</h2>
		</header>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-hover mb-none" id="amcTable" style="font-size:13px">
				<thead>
					<tr>
						<th>SN</th>
						<th>Time</th>
						<th>Type</th>
            <th>Sort no</th>
						<th style="text-align:center">Created On</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($timelist as $id=>$R) { ?>
						<tr>
							<td width="80px"><?=$id+1?></td>
              <td><?=$R['name']?></td>
              <td>
                <span style="text-transform:capitalize"><?=$R['type']?></span>
                <?php if($R['type'] == 'on'){?>
                  <span class="type-indicate color-on"></span>
                <?php }else if($R['type'] == 'work'){?>
                  <span class="type-indicate color-work"></span>
                <?php }else if($R['type'] == 'lunch'){?>
                  <span class="type-indicate color-lunch"></span>
                <?php }else if($R['type'] == 'off'){?>
                  <span class="type-indicate color-off"></span>
                <?php }?>
              </td>
              <td style="text-align:center"><?=$R['sortno']?></td>
              <td style="text-align:center"><?=fDate($R['doc'])?></td>
              <td><?=$R['status']?></td>
              <td style="text-align:center" class="actions-hover actions-fade">
                <a class="btn btn-primary" href="?id=<?=$R['id']?>&module=schedules&action=edit_time"><i class="fa fa-pencil"></i></a>
              </td>
            </tr>
						<?php } ?>
					</tbody>
				</table>
		</div>
