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

    .col-md-2 label, .col-md-7 label .col-md-3 label {
        font-weight: 600;
    }

    .col-md-2, .col-md-3, .col-md-7, .col-md-4, .col-md-1 {
        font-size: 15px;
    }

    .col-md-2, .col-md-3 {
        border-left: 1px solid white;
    }

    .col-md-2, .col-md-3, .col-md-1, .col-md-4 {
        text-align: center;
    }

    .focusInput {
        font-weight: 700;
        border: 2px solid #4CAF50;
    }

    .col-md-12.saveBtn-holder {
        padding: 10px;
        text-align: center;
    }

    .fordetails .col-md-2, .col-md-1, .col-md-3 {
        padding-left: 0;
        padding-right: 0;
    }


    .panel-body {
        padding: 0;
    }


    h5 {
        text-align: left;
    }

    .group {
        border: 1px dashed grey;
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 20px 15px 5px 15px;
        position: relative;
    }

    .border-danger, .input-error {
        border: 1px solid red;
    }

    .out-of-stock {
        border: 1px dashed red !important;
        background-color: #ffdada;
    }

    input.productname {
        cursor: pointer !important;
        background: white !important;
    }

    .removed {
        display: none;
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
</style>
<header class="page-header">
    <h2>Stock Transfer</h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h1 class="text-weight-semibol text-danger">Please wait</h1>
    </div>
</div>
<form action="<?= url('stocks', 'save_transfer') ?>" method="post" onsubmit="return validateInputs()">
    <input type="hidden" name="transfer[token]" value="<?= unique_token() ?>">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10 forSetting">
            <div class="panel">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Transfer Details</h2>
                </div>
                <div class="panel-body">
                    <div class="col-md-4 locationid">
                        <label class="col-md-12" style="font-size: 17px;padding: 10px;">From (Stock Location)</label>
                        <? if ($requisition) { ?>
                            <input id="locationid" type="hidden" name="transfer[location_from]"
                                   value="<?= $requisition['location_to'] ?>">
                            <input type="text" readonly class="form-control" value="<?= $requisition['tolocation'] ?>">
                        <? } else { ?>
                            <select onchange="getLocationStock(this);" id="locationid" required
                                    title="Please enter Location"
                                    class="form-control" name="transfer[location_from]">
                                <? if ($stocklocationDefault) { ?>
                                    <option selected
                                            value="<?= $stocklocationDefault[0]['id'] ?>"><?= $stocklocationDefault[0]['name'] ?></option>
                                <? } ?>
                            </select>
                        <? } ?>
                    </div>
                    <div class="col-md-4">
                        <label class="col-md-12" style="font-size: 17px;padding: 10px;">To (Stock Location)</label>
                        <? if ($requisition) { ?>
                            <input type="hidden" name="transfer[location_to]" value="<?= $requisition['location_from'] ?>">
                            <input type="text" readonly class="form-control" value="<?= $requisition['fromlocation'] ?>">
                        <? } else { ?>
                            <select id="locationidto" required title="Please enter Location" class="form-control"
                                    name="transfer[location_to]" onchange="checkLocation(this)"></select>
                        <? } ?>
                    </div>
                    <div class="col-md-4">
                        <label class="col-md-12" style="font-size: 17px;padding: 10px;">Requisition No</label>
                        <input type="text" name="transfer[reqid]" class="form-control text-center"
                               placeholder="Requisition no"
                               readonly value="<?= $requisition['id'] ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="col-md-12" style="font-size: 17px;padding: 10px;">Description</label>
                        <textarea name="transfer[description]" class="form-control" placeholder="Transfer description"
                                  rows="2" cols="80"></textarea>
                    </div>
                    <div class="col-md-12">
                        <section class="panel">
                            <header class="panel-heading">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h2 class="panel-title">Details / Item List</h2>

                                </div>
                            </header>
                            <div class="panel-body fordetails">
                                <input id="oldPrice" type="hidden"/>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3"><label>Product Name</label></div>
                                        <div class="col-md-3"><label>Stock Qty</label></div>
                                        <div class="col-md-3"><label>Qty</label></div>
                                        <div class="col-md-2"><label></label></div>
                                    </div>
                                    <div id="items-holder">
                                        <? if ($requisition) {
                                            foreach ($requisition['details'] as $index => $detail) { ?>
                                                <?= component('stock/transfer_detail_item.tpl.php', compact('detail')) ?>
                                            <? }
                                        } else { ?>
                                            <div class="group container-fluid">
                                                <button type="button" class="btn btn-danger" style="position: absolute;top:10px;right: 10px;"
                                                        onclick="removeRow(this);"><i class="fa fa-remove"></i>
                                                </button>
                                                <div class="row">
                                                    <div class="col-md-3" style="position: relative;">
                                                        <object class="search-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="30"
                                                                width="30" style="position: absolute;top:0;right: 10px;display: none;"></object>
                                                        <input type="hidden" class="inputs producttype" name="stockid[]">
                                                        <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                                               onclick="open_modal(this,'.group',getProductDetails)">
                                                        <input type="hidden" class="inputs productid">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" data-input='qty' placeholder="stock qty" readonly
                                                               class="form-control inputs input-block stockQty">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="number" oninput="maxmumSelection(this)" data-input='qty' placeholder="qty"
                                                               autocomplete="off" class="form-control inputs input-block forqty" min="1" required
                                                               name="quantity[]">
                                                    </div>
                                                    <div class="col-md-3 serialno-holder">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 p-none">
                                                        <textarea rows="2" class="form-control inputs product_description" readonly></textarea>
                                                    </div>
                                                    <div class="col-md-6 p-none">
                                                        <div class="batch-holder"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-success" onclick="addRow()">
                                        <i class="fa fa-plus"></i> Add Item
                                    </button>
                                </div>
                                <div class="col-md-12 saveBtn-holder">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-primary btn-block">
                                                <i class="fa fa-save"></i> Save transfer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= component('normal-order/product_search_modal.tpl.php') ?>
<script src="assets/js/quick_adds.js"></script>
<script>
    let INCLUDE_NON_STOCK = 'no';

    $(function () {
        <?if(!$requisition){?>
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'select location', 2);
        initSelectAjax('#locationidto', "?module=locations&action=getLocations&format=json", 'select location', 2);
        <?}else{?>

        $('.serial-modal').each(function (i,modal) {
            populateSerialTable($(modal).closest('.group').find('.forqty'));
        });

        <?}?>
    });

    function validateInputs() {
        let valid = true;
        if ($('.group').length === 0) {
            triggerError('Choose at least one product!');
            return false;
        }

        if ($('.group.out-of-stock').length > 0) {
            triggerError('Some items are stock of stock!', 4000);
            doBounce($('.group.out-of-stock'), 3, '10px', 200);
            valid = false;
            return false;
        }
        if (!valid) return false;

        //validate serialnos
        $('.group .serial-modal').each(function (i, modal) {
            let qty = parseInt($(modal).closest('.group').find('.forqty').val());
            let serialno_qty = $(modal).find('tbody tr').length;
            // console.log(qty,serialno_qty);
            if (qty !== serialno_qty) {
                triggerError("Serial number qty does not match transfer qty", 6000);
                $(modal).closest('.group').find('.forqty').focus();
                valid = false;
                return false;
            }

            $(modal).find('tbody tr').each(function (i, tr) {
                let icon = $(tr).find('td.status i');  //status icon
                if ($(icon).hasClass('fa-close')) { //not validated
                    valid = false;
                    let productName = $(tr).closest('.group').find(".producttype").text();
                    triggerError(`${productName} Serial number not validated!`, 2500);
                    $(tr).closest('.group').find(".serialBtn").addClass('btn-danger').focus();//focus product
                    setTimeout(function () {
                        $(tr).closest('.group').find(".serialBtn").removeClass('btn-danger');
                    }, 2000);
                    return false;
                }
            });
        });
        if (!valid) return false;

        $('.group .batch-table').each(function (i, batchTable) {
            if ($(batchTable).find('tbody tr').not('.removed').length === 0) {
                triggerError('Batch qty is required!', 4000);
                $(batchTable).closest('.group').find('.forqty').focus();
                valid = false;
                return false;
            }
        });
        if (!valid) return false;
        $('#spinnerHolder').show();
    }

    function checkLocation(obj) {
        let location_from = $('#locationid').val();
        let location_to = $('#locationidto').val();
        if (!location_from || !location_to) return;
        if (location_to === location_from) {
            triggerError('From location and To locations cant be the same!', 5000);
            $(obj).val('').trigger('change');
        }
    }


    function addRow() {
        let assetRow = `<div class="group container-fluid">
                                            <button type="button" class="btn btn-danger" style="position: absolute;top:10px;right: 10px;"
                                                    onclick="removeRow(this);"><i class="fa fa-remove"></i>
                                            </button>
                                            <div class="row">
                                                <div class="col-md-3" style="position: relative;">
                                                    <object class="search-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="30"
                                                            width="30" style="position: absolute;top:0;right: 10px;display: none;"></object>
                                                    <input type="hidden" class="inputs producttype" name="stockid[]">
                                                    <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                                           onclick="open_modal(this,'.group',getProductDetails)">
                                                    <input type="hidden" class="inputs productid">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" data-input='qty' placeholder="stock qty" readonly
                                                           class="form-control inputs input-block stockQty">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" oninput="maxmumSelection(this)" data-input='qty' placeholder="qty"
                                                           autocomplete="off" class="form-control inputs input-block forqty" min="1" required
                                                           name="quantity[]">
                                                </div>
                                                <div class="col-md-3 serialno-holder">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 p-none">
                                                    <textarea rows="2" class="form-control inputs product_description" readonly></textarea>
                                                </div>
                                                <div class="col-md-6 p-none">
                                                    <div class="batch-holder"></div>
                                                </div>
                                            </div>
                                        </div>`;
        $('#items-holder').append(assetRow);
        $("html, body").animate({scrollTop: $(document).height()}, 500);
    }

    function removeRow(obj) {
        $(obj).closest('.group').remove();
    }

    function addBatchTable(obj, batchList) {
        let stockid = $(obj).closest('.group').find('.producttype').val();
        let batches = ``;
        $.each(batchList, function (i, item) {
            batches += `<tr>
                           <td>
                               <input type="hidden"
                                      name="batch[${stockid}][batchId][]"
                                      value="${item.batchId}">
                               <input type="text" readonly placeholder="batch no."
                                      class="form-control input-sm input-block batch_no"
                                      value="${item.batch_no}">
                           </td>
                           <td>
                               <input type="number" readonly placeholder="qty in"
                                      class="form-control input-sm input-block batch_qty_in"
                                      value="${item.total}">
                           </td>
                           <td>
                               <input type="text" readonly placeholder="qty in"
                                      class="form-control input-sm input-block batch_qty_in"
                                      value="${item.expire_date}">
                           </td>
                           <td>
                               <input type="number" placeholder="qty out"
                                      name="batch[${stockid}][qty_out][]"
                                      min="1" max="${item.total}" required
                                      class="form-control input-sm input-block batch_qty_out"
                                      oninput="updateQty(this)">
                           </td>
                           <td>
                               <button type="button" class="btn btn-sm btn-danger"
                                       onclick="removeBatch(this)">
                                   <i class="fa fa-minus"></i>
                               </button>
                           </td>
                       </tr>`;
        });

        let batchTable = `<table class="table table-hover table-bordered batch-table" style="font-size:10pt">
                              <thead>
                              <tr>
                                  <td>Batch No</td>
                                  <td>Stock Qty</td>
                                  <td>Expire Date</td>
                                  <td>Qty</td>
                                  <td>
                                      <button title="clear unused" type="button" class="btn btn-warning btn-sm mr-md"
                                              onclick="clearUnusedBatches(this)"><i class="fa fa-filter"></i>
                                      </button>
                                      <button title="refresh batches" type="button" class="btn btn-info btn-sm"
                                              onclick="refreshBatches(this)"><i class="fa fa-refresh"></i>
                                      </button>
                                  </td>
                              </tr>
                              </thead>
                              <tbody>${batches}</tbody>
                          </table>
                          `;
        $(obj).closest('.group').find('.batch-holder').append(batchTable);
    }

    function removeBatch(obj) {

        $(obj).closest('tr').addClass('removed');
        $(obj).closest('tr').find('input').prop('disabled', true);
        $(obj).closest('tr').find('.batch_qty_out').val('');

        //remove entire product if no batch selected
        updateQty($(obj).closest('tr').find('.batch_qty_out'));
    }

    function updateQty(obj) {
        //limit selected batch qty to max qty
        let maxBatchQty = parseInt($(obj).attr('max'));
        let qty = parseInt($(obj).val()) || 0;
        if (qty > maxBatchQty) {
            triggerError('Selected quantity exceed available batch quantity!');
            $(obj).val(maxBatchQty);
            updateQty(obj);
            return;
        }

        let totalBatchQty = 0;
        let group = $(obj).closest('.group');
        $(group).find('.batch-holder .batch_qty_out').each(function () {
            totalBatchQty += parseInt($(this).val()) || 0;
        });
        $(group).find('.forqty').val(totalBatchQty);
    }

    //shows hidden batches
    function refreshBatches(obj) {
        $(obj).closest('.batch-holder').find('tbody tr').each(function (i, item) {
            setTimeout(function () {
                $(item).removeClass('removed');
                $(item).find('input').prop('disabled', false);
            }, i * 50);
        });
    }

    //clear unused batches
    function clearUnusedBatches(obj) {
        $(obj).closest('.batch-holder').find('tbody tr').each(function (i, item) {
            let selectedBatchQty = parseInt($(item).find('.batch_qty_out').val()) || 0;
            if (isNaN(selectedBatchQty) || selectedBatchQty < 1) {
                setTimeout(function () {
                    $(item).addClass('removed');
                    $(item).find('input').prop('disabled', true);
                    $(item).find('.batch_qty_out').val('')
                }, i * 50);
            }
        });
    }

    function maxmumSelection(obj) {
        let maxTransferQty = parseFloat($(obj).attr('max'));
        let qty = parseFloat($(obj).val());
        if (qty > maxTransferQty) {
            triggerError(`You can't transfer more than you have (${maxTransferQty}) in stock!`, 3000);
            $(obj).val(maxTransferQty);
            maxmumSelection(obj);
            return;
        }
        populateSerialTable(obj);
    }

    function getLocationStock(obj) {
        $('#items-holder').empty();
        addRow();
        checkLocation(obj);
    }


    function getProductDetails(obj) {
        let group = $('.group.active-group');
        let locationid = $('#locationid').val();
        let productid = $(obj).data('productid');
        let stockid = $(obj).data('stockid');
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');
        let stockqty = parseInt($(obj).data('stockqty')) || 0;
        if (productid == null || productid == '') {
            triggerError('Product info not found');
            return;
        }

        //check duplicate
        if ($(`.group input.productid[value='${productid}']`).length > 0) {
            triggerError(`Product already selected`, 2000);
            return;
        }
        //check stock
        if (stockqty <= 0) {
            triggerError(`No enough stock for product ${productname}`, 2000);
            return;
        }

        $(group).find('.inputs').val('');
        $(group).find('.producttype').val(stockid);
        $(group).find('.productid').val(productid);
        $(group).find('.productname').val(productname);
        $(group).find('.product_description').val(description);

        $(group).removeClass('active-group');
        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();

        let searchSpinner = $(group).find('.search-spinner');
        searchSpinner.show();
        $.get(`?module=products&action=getStockProductDetails&format=json&`, {stockid: stockid, locationid: locationid}, function (details) {
            searchSpinner.hide();
            let product = JSON.parse(details);
            // console.log(product[0].quantity);
            if (product[0].found == 'yes') {
                $(group).find('.forqty').val(0).attr('max', product[0].quantity);
                $(group).find('.stockQty').val(product[0].quantity);

                $(group).find('.batch-holder').empty();
                if (product[0].track_expire_date == 1) {
                    $(group).find('.forqty').prop('readonly', true);
                    addBatchTable($(group).find('.forqty'), product[0].batch_stock);
                }
                $(group).find('.serialno-holder').empty();
                if (product[0].trackserial == 1) {
                    addSerialModal($(group).find('.forqty'));
                }
            }
        });
    }

    function addSerialModal(obj) { //obj => grn detail row
        let group = $(obj).closest('.group');
        let stockid = $(group).find('.producttype').val();
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
                                            <h4 class="modal-title productName">Serial No</h4>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="text-primary">${productname}</h5>
                                            <div class="d-flex justify-content-between mb-sm">
                                                <div>
                                                    <button type="button" class="btn btn-default btn-sm ml-xs" onclick="$(this).closest('.modal').find('.file-input').trigger('click')"><i
                                                                class="fa fa-file"></i> Add from file
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm ml-xs" onclick="clearItems(this)"><i class="fa fa-trash"></i> Clear
                                                    </button>
                                                    <input type="file" class="file-input" style="display: none;" accept="*.txt" onchange="getSerialNos(this)">
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <object class="validate_spinner" data="images/loading_spinner.svg"
                                                               type="image/svg+xml" height="30" width="30" style="display: none"></object>
                                                    <button type="button" class="btn btn-primary btn-sm validate-btn" onclick="validateAllSerialNo(this)"> Validate all</button>
                                                </div>
                                            </div>
                                            <div style="max-height: 60vh; overflow-y: auto;">
                                                <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                                    <thead>
                                                    <tr>
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
        $(group).find('.serialno-holder').append(serialModal);
    }

    function populateSerialTable(obj) {
        let group = $(obj).closest('.group');
        let qty = parseInt($(group).find('.forqty').val());
        let stockid = $(group).find('.producttype').val();
        $(group).find('.serial-modal tbody tr').remove();
        // console.log(qty, 'stockid', stockid);
        for (let i = 0; i < qty; i++) {
            let row = `<tr>
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

    function getSerialNos(obj) {
        let modal = $(obj).closest('.modal');
        let stockid = $(obj).closest('.group').find('.producttype').val();
        let qty = parseInt($(obj).closest('.group').find('.forqty').val());
        let file_input = $(modal).find('.file-input')[0];
        const reader = new FileReader();
        reader.readAsBinaryString(file_input.files[0]);

        reader.onload = function () {
            let serial_numbers = reader.result.split('\n');

            let snos = [];
            $.each(serial_numbers, function (i, no) {
                no = no.replace('\r', '');
                if (no.length > 0) snos.push(no);
            });
            if (snos.length !== qty) {
                triggerError(`Invalid number of uploaded serial number qty, needed ${qty} uploaded ${snos.length}`, 10000);
                return;
            }
            $(modal).find('table tbody').empty();

            $.each(snos, function (i, no) {
                let row = `<tr>
                               <td>
                                   <input type="text" class="form-control input-sm serial_number" name="serialno[${stockid}][serial_number][]"
                                       autocomplete="off" value="${no}" readonly>
                                   <small class="error-msg text-danger" style="display:none ;"></small>
                               </td>
                               <td class="status" style="text-align: center;vertical-align: middle;">
                                   <i class="fa fa-close text-danger text-weight-bold" style="display: none"></i>
                               </td>
                           </tr>`;
                $(modal).find('table tbody').append(row);
            });
            resetFileInput(obj);

        };
        // start reading the file. When it is done, calls the onload event defined above.

    }

    function clearItems(obj) {
        let modal = $(obj).closest('.modal');
        $(modal).find('table tbody').empty();
        resetFileInput(obj);
    }

    function resetFileInput(obj) {
        let file_input = `<input type="file" class="file-input" style="display: none;" accept="*.txt" onchange="getSerialNos(this)">`;
        let modal = $(obj).closest('.modal');
        $(modal).find('.file-input').replaceWith(file_input);
    }

    function validateAllSerialNo(obj) {
        let modal = $(obj).closest('.modal');
        let spinner = $(modal).find('.validate_spinner');
        let validate_btn = $(modal).find('.validate-btn');
        let serial_number_inputs = $(modal).find('table tbody .serial_number');
        let stockid = $(obj).closest('.group').find('.producttype').val();
        let qty = parseInt($(obj).closest('.group').find('.forqty').val());

        if (serial_number_inputs.length === 0) {
            triggerError('Entered at least one serial number', 3000);
            return false;
        }
        //check values
        let valid = true;
        $(serial_number_inputs).each(function () {
            if ($.trim($(this).val()).length === 0) {
                triggerError('Invalid serial number');
                $(this).focus();
                valid = false;
                return false;
            }
        });
        if (!valid) return;
        if (serial_number_inputs.length > qty) {
            triggerError(`Entered serial number qty ${serial_number_inputs.length} exceed transfer qty ${qty}`, 3000);
            resetFileInput(obj);
            return false;
        }


        let serialnos = Array.from($(serial_number_inputs).get(), function (e) {
            return $.trim(e.value);
        });

        //check duplicates
        if (hasDuplicateSerialNo(serialnos, obj)) return false;
        //check duplicates
        validate_btn.prop('disabled', true);
        spinner.show();

        if (!stockid) {
            triggerError('Stock id not found', 3000);
            return false;
        }

        $.post(`?module=serialnos&action=validateSerialnoBundleForTransfer&format=json&`,
            {
                stockid: stockid,
                serialnos: serialnos
            },
            function (data) {
                spinner.hide();
                validate_btn.prop('disabled', false);
                let result = JSON.parse(data);
                // console.log(result);
                if (result.status === 'success') {
                    validate_btn.prop('disabled', false);
                    $.each(result.data, function (i, item) {
                        $(modal).find('table tbody input.serial_number').each(function (i, input) {
                            if ($(input).val() == item.number) {
                                let tr = $(this).closest('tr');
                                if (item.status === 1) {
                                    $(tr).find('.status i.fa').removeClass('fa-close text-danger')
                                        .addClass('fa-check text-success')
                                        .show();
                                    $(tr).find('.error-msg').text('').hide();
                                } else {
                                    $(tr).find('.status i.fa').show();
                                    $(tr).find('.error-msg').text(item.msg || 'error').show();
                                }
                            }
                        });
                    });

                } else {
                    triggerError(result.msg || 'error found');
                }
            });

    }

    function hasDuplicateSerialNo(serialnos, obj) {
        let modal = $(obj).closest('.modal');
        let serial_number_inputs = $(modal).find('table tbody .serial_number');
        $(serial_number_inputs).removeClass('border-danger');
        let sorted_arr = serialnos.slice().sort();
        let duplicates = [];
        for (let i = 0; i < sorted_arr.length - 1; i++) {
            if (sorted_arr[i + 1] == sorted_arr[i]) {
                duplicates.push(sorted_arr[i]);
            }
        }

        if (duplicates.length > 0) {
            triggerError('Duplicate serial number found!', 3000);
            $.each(duplicates, function (i, no) {
                $(modal).find(`tbody .serial_number[value="${no}"]`).addClass('border-danger');
            });
            return true;
        } else {
            return false;
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
            let stockid = $(obj).closest('.group').find(`.producttype`).val();
            // console.log(number, 'stockid', stockid);
            //ajax query
            $.get(`?module=serialnos&action=validateSerialno&format=json&number=${number}&stockid=${stockid}`, null, function (data) {
                let result = JSON.parse(data);
                spinner.hide();
                // console.log(result);
                if (result.status === 'success') {
                    triggerMessage(result.message);
                    parentSerialRow.attr('title', result.message);
                    icon.removeClass('fa-close text-danger')
                        .addClass('fa-check text-success');
                } else {
                    triggerError(result.message||'error found',3000);
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

    function addComma(obj) {
        return $(obj).val($(obj).val().replace(/(,|)/g, '').replace(/(.)(?=(.{3})+$)/g, "$1,").replace(',.', '.'));
    }

    function doBounce(element, times, distance, speed) {
        $(element).css('position', 'absolute');
        for (let i = 0; i < times; i++) {
            element.animate({top: '-=' + distance}, speed)
                .animate({top: '+=' + distance}, speed);
        }
        $(element).css('position', 'relative');
    }

</script>
