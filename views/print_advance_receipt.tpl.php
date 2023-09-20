<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title><?= $pagetitle ?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/css/custom.css"/>
    <link rel="stylesheet" href="assets/css/paper.css"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style>
        body {
            font-family: Tahoma;
            position: relative;
            font-size: 10pt;
            color: black !important;
        }

        table tr, table td {
            border: 1px solid #000000 !important;
        }

        td span.value {
            font-weight: 600;
        }

        @page {
            size: A5
        }

        @media print {
            #topButtons {
                display: none;
            }
        }
    </style>
</head>
<body class="A5 landscape">
<section class="sheet padding-10mm">
    <div id="topButtons" style="position: absolute;top: 5mm;right: 5mm;">
        <a type="button" href="<?=url('advance_payments','list')?>" class="btn btn-success" >Back</a>
        <button id="printBtn" type="button" class="btn btn-primary" onclick="window.print();">Print</button>
    </div>
    <div style="position: absolute;top: 5mm;left: 5mm;width: 60mm;">
        <img src="<?=CS_LOGO?>" alt="" style="width: 258px;height: 60px; filter: grayscale(1)">
    </div>
    <h4 class="text-center"><strong>ADVANCE RECEIPT</strong></h4>
    <p style="margin-top: 5mm;">Receipt No: <strong><?= $paymentInfo['id'] ?></strong></p>
    <p style="margin: 0">Date: <strong><?= fDate($paymentInfo['doc']) ?></strong></p>
    <div style="margin-top: 5mm;">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td colspan="2">
                    <p style="margin: 1mm">Received with Thanks from M/S: <span
                                class="value"><?= $paymentInfo['clientname'] ?></span></p>
                    <p style="margin: 1mm">Currency: <span
                                class="value"><?= $paymentInfo['currencyname'] ?> - <?= $paymentInfo['currency_description'] ?></span></p>
                    <p style="margin: 1mm">The sum of: <span
                                class="value"><?= formatN($paymentInfo['amount']) ?></span></p>
                    <p style="margin: 1mm">Amount in words: <span class="value" style="text-transform: capitalize"><?=toWords($paymentInfo['amount'])?></span></p>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p style="margin: 1mm">Mode of payment:
                        <span class="value"><?= strtoupper($paymentInfo['methodname']) ?></span>
                        <? if ($paymentInfo['methodname'] == PaymentMethods::BANK) { ?>
                            <span style="margin-left: 3mm;">Bank Name:</span>
                            <span class="value"><?= $paymentInfo['bankname']?:$paymentInfo['bank_name'] ?></span>
                            <span style="margin-left: 3mm;">Bank Reference:</span>
                            <span class="value"><?= $paymentInfo['bankreference']?:$paymentInfo['bank_accno'] ?></span>
                        <? } ?>
                        <? if ($paymentInfo['methodname'] == PaymentMethods::CHEQUE) { ?>
                            <span style="margin-left: 3mm;">Bank:</span>
                            <span class="value"><?= $paymentInfo['bank_name'] ?></span>
                            <span style="margin-left: 3mm;">Cheque No:</span>
                            <span class="value"><?= $paymentInfo['chequename'] ?></span>
                            <span style="margin-left: 3mm;">Cheque Type:</span>
                            <span class="value"><?= $paymentInfo['chequetype'] ?></span>
                        <? } ?>
                        <? if ($paymentInfo['methodname'] == PaymentMethods::CREDIT_CARD) { ?>
                            <span style="margin-left: 3mm;">Reference:</span>
                            <span class="value"><?= $paymentInfo['electronic_account'] ?>, <?= $paymentInfo['credit_cardno'] ?></span>
                        <? } ?>

                    </p>
                    <p style="margin: 1mm">Being payment for: <span class="value"></span></p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end" style="margin-top: 5mm">
        <div style="width: 50%">
            <p style="margin: 0">____________________________________________</p>
            <p class="text-center" style="margin: 0">With Thanks From</p>
            <p class="text-center" style="margin: 0"><?= CS_COMPANY ?></p>
        </div>
    </div>

</section>
<script>
    window.onload = function () {
        // window.print();
    }
</script>
</body>
</html>
