<style media="screen">

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
        /* display:none; */
    }

    .pop-col-holder {
        background: white;
        width: 85%;
        margin: 0 auto;
        position: relative;
        top: 117px;
        border-radius: 5px;
        padding: 16px;
    }

    .popup_info h4 {
        border-bottom: 1px solid rgba(158, 158, 158, 0.5215686274509804);
        width: 100%;
        padding: 20px;
    }

    .popup_info p {
        font-size: 15px;
    }

    .popup_container .row {
        margin-top: 10px;
    }

    .select2-container .select2-selection--single {
        height: 36px;
    }

    .num-hide {
        display: block;
        background: white;
        width: 32px;
        height: 28px;
        position: absolute;
        right: 21px;
        top: 40px;
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


    #items-holder {
        transition: 0.5s;
    }

    #items-holder .row {
        border: 1px dashed grey;
        border-radius: 8px;
        margin: 5px;
        padding: 5px;
    }

    .holder-scroll {
        max-height: 70vh;
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: inset 0 0 5px grey;
        padding: 5px;
        border-radius: 4px;
        transition: 0.2s;
    }

    .external-label {
        font-size: 7pt;
        position: absolute;
        top: 0;
        left: 0;
        color: orangered;
        transform: rotateZ(335deg);
        font-weight: bold;
    }

    .non-stock .non-stock-label {
        display: block !important;
        position: absolute;
        top: -5px;
        left: 0;
        z-index: 4;
        transform: rotateZ(335deg);
    }

    input.productname {
        cursor: pointer !important;
        background: white !important;
    }

    input[type='checkbox'].print_extra {
        width: 30px;
        height: 30px;
    }
</style>
<header class="page-header">
    <h2>
        <? if ($proforma && $COPY) { ?>
            Copy Proforma
        <? } elseif ($proforma) { ?>
            Edit Proforma No: <span class="text-primary"><?= $proforma['proformaid'] ?></span>
        <? } else { ?>
            Create Proforma
        <? } ?>
    </h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h1 class="text-weight-semibol text-danger">Please wait</h1>
    </div>
</div>


<div class="row d-flex justify-content-center">
    <div class="col-xs-12 col-md-11">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    <? if ($proforma && $COPY) { ?>
                        Copy Proforma
                    <? } elseif ($proforma) { ?>
                        Edit Proforma No: <span class="text-primary"><?= $proforma['proformaid'] ?></span>
                    <? } else { ?>
                        Create Proforma
                    <? } ?>
                </h2>
            </header>
            <div class="panel-body">
                <form class="formProforma form-horizontal form-bordered" method="post" action="<?= url('proformas', 'save_proforma') ?>"
                      onsubmit="return validateInputs();">
                    <input type="hidden" name="proforma[id]" value="<?= $COPY ? '' : $proforma['proformaid'] ?>">
                    <? if (!$proforma || $COPY) { ?>
                        <input type="hidden" name="proforma[token]" value="<?= unique_token() ?>">
                    <? } ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Client Name</h5>
                                <button type="button" title="Quick add client" data-toggle="modal" data-target="#quick-add-client-modal"
                                        class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></button>
                            </div>
                            <select onchange="getClientId(this)" required id="clientid" name="proforma[clientid]" class="form-control">
                                <? if ($proforma) { ?>
                                    <option selected value="<?= $proforma['clientid'] ?>"><?= $proforma['clientname'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-4">
                                <h5>TIN No.</h5>
                                <input id="tinnoid" placeholder="TIN number" type="text" class="form-control" value="<?= $proforma['tinno'] ?>"
                                       readonly>
                            </div>
                            <div class="col-md-4">
                                <h5>VAT/VRN No.</h5>
                                <input id="vatnoid" placeholder="VAT/VRN number" type="text" class="form-control" value="<?= $proforma['vatno'] ?>"
                                       readonly>
                            </div>
                            <div class="col-md-4">
                                <h5>Address</h5>
                                <input id="addressid" placeholder="Address" type="text" class="form-control" value="<?= $proforma['address'] ?>"
                                       readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 formessage">
                            <h5>Mobile</h5>
                            <input id="mobileid" placeholder="Mobile number" type="number" class="form-control" value="<?= $proforma['mobile'] ?>"
                                   readonly>
                        </div>
                        <div class="col-md-4">
                            <h5>Tel</h5>
                            <input id="telid" placeholder="Telephone number" type="text" class="form-control" value="<?= $proforma['tel'] ?>"
                                   readonly>
                        </div>
                        <div class="col-md-4">
                            <h5>Email</h5>
                            <input id="emailid" placeholder="Email" type="text" class="form-control" value="<?= $proforma['email'] ?>" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Location</h5>
                            <select id="locationid" name="proforma[locid]" class="form-control" onchange="getLocation()">
                                <option value="<?= $defaultLocation['id'] ?>"><?= $defaultLocation['name'] ?></option>
                            </select>
                        </div>
                        <div class="col-md-4" title="No of days this proforma will be valid">
                            <h5>Validity Days <small class="text-danger ml-md">default: <?= CS_PROFORMA_VALID_DAYS ?> days</small></h5>
                            <input id="valid-days" type="number" name="proforma[validity_days]" class="form-control"
                                   value="<?= $proforma['validity_days'] ?? CS_PROFORMA_VALID_DAYS ?>" required onchange="termsCondition()"
                                   onkeyup="termsCondition()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Currency</h5>
                            <select id="currencyid" required name="proforma[currencyid]" class="form-control" onchange="getExchangeRate(this)">
                                <? foreach ($currencies as $c) {
                                    if ($proforma) { ?>
                                        <option value="<?= $c['currencyid'] ?>" data-name="<?= $c['currencyname'] ?>"
                                                data-exchange-rate="<?= $c['rate_amount'] ?>" <?= selected($proforma['currencyid'], $c['currencyid']) ?>>
                                            <?= $c['currencyname'] ?> - <?= $c['description'] ?>
                                        </option>
                                    <? } else { ?>
                                        <option value="<?= $c['currencyid'] ?>" data-name="<?= $c['currencyname'] ?>"
                                                data-exchange-rate="<?= $c['rate_amount'] ?>" <?= selected($c['base'], 'yes') ?> >
                                            <?= $c['currencyname'] ?> - <?= $c['description'] ?>
                                        </option>
                                    <? }
                                } ?>
                            </select>
                            <input id="currency_amount" type="hidden" value="<?= $proforma[''] ?>">
                        </div>
                        <div class="col-md-4">
                            <h5>Payment Terms</h5>
                            <select id="paymentterms" name="proforma[paymentterms]" class="form-control" style="text-transform: capitalize"
                                    onchange="paymentTerms(this)">
                                <option <?= selected($proforma['paymentterms'], PAYMENT_TYPE_CASH) ?>
                                        value="<?= PAYMENT_TYPE_CASH ?>"><?= PAYMENT_TYPE_CASH ?></option>
                                <option <?= selected($proforma['paymentterms'], PAYMENT_TYPE_CREDIT) ?>
                                        value="<?= PAYMENT_TYPE_CREDIT ?>"><?= PAYMENT_TYPE_CREDIT ?></option>
                            </select>
                        </div>
                        <div id="payment-days" class="col-md-4" title="payment days for credit payment" style="display: none;">
                            <h5>Payment Days</h5>
                            <input type="number" name="proforma[payment_days]" class="form-control" value="<?= $proforma['payment_days'] ?>"
                                   placeholder="default 30 days">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Description</h5>
                            <textarea class="form-control text-sm" name="proforma[description]" rows="2"
                                      placeholder="proforma description"></textarea>
                        </div>
                    </div>
                    <div class="row mt-md d-flex justify-content-center">
                        <div class="col-md-4">
                            <h5 class="text-weight-bold">TOTAL</h5>
                            <input id="proformaValueLabel" readonly placeholder="Proforma value" type="text"
                                   class="form-control text-center text-primary input-lg">
                            <input id="proformaValue" type="hidden" name="proforma[proforma_value]">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Proforma Details</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 text-center"><h5>Product</h5></div>
                        <div class="col-md-1 text-center"><h5>Qty</h5></div>
                        <div class="col-md-2 text-center"><h5>Exc Price</h5></div>
                        <div class="col-md-1 text-center"><h5>Vat %</h5></div>
                        <div class="col-md-2 text-center"><h5>Inc Price</h5></div>
                        <div class="col-md-2 text-center"><h5>Total</h5></div>
                        <div class="col-md-1"></div>
                    </div>
                    <div id="items-holder">
                        <? if ($proforma) {
                            foreach ($proforma['details'] as $index => $detail) {
                                if ($detail['source'] == 'internal') { ?>
                                    <?= component('proforma/internal_product.tpl.php', compact('detail')) ?>
                                <? } else { ?>
                                    <?= component('proforma/external_product.tpl.php', compact('detail', 'categories')) ?>
                                <? }
                            }
                        } else { ?>
                            <div class="row mb-md" style="position: relative;">
                                <small class="text-danger non-stock-label" style="display: none">non-stock</small>
                                <div class="col-md-3 pl-none pr-none">
                                    <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                           onclick="open_modal(this,'.row')">
                                    <input type="hidden" class="inputs productid" name="productid[]" required>
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input placeholder="Quantity" type="text" class="form-control qty" name="qty[]" oninput="calProductAmount(this)"
                                           min="1" readonly data-source="qty">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative">
                                    <div class="price_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Exclusive Price" type="text" class="form-control price" name="price[]" readonly
                                           oninput="calProductAmount(this)" data-source="price">
                                    <input type="hidden" class="base_price">
                                    <input type="hidden" class="min_price">
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input type="text" readonly class="form-control vat_rate" name="vat_rate[]">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative;">
                                    <div class="incprice_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Inclusive Price" type="text" class="form-control incprice" name="incprice[]" readonly
                                           oninput="calProductAmount(this)" data-source="incprice">
                                    <input type="hidden" class="sinc" name="sinc[]">
                                </div>
                                <div class="col-md-2 pl-none pr-none">
                                    <input type="text" readonly class="form-control incamount">
                                </div>
                                <div class="col-md-1 pl-xs pr-none">
                                    <button type="button" class='btn btn-info btn-sm view_product_btn' title="view product" data-productid=""
                                            data-toggle="modal"
                                            data-target="#product-view-modal">
                                        <i class='fa fa-eye'></i></button>
                                    <button type="button" class='btn btn-danger btn-sm' title="remove item" onclick='removeRow(this);'><i
                                                class='fa fa-close'></i></button>
                                </div>
                                <div class="col-md-4 pl-none pr-none">
                                    <textarea rows="2" name="product_description[]" class="form-control inputs product_description" readonly
                                              placeholder="description"></textarea>
                                </div>
                                <div class="col-md-4 pl-none pr-none">
                                    <label class="d-flex align-items-center ml-md mr-sm">
                                        <input type="checkbox" name="print_extra[]" class="print_extra" disabled onchange="enableDescription(this)">
                                        <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
                                    </label>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row mt-lg">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addRow()"><i class="fa fa-plus"></i> Add Item</button>
                            <button type="button" class="btn btn-dark btn-sm" onclick="addExternal()"><i class="fa fa-plus-circle"></i> Add External
                                Product
                            </button>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-6">
                            <h5>Terms and Conditions</h5>
                            <textarea id="terms-conditions" name="proforma[terms_conditions]" rows="3"
                                      class="form-control text-sm text-weight-semibold"><?= $proforma['terms_conditions'] ?></textarea>
                        </div>
                    </div>
                    <div class="row mt-md d-flex justify-content-center">
                        <div class="col-md-4">
                            <button class="btn btn-success btn-block"> Save Proforma</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?= component('shared/quick_add_client_modal.tpl.php') ?>
<?= component('shared/product_view_modal.tpl.php') ?>
<?= component('normal-order/product_search_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script>
    let scroll_item_height = 7;
    //new FroalaEditor('#example')
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json&no_default", 'choose client');
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'choose location');

        let exchange_rate = parseFloat($('#currencyid').find(':selected').data('exchange-rate'));
        $('#currency_amount').val(exchange_rate);
        paymentTerms();

        <?if($proforma){?>

        $('#terms-conditions').val(`<?=$proforma['terms_conditions']?>`);
        $('#items-holder .row .qty').each(function (i, qty) {
            calProductAmount(qty, false);
        });
        <?}?>
    });

    function validateInputs() {
        let valid = true;
        if ($('#items-holder .row').length === 0) {
            triggerError('Choose at least one product!');
            addRow();
            valid = false;
            return false;
        }

        $('#items-holder .row').each(function (i, row) {
            let productid = $(row).find('.productid').val();
            let productname = $(row).find('.productname').val();
            if (!productid && !$(row).hasClass('external')) {
                triggerError('Choose product');
                $(row).find('.productname').focus();
                valid = false;
                return false;
            }
            if ($(row).hasClass('external') && productname.length === 0) {
                triggerError('Enter product name');
                $(row).find('.productname').focus();
                valid = false;
                return false;
            }
            let qty = parseInt($(row).find('.qty').val()) || 0;
            if (qty <= 0) {
                triggerError('Enter valid quantity');
                $(row).find('.qty').focus();
                valid = false;
                return false;
            }
            let price = $(row).find('.price').val();
            if (!price) {
                triggerError('Enter valid exclusive price');
                $(row).find('.price').focus();
                valid = false;
                return false;
            }

            let min_price = parseFloat($(row).find('.price').attr('min'));
            if (removeCommas(price) < min_price) {
                triggerError(`Exclusive price cant be below ${min_price}`);
                $(row).find('.price').focus();
                valid = false;
                return false;
            }
            let incprice = $(row).find('.incprice').val();
            if (!incprice) {
                triggerError('Enter valid inclusive price');
                $(row).find('.incprice').focus();
                valid = false;
                return false;
            }
            let min_incprice = parseFloat($(row).find('.incprice').attr('min'));
            if (removeCommas(incprice) < min_incprice) {
                triggerError(`Inclusive price cant be below ${min_incprice}`);
                $(row).find('.incprice').focus();
                valid = false;
                return false;
            }
        });

        if (!valid) return false;

        $('#spinnerHolder').show();
    }

    function format_inputs() {
        qtyInput('.qty');
        thousands_separator('.price,.incprice,.incamount');
    }

    function getLocation() {
        $('#items-holder').empty();
        addRow();
    }

    function paymentTerms() {
        if ($('#paymentterms').val() === '<?=PAYMENT_TYPE_CREDIT?>') {
            $('#payment-days').show('fast');
        } else {
            $('#payment-days').hide('fast');
            $('#payment-days input').val('');
        }
        termsCondition();
    }

    function termsCondition() {
        let valid_days = $('#valid-days').val();
        let payment_terms = $('#paymentterms').val().toUpperCase();
        let text = `1. Proforma is valid for ${valid_days} days.\n2. Terms of Payment - ${payment_terms}`;
        $('#terms-conditions').val(text);
    }

    function addRow() {
        let item = `<div class="row mb-md" style="position: relative;">
                                <small class="text-danger non-stock-label" style="display: none">non-stock</small>
                                <div class="col-md-3 pl-none pr-none">
                                    <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                           onclick="open_modal(this,'.row')">
                                    <input type="hidden" class="inputs productid" name="productid[]" required>
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input placeholder="Quantity" type="text" class="form-control qty" name="qty[]" oninput="calProductAmount(this)"
                                           min="1" readonly data-source="qty">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative">
                                    <div class="price_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Exclusive Price" type="text" class="form-control price" name="price[]" readonly
                                           oninput="calProductAmount(this)" data-source="price">
                                    <input type="hidden" class="base_price">
                                    <input type="hidden" class="min_price">
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input type="text" readonly class="form-control vat_rate" name="vat_rate[]">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative;">
                                    <div class="incprice_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Inclusive Price" type="text" class="form-control incprice" name="incprice[]" readonly
                                           oninput="calProductAmount(this)" data-source="incprice">
                                    <input type="hidden" class="sinc" name="sinc[]">
                                </div>
                                <div class="col-md-2 pl-none pr-none">
                                    <input type="text" readonly class="form-control incamount">
                                </div>
                                <div class="col-md-1 pl-xs pr-none">
                                    <button type="button" class='btn btn-info btn-sm view_product_btn' title="view product" data-productid=""
                                            data-toggle="modal"
                                            data-target="#product-view-modal">
                                        <i class='fa fa-eye'></i></button>
                                    <button type="button" class='btn btn-danger btn-sm' title="remove item" onclick='removeRow(this);'><i
                                                class='fa fa-close'></i></button>
                                </div>
                                <div class="col-md-4 pl-none pr-none">
                                    <textarea rows="2" name="product_description[]" class="form-control inputs product_description" readonly placeholder="description"></textarea>
                                </div>
                                <div class="col-md-4 pl-none pr-none">
                                    <label class="d-flex align-items-center ml-md mr-sm">
                                        <input type="checkbox" name="print_extra[]" class="print_extra" disabled onchange="enableDescription(this)">
                                        <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
                                    </label>
                                </div>
                            </div>`;

        $('#items-holder').append(item);
        if ($('#items-holder .row').length >= scroll_item_height) $('#items-holder').addClass('holder-scroll');
        $("#items-holder").animate({scrollTop: $('#items-holder')[0].scrollHeight}, 500);
        $("html,body").stop().animate({scrollTop: $(document).height()}, 500);
    }

    function addExternal() {
        let categories = `<? foreach ($categories as $c) { ?>
                              <option value="<?= $c['id'] ?>"
                                      data-vatrate="<?= $c['vat_percent'] ?>"><?= $c['name'] ?> <?= $c['vat_percent'] ?>%
                              </option>
                          <? } ?>`;
        let item = `<div class="row mb-md external">
                                <div class="col-md-3 pl-none pr-none" style="position: relative;">
                                    <span class="external-label">external</span>
                                    <input required name="external[productname][]" class="form-control productname" placeholder="product name" minlength="3"/>
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input placeholder="Quantity" type="text" class="form-control qty" name="external[qty][]"
                                           oninput="calProductAmount(this)" min="1" data-source="qty">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative">
                                    <div class="price_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Exclusive Price" type="text" class="form-control price" name="external[price][]"
                                           min="0" oninput="calProductAmount(this)" data-source="price">
                                    <input type="hidden" class="base_price">
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input type="hidden" class="vat_rate" name="external[vat_rate][]">
                                    <select class="form-control vat_category" name="external[tax_category][]" onchange="calProductAmount(this,false)" data-source="price">
                                        ${categories}
                                    </select>
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative">
                                    <div class="incprice_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Inclusive Price" type="text" class="form-control incprice" name="external[incprice][]"
                                           min="0" oninput="calProductAmount(this)" data-source="incprice">
                                    <input type="hidden" class="sinc" name="external[sinc][]">
                                </div>
                                <div class="col-md-2 pl-none pr-none">
                                    <input type="text" readonly class="form-control incamount">
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <button type="button" class='btn btn-danger btn-sm ml-xs' onclick='removeRow(this);'><i class='fa fa-close'></i></button>
                                </div>
                            </div>`;

        $('#items-holder').append(item);
        format_inputs();
        if ($('#items-holder .row').length >= scroll_item_height) $('#items-holder').addClass('holder-scroll');
        $("#items-holder").animate({scrollTop: $('#items-holder')[0].scrollHeight}, 500);
        $("html,body").stop().animate({scrollTop: $(document).height()}, 500);
    }

    function removeRow(obj) {
        $(obj).closest('.row').remove();
        if ($('#items-holder .row').length <= scroll_item_height) $('#items-holder').removeClass('holder-scroll');
        calProformaValue();
    }

    function getClientId(obj) {
        let select = $(obj);
        let client = {...$(obj).select2("data")[0]};

        $('#tinnoid').val('').val(client.tinno);
        $('#vatnoid').val('').val(client.vatno);
        $('#addressid').val('').val(client.address);
        $('#mobileid').val('').val(client.mobile);
        $('#telid').val('').val(client.tel);
        $('#emailid').val('').val(client.email);
    }

    function fetchDetails(obj) {
        let parent = $('.row.active-group');
        let productid = $(obj).data('productid');
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');
        let stockqty = $(obj).data('stockqty');

        if (!productid) {
            triggerError('Product info not found');
            return;
        }

        if ($(`.row input.productid[value='${productid}']`).length > 0) {
            triggerError('Product already selected');
            return;
        }
        $(parent).find('.inputs').val('');
        $(parent).find('.productid').val(productid);
        $(parent).find('.productname').val(productname);
        $(parent).find('.product_description').val(description);
        $(parent).find('.print_extra').val(productid).prop('disabled', false);

        $(parent).removeClass('active-group');

        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();

        let locationid = $('#locationid').val();
        let exchange_rate = parseFloat($('#currency_amount').val()) || 0;


        $.get(`?module=hierarchics&action=getProductPrice&format=json&productid=${productid}&locationid=${locationid}&for_proforma`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let product = result.data;
                // console.log(product);
                let price = (product.suggested / exchange_rate).toFixed(2);
                let min_price = (product.minimum / exchange_rate).toFixed(2);

                let vat_rate = parseFloat(product.vat_rate);
                let incprice = (product.suggested * (1 + vat_rate / 100) / exchange_rate).toFixed(2);
                let min_incprice = (product.minimum * (1 + vat_rate / 100) / exchange_rate).toFixed(2);
                $(parent).find('.qty').val(1);
                $(parent).find('.view_product_btn').attr('data-productid', productid);
                if (product.non_stock === '0') {
                    $(parent).removeClass('non-stock');
                    $(parent).find('.qty').prop('readonly', false).attr('title', `current stock ${stockqty}`);
                    $(parent).find('.price').val(price).attr('min', min_price).prop('readonly', false);
                    $(parent).find('.base_price').val(product.suggested);
                    $(parent).find('.min_price').val(product.minimum);
                    $(parent).find('.incprice').val(incprice).attr('min', min_incprice).prop('readonly', false);
                } else {
                    $(parent).addClass('non-stock');
                    $(parent).find('.qty').prop('readonly', false).attr('title', ``);
                    $(parent).find('.price,.incprice').val(0).attr('min', 0).prop('readonly', false);
                    $(parent).find('.base_price').val(0);
                    $(parent).find('.min_price').val(0);
                }
                $(parent).find('.vat_rate').val(product.vat_rate);
                calProductAmount($(parent).find('.qty'));
            } else {
                triggerError('Product info not found!', 5000);
            }
        });

    }

    function enableDescription(obj) {
        if ($(obj).is(':checked')) {
            $(obj).closest('.row').find('.product_description').prop('readonly', false);
        } else {
            $(obj).closest('.row').find('.product_description').prop('readonly', true);
        }
    }

    function getExchangeRate(obj) {
        let exchange_rate = parseFloat($(obj).find(':selected').data('exchange-rate'));
        $('#currency_amount').val(exchange_rate);
        recalculateProductAmounts();
    }

    function recalculateProductAmounts() {
        let exchange_rate = parseFloat($('#currency_amount').val()) || 0;
        $('#items-holder .row').each(function (i, row) {
            let base_price = parseFloat($(row).find('.base_price').val()) || 0;
            let min_price = parseFloat($(row).find('.min_price').val()) || 0;
            let vat_rate = parseFloat($(row).find('.vat_rate').val()) || 0;
            $(row).find('.price').val((base_price / exchange_rate).toFixed(2))
                .attr('min', (min_price / exchange_rate).toFixed(2));
            $(row).find('.incprice').val((base_price * (1 + vat_rate / 100) / exchange_rate).toFixed(2))
                .attr('min', (min_price * (1 + vat_rate / 100) / exchange_rate).toFixed(2));
            calProductAmount($(row).find('.qty'), false);
        });
    }

    let finalizeTimer = null;
    let finalizeTime = 1000;

    function calProductAmount(obj, timer = true) {
        let parent = $(obj).closest('.row');
        let qty = parseInt($(parent).find('.qty').val());
        let price = removeCommas($(parent).find('.price').val());
        let min_price = removeCommas($(parent).find('.price').attr('min'));
        let incprice = removeCommas($(parent).find('.incprice').val());
        let min_incprice = removeCommas($(parent).find('.incprice').attr('min'));
        let source = $(obj).data('source');
        let sinc = $(parent).find('.sinc').val() === '1';

        let vat_rate = 0;
        if ($(parent).hasClass('external')) {
            vat_rate = parseFloat($(parent).find('select.vat_category :selected').data('vatrate')) || 0;
            $(parent).find('.vat_rate').val(vat_rate)
        } else {
            vat_rate = parseFloat($(parent).find('.vat_rate').val());
        }

        let price_spinner = $(parent).find('.price_loading_spinner');
        let incprice_spinner = $(parent).find('.incprice_loading_spinner');


        if (source === 'qty') {
            finalizeCal();
        } else if (source === 'price') {
            price_spinner.css('visibility', 'visible');
            incprice = (price * (1 + vat_rate / 100)).toFixed(2);
            $(parent).find('.incprice').val(incprice);
            $(parent).find('.sinc').val('');
            sinc = false;
            let callback = function () {
                if (price < min_price) {
                    triggerError(`Price cant be below ${min_price}`);
                    $(parent).find('.price').val(min_price);
                    incprice = (min_price * (1 + vat_rate / 100)).toFixed(2);
                    $(parent).find('.incprice').val(incprice);
                    calProductAmount($(parent).find('.qty'));
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
        } else if (source === 'incprice') {
            incprice_spinner.css('visibility', 'visible');
            price = (incprice / (1 + vat_rate / 100)).toFixed(2);
            $(parent).find('.price').val(price);
            $(parent).find('.sinc').val(1);
            sinc = true;
            let callback = function () {
                if (incprice < min_incprice) {
                    triggerError(`Inclusive price cant be below ${min_incprice}`);
                    $(parent).find('.incprice').val(min_incprice);
                    price = (min_incprice / (1 + vat_rate / 100)).toFixed(2);
                    $(parent).find('.price').val(price);
                    calProductAmount($(parent).find('.qty'));
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
            price_spinner.css('visibility', 'hidden');
            incprice_spinner.css('visibility', 'hidden');

            //update base price
            let exchange_rate = parseFloat($('#currency_amount').val() || 0);
            let base_price = (price * exchange_rate).toFixed(2);
            $(parent).find('.base_price').val(base_price);

            let incamount = 0;
            if (sinc) {
                incamount = qty * incprice;
            } else {
                incamount = (price * qty * (1 + vat_rate / 100)).toFixed(2);
            }
            $(parent).find('.incamount').val(incamount);
            calProformaValue();
            format_inputs();
        }
    }

    function calProformaValue() {
        let total = 0;
        let currencyname = $('#currencyid').find(':selected').data('name');
        $('.incamount').each(function (i, item) {
            total += removeCommas($(item).val());
        });

        $('#proformaValueLabel').val(`${currencyname} ${numberWithCommas(total.toFixed(2))}`);
        $('#proformaValue').val(total.toFixed(2));
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
</script>