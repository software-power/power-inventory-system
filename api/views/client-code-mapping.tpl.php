<html>
<!doctype html>
<head>
    <script src="../assets/vendor/jquery/jquery.js"></script>
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="../assets/vendor/select2/select2.css"/>
    <link rel="stylesheet" href="../assets/vendor/pnotify/pnotify.custom.css"/>
    <link rel="stylesheet" href="../assets/css/notification-style.css"/>
    <script src="../assets/vendor/select2/select2.full.js"></script>
    <link rel="stylesheet" href="../assets/intl-tel/css/intlTelInput.css">
    <link rel="stylesheet" href="../assets/stylesheets/theme.css">
    <link rel="stylesheet" href="../assets/css/custom.css"/>
    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Client Code Mapping</title>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style media="screen">
        body {
            background-color: #ecedf0;
        }

        .border-danger {
            box-shadow: 0 0 5px red !important;
        }

        tr:hover .map-btn {
            opacity: 1 !important;
            transition: 0.5s;
        }
    </style>
</head>
<body>


<div class="row d-flex justify-content-center" style="margin-top: 20px">
    <div class="col-sm-12 col-lg-8">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between align-items-center">
                <h2 class="panel-title">Possible matching Clients</h2>
                <div class="d-flex align-items-center" style="cursor: pointer" onclick="window.close()">
                    <i class="fa fa-close fa-3x text-danger"></i>
                    <span class="text-lg ml-xs">Close</span>
                </div>
            </header>
            <div class="panel-body">
                <div>
                    <p>
                        <span class="mr-lg">From support:</span>
                        <span class="mr-md">Name: <span class="text-danger"><?= $sclient['name'] ?></span></span>
                        <span class="mr-md">TIN: <span class="text-danger"><?= $sclient['tinno'] ?></span></span>
                        <span class="mr-md">VRN: <span class="text-danger"><?= $sclient['vatno'] ?></span></span>
                        <span class="mr-md">Mobile: <span class="text-danger"><?= $sclient['mobile'] ?></span></span>
                        <span class="mr-md">Address: <span class="text-danger"><?= $sclient['address'] ?></span></span>
                        <span class="mr-md">District: <span class="text-danger"><?= $sclient['district'] ?></span></span>
                        <span class="mr-md">City: <span class="text-danger"><?= $sclient['city'] ?></span></span>
                    </p>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-danger text-weight-bold mt-md mb-md">NB: Choose correct matching client</div>
                    <div class="d-flex align-items-center">
                        <div id="add-spinner" class="mr-sm" style="display: none">
                            <span class="spinner-border spinner-border-sm" style="width: 25px;height: 25px;"></span>
                        </div>
                        <button id="add-new-btn" type="button" class="btn btn-success" onclick="add_new_client()">Add Client</button>
                    </div>
                </div>
                <table class="table table-condensed" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>TIN</th>
                        <th>VRN</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>District</th>
                        <th>Plot no</th>
                        <th>city</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($possible_matches as $c) { ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $c['name'] ?></td>
                            <td><?= $c['tinno'] ?></td>
                            <td><?= $c['vatno'] ?></td>
                            <td><?= $c['mobile'] ? ($c['mobile_country_code'] . $c['mobile']) : $c['mobile'] ?></td>
                            <td><?= $c['address'] ?></td>
                            <td><?= $c['district'] ?></td>
                            <td><?= $c['plotno'] ?></td>
                            <td><?= $c['city'] ?></td>
                            <td style="width: 100px">
                                <div class="d-flex align-items-center">
                                    <div class="mr-sm mapping-spinner" style="display: none">
                                        <span class="spinner-border spinner-border-sm" style="width: 25px;height: 25px;"></span>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm map-btn" style="opacity: 0.3"
                                            onclick="map_client(this)" data-mainclientid="<?= $c['id'] ?>">
                                        <i class="fa fa-arrow-right"></i> Map
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script src="../assets/js/quick_adds.js"></script>
<script>
    function map_client(obj) {
        let mainclientid = $(obj).data('mainclientid');
        let spinner = $(obj).closest('td').find('.mapping-spinner');
        spinner.hide();
        $('button.map-btn').hide();
        if (mainclientid == null || mainclientid.length === 0) {
            triggerError("No client id found!");
            $('button.map-btn').show();
            return;
        }
        $('#add-new-btn').hide();
        spinner.show();
        let token = `<?=$_GET['access_token']?>`;
        $.post(`?module=endpoints&action=ajax_mapSupportClient&access_token=${token}`, JSON.stringify({client_maincode: mainclientid}), function (data) {
            spinner.hide();
            console.log(data);
            if (data.status === 'success') {
                notifyMessage(data.msg + `\n Window will close soon`, 3000);
            } else {
                notifyError((data.msg || "error found") + `\nWindow will close soon`, 3000);
                $('button.map-btn').show();
            }
            setTimeout(function () {
                window.close();
            }, 3500);
        });
    }

    function add_new_client() {
        let spinner = $('#add-spinner');
        $('#add-new-btn').hide();
        spinner.hide();
        spinner.show();
        let token = `<?=$_GET['access_token']?>`;
        $.post(`?module=endpoints&action=ajax_saveClients&access_token=${token}`, JSON.stringify([]), function (data) {
            spinner.hide();
            console.log(data);
            if (data.status === 'success') {
                notifyMessage(data.msg + `\n Window will close soon`, 3000);
            } else {
                notifyError((data.msg || "error found") + `\nWindow will close soon`, 3000);
                $('button.map-btn').show();
            }
            setTimeout(function () {
                window.close();
            }, 3300);
        });
    }
</script>


<script src="../assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="../assets/vendor/pnotify/pnotify.custom.js"></script>
<script src="../assets/js/cleave.min.js"></script>
<script>
    (function ($) {

        'use strict';

        // use font awesome icons if available
        if (typeof PNotify != 'undefined') {
            PNotify.prototype.options.styling = "fontawesome";

            $.extend(true, PNotify.prototype.options, {
                shadow: false,
                stack: {
                    spacing1: 15,
                    spacing2: 15
                }
            });

            $.extend(PNotify.styling.fontawesome, {
                // classes
                container: "notification",
                notice: "notification-warning",
                info: "notification-info",
                success: "notification-success",
                error: "notification-danger",

                // icons
                notice_icon: "fa fa-exclamation",
                info_icon: "fa fa-info",
                success_icon: "fa fa-check",
                error_icon: "fa fa-times"
            });
        }

    }).apply(this, [jQuery]);

    function notifyMessage(msg, delay = 1000) {
        new PNotify({
            title: 'Success',
            text: '' + msg + '',
            type: 'success',
            delay: delay
        });
    }

    function notifyError(msg, delay = 1000) {
        new PNotify({
            title: 'Error',
            text: '' + msg + '',
            type: 'error',
            delay: delay
        });
    }

    function truncateDecimals(number, digits) {
        let multiplier = Math.pow(10, digits), adjustedNum = number * multiplier,
            truncatedNum = Math[adjustedNum < 0 ? 'ceil' : 'floor'](adjustedNum);

        return truncatedNum / multiplier;
    }

    function thousands_separator(selector, decimal = 2) {
        $(selector).toArray().forEach(function (field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: decimal,
                numeralPositiveOnly: true
            });
        });
    }

    function qtyInput(selector) {
        $(selector).toArray().forEach(function (field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'none',
                numeralDecimalScale: 0,
                numeralPositiveOnly: true
            });
        });
    }
</script>
</body>
</html>
