<style>
    #spinnerHolder {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100vh;
        width: 100%;
        display: none;
        background-color: black;
        opacity: 0.5;
        z-index: 10000;
    }
</style>
<header class="page-header">
    <h2>Client Receipts</h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="btn-holder">
                    <a class="btn" href="?module=payments&action=invoice_list"> <i class="fa fa-list"></i> Invoice List</a>
                    <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Client Receipts</h2>
            </header>
            <div class="panel-body">
                <form class="form-horizontal form-bordered" method="post" action="<?= url('payments', 'credit_payment_save') ?>"
                      onsubmit="return validateInputs()">
                    <input type="hidden" name="payment[token]" value="<?= unique_token() ?>">
                    <input type="hidden" name="redirect" value="<?= $redirect ?>">

                    <div class="row">
                        <div class="col-md-3">
                            <h5>Client Name</h5>
                            <input type="hidden" name="payment[clientid]" value="<?= $client['id'] ?>">
                            <input type="hidden" name="payment[receipt_type]" value="<?= $receipt_type ?>">
                            <input readonly placeholder="Client name" type="text" value="<?= $client['name'] ?>"
                                   class="form-control">
                        </div>
                        <div class="col-md-3">
                            <h5>Currency</h5>
                            <select id="currencyid" required title="payment method" class="form-control"
                                    name="payment[currencyid]" onchange="chooseCurrency()">
                                <? foreach ($currencies as $key => $c) { ?>
                                    <option <?= selected($c['base'], 'yes') ?>
                                            value="<?= $c['id'] ?>" data-base="<?= $c['base'] ?>"
                                            data-name="<?= $c['name'] ?>">
                                        <?= $c['name'] ?> - <?= $c['description'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Buying Exchange Rate</h5>
                            <input id="base_buying" type="number" class="form-control" name="payment[buying_rate]"
                                   min="0.01" step="0.01" required oninput="calRequiredAmount()">
                        </div>
                        <div class="col-md-3">
                            <h5>Payment Method</h5>
                            <select id="pmethod" required title="payment method" class="form-control"
                                    name="payment[pmethod_id]" onchange="configPaymentDetails()">
                                <? foreach ($paymentmethod as $key => $method) { ?>
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

                    <? if ($advanceBalances) { ?>
                        <div class="row mt-lg">
                            <div class="col-md-4">
                                <h5>Advance Balances:</h5>
                                <table class="table table-bordered" style="font-size: 9pt;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Currency</th>
                                        <th>Amount</th>
                                        <th>offset</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <? foreach ($advanceBalances as $index => $a) { ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= $a['currencyname'] ?></td>
                                            <td class="text-success"><?= formatN($a['remaining_advance']) ?></td>
                                            <td style="vertical-align: center;text-align: center">
                                                <div class="checkbox p-none">
                                                    <label>
                                                        <input type="checkbox" name="offset_advance"
                                                               class="offset_checkbox"
                                                               data-currencyname="<?= $a['currencyname'] ?>"
                                                               data-amount="<?= $a['remaining_advance'] ?>"
                                                               style="height: 20px;width: 20px;" disabled
                                                               title="offset <?= formatN($a['remaining_advance']) ?> <?= $a['currencyname'] ?>"
                                                               onchange="offsetAdvance(this)">
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <? } ?>
                    <div class="row">
                        <div class="col-md-4">
                            <h5 style="text-align:center">Grand Amount to Pay</h5>
                            <input id="grandAmount" type="hidden" name="grandAmountToPay">
                            <input id="grandAmountLabel" readonly placeholder="Total amount to pay" type="text"
                                   class="form-control text-center text-weight-bold">
                        </div>
                        <div class="col-md-4">
                            <h5 id="receiveLabel" style="text-align:center">Offset Amount</h5>
                            <input id="offsetAmountLabel" type="text" readonly
                                   class="form-control text-center text-weight-bold"
                                   name="offset_amount" placeholder="Offset amount">
                            <input id="offsetAmount" type="hidden" name="offset_amount">
                        </div>
                        <div class="col-md-4">
                            <h5 id="receiveLabel" style="text-align:center">Received Amount</h5>
                            <input id="receivedAmount" autocomplete="off" placeholder="Enter received amount" type="text"
                                   class="form-control amounts" oninput="distributeAmount()" name="received_amount" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Remark</h5>
                            <textarea name="payment[remark]" placeholder="Write remark here......" class="form-control"
                                      rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row mt-lg">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="button" class="btn btn-success btn-sm" onclick="payAll()">Pay All</button>
                        </div>
                        <div class="col-md-12">
                            <table id="sales-table" class="table table-hover mb-none" style="font-size:10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No.</th>
                                    <th>Currency</th>
                                    <th>Pending amount</th>
                                    <th title="Selling Exchange Rate To (<?= $baseCurrency['name'] ?>)">
                                        Selling Exchange Rate
                                    </th>
                                    <th>Required Amount</th>
                                    <th>Amount Paid</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? $count = 1;
                                foreach ($sales as $s) { ?>
                                    <tr>
                                        <td><?= $count ?></td>
                                        <td>
                                            <input type="hidden" name="sales[<?= $s['salesid'] ?>][salesid]" value="<?= $s['salesid'] ?>" required>
                                            <input readonly type="text" class="form-control"
                                                   value="<?= $s['receipt_no'] ?>">
                                        </td>
                                        <td>
                                            <input readonly class="form-control" type="text"
                                                   value="<?= $s['currencyname'] ?>">
                                            <input type="hidden" class="currencyid" value="<?= $s['currencyid'] ?>">
                                        </td>
                                        <td>
                                            <input readonly class="form-control" type="text" value="<?= formatN($s['pending_amount']) ?>">
                                            <input class="pending_amount" type="hidden" value="<?= $s['pending_amount'] ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center base_selling" min="0.01" required step="0.01"
                                                   readonly name="sales[<?= $s['salesid'] ?>][base_selling]"
                                                   title="<?= $s['currencyname'] ?> to <?= $baseCurrency['name'] ?>"
                                                   value="<?= $s['current_rate'] ?>">
                                        </td>
                                        <td>
                                            <input class="form-control required_amount" placeholder="required" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control amount" required placeholder="amount" type="text" step="0.01"
                                                   name="sales[<?= $s['salesid'] ?>][amount]" oninput="getPaidAmount()">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-xs" title="remove" onclick="removeItem(this)">
                                                <i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <? $count++;
                                } ?>
                                <? foreach ($opening_outstandings as $op) { ?>
                                    <tr>
                                        <td><?= $count ?></td>
                                        <td>
                                            <input type="hidden" name="opening[<?= $op['id'] ?>][id]" value="<?= $op['id'] ?>" required>
                                            <input readonly type="text" class="form-control"
                                                   value="<?= $op['invoiceno'] ?>">
                                        </td>
                                        <td>
                                            <input readonly class="form-control" type="text"
                                                   value="<?= $op['currencyname'] ?>">
                                            <input type="hidden" class="currencyid" value="<?= $op['currencyid'] ?>">
                                        </td>
                                        <td>
                                            <input readonly class="form-control" type="text" value="<?= formatN($op['pending_amount']) ?>">
                                            <input class="pending_amount" type="hidden" value="<?= $op['pending_amount'] ?>">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control text-center base_selling" min="0.01" required step="0.01"
                                                   readonly name="opening[<?= $op['id'] ?>][base_selling]"
                                                   title="<?= $op['currencyname'] ?> to <?= $baseCurrency['name'] ?>"
                                                   value="<?= $op['current_rate'] ?>">
                                        </td>
                                        <td>
                                            <input class="form-control required_amount" placeholder="required" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control amount" required placeholder="amount" type="text" step="0.01"
                                                   name="opening[<?= $op['id'] ?>][amount]" oninput="getPaidAmount()">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-xs" title="remove" onclick="removeItem(this)">
                                                <i class="fa fa-close"></i></button>
                                        </td>
                                    </tr>
                                    <? $count++;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <div id="saveBtnx" class="col-md-12">
                            <div class="col-md-6">
                                <a href="<?= $_SERVER['HTTP_REFERER'] ?>" class="mb-xs mt-xs mr-xs btn btn-success btn-block">
                                    <i class="fa fa-list"></i> Back</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save</button>
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
        chooseCurrency();
    });

    function validateInputs() {
        let valid = true;
        if ($('#sales-table tbody tr').length === 0) {
            triggerError('At least one invoice is required!', 5000);
            return false;
        }

        $('#sales-table tbody tr .amount').each(function (i, item) {
            if ($(item).val().length === 0) {
                triggerError('Paid amount is required!', 4000);
                $(item).focus();
                valid = false;
                return false;
            }
        });
        if (!valid) return false;

        let received_amount = removeCommas($('#receivedAmount').val()) || 0;
        let max_receive_amount = parseFloat($('#receivedAmount').attr('max'));
        let grand_amount = $('#grandAmount').val();
        if (received_amount > max_receive_amount) {
            triggerError(`You cant receive more than ${numberWithCommas(max_receive_amount)},
            \nOverall receipt amount must be less than or equal to ${numberWithCommas(grand_amount)}`, 6000);
            $('#receivedAmount').focus();
            return false;
        }

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

    function format_input() {
        thousands_separator('#receivedAmount, .amount');
    }

    function chooseCurrency() {
        let obj = $('#currencyid');
        let currencyid = $(obj).val();
        let currencyname = $(obj).find(':selected').data('name');
        let base_currencyid = $(obj).find("option[data-base='yes']").val();
        let base_currencyname = $(obj).find("option[data-base='yes']").data('name');

        $('#offsetAmountLabel,#offsetAmount').val('');
        $('#base_buying').attr('placeholder', `Buy ${currencyname} to ${base_currencyname}`)
            .attr('title', `Buying ${currencyname} to ${base_currencyname} exchange rate`);

        if (currencyid === base_currencyid) {
            $('#base_buying').val('1.00');
        } else {
            $('#base_buying').val('');
        }
        calRequiredAmount();
    }

    function enableOffsetChecker() {
        let currencyname = $('#currencyid').find(':selected').data('name');
        $('#offsetAmountLabel,#offsetAmount').val('');
        $('.offset_checkbox').prop('checked', false).prop('disabled', true);
        if ($('#base_buying').val()) {
            $('.offset_checkbox').each(function (i, checkbox) {
                if ($(checkbox).data('currencyname') === currencyname) {
                    $(checkbox).prop('disabled', false);
                    return false;
                }
            });
        }
    }

    function calRequiredAmount() {
        let currencyname = $('#currencyid').find(':selected').data('name');
        let base_buying = parseFloat($('#base_buying').val()) || 0;
        let grand_amount = 0;
        if (base_buying) {
            $('#sales-table tbody tr').each(function (i, tr) {
                let outstanding_amount = parseFloat($(tr).find('.pending_amount').val()) || 0;
                let base_selling = parseFloat($(tr).find('.base_selling').val()) || 0;

                let required_amount = (outstanding_amount * base_selling / base_buying).toFixed(2);
                required_amount = parseFloat(required_amount);
                required_amount = required_amount ? required_amount : '0.01';
                $(tr).find('.required_amount').val(`${numberWithCommas(required_amount)} ${currencyname}`);
                $(tr).find('.amount').attr('max', required_amount).val('');
                grand_amount += parseFloat(required_amount);
                // console.log(required_amount, grand_amount);
            });
            $('#grandAmountLabel').val(`${numberWithCommas(grand_amount.toFixed(2))} ${currencyname}`);
            $('#grandAmount').val(grand_amount.toFixed(2));
        } else {
            $('#sales-table tbody tr .required_amount').val('');
            $('#sales-table tbody tr .amount').attr('max', '').val('');
            $('#grandAmountLabel,#grandAmount').val('');
        }
        enableOffsetChecker();
        maxReceiveAmount();
        distributeAmount();
        format_input();
    }

    function offsetAdvance(obj) {
        let currencyname = $('#currencyid').find(':selected').data('name');
        let offsetAmount = parseFloat($(obj).data('amount'));
        let grand_amount = parseFloat($('#grandAmount').val()) || 0;
        offsetAmount = grand_amount >= offsetAmount ? offsetAmount : grand_amount;
        console.log('grand: ', grand_amount, 'offset:', offsetAmount);
        if ($(obj).is(':checked')) {
            $('#offsetAmountLabel').val(`${numberWithCommas(offsetAmount)} ${currencyname}`);
            $('#offsetAmount').val(offsetAmount);
        } else {
            $('#offsetAmountLabel,#offsetAmount').val('');
        }
        maxReceiveAmount();
        distributeAmount();
    }


    function maxReceiveAmount() {
        let currencyname = $('#currencyid').find(':selected').data('name');
        let grand_amount = parseFloat($('#grandAmount').val()) || 0;
        let offset_amount = parseFloat($('#offsetAmount').val()) || 0;
        let max_receive_amount = grand_amount - offset_amount;
        max_receive_amount = max_receive_amount > 0 ? max_receive_amount.toFixed(2) : 0;
        $('#receivedAmount').val('').attr('max', max_receive_amount)
            .attr('placeholder', `max, ${numberWithCommas(max_receive_amount)} ${currencyname}`);
    }

    function distributeAmount() {

        let received_amount = removeCommas($('#receivedAmount').val()) || 0;
        let offset_amount = parseFloat($('#offsetAmount').val()) || 0;
        // console.log(totalReceived);
        let remainingAmount = received_amount + offset_amount;
        $('.amount').each(function () {
            let payamount = parseFloat($(this).attr('max'));
            if (isNaN(payamount)) {
                triggerWarning('Choose Buy rate first!');
                $('#base_buying').focus();
                return false;
            }
            // console.log(payamount);
            if (payamount > remainingAmount) {
                if (remainingAmount === 0) {
                    $(this).val('');
                } else {
                    $(this).val(remainingAmount.toFixed(2));
                }
                remainingAmount = 0;
            } else {
                $(this).val(payamount.toFixed(2));
                remainingAmount -= payamount;
            }
        });
        colorValue();
        format_input();
    }

    function getPaidAmount() {
        let totalPaidAmount = 0;
        let offset_amount = parseFloat($('#offsetAmount').val()) || 0;
        let valid = true;
        $('.amount').each(function () {
            let amount = removeCommas($(this).val()) || 0;
            let max = parseFloat($(this).attr('max'));
            if (amount > max) {
                triggerError('Paid amount cant exceed remaining amount!');
                $(this).val(max);
                // console.log('max', max);
                valid = false;
                return false;
            }
            // console.log('paid:', amount);
            totalPaidAmount += amount;
            // console.log('total:', totalPaidAmount);
        });
        colorValue();
        let received = totalPaidAmount - offset_amount;
        received = received > 0 ? received.toFixed(2) : '';
        $('#receivedAmount').val(received);
        format_input();
        if (!valid) getPaidAmount();
    }

    function payAll() {
        let base_buying = parseFloat($('#base_buying').val()) || 0;
        if (base_buying) {
            $('#receivedAmount').val($('#receivedAmount').attr('max'));
            distributeAmount();
        }
    }

    function removeItem(obj) {
        $(obj).closest('tr').remove();
        calRequiredAmount();
    }

    function colorValue() {
        $('.amount').each(function () {
            let payamount = parseFloat($(this).attr('max'));
            if (payamount === removeCommas($(this).val())) {
                $(this).addClass('text-success');
                $(this).removeClass('text-warning');
            } else if (payamount > parseFloat($(this).val())) {
                $(this).addClass('text-warning');
                $(this).removeClass('text-success');
            }
        });
    }

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }


    function removeCommas(amount) {
        return parseFloat(amount.replace(/,/g, ''));
    }
</script>
