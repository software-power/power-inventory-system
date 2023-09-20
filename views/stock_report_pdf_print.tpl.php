<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Stock Report <?=fDate($stockdate)?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="assets/css/custom.css"/>
    <link rel="stylesheet" href="assets/css/paper.css"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style>
        body {
            /*font-family: Arial;*/
            position: relative;
        }

        @page {
            size: A4;
        }

        @media print {
            #printBtn {
                display: none;
            }

            @page {
                /*margin for each printed piece of paper*/
                margin: 10mm;
            }

            @page :first {
                margin-top: 0;
            }


        }
    </style>
</head>

<body class="A4">
<div style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button id="printBtn" type="button" class="btn btn-primary" style="background-color: cornflowerblue;" onclick="window.print()">
        Print
    </button>
</div>
<section class="sheet padding-5mm no-height">
    <div class="company-info d-flex justify-content-between" style="border-bottom: 2px solid grey;">
        <div class="logo" style="width: 100mm">
            <img src="<?= CS_LOGO ?>" alt="logo" style="width: inherit;height: 25mm;"/>
        </div>
        <div class="text-weight-semibold">
            <p class="p-none m-none"><?= CS_COMPANY ?></p>
            <p class="p-none m-none"><?= CS_ADDRESS ?></p>
            <p class="p-none m-none"><?= CS_STREET ?></p>
            <p class="p-none m-none"><?= CS_TEL ?></p>
            <p class="p-none m-none"><?= CS_FAX ?></p>
            <p class="p-none m-none"><?= CS_EMAIL ?></p>
            <p class="p-none m-none">TIN: <?= CS_TIN ?></p>
            <p class="p-none m-none">VRN: <?= CS_VRN ?></p>
        </div>
    </div>
    <div class="heading d-flex justify-content-center">
        <h3 class="text-weight-bold">Stock Report</h3>
    </div>
    <div class="d-flex justify-content-between">
        <div>
            <p class="p-none m-none">Date: <span class="text-weight-bold"><?=fDate($stockdate)?></span></p>
        </div>
    </div>
    <div class="products">
        <table class="table" style="font-size:10pt;">
            <thead>
            <tr>
                <th>#</th>
                <th>Barcode</th>
                <th style="width: 10%;">Product name</th>
                <th>Tax</th>
                <th style="text-align:center">Stock Qty</th>
                <th>In Unit</th>
                <!--                        <th>Bulk Unit</th>-->
            </tr>
            </thead>
            <tbody>

            <? $count = 1;
            foreach ($stocklist as $ins => $list) { ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?= $list['barcode_office'] ?></td>
                    <td><?= $list['name'] ?></td>
                    <td><?= $list['catName'] ?></td>
                    <td><strong><?= $list['total'] ?></strong></td>
                    <td><?= $list['inunit'] ?></td>
                </tr>
                <? $count++;
            } ?>
            </tbody>
        </table>
    </div>
    <div>
        <p>Printed by: <span class="text-weight-bold"><?=$_SESSION['member']['name']?></span></p>
    </div>
</section>
<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
