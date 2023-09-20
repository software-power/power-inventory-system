<link rel="stylesheet" href="assets/css/floating-msg.css">

<style>
    .col-md-6 label {
        font-size: 15px;
    }

    .col-md-1 label {
        font-weight: 600;
    }

    .col-md-4 label {
        font-weight: 600;
    }

    .col-md-2 label, .col-md-3 label {
        font-weight: 600;
    }

    .col-md-2, .col-md-3, .col-md-4, .col-md-1 {
        font-size: 15px;
    }

    .col-md-2, .col-md-3 {
        border-left: 1px solid white;
    }

    .col-md-2, .col-md-3, .col-md-1, .col-md-4 {
        text-align: center;
    }

    .container-checkbox {
        display: block;
        position: relative;
        padding-left: 35px;
        cursor: pointer;
        font-size: 14px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .container-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .container-checkbox .checkmark {
        position: absolute;
        top: 3px;
        left: 34px;
        height: 19px;
        width: 36px;
        background-color: #b7b9c1;
    }

    .container-checkbox:hover input ~ .checkmark {
        background-color: #ccc;
    }

    .container-checkbox input:checked ~ .checkmark {
        background-color: #2196F3;
    }

    .container-checkbox .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .container-checkbox input:checked ~ .checkmark:after {
        display: block;
    }

    .container-checkbox .checkmark:after {
        left: 13px;
        top: 5px;
        width: 9px;
        height: 18px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .fordetails .col-md-2, .fordetails .col-md-1, .fordetails .col-md-3, .fordetails .col-md-5 {
        padding-left: 0;
        padding-right: 0;
    }

    .grn-details .col-md-6 {
        padding-left: 0;
        padding-right: 0;
    }

    .panel-body {
        padding: 0;
    }

    h5 {
        text-align: left;
    }

    th.stick {
        position: sticky;
        top: 0; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }

    .popup_container {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1042;
        overflow: hidden;
        position: fixed;
        overflow-y: scroll;
        background: rgba(11, 11, 11, 0.8);
    }

    .pop-col-holder {
        background: #ffffff;
        border-radius: 5px;
        width: 62%;
        margin: 0 auto;
        padding: 27px;
        position: relative;
        top: 136px;
    }

    .popup_info h4 {
        border-bottom: 1px solid rgba(158, 158, 158, 0.5215686274509804);
        width: 100%;
        padding-bottom: 10px;
    }

    .container-checkbox.for-select .checkmark {
        height: 32px;
        top: 0;
    }

    .container-checkbox.for-select .checkmark:after {
        top: 4px;
    }


    .for-row .container-checkbox .checkmark {
        height: 31px;
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

    .price-range, .incprice-range {
        display: none;
        background: #47a447;
        color: #ffffff;
        width: max-content;
        min-height: 62px;
        padding: 10px;
        position: absolute;
        top: -80px;
        left: 0;
        font-size: 8pt;
        border-radius: 6px;
        box-shadow: 0 0 8px grey;
    }

    #items-holder {
        transition: 0.5s;
        position: relative;
    }

    .holder-scroll {
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: inset 0 0 5px grey;
        padding: 10px;
        border-radius: 5px;
        transition: 0.2s;
    }

    .group {
        border: 1px dashed grey;
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 20px 15px 15px 15px;
        position: relative;
    }


    .non-stock .non-stock-label {
        display: block !important;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 4;
        transform: rotateZ(335deg);
    }

    .max-input-error {
        background: #d9534f;
        border: 2px solid orangered;
    }

    .max-input-warning {
        background: #d9882a;
        border: 2px solid orange;
    }

    .group.out-of-stock {
        border: 2px solid orangered !important;
    }

    .group .bulk-label {
        display: none;
        position: absolute;
        top: -17px;
        left: 20px;
        font-size: 9pt;
        z-index: 10;
    }

    .input-error {
        border: 1px solid red;
    }

    .removed {
        display: none;
    }

    .batchTableHolder {
        max-height: 350px;
        overflow: auto;
    }

    input.productname {
        cursor: pointer !important;
        background: white !important;
    }

    input[type='checkbox'].print_extra {
        width: 20px;
        height: 20px;
    }

    @media (min-width: 768px) {
        .modal-lg {
            width: 70% !important;
        }

        .modal-md {
            width: 50% !important;
        }
    }

</style>
<header class="page-header">
    <h2>
        <? if ($sale) { ?>
            Edit Sale: <span class="text-primary"><?= $sale['receipt_no'] ?></span>
        <? } elseif ($order) { ?>
            Make Sale From Order: <span class="text-primary"><?= $order['id'] ?></span>
        <? } elseif ($proforma) { ?>
            Make Sale From Proforma: <span class="text-primary"><?= $proforma['proformaid'] ?></span>
        <? } else { ?>
            Make Sales
        <? } ?>
    </h2>
</header>

<?= component('shared/product_view_modal.tpl.php') ?>
<?= component('shared/quick_add_client_modal.tpl.php') ?>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>

<div class="floating-msg hidden">
    <div class="msg-holder">
        <ul></ul>
    </div>
    <button class="btn  animated bounce"><i class="fa fa-warning"></i></button>
</div>

<form id="saleForm" action="<?= url('sales', 'save_sales_new') ?>" method="post" onsubmit="return validateInputs()">
    <input type="hidden" name="sales[id]" value="<?= $sale['id'] ?>">
    <? if (!$sale['id']) { ?><input type="hidden" name="sales[token]" value="<?= unique_token() ?>"><? } ?>
    <? $CANT_CHANGE_CLIENT = $order || $proforma || $sale['proformaid'] || $sale['previd'] ?>
    <div class="col-md-12 forSetting">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-9">
                        <h2 class="panel-title">
                            <? if ($sale) { ?>
                                Edit Sale: <span class="text-primary"><?= $sale['receipt_no'] ?></span>
                            <? } elseif ($order) { ?>
                                Make Sale From Order No:  <span class="text-primary"><?= $order['orderid'] ?></span>
                            <? } elseif ($proforma) { ?>
                                Make Sale From Proforma No: <span class="text-primary"><?= $proforma['proformaid'] ?></span>
                            <? } else { ?>
                                Make Sales
                            <? } ?>
                        </h2>
                    </div>
                    <div class="col-md-3">

                        <a href="?module=home&action=index" title="Home" class="btn"><i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <fieldset class="row-panel">
                        <div class="row">
                            <legend>Client details</legend>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <h5>Client Name</h5>
                                    <a title="Add New Client" class="btn btn-primary btn-sm ml-lg" href="#quick-add-client-modal"
                                       data-toggle="modal">
                                        <i class="fa fa-plus"></i> Add</a>
                                </div>
                                <? if ($CANT_CHANGE_CLIENT) { ?>
                                    <input id="clientid" type="hidden" name="sales[clientid]" value="<?= $client['id'] ?>">
                                    <input type="text" readonly class="form-control" value="<?= $client['name'] ?>">
                                <? } else { ?>
                                    <select onchange="getClientId(this)" id="clientid" required title="Please enter client"
                                            class="form-control" name="sales[clientid]">
                                        <? if ($client) { ?>
                                            <option selected
                                                    value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                                        <? } else { ?>
                                            <option value="" selected disabled>--Select Client--</option>
                                        <? } ?>
                                    </select>
                                <? } ?>
                                <h5 id="reseller" class="text-danger text-weight-semibold" style="display: none">Registered Reseller</h5>
                            </div>
                            <div class="col-md-2">
                                <h5>TIN No.</h5>
                                <input readonly id="tinnoid" placeholder="Mobile number" type="text"
                                       class="form-control" value="<?= $client['tinno'] ?>">
                            </div>
                            <div class="col-md-2">
                                <h5>VAT/VRN No.</h5>
                                <input readonly id="vatid" placeholder="VAT/VRN No." type="text" class="form-control"
                                       value="<?= $client['vatno'] ?>">
                            </div>
                            <div class="col-md-2">
                                <h5>Mobile</h5>
                                <input readonly id="mobileid" placeholder="Mobile number" type="text"
                                       class="form-control" value="<?= $client['mobile'] ?>">
                            </div>
                            <div class="col-md-2">
                                <h5>Address</h5>
                                <input readonly id="addressid" placeholder="Address" type="text" class="form-control"
                                       value="<?= $client['address'] ?>">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="row-panel">
                        <div class="row for-row">
                            <legend>VAT, Currency details and etc.</legend>
                            <div class="col-md-1">
                                <label for="">Delivery</label>
                                <label class="container-checkbox">
                                    <input type="checkbox" checked name="stockdelivery" value="yes">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="col-md-1" style="position: relative">
                                <div class="vat_loading_spinner"
                                     style="position: absolute;right:20%;display: none;">
                                    <object data="images/loading_spinner.svg"
                                            type="image/svg+xml" height="30"
                                            width="30"></object>
                                </div>
                                <label for="">VAT</label>
                                <label class="container-checkbox">
                                    <input id="vatstatus" type="checkbox" <?= !$sale['vat_exempted'] ? 'checked' : '' ?> name="with_vat"
                                           onchange="checkVatStatus()">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="col-md-2">
                                <label>Choose Currency</label>
                                <select id="currencyid" onchange="getExchangeRate(this)" class="form-control"
                                        name="sales[currency_rateid]">
                                    <? foreach ($currencies as $key => $currency) { ?>
                                        <? if ($sale || $order || $proforma) { ?>
                                            <option <?= selected($sale['currency_rateid'] ?? $order['currency_rateid'] ?? $proforma['currency_rateid'], $currency['rateid']); ?>
                                                    value="<?= $currency['rateid'] ?>"
                                                    data-currencyid="<?= $currency['currencyid'] ?>"
                                                    data-currencyname="<?= $currency['currencyname'] ?>"
                                                    data-exchange-rate="<?= $currency['rate_amount'] ?>"
                                                    data-currency-description="<?= $currency['description'] ?>">
                                                <?= $currency['currencyname'] ?> - <?= $currency['description'] ?>
                                            </option>
                                        <? } else { ?>
                                            <option <?= selected($currency['base'], 'yes'); ?>
                                                    value="<?= $currency['rateid'] ?>"
                                                    data-currencyid="<?= $currency['currencyid'] ?>"
                                                    data-currencyname="<?= $currency['currencyname'] ?>"
                                                    data-exchange-rate="<?= $currency['rate_amount'] ?>"
                                                    data-currency-description="<?= $currency['description'] ?>">
                                                <?= $currency['currencyname'] ?> - <?= $currency['description'] ?>
                                            </option>
                                        <? } ?>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label>Base Exchange Rate</label>
                                    <div class="rate_loading_spinner"
                                         style="display: none;">
                                        <object data="images/loading_spinner.svg"
                                                type="image/svg+xml" height="30"
                                                width="30"></object>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm" title="use current rate"
                                            onclick="fetchCurrentExchangeRate()">
                                        <i class="fa fa-refresh"></i></button>
                                </div>
                                <input id="currency_amount" type="text" readonly class="form-control text-center"
                                       name="sales[currency_amount]"
                                       value="<?= $sale['currency_amount'] ?? $order['currency_amount'] ?? $proforma['currency_amount'] ?>">
                            </div>
                            <div class="col-md-2">
                                <label>Order Number</label>
                                <input id="orderid" type="text" class="form-control text-center" placeholder="Order Number"
                                       name="sales[orderid]" readonly value="<?= $sale['orderid'] ?: $order['orderid'] ?>">
                            </div>
                            <div class="col-md-2">
                                <label>Proforma Number</label>
                                <input id="proformaid" type="text" class="form-control text-center" placeholder="Proforma Number"
                                       name="sales[proformaid]" readonly value="<?= $sale['proformaid'] ?: $proforma['proformaid'] ?>">
                                <input type="hidden" name="sales[op_reuse]" value="<?= $sale['op_reuse'] ?: isset($_GET['reuse']) ?>">
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-12">
                    <fieldset class="row-panel">
                        <legend>Store & sales person</legend>
                        <div class="col-md-4">
                            <label class="col-md-12" style="font-size: 17px;">Stock Location</label>
                            <? if (Users::can(OtherRights::approve_other_credit_invoice) && !($CANT_CHANGE_CLIENT || $sale['orderid'])) { ?>
                                <select id="locationid" onchange="getLocationStock(this)" required class="form-control"
                                        name="sales[locationid]">
                                    <? if ($defaultLocation) { ?>
                                        <option selected value="<?= $defaultLocation['id'] ?>"><?= $defaultLocation['name'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input id="locationid" type="hidden" name="sales[locationid]"
                                       value="<?= $defaultLocation['id'] ?>">
                                <input type="text" readonly class="form-control text-center"
                                       value="<?= $defaultLocation['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            <label class="col-md-12" style="text-align: center;font-size: 17px;">Sales Person</label>
                            <input type="hidden" name="sales[salespersonid]" value="<?= $salesPerson['id'] ?>">
                            <input type="text" readonly class="form-control text-center"
                                   value="<?= $salesPerson['name'] ?>">
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-12 mt-md">
                    <div><h4>Products/Items</h4></div>
                    <div class="fordetails">
                        <div class="col-md-12 p-none">
                            <div class="row">
                                <div class="col-md-3 text-weight-bold">Product Name</div>
                                <div class="col-md-1 text-weight-bold">Qty</div>
                                <div class="col-md-1 text-weight-bold">Excl Price</div>
                                <div class="col-md-1 text-weight-bold d-flex flex-column">
                                    <span>VAT %</span>
                                    <span id="vat-exempted" class="text-xs text-danger" style="display: none;">VAT Exempted</span>
                                </div>
                                <div class="col-md-1 text-weight-bold">Inc Price</div>
                                <div class="col-md-2 text-weight-bold">Exc Amount</div>
                                <div class="col-md-2 text-weight-bold">Total Amount</div>
                            </div>
                            <div id="items-holder">
                                <? if ($sale || $order || $proforma) {
                                    foreach ($sale['details'] ?? $order['details'] ?? $proforma['details'] as $index => $detail) { ?>
                                        <?= component('sale/sale_item.tpl.php', compact('detail')) ?>
                                        <?
                                    }
                                } else { ?>
                                    <div class="group">
                                        <small class="text-danger non-stock-label" style="display: none">non-stock</small>
                                        <button type="button" class="btn btn-danger"
                                                style="position: absolute;top:10px;right: 10px;"
                                                onclick="removeRow(this);">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                        <div class="row grn-details">
                                            <div class="col-md-3" style="position: relative">
                                                <input type="text" readonly required class="form-control inputs productname"
                                                       onclick="open_modal(this,'.group')" placeholder="search product">
                                                <input type="hidden" class="inputs productid" name="productid[]">
                                                <input type="hidden" class="inputs stockid" name="stockid[]">
                                                <input type="hidden" class="inputs validate_serialno">
                                                <object class="search-spinner" data="images/loading_spinner.svg"
                                                        type="image/svg+xml"
                                                        height="30" width="30"
                                                        style="position: absolute;top:0;right: 10px;display: none;"></object>
                                            </div>
                                            <div class="col-md-1" style="position: relative">
                                                <input type="text" oninput="calProductAmount(this)" autocomplete="off"
                                                       data-source="qty" placeholder="qty"
                                                       class="form-control inputs qty" name="qty[]" required>
                                                <input type="hidden" class="unitname">
                                            </div>
                                            <div class="col-md-1" style="position: relative">
                                                <div class="price-range text-left">
                                                    <input type="hidden" class="base_min_price">
                                                    <input type="hidden" class="base_suggested_price">
                                                    <div>Min: <span class="min_price"></span></div>
                                                    <div>Suggested: <span class="suggested_price"></span></div>
                                                </div>
                                                <object class="price-spinner" data="images/loading_spinner.svg"
                                                        type="image/svg+xml"
                                                        height="25" width="25"
                                                        style="position: absolute;top:0;right: 10px;display: none;"></object>
                                                <input autocomplete="off" type="text" oninput="calProductAmount(this)" name="price[]"
                                                       required
                                                       data-source="price" placeholder="Price" class="form-control inputs price" step="0.01"
                                                       title="exclusive price">
                                                <input class="hidden_cost" type="hidden" name="hidden_cost[]"/>
                                                <input class="base_price" type="hidden"/>
                                                <input class="base_hidden_cost" type="hidden"/>
                                            </div>
                                            <div class="col-md-1">
                                                <input type="text" readonly placeholder="VAT"
                                                       class="form-control text-center inputs vat_rate"
                                                       name="vat_rate[]">
                                                <input type="hidden" class="og_vat_rate">
                                            </div>
                                            <div class="col-md-1" style="position: relative">
                                                <div class="incprice-range text-left">
                                                    <input type="hidden" class="base_min_incprice">
                                                    <input type="hidden" class="base_suggested_incprice">
                                                    <div>Min: <span class="min_incprice"></span></div>
                                                    <div>Suggested: <span class="suggested_incprice"></span></div>
                                                </div>
                                                <object class="incprice-spinner" data="images/loading_spinner.svg"
                                                        type="image/svg+xml"
                                                        height="25" width="25"
                                                        style="position: absolute;top:0;right: 10px;display: none;"></object>
                                                <input autocomplete="off" type="text" oninput="calProductAmount(this)"
                                                       data-source="incprice" placeholder="inc Price" name="incprice[]"
                                                       class="form-control inputs incprice" step="0.01" title="inclusive price">
                                                <input type="hidden" name="sinc[]" class="sinc">
                                            </div>
                                            <div class="col-md-2">
                                                <input readonly type="text" placeholder="Exclusive Amount"
                                                       class="form-control text-center inputs excamountLabel">
                                            </div>
                                            <div class="col-md-2">
                                                <input readonly type="text" placeholder="Inc Amount"
                                                       class="form-control text-center inputs incamountLabel">
                                                <input type="hidden" class="inputs excamount">
                                                <input type="hidden" class="inputs vatamount">
                                                <input type="hidden" class="inputs incamount">
                                            </div>
                                            <div class="col-md-12 mt-sm">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <textarea readonly placeholder="Extra Description" name="product_description[]"
                                                                  class="form-control product_description" rows="2"></textarea>
                                                    </div>
                                                    <div class="col-md-6 d-flex justify-content-center descriptionButtons">
                                                        <input type="hidden" name="show_print[]" class="show_print" value="1">
                                                        <input type="hidden" class="combined">
                                                        <label class="d-flex align-items-center mr-sm">
                                                            <input type="checkbox" name="print_extra[]" class="print_extra" disabled
                                                                   onchange="enableDescription(this)">
                                                            <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
                                                        </label>
                                                        <button type="button" class="btn btn-default mr-sm btn-sm combineDescriptionBtn"
                                                                title="Combine description" onclick="combine_with(this)">
                                                            <i class="fa fa-compress"></i> Combine with
                                                        </button>
                                                        <button type="button"
                                                                title="Product view"
                                                                class="btn btn-default btn-sm viewProductBtn mr-sm"
                                                                data-toggle="modal"
                                                                data-target="#product-view-modal"
                                                                data-productid="">
                                                            <i class="fa fa-eye"></i> View
                                                        </button>
                                                        <div class="serialno-holder"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span id="extra-desc-approval-alert" class="text-danger text-weight-bold" style="display: none">Extra description require approval</span>
                        <input id="extra-desc-approval" type="hidden" name="extra_desc_approval">
                    </div>
                    <div class="col-md-12 mt-sm">
                        <button type="button" class="btn btn-success btn-sm ml-md" onclick="addRow()">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
                <div class="col-md-12">
                    <fieldset class="row-panel">
                        <div class="row">
                            <legend>Money Counter section for total amount, total VAT amount and full amount details
                            </legend>
                            <div class="col-md-4">
                                <label class="col-md-12">Exclusive Total Amount</label>
                                <input readonly class="form-control input-lg text-center totalExcAmountLabel"
                                       type="text" placeholder="Exc Amount"/>
                                <input type="hidden" name="sales[grand_amount]" class="totalExcAmount">
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-12">Inclusive VAT Amount</label>
                                <input readonly class="form-control input-lg text-center totalIncAmountLabel"
                                       type="text" placeholder="Total Amount"/>
                                <input type="hidden" name="sales[full_amount]" class="totalIncAmount">
                                <small id="sale-limit-alert" class="text-danger text-weight-bold" style="display: none">You exceed your sale limit</small>
                                <input id="sale-limit" type="hidden" name="exceed_limit">
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-12">Total VAT Amount</label>
                                <input readonly class="form-control input-lg text-center totalVatAmountLabel"
                                       type="text" placeholder="VAT Amount"/>
                                <input type="hidden" name="sales[grand_vatamount]" class="totalVatAmount">
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-12">
                    <fieldset class="row-panel">
                        <div class="row">
                            <legend>Section for sales remark</legend>
                            <div class="col-md-6">
                                <label class="col-md-12" style="font-size: 10pt;">Remarks</label>
                                <textarea placeholder="Invoice remarks" name="sales[description]"
                                          class="form-control text-sm" rows="3"><?= $sale['description'] ?: $order['remarks'] ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="col-md-12" style="font-size: 10pt;">Internal Remarks</label>
                                <textarea placeholder="Internal remarks" name="sales[internal_remarks]"
                                          class="form-control text-sm"
                                          rows="3"><?= $sale['internal_remarks'] ?: $order['internal_remarks'] ?></textarea>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="row-panel saveBtn-holder fieldset mb-xlg">
                        <legend>Save Sales</legend>
                        <div class="col-md-12 d-flex align-items-center">
                            <div class="col-md-3">
                                <? if ($proforma) { ?>
                                    <input type="hidden" name="sales[paymenttype]" value="<?= $proforma['paymentterms'] ?>">
                                <? } ?>
                                <select class="form-control saveoptions" name="sales[paymenttype]"
                                        onchange="saveOptions(this)" <?= $proforma ? 'disabled' : '' ?>>
                                    <option <?= selected($sale['paymenttype'] ?? $proforma['paymentterms'], PAYMENT_TYPE_CREDIT) ?>
                                            value="<?= PAYMENT_TYPE_CREDIT ?>">Credit Sales
                                    </option>
                                    <option <?= selected($sale['paymenttype'] ?? $proforma['paymentterms'], PAYMENT_TYPE_CASH) ?>
                                            value="<?= PAYMENT_TYPE_CASH ?>">Cash Sales
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="sales[receipt_method]" required>
                                    <? foreach ($reciepts as $key => $R) { ?>
                                        <option <?= selected($sale['receipt_method'], $R['name']) ?>
                                                value="<?= $R['name'] ?>"><?= $R['lable'] ?> (<?= $R['name'] ?>)
                                        </option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2 credit_days" style="display: none;">
                                <input type="number" placeholder="Credit Days (Default is 30 Days)"
                                       value="<?= $sale['credit_days'] ?? $proforma['payment_days'] ?>" name="sales[credit_days]"
                                       class="form-control" min="0">
                            </div>
                            <div class="col-md-2 need_approval" style="display: ;">
                                <label class="d-flex align-items-center" style="cursor: pointer">
                                    <input id="need-approval" type="checkbox" name="need_approval" style="width: 30px;height: 30px"
                                           onchange="needsApproval()">
                                    <span class="ml-xs">Need Approval</span>
                                </label>
                            </div>
                            <div class="col-md-2 need_approval_alert" style="display: none;">
                                <h4 class="text-white text-weight-bold bg-danger pt-sm pb-sm m-none rounded">Going For Approval</h4>
                                <input id="has-combine" type="checkbox" name="has_combine" style="display: none">
                            </div>
                        </div>
                        <? if (CS_INSTALLMENT_PAYMENT) { ?>
                            <div class="col-md-12 mt-md installment-holder">
                                <div class="col-md-3">
                                    <label class="d-flex justify-content-center align-items-center" style="cursor: pointer">
                                        <input id="installment" type="checkbox" name="sales[has_installment]"
                                               style="width: 30px;height: 30px"
                                            <?= $sale['has_installment'] ? 'checked' : '' ?> onchange="installment_payment()">
                                        <span class="ml-md">Installment Payment</span>
                                    </label>
                                </div>
                                <div class="col-md-2">
                                    <button id="installment-plan-btn" type="button" class="btn btn-primary btn-block"
                                            onclick="show_installment_plan_modal()" style="display: none">
                                        Installment Plan
                                    </button>
                                </div>
                            </div>
                        <? } ?>
                        <div class="col-md-12 mt-md">
                            <div class="col-md-12 credit_days" style="display: none;">
                                <button class="form-control btn btn-warning btn-block"><i class="fa fa-save"></i>
                                    Save - Credit Invoice
                                </button>
                            </div>
                            <div class="col-md-12 cash_sales">
                                <button id="payment-modal-btn" type="button" class="form-control btn btn-primary btn-block"
                                        data-target="#cashPaymentModal" data-toggle="modal">
                                    <i class="fa fa-money"></i>
                                    Cash - Payment
                                </button>
                                <button id="save-cash-btn" class="form-control btn btn-primary btn-block m-none">
                                    <i class="fa fa-save"></i>
                                    Save - Cash Invoice
                                </button>
                            </div>
                        </div>
                    </fieldset>

                </div>

            </div>
        </div>
    </div>

    <!--    payment modal-->
    <div class="modal fade" id="cashPaymentModal" tabindex="-1" role="dialog" aria-labelledby="cashPaymentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-center modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><i class="fa fa-money"></i> Cash Payment</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="d-flex justify-content-between ml-md mr-md">
                            <h2>TOTAL</h2>
                            <h2 id="modalTotalAmount" style="font-weight: bold;">0/=</h2>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <h4>Print Size:</h4>
                            <select class="form-control input-sm" name="sales[print_size]">
                                <? foreach ($print_sizes as $size) { ?>
                                    <option value="<?= $size ?>"><?= ucfirst($size) ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="">Received Amount</label>
                            <input type="number" class="form-control receivedCash" name="receivedCash" min="0"
                                   step="0.01" onchange="receiveAmount(this)" onkeyup="receiveAmount(this)">
                            <p id="changeHolder" style="display: none">Change: <span id="changeAmount" class="text-danger"></span></p>
                        </div>
                        <div class="col-md-6">
                            <label for="">Payment Method</label>
                            <select id="paymentMethod" class="form-control" name="payment_method" onchange="checkPaymentMethod(this)">
                                <option selected value="<?= PaymentMethods::CASH ?>">Cash</option>
                                <option value="<?= PaymentMethods::CREDIT_CARD ?>">Credit Card</option>
                            </select>
                        </div>
                    </div>
                    <div id="creditCardHolder" class="form-group" style="display: none">
                        <div class="col-md-6">
                            <label for="">Electronic Account</label>
                            <select id="electronic-account" class="form-control" name="electronic_account">
                                <option value="">-- choose account --</option>
                                <? foreach ($electronic_accounts as $acc) { ?>
                                    <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="">Reference</label>
                            <input id="credit_card_no" type="text" name="credit_card_no" class="form-control"
                                   placeholder="reference number">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 text-center">
                            <button type="button" class="btn btn-primary btn-block" data-dismiss="modal">Cancel</button>
                        </div>
                        <div class="col-md-6 text-center">
                            <button type="submit" class="btn btn-success btn-block confirmCashBtn"
                                    style="display: none;">Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= component('sale/installment_payment_modal.tpl.php', ['detail' => ['dist_plan' => $sale['dist_plan'], 'installments' => $sale['installments']]]) ?>
</form>

<!--    combine modal-->
<div class="modal fade" id="combine-modal" tabindex="-1" role="dialog" aria-labelledby="combine-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-money"></i> Combine with</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="font-size: 10pt">
                    <theady>
                        <tr>
                            <th>#</th>
                            <th>Item name</th>
                            <th>Extra description</th>
                            <th></th>
                        </tr>
                    </theady>
                    <tbody class="tbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= component('normal-order/product_search_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script>

    let INCLUDE_NON_STOCK = 'yes';
    $(function () {
        <? if (!($CANT_CHANGE_CLIENT)) { ?>
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", "Choose client");
        <?}?>

        <? if (Users::can(OtherRights::approve_other_credit_invoice) && !($CANT_CHANGE_CLIENT || $sale['orderid'])) { ?>
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", "Choose Location");
        <?}?>
        format_inputs();
        // var parent = $('.saveoptions');
        saveOptions();
        installment_payment();

        if ($('.out-of-stock').length > 0) {
            triggerError(`System found ${$('.out-of-stock').length} product(s) is out of stock!`, 10000);
        }

        initPrescriptionEntry();
        //load base currency
        <?if($order || $sale || $proforma){ //for products from order or sale editing?>
        if ($('#items-holder .group').length >= 4) $('#items-holder').addClass('holder-scroll');
        let notify_batch_changes = false;
        $('#items-holder .group').each(function (i, group) {
            if ($(group).find('.track_expire_date').val() === '1') {
                <?if($sale){?>
                refreshBatches($(group).find("button[title='refresh batches']"));
                notify_batch_changes = true;
                <?}else{?>
                distributeQtyToBatches(group);
                <?}?>
            }
        });
        if (notify_batch_changes) triggerError('System will require you to select batch quantity again for products that track expire date', 10000);
        checkErrors();

        $('.group .qty').trigger('input');
        <?if($sale['vat_exempted']){?>checkVatStatus();<?}?>
        <?}else{?>

        $('#currencyid').trigger('change');

        <?}?>
    });

    function format_inputs() {
        qtyInput('.qty, .batch_qty_out');
        thousands_separator('.price,.incprice');
    }

    function checkErrors() {
        let floating_msg = $('.floating-msg');
        $(floating_msg).find('ul').empty();
        $('#items-holder .group').each(function (i, group) {
            let productname = $(group).find('.productname').val();
            if ($(group).hasClass('out-of-stock')) {
                let item = ``;
                if ($(group).hasClass('external-product')) {
                    item = `<li data-productname="${productname}" onclick="findItem(this)" title="${productname} not found in stock, issue GRN or find it in list if already purchased"><span class="text-danger">${productname.substring(0, 10)}</span> <span class="text-weight-bold">is an external product</span></li>`;
                } else {
                    item = `<li data-productname="${productname}" onclick="findItem(this)" title="${productname}"><span class="text-danger">${productname.substring(0, 10)}</span> is out of stock</li>`;
                }

                $(floating_msg).find('ul').append(item);
                $(group).find('input,textarea').prop('disabled', true);
            }

            if ($(group).find('.max-input-warning').length > 0) {
                let item = `<li data-productname="${productname}" onclick="findItem(this)" title="${productname}"><span class="text-warning">${productname.substring(0, 10)}</span> not enough stock</li>`;
                $(floating_msg).find('ul').append(item);
            }
        });

        if ($(floating_msg).find('ul li').length > 0) {
            $(floating_msg).removeClass('hidden');
        } else {
            $(floating_msg).addClass('hidden');
        }
    }

    function findItem(obj) {
        let productname = $(obj).data('productname');
        let group = $(`input.productname[value='${productname}']`).closest('.group');
        $('#items-holder').scrollTop(0).animate({
            scrollTop: $(group).position().top - 100
        }, 'slow');
        $('html, body').animate({
            scrollTop: $('#items-holder').offset().top - 100
        }, 'slow');
    }

    $("form").keypress(function (e) {
        //Enter key
        if (e.which === 13) return false;
    });

    function validateInputs(e) { //on submit form

        let valid = true;

        if ($('.group').length === 0) {
            triggerError('Choose at least one item!');
            return false;
        }

        $('.group').each(function (i, group) {
            let productid = $(group).find('.productid').val();
            let productname = $(group).find('.productname').val();
            if (!productid) {
                triggerError(`Choose valid product`);
                $(group).find('.productname').focus();
                valid = false;
                return false;
            }

            let qty = parseInt($(group).find('.qty').val());
            if (qty < 0 || isNaN(qty)) {
                triggerError(`Enter valid quantity`);
                $(group).find('.qty').focus();
                valid = false;
                return false;
            }

            let price = removeCommas($(group).find('.price').val());
            if (isNaN(price) || price < 0) {
                triggerError(`Enter valid Price`);
                $(group).find('.price').focus();
                valid = false;
                return false;
            }

            let incprice = removeCommas($(group).find('.incprice').val());
            let min_incprice = removeCommas($(group).find('.incprice').attr('min'));
            if (isNaN(incprice) || incprice < 0) {
                triggerError(`Enter valid Price`);
                $(group).find('.incprice').focus();
                valid = false;
                return false;
            }
            if (incprice < min_incprice) {
                triggerError(`Minimum inclusive price is ${numberWithCommas(min_incprice)}`, 5000);
                $(group).find('.incprice').focus();
                valid = false;
                return false;
            }
        });
        if (!valid) return false;

        if ($('.out-of-stock').length > 0) {
            triggerError(`System found ${$('.out-of-stock').length} product(s) is out of stock!`, 5000);
            return false;
        }

        if ($('.external-product').length > 0) {
            triggerError(`System found ${$('.external-product').length} external product, issue GRN or find in list if already purchased!`, 5000);
            return false;
        }

        //validate serialnos
        $('.serial_number').each(function () {
            let parentSerialRow = $(this).closest('tr');
            let icon = $(parentSerialRow).find('td.status i');  //status icon
            if ($(icon).hasClass('fa-close')) { //not validated
                valid = false;
                let productName = $(this).closest('.group').find(".productname").val();
                triggerError(`${productName} Serial number not validated!`, 2500);
                $(this).closest('.group').find(".product_description").focus();//focus product
                $('#cashPaymentModal').modal('hide');
                return false;
            }
        });
        if (!valid) return false;

        $('.batches').each(function (i, batchHolder) {
            if ($(batchHolder).find('tbody tr:not(.removed)').length === 0) {
                let productname = $(batchHolder).closest('.group').find('.productname').val();
                triggerError(`Product ${productname}, choose at least one batch`, 5000);
                valid = false;
                return false;
            }
        });

        if (!valid) return false;


        <? if ($order) { //for order ?>
        //checking for unfulfilled order qty
        if ($('.max-input-warning').length > 0) {
            valid = confirm("System found, Some of the Quantity not fulfilled by stock. \nDo you want to Continue?");
        }
        <? } ?>
        if (!valid) return false;

        if (!validClient()) return false;

        //check receipt type
        if (!$(`select[name="sales[receipt_method]"]`).val()) {
            triggerError('Choose receipt type!', 5000);
            $(`select[name="sales[receipt_method]"]`).focus();
            return false;
        }

        if ($('#paymentMethod').val() === '<?=PaymentMethods::CREDIT_CARD?>') {
            if ($.trim($('#electronic-account').val()) === '') {
                notifyError('Electronic account is required');
                $('#electronic-account').focus();
                return false;
            }
            if ($.trim($('#credit_card_no').val()) === '') {
                notifyError('Payment reference number is required');
                $('#credit_card_no').focus();
                return false;
            }
        }


        //check installment
        if ($('.saveoptions').val() === "<?=PAYMENT_TYPE_CREDIT?>" && $('#installment').is(':checked')) {
            $(installment_plan_modal).find('tbody.tbody tr .border-danger').removeClass('border-danger');
            $(installment_plan_modal).find('tbody.tbody tr').each(function (i, tr) {
                if (!$(tr).find('.installment_date').val()) {
                    triggerError("Invalid installment plan inputs!", 5000);
                    valid = false;
                    show_installment_plan_modal();
                    $(tr).find('.installment_date').addClass('border-danger');
                    return false;
                }
                if (!$(tr).find('.installment_amount').val()) {
                    triggerError("Invalid installment plan inputs!", 5000);
                    valid = false;
                    show_installment_plan_modal();
                    $(tr).find('.installment_amount').addClass('border-danger');
                    return false;
                }
            });
            if (!valid) return false;

            let total_plan_amount = removeCommas($(installment_plan_modal).find('span.total_plan_amount').text()) || 0;
            let invoice_amount = removeCommas($('.totalIncAmount').val());
            $(installment_plan_modal).find('.invoice-amount').text(numberWithCommas(invoice_amount));
            if (total_plan_amount !== invoice_amount) {
                triggerError("Total installment plan amount does not match with total invoice amount!", 5000);
                $(installment_plan_modal).modal('show');
                return false;
            }
        }


        $('#spinnerHolder').show();
    }

    function quickAddDoctor(obj) {
        $(obj).parent().find('.new_doctor').toggle('slow');
    }

    function quickAddHospital(obj) {
        $(obj).parent().find('.new_hospital').toggle('slow');
    }

    function saveDoctor(obj) {
        let doctorInput = $(obj).closest('.new_doctor').find('.doctor');
        if (doctorInput.val().length < 3) {
            triggerError("Enter valid doctor's name");
            doctorInput.focus();
            return;
        }

        let spinner = $(obj).closest('.new_doctor').find('.loading_spinner');
        spinner.show();
        let name = doctorInput.val();
        $.post('?module=doctors&action=saveNewDoctor&format=json', {name: name}, function (data) {
            let result = JSON.parse(data);
            console.log(result);
            if (result[0].status == "error") {
                triggerError(result[0].details);
                doctorInput.focus();
            } else {
                triggerMessage(result[0].details);
                doctorInput.val('');
                $(obj).closest('.new_doctor').toggle('slow');
            }
            spinner.hide();
        });
    }

    function saveHospital(obj) {
        let hospitalInput = $(obj).closest('.new_hospital').find('.hospital');
        if (hospitalInput.val().length < 3) {
            triggerError("Enter valid hospital's name");
            hospitalInput.focus();
            return;
        }

        let spinner = $(obj).closest('.new_hospital').find('.loading_spinner');
        spinner.show();
        let name = hospitalInput.val();
        $.post('?module=hospitals&action=saveNewHospital&format=json', {name: name}, function (data) {
            let result = JSON.parse(data);
            console.log(result);
            if (result[0].status == "error") {
                triggerError(result[0].details);
                hospitalInput.focus();
            } else {
                triggerMessage(result[0].details);
                hospitalInput.val('');
                $(obj).closest('.new_hospital').toggle('slow');
            }
            spinner.hide();
        });
    }


    function saveOptions() {
        let obj = $('.saveoptions');
        let parent = $(obj).closest('fieldset');
        let savetype = $(obj).val();
        let credit_days = $(parent).find(".credit_days");

        if (savetype === "<?=PAYMENT_TYPE_CREDIT?>") {
            credit_days.show('fast');
            $('.installment-holder').show('fast');
            $(".cash_sales").hide('fast');
        } else if (savetype === "<?=PAYMENT_TYPE_CASH?>") {
            credit_days.hide('fast');
            $('.installment-holder').hide('fast');
            $(".cash_sales").show('fast');
        }
        needsApproval();
    }

    function installment_payment() {
        if ($('#installment').is(':checked')) {
            $('#installment-plan-btn').show();
        } else {
            $('#installment-plan-btn').hide();
        }
    }

    function show_installment_plan_modal() {
        let invoice_amount = $('.totalIncAmount').val();
        $(installment_plan_modal).find('.invoice-amount').text(numberWithCommas(invoice_amount));
        $(installment_plan_modal).modal('show');
    }

    function validClient() {
        let clientid = $('#clientid').val();
        if (clientid == '1' && $('.saveoptions').val() === "<?=PAYMENT_TYPE_CREDIT?>") {
            triggerError("Cash client can not be used for credit sales!", 6000);
            return false;
        }
        return true
    }

    function receiveAmount(obj) {
        let receivedAmount = parseFloat($(obj).val());
        let fullAmount = parseFloat($('.totalIncAmount').val());
        // console.log(receivedAmount, fullAmount);

        if (receivedAmount >= fullAmount) {
            $('.confirmCashBtn').show('fast');
        } else {
            $('.confirmCashBtn').hide('fast');
        }

        if (receivedAmount > fullAmount) {
            $('#changeAmount').text((receivedAmount - fullAmount).toFixed(2));
            $('#changeHolder').show('fast');
        } else {
            $('#changeAmount').text(0);
            $('#changeHolder').hide('fast');
        }

    }

    function checkPaymentMethod(obj) {
        let cardHolder = $('#creditCardHolder');
        let method = $(obj).val();
        if (method === '<?=PaymentMethods::CREDIT_CARD?>') {
            $(cardHolder).show('fast');
            $('#credit_card_no,#electronic-account').prop('required', true);
            $('#electronic-account').focus();
        } else {
            $(cardHolder).hide('fast');
            $('#credit_card_no,#electronic-account').val('').prop('required', false);
        }
    }

    $('#cashPaymentModal').on('show.bs.modal', function () {
        $('#modalTotalAmount').text(($('.totalIncAmountLabel').val()));
        $('#changeHolder').hide();
        $('.confirmCashBtn').hide();
        $('.receivedCash').val('');
    });


    function initPrescriptionEntry() {
        initSelectAjax('.prescription_doctor', "?module=doctors&action=getDoctors&format=json", "Choose doctor");
        initSelectAjax('.prescription_hospital', "?module=hospitals&action=getHospitals&format=json", "Choose hospital");
    }

    function getClientId(obj) {
        let clientId = $(obj).val();
        let client = $(obj).select2("data")[0];

        $('#clientname').val('').val(client.name);
        $('#mobileid').val('').val(client.mobile);
        $('#addressid').val('').val(client.address);
        $('#emailid').val('').val(client.email);
        $('#tinnoid').val('').val(client.tinno);
        $('#vatid').val('').val(client.vatno);
        $('#telid').val('').val(client.tel);

        client.reseller == 1 ? $('#reseller').show() : $('#reseller').hide();
    }

    function getExchangeRate(obj) {
        let exchange_rate = parseFloat($(obj).find(':selected').data('exchange-rate'));
        $('#currency_amount').val(exchange_rate);
        recalculateProductAmounts();
    }

    function recalculateProductAmounts() {
        let exchange_rate = parseFloat($('#currency_amount').val()) || 0;

        $('.group').each(function (i, group) {
            let base_price = parseFloat($(group).find('.base_price').val()) || 0;
            let base_incprice = parseFloat($(parent).find('.base_incprice').val()) || 0;
            let base_min_price = parseFloat($(group).find('.base_min_price').val()) || 0;
            let base_suggested_price = parseFloat($(group).find('.base_suggested_price').val()) || 0;
            let base_min_incprice = parseFloat($(group).find('.base_min_incprice').val()) || 0;
            let base_suggested_incprice = parseFloat($(group).find('.base_suggested_incprice').val()) || 0;
            let base_hidden_cost = parseFloat($(group).find('.base_hidden_cost').val()) || 0;
            let vat_rate = parseFloat($(group).find('.vat_rate').val());

            $(group).find('.min_price').text((base_min_price / exchange_rate).toFixed(2));
            $(group).find('.suggested_price').text((base_suggested_price / exchange_rate).toFixed(2));
            $(group).find('.min_incprice').text((base_min_incprice / exchange_rate).toFixed(2));
            $(group).find('.suggested_incprice').text((base_suggested_incprice / exchange_rate).toFixed(2));
            let price = (base_price / exchange_rate).toFixed(2);
            let incprice = base_incprice ? parseFloat((base_incprice / exchange_rate).toFixed(2)) : (price * (1 + vat_rate / 100)).toFixed(2);
            $(group).find('.price').val(price).attr('min', (base_min_price / exchange_rate).toFixed(2));
            $(group).find('.incprice').val(incprice).attr('min', (base_min_incprice / exchange_rate).toFixed(2));
            $(group).find('.hidden_cost').val((base_hidden_cost / exchange_rate).toFixed(2));
            calProductAmount($(group).find('.qty'));
        });
    }

    function fetchCurrentExchangeRate() {
        let currency_rateid = $('#currencyid').val();
        let spinner = $('.rate_loading_spinner');

        spinner.show();
        $.get(`?module=currencies&action=getCurrentExchangeRate&format=json&rateid=${currency_rateid}`, null, function (data) {
            let result = JSON.parse(data);
            spinner.hide();
            // console.log(result);
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

    function addRow() {
        let assetRow = `<div class="group">
                                        <small class="text-danger non-stock-label" style="display: none">non-stock</small>
                                        <button type="button" class="btn btn-danger"
                                                style="position: absolute;top:10px;right: 10px;"
                                                onclick="removeRow(this);">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                        <div class="row grn-details">
                                            <div class="col-md-3" style="position: relative">
                                                <input type="text" readonly required class="form-control inputs productname"
                                                       onclick="open_modal(this,'.group')" placeholder="search product">
                                                <input type="hidden" class="inputs productid" name="productid[]">
                                                <input type="hidden" class="inputs stockid" name="stockid[]">
                                                <input type="hidden" class="inputs validate_serialno">
                                                <object class="search-spinner" data="images/loading_spinner.svg"
                                                        type="image/svg+xml"
                                                        height="30" width="30"
                                                        style="position: absolute;top:0;right: 10px;display: none;"></object>
                                            </div>
                                            <div class="col-md-1" style="position: relative">
                                                <input type="text" oninput="calProductAmount(this)" autocomplete="off"
                                                       data-source="qty" placeholder="qty"
                                                       class="form-control inputs qty" name="qty[]" required>
                                                <input type="hidden" class="unitname">
                                            </div>
                                            <div class="col-md-1" style="position: relative">
                                                <div class="price-range text-left">
                                                    <input type="hidden" class="base_min_price">
                                                    <input type="hidden" class="base_suggested_price">
                                                    <div>Min: <span class="min_price"></span></div>
                                                    <div>Suggested: <span class="suggested_price"></span></div>
                                                </div>
                                                <object class="price-spinner" data="images/loading_spinner.svg"
                                                        type="image/svg+xml"
                                                        height="25" width="25"
                                                        style="position: absolute;top:0;right: 10px;display: none;"></object>
                                                <input autocomplete="off" type="text" oninput="calProductAmount(this)" name="price[]" required
                                                       data-source="price" placeholder="Price" class="form-control inputs price" step="0.01"
                                                       title="exclusive price">
                                                <input class="hidden_cost" type="hidden" name="hidden_cost[]"/>
                                                <input class="base_price" type="hidden"/>
                                                <input class="base_hidden_cost" type="hidden"/>
                                            </div>
                                            <div class="col-md-1">
                                                <input type="text" readonly placeholder="VAT"
                                                       class="form-control text-center inputs vat_rate"
                                                       name="vat_rate[]">
                                                <input type="hidden" class="og_vat_rate">
                                            </div>
                                            <div class="col-md-1" style="position: relative">
                                                <div class="incprice-range text-left">
                                                    <input type="hidden" class="base_min_incprice">
                                                    <input type="hidden" class="base_suggested_incprice">
                                                    <div>Min: <span class="min_incprice"></span></div>
                                                    <div>Suggested: <span class="suggested_incprice"></span></div>
                                                </div>
                                                <object class="incprice-spinner" data="images/loading_spinner.svg"
                                                        type="image/svg+xml"
                                                        height="25" width="25"
                                                        style="position: absolute;top:0;right: 10px;display: none;"></object>
                                                <input autocomplete="off" type="text" oninput="calProductAmount(this)"
                                                       data-source="incprice" placeholder="inc Price" name="incprice[]"
                                                       class="form-control inputs incprice" step="0.01" title="inclusive price">
                                                <input type="hidden" name="sinc[]" class="sinc">
                                            </div>
                                            <div class="col-md-2">
                                                <input readonly type="text" placeholder="Exclusive Amount"
                                                       class="form-control text-center inputs excamountLabel">
                                            </div>
                                            <div class="col-md-2">
                                                <input readonly type="text" placeholder="Inc Amount"
                                                       class="form-control text-center inputs incamountLabel">
                                                <input type="hidden" class="inputs excamount">
                                                <input type="hidden" class="inputs vatamount">
                                                <input type="hidden" class="inputs incamount">
                                            </div>
                                            <div class="col-md-12 mt-sm">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <textarea readonly placeholder="Extra Description" name="product_description[]"
                                                                  class="form-control product_description" rows="2"></textarea>
                                                    </div>
                                                    <div class="col-md-6 d-flex justify-content-center descriptionButtons">
                                                        <input type="hidden" name="show_print[]" class="show_print" value="1">
                                                        <input type="hidden" class="combined">
                                                        <label class="d-flex align-items-center mr-sm">
                                                            <input type="checkbox" name="print_extra[]" class="print_extra" disabled
                                                                   onchange="enableDescription(this)">
                                                            <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
                                                        </label>
                                                        <button type="button" class="btn btn-default mr-sm btn-sm combineDescriptionBtn"
                                                                title="Combine description" onclick="combine_with(this)">
                                                            <i class="fa fa-compress"></i> Combine with
                                                        </button>
                                                        <button type="button"
                                                                title="Product view"
                                                                class="btn btn-default btn-sm viewProductBtn mr-sm"
                                                                data-toggle="modal"
                                                                data-target="#product-view-modal"
                                                                data-productid="">
                                                            <i class="fa fa-eye"></i> View
                                                        </button>
                                                        <div class="serialno-holder"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
        $('#items-holder').append(assetRow);
        format_inputs();
        if ($('#items-holder .group').length >= 4) $('#items-holder').addClass('holder-scroll');
        $('#items-holder').animate({
            scrollTop: $('#items-holder').stop().prop("scrollHeight")
        }, 500);
    }


    let fetchTimer = null;

    function fetchProductList(obj) {
        $('.group').removeClass('active_group');
        $(obj).closest('.group').addClass('active_group');

        let locationid = $('#locationid').val();
        let term = $(obj).val();
        let proformaid = $('#proformaid').val();
        let searchSpinner = $(obj).closest('.group').find('.search-spinner');

        searchSpinner.show();
        $(productSearchModal).find('.search-term').text(term);
        $(productSearchModal).find('tbody').empty();

        if (term === '') {
            if (fetchTimer) clearTimeout(fetchTimer);
            searchSpinner.hide();
            return;
        }
        if (fetchTimer) clearTimeout(fetchTimer);
        fetchTimer = setTimeout(function () {
            $.get(`?module=stocks&action=locationStockSearch&format=json&locationid=${locationid}&term=${term}&except_proforma=${proformaid}`, null, function (data) {
                searchSpinner.hide();
                $(productSearchModal).find('tbody').empty();
                let products = JSON.parse(data);
                // console.log(products);
                // return;
                if (products.length > 0) {
                    $(productSearchModal).modal('show');
                    $.each(products, function (index, item) {
                        let small_text = '', vat_rate = '', non_stock = 0, stockid = item.id, total = item.total;
                        if (item.non_stock === '1') {
                            small_text = 'non-stock';
                            non_stock = 1;
                            vat_rate = item.vatPercent;
                            total = stockid = '';
                        }
                        let edit_product_link = ``;
                        <?if(Users::can(OtherRights::edit_product)){?>
                        edit_product_link = `<a href="?id=${item.productid}&module=products&action=product_edit"
                                                 class="btn btn-default btn-sm" title="edit product">
                                                  <i class="fa fa-pencil"></i></a>`;
                        <?}?>
                        let row = `<tr>
                                        <td>${index + 1}</td>
                                        <td>
                                            <p class="m-none">${item.name}</p>
                                            <small class="text-rosepink">${small_text}</small>
                                        </td>
                                        <td>${item.description}</td>
                                        <td>${total}</td>
                                        <td>
                                             <div class="d-flex justify-content-between">
                                                   ${edit_product_link}
                                                <button type="button" class="btn btn-default btn-sm" title="select product"
                                                        onclick="getProductDetails(this)" data-stockid="${stockid}" data-productid="${item.productid}"
                                                        data-productname="${item.name}" data-description="${item.description}" data-nonstock="${non_stock}" data-vatrate="${vat_rate}">
                                                    <i class="fa fa-check"></i></button>
                                             </div>
                                        </td>
                                    </tr>`;
                        $(productSearchModal).find('tbody').append(row);
                    });
                } else {
                    triggerError('No stock found with that name!', 5000);
                }

            })
        }, 500);
    }

    function fetchDetails(obj) {
        let group = $('.group.active-group');
        let locationid = $('#locationid').val();
        let exchange_rate = parseFloat($('#currency_amount').val()) || 0;
        $(group).removeClass('out-of-stock');
        // console.log($(obj).data());
        let stockid = $(obj).data('stockid');
        let productid = $(obj).data('productid');
        let nonstock = $(obj).data('nonstock') === 1;
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');
        let vat_rate = $(obj).data('vatrate');
        let proformaid = $('#proformaid').val();
        let searchSpinner = $(group).find('.search-spinner');
        let charge_vat = $('#vatstatus').is(':checked');

        if (!productid) {
            triggerError('Product info not found');
            return;
        }

        if ($(`.group input.productid[value='${productid}']`).length > 0) { //check duplicate
            triggerError('Product already selected');
            $(productSearchModal).modal('hide');
            $(productSearchModal).find('tbody.tbody').empty();
            $(`.group input.productid[value='${productid}']`).closest('.group').find('.productname').focus();
            return;
        }

        searchSpinner.show();

        if (nonstock) {
            $(group).addClass('non-stock');
        } else {
            $(group).removeClass('non-stock');
        }

        $(group).find('.inputs').val('');
        $(group).find('.stockid').val(stockid);
        $(group).find('.productid').val(productid);
        $(group).find('.productname').val(productname);
        $(group).find('.product_description').val(description);
        $(group).find('.vat_rate').val(vat_rate);
        $(group).find('.bulk').prop('checked', false);
        $(group).find('.print_extra').val(productid).prop('disabled', false);

        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();

        if (!nonstock) {
            $.get(`?module=products&action=getStockProductDetails&format=json&stockid=${stockid}&locationid=${locationid}&except_proforma=${proformaid}`, null, function (data) {
                searchSpinner.hide();
                let product = JSON.parse(data)[0];
                // console.log(product);
                if (product.found === 'yes') {
                    let min_price = (product.minimum / exchange_rate).toFixed(2);
                    let min_incprice = (product.incminimum / exchange_rate).toFixed(2);
                    let suggested_price = (product.maximum / exchange_rate).toFixed(2);
                    let suggested_incprice = (product.incmaximum / exchange_rate).toFixed(2);
                    let hidden_cost = (product.costprice / exchange_rate).toFixed(2);

                    $(group).find('.validate_serialno').val(product.validate_serialno);
                    $(group).find('.bulk-label').text(`x ${product.bulk_rate} ${product.unitabbr}`);
                    $(group).find('.qty')
                        .attr('max', product.quantity)
                        .attr('title', `available stock ${product.quantity}`)
                        .val(1)
                        .prop('readonly', product.track_expire_date === '1');
                    $(group).find('.unitname').val(product.unitname);
                    $(group).find('.min_price').text(min_price);
                    $(group).find('.min_incprice').text(min_incprice);
                    $(group).find('.base_min_price').val(product.minimum);
                    $(group).find('.base_min_incprice').val(product.incminimum);
                    $(group).find('.suggested_price').text(suggested_price);
                    $(group).find('.suggested_incprice').text(suggested_incprice);
                    $(group).find('.base_suggested_price').val(product.maximum);
                    $(group).find('.base_suggested_incprice').val(product.incmaximum);
                    $(group).find('.price').val(suggested_price).attr('min', min_price);
                    $(group).find('.incprice').val(suggested_incprice).attr('min', min_incprice);
                    $(group).find('.hidden_cost').val(hidden_cost);
                    $(group).find('.base_hidden_cost').val(product.costprice);
                    $(group).find('.base_price').val(product.maximum);
                    $(group).find('.vat_rate').val(charge_vat ? product.vat_rate : 0);
                    $(group).find('.og_vat_rate').val(product.vat_rate);
                    $(group).find('.bulk').closest('label')
                        .attr('title', `use 1 qty => ${product.bulk_rate} ${product.unitname}\n\nBuy by ${product.bulkname} (${product.bulk_rate}${product.unitabbr})`);
                    $(group).find('.bulk').val(stockid);
                    $(group).find('.bulk_rate').val(product.bulk_rate);
                    $(group).find('.product_description').val(product.description);
                    $(group).find('button.viewProductBtn').attr('data-productid', product.proid);

                    $(group).find('.batches').remove();
                    if (product.track_expire_date === '1') {
                        addBatchTable(group, product);
                        distributeQtyToBatches(group);
                        $(group).find('.batch_qty_out').eq(0).focus();
                    } else {
                        $(group).find('.qty').focus();
                    }

                    $(group).find('.serialno-holder').empty();
                    if (product.trackserial == 1) {
                        addSerialModal(group);
                        populateSerialTable(group);
                    }
                    calProductAmount($(group).find('.qty'));
                }
            });
        } else {
            $(group).find('.validate_serialno').val('');
            $(group).find('.bulk-label').text(``);
            $(group).find('.qty').val(1).prop('readonly', false).attr('max', '');
            $(group).find('.unitname').val('');
            $(group).find('.min_price').text(0);
            $(group).find('.min_incprice').text(0);
            $(group).find('.base_min_price').val(0);
            $(group).find('.base_min_incprice').val(0);
            $(group).find('.suggested_price').text(0);
            $(group).find('.suggested_incprice').text(0);
            $(group).find('.base_suggested_price').val(0);
            $(group).find('.base_suggested_incprice').val(0);
            $(group).find('.price').val(0).attr('min', 0);
            $(group).find('.incprice').val(0).attr('min', 0);
            $(group).find('.hidden_cost').val(0);
            $(group).find('.base_hidden_cost').val(0);
            $(group).find('.base_price').val(0);
            $(group).find('.vat_rate').val(charge_vat ? vat_rate : 0);
            $(group).find('.og_vat_rate').val(vat_rate);
            $(group).find('.bulk').closest('label').attr('title', ``);
            $(group).find('.bulk').val('');
            $(group).find('.bulk_rate').val('');
            $(group).find('.product_description').val(description);
            $(group).find('button.viewProductBtn').attr('data-productid', productid);

            $(group).find('.batches').remove();
            $(group).find('.price').focus();

            $(group).find('.serialno-holder').empty();
            searchSpinner.hide();
            calProductAmount($(group).find('.qty'));
        }
    }

    function updateQty(obj) {
        //limit selected batch qty to max qty
        let maxBatchQty = parseInt($(obj).attr('max')) || 0;
        let qty = parseInt($(obj).val()) || 0;
        if (qty > maxBatchQty) {
            triggerError('Selected quantity exceed available batch quantity!');
            $(obj).val(maxBatchQty);
            updateQty(obj);
        }

        // console.log(qty, maxBatchQty,$(obj).closest('.batches tbody').find('.batch_qty_out'));

        let totalBatchQty = 0;
        let group = $(obj).closest('.group');
        $(obj).closest('.batches tbody').find('.batch_qty_out').not('.removed').each(function () {
            totalBatchQty += parseInt($(this).val())||0;
        });
        $(group).find('.qty').val(totalBatchQty).trigger('input'); //triggers keyup event for calculations
    }

    function removeBatch(obj) {
        let tbody = $(obj).closest('tbody');

        $(obj).closest('tr').addClass('removed');
        $(obj).closest('tr').find('input').prop('disabled', true);
        $(obj).closest('tr').find('.batch_qty_out').val('');
        updateQty(obj);
    }

    //shows hidden batches
    function refreshBatches(obj) {
        $(obj).closest('.batches').find('.batch-table tbody tr').each(function (i, item) {
            setTimeout(function () {
                $(item).removeClass('removed');
                $(item).find('input').prop('disabled', false);
            }, i * 50);
        });
    }

    //clear unused batches
    function clearUnusedBatches(obj) {
        $(obj).closest('.batches').find('tbody tr').each(function (i, item) {
            let selectedBatchQty = parseInt($(item).find('.batch_qty_out').val());
            if (isNaN(selectedBatchQty) || selectedBatchQty < 1) {
                // console.log(selectedBatchQty);
                setTimeout(function () {
                    $(item).addClass('removed');
                    $(item).find('input').prop('disabled', true);
                    $(item).find('.batch_qty_out').val('')
                }, i * 50);
            }
        });
    }

    function addBatchTable(group, product) {
        // console.log(stockBatches);
        let stockId = $(group).find('.stockid').val();//from batches
        let batches = ``;
        $.each(product.batch_stock, function (i, item) {
            batches += `<tr>
                           <td>
                               <input type="hidden"
                                      name="batch[${stockId}][batchId][]"
                                      value="${item.batchId}">
                               <input type="text" readonly placeholder="batch no."
                                      class="form-control input-sm batch_no"
                                      value="${item.batch_no}">
                           </td>
                           <td>
                               <input type="number" readonly placeholder="qty in"
                                      class="form-control input-sm"
                                      value="${item.total}">
                           </td>
                           <td>
                               <input type="text" readonly class="form-control input-sm"
                                      value="${item.expire_date}">
                           </td>
                           <td>
                               <input type="text" placeholder="qty out"
                                      name="batch[${stockId}][qty_out][]"
                                      min="1" max="${item.total}" required
                                      class="form-control input-sm batch_qty_out"
                                      oninput="updateQty(this)">
                           </td>
                           <td>
                               <button type="button" class="btn btn-sm btn-warning"
                                       onclick="removeBatch(this)">
                                   <i class="fa fa-minus"></i>
                               </button>
                           </td>
                       </tr>`;
        });

        let prescriptions = ``;
        if (product.prescription_required == 1) {
            prescriptions = `<div class="col-md-12">
                                    <div class="col-md-5">
                                        <label>Prescription Doctor <span
                                                    class="text-danger">*</span></label>
                                        <button type="button" onclick="quickAddDoctor(this)"
                                                title="Quick add doctor"
                                                class="btn btn-primary btn-xs ml-lg">+ Add
                                        </button>
                                        <div class="col-md-12 new_doctor mb-md"
                                             style="padding:0;display: none;">
                                            <div class="col-md-10" style="padding:0;position: relative">
                                                <div class="loading_spinner"
                                                     style="position: absolute;top:5px;right: 10px;display: none;">
                                                    <object data="images/loading_spinner.svg"
                                                            type="image/svg+xml" height="30"
                                                            width="30"></object>
                                                </div>
                                                <input type="text" class="form-control doctor"
                                                       placeholder="Doctor's name">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="saveDoctor(this)"
                                                        title="Quick add doctor"
                                                        class="btn btn-success btn-xs ml-sm">Save
                                                </button>
                                            </div>
                                        </div>
                                        <select title="Choose doctor"
                                                class="form-control prescription_doctor"
                                                name="prescription[${stockId}][doctor]" required>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-md-offset-1">
                                        <label>Prescription Hospital <span class="text-danger">*</span></label>
                                        <button type="button" onclick="quickAddHospital(this)"
                                                title="Quick add Hospital"
                                                class="btn btn-primary btn-xs ml-lg">+ Add
                                        </button>
                                        <div class="col-md-12 new_hospital mb-md"
                                             style="padding:0;display: none;">
                                            <div class="col-md-10" style="padding:0;position: relative">
                                                <div class="loading_spinner"
                                                     style="position: absolute;top:5px;right: 10px;display: none;">
                                                    <object data="images/loading_spinner.svg"
                                                            type="image/svg+xml" height="30"
                                                            width="30"></object>
                                                </div>
                                                <input type="text" class="form-control hospital"
                                                       placeholder="hospital's name">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="saveHospital(this)"
                                                        title="Quick add doctor"
                                                        class="btn btn-success btn-xs ml-sm">Save
                                                </button>
                                            </div>
                                        </div>
                                        <select title="Choose hospital"
                                                class="form-control prescription_hospital"
                                                name="prescription[${stockId}][hospital]" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-md">
                                    <div class="col-md-2">
                                        <label class="checkbox-inline" title="Check if is referral">
                                            <input type="checkbox" name="prescription[${stockId}][referred]"
                                                   value="1">Referred?
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12 mt-sm">
                                    <label>Prescription <span class="text-danger">*</span></label>
                                    <textarea name="prescription[${stockId}][text]" class="form-control text-sm"
                                              rows="4" required></textarea>
                                </div>`;
        }

        let batchTable = `<div class="row pt-xs batches">
                            <div class="col-md-6 prescription_container">
                                ${prescriptions}
                            </div>
                          <div class="col-md-6">
                                <div class="col-md-12 mb-xs"
                                     style="display: flex;justify-content: end;">
                                    <button title="clear unused" type="button"
                                            class="btn btn-warning btn-sm mr-md"
                                            onclick="clearUnusedBatches(this)">
                                        <i class="fa fa-filter"></i>
                                    </button>
                                    <button title="refresh batches" type="button"
                                            class="btn btn-info btn-sm"
                                            onclick="refreshBatches(this)">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                                <div class="col-md-12 batchTableHolder">
                                    <table class="table table-condensed table-bordered batch-table" style="font-size: 10pt;">
                                  <thead>
                                  <tr>
                                      <th>Batch No</th>
                                      <th>Stock Qty</th>
                                      <th>Expire Date</th>
                                      <th>Sell Qty</th>
                                      <th></th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  ${batches}
                                  </tbody>
                              </table>
                                </div>
                          </div>
                      </div>`;
        $(group).append(batchTable);
        initPrescriptionEntry();
        format_inputs();
    }

    //distributes qty to stock batches
    function distributeQtyToBatches(group) {
        let remainQty = parseInt($(group).find('.qty').val()) || 0;

        //clear inputs first
        $(group).find('.batch_qty_out').val('');

        //loop until remainQty is zero
        $(group).find('.batch-table tbody tr').each(function () {
            let batchQty = parseInt($(this).find('.batch_qty_out').attr('max'));

            if (batchQty >= remainQty) { //if batch qty covers whole qty
                $(this).find('.batch_qty_out').val(remainQty);
                $(this).removeClass('removed');
                $(this).find('input').prop('disabled', false);
                return false;
            } else {
                $(this).find('.batch_qty_out').val(batchQty);
                $(this).removeClass('removed');
                $(this).find('input').prop('disabled', false);
                remainQty -= batchQty;
            }


            if (remainQty === 0) return false; //break the loop
        });
        clearUnusedBatches($(group).find('.batch-table tbody'));
    }

    function addSerialModal(group) { //obj => grn detail row
        let stockid = $(group).find('.stockid').val();
        let validate_serialno = $(group).find('.validate_serialno').val();
        let productname = $(group).find('.productname').val();
        let serialModal = `<button type="button" class="btn btn-default serialBtn" title="Serial numbers"
                                data-toggle="modal" data-target="#serialModal${stockid}">
                                <i class="fa fa-barcode"></i> Serial no
                            </button>
                            <div class="modal fade serial-modal" id="serialModal${stockid}" tabindex="-1" role="dialog" aria-labelledby="serialModal${stockid}"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-center">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title productName">Serial No:  <span class="text-primary">${productname}</span></h4>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-danger">${validate_serialno == 1 ? 'Validates from Stock' : 'Enter manually'}</p>
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Serial Number</td>
                                                    <td>Status</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
        $(group).find('.serialno-holder').append(serialModal);
    }

    function populateSerialTable(group) { //group => .group
        let qty = parseInt($(group).find('.qty').val());
        let stockid = $(group).find('.stockid').val();
        $(group).find('.serial-modal tbody tr').remove();
        // console.log(qty, 'stockid', stockid);
        for (let i = 0; i < qty; i++) {
            let row = `<tr>
                           <td>${i + 1}</td>
                           <td>
                               <input type="text" class="form-control input-sm serial_number" name="serialno[${stockid}][serial_number][]"
                                   autocomplete="off" onchange="validateSerialNo(this)">
                           </td>
                           <td class="status" style="text-align: center;vertical-align: middle;">
                               <object class="validate-spinner" data="images/loading_spinner.svg"
                                style="display: none" type="image/svg+xml" height="25" width="25"></object>
                               <i class="fa fa-close text-danger text-weight-bold" style="display: none"></i>
                           </td>
                       </tr>`;
            $(group).find('.serial-modal tbody').append(row);
        }
    }

    let serialValidateTimer = null;

    function validateSerialNo(obj) {
        let parentSerialRow = $(obj).closest('tr');
        let spinner = $(parentSerialRow).find('.validate-spinner');


        let icon = $(parentSerialRow).find('.status i');
        icon.hide();
        icon.addClass('fa-close text-danger');
        icon.removeClass('fa-check text-success');

        if (duplicateSerialNo()) {
            triggerError('Duplicate serial no found!', 5000);
            return;
        }


        spinner.show();
        if (serialValidateTimer) clearTimeout(serialValidateTimer);
        serialValidateTimer = setTimeout(function () {
            let number = $.trim($(obj).val());
            $(obj).val(number);
            let stockid = $(obj).closest('.group').find(`.stockid`).val();
            console.log(number, 'stockid', stockid);
            //ajax query
            $.get(`?module=serialnos&action=validateSerialno&format=json&number=${number}&stockid=${stockid}`, null, function (data) {
                let result = JSON.parse(data);
                spinner.hide();
                console.log(result);
                if (result.status === 'success') {
                    triggerMessage(result.message);
                    parentSerialRow.attr('title', result.message);
                    icon.removeClass('fa-close text-danger');
                    icon.addClass('fa-check text-success');
                } else {
                    triggerError(result.message);
                    parentSerialRow.attr('title', result.message);
                }
                icon.show();
            });
        }, 250);
    }

    function duplicateSerialNo() {
        let serial_numbers = [];
        $('.serial_number').each(function () {
            let sno = $.trim($(this).val());
            if (sno) {
                serial_numbers.push(sno);
            }
        });

        let sorted = serial_numbers.slice().sort();
        let duplicates = [];
        for (let i = 0; i < sorted.length - 1; i++) {
            if (sorted[i] === sorted[i + 1]) {
                duplicates.push(sorted[i]);
            }
        }

        $('.serial_number').each(function () {
            let sno = $.trim($(this).val());
            if ($.inArray(sno, duplicates) !== -1) {
                $(this).addClass('input-error');
            } else {
                $(this).removeClass('input-error');
            }
        });
        return duplicates.length > 0;
    }

    function removeRow(obj) {
        let group = $(obj).closest('.group');
        let combined = $(group).find('.show_print').val() == '0' || $(group).find('.combined').val() == '1';
        if (combined) {
            triggerError('Item combined, can not be removed!');
            return;
        }
        $(group).remove();
        if ($('#items-holder .group').length < 4) $('#items-holder').removeClass('holder-scroll');
        checkErrors();
        calTotalAmount();
    }

    function getLocationStock(obj) {
        $('.group').remove();
        addRow();
    }

    function generateBarcode(element, bacodeText) {
        if (bacodeText) {
            JsBarcode(element, bacodeText, {
                displayValue: true
            });
        }
    }

    function enableDescription(obj) {
        if ($(obj).is(':checked')) {
            $(obj).closest('.group').find('.product_description').prop('readonly', false);
        } else {
            $(obj).closest('.group').find('.product_description').prop('readonly', true);
        }
        needsApproval();
    }

    function combine_with(obj) {
        let group = $(obj).closest('.group');
        let productid = $(group).find('.productid').val();
        if (!productid) {
            triggerError('Choose product first');
            return;
        }
        if ($(group).find('.show_print').val() !== '1') {
            triggerError('Item Already combined');
            return;
        }

        let combineModal = $('#combine-modal');
        $(combineModal).find('tbody.tbody').empty();
        let count = 1;
        $(`.group input.show_print[value='1']`).not($(group).find('.show_print')).each(function (i, item) {
            let itemGroup = $(item).closest('.group');
            let combinewithid = $(itemGroup).find('.productid').val();
            if (combinewithid) {
                let productname = $(itemGroup).find('.productname').val();
                let description = $(itemGroup).find('.product_description').val();
                let row = `<tr>
                            <td>${count}</td>
                            <td>${productname}</td>
                            <td>${description}</td>
                            <td>
                                <button type="button" data-productid="${productid}" data-combinewithid="${combinewithid}" class="btn btn-default btn-sm" onclick="combineDescription(this)">Select</button>
                            </td>
                        </tr>`;
                $(combineModal).find('tbody.tbody').append(row);
                count++;
            }
        });
        $(combineModal).modal('show');
    }

    function needsApproval() {
        check_sale_limit();
        check_extra_desc_approval();

        let need_approve = $('#has-combine').is(':checked')
            || $('#need-approval').is(':checked')
            || $('#sale-limit').val() === '1'
            || $('#extra-desc-approval').val() === '1'
            || !$('#vatstatus').is(':checked');
        if (need_approve) {
            $('.need_approval_alert').show();
            $('#payment-modal-btn').hide();
            $('#save-cash-btn').show();
        } else {
            $('.need_approval_alert').hide();
            $('#payment-modal-btn').show();
            $('#save-cash-btn').hide();
        }
    }

    function check_sale_limit() {
        let sale_limit = parseFloat(`<?=$salesPerson['sale_limit']?>`);
        if (sale_limit <= 0) return;
        let total_amount = parseFloat($('.totalIncAmount').val());
        let currency_amount = parseFloat($('#currency_amount').val());
        // console.log(total_amount,currency_amount,sale_limit);
        total_amount *= currency_amount;
        if (total_amount > sale_limit && $(`[name="sales[paymenttype]"]`).val() === `<?=PAYMENT_TYPE_CASH?>`) {
            $('#sale-limit-alert').show();
            $('#sale-limit').val('1');
        } else {
            $('#sale-limit-alert').hide();
            $('#sale-limit').val('');
        }
    }

    function check_extra_desc_approval() {
        let check_extra_approval = $(`input:checkbox.print_extra:checked`).length>0 && `<?=$salesPerson['extra_desc_approval']?>`===`1`;
       if(check_extra_approval){
           $('#extra-desc-approval-alert').show();
           $('#extra-desc-approval').val('1');
       } else {
           $('#extra-desc-approval-alert').hide();
           $('#extra-desc-approval').val('');
       }
    }

    function combineDescription(obj) {

        $('#has-combine').prop('checked', true);
        $('#currencyid').find('option').not(':selected').prop('disabled', true);

        let combinewithid = $(obj).data('combinewithid');
        let productid = $(obj).data('productid');

        let group = $(`.group input.productid[value='${productid}']`).closest('.group');
        let combinegroup = $(`.group input.productid[value='${combinewithid}']`).closest('.group');

        let group_min_incprice = parseFloat($(group).find('.incprice').attr('min'));
        let group_qty = parseInt($(group).find('.qty').val());
        let group_min_incamount = group_qty * group_min_incprice;
        let group_incamount = parseFloat($(group).find('.incamount').val());

        let combine_min_incprice = parseFloat($(combinegroup).find('.incprice').attr('min'));
        let combine_qty = parseInt($(combinegroup).find('.qty').val());
        let combine_vat_rate = parseFloat($(combinegroup).find('.vat_rate').val());
        let combine_min_incamount = combine_min_incprice * combine_qty;
        let combine_incamount = parseFloat($(combinegroup).find('.incamount').val());
        $(combinegroup).find('.combined').val(1);
        //disable inputs
        $(group).find('.price, .incprice').val(0).attr('min', 0).prop('readonly', true);
        $(group).find('.productname, .qty, .product_description, .batch_qty_out').prop('readonly', true);
        $(group).find('.show_print').val(0);
        $(group).find('.print_extra').prop('disabled', true);
        $(group).find('.print_extra,.combineDescriptionBtn').prop('disabled', true);
        calProductAmount($(group).find('.incprice'), false);

        // if (combinegroup_sinc == 1) {
        combine_incamount += group_incamount;
        combine_min_incamount += group_min_incamount;
        let combine_incprice = combine_incamount / combine_qty;
        let combine_price = (combine_incprice / (1 + combine_vat_rate / 100)).toFixed(2);
        combine_min_incprice = combine_min_incamount / combine_qty;
        let combine_min_price = (combine_min_incprice / (1 + combine_vat_rate / 100)).toFixed(2);
        $(combinegroup).find('.incprice').val(combine_incprice.toFixed(2)).attr('min', combine_min_incprice.toFixed(2));
        $(combinegroup).find('.price').val(combine_price).attr('min', combine_min_price);
        $(combinegroup).find('.productname, .qty, .batch_qty_out').prop('readonly', true);
        //popups
        $(combinegroup).find('.min_incprice').text(combine_min_incprice.toFixed(2));
        $(combinegroup).find('.suggested_incprice').text(combine_incprice.toFixed(2));
        $(combinegroup).find('.min_price').text(combine_min_price);
        $(combinegroup).find('.suggested_price').text(combine_price);
        calProductAmount($(combinegroup).find('.incprice'), false);
        // } else {
        //     combine_price += price;
        //     $(combinegroup).find('.price').val(combine_price);
        //     calProductAmount($(combinegroup).find('.price'),false);
        // }

        let combineModal = $('#combine-modal');
        $(combineModal).find('tbody.tbody').empty();
        $(combineModal).modal('hide');
        // if (combinegroup_sinc == 1) {
        $(combinegroup).find('.incprice').focus();
        // } else {
        //     $(combinegroup).find('.price').focus();
        // }

    }

    let finalizeTimer = null;
    let finalizeTime = 1000;

    function calProductAmount(obj, timer = true) {
        let group = $(obj).closest('.group');
        let price = removeCommas($(group).find('.price').val());
        let incprice = removeCommas($(group).find('.incprice').val());
        let min_price = parseFloat($(group).find('.price').attr('min'));
        let min_incprice = parseFloat($(group).find('.incprice').attr('min'));
        let qty = parseInt($(group).find('.qty').val());
        let max = parseInt($(group).find('.qty').attr('max'));
        let unitname = $(group).find('.unitname').val();
        let source = $(obj).data('source');
        let sinc = $(group).find('.sinc').val() === '1';

        let charge_vat = $('#vatstatus').is(':checked');

        let vat_rate = charge_vat ? parseFloat($(group).find('.vat_rate').val()) : 0;
        let nonstock = $(group).hasClass('non-stock');

        if (!nonstock && qty > max) {
            triggerError(`No enough stock available!, Only ${max} ${unitname} remains`, 5000);
            $(group).find('.qty').val('').focus();
            calProductAmount(obj);
            return;
        }
        if (source === 'qty') {
            populateSerialTable(group);
            finalizeCal();
        } else if (source === 'price') { //price
            if (!nonstock) $(group).find('.price-range').fadeIn();
            $(group).find('.price-spinner').show();
            incprice = (price * (1 + vat_rate / 100)).toFixed(2);
            $(group).find('.incprice').val(incprice);
            $(group).find('.sinc').val('');
            sinc = false;
            let callback = function () {
                if (price < min_price) {
                    triggerError(`Price cant be below ${min_price}`);
                    $(group).find('.price').val(min_price);
                    incprice = (min_price * (1 + vat_rate / 100)).toFixed(2);
                    $(group).find('.incprice').val(incprice);
                    calProductAmount($(group).find('.qty'));
                } else {
                    finalizeCal();
                }
            };
            if (timer) {
                if (finalizeTimer) clearTimeout(finalizeTimer);
                finalizeTimer = setTimeout(callback, finalizeTime)
            } else {
                callback();
            }
        } else if (source === 'incprice') { //inclusive price
            if (!nonstock) $(group).find('.incprice-range').fadeIn();
            $(group).find('.incprice-spinner').show();
            price = (incprice / (1 + vat_rate / 100)).toFixed(2);
            $(group).find('.price').val(price);
            $(group).find('.sinc').val(1);
            sinc = true;
            let callback = function () {
                if (incprice < min_incprice) {
                    triggerError(`Inclusive price cant be below ${min_incprice}`);
                    $(group).find('.incprice').val(min_incprice);
                    price = (min_incprice / (1 + vat_rate / 100)).toFixed(2);
                    $(group).find('.price').val(price);
                    calProductAmount($(group).find('.qty'));
                } else {
                    finalizeCal();
                }
            };
            if (timer) {
                if (finalizeTimer) clearTimeout(finalizeTimer);
                finalizeTimer = setTimeout(callback, finalizeTime)
            } else {
                callback();
            }
        } else {
            finalizeCal();
        }

        function finalizeCal() {
            $(group).find('.price-range').fadeOut();
            $(group).find('.price-spinner').hide();
            $(group).find('.incprice-range').fadeOut();
            $(group).find('.incprice-spinner').hide();

            //update base price
            let exchange_rate = parseFloat($('#currency_amount').val() || 0);
            let base_price = (price * exchange_rate).toFixed(2);
            $(group).find('.base_price').val(base_price);

            let excamount = 0, vatamount = 0, incamount = 0;
            if (!sinc) {//price from exc
                excamount = (qty * price).toFixed(2);
                vatamount = (qty * price * (vat_rate / 100)).toFixed(2);
                // incamount = (qty * price * (1 + vat_rate / 100)).toFixed(2);


                incamount = (parseFloat(excamount)+parseFloat(vatamount)).toFixed(2);
            } else {//price from inc
                incamount = qty * incprice;
                excamount = parseFloat((incamount / (1 + vat_rate / 100)).toFixed(2));
                vatamount = (incamount - excamount).toFixed(2);
                // console.log('inc: ',incamount,'exc: ',excamount,'vat: ',vatamount);
            }
            // console.log('source: ', source, 'qty: ', qty, 'incprice: ', incprice, 'price: ', price, ' exc: ', excamount, ' inc: ', incamount);


            $(group).find('.excamountLabel').val(numberWithCommas(excamount));
            $(group).find('.incamountLabel').val(numberWithCommas(incamount));
            $(group).find('.excamount').val(excamount);
            $(group).find('.vatamount').val(vatamount);
            $(group).find('.incamount').val(incamount);
            calTotalAmount();
            format_inputs();
        }
    }

    function calTotalAmount() {
        let totalVat = 0, totalExc = 0, totalInc = 0;
        $('.group').each(function (i, group) {
            let excamount = parseFloat($(group).find('.excamount').val()) || 0;
            let vatamount = parseFloat($(group).find('.vatamount').val()) || 0;
            let incamount = parseFloat($(group).find('.incamount').val()) || 0;
            totalExc += excamount;
            totalVat += vatamount;
            totalInc += incamount;
        });

        let currency = $('#currencyid').find(':selected').data('currencyname');
        //labels
        $('.totalExcAmountLabel').val(`${currency} ` + numberWithCommas(totalExc.toFixed(2)));
        $('.totalIncAmountLabel').val(`${currency} ` + numberWithCommas(totalInc.toFixed(2)));
        $('.totalVatAmountLabel').val(`${currency} ` + numberWithCommas(totalVat.toFixed(2)));

        $('.totalExcAmount').val(totalExc.toFixed(2));
        $('.totalIncAmount').val(totalInc.toFixed(2));
        $('.totalVatAmount').val(totalVat.toFixed(2));
        needsApproval();
    }

    function checkVatStatus() {
        $('.vat_loading_spinner').show();
        let charge_vat = $('#vatstatus').is(':checked');
        if (charge_vat) {
            $('#vat-exempted').hide();
        } else {
            $('#vat-exempted').show();
        }
        $('.group').each(function (i, group) {
            let og_vat_rate = $(group).find('.og_vat_rate').val();
            $(group).find('.vat_rate').css('opacity', charge_vat ? 1 : 0.5).val(charge_vat ? og_vat_rate : 0);
            calProductAmount($(group).find('.price'), false);
        });
        setTimeout(function () {
            $('.vat_loading_spinner').hide();
        }, 200);
    }

    //Function for separate number in three digits
    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    function removeCommas(amount) {
        return parseFloat(amount.replace(/,/g, ''));
        //return parseFloat(amount.replace(",", ""));
    }
</script>
