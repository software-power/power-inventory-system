<style media="screen">
    #spinnerHolder {
        position: fixed;
        display: none;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        height: 100vh;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.16);
        z-index: 50000;
    }

    .holder-scroll {
        max-height: 70vh;
        overflow-x: hidden;
        overflow-y: auto;
        box-shadow: inset 0 0 5px grey;
        border-radius: 10px;
        padding-top: 5px;
    }

    .group {
        border: 1px dashed grey;
        border-radius: 15px;
        margin-bottom: 5px;
        position: relative;
    }

    .all-return-label {
        position: absolute;
        left: 45%;
        top: 50%;
        transform: rotateZ(332deg);
        z-index: 20;
        font-size: 19pt;
        opacity: 0.6;
    }
</style>
<header class="page-header">
    <h2>Credit Note</h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h1 class="text-weight-semibol text-danger">Please wait</h1>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-xs-12 col-xl-11">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Credit Note | Invoice no: <span class="text-primary"><?= $invoice['receipt_no'] ?></span> | Price Correction
                </h2>
            </header>
            <div class="panel-body">
                <form class="formProforma form-horizontal form-bordered" method="post" action="<?= url('sales_returns', 'save_credit_note') ?>"
                      onsubmit="return validateInputs()">
                    <input type="hidden" name="salereturn[id]" value="<?= $salereturn['id'] ?>">
                    <input type="hidden" name="salereturn[salesid]" value="<?= $invoice['salesid'] ?>">
                    <input type="hidden" name="salereturn[type]" value="<?= SalesReturns::TYPE_PRICE ?>">
                    <input type="hidden" name="salereturn[token]" value="<?= unique_token() ?>">
                    <div class="row">
                        <div class="col-md-2">
                            <h5>Client</h5>
                            <input type="text" class="form-control" readonly value="<?= $invoice['client']['name'] ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>TIN No.</h5>
                            <input type="text" class="form-control" readonly value="<?= $invoice['client']['tinno'] ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>VAT/VRN No.</h5>
                            <input type="text" class="form-control" readonly value="<?= $invoice['client']['vatno'] ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>Address</h5>
                            <input type="text" class="form-control" readonly value="<?= $invoice['client']['address'] ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>Mobile</h5>
                            <input type="text" class="form-control" readonly value="<?= $invoice['client']['mobile'] ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>Email</h5>
                            <input type="text" class="form-control" readonly value="<?= $invoice['client']['email'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <h5>Currency</h5>
                            <input type="hidden" name="salereturn[currencyid]" value="<?= $invoice['currencyid'] ?>">
                            <input type="hidden" class="currencyname" value="<?= $invoice['currencyname'] ?>">
                            <input type="text" class="form-control" readonly
                                   value="<?= $invoice['currencyname'] ?> - <?= $invoice['currency_description'] ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>Exchange Rate</h5>
                            <input type="text" class="form-control" readonly name="salereturn[currency_amount]"
                                   value="<?= $invoice['currency_amount'] ?>">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-2">
                            <h5>Invoice type</h5>
                            <input type="text" readonly class="form-control text-capitalize" value="<?= $invoice['paymenttype'] ?> Invoice">
                        </div>
                        <div class="col-md-2">
                            <h5>Full Amount</h5>
                            <input type="text" readonly class="form-control" value="<?= formatN($invoice['full_amount']) ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>Paid Amount</h5>
                            <input type="text" readonly class="form-control text-success" value="<?= formatN($invoice['lastpaid_totalamount']) ?>">
                        </div>
                        <div class="col-md-2">
                            <h5>Previous Credit Notes</h5>
                            <input type="text" readonly class="form-control text-rosepink"
                                   value="<?= formatN($invoice['total_increturn']) ?>">
                        </div>
                        <? if ($invoice['paymenttype'] == PAYMENT_TYPE_CREDIT) { ?>
                            <div class="col-md-2">
                                <h5>Outstanding Amount</h5>
                                <input type="text" readonly class="form-control text-danger" value="<?= formatN($invoice['pending_amount']) ?>">
                            </div>
                        <? } ?>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-12">
                            <h5 class="text-weight-bold">Items/ Description</h5>
                        </div>
                    </div>
                    <div class="p-sm">
                        <div class="row">
                            <div class="col-md-3 text-weight-bold text-center">Product Name</div>
                            <div class="col-md-1 text-weight-bold text-center">Rate</div>
                            <div class="col-md-1 text-weight-bold text-center">Reduce Rate</div>
                            <div class="col-md-1 text-weight-bold text-center">Purchased Qty</div>
                            <div class="col-md-1 text-weight-bold text-center">VAT %</div>
                            <div class="col-md-1 text-weight-bold text-center">Total Amount</div>
                            <div class="col-md-1 text-weight-bold text-center">Reduce Amount</div>
                            <div class="col-md-2 text-weight-bold text-center">New Total Amount</div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                    <div id="product-holder" class="<?= count($invoice['products']) >= 5 ? 'holder-scroll' : '' ?>">
                        <? foreach ($invoice['products'] as $index => $R) { ?>
                            <div class="group p-sm">
                                <? if ($R['quantity'] <= 0) { ?>
                                    <span class="text-danger text-weight-bold all-return-label">Returned</span>
                                <? } ?>
                                <div class="row">
                                    <div class="col-md-3 p-xs pl-sm">
                                        <input type="text" readonly class="form-control input-sm productname" value="<?= $R['productname'] ?>">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="text" readonly class="form-control input-sm" value="<?= formatN($R['selling_price']) ?>">
                                        <input type="hidden" class="price inputs" disabled value="<?= $R['selling_price'] ?>">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" min="0" step="0.01" name="details[<?= $R['sdi'] ?>][rate]"
                                               class="form-control input-sm inputs discount" placeholder="discount" data-source="discount"
                                               oninput="calProductAmount(this)" disabled>
                                        <input type="hidden" name="details[<?= $R['sdi'] ?>][sinc]" class="sinc inputs" value="<?= $R['sinc'] ?>"
                                               disabled>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="text" readonly name="details[<?= $R['sdi'] ?>][return_qty]"
                                               class="form-control input-sm inputs qty" value="<?= $R['quantity'] ?>" disabled>
                                        <? if ($R['prev_return'] > 0) { ?>
                                            <small class="text-danger">returned <?= $R['prev_return'] ?></small>
                                        <? } ?>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="text" readonly name="details[<?= $R['sdi'] ?>][vat_rate]" disabled
                                               class="form-control input-sm inputs vat_rate" value="<?= $R['vat_rate'] ?>">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="text" readonly class="form-control input-sm" value="<?= formatN($R['total_amount']) ?>">
                                        <input type="hidden" readonly class="total_amount" value="<?= $R['total_amount'] ?>">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" min="0" step="0.01" class="form-control input-sm inputs discount_amount"
                                               data-source="amount" name="details[<?= $R['sdi'] ?>][return_amount]" oninput="calProductAmount(this)"
                                               disabled>
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" readonly class="form-control input-sm inputs new_total_amount_label">
                                    </div>
                                    <div class="col-md-1 p-xs d-flex align-items-center justify-content-end">
                                        <input type="checkbox" class="mr-sm" onchange="selectItem(this)"
                                               style="height: 20px;width: 20px;" title="check to return item" name="sdis[]"
                                               value="<?= $R['sdi'] ?>" <?= $R['quantity'] <= 0 ? 'disabled' : '' ?>>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 p-xs pl-sm">
                                        <span>Remarks</span>
                                        <textarea name="details[<?= $R['sdi'] ?>][remarks]" class="form-control input-sm inputs remarks" rows="2"
                                                  placeholder="remarks" disabled></textarea>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row mt-md d-flex justify-content-center">
                        <div class="col-md-3">
                            <h5 class="text-weight-bold">Credit Note Exclusive</h5>
                            <input type="text" readonly class="form-control total_excamount_label">
                            <input type="hidden" class="total_excamount" name="salereturn[total_excamount]">
                        </div>
                        <div class="col-md-3">
                            <h5 class="text-weight-bold">Credit Note Vat</h5>
                            <input type="text" readonly class="form-control total_vatamount_label">
                            <input type="hidden" class="total_vatamount" name="salereturn[total_vatamount]">
                        </div>
                        <div class="col-md-3">
                            <h5 class="text-weight-bold">Credit Note Inclusive</h5>
                            <input type="text" readonly class="form-control total_incamount_label">
                            <input type="hidden" class="total_incamount" name="salereturn[total_incamount]">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-12">
                            <h5>Description <span class="text-danger">*</span></h5>
                            <textarea class="form-control text-sm" name="salereturn[description]" rows="3" required
                                      placeholder="description"><?= $salereturn['description'] ?></textarea>
                        </div>
                    </div>
                    <div class="row mt-md d-flex justify-content-center">
                        <div class="col-md-4">
                            <button class="btn btn-success btn-block"> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script>

    function validateInputs() {
        let valid = true;
        if ($('.group.selected').length === 0) {
            triggerError('Choose at least one item!');
            return false;
        }

        //check return qty>0
        $('.group.selected').each(function (i, group) {
            let productname = $(group).find('.productname').val();
            let new_price = parseFloat($(group).find('.new_price').val());
            let price = parseFloat($(group).find('.price').val());

            //check new rate
            if (new_price >= price) {
                triggerError(`Enter valid new rate for product (${productname})`, 3000);
                $(group).find('.new_price').focus();
                valid = false;
                return false
            }
        });

        if (!valid) return false;


        $('#spinnerHolder').show();
    }

    function selectItem(obj) {
        let group = $(obj).closest('.group');
        if ($(obj).is(':checked')) {
            $(group).addClass('selected');
            $(group).find('.inputs').prop('disabled', false);
            let sinc = $(group).find('.sinc').val() === '1';
            if (sinc) {
                $(group).find('.discount').prop('readonly', true);
            } else {
                $(group).find('.discount_amount').prop('readonly', true);
            }
        } else {
            $(group).removeClass('selected');
            $(group).find('.inputs').prop('disabled', true);
        }
        calTotalAmount();
    }

    function calProductAmount(obj) {
        let group = $(obj).closest('.group');
        let source = $(obj).data('source');
        let qty = parseInt($(group).find('.qty').val());
        let price = parseFloat($(group).find('.price').val());
        let discount = parseFloat($(group).find('.discount').val());
        let vat_rate = parseFloat($(group).find('.vat_rate').val());
        let discount_amount = parseFloat($(group).find('.discount_amount').val());
        let total_amount = parseFloat($(group).find('.total_amount').val());

        let new_total_amount = 0;
        if (source === 'discount') { //price from exc
            if (discount > price) {
                triggerError("Enter valid value");
                $(group).find('.discount').val('');
                calProductAmount(obj);
                return;
            }

            discount_amount = (discount * qty * (1 + vat_rate / 100)).toFixed(2);
            new_total_amount = total_amount - discount_amount;
            $(group).find('.discount_amount').val(discount_amount);
        } else { //price from inc
            if (discount_amount > total_amount) {
                triggerError("Enter valid value");
                $(group).find('.discount_amount').val('');
                calProductAmount(obj);
                return;
            }
            new_total_amount = total_amount - discount_amount;
            let new_price = new_total_amount / (qty * (1 + vat_rate / 100));
            // let discount = truncateDecimals(price - new_price, 2);
            let discount = (price - new_price).toFixed(2);
            $(group).find('.discount').val(discount);
        }
        $(group).find('.new_total_amount_label').val(numberWithCommas(new_total_amount.toFixed(2)));
        $(group).find('.new_total_amount').val(new_total_amount);

        calTotalAmount();
    }

    function calTotalAmount() {
        let currency = $('.currencyname').val();
        let total_excamount = 0, total_vatamount = 0, total_incamount = 0;

        $('.group.selected').each(function (i, group) {
            let discount_amount = parseFloat($(group).find('.discount_amount').val());
            let vat_rate = parseFloat($(group).find('.vat_rate').val());
            let sinc = $(group).find('.sinc').val() === '1';

            let vatamount = 0, excamount = 0;
            if (sinc) {
                excamount = parseFloat((discount_amount / (1 + vat_rate / 100)).toFixed(2));
                vatamount = discount_amount - excamount;
            } else {
                excamount = parseFloat((discount_amount / (1 + vat_rate / 100)).toFixed(2));
                vatamount = parseFloat((excamount * (vat_rate / 100)).toFixed(2));
            }
            // let vatamount = discount_amount - excamount;

            total_excamount += excamount;
            total_vatamount += vatamount;
            total_incamount += discount_amount;
        });

        $('.total_excamount_label').val(`${currency} ` + numberWithCommas(total_excamount.toFixed(2)));
        $('.total_vatamount_label').val(`${currency} ` + numberWithCommas(total_vatamount.toFixed(2)));
        $('.total_incamount_label').val(`${currency} ` + numberWithCommas(total_incamount.toFixed(2)));

        $('.total_excamount').val(total_excamount.toFixed(2));
        $('.total_vatamount').val(total_vatamount.toFixed(2));
        $('.total_incamount').val(total_incamount.toFixed(2));
    }

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>