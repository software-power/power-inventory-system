<style media="screen">
.formModel {
	display:none;
	position: fixed;
	width: 100%;
	z-index: 14;
	background: rgba(238, 238, 238, 0.6196078431372549);
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	height: 100%;
}
.select2-container--default .select2-selection--single {
    padding: 9px;
    height: 52px;
    font-size: 16px;
    border: none;
    outline: none;
    margin-top: 2px;
}
.for-input {
    padding: 9px;
    height: 52px;
    font-size: 16px;
    border: none;
    outline: none;
		margin-top: 2px;
}
.formholder {
	position: relative;
	display:none;
	z-index: 26;
	border-radius: 5px;
	padding: 16px;
	width: 100%;
	background: #ededee;
	height: auto;
	-webkit-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	-moz-box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	box-shadow: 0px 4px 33px -4px rgba(0,0,0,0.41);
	}

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
.btn-align{
	float:right;
	position:relative;
	top:-25px;
}
.badge-orange{
	background-color:#47a447;
}
.badge-red{
	background-color:#d2322d;
}
.badge-blue{
	background-color:#0088cc;
}
.dropleft .dropdown-menu {
    top: 0;
    right: 100%;
    left: auto;
    margin-top: 0;
    margin-right: .125rem;
}
a.dropdown-item{
	text-decoration:none;
}
.dropdown-item {
    display: block;
    width: 100%;
    padding: .25rem 1.5rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
}
tr.colorClosed {
    background: #ecedf0;
}
.table-responsive {
    min-height: 150px;
}
.badge-danger {
    background-color: #d2322d;
}
.badge-success {
    background-color: #47a447;
}
.badge-primary {
    background-color: #0088cc;
}
</style>
<header class="page-header">
	<h2><?php if ($issue_spare) echo "Issue Spare"; else echo 'Requested Spare';?></h2>
</header>

<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="sales">
		<input type="hidden" name="action" value="order_list">
		<div id="filter_table">
			<div class="row">
				<div class="col-md-6">
					<select id="sales_status" class="form-control for-input" name="sales_status">
						<option value="" selected disabled>--Choose Status--</option>
						<option <?=selected($sales_status,'closed')?> value="closed">Closed</option>
						<option <?=selected($sales_status,'pending')?> value="pending">Pending</option>
					</select>
				</div>
				<div class="col-md-6">
					<select id="clientid" class="form-control" name="clientid">
						<?php if ($clientid){?>
							<option value="<?=$selectedClient['id']?>"><?=$selectedClient['name']?></option>
						<?php }else {?>
							<option value="" selected disabled>Client Name</option>
						<?php }?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-3">
							<h5>From Date</h5>
						</div>
						<div class="col-md-9">
							<input type="text" readonly name="fromdate"  class="datepicker form-control for-input" value="<?=$fromdate?>">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-3">
							<h5>To Date</h5>
						</div>
						<div class="col-md-9">
							<input type="text" readonly name="todate"  class="datepicker form-control for-input" value="<?=$todate?>">
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i> SEARCH</button>
				</div>
				<div class="col-md-4">
					<a href="?module=sales&action=order_list" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET</a>
				</div>
				<div class="col-md-4">
					<div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading for-heading">
			<h2 class="panel-title">List of Spare</h2>
			<div class="btn-align">
				<!-- <button id="openModel" class="btn btn-success"> <i class="fa fa-search"></i> Search</button> -->
        <?php if ($issue_spare){ ?>
          <a href="?module=spares&action=request_spare_edit&filter=issue_spare" class="btn"><i class="fa fa-check"></i> Issue Spare</a>
        <?php }else{ ?>
        <a href="?module=spares&action=request_spare_edit" class="btn"><i class="fa fa-check"></i> Request Spare</a>
        <?php }?>
			</div>
		</header>
	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px">
					<thead>
						<tr>
							<th>No.</th>
							<th>Spare Name</th>
							<th>Order Number</th>
							<th>Request By</th>
							<th>Request Date</th>
							<th>Approved By</th>
							<th>Issued By</th>
							<th>Order Status</th>
							<th>Invoice Status</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($spare_orders as $id=>$R) { ?>
							<tr>
								<td width="80px"><?=$id+1?></td>
								<td><?=$R['productname']?></td>
								<td><?=$R['ordernumber']?></td>
								<td><?=$R['requestedby']?></td>
								<td><?=fDate($R['orderdate'])?></td>
								<td><?if($R['approvedbyname']) echo $R['approvedbyname']; else echo 'Null';?></td>
								<td><?if($R['issuedbyname']) echo $R['issuedbyname']; else echo 'Null';?></td>
								<td>
			            <?php if ($R['isfrom_jobcard']){?>

			              <?php if ($R['pro_approval']){?>

			                <?php if ($R['pro_issued']){ ?>
			                  <span class="badge badge-success">Issued</span>
			                <?php }else{?>
			                  <span class="badge badge-primary">Not Issued</span>
			                <?php }; ?>

			              <?php }else{; ?>
			                <span class="badge badge-danger">Not Approved</span>
			              <?php }; ?>

			            <?php }; ?>
								</td>
								<td>
									<span
									class="badge <?if($R['is_invoiced']) echo 'badge-orange'; else echo 'badge-red';?>">
										<?if($R['is_invoiced'])echo "Invoiced"; else echo 'Not Invoiced'?>
									</span>
								</td>

								<td>
									<?php if ($role == 'Admin' || $head == 1){?>

										<?php if ($R['is_invoiced']){?>
											<button type="button" class="btn btn-secondary"><i class="fa fa-ban"></i></button>
										<?php }else{ ?>

											<div class="btn-group dropleft">
				  						  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  						    <i class="fa fa-list"></i>
				  						  </button>
				  						  <div class="dropdown-menu">
				  								<?//if($R['salestatus'] == 'pending'){?>
				                    <?php if ($R['pro_approval']){?>

															<?php if ($R['ticket_status'] == 'Completed'){ ?>
																<a class="dropdown-item" href="?order_number=<?=$R['ordernumber']?>&from=spare&module=sales&action=add_sales" title="Order to sales"><i class="fa fa-money"></i> Sales Invoice</a>
															<?php }else{ ?>
																<span class="dropdown-item">Ticket is <?=$R['ticket_status']?></span>
															<?php }; ?>

				                      <?php if ($issue_spare){?>
				                        <?php if ($R['pro_issued'] == 0){ ?>
																	<a class="dropdown-item" href="?order_number=<?=$R['id']?>&module=spares&action=issuing_process" title="Issue spare"><i class="fa fa-cog"></i> Issue Spare</a>
				                        <?php }; ?>
				                      <?php }; ?>
				                    <?php }; ?>
				  								<?//}?>
				  								<?if($R['pro_approval'] !=1){?>
				  		            	<a class="dropdown-item" href="?order_number=<?=$R['id']?>&module=spares&action=approve_spare" title="Approve spare"><i class="fa fa-check"></i> Approve Spare</a>
				  								<?}?>
				  								<!-- <a class="dropdown-item" target="_blank" href="?module=sales&action=print_orderform&ordernumber=<?=$R['ordernumber']?>" title="Print Order"><i class="fa fa-print"></i> Print Order</a> -->
				  						  </div>
				  						</div>

										<?php }?>

			            <?php }else{ ?>
			              <button type="button" class="btn btn-secondary"><i class="fa fa-ban"></i></button>
			            <?php }; ?>
								</td>
								<!-- <td>

			            <?if($R['salestatus'] == 'pending'){?>
										<a class="badge badge-blue" href="?module=sales&action=closing_order&orderid=<?=$R['orderid']?>" title="Closing Order"><i class="fa fa-check"></i> Close Deal</a>
			            <?} else if($R['salestatus'] == 'closed'){?>
										<i class="fa-ban fa"></i> Closed
			            <?}?>

			          </td> -->
							<?php } ?>
						</tbody>
					</table>
				</div>

		<script type="text/javascript">
		$(function(){

			$("#clientid").select2({ width: '100%', minimumInputLength: 3,
				ajax: {
					url: "?module=clients&action=getClients&format=json", dataType: 'json', delay: 250, quietMillis: 200,
					data: function (term) {	return { search : term }; },
					results: function (data, page) { return { results: data }; }
				}
			 });
		})

		$('#openModel').on('click', function (){
			$('#formHolder').show('slow');
			$('#formModel').show('slow');
		});

		$('#closeSearchModel').on('click', function (){
			$('#formHolder').hide('slow');
			$('#formModel').hide('slow');
		});

			$('#open_filter').on('click', function(){
				$('#for-search-report').toggleClass('for-view-filter');
			})
			//$ordernumber
      <?if ($_GET['ordernumber']) {?>
        window.open('?module=orders&action=print_orderform&ordernumber=<?=$_GET['ordernumber'];?>', '_blank');
      <?}?>

			<?if ($orderid) {?>
        window.open('?module=sales&action=print_checklist&orderid=<?=$orderid;?>', '_blank');
      <?}?>

		</script>
