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
    <div class="col-xs-12 col-xl-10">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Credit Note | Invoice no: <span class="text-primary"><?= $invoice['receipt_no'] ?></span> | Amount & Stock
                    return</h2>
            </header>
            <div class="panel-body">
                <form class="formProforma form-horizontal form-bordered" method="post" action="<?= url('sales_returns', 'save_credit_note') ?>"
                      onsubmit="return validateInputs()">
                    <input type="hidden" name="salereturn[id]" value="<?= $salereturn['id'] ?>">
                    <input type="hidden" name="salereturn[salesid]" value="<?= $invoice['salesid'] ?>">
                    <input type="hidden" name="salereturn[type]" value="<?= SalesReturns::TYPE_ITEM ?>">
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
                            <div class="col-md-2 text-weight-bold text-center">Rate</div>
                            <div class="col-md-1 text-weight-bold text-center">Purchased Qty</div>
                            <div class="col-md-1 text-weight-bold text-center">Return Qty</div>
                            <div class="col-md-1 text-weight-bold text-center">VAT %</div>
                            <div class="col-md-2 text-weight-bold text-center">Total Amount</div>
                            <div class="col-md-2"></div>
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
                                    <div class="col-md-2 p-xs">
                                        <input type="text" readonly class="form-control input-sm" value="<?= formatN($R['selling_price']) ?>">
                                        <input type="hidden" name="details[<?= $R['sdi'] ?>][rate]" class="price inputs" disabled
                                               value="<?= $R['selling_price'] ?>">
                                        <input type="hidden" class="incprice" value="<?= $R['incprice'] ?>">
                                        <input type="hidden" name="details[<?= $R['sdi'] ?>][sinc]" class="sinc" value="<?= $R['sinc'] ?>">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="text" readonly class="form-control input-sm inputs qty" value="<?= $R['quantity'] ?>">
                                        <? if ($R['prev_return'] > 0) { ?>
                                            <small class="text-danger">returned <?= $R['prev_return'] ?></small>
                                        <? } ?>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" name="details[<?= $R['sdi'] ?>][return_qty]" min="0"
                                               class="form-control input-sm inputs return_qty" disabled
                                               value="0" <?= $R['track_expire_date'] ? 'readonly' : '' ?> max="<?= $R['quantity'] ?>"
                                               oninput="checkQty(this)">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="text" readonly name="details[<?= $R['sdi'] ?>][vat_rate]" disabled
                                               class="form-control input-sm inputs vat_rate" value="<?= $R['vat_rate'] ?>">
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" readonly class="form-control input-sm total_amount"
                                               value="<?= formatN($R['total_amount']) ?>">
                                        <input type="hidden" name="details[<?= $R['sdi'] ?>][return_amount]" class="return_amount">
                                    </div>
                                    <div class="col-md-2 p-xs d-flex align-items-center justify-content-end">
                                        <input type="checkbox" class="mr-sm" onchange="selectItem(this)"
                                               style="height: 20px;width: 20px;" title="check to return item" name="sdis[]"
                                               value="<?= $R['sdi'] ?>" <?= $R['quantity'] <= 0 ? 'disabled' : '' ?>>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 p-xs pl-sm">
                                        <span>Remarks</span>
                                        <textarea name="details[<?= $R['sdi'] ?>][remarks]" class="form-control input-sm inputs remarks" rows="2"
                                                  placeholder="remarks"
                                                  disabled></textarea>
                                    </div>
                                    <div class="col-md-5 p-xs batch-serial">
                                        <? if ($R['track_expire_date']) { ?>
                                            <div>
                                                <span>Batches</span>
                                                <table class="table table-bordered table-condensed batch-table" style="font-size: 9pt;">
                                                    <thead>
                                                    <tr style="font-weight: bold;">
                                                        <td>Batch No</td>
                                                        <td>Sold Qty</td>
                                                        <td>Expire Date</td>
                                                        <td style="text-align: right;">Return Qty</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($R['batches'] as $bi => $batch) { ?>
                                                        <tr>
                                                            <td><?= $batch['batch_no'] ?></td>
                                                            <td>
                                                                <p class="p-none m-none"><?= $batch['batchSoldQty'] ?></p>
                                                                <? if ($batch['prev_return'] > 0) { ?>
                                                                    <small class="text-danger">returned <?= $batch['prev_return'] ?></small>
                                                                <? } ?>
                                                            </td>
                                                            <td><?= $batch['expire_date'] ?></td>
                                                            <td>
                                                                <input type="hidden" name="batches[<?= $R['sdi'] ?>][batchid][]"
                                                                       value="<?= $batch['batchId'] ?>">
                                                                <input type="number" name="batches[<?= $R['sdi'] ?>][batch_qty][]" min="0"
                                                                       max="<?= $batch['batchSoldQty'] ?>" value="0" disabled
                                                                       class="form-control input-sm inputs batch_qty"
                                                                       onchange="checkBatchQty(this)" onkeyup="checkBatchQty(this)">
                                                            </td>
                                                        </tr>
                                                    <? } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <? } ?>
                                        <? if ($R['trackserialno']) { ?>
                                            <div>
                                                <span>Serial No</span>
                                                <table class="table table-bordered table-condensed serialno-table" style="font-size: 9pt;">
                                                    <thead>
                                                    <tr>
                                                        <td>#</td>
                                                        <td>Serial no</td>
                                                        <td></td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? $scount = 1;
                                                    foreach ($R['serialnos'] as $sno) { ?>
                                                        <tr>
                                                            <td><?= $scount ?></td>
                                                            <td><?= $sno['number'] ?></td>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="inputs" title="return this serial no"
                                                                       name="serialnos[<?= $R['sdi'] ?>][snoid][]" value="<?= $sno['id'] ?>"
                                                                       style="height: 20px;width: 20px;" disabled>
                                                            </td>
                                                        </tr>
                                                        <? $scount++;
                                                    } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row mt-md d-flex justify-content-center">
                        <div class="col-md-3">
                            <h5 class="text-weight-bold">Total Exclusive</h5>
                            <input type="text" readonly class="form-control total_excamount_label">
                            <input type="hidden" class="total_excamount" name="salereturn[total_excamount]">
                        </div>
                        <div class="col-md-3">
                            <h5 class="text-weight-bold">Total Vat</h5>
                            <input type="text" readonly class="form-control total_vatamount_label">
                            <input type="hidden" class="total_vatamount" name="salereturn[total_vatamount]">
                        </div>
                        <div class="col-md-3">
                            <h5 class="text-weight-bold">Total Inclusive</h5>
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
            let return_qty = parseInt($(group).find('.return_qty').val()) || 0;

            //check qty
            if (return_qty <= 0) {
                triggerError(`Enter valid quantity for product (${productname})`, 3000);
                $(group).find('.return_qty').focus();
                valid = false;
                return false
            }

            //check serialno qty === return qty
            if ($(group).find('table.serialno-table').length > 0) {
                let selected_serialnos = $(group).find('table.serialno-table input:checkbox:checked').length;
                if (return_qty !== selected_serialnos) {
                    triggerError(`Selected serialnos for return dont match return quantity for product (${productname})`, 3000);
                    $(group).find('.return_qty').focus();
                    valid = false;
                    return false
                }
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
        } else {
            $(group).removeClass('selected');
            $(group).find('.inputs').prop('disabled', true);
        }
        calAmount();
    }

    function checkQty(obj) {
        let group = $(obj).closest('.group');
        let return_qty = parseInt($(group).find('.return_qty').val());
        let max_return_qty = parseInt($(group).find('.return_qty').attr('max'));

        if (return_qty < 0) {
            triggerError('Enter valid quantity!');
            $(group).find('.return_qty').val(0);
        } else if (return_qty > max_return_qty) {
            triggerError('Cant return more than purchased quantity');
            $(group).find('.return_qty').val(max_return_qty);
        }

        calAmount();
    }

    function calAmount() {
        let currency = $('.currencyname').val();
        let total_excamount = 0, total_vatamount = 0, total_incamount = 0;

        $('.group.selected').each(function (i, group) {
            let return_qty = parseInt($(group).find('.return_qty').val()) || 0;
            let price = parseFloat($(group).find('.price').val());
            let incprice = parseFloat($(group).find('.incprice').val());
            let vat_rate = parseFloat($(group).find('.vat_rate').val());
            let sinc = $(group).find('.sinc').val() === '1';

            let excamount = 0, vatamount = 0, incamount = 0;
            if (sinc) {
                incamount = incprice * return_qty;
                excamount = (incamount / (1 + vat_rate / 100)).toFixed(2);
                vatamount = incamount - excamount;
            } else {
                excamount = (return_qty * price).toFixed(2);
                vatamount = (return_qty * price * (vat_rate / 100)).toFixed(2);
                incamount = parseFloat(excamount) + parseFloat(vatamount);
                console.log('inc: ',incamount,'exc: ',excamount,'vat: ',vatamount);
            }
            $(group).find('.return_amount').val(incamount);
            total_excamount += parseFloat(excamount);
            total_vatamount += parseFloat(vatamount);
            total_incamount += parseFloat(incamount);
        });

        $('.total_excamount_label').val(`${currency} ` + numberWithCommas(total_excamount.toFixed(2)));
        $('.total_vatamount_label').val(`${currency} ` + numberWithCommas(total_vatamount.toFixed(2)));
        $('.total_incamount_label').val(`${currency} ` + numberWithCommas(total_incamount.toFixed(2)));

        $('.total_excamount').val(total_excamount.toFixed(2));
        $('.total_vatamount').val(total_vatamount.toFixed(2));
        $('.total_incamount').val(total_incamount.toFixed(2));
    }

    function checkBatchQty(obj) {
        let group = $(obj).closest('.group');

        let batch_qty = parseInt($(obj).val());
        let max_batch_qty = parseInt($(obj).attr('max'));

        if (batch_qty < 0) {
            triggerError('Enter valid quantity!');
            $(obj).val(0);
        } else if (batch_qty > max_batch_qty) {
            triggerError('Cant return more than purchased quantity');
            $(obj).val(max_batch_qty);
        }

        let total_batch_qty = 0;
        $(group).find('.batch_qty').each(function (i, item) {
            total_batch_qty += parseInt($(item).val()) || 0;
        });
        $(group).find('.return_qty').val(total_batch_qty);
        checkQty(obj);
    }

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>