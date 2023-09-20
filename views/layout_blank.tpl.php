<html>
<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.js"></script>
    <? if ($plugins) { ?>
        <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css"/>
        <link rel="stylesheet" href="assets/vendor/select2/select2.css"/>
        <link rel="stylesheet" href="assets/vendor/pnotify/pnotify.custom.css"/>
        <link rel="stylesheet" href="assets/css/notification-style.css"/>
        <script src="assets/vendor/select2/select2.full.js"></script>
    <? }?>
    <!-- Basic -->
    <meta charset="UTF-8">

    <title><?= $pagetitle ?></title>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

</head>
<body>
<?= $content ?>


<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="assets/vendor/pnotify/pnotify.custom.js"></script>
<script src="assets/js/cleave.min.js"></script>
<script>
    (function($) {

        'use strict';

        // use font awesome icons if available
        if ( typeof PNotify != 'undefined' ) {
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

    function thousands_separator(selector,decimal=2) {
        $(selector).toArray().forEach(function(field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: decimal,
                numeralPositiveOnly: true
            });
        });
    }
    function qtyInput(selector) {
        $(selector).toArray().forEach(function(field) {
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
