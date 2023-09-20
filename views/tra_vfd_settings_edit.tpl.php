<style media="screen">
	h5{
		font-weight:700;
	}
	.action-holder{
		float:right;
	}
	.center-panel{
		width:71%;
		margin:0 auto;
	}
	.required{
		height:37px;
		font-size:14px;
	}
	input.contry-code {
    position: absolute;
    z-index: 9;
    font-size: 16px;
    font-weight: 600;
    width: 40px;
    border: none;
    left: 20px;
    padding: 3px;
    background: #ecedf0;
}
.hide-number {
    display: block;
    background: #ffffff;
    width: 28px;
    height: 32px;
    position: absolute;
    right: 20px;
    z-index: 9;
}
#mobile{
	text-align:center;
}
#savemobile{
  /* display:none; */
}
</style>
<header class="page-header">
	<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> VFD Settings</h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="panel center-panel">
			<header class="panel-heading">
				<div class="action-holder">
					<a href="?module=home&action=index" class="btn"><i class="fa fa-home"></i> Home</a>
				</div>
				<h2 class="panel-title">VFD Details</h2>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('tra','vfd_registration')?>">
          <div class="row">
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Username</h5>
                <input type="hidden" name="id" value="<?=$vfd['id']?>">
                <input type="text" placeholder="username" class="required form-control" id="mname" title="username" name="" value="<?=$vfd['vfd_username']?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Password</h5>
                <input type="hidden" name="id" value="<?=$vfd['id']?>">
                <input readonly type="password" placeholder="username" class="required form-control" id="pwd" title="password" name="" value="<?=$vfd['vfd_password']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <h5>Routing Key</h5>
                <input type="text" placeholder="Email" class="required form-control" id="email1" title="Name is required" name="tra[email]" value="<?=$vfd['vfd_routingKey']?>">
              </div>
              <div class="col-md-4">
                <h5>Registration ID</h5>
                <input type="text" placeholder="Email 2" class="form-control" name="tra[email2]" value="<?=$vfd['vfd_registrationID']?>">
              </div>
              <div class="col-md-4">
                <h5>Serial number</h5>
                <input type="text" placeholder="Email 3" class="form-control" name="tra[email3]" value="<?=$vfd['vfd_serial']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <h5>UIN</h5>
                <input type="text" placeholder="UIN" class="required form-control" id="uin" title="uin" name="" value="<?=$vfd['vfd_UIN']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Receipt Number</h5>
                <input readonly type="text" placeholder="Rceipt number" class="form-control" name="" value="<?=$vfd['vfd_receiptCode']?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Regisration Status</h5>
                <select name="tra[status]" id="status" title="Status is required" class="form-control">
                  <option selected disabled>--Choose Status--</option>
                  <option value="active" <?=selected($vfd['is_vfdReg'],1)?>>Registered</option>
                  <option value="inactive" <?=selected($vfd['is_vfdReg'],0)?>>Not-yet</option>
                </select>
              </div>
            </div>
          </div>
					<div class="form-group">
            <!--<div class="" id="notsave">
              <div class="col-md-12">
  							<div class="col-md-12">
  								<a href="?module=tra&action=regional_list" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back to list</a>
  							</div>
  						</div>
            </div>-->
						<div class="" >
              <div class="col-md-12">
  							<div class="col-md-6">
  								<a href="?module=tra&action=regional_list" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back to list</a>
  							</div>
  							<div class="col-md-6">
  								<button id="savemobile" type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save</button>
  							</div>
  						</div>
            </div>
					</div>
				</form>
			</div>
		</section>
	</div>
</div>
<script>
$(function(){
	$("#mname").focus();
})
</script>
