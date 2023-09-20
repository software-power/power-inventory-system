<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>LPO No <?= $lpo['lponumber'] ?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="assets/css/custom.css"/>
    <link rel="stylesheet" href="assets/css/paper.css"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style>
        body {
            /*font-size: 13px;*/
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
                margin: 5mm;
            }

            @page :first {
                margin-top: 0;
            }


        }
    </style>
</head>

<body class="A4">
<button id="printBtn" type="button" class="btn btn-primary" onclick="window.print()"
        style="position: absolute;top:0;right: 20mm;background-color: cornflowerblue;z-index: 1000;">
    Print
</button>
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
        <h4 class="text-weight-bold"><u>Local Purchased Order (LPO)</u></h4>
    </div>
    <div class="d-flex justify-content-between">
        <div class="supplier-info">
            <p class="p-none m-none">Supplier Name: <span class="text-weight-bold"><?= $lpo['suppliername'] ?></span>
            </p>
            <? if ($lpo['auto_approve']) { ?>
                <p class="p-none m-none">Approval Status: <span class="text-muted">Auto approved</span></p>
            <? } else {
                if ($lpo['approver']) { ?>
                    <p class="p-none m-none">Approved By: <span
                                class="text-weight-bold"><?= $lpo['approver'] ?>, <?= fDate($lpo['approval_date']) ?></span>
                    </p>
                <? } else { ?>
                    <p class="p-none m-none">Approved By: <span
                                class="text-muted"><small>not approved</small></span></p>
                <? }
            } ?>
        </div>
        <div class="lpo-info">
            <p class="p-none m-none">LPO No: <span class="text-weight-bold"><?= $lpo['lponumber'] ?></span></p>
            <p class="p-none m-none">Date: <span class="text-weight-bold"><?= fDate($lpo['issuedate']) ?></span></p>
            <p class="p-none m-none">Currency: <span
                        class="text-weight-bold"><?= $lpo['currency_name'] ?> - <?= ucfirst($lpo['currency_description']) ?></span>
            </p>
            <p class="p-none m-none">Exchange rate: <span class="text-weight-bold"><?= $lpo['currency_amount'] ?></span>
            </p>
        </div>
    </div>
    <div class="products mt-xlg">
        <table class="table" style="font-size: 10pt">
            <thead>
            <tr>
                <th>S/No.</th>
                <th style="width: 44%;">Product Name</th>
                <th style="text-align: center;">Quantity</th>
                <th style="text-align: center;">Rate (<?= $lpo['currency_name'] ?>)</th>
                <th style="text-align: center;">VAT %</th>
                <th style="text-align: right;">Purchased Cost (<?= $lpo['currency_name'] ?>)</th>
            </tr>
            </thead>
            <tbody>
            <? $count = 1;
            foreach ($lpo['details'] as $index => $d) { ?>
                <tr id="grn<?= $count ?>">
                    <td><?= $count ?></td>
                    <td><?= CS_PRINTING_SHOW_DESCRIPTION? $d['productdescription']:$d['productname'] ?></td>
                    <td style="text-align: center;"><?= $d['qty'] ?></td>
                    <td style="text-align: center;"><?= formatN($d['rate']) ?></td>
                    <td style="text-align: center;"><?= formatN($d['vat_rate']) ?></td>
                    <td style="text-align: right;"><?= formatN($d['incamount']) ?></td>
                </tr>
                <? $count++;
            } ?>
            </tbody>
        </table>
    </div>
    <div class="totals d-flex justify-content-end">
        <table>
            <tbody>
            <tr>
                <td class="text-right text-weight-bold">Exclusive Amount</td>
                <td><span class="text-weight-bold ml-sm"><?= $lpo['currencyname'] ?></span></td>
                <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($lpo['total_amount']) ?></span>
                </td>
            </tr>
            <tr>
                <td class="text-right text-weight-bold">VAT Amount</td>
                <td><span class="text-weight-bold ml-sm"><?= $lpo['currencyname'] ?></span></td>
                <td class="text-right"><span
                            class="text-weight-bold ml-md"><?= formatN($lpo['grand_vatamount']) ?></span></td>
            </tr>
            <tr>
                <td class="text-right text-weight-bold">Inclusive Amount</td>
                <td><span class="text-weight-bold ml-sm"><?= $lpo['currencyname'] ?></span></td>
                <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($lpo['full_amount']) ?></span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-md">
        <div class="grn-info">
            <p class="p-none m-none">Created By: <span class="text-weight-bold"><?= $lpo['issuedby'] ?></span></p>
            <p class="p-none m-none">Location: <span class="text-weight-bold"><?= $lpo['locationname'] ?></span></p>
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
