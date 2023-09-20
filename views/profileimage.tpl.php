<style media="screen">
  .setting_holder .setting_tab .setting {
    padding: 10px;
    font-size: 15px;
}
.ico-setting {
    width: 76px;
    height: 76px;
    padding: 18px;
    font-size: 45px;
    margin: 0 auto;
    background: #ecedf0;
}
.setting_holder .setting_tab .setting .setting-wrapper {
    background: #ecedf0;
    padding: 18px;
    border-radius: 5px;
}
.setting_holder .setting_tab .setting .setting_link,.ico-setting {
    text-align: center;
}
.center-panel{
  margin:0 auto;
  float:none;
}
.image-holder .old-image {
    width: 174px;
    height: 177px;
    padding: 10px;
    background: #ecedf0;
}
.image-holder .old-image img{
  width:156px;
}
form{
  margin-top:20px;
}
.character_name {
    padding: 58px;
    font-size: 100px;
    text-align: center;
    display: inline-block;
}
</style>
<header class="page-header">
	<h2>Profile Image</h2>
</header>
<div class="row col-md-8 center-panel">
	<div class="col-xs-12 col-md-12 col-lg-12">
		<section class="panel">
			<header class="panel-heading">
				<div class="panel-actions">
          <a href="?module=profile&action=index" class="panel-action"> <i class="fa fa-cogs"></i> User Preferences</a>
					<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
				</div>

				<h2 class="panel-title">Upload Image</h2>
			</header>
			<div class="panel-body">
        <div class="image-holder col-md-4">
          <div class="old-image">

            <?if(empty($_SESSION['member']['image'])){?>
                <span class="character_name"><?=$name_character?></span>
              <?}else{?>
                <img src="images/dp/<?=$_SESSION['member']['image']?>" alt="">
            <?}?>

          </div>
        </div>
        <form class="col-md-8" method="post" action="<?=url('profile','image_save')?>" enctype="multipart/form-data">
          <input type="file" class="box form-control" name="image" value="" id="profile_image">
          <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary" name="button">Save Image</button>
        </form>
			</div>
		</section>
	</div>
</div>
