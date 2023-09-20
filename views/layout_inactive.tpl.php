<!doctype html>
<html class="fixed">
<head>
    <meta charset="UTF-8">
    <title><?=CS_COMPANY?></title>
    <meta name="keywords" content="Admin MVC"/>
    <meta name="description" content="">
    <meta name="author" content="Power Web">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/vendor/pnotify/pnotify.custom.css"/>
    <link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="assets/stylesheets/skins/default.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme-custom.css">
    <script src="assets/vendor/modernizr/modernizr.js"></script>
    <style>
        html, body {
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body style="background-color:#222222;position: relative;">
<div style="display: flex;height: 100%;justify-content: center;align-items: center;">
    <div>
        <div style="display: flex;align-items: center;">
            <i class="fa fa-user text-danger" style="font-size: 6em;"></i>
            <h3 class="ml-md">User Account Deactivated</h3>
        </div>
        <p class="mt-md">You are seeing this page because your account is inactive</p>
        <div class="text-center">
            <a class="btn btn-danger" href="<?=url('authenticate', 'logout')?>">Logout</a>
        </div>
    </div>

</div>
<p class="text-center text-muted mt-md mb-md" style="position: absolute;bottom: 0;right: 0;left: 0;">Developed and maintained by <strong>
        <a style="color:#ff1c1f;" target="_blank" href="https://www.powerwebtz.com/">Powerweb</a>
    </strong>
    <br> &copy; Copyright <? echo date('Y') ?>. All Rights Reserved. </p>
<script src="assets/vendor/jquery/jquery.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
</body>
</html>
