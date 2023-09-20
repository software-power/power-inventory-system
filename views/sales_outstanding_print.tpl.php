<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Sales Outstanding</title>
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
                margin: 5mm;
                size: landscape;
            }

            @page :first {
                margin-top: 0;
            }


        }
    </style>
</head>

<body class="A4 landscape">
<div style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button id="printBtn" type="button" class="btn btn-primary" style="background-color: cornflowerblue;" onclick="window.print()">
        Print
    </button>
    <? if (isset($_GET['redirect'])) { ?>
        <a id="printBtn" href="<?= base64_decode($_GET['redirect']) ?>" class="btn btn-primary" style="background-color: forestgreen;">
            Back</a>
    <? } ?>
</div>
<section class="sheet padding-5mm no-height">
    <?= component('shared/print_top_header.tpl.php') ?>
    <div class="heading d-flex justify-content-center">
        <h3 class="text-weight-bold"><u>Client Sales Outstanding</u></h3>
    </div>

    <div class="d-flex justify-content-between">
        <div class="customer-info">
            <?if(isset($client)){?>
                <p class="p-none m-none text-weight-bold"><u>Customer Details</u></p>
                <p class="p-none m-none text-weight-bold"><?= $client['name'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $client['mobile'] ?></p>
                <p class="p-none m-none text-weight-bold"><?= $client['email'] ?></p>
                <p class="p-none m-none text-weight-bold">TIN: <?= $client['tinno'] ?></p>
                <p class="p-none m-none text-weight-bold">VRN: <?= $client['vatno'] ?></p>
            <?}?>
            <?if(isset($acc_manager)){?>
                <p class="p-none m-none text-weight-bold"><u>Account manager</u></p>
                <p class="p-none m-none text-weight-bold"><?= ucwords($acc_manager['name']) ?></p>
            <?}?>
        </div>
    </div>
    <div>
        <div style="width: 100mm">
            <h5>Overall Outstanding</h5>
            <table class="table table-bordered table-condensed" style="font-size: 8pt">
                <thead>
                <tr>
                    <th>Currency</th>
                    <th>Amount</th>
                    <th>Base Amount (<?= $baseCurrency['name'] ?>)</th>
                </tr>
                </thead>
                <tbody>
                <? foreach ($total_outstanding['currencies'] as $currency => $R) { ?>
                    <tr>
                        <td><?= $currency ?></td>
                        <td class="text-danger"><?= formatN($R['amount']) ?></td>
                        <td class="text-rosepink"><?= $R['base_currency'] == 'yes' ? '-' : formatN($R['base_amount']) ?></td>
                    </tr>
                <? } ?>
                <tr class="text-weight-bold">
                    <td colspan="2">BASE TOTAL</td>
                    <td class="text-rosepink"><?= formatN($total_outstanding['base_total']) ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <table class="table table-condensed" style="font-size:9pt;">
            <thead>
            <tr>
                <th>#</th>
                <th>Issue Date</th>
                <th>Invoice No.</th>
                <? if (isset($acc_manager)) { ?>
                    <th>Client</th>
                <? } ?>
                <th>Currency</th>
                <th class=" text-right">Full Amt</th>
                <th class=" text-right">Paid Amt</th>
                <th class=" text-right">Credit Notes</th>
                <th class=" text-right">Pending Amt</th>
                <th class=" text-right">(<30 days)</th>
                <th class=" text-right">(30 to 45 days)</th>
                <th class=" text-right">(45 to 90 days)</th>
                <th class=" text-right">(>90 days)</th>
                <th class=" text-right">Final Balance</th>
                <th class=" text-right">Due on</th>
            </tr>
            </thead>
            <tbody>
            <? $count = 1;
            foreach ($invoice_list as $invoice) { ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?= fDate($invoice['invoice_date'], 'd-M-Y') ?></td>
                    <td><?= $invoice['receipt_no'] ?></td>
                    <? if (isset($acc_manager)) { ?>
                        <td><?= $invoice['clientname'] ?></td>
                    <? } ?>
                    <td><?= $invoice['currencyname'] ?></td>
                    <td class="text-right"><?= formatN($invoice['full_amount']) ?></td>
                    <td class="text-right"><?= formatN($invoice['lastpaid_totalamount']) ?></td>
                    <td class="text-right"><?= formatN($invoice['total_increturn']) ?></td>
                    <td class="text-right"><?= formatN($invoice['pending_amount']) ?></td>
                    <td class="text-right"><?= formatN($invoice['(<30 days)']) ?></td>
                    <td class="text-right"><?= formatN($invoice['(30 to 45 days)']) ?></td>
                    <td class="text-right"><?= formatN($invoice['(45 to 90 days)']) ?></td>
                    <td class="text-right"><?= formatN($invoice['(>90 days)']) ?></td>
                    <td class="text-right"><?= formatN($invoice['pending_amount']) . ' Dr' ?></td>
                    <td class="text-right"><?= fDate($invoice['due_date'], 'd-M-Y') ?></td>
                </tr>
                <? if ($with_details) { ?>
                    <tr>
                        <td colspan="<?= isset($acc_manager) ? '15' : '14' ?>">
                            <div class="d-flex justify-content-end">
                                <div style="width: 80%">
                                    <table class="table table-bordered table-condensed" style="font-size: 9pt;">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Qty</th>
                                            <th class="text-right">Vat %</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? $pcount = 1;
                                        foreach ($invoice['details'] as $index => $detail) { ?>
                                            <tr>
                                                <td><?= $pcount ?></td>
                                                <td><?= CS_PRINTING_SHOW_DESCRIPTION ? $detail['productdescription'] : $detail['productname'] ?></td>
                                                <td class="text-right"><?= formatN($detail['price']) ?></td>
                                                <td class="text-right"><?= $detail['quantity'] ?></td>
                                                <td class="text-right"><?= $detail['vat_rate'] ?></td>
                                                <td class="text-right"><?= formatN($detail['incamount']) ?></td>
                                            </tr>
                                            <? $pcount++;
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                <? } ?>
                <? $count++;
            } ?>
            <? foreach ($opening_outstandings as $op) { ?>
                <tr>
                    <td><?= $count ?></td>
                    <td><?= fDate($op['invoicedate'], 'd-M-Y') ?></td>
                    <td><?= $op['invoiceno'] ?></td>
                    <? if (isset($acc_manager)) { ?>
                        <td><?= $op['clientname'] ?></td>
                    <? } ?>
                    <td><?= $op['currencyname'] ?></td>
                    <td class="text-right"><?= formatN($op['outstanding_amount']) ?></td>
                    <td class="text-right"><?= formatN($op['paid_amount']) ?></td>
                    <td class="text-right">-</td>
                    <td class="text-right"><?= formatN($op['pending_amount']) ?></td>
                    <td class="text-right"><?= formatN($op['(<30 days)']) ?></td>
                    <td class="text-right"><?= formatN($op['(30 to 45 days)']) ?></td>
                    <td class="text-right"><?= formatN($op['(45 to 90 days)']) ?></td>
                    <td class="text-right"><?= formatN($op['(>90 days)']) ?></td>
                    <td class="text-right"><?= formatN($op['pending_amount']) . ' Dr' ?></td>
                    <td class="text-right"><?= fDate($op['duedate'], 'd-M-Y') ?></td>
                </tr>
                <? $count++;
            } ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between mt-xlg">
        <div class="invoice-info">
            <p class="p-none m-none">Printed by: <?= $_SESSION['member']['name'] ?></p>
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
</section>
<script>
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
