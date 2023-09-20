<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>ZRA receipt <?= $sale['receipt_no'] ?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="assets/css/custom.css"/>
    <link rel="stylesheet" href="assets/css/paper.css"/>
    <link href="assets/fonts/Helvetica/stylesheet.css" rel="stylesheet" type="text/css"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style>
        body {
            /*font-family: 'Arial';*/
            font-family: 'Helvetica', Arial;
            position: relative;
            font-size: 9pt;
            color: black !important;
            -webkit-print-color-adjust: exact !important;
        }

        .condense {
            line-height: 13px;
        }

        .dashed-line {
            line-height: 0;
        }

        .dashed-line::before {
            content: "_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _";
        }

        .table > tbody > tr > td{
            padding: 4px;
        }

        @page {
            size: 80mm;
        }

        @media print {
            #printBtn {
                display: none;
            }

            @page {
                /*margin for each printed piece of paper*/
                margin: 0;
            }

            @page :first {
                margin-top: 0;
            }
        }
    </style>
</head>

<body class="small80">
<div style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button id="printBtn" type="button" class="btn btn-primary" style="background-color: cornflowerblue;"
            onclick="window.print()">
        Print
    </button>
    <a id="printBtn" href="<?= $_GET['redirect'] ? base64_decode($_GET['redirect']) : '?' ?>" class="btn btn-primary"
       style="background-color: forestgreen;">
        Back
    </a>
</div>
<section class="sheet padding-2mm no-height">
    <div class="d-flex flex-column justify-content-center align-items-center">
        <div>**** START OF LEGAL RECEIPT ****</div>
        <div class="logo">
            <img src="images/zrb_logo.jpg" style="width: 12mm;height: 11mm;" alt="ZRB logo">
        </div>
    </div>
    <div class="details d-flex justify-content-center condense">
        <table>
            <tbody>
            <tr>
                <td class="text-right text-weight-bold ">ZNUMBER:</td>
                <td><span class="ml-sm"><?= $sale['znumber'] ?></span></td>
            </tr>
            <tr>
                <td class="text-right text-weight-bold ">NAME:</td>
                <td><span class="ml-sm"><?= $sale['companyname'] ?></span></td>
            </tr>
            <tr>
                <td class="text-right text-weight-bold ">VRN:</td>
                <td><span class="ml-sm"><?= $sale['vrnno'] ?></span></td>
            </tr>
            <tr>
                <td class="text-right text-weight-bold ">STREET:</td>
                <td><span class="ml-sm"><?= $sale['street'] ?></span></td>
            </tr>
            <tr>
                <td class="text-right text-weight-bold ">TIN:</td>
                <td><span class="ml-sm"><?= $sale['tinnumber'] ?></span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="customer-details mt-sm text-weight-bold condense" style="margin: 0 3mm;">
        <table>
            <tbody>
            <tr valign="baseline">
                <td class="text-weight-bold ">TAX INVOICE NO</td>
                <td class="pl-sm"><span class="mr-sm">:</span><?= $sale['rctvcode'] ?></td>
            </tr>
            <tr valign="baseline">
                <td class="text-weight-bold ">INVOICE NO</td>
                <td class="pl-sm"><span class="mr-sm">:</span><?= $sale['receipt_no'] ?></td>
            </tr>
            <tr valign="baseline">
                <td class="text-weight-bold ">CUSTOMER NAME</td>
                <td class="pl-sm"><span class="mr-sm">:</span><?= $sale['clientname'] ?></td>
            </tr>
            <tr valign="baseline">
                <td class="text-weight-bold ">CURRENCY</td>
                <td class="pl-sm"><span class="mr-sm">:</span><?= $sale['currencyname'] ?></td>
            </tr>
            <tr valign="baseline">
                <td class="text-weight-bold ">DATE & TIME</td>
                <td class="pl-sm"><span class="mr-sm">:</span><?= $sale['doc'] ?></td>
            </tr>
            <tr valign="baseline">
                <td class="text-weight-bold ">CUSTOMER MOBILE</td>
                <td class="pl-sm"><span class="mr-sm">:</span><?= $sale['mobile'] ?></td>
            </tr>
            </tbody>
        </table>
        <div class="d-block dashed-line"></div>
    </div>
    <div class="product-details mt-sm">
        <table class="table p-none m-none">
            <tr>
                <td>Item</td>
                <td>Qty</td>
                <td align="right">Price</td>
            </tr>
            <? foreach ($sale['items'] as $key => $details) {
                $text = CS_PRINTING_SHOW_DESCRIPTION ? ($details['print_extra'] ? $details['extra_description'] : $details['productdescription']) : $details['productname']; ?>
                <tr>
                    <td><?= $text ?></td>
                    <td><?= $details['quantity'] ?></td>
                    <td align="right"><?= formatN(addTax($details['price'], $details['vat_rate'])) ?></td>
                </tr>
            <? } ?>
        </table>
    </div>
    <div class="totals">
        <div class="d-block dashed-line"></div>
        <div class="d-flex justify-content-between pt-sm">
            <span>TOTAL DISCOUNT:</span>
            <span><?= formatN(0) ?></span>
        </div>
        <div class="d-block dashed-line"></div>
        <div class="d-flex justify-content-between pt-sm">
            <span>TAX AMOUNT:</span>
            <span><?= formatN($sale['grand_vatamount']) ?></span>
        </div>
        <div class="d-block dashed-line"></div>
        <div class="d-flex justify-content-between pt-sm">
            <span>TAX EXCLUSIVE:</span>
            <span><?= formatN($sale['grand_amount']) ?></span>
        </div>
        <div class="d-block dashed-line"></div>
        <div class="d-flex justify-content-between pt-sm">
            <span>TOTAL AMOUNT:</span>
            <span><?= formatN($sale['full_amount']) ?></span>
        </div>
        <div class="d-block dashed-line"></div>
        <div class="d-flex justify-content-between pt-sm">
            <span>CASH:</span>
            <span><?= formatN($sale['full_amount']) ?></span>
        </div>
    </div>
    <div class="d-flex flex-column align-items-center">
        <div class="p-none m-none">RECEIPT VERIFICATION CODE</div>
        <div class="text-weight-bold" style="font-size: 16pt"><?= $sale['rctvcode'] ?></div>
        <img id="qrcode-holder" class="mt-sm" height="100"/>
    </div>
    <div class="footer-bar">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <p>**** END OF LEGAL RECEIPT ****</p>
            <!--            <p>Powered By Powercomputers LTD</p>-->
        </div>
    </div>
</section>

<script src="assets/barcode/bwip-js.js"></script>
<script>
    $(function () {
        try {
            let canvas = document.createElement('canvas');
            bwipjs.toCanvas(canvas, {
                bcid: "qrcode",       // Barcode type
                text: `<?=$sale['receipt_v_num']?>`,    // Text to encode
                scale: 3,               // 3x scaling factor
                // height: 15,              // Bar height, in millimeters
            });
            $('#qrcode-holder').attr('src', canvas.toDataURL('image/png'));
        } catch (e) {
            console.log(e);
        }
    });
    window.onload = function () {
        window.print();
    }
</script>
</body>
</html>
