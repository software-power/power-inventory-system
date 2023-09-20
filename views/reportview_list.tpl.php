<!-- <link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script> -->

<style media="screen">
	.panel-actions a, .panel-actions .panel-action{
		font-size: 21px;
	}
div.dataTables_wrapper div.dataTables_filter input {
    width: 100%;
}
</style>
<header class="page-header for_heading">
	<h2>
		<?if($_GET['period'] == 'month' || $_GET['period'] == 'week'){echo $_GET['period']."ly";}else{echo $_GET['period'];}?>
		<?=$_GET['preferred']?> Tickets Report review</h2>
	<!-- <h2>Report View</h2> -->
</header>

<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading">
			<!-- <a class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn btn-success" href="#">DOWNLOAD</a> -->
			<div class="panel-actions">
				<!-- <a href="?module=reports&action=export_excel&preferred=<?=$_GET['preferred']?>&period=<?=$_GET['period']?>" title="Export to Excel"> <i class="fa fa-download"></i> </a> -->
				<a href="?module=home&action=index" title="Go To Dashboard"> <i class="fa fa-home"></i> </a>
			</div>

			<h2 class="panel-title">Ticket (Task) Reports</h2>
		</header>
		<div class="panel-body">
			<div class="table-responsivess">
				<!-- class="table table-hover mb-none" -->
				<table id="dataArea" class="table table-striped"  style="font-size:13px;">
					<thead>
						<tr>
							<th></th>
							<th>#</th>
							<th>Ticket</th>
							<th>Serial No</th>
							<th>Client Name</th>
							<th>Client id</th>
							<th>Branch</th>
							<th>Product</th>
							<th>Departement</th>
							<th>Assigned To</th>
							<th>Assigned By</th>
							<th>Assigned Date</th>
							<!-- <th>Problem Details</th> -->
						<!--	<th>Remark</th>
							<th>Invoice No</th>
							<th>Type</th>
							<th>Invoice Amount</th>
							<th>Spare/Part</th>
							<th>Spare Amount</th>
							<th>Timespent</th>-->
							<th>Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>

						<?php $count=1;
							foreach($listData as $ins=>$list) {?>


							<tr style="background-color:<?=$R['color']?>">
								<!-- <td><a href="?module=insuranceclaims_report&action=view&id=<?=$R['id']?>" target="_blank"><i class="fa fa-clipboard"></i></a></td> -->
								<td></td>
								<td><?=$count?></td>
								<td><?=$list['id']?></td>
								<td><?=$list['serialno']?></td>
								<td><?=$list['client']?></td>
								<td><?=$list['clientname']?></td>
								<td><?=$list['branchname']?></td>
								<td><?=$list['productname']?></td>
								<td><?=$list['deptname']?></td>
								<td><?=$list['assigname']?></td>
								<td><?=$list['assignby']?></td>
								<td><?=fDate($list['assignedon'])?></td>
									<!-- <td><?=$list['clientremark']?></td> -->
								<!--<td><?=$list['remark']?></td>
								<td><?=$list['invoiceno']?></td>
								<td><?=$list['type']?></td>
								<td><?=$list['amount']?></td>
								<td><?=$list['sparepart']?></td>
								<td><?=$list['spareamount']?></td>
								<td><?=$list['timespent']?></td>-->
								<td><?=$list['statusname']?></td>
								<td><a title="Full Report" href="?module=reports&action=full_report&id=<?=$list['id']?>" target="_blank"> <i class="fa fa-file"></i> </a> </td>
							</tr>
							<?php $count++;}?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>
<script type="text/javascript">
$(document).ready(function(){
 $('#dataArea').DataTable({
	 dom: '<"top"fB>t<"bottom"ip>',
	 colReorder:true,
	 keys:true,
	 buttons: [
		 'copyHtml5', 'excelHtml5', 'pdfHtml5','csvHtml5','print'],
	 <?if($_GET['status']){?>
	 title:'<?=$_GET['status']?>',
	 <?}?>
 });
})
</script>
