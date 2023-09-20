<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Order <?= $order['orderid'] ?></title>
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
<div id="printBtn" style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button type="button" class="btn btn-primary" style="background-color: cornflowerblue;" onclick="window.print()">
        Print
    </button>
    <? if (isset($_GET['redirect'])) { ?>
        <a id="printBtn" href="<?= base64_decode($_GET['redirect']) ?>" class="btn btn-primary" style="background-color: forestgreen;"> Back </a>
    <? } ?>
</div>
<section class="sheet no-height">
    <div style="padding: 5mm;">
        <?=component('shared/print_top_header.tpl.php')?>
        <div class="heading d-flex justify-content-center">
            <h3 class="text-weight-bold"><u>ORDER FORM</u></h3>
        </div>
        <div class="d-flex justify-content-between">
            <div class="customer-info">
                <p class="p-none m-none text-weight-bold"><u>Customer Details</u></p>
                <p class="p-none m-none text-weight-bold"><?= $order['clientname'] ?></p>
                <p class="p-none m-none text-weight-bold">TIN: <?= $order['clienttinno'] ?></p>
                <p class="p-none m-none text-weight-bold">VRN: <?= $order['clientvrn'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $order['clientaddress'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $order['clientmobile'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $order['clientemail'] ?></p>
            </div>
            <div class="invoice-info">
                <table>
                    <tbody>
                    <tr>
                        <td class="text-right">Order No:</td>
                        <td><span class="text-weight-bold ml-sm"><?= $order['orderid'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Date:</td>
                        <td><span class="text-weight-bold ml-sm"><?= fDate($order['doc']) ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Currency:</td>
                        <td>
                            <span class="text-weight-bold ml-sm"><?= strtoupper($order['currencyname']) ?> - <?= $order['currency_description'] ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="products">
            <table class="table product-table">
                <thead>
                <tr>
                    <th>S No.</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>VAT %</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
                </thead>
                <tbody>
                <? $count = 1;
                foreach ($order['details'] as $index => $item) { ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= CS_PRINTING_SHOW_DESCRIPTION ? $item['productdescription'] : $item['productname'] ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td><?= $item['vat_rate'] ?></td>
                        <td class="text-right"><?= formatN($item['price']) ?></td>
                        <td class="text-right"><?= formatN($item['incamount']) ?></td>
                    </tr>
                    <? $count++;
                } ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end" style="margin-top: 1mm; page-break-inside: avoid;">
            <div style="width: 50%">
                <table class="table product-table">
                    <tbody>
                    <tr>
                        <td colspan="4" class="text-right text-weight-bold" style="border: none;">TOTAL</td>
                        <td class="text-weight-bold"><?= $order['currencyname'] ?></td>
                        <td class="text-weight-bold text-right"><?= formatN($order['order_value']) ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-between" style="page-break-inside: avoid;">
            <div class="invoice-info">
                <p class="p-none m-none">Amount in Words (<?= $order['currencyname'] ?>): <span
                            class="text-weight-bold"><?= strtoupper(toWords($order['order_value'])) ?></span></p>
                <p class="p-none m-none">Remarks: <?= $order['remarks'] ?></p>
                <p class="p-none m-none">Issued by: <?= $order['issuedby'] ?></p>
                <p class="p-none m-none">Print Date: <?= date('d/m/Y') ?></p>
                <p class="p-none m-none">Print Time: <?= date('H:i:s') ?></p>
            </div>
            <div class="signature d-flex flex-column justify-content-end align-items-center">
                <p class="p-none m-none">Signature:_______________________________</p>
            </div>
        </div>
        <? if ($banks) { ?>
            <?= component('shared/print_bank_info.tpl.php', ['banks' => $banks]) ?>
        <? } ?>
    </div>
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
                                    <th>Qty</th>
                                    <th>VAT %</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Amount</th>
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
