<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Installment Plan Invoice No <?= $sale['receipt_no'] ?></title>
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
            -webkit-print-color-adjust: exact !important;
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
    <button type="button" class="btn btn-primary" style="background-color: cornflowerblue" onclick="window.print()"> Print</button>
    <? if (isset($_GET['redirect'])) { ?>
        <a href="<?= base64_decode($_GET['redirect']) ?>" class="btn btn-success">Go back</a>
    <? } ?>
</div>
<section class="sheet no-height">
    <div style="padding: 5mm;">
        <?=component('shared/print_top_header.tpl.php')?>
        <div class="heading d-flex justify-content-center">
            <h3 class="text-weight-bold"><u>INSTALLMENT PLAN</u></h3>
        </div>
        <div class="d-flex justify-content-between">
            <div class="customer-info">
                <p class="p-none m-none text-weight-bold"><u>Customer Details</u></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['clientname'] ?></p>
                <p class="p-none m-none text-weight-bold">TIN: <?= $sale['clientinno'] ?></p>
                <p class="p-none m-none text-weight-bold">VRN: <?= $sale['clientvrn'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['client_address'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['mobile'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $sale['email'] ?></p>
            </div>
            <div class="invoice-info">
                <table>
                    <tbody>
                    <tr>
                        <td class="text-right">Invoice No:</td>
                        <td><span class="text-weight-bold ml-sm"><?= $sale['receipt_no'] ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Invoice Date:</td>
                        <td><span class="text-weight-bold ml-sm"><?= fDate($sale['doc']) ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right">Currency:</td>
                        <td><span class="text-weight-bold ml-sm"><?= strtoupper($sale['currencyname']) ?></span></td>
                    </tr>
                    <? if ($sale['base_currency'] != 'yes') { ?>
                        <tr>
                            <td class="text-right">Exchange rate:</td>
                            <td><span class="text-weight-bold ml-sm"><?= strtoupper($sale['currency_amount']) ?></span></td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="products" style="margin-top: 2mm;">
            <table class="table product-table">
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
                <tbody>
                <? $count = 1;
                foreach ($sale['products'] as $index => $product) { ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= CS_PRINTING_SHOW_DESCRIPTION ? ($product['print_extra'] ? $product['extra_description'] : $product['productdescription']) : $product['productname'] ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td><?= $product['vat_rate'] ?></td>
                        <td class="text-right"><?= formatN($product['price']) ?></td>
                        <td class="text-right"><?= formatN($product['amount']) ?></td>
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
                        <td class="text-right text-weight-bold" style="border: none;width: 50%">Exclusive Amount</td>
                        <td><span class="text-weight-bold text-right"><?= $sale['currencyname'] ?></span></td>
                        <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($sale['grand_amount']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right text-weight-bold" style="border: none;width: 50%">VAT Amount</td>
                        <td><span class="text-weight-bold text-right"><?= $sale['currencyname'] ?></span></td>
                        <td class="text-right"><span
                                    class="text-weight-bold ml-md"><?= formatN($sale['grand_vatamount']) ?></span></td>
                    </tr>
                    <tr>
                        <td class="text-right text-weight-bold" style="border: none;width: 50%">Inclusive Amount</td>
                        <td><span class="text-weight-bold text-right"><?= $sale['currencyname'] ?></span></td>
                        <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($sale['full_amount']) ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-between" style="page-break-inside: avoid;">
            <div class="installment-plans" style="width: 50%;">
                <p><strong>Installment Plans</strong></p>
                <table class="table table-bordered table-condensed">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Time</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    $total_plan_amount = 0;
                    foreach ($sale['installment_plans'] as $p) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $p['time'] ?></td>
                            <td><?= formatN($p['amount']) ?></td>
                        </tr>
                        <? $count++;
                        $total_plan_amount += $p['amount'];
                    } ?>
                    <tr class="text-weight-semibold">
                        <td colspan="2" class="text-center">TOTAL</td>
                        <td><?= formatN($total_plan_amount) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="signature d-flex flex-column justify-content-end align-items-end">
                <p class="p-none m-none mb-md">Name:__________________________________</p>
                <p class="p-none m-none mb-md">Date:__________________________________</p>
                <p class="p-none m-none">Signature:__________________________________</p>
            </div>
        </div>
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
