<!doctype html>
<html class="fixed">
<head>
	<meta charset="UTF-8">
	<title>Power Computers Inventory System</title>
	<meta name="keywords" content="Admin MVC" />
	<meta name="description" content="">
	<meta name="author" content="Power Computers">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" href="assets/vendor/pnotify/pnotify.custom.css" />
	<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css" />
	<link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css" />
	<link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
	<link rel="stylesheet" href="assets/stylesheets/theme.css" />
	<link rel="stylesheet" href="assets/stylesheets/skins/default.css" />
	<link rel="stylesheet" href="assets/stylesheets/theme-custom.css">
	<script src="assets/vendor/modernizr/modernizr.js"></script>
</head>
<body style="background-color:#222222;position: relative">
<?= component('shared/license_error_message.tpl.php') ?>

	<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<!-- new login form start -->
				<div class="login-panel" id="loginBody">
					<div class="row">
						<div class="col-md-12">
							<a href="/" class="logo">
								<img src="assets/images/pctl-Logo-white.png" height="54" alt="PowerComputers" />
							</a>
						</div>
					</div>
					<form class="login-form" id="loginForm" method="post" action="<?=url('authenticate','dologin')?>">
						<div class="row lg-new-row">
							<div class="col-md-2">
								<span class="lg-new-form lg-icon"><i class="fa fa-user"></i></span>
							</div>
							<div class="col-md-10">
								<input name="username" autocomplete="off" placeholder="Username" type="text"  id="username" value="<?=$username?>" class="form-control lg-new-form" />
							</div>
						</div>
						<div class="row lg-new-row">
							<div class="col-md-2">
								<span class="lg-new-form lg-icon"><i class="fa fa-lock"></i></span>
							</div>
							<div class="col-md-10">
								<input name="password" placeholder="Password" type="password" class="form-control lg-new-form" />
							</div>
						</div>
						<div class="row lg-new-btn">
							<div class="col-md-12">
								<button type="submit" class="btn btn-block lg-new-login-btn">Login In</button>
							</div>
						</div>
					</form>
				</div>
				<!-- new login form end -->

				<div class="panel panel-sign" id="installBody" style="display:none">
					<div class="login-panel">
						<form class="login-form" id="installForm" method="post" action="<?=url('authenticate','install_index')?>">
							<div class="row lg-new-row">
								<div class="col-md-2">
									<span class="lg-new-form lg-icon"><i class="fa fa-database"></i></span>
								</div>
								<div class="col-md-10">
									<input name="database" autocomplete="off" placeholder="Database Name" type="text" class="form-control lg-new-form" />
								</div>
							</div>
							<div class="row lg-new-row">
								<div class="col-md-2">
									<span class="lg-new-form lg-icon"><i class="fa fa-user"></i></span>
								</div>
								<div class="col-md-10">
									<input name="username" autocomplete="off" placeholder="username" type="text" class="form-control lg-new-form" />
								</div>
							</div>
							<div class="row lg-new-row">
								<div class="col-md-2">
									<span class="lg-new-form lg-icon"><i class="fa fa-lock"></i></span>
								</div>
								<div class="col-md-10">
									<input name="password" autocomplete="off" placeholder="Password" type="password" class="form-control lg-new-form" />
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<button type="submit" class="btn btn-block lg-new-login-btn">Connect to Database</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				<p class="text-center text-muted mt-md mb-md">Developed and maintained by <strong><a style="color:#ff1c1f;" target="_blank" href="https://www.powercomputers.co.tz/">PowerComputers</a></strong> <br> &copy; Copyright <?echo date('Y')?>. All Rights Reserved. </p>
			</div>
		</section>
		<script src="assets/vendor/jquery/jquery.js"></script>
		<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
		<script src="assets/vendor/pnotify/pnotify.custom.js"></script>
		<script src="assets/javascripts/theme.js"></script>
		<script src="assets/javascripts/theme.custom.js"></script>
		<script src="assets/javascripts/theme.init.js"></script>
		<script type='text/javascript'>

	function triggerMessage(msg, o) {
		new PNotify({
			title: 'Success',
			text: ''+msg+'',
			type: 'success',
			delay: 10000
		});
	};

	function triggerError(msg, o) {
		new PNotify({
			title: 'Error',
			text: ''+msg+'',
			type: 'error',
			delay: 10000
		});
	};

	function date(){
		$('.datepicker').datepicker({
			format: 'dd/mm/yyyy',
			autoclose: true,
			startDate: '+6m'
		})

		}
	$( function() {
		$("#username").focus();
		try {
			date();
			<?php if ( $_SESSION['error'] ) { echo 'triggerError("'.$_SESSION['error'].'",null)'; } ?>;
			<?php if ( $_SESSION['message'] ) { echo 'triggerMessage("'.$_SESSION['message'].'",null)'; } ?>;
		}
		catch (e) {}
	});

	function showWizard(){

		$("#loginBody").hide();
		$("#installBody").show();
	}
</script>
	</body>
</html>
</html>
