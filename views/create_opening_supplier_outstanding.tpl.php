<style>
    .row-margin {
        margin-left: 5px;
        margin-right: 5px;
    }

    .group {
        border: 1px dashed #DADADA;
        /*margin-bottom: 2px;*/
        border-radius: 1px;
        padding: 15px;
        position: relative;
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
</style>
<header class="page-header">
    <h2><?= $requisition ? 'Edit' : 'Create' ?> Opening Outstanding</h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h1 class="text-weight-semibol text-danger">Please wait</h1>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    <? if ($requisition) { ?>
                        Requisition No: <span class="text-primary"><?= $requisition['reqid'] ?></span>
                    <? } else { ?>
                        Create Opening Outstanding
                    <? } ?>
                </h2>
            </header>
            <form action="<?= url('suppliers', 'save_opening_outstanding') ?>" method="post"
                  onsubmit="return validateInputs()">
                <div class="panel-body" style="padding-bottom: 70px;">
                    <div class="row mt-md border-bottom">
                        <div class="col-md-3">
                            <label>Total Outstanding (<?= $basecurrency['name'] ?>)</label>
                            <input id="total_outstanding" type="text" readonly placeholder="Total Outstanding"
                                   class="form-control input-lg text-weight-bold text-center  text-danger">
                        </div>
                    </div>
                    <div class="row mt-xlg">
                        <div class="col-md-12 mt-lg"><h4>Outstandings</h4></div>
                    </div>
                    <div class="row row-margin">
                        <div class="col-md-12 text-center text-weight-bold" style="font-size: 11pt;">
                            <div class="col-md-2">Grn No</div>
                            <div class="col-md-2">Supplier</div>
                            <div class="col-md-2">Supplier Invoice No</div>
                            <div class="col-md-1">Location</div>
                            <div class="col-md-2">Currency</div>
                            <div class="col-md-2">Amount</div>
                        </div>
                    </div>
                    <div id="items-holder">
                        <div class="row-margin group">
                            <div class="row">
                                <button type="button" class="btn btn-warning btn-sm close-btn" title="remove item"
                                        onclick="removeItem(this)">
                                    <i class="fa fa-close"></i>
                                </button>
                                <div class="col-md-2 p-xs">
                                    <input type="hidden" name="openid[]">
                                    <input type="text" name="grnno[]" class="form-control grnno"
                                           placeholder="grn number" onkeyup="checkDuplicate(this)"
                                           onchange="checkDuplicate(this)" required>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select class="form-control supplierid" name="supplierid[]" required> </select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="text" name="invoiceno[]" class="form-control invoiceno"
                                           placeholder="supplier invoice no">
                                </div>
                                <div class="col-md-1 p-xs">
                                    <select class="form-control locationid" name="locationid[]" required>
                                        <option selected value="<?= $defaultLocation['id'] ?>">
                                            <?= $defaultLocation['name'] ?></option>
                                    </select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input class="currency_amount" type="hidden" name="currency_amount[]">
                                    <select class="form-control currencyid" name="currency_rateid[]"
                                            onchange="getExchangeRate()" required>
                                        <? foreach ($currencies as $c) { ?>
                                            <option <?= selected($c['base'], $basecurrency['base']) ?>
                                                    data-currencyid="<?= $c['rateid'] ?>"
                                                    data-currencyname="<?= $c['currencyname'] ?>"
                                                    data-exchange-rate="<?= $c['rate_amount'] ?>"
                                                    value="<?= $c['rateid'] ?>"><?= $c['currencyname'] ?>
                                                - <?= $c['description'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="number" name="amount[]" class="form-control text-center amount"
                                           min="0.01" step="0.01" placeholder="outstanding amount" onchange="calTotal()"
                                           onkeyup="calTotal()" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-xlg border-bottom">
                        <button type="button" class="btn btn-primary" onclick="addItem()">Add Item</button>
                    </div>
                    <div class="mt-xlg d-flex justify-content-end">
                        <button class="btn btn-success btn-lg"><?= $requisition ? 'Update' : 'Save' ?></button>
                    </div>
            </form>
        </section>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>


    $(function () {
        initSelectAjax('.supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);
        initSelectAjax('.locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);

        getExchangeRate();
    });

    function validateInputs() {
        let valid = true;
        let groups = $('.group');
        if ($(groups).length === 0) {
            triggerError('Enter at least one outstanding!');
            return false;
        }
        $(groups).each(function () {
            let amount = $(this).find('.amount').val();
            if (!amount) {
                $(this).find('.amount').focus();
                triggerError('Enter valid amount!');
                valid = false;
                return false;
            }
        });
        if (!valid) return false;


        $('#spinnerHolder').show();
    }

    function getExchangeRate() {
        $('.group').each(function (i, group) {
            let exchange_rate = $(group).find('select.currencyid :selected').data('exchange-rate');
            $(group).find('.currency_amount').val(exchange_rate);
        });
        calTotal();
    }

    function addItem() {
        let defaultLocation = `<option selected value="<?= $defaultLocation['id'] ?>">
                                            <?= $defaultLocation['name'] ?></option>`;
        let currencies = `<? foreach ($currencies as $c) { ?>
                                            <option <?= selected($c['base'], $basecurrency['base']) ?>
                                                    data-currencyid="<?= $c['rateid'] ?>"
                                                    data-currencyname="<?= $c['currencyname'] ?>"
                                                    data-exchange-rate="<?= $c['rate_amount'] ?>"
                                                    value="<?= $c['rateid'] ?>"><?= $c['currencyname'] ?>
                                                - <?= $c['description'] ?></option>
                                        <? } ?>`;
        let item = `<div class="row-margin group">
                            <div class="row">
                                <button type="button" class="btn btn-warning btn-sm close-btn" title="remove item"
                                        onclick="removeItem(this)">
                                    <i class="fa fa-close"></i>
                                </button>
                                <div class="col-md-2 p-xs">
                                    <input type="hidden" name="openid[]">
                                    <input type="text" name="grnno[]" class="form-control grnno"
                                           placeholder="grn number" onkeyup="checkDuplicate(this)"
                                           onchange="checkDuplicate(this)" required>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select class="form-control supplierid" name="supplierid[]" required> </select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="text" name="invoiceno[]" class="form-control invoiceno"
                                           placeholder="supplier invoice no">
                                </div>
                                <div class="col-md-1 p-xs">
                                    <select class="form-control locationid" name="locationid[]" required>
                                        ${defaultLocation}
                                    </select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input class="currency_amount" type="hidden" name="currency_amount[]">
                                    <select class="form-control currencyid" name="currency_rateid[]"
                                            onchange="getExchangeRate()" required>
                                        ${currencies}
                                    </select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="number" name="amount[]" class="form-control text-center amount"
                                           min="0.01" step="0.01" placeholder="outstanding amount" onchange="calTotal()"
                                           onkeyup="calTotal()" required>
                                </div>
                            </div>
                        </div>`;
        $('#items-holder').append(item);
        initSelectAjax('.supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);
        initSelectAjax('.locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        getExchangeRate();
        $("html, body").animate({scrollTop: $(document).height()}, 500);
    }

    let timer = null;

    function checkDuplicate(obj) {
        if (timer) clearTimeout(timer);
        timer = setTimeout(function () {
            let grnno = $(obj).val();
            $('.group .grnno').not(obj).each(function () {
                if ($(this).val() === grnno) {
                    triggerError(`GRN no ${$(this).val()} already entered!`, 3000);
                    $(this).closest('.group').find('.grnno').focus();
                    setTimeout(function () {
                        $(obj).closest('.group').fadeOut('fast', function () {
                            removeItem(obj);
                        });
                    }, 1000);
                    return false;
                }
            });
        }, 500);
    }

    function removeItem(obj) {
        $(obj).closest('.group').remove();
    }

    function calTotal() {
        let total = 0;
        $('.group').each(function (i, group) {
            let exchange_rate = parseFloat($(group).find('.currency_amount').val()) || 0;
            let amount = parseFloat($(group).find('.amount').val()) || 0;
            total += exchange_rate * amount;
            console.log(`group ${i}, exchange: ${exchange_rate}, amount: ${amount}`);
        });
        $('#total_outstanding').val(numberWithCommas(total.toFixed(2)));
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
