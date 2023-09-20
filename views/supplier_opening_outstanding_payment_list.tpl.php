<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
    .btn-holder {
        float: right;
    }

    .center-panel {
        width: 70%;
        margin: 0 auto;
    }

    .table .actions a:hover {
        color: #ffffff;
    }

    .table .actions a, .table .actions-hover a {
        color: #ffffff;
    }

    .row-color h5, .row-color-danger h5 {
        font-size: 18px;
        padding: 0;
        margin-bottom: 0;
        text-align: center;
        text-transform: uppercase;
    }
</style>
<header class="page-header">
    <h2>Opening Outstanding Supplier Payment</h2>
</header>
<div class="center-panel">
    <section class="panel">
        <header class="panel-heading for-heading">
            <div class="d-flex justify-content-between">
                <h2 class="panel-title"><i class="fa fa-money"></i> Opening Outstanding Supplier Payments </h2>
                <div class="d-flex">
                    <a class="btn btn-cog" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <fieldset class="row-panel">
                <legend>Opening Outstanding Info</legend>
                <div class="row">
                    <div class="col-md-6">
                        <p>GRN No: <span class="text-primary"><?= $opening['grnno'] ?></span></p>
                        <p>Invoice No: <span class="text-primary"><?= $opening['invoiceno'] ?></span></p>
                        <p>Supplier Name: <span class="text-primary"><?= $opening['suppliername'] ?></span></p>
                        <p>Payment Status:
                            <? if ($opening['payment_status'] == PAYMENT_STATUS_COMPLETE) { ?>
                                <span class="text-success text-uppercase" style="font-weight: bold">Completed</span>
                            <? } elseif ($opening['payment_status'] == PAYMENT_STATUS_PARTIAL) { ?>
                                <span class="text-warning text-uppercase" style="font-weight: bold">Partial</span>
                            <? } else { ?>
                                <span class="text-danger text-uppercase" style="font-weight: bold">Pending</span>
                            <? } ?>
                        </p>
                        <p>Full Amount: <span class="text-primary"><?= formatN($opening['full_amount']) ?></span>
                        </p>
                        <p>Total Paid: <span
                                    class="client_tin text-success"><?= formatN($opening['total_paid']) ?></span>
                        </p>
                        <p>Pending Amount: <span
                                    class="text-danger"><?= formatN($opening['outstanding_amount']) ?></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>LPO: <span class="text-primary"><?= $opening['lpoid']?$opening['lpoid']:''?></span>
                        </p>
                        <p>Payment Type: <span class="text-primary"><?= ucfirst($opening['paymenttype']) ?></span>
                        </p>
                        <p>Issue Date: <span
                                    class="client_address text-primary"><?= fDate($opening['doc']) ?></span></p>
                        <p>Created by: <span
                                    class="client_tin text-primary"><?= $opening['creator'] ?></span></p>

                    </div>
                </div>
            </fieldset>
            <div class="table-responsive mt-xlg">
                <table class="table table-hover mb-none" id="amcTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th><i class="fa fa-hashtag"></i> SN</th>
                        <th title="Payment Number"><i></i> Payment No.</th>
                        <th style="text-align:center" title="GRN Number"><i></i> GRN No.</th>
                        <th style="text-align:center" title="Invoice Number"><i></i> Invoice No.</th>
                        <th style="text-align:center"><i class="fa fa-credit-card"></i> Method</th>
                        <th style="text-align:center"><i class="fa fa-money"></i> Amount</th>
                        <th style="text-align:center"><i class="fa fa-calendar"></i> Paid On/Issued date</th>
                        <th style="text-align:center;" title="Transaction Status"><i class="fa fa-user"></i> Created
                            by
                        </th>
                        <th></th>

                    </tr>
                    </thead>
                    <tbody>
                    <? if (empty($payments)) { ?>
                        <tr>
                            <td colspan="9" align="center" style="color:red">No payment yet..</td>
                        </tr>
                    <? } else { ?>
                        <? foreach ($payments as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $id + 1 ?></td>
                                <td><?= $R['id'] ?></td>
                                <td style="text-align:center;text-transform:uppercase"><?= $R['grnno'] ?></td>
                                <td style="text-align:center;text-transform:uppercase"><?= $R['invoiceno'] ?></td>
                                <td style="text-align:center;text-transform:uppercase"><?= $R['method'] ?></td>
                                <td style="text-align:center"><?= formatN($R['amount']) ?></td>
                                <td style="text-align:center"><?= fDate($R['doc']) ?></td>
                                <td style="text-align:center;text-transform:capitalize"><?= $R['creator'] ?></td>
                                <td>
                                    <a href="?module=suppliers&action=payment_slip&id=<?= $R['id'] ?>"
                                       target="_blank"
                                       class="btn btn-primary btn-sm" title="Print Payment Slip">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        <? } ?>
                    <? } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script>

    function validateInputs() {
        let valid = true;
        let amountToPay = Number('<?=$full_amount?>');
        let receivedAmount = Number($('#receivedAmount').val());

        // console.log(amountToPay,receivedAmount);
        if (receivedAmount < amountToPay) {
            valid = false;
            triggerError('Received Amount Not Enough');
            $('#receivedAmount').focus();
        }
        return valid;
    }

    function paymentAmount(e, obj) {
        let amountToPay = Number('<?=$full_amount?>');
        let receivedAmount = Number($(obj).val());

        // console.log(amountToPay,receivedAmount);
        if (receivedAmount > amountToPay) {
            $('#changeAmount').val(numberWithCommas(Number(receivedAmount - amountToPay).toFixed(2)));
            $('#changeHolder').show('slow');
        } else {
            $('#changeHolder').hide('slow');
            $('#changeAmount').val(0);
        }
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
