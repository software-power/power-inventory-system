<style>
    .row {
        border-bottom: 1px solid #ecedf0;
        margin-top: 10px;
        /* background: #ecedf0; */
        padding: 3px;
    }

    .col-md-6 label {
        font-size: 15px;
    }

    .col-md-1 label {
        font-weight: 600;
    }

    .col-md-4 label {
        font-weight: 600;
    }

    .col-md-3 label, .col-md-2 label, .col-md-7 label .col-md-3 label {
        font-weight: 600;
    }

    .col-md-2, .col-md-3, .col-md-7, .col-md-4, .col-md-1 {
        font-size: 15px;
    }

    .col-md-2, .col-md-3 {
        border-left: 1px solid white;
    }

    .fordetails .col-md-2, .fordetails .col-md-3, .fordetails .col-md-1, .fordetails .col-md-4 {
        text-align: center;
    }

    .col-md-12.saveBtn-holder {
        padding: 10px;
        text-align: center;
    }

    select#supplierInpt {
        height: 38px;
    }

    .fordetails .col-md-2, .fordetails .col-md-1, .fordetails .col-md-3 {
        padding-left: 0;
        padding-right: 0;
    }

    .select2-container .select2-selection--single {
        text-align: left;
    }

    .panel-body {
        padding: 0;
    }

    .group {
        border: 1px dashed grey;
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 15px;
        position: relative;
    }

</style>
<header class="page-header">
    <h2>GRN - Return</h2>
</header>
<form action="<?= url('grns', 'grn_return_save') ?>" method="post" onsubmit="return validateSubmit(this)">
    <div class="col-md-12 forSetting">
        <div class="panel">
            <div class="panel-heading">
                <h2 class="panel-title"><i class="fa fa-archive"></i> GRN Return</h2>
            </div>
            <div class="panel-body">
                <div class="col-md-4">
                    <label class="col-md-12" style="font-size: 17px;padding: 10px;">Supplier</label>
                    <input readonly required class="form-control" value="<?= $grn['suppliername'] ?>"/>
                </div>
                <div class="col-md-3">
                    <label class="col-md-12" style="font-size: 17px;padding: 10px;">Stock Location</label>
                    <input type="hidden" name="grn[locid]" value="<?=$grn['st_locid']?>">
                    <input readonly required class="form-control" value="<?= $grn['stock_location'] ?>"/>
                </div>
                <div class="col-md-1">
                    <label class="col-md-12" style="font-size: 17px;padding: 10px;">GRN No.</label>
                    <input id="grnid" name="grn[grnid]" readonly
                           style="text-align: center;font-size: 16px;" class="form-control" type="number"
                           value="<?= $grn['grnnumber'] ?>"/>
                </div>
                <div class="col-md-2">
                    <label class="col-md-12" style="font-size: 17px;padding: 10px;">Invoice Number</label>
                    <input readonly style="text-align: center;font-size: 16px;"
                           class="form-control" type="text" value="<?= $grn['invoiceno'] ?>"/>
                </div>
                <div class="col-md-2">
                    <label class="col-md-12" style="font-size: 17px;padding: 10px;">Currency</label>
                    <input readonly style="text-align: center;font-size: 16px;"
                           class="form-control" type="text" value="<?= $grn['currency_name'] ?>"/>
                </div>
                <div class="col-md-12">
                    <label class="col-md-12" style="font-size: 17px;padding: 10px;">Description/Purpose</label>
                    <textarea name="grn[description]" class="form-control"
                              placeholder="Purpose of returning product to the supplier" rows="2" cols="80"></textarea>
                </div>
                <div class="col-md-12">
                    <section class="panel">
                        <header class="panel-heading">
                            <h2 class="panel-title">Details / Item List</h2>
                        </header>
                        <div class="panel-body fordetails">
                            <input id="oldPrice" type="hidden"/>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3"><label>Product Name</label></div>
                                    <div class="col-md-2"><label>Qty In</label></div>
                                    <div class="col-md-1"><label>Previous Out</label></div>
                                    <div class="col-md-1"><label>Current Stock Qty</label></div>
                                    <div class="col-md-2"><label>Qty Out</label></div>
                                    <div class="col-md-1"><label></label></div>
                                </div>
                                <div id="dropTable">
                                    <? foreach ($grn['stock'] as $index => $stock) { ?>
                                        <div class="group container-fluid">
                                            <button type="button" class="btn btn-danger"
                                                    style="position: absolute;top:10px;right: 10px;"
                                                    onclick="removeRow(this);">
                                                <i class="fa fa-remove"></i>
                                            </button>
                                            <div class="row grn-details">
                                                <div class="col-md-3">
                                                    <input type="text" readonly class="form-control productname"
                                                           value="<?= $stock['productname'] ?>">
                                                    <input type="hidden" required value="<?= $stock['stockid'] ?>"
                                                           name="stockid[]" class="stockid"/>
                                                    <input type="hidden" name="gdi[]" value="<?= $stock['gdi'] ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" readonly class="form-control input-block"
                                                           value="<?= $stock['qty'] ?>">
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="number" readonly class="form-control input-block"
                                                           value="<?= $stock['totalReturnedQty'] ?>">
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="number" readonly class="form-control input-block"
                                                           value="<?= $stock['current_stock_qty'] ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" onkeyup="maxmumSelection(this)"
                                                           onchange="maxmumSelection(this)" placeholder="qty"
                                                        <?= $stock['track_expire_date'] == 1 ? 'readonly' : '' ?>
                                                           class="form-control input-block forqty" value="0" min="1"
                                                           max="<?= $stock['current_stock_qty'] ?>" name="quantity[]">
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="serial-holder text-left ml-sm">
                                                        <? if ($stock['trackserialno']) { ?>
                                                            <button type="button" class="btn btn-default serialBtn"
                                                                    title="Serial numbers"
                                                                    data-toggle="modal"
                                                                    data-target="#serialModal<?= $stock['stockid'] ?>">
                                                                <i class="fa fa-barcode"></i> Serial no
                                                            </button>
                                                            <div class="modal fade serial-modal"
                                                                 id="serialModal<?= $stock['stockid'] ?>" tabindex="-1"
                                                                 role="dialog"
                                                                 aria-labelledby="serialModal<?= $stock['stockid'] ?>"
                                                                 aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-center">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close"
                                                                                    data-dismiss="modal"
                                                                                    aria-label="Close"><span
                                                                                        aria-hidden="true">&times;</span>
                                                                            </button>
                                                                            <h4 class="modal-title productName">Serial
                                                                                No: <span
                                                                                        class="text-primary"><?= $stock['productname'] ?></span>
                                                                            </h4>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p class="text-danger"><?= $stock['validate_serialno'] == 1 ? 'Validates from Stock' : 'Enter manually' ?></p>
                                                                            <table class="table table-bordered">
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
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                    class="btn btn-default btn-sm"
                                                                                    data-dismiss="modal">Close
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <? } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <? if ($stock['track_expire_date']) { ?>
                                                <div class="row pt-md batches">
                                                    <div class="col-md-8 col-md-offset-4">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <td>Batch No</td>
                                                                <td>Qty In</td>
                                                                <td>Previous Out</td>
                                                                <td>Current Stock</td>
                                                                <td>Qty Out</td>
                                                                <td></td>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <? foreach ($stock['batches'] as $bi => $batch) { ?>
                                                                <tr>
                                                                    <td>
                                                                        <input type="hidden"
                                                                               name="batch[<?= $stock['stockid'] ?>][batchId][]"
                                                                               value="<?= $batch['batchId'] ?>">
                                                                        <input type="text" readonly
                                                                               placeholder="batch no."
                                                                               class="form-control input-block batch_no"
                                                                               value="<?= $batch['batch_no'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" readonly
                                                                               placeholder="qty in"
                                                                               class="form-control input-block batch_qty_in"
                                                                               value="<?= $batch['batchqty'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" readonly
                                                                               placeholder="qty in"
                                                                               class="form-control input-block"
                                                                               value="<?= $batch['totalBatchReturnQty'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" readonly
                                                                               placeholder="qty in"
                                                                               class="form-control input-block"
                                                                               value="<?= $batch['current_stock'] ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" placeholder="qty out"
                                                                               name="batch[<?= $stock['stockid'] ?>][qty_out][]"
                                                                               min="1"
                                                                               max="<?= $batch['current_stock'] ?>"
                                                                               required
                                                                               class="form-control input-block batch_qty_out"
                                                                               onkeyup="updateQty(this)"
                                                                               onchange="updateQty(this)">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button"
                                                                                class="btn btn-sm btn-warning"
                                                                                onclick="removeBatch(this)">
                                                                            <i class="fa fa-minus"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            <? } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            <? } ?>
                                        </div>
                                    <? } ?>
                                </div>
                                <div class="col-md-12 saveBtn-holder">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="?module=grns&action=grn_list" class="btn btn-success btn-block">
                                                <i
                                                        class="fa fa-list"></i> GRN list</a>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-primary btn-block">
                                                <i class="fa fa-save"></i> Save
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

</form>
<script>
    function validateSubmit(obj) {
        let selectedLength = $('.group').length;
        if (selectedLength < 1) {
            triggerError("Select at least one product!");
            return false;
        }

        //validate serialnos
        let valid = true;
        $('.serial_number').each(function () {
            let parentSerialRow = $(this).closest('tr');
            let icon = $(parentSerialRow).find('td.status i');  //status icon
            if ($(icon).hasClass('fa-close')) { //not validated
                valid = false;
                let productName = $(this).closest('.group').find(".productname").val();
                triggerError(`${productName} Serial number not validated!`, 2500);
                $(this).closest('.group').find(".forproducttype").focus();//focus product
                $('#cashPaymentModal').modal('hide');
                return false;
            }
        });
        if (!valid) return false;
    }

    $(function () {
        <?if($grnid){?>
        getGRNdetails('#grnid');
        <?}?>
    });

    function removeBatch(obj) {
        let tbody = $(obj).closest('tbody');
        $(obj).closest('tr').remove();
        if ($(tbody).find('tr').length > 0) {
            updateQty(tbody);
        } else {
            removeRow(tbody);
        }
    }

    function updateQty(obj) {
        //limit selected batch qty to max qty
        let maxReturnQty = Number($(obj).attr('max'));
        let qty = Number($(obj).val());
        if (qty > maxReturnQty) {
            triggerError(`You can't return more than you have in stock!`);
            $(obj).val(maxReturnQty);
            updateQty(obj);
        }

        let totalBatchQty = 0;
        let grn_details_row = $(obj).closest('.group');
        $(obj).closest('.batches tbody').find('.batch_qty_out').each(function () {
            totalBatchQty += Number($(this).val());
        });
        grn_details_row.find('.forqty').val(totalBatchQty);
    }

    function removeRow(obj) {
        $(obj).closest('.group').remove();
    }

    function populateSerialTable(group) { //group => .group
        let qty = parseInt($(group).find('.forqty').val());
        let stockid = $(group).find('.stockid').val();
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

    function maxmumSelection(obj) {
        let maxReturnQty = parseFloat($(obj).attr('max'));
        let qty = parseFloat($(obj).val());
        if (qty > maxReturnQty) {
            triggerError(`You can't return more than you have (${maxReturnQty}) in stock!`, 3000);
            $(obj).val(maxReturnQty);
            maxmumSelection(obj);
        }
        populateSerialTable($(obj).closest('.group'));
    }

    function hideSelect2(elementId) {
        $('#' + elementId).next(".select2-container").hide();
    }

    function showSelect2(elementId) {
        $('#' + elementId).next(".select2-container").show();
    }
</script>
