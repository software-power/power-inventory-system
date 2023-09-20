<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- <link href="https://fonts.googleapis.com/css?family=Alegreya+Sans&display=swap" rel="stylesheet"> -->

    <script type="text/javascript" src="assets/qr/jquery.min.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>

    <script src="assets/print-js/dist/print.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/print-js/dist/print.css">
    <link href="assets/fonts/AgenyFB/stylesheet.css" rel="stylesheet" type="text/css"/>
</head>
<style>


    #print_form_container {
        /*margin: 0;*/
        font-family: 'Agency FB', arial;
        /* font-family:Roboto,Arial,"Droid Sans",sans-serif; */
        /* "Helvetica Neue", */
        /* font-family:serif; */
        font-size: 15px;
    }

    table {
        font-size: 15px;
        color: black;
    }

    .receipt-container {
        width: 219px;
        margin: 0 auto;
        background: #ffffff;
        color: black;
    }

    .logobar img {
        width: 120px;
    }

    .logobar {
        width: 120px;
        margin: 0 auto;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .details {
        /* margin-top:20px; */
    }

    .company-details ul li, h4, h3 {
        text-align: center;
        text-transform: uppercase;
    }

    #qrcode, #VFDcode {
        width: 100%;
        margin: 0 auto;
    }

    #VFDcode {
        border: 2px solid #fff;
    }

    #qrcode img, #VFDcode img {
        margin: 0 auto;
    }

    #VFDQRcode img {
        margin-top: 10px;
        border: 1px solid #333;
        padding: 5;
        box-sizing: border-box;
        width: 100px;
        height: auto;
        margin: 0 auto;
        /* padding: 10px; */
        position: relative;
    }

    .normal-header {
        /* float: left; */
        display: block;
        width: 102px;
    }

    .normal {
        text-transform: uppercase;
    }

    .dashed-line {
        border-bottom: 1px dashed
    }

    .receipt_tbl {
        width: 100%;
    }

    .receipt_tbl th {
        /* width: 80%; */
        text-align: left;
    }

    .product_tbl, .total_tbl {
        width: 100%;
        /* margin: 0 auto; */
    }

    .product_tbl, .total_tbl th {
        text-align: left;
    }

    .product_tbl td, .total_tbl td, .total_tbl th {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;
        border-top: 1px dashed #ddd;
    }

    .nomargin {
        padding: 0;
        margin: 0;
        font-size: 15px;
    }

    .receipt-code {
        padding-bottom: 20px;
    }

    .qrCodeHolder {
        padding-bottom: 20px;
    }

    .customer-details li {
        margin-top: -4px;
    }

    .company-details ul li strong {
        margin: 0px;
        padding: 0px;
    }

    .company-details ul li {
        text-align: center;
        font-size: 15px;
        padding: 0px !important;
        line-height: 15px;
        font-size: 15px;
    }

    .company-details h4 {
        margin: 0px;
        font-style: bold;
        text-decoration: underline;
    }

    #print_form_container {
        width: 219px;
        padding: 0;
    }

    .header-bar h3, .footer-bar h3 {
        font-size: 15px;
    }

    .ms-print-btn {
        right: 38%;
    }

    th {
        font-weight: normal;
    }
</style>
<div class="ms-print-btn">
    <ul class="nav">
        <li class="nav-item">
            <button title="Print expenses form" onclick="printForm(form_holder)"
                    class="btn btn-primary ms-btn ms-float-right" type="button" name="button"><i
                        class="fa fa-print"></i></button>
        </li>

        <li class="nav-item item-top">
            <a title="Back to Sales"
               href="<?= isset($_GET['redirect']) ? base64_decode($_GET['redirect']) : url('home', 'index') ?>"
               class="btn btn-success btn-sm ms-btn ms-float-right"><i class="fa fa-arrow-left"></i></a>
        </li>

    </ul>
</div>
<div id="print_form_container" class="receipt-container">
    <div id="form_holder">
        <div class="header-bar">
            <div class="logobar">
                <center>
                    <img src="assets/images/tra_logo.png" style="width:50px;" alt="tanzania revenue authority logo">
                </center>
            </div>
            <h3>**** START OF VFD RECEIPT ****</h3>
        </div>
        <div class="details company-details">
            <h4><strong><?= CS_COMPANY ?></strong></h4>
            <ul>
                <li><?= CS_ADDRESS ?></li>
                <li>MOBILE: <?= CS_MOBILE ?></li>
                <li style="font-size: 15px;">TIN: <?= CS_TIN ?></li>
                <li>VRN:<?= CS_VRN ?></li>
                <li>SERIAL NO: <?= $settings['vfd_serial'] ?></li>
                <li class="uin">UIN:<?= $settings['vfd_UIN'] ?></li>
                <li>TAX OFFICE: <?= CS_TAX_OFFICE ?></li>
            </ul>
        </div>
        <div class="details dashed-line customer-details">
            <ul>
                <li>
                    <span class=" ">CUSTOMER NAME:</span>
                    <span class="normal" align="right"><?= $sale['clientname'] ?></span>
                </li>
                <li>
                    <span class=" ">ID TYPE:</span>
                    <span class="normal" align="right">TIN</span>
                </li>
                <li>
                    <span class=" ">CUSTOMER ID:</span>
                    <span class="normal"><?= $sale['clientinno'] ?></span>
                </li>
                <li>
                    <span class=" ">CUSTOMER VRN:</span>
                    <span class="normal"><?= $sale['clientvrn'] ?></span>
                </li>
                <li>
                    <span class=" ">CUSTOMER MOBILE:</span>
                    <span class="normal">+<?= $sale['mobile'] ?></span>
                </li>
            </ul>
        </div>
        <div class="details dashed-line receipt-details">
            <table class="receipt_tbl">
                <tr>
                    <th>INVOICE NO:</th>
                    <td align="right"><?= $sale['receipt_no'] ?></td>
                </tr>
                <tr>
                    <th>VFD NUMBER:</th>
                    <td align="right"><?= $sale['receipt_num'] ?></td>
                </tr>
                <tr>
                    <th>Z NUMBER:</th>
                    <td align="right"><?= $sale['znumber'] ?></td>
                </tr>
                <tr>
                    <th>RECEIPT DATE:</th>
                    <td align="right"><?= TODAY ?></td>
                </tr>
                <tr>
                    <th>RECEIPT TIME:</th>
                    <td align="right"><?= NOW ?></td>
                </tr>
            </table>
        </div>
        <div class="details dashed-line product-details">
            <table class="product_tbl">
                <? foreach ($sale['items'] as $key => $details) {
                    $text = CS_PRINTING_SHOW_DESCRIPTION ? ($details['print_extra'] ? $details['extra_description'] : $details['productdescription']) : $details['productname']; ?>
                    <tr class="dashed-line">
                        <td title="<?= $text ?>"><?= strlen($text) > 30 ? substr($text, 0, 30) : $text ?></td>
                        <td align="right"><?= $details['quantity'] ?></td>
                        <td align="right"><?= formatN($details['base_amount'] + $details['base_vat_amount']) ?></td>
                    </tr>

                <? } ?>

            </table>
            <table class="total_tbl">
                <tr>
                    <th>TOTAL EXCLUSIVE:</th>
                    <td align="right"><?= formatN($sale['base_grand_amount']) ?></td>
                </tr>
                <tr>
                    <th>TOTAL TAX:</th>
                    <td align="right"><?= formatN($sale['base_grand_vatamount']) ?></td>
                </tr>
                <tr>
                    <th>TOTAL INCLUSIVE:</th>
                    <td align="right"><?= formatN($sale['base_full_amount']) ?></td>
                </tr>
                <? if (CS_SHOW_CHANGE && $sale['change_amount'] > 0) { ?>
                    <tr>
                        <th>AMOUNT RECEIVED:</th>
                        <td align="right"><?= formatN($sale['handed_amount']) ?></td>
                    </tr>
                    <tr>
                        <th>CHANGE:</th>
                        <td align="right"><?= formatN($sale['change_amount']) ?></td>
                    </tr>
                <? } ?>
            </table>
        </div>
        <div class="">
            <h4 class="nomargin">RECEIPT VERIFICATION CODE<br><br> <span><?= $sale['rctvcode'] ?></span></h4>
        </div>
        <div class="details receipt_qr">
            <div class="qrCodeHolder">
                <div id="VFDcode">
                    <center>
                <span id="VFDQRcode">
                </span>
                    </center>
                </div>
            </div>
        </div>
        <div class="footer-bar">
            <h3>**** END OF VFD RECEIPT ****</h3>
            <div align="center">Powered By Powercomputers LTD</div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function () {
        printForm('#form_holder');

        $('body').bind('copy cut paste', function (e) {
            e.preventDefault();
        });

        $('body').on('contextmenu', function (e) {
            return false;
        });
    });

    new QRCode(document.getElementById("VFDQRcode"), "<?=$sale['receipt_v_num']?>");

    function printForm(areaToPrint) {
        var id = $(areaToPrint).attr('id');
        printJS({
            printable: id,
            type: 'html',
            showModal: true,
            targetStyles: ['*']
        });
    }
</script>

</html>
