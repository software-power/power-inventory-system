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

    <title>POS Display Screen</title>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style media="screen">
        body {
            background-color: #7d7f85;
        }

        .border-danger {
            box-shadow: 0 0 5px red !important;
        }

        .num-hide {
            display: block;
            background: white;
            width: 32px;
            height: 28px;
            position: absolute;
            right: 21px;
            top: 40px;
        }
    </style>
</head>
<body>


<div class="row d-flex justify-content-center pt-lg">
    <div class="col-sm-11">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between align-items-center">
                <h2 class="panel-title">POS Display Screen</h2>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-sm-12 col-lg-8">
                        <div>
                            <h5>Barcode</h5>
                            <input id="barcode" type="text" autofocus class="form-control input-lg" placeholder="barcode"
                                   data-source="barcode" onchange="search_item(this)">
                        </div>
                        <div class="d-flex justify-content-between mt-sm">
                            <div>
                                <div id="search-spinner" style="display: none">
                                    <div class="d-flex align-items-center mr-sm">
                                        <span class="spinner-border text-danger mr-xs" style="height: 30px;width: 30px;"></span>
                                        <span>please wait ...</span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-lg" onclick="open_modal(this,search_item)"><i
                                        class="fa fa-search"></i> Find
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row d-flex justify-content-center mt-md" style="visibility: <?= $product ? 'visible' : 'hidden' ?>">
                    <div class="col-sm-12 col-md-9">
                        <div>
                            <table class="table" style="font-size: 25pt">
                                <tbody>
                                <tr>
                                    <th>Barcode</th>
                                    <td><?= $product['barcode_office'] ?: $product['barcode_manufacture'] ?></td>
                                </tr>
                                <tr>
                                    <th>Product</th>
                                    <td><?= $product['name'] ?></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td><?= $product['description'] ?></td>
                                </tr>
                                <tr>
                                    <th>QTY</th>
                                    <td><?= $product['stock_qty'] ?></td>
                                </tr>
                                <tr>
                                    <th>PRICE <?= $product["currency"] ?></th>
                                    <td class="text-weight-semibold"><?= formatN($product['inc_quicksale_price']) ?></td>
                                </tr>
                                <tr>
                                    <th>Expiry</th>
                                    <td>
                                        <table class="table table-bordered text-weight-bold" style="font-size: 20pt;">
                                            <tbody>
                                            <? if ($product['batches']) { ?>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Batch no</td>
                                                    <td>Expire date</td>
                                                    <td>QTY</td>
                                                </tr>
                                                <? $bcount = 1;
                                                foreach ($product['batches'] as $b) { ?>
                                                    <tr>
                                                        <td><?= $bcount++ ?></td>
                                                        <td><?= $b['batch_no'] ?></td>
                                                        <td class="<?= $b['expire_remain_days'] <= 0 ? 'text-danger' : '' ?>"><?= $b['expire_date'] ?></td>
                                                        <td><?= $b['total'] ?></td>
                                                    </tr>
                                                <? } ?>
                                            <? } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-2">
                        <a target="_blank"
                           href="<?= url('endpoints', 'print_barcode', ['access_token' => '', 'productcode' => $product['id']]) ?>"
                           class="btn btn-dark btn-lg"><i
                                    class="fa fa-print"></i> Print Label</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?= component('product_search_modal.tpl.php') ?>

<script src="../assets/js/quick_adds.js"></script>
<script>
    function search_item(obj) {
        let source = $(obj).data('source');
        let spinner = $('#search-spinner');
        let url = `<?=url('endpoints', 'pos_display', ['access_token' => ''])?>`;
        if (source === 'barcode') {
            let barcode = $(obj).val() || '';
            if (barcode.length === 0) {
                $(obj).focus();
                return;
            }
            url = `${url}&barcode=${barcode}`;
        } else if (source === 'find') {
            let productcode = $(obj).data('productid') || '';
            if (productcode.length === 0) {
                notifyError('Invalid product code!');
                return;
            }
            url = `${url}&productcode=${productcode}`;
        } else {
            return;
        }
        $(productSearchModal).modal('hide');
        console.log(url);
        spinner.show();
        window.location.replace(url);
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

    <?if($_SESSION['message']){?>
    notifyMessage(`<?=$_SESSION['message']?>`);
    <?}?>

    <?if($_SESSION['error']){?>
    notifyError(`<?=$_SESSION['error']?>`);
    <?}?>
</script>
</body>
</html>
