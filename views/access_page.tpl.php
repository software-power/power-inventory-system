<link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css"/>
<link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
<link rel="stylesheet" href="assets/css/custom.css"/>
<style media="screen">

    body {
        background-color: #ecedf0;
        height: 100vh;
    }
</style>

<div class="d-flex justify-content-center align-items-center" style="height: 100vh">
    <div class="col-md-6">
        <div style="font-size: 40pt"><i class="fa fa-lock" style="color: red"></i> Access Denied</div>
        <p>System is working fine! but You don't have permission to
            <span class="text-danger" style="font-weight: bold"><?if($msg = base64_decode($_GET['right_action'])){echo strtolower($msg);}else{?>access this page<?}?></span>. Please contact administrator(s) for access</p>
        <? if (getBack()) { ?>
            <a class="btn btn-primary" href="<?= getBack() ?>"><i class="fa fa-arrow-left"></i> Back</a>
        <? } ?>
        <a class="btn btn-success" href="?"><i class="fa fa-home"></i> Home</a>
    </div>
</div>
