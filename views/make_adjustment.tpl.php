<style>
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

    .group {
        border: 1px dashed grey;
        border-radius: 15px;
        margin-bottom: 15px;
        padding-top: 40px;
        padding-bottom: 5px;
        position: relative;
    }

    .remarks {
        font-size: 9pt;
    }

    .removed {
        display: none;
    }

    input.productname {
        cursor: pointer !important;
        background: white !important;
    }
</style>
<header class="page-header">
    <h2>Stock Adjustment</h2>
</header>


<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>

<div class="d-flex justify-content-center">
    <div class="col-md-11">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="panel-title">Make Adjustment</h2>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <a href="?" class="btn btn-default">
                            <i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <form action="<?= url('stocks', 'save_adjustment') ?>" method="post"
                      onsubmit="return validateInputs()">
                    <input type="hidden" name="adj[token]" value="<?= unique_token() ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Location:</h5>
                            <select id="locationid" class="form-control" name="adj[locationid]" required
                                    onchange="setLocation(this)"></select>
                        </div>
                        <div class="col-md-8">
                            <h5>Remarks:</h5>
                            <textarea name="adj[remarks]" class="form-control remarks" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="mt-xlg">
                        <h5 class="text-weight-bold">Items</h5>
                    </div>
                    <div class="row m-sm">
                        <div class="col-md-12">
                            <div class="col-md-4 p-none pl-xs text-weight-bold">Product</div>
                            <div class="col-md-1 p-none pl-xs text-weight-bold">Current Stock</div>
                            <div class="col-md-1 p-none pl-xs text-weight-bold text-center">Action</div>
                            <div class="col-md-2 p-none pl-xs text-weight-bold" title="adjusting quantity">Qty</div>
                            <div class="col-md-2 p-none pl-xs text-weight-bold">New Stock</div>
                            <div class="col-md-2 p-none pl-xs text-weight-bold">Remarks</div>
                        </div>
                    </div>
                    <div id="items-holder">
                        <div class="row m-sm group">
                            <button type="button" class="btn btn-danger"
                                    style="position: absolute;top:5px;right: 5px;"
                                    onclick="removeRow(this);">
                                <i class="fa fa-remove"></i>
                            </button>
                            <div class="col-md-12">
                                <div class="col-md-4 p-none pl-xs" style="position: relative;">
                                    <object class="loading-spinner" data="images/loading_spinner.svg" type="image/svg+xml"
                                            height="30" width="30"
                                            style="display:none;position: absolute;top: -10px;right: 0;z-index: 1000"></object>
                                    <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                           onclick="open_modal(this,'.group',fetchDetails,$('#locationid'))">
                                    <input type="hidden" class="inputs productid" name="productid[]">
                                    <input type="hidden" class="inputs stockid" name="stockid[]">
                                </div>
                                <div class="col-md-1 p-none pl-xs">
                                    <input type="text" class="form-control current_stock" name="current_stock[]" readonly>
                                </div>
                                <div class="col-md-1 p-none pl-xs">
                                    <div class="checkbox m-none text-center" title="check if you want to add">
                                        <label class="d-flex">
                                            <input type="checkbox" class="action" name="product_action[]" value="stockid"
                                                   style="height: 20px;width: 20px;" onchange="updateProductNewQty(this)">
                                            <small class="ml-xs action_label">reducing</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2 p-none pl-xs" title="adjusting quantity">
                                    <input type="number" class="form-control qty" name="qty[]" autocomplete="off" readonly
                                           value="0" min="0" onchange="updateProductNewQty(this)"
                                           onkeyup="updateProductNewQty(this)">
                                </div>
                                <div class="col-md-2 p-none pl-xs">
                                    <input type="text" class="form-control new_stock" readonly>
                                </div>
                                <div class="col-md-2 p-none pl-xs">
                                    <textarea name="remarks[]" class="form-control remarks" rows="2" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-md">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addItem()">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-3">
                            <button class="btn btn-success btn-block">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<?= component('normal-order/product_search_modal.tpl.php') ?>


<script src="assets/js/quick_adds.js"></script>
<script>

    let WITH_EXPIRED = 'yes';
    $(function () {
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
    });

    function validateInputs() {
        if ($('.group').length < 1) {
            triggerError('Choose at-least one item!');
            return false;
        }

        let valid = true;
        $('.group').each(function () {
            let stockid = $(this).find('.stockid').val();
            if (stockid == null || stockid == '') {
                triggerError('Enter valid stock adjustment info!', 3000);
                $(this).find('.current_stock').focus();
                valid = false;
                return false;
            }

            //validate batches if exists
            let batches = $(this).find('.batches');
            if (batches.length > 0) {
                if ($(batches).find('tbody tr').not('.removed').length < 1) {
                    triggerError('Adjust at-least one batch!', 3000);
                    $(this).find('.current_stock').focus();
                    valid = false;
                    return false;
                }
            }
        });
        if (!valid) return false;
        $('#spinnerHolder').show();
    }

    function setLocation(obj) {
        $('#items-holder').empty();
        addItem();
    }

    function fetchDetails(obj) {
        let group = $('.group.active-group');
        let productId = $(obj).data('productid');
        let productname = $(obj).data('productname');
        let stockid = $(obj).data('stockid');
        let description = $(obj).data('description');
        let locationid = $('#locationid').val();

        if (productId == null || productId == '') {
            triggerError('Product info not found');
            return;
        }
        if (!locationid || locationid == '') {
            triggerError('Choose location first');
            return;
        }

        if (stockid == null || stockid == '') {
            triggerError('No stock found for adjusting');
            return;
        }

        $(group).find('.form-control.inputs').val('');
        // $(group).find('.productid').val(productId);
        // $(group).find('.productname').val(productname);

        $(group).removeClass('active-group');
        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();

        let spinner = $(group).find('.loading-spinner');
        spinner.show();
        //ajax
        $.get(`?module=stocks&action=getAdjustmentStock&format=json`, {locationId: locationid, productId: productId}, function (data) {
            let result = JSON.parse(data);
            spinner.hide();
            // console.log(result);
            if (result.length > 0) {
                let product = result[0];

                $(group).find('.productid').val(productId);
                $(group).find('.productname').val(productname);

                $(group).find('.stockid, .action').val(product.id);
                $(group).find('.current_stock, .new_stock').val(product.total);
                $(group).find('.qty').attr('max', product.total).attr('min', 1).val(0);

                $(group).find('.batches').remove();
                if (product.track_expire_date == 1) {
                    $(group).find('.action').prop('disabled', true);
                    $(group).find('.qty').prop('readonly', true).prop('required', false).removeAttr('min');
                    addBatchTable(group, product.batches);
                } else {
                    $(group).find('.action').prop('disabled', false);
                    $(group).find('.qty').prop('readonly', false);
                }
            } else {
                triggerError(`No stock found for ${productname} for adjusting`, 5000);
            }
        });
    }

    function updateBatchNewQty(obj) {
        let tr = $(obj).closest('tr');
        let currentBatchStock = parseInt($(tr).find('.current_batch_stock').val()) || 0;
        let batchQty = parseInt($(tr).find('.batch_qty').val()) || 0;
        if ($(tr).find('.batch_action').is(':checked')) { //if action checked
            $(tr).find('.batch_action_label').text('adding');
            $(tr).find('.batch_qty').removeAttr('max');
        } else {
            $(tr).find('.batch_action_label').text('reducing');
            $(tr).find('.batch_qty').attr('max', currentBatchStock);
            if (batchQty > currentBatchStock) {
                triggerError('You cant reduce more than you have in stock!', 1500);
                batchQty = currentBatchStock;
                $(tr).find('.batch_qty').val(batchQty);
            }
            batchQty = -batchQty;
        }
        let newStock = currentBatchStock + batchQty;
        $(tr).find('.new_batch_stock').val(newStock);

        updateProductNewQty(obj);
    }

    function updateProductNewQty(obj) {
        let group = $(obj).closest('.group');
        if ($(group).find('.batches').length > 0) { //fro tracking expire products
            let totalBatchQty = 0;
            $(group).find('.batches tbody tr .new_batch_stock').each(function () {
                let newStockQty = parseInt($(this).val());
                // console.log(newStockQty);
                totalBatchQty += newStockQty;
            });
            $(group).find('.new_stock').val(totalBatchQty);
        } else {
            let adjQty = parseInt($(group).find('.qty').val()) || 0;
            let currentStock = parseInt($(group).find('.current_stock').val()) || 0;
            if ($(group).find('.action').is(':checked')) {
                $(group).find('.action_label').text('adding');
                $(group).find('.qty').removeAttr('max');
            } else {
                $(group).find('.action_label').text('reducing');
                $(group).find('.qty').attr('max', currentStock);
                if (adjQty > currentStock) {
                    triggerError('You cant reduce more than you have in stock!', 1500);
                    adjQty = currentStock;
                    $(group).find('.qty').val(adjQty);
                }
                adjQty = -adjQty;
            }
            let newStock = currentStock + adjQty;
            $(group).find('.new_stock').val(newStock);
        }
    }

    function addItem() {
        let item = `<div class="row m-sm group">
                            <button type="button" class="btn btn-danger"
                                    style="position: absolute;top:5px;right: 5px;"
                                    onclick="removeRow(this);">
                                <i class="fa fa-remove"></i>
                            </button>
                            <div class="col-md-12">
                                <div class="col-md-4 p-none pl-xs" style="position: relative;">
                                    <object class="loading-spinner" data="images/loading_spinner.svg" type="image/svg+xml"
                                            height="30" width="30" style="display:none;position: absolute;top: -10px;right: 0;z-index: 1000"></object>
                                    <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                           onclick="open_modal(this,'.group',fetchDetails,$('#locationid'))">
                                    <input type="hidden" class="inputs productid" name="productid[]">
                                    <input type="hidden" class="inputs stockid" name="stockid[]">
                                </div>
                                <div class="col-md-1 p-none pl-xs">
                                    <input type="text" class="form-control current_stock" name="current_stock[]" readonly>
                                </div>
                                <div class="col-md-1 p-none pl-xs">
                                    <div class="checkbox m-none text-center" title="check if you want to add">
                                        <label class="d-flex">
                                            <input type="checkbox" class="action" name="product_action[]" value="stockid"
                                                   style="height: 20px;width: 20px;" onchange="updateProductNewQty(this)">
                                            <small class="ml-xs action_label">reducing</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2 p-none pl-xs" title="adjusting quantity">
                                    <input type="number" class="form-control qty" name="qty[]" autocomplete="off" readonly
                                           value="0" min="0" onchange="updateProductNewQty(this)"
                                           onkeyup="updateProductNewQty(this)">
                                </div>
                                <div class="col-md-2 p-none pl-xs">
                                    <input type="text" class="form-control new_stock" readonly>
                                </div>
                                <div class="col-md-2 p-none pl-xs">
                                    <textarea name="remarks[]" class="form-control remarks" rows="2" required></textarea>
                                </div>
                            </div>
                        </div>`;
        $('#items-holder').append(item);
    }

    function removeRow(obj) {
        $(obj).closest('.group').remove();
    }

    function addBatchTable(group, batches) {
        let stockid = $(group).find('.stockid').val();
        let batchRows = ``;
        $.each(batches, function (i, batch) {
            // console.log(batch);
            batchRows += `<tr title="${batch.expire_remain_days <= 0 ? 'expired' : ''}">
                            <td>
                                <input type="hidden" class="batch_id" name="batches[${stockid}][batchId][]" value="${batch.batchId}">
                                <span class="batch_no">${batch.batch_no}</span>
                            </td>
                            <td class="${batch.expire_remain_days <= 0 ? 'bg-danger' : ''}"><span class="expire_date">${batch.expire_date}</span></td>
                            <td>
                                <input class="form-control input-sm current_batch_stock" readonly name="batches[${stockid}][current_stock][]" value="${batch.total}">
                            </td>
                            <td>
                                <div class="checkbox m-none text-center" title="check if you want to add">
                                    <label>
                                        <input type="checkbox" class="batch_action" name="batch_actions[]" value="${batch.batchId}"
                                                onchange="updateBatchNewQty(this)">
                                                <small class="batch_action_label">reducing</small>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control input-sm batch_qty" min="1" max="${batch.total}" required
                                       name="batches[${stockid}][qty][]" onchange="updateBatchNewQty(this)" onkeyup="updateBatchNewQty(this)">
                            </td>
                            <td>
                                <input class="form-control input-sm new_batch_stock" readonly value="${batch.total}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-xs"
                                        onclick="removeBatch(this)">
                                    <i class="fa fa-close"></i></button>
                            </td>
                        </tr>`;
        });
        let batchTable = `<div class="col-md-12 batches">
                            <div class="col-md-7 col-md-offset-5">
                                <div class="d-flex justify-content-between align-items-center mt-sm">
                                    <h5>Batches</h5>
                                    <div>
                                        <button type="button" class="btn btn-warning btn-sm" title="clear unused batches"
                                            onclick="clearUnusedBatches(this)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" title="restore unused batches"
                                            onclick="restoreUnusedBatches(this)">
                                            <i class="fa fa-recycle"></i>
                                        </button>
                                    </div>
                                </div>
                                <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                    <thead>
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Expire Date</th>
                                        <th>Current Stock</th>
                                        <th>Action +/-</th>
                                        <th>Qty</th>
                                        <th>New Stock</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    ${batchRows}
                                    </tbody>
                                </table>
                            </div>
                        </div>`;
        $(group).append(batchTable);
    }

    function removeBatch(obj) {
        let tr = $(obj).closest('tr');
        $(tr).find('.batch_id').prop('disabled', true);
        $(tr).find('.current_batch_stock').prop('disabled', true);
        $(tr).find('.batch_action').prop('disabled', true);
        $(tr).find('.batch_qty').prop('disabled', true);
        $(tr).find('.new_batch_stock').prop('disabled', true);
        $(tr).addClass('removed');
    }

    function clearUnusedBatches(obj) {
        let batchHolder = $(obj).closest('.batches');
        $(batchHolder).find('table tbody tr').each(function (i, tr) {
            let batchQty = parseInt($(tr).find('.batch_qty').val()) || 0;
            if (batchQty === 0) {
                $(tr).find('.batch_id').prop('disabled', true);
                $(tr).find('.current_batch_stock').prop('disabled', true);
                $(tr).find('.batch_action').prop('disabled', true);
                $(tr).find('.batch_qty').prop('disabled', true);
                $(tr).find('.new_batch_stock').prop('disabled', true);
                $(tr).addClass('removed');
            }
        });
    }

    function restoreUnusedBatches(obj) {
        let batchHolder = $(obj).closest('.batches');
        $(batchHolder).find('table tbody tr').each(function (i, tr) {
            $(tr).find('.batch_id').prop('disabled', false);
            $(tr).find('.current_batch_stock').prop('disabled', false);
            $(tr).find('.batch_action').prop('disabled', false);
            $(tr).find('.batch_qty').prop('disabled', false);
            $(tr).find('.new_batch_stock').prop('disabled', false);
            $(tr).removeClass('removed');
        });
    }
</script>