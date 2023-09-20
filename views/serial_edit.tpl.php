<style media="screen">
.popup_container {
top: 0;
left: 0;
width: 100%;
height: 100%;
z-index: 1042;
overflow: hidden;
position: fixed;
background: #0b0b0b;
opacity: 0.8;
display:none;
filter: alpha(opacity=80);
}
.pop-col-holder {
background: #ffffff;
width: 50%;
margin: 0 auto;
position: relative;
top: 117px;
border-radius: 5px;
padding: 16px;
}
.popup_info{
width:50%;
}
.popup_info h4{
border-bottom: 1px solid rgba(158, 158, 158, 0.5215686274509804);
width: 100%;
padding: 10px;
}
.control-btn ul{
padding:0;
list-style:none;
}
.control-btn{
float:right;
}
.control-btn ul li{
display:inline-block;
margin-left:10px;
}
.popup_info p{
font-size:15px;
}
p.serialNote {
font-size: 16px;
color: red;
}
#form h5 {
    font-size: 16px;
    font-weight: 600;
}
.select2-container .select2-selection--single {
    height: 36px;
}
.diagnose-issue {
  width: 10px;
    height: 10px;
    background: red;
    display: inline-block;
    border-radius: 100%;
    position: absolute;
    right: 5px;
}
</style>
<header class="page-header">
<h2><?if ($edit) echo 'Edit'; else echo 'Add';?> Serial Number</h2>
</header>

<?if($_GET['serial']){?>

<div class="popup_container">
  <div class="pop-col-holder">
  	<div class="container">
  		<div class="popup_info">
  			<h4>AMC (<i>Annual Maintenance Contract</i>)</h4>
  			<p>Do you want to create AMC for this serial number <strong><?=$_GET['serial']?></strong> ?</p>
  			<div class="control-btn">
  				<ul>
  					<li><a href="?module=serials&action=add_amc&id=<?=$_GET['id']?>&serial=<?=$_GET['serial']?>" class="btn btn-primary">Go to</a> </li>
  					<li> <button onclick="closeModel(this)" id="close-popup-module" class="btn btn-danger">Cancel</button> </li>
  				</ul>
  			</div>
  		</div>
  	</div>
  </div>
</div>

<?}?>



<div class="row">
<div class="col-lg-12">
	<section class="panel" style="width: 70%;margin:0 auto">
		<header class="panel-heading">
			<h2 class="panel-title">Serial Details</h2>
			<?if($serial['isreplaced'] == 1){
				echo "<p class='serialNote'>*NOTE: This serial number is replaced from <strong>".$serial['replacedfrom']."</strong> to <strong>".$serial['name']."</strong></p>";
			}?>
		</header>
		<div class="panel-body">
			<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('serials','serial_save')?>"  enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="300000000" />
				<div class="row">
					<div class="col-md-12">
						<h5>Serial Number</h5>
						<input type="hidden" name="id" value="<?=$serial['id']?>">
						<input type="text" required class="form-control" id="name" title="Name is required" style="width:100%" name="serial[name]" value="<?if($_GET['newserial']){echo $_GET['newserial'];}else{echo $serial['name'];}?>">
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h5>Product</h5>
            <?php if ($_GET['action'] != 'serial_add'){?>
              <span class="<?if(empty($serial['prodid'])) echo 'diagnose-issue';?>"></span>
            <?php }; ?>
						<select name="serial[prodid]" id="productsid" required class="form-control mb-md">
							<option value="<?=$serial['prodid']?>"><?=$serial['productname']?></option>
						</select>
					</div>
					<div class="col-md-6">
						<h5>Department</h5>
            <?php if ($_GET['action'] != 'serial_add'){?>
              <span class="<?if(empty($serial['deptid'])) echo 'diagnose-issue';?>"></span>
            <?php }; ?>
						<select id="departId" name="serial[deptid]" required class="form-control mb-md">
							<option value="<?=$serial['deptid']?>"><?=$serial['departname']?></option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h5>Client</h5>
            <?php if ($_GET['action'] != 'serial_add'){?>
              <span class="<?if(empty($serial['clientid'])) echo 'diagnose-issue';?>"></span>
            <?php }; ?>
						<select name="serial[clientid]" id="clientid" class="form-control mb-md">
							<option value="<?=$serial['clientid']?>"><?=$serial['clientname']?></option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h5>Location</h5>
						<select name="serial[locid]" required class="form-control mb-md">
							<option value=""></option>
							<? foreach ($locs as $p){?>
								<option <?if ($p['id'] == $serial['locid']) echo 'selected';?> value="<?=$p['id']?>"><?=$p['name']?></option>
							<?}?>
						</select>
					</div>
					<div class="col-md-6">
						<h5>invoice No.</h5>
						<input type="text" class="form-control" name="serial[invoiceno]" value="<?=$serial['invoiceno']?>">
					</div>
				</div>
        <div class="row">
          <div class="col-md-12">
            <h5>Physical Location</h5>
            <input placeholder="tabata, ilala" type="text" class="form-control" name="serial[ploc]" value="<?=$serial['ploc']?>">
          </div>
        </div>
				<div class="row">
					<div class="col-md-6">
						<h5>AMC From</h5>
						<input type="text" readonly class="form-control" readonly name="serial[amcfrom]" value="<?if ($serial['amcfrom']>0) echo fDate($serial['amcfrom'],'d/m/Y'); else echo '00/00/0000'?>">
					</div>
					<div class="col-md-6">
						<h5>AMC To</h5>
						<input type="text" readonly class="form-control" readonly name="serial[amcto]" value="<?if ($serial['amcto']>0) echo fDate($serial['amcto'],'d/m/Y'); else echo '00/00/0000'?>">
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h5>Site Visits</h5>
						<input type="number" name="serial[sitevisit]" class="form-control" value="<?=$serial['sitevisit']?>"/>
					</div>
					<div class="col-md-6">
						<h5>Remote Support</h5>
						<input type="number" name="serial[remote]" class="form-control" value="<?=$serial['remote']?>"/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h5>Warranty From</h5>
						<input type="text" readonly class="form-control" readonly name="serial[warrantydatefrom]" value="<?if ($serial['warrantydatefrom']>0) echo fDate($serial['warrantydatefrom'],'d/m/Y'); else echo fDate(TODAY,'d/m/Y')?>">
					</div>
					<div class="col-md-6">
						<h5>Warranty To</h5>
						<input type="text" readonly class="form-control" readonly name="serial[warrantydateto]" value="<?if ($serial['warrantydateto']>0) echo fDate($serial['warrantydateto'],'d/m/Y'); else echo '00/00/0000' ?>">
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<h5>Fiscal Status</h5>
						<select <?if($serial['isfiscal'] == 1) echo 'readonly disabled';?> name="serial[isfiscal]" class="form-control">
							<option value=""></option>
							<option value="1" <?=selected($serial['isfiscal'],'1')?>>Yes</option>
							<option value="0" <?=selected($serial['isfiscal'],'0')?>>No</option>
						</select>
					</div>
					<div class="col-md-6">
						<h5>Status</h5>
            <?php if ($_GET['action'] != 'serial_add'){?>
              <span class="<?if($serial['status'] == 'inactive') echo 'diagnose-issue';?>"></span>
            <?php }?>
						<select name="serial[status]" class="form-control">
							<option value="active" <?=selected($serial['status'],'active')?>>Active</option>
							<option value="inactive" <?=selected($serial['status'],'inactive')?>>In-Active</option>
						</select>
					</div>
				</div>
        <div class="form-group">
          <div class="col-md-6">
            <a href="?module=serials&action=serial_index" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back to list</a>
          </div>
          <div class="col-md-6">
            <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block">
              <?php if ($diagnose){?>
                <i class="fa fa-steam"></i> Resolve Serial Number
              <?php }else{?>
                <i class="fa fa-save"></i> Save
              <?php }; ?>
            </button>
          </div>
        </div>
      </form>
      <script>

      $(function(){

        $('#departId').select2({width:'100%',minimumInputLength:3,
          ajax:{
            url:"?module=departments&action=getDepartments&format=json",dataType:'json',delay:250,quietMillis:200,
            data: function (term) {return {search:term};},
            results:function (data,page){return{result:data};}
          }
        });

        $('#productsid').select2({width:'100%',minimumInputLength:3,
          ajax:{
            url:"?module=products&action=getProducts&format=json",dataType:'json',delay:250,quietMillis:200,
            data: function (term) {return {search:term};},
            results:function (data,page){return{result:data};}
          }
        });

        $("#clientid").select2({ width: '100%', minimumInputLength: 3,
      		ajax: {
      			url: "?module=clients&action=getClients&format=json", dataType: 'json', delay: 250, quietMillis: 200,
            data: function (term) {	return { search : term }; },
            results: function (data, page){
              return { results: data };
            }
      		}
      	 });

        $("#name").focus();
        toggleAmc();
      })


      function toggleAmc(){
        var amc = $("#amc").val();
        if (amc == 'no') $(".amcDet").hide();
        if (amc == 'yes') $(".amcDet").show();
      }

      function closeModel() {
        $(this).on('click', function(){
          $('.popup_container').hide();
          //console.log('yes')
        });
      }

    </script>
