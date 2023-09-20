<style>
    #items-holder {
        max-height: 80vh;
        overflow-x: hidden;
        overflow-y: auto;
    }
</style>
<div class="row d-flex justify-content-center">
    <div class="col-xs-12 col-xl-10">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">Credit note No: <span class="text-primary"><?= getCreditNoteNo($salereturn['id']) ?></span></h2>
                <div class="d-flex align-items-center">
                </div>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>Client Name: <span class="text-primary"><?= $salereturn['clientname'] ?></span></p>
                        <p>Invoice no: <span class="text-primary"><?= $salereturn['invoiceno'] ?></span></p>
                        <p>Invoice type: <span class="text-primary text-capitalize"><?= $salereturn['invoicetype'] ?> Invoice</span></p>
                        <p>Currency: <span class="text-primary"><?= $salereturn['currencyname'] ?> - <?= $salereturn['currency_description'] ?></span>
                        </p>
                        <p>Return Type:
                            <span class="text-rosepink text-capitalize">
                                <? if ($salereturn['type'] == SalesReturns::TYPE_PRICE) { ?>
                                    Price Correction
                                <? } elseif ($salereturn['type'] == SalesReturns::TYPE_FULL) { ?>
                                    Full invoice
                                <? } else { ?>
                                    Items return
                                <? } ?>
                            </span>
                        </p>
                        <p>Issued by: <span class="text-primary"><?= $salereturn['issuedby'] ?></span></p>
                        <p>Issue Date: <span class="text-primary"><?= fDate($salereturn['doc'], 'd M Y H:i') ?></span></p>
                        <p>Total Amount: <span class="text-primary"><?= formatN($salereturn['total_incamount']) ?></span></p>
                        <p>Status:
                            <? if ($salereturn['return_status'] == 'approved') { ?>
                                <span class="text-primary">Approved by <?= $salereturn['approver'] ?>, <?= fDate($salereturn['approval_date'], 'd M Y H:i') ?></span>
                            <? } elseif ($salereturn['return_status'] == 'canceled') { ?>
                                <span class="text-rosepink">Canceled</span>
                            <? } else { ?>
                                <span class="text-muted">not approved</span>
                            <? } ?>
                        </p>
                        <p class="">Return Amount:
                            <span class="text-rosepink">
                                <?if($salereturn['apid']){?>
                                    <span class="mr-lg"><?=formatN($salereturn['advance_amount'])?></span>
                                    <a href="<?=url('advance_payments', 'list',['apid'=>$salereturn['apid']])?>">
                                        Advance receipt no <?=$salereturn['apid']?></a>
                                <?}elseif ($salereturn['return_method']){?>
                                    <?= formatN($salereturn['return_amount']) ?>
                                    <? if ($salereturn['return_method'] == PaymentMethods::CASH) { ?>Cash<? } ?>
                                    <? if ($salereturn['return_method'] == PaymentMethods::BANK) { ?>
                                        <?= "Bank Name: {$salereturn['bankname']}, Bank Reference: {$salereturn['bankreference']}" ?>
                                    <? } ?>
                                    <? if ($salereturn['return_method'] == PaymentMethods::CHEQUE) { ?>
                                        <?= "Cheque No: {$salereturn['chequename']}, Cheque Type: {$salereturn['chequetype']}" ?>
                                    <? } ?>
                                    <? if ($salereturn['return_method'] == PaymentMethods::CREDIT_CARD) { ?>
                                        <?= "Reference No: {$salereturn['credit_cardno']}" ?>
                                    <? } ?>
                                <?}?>
                            </span>
                        </p>
                        <p>Description: <?= $salereturn['description'] ?></p>
                        <div class="row">
                            <div class="col-xs-8">
                                <textarea readonly rows="3" class="form-control input-sm"><?= $salereturn['description'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <? if ($salereturn['return_status'] == 'not_approved' && (Users::can(OtherRights::approve_other_credit_note)||Users::can(OtherRights::approve_credit_note))) { ?>
                            <? if ($salereturn['require_client_payment_return']) { ?>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#payment-return-modal">
                                    <i class="fa fa-check"></i> Approve
                                </button>
                            <? } else { ?>
                                <form action="<?= url('sales_returns', 'approve_credit_note') ?>" method="POST" class="p-none m-none"
                                      onsubmit="return confirm('Do want to approve this credit note?')">
                                    <input type="hidden" name="returnid" value="<?= $salereturn['id'] ?>">
                                    <button class="btn btn-success btn-sm"><i class="fa fa-check"></i> Approve</button>
                                </form>
                            <? } ?>
                            <? if ($salereturn['type'] != SalesReturns::TYPE_FULL) { ?>
                                <form action="<?= url('sales_returns', 'issue_credit_note') ?>" method="POST" class="p-none m-none">
                                    <input type="hidden" name="salesid" value="<?= $salereturn['salesid'] ?>">
                                    <input type="hidden" name="returnid" value="<?= $salereturn['id'] ?>">
                                    <button class="btn btn-primary btn-sm ml-sm"><i class="fa fa-pencil"></i> Edit</button>
                                </form>
                            <? } ?>
                            <form action="<?= url('sales_returns', 'cancel_credit_note') ?>" method="POST" class="p-none m-none"
                                  onsubmit="return confirm('Do want to cancel this credit note?')">
                                <input type="hidden" name="returnid" value="<?= $salereturn['id'] ?>">
                                <button class="btn btn-danger btn-sm ml-sm"><i class="fa fa-close"></i> Cancel</button>
                            </form>
                        <? } ?>
                        <? if ($salereturn['return_status'] == 'approved') { ?>
                            <a class="btn btn-primary btn-sm" target="_blank" title="Print credit note"
                               href="?module=sales_returns&action=print_credit_note&returnno=<?= $salereturn['id'] ?>">
                                <i class="fa fa-print"></i> Print</a>
                        <? } ?>
                    </div>
                </div>
                <div id="items-holder">
                    <div class="table-responsive  mt-md">
                        <table class="table table-hover table-bordered" style="font-size:10pt;">
                            <thead class="thead">
                            <tr>
                                <th class="stick">#</th>
                                <th class="stick">Product Name</th>
                                <th class="stick">Rate</th>
                                <th class="stick">Quantity</th>
                                <th class="stick">VAT%</th>
                                <th class="stick">Total Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? $count = 1;
                            foreach ($salereturn['details'] as $index => $R) { ?>
                                <tr>
                                    <td><?= $count ?></td>
                                    <td><?= $R['productname'] ?></td>
                                    <td><?= formatN($R['rate']) ?></td>
                                    <td><?= $R['qty'] ?></td>
                                    <td><?= $R['vat_rate'] ?></td>
                                    <td><?= formatN($R['incamount']) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <span>Remarks</span>
                                                <textarea rows="2" class="form-control" readonly><?= $R['remarks'] ?></textarea>
                                            </div>
                                            <? if ($salereturn['type'] != SalesReturns::TYPE_PRICE) { ?>
                                                <div class="col-md-5 col-md-offset-1">
                                                    <? if ($R['track_expire_date']) { ?>
                                                        <div>
                                                            <span>Batches</span>
                                                            <table class="table table-bordered table-condensed batch-table" style="font-size: 9pt;">
                                                                <thead>
                                                                <tr style="font-weight: bold;">
                                                                    <td>Batch No</td>
                                                                    <td>Qty</td>
                                                                    <td>Expire Date</td>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <? foreach ($R['batches'] as $bi => $batch) { ?>
                                                                    <tr>
                                                                        <td><?= $batch['batch_no'] ?></td>
                                                                        <td><?= $batch['qty'] ?></td>
                                                                        <td><?= $batch['expire_date'] ?></td>
                                                                    </tr>
                                                                <? } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <? } ?>
                                                    <? if ($R['trackserialno']) { ?>
                                                        <div>
                                                            <span>Serial No</span>
                                                            <table class="table table-bordered table-condensed serialno-table"
                                                                   style="font-size: 9pt;">
                                                                <thead>
                                                                <tr>
                                                                    <td>#</td>
                                                                    <td>Serial no</td>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <? $scount = 1;
                                                                foreach ($R['serialnos'] as $sno) { ?>
                                                                    <tr>
                                                                        <td><?= $scount ?></td>
                                                                        <td><?= $sno['number'] ?></td>
                                                                    </tr>
                                                                    <? $scount++;
                                                                } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                            <? } ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6"></td>
                                </tr>
                                <? $count++;
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<div class="modal fade" id="payment-return-modal" tabindex="-1" role="dialog" aria-labelledby="payment-return-modal" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= url('sales_returns', 'approve_credit_note') ?>" method="post">
            <input type="hidden" name="returnid" value="<?= $salereturn['id'] ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Payment Return</h4>
                </div>
                <div class="modal-body">
                    <h5>Amount to return: <span
                                class="text-rosepink"><?= $salereturn['currencyname'] ?> <?= formatN($salereturn['return_amount']) ?></span></h5>
                    <input type="hidden" name="payment[amount]" value="<?= $salereturn['return_amount'] ?>">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <label class="radio" title="">
                                <input id="manual-return" type="radio" name="return_action" value="<?= SalesReturns::ACTION_MANUAL ?>" checked
                                       onchange="checkType()">Return amount to client
                            </label>
                            <div id="pmethod-container">
                                <label>Return Method</label>
                                <select id="pmethod" name="payment[pmethod_id]" class="form-control input-sm" onchange="configPaymentDetails()">
                                    <? foreach ($payment_methods as $pm) { ?>
                                        <option value="<?= $pm['id'] ?>"><?= $pm['name'] ?></option>
                                    <? } ?>
                                </select>
                                <div id="payment-method-desc">
                                    <div class="row" id="chequeDetails" style="display: none;">
                                        <div class="col-md-4">
                                            <h5>Deposited Bank</h5>
                                            <select name="payment[bankid]" class="form-control input-sm banks">
                                                <option value="">-- choose bank --</option>
                                                <? foreach ($banks as $bank) { ?>
                                                    <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <h5>Cheque Number</h5>
                                            <input placeholder="Cheque Number" class="form-control input-sm inputs" type="text"
                                                   name="payment[chequename]" value="">
                                        </div>
                                        <div class="col-md-4">
                                            <h5>Cheque Type</h5>
                                            <select name="payment[chequetype]" class="form-control input-sm inputs">
                                                <option value="">--Cheque type--</option>
                                                <option value="PDC">PDC</option>
                                                <option value="Normal">Normal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" id="creditDetails" style="display: none;">
                                        <div class="col-md-6">
                                            <h5>Electronic Account</h5>
                                            <select name="payment[eaccid]" class="form-control input-sm inputs">
                                                <option value="">--choose account--</option>
                                                <? foreach ($eaccounts as $acc) { ?>
                                                    <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Reference No</h5>
                                            <input placeholder="Reference no" class="form-control input-sm inputs" type="text"
                                                   name="payment[credit_cardno]" value="">
                                        </div>
                                    </div>
                                    <div class="row" id="bankDetails" style="display: none;">
                                        <div class="col-md-6">
                                            <h5>Bank Name</h5>
                                            <select name="payment[bankid]" class="form-control input-sm banks">
                                                <option value="">-- choose bank --</option>
                                                <? foreach ($banks as $bank) { ?>
                                                    <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?> <?= $bank['accno'] ?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <? if ($salereturn['not_cash_client']) { ?>
                                <label class="radio mt-lg" title="create advance receipt for this amount">
                                    <input id="advance-payment" type="radio" name="return_action" value="<?= SalesReturns::ACTION_ADVANCE ?>"
                                           onchange="checkType()">Create advance receipt
                                </label>
                            <? } ?>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm mr-sm">Continue</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        checkType();
    });

    function checkType() {
        if ($('#manual-return').is(':checked')) {
            $('#pmethod').prop('disabled', false);
        } else {
            $('#pmethod').val($('#pmethod option').eq(0).val()).trigger('change').prop('disabled', true);
            // $('#pmethod-container').hide();
        }
    }

    function configPaymentDetails() {
        let text = $('#pmethod').find(':selected').text();
        let bankDetails = $('#bankDetails');
        let chequeDetails = $('#chequeDetails');
        let creditDetails = $('#creditDetails');

        $('#payment-method-desc .form-control').prop('disabled', true);

        $('select.banks').val('');

        if (text === '<?=PaymentMethods::BANK?>') {
            $(bankDetails).show('fast');
            $(bankDetails).find('.form-control').prop('disabled', false).prop('required', true);
            $('#chequeDetails,#creditDetails').hide('fast');
        } else if (text === '<?=PaymentMethods::CHEQUE?>') {
            $(chequeDetails).show('fast');
            $(chequeDetails).find('.form-control').prop('disabled', false).prop('required', true);
            $('#bankDetails,#creditDetails').hide('fast');
        } else if (text === '<?=PaymentMethods::CREDIT_CARD?>') {
            $(creditDetails).show('fast');
            $(creditDetails).find('.form-control').prop('disabled', false).prop('required', true);
            $('#bankDetails,#chequeDetails').hide('fast');
        } else {
            $('#bankDetails,#chequeDetails,#creditDetails').hide('fast');
        }
    }
</script>