<div class="col-md-12 for-panel">
  <?if(empty($_SESSION['member']['image'])){?>
    <div class="col-md-12">
      <h5 class="alert-info">Your account has no profile image, <a class="badge badge-light" href="?module=profile&action=index">Manage Account</a> </h5>
    </div>
  <?}?>
  <section class="panel">

    <?if(IS_ADMIN){?>

      <div class="col-md-12">
        <div class="user-information">
          <div class="white-box">
            <h3 class="box-title">Hi, <?=$name;?></h3>
            <div class="user-some-details">
              <ul>
                <li><i class="fa fa-user"></i> <?=$username;?></li>
                <li><i class="fa fa-mobile"></i> <?=$mobile;?></li>
                <li><i class="fa fa-at"></i> <?=$email;?></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="main-dashboard">
          <div class="row">
            <div class="col-md-7 col-sm-12 col-xs-12">
              <div class="white-box">
                  <h3 class="box-title" style="text-transform: capitalize;"><?=fDate(TODAY);?></h3>
                  <!--<ul class="list-inline text-right">
                      <li>
                          <h5><i class="fa fa-circle m-r-5" style="color: #00b5c2;"></i> iPhone</h5>
                      </li>
                      <li>
                          <h5><i class="fa fa-circle m-r-5" style="color: #f75b36;"></i> iPad</h5>
                      </li>
                      <li>
                          <h5><i class="fa fa-circle m-r-5" style="color: #2c5ca9;"></i> iPod</h5>
                      </li>
                  </ul>-->
                  <div class="user-info">
                    <div class="row">
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($brach['name'] == '')echo "Unassigned"; else echo $brach['name'];?></div>
                        <p>Branch</p>
                      </div>
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($department['name'] == '')echo "Unassigned"; else echo $department['name'];?></div>
                        <p>Department</p>
                      </div>
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($head == 1)echo "HOD"; else echo 'Normal Staff';?></div>
                        <p>Position</p>
                      </div>
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($role['name'] == 'User') echo "Normal User"; else echo $role['name'];?></div>
                        <p>Account type</p>
                      </div>
                    </div>
                  </div>
                  <div class="group-ticket">
                    <p>Summery for Ticket (s) type (AMC, Warranty or Chargeable)</p>
                    <div class="row">

                      <?php foreach ($total_summery_type as $key => $summery_type){?>
                        <div class="col-md-4 col-sm-12">
                          <div class="white-box text-center box-grey">
                              <h1 class="text-grey counter"><span class="for-icon"><i class="fa fa-file"></i></span> <?=$summery_type['total']?></h1>
                              <p class="text-grey"><? if($summery_type['type']) echo $summery_type['type']; else echo 'Null';?></p>
                          </div>
                        </div>
                      <?php }; ?>

                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="summer_forcrm">
                    <div class="col-md-12 col-sm-12">
                      <div class="white-box text-center bg-info">
                          <h1 class="text-white counter"><span class="for-icon"><i class="fa fa-book"></i></span> <?=$totalOrders['numberOForder']?></h1>
                          <p class="text-white" style="text-transform: uppercase;">total number of order form</p>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                      <div class="white-box text-center bg-inverse">
                          <h1 class="text-white counter"><span class="for-icon"><i class="fa fa-ticket"></i></span> <?=$ticketWithFeedback['numberOfticket']?></h1>
                          <p class="text-white" style="text-transform: uppercase;">Total number of Ticket (s) which has feedback</p>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                      <div class="white-box text-center bg-danger">
                          <h1 class="text-white counter"><span class="for-icon"><i class="fa fa-ticket"></i></span> <?=$ticketWithNoFeedback['numberOfticket']?></h1>
                          <p class="text-white" style="text-transform: uppercase;">Total number of Ticket (s) which has no feedback</p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-md-5">
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="white-box">
                      <h3 class="box-title">Total number of ticket</h3>
                      <ul class="list-inline two-part">
                          <li><i class="fa fa-folder"></i></li>
                          <li class="text-right"><span class="counter"><?=$total_summery[0]['total']?></span></li>
                      </ul>
                  </div>
                </div>
                <div class="col-md-12">
                  <p>Total Number of ticket (s), Department wise</p>
                </div>

                <?php foreach ($total_summery_department as $key => $summery){?>

                  <div class="col-md-6 col-sm-12">
                    <div class="white-box text-center bg-megna">
                        <h1 class="text-white counter"><span class="for-icon"><i class="fa fa-ticket"></i></span> <?=$summery['total']?></h1>
                        <p class="text-white" style="text-transform: uppercase;"><?=$summery['department']?></p>
                    </div>
                  </div>

                <?php }; ?>

              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 col-sm-12">
              <div class="white-box">
                  <h3 class="box-title">Technician pending ticket board</h3>
              </div>
            </div>

            <?php foreach ($listpendingleader as $key => $list){?>

              <div class="col-md-4 col-xs-12 col-sm-6">
                <div class="white-box white-box-overflow">
                    <h3 class="box-title"><?=$list['department']?></h3>
                    <div class="message-center">
                        <?php foreach ($list['technician'] as $key => $tech){ ?>

                          <a href="#">
                              <div class="user-img">
                                <?php if (empty($tech['image'])){ ?>

                                  <span class="img-circle character_dp">
                                    <?$default_character = str_split($tech['techname']);?>
                                    <?=$default_character[0]?>
                                  </span>

                                <?php }else{; ?>
                                  <img src="images/dp/<?=$tech['image']?>" alt="user" class="img-circle">
                                <?php }; ?>
                                <span class="profile-status online pull-right"></span> </div>
                              <div class="mail-contnet">
                                  <h5><?=$tech['techname']?></h5>
                                  <span class="mail-desc"><?=$tech['branch']?></span> <span class="time"><?=$tech['total']?></span>
                              </div>
                          </a>

                        <?php }; ?>
                    </div>
                </div>
              </div>

            <?php }; ?>

          </div>
        </div>
      </div>

    <?}else{?>

      <div class="col-md-12">
        <div class="user-information">
          <div class="white-box">
            <h3 class="box-title">Hi, <?=$name;?></h3>
            <div class="user-some-details">
              <ul>
                <li><i class="fa fa-user"></i> <?=$username;?></li>
                <li><i class="fa fa-mobile"></i> <?=$mobile;?></li>
                <li><i class="fa fa-at"></i> <?=$email;?></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="main-dashboard">
          <div class="row">
            <div class="col-md-7 col-sm-12 col-xs-12">
              <div class="white-box">
                  <h3 class="box-title" style="text-transform: capitalize;"><?=fDate(TODAY);?></h3>
                  <!--<ul class="list-inline text-right">
                      <li>
                          <h5><i class="fa fa-circle m-r-5" style="color: #00b5c2;"></i> iPhone</h5>
                      </li>
                      <li>
                          <h5><i class="fa fa-circle m-r-5" style="color: #f75b36;"></i> iPad</h5>
                      </li>
                      <li>
                          <h5><i class="fa fa-circle m-r-5" style="color: #2c5ca9;"></i> iPod</h5>
                      </li>
                  </ul>-->
                  <div class="user-info">
                    <div class="row">
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($brach['name'] == '')echo "Unassigned"; else echo $brach['name'];?></div>
                        <p>Branch</p>
                      </div>
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($department['name'] == '')echo "Unassigned"; else echo $department['name'];?></div>
                        <p>Department</p>
                      </div>
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($head == 1)echo "HOD"; else echo 'Normal Staff';?></div>
                        <p>Position</p>
                      </div>
                      <div class="col-md-3 user-info-tab">
                        <div class="info-title"><?if($role['name'] == 'User') echo "Normal User"; else echo $role['name'];?></div>
                        <p>Account type</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <?php if ($head){?>
                      <div class="col-md-12">
                        <p>Total Number of ticket (s), Department wise</p>
                      </div>

                    <div class="col-md-12 col-sm-12">
                      <div class="white-box text-center bg-megna">
                          <h1 class="text-white counter"><span class="for-icon"><i class="fa fa-ticket"></i></span> <?=$summery_department[0]['total']?></h1>
                          <p class="text-white">Total number of ticket</p>
                      </div>
                    </div>
                  </div>
                  <div class="group-ticket">
                    <?php }; ?>

                    <?php if ($head){?>

                      <p>Summery for Ticket (s) type (AMC, Warranty or Chargeable)</p>
                      <div class="row">

                        <?php foreach ($total_summery_type as $key => $summery_type){?>
                          <div class="col-md-4 col-sm-12">
                            <div class="white-box text-center box-grey">
                                <h1 class="text-grey counter"> <span class="for-icon"><i class="fa fa-file"></i></span> <?=$summery_type['total']?></h1>
                                <p class="text-grey"><? if($summery_type['type']) echo $summery_type['type']; else echo 'Null';?></p>
                            </div>
                          </div>
                        <?php }; ?>

                      </div>

                    <?php }; ?>

                  </div>
                </div>
            </div>
            <div class="col-md-5">
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="white-box">
                      <h3 class="box-title">Total number of ticket</h3>
                      <ul class="list-inline two-part">
                          <li><i class="fa fa-folder"></i></li>
                          <li class="text-right"><span class="counter"><?=$total_summery[0]['total']?></span></li>
                      </ul>
                  </div>
                </div>
                <?php if ($head){?>

                  <?php if ($technician){ ?>
                    <div class="col-md-12 col-xs-12 col-sm-6">
                      <div class="white-box white-box-overflow">
                          <h3 class="box-title">TECHNICIANS PENDING TICKET BOARD</h3>
                          <div class="message-center">

                            <?php foreach ($technician as $key => $tech){ ?>

                              <a href="#">
                                  <div class="user-img">
                                    <?php if (empty($tech['image'])){ ?>

                                      <span class="img-circle character_dp">
                                        <?$default_character = str_split($tech['techname']);?>
                                        <?=$default_character[0]?>
                                      </span>

                                    <?php }else{; ?>
                                      <img src="images/dp/<?=$tech['image']?>" alt="user" class="img-circle">
                                    <?php }; ?>
                                    <span class="profile-status online pull-right"></span> </div>
                                  <div class="mail-contnet">
                                      <h5><?=$tech['techname']?></h5>
                                      <span class="mail-desc"><?=$tech['branch']?></span> <span class="time"><?=$tech['total']?></span>
                                  </div>
                              </a>
                            <?php }; ?>

                          </div>
                      </div>
                    </div>
                  <?php }; ?>

                <?php }?>
              </div>
            </div>
          </div>
        </div>
      </div>

    <?}?>

    <div class="panel-details-body">
      <div class="col-md-12">

      <?if(IS_ADMIN){?>

        <?foreach ($adminStatics as $key => $statuStatics) {?>
          <div class="row">
            <p style="font-size: 17px;padding: 9px;">All Tickets for <?=$statuStatics['depart']?> <strong>(<?=fDate($weektodate);?>)</strong> </p>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#dc3545;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bell"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Pending Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$statuStatics['pending'];?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a  style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=admin&status=pending&depId=<?=$statuStatics['departId']?>" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#218838;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bed"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Completed (Not Verified)</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$statuStatics['notVerified'];?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a  style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=admin&status=notVerified&depId=<?=$statuStatics['departId']?>" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#138496;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-trophy"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Under Process (for parts)</h4>
                        <div class="info">
                          <div style="color:#f6f6f6;"  class="amount"><?=$statuStatics['PendingForParts']?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a  style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=admin&status=pendingForParts&depId=<?=$statuStatics['departId']?>" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


        <?}?>



        <?}else{?>

          <div class="row">
            <p>All Your Tickets <strong>(<?=fDate($weektodate);?>)</strong> </p>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#138496;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bell"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Assigned Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$allAssigned;?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=assigned" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#dc3545;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bed"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Pending Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$allPending;?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=pending" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#218838;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-trophy"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Completed Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$allCompleted?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=completed" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <p>Weekly Bases from <strong>(<?=fDate($weekfromdate);?> - <?=fDate($weektodate);?>)</strong> </p>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#138496;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bell"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Assigned Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$weekassigned_static;?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=assigned&period=week" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#dc3545;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bed"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Pending Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$weekapending_static;?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=pending&period=week" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#218838;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-trophy"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Completed Tickets</h4>
                        <div class="info" >
                          <div class="amount" style="color:#f6f6f6;"><?=$weekacomplete_static?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=completed&period=week" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <p>Monthly Bases from <strong>(<?=fDate($monthfromdate);?> - <?=fDate($monthtodate);?>)</strong> </p>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#138496;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bell"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Assigned Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$monthassigned_static;?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=assigned&period=month" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#dc3545;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-bed"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Pending Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$monthapending_static;?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=pending&period=month" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-6 col-md-4">
              <div class="card card-featured-left card-featured-primary mb-3">
                <div class="card-body" style="background-color:#218838;">
                  <div class="widget-summary">
                    <div class="widget-summary-col widget-summary-col-icon">
                      <div class="summary-icon bg-tertiary">
                        <i class="fa fa-trophy"></i>
                      </div>
                    </div>
                    <div class="widget-summary-col">
                      <div class="summary">
                        <h4 class="title" style="color:#f6f6f6;">Completed Tickets</h4>
                        <div class="info">
                          <div class="amount" style="color:#f6f6f6;"><?=$monthacomplete_static?></div>
                        </div>
                      </div>
                      <div class="summary-footer">
                        <span></span>
                        <a style="color:#f6f6f6 !important;" href="?module=reports&action=view_report&preferred=completed&period=month" class="text-muted text-uppercase">More details</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <?}?>

      </div>
    </div>
  </section>
</div>
