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
.col-align{
  margin:0 auto;
}
</style>
<header class="page-header">
	<h2>Profile Settings</h2>
</header>
<div class="row">
	<div class="col-xs-12 col-md-12 col-lg-12">
		<section class="panel">
			<header class="panel-heading">
				<div class="panel-actions">
					<a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
					<a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
				</div>

				<h2 class="panel-title">User Preferences</h2>
			</header>
			<div class="panel-body">
        <div class="setting_holder">
          <div class="setting_tab">
            <div class="setting col-md-4">
              <div class="setting-wrapper">
                <div class="ico-setting"><i class="fa fa-image"></i></div>
                <div class="setting_link">
                  <a href="?module=profile&action=profile_image">Profile Picture</a>
                </div>
              </div>
            </div>
            <div class="setting col-md-4">
            <div class="setting-wrapper">
              <div class="ico-setting"><i class="fa fa-lock"></i></div>
              <div class="setting_link">
                <a href="?module=profile&action=password">Manage Password</a>
              </div>
            </div>
            </div>
            <div class="setting col-md-4">
              <div class="setting-wrapper">
                <div class="ico-setting"><i class="fa fa-adjust"></i></div>
                <div class="setting_link">
                  <a href="#">Manage Theme</a>
                </div>
              </div>
            </div>
          </div>
        </div>
			</div>
		</section>




	</div>
</div>
