<style>
    .row-margin {
        margin-left: 5px;
        margin-right: 5px;
    }

    .group {
        /*border: 1px dashed #DADADA;*/
        /*margin-bottom: 2px;*/
        border-radius: 1px;
        padding-left: 15px;
        padding-right: 15px;
        position: relative;
        box-shadow: 0 0 10px #dadada;
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

    input.productname, input.end_productname {
        cursor: pointer !important;
        background: white !important;
    }
</style>
<header class="page-header">
    <h2><?= $requisition ? 'Edit' : 'Create' ?> Transfer Requisition</h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h1 class="text-weight-semibol text-danger">Please wait</h1>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    <? if ($requisition) { ?>
                        Requisition No: <span class="text-primary"><?= $requisition['reqid'] ?></span>
                    <? } else { ?>
                        Create Transfer Requisition
                    <? } ?>
                </h2>
            </header>
            <form action="<?= url('stocks', 'save_requisition') ?>" method="post"
                  onsubmit="return validateInputs()">
                <input type="hidden" name="requisition[id]" value="<?= $requisition['reqid'] ?>">
                <div class="panel-body" style="padding-bottom: 70px;">
                    <div class="row border-bottom">
                        <div class="col-md-12 mt-lg"><h4>Info</h4></div>
                        <div class="col-md-6">
                            <label>Request Products From:</label>
                            <select id="location_to" class="form-control locationid" name="requisition[location_to]"
                                    onchange="checkLocation(this)" required>
                                <? if ($requisition) { ?>
                                    <option selected
                                            value="<?= $requisition['location_to'] ?>"><?= $requisition['tolocation'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>To:</label>
                            <? if (IS_ADMIN) { ?>
                                <select id="location_from" class="form-control locationid"
                                        name="requisition[location_from]" required>
                                    <? if ($requisition) { ?>
                                        <option selected
                                                value="<?= $requisition['location_from'] ?>"><?= $requisition['fromlocation'] ?></option>
                                    <? } else { ?>
                                        <option selected
                                                value="<?= $defaultLocation['id'] ?>"><?= $defaultLocation['name'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input id="location_from" type="hidden" value="<?= $defaultLocation['id'] ?>"
                                       name="requisition[location_from]">
                                <input type="text" class="form-control" value="<?= $defaultLocation['name'] ?>"
                                       readonly>
                            <? } ?>
                        </div>
                        <div class="col-md-12 mt-lg">
                            <label>Description:</label>
                            <textarea name="requisition[remark]" class="form-control"
                                      rows="2"><?= $requisition['remark'] ?></textarea>
                        </div>
                    </div>
                    <div class="row mt-xlg">
                        <div class="col-md-12 mt-lg"><h4>Items</h4></div>
                    </div>
                    <div class="row row-margin">
                        <div class="col-md-12 text-weight-bold text-center" style="font-size: 11pt;">
                            <div class="col-md-4">Product</div>
                            <div class="col-md-4">Qty</div>
                            <div class="col-md-4"></div>
                        </div>
                    </div>
                    <div id="items-holder">
                        <? if ($requisition) {
                            foreach ($requisition['details'] as $detail) { ?>
                                <?=component('stock/requisition_detail_item.tpl.php',compact('detail'))?>
                            <? }
                        } else { ?>
                            <div class="row-margin group">
                                <div class="row">
                                    <div class="col-md-5 p-xs">
                                        <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                               onclick="open_modal(this,'.group',fetchDetails,$('#location_to'))">
                                        <input type="hidden" class="inputs productid" name="productid[]">
                                        <textarea rows="3" class="form-control inputs product_description" readonly placeholder="description"></textarea>
                                    </div>
                                    <div class="col-md-3 p-xs">
                                        <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                               autocomplete="off" placeholder="quantity" required>
                                    </div>
                                    <div class="col-md-4 p-xs d-flex justify-content-end">
                                        <button type="button" class="btn btn-info btn-sm view-product-btn" title="view product" data-toggle="modal"
                                                data-productid="" data-target="#product-view-modal">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm ml-sm" title="remove item" onclick="removeItem(this)">
                                            <i class="fa fa-close"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="mt-xlg border-bottom">
                        <button type="button" class="btn btn-primary" onclick="addItem()">Add Item</button>
                    </div>
                    <div class="mt-xlg d-flex justify-content-end">
                        <button class="btn btn-success btn-lg"><?= $requisition ? 'Update' : 'Save' ?></button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>

<?= component('shared/product_view_modal.tpl.php') ?>
<?= component('normal-order/product_search_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script>

    let NON_STOCK = '';
    $(function () {
        initSelectAjax('.locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
    });

    function validateInputs() {
        let valid = true;
        let groups = $('.group');
        if ($(groups).length === 0) {
            triggerError('Enter at least one product!');
            return false;
        }
        $(groups).each(function () {
            let qty = $(this).find('.qty').val();
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

    function checkLocation(obj) {
        let location_from = $('#location_from').val();
        let location_to = $('#location_to').val();
        if (!location_from || !location_to) return;
        if (location_to === location_from) {
            triggerError('From location and To locations cant be the same!', 5000);
            $(obj).val('').trigger('change');
        }
    }

    function addItem() {
        let item = `<div class="row-margin group">
                        <div class="row">
                            <div class="col-md-5 p-xs">
                                <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                       onclick="open_modal(this,'.group',fetchDetails,$('#location_to'))">
                                <input type="hidden" class="inputs productid" name="productid[]">
                                <textarea rows="3" class="form-control inputs product_description" readonly placeholder="description"></textarea>
                            </div>
                            <div class="col-md-3 p-xs">
                                <input type="number" class="form-control inputs qty" name="qty[]" min="1"
                                       autocomplete="off" placeholder="quantity" required>
                            </div>
                            <div class="col-md-4 p-xs d-flex justify-content-end">
                                <button type="button" class="btn btn-info btn-sm view-product-btn" title="view product" data-toggle="modal"
                                        data-productid="" data-target="#product-view-modal">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm ml-sm" title="remove item" onclick="removeItem(this)">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
        $('#items-holder').append(item);
        $("html, body").animate({scrollTop: $(document).height()}, 500);
    }

    function fetchDetails(obj) {
        let group = $('.group.active-group');
        let productid = $(obj).data('productid');
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');
        $(group).find('.view-product-btn').attr('data-productid', productid);
        if (productid == null || productid == '') {
            triggerError('Product info not found');
            return;
        }
        //check duplicate
        if ($(`.group input.productid[value='${productid}']`).length > 0) {
            triggerError(`Product already selected`, 2000);
            return;
        }

        $(group).find('.form-control.inputs').val('');

        $(group).find('.productid').val(productid);
        $(group).find('.productname').val(productname);
        $(group).find('.productname').val(productname);
        $(group).find('.product_description').val(description);
        $(group).find('.qty').val(1);

        $(group).removeClass('active-group');
        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();
    }

    function removeItem(obj) {
        $(obj).closest('.group').remove();
    }

</script>
