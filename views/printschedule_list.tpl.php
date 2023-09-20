<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
<style type="text/css">
@media print{
  .print_wrapper{
    page-break-after:always;
    page-break-before:always;
    page-break-inside:always;
  }
}
body {
  target-new:tab;
  -webkit-print-color-adjust: exact !important;
  margin:0;
  font-family: 'Roboto', sans-serif;
}
@page {
  size: auto;
  margin: 0mm;
}
  .jobsheet-body{
    width: 769px;
    height: auto;
    margin: 0 auto;
  }
  .jobsheet-body .sheet-header {
    height: 63px;
    width: 100%;
    float: left;
    margin: 0 auto;
}
.jobsheet-body .sheet-header .logo-bar {
  width: 30%;
  margin: 0 auto;
  float: left;
  /* margin-top: 11px; */
}

.jobsheet-body .sheet-header .company-details {
    width: 70%;
    float: left;
    /* margin-top: 11px; */
}
.jobsheet-body .sheet-header .company-details ul {
    margin: 0;
    font-size: 12px;
    text-align:left;
    border-left: 1px solid;
    margin-left: 21px;
}
.jobsheet-body .sheet-header .company-details ul li {
    margin-top: 3px;
    display:inline-block;
}
.logo-bar {
    width: 190px;
    margin: 0 auto;
}
.jobsheet-body .sheet-header  h1 {
    text-align: center;
    padding: 0;
}
.jobsheet-body .sheet-header  p {
    text-align: center;
    padding: 0;
    font-style:italic;
}
.jobsheet-body .sheet-header .job-serial {
    float: left;
    position: absolute;
    left: 10.5%;
    padding: 0;
    font-size: 23px;
    margin: 0;
}

.logo-bar {
    width: 190px;
}
.logo-bar img {
    display: block;
    width: 100%;
    height: auto;
}
.sheet-header h1 span {
    display: block;
    font-weight: 100;
    font-size: 20px;
    background: black;
    color: white;
}
.srial-text {
  width: 182px;
  background: black;
  color: white;
  padding: 5px;
  font-size: 17px;
  display: inline-block;
  font-weight: 300;
  height: 21px;
  float:left;
}
.status-bar {
    float: left;
    display: inline-block;
    width: 61%;
    height:19px;
    padding: 5px;
    font-size: 17px;
    font-weight: 300;
}
.status-bar .client-details ul {
    padding: 0;
    list-style: none;
    margin: 0;
    text-align:center;
}
.status-bar .client-details ul li {
    padding: 0;
    display: inline-block;
    margin-left: 10px;
}
.col-line-bar{
    width: 100%;
    background: black;
    color: white;
    font-size: 17px;
    display: inline-block;
    font-weight: 300;
    height: 21px;
    float: left;
}
.date-serial-bar{
  width: 100%;
    height: auto;
    float: left;
    font-size:11px;
    margin-top: 4px;
}
.date-serial-align,.client-terms-align{
  width:95%;
  margin:0 auto;
}
.date-serial-bar .line-date {
    float: left;
}
.date-serial-bar .line-serial {
    float: right;
}
.col-line-bar .line-text p{
  text-align: center;
    padding: 0;
    margin: 5px;
    font-size: 11px;
}
.for-jobcard-info{
  height: 228px;
  float: left;
  width: 100%;
}
.overide-card-height{
  height:336px;
}
.client-info {
    float: left;
    width: 100%;
    /* height: 202px; */
    height: auto;
}
.for-jobcard-info .client-info table {
    width: 50%;
    float: left;
    /* padding: 10px; */
    margin-top: 21px;
    font-size: 11px;
}
.for-jobcard-info .client-info table tr {
    text-align: left;
}
.for-jobcard-info .client-info table tr.scheduletr{
  text-align:center;
}
.for-jobcard-info .client-header table th{
  width:50%;
}
.for-jobcard-info .client-info table th, td {
    padding: 7px;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
.client-terms-bar {
    height: auto;
    width: 100%;
    float: left;
}
.client-terms-bar p{
  padding:0;
  font-size:11px;
}
.client-terms-bar ul{
  font-size:11px;
  list-style:none;
  padding:0;
}
.client-terms-bar .align-right {
    float: right;
}
.problem-state {
  float: left;
width: 100%;
border: 1px dashed rgba(0, 0, 0, 0.3);
}
.problem-state table {
  font-size:11px;
}
.problem-state table td {
    padding-left: 17px;
    border:none;
}
.col-serial {
    border-bottom: 1px dashed black;
    height: 29px;
}
.line {
    width: 100%;
    height: 2px;
    border: 1px solid;
    float: left;
    background: black;
}
.bar-line {
    float: left;
    height: 7px;
    background: black;
    width: 100%;
    margin-top: 10px;
}
.col-header div {
    float: left;
}
.sheet-two .jobsheet-body .sheet-header {
    height: 63px;
    width: 100%;
    float: left;
    margin: 0 auto;
}
.sheet-two .jobsheet-body .wrapper-header {
    height: 40px;
}
.sheet-two .jobsheet-body .sheet-header .logo-bar {
    width: 20%;
    margin: 0 auto;
    float: left;
    padding-top: 0;
}
.sheet-two .jobsheet-body .sheet-header .company-details {
    width: 80%;
    float: left;
}
.sheet-two .jobsheet-body .sheet-header .company-details ul {
    float:unsert;
    margin: 0;
    text-align: center;
}
.sheet-two .jobsheet-body .sheet-header .company-details ul li {
    margin-top: 3px;
    display: inline-block;
}
.sheet-one,.sheet-two,.sheet-separate {
    height: auto;
    float: left;
    width: 100%;
}
.sheet-separate {
  height: 25px;
}
.sheet-separate::before{
  width: 100%;
  content: "----Cut Here---";
  position: absolute;
  right: 0;
  left: 0;
  text-align: center;
  padding-top: 3px;
  font-size:12px;
}
.work-align {
    width: 100%;
    float: left;
}
.work-done-are {
    width: 97%;
    height: 154px;
    border: 1px solid rgba(0, 0, 0, 0.21);
    margin: 0 auto;
}
.work-done-overideheight{
  height: 76px;
}
.terms-overideheight{
  height:auto;
}
.work-done-are .work-details table {
  width: 50%;
  font-size: 11px;
  float: left;
}
.work-done-are .work-details .final-tbl{
  width:100%;
}
.work-done-are .work-details .final-tbl td{
  border:none;
}
.work-done-are .work-details p {
    float: left;
    font-size: 12px;
    margin-left: 10px;
}
.work-details .work-done {
    width: 100%;
    height: 35px;
    border: 1px dashed rgba(0, 0, 0, 0.5019607843137255);
    display: inline-block;
    margin-left: -9px;
}
.work-details td ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
.work-details td ul li {
    display: inline-block;
    width:79px;
}
.work-details td ul li:before{
  content: '';
    width: 16px;
    height: 16px;
    border: 1px solid;
    position: absolute;
    margin: -4px 0 0 -23px;
}
.supported-area table{
  width:100%;
  font-size:12px;
}
.some-list {
  position: absolute;
  margin-left: 3px;
  bottom: 0;
  left: 0;
  right: 0;
}
.some-list-1{
  top: 97px;
}
.some-list-2{
  top: 480px;
}
.some-list .align-list {
  text-align: center;
  text-transform: capitalize;
}
.some-list .align-list ul {
    padding: 0;
    margin: 0;
}
.some-list .align-list ul li {
  display: inline-block;
  width: 70px;
  font-weight: 900;
}
.wrapper-body {
    height: 13px;
}
.table-checklist td{
  border-left: 1px solid #dee2e6;
}
.declaration-list {
    float: left;
    list-style: none;
    font-size: 12px;
}
.declaration-list li{
  margin-top: 18px;
}
.details {
    font-size: 10px;
}
.term-p{
    font-size: 13px;
    margin-left: 10px;
    font-weight: 600;
}
span.hold-icon {
    display: inline-block;
    margin-left: 4px;
    text-align: center;
    width: 18px;
}
.time{
  font-weight:600;
}
.lunch{
  background:#d64742;
  border:1px solid #d64742;
}
</style>
<header class="page-header" style="text-transform:capitalize;">
	<!-- <h2><?=$name;?></h2> -->
</header>
<div class="row">
	<div class="col-lg-8 center-panels">
		<section class="panel">
			<header class="panel-heading" style="display:none;">
				<div class="panel-actions">
					<a href="?module=cards&action=index" class="panel-title"><i class="fa fa-user"></i> List of Card</a>
				</div>
				<!-- <h2 class="panel-title"><i class="fa fa-credit-card"></i> ID for <?=$name;?></h2> -->
			</header>
			<div class="panel-body">
        <div class="col-md-8 center-panels">
          <div class="wrapper-body"></div>

          <div class="sheet-one">
            <div class="jobsheet-body">
              <div class="sheet-header">
                <div class="logo-bar"> <img src="<?=CS_LOGO?>" style="width: 258px;height: 60px;" alt="company logo"> </div>
                <div class="company-details">
                    <ul>
                      <li>Sabodo Car park Tower 10th Floor | </li>
                      <li>India Street - Dar es salaam | </li>
                      <li>Tel: 2133039 - Mob: (255) 0656 760 050 | 0686 677 755 | (255) 0767 084 602</li>
                      <li>P.O.Box 8758 - Email: efd@powercomputers.net | </li>
                      <li>http//:www.powercomputers.net | </li>
                      <!-- <li>Branch Name: <?=$ticketdata['branchname'];?></li> -->
                    </ul>
                </div>

              </div>
              <div class="col-line-bar">
                <div class="line-text">
                  <p>DAY SCHEDULE</p>
                </div>
              </div>
            </div>
          </div>

            <div class="sheet-one">
              <div class="jobsheet-body">
                <div class="date-serial-bar">
                  <div class="date-serial-align">
                    <div class="line-date">Printed Date: <span><strong><?=date('d-m-Y h:i:s:a', time())?></strong> </span> </div>
                  </div>
                </div>
                <div class="for-jobcard-info overide-card-height">
                  <div class="client-info">
                    <table class="table-checklist" style="width:100%;">
                      <thead>
                				<tr class="trheader">
                					<th>#</th>
                					<th>Time Slot</th>
                					<th>Ticket Slot 1</th>
                					<th>Ticket Slot 2</th>
                					<th>Ticket Slot 3</th>
                				</tr>
                			</thead>
                			<tbody id="tbodyforticekt">

                        <?php if ($schedulelist){ ?>

                          <?php foreach ($schedulelist as $key => $schedule){ ?>

                            <?php if ($schedule['type'] == 'lunch'){ ?>

                              <tr class='scheduletr'>
                                <td><?=$key;?></td>
                      					<td><?=$schedule['time']?> LUNCH TIME <i class='fa fa-coffee'></i></td>
                                <td><?=$schedule['time']?> LUNCH TIME <i class='fa fa-coffee'></i></td>
                                <td><?=$schedule['time']?> LUNCH TIME <i class='fa fa-coffee'></i></td>
                      					<td><?=$schedule['time']?> LUNCH TIME <i class='fa fa-coffee'></i></td>
                              </tr>

                            <?php } else { ?>

                              <tr class='scheduletr'>
                                <td><?=$key;?></td>
                      					<td class="time"><?=$schedule['time']?><span class="hold-icon"><i class='fa fa-bell'></span></i></td>
                                <td><?if($schedule['slot_1']) echo $schedule['slot_1']; else echo "<i class='fa fa-bed'></i>";?> </td>
                                <td><?if($schedule['slot_2']) echo $schedule['slot_2']; else echo "<i class='fa fa-bed'></i>";?> </td>
                      					<td><?if($schedule['slot_3']) echo $schedule['slot_3']; else echo "<i class='fa fa-bed'></i>";?> </td>
                              </tr>

                            <?php }; ?>

                          <?php }; ?>

                        <?php }; ?>

                      </tbody>
                    </table>
                    <div class="">
                      <table>
                        <tr>
                          <th>Schedule by</th>
                          <td><?=$createdby?></td>
                        </tr>
                        <tr>
                          <th>Created on</th>
                          <td><?=fDate($createdon)?></td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>

              </div>
            </div>


        </div>
			</div>
		</section>
	</div>
</div>
<script type="text/javascript">
window.print();
//window.open('?module=tickets&action=jobcard&id=9','_blank');
</script>
