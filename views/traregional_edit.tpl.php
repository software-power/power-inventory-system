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
	<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> TRA Regional</h2>
</header>
<div class="row">
	<div class="col-lg-12">
		<section class="panel center-panel">
			<header class="panel-heading">
				<div class="action-holder">
					<a href="?module=home&action=index" class="btn btn-primary"><i class="fa fa-home"></i></a>
				</div>
				<h2 class="panel-title">Regional Details</h2>
			</header>
			<div class="panel-body">
				<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('tra','tra_save')?>">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <h5>Manager Name</h5>
                <input type="hidden" name="id" value="<?=$tra['id']?>">
                <input type="text" placeholder="Manager Name" class="required form-control" id="mname" title="Manager Name is required" name="tra[mname]" value="<?=$tra['mname']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <h5>*Email 1 <small>(Mandatory)</small></h5>
                <input type="text" placeholder="Email" class="required form-control" id="email1" title="Name is required" name="tra[email]" value="<?=$tra['email']?>">
              </div>
              <div class="col-md-4">
                <h5>Email 2 <small>(Optional)</small></h5>
                <input type="text" placeholder="Email 2" class="form-control" name="tra[email2]" value="<?=$tra['email2']?>">
              </div>
              <div class="col-md-4">
                <h5>Email 3 <small>(Optional)</small></h5>
                <input type="text" placeholder="Email 3" class="form-control" name="tra[email3]" value="<?=$tra['email3']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Mobile</h5>
                <input type="hidden" name="id" value="<?=$tra['id']?>">
                <input readonly class="contry-code" type="text" name="contrycode" value="255">
                <span class="hide-number"></span>
                <input onblur="checkmobilenumber(this)" type="number" class="required form-control" id="mobile" title="Mobile is required" name="tra[mobile]" value="<?=$tra['mobile']?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12">
                <h5>Location</h5>
                <input type="text" placeholder="Regional Location" class="required form-control" id="location" title="Location Mobile is required" name="tra[location]" value="<?=$tra['location']?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <h5>Status</h5>
                <select name="tra[status]" id="status" title="Status is required" class="required form-control">
                  <option selected disabled>--Choose Status--</option>
                  <option value="active" <?=selected($tra['status'],'active')?>>Active</option>
                  <option value="inactive" <?=selected($tra['status'],'inactive')?>>In-Active</option>
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
function checkmobilenumber(obj){
	var input = $(obj);
	var number = input.val();
	var length = number.toString().length;
	if(length != 9){
		triggerError('Mobile is Min than 9 digits or Max than 9');
		$('#savemobile').hide();
		$('#notsave').show();
	}else{
		$('#savemobile').show();
    $('#notsave').hide();
	}
}
</script>
