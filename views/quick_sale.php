<link rel="stylesheet" href="assets/stylesheets/theme.css">
<link rel="stylesheet" href="assets/css/custom.css">
<link rel="stylesheet" href="assets/css/floating-msg.css">
<style media="screen">
    body {
        background: #dadada;
    }

    #spinnerHolder {
        position: absolute;
        height: 100vh;
        width: 100%;
        display: none;
        background-color: black;
        opacity: 0.5;
        z-index: 1000;
    }


    .select2-container--default .select2-selection--single {
        height: 40px;
    }

    #product-table-tbody td {
        padding: 5px 3px;
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

    #product-holder {
        height: 359px;
        overflow: hidden;
        overflow-y: scroll;
    }

    #product-holder table.table {
        position: relative;
    }

    #product-holder table.table thead.thead {
        position: sticky;
        top: 0;
        background-color: white;
        z-index: 50;
        box-shadow: 0 0 2px grey;
    }

    #quick-items {
        height: 438px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #quick-items .quick-item {
        font-size: 8pt;
        background-color: #428bca;
        color: #eee;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 25px;
        border-radius: 5px;
        box-shadow: 0 0 5px #dadada;
        transition: 200ms;
        user-select: none;
    }

    #quick-items .quick-item.no-stock {
        background-color: #d9534f !important;
    }

    #quick-items .quick-item:hover {
        box-shadow: 0 0 10px grey;
    }

    .bottom-btn {
        padding: 30px;
        font-size: 12pt;
        font-weight: bold;
    }

    .bottom-btn i {
        font-size: 20pt;
    }

    tr.out-of-stock {
        background-color: #ffc0a9;
    }

    .max-input-error {
        background: #d9534f;
        color: #fff;
    }

    .input-error {
        border: 2px solid red;
    }

    .input-stock-warning {
        border: 2px solid darkorange;
    }

    #paymentModal .modal-dialog-center {
        top: 10%;
    }

    @media (min-width: 992px) {
        .modal-lg.prescription {
            width: 70%;
        }
    }

    .max-input-warning {
        background: #d9882a;
        border: 2px solid orange;
    }

    .non-stock .non-stock-label {
        display: block !important;
        font-size: 8pt;
        position: absolute;
        top: 1px;
        left: -4px;
        z-index: 4;
        transform: rotateZ(335deg);
    }
</style>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>

<div class="floating-msg hidden">
    <div class="msg-holder">
        <ul>
            <li data-stockid="" onclick="findItem(this)">msg 1</li>
            <li>msg 1</li>
            <li>msg 1</li>
            <li>msg 1</li>
            <li>msg 1</li>
            <li>msg 1</li>
            <li>msg 1</li>
            <li>msg 1</li>
            <li>msg 1</li>
        </ul>
    </div>
    <button class="btn  animated bounce"><i class="fa fa-warning"></i></button>
</div>

<!-- quick save the client -> model start -->
<?= component('shared/quick_add_client_modal.tpl.php') ?>
<!-- quick save the client -> model end here -->

<form id="saveForm" class="m-none"
      action="<?= url('pos', $QUICK_ORDER_MODE ? 'save_quick_order' : 'save_quick_sales_new') ?>" method="post"
      novalidate onsubmit="return validateInputs(event)">
    <? $USER_CAN_DISCOUNT = CS_QUICKSALE_DISCOUNT && Users::can(OtherRights::sale_discount) && !$QUICK_ORDER_MODE; ?>
    <input type="hidden" name="sales[id]" value="<?= $sale['id'] ?>">
    <? if (!$sale['id']) { ?><input type="hidden" name="sales[token]" value="<?= unique_token() ?>"><? } ?>
    <? if (CS_QUICKSALE_KEYBOARDONLY) { ?><input type="hidden" name="keyboardonly"><? } ?>

    <div class="modal fade" id="order-print-size-modal" role="dialog"
         aria-labelledby="order-print-size-modal"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Print Size:</h4>
                    <select class="form-control input-sm" name="order_print_size">
                        <? foreach ($print_sizes as $size) { ?>
                            <option value="<?= $size ?>"><?= ucfirst($size) ?></option>
                        <? } ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm">Confirm</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" role="dialog" aria-labelledby="paymentModal"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-center">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><i class="fa fa-money"></i> Payment</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between">
                        <h4>TOTAL</h4>
                        <h4 class="text-rosepink text-weight-bold total_incamount_label">0.00</h4>
                    </div>
                    <div class="checkbox" title="add client TIN & Company name">
                        <label>
                            <input onchange="clientTIN(this);" type="checkbox" value="2">
                            Add <strong>TIN and Company Name</strong>
                        </label>
                    </div>
                    <div class="checkbox assign_new_card" title="Assign royalty card to customer">
                        <label>
                            <input onchange="newroyaltyno(this);" type="checkbox" class="newroyaltyno" value="3">
                            Assign <strong>Royalty Card</strong>
                        </label>
                    </div>
                    <div class="checkbox redeem_card" style="display: none" title="Redeem client royalty card">
                        <label>
                            <input onclick="//redeemCard(this);" type="checkbox">
                            Redeem Card
                        </label>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-6">
                            <div class="<?= CS_QUICKSALE_KEYBOARDONLY ? 'd-none' : '' ?>">
                                Receipt Type:
                                <? if ($_SESSION['member']['receipt_type'] == 'both' && !$sale['receipt_method']) { ?>
                                    <select class="form-control input-sm receipt_type" name="sales[receipt_method]" required>
                                        <? foreach ($receipts as $key => $R) { ?>
                                            <option value="<?= $R['name'] ?>"><?= $R['lable'] ?> (<?= $R['name'] ?>)</option>
                                        <? } ?>
                                    </select>
                                <? } else { ?>
                                    <input type="text" readonly class='form-control input-sm receipt_type' name="sales[receipt_method]"
                                           value="<?= $sale['receipt_method'] ?: $_SESSION['member']['receipt_type'] ?>">
                                <? } ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            Print Size:
                            <select class="form-control input-sm" name="sales[print_size]">
                                <? foreach ($print_sizes as $size) { ?>
                                    <option value="<?= $size ?>"><?= ucfirst($size) ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Amount Received:</h4>
                                    <input id="receivedAmount" onkeyup="paidAmount(event,this)"
                                           placeholder="Received amount"
                                           autocomplete="off"
                                           readonly class="form-control input-lg paidAmount" type="text"
                                           name="paid_totalamount">
                                </div>
                                <div class="col-md-12 change-holder" style="display: none;">
                                    <div class="d-flex justify-content-between">
                                        <h5>Change: <span class="text-danger text-weight-bold change-amount">0</span>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>Payment Method:</h4>
                            <select id="paymentMethod" class="form-control" name="payment_method"
                                    onchange="checkPaymentMethod(this)">
                                <option selected value="<?= PaymentMethods::CASH ?>">Cash</option>
                                <option value="<?= PaymentMethods::CREDIT_CARD ?>">Credit Card</option>
                            </select>
                        </div>
                    </div>
                    <div id="creditCardHolder" class="row mt-md" style="display: none;">
                        <div class="col-md-6">
                            <h4>Electronic Account:</h4>
                            <select id="electronic-account" class="form-control" name="electronic_account">
                                <option value="">-- choose account --</option>
                                <? foreach ($electronic_accounts as $acc) { ?>
                                    <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <h4>Reference:</h4>
                            <input id="credit_card_no" type="text" name="credit_card_no" class="form-control"
                                   placeholder="reference number">
                        </div>
                    </div>

                    <div class="row clientTIN" style="display:none;">
                        <hr style="border: 1px solid #fff;">
                        <div class="pl-sm">Client Info:</div>
                        <div class="col-md-6">
                            <input placeholder="TIN No." class="form-control input-sm" type="text"
                                   name="sales[client_tin]" value="">
                        </div>
                        <div class="col-md-6">
                            <input placeholder="Client Name" class="form-control input-sm" type="text"
                                   name="sales[client_name]" value="">
                        </div>
                    </div>
                    <div class="row royaltyno" style="display:none; margin-top: 20px;">
                        <div class="col-md-6">
                            <div class="form-group" style="position: relative;">
                                <object id="saveCardSpinner" data="images/loading_spinner.svg"
                                        style="position: absolute;right: 0;top:0;display: none"
                                        type="image/svg+xml" height="30" width="30"></object>
                                <label for="">Choose Card</label>
                                <select class="form-control input-sm royalty-card" name="royaltycardId"> </select>
                                <button id="saveCardBtn" type="button" onclick="assignCard()"
                                        class="btn btn-success btn-sm btn-block" style="margin-top: 5px;">Assign
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger btn-block btn-lg" data-dismiss="modal">Close
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button id="confirm-btn" class="btn btn-default btn-success btn-block btn-lg" disabled>
                                Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mt-xs mb-xs">
            <div class="col-md-4">
                <div class="d-flex">
                    <button type="button" class="btn btn-default btn-lg" title="reset"
                            onclick="window.location.reload()"><i class="fa fa-recycle"></i></button>
                    <a href="<?= url('home', 'index') ?>" class="btn btn-default btn-lg" title="home"><i
                                class="fa fa-home"></i></a>
                    <button type="button" class="btn btn-default btn-lg" title="add client" data-toggle="modal"
                            data-target="#quick-add-client-modal">
                        <i class="fa fa-user-plus"></i></button>
                    <? if ($order) { ?>
                        <button type="button" disabled class="btn btn-dark ml-md">
                            <i class="fa <?= $QUICK_ORDER_MODE ? 'fa-pencil' : 'fa-gears' ?>"></i> <?= $QUICK_ORDER_MODE ? 'Editing' : 'Processing' ?>
                            Order No: <?= $order['orderid'] ?>
                        </button>
                    <? } elseif ($proforma) { ?>
                        <button type="button" disabled class="btn btn-dark ml-md">
                            <i class="fa fa-gears"></i> Processing Proforma No: <?= $proforma['proformaid'] ?>
                        </button>
                    <? } elseif ($sale) { ?>
                        <button type="button" disabled class="btn btn-dark ml-md">
                            <i class="fa fa-pencil"></i> Editing Invoice: <?= $sale['receipt_no'] ?>
                        </button>
                    <? } ?>
                </div>
            </div>
            <div class="col-md-2">
                <input id="barcodeInput" autofocus="autofocus" onchange="scanningBarcode(this)" type="text"
                       class="form-control"
                       placeholder="Scan Barcode">
            </div>
            <? if (CS_QUICKSALE_KEYBOARDONLY) { ?>
                <div class="col-md-6 d-flex align-items-center flex-wrap">
                    <div class="mr-sm">(<span class="text-weight-bold text-danger">Alt + S</span>) Scan barcode,</div>
                    <div class="mr-sm">(<span class="text-weight-bold text-danger">Alt + R</span>) Search product,</div>
                    <div class="mr-sm">(<span class="text-weight-bold text-danger">Alt + X</span>) Delete product,</div>
                    <div class="mr-sm">(<span class="text-weight-bold text-danger">Alt + P</span>) Reprint receipt,</div>
                    <div class="mr-sm">(<span class="text-weight-bold text-danger">Esc</span>) Clear all products,</div>
                    <div class="mr-sm">
                        (<span class="text-weight-bold text-danger">
                            <i class="fa fa-arrow-up"></i>
                            <i class="fa fa-arrow-down"></i>
                        </span>) Highlight product
                    </div>
                </div>
            <? } ?>
        </div>
        <div class="row">
            <div class="col-md-2 col-sm-6 mb-xs">
                <? if ($order || $proforma) { ?>
                    <input type="hidden" name="sales[orderid]" value="<?= $order['orderid'] ?>">
                    <input type="hidden" name="sales[proformaid]" class="proformaid"
                           value="<?= $proforma['proformaid'] ?>">
                    <input type="hidden" name="sales[op_reuse]" value="<?= isset($_GET['reuse']) ?>">
                <? } ?>
                <? if ((($order || $proforma) && !$QUICK_ORDER_MODE) || ($QUICK_ORDER_MODE & $proforma)) { ?>
                    <input id="clientid" type="hidden" name="sales[clientid]"
                           value="<?= $order['clientid'] ?: $proforma['clientid'] ?>">
                    <input type="text" readonly class="form-control"
                           value="<?= $order['clientname'] ?: $proforma['clientname'] ?>"
                           title="<?= $order['clientname'] ?: $proforma['clientname'] ?>">
                <? } else { ?>
                    <select id="clientid" onchange="clientDet(this)" class="form-control" name="sales[clientid]"
                            title="client">
                        <? if ($defaultClient) { ?>
                            <option value="<?= $defaultClient['id'] ?>" selected><?= $defaultClient['name'] ?></option>
                        <? } ?>
                    </select>
                <? } ?>
            </div>
            <div class="col-md-2 col-sm-6 mb-xs">
                <select id="currencyid" onchange="getExchangeRate(this)" class="form-control"
                        name="sales[currency_rateid]"
                        title="currency">
                    <? foreach ($currencies as $currency) {
                        if ($order || $proforma || $sale) { ?>
                            <option <?= selected($currency['currencyid'], $order['currencyid'] ?: $proforma['currencyid'] ?: $sale['currencyid']); ?>
                                    value="<?= $currency['rateid'] ?>"
                                    data-currencyid="<?= $currency['currencyid'] ?>"
                                    data-currencyname="<?= $currency['currencyname'] ?>"
                                    data-exchange-rate="<?= $currency['rate_amount'] ?>"
                                    data-currency-description="<?= $currency['description'] ?>">
                                <?= $currency['currencyname'] ?> - <?= $currency['description'] ?>
                            </option>
                        <? } else {
                            ?>
                            <option <?= selected($currency['base'], 'yes'); ?>
                                    value="<?= $currency['rateid'] ?>"
                                    data-currencyid="<?= $currency['currencyid'] ?>"
                                    data-currencyname="<?= $currency['currencyname'] ?>"
                                    data-exchange-rate="<?= $currency['rate_amount'] ?>"
                                    data-currency-description="<?= $currency['description'] ?>">
                                <?= $currency['currencyname'] ?> - <?= $currency['description'] ?>
                            </option>
                        <? }
                    } ?>
                </select>
                <input id="currency_amount" type="hidden" name="sales[currency_amount]"
                       value="<?= $sale['currency_amount'] ?>">
            </div>
            <div class="col-md-2 hidden-xs hidden-sm mb-xs">
                <? if (CS_QUICKSALE_SEARCH) { ?>
                    <select id="search-product-input" onchange="fetchTyped(this)" onkeydown="enterKeyPressed(event)"
                            data-source="typing"
                            class="form-control search_product"
                            title="search product"></select>
                <? } ?>
            </div>
            <div class="col-md-2 col-sm-6 mb-xs">
                <? if ((Users::cannot(OtherRights::approve_other_credit_invoice) && !$QUICK_ORDER_MODE) || $proforma || $sale['orderid'] || $sale['proformaid']) { ?>
                    <input id="locationid" type="hidden" name="sales[locationid]" value="<?= $defaultLocation['id'] ?>">
                    <input type="text" readonly class="form-control" value="<?= $defaultLocation['name'] ?>">
                <? } else { ?>
                    <select id="locationid" onchange="getLocationStock(this)" class="form-control" title="location"
                            name="sales[locationid]"
                            data-source="location">
                        <? if ($defaultLocation) { ?>
                            <option value="<?= $defaultLocation['id'] ?>"><?= $defaultLocation['name'] ?></option>
                        <? } ?>
                    </select>
                <? } ?>
            </div>
            <div class="col-md-2 col-sm-6 mb-xs" title="product categories">
                <select id="category" class="form-control" onchange="getLocationStock(this)" data-source="category">
                    <option value="" selected>All category</option>
                    <? foreach ($categories as $index => $category) { ?>
                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                    <? } ?>
                </select>
            </div>
            <div class="col-md-2" title="product subcategories">
                <select id="subcategory" class="form-control" onchange="getLocationStock(this)"
                        data-source="subcategory">
                    <option value="" selected>All subcategory</option>
                    <? foreach ($subcategories as $index => $sub) { ?>
                        <option value="<?= $sub['id'] ?>"
                                data-categoryid="<?= $sub['category_id'] ?>"><?= $sub['name'] ?></option>
                    <? } ?>
                </select>
            </div>
            <div class="col-md-2 col-xs-12 hidden-md hidden-lg hidden-xl mt-md">
                <? if (CS_QUICKSALE_SEARCH) { ?>
                    <select onchange="fetchTyped(this)" data-source="typing" class="form-control search_product"
                            title="search product"></select>
                <? } ?>
            </div>
        </div>
        <? if (!$QUICK_ORDER_MODE) { ?>
            <div class="row">
                <div class="col-md-2">

                    <? if ($proforma) { ?>
                        <input type="hidden" name="sales[paymenttype]" value="<?= $proforma['paymentterms'] ?>">
                    <? } ?>
                    <select class="form-control saveoptions" name="sales[paymenttype]"
                            onchange="saveOptions(this)" <?= $proforma ? 'disabled' : '' ?>>
                        <option <?= selected($sale['paymenttype'] ?: $proforma['paymentterms'], PAYMENT_TYPE_CASH) ?>
                                value="<?= PAYMENT_TYPE_CASH ?>">Cash Sales
                        </option>
                        <option <?= selected($sale['paymenttype'] ?: $proforma['paymentterms'], PAYMENT_TYPE_CREDIT) ?>
                                value="<?= PAYMENT_TYPE_CREDIT ?>">Credit Sales
                        </option>
                    </select>
                </div>
                <div class="col-md-2 credit_days" style="display: none">
                    <input type="number" placeholder="Credit Days (Default is 30 Days)"
                           value="<?= $sale['credit_days'] ?: $proforma['payment_days'] ?>"
                           name="sales[credit_days]" class="form-control" min="0" title="credit days">
                </div>
            </div>
        <? } ?>
        <div class="row mt-xs">
            <div class="<?= !$USER_CAN_DISCOUNT ? 'col-md-6' : 'col-md-7' ?> p-xs">
                <div class="panel">
                    <div class="panel-body" style="position: relative">
                        <div class="d-flex flex-wrap">
                            <h5 class="mr-md">Client: <span id="clientname"
                                                            class="text-rosepink text-weight-bold"><?= $defaultClient['name'] ?></span>
                            </h5>
                            <h5 class="mr-md">Mobile: <span id="clientmobile"
                                                            class="text-rosepink text-weight-bold"><?= $defaultClient['mobile'] ?></span>
                            </h5>
                            <h5 class="mr-md">TIN: <span id="clienttin"
                                                         class="text-rosepink text-weight-bold"><?= $defaultClient['tinno'] ?></span>
                            </h5>
                            <h5 class="mr-md">VRN: <span id="clientvatno"
                                                         class="clientvatno text-rosepink text-weight-bold"><?= $defaultClient['vatno'] ?></span>
                            </h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between align-items-center">
                                <h5 class="mt-lg text-weight-bold">Products <small id="total-items-label"
                                                                                   class="text-rosepink"></small>
                                </h5>
                                <?
                                if ($USER_CAN_DISCOUNT) { ?>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-warning" title="Overall discount"
                                                data-toggle="modal"
                                                data-target="#discount-modal">
                                            <i class="fa fa-arrow-down"></i></button>
                                        <button type="button" class="btn btn-sm btn-info" title="Remove discounts"
                                                onclick="restoreOriginalPrices()">
                                            <i class="fa fa-recycle"></i></button>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                        <div id="product-holder">
                            <table class="table">
                                <thead class="thead" style="font-size: 10pt">
                                <tr>
                                    <th></th>
                                    <th class="text-center"
                                        style="<?= !$USER_CAN_DISCOUNT ? 'width: 40%' : 'width: 20%' ?>">Name
                                    </th>
                                    <th class="text-center">Unit name</th>
                                    <th class="text-center" style="width: 8%">Qty</th>
                                    <th class="text-center <?= !$USER_CAN_DISCOUNT ? 'hidden' : '' ?>"
                                        style="width: 8%">VAT%
                                    </th>
                                    <th class="text-center <?= !$USER_CAN_DISCOUNT ? 'hidden' : '' ?>">Price exc</th>
                                    <th class="text-center <?= !$USER_CAN_DISCOUNT ? 'hidden' : '' ?>"
                                        style="width: 8%">Disc%
                                    </th>
                                    <th class="text-center <?= !$USER_CAN_DISCOUNT ? 'hidden' : '' ?>">Disc Amt</th>
                                    <th class="text-center">Selling Price</th>
                                    <th class="text-center">Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="product-table-tbody">
                                <? if ($order || $proforma || $sale) {
                                    foreach ($order['details'] ?: $proforma['details'] ?: $sale['details'] as $index => $detail) { ?>
                                        <?= component('quick-sale/product_item.tpl.php', ['detail' => $detail, 'QUICK_ORDER_MODE' => $QUICK_ORDER_MODE]) ?>
                                    <? }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12 pl-lg pr-lg d-flex justify-content-between">
                                <h5 class="text-weight-bold">VAT</h5>
                                <h5 class="text-weight-bold total_vatamount_label"></h5>
                                <input class="total_vatamount" type="hidden" name="sales[grand_vatamount]">
                            </div>
                            <div class="col-md-12 pl-lg pr-lg d-flex justify-content-between">
                                <h5 class="text-weight-bold">DISCOUNT</h5>
                                <h5 class="text-weight-bold total_discamount_label"></h5>
                                <input class="total_discamount" type="hidden" name="sales[total_discount]">
                            </div>
                            <div class="col-md-12 pl-lg pr-lg d-flex justify-content-between">
                                <h3 class="text-weight-bold">TOTAL</h3>
                                <div>
                                    <h3 class="text-weight-bold text-success total_incamount_label"></h3>
                                    <input class="total_incamount" type="hidden" name="sales[full_amount]">
                                    <input class="total_excamount" type="hidden" name="sales[grand_amount]">
                                    <span id="sale-limit-alert" class="text-danger text-weight-bold"
                                          style="display: none">Your sale limit
                                        exceeded</span>
                                    <input id="sale-limit" type="hidden" name="exceed_limit">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="<?= !$USER_CAN_DISCOUNT ? 'col-md-6' : 'col-md-5' ?> p-xs">
                <div class="panel">
                    <div class="panel-body">
                        <div id="quick-items" class="row p-xs"></div>
                        <div class="row d-flex justify-content-center mt-md">
                            <? if ($QUICK_ORDER_MODE) { ?>
                                <div class="col-md-4">
                                    <button id="save-order-btn" type="button" class="btn btn-info btn-block bottom-btn"
                                            data-toggle="modal"
                                            data-target="#order-print-size-modal">
                                        <i class="fa fa-shopping-cart"></i>
                                        <span class="m-none d-block"><?= $order ? 'Update' : 'Create' ?> Order</span>
                                    </button>
                                </div>
                            <? } else { ?>
                                <div class="col-md-4">
                                    <button id="cash-payment-btn" type="button"
                                            class="btn btn-success btn-block bottom-btn"
                                            data-toggle="modal"
                                            data-target="#paymentModal">
                                        <i class="fa fa-money"></i>
                                        <span class="m-none d-block">Payment</span>
                                    </button>
                                    <button id="credit-payment-btn" class="btn btn-success btn-block bottom-btn"
                                            style="display: none;">
                                        <i class="fa fa-credit-card"></i>
                                        <span class="m-none d-block">Credit Sale</span>
                                    </button>
                                </div>
                            <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<div class="modal fade" id="discount-modal" tabindex="-1" role="dialog" aria-labelledby="discount-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Overall Discount</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">Total Exc Amt</label>
                        <input type="text" readonly class="form-control total_excamount_label">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">Discount %</label>
                        <input type="number" step="0.01" min="0" value="0" class="form-control overall-discount-percent"
                               onchange="overallDiscount(this)" onkeyup="overallDiscount(this)" data-source="percent">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Discount Amt</label>
                        <input type="number" step="0.01" min="0" value="0" class="form-control overall-discount"
                               onchange="overallDiscount(this)"
                               onkeyup="overallDiscount(this)" data-source="amount">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" onclick="distributeDiscount()">Confirm</button>
            </div>
        </div>
    </div>
</div>
<?= component('shared/product_view_modal.tpl.php') ?>

<div class="modal fade" id="reprint-receipt-modal" data-backdrop="static" role="dialog" aria-labelledby="reprint-receipt-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4>Receipt no:</h4>
                <input type="text" class="form-control input-sm" name="receiptno" placeholder="receipt no"
                       onkeyup="reprintReceipt(event)">
                <small>press (<span class="text-danger">Enter</span>) to print</small>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    let CAN_DISCOUNT = false;
    <?if($USER_CAN_DISCOUNT){?>CAN_DISCOUNT = true;<?}?>

    let CAN_SELL_RANDOM_BATCH = false;
    <?if(Users::can(OtherRights::sale_random_batch)){?>CAN_SELL_RANDOM_BATCH = true;<?}?>

    let ORDER_MODE = false;
    <?if($QUICK_ORDER_MODE){?>ORDER_MODE = true;<?}?>

    $(function () {
        // $('#paymentModal').modal('show');
        <? if ($_SESSION['error']) {?>
        notifyError(`<?=$_SESSION['error']?>`, <?=$_SESSION['delay'] ?: 1000?>);
        <?} ?>
        <? if ($_SESSION['message']) {?>
        notifyMessage(`<?=$_SESSION['message']?>`, <?=$_SESSION['delay'] ?: 1000?>);
        <?} ?>

        <? if (!((($order || $proforma) && !$QUICK_ORDER_MODE) || ($QUICK_ORDER_MODE & $proforma))) { ?>
        initSelectAjax("#clientid", "?module=clients&action=getClients&format=json", "Choose Client");
        <?}?>
        initSelectAjax("#royalty-card", "?module=royalty_card&action=findcard&format=json", "Choose card");
        getExchangeRate('#currencyid');

        <?if(!$proforma
    && !$sale['orderid']
    && !$sale['proformaid']
    && (Users::can(OtherRights::approve_other_credit_invoice) || $QUICK_ORDER_MODE)){?>
        initSelectAjax("#locationid", "?module=locations&action=getLocations&format=json", "Choose location");
        <?}?>

        initSearchProductLocation();
        $('#category').select2();
        $('#subcategory').select2();

        getLocationStock();
        saveOptions();
        checkErrors();
        <?if($order || $proforma || $sale){?>

        if ($('tr.out-of-stock').length > 0) {
            notifyError(`System found ${$('tr.out-of-stock').length} product out of stock`);
        }

        $('.product-item').each(function (i, tr) {
            if ($(tr).hasClass('out-of-stock')) {
                $(tr).find('input,textarea').prop('disabled', true);
            } else {
                checkMaxQty($(tr).find('.qty'), false);
            }
        });

        initPrescriptionInputs();
        <?}?>

        <?if(CS_QUICKSALE_KEYBOARDONLY){?>initKeyboardOnly();<?}?>

    });

    function checkErrors() {
        let floating_msg = $('.floating-msg');
        $(floating_msg).find('ul').empty();
        $('.product-item').each(function (i, tr) {
            let productname = $(tr).find('.productname').val();
            if ($(tr).hasClass('out-of-stock')) {
                let item = ``;
                if ($(tr).hasClass('external-product')) {
                    item = `<li data-productname="${productname}" onclick="findItem(this)" title="${productname} not found in stock, issue GRN or find it in list if already purchased"><span class="text-danger">${productname.substring(0, 10)}</span> <span class="text-weight-bold">is an external product</span></li>`;
                } else {
                    item = `<li data-productname="${productname}" onclick="findItem(this)" title="${productname}"><span class="text-danger">${productname.substring(0, 10)}</span> is out of stock</li>`;
                }
                $(floating_msg).find('ul').append(item);
                $(tr).find('input,textarea').prop('disabled', true);
            }

            if ($(tr).find('.max-input-warning').length > 0) {
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
        let tr = $(`.productname[value='${productname}']`).closest('tr');
        $('#product-holder').scrollTop(0).animate({
            scrollTop: $(tr).position().top - 100
        }, 'slow');
    }

    let SEARCHING_PRODUCT = false;

    function initKeyboardOnly() {
        document.addEventListener('keydown', function (e) {
            // console.log('keypressed: ', e);

            if (e.key === 'Escape' || e.keyCode === 27) { //Escape
                clearAllProducts();
            } else if (e.altKey && (e.key === 's' || e.keyCode === 83)) { //Alt + S
                // console.log('Scan barcode');
                scanBarcode();
            } else if (e.altKey && (e.key === 'r' || e.keyCode === 82)) { //Alt + R
                // console.log('Search product');
                searchProduct();
            } else if (e.altKey && (e.key === 'x' || e.keyCode === 88)) { //Alt + X
                // console.log('delete product');
                deleteItem();
            } else if (e.altKey && (e.key === 'p' || e.keyCode === 80)) { //Alt + P
                // console.log('reprint receipt');
                showReprintModal();
            } else if (e.key === 'ArrowUp' || e.keyCode === 38 || e.key === 'ArrowDown' || e.keyCode === 40) { //Arrows
                // console.log('focus items');
                navigateRow(e);
            } else if (e.key === 'Enter' || e.keyCode === 13) { //Enter
                // console.log('enter pressed');
                enterKeyPressed();
            }
        });

        //listen if search is opened
        $('#search-product-input').on('select2:open', function () {
            SEARCHING_PRODUCT = true;
            // console.log('searching...')
        }).on('select2:close', function () {
            SEARCHING_PRODUCT = false;
            // console.log('close search');
        }).on('select2:select', function (e) {
            // console.log('selected item', e);
            ENTER_FROM_PRODUCT_SEARCHING = true;
        });
    }

    function showReprintModal() {
        if ($('#reprint-receipt-modal').data('bs.modal')?.isShown) {
            $('#reprint-receipt-modal').modal('hide');
        } else {
            $('#reprint-receipt-modal input').val('');
            $('#reprint-receipt-modal').modal('show');
            setTimeout(() => {
                $('#reprint-receipt-modal input').focus();
            }, 500)
        }
    }

    function reprintReceipt(e) {
        e.preventDefault();
        if (e.key === 'Enter' || e.keyCode === 13) {
            let val = $(e.target).val()||'';
            // console.log(val);
            if(val.length===0){
                notifyError('Enter valid receipt no');
                return;
            }
            window.location.assign(`<?=url('pos','reprint')?>&receiptno=${val}`);
        }
    }

    function clearAllProducts() {
        if (PAYMENT_MODAL_SHOWN) {
            $('#paymentModal').modal('hide');
            return;
        }
        // console.log('clear all products');
        $('#product-table-tbody').empty();
        calcAmount();
    }

    let ENTER_FROM_PRODUCT_SEARCHING = false;
    let PAYMENT_MODAL_SHOWN = false;

    function enterKeyPressed(e) {
        // console.log(e);
        if (ENTER_FROM_PRODUCT_SEARCHING) {
            ENTER_FROM_PRODUCT_SEARCHING = false;
            return;
        }
        let modal = $('#paymentModal');

        if (PAYMENT_MODAL_SHOWN) {
            // console.log('shown');
        } else {
            // console.log('not shown');
            if ($('#product-table-tbody tr.product-item').length > 0 && !SEARCHING_PRODUCT) {
                $(modal).modal('show');
            } else {
            }
        }
    }

    function deleteItem() {
        let row = $(document.activeElement).closest('tr.product-item');
        if (row.length > 0) removeItem($(row).find('input.productname'));
    }

    function scanBarcode() {//focus on barcode scanner
        $('#search-product-input').select2('close');
        $('#barcodeInput').focus();
    }

    function searchProduct() {//focus search product
        let search_input = $('#search-product-input');
        $(search_input).select2('close').select2('destroy');
        $(search_input).empty();
        initSearchProductLocation();
        $(search_input).select2('open');
    }

    function navigateRow(e) {
        if (SEARCHING_PRODUCT) return;
        // if (e.key === 'ArrowLeft' || e.keyCode === 37) {//left
        //     currentCell = currentCell <= 0 ? 0 : --currentCell;
        //     changeCurrentCell();
        //     return false;
        // }
        if (e.key === 'ArrowUp' || e.keyCode === 38) {//up
            currentRow = currentRow <= 0 ? 0 : --currentRow;
            changeCurrentCell();
            return false;
        }
        // if (e.key === 'ArrowRight' || e.keyCode === 39) {//right
        //     currentCell++;
        //     changeCurrentCell();
        //     return false;
        // }
        if (e.key === 'ArrowDown' || e.keyCode === 40) {//down
            let productLength = $('#product-table-tbody tr.product-item').length - 1;
            currentRow = currentRow >= productLength ? productLength : ++currentRow;
            changeCurrentCell();
            return false;
        }
    }

    let currentRow = 0;

    function changeCurrentCell() {
        let selectedProducts = $('#product-table-tbody tr.product-item');
        if ($(selectedProducts).length === 0) {
            currentRow = 0;
            return;
        }
        // console.log('row: ', currentRow);
        let tableRow = $(selectedProducts).eq(currentRow);
        $(tableRow).find('td input.productname').focus();
    }

    // changeCurrentCell();

    function format_inputs() {
        qtyInput('.qty, .batch_qty_out');
        thousands_separator('.price, .unit_price, .discount');
        thousands_separator('.discpercent', 2);
    }

    function initSearchProductLocation() {
        let locationid = $('#locationid').val();
        let proformaid = $('.proformaid').val();
        //TODO IMPROVE SELECT2 RESULT TEMPLATE
        $(".search_product").select2({
            placeholder: 'search product',
            width: '100%', minimumInputLength: 2,
            ajax: {
                url: `?module=stocks&action=getLocationStocks&format=json&non_stock=yes&locationid=${locationid}&except_proforma=${proformaid}`,
                dataType: 'json',
                delay: 250,
                quietMillis: 200,
                data: function (term) {
                    return {search: term};
                },
                results: function (data, page) {
                    return {result: data};
                },
                processResults: function (data) {
                    let results = $.map(data.results, function (obj) {
                        obj.id = obj.id ? obj.id : obj.productid; // replace id
                        // console.log('after: ',obj);
                        return obj;
                    });
                    return {
                        results: results
                    };
                }
            }
            // templateResult: function (item) {
            //     console.log(item);
            //     let span = `<span data-stockid="${item.non_stock==1?'':item.stockid}" data-productid="${item.productid}" data-productname="${item.text}" data-vatrate="${item.vat_rate}"
            //                                       data-description="${item.description}" data-nonstock="${item.non_stock}" data-unitname="${item.unitname}">${item.text}</span>`;
            //     return $(span);
            // }
        });

    }

    function getExchangeRate(obj) {
        let exchange_rate = parseFloat($(obj).find(':selected').data('exchange-rate'));
        $('#currency_amount').val(exchange_rate);
        recalculateProductAmounts();
    }

    function recalculateProductAmounts() {
        let exchange_rate = parseFloat($('#currency_amount').val()) || 0;

        $('.product-item').each(function (i, parent) {
            let productid = $(parent).find('.productid').val();
            let vat_rate = parseFloat($(parent).find('.vat_rate').val());
            let base_price = parseFloat($(parent).find('.base_price').val()) || 0;
            let base_incprice = parseFloat($(parent).find('.base_incprice').val()) || 0;
            let base_hidden_cost = parseFloat($(parent).find('.base_hidden_cost').val()) || 0;
            let discpercent = parseFloat($(parent).find('.discpercent').val()) || 0;
            let max_discount_percent = parseFloat($(parent).find('.discpercent').attr('max')) || 0;
            let non_stock = $(parent).find('.non-stock').length > 0;

            let price = parseFloat((base_price / exchange_rate).toFixed(2));
            let unit_price = base_incprice ? parseFloat((base_incprice / exchange_rate).toFixed(2)) : (price * (1 + vat_rate / 100)).toFixed(2);
            let discount = (price * discpercent / 100).toFixed(2);
            let max_discount = (price * max_discount_percent / 100).toFixed(2);
            $(parent).find('.price').val(price);
            $(parent).find('.unit_price').val(unit_price);
            $(parent).find('.discount')
                .attr('max', non_stock ? '' : max_discount)
                .attr('title', non_stock ? '' : `discount\nMax: ${max_discount}`)
                .val(discount);
            $(parent).find('.hidden_cost').val((base_hidden_cost / exchange_rate).toFixed(2));
            calProductAmount(productid);
        });
    }

    let finalizeCalTimer = null;
    const finalizeCalTime = 500; //milliseconds
    function calProductAmount(productid, obj) {
        let parent = $(`tr.productid-${productid}`);
        let productname = $(parent).find('.productname').val();
        let qty = parseInt($(parent).find('.qty').val());
        let prev_qty = parseInt($(parent).find('.qty').attr('prev-qty'));

        let vat_rate = parseFloat($(parent).find('.vat_rate').val());
        let excprice = removeCommas($(parent).find('.price').val());
        let unit_price = removeCommas($(parent).find('.unit_price').val()) || 0;
        let discpercent = parseFloat($(parent).find('.discpercent').val()) || 0;
        let max_discpercent = parseFloat($(parent).find('.discpercent').attr('max')) || 0;
        let discount = removeCommas($(parent).find('.discount').val()) || 0;
        let max_discount = parseFloat($(parent).find('.discount').attr('max')) || 0;
        let non_stock = $(parent).find('.non-stock').length > 0;
        if (finalizeCalTimer) {
            clearTimeout(finalizeCalTimer);
            finalizeCalTimer = null;
        }

        let excprice_after_discount = excprice;
        let source = $(obj).data('source');
        if (source === 'discpercent') {
            if (discpercent > max_discpercent) {
                notifyError(`Maximum is ${numberWithCommas(max_discpercent)}%`);
                discpercent = max_discpercent;
                $(parent).find('.discpercent').val(discpercent);
            }
            discount = excprice * (discpercent / 100);
            discount = parseFloat(discount.toFixed(2));
            $(parent).find('.sinc').val(discpercent <= 0 ? 1 : 0);
            $(parent).find('.discount').val(discount);
            finalizeCal();
        } else if (source === 'discount') {
            if (discount > max_discount) {
                notifyError(`Maximum discount is ${numberWithCommas(max_discount)}`);
                discount = max_discount;
                $(parent).find('.discount').val(discount);
            }
            discpercent = (discount / excprice) * 100;
            discpercent = parseFloat(discpercent.toFixed(2));
            $(parent).find('.sinc').val(discpercent <= 0 ? 1 : 0);
            $(parent).find('.discpercent').val(discpercent);
            finalizeCal();
        } else if (source === 'price') { //for non-stock
            discount = discpercent = 0;
            $(parent).find('.discount').val(discount);
            $(parent).find('.discpercent').val(discpercent);
            $(parent).find('.sinc').val(0);
            finalizeCal();
        } else if (source === 'unit_price') { //for non-stock
            $(parent).find('.incprice_spinner').show();
            discount = discpercent = 0;
            $(parent).find('.discount').val(discount);
            $(parent).find('.discpercent').val(discpercent);
            excprice = (unit_price / (1 + vat_rate / 100)).toFixed(2);
            $(parent).find('.price').val(excprice);
            $(parent).find('.sinc').val(1);
            // finalizeCal();
            finalizeCalTimer = setTimeout(finalizeCal, finalizeCalTime);
        } else {
            finalizeCal();
        }

        function finalizeCal() {

            if (!isNaN(prev_qty) && qty < prev_qty) beep(`reduce_qty`);
            $(parent).find('.qty').attr('prev-qty', qty);

            $('.incprice_spinner').hide();
            let sinc = $(parent).find('.sinc').val() === '1';
            excprice_after_discount = excprice - discount;
            // if (finalizeCalTimer) console.log('found timer: ', finalizeCalTimer);
            unit_price = finalizeCalTimer || sinc ? unit_price : parseFloat(addPercent(excprice_after_discount, vat_rate).toFixed(2));
            // console.log('product: ', productname, 'sinc: ', sinc, 'incprice: ', unit_price, 'source: ', source);


            $(parent).find('.unit_price').val(unit_price);
            //update base prices
            let exchange_rate = parseFloat($('#currency_amount').val() || 0);
            let base_price = (excprice * exchange_rate).toFixed(2);
            $(parent).find('.base_price').val(base_price);

            let discamount = 0, excamount = 0, vatamount = 0, incamount = 0;

            if (!sinc) {
                discamount = (discount * qty).toFixed(2);
                excamount = (excprice_after_discount * qty).toFixed(2);
                vatamount = (excprice_after_discount * qty * (vat_rate / 100)).toFixed(2);
                // incamount = (excprice_after_discount * qty * (1 + vat_rate / 100)).toFixed(2);

                incamount = (parseFloat(excamount) + parseFloat(vatamount)).toFixed(2);
            } else {

                incamount = unit_price * qty;
                // console.log("inc: ",incamount,`${unit_price} * ${qty}`);
                excamount = (incamount / (1 + vat_rate / 100)).toFixed(2);
                vatamount = incamount - excamount;
                discamount = 0;
            }
            // console.log('sinc: ', sinc,'source: ',source, 'qty: ', qty, 'incprice: ', unit_price, 'price: ', excprice_after_discount, ' exc: ', excamount, ' inc: ', incamount);

            $(parent).find('.excamount').val(excamount);
            $(parent).find('.discamount').val(discamount);
            $(parent).find('.vatamount').val(vatamount);
            $(parent).find('.incamount').val(incamount);
            $(parent).find('.incamount_label').val(numberWithCommas(incamount));

            calcAmount();
            format_inputs();
        }
    }

    function addPercent(value, percent) {
        return value * (1 + (percent / 100));
    }

    function removePercent(value, percent) {
        return value / (1 + (percent / 100));
    }

    function quickAddDoctor(obj) {
        $(obj).closest('.row').find('.new_doctor').toggle('fast');
    }

    function quickAddHospital(obj) {
        $(obj).closest('.row').find('.new_hospital').toggle('fast');
    }

    function saveDoctor(obj) {
        let doctorInput = $(obj).closest('.new_doctor').find('.doctor');
        if (doctorInput.val().length < 3) {
            notifyError("Enter valid doctor's name");
            doctorInput.focus();
            return;
        }

        let spinner = $(obj).closest('.new_doctor').find('.loading_spinner');
        spinner.show();
        let name = doctorInput.val();
        $.post('?module=doctors&action=saveNewDoctor&format=json', {name: name}, function (data) {
            let result = JSON.parse(data);
            // console.log(result);
            if (result[0].status === "error") {
                notifyError(result[0].details);
                doctorInput.focus();
            } else {
                notifyMessage(result[0].details);
                $(obj).closest('.new_doctor').toggle('fast');
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
            if (result[0].status === "error") {
                notifyError(result[0].details);
                hospitalInput.focus();
            } else {
                notifyMessage(result[0].details);
                $(obj).closest('.new_hospital').toggle('fast');
            }
            spinner.hide();
        });
    }

    let RANDOM_BATCH_SELECTING_MODE = false;

    function clientTIN(obj) {
        if ($(obj).is(':checked')) {
            $('.clientTIN').show('fast');
        } else {
            $('.clientTIN').hide('slow');
        }
    }

    $("form").keypress(function (e) {
        //Enter key
        if (e.which == 13) {
            return false;
        }
    });

    function scanningBarcode(obj) {
        let barcode = $(obj).val();
        let locationid = $('#locationid').val();

        if (!locationid) {
            notifyError('choose location!', 4000);
            return;
        }
        $(obj).val('');
        $.get(`?module=stocks&action=getBarcodeStockId&format=json&locationid=${locationid}&barcode=${barcode}`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'error') {
                notifyError(result.msg || 'Error found');
            } else {
                let item = result.data;
                let input = `<input type="hidden" data-stockid="${item.stockid}" data-productid="${item.productid}" data-productname="${item.productname}" data-vatrate="${item.vat_rate}"
                                                  data-description="${item.description}" data-nonstock="${item.non_stock}" data-unitname="${item.unitname}" data-source="barcode">`;
                getProductDetails(input);
            }
            $(obj).focus();
        });
    }

    function fetchTyped(obj) {
        let item = {...$(obj).select2('data')[0]};
        let input = `<input type="hidden" data-stockid="${item.non_stock == 1 ? '' : item.id}" data-productid="${item.productid}" data-productname="${item.text}" data-vatrate="${item.vat_rate}"
                                                  data-description="${item.description}" data-nonstock="${item.non_stock}" data-unitname="${item.unitname}">`;
        getProductDetails(input);
        $(obj).select2('destroy');
        $(obj).empty();
        initSearchProductLocation();
    }

    function getLocationStock(obj) {
        <?if(!$order && !$proforma){?>
        if ($(obj).data('source') === 'location') {
            clearAllProducts();
        }
        <?}?>

        let locationid = $('#locationid').val();
        let categoryid = $('#category').val();
        let subcategoryid = $('#subcategory').val();
        let proformaid = $('.proformaid').val();

        if (locationid) {

            initSearchProductLocation();
            let quick_items = $("#quick-items");
            $(quick_items).empty();
            let load_spinner = `<div class="d-flex justify-content-center align-items-center" style="height: inherit;width: inherit">
                                    <div class="d-flex align-items-center">
                                        <span class="spinner-border mr-xs" style="height: 40px;width: 40px;"></span>
                                        <span>Loading Items...</span>
                                    </div>
                                </div>`;
            $(quick_items).append(load_spinner);
            $.get(`?module=stocks&action=getLocationStocks&format=json&quicksale=1&non_stock=yes&locationid=${locationid}&categoryid=${categoryid}&subcategoryid=${subcategoryid}&except_proforma=${proformaid}`, null, function (data) {
                $(quick_items).empty();
                let results = JSON.parse(data)['results'];

                if (results[0].test === 'No results') {
                    let item = `<div class="col-md-3 mb-xs p-xs">
                                    <div class="quick-item no-stock" disabled>
                                        No Item set for Quick sale
                                    </div>
                                </div>`;
                    $(quick_items).append(item);

                } else {
                    $.each(results, function (index, item) {
                        let quick_item = `<div class="col-md-3 mb-xs p-xs">
                                              <div class="quick-item" onclick="getProductDetails(this)" title="${item.text}" data-source="button"
                                                  data-stockid="${item.id}" data-productid="${item.productid}" data-productname="${item.text}" data-vatrate="${item.vat_rate}"
                                                  data-description="${item.description}" data-nonstock="${item.non_stock}" data-unitname="${item.unitname}">
                                                  ${item.text}
                                              </div>
                                          </div>`;
                        // setTimeout(function () {
                        $(quick_items).append(quick_item);
                        // }, 10 * index);
                    });
                }
            })
        }
    }

    function getProductDetails(obj) {
        let productTbody = $('#product-table-tbody');
        let location = $('#locationid').val();
        let stockid = $(obj).data('stockid');
        const productid = $(obj).data('productid');
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');
        let unitname = $(obj).data('unitname');
        let vatrate = $(obj).data('vatrate');
        let non_stock = $(obj).data('nonstock') == 1;
        let proformaid = $('.proformaid').val();
        if (!productid) {
            notifyError('Choose product first');
            return;
        }
        let exchange_rate = parseFloat($('#currency_amount').val()) || 0;
        let parent = $(`tr.productid-${productid}`);
        if (!non_stock) {
            $.get(`?module=products&action=getStockProductDetails&format=json&stockid=${stockid}&locationid=${location}&except_proforma=${proformaid}`, null, function (data) {
                let result = JSON.parse(data);
                let product = result[0];
                // console.log(product);
                if (product.found === 'yes') {
                    parent = $(`tr.productid-${productid}`);
                    if (parent.length === 0) {//adding first time
                        let unit_price = (product.quicksale_price_VAT / exchange_rate).toFixed(2);
                        let max_discount_percent = product.max_quicksale_disc_percent;
                        let price = (product.quicksale_price / exchange_rate).toFixed(2);
                        price = parseFloat(price);
                        let max_discount = (price * (max_discount_percent / 100)).toFixed(2);
                        let hidden_cost = (product.costprice / exchange_rate).toFixed(2);

                        let item = `<tr class="product-item productid-${productid}">
                                    <td>
                                        <button type="button" class="btn btn-danger btn-xs" title="remove item" onclick="removeItem(this)">
                                            <i class="fa fa-close"></i></button>
                                    </td>
                                    <td>
                                        <input type="text" readonly class="form-control p-xs input-sm inputs productname" value="${product.productname}">
                                        <input type="hidden" class="inputs stockid" name="stockid[]" value="${stockid}">
                                        <input type="hidden" class="inputs productid" name="productid[]" value="${product.productid}">
                                        <input type="hidden" class="inputs description" name="description[]" value="${product.description}">
                                        <input type="hidden" class="inputs validate_serialno" value="${product.validate_serialno}">
                                    </td>
                                    <td><input type="text" readonly class="form-control p-xs input-sm inputs" value="${product.unitname}"></td>
                                    <td>
                                        <input type="text" class="form-control p-xs text-center input-sm inputs qty" name="qty[]" autocomplete="off"
                                            oninput="checkMaxQty(this)" min="1" value="1" max="${product.quantity}"
                                            title="available stock ${product.quantity}">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" readonly class="form-control p-xs text-center input-sm inputs vat_rate" name="vat_rate[]"
                                            value="${product.vat_rate}">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" readonly class="form-control input-sm inputs price" name="price[]" value="${price}" data-source="price">
                                        <input type="hidden" class="input-sm inputs base_price" value="${product.quicksale_price}">
                                        <input type="hidden" class="inputs hidden_cost" name="hidden_cost[]" value="${hidden_cost}">
                                        <input type="hidden" class="inputs base_hidden_cost" value="${product.costprice}">
                                        <input type="hidden" class="inputs excamount">
                                        <input type="hidden" class="inputs discamount">
                                        <input type="hidden" class="inputs vatamount">
                                        <input type="hidden" class="inputs incamount">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" class="form-control p-xs text-center input-sm inputs discpercent" autocomplete="off"
                                               value="0" step="0.01" min="0" max="${max_discount_percent}" title="discount percentage\nMax: ${max_discount_percent}%"
                                                oninput="calProductAmount(${productid},this)" data-source="discpercent">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" class="form-control p-xs text-center input-sm inputs discount" name="discount[]"
                                               value="0" step="0.01" min="0" max="${max_discount}" title="discount \nMax: ${max_discount}" autocomplete="off"
                                                oninput="calProductAmount(${productid},this)" data-source="discount">
                                    </td>
                                    <td style="position: relative;">
                                        <div class="incprice_spinner" style="position: absolute;top:-5px;right: 0;display: none;">
                                           <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                        </div>
                                        <input type="text" readonly name="incprice[]" class="form-control input-sm inputs unit_price" value="${unit_price}" data-source="unit_price">
                                        <input type="hidden" class="sinc" name="sinc[]" value="1">
                                    </td>
                                    <td>
                                        <input type="text" readonly class="form-control input-sm inputs incamount_label">
                                    </td>
                                    <td class="extra-cell d-flex align-items-center">
                                        <div class="btn-holder d-flex align-items-center">
                                            <button type="button" title="view product" class="btn btn-info btn-xs mr-xs" data-toggle="modal" data-target="#product-view-modal"
                                                    data-productid="${product.productid}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="modals-holder"></div>
                                    </td>
                                </tr>`;
                        $(productTbody).append(item);
                        $('#product-holder').animate({
                            scrollTop: $('#product-holder').stop().prop("scrollHeight")
                        }, 500);

                        if (!ORDER_MODE) {
                            if (product.prescription_required === '1' || product.track_expire_date === '1') {
                                let btn = `<button type="button" title="prescription and batch details" class="btn btn-warning btn-xs ml-xs"
                                           data-toggle="modal" data-target="#prescriptionModal-${stockid}">
                                       <i class="fa fa-list"></i>
                                   </button>`;
                                $(productTbody).find(`tr.productid-${product.productid} .btn-holder`).append(btn);
                                // console.log(product.batch_stock.length);
                                if (product.batch_stock == null || product.batch_stock.length == 0) {
                                    notifyError(`Product ${product.productname} has no valid batch for sale \n\n CHECK EXPIRE DATES`, 4000);
                                    $(productTbody).find(`tr.productid-${product.productid}`).remove();
                                    beep(`no_stock`);
                                    return;
                                }
                                addBatchPrescriptionModal(product.productid, product);
                                distributeQtyToBatches(product.productid);
                            } else {
                                // console.log(product);
                                if (product.quantity == null || product.quantity == 0) {
                                    notifyError(`Product ${product.productname} has not enough STOCK`, 4000);
                                    $(productTbody).find(`tr.productid-${product.productid}`).remove();
                                    beep(`no_stock`);
                                    return;
                                }
                            }
                            if (product.trackserial === '1') {
                                addSerialEntry(product.productid);
                                populateSerialTable(product.productid);
                            }
                        }
                        calProductAmount(product.productid);
                    } else {//increase qty second time
                        let qty = parseInt($(parent).find('.qty').val());
                        if ($(obj).data('source') === 'barcode') {
                            $(parent).find('.qty').val(qty + 1);
                        } else {
                            $(parent).find('.qty').val(qty + 1).focus();
                        }

                        checkMaxQty($(parent).find('.qty'), false);
                        calProductAmount(productid);
                    }
                } else {
                    notifyError('Stock not found!');
                }
            });
        } else {
            if (parent.length === 0) {
                let item = `<tr class="product-item productid-${productid}">
                                    <td>
                                        <button type="button" class="btn btn-danger btn-xs" title="remove item" onclick="removeItem(this)">
                                            <i class="fa fa-close"></i></button>
                                    </td>
                                    <td class="non-stock" style="position: relative">
                                        <small class="text-danger non-stock-label" style="display: none">non-stock</small>
                                        <input type="text" readonly class="form-control p-xs input-sm inputs productname" value="${productname}">
                                        <input type="hidden" class="inputs stockid" name="stockid[]">
                                        <input type="hidden" class="inputs productid" name="productid[]" value="${productid}">
                                        <input type="hidden" class="inputs description" name="description[]" value="${description}">
                                        <input type="hidden" class="inputs validate_serialno">
                                    </td>
                                    <td><input type="text" readonly class="form-control p-xs input-sm inputs" value="${unitname}"></td>
                                    <td>
                                        <input type="text" class="form-control p-xs text-center input-sm inputs qty" name="qty[]" autocomplete="off"
                                            oninput="checkMaxQty(this)" min="1" value="1">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" readonly class="form-control p-xs text-center input-sm inputs vat_rate" name="vat_rate[]"
                                            value="${vatrate}">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" step="0.01" min="0" class="form-control input-sm inputs price" name="price[]" value="0"
                                               oninput="calProductAmount(${productid},this)" data-source="price">
                                        <input type="hidden" class="input-sm inputs base_price" value="0">
                                        <input type="hidden" class="inputs hidden_cost" name="hidden_cost[]" value="0">
                                        <input type="hidden" class="inputs base_hidden_cost" value="0">
                                        <input type="hidden" class="inputs excamount">
                                        <input type="hidden" class="inputs discamount">
                                        <input type="hidden" class="inputs vatamount">
                                        <input type="hidden" class="inputs incamount">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" class="form-control p-xs text-center input-sm inputs discpercent" autocomplete="off"
                                               value="0" step="0.01" min="0" max="100" oninput="calProductAmount(${productid},this)"
                                               data-source="discpercent">
                                    </td>
                                    <td class="${!CAN_DISCOUNT ? 'hidden' : ''}">
                                        <input type="text" readonly class="form-control p-xs text-center input-sm inputs discount" name="discount[]"
                                               value="0" step="0.01" min="0" data-source="discount">
                                    </td>
                                    <td style="position: relative;">
                                        <div class="incprice_spinner" style="position: absolute;top:-5px;right: 0;display: none;">
                                           <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                        </div>
                                        <input type="text" step="0.01" min="0" name="incprice[]" class="form-control input-sm inputs unit_price" value="0"
                                               oninput="calProductAmount(${productid},this)" data-source="unit_price">
                                        <input type="hidden" class="sinc" name="sinc[]" value="1">
                                    </td>
                                    <td>
                                        <input type="text" readonly class="form-control input-sm inputs incamount_label">
                                    </td>
                                    <td class="extra-cell d-flex align-items-center">
                                        <div class="btn-holder d-flex align-items-center">
                                            <button type="button" title="view product" class="btn btn-info btn-xs mr-xs" data-toggle="modal" data-target="#product-view-modal"
                                                    data-productid="${productid}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="modals-holder"></div>
                                    </td>
                                </tr>`;
                $(productTbody).append(item);
                $('#product-holder').animate({
                    scrollTop: $('#product-holder').stop().prop("scrollHeight")
                }, 500);
            } else {
                $(parent).find('.productname').focus();
            }
        }
    }

    function removeItem(obj) {
        $(obj).closest('tr').remove();
        checkErrors();
        calcAmount();
        beep(`remove_item`);
    }

    function addBatchPrescriptionModal(productid, product) {
        let parent = $(`tr.productid-${productid}`);
        let stockid = $(parent).find('.stockid').val();
        let productname = $(parent).find('.productname').val();
        if (!stockid) {
            notifyError(`product ${productname} cant have batches`, 5000);
            return;
        }

        let randomBatchPermission = ``;
        if (CAN_SELL_RANDOM_BATCH) {
            randomBatchPermission = `<div>
                                        <button title="Choose from random" type="button" class="btn btn-default btn-sm" onclick="chooseRandomBatches(this)">
                                            Choose random
                                        </button>
                                        <button title="refresh batches" type="button" class="btn btn-info btn-sm" style="margin-left: 20px;" onclick="refreshBatches(this)">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                     </div>`;
        }
        let inputState = "readonly disabled";
        let prescriptCol = '';
        let batchCol = 'col-md-7 col-md-offset-2';
        if (product.prescription_required === '1') {
            batchCol = 'col-md-6 col-md-offset-1';
            prescriptCol = 'col-md-5';
        }
        let batches = ``;
        $.each(product.batch_stock, function (i, item) {
            batches += `<tr>
                         <td>
                             <input type="hidden" ${inputState} class="batch_id"
                                    name="batch[${stockid}][batchId][]"
                                    value="${item.batchId}">
                             <input type="text" readonly placeholder="batch no."
                                    class="form-control input-sm batch_no"
                                    value="${item.batch_no}">
                         </td>
                         <td>
                             <input type="number" readonly placeholder="qty in"
                                    class="form-control input-sm batch_qty"
                                    value="${item.total}">
                         </td>
                         <td>
                             <input type="text" readonly placeholder="qty in"
                                    class="form-control input-sm"
                                    value="${item.expire_date}" title="${item.expire_description}">
                         </td>
                         <td style="width: 20%;">
                             <input type="text" placeholder="qty" ${inputState} name="batch[${stockid}][qty_out][]" min="1" max="${item.total}" required
                                    class="form-control input-sm batch_qty_out" oninput="updateQty(this)">
                         </td>
                     </tr>`;
        });

        let modal = `<div class="modal fade prescription_modal" id="prescriptionModal-${stockid}" role="dialog" aria-labelledby="prescriptionModal-${stockid}" aria-hidden="true">
                        <div class="modal-dialog modal-lg prescription">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Product: <span class="text-rosepink productName">${productname}</span></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="${prescriptCol} prescription-inputs"></div>
                                        <div class="${batchCol}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div style="font-size: 16pt;">Total Qty: <label class="totalBatchQtyChosen text-rosepink"></label></div>
                                                ${randomBatchPermission}
                                            </div>
                                            <table class="table table-hover table-bordered table-condensed batch-table" style="font-size:10pt;">
                                                <thead>
                                                <tr>
                                                    <td>Batch No</td>
                                                    <td>Stock Qty</td>
                                                    <td>Expire Date</td>
                                                    <td>Sell Qty</td>
                                                </tr>
                                                </thead>
                                                <tbody>${batches}</tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default btn-sm" onclick="closeModal(this)">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>`;

        $(parent).find('.modals-holder').append(modal);
        // console.log(parent);
        if (product.prescription_required === '1') {
            addPrescriptionEntries(stockid);
        }
    }

    function addPrescriptionEntries(stockid) {
        let entries = `<div class="row">
                           <div class="col-md-6">
                               <div class="d-flex justify-content-between align-items-center">
                                   <label style="font-size:10pt">Prescription Doctor <span class="text-danger">*</span></label>
                                   <button type="button" onclick="quickAddDoctor(this)" title="Quick add doctor"
                                           class="btn btn-info btn-xs ml-lg">Add
                                   </button>
                               </div>
                               <div class="col-md-12 new_doctor mb-md" style="display:none ;">
                                   <div class="col-md-10"
                                        style="padding:0;position: relative">
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
                                               class="btn btn-success btn-xs ml-sm mt-xs">Save
                                       </button>
                                   </div>
                               </div>
                               <select title="Choose doctor" class="form-control prescription_doctor" name="prescription[${stockid}][doctor]"
                                       required>
                               </select>
                           </div>
                           <div class="col-md-6">
                               <div class="d-flex justify-content-between align-items-center">
                                   <label style="font-size:10pt">Prescription Hospital <span class="text-danger">*</span></label>
                                   <button type="button" onclick="quickAddHospital(this)" title="Quick add Hospital"
                                           class="btn btn-info btn-xs ml-lg">Add
                                   </button>
                               </div>
                               <div class="col-md-12 new_hospital mb-md"
                                    style="padding:0;display: none;">
                                   <div class="col-md-10"
                                        style="padding:0;position: relative">
                                       <div class="loading_spinner"
                                            style="position: absolute;top:5px;right: 10px;display: none;">
                                           <object data="images/loading_spinner.svg"
                                                   type="image/svg+xml" height="30"
                                                   width="30"></object>
                                       </div>
                                       <input type="text" class="form-control hospital" placeholder="hospital's name">
                                   </div>
                                   <div class="col-md-1">
                                       <button type="button" onclick="saveHospital(this)" title="Quick add doctor"
                                               class="btn btn-success btn-xs ml-sm mt-xs">Save
                                       </button>
                                   </div>
                               </div>
                               <select title="Choose hospital" class="form-control prescription_hospital" name="prescription[${stockid}][hospital]"
                                       required>
                               </select>
                           </div>
                       </div>
                       <div class="row" style="margin:15px 0;">
                           <div class="col-md-2">
                               <label style="font-size:10pt" class="checkbox-inline" title="Check if is referral">
                                   <input type="checkbox" name="prescription[${stockid}][referred]" value="1">Referred?
                               </label>
                           </div>
                       </div>
                       <div class="row mt-sm">
                           <div class="col-md-12">
                               <label style="font-size:10pt">Prescription <span class="text-danger">*</span></label>
                               <textarea name="prescription[${stockid}][text]"
                                         class="form-control text-sm prescription_text"
                                         rows="4" required></textarea>
                           </div>
                       </div>`;
        let parent = $(`input.stockid[value=${stockid}]`).closest('.product-item');
        $(parent).find('.prescription-inputs').addClass('has-prescription').empty().append(entries);
        initPrescriptionInputs();
    }

    function initPrescriptionInputs() {
        initSelectAjax('.prescription_doctor', "?module=doctors&action=getDoctors&format=json", "choose doctor");
        initSelectAjax('.prescription_hospital', "?module=hospitals&action=getHospitals&format=json", "choose hospital");
    }

    $('#paymentModal').on('show.bs.modal', function () {
        PAYMENT_MODAL_SHOWN = true;
        $('#receivedAmount').val('').trigger('keyup');
        let required = parseFloat($('.total_incamount').val());
        // console.log(required > 0);
        if (required > 0) {
            $('#receivedAmount').prop('readonly', false);
            thousands_separator($('#receivedAmount'), 2);
            setTimeout(() => {
                $('#receivedAmount').focus();
                // console.log(document.activeElement);
            }, 500)
        }
    }).on('hidden.bs.modal', function () {
        PAYMENT_MODAL_SHOWN = false;
    });

    function calcAmount() {
        $('#total-items-label').text(`${$('.product-item').length} items`);
        let currencyname = $('#currencyid').find(':selected').data('currencyname');
        let total_excamount = 0, total_discamount = 0, total_incamount = 0, total_vatamount = 0;
        $('.product-item').each(function (i, parent) {
            let vatamount = parseFloat($(parent).find('.vatamount').val()) || 0;
            let excamount = parseFloat($(parent).find('.excamount').val()) || 0;
            let discamount = parseFloat($(parent).find('.discamount').val()) || 0;
            let incamount = parseFloat($(parent).find('.incamount').val()) || 0;
            total_vatamount += vatamount;
            total_excamount += excamount;
            total_discamount += discamount;
            total_incamount += incamount;
        });

        $('.total_vatamount_label').text(`${currencyname} ${numberWithCommas(total_vatamount.toFixed(2))}`);
        $('.total_discamount_label').text(`${currencyname} ${numberWithCommas(total_discamount.toFixed(2))}`);
        $('.total_excamount_label').val(`${currencyname} ${numberWithCommas(total_excamount.toFixed(2))}`);
        $('.total_incamount_label').text(`${currencyname} ${numberWithCommas(total_incamount.toFixed(2))}`);

        $('.total_vatamount').val(total_vatamount.toFixed(2));
        $('.total_excamount').val(total_excamount.toFixed(2));
        $('.total_discamount').val(total_discamount.toFixed(2));
        $('.total_incamount').val(total_incamount.toFixed(2));
        needsApproval();
    }

    function needsApproval() {
        check_sale_limit();


        let obj = $('.saveoptions');
        let parent = $(obj).closest('.row');
        let savetype = $(obj).val();
        let credit_days = $(parent).find(".credit_days");
        let credit_btn = $('#credit-payment-btn');
        let cash_btn = $('#cash-payment-btn');

        let need_approval = $('#need-approval').is(':checked') || $('#sale-limit').val() === '1';

        if (savetype === "<?=PAYMENT_TYPE_CREDIT?>") {
            credit_days.show('fast');

            $(credit_btn).find('span').text('Credit Sale');
            $(credit_btn).show('fast');
            $(cash_btn).hide('fast');
        } else if (savetype === "<?=PAYMENT_TYPE_CASH?>") {
            credit_days.hide('fast');
            if (need_approval) {
                $(credit_btn).find('span').text('Save Sale');
                $(credit_btn).show('fast');
                $(cash_btn).hide('fast');
            } else {
                $(credit_btn).hide('fast');
                $(cash_btn).show('fast');
            }
        }
    }

    function check_sale_limit() {
        let sale_limit = parseFloat(`<?=$salesPerson['sale_limit']?>`);
        if (sale_limit <= 0) return;
        let total_amount = parseFloat($('.total_incamount').val());
        let currency_amount = parseFloat($('#currency_amount').val());
        total_amount *= currency_amount;
        if (total_amount > sale_limit && $(`[name="sales[paymenttype]"]`).val() === `<?=PAYMENT_TYPE_CASH?>`) {
            // console.log(total_amount,currency_amount,sale_limit);
            $('#sale-limit-alert').show();
            $('#sale-limit').val('1');
        } else {
            $('#sale-limit-alert').hide();
            $('#sale-limit').val('');
        }
    }

    function restoreOriginalPrices() {
        $('.product-item').each(function (i, product) {
            let productid = $(product).find('.productid').val();
            $(product).find('.discpercent').val(0);
            $(product).find('.discount').val(0);
            calProductAmount(productid);
        });
    }

    $('#discount-modal').on('show.bs.modal', function () {
        restoreOriginalPrices();
        $('#discount-modal').find('.overall-discount-percent').val('');
        $('#discount-modal').find('.overall-discount').val('');
    });

    function overallDiscount(obj) {
        let overall_discpercent = parseFloat($('#discount-modal').find('.overall-discount-percent').val()) || 0;
        let overall_discount = parseFloat($('#discount-modal').find('.overall-discount').val()) || 0;
        let total_excamount = parseFloat($('.total_excamount').val()) || 0;
        if ($(obj).data('source') === 'percent') {
            overall_discount = total_excamount * overall_discpercent / 100;
            $('#discount-modal').find('.overall-discount').val(overall_discount.toFixed(2));
        } else {
            overall_discpercent = (overall_discount / total_excamount) * 100;
            $('#discount-modal').find('.overall-discount-percent').val(overall_discpercent.toFixed(2));

        }
    }

    function distributeDiscount() {
        let overall_percent = parseFloat($('#discount-modal').find('.overall-discount-percent').val()) || 0;
        $('.product-item').each(function (i, product) {
            let productid = $(product).find('.productid').val();
            $(product).find('.discpercent').val(overall_percent);
            $(product).find('.discount').val(0);
            calProductAmount(productid, $(product).find('.discpercent'));
        });
        $('#discount-modal').modal('hide');
        $('#discount-modal').find('.overall-discount-percent').val('');
        $('#discount-modal').find('.overall-discount').val('');
    }

    function validateInputs(e) {
        // console.log(e);
        let valid = true;
        try {
            //check if no product selected
            if ($('.product-item').length === 0) {
                notifyError("Choose at-least one product!");
                $('#paymentModal').modal('hide');
                return false;
            }


            if ($('.product-item.external-product').length > 0) {
                notifyError('System found external product, issue GRN or remove it from list', 5000);
                return false;
            }
            if ($('.product-item.out-of-stock').length > 0) {
                notifyError(`System found ${$('tr.out-of-stock').length} product out of stock`);
                return false;
            }

            if (ORDER_MODE) {
                $('#spinnerHolder').show();
                $('#order-print-size-modal').modal('hide');
                return true;
            }

            $('.product-item').each(function (i, product) {
                let productname = $(product).find('.productname').val();
                let qty = parseInt($(product).find('.qty').val());
                let unit_price = parseFloat($(product).find('.unit_price').val()) || 0;
                if (qty < 1) {
                    notifyError(`Enter valid qty for product ${productname}`, 5000);
                    findItem(`<input data-productname="${productname}">`);
                    valid = false;
                    return false;
                }
                if (unit_price < 0) {
                    notifyError(`Enter valid price for product ${productname}`, 5000);
                    findItem(`<input data-productname="${productname}">`);
                    valid = false;
                    return false;
                }
            });
            if (!valid) {
                $('#paymentModal').modal('hide');
                return false;
            }

            //validate prescription inputs
            $('.prescription-inputs.has-prescription').each(function () {
                let doctor = $(this).find('.prescription_doctor').val();
                let hospital = $(this).find('.prescription_hospital').val();
                let prescription = $(this).find('.prescription_text').val();

                if (!doctor || !hospital || !prescription) {
                    valid = false;
                    let productname = $(this).closest('.product-item').find('.productname').val();
                    $('#paymentModal').modal('hide');
                    $(this).closest('.product-item').find('.productname').focus();
                    notifyError(`${productname} missing prescription!`, 5000);
                    return false;
                }
            });
            if (!valid) return false;

            //check serialnos
            $('.add-serial-btn').each(function (i, btn) {
                if ($(btn).closest('.product-item').find('.serial-modal table tbody tr').length === 0) {
                    valid = false;
                    let productname = $(btn).closest('.product-item').find('.productname').val();
                    notifyError(`${productname} require serial no`, 3000);
                    return false;
                }
            });
            if (!valid) return false;

            //validate serialnos
            $('.serial_number').each(function () {
                let parentSerialRow = $(this).closest('tr');
                let icon = $(parentSerialRow).find('td.status i');  //status icon
                if ($(icon).hasClass('fa-close')) { //not validated
                    valid = false;
                    let productname = $(this).closest('.product-item').find('.productname').val();
                    $('#paymentModal').modal('hide');
                    $(this).closest('.product-item').find('.productname').focus();
                    notifyError(`${productname} serial number not validated!`);
                    return false;
                }
            });
            if (!valid) return false;
            if ($('.saveoptions').val() === "<?=PAYMENT_TYPE_CASH?>") {

                let requiredAmount = parseFloat($('.total_incamount').html());
                let receivedAmount = removeCommas($('#receivedAmount').val());
                // console.log('required', requiredAmount, 'paid', receivedAmount);
                if (receivedAmount < requiredAmount) {
                    notifyError('Received amount not enough', 2500);
                    $('#receivedAmount').focus();
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

            } else {
                if (!validClient()) return false;
            }
            //check order qty vs stock qty
            if ($('.input-stock-warning').length > 0) {
                valid = confirm('Some of the product order quantities not fulfilled,\nDo you want to continue?');
            }
            if (!valid) return false;



            <?if(CS_QUICKSALE_KEYBOARDONLY){?>
            //confirm receipt type
            let element = $('.receipt_type')[0].localName;
            if (element === 'select' && $('.receipt_type option').length > 1) {
                let confirm_fisc = confirm('Do you want to fiscalize?');
                $('.receipt_type option').each(function (i, item) {
                    console.log();
                    if (confirm_fisc) {
                        if ($(item).val() !== 'sr') {
                            $('.receipt_type').val($(item).val());
                            return false;
                        }
                    } else {
                        if ($(item).val() === 'sr') {
                            $('.receipt_type').val($(item).val());
                            return false;
                        }
                    }
                })
            }

            // return false;
            <?}?>


            //check receipt
            if (!$('.receipt_type').val()) {
                notifyError('Specify Receipt Type', 5000);
                $('#paymentModal').modal('show');
                $('#paymentModal .receipt_type').focus();
                return false;
            }

            if (valid) {
                //show spinner
                $('#paymentModal').modal('hide');
                $('#spinnerHolder').show();
            }

            return valid;

        } catch (e) {
            console.log(e);
            return false;
        }
    }

    function updateQty(obj) {
        let totalBatchQty = 0;
        let maxValue = parseInt($(obj).attr('max'));
        let currentValue = parseInt($(obj).val());
        if (currentValue > maxValue) {
            notifyError('Input quantity exceed the stock quantity in the batch');
            $(obj).val(maxValue);
            $(obj).addClass('max-input-error');
            setTimeout(function () {
                $(obj).removeClass('max-input-error');
            }, 1000);

            updateQty(obj);
            return;
        }
        let parent = $(obj).closest('.product-item');
        $(obj).closest('tbody').find('.batch_qty_out').each(function () {
            totalBatchQty += parseInt($(this).val()) || 0;
        });
        //update product qty
        $(parent).find('.qty').val(totalBatchQty);
        $(parent).find('.totalBatchQtyChosen').text(totalBatchQty);

        let productid = $(parent).find('.productid').val();
        calProductAmount(productid);
    }

    function chooseRandomBatches(obj) {
        RANDOM_BATCH_SELECTING_MODE = true;
        let parent = $(obj).closest('.product-item');
        $(parent).find('.batch-table tbody tr').each(function () {
            //enable editing
            $(this).find('.batch_id').prop('disabled', false);
            $(this).find('.batch_qty_out').prop('disabled', false);
            $(this).find('.batch_qty_out').prop('readonly', false);
        });
    }

    function refreshBatches(obj) {
        RANDOM_BATCH_SELECTING_MODE = false;
        let parent = $(obj).closest('.product-item');
        //disable editing
        $(parent).find('.batch-table tbody tr').each(function () {
            $(this).find('.batch_id').prop('disabled', true);
            $(this).find('.batch_qty_out').prop('disabled', true);
            $(this).find('.batch_qty_out').prop('readonly', true);
            $(this).find('.batch_qty_out').val('');
        });

        //distribute batch again
        let productid = $(parent).find('.productid').val();
        distributeQtyToBatches(productid);
    }

    //distributes qty to stock batches
    function distributeQtyToBatches(productid) {
        let parent = $(`tr.productid-${productid}`);
        let qty = parseInt($(parent).find('.qty').val());
        let remainQty = qty;
        $(parent).find('.totalBatchQtyChosen').text(qty);

        if ($(parent).find('.choose_random').val() === '1') return;
        //clear inputs first
        $(parent).find('.batch_qty_out').val('');

        //loop until remainQty is zero
        $(parent).find('.batch-table tbody tr').each(function () {
            let batchQty = parseInt($(this).find('.batch_qty').val());

            if (batchQty >= remainQty) { //if batch qty covers whole qty
                $(this).find('.batch_qty_out').val(remainQty);
                //enable inputs
                $(this).find('.batch_id').prop('disabled', false);
                $(this).find('.batch_qty_out').prop('disabled', false);
                return false;
            } else {
                $(this).find('.batch_qty_out').val(batchQty);
                //enable inputs
                $(this).find('.batch_id').prop('disabled', false);
                $(this).find('.batch_qty_out').prop('disabled', false);
                remainQty -= batchQty;
            }

            if (remainQty === 0) return false; //break the loop
        });
    }

    //prescription modal close
    function closeModal(obj) {
        RANDOM_BATCH_SELECTING_MODE = false;
        $(obj).closest('.modal').modal('hide');
        let parent = $(obj).closest('.product-item');

        //disable unused batches
        $(parent).find('.batch-table tbody tr').each(function () {
            $(this).find('.batch_qty_out').prop('readonly', true);
            let value = parseInt($(this).find('.batch_qty_out').val());
            if (!value) {
                $(this).find('.batch_id').prop('disabled', true);
                $(this).find('.batch_qty_out').prop('disabled', true);
            }
        });
    }

    let qtyTimer = null;

    function checkMaxQty(obj, timer = true) {
        RANDOM_BATCH_SELECTING_MODE = false;
        let max = parseInt($(obj).attr('max'));
        let non_stock = $(obj).closest('tr').find('.non-stock').length > 0;
        let inputQty = parseInt($(obj).val()) || 0;
        if (qtyTimer) clearTimeout(qtyTimer);
        let saveOrderBtn = $('#save-order-btn');
        if (ORDER_MODE) $(saveOrderBtn).prop('disabled', true);

        if (!non_stock && inputQty > max) {
            $(obj).addClass('max-input-error');
            $(obj).val(max);
        } else if (inputQty < 1 || isNaN(inputQty)) {
            $(obj).addClass('max-input-error');
        }

        let callback = function () {
            if (!non_stock && inputQty > max) {
                notifyError(`Not enough Stock, only ${max} is available`, 2000);

                $(obj).val(max);
                beep(`no_stock`);
                $(obj).addClass('max-input-error');
            } else if (inputQty <= 0) {
                notifyError(`Enter valid quantity`, 2000);
                $(obj).val(1);
                $(obj).addClass('max-input-error');
            } else {
                $(obj).val(inputQty);
            }
            setTimeout(function () {
                $(obj).removeClass('max-input-error');
            }, 500);
            let productid = $(obj).closest('tr').find('.productid').val();
            // console.log(productid);


            calProductAmount(productid);
            distributeQtyToBatches(productid);
            populateSerialTable(productid);
            $(saveOrderBtn).prop('disabled', false);
        };

        timer ? qtyTimer = setTimeout(callback, 1000) : callback();
    }

    function addSerialEntry(productid) {
        let parent = $(`tr.productid-${productid}`);
        let stockid = $(parent).find('.stockid').val();
        let productname = $(parent).find('.productname').val();
        if (!stockid) {
            notifyError(`product ${productname} cant have serial no`);
            return false;
        }
        let validate_serialno = $(parent).find('.validate_serialno').val() === '1';
        let btn = `<button type="button" title="serial numbers" class="btn btn-default btn-xs ml-xs add-serial-btn" data-toggle="modal"
                           data-target="#serialModal-${stockid}">
                       <i class="fa fa-barcode"></i>
                   </button>`;
        $(parent).find('.btn-holder').append(btn);

        let modal = `<div class="modal fade serial-modal" id="serialModal-${stockid}" tabindex="-1" role="dialog" aria-labelledby="serialModal-${stockid}"
                          aria-hidden="true">
                         <div class="modal-dialog modal-dialog-center">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                     </button>
                                     <h4 class="modal-title productName">Serial No:  <span class="text-primary">${productname}</span></h4>
                                 </div>
                                 <div class="modal-body">
                                     <p class="text-danger text-sm">${validate_serialno ? 'Validates from Stock' : 'Enter manually'}</p>
                                     <div style="max-height: 60vh;overflow-y: auto">
                                         <table class="table table-bordered" style="font-size: 10pt">
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
                                 </div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                                 </div>
                             </div>
                         </div>
                     </div>`;
        $(parent).find('.modals-holder').append(modal);
    }

    function populateSerialTable(productid) {
        let parent = $(`tr.productid-${productid}`);
        let stockid = $(parent).find('.stockid').val();
        let productname = $(parent).find('.productname').val();
        if ($(parent).find('.serial-modal tbody').length > 0 && !stockid) {
            notifyError(`product ${productname} cant populate serial no`);
            return false;
        }
        let qty = parseInt($(parent).find('.qty').val());
        $(parent).find('.serial-modal tbody').empty();
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
            $(parent).find('.serial-modal tbody').append(row);
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
            notifyError('Duplicate serial no found!', 5000);
            return;
        }
        let number = $.trim($(obj).val());
        if (!number) return;
        $(obj).val(number);

        spinner.show();
        if (serialValidateTimer) clearTimeout(serialValidateTimer);
        serialValidateTimer = setTimeout(function () {
            let stockid = $(obj).closest('.product-item').find('.stockid').val();
            // console.log(number, 'stockid', stockid);
            //ajax query
            $.get(`?module=serialnos&action=validateSerialno&format=json&number=${number}&stockid=${stockid}`, null, function (data) {
                let result = JSON.parse(data);
                spinner.hide();
                // console.log(result);
                if (result.status === 'success') {
                    notifyMessage(result.message);
                    parentSerialRow.attr('title', result.message);
                    icon.removeClass('fa-close text-danger');
                    icon.addClass('fa-check text-success');
                } else {
                    notifyError(result.message);
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

    function clientDet(obj) {
        let clientid = $('#clientid').find('option:selected').val();
        let clientinfo = $('#clientid').select2('data')[0];
        $('#clientname').text(clientinfo.text);
        $('#clientmobile').text(clientinfo.mobile);
        $('#clienttin').text(clientinfo.tinno);
        $('#clientvatno,.clientvatno').text(clientinfo.vatno);

        $.get(`?module=clients&action=getClientCardInfo&format=json&clientId=${clientid}`, null, function (data) {
            let results = JSON.parse(data);
            if (results.found === 'yes') {
                if (results.details.cardId != null) {
                    $('.redeem_card').show();
                    $('.assign_new_card').hide();
                } else {
                    $('.redeem_card').hide();
                    $('.assign_new_card').show();
                }
            }
        });
    }

    function newroyaltyno(obj) {
        var checkbox = $(obj);
        if (checkbox.prop('checked') == true) {

            $('.royaltyno').show('slow');

        } else if (checkbox.prop('checked') == false) {

            $('.royaltyno').hide('slow');

        }
    }

    function assignCard() {
        let clientId = $('[name="client[clientid]"]').val();
        let cardId = $('.royaltyno').find('select').val();
        if (cardId == null) {
            notifyError('Choose card first');
            return;
        }

        let spinner = $('#saveCardSpinner');
        spinner.show();
        // console.log(cardId, clientId);
        $.post('?module=clients&action=assignCard&format=json', {
            cardId: cardId,
            clientId: clientId
        }, function (data) {
            let results = JSON.parse(data);
            spinner.hide();
            // console.log(results);
            if (results.status === 'success') {
                triggerMessage(results.message);
                $('.royaltyno').hide('fast');
                // alert(results.message);
            } else {
                alert(results.message);
            }
        })
    }

    //forfullpayment
    function paidAmount(e, obj) {
        let validAmount = false;
        let received = removeCommas($(obj).val()) || 0;
        let required = parseFloat($('.total_incamount').val());
        // console.log(required);
        if (isNaN(required) || required === 0) {
            $('#confirm-btn').prop('disabled', true);
            $(obj).val('');
            thousands_separator($(obj));
            return;
        }

        if (received < required) {
            $('#confirm-btn').prop('disabled', true);
        } else {
            $('#confirm-btn').prop('disabled', false);
            validAmount = true;
        }

        let change = parseFloat((received - required).toFixed(2));
        if (change > 0) {
            $('.change-amount').text(numberWithCommas(change));
            $('.change-holder').fadeIn();
        } else {
            $('.change-holder').fadeOut('fast', function () {
                $('.change-amount').text('');
            });
        }
        let code = e.keyCode ? e.keyCode : e.which;
        if (code === 13 && validAmount) { //if enter pressed
            // console.log(code);
            $('#confirm-btn').trigger('click');
        }
    }


    function checkPaymentMethod(obj) {
        let cardHolder = $('#creditCardHolder');
        let method = $(obj).val();
        if (method === '<?=PaymentMethods::CREDIT_CARD?>') {
            $(cardHolder).show('fast');
            $('#electronic-account').focus();
        } else {
            $(cardHolder).hide('fast');
            $('#credit_card_no,#electronic-account').val('');
        }
    }

    function saveOptions() {
        needsApproval();
    }

    function validClient() {
        let clientid = $('#clientid').val();
        // console.log(clientid);
        if (clientid == '1' && $('.saveoptions').val() === "<?=PAYMENT_TYPE_CREDIT?>") {
            notifyError("Cash client can not be used for credit sales!", 6000);
            return false;
        }
        return true;
    }

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    function removeCommas(amount) {
        return parseFloat(amount.replace(/,/g, ''));
        //return parseFloat(amount.replace(",", ""));
    }

    function beep(audio_type) {
        <?if(CS_QUICK_SALE_BEEP){?>
        let snd;
        switch (audio_type) {
            case `no_stock`:
                snd = new Audio(`<?=BEEP_SOUND['no_stock']?>`);
                snd.play();
                break;
            case `reduce_qty`:
                snd = new Audio(`<?=BEEP_SOUND['reduce_qty']?>`);
                snd.play();
                break;
            case `remove_item`:
                snd = new Audio(`<?=BEEP_SOUND['remove_item']?>`);
                snd.play();
                break;
            default:
                break;
        }
        <?}?>
    }

</script>
