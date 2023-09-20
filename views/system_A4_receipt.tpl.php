<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>System Receipt<?= $sale['receipt_no'] ?></title>
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
    <a id="printBtn" href="<?= $_GET['redirect'] ? base64_decode($_GET['redirect']) : '?' ?>" class="btn btn-primary"
       style="background-color: forestgreen;">
        Back
    </a>
</div>
<section class="sheet no-height">
    <div style="padding: 5mm;">
        <?= component('shared/print_top_header.tpl.php') ?>
        <div class="heading d-flex justify-content-center">
            <h3 class="text-weight-bold">
                <u><?= CS_SR_INVOICE_TITLE ?></u>
            </h3>
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
                    <tr>
                        <td class="text-right">Currency:</td>
                        <td><span class="text-weight-bold ml-sm"><?= strtoupper($sale['currencyname']) ?></span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="products">
            <table class="table">
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
                foreach ($sale['products'] as $index => $product) { ?>
                    <tr>
                        <td><?= $count ?></td>
                        <td><?= CS_PRINTING_SHOW_DESCRIPTION ? ($product['print_extra'] ? $product['extra_description'] : $product['productdescription']) : $product['productname'] ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td><?= $product['vat_rate'] ?></td>
                        <td class="text-right"><?= formatN($product['price']) ?></td>
                        <td class="text-right"><?= formatN($product['incamount']) ?></td>
                    </tr>
                    <? $count++;
                } ?>
                <tr>
                    <td colspan="6" style="border: none;"></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right text-weight-bold" style="border: none;">Exclusive Amount</td>
                    <td><span class="text-weight-bold ml-sm"><?= $sale['currencyname'] ?></span></td>
                    <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($sale['grand_amount']) ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right text-weight-bold" style="border: none;">VAT Amount</td>
                    <td><span class="text-weight-bold ml-sm"><?= $sale['currencyname'] ?></span></td>
                    <td class="text-right"><span
                                class="text-weight-bold ml-md"><?= formatN($sale['grand_vatamount']) ?></span></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right text-weight-bold" style="border: none;">Inclusive Amount</td>
                    <td><span class="text-weight-bold ml-sm"><?= $sale['currencyname'] ?></span></td>
                    <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($sale['full_amount']) ?></span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between">
            <div class="invoice-info" style="width: 50%">
                <p class="p-none m-none">Amount in Words (<?= $sale['currencyname'] ?>): <span
                            class="text-weight-bold"><?= strtoupper(toWords($sale['full_amount'])) ?></span></p>
                <p class="p-none m-none">Due Date of Invoice: <?= fDate($sale['doc']) ?></p>
                <p class="p-none m-none">Sales Counter: <?= $sale['issuedby'] ?></p>
                <p class="p-none m-none">Print Date: <?= date('d/m/Y') ?></p>
                <p class="p-none m-none">Print Time: <?= date('H:i:s') ?></p>
                <p class="p-none m-none text-weight-bold">Remarks:</p>
                <p class="p-none m-none mb-xs" style="white-space: pre-wrap"><?= $sale['description'] ?></p>
            </div>
            <div class="signature d-flex flex-column justify-content-end align-items-center" style="width: 50%">
                <p class="p-none m-none">Signature:_______________________________</p>
            </div>
        </div>
        <? if ($sale['billid']) { ?>
            <div class="mt-md text-weight-semibold">
                <div>* The above is Provisional for verification, FISCALIZE Invoice will be Issued after CLIENT verification.</div>
                <div>* Please request for your FISCAL Invoice after verification.</div>
            </div>
        <? } ?>
        <? if ($banks) { ?>
            <?= component('shared/print_bank_info.tpl.php', ['banks' => $banks]) ?>
        <? } ?>
    </div>
    <? //component('shared/print_bottom_footer.tpl.php') ?>
</section>
<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
