<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>

    <meta charset="UTF-8">

    <title><?= $pagetitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/css/custom.css"/>
</head>
<style>
    html, body {
        height: 100vh;
    }
</style>
<body>
<div class="d-flex justify-content-center" style="height: 100vh;position: relative;">
    <? if (isset($_GET['redirect'])) { ?>
        <a href="<?= base64_decode($_GET['redirect']) ?>" class="btn btn-success"
           style="position: absolute;top:20px;left: 30px;">Back</a>
    <? } else { ?>
        <a href="<?= url('home', 'index') ?>" class="btn btn-success" style="position: absolute;top:20px;left: 30px;">Back</a>
    <? } ?>
    <iframe src="<?= $url ?>" style="height: 100%" onload="this.contentWindow.print()"></iframe>
</div>
</body>
</html>
