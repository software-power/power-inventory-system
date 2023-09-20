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
</style>
<header class="page-header">
	<h2>My Assigned Fiscalize</h2>
</header>

<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
	<h5>Search Query</h5>
	<form>
		<input type="hidden" name="module" value="serials">
		<input type="hidden" name="action" value="myassigned_fiscal">
		<div id="filter_table">
      <div class="row">
        <div class="col-md-12">
					<select id="clientid" class="form-control" name="clientid">
						<?php if ($clientid){?>
							<option value="<?=$clientid?>"><?=$clientname?></option>
						<?php }else {?>
							<option value="" selected disabled>Client Name</option>
						<?php }?>
					</select>
				</div>
      </div>
			<div class="row">
				<div class="col-md-6">
					<select id="sales_status" class="form-control for-input" name="fstatus">
						<option value="" selected disabled>--Fiscalize Status--</option>
						<option <?=selected($fstatus,'yes')?> value="yes">Fiscalized</option>
						<option <?=selected($fstatus,'not')?> value="not">Not Fiscalized</option>
					</select>
				</div>
				<div class="col-md-6">
					<input type="text" class="form-control for-input" name="serialno" placeholder="Serial Number" value="<?=$serialno?>">
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
					<div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
				</div>
				<div class="col-md-4">
					<a href="?module=serials&action=myassigned_fiscal" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET</a>
				</div>
        <div class="col-md-4">
					<button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i> SEARCH</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="col-md-12">
	<section class="panel">
		<header class="panel-heading for-heading">
			<h2 class="panel-title">List of Machine</h2>
			<div class="btn-align">
				<button id="openModel" class="btn btn-success"> <i class="fa fa-search"></i> Search</button>
			</div>
		</header>
	<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-hover mb-none" id="userTable" style="font-size:13px">
		<thead>
			<tr>
				<th>SN</th>
				<th>Serial Number</th>
				<th>Client Name</th>
				<th>Warranty From</th>
				<th>Warranty To</th>
				<th>Created On</th>
				<th>Fiscalize Status</th>
				<th>Status</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($serials as $id=>$R) { ?>
				<tr class="<?if($R['salestatus'] == 'closed') echo 'colorClosed';?>">
					<td width="80px"><?=$id+1?></td>
					<td><?=$R['name']?></td>
					<td><?=$R['clientname']?></td>
					<td><?=fdate($R['warrantydatefrom'])?></td>
					<td><?=fDate($R['warrantydateto'])?></td>
					<td><?=fDate($R['doc'])?></td>
					<td>
						<span
						class="badge <?if($R['isfiscal'] == 1) echo 'badge-orange'; else echo 'badge-red';?>">
							<?=($R['isfiscal']? 'Fiscalized':'Not Fiscalized')?>
						</span>
					</td>
          <td><?=$R['status']?></td>
					<td>
						<?php if ($R['isfiscal']){ ?>
              <a href="#"><i class="fa fa-ban"></i></a>
            <?php }else{; ?>
              <a class="btn btn-primary" href="?module=serials&action=fiscalizing&id=<?=$R['serialid']?>"><i class="fa fa-cog"></i> Fiscalizing</a>
            <?php }; ?>
					</td>
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
