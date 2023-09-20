<!doctype html>
<html class="fixed sidebar-left-collapsed" lang="en">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>GRN No <?= $grn['grnnumber'] ?></title>
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
        <h4 class="text-weight-bold"><u>GOODS RECEIVE NOTE (GRN)</u></h4>
    </div>
    <div class="d-flex justify-content-between">
        <div class="supplier-info">
            <p class="p-none m-none">Supplier Name: <span class="text-weight-bold"><?= $grn['suppliername'] ?></span>
            </p>
            <p class="p-none m-none">LPO No: <span
                        class="text-weight-bold"><?= $grn['lponumber'] ?: 'n/a' ?></span></p>
            <p class="p-none m-none">
                Approval:
                <? if ($grn['approver']) { ?>
                    <? if ($grn['auto_approve']) { ?>
                        <span class="text-weight-bold">Auto approved</span>
                    <? } else { ?>
                        <span class="text-weight-bold"><?= $grn['approver'] ?>, <?= fDate($grn['approval_date']) ?></span>
                    <? } ?>
                <? } else { ?>
                    <span class="text-muted"><small>not approved</small></span>
                <? } ?>
            </p>
        </div>
        <div class="grn-info">
            <p class="p-none m-none">GRN No: <span class="text-weight-bold"><?= $grn['grnnumber'] ?></span></p>
            <p class="p-none m-none">Date: <span class="text-weight-bold"><?= fDate($grn['issuedate']) ?></span></p>
            <p class="p-none m-none">Payment Method: <span
                        class="text-weight-bold"><?= strtoupper($grn['paymenttype']) ?></span></p>
        </div>
    </div>
    <div class="products mt-xlg">
        <table class="table">
            <thead>
            <tr>
                <th>S/No.</th>
                <th>Barcode</th>
                <th style="width: 45%;">Product Name</th>
                <th style="text-align: center;">VAT %</th>
                <th style="text-align: center;">Current Stock Qty</th>
                <th style="text-align: center;">GRN Qty</th>
            </tr>
            </thead>
            <tbody>
            <? $count = 1;
            foreach ($grn['details'] as $index => $g) { ?>
                <tr id="grn<?= $count ?>">
                    <td><?= $count++ ?></td>
                    <td><?= $g['barcode_office'] ?: $g['barcode_manufacture'] ?></td>
                    <td><?= CS_PRINTING_SHOW_DESCRIPTION ? $g['productdescription'] : $g['productname'] ?></td>
                    <td style="text-align: center;"><?= formatN($g['vat_percentage']) ?></td>
                    <td style="text-align: center;"><?= $g['currentstock'] ?></td>
                    <td style="text-align: center;"><?= $g['qty'] ?></td>
                </tr>
                <? if ($g['track_expire_date']) { ?>
                    <tr>
                        <td colspan="6">
                            <div style="width: 50%;margin-left: 50%;">
                                <div>Batches</div>
                                <table class="table table-bordered table-condensed" style="font-size: 8pt;margin-bottom: 0;">
                                    <thead>
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Qty</th>
                                        <th>Expire Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <? foreach ($g['batches'] as $i => $R) { ?>
                                        <tr>
                                            <td><?= $R['batch_no'] ?></td>
                                            <td><?= $R['qty'] ?></td>
                                            <td><?= fDate($R['expire_date']) ?></td>
                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                <? } ?>
                <tr>
                    <td colspan="6"></td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-md">
        <div class="grn-info">
            <p class="p-none m-none">Print Time: <span class="text-weight-bold"><?= fDate(TIMESTAMP,'d-M-Y H:i') ?></span></p>
            <p class="p-none m-none">Created By: <span class="text-weight-bold"><?= $grn['issuedby'] ?></span></p>
            <p class="p-none m-none">Location: <span class="text-weight-bold"><?= $grn['locationname'] ?> - <?= $grn['branchname'] ?></span>
            </p>
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
