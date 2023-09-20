<style>
    .row-margin {
        margin-left: 5px;
        margin-right: 5px;
    }

    .group {
        border: 1px dashed grey;
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 15px;
        position: relative;
    }

    .group .close-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
    }

    .group .bulk-label {
        position: absolute;
        top: -14px;
        left: 20px;
        z-index: 10;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .input-error {
        border: 1px solid red;
    }

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

    .border-bottom {
        padding-bottom: 20px;
        border-bottom: 1px solid #DADADA;
    }

    .sticky-bottom {
        position: fixed;
        top: 80px;
        left: 0;
        right: 0;
        padding-bottom: 40px;
        padding-left: 10px;
        padding-right: 10px;
        background-color: white;
        box-shadow: 0 0 10px grey;
        z-index: 100;
    }

    input.productname {
        cursor: pointer !important;
        background: white !important;
    }
</style>
<header class="page-header">
    <h2><?= $grn['id'] ? 'Edit' : 'Create' ?> GRN</h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h1 class="text-weight-semibol text-danger">Please wait</h1>
    </div>
</div>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    <? if ($grn['id']) { ?>
                        Edit GRN No: <span class="text-primary"><?= $grn['id'] ?></span>
                    <? } else { ?>
                        Create GRN <?= $lpo ? "From LPO" : "" ?>
                    <? } ?>
                </h2>
            </header>
            <form action="<?= url('grns', 'save_grn_new') ?>" method="post" onsubmit="return validateInputs()">
                <input type="hidden" name="grn[id]" value="<?= $grn['id'] ?>">
                <input type="hidden" name="grn[token]" value="<?= unique_token() ?>">
                <div class="panel-body" style="padding-bottom: 70px;">
                    <div class="row">
                        <div class="col-md-12 mt-lg"><h4>Info</h4></div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label>Supplier</label>
                                <button type="button" class="btn btn-primary btn-sm" title="quick add supplier" data-toggle="modal"
                                        data-target="#quick-add-supplier-modal"><i class="fa fa-plus"></i></button>
                            </div>
                            <select id="supplierid" class="form-control" name="grn[supplierid]" required onchange="checkVATStatus()">
                                <? if ($grn) { ?>
                                    <option value="<?= $grn['supplierid'] ?>"><?= $grn['suppliername'] ?></option>
                                <? } else { ?>
                                    <option value="<?= $lpo['supplierid'] ?>"><?= $lpo['suppliername'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>LPO</label>
                            <input type="text" class="form-control" name="grn[lpoid]" placeholder="lpo number" readonly
                                   autocomplete="off" value="<?= $grn['lpoid'] ?? $lpo['id'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Supplier Invoice No</label>
                            <input id="invoiceno" type="text" class="form-control" name="grn[invoiceno]"
                                   placeholder="invoice no" autocomplete="off" value="<?= $grn['invoiceno'] ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Verification Code</label>
                            <input id="verificationcode" type="text" class="form-control" name="grn[verificationcode]"
                                   placeholder="verification code" value="<?= $grn['verificationcode'] ?>">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-3">
                            <label>Location</label>
                            <select id="locationid" class="form-control" name="grn[locid]" required>
                                <? if ($grn) { ?>
                                    <option selected value="<?= $grn['locid'] ?>"><?= $grn['locationname'] ?></option>
                                <? } elseif ($lpo) { ?>
                                    <option selected value="<?= $lpo['locationid'] ?>">
                                        <?= $lpo['locationname'] ?></option>
                                <? } else { ?>
                                    <option selected value="<?= $defaultLocation['id'] ?>">
                                        <?= $defaultLocation['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Currency</label>
                            <? if ($lpo) { ?>
                                <input id="currencyid" type="hidden" name="grn[currency_rateid]"
                                       value="<?= $lpo['currency_rateid'] ?>">
                                <input id="currency_name" type="text" class="form-control" readonly
                                       value="<?= $lpo['currencyname'] ?> - <?= $lpo['currency_description'] ?>"
                                       data-currencyname="<?= $lpo['currencyname'] ?>">
                            <? } elseif ($grn['lpoid']) { ?>
                                <input id="currencyid" type="hidden" name="grn[currency_rateid]"
                                       value="<?= $grn['currency_rateid'] ?>">
                                <input id="currency_name" type="text" class="form-control" readonly
                                       value="<?= $grn['currencyname'] ?> - <?= $grn['currency_description'] ?>"
                                       data-currencyname="<?= $grn['currencyname'] ?>">
                            <? } else { ?>
                                <select id="currencyid" class="form-control" name="grn[currency_rateid]"
                                        onchange="getExchangeRate(this)" required>
                                    <? foreach ($currencies as $c) { ?>
                                        <? if ($grn) { ?>
                                            <option <?= selected($c['rateid'], $grn['currency_rateid']) ?>
                                                    data-currencyid="<?= $c['rateid'] ?>"
                                                    data-currencyname="<?= $c['currencyname'] ?>"
                                                    data-exchange-rate="<?= $c['rate_amount'] ?>"
                                                    value="<?= $c['rateid'] ?>"><?= $c['currencyname'] ?>
                                                - <?= $c['description'] ?></option>
                                        <? } else { ?>
                                            <option <?= selected($c['base'], $basecurrency['base']) ?>
                                                    data-currencyid="<?= $c['rateid'] ?>"
                                                    data-currencyname="<?= $c['currencyname'] ?>"
                                                    data-exchange-rate="<?= $c['rate_amount'] ?>"
                                                    value="<?= $c['rateid'] ?>"><?= $c['currencyname'] ?>
                                                - <?= $c['description'] ?></option>
                                        <? } ?>
                                    <? } ?>
                                </select>
                            <? } ?>
                        </div>
                        <div class="col-md-3">
                            <label>Base Exchange Rate</label>
                            <div class="d-flex align-items-center">
                                <? if ($lpo) { ?>
                                    <input id="currency_amount" type="text" readonly class="form-control"
                                           name="grn[currency_amount]"
                                           value="<?= $lpo['currency_amount'] ?>">
                                <? } elseif ($grn['lpoid']) { ?>
                                    <input id="currency_amount" type="text" readonly class="form-control"
                                           name="grn[currency_amount]"
                                           value="<?= $grn['currency_amount'] ?>">
                                <? } else { ?>
                                    <input id="currency_amount" type="text" readonly class="form-control"
                                           name="grn[currency_amount]"
                                           value="<?= $grn['currency_amount'] ?>">
                                <? } ?>
                                <button type="button" class="btn btn-info btn-sm ml-sm" title="use current rate"
                                        onclick="fetchCurrentExchangeRate()">
                                    <i class="fa fa-refresh"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-md border-bottom">
                        <div class="col-md-3 text-center">
                            <label>VAT Registered</label>
                            <div class="checkbox d-flex justify-content-center">
                                <label class="d-flex align-items-center">
                                    <input id="vat_registered" name="grn[vat_registered]" onchange="enableVatDesc(this)"
                                           type="checkbox" value="1"
                                           style="height: 40px;width: 40px;" <?= $grn['vat_registered'] == 1 ? 'checked' : '' ?>>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <label>VAT Description</label>
                            <textarea id="vat_desc" disabled class="form-control" name="grn[vat_desc]"
                                      placeholder="VAT description"><?= $grn['vat_desc'] ?></textarea>
                        </div>
                        <? if (CS_SUPPLIER_PAYMENT) { ?>
                            <div class="col-md-3 text-center" title="check if you want to track supplier payments for this GRN">
                                <label>Track Supplier Payment</label>
                                <div class="checkbox d-flex justify-content-center">
                                    <label class="d-flex align-items-center">
                                        <input name="grn[supplier_payment]" type="checkbox" style="height: 40px;width: 40px;cursor: pointer;"
                                               value="1"
                                            <? if ($grn) { ?>
                                                <?= $grn['supplier_payment'] == 1 ? 'checked' : '' ?>
                                            <? } else { ?>
                                                checked
                                            <? } ?>>
                                    </label>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div id="paymentInfo" class="row mt-xlg">
                        <div class="col-md-12 mt-lg"><h4>Payments</h4></div>
                        <div class="col-md-3">
                            <label>Total Exclusive Amount</label>
                            <input type="text" readonly class="form-control text-weight-bold total_exc_amount_label">
                            <input type="hidden" class="total_exc_amount" name="grn[total_amount]">
                        </div>
                        <div class="col-md-3">
                            <label>VAT Amount</label>
                            <input type="text" readonly class="form-control text-weight-bold total_vat_amount_label"
                                   name="grn[grand_vatamount]">
                            <input type="hidden" class="total_vat_amount" name="grn[grand_vatamount]">
                        </div>
                        <div class="col-md-3">
                            <label>Total Inclusive Amount</label>
                            <input type="text" readonly class="form-control text-weight-bold total_inc_amount_label">
                            <input type="hidden" class="total_inc_amount" name="grn[full_amount]">
                        </div>
                        <div class="col-md-3" title="amount adjusted for total supplier invoice amount">
                            <label>Adjustment Amount</label>
                            <input type="number" step="0.01" value="<?= $grn['adjustment_amount'] ?: '0.00' ?>"
                                   class="form-control text-weight-bold adjustment_amount"
                                   name="grn[adjustment_amount]" onchange="calGrnTotalAmount()" onkeyup="calGrnTotalAmount()">
                        </div>
                    </div>
                    <div class="row mt-lg border-bottom">
                        <div class="col-md-4">
                            <label>Payment Type</label>
                            <select id="paymenttype" class="form-control" name="grn[paymenttype]"
                                    onchange="configPayment(this)">
                                <option <?= selected($grn['paymenttype'], PAYMENT_TYPE_CASH) ?>
                                        value="<?= PAYMENT_TYPE_CASH ?>"><?= PAYMENT_TYPE_CASH ?></option>
                                <option <?= selected($grn['paymenttype'], PAYMENT_TYPE_CREDIT) ?>
                                        value="<?= PAYMENT_TYPE_CREDIT ?>"><?= PAYMENT_TYPE_CREDIT ?></option>
                            </select>
                        </div>
                        <div id="credit-days" class="col-md-4" style="display: none">
                            <label>Credit days</label>
                            <input type="number" class="form-control" name="grn[credit_days]"
                                   value="<?= $grn['credit_days'] ?? '30' ?>" min="0" disabled>
                        </div>
                    </div>
                    <div class="row mt-xlg">
                        <div class="col-md-12 mt-lg"><h4>Items</h4></div>
                    </div>
                    <div class="row row-margin">
                        <div class="col-md-12 text-weight-bold text-center" style="font-size: 11pt;">
                            <div class="col-md-2">Product</div>
                            <div class="col-md-2">Unit Rate</div>
                            <div class="col-md-1">Qty</div>
                            <div class="col-md-1" title="Quantity that is being payed for to supplier">Billable Qty</div>
                            <div class="col-md-1">VAT %</div>
                            <div class="col-md-2" title="price inclusive VAT">Quick Sale Price Inc
                                (<?= $basecurrency['name'] ?>)
                            </div>
                            <div class="col-md-2">Purchase Cost</div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                    <div id="items-holder">
                        <? if ($grn) {
                            foreach ($grn['details'] as $index => $detail) { ?>
                                <div class="row-margin group">
                                    <div class="row">
                                        <button type="button" class="btn btn-warning btn-sm close-btn"
                                                title="remove item"
                                                onclick="removeItem(this)">
                                            <i class="fa fa-close"></i>
                                        </button>
                                        <div class="col-md-2 p-xs">
                                            <input type="text" readonly class="form-control productname" placeholder="search product"
                                                   value="<?= $detail['productname'] ?>" onclick="open_modal(this)">
                                            <input type="hidden" class="productid" name="productid[]" value="<?= $detail['productid'] ?>">
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="number" class="form-control inputs rate" name="rate[]" min="0"
                                                   placeholder="rate" autocomplete="off" value="<?= $detail['rate'] ?>"
                                                   onkeyup="calProductAmount(this)" step="0.01"
                                                   onchange="calProductAmount(this)" required>
                                            <input type="hidden" class="form-control inputs base_rate" value="<?= $detail['base_rate'] ?>">
                                        </div>
                                        <div class="col-md-1 p-xs" style="position: relative">
                                            <span class="text-success text-weight-bold bulk-label"
                                                  style="display: none">x <?= $detail['bulk_rate'] ?></span>
                                            <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                                   autocomplete="off" placeholder="quantity" data-source="qty"
                                                   value="<?= $detail['qty'] ?>" <?= $detail['track_expire_date'] ? 'readonly' : '' ?>
                                                   onkeyup="calProductAmount(this)"
                                                   onchange="calProductAmount(this)" required>
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <input type="number" class="form-control inputs billable_qty" name="billable_qty[]" min="0"
                                                   autocomplete="off" placeholder="billable quantity" data-source="billable"
                                                   value="<?= $detail['billable_qty'] ?>"
                                                   onkeyup="calProductAmount(this)" onchange="calProductAmount(this)" required>
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <input type="number" class="form-control inputs vat_percentage"
                                                   name="vat_percentage[]" value="<?= $detail['vat_percentage'] ?>"
                                                   placeholder="vat percent" readonly required>
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="number" class="form-control inputs quick_sale_price" name="quick_sale_price[]" readonly
                                                   ondblclick="enableQuickSale(this)" onblur="disableQuickSale(this)"
                                                   title="double click to enable input" min="0" step="0.01" placeholder="quick sale price"
                                                   autocomplete="off" onkeyup="checkQuickSalePrice(this)" value="<?= $detail['quick_sale_price'] ?>"
                                                   onchange="checkQuickSalePrice(this)">
                                            <input type="hidden" class="base_percentage" value="<?= $detail['base_percentage'] ?>">
                                            <small class="base_text" style="display: none">below suggested <span class="base_percent_text"></span>%,
                                                <span
                                                        class="text-rosepink base_incprice">0</span></small>
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="text" class="form-control inputs purchase_cost"
                                                   placeholder="total" readonly>
                                            <input type="hidden" class="inputs excamount">
                                            <input type="hidden" class="inputs vatamount">
                                            <input type="hidden" class="inputs incamount">
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <div class="checkbox">
                                                <label title="use 1 qty => <?= $detail['bulk_rate'] ?> <?= $detail['unitname'] ?>">
                                                    <input type="checkbox" class="bulk inputs" name="bulk[]" title=""
                                                           onchange="enableBulk(this)" style="height:20px;width:20px;"
                                                           value="<?= $detail['productid'] ?>">
                                                    <input type="hidden" class="bulk_rate inputs" name="bulk_rate[]"
                                                           value="<?= $detail['bulk_rate'] ?>">
                                                    <span class="ml-sm">Use Bulk</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 p-xs">
                                            <textarea rows="2" class="form-control product_description" readonly
                                                      placeholder="product description"><?= $detail['description'] ?></textarea>
                                        </div>
                                        <div class="col-md-5 col-md-offset-3 batch-holder">
                                            <? if ($detail['track_expire_date']) { ?>
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-success btn-xs add-batch-btn" onclick="addBatch(this)"
                                                            style="z-index: 10;">
                                                        <i class="fa fa-plus"></i> Add Batch
                                                    </button>
                                                </div>
                                                <table class="table table-hover" style="font-size: 10pt;">
                                                    <thead>
                                                    <tr>
                                                        <td>Batch No</td>
                                                        <td>Qty</td>
                                                        <td>Expire Date</td>
                                                        <td></td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($detail['batches'] as $bi => $batch) { ?>
                                                        <tr>
                                                            <td>
                                                                <input type="text" placeholder="Batch No"
                                                                       onkeyup="checkDuplicate()"
                                                                       class="form-control input-sm batch_no"
                                                                       name="batch[<?= $detail['productid'] ?>][batch_no][]"
                                                                       value="<?= $batch['batch_no'] ?>">
                                                            </td>
                                                            <td>
                                                                <input type="number" placeholder="Quantity" min="1"
                                                                       required onkeyup="updateQty(this)"
                                                                       onchange="updateQty(this)"
                                                                       class="form-control input-sm batch_qty"
                                                                       name="batch[<?= $detail['productid'] ?>][qty][]"
                                                                       value="<?= $batch['qty'] ?>">
                                                            </td>
                                                            <td>
                                                                <input type="date" placeholder="Expire Date" required
                                                                       class="form-control input-sm batch_expire_date"
                                                                       name="batch[<?= $detail['productid'] ?>][expire_date][]"
                                                                       value="<?= $batch['expire_date'] ?>">
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                        onclick="removeBatch(this)">
                                                                    <i class="fa fa-remove"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <? } ?>
                                                    </tbody>
                                                </table>
                                            <? } ?>
                                        </div>
                                    </div>
                                </div>
                            <? }
                        } elseif ($lpo) {
                            foreach ($lpo['details'] as $index => $detail) { ?>
                                <div class="row-margin group">
                                    <div class="row">
                                        <button type="button" class="btn btn-warning btn-sm close-btn"
                                                title="remove item"
                                                onclick="removeItem(this)">
                                            <i class="fa fa-close"></i>
                                        </button>
                                        <div class="col-md-2 p-xs">
                                            <input type="text" readonly class="form-control productname" placeholder="search product"
                                                   value="<?= $detail['productname'] ?>" onclick="open_modal(this)">
                                            <input type="hidden" class="productid" name="productid[]" value="<?= $detail['productid'] ?>">
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="number" class="form-control inputs rate" name="rate[]" min="0"
                                                   placeholder="rate" autocomplete="off" value="<?= $detail['rate'] ?>"
                                                   onkeyup="calProductAmount(this)" step="0.01"
                                                   onchange="calProductAmount(this)" required>
                                            <input type="hidden" class="form-control inputs base_rate" value="<?= $detail['base_rate'] ?>">
                                        </div>
                                        <div class="col-md-1 p-xs" style="position: relative">
                                            <span class="text-success text-weight-bold bulk-label"
                                                  style="display: none">x <?= $detail['bulk_rate'] ?></span>
                                            <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                                   autocomplete="off" placeholder="quantity" data-source="qty"
                                                <?= $detail['track_expire_date'] ? 'readonly' : '' ?>
                                                   onkeyup="calProductAmount(this)"
                                                   onchange="calProductAmount(this)" required>
                                            <i class="text-primary required-label">lpo qty <?= $detail['qty'] ?></i>
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <input type="number" class="form-control inputs billable_qty" name="billable_qty[]" min="0"
                                                   autocomplete="off" placeholder="billable quantity" data-source="billable"
                                                   value="<?= $detail['billable_qty'] ?>"
                                                   onkeyup="calProductAmount(this)" onchange="calProductAmount(this)" required>
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <input type="number" class="form-control inputs vat_percentage"
                                                   name="vat_percentage[]" value="<?= $detail['vat_rate'] ?>"
                                                   placeholder="vat percent" readonly required>
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="number" class="form-control inputs quick_sale_price" name="quick_sale_price[]" readonly
                                                   ondblclick="enableQuickSale(this)" onblur="disableQuickSale(this)"
                                                   title="double click to enable input" min="0"
                                                   step="0.01" placeholder="quick sale price" autocomplete="off"
                                                   onkeyup="checkQuickSalePrice(this)"
                                                   value="<?= $detail['quick_sale_price'] ?>"
                                                   onchange="checkQuickSalePrice(this)">
                                            <input type="hidden" class="base_percentage" value="<?= $detail['base_percentage'] ?>">
                                            <small class="base_text" style="display: none">below suggested <span class="base_percent_text"></span>%,
                                                <span
                                                        class="text-rosepink base_incprice">0</span></small>
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="text" class="form-control inputs purchase_cost"
                                                   placeholder="total" readonly>
                                            <input type="hidden" class="inputs excamount">
                                            <input type="hidden" class="inputs vatamount">
                                            <input type="hidden" class="inputs incamount">
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <div class="checkbox">
                                                <label title="use 1 qty => <?= $detail['bulk_rate'] ?> <?= $detail['unitname'] ?>">
                                                    <input type="checkbox" class="bulk inputs" name="bulk[]" title=""
                                                           onchange="enableBulk(this)" style="height:20px;width:20px;"
                                                           value="<?= $detail['productid'] ?>">
                                                    <input type="hidden" class="bulk_rate inputs" name="bulk_rate[]"
                                                           value="<?= $detail['bulk_rate'] ?>">
                                                    <span class="ml-sm">Use Bulk</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 p-xs">
                                            <textarea rows="2" class="form-control product_description" readonly
                                                      placeholder="product description"><?= $detail['description'] ?></textarea>
                                        </div>
                                        <div class="col-md-5 col-md-offset-3 batch-holder">
                                            <? if ($detail['track_expire_date']) { ?>
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-success btn-xs add-batch-btn" onclick="addBatch(this)"
                                                            style="z-index: 10;">
                                                        <i class="fa fa-plus"></i> Add Batch
                                                    </button>
                                                </div>
                                                <table class="table table-hover" style="font-size: 10pt;">
                                                    <thead>
                                                    <tr>
                                                        <td>Batch No</td>
                                                        <td>Qty</td>
                                                        <td>Expire Date</td>
                                                        <td></td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            <? } ?>
                                        </div>
                                    </div>
                                </div>
                            <? }
                        } else { ?>
                            <div class="row-margin group">
                                <div class="row">
                                    <button type="button" class="btn btn-warning btn-sm close-btn" title="remove item"
                                            onclick="removeItem(this)">
                                        <i class="fa fa-close"></i>
                                    </button>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" readonly class="form-control productname" placeholder="search product"
                                               onclick="open_modal(this)">
                                        <input type="hidden" class="productid" name="productid[]">
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="number" class="form-control inputs rate" name="rate[]" min="0"
                                               placeholder="rate" autocomplete="off" onkeyup="calProductAmount(this)"
                                               onchange="calProductAmount(this)" required step="0.01">
                                        <input type="hidden" class="form-control inputs base_rate">
                                    </div>
                                    <div class="col-md-1 p-xs" style="position: relative">
                                        <span class="text-success text-weight-bold bulk-label" style="display: none">bulk</span>
                                        <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                               autocomplete="off" placeholder="quantity" data-source="qty"
                                               onkeyup="calProductAmount(this)"
                                               onchange="calProductAmount(this)" required>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs billable_qty" name="billable_qty[]" min="0"
                                               autocomplete="off" placeholder="billable quantity" data-source="billable"
                                               onkeyup="calProductAmount(this)" onchange="calProductAmount(this)" required>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs vat_percentage"
                                               name="vat_percentage[]"
                                               placeholder="vat percent" readonly required>
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="number" class="form-control inputs quick_sale_price" name="quick_sale_price[]" readonly
                                               ondblclick="enableQuickSale(this)"
                                               onblur="disableQuickSale(this)" title="double click to enable input" min="0"
                                               step="0.01" placeholder="quick sale price" autocomplete="off"
                                               onkeyup="checkQuickSalePrice(this)"
                                               onchange="checkQuickSalePrice(this)">
                                        <input type="hidden" class="base_percentage">
                                        <small class="base_text" style="display: none">below suggested <span class="base_percent_text"></span>%, <span
                                                    class="text-rosepink base_incprice">0</span></small>
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" class="form-control inputs purchase_cost"
                                               placeholder="total" readonly>
                                        <input type="hidden" class="inputs excamount">
                                        <input type="hidden" class="inputs vatamount">
                                        <input type="hidden" class="inputs incamount">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="bulk inputs" name="bulk[]" title=""
                                                       disabled
                                                       onchange="enableBulk(this)" style="height:20px;width:20px;">
                                                <input type="hidden" class="bulk_rate inputs" name="bulk_rate[]">
                                                <span class="ml-sm">Use Bulk</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 p-xs">
                                        <textarea rows="2" class="form-control product_description" readonly
                                                  placeholder="product description"></textarea>
                                    </div>
                                    <div class="col-md-5 col-md-offset-3 batch-holder">

                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="mt-xlg border-bottom">
                        <button type="button" class="btn btn-primary" onclick="addItem()">Add Item</button>
                    </div>
                    <div class="mt-xlg d-flex justify-content-end">
                        <button class="btn btn-success btn-lg"><?= $grn ? 'Update' : 'Save' ?></button>
                    </div>
                </div>
            </form>
            <div id="bottom-amounts" class="sticky-bottom" style="display: none">
                <div class="row">
                    <div class="col-md-12 mt-lg"><h4>Payments</h4></div>
                    <div class="col-md-3">
                        <label>Total Exclusive Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_exc_amount_label">
                    </div>
                    <div class="col-md-3">
                        <label>VAT Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_vat_amount_label">
                    </div>
                    <div class="col-md-3">
                        <label>Total Inclusive Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_inc_amount_label">
                    </div>
                    <div class="col-md-3" title="amount adjusted for total supplier invoice amount">
                        <label>Adjustment Amount</label>
                        <input type="number" step="0.01" value="0.00" class="form-control text-weight-bold adjustment_amount"
                               onchange="calGrnTotalAmount()" onkeyup="calGrnTotalAmount()">
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?= component('shared/quick_add_supplier_modal.tpl.php') ?>
<?= component('grn/product_search_modal.tpl.php') ?>

<!--search modal-->


<script src="assets/js/quick_adds.js"></script>
<script>

    $(function () {
        initSelectAjax('#supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);

        $('#currencyid').trigger('change');
        enableVatDesc('#vat_registered');
        configPayment('#paymenttype');

        <?if($grn || $lpo){?>
        $('.productid').each(function (i, product) {
            calProductAmount(product);
        });
        <?}?>

        //floating total amounts
        let trackingElement = $('#paymentInfo');
        let elementTopOffset = $(trackingElement).offset().top;
        let bottomAmounts = $('#bottom-amounts');

        $(window).scroll(function () {
            let windowTop = $(window).scrollTop();
            // console.log(windowTop, elementTopOffset);
            if (windowTop > (elementTopOffset + $(trackingElement).outerHeight())) {
                $(bottomAmounts).show();
            } else {
                $(bottomAmounts).hide();
            }

        });
    });

    function validateInputs() {
        let valid = true;
        let groups = $('.group');
        if ($(groups).length === 0) {
            triggerError('Enter at least one product!');
            return false;
        }
        $(groups).each(function () {
            let rate = $(this).find('.rate').val();
            let qty = $(this).find('.qty').val();
            let billable_qty = $(this).find('.billable_qty').val();
            if (!rate) {
                $(this).find('.rate').focus();
                triggerError('Enter valid rate!');
                valid = false;
                return false;
            }
            if (!qty) {
                $(this).find('.qty').focus();
                triggerError('Enter valid qty!');
                valid = false;
                return false;
            }
            if (!billable_qty) {
                $(this).find('.billable_qty').focus();
                triggerError('Enter valid billabel qty!');
                valid = false;
                return false;
            }
        });
        if (!valid) return false;

        $('.batches').each(function () {
            if ($(this).find('tbody .batch_no').length < 1) {
                let productname = $(this).closest('.group').find('select.productid :selected').text();
                triggerError(`Product ${productname} is missing batches`, 5000);
                $(this).closest('.group').find('.productid').focus();
                valid = false;
                return false;
            }
        });

        if (!valid) return false;

        valid = checkDuplicate() ? confirm('System found there are duplicates batch numbers!\n Do you want to continue?') : true;
        if (!valid) return false;
        $('#spinnerHolder').show();
    }

    function checkVATStatus() {
        let supplier_info = $('#supplierid').select2('data')[0];
        if (supplier_info.vat_registered === '1') {
            triggerMessage('Supplier is VAT registered');
            $('#vat_registered').prop('checked', true).trigger('change');
        } else {
            triggerMessage('Supplier is NOT VAT registered');
            $('#vat_registered').prop('checked', false).trigger('change');
        }
    }

    function getExchangeRate(obj) {
        let exchange_rate = parseFloat($(obj).find(':selected').data('exchange-rate'));
        $('#currency_amount').val(exchange_rate);
        recalculateProductAmounts();
    }

    function recalculateProductAmounts() {
        let exchange_rate = parseFloat($('#currency_amount').val()) || 0;

        $('.group').each(function (i, group) {
            let base_rate = parseFloat($(group).find('.base_rate').val()) || 0;
            $(group).find('.rate').val((base_rate / exchange_rate).toFixed(2));
            calProductAmount($(group).find('.rate'));
        });
    }

    function enableVatDesc(obj) {
        if ($(obj).is(':checked')) {
            $('#vat_desc').prop('disabled', false).prop('required', true);
        } else {
            $('#vat_desc').prop('disabled', true).val('');
        }

        calGrnTotalAmount();
    }

    function configPayment(obj) {
        let creditGroup = $('#credit-days');
        if ($(obj).val() === '<?=PAYMENT_TYPE_CREDIT?>') {
            $(creditGroup).fadeIn('fast');
            $(creditGroup).find('input').prop('disabled', false);
        } else {
            $(creditGroup).fadeOut('fast');
            $(creditGroup).find('input').prop('disabled', true);
        }
    }

    function addItem() {
        let item = `<div class="row-margin group">
                                <div class="row">
                                    <button type="button" class="btn btn-warning btn-sm close-btn" title="remove item"
                                            onclick="removeItem(this)">
                                        <i class="fa fa-close"></i>
                                    </button>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" readonly class="form-control productname" placeholder="search product"
                                               onclick="open_modal(this)">
                                        <input type="hidden" class="productid" name="productid[]" onchange="fetchDetails(this)">
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="number" class="form-control inputs rate" name="rate[]" min="0"
                                               placeholder="rate" autocomplete="off" onkeyup="calProductAmount(this)"
                                               onchange="calProductAmount(this)" required step="0.01">
                                        <input type="hidden" class="form-control inputs base_rate">
                                    </div>
                                    <div class="col-md-1 p-xs" style="position: relative">
                                        <span class="text-success text-weight-bold bulk-label" style="display: none">bulk</span>
                                        <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                               autocomplete="off" placeholder="quantity" data-source="qty"
                                               onkeyup="calProductAmount(this)"
                                               onchange="calProductAmount(this)" required>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs billable_qty" name="billable_qty[]" min="0"
                                               autocomplete="off" placeholder="billable quantity" data-source="billable"
                                               onkeyup="calProductAmount(this)" onchange="calProductAmount(this)" required>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs vat_percentage"
                                               name="vat_percentage[]"
                                               placeholder="vat percent" readonly required>
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="number" class="form-control inputs quick_sale_price" name="quick_sale_price[]" readonly
                                               ondblclick="enableQuickSale(this)"
                                               onblur="disableQuickSale(this)" title="double click to enable input" min="0"
                                               step="0.01" placeholder="quick sale price" autocomplete="off"
                                               onkeyup="checkQuickSalePrice(this)"
                                               onchange="checkQuickSalePrice(this)">
                                        <input type="hidden" class="base_percentage">
                                        <small class="base_text" style="display: none">below suggested <span class="base_percent_text"></span>%, <span
                                                    class="text-rosepink base_incprice">0</span></small>
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" class="form-control inputs purchase_cost"
                                               placeholder="total" readonly>
                                        <input type="hidden" class="inputs excamount">
                                        <input type="hidden" class="inputs vatamount">
                                        <input type="hidden" class="inputs incamount">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="bulk inputs" name="bulk[]" title=""
                                                       disabled
                                                       onchange="enableBulk(this)" style="height:20px;width:20px;">
                                                <input type="hidden" class="bulk_rate inputs" name="bulk_rate[]">
                                                <span class="ml-sm">Use Bulk</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 p-xs">
                                        <textarea rows="2" class="form-control product_description" readonly
                                                  placeholder="product description"></textarea>
                                    </div>
                                    <div class="col-md-5 col-md-offset-3 batch-holder">

                                    </div>
                                </div>
                            </div>`;
        $('#items-holder').append(item);
        $("html, body").animate({scrollTop: $(document).height()}, 500);
    }

    function removeItem(obj) {
        $(obj).closest('.group').remove();
        calGrnTotalAmount();
    }

    function fetchDetails(obj) {
        let group = $('.group.active-group');
        let productid = $(obj).data('productid');
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');

        if (!productid) {
            triggerError('Product info not found');
            return;
        }
        if ($(`.group input.productid[value='${productid}']`).length > 0) {
            triggerError('Product already selected');
            return;
        }
        $(group).find('.inputs').val('');
        $(group).find('.required-label').text('');

        $(group).find('.productid').val(productid);
        $(group).find('.productname').val(productname);
        $(group).find('.product_description').val(description);

        $(group).removeClass('active-group');

        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();

        let exchange_rate = parseFloat($('#currency_amount').val() || 0);
        let locationid = $('#locationid').val();
        if (!locationid) {
            triggerError('Choose location first!');
            return;
        }

        $.get(`?module=products&action=getProductDetailsForGRN&format=json&productid=${productid}&locationid= ${locationid}`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let product = result.data;
                // console.log(product);
                $(group).find('.rate').val((product.costprice / exchange_rate).toFixed(2));
                $(group).find('.base_rate').val(product.costprice);
                $(group).find('.base_percentage').val(product.baseprice);
                $(group).find('.vat_percentage').val(product.category.vat_percent);
                $(group).find('.quick_sale_price').val(product.quicksale_price);
                if (product.bulk_unit) {
                    $(group).find('.bulk').prop('disabled', false).val(productid).closest('label')
                        .attr('title', `use 1 qty => ${product.bulk_unit.rate} ${product.single_unit.name || ''}`);
                    $(group).find('.bulk-label').text(`x ${product.bulk_unit.rate}`);
                    $(group).find('.bulk_rate').val(product.bulk_unit.rate);
                } else {
                    $(group).find('.bulk').prop('disabled', true).val('').closest('label').attr('title', ``);
                    $(group).find('.bulk-label').text(``);
                    $(group).find('.bulk_rate').val(1);
                }
                $(group).find('.batch-holder').empty();
                if (product.track_expire_date == 0) {
                    $(group).find('.qty,.billable_qty').val(1).prop('readonly', false);
                } else {
                    $(group).find('.qty').val('').prop('readonly', true);
                    addBatchTable(group);
                }
                calProductAmount($(group).find('.productid'));
            } else {
                triggerError(result.msg || "Error found", 2000)
            }
        });
    }

    function fetchCurrentExchangeRate() {
        let currency_rateid = $('#currencyid').val();
        let spinner = $('.rate_loading_spinner');

        spinner.show();
        $.get(`?module=currencies&action=getCurrentExchangeRate&format=json&rateid=${currency_rateid}`, null, function (data) {
            let result = JSON.parse(data);
            spinner.hide();
            console.log(result);
            if (result.status === 'success') {
                let rate = result.data;
                $('#currency_amount').val(rate.rate_amount);
                $('#currencyid').find(':selected').attr('data-exchange-rate', rate.rate_amount);
                triggerMessage('Rate updated', 4000);
                recalculateProductAmounts();
            } else {
                triggerError(result.msg || 'Error found!', 5000);
            }
        });
    }

    function addBatchTable(group) {
        let batchTable = `<div class="d-flex justify-content-end">
                               <button type="button" class="btn btn-success btn-xs add-batch-btn" onclick="addBatch(this)"
                                       style="z-index: 10;">
                                   <i class="fa fa-plus"></i> Add Batch
                               </button>
                           </div>
                           <table class="table table-hover" style="font-size: 10pt;">
                               <thead>
                               <tr>
                                   <td>Batch No</td>
                                   <td>Qty</td>
                                   <td>Expire Date</td>
                                   <td></td>
                               </tr>
                               </thead>
                               <tbody></tbody>
                           </table>`;
        $(group).find('.batch-holder').append(batchTable);
    }

    function addBatch(obj) {
        let product = $(obj).closest('.group').find('.productid');
        if (product.val() == null) {
            triggerError("Choose Product first");
            product.focus();
            return;
        }
        let productId = product.val();
        let parent = $(obj).closest('.batch-holder');
        let row = `<tr>
                       <td>
                           <input type="text" placeholder="Batch No" onkeyup="checkDuplicate()"
                                  class="form-control input-sm batch_no" name="batch[${productId}][batch_no][]">
                       </td>
                       <td>
                           <input type="number" placeholder="Quantity" min="1" value="" required onkeyup="updateQty(this)" onchange="updateQty(this)"
                                  class="form-control input-sm batch_qty" name="batch[${productId}][qty][]">
                       </td>
                       <td>
                           <input type="date" placeholder="Expire Date" required
                                  class="form-control input-sm batch_expire_date" name="batch[${productId}][expire_date][]">
                       </td>
                       <td>
                           <button type="button" class="btn btn-sm btn-danger" onclick="removeBatch(this)">
                               <i class="fa fa-remove"></i>
                           </button>
                       </td>
                   </tr>`;
        $(parent).find('tbody').append(row);
    }

    function checkDuplicate() {
        let batch_nos = [];
        $('.batch_no').each(function () {
            if ($(this).val()) {
                batch_nos.push($(this).val());
            }
        });

        let sorted = batch_nos.slice().sort();
        let duplicates = [];
        for (let i = 0; i < sorted.length - 1; i++) {
            if (sorted[i] === sorted[i + 1]) {
                duplicates.push(sorted[i]);
            }
        }

        $('.batch_no').each(function () {
            if ($.inArray($(this).val(), duplicates) !== -1) {
                $(this).addClass('input-error');
            } else {
                $(this).removeClass('input-error');
            }
        });

        return duplicates.length > 0;

    }

    function removeBatch(obj) {
        let tbody = $(obj).closest('tbody');
        $(obj).closest('tr').remove();
        updateQty(tbody);
    }

    function updateQty(obj) {
        let totalBatchQty = 0;
        let group = $(obj).closest('.group');
        $(obj).closest('.batch-holder tbody').find('.batch_qty').each(function () {
            totalBatchQty += Number($(this).val());
        });
        $(group).find('.qty,.billable_qty').val(totalBatchQty ? totalBatchQty : '');
        calProductAmount(obj);
    }

    function enableBulk(obj) {
        let group = $(obj).closest('.group');
        if ($(obj).is(':checked')) {
            $(group).find('.bulk-label').fadeIn();
        } else {
            $(group).find('.bulk-label').fadeOut();
        }
        calProductAmount(obj);
    }

    function calProductAmount(obj) {
        let group = $(obj).closest('.group');
        let rate = parseFloat($(group).find('.rate').val());
        let qty = parseFloat($(group).find('.qty').val());
        let billable_qty = parseFloat($(group).find('.billable_qty').val());
        let vat_percentage = parseFloat($(group).find('.vat_percentage').val());
        let bulk_rate = parseFloat($(group).find('.bulk_rate').val());

        if ($(obj).data('source') === 'qty') {
            billable_qty = qty;
            $(group).find('.billable_qty').val(billable_qty);
        } else {
            if (billable_qty < 0 || billable_qty > qty) {
                billable_qty = qty;
                $(group).find('.billable_qty').val(billable_qty);
                triggerError('Enter valid billable qty');
            }
            qty = billable_qty;
        }

        if ($(group).find('.bulk').is(':checked')) {
            qty *= bulk_rate;
        }
        //update base rate
        let exchange_rate = parseFloat($('#currency_amount').val() || 0);
        let base_rate = (rate * exchange_rate).toFixed(2);
        $(group).find('.base_rate').val(base_rate);

        checkQuickSalePrice(obj);

        let excamount = (qty * rate).toFixed(2);
        let vatamount = (qty * rate * (vat_percentage / 100)).toFixed(2);
        let incamount = (parseFloat(excamount) + parseFloat(vatamount)).toFixed(2);
        $(group).find('.excamount').val(excamount);
        $(group).find('.vatamount').val(vatamount);
        $(group).find('.incamount').val(incamount);
        $(group).find('.purchase_cost').val(numberWithCommas(incamount));
        calGrnTotalAmount();
    }


    function calGrnTotalAmount() {
        let vat_registered = $('#vat_registered').is(':checked');

        let totalAmount = 0, totalVatAmount = 0, fullAmount = 0;
        let adjustment_amount = parseFloat($('.adjustment_amount').val()) || 0;
        $('.group').each(function (i, group) {
            let excamount = parseFloat($(group).find('.excamount').val());
            let vatamount = parseFloat($(group).find('.vatamount').val());
            let incamount = parseFloat($(group).find('.incamount').val());
            totalAmount += excamount;
            totalVatAmount += vatamount;
            fullAmount += incamount;
        });
        if (!vat_registered) {
            totalVatAmount = 0;
            fullAmount = totalAmount;
        }
        fullAmount = parseFloat(fullAmount.toFixed(2)) + adjustment_amount;
        $('.total_exc_amount').val(numberWithCommas(totalAmount.toFixed(2)));
        $('.total_vat_amount').val(numberWithCommas(totalVatAmount.toFixed(2)));
        $('.total_inc_amount').val(numberWithCommas(fullAmount.toFixed(2)));

        let currencyname = '';

        <?if($lpo || $grn['lpoid']){?>

        currencyname = $('#currency_name').data('currencyname');

        <?}else{?>

        currencyname = $('#currencyid').find(':selected').data('currencyname');

        <?}?>
        $('.total_exc_amount_label').val(`${currencyname} ${numberWithCommas(totalAmount.toFixed(2))}`);
        $('.total_inc_amount_label').val(`${currencyname} ${numberWithCommas(fullAmount.toFixed(2))}`);
        $('.total_vat_amount_label').val(`${currencyname} ${numberWithCommas(totalVatAmount.toFixed(2))}`);
    }

    function enableQuickSale(obj) {
        $(obj).prop('readonly', false).focus();
    }

    function disableQuickSale(obj) {
        let quick_price = parseFloat($(obj).val()) || 0;
        if (quick_price == 0) {
            $(obj).val('').prop('readonly', true);
        }
    }


    function checkQuickSalePrice(obj) {
        let group = $(obj).closest(".group");
        let quick_sale_price = parseFloat($(group).find('.quick_sale_price').val()) || 0;
        let base_percentage = parseFloat($(group).find('.base_percentage').val()) || 0;
        let base_rate = parseFloat($(group).find('.base_rate').val());
        let vat_rate = parseFloat($(group).find('.vat_percentage').val());

        let purchase_cost = base_rate;
        let base_incprice = (purchase_cost * (1 + base_percentage / 100) * (1 + vat_rate / 100)).toFixed(2);
        base_incprice = parseFloat(base_incprice);

        $(group).find('.base_percent_text').text(base_percentage);
        $(group).find('.base_incprice').text(numberWithCommas(base_incprice));
        if (quick_sale_price < base_incprice) {
            $(group).find('.quick_sale_price').addClass('input-error');
            $(group).find('.base_text').show();
        } else {
            $(group).find('.quick_sale_price').removeClass('input-error');
            $(group).find('.base_text').hide();
        }
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
