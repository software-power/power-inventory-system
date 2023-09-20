<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
.panelControl {
    float: right;
}
.qnname {
  font-size: 15px;
  text-transform: capitalize;
  font-weight: 700;
}
.report-th {
  font-size: 15px;
  padding: 10px;
  border-bottom: 1px solid #ecedf0;
}
.report-td {
  padding-left: 25px;
  text-transform: capitalize;
  margin-top: 6px;
  display: inline-block;
  background: #ecedf0;
  width: 100%;
  height: 35px;
  font-weight: 500;
}
.align-box {
  width: 77%;
  margin: 0 auto;
}
.badge-primary{
  margin-top:7px;
}
</style>
<header class="page-header">
	<h2>CRM Summary Report</h2>
</header>

<div class="align-box">
  <div class="col-md-12">
  	<section class="panel">
  		<header class="panel-heading">
  			<div class="panelControl">
  				<!-- <button id="openModel" class="btn btn-primary" href="?module=home&action=index" title="Home"> <i class="fa fa-search"></i> Open Search </button> -->
  				<a class="btn btn-primary" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> </a>
  			</div>
  			<h2 class="panel-title">Question(s) Summary report</h2>
  		</header>
  		<div class="panel-body">
  			<div class="">
          <div class="report-holder">
            <?php foreach ($report as $question_name => $qn_answers){ ?>

              <div class="qn-name col-md-12"><h5 class="qnname"><?=$question_name?></h5> </div>
              <div class="col-md-12">
                <div class="row report-row-th">
                  <div class="col-md-6">
                    <p class="report-th">Answer</p>
                  </div>
                  <div class="col-md-6">
                    <p class="report-th">Customer (s)</p>
                  </div>
                </div>

                <?php foreach ($qn_answers as $possibleAnswer => $details){?>

                  <div class="row report-row-td">
                    <div class="col-md-6">
                      <span class="report-td"><?=$possibleAnswer?></span>
                    </div>
                    <div class="col-md-6">
                      <span class="report-td">
                      <span class="badge badge-primary"><?=$details['customers']?></span> </span>
                    </div>
                  </div>

                <?php };?>


              </div>


            <?php }; ?>
          </div>
  				<!-- <table class="table table-hover mb-none" style="font-size:13px;" id="printing_area">
  					<thead>
  					</thead>
  					<tbody>

  					</tbody>
  				</table> -->
  			</div>
  		</div>
  	</section>
  </div>
</div>
