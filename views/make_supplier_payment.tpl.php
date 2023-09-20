<style media="screen">
    h5 {
        font-size: 16px;
        font-weight: 600;
    }

    .btn-holder {
        float: right;
    }

    #saveBtn {
        display: none;
    }

    .amounts {
        padding: 10px;
        height: 50px;
        font-size: 22px;
        font-weight: 700;
        text-align: center;
    }
</style>
<header class="page-header">
    <h2 class="panel-title">Supplier Receipts</h2>
</header>
<div class="row">
    <div class="col-lg-12">
        <section class="panel" style="width:70%;margin:0 auto">
            <header class="panel-heading">
                <div class="btn-holder">
                    <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Supplier Receipts</h2>
            </header>
            <div class="panel-body">
                <form class="form-horizontal form-bordered" method="post" onsubmit="return validateInputs()"
                      action="<?= url('suppliers', 'save_payment') ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Supplier Name</h5>
                            <input type="hidden" name="payment[supplierid]" value="<?= $supplier['id'] ?>">
                            <input readonly placeholder="Supplier name" type="text" value="<?= $supplier['name'] ?>"
                                   class="form-control">
                        </div>
                        <div class="col-md-3">
                            <h5>Branch Name</h5>
                            <input type="hidden" name="payment[branch_id]" value="<?= $branch['id'] ?>">
                            <input readonly placeholder="Branch name" type="text" value="<?= $branch['name'] ?>"
                                   class="form-control">
                        </div>
                        <div class="col-md-3">
                            <h5>Currency</h5>
                            <input id="exchange_rate" type="hidden" name="payment[currency_amount]"
                                   value="<?= $currentCurrency['rate_amount'] ?>">
                            <input type="hidden" name="payment[currency_rateid]"
                                   value="<?= $currentCurrency['rateid'] ?>">
                            <input type="text" readonly class="form-control"
                                   value="<?= $currentCurrency['currencyname'] ?> - <?= $currentCurrency['description'] ?>">
                            <!--                            <select id="pmethod" required title="payment method" class="form-control"-->
                            <!--                                    name="payment[currency_rateid]" onchange="configCurrency(this)">-->
                            <!--                                --><? // foreach ($currencies as $c) {?>
                            <!--                                    <option -->
                            <? //=selected($c['base'],$base_currency['base'])?><!-- value="-->
                            <? //=$c['rateid']?><!--" data-exchange-rate="--><? //=$c['rate_amount']?><!--">-->
                            <!--                                        --><? //=$c['currencyname']?><!-- - -->
                            <? //=$c['description']?><!--</option>-->
                            <!--                                --><? //}?>
                            <!--                            </select>-->
                        </div>
                        <div class="col-md-3">
                            <h5>Payment Method</h5>
                            <select id="pmethod" required title="payment method" class="form-control"
                                    name="payment[pmethod_id]" onchange="configPaymentDetails()">
                                <? if ($paymentmethod) { ?>
                                    <? foreach ($paymentmethod as $key => $method) { ?>
                                        <option <?= selected($method['name'], PaymentMethods::CASH) ?>
                                                value="<?= $method['id']; ?>"><?= $method['name']; ?></option>
                                    <? } ?>
                                <? } else { ?>
                                    <option selected disabled>--Choose Method--</option>
                                <? } ?>
                            </select>
                        </div>
                    </div>

                    <div id="payment-method-desc">
                        <div class="row" id="chequeDetails" style="display: none;">
                            <div class="col-md-4">
                                <h5>Deposited Bank</h5>
                                <select name="payment_info[bankid]" class="form-control banks">
                                    <option value="">-- choose bank --</option>
                                    <? foreach ($banks as $bank) { ?>
                                        <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <h5>Cheque Number</h5>
                                <input placeholder="Cheque Number" class="form-control inputs" type="text"
                                       name="payment_info[chequename]" value="">
                            </div>
                            <div class="col-md-4">
                                <h5>Cheque Type</h5>
                                <select name="payment_info[chequetype]" class="form-control inputs">
                                    <option value="">--Cheque type--</option>
                                    <option value="PDC">PDC</option>
                                    <option value="Normal">Normal</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" id="creditDetails" style="display: none;">
                            <div class="col-md-6">
                                <h5>Electronic Account</h5>
                                <select name="payment_info[eaccid]" class="form-control inputs">
                                    <option value="">--choose account--</option>
                                    <? foreach ($eaccounts as $acc) { ?>
                                        <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <h5>Reference No</h5>
                                <input placeholder="Reference no" class="form-control inputs" type="text"
                                       name="payment_info[credit_cardno]" value="">
                            </div>
                        </div>
                        <div class="row" id="bankDetails" style="display: none;">
                            <div class="col-md-6">
                                <h5>Bank Name</h5>
                                <select name="payment_info[bankid]" class="form-control banks">
                                    <option value="">-- choose bank --</option>
                                    <? foreach ($banks as $bank) { ?>
                                        <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?> <?= $bank['accno'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <? if ($advanceAmount) { ?>
                        <div class="row mt-lg">
                            <div class="col-md-12">
                                <div class="d-flex align-items-center">
                                    <div class=" col-md-6">
                                        <h5 style="text-align:center">Advance Payment Balance</h5>
                                        <input id="advance-balance" type="hidden" name="offset_amount"
                                               value="<?= $advanceAmount ?>">
                                        <input readonly type="text" class="form-control amounts text-success"
                                               value="<?= $base_currency['name'] ?> <?= formatN($advanceAmount) ?>">
                                    </div>
                                    <div class="checkbox checkbox-success ml-xlg d-flex align-items-center">
                                        <label>
                                            <input id="offset-checker" onchange="offsetAdvance();" type="checkbox"
                                                   style="height: 40px;width: 40px;" name="offset_advance">
                                            <span class="ml-xlg">Offset Advance payments</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 style="text-align:center">Outstanding Amount to Pay</h5>
                            <input id="total-outstanding" type="hidden" name="total_outstanding"
                                   value="<?= $totalamount ?>">
                            <input autocomplete="off" readonly placeholder="Total amount to pay" type="text"
                                   class="form-control amounts"
                                   value="<?= $base_currency['name'] ?> <?= formatN($totalamount) ?>">
                        </div>
                        <div class="col-md-6">
                            <h5 id="receiveLabel" style="text-align:center">Amount Paid <small id="remainLabel"
                                                                                               class="ml-lg"></small>
                            </h5>
                            <input placeholder="amount paid" type="number" class="form-control amounts"
                                   id="inputAmount" name="input_amount" onkeyup="distributeAmount(this)"
                                   onchange="distributeAmount(this)" autocomplete="off" step="0.01" required>
                            <div class="row">
                                <div class="col-md-6">
                                    <span id="inputFormatted" class="text-primary" style="display: none;"></span>
                                </div>
                                <div class="col-md-6">
                                    <p id="newAdvanceFormatted" style="display: none;">New advance <span
                                                class="text-success"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Remark</h5>
                            <textarea name="payment[remarks]" placeholder="Write remark here......" class="form-control"
                                      rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row mt-xlg">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button id="pay-all-btn" type="button" class="btn btn-success btn-sm" onclick="payAll()">Pay
                                All
                            </button>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-hover mb-none" id="amcTable" style="font-size:13px">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="text-align:center"><i class="fa fa-list-ol"></i> GRN No.</th>
                                    <th style="text-align:center"><i class="fa fa-list"></i> Type</th>
                                    <th style="text-align:center"><i class="fa fa-bolt"></i> Supplier Invoice No.</th>
                                    <th><i class="fa fa-money"></i> Outstanding amount</th>
                                    <th title="supplier receipt no">Supplier Receipt No.</th>
                                    <th><i class="fa fa-money"></i> Amount Paid</th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($grns as $key => $R) { ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td style="width:15%;text-align:center;font-weight:900">
                                            <input readonly type="hidden" name="grnid[]"
                                                   value="<?= $R['source'] == 'grn' ? $R['id'] : '' ?>">
                                            <input readonly type="hidden" name="openingid[]"
                                                   value="<?= $R['source'] == 'opening' ? $R['id'] : '' ?>">
                                            <input type="text" class="form-control text-center" readonly
                                                   value="<?= $R['grnno'] ?>">
                                        </td>
                                        <td style="width:15%;text-align:center;">
                                            <input readonly type="text" class="form-control"
                                                   value="<?= $R['source']=='grn'?'Normal':'Opening outstanding' ?>">
                                        </td>
                                        <td style="width:15%;text-align:center;">
                                            <input readonly type="text" class="form-control"
                                                   value="<?= $R['invoiceno'] ?>">
                                        </td>
                                        <td>
                                            <input readonly class="form-control" type="text"
                                                   value="<?= formatN($R['outstanding_amount']) ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" placeholder="receipt no"
                                                   name="receipt_no[]">
                                        </td>
                                        <td>
                                            <input class="form-control payamount"
                                                   style="font-weight: bold;"
                                                   max="<?= $R['outstanding_amount'] ?>" min="0.01"
                                                   placeholder="amount <?= formatN($R['outstanding_amount']) ?>"
                                                   onkeyup="getPaidAmount()" onchange="getPaidAmount()"
                                                   type="number" step="0.01" name="pay_amount[]" required>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <div id="saveBtnx" class="col-md-12">
                            <div class="col-md-6">
                                <a href="?module=suppliers&action=payment_list"
                                   class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i>
                                    Back</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i
                                            class="fa fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script type="text/javascript">

    $(function () {
        $(':input[type="number"]').on('wheel', function (e) {
            e.preventDefault();
        });
    });

    function validateInputs() {
        let valid = true;
        if ($('.payamount').length < 1) {
            triggerError('Select at least one GRN!', 5000);
            return false;
        }

        $('.payamount').each(function () {
            if (!parseFloat($(this).val())) {
                triggerError('Amount is required!');
                $(this).focus();
                valid = false;
                return false;
            }
        });
        return valid;
    }

    function configCurrency(obj) {
        let exchange_rate = $(obj).find(':selected').data('exchange-rate');
        $('#exchange_rate').val(exchange_rate);
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

    function offsetAdvance() {
        let totalOutstanding = parseFloat($('#total-outstanding').val()) || 0;
        let advanceAmount = parseFloat($('#advance-balance').val()) || 0;
        let remainAfterOffset = totalOutstanding - advanceAmount;
        if ($('#offset-checker').is(':checked')) {
            $('#inputAmount').val('').prop('required', false);
            $('.payamount').val('').prop('readonly', true);
            $('#pay-all-btn').prop('disabled', true);
            if (remainAfterOffset > 0) {
                $('#remainLabel').text(`(remain ${numberWithCommas(remainAfterOffset.toFixed(2))})`);
            }
        } else {
            $('#inputAmount').val('').prop('required', true);
            $('.payamount').val('').prop('readonly', false);
            $('#pay-all-btn').prop('disabled', false);
            $('#remainLabel').text(``);
        }
        distributeAmount();
    }

    function distributeAmount() {
        $('.payamount').val('');

        let inputAmount = parseFloat($('#inputAmount').val()) || 0;
        let totalOutstanding = parseFloat($('#total-outstanding').val()) || 0;
        let advanceAmount = parseFloat($('#advance-balance').val()) || 0;

        let paidAmount = inputAmount;
        if ($('#offset-checker').is(':checked')) {
            paidAmount += advanceAmount;
        }
        if (!paidAmount) return;

        let remainAmount = paidAmount;
        $('.payamount').each(function () {
            let grnAmount = parseFloat($(this).attr('max'));
            if (remainAmount > grnAmount) {
                $(this).val(grnAmount);
                remainAmount -= grnAmount;
            } else {
                $(this).val(remainAmount);
                remainAmount -= remainAmount;
            }
            if (remainAmount == 0) return false;
        });

        let newAdvance = 0;
        if ($('#offset-checker').is(':checked')) {
            let remainAfterOffset = totalOutstanding - advanceAmount;
            if (remainAfterOffset > 0) {
                newAdvance = inputAmount - remainAfterOffset;
            } else {
                newAdvance = inputAmount;
            }
        } else {
            newAdvance = inputAmount - totalOutstanding;
        }

        if (newAdvance > 0) {
            $('#newAdvanceFormatted').show('fast').find('span').text(numberWithCommas(newAdvance.toFixed(2)));
        } else {
            $('#newAdvanceFormatted').hide('fast').find('span').text(numberWithCommas(newAdvance));
        }

        let receivedFormatted = $('#receivedFormatted');
        $(receivedFormatted).show().text(numberWithCommas(inputAmount.toFixed(2)));
        colorValue();
    }

    function getPaidAmount() {
        let inputAmount = 0;
        $('.payamount').each(function () {
            let maxamount = parseFloat($(this).attr('max'));
            let payamount = parseFloat($(this).val()) || 0;
            if (payamount > maxamount) {
                triggerError('Entered amount cant exceed required amount!');
                $(this).val(maxamount);
                payamount = maxamount;
            }
            inputAmount += payamount;
        });
        $('#inputAmount').val(inputAmount);
        colorValue();
    }

    function payAll() {
        let inputAmount = 0;
        $('.payamount').each(function () {
            let payamount = parseFloat($(this).attr('max'));
            $(this).val(payamount);
            inputAmount += payamount;
        });
        $('#inputAmount').val(inputAmount);
        $('#newAdvanceFormatted').hide('fast').find('span').text('');
        colorValue();
    }


    function colorValue() {
        $('.payamount').each(function () {
            let payamount = parseFloat($(this).attr('max'));
            if (payamount === parseFloat($(this).val())) {
                $(this).addClass('text-success');
                $(this).removeClass('text-warning');
            } else if (payamount > parseFloat($(this).val())) {
                $(this).addClass('text-warning');
                $(this).removeClass('text-success');
            }
        });
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
