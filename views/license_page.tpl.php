<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title><?=$pagetitle?></title>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>


    <link rel="stylesheet" href="assets/css/custom.css"/>
    <style media="screen">

        body {
            background-color: #777;
            height: 100vh;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center" style="height: 100vh">
    <div class="col-xs-12 col-lg-6">
        <div class="panel">
            <header class="panel-heading">
                <h4>LICENSE</h4>
            </header>
            <div class="panel-body">
                <div>
                    COMPANY NAME:
                    <input type="text" readonly class="form-control input-sm" value="<?= CS_COMPANY ?>">
                </div>
                <div class="mt-xs">
                    TIN:
                    <input id="tin" type="text" class="form-control input-sm" name="tin" value="<?= CS_TIN ?>">
                </div>
                <div class="mt-xs">
                    TOKEN:
                    <textarea id="token" name="token" class="form-control input-sm" rows="15"><?= CS_LICE_TOKEN ?></textarea>
                </div>
                <div class="d-flex justify-content-between mt-sm">
                    <div>
                        <p id="token-msg" class="text-weight-semibold" style="display: none"></p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-default btn-sm" onclick="check_license()">
                                <span class="d-flex align-items-center">
                                    <span id="check-spinner" style="display:none ">
                                        <span class="spinner-border  spinner-border-sm mr-xs" style="height: 15px;width: 15px;"></span>
                                    </span>
                                    <span>Check License</span>
                                </span>
                        </button>
                        <button id="update-token" type="button" class="btn btn-success btn-sm ml-md" style="display: none"
                                onclick="register_license()">
                                <span class="d-flex align-items-center">
                                    <span id="register-spinner" style="display:none ">
                                        <span class="spinner-border  spinner-border-sm mr-xs" style="height: 15px;width: 15px;"></span>
                                    </span>
                                    <span>Register Token</span>
                                </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function check_license() {
        let tin = $('#tin').val();
        let token = $('#token').val();
        let spinner = $('#check-spinner');
        let message = $('#token-msg');
        let save_btn = $('#update-token');

        save_btn.hide();
        message.hide();
        spinner.hide().show();
        $.post(`?module=authenticate&action=checkLicense&format=json`, {tin: tin, token: token}, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            // console.log(result);

            if (result.status === 'success') {
                $('#tin, #token').prop('readonly', true);
                message.text("Token is valid").removeClass('text-danger').addClass('text-success').show('fast');
                save_btn.show('fast');
            } else {
                message.text(result.msg || 'error found').addClass('text-danger').removeClass('text-success').show('fast');
            }
        });
    }

    function register_license() {
        let tin = $('#tin').val();
        let token = $('#token').val();
        let spinner = $('#register-spinner');
        let message = $('#token-msg');

        message.hide();
        spinner.hide().show();
        $.post(`?module=authenticate&action=registerLicense&format=json`, {tin: tin, token: token}, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            // console.log(result);

            if (result.status === 'success') {
                $('#tin, #token').prop('readonly', true);
                message.text("Token is valid, you will be redirected soon").removeClass('text-danger').addClass('text-success').show('fast');

                setTimeout(function () {
                    window.location.replace(`<?=url('home', 'index')?>`);
                }, 3000);
            } else {
                message.text(result.msg || 'error found').addClass('text-danger').removeClass('text-success').show('fast');
            }
        });
    }
</script>

</body>
</html>