<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Credit Note <?= getCreditNoteNo($salereturn['id']) ?></title>
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
        </div>
    </div>
    <div class="heading d-flex justify-content-center">
        <h3 class="text-weight-bold"><u>CREDIT NOTE</u></h3>
    </div>
    <div class="d-flex justify-content-between">
        <div>
            <p class="p-none m-none">Ref: <span class="text-weight-bold"><?= getCreditNoteNo($salereturn['id']) ?></span></p>
            <p class="p-none m-none">To: <span class="text-weight-bold"><?= $salereturn['clientname'] ?></span></p>
            <p class="p-none m-none">Invoice No: <span class="text-weight-bold"><?= $salereturn['invoiceno'] ?></span></p>
            <p class="p-none m-none">Date: <span class="text-weight-bold"><?= fDate($salereturn['doc'], 'd M Y') ?></span></p>
        </div>
    </div>
    <div class="products">
        <table class="table">
            <thead>
            <tr>
                <th>#.</th>
                <th>Description</th>
                <th>Qty</th>
                <th>VAT %</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($salereturn['details'] as $index => $product) { ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= CS_PRINTING_SHOW_DESCRIPTION ? $product['productdescription'] : $product['productname'] ?></td>
                    <td><?= $product['qty'] ?></td>
                    <td><?= $product['vat_rate'] ?></td>
                    <td class="text-right"><?= formatN($product['rate']) ?></td>
                    <td class="text-right"><?= formatN($product['incamount']) ?></td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <div class="totals d-flex justify-content-end">
        <table>
            <tbody>
            <tr>
                <td class="text-right text-weight-bold">Total Amount</td>
                <td><span class="text-weight-bold ml-sm"><?= $salereturn['currencyname'] ?></span></td>
                <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($salereturn['total_incamount']) ?></span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <p class="text-weight-bold"><u>Narration:</u></p>
        <p class="text-weight-bold">BEING CREDIT NOTE ISSUED AGAINST GOODS RETURN NOW ADJUSTED FOR</p>
    </div>
    <div class="d-flex justify-content-between">
        <div class="invoice-info">
            <p class="p-none m-none">Description: <?= $salereturn['description'] ?></p>
            <p class="p-none m-none">Issued by: <?= $salereturn['issuedby'] ?></p>
            <p class="p-none m-none">Approved by: <?= $salereturn['approver'] ?></p>
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
