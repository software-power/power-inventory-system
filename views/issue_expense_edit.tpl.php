<style media="screen">
    .center-panel {
        width: 70%;
        margin: 0 auto;
    }

    .expe-row {
        margin-top: 10px;
    }

    .select2-container .select2-selection--single {
        font-size: 15px;
        height: 34px;
    }

    .expenses_row {
        margin-top: 5px;
    }

    #total_amount {
        padding: 11px;
        height: 46px;
        text-align: center;
        font-size: 32px;
        font-weight: 700;
    }

    .lbl {
        text-align: center;
        font-size: 17px;
        width: 100%;
        font-weight: 600;
    }
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


<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel center-panel">
            <header class="panel-heading">
                <h2 class="panel-title">Issue Expense</h2>
            </header>
            <div class="panel-body">
                <form id="form" class="form-horizontal form-bordered" method="post"
                      action="<?= url('expenses', 'save_issued_expense') ?>"
                      onsubmit="return validateInputs()">
                    <input type="hidden" name="expense[id]" value="<?= $expense['id'] ?>">
                    <fieldset class="row-panel">
                        <legend>Expense info</legend>
                        <div class="row">
                            <? if (!empty($sale)) { ?>
                                <div class="col-md-3">
                                    <label for="">Sale Receipt</label>
                                    <input type="hidden" name="expense[saleid]" value="<?= $sale['id'] ?>">
                                    <input type="text" class="form-control"
                                           placeholder="Receipt No" value="<?= $sale['receipt_no'] ?>" readonly>
                                </div>
                            <? } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Branch</label>
                                <select id="branchid" name="expense[branchid]" class="form-control" required>
                                    <? if (empty($sale)) { ?>
                                        <option value="" selected disabled>Choose Branch</option>
                                    <? } ?>
                                    <? foreach ($branches as $index => $R) { ?>
                                        <option <?= selected($expense['branchid'], $R['id']) ?>
                                                value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="">Currency</label>
                                <select name="expense[currencyid]" class="form-control" readonly>
                                    <? foreach ($currencies as $currency) {
                                        if ($currency['base'] == 'yes') { ?>
                                            <option value="<?= $currency['id'] ?>"><?= $currency['name'] ?> - <?= $currency['description'] ?></option>
                                        <? }
                                    } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="">Invoice No</label>
                                <input type="text" name="expense[invoiceno]" class="form-control"
                                       placeholder="Invoice no" value="<?= $expense['invoiceno'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="">Paid To</label>
                                <input type="text" name="expense[paidto]" class="form-control"
                                       placeholder="Paid to" value="<?= $expense['paidto'] ?>">
                            </div>
                        </div>
                        <div class="row mt-md">
                            <div class="col-md-3">
                                <label for="">Verification Code</label>
                                <input type="text" name="expense[verificationcode]" class="form-control"
                                       placeholder="verification code" value="<?= $expense['verificationcode'] ?>">
                            </div>
                            <div class="col-md-9">
                                <label for="">Remarks</label>
                                <textarea type="text" name="expense[remarks]" class="form-control" rows="3"
                                          placeholder="remarks"><?= $expense['remarks'] ?></textarea>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="row-panel">
                        <legend>Money Counter section for expenses amount</legend>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="lbl">GRAND TOTAL</label>
                                <input id="total_amount" class="form-control" readonly type="text"
                                       name="expense[total_amount]" value="<?= formatN($expense['total_amount']) ?>">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="row-panel">
                        <legend>Expenses/Cost attribute and it's amount</legend>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-success" onclick="addRow()"><i
                                            class="fa fa-plus"></i> Add row
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Expenses/cost attribute</h5>
                            </div>
                            <div class="col-md-5">
                                <h5>Amount</h5>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <? if ($expense) { ?>
                            <? foreach ($expense['details'] as $index => $detail) { ?>
                                <div class="row expense-detail">
                                    <div class="col-md-6">
                                        <select required class="expenses_attribute form-control" name="attrId[]">
                                            <option value="<?= $detail['attributeid'] ?>"
                                                    selected><?= $detail['attrname'] ?></option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" oninput="totalAmount(this)" required min="0" step="0.01"
                                               class="form-control amount" title="Cost is required"
                                               placeholder="Expenses amount" name="amount[]"
                                               value="<?= $detail['amount'] ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <div class="btn btn-float" onclick="removeRow(this)"><i class="fa fa-minus"></i>
                                        </div>
                                    </div>
                                </div>
                            <? } ?>
                        <? } else { ?>
                            <div class="row expense-detail">
                                <div class="col-md-6">
                                    <select required class="expenses_attribute form-control" name="attrId[]">
                                        <option value="">--Choose attribute--</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" oninput="totalAmount(this)" required min="0" step="0.01"
                                           class="form-control amount" title="Cost is required"
                                           placeholder="Expenses amount" name="amount[]" value="0">
                                </div>
                                <div class="col-md-1">
                                    <div class="btn btn-float" onclick="removeRow(this)"><i class="fa fa-minus"></i>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                        <div id="drop_area"></div>
                    </fieldset>

                    <div class="row expe-row">
                        <div class="col-md-6">
                            <a href="<?= url('expenses', 'issued_list') ?>" class="btn btn-success btn-block">
                                <i class="fa fa-list"></i> Back to list</a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save expenses</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script>
    $(function () {
        $("#name").focus();
        $('#branchid').select2({width:'100%'});
        initExpAttrSelect();
        format_input();
    });

    function validateInputs() {
        if ($('#branchid').val().length < 1) {
            triggerError('Choose branch!');
            return false;
        }
        if ($('.expenses_attribute').length < 1) {
            triggerError('Enter at least one attribute');
            return false;
        }

        let valid = true;
        $('.expense-detail').each(function () {
            let expense_attr = $(this).find('.expenses_attribute').val();
            if (expense_attr == null || expense_attr == '') {
                triggerError('Choose item first!');
                $(this).find('.amount').focus();
                valid = false;
                return false;
            }

            let amount = removeCommas($(this).find('.amount').val()) || 0;
            if (amount === 0) {
                triggerError('Enter valid amount!');
                $(this).find('.amount').focus();
                valid = false;
                return false;
            }
        });
        if (!valid) return false;
        $('#spinnerHolder').show();
    }

    function initExpAttrSelect() {
        $('.expenses_attribute').select2({
            width: '100%', minimumInputLength: 1, ajax: {
                url: "?module=expenses&action=getexpensesAttributes&format=json",
                dataType: 'json',
                delay: 250,
                quietMillis: 200,
                data: function (term) {
                    return {search: term};
                },
                results: function (data, page) {
                    return {result: data};
                }
            }
        });

    }

    function format_input() {
        thousands_separator('.amount');
    }

    function addRow() {
        let row = `<div class="row expense-detail">
                            <div class="col-md-6">
                                <select required class="expenses_attribute form-control" name="attrId[]">
                                    <option value="">--Choose attribute--</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="text" oninput="totalAmount(this)" required min="0" step="0.01"
                                       class="form-control amount" title="Cost is required"
                                       placeholder="Expenses amount" name="amount[]" value="0">
                            </div>
                            <div class="col-md-1">
                                <div class="btn btn-float" onclick="removeRow(this)"><i class="fa fa-minus"></i>
                                </div>
                            </div>
                        </div>`;
        $('#drop_area').append(row);
        //list
        initExpAttrSelect();
        format_input();
    }

    function removeRow(obj) {
        $(obj).closest('.row').remove();
        totalAmount();
    }

    function totalAmount() {
        let grand = 0;
        $('.amount').each(function (index, el) {
            let amount = removeCommas($(el).val());
            grand += amount;
        });
        $('#total_amount').val(numberWithCommas(grand));
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    function removeCommas(amount) {
        return parseFloat(amount.replace(/,/g, ''));
        //return parseFloat(amount.replace(",", ""));
    }
</script>
