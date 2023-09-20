<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
<script src="assets/print-js/dist/print.js"></script>
<link rel="stylesheet" type="text/css" href="assets/print-js/dist/print.css">
<style type="text/css">
body {
  target-new:tab;
  -webkit-print-color-adjust: exact !important;
  margin:0;
  font-family: 'Roboto', sans-serif;
}
  .jobsheet-body .sheet-header {
    height: 63px;
    width: 100%;
    float: left;
    margin: 0 auto;
}
.jobsheet-body .sheet-header .logo-bar {
  width: 20%;
  margin: 0 auto;
  float: left;
  /* margin-top: 11px; */
}

.jobsheet-body .sheet-header .company-details {
    width: 80%;
    float: left;
    /* margin-top: 11px; */
}
.jobsheet-body .sheet-header .company-details ul {
    float: left;
    margin: 0;
    font-size: 12px;
    text-align:center;
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
.date-serial-bar .line-date {
    float: left;
}
.date-serial-bar .line-serial {
    float: right;
}
.col-line-bar .line-text p{
  text-align: center;
    padding: 0;
    /* margin: 5px; */
    font-size: 11px;
}
.for-jobcard-info{
  height: 228px;
  float: left;
  width: 100%;
}
.overide-card-height{
  height:266px;
}
.client-info {
    float: left;
    width: 100%;
    height: 202px;
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
.for-jobcard-info .client-info table th{
  width:29%;
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
  /* bottom: 0; */
  left: 0;
  right: 0;
}
.some-list-1{
  top: 150px;
}
.some-list-2{
  top: 526px;
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
.forpaid {
  display:none;
  position: absolute;
  width: 100%;
  margin-top: 143px;
  left:0;
}
.forpaid.forpadi-bottom {
    margin-top: 32%;
}
#print_form_container caption,table{
  color:black;
}
#print_form_container{
  /* width:769px; */
  color:black;
}
#print_form_container #form_holder{
  padding:0;
}
</style>
<div class="ms-print-btn">
  <ul class="nav">
    <li class="nav-item">
      <button title="Print tickets form" onclick="printForm(form_holder)" class="btn btn-primary ms-btn ms-float-right" type="button" name="button"><i class="fa fa-print"></i></button>
    </li>
    <li class="nav-item item-top">
      <a title="Edit or ticket" href="<?=url('tickets','ticket_edit','id='.$ticketdata['jobid'])?>" class="btn btn-success ms-btn ms-float-right"><i class="fa fa-edit"></i></a>
    </li>
  </ul>
</div>
<div id="print_form_container">
  <div id="form_holder">
    <div class="row">
      <div class="forpaid" style="<?if($ticketdata['prostatus'] == 'chargeable') echo 'display:block'; ?>">
        <div class="logo-bar"> <img src="assets/images/chargeable.png" alt="chargeable"> </div>
      </div>
      <div class="sheet-one">
        <div class="jobsheet-body">
          <div class="sheet-header">
            <div class="logo-bar"> <img src="<?=CS_LOGO?>" style="width: 258px;height: 60px;" alt="company logo"> </div>
            <div class="company-details">
                <ul>
                  <li>Sabodo Car park Tower 10th Floor | </li>
                  <li>India Street - Dar es salaam | </li>
                  <li>Tel: 2133039 - Mob: (255) 782769370 | 0686679911</li>
                  <li>Fax: (255) 222137379 | </li>
                  <li>P.O.Box 8758 - Email: info@powercomputers.net | </li>
                  <li>http//:www.powercomputers.net | </li>
                  <li>Branch Name: <?=$ticketdata['branchname'];?></li>
                </ul>
            </div>

          </div>
          <div class="col-line-bar">
            <div class="line-text">
              <p>JOBCARD / DELIVERY NOTE</p>
            </div>
          </div>
          <div class="date-serial-bar">
            <div class="date-serial-align">
              <!-- date('d-m-Y h:i:s:a', time()) -->
              <div class="line-date">Printed Date: <span><strong><?=date('d-m-Y h:i:s:a', time())?></strong> </span> </div>
              <div class="some-list some-list-1">
                <div class="align-list">
                  <ul>
                    <li><?=$ticketdata['supporttype'];?></li>
                    <li><?=$ticketdata['prostatus'];?></li>
                    <li><?=$ticketdata['raisedfrom'];?></li>
                  </ul>
                </div>
              </div>
              <div class="line-serial"> <span> <strong>Job Card No: <?=$_GET['id'];?></strong> </span> </div>
            </div>
          </div>
          <div class="for-jobcard-info overide-card-height">
            <div class="client-terms-bar">
              <div class="client-terms-align">
                <ul>
                  <li>Repairs will only be carried out with an official order or on local Purchase order.
                    (For Corporate, Organizations & NGO's), Strictly cash terms for amount less than 100,000/= TSHS</li>
                    <li style="float:right"><strong>Created Date: <?=$ticketdata['createdate']?></strong></li>
                </ul>
              </div>
            </div>
            <div class="client-info">
              <table>
                <caption>Collector/Contact Details</caption>
                <tr>
                  <th>Collector Name</th>
                  <td><?if($ticketdata['contactname']){echo $ticketdata['contactname'];}else{ echo "Null";}?></td>
                </tr>
                <tr>
                  <th>Contact</th>
                  <td><?if($ticketdata['contactmobile']){echo $ticketdata['contactmobile'];}else{ echo "Null";}?></td>
                </tr>
                <tr>
                  <th>Device Type</th>
                  <td><?=$ticketdata['productname']?></td>
                </tr>
                <!-- class="problem-state" -->
                <tr>
                  <th>Problem details :</th>
                  <td><?if($ticketdata['clientremark']){echo $ticketdata['clientremark'];}else{ echo "Null";}?></td>
                </tr>
                <tr class="work-details">
                  <th>Verify Work :</th>
                  <td>
                    <ul>
                      <li>Completed</li>
                      <li>Pending</li>
                    </ul>
                  </td>
                </tr>
                <tr>
                  <th>Signature</th>
                  <td> ________________________</td>
                </tr>

              </table>

              <table>
                <caption>Client Details</caption>
                <tr>
                  <th>Client Name</th>
                  <td><?=$ticketdata['clientname']?></td>
                </tr>
                <tr>
                  <th>TIN</th>
                  <td><?if($ticketdata['tinnumber'] == "") {echo "null";} else {echo $ticketdata['tinnumber'];}?></td>
                </tr>
                <tr>
                  <th>Serial Number</th>
                  <td><?=$ticketdata['serialno']?></td>
                </tr>
                <tr>
                  <th>Contact</th>
                  <td><?=$ticketdata['mobilenumber']?></td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td><?=$ticketdata['clientemail']?></td>
                </tr>
                <tr>
                  <th>Date/Time</th>
                  <td> ________________________</td>
                </tr>

              </table>
            </div>
          </div>

        </div>
      </div>

      <div class="sheet-separate"></div>

      <div class="sheet-two">
        <div class="forpaid forpadi-bottom" style="<?if($ticketdata['prostatus'] == 'chargeable') echo 'display:block'; ?>">
          <div class="logo-bar"> <img src="assets/images/chargeable.png" alt="chargeable"> </div>
        </div>
        <div class="jobsheet-body">
          <div class="sheet-header wrapper-header">
            <div class="logo-bar"> <img src="<?=CS_LOGO?>" style="width: 258px;height: 60px;" alt="company logo"> </div>
          </div>
          <div class="col-line-bar">
            <div class="line-text">
              <p>JOBCARD / DELIVERY NOTE</p>
            </div>
          </div>
          <div class="date-serial-bar">
            <div class="date-serial-align">
              <div class="line-date">Printed Date: <span><strong><?=date('d-m-Y h:i:s:a', time())?></strong> </span> </div>
              <div class="some-list some-list-2">
                <div class="align-list">
                  <ul>
                    <li><?=$ticketdata['supporttype'];?></li>
                    <li><?=$ticketdata['prostatus'];?></li>
                    <li><?=$ticketdata['raisedfrom'];?></li>
                  </ul>
                </div>
              </div>
              <div class="line-serial"> <span> <strong>Job Card No: <?=$_GET['id'];?></strong> </span> </div>
            </div>
          </div>
          <div class="for-jobcard-info">
            <div class="client-info">
              <table>
                <caption>Collector Details</caption>
                <tr>
                  <th>Collector Name</th>
                  <td><?if($ticketdata['contactname']){echo $ticketdata['contactname'];}else{ echo "Null";}?></td>
                </tr>
                <tr>
                  <th>Contact</th>
                  <td><?if($ticketdata['contactmobile']){echo $ticketdata['contactmobile'];}else{ echo "Null";}?></td>
                </tr>
                <tr>
                  <th>Device Type</th>
                  <td><?=$ticketdata['productname']?></td>
                </tr>
                <!-- problem-state -->
                <tr>
                  <th>Problem details :</th>
                  <td><?if($ticketdata['clientremark']){echo $ticketdata['clientremark'];}else{ echo "Null";}?></td>
                </tr>

              </table>

              <table>
                <caption>Client Details</caption>
                <tr>
                  <th>Client Name</th>
                  <td><?=$ticketdata['clientname']?></td>
                </tr>
                <tr>
                  <th>TIN</th>
                  <td><?if($ticketdata['tinnumber'] == "") {echo "null";} else {echo $ticketdata['tinnumber'];}?></td>
                </tr>
                <tr>
                  <th>Serial Number</th>
                  <td><?=$ticketdata['serialno']?></td>
                </tr>
                <tr>
                  <th>Contact</th>
                  <td><?=$ticketdata['mobilenumber']?></td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td><?=$ticketdata['clientemail']?></td>
                </tr>
                <tr>
                  <th>Created Date</th>
                  <td><?=$ticketdata['createdate']?></td>
                </tr>


              </table>
            </div>
          </div>

          <div class="work-align">
            <div class="work-done-are">
              <div class="work-details">
                <table>
                  <caption>For Engineer Work Done</caption>
                  <tr>
                    <td>Work Done:</td>
                    <td class='work-done'></td>
                  </tr>
                  <tr>
                    <td>Date / Time:</td>
                    <td>________________</td>
                  </tr>
                  <tr>
                    <td>Work :</td>
                    <td>
                      <ul>
                        <li>Completed</li>
                        <li>Pending</li>
                      </ul>
                    </td>
                  </tr>
                  <tr>
                    <td>Eng. Signature</td>
                    <td>________________</td>
                  </tr>
                </table>

                <table>
                  <caption>For Client: Work Done</caption>
                  <tr>
                    <td>Customer Name: ________________</td>
                    <td>Remarks: ___________________</td>
                  </tr>
                  <tr>
                    <td>Verify Work :</td>
                    <td>
                      <ul>
                        <li>Completed</li>
                        <li>Pending</li>
                      </ul>
                    </td>
                  </tr>
                  <tr>
                    <td>Signature: ________________</td>
                    <td>Date/time: ________________</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="work-align">
            <div class="work-done-are work-done-overideheight">
              <div class="work-details">
                <table class="final-tbl">
                  <tr>
                    <td>Amount to be paid</td>
                    <td>Vat%: _______________</td>
                    <td>Total: _______________</td>
                  </tr>
                </table>
                <p>I have received the machine in working condition with all necessary items.</p>
              </div>
            </div>
          </div>
          <div class="supported-area">
            <table>
              <tr>
                <td>Supported By:</td>
                <td> <strong><?=$ticketdata['assigname']?></strong> </td>
                <td>Client Signature:</td>
                <td>___________________</td>
              </tr>
              <tr>
                <td>Supported Signature:</td>
                <td>_______________________</td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <div class="print_wrapper"></div>
      <!-- $ticketdata['supporttype'] === 'training' -->

      <?if($ticketdata['supporttype'] === 'training'){?>

        <div class="sheet-one">
          <div class="jobsheet-body">
            <div class="sheet-header">
              <div class="logo-bar"> <img src="<?=CS_LOGO?>" style="width: 258px;height: 60px;" alt="company logo"> </div>
              <div class="company-details">
                  <ul>
                    <li>Sabodo Car park Tower 10th Floor | </li>
                    <li>India Street - Dar es salaam | </li>
                    <li>Tel: 2133039 - Mob: (255) 782769370 | 0686679911</li>
                    <li>Fax: (255) 222137379 | </li>
                    <li>P.O.Box 8758 - Email: info@powercomputers.net | </li>
                    <li>http//:www.powercomputers.net | </li>
                    <li>Branch Name: <?=$ticketdata['branchname'];?></li>
                  </ul>
              </div>

            </div>
            <div class="col-line-bar">
              <div class="line-text">
                <p><?if($ticketdata['supporttype'] === 'training'){echo "TRAINING";}?> CHECKLIST</p>
              </div>
            </div>
            <div class="date-serial-bar">
              <div class="date-serial-align">
                <div class="line-date">Date: <span><strong><?=date('d-m-Y h:i:s:a', time())?></strong> </span> </div>
                <div class="some-list some-list-1">
                  <div class="align-list">
                    <ul>
                      <li><?=$ticketdata['supporttype'];?></li>
                      <li><?=$ticketdata['prostatus'];?></li>
                      <li><?=$ticketdata['raisedfrom'];?></li>
                    </ul>
                  </div>
                </div>
                <div class="line-serial"> <span> <strong>Job Card No: <?=$_GET['id'];?></strong> </span> </div>
              </div>
            </div>
            <div class="for-jobcard-info overide-card-height">
              <div class="client-info">
                <div class="client-info">
                  <table>
                    <caption>Collector Details</caption>
                    <tr>
                      <th>Collector Name</th>
                      <td><?if($ticketdata['contactname']){echo $ticketdata['contactname'];}else{ echo "Null";}?></td>
                    </tr>
                    <tr>
                      <th>Contact</th>
                      <td><?if($ticketdata['contactmobile']){echo $ticketdata['contactmobile'];}else{ echo "Null";}?></td>
                    </tr>
                    <tr>
                      <th>Device Type</th>
                      <td><?=$ticketdata['productname']?></td>
                    </tr>
                    <tr>
                      <th>Serial Number</th>
                      <td><?=$ticketdata['serialno']?></td>
                    </tr>

                  </table>

                  <table>
                    <caption>Client Details</caption>
                    <tr>
                      <th>Client Name</th>
                      <td><?=$ticketdata['clientname']?></td>
                    </tr>
                    <tr>
                      <th>TIN</th>
                      <td><?if($ticketdata['tinnumber'] == "") {echo "null";} else {echo $ticketdata['tinnumber'];}?></td>
                    </tr>
                    <tr>
                      <th>Contact</th>
                      <td><?=$ticketdata['mobilenumber']?></td>
                    </tr>
                    <tr>
                      <th>Email</th>
                      <td><?=$ticketdata['clientemail']?></td>
                    </tr>


                  </table>
                </div>
                <table class="table-checklist" style="width:100%;">
                  <tr>
                    <caption style="text-align: left;">TRAINING HAS TO BE DONE ON</caption>
                    <td style="text-align:center;">TOPIC</td>
                    <td style="text-align:center;">STATUS <br>(Tick) </td>
                    <td style="text-align:center;">CLIENT SIGN <br> (IF TRAINING IS DONE SUCCESSFULLY) </td>
                  </tr>
                  <tr>
                    <td>1. Sales (Jinsi yakufanya Mauzo)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>2. How to send z-report (Kutuma Z ripoti)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>3.Monthly Report (Ripoti ya mauzo ya mwezi)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>4.Purchase Enrty (Kuingiza Manunuzi)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>5.Purchase Report (Kutuma Ripoti ya Manunuzi)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>6.Product list Report (kutafuta listi ya bidha)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>7.X-Report (daily report) (Jinsi ya Kutoa Ripoti ya kila siku X-ripoti)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>8.Product Report (Ripoti ya Bidhaa)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>9. How to Insert a Roll (Jinsi yakuweka karatasi)</td>
                    <td></td>
                    <td></td>
                  </tr>

                  <tr>
                    <td>10. Power Connection (Kuunganisha kwenye umeme)</td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td></td>
                    <td> <strong>*Don't leave blank*</strong> </td>
                  </tr>
                </table>

                <ul class="declaration-list">
                  <li>
                    I ___________________________________________________ Declare that the training and configuration
                    has been conducted at my premises
                  </li>

                  <li>
                    Mimi___________________________Nathibitisha kwamba mafunzo yakutumia mashine hii na Konfigurasheni
                    imefanyika kwa usahihi katika eneo langu la biashara.
                  </li>

                  <li>
                    Technician Name <strong><?=$ticketdata['assigname']?></strong> , Signature  ___________________________________
                    Jina na Sahihi ya mfundishaji
                  </li>

                  <li>
                    Trained Username _________________________________________________________________
                    Jina/majina ya Waliyofundishwa
                  </li>

                  <li>
                    EFD Manager Signature _________________________________________________________________
                    Sahihi ya meneja wa EFD
                  </li>
                </ul>
              </div>
            </div>

          </div>
        </div>
      <?}?>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function(){
  printForm('#form_holder');
  $('body').bind('copy cut paste', function(e){
    e.preventDefault();
  })

  $('body').on('contextmenu', function(e){
    return false;
  });
})
function printForm(areaToPrint){
  var id = $(areaToPrint).attr('id');
  printJS({
    printable:id,
    type: 'html',
    showModal: true,
    targetStyles: ['*']
    // scanStyles:true
  })
}
</script>
