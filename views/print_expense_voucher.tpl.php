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
        }

        table tr, table td {
            border: 1px solid #000000 !important;
        }

        td span.value {
            font-weight: 600;
        }

        .inner-table tr, .inner-table td, .inner-table > thead > tr > th {
            border: none !important;
        }

        .inner-table.table-condensed > tbody > tr > td{
            padding:0;
        }

        @page {
            size: A5
        }

        @media print {
            #printBtn {
                display: none;
            }
        }
    </style>
</head>

<body class="A5 landscape">
<section class="sheet padding-10mm">
    <div style="position: absolute;top: 5mm;right: 5mm;">
        <button id="printBtn" type="button" class="btn btn-primary" onclick="window.print();">Print</button>
    </div>
    <div style="position: absolute;top: 5mm;left: 5mm;width: 258px;">
        <img src="<?=CS_LOGO?>" style="width: 258px;height: 60px; filter: grayscale(1)" alt="">
    </div>
    <h3 class="text-center"><strong>PAYMENT VOUCHER</strong></h3>
    <p style="margin-top: 5mm;">Voucher No: <strong><?= getVoucherNo($expense['id']) ?></strong></p>
    <div style="margin-top: 5mm;">
        <table class="table table-bordered table-condensed" style="font-size: 10pt">
            <tbody>
            <tr>
                <td>
                    <div class="d-flex justify-content-between">
                        <span>Total Amount:</span>
                        <span class="value"><?= formatN($expense['total_amount']) ?></span>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-between">
                        <span>Date:</span>
                        <span class="value"><?= fDate($expense['doc']) ?></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="d-flex justify-content-between">
                        <span>Branch:</span>
                        <span class="value"><?= $expense['branchname'] ?></span>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-between">
                        <span>Paid To:</span>
                        <span class="value"><?= $expense['paidto'] ?></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="d-flex justify-content-center">
                        <div style="width: 80%;">
                            <table class="table inner-table table-condensed">
                                <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th>Expense Type</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?
                                $count = 1;
                                foreach ($expense['details'] as $index => $detail) { ?>
                                    <tr>
                                        <td class="text-center"><?=$count?></td>
                                        <td><?=$detail['attrname']?></td>
                                        <td><?=formatN($detail['amount'])?></td>
                                    </tr>
                                    <?
                                    $count++;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="height: 15mm;">Remark: <p
                            style="white-space: pre;margin-left: 15mm;"><?= $expense['remarks'] ?></p>
                </td>
            </tr>
            <tr>
                <td style="height: 15mm;">Issued By: <span class="value"><?= $expense['username'] ?></span></td>
                <td style="height: 15mm;">Signature:</td>
            </tr>
            </tbody>
        </table>
    </div>

</section>
<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
