<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Ledger Account</title>
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
    <div class="company-info d-flex justify-content-between" style="border-bottom: 2px solid grey;min-height: 25mm">
        <? if (CS_SHOW_PRINT_HEADER) { ?>
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
        <? } ?>
    </div>
    <div class="heading d-flex justify-content-center">
        <h3 class="text-weight-bold">Ledger Account</h3>
    </div>
    <div class="d-flex justify-content-between">
        <div>
            <p class="p-none m-none">Client: <span class="text-weight-bold"><?= $client['name'] ?></span></p>
            <p class="p-none m-none">TIN: <span class="text-weight-bold"><?= $client['tinno'] ?></span></p>
            <p class="p-none m-none">Date: <span class="text-weight-bold"><?= fDate($fromdate, 'd M Y') ?> to <?= fDate($todate, 'd M Y') ?></span>
            </p>
        </div>
    </div>
    <div class="products">
        <table id="ledger-table" class="table mb-none" style="font-size:10pt;">
            <thead>
            <tr>
                <th>DATE</th>
                <th>VOUCHER TYPE</th>
                <th>VOUCHER NUMBER</th>
                <th>DEBIT</th>
                <th>CREDIT</th>
            </tr>
            </thead>
            <tbody>
            <tr class="text-weight-bold">
                <td colspan="3" class="text-right">OPENING BALANCE <span><?= $currency['name'] ?></span></td>
                <td><?= $opening_balance['balance'] > 0 ? formatN($opening_balance['balance']) : '' ?></td>
                <td><?= $opening_balance['balance'] < 0 ? formatN(abs($opening_balance['balance'])) : '' ?></td>
            </tr>
            <?
            foreach ($ledgers as $index => $l) { ?>
                <tr>
                    <td><?= fDate($l['action_date'], 'd M Y H:i') ?></td>
                    <td class="text-capitalize"><?= $l['voucher_type'] ?></td>
                    <td><?= $l['voucherno'] ?></td>
                    <td><?= $l['side'] == 'debit' ? formatN($l['amount']) : '' ?></td>
                    <td><?= $l['side'] == 'credit' ? formatN($l['amount']) : '' ?></td>
                </tr>
            <? } ?>
            <tr class="text-weight-bold">
                <td colspan="3" class="text-right">TOTAL <?= $currency['name'] ?>:</td>
                <td><?= formatN($total['debit']) ?></td>
                <td><?= formatN($total['credit']) ?></td>
            </tr>
            <tr class="text-weight-bold">
                <td colspan="3" class="text-right">Closing Balance <?= $currency['name'] ?>:</td>
                <td><?= formatN($total['closing_debit']) ?></td>
                <td><?= formatN($total['closing_credit']) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <p>Printed by: <span class="text-weight-bold"><?= $_SESSION['member']['name'] ?></span></p>
    </div>
</section>
<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
