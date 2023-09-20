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

    #spinnerHolder {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        height: 100vh;
        overflow: hidden;
        width: 100%;
        display: none;
        background-color: black;
        opacity: 0.5;
        z-index: 10000;
    }
</style>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <section class="panel" style="width:70%;margin:0 auto">
            <header class="panel-heading">
                <div class="btn-holder">
                    <a class="btn" href="?module=advance_payments&action=list"> <i class="fa fa-list"></i> Advance
                        Payment List</a>
                    <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Make Advance Receipt</h2>
            </header>
            <div class="panel-body">
                <form id="advance-form" method="post" action="<?= url('advance_payments', 'save') ?>" onsubmit="return validateInputs()">
                    <input type="hidden" name="payment[token]" value="<?= unique_token() ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Client</h5>
                            <input type="hidden" name="payment[id]">
                            <select id="client" class="form-control" name="payment[clientid]" required></select>
                        </div>
                        <div class="col-md-3">
                            <h5>Branch</h5>
                            <select id="branchid" required class="form-control" name="payment[branchid]">
                                <? foreach ($branches as $key => $b) { ?>
                                    <option <?= selected($b['id'], $_SESSION['member']['id']) ?> value="<?= $b['id']; ?>"><?= $b['name']; ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Currency</h5>
                            <select required title="Currency" class="form-control" name="payment[currencyid]">
                                <? foreach ($currencies as $c) { ?>
                                    <option <?= selected($c['base'], 'yes') ?>
                                            value="<?= $c['id']; ?>"><?= $c['name'] ?> - <?= $c['description'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Payment Method</h5>
                            <select id="pmethod" required title="payment method" class="form-control"
                                    name="payment[pmethod_id]" onchange="configPaymentDetails()">
                                <? foreach ($paymentmethods as $key => $method) { ?>
                                    <option <?= selected($method['name'], PaymentMethods::CASH) ?>
                                            value="<?= $method['id']; ?>"><?= $method['name']; ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div id="payment-method-desc">
                        <div class="row" id="chequeDetails" style="display: none;">
                            <div class="col-md-4">
                                <h5>Deposited Bank</h5>
                                <select name="payment[bankid]" class="form-control banks">
                                    <option value="">-- choose bank --</option>
                                    <? foreach ($banks as $bank) { ?>
                                        <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <h5>Cheque Number</h5>
                                <input placeholder="Cheque Number" class="form-control inputs" type="text"
                                       name="payment[chequename]" value="">
                            </div>
                            <div class="col-md-4">
                                <h5>Cheque Type</h5>
                                <select name="payment[chequetype]" class="form-control inputs">
                                    <option value="">--Cheque type--</option>
                                    <option value="PDC">PDC</option>
                                    <option value="Normal">Normal</option>
                                </select>
                            </div>
                        </div>
                        <div class="row" id="creditDetails" style="display: none;">
                            <div class="col-md-6">
                                <h5>Electronic Account</h5>
                                <select name="payment[eaccid]" class="form-control inputs">
                                    <option value="">--choose account--</option>
                                    <? foreach ($eaccounts as $acc) { ?>
                                        <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <h5>Reference No</h5>
                                <input placeholder="Reference no" class="form-control inputs" type="text"
                                       name="payment[credit_cardno]" value="">
                            </div>
                        </div>
                        <div class="row" id="bankDetails" style="display: none;">
                            <div class="col-md-6">
                                <h5>Bank Name</h5>
                                <select name="payment[bankid]" class="form-control banks">
                                    <option value="">-- choose bank --</option>
                                    <? foreach ($banks as $bank) { ?>
                                        <option value="<?= $bank['id'] ?>"><?= $bank['name'] ?> <?= $bank['accno'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 30px;">
                        <div class="col-md-12">
                            <h5 style="text-align:center">Received Amount</h5>
                            <input autocomplete="off" placeholder="Enter received amount" type="text" required
                                   class="form-control amounts text-success" id="totalReceived"
                                   name="payment[amount]" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Remark</h5>
                            <textarea name="payment[remark]" placeholder="Write remark here......" class="form-control"
                                      rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div id="saveBtnx" class="col-md-12">
                            <div class="col-md-6">
                                <a href="<?= url('advance_payments', 'list') ?>"
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
<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#client', "?module=clients&action=getClients&format=json&no_default", 'Choose Client');
        $('#branchid').select2({width:'100%'});
        thousands_separator('.amounts');
    });

    function validateInputs() {
        $('#spinnerHolder').show();
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

    function distributeAmount() {
        let totalReceived = parseFloat($('#totalReceived').val());
        // console.log(totalReceived);
        let remainingAmount = isNaN(totalReceived) ? 0 : totalReceived;
        $('.payamount').each(function () {
            let payamount = parseFloat($(this).attr('max'));
            // console.log(payamount);
            if (payamount > remainingAmount) {
                if (remainingAmount === 0) {
                    $(this).val('');
                } else {
                    $(this).val(remainingAmount);
                }
                remainingAmount = 0;
            } else {
                $(this).val(payamount);
                remainingAmount -= payamount;
            }
        });
        colorValue();
    }

    function getPaidAmount() {
        let totalPaid = 0;
        let valid = true;
        $('.payamount').each(function () {
            let paidamount = parseFloat($(this).val());
            paidamount = isNaN(paidamount) ? 0 : paidamount;
            let max = parseFloat($(this).attr('max'));
            if (paidamount > max) {
                triggerError('Paid amount cant exceed remaining amount!');
                $(this).val(max);
                // console.log('max', max);
                valid = false;
                return false;
            }
            // console.log('paid:', paidamount);
            totalPaid += paidamount;
            // console.log('total:', totalPaid);
        });
        colorValue();
        $('#totalReceived').val(totalPaid);
        if (!valid) getPaidAmount();
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

    function remoComma(number) {
        return number.replace(/,/g, '')
    }

    function addComma(obj) {
        // return $(obj).val($(obj).val().replace(/(,|)/g,'').replace(/(.)(?=(.{3})+$)/g,"$1,").replace(',.', '.'));
    }
</script>
