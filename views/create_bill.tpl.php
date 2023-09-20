<style media="screen">

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


    .external-product {
        border-radius: 5px;
        background-color: #ffc0a9;
        padding: 5px;
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
<link rel="stylesheet" href="assets/css/floating-msg.css">
<header class="page-header">
    <h2>
        <? if ($bill) { ?>
            Edit Bill No: <span class="text-primary"><?= $bill['billid'] ?></span>
        <? } else { ?>
            Create Bill
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
<div class="floating-msg hidden">
    <div class="msg-holder">
        <ul>
            <li data-stockid="" onclick="findItem(this)">msg 1</li>
        </ul>
    </div>
    <button class="btn  animated bounce"><i class="fa fa-warning"></i></button>
</div>
<?= component('shared/quick_add_client_modal.tpl.php') ?>
<div class="row d-flex justify-content-center">
    <div class="col-md-11">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    <? if ($bill) { ?>
                        Edit Bill No: <span class="text-primary"><?= $bill['billid'] ?></span>
                    <? } else { ?>
                        Create Bill
                    <? } ?>
                </h2>
            </header>
            <div class="panel-body">
                <form class="formOrder form-horizontal form-bordered" method="post" action="<?= url('recurring_bills', 'save_bill') ?>"
                      onsubmit="return validateInputs()">
                    <input type="hidden" name="bill[id]" value="<?= $bill['billid'] ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Client Name</h5>
                                <button type="button" title="Quick add client" data-target="#quick-add-client-modal" data-toggle="modal"
                                        class="btn btn-primary btn-sm"><i class="fa fa-plus"></i></button>
                            </div>
                            <? if ($bill['has_sales']) { ?>
                                <input type="text" readonly class="form-control" value="<?=$bill['clientname']?>">
                            <? } else { ?>
                                <select onchange="getClientId(this)" required id="clientid" name="bill[clientid]" class="form-control">
                                    <? if ($bill) { ?>
                                        <option selected value="<?= $bill['clientid'] ?>"><?= $bill['clientname'] ?></option>
                                    <? } ?>
                                </select>
                            <? } ?>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-4">
                                <h5>TIN No.</h5>
                                <input id="tinnoid" placeholder="TIN number" type="text" class="form-control"
                                       value="<?= $bill['clienttinno'] ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <h5>VAT/VRN No.</h5>
                                <input id="vatnoid" placeholder="VAT/VRN number" type="text" class="form-control"
                                       value="<?= $bill['clientvrn'] ?>" readonly>
                            </div>
                            <div class="col-md-4">
                                <h5>Address</h5>
                                <input id="addressid" placeholder="Address" type="text" class="form-control"
                                       value="<?= $bill['clientaddress'] ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 formessage">
                            <h5>Mobile</h5>
                            <input id="mobileid" placeholder="Mobile number"
                                   type="number" class="form-control" value="<?= $bill['clientmobile'] ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <h5>Tel</h5>
                            <input id="telid" placeholder="Telephone number" type="text" class="form-control"
                                   value="<?= $bill['clienttel'] ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <h5>Email</h5>
                            <input id="emailid" placeholder="Email" type="text" class="form-control"
                                   value="<?= $bill['clientemail'] ?>" readonly>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-3">
                            <h5>Location</h5>
                            <select id="locationid" name="bill[locationid]" class="form-control" onchange="getLocation()">
                                <option value="<?= $defaultLocation['id'] ?>"><?= $defaultLocation['name'] ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <h5>Currency</h5>
                            <select id="currencyid" required name="bill[currencyid]" class="form-control" onchange="getExchangeRate(this)">
                                <? foreach ($currencies as $c) {
                                    if ($bill) { ?>
                                        <option value="<?= $c['currencyid'] ?>" data-name="<?= $c['currencyname'] ?>"
                                                data-exchange-rate="<?= $c['rate_amount'] ?>" <?= selected($bill['currencyid'], $c['currencyid']) ?>>
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
                            <input id="currency_amount" type="hidden">
                        </div>
                        <div class="col-md-3">
                            <h5>Billing Interval</h5>
                            <? if ($bill['has_sales']) { ?>
                                <input type="text" readonly class="form-control" value="<?= $bill['billtypename'] ?>"
                                       title="<?= $bill['bill_interval'] . ' ' . $bill['billtype'] . ' interval' ?>">
                            <? } else { ?>
                                <select id="billtypeid" name="bill[billtypeid]" class="form-control inputs" required>
                                    <option value="" selected disabled>-- bill interval --</option>
                                    <? foreach ($bill_types as $b) { ?>
                                        <option <?= selected($bill['billtypeid'], $b['id']) ?> value="<?= $b['id'] ?>"
                                                                                               title="<?= $b['bill_interval'] . ' ' . $b['type'] . ' interval' ?>">
                                            <?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                            <? } ?>
                        </div>
                        <div class="col-md-3">
                            <h5>Billing Start month</h5>
                            <input id="startdate" type="text" readonly name="startdate" class="form-control"
                                   value="<?= $bill['startdate'] ? fDate($bill['startdate'], 'F Y') : '' ?>" required style="cursor: pointer">
                            <? if (!$bill['has_sales']) { ?>
                                <input id="startdatevalue" type="hidden" name="bill[startdate]"
                                       value="<?= $bill['startdate'] ? fDate($bill['startdate'], 'Y-m-d') : '' ?>" required>
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-3">
                            <h5>Billing Item Type</h5>
                            <select id="bill_non_stock" name="bill[non_stock]" class="form-control inputs" required onchange="getLocation()">
                                <option <?= selected($bill['non_stock'], '1') ?> value="1">Services</option>
                                <option <?= selected($bill['non_stock'], '0') ?> value="0">Stock Items</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-3">
                            <h5 class="text-center text-weight-semibold">Total Exclusive Amount</h5>
                            <input id="bill-excamount-label" type="text" readonly placeholder="Total Exclusive amount"
                                   class="form-control text-center text-weight-semibold">
                            <input id="bill-excamount" type="hidden" name="bill[total_excamount]">
                        </div>
                        <div class="col-md-3">
                            <h5 class="text-center text-weight-semibold">Total Vat Amount</h5>
                            <input id="bill-vatamount-label" type="text" readonly placeholder="Total Vat amount"
                                   class="form-control text-center text-weight-semibold">
                            <input id="bill-vatamount" type="hidden" name="bill[total_vatamount]">
                        </div>
                        <div class="col-md-3">
                            <h5 class="text-center text-weight-semibold">Total Amount</h5>
                            <input id="bill-incamount-label" type="text" readonly placeholder="Total Bill amount"
                                   class="form-control text-center text-weight-semibold">
                            <input id="bill-incamount" type="hidden" name="bill[total_amount]">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Bill Items</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 pl-none pr-none text-center"><h5 class="text-weight-bold">Product</h5></div>
                        <div class="col-md-1 pl-none pr-none text-center"><h5 class="text-weight-bold">Qty</h5></div>
                        <div class="col-md-2 pl-none pr-none text-center"><h5 class="text-weight-bold">Exc Price</h5></div>
                        <div class="col-md-1 pl-none pr-none text-center"><h5 class="text-weight-bold">Vat %</h5></div>
                        <div class="col-md-2 pl-none pr-none text-center"><h5 class="text-weight-bold">Inc Price</h5></div>
                        <div class="col-md-2 pl-none pr-none text-center"><h5 class="text-weight-bold">Total</h5></div>
                        <div class="col-md-1 pl-none pr-none"></div>
                    </div>
                    <div id="items-holder">
                        <? if ($bill) {
                            foreach ($bill['details'] as $index => $detail) { ?>
                                <?= component('normal-order/order_item.tpl.php', compact('detail')) ?>
                            <? }
                        } else { ?>
                            <div class="row mb-md" style="position: relative;">
                                <small class="text-danger non-stock-label" style="display: none">non-stock</small>
                                <div class="col-md-3 pl-none pr-none">
                                    <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                           onclick="open_modal(this,'.row')">
                                    <input type="hidden" class="inputs productid" name="productid[]" required>
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input placeholder="Quantity" type="text" class="form-control inputs qty" name="qty[]"
                                           oninput="calProductAmount(this)" min="1" readonly data-source="qty">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative;">
                                    <div class="price_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Exclusive Price" type="text" class="form-control inputs price" name="price[]" readonly
                                           oninput="calProductAmount(this)" data-source="price">
                                    <input type="hidden" class="base_price">
                                    <input type="hidden" class="min_price">
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input type="text" readonly class="form-control inputs vat_rate" name="vat_rate[]" placeholder="vat">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative;">
                                    <div class="incprice_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Inclusive Price" type="text" class="form-control inputs incprice" name="incprice[]" readonly
                                           oninput="calProductAmount(this)" data-source="incprice">
                                    <input type="hidden" class="inputs sinc" name="sinc[]">
                                </div>
                                <div class="col-md-2 pl-none pr-none">
                                    <input type="text" readonly class="form-control inputs incamount" placeholder="total amount">
                                    <input type="hidden" class="excamount">
                                    <input type="hidden" class="vatamount">
                                </div>
                                <div class="col-md-1 pl-xs pr-none">
                                    <button type="button" class='btn btn-info btn-sm view_product_btn' title="view product" data-productid=""
                                            data-toggle="modal" data-target="#product-view-modal"><i class='fa fa-eye'></i></button>
                                    <button type="button" class='btn btn-danger btn-sm' title="remove item" onclick='removeRow(this);'><i
                                                class='fa fa-close'></i></button>
                                </div>
                                <div class="col-md-4 pl-none pr-none">
                                    <textarea rows="2" name="product_description[]" class="form-control inputs product_description" readonly
                                              placeholder="description"></textarea>
                                </div>
                                <div class="col-md-2 pl-none pr-none">
                                    <label class="d-flex align-items-center ml-md mr-sm">
                                        <input type="checkbox" name="print_extra[]" class="print_extra" disabled onchange="enableDescription(this)">
                                        <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
                                    </label>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addRow()"><i class="fa fa-plus"></i> Add Item</button>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-6">
                            remarks:
                            <textarea name="bill[remarks]" class="form-control" rows="3" placeholder="bill remarks"><?= $bill['remarks'] ?></textarea>
                        </div>
                        <div class="col-md-6">
                            Internal remarks:
                            <textarea name="bill[internal_remarks]" class="form-control" rows="3"
                                      placeholder="Internal remarks"><?= $bill['internal_remarks'] ?></textarea>
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

<?= component('shared/product_view_modal.tpl.php') ?>
<?= component('normal-order/product_search_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script>
    let item_scroll_height = 6;
    //new FroalaEditor('#example')
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'choose client');
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'choose location');
        $('#billtypeid').select2({width: '100%'});

        <?if(!$bill['has_sales']){?>
        $("#startdate").datepicker({
            orientation: "top",
            format: "MM yyyy",
            startView: "months",
            minViewMode: "months",
            startDate: new Date("<?=TODAY?>"),
            autoclose: true,
        }).on('changeDate', function (e) {
            let date = e.date;
            let date_value = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${(date.getDate()).toString().padStart(2, '0')}`;
            $('#startdatevalue').val('').val(date_value);
        });
        <?}?>

        // getExchangeRate('#currencyid');
        let exchange_rate = parseFloat($('#currencyid').find(':selected').data('exchange-rate'));
        $('#currency_amount').val(exchange_rate);

        <?if($bill['details']){?>
        if ($('#items-holder .row').length >= item_scroll_height) $('#items-holder').addClass('holder-scroll');
        $('#items-holder .row .qty').each(function (i, qty) {
            calProductAmount(qty, false);
        });
        <?}?>
    });

    function format_inputs() {
        qtyInput('.qty');
        thousands_separator('.price,.incprice,.incamount');
    }


    function validateInputs() {
        let valid = true;

        <?if(!$bill){?>

        let startdatevalue = $('#startdatevalue').val();
        if (!startdatevalue.length) {
            triggerError("Choose billing start month");
            return false;
        }

        <?}?>

        if ($('#items-holder .row').length === 0) {
            triggerError('Choose at least one product!');
            addRow();
            valid = false;
            return false;
        }

        $('#items-holder .row').each(function (i, row) {
            let productid = $(row).find('.productid').val();
            if (!productid) {
                triggerError('Choose product');
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

        if ($('.external-product').length > 0) {
            triggerError('System found external product, You can add product or remove it from list', 5000);
            return false;
        }

        $('#spinnerHolder').show();
    }


    function getLocation() {
        $('#items-holder').empty();
        addRow();
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
                                    <input placeholder="Quantity" type="text" class="form-control inputs qty" name="qty[]"
                                           oninput="calProductAmount(this)" min="1" readonly data-source="qty">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative;">
                                    <div class="price_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Exclusive Price" type="text" class="form-control inputs price" name="price[]" readonly
                                           oninput="calProductAmount(this)" data-source="price">
                                    <input type="hidden" class="base_price">
                                    <input type="hidden" class="min_price">
                                </div>
                                <div class="col-md-1 pl-none pr-none">
                                    <input type="text" readonly class="form-control inputs vat_rate" name="vat_rate[]" placeholder="vat">
                                </div>
                                <div class="col-md-2 pl-none pr-none" style="position: relative;">
                                    <div class="incprice_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                                    </div>
                                    <input placeholder="Inclusive Price" type="text" class="form-control inputs incprice" name="incprice[]" readonly
                                           oninput="calProductAmount(this)" data-source="incprice">
                                    <input type="hidden" class="inputs sinc" name="sinc[]">
                                </div>
                                <div class="col-md-2 pl-none pr-none">
                                    <input type="text" readonly class="form-control inputs incamount" placeholder="total amount">
                                    <input type="hidden" class="excamount">
                                    <input type="hidden" class="vatamount">
                                </div>
                                <div class="col-md-1 pl-xs pr-none">
                                    <button type="button" class='btn btn-info btn-sm view_product_btn' title="view product" data-productid=""
                                            data-toggle="modal" data-target="#product-view-modal"><i class='fa fa-eye'></i></button>
                                    <button type="button" class='btn btn-danger btn-sm' title="remove item" onclick='removeRow(this);'><i
                                                class='fa fa-close'></i></button>
                                </div>
                                <div class="col-md-4 pl-none pr-none">
                                    <textarea rows="2" name="product_description[]" class="form-control inputs product_description" readonly
                                              placeholder="description"></textarea>
                                </div>
                                <div class="col-md-2 pl-none pr-none">
                                    <label class="d-flex align-items-center ml-md mr-sm">
                                        <input type="checkbox" name="print_extra[]" class="print_extra" disabled onchange="enableDescription(this)">
                                        <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
                                    </label>
                                </div>
                            </div>`;

        $('#items-holder').append(item);
        if ($('#items-holder .row').length >= item_scroll_height) $('#items-holder').addClass('holder-scroll');
        $("#items-holder").animate({scrollTop: $('#items-holder')[0].scrollHeight}, 500);
        $("html,body").animate({scrollTop: $(document).height()}, 500);
    }

    function removeRow(obj) {
        $(obj).closest('.row').remove();
        if ($('#items-holder .row').length <= item_scroll_height) $('#items-holder').removeClass('holder-scroll');
        calTotalAmount();
    }

    $('#isordernew').change(function () {
        if ($(this).val() == 'new') {
            $('#odernumber').hide();
        } else if ($(this).val() == 'old') {
            $('#odernumber').show();
        }
    });

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
        let nonstock = $(obj).data('nonstock') == '1';
        let billing_non_stock = $('#bill_non_stock').val() == '1';

        if (billing_non_stock && !nonstock) {
            triggerError(`This bill is for non stock items only!\nTry to change bill items type`, 5000);
            return;
        }

        if (!billing_non_stock && nonstock) {
            triggerError('This bill is for stock items only!\nTry to change bill items type', 5000);
            return;
        }

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

        $.get(`?module=hierarchics&action=getProductPrice&format=json&productid=${productid}&locationid=${locationid}`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let product = result.data;
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
            let vat_rate = parseFloat($(row).find('.vat_rate').val());
            $(row).find('.price').val((base_price / exchange_rate).toFixed(2))
                .attr('min', (min_price / exchange_rate).toFixed(2));
            $(row).find('.incprice').val((base_price * (1 + vat_rate / 100) / exchange_rate).toFixed(2))
                .attr('min', (min_price * (1 + vat_rate / 100) / exchange_rate).toFixed(2));
            calProductAmount($(row).find('.price'), false);
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
        let vat_rate = parseFloat($(parent).find('.vat_rate').val());
        let source = $(obj).data('source');
        let sinc = $(parent).find('.sinc').val() === '1';
        let non_stock = $(parent).hasClass('non-stock');
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

            let excamount = 0, vatamount = 0, incamount = 0;
            if (sinc) {
                incamount = qty * incprice;
                excamount = parseFloat((incamount / (1 + vat_rate / 100)).toFixed(2));
                vatamount = (incamount - excamount).toFixed(2);
            } else {
                incamount = (price * qty * (1 + vat_rate / 100)).toFixed(2);
                excamount = (qty * price).toFixed(2);
                vatamount = (qty * price * (vat_rate / 100)).toFixed(2);
            }
            // console.log('sinc: ', sinc, 'inc: ', incamount, 'exc: ', excamount, 'vat: ', vatamount);

            $(parent).find('.incamount').val(incamount);
            $(parent).find('.excamount').val(excamount);
            $(parent).find('.vatamount').val(vatamount);
            calTotalAmount();
            format_inputs();
        }
    }

    function calTotalAmount() {
        let totalVat = 0, totalExc = 0, totalInc = 0;
        let currencyname = $('#currencyid').find(':selected').data('name');
        $('#items-holder .row').each(function (i, row) {
            let excamount = removeCommas($(row).find('.excamount').val()) || 0;
            let vatamount = removeCommas($(row).find('.vatamount').val()) || 0;
            let incamount = removeCommas($(row).find('.incamount').val()) || 0;

            totalExc += excamount;
            totalVat += vatamount;
            totalInc += incamount;
        });

        // console.log('exc:', totalExc, 'vat: ', totalVat, 'inc: ', totalInc);

        $('#bill-excamount-label').val(`${currencyname} ${numberWithCommas(totalExc.toFixed(2))}`);
        $('#bill-vatamount-label').val(`${currencyname} ${numberWithCommas(totalVat.toFixed(2))}`);
        $('#bill-incamount-label').val(`${currencyname} ${numberWithCommas(totalInc.toFixed(2))}`);

        $('#bill-excamount').val(totalExc.toFixed(2));
        $('#bill-vatamount').val(totalVat.toFixed(2));
        $('#bill-incamount').val(totalInc.toFixed(2));
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