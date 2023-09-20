<style>
    .row-margin {
        margin-left: 5px;
        margin-right: 5px;
    }

    .group {
        border: 1px dashed grey;
        border-radius: 8px;
        padding: 10px 15px;
        position: relative;
        /*box-shadow: 0 0 10px #dadada;*/
        margin-bottom: 5px;
    }

    .group .close-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
    }

    .group .bulk-label {
        position: absolute;
        top: 0;
        left: 5px;
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

    .holder-scroll {
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: inset 0 0 5px grey;
        padding: 10px;
        border-radius: 5px;
        transition: 0.2s;
    }
</style>
<header class="page-header">
    <h2><?= $lpo ? 'Edit' : 'Create' ?> LPO</h2>
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
                    <? if ($lpo) { ?>
                        Edit LPO No: <span class="text-primary"><?= $lpo['id'] ?></span>
                    <? } else { ?>
                        Create LPO
                    <? } ?>
                </h2>
            </header>
            <form action="<?= url('grns', 'save_lpo_new') ?>" method="post" onsubmit="return validateInputs()">
                <input type="hidden" name="lpo[id]" value="<?= $lpo['id'] ?>">
                <div class="panel-body" style="padding-bottom: 70px;">
                    <div class="row">
                        <div class="col-md-12 mt-lg"><h4>Info</h4></div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label>Supplier</label>
                                <button type="button" class="btn btn-primary btn-sm" title="quick add supplier" data-toggle="modal"
                                        data-target="#quick-add-supplier-modal"><i class="fa fa-plus"></i></button>
                            </div>
                            <select id="supplierid" class="form-control" name="lpo[supplierid]" required>
                                <option value="<?= $lpo['supplierid'] ?>"><?= $lpo['suppliername'] ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Location</label>
                            <select id="locationid" class="form-control" name="lpo[locationid]" required>
                                <? foreach ($locations as $l) {?>
                                    <option <?=selected($defaultLocation['id'],$l['id'])?> value="<?=$l['id']?>"><?=$l['name']?> - <?=$l['branchname']?></option>
                                <?}?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Currency</label>
                            <select id="currencyid" class="form-control" name="lpo[currency_rateid]"
                                    onchange="getExchangeRate(this)" required>
                                <? foreach ($currencies as $c) { ?>
                                    <? if ($lpo) { ?>
                                        <option <?= selected($c['rateid'], $lpo['currency_rateid']) ?>
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
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex align-items-center">
                                <label>Base Exchange Rate</label>
                                <div class="rate_loading_spinner"
                                     style="display: none;">
                                    <object data="images/loading_spinner.svg"
                                            type="image/svg+xml" height="30"
                                            width="30"></object>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <input id="currency_amount" type="text" name="lpo[currency_amount]"
                                       class="form-control text-center" value="<?= $lpo['currency_amount'] ?>" readonly>
                                <button type="button" class="btn btn-info btn-sm ml-sm" title="use current rate"
                                        onclick="fetchCurrentExchangeRate()">
                                    <i class="fa fa-refresh"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row border-bottom mt-md">
                        <div class="col-md-3">
                            <label>Expecting Days</label>
                            <input class="form-control" name="lpo[expecting_days]" min="0" placeholder="stock expecting days">
                        </div>
                    </div>
                    <div id="paymentInfo" class="row mt-lg border-bottom">
                        <div class="col-md-12 mt-lg"><h4>Payments</h4></div>
                        <div class="col-md-4">
                            <label>Total Amount</label>
                            <input type="text" readonly class="form-control text-weight-bold total_exc_amount_label">
                            <input type="hidden" class="total_exc_amount" name="lpo[total_amount]">
                        </div>
                        <div class="col-md-4">
                            <label>Inclusive Amount</label>
                            <input type="text" readonly class="form-control text-weight-bold total_inc_amount_label">
                            <input type="hidden" class="total_inc_amount" name="lpo[full_amount]">
                        </div>
                        <div class="col-md-4">
                            <label>VAT Amount</label>
                            <input type="text" readonly class="form-control text-weight-bold total_vat_amount_label"
                                   name="lpo[grand_vatamount]">
                            <input type="hidden" class="total_vat_amount" name="lpo[grand_vatamount]">
                        </div>
                    </div>
                    <div class="row mt-xlg">
                        <div class="col-md-12 mt-lg"><h4>Items</h4></div>
                    </div>
                    <div class="row row-margin">
                        <div class="col-md-12 text-weight-bold text-center" style="font-size: 11pt;">
                            <div class="col-md-3">Product</div>
                            <div class="col-md-2">Rate</div>
                            <div class="col-md-1">Qty</div>
                            <div class="col-md-1">VAT %</div>
                            <div class="col-md-2" title="price inclusive VAT">VAT Amount</div>
                            <div class="col-md-2">Total</div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                    <div id="items-holder">
                        <? if ($lpo) {
                            foreach ($lpo['details'] as $index => $detail) { ?>
                                <div class="row-margin group">
                                    <div class="row">
                                        <button type="button" class="btn btn-warning btn-sm close-btn"
                                                title="remove item"
                                                onclick="removeItem(this)">
                                            <i class="fa fa-close"></i>
                                        </button>
                                        <div class="col-md-3 p-xs">
                                            <input type="text" readonly class="form-control productname" placeholder="search product"
                                                   value="<?= $detail['productname'] ?>" onclick="open_modal(this)">
                                            <input type="hidden" class="productid" name="productid[]" value="<?= $detail['prodid'] ?>">
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="number" class="form-control inputs rate" name="rate[]" min="0"
                                                   placeholder="rate" autocomplete="off"
                                                   onkeyup="calProductAmount(this)"
                                                   onchange="calProductAmount(this)" required step="0.01"
                                                   value="<?= $detail['rate'] ?>">
                                            <input type="hidden" class="form-control inputs base_rate"
                                                   value="<?= $detail['base_rate'] ?>">
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                                   autocomplete="off" placeholder="quantity"
                                                   value="<?= $detail['qty'] ?>"
                                                   onkeyup="calProductAmount(this)"
                                                   onchange="calProductAmount(this)" required>
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <input type="number" class="form-control inputs vat_percentage"
                                                   value="<?= $detail['vat_rate'] ?>"
                                                   name="vat_percentage[]" placeholder="vat percent" readonly required>
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="text" class="form-control text-right inputs product_vatamount"
                                                   readonly
                                                   placeholder="vat amount" autocomplete="off">
                                        </div>
                                        <div class="col-md-2 p-xs">
                                            <input type="text" class="form-control text-right inputs total_cost"
                                                   placeholder="total" readonly>
                                            <input type="hidden" class="inputs excamount">
                                            <input type="hidden" class="inputs vatamount">
                                            <input type="hidden" class="inputs incamount">
                                        </div>
                                        <div class="col-md-1 p-xs">
                                            <button type="button" class="btn btn-info btn-sm view-product-btn" title="view product"
                                                    data-productid="<?= $detail['prodid'] ?>" data-toggle="modal" data-target="#product-view-modal"><i
                                                        class="fa fa-eye"></i></button>
                                        </div>
                                        <div class="col-md-5 p-xs">
                                            <textarea rows="2" readonly class="form-control product_description"
                                                      placeholder="description"><?= $detail['description'] ?></textarea>
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
                                    <div class="col-md-3 p-xs">
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
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                               autocomplete="off" placeholder="quantity"
                                               onkeyup="calProductAmount(this)"
                                               onchange="calProductAmount(this)" required>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs vat_percentage"
                                               name="vat_percentage[]" placeholder="vat percent" readonly required>
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" class="form-control text-right inputs product_vatamount"
                                               readonly
                                               placeholder="vat amount" autocomplete="off">
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" class="form-control text-right inputs total_cost"
                                               placeholder="total" readonly>
                                        <input type="hidden" class="inputs excamount">
                                        <input type="hidden" class="inputs vatamount">
                                        <input type="hidden" class="inputs incamount">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <button type="button" class="btn btn-info btn-sm view-product-btn" title="view product" data-productid=""
                                                data-toggle="modal" data-target="#product-view-modal"><i class="fa fa-eye"></i></button>
                                    </div>
                                    <div class="col-md-5 p-xs">
                                        <textarea rows="2" readonly class="form-control product_description" placeholder="description"></textarea>
                                    </div>
                                </div>
                            </div>
                        <? } ?>

                    </div>
                    <div class="mt-xlg border-bottom">
                        <button type="button" class="btn btn-primary" onclick="addItem()">Add Item</button>
                    </div>
                    <div class="mt-xlg d-flex justify-content-end">
                        <button class="btn btn-success btn-lg"><?= $lpo ? 'Update' : 'Save' ?></button>
                    </div>
                </div>
            </form>
            <div id="bottom-amounts" class="sticky-bottom" style="display: none">
                <div class="row">
                    <div class="col-md-12 mt-lg"><h4>Payments</h4></div>
                    <div class="col-md-4">
                        <label>Total Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_exc_amount_label">
                    </div>
                    <div class="col-md-4">
                        <label>Inclusive Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_inc_amount_label">
                    </div>
                    <div class="col-md-4">
                        <label>VAT Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_vat_amount_label">
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?= component('shared/quick_add_supplier_modal.tpl.php') ?>
<?= component('shared/product_view_modal.tpl.php') ?>
<?= component('grn/product_search_modal.tpl.php') ?>
<script src="assets/js/quick_adds.js"></script>
<script>

    $(function () {
        initSelectAjax('#supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);

        $('#locationid').select2({width:'100%'});

        $('#currencyid').trigger('change');

        <?if($lpo){?>
        if ($('#items-holder .group').length >= 7) $('#items-holder').addClass('holder-scroll');
        $('.productid').each(function (i, product) {
            calProductAmount(product);
        });
        <?}?>

        //floating total amounts
        // let trackingElement = $('#paymentInfo');
        // let elementTopOffset = $(trackingElement).offset().top;
        // let bottomAmounts = $('#bottom-amounts');
        //
        // $(window).scroll(function () {
        //     let windowTop = $(window).scrollTop();
        //     // console.log(windowTop, elementTopOffset);
        //     if (windowTop > (elementTopOffset + $(trackingElement).outerHeight())) {
        //         $(bottomAmounts).show();
        //     } else {
        //         $(bottomAmounts).hide();
        //     }
        //
        // });
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
        });
        if (!valid) return false;


        $('#spinnerHolder').show();
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

    function addItem() {
        let item = `<div class="row-margin group">
                                <div class="row">
                                    <button type="button" class="btn btn-warning btn-sm close-btn" title="remove item"
                                            onclick="removeItem(this)">
                                        <i class="fa fa-close"></i>
                                    </button>
                                    <div class="col-md-3 p-xs">
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
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                               autocomplete="off" placeholder="quantity"
                                               onkeyup="calProductAmount(this)"
                                               onchange="calProductAmount(this)" required>
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <input type="number" class="form-control inputs vat_percentage"
                                               name="vat_percentage[]" placeholder="vat percent" readonly required>
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" class="form-control text-right inputs product_vatamount"
                                               readonly
                                               placeholder="vat amount" autocomplete="off">
                                    </div>
                                    <div class="col-md-2 p-xs">
                                        <input type="text" class="form-control text-right inputs total_cost"
                                               placeholder="total" readonly>
                                        <input type="hidden" class="inputs excamount">
                                        <input type="hidden" class="inputs vatamount">
                                        <input type="hidden" class="inputs incamount">
                                    </div>
                                    <div class="col-md-1 p-xs">
                                        <button type="button" class="btn btn-info btn-sm view-product-btn" title="view product" data-productid=""
                                                data-toggle="modal" data-target="#product-view-modal"><i class="fa fa-eye"></i></button>
                                    </div>
                                    <div class="col-md-5 p-xs">
                                        <textarea rows="2" readonly class="form-control product_description" placeholder="description"></textarea>
                                    </div>
                                </div>
                            </div>`;
        $('#items-holder').append(item);
        if ($('#items-holder .group').length >= 7) $('#items-holder').addClass('holder-scroll');

        $('#items-holder').animate({
            scrollTop: $('#items-holder').stop().prop("scrollHeight")
        }, 500);
    }

    function removeItem(obj) {
        $(obj).closest('.group').remove();
        if ($('#items-holder .group').length < 7) $('#items-holder').removeClass('holder-scroll');
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

        $(group).find('.productid').val(productid);
        $(group).find('.productname').val(productname);
        $(group).find('.product_description').val(description);
        $(group).find('.view-product-btn').attr('data-productid', productid);

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
                $(group).find('.vat_percentage').val(product.category.vat_percent);
                $(group).find('.qty').val(1);
                calProductAmount($(group).find('.qty'));
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
            // console.log(result);
            if (result.status === 'success') {
                let rate = result.data;
                $('#currency_amount').val(rate.rate_amount);
                $('#currencyid').find(':selected').attr('data-exchange-rate', rate.rate_amount);
                triggerMessage('Rate updated', 4000);
            } else {
                triggerError(result.msg || 'Error found!', 5000);
            }
        });
    }

    function calProductAmount(obj) {
        let group = $(obj).closest('.group');
        let rate = parseFloat($(group).find('.rate').val());
        let qty = parseFloat($(group).find('.qty').val());
        let vat_percentage = parseFloat($(group).find('.vat_percentage').val());

        //update base rate
        let exchange_rate = parseFloat($('#currency_amount').val() || 0);
        let base_rate = (rate * exchange_rate).toFixed(2);
        $(group).find('.base_rate').val(base_rate);


        let excamount = qty * rate;
        let vatamount = qty * rate * (vat_percentage / 100);
        let incamount = excamount + vatamount;
        $(group).find('.excamount').val(excamount.toFixed(2));
        $(group).find('.vatamount').val(vatamount.toFixed(2));
        $(group).find('.incamount').val(incamount.toFixed(2));
        $(group).find('.product_vatamount').val(numberWithCommas(vatamount.toFixed(2)));
        $(group).find('.total_cost').val(numberWithCommas(incamount.toFixed(2)));
        calGrnTotalAmount();
    }


    function calGrnTotalAmount() {
        let totalAmount = 0, totalVatAmount = 0, fullAmount = 0;
        $('.group').each(function (i, group) {
            let excamount = parseFloat($(group).find('.excamount').val());
            let vatamount = parseFloat($(group).find('.vatamount').val());
            let incamount = parseFloat($(group).find('.incamount').val());
            totalAmount += excamount;
            totalVatAmount += vatamount;
            fullAmount += incamount;
        });

        $('.total_exc_amount').val(numberWithCommas(totalAmount.toFixed(2)));
        $('.total_inc_amount').val(numberWithCommas(fullAmount.toFixed(2)));
        $('.total_vat_amount').val(numberWithCommas(totalVatAmount.toFixed(2)));

        let currencyname = $('#currencyid').find(':selected').data('currencyname');
        $('.total_exc_amount_label').val(`${currencyname} ${numberWithCommas(totalAmount.toFixed(2))}`);
        $('.total_inc_amount_label').val(`${currencyname} ${numberWithCommas(fullAmount.toFixed(2))}`);
        $('.total_vat_amount_label').val(`${currencyname} ${numberWithCommas(totalVatAmount.toFixed(2))}`);
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
