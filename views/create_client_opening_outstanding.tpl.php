<style>
    .group {
        border: 1px dashed #DADADA;
        /* margin-bottom: 2px; */
        border-radius: 1px;
        padding: 15px;
        position: relative;
        margin-bottom: 5px;
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

    @media (min-width: 768px) {
        .modal-lg {
            width: 90% !important;
        }

        .modal-md {
            width: 60% !important;
        }
    }
</style>
<header class="page-header">
    <h2>Create Client Opening Outstanding</h2>
</header>
<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>
<!-- center-panel -->
<div class="col-amd-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title"><i class="fa fa-file"></i> Create Client Opening Outstanding</h2>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <form action="<?= url('sales', 'save_client_opening_outstanding') ?>" method="post" onsubmit="return validateInputs()">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-3">
                        Total Outstanding <span class="text-primary">(<?= $basecurrency['name'] ?>)</span>
                        <input type="text" readonly class="form-control text-danger text-center total_outstanding" value="0.00">
                    </div>
                </div>
                <div class="row mt-md text-center text-weight-bold" style="font-size: 10pt;">
                    <div class="col-md-11 p-none">
                        <div class="col-md-2">Invoice No</div>
                        <div class="col-md-1">Client</div>
                        <div class="col-md-2">Location</div>
                        <div class="col-md-2">Currency</div>
                        <div class="col-md-1">Exchange Rate to <?= $basecurrency['name'] ?></div>
                        <div class="col-md-2">Outstanding Amount</div>
                        <div class="col-md-1">Invoice Date</div>
                        <div class="col-md-1">Credit Days</div>
                    </div>
                </div>
                <div id="items-holder" style="font-size: 10pt;">
                    <div class="group">
                        <button type="button" class="btn btn-warning btn-sm" title="remove" style="position: absolute;top:4px;right: 4px"
                                onclick="removeItem(this)">
                            <i class="fa fa-close"></i></button>
                        <div class="row">
                            <div class="col-md-11 p-none">
                                <div class="col-md-1 p-xs">
                                    <input type="text" class="form-control invoiceno" name="invoiceno[]" placeholder="invoice no"
                                           onchange="checkInvoice(this)" onkeyup="checkInvoice(this)" required>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select name="clientid[]" class="form-control clientid" required></select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select name="locationid[]" class="form-control locationid" required></select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select name="currencyid[]" class="form-control currencyid" onchange="getExchangeRate(this)" required>
                                        <? foreach ($currencies as $c) { ?>
                                            <option data-base="<?= $c['base'] ?>" value="<?= $c['id'] ?>"><?= $c['name'] ?>
                                                - <?= $c['description'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="number" step="0.01" min="0" class="form-control currency_amount" name="currency_amount[]"
                                           placeholder="exchange rate" value="1" required onchange="calTotal()" onkeyup="calTotal()">
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="number" step="0.01" min="0" class="form-control outstanding_amount" name="outstanding_amount[]"
                                           placeholder="outstanding amount" required onchange="calTotal()" onkeyup="calTotal()">
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="date" class="form-control" name="invoicedate[]" placeholder="invoice date" required>
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="number" min="1" class="form-control" name="credit_days[]" placeholder="credit days" required>
                                </div>
                            </div>
                            <div class="col-md-11 mt-sm p-none">
                                <div class="col-md-4 p-xs">
                                    <textarea name="description[]" class="form-control" rows="2" placeholder="description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-md">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addItem()"><i class="fa fa-plus"></i> Add</button>
                    </div>
                </div>
                <div class="row mt-md d-flex justify-content-center">
                    <div class="col-md-2">
                        <button class="btn btn-success btn-block">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>


<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">

    $(function () {
        initSelects();
    });

    function validateInputs() {
        if($('#items-holder .group').length===0){
            triggerError('Enter at least one invoice details!');
            return false;
        }
        $('#spinnerHolder').show();
    }

    function initSelects() {
        initSelectAjax('.locationid', '?module=locations&action=getLocations&format=json', 'Choose location');
        initSelectAjax('.clientid', '?module=clients&action=getClients&no_default&format=json', 'Choose client');
    }

    function addItem() {
        let currencies = `<? foreach ($currencies as $c) { ?>
                              <option data-base="<?=$c['base']?>" value="<?= $c['id'] ?>"><?= $c['name'] ?> - <?= $c['description'] ?></option>
                          <? } ?>`;

        let row = `<div class="group">
                        <button type="button" class="btn btn-warning btn-sm" title="remove" style="position: absolute;top:4px;right: 4px"
                                onclick="removeItem(this)">
                            <i class="fa fa-close"></i></button>
                        <div class="row">
                            <div class="col-md-11 p-none">
                                <div class="col-md-1 p-xs">
                                    <input type="text" class="form-control invoiceno" name="invoiceno[]" placeholder="invoice no"
                                           onchange="checkInvoice(this)" onkeyup="checkInvoice(this)" required>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select name="clientid[]" class="form-control clientid" required></select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select name="locationid[]" class="form-control locationid" required></select>
                                </div>
                                <div class="col-md-2 p-xs">
                                    <select name="currencyid[]" class="form-control currencyid" onchange="getExchangeRate(this)" required>
                                        ${currencies}
                                    </select>
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="number" step="0.01" min="0" class="form-control currency_amount" name="currency_amount[]"
                                           placeholder="exchange rate" value="1" required onchange="calTotal()" onkeyup="calTotal()">
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="number" step="0.01" min="0" class="form-control outstanding_amount" name="outstanding_amount[]"
                                           placeholder="outstanding amount" required onchange="calTotal()" onkeyup="calTotal()">
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="date" class="form-control" name="invoicedate[]" placeholder="invoice date" required>
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="number" min="1" class="form-control" name="credit_days[]" placeholder="credit days" required>
                                </div>
                            </div>
                            <div class="col-md-11 mt-sm p-none">
                                <div class="col-md-4 p-xs">
                                    <textarea name="description[]" class="form-control" rows="2" placeholder="description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>`;

        $('#items-holder').append(row);
        initSelects();
        getExchangeRate();
    }

    function removeItem(obj) {
        $(obj).closest('.group').remove();
        calTotal();
    }

    function calTotal() {
        let total = 0;
        $('.group').each(function (i, group) {
            let exchange_rate = parseFloat($(group).find('.currency_amount').val()) | 0;
            let outstanding_amount = parseFloat($(group).find('.outstanding_amount').val());

            total += exchange_rate * outstanding_amount;
        });

        $('.total_outstanding').val(numberWithCommas(total));
    }

    function getExchangeRate(obj) {
        if ($(obj).find('option:selected').data('base') === 'yes') {
            $(obj).closest('.group').find('.currency_amount').val(1);
        } else {
            $(obj).closest('.group').find('.currency_amount').val('').focus();
        }
        calTotal();
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>



