<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700" rel="stylesheet">
<style>
    body {
        target-new: tab;
        -webkit-print-color-adjust: exact !important;
        margin: 0;
        font-family: 'Roboto', sans-serif;
    }

    @page {
        size: auto;
        margin: 0mm;
    }

    .company-holder {
        width: 93%;
        margin: 0 auto;
    }

    .company-holder .company-details {
        width: 80%;
        float: left;
        text-align: right;
    }

    .company-info ul li {
        /*padding: 5px;*/
        display: inline-block;
        margin-left: 23px;
    }

    .company-holder .company-details ul {
        list-style: none;
    }

    .company-holder .logo-bar {
        width: 20%;
        float: left;
    }

    .company-holder .logo-bar .logo {
        margin-top: 19px;
    }

    .company-holder .logo-bar .logo img {
        display: block;
        width: 280px;
        height: 51px;
    }

    .header {
        width: 100%;
        text-align: center;
    }

    .header h2 {
        float: left;
        width: 100%;
        font-weight: 300;
        background: black;
        color: white;
    }

    .GRN-header {
        margin: 0 auto;
        width: 93%;
    }

    .GRN-details {
        width: 80%;
        margin: 0 auto;
    }

    .GRN-header .supplier-details {
        width: 50%;
        float: left;
        /* height: 190px; */
    }

    .GRN-header .supplier-details ul {
        list-style: none;
        padding: 0;
    }

    .GRN-header .supplier-details ul li {
        border-bottom: 1px solid #dee2e6;
        padding: 5px;
    }

    .GRN-details table {
        width: 100%;
        border-collapse: collapse;
    }

    .GRN-details table thead th {
        vertical-align: middle;
        border-top: 2px solid black;
        border-bottom: 2px solid black;
    }

    .GRN-details table tr {
        height: 32px;
        border-top: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
    }

    .GRN-details table th {
        text-align: left
    }

    .GRN-details .totalAmount {
        width: 38%;
        float: right;
    }

    .GRN-details .remarkArea {
        width: 40%;
        margin-top: 10%;
    }
</style>
<div class="row">
    <div class="col-md-8">
        <div class="company-holder col-md-12">
            <div class="logo-bar">
                <div class="logo"><img src="<?= CS_LOGO ?>" alt="logo"/></div>
            </div>
            <div class="company-details">
                <h3><?= $company['name'] ?></h3>
                <div class="company-info">
                    <ul>
                        <li>TIN: <?= $company['tin'] ?><br></li>
                        <li><?= $company['address'] ?></li>
                        <li><?= $company['mobile'] ?> | <?= $company['tel'] ?></li>
                        <li><?= $company['email'] ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="header col-md-12"><h2>Goods Received Note (GRN)</h2></div>
        <div class="GRN-header col-md-12">
            <div class="supplier-details col-md-6">
                <ul style="text-align: left">
                    <li>Supplier Name: <span style="text-transform: uppercase;"><?= $grn[0]['suppliername'] ?></span>
                    </li>
                    <li>GRN Verification Code: <?= $grn[0]['verificationcode'] ?></li>
                    <li>Supplier Invoice No.: <?= $grn[0]['invoiceno'] ?></li>
                    <li>Payment: CREDIT DAYS: 0</li>
                    <li>LPO. No.: <?= $grn[0]['lpoid'] ?></li>
                </ul>
            </div>
            <div class="supplier-details col-md-6">
                <ul style="text-align: right">
                    <li>Date: <?= fDate($grn[0]['issuedate']) ?></li>
                    <li>GRN. No. : <?= $grn[0]['grnnumber'] ?></li>
                    <!-- <li>Location: Stores</li> -->
                    <li>Currency: (<strong><?= $grn[0]['currency_name'] ?></strong>) <span
                                style="text-transform:capitalize"><?= $grn[0]['currency_description'] ?></span></li>
                </ul>
            </div>
        </div>
        <div class="GRN-details col-md-12">
            <table>
                <thead>
                <tr>
                    <th>S/No.</th>
                    <th>Product Name</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: center;">Rate (<?= $grn[0]['currency_name'] ?>)</th>
                    <th style="text-align: right;">Purchased Cost (<?= $grn[0]['currency_name'] ?>)</th>
                </tr>
                </thead>
                <tbody>
                <? $count = 1;
                foreach ($grn[0]['stock'] as $index => $g) {
                    ?>
                    <tr id="grn<?= $count ?>">
                        <td><?= $count ?></td>
                        <td><?= $g['productname'] ?></td>
                        <td style="text-align: center;"><?= $g['qty'] ?></td>
                        <td style="text-align: center;"><?= formatN($g['rate']) ?></td>
                        <td style="text-align: right;"><?= formatN($g['qty'] * $g['rate']) ?></td>
                    </tr>
                    <? if ($g['track_expire_date']) { ?>
                        <tr>
                            <td colspan="5">
                                <table style="width:60%;margin-left:40%;">
                                    <thead>
                                    <tr style="font-weight: bold;">
                                        <td>Batch No</td>
                                        <td style="text-align: center;">Qty</td>
                                        <td style="text-align: right;">Expire Date</td>
                                    </tr>
                                    </thead>
                                    <tbody style="font-size:10pt;">
                                    <? foreach ($g['batches'] as $i => $R) { ?>
                                        <tr>
                                            <td><?= $R['batch_no'] ?></td>
                                            <td style="text-align: center;"><?= $R['batchqty'] ?></td>
                                            <td style="text-align: right;"><?= fDate($R['expire_date']) ?></td>
                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <? } ?>
                    <tr>
                        <td colspan="5"></td>
                    </tr>
                    <? $count++;
                } ?>
                </tbody>
            </table>
            <div class="totalAmount">
                <table>
                    <tr>
                        <td>Exclusive Amount</td>
                        <td style="text-align: right;"><strong><?= formatN($grn[0]['total_amount']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>VAT Amount</td>
                        <td style="text-align: right;"><strong><?= formatN($grn[0]['grand_vatamount']) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Inclusive Amount</td>
                        <td style="text-align: right;"><strong><?= formatN($grn[0]['full_amount']) ?></strong></td>
                    </tr>
                </table>
            </div>
            <div class="remarkArea">
                <table>
                    <tr>
                        <td><strong>Created By</strong> :</td>
                        <td style="text-align: right;"><?= $grn[0]['issuedby'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Store Location</strong> :</td>
                        <td style="text-align: right; text-transform: capitalize;"><?= $grn[0]['stock_location'] ?></td>
                    </tr>
                    <tr>
                        <td><strong>Received By</strong> :</td>
                        <td style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td><strong>Signature</strong> :</td>
                        <td style="text-align: right;"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    //window.print();
</script>
