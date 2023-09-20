<style type="text/css">
td,th {
  text-align: center;
}
.gif, .downloadinggif{
	display: none;
}
.FoundUpdates, .downloaddone, .updatedone, .NoFoundUpdates{
	display: none;
}
</style>
	<div class="panel-top">
		<div class="col-md-12 container">
			<header class="page-header">
				<h2>Updates</h2>
			</header>
		</div>
		<div class="col-xs-12 col-md-12 container">
			<div class="panel-body">
			<?=$msg?>
			<table class="crm_Table" width="100%">
				<tr>
					<th>Current Version : V <?=formatN(SUPPORT_VERSION)?><br></th>
				</tr>
				<tr></tr>
				
				<tr style="margin-bottom: 10px;">
					<td colspan="2">
						<span id="first" class="gif">Checking for updates <br/>
							<img src="images/loading.gif" width="200px"/> <br/>
						</span>

						<span id="first" class="downloadinggif">Downloading updates <br/>
							<img src="images/loading.gif" width="200px"/> <br/>
						</span>

						<span id="" class="FoundUpdates">New Updates Found, Version <b><span class="newupdate text-success"></span></b> <br/>
							<input type="hidden" class="newupdateval" name="">
							<span class="fa fa-check-circle-o text-success fa-2x" style="font-size: 8em;"></span>
						</span>

						<span id="" class="NoFoundUpdates">No Updates Found.<br/>
							<span class="fa fa-ban text-danger fa-2x" style="font-size: 8em;"></span>
						</span>

						<span id="" class="downloaddone">Downloads Done Succesfully <br/>
							<span class="fa fa-check-circle-o text-success fa-2x" style="font-size: 8em;"></span>
						</span>

						<span id="" class="updatedone">Downloads Done Succesfully <br/>
							<span class="fa fa-check-circle-o text-success fa-2x" style="font-size: 8em;"></span>
						</span>
					</td>
				</tr>
				

				<tr>
					<td colspan=2 class="">
						<!-- <span id="first" class="gif">Checking for updates <br/><img src="images/loading.gif" width="200px"/> <br/></span> -->
						<span id="" class="btn btn-success checkupdates" onclick="checkupdates(this);"> <span class="fa fa-cloud"></span> Check for Updates</span>
						<span class="btn btn-success downloadupdates" onclick="downloadupdates(this);" style="display: none;"> <span class="fa fa-cloud-download"></span> Download Updates</span>
						<span class="btn btn-success installupdates" onclick="installupdates(this);" style="display: none;"> <span class="fa fa-cloud-download"></span> Install Updates</span>
						
						<small id="version"></small><br/>
						<span id="downloading"></span><br/>
						<span id="update"></span><br/>
						<span id="updateMsg"></span><br/>
					</td>	
				</tr>
			</table>
		</div>
	</div>


<script type="text/javascript">

	function checkupdates(obj) {
		$('.NoFoundUpdates').hide('slow');
		$('.checkupdates').hide('slow');
		$(".gif").hide('slow');
		var gif = $(".gif");
		var FoundUpdates = $(".FoundUpdates");
		gif.show('slow');
		    $.get("?module=update&action=checkUpdates&format=json",null,function(data){
		      var update = eval(data);

		      if (update[0].found == true) {
					gif.hide('slow');
					FoundUpdates.show('slow');
					$('.newupdate').append(update[0].updateVersion);
					$('.newupdateval').val(update[0].updateVersion);
					$('.checkupdates').hide('slow');
					$('.downloadupdates').show('slow');
		      }else{
		      		$('.NoFoundUpdates').show('slow');
					$('.checkupdates').show('slow');
					$(".gif").hide('slow');
		      }
		      
		    });
	}

	function downloadupdates(obj) {
		var version = $('.newupdateval').val();
		$(".downloadinggif").show('slow');
		$(".FoundUpdates").hide('slow');

		$.get("?module=update&action=downloadUpdate&format=json&version="+version,null,function(data){
			var update = eval(data);
			if (update[0].downloaded == 1) {
				$(".downloadinggif").hide('slow');
				$('.downloadupdates').hide('slow');
				$('.downloaddone').show('slow');
				$('.installupdates').show('slow');

			}
			console.log(update);

		});
	}

	function installupdates(obj) {
		var version = $('.newupdateval').val();
		$.get("?module=update&action=installUpdate&format=json&version="+version,null,function(data){
			var update = eval(data);

			if (update.updated == true) {
				$(".updatedone").show('slow');
				$("#updateMsg").append(update.msg);

				$(".downloadinggif").hide('slow');
				$('.downloadupdates').hide('slow');
				$('.downloaddone').hide('slow');
				$('.installupdates').hide('slow');
				$('.checkupdates').show('slow');
			}

		});
	}

</script>