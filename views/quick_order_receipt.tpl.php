<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Quick order <?= $order['orderid'] ?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="assets/css/custom.css"/>
    <link rel="stylesheet" href="assets/css/paper.css"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style>
        body {
            font-family: 'Tahoma';
            position: relative;
            font-size: 9pt;
            color: black !important;
        }

        .condense {
            line-height: 16px;
        }

        .dashed-line {
            border: none !important;
            border-bottom: 1px dashed !important;
        }

        .totals td {
            border: none !important;
        }

        @page {
            size: 80mm;
        }

        @media print {
            #printBtn {
                display: none;
            }

            @page {
                /*margin for each printed piece of paper*/
                margin: 5mm;
            }

            @page :first {
                margin-top: 0;
            }
        }
    </style>
</head>

<body class="small80">
<div style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button id="printBtn" type="button" class="btn btn-primary" style="background-color: cornflowerblue;"
            onclick="window.print()"> Print
    </button>
    <? if (isset($_GET['redirect'])) { ?>
        <a id="printBtn" href="<?= base64_decode($_GET['redirect']) ?>" class="btn btn-primary" style="background-color: forestgreen;"> Back </a>
    <? } ?>
</div>
<section class="sheet padding-5mm no-height">
    <div class="d-flex flex-column justify-content-center align-items-center">
        <? if (file_exists(CS_LOGO)) { ?>
            <div class="logobar">
                <img src="<?= CS_LOGO ?>" style="width: 258px;height: 60px;" alt="company logo">
            </div>
        <? } ?>
        <p>**** ORDER ****</p>
    </div>
    <div class="company-details text-center condense">
        <p class="p-none m-none text-weight-bold"><?= CS_COMPANY ?></p>
    </div>
    <? if ($order['use_location_address']) { ?>
        <div class="mt-sm condense">
            <? foreach ($order['location_address'] as $item) { ?>
                <p class="p-none m-none"><?= $item ?></p>
            <? } ?>
        </div>
    <? } else { ?>
        <div class="company-details text-center condense">
            <p class="p-none m-none"><?= CS_ADDRESS ?></p>
            <p class="p-none m-none">MOBILE: <?= CS_MOBILE ?></p>
            <p class="p-none m-none">TIN: <?= CS_TIN ?></p>
            <? if (CS_SR_TYPE == SR_TYPE_DETAILED) { ?>
                <p class="p-none m-none">VRN:<?= CS_VRN ?></p>
                <p class="p-none m-none">TAX OFFICE: <?= CS_TAX_OFFICE ?></p>
            <? } ?>
        </div>
    <? } ?>
    <div class="details customer-details mt-sm pb-sm condense">
        <p class="m-none p-none">ORDER NO: <?= $order['orderid'] ?></p>
        <p class="m-none p-none">CUSTOMER NAME: <?= $order['clientname'] ?></p>
        <p class="m-none p-none">ORDER DATE: <?= fDate($order['doc'], 'd F Y H:i') ?></p>
        <p class="m-none p-none text-uppercase">CURRENCY: <?= $order['currencyname'] ?> - <?= $order['currency_description'] ?></p>
    </div>
    <div class="details product-details">
        <table class="table table-condensed">
            <tr class="text-weight-bold">
                <td>Description</td>
                <td align="right">Qty</td>
                <td align="right">Amnt</td>
            </tr>
            <? foreach ($order['details'] as $key => $details) { ?>
                <tr>
                    <td title="<?= $details['productname'] ?>"><?= substr($details['productname'], 0, 30) ?><?= strlen($details['productname']) > 30 ? '...' : '' ?></td>
                    <td align="right"><?= $details['qty'] ?></td>
                    <td align="right"><?= formatN($details['incamount']) ?></td>
                </tr>
            <? } ?>
            <tr>
                <td colspan="3" class="dashed-line"></td>
            </tr>
            <tr class="totals">
                <td colspan="2" class="text-weight-bold">TOTAL:</td>
                <td class="text-right"><?= formatN($order['order_value']) ?></td>
            </tr>
        </table>
    </div>
    <div class="footer-bar">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <p>**** END OF RECEIPT ****</p>
            <p>Powered By Powercomputers LTD</p>
        </div>
    </div>
</section>
<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
