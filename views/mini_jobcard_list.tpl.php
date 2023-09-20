<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <link href="https://fonts.googleapis.com/css?family=Alegreya+Sans&display=swap" rel="stylesheet">
    <script src="assets/print-js/dist/print.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/print-js/dist/print.css">
  </head>
  <style>
  body {
    font-family: 'Alegreya Sans', sans-serif;
  }
  .jobcard-container {
    width: 219px;
    margin: 0 auto;
    font-size: 12px;
    background: #ffffff;
}
.logobar img{
  width: 186px;
}
.header-bar p {
    font-size: 16;
    font-weight: 800;
    text-align: center;
    text-transform: uppercase;
}
.content-holder p {
    font-size: 13;
    font-weight: 800;
    text-align: center;
    text-transform: uppercase;
}
#print_form_container span {
    display: inline-block;
    text-align: center;
    width: 100%;
}
ul{
  padding:0;
  list-style:none;
}
.content-holder {
    text-align: center;
    /* width: 219px; */
}
.content-holder ul li{
  margin-top:10px;
}
p.for-border {
    border: 1px solid;
    border-radius: 5px;
    padding: 4px;
    width: 122px;
    margin: 0 auto;
    margin-top: 10px;
}
#print_form_container{
  width:219px;
  padding:0;
  color:black;
}
.ms-print-btn{
  right:38%;
}
  </style>
  <div class="ms-print-btn">
    <ul class="nav">
      <li class="nav-item">
        <button title="Print expenses form" onclick="printForm(form_holder)" class="btn btn-primary ms-btn ms-float-right" type="button" name="button"><i class="fa fa-print"></i></button>
      </li>
      <li class="nav-item item-top">
        <a title="Edit tickets" href="<?=url('tickets','ticket_edit','id='.$ticketdata['jobid'])?>" class="btn btn-success ms-btn ms-float-right"><i class="fa fa-edit"></i></a>
      </li>
    </ul>
  </div>
  <div id="print_form_container" class="jobcard-container">
    <div id="form_holder">
      <div class="jobcard-holder">
        <div class="header-bar">
          <div class="logobar">
            <img src="<?=CS_LOGO?>" style="width: 258px;height: 60px;" alt="company logo">
          </div>
          <p>powercomputers</p>
          <span>Sabodo Car park Tower <br>10th Floor,India Street - <br>Dar es salaam</span>
          <span>Tel: 2133039<br>Mob: (255) 782769370 <br> 0686679911</span>
          <span>P.O.Box 8758<br>Email: info@powercomputers.net</span>
          <span>http//:www.powercomputers.net</span>
        </div>
        <div class="content-holder">
          <p class="for-border"><?=$ticketdata['type']?></p>
          <p>Job Card/Ticket No. <br> <?=$ticketdata['jobid']?></p>
          <ul>
            <li><strong>Created Date</strong> <br><?=$ticketdata['createdate']?></li>
            <li><strong>Printed Date</strong> <br><?=date('d-m-Y h:i:s:a', time())?></li>
            <li><strong>Department</strong> <br><?=$ticketdata['deptname']?></li>
            <li><strong>Branch:</strong> <br><?=$ticketdata['branchname']?></li>
          </ul>
          <span>--------- Client Details ---------</span>
          <ul>
            <li><strong>Client Name:</strong> <br><?=$ticketdata['clientname']?></li>
            <li><strong>TIN No. :</strong> <br><?=$ticketdata['tinnumber']?></li>
            <li><strong>MOBILE :</strong> <br><?=$ticketdata['mobilenumber']?></li>
          </ul>
          <span>--------- Collector/Contact Details ---------</span>
          <ul>
            <li><strong>Contact/Collector Name :</strong> <br><?=$ticketdata['contactname']?></li>
            <li><strong>Contact/Collector Mobile :</strong> <br><?=$ticketdata['contactmobile']?></li>
          </ul>
          <span>--------- Product Details ---------</span>
          <ul>
            <li><strong>Product Type :</strong> <br><?=$ticketdata['productname']?></li>
            <li><strong>Serial No. :</strong> <br><?=$ticketdata['serialno']?></li>
            <li><strong>Support Type. :</strong> <br><?=$ticketdata['supporttype']?></li>
            <li><strong>Reported Problem :</strong> <br><br><?=$ticketdata['clientremark']?>
            </li>
          </ul>
          <span>--------- THANK YOU ---------</span>
        </div>
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
    })
  }
  </script>
</html>
