<header class="page-header">
    <h2>Sales Payment</h2>
</header>
<div class="row d-flex justify-content-center">
    <div class="col-md-11 col-lg-8">
        <section class="panel">
            <header class="panel-heading for-heading">
                <div class="d-flex justify-content-between">
                    <h2 class="panel-title"><i class="fa fa-money"></i> Invoice Receipts </h2>
                    <div class="d-flex">
                        <? if ($sale['payment_status'] != PAYMENT_STATUS_COMPLETE && Users::can(OtherRights::receive_credit_payment)) { ?>
                            <form action="<?= url('payments', 'credit_payment') ?>" style="margin:0;" method="post">
                                <input type="hidden" value="<?= $sale['id'] ?>" name="invoicenum">
                                <input type="hidden" value="<?= $sale['clientid'] ?>" name="selectedclient">
                                <input type="hidden" name="receipt_type"
                                       value="<?= $sale['receipt_method'] == 'sr' ? SalesPayments::RECEIPT_TYPE_SR : SalesPayments::RECEIPT_TYPE_TRA ?>">
                                <input type="hidden" value="<?= base64_encode(url('payments', 'payment_list', ['id' => $sale['id']])) ?>"
                                       name="redirect">
                                <button class="btn btn-default"><i class="fa fa-money"></i> Make Credit Payment</button>
                            </form>
                        <? } ?>
                        <a class="btn btn-cog" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <fieldset class="row-panel">
                    <legend>Sales Info</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <p>Invoice No: <span class="text-primary"><?= $sale['receipt_no'] ?></span></p>
                            <p>Client Name: <span class="text-primary"><?= $sale['clientname'] ?></span></p>
                            <p>Payment Status:
                                <? if ($sale['payment_status'] == PAYMENT_STATUS_COMPLETE) { ?>
                                    <span class="text-success text-uppercase" style="font-weight: bold">Completed</span>
                                <? } elseif ($sale['payment_status'] == PAYMENT_STATUS_PARTIAL) { ?>
                                    <span class="text-warning text-uppercase" style="font-weight: bold">Partial</span>
                                <? } else { ?>
                                    <span class="text-danger text-uppercase" style="font-weight: bold">Pending</span>
                                <? } ?>
                            </p>
                            <p>Currency: <span class="text-primary">
                                <?= $sale['currencyname'] ?> - <?= $sale['currency_description'] ?></span></p>
                            <p>Full Amount: <span class="text-primary"><?= formatN($sale['full_amount']) ?></span></p>
                            <p>Total Paid: <span
                                        class="client_tin text-success"><?= formatN($sale['lastpaid_totalamount']) ?></span>
                            </p>
                            <p>Credit Note: <span
                                        class="client_tin text-rosepink"><?= formatN($sale['total_increturn']) ?></span>
                            </p>
                            <? if ($sale['paymenttype'] == PAYMENT_TYPE_CREDIT && $sale['pending_amount'] > 0) { ?>
                                <p>Pending Amount: <span
                                            class="text-danger"><?= formatN($sale['pending_amount']) ?></span>
                                </p>
                            <? } ?>
                        </div>
                        <div class="col-md-6">
                            <p>Sale ID: <span class="text-primary"><?= $sale['id'] ?></span></p>
                            <p>Payment Type: <span class="text-primary"><?= ucfirst($sale['paymenttype']) ?></span>
                            </p>
                            <p>Issue Date: <span
                                        class="client_address text-primary"><?= fDate($sale['doc'], 'd F Y H:i') ?></span>
                            </p>
                            <p>Sales person: <span
                                        class="client_tin text-primary"><?= $sale['sales_person'] ?></span></p>
                        </div>
                    </div>
                </fieldset>
                <div class="table-responsive mt-xlg">
                    <table class="table table-hover mb-none" id="amcTable" style="font-size:13px">
                        <thead>
                        <tr>
                            <th><i class="fa fa-hashtag"></i> SN</th>
                            <th title="Transaction Number"><i></i> Trans No.</th>
                            <th style="text-align:center">Currency</th>
                            <th style="text-align:center"><i class="fa fa-money"></i> Offset Amount</th>
                            <th style="text-align:center"><i class="fa fa-list"></i> Total Receipt Amount</th>
                            <th style="text-align:center"><i class="fa fa-money"></i> Invoice Amount</th>
                            <th style="text-align:center"><i class="fa fa-credit-card"></i> Method</th>
                            <th style="text-align:center"><i class="fa fa-calendar"></i> Issued on</th>
                            <th style="text-align:center;" title="Transaction Status"><i class="fa fa-check"></i>
                                Trans. Status
                            </th>
                            <th style="text-align:center;" title="Transaction Status"><i class="fa fa-user"></i> Created by
                            </th>
                            <th></th>

                        </tr>
                        </thead>
                        <tbody>
                        <? if (empty($payments)) { ?>
                            <tr>
                                <td colspan="10" align="center" style="color:red">No payment yet..</td>
                            </tr>
                        <? } else { ?>
                            <? foreach ($payments as $id => $R) { ?>
                                <tr>
                                    <td width="80px"><?= $id + 1 ?></td>
                                    <td><?= getTransNo($R['id']) ?></td>
                                    <td style="text-align:center"><?= $R['currencyname'] ?></td>
                                    <td style="text-align:center"><?= formatN($R['offset_amount']) ?></td>
                                    <td style="text-align:center"><?= formatN($R['received_amount']) ?></td>
                                    <td style="text-align:center"><?= formatN($R['amount']) ?></td>
                                    <td style="text-align:center;text-transform:uppercase"><?= $R['received_amount'] > 0 ? $R['method'] : '' ?></td>
                                    <td style="text-align:center"><?= fDate($R['doc']) ?></td>
                                    <td style="text-align:center;text-transform:capitalize"><?= $R['sp_status'] ?></td>
                                    <td style="text-align:center;text-transform:capitalize"><?= $R['creator'] ?></td>
                                    <td>
                                        <? if ($R['received_amount'] > 0) { //if not all from offset?>
                                            <a
                                                    href="<?= url('payments', 'payment_receipt', ['id' => $R['id']]) ?>"
                                                    target="_blank"
                                                    class="btn btn-primary btn-sm" title="Print Payment Receipt">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        <? } ?>
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
</div>
