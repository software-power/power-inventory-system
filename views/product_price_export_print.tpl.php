<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Price List</title>
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
            color: black !important;
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
                margin: 5mm;
                size: landscape;
            }

            @page :first {
                margin-top: 0;
            }


        }
    </style>
</head>

<body class="A4 landscape">
<div style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button id="printBtn" type="button" class="btn btn-primary" style="background-color: cornflowerblue;" onclick="window.print()">
        Print
    </button>
    <a id="printBtn" href="<?= $_GET['redirect'] ? base64_decode($_GET['redirect']) : '?' ?>" class="btn btn-primary"
       style="background-color: forestgreen;">
        Back
    </a>
</div>
<section class="sheet padding-5mm no-height">
    <div class="company-info d-flex justify-content-between" style="border-bottom: 2px solid grey;">
        <div class="logo" style="width: 100mm">
            <img src="<?= CS_LOGO ?>" alt="logo" style="width: inherit;height: 25mm;"/>
        </div>
        <div class="text-weight-semibold">
            <p class="p-none m-none"><?= CS_ADDRESS ?></p>
            <p class="p-none m-none"><?= CS_STREET ?></p>
            <p class="p-none m-none"><?= CS_TEL ?></p>
            <p class="p-none m-none"><?= CS_FAX ?></p>
            <p class="p-none m-none"><?= CS_EMAIL ?></p>
        </div>
    </div>
    <div class="heading d-flex justify-content-center">
        <h3 class="text-weight-bold"><u>Price List</u></h3>
    </div>
    <div>
        <table class="table table-hover mb-none" id="price-list-datatable" style="font-size: 10pt;">
            <thead>
            <tr>
                <th>No.</th>
                <th style="width:10%">Name</th>
                <th>Department</th>
                <th>Brand</th>
                <th>VAT %</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Unit</th>
                <th>Currency</th>
                <? if ($with_stock) { ?>
                    <th>Stock Qty</th>
                    <th><span class="text-success">Exc</span> Price</th>
                <? } ?>
                <th class="text-right"><span class="text-danger">Inc</span> Price</th>
            </tr>
            </thead>
            <tbody>
            <? $count = 1;
            foreach ($products as $p) { ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?=CS_PRINTING_SHOW_DESCRIPTION?$p['productdescription']: $p['productname'] ?></td>
                    <td><?= $p['departmentname'] ?></td>
                    <td><?= $p['brandname'] ?></td>
                    <td><?= $p['vat_percent'] ?>%</td>
                    <td><?= $p['productcategoryname'] ?></td>
                    <td><?= $p['productsubcategoryname'] ?></td>
                    <td><?= $p['unitname'] ?></td>
                    <td><?= $basecurrency['name'] ?></td>
                    <? if ($with_stock) { ?>
                        <td><?= $p['branch_stock_qty'] ?></td>
                        <td class="text-right"><?= formatN($p['export_excprice']) ?></td>
                    <? } ?>
                    <td class="text-right"><?= formatN($p['export_incprice']) ?></td>
                </tr>
                <? $count++;
            } ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-xlg">
        <div class="invoice-info">
            <p class="p-none m-none">Printed by: <?= $_SESSION['member']['name'] ?></p>
            <p class="p-none m-none">Print Date: <?= date('d/m/Y') ?></p>
            <p class="p-none m-none">Print Time: <?= date('H:i:s') ?></p>
        </div>
        <div class="signature d-flex flex-column justify-content-end align-items-center">
            <p class="p-none m-none">Signature:_______________________________</p>
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
