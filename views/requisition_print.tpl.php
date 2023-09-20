<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Transfer Requisition <?= $requisition['reqid'] ?></title>
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
        <h4 class="text-weight-bold"><u>TRANSFER REQUISITION</u></h4>
    </div>
    <div class="transfer-info">
        <p class="p-none m-none">Requisition No: <span class="text-weight-bold"><?= $requisition['reqid'] ?></span></p>
        <p class="p-none m-none">From: <span class="text-weight-bold"><?= $requisition['fromlocation'] ?></span></p>
        <p class="p-none m-none">To: <span class="text-weight-bold"><?= $requisition['tolocation'] ?></span></p>
        <p class="p-none m-none">Date: <span class="text-weight-bold"><?= fDate($requisition['doc']) ?></span></p>
        <p class="p-none m-none">Issued By: <span class="text-weight-bold"><?= $requisition['issuedby'] ?></span></p>
        <? if ($requisition['auto_approve']) { ?>
            <p class="p-none m-none">Approval Status: <span class="text-muted">Auto approved</span></p>
        <? } else {
            if ($requisition['approver']) { ?>
                <p class="p-none m-none">Approval Status: <span
                            class="text-weight-bold text-muted"><?= $requisition['approver'] ?>, <?= fDate($requisition['approve_date']) ?></span>
                </p>
            <? } else { ?>
                <p class="p-none m-none">Approval Status: <span class="text-muted">Not approved</span></p>
            <? }
        } ?>
    </div>
    <div class="products">
        <table class="table" style="font-size: 9pt">
            <thead>
            <tr>
                <th>SNo.</th>
                <th>Description</th>
                <th>Qty</th>
            </tr>
            </thead>
            <tbody>
            <? $count = 1;
            foreach ($requisition['details'] as $index => $product) { ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?= CS_PRINTING_SHOW_DESCRIPTION ? $product['productdescription'] : $product['productname'] ?></td>
                    <td><?= $product['qty'] ?></td>
                </tr>
                <? $count++;
            } ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between">
        <div class="invoice-info">
            <p class="p-none m-none">Remarks: <?= $requisition['remark'] ?></p>
            <p class="p-none m-none">Print Date: <?= date('d M Y') ?></p>
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
