<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Order No <?= $order['orderid'] ?></title>
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
            target-new: tab;
            -webkit-print-color-adjust: exact !important;
            color: black;
            font-family: 'Roboto', sans-serif;
        }

        .bg-black {
            background: black !important;
            color: white !important;
        }

        .bottom-border {
            border-bottom: 1px solid #dadada;
        }

        .top-border {
            border-top: 1px solid #dadada;
        }

        .terms-condition li {
            line-height: 15px;
        }

        @page {
            size: A4;
        }

        @media print {
            #printBtn {
                display: none;
            }

            .bg-black span {
                color: white !important;
                color-adjust: exact !important;
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
<div id="printBtn" style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button type="button" class="btn btn-primary" style="background-color: cornflowerblue;" onclick="window.print()">
        Print
    </button>
    <? if (isset($_GET['redirect'])) { ?>
        <a id="printBtn" href="<?= base64_decode($_GET['redirect']) ?>" class="btn btn-primary" style="background-color: forestgreen;"> Back </a>
    <? } ?>
</div>
<section class="sheet padding-5mm no-height">
    <?=component('shared/print_top_header.tpl.php')?>
    <div style="font-size: 11px;">
        <div class="heading d-flex justify-content-center bg-black">
            <span>SALES AGREEMENT / MAKUBALIANO YA MAUZO</span>
        </div>
        <div class="d-flex justify-content-between mt-xs">
            <p>Printed Date: <span class="text-weight-semibold"><?= date('d-m-Y h:i:s:a') ?></span></p>
            <p class="text-weight-semibold">NUMBER: <?= $order['orderid'] ?></p>
        </div>
        <div class="d-flex justify-content-between mt-xs">
            <div style="width: 49%">
                <div class="text-center bottom-border">Sales Information</div>
                <div class="d-flex justify-content-between">
                    <div class="text-weight-semibold" style="width: 50%">Sales Person / Muuzaji</div>
                    <div style="width: 50%"><?= $order['issuedby'] ?></div>
                </div>
                <div class="text-center mt-md bottom-border">Full Address / Anwani kamili</div>
                <div class="d-flex justify-content-between pt-xs">
                    <div class="text-weight-semibold" style="width: 50%">Town / Mkoa</div>
                    <div style="width: 50%"><?= $client['city'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">District / Wilaya</div>
                    <div style="width: 50%"><?= $client['district'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">Street / Mtaa</div>
                    <div style="width: 50%"><?= $client['street'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">Plot No. / Nyumba no.</div>
                    <div style="width: 50%"><?= $client['plotno'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">Address / Anwani</div>
                    <div style="width: 50%"><?= $client['address'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">Email</div>
                    <div style="width: 50%"><?= $client['email'] ?></div>
                </div>
            </div>
            <div style="width: 49%">
                <div class="text-center" style="border-bottom: 1px solid #dadada;">Sales Date</div>
                <div class="d-flex justify-content-between">
                    <div class="text-weight-semibold" style="width: 50%">Date / Tarehe</div>
                    <div style="width: 50%"><?= fDate($order['doc'], 'd F Y') ?></div>
                </div>
                <div class="text-center mt-md" style="border-bottom: 1px solid #dadada;">Company Details / Taarifa za kampuni</div>
                <div class="d-flex justify-content-between pt-xs">
                    <div class="text-weight-semibold" style="width: 50%">Company Name / Jina la Kampuni</div>
                    <div style="width: 50%"><?= $client['name'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">TIN</div>
                    <div style="width: 50%"><?= $client['tinno'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">VRN</div>
                    <div style="width: 50%"><?= $client['vatno'] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">Responsible Person / Jina la mhusika</div>
                    <div style="width: 50%"><?= $client[''] ?></div>
                </div>
                <div class="d-flex justify-content-between pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">Mobile No.</div>
                    <div style="width: 50%"><?= $client['mobile'] ?></div>
                </div>
                <div class="d-flex justify-content-between mt-xs pt-xs top-border">
                    <div class="text-weight-semibold" style="width: 50%">Tel No.</div>
                    <div style="width: 50%"><?= $client['tel'] ?></div>
                </div>
            </div>
        </div>
        <div class="heading d-flex justify-content-center bg-black" style="margin-top: 5mm">
            <span>ORDER DETAILS / VIFAA KUODA</span>
        </div>
        <div class="products">
            <table class="table" style="margin: 0">
                <thead>
                <tr>
                    <th>No.</th>
                    <th style="width: 50%">DETAILS</th>
                    <th class="text-right">QTY</th>
                    <th class="text-right">PRICE</th>
                    <th class="text-right">TOTAL</th>
                </tr>
                </thead>
                <tbody>
                <? $count = 1;
                foreach ($order['details'] as $index => $item) { ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= CS_PRINTING_SHOW_DESCRIPTION ? $item['productdescription'] : $item['productname'] ?></td>
                        <td class="text-right"><?= $item['qty'] ?></td>
                        <td class="text-right"><?= formatN($item['incprice']) ?></td>
                        <td class="text-right"><?= formatN($item['incamount']) ?></td>
                    </tr>
                    <? $count++;
                } ?>
                <tr>
                    <td colspan="3" class="text-right text-weight-bold" style="border: none;">TOTAL</td>
                    <td class="text-weight-bold"><?= $order['currencyname'] ?></td>
                    <td class="text-weight-bold text-right"><?= formatN($order['order_value']) ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex">
            <div id="qrcode" style="width: 55px;height: 55px;"></div>
        </div>
        <div class="terms-condition p-sm" style="border: 1px solid #dadada">
            <p class="m-none mb-xs text-weight-semibold" style="font-size: 13px">Terms & Condition / Masharti</p>
            <div style="font-size: 10px;">
                <?= base64_decode(CS_SALES_AGREEMENT) ?>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-xs p-xs" style="border: 1px solid #dadada;min-height: 90px;">
            <div style="width: 49%">
                <div class="d-flex justify-content-between align-items-end pt-xs">
                    <div style="width: 50%">Proposed payment Method</div>
                    <div style="width: 50%">__________________________</div>
                </div>
                <div class="d-flex justify-content-between pt-xs">
                    <div style="width: 50%">
                        <div>Contact personel (<span class="text-weight-semibold">Fullname </span>):</div>
                        <div class="text-weight-semibold"><?= $contact['name'] ?></div>
                    </div>
                    <div style="width: 50%">
                        <div>Contact personel (<span class="text-weight-semibold">Mobile </span>):</div>
                        <div class="text-weight-semibold"><?= $contact['mobile'] ?></div>
                    </div>
                </div>
            </div>
            <div style="width: 49%">
                <div class="d-flex justify-content-between align-items-end pt-xs">
                    <div style="width: 60%">Advance Installment payment method</div>
                    <div style="width: 40%">__________________________</div>
                </div>
                <div class="d-flex justify-content-between align-items-end pt-xs">
                    <div style="width: 60%">
                        <div>Contact personel <span class="text-weight-semibold">(Signature )</span>:</div>
                    </div>
                    <div style="width: 40%">__________________________</div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-xs">
            <div style="width: 49%">
                <div class="d-flex justify-content-between align-items-end pt-xs">
                    <div style="width: 50%">Full Name</div>
                    <div style="width: 50%">__________________________</div>
                </div>
                <div class="d-flex justify-content-between align-items-end pt-xs">
                    <div style="width: 50%">Signature of PCTL Representetive</div>
                    <div style="width: 50%">__________________________</div>
                </div>
            </div>
            <div style="width: 49%">
                <div class="d-flex justify-content-between align-items-end pt-xs">
                    <div style="width: 60%">Signature of Customer and ruber stamp</div>
                    <div style="width: 40%">__________________________</div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    window.onload = function () {
        var qrcode = new QRCode("qrcode", {
            text: "<?= "Name: " . $order['clientname'] . " TIN: " . $order['clienttinno'] . " ORD: " . $order['orderid'] ?>",
            width: 55,
            height: 55,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        window.print();
    }
</script>
</body>
</html>
