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
.form-control{
  height:44px;
  font-size: 16px;
}
</style>
<header class="page-header">
<h2>Diagnose Serial Number</h2>
</header>
<div class="row">
<div class="col-lg-12">
	<section class="panel" style="width: 70%;margin:0 auto">
		<header class="panel-heading">
			<h2 class="panel-title">Serial Details</h2>
      <small>Diagnose serial number, this for finding the problems for serial number and it will allow you to Resolve those issues</small>
		</header>
		<div class="panel-body">
			<form id="form" class="form-horizontal form-bordered" method="post" action="<?=url('serials','serial_diagnose_process')?>">
				<div class="row">
					<div class="col-md-12">
						<h5>Serial Number</h5>
						<input placeholder="Serial number" type="text" required class="form-control" id="name" title="Serial number is required" name="serialnumber" value="<?=$serialno?>">
					</div>
				</div>
        <div class="form-group">
          <div class="col-md-12">
            <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-stethoscope"></i> Diagnosing</button>
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
            results: function (data, page) { return { results: data }; }
      		}
      	 });
        $("#name").focus();
      })

      function closeModel() {
        $(this).on('click', function(){
          $('.popup_container').hide();
          //console.log('yes')
        });
      }
    </script>
