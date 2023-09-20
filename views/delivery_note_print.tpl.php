<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Delivery Note <?= $sale['receipt_no'] ?></title>
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

        div.tablePage:not(div.tablePage:first-of-type) {
            page-break-inside: avoid;
            page-break-before: always;
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
                margin-top: 10mm;
            }
        }
    </style>
</head>

<body class="A4">
<button id="printBtn" type="button" class="btn btn-primary" onclick="window.print()"
        style="position: absolute;top:0;right: 20mm;background-color: cornflowerblue;z-index: 1000;">
    Print
</button>
<section class="sheet no-height">
    <div style="padding: 5mm;">
        <?=component('shared/print_top_header.tpl.php')?>
        <div class="heading d-flex justify-content-center">
            <h3 class="text-weight-bold"><u>DELIVERY NOTE</u></h3>
        </div>
        <div class="d-flex justify-content-between">
            <div class="customer-info">
                <p class="p-none m-none text-weight-bold"><u>Customer Details</u></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['clientname'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['clientinno'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['client_address'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['mobile'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['email'] ?></p>
            </div>
            <div class="invoice-info">
                <table>
                    <tbody>
                    <tr>
                        <td class="text-right">Delivery No:</td>
                        <td><span class="text-weight-bold ml-sm"><?= $sale['id'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Invoice Date:</td>
                        <td><span class="text-weight-bold ml-sm"><?= fDate($sale['doc']) ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Invoice No:</td>
                        <td><span class="text-weight-bold ml-sm"><?= $sale['receipt_no'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Terms of payment:</td>
                        <td><span class="text-weight-bold ml-sm"><?= strtoupper($sale['paymenttype']) ?></span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="products">
            <table class="table product-table">
                <thead>
                <tr>
                    <th>SNo.</th>
                    <th>Description</th>
                    <th style="min-width: 20mm;">Qty</th>
                </tr>
                </thead>
                <tbody>
                <? $count = 1;
                foreach ($sale['products'] as $index => $product) { ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= CS_PRINTING_SHOW_DESCRIPTION ? ($product['print_extra'] ? $product['extra_description'] : $product['productdescription']) : $product['productname'] ?></td>
                        <td><?= $product['quantity'] ?></td>
                    </tr>
                    <? $count++;
                } ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between" style="margin-top:2mm;page-break-inside: avoid;">
            <div class="invoice-info">
                <p class="p-none m-none">Sales Counter: <?= $sale['issuedby'] ?></p>
                <p class="p-none m-none">Print Date: <?= date('d/m/Y') ?></p>
                <p class="p-none m-none">Print Time: <?= date('H:i:s') ?></p>
            </div>
            <div class="signature d-flex flex-column justify-content-end align-items-center">
                <p class="p-none m-none">Signature:_______________________________</p>
            </div>
        </div>
        <div class="d-flex justify-content-between" style="margin-top:5mm;page-break-inside: avoid;">
            <p class="p-none m-none">Receiver's name:______________________________________</p>
            <p class="p-none m-none">Receiver's Signature:_______________________________</p>
        </div>
    </div>
    <? if ($sale['serialnos']) { ?>
        <div class="serialnos" style="margin: 2mm 5mm;page-break-before: always">
            <h5>Serial Nos</h5>
            <div style="width: 50%;">
                <table class="table table-bordered table-condensed serialno-table">
                    <thead>
                    <tr>
                        <th>SNo.</th>
                        <th>Product</th>
                        <th>Serial Nos</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($sale['serialnos'] as $index => $item) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $item['productname'] ?></td>
                            <td>
                                <? foreach ($item['numbers'] as $number) { ?>
                                    <div><?= $number ?></div>
                                <? } ?>
                            </td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <? } ?>
    <?= component('shared/print_bottom_footer.tpl.php') ?>
</section>
<script>
    window.onload = function () {
        window.print();
    };

    $(function () {
        <?if(CS_SHOW_PRINT_FOOTER){?>

        let mm2px = 3.779528;
        let MaxHeight = 270 * mm2px;
        let RunningHeight = 0;
        let PageNo = 1;
        $('div.products table.product-table tbody tr').each(function () {
            if (PageNo === 1) {
                MaxHeight = 160 * mm2px;
            } else {
                MaxHeight = 250 * mm2px;
            }
            if (RunningHeight + $(this).height() > MaxHeight) {
                RunningHeight = 0;
                PageNo += 1;
            }
            RunningHeight += $(this).height();
            $(this).attr("data-page-no", PageNo);
        });
        for (let i = 1; i <= PageNo; i++) {
            let table = `
                        <div class="tablePage">
                            <table id="Table${i}" class="table tablePage">
                                <thead>
                                <tr>
                                    <th>SNo.</th>
                                    <th>Description</th>
                                    <th style="min-width: 20mm;">Qty</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>`;
            $('.products>table.product-table').parent().append(table);
            let rows = $('table tr[data-page-no="' + i + '"]');
            $('#Table' + i).find("tbody").append(rows);
        }
        $('.products table.product-table').remove();

        <?}?>
    });
</script>
</body>
</html>
