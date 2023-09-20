<style>
    th.stick {
        position: sticky !important;
        top: 100px; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }

    .input-error {
        border: 1px solid red;
    }


</style>
<header class="page-header">
    <h2>Edit GRN</h2>
</header>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">
                    <p>Edit GRN: <span class="text-primary ml-sm"><?= $grn['grnnumber'] ?></span></p>
                    <? if ($grn['supplier_payment']) { ?>
                        <span class="text-weight-bold text-danger" style="font-size: 11pt">Warning: This GRN tracks supplier payment, edit on your own risk!</span>
                    <? } ?>
                </h2>
            </header>
            <div class="panel-body">
                <div class="row">
                    <input type="hidden" class="grnid" value="<?= $grn['grnnumber'] ?>">
                    <div class="col-md-3">
                        <label>Supplier</label>
                        <? if ($grnPayment['paid_amount'] > 0) { ?>
                            <input id="supplierid" type="hidden" value="<?= $grn['supplierid'] ?>">
                            <input type="text" readonly class="form-control" value="<?= $grn['suppliername'] ?>">
                        <? } else { ?>
                            <select id="supplierid" class="form-control">
                                <option value="<?= $grn['supplierid'] ?>"><?= $grn['suppliername'] ?></option>
                            </select>
                        <? } ?>
                    </div>
                    <div class="col-md-3">
                        <label>LPO</label>
                        <input type="text" readonly class="form-control"
                               value="<?= $grn['lpoid'] ?: '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Invoice No</label>
                        <input id="invoiceno" type="text" class="form-control" placeholder="invoice no"
                               value="<?= $grn['invoiceno'] ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Verification Code</label>
                        <input id="verificationcode" type="text" class="form-control"
                               placeholder="verification code" value="<?= $grn['verificationcode'] ?>">
                    </div>
                </div>
                <div class="row mt-md">
                    <div class="col-md-3">
                        <label>Location</label>
                        <select id="locationid" class="form-control">
                            <option value="<? $grn['st_locid'] ?>"><?= $grn['stock_location'] ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Currency</label>
                        <? if ($grnPayment['paid_amount'] > 0) { ?>
                            <input id="currencyid" type="hidden" value="<?= $grn['currency']['rateid'] ?>"
                                   data-currencyname="<?= $grn['currency']['currencyname'] ?>">
                            <input type="text" readonly class="form-control"
                                   value="<?= $grn['currency']['currencyname'] ?> - <?= $grn['currency']['description'] ?>">
                        <? } else { ?>
                            <select id="currencyid" class="form-control">
                                <? foreach ($currencies as $i => $c) { ?>
                                    <option <?= selected($grn['currency_rateid'], $c['rateid']) ?>
                                            value="<?= $c['rateid'] ?>"><?= $c['currencyname'] ?></option>
                                <? } ?>
                            </select>
                        <? } ?>

                    </div>
                </div>
                <div class="row mt-xlg" style="<?= $grn['supplier_payment'] ? '' : 'display:none' ?>">
                    <div class="col-md-4">
                        <label>Amount Paid To Supplier</label>
                        <input type="hidden" id="paid_amount"
                               value="<?= $grnPayment['paid_amount'] ?>">
                        <input id="paid_amount_label" type="text" readonly
                               class="form-control text-weight-bold text-success"
                               value="<?= formatN($grnPayment['paid_amount']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>Outstanding Amount</label>
                        <input type="hidden" id="outstanding_amount"
                               value="<?= $grnPayment['outstanding_amount'] ?>">
                        <input id="outstanding_amount_label" type="text" readonly
                               class="form-control text-weight-bold text-danger"
                               value="<?= formatN($grnPayment['outstanding_amount']) ?>">
                    </div>
                </div>
                <div class="row mt-md">
                    <div class="col-md-3">
                        <label>Total Exclusive Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_exc_amount"
                               value="<?= formatN($grn['total_amount']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label>VAT Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_vat_amount"
                               value="<?= formatN($grn['grand_vatamount']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Total Inclusive Amount</label>
                        <input type="text" readonly class="form-control text-weight-bold total_inc_amount"
                               value="<?= formatN($grn['full_amount']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label>Adjustment Amount</label>
                        <input type="number" step="0.01" class="form-control text-weight-bold adjustment_amount"
                               value="<?= $grn['adjustment_amount'] ?>" onchange="calGrnTotalAmount()" onkeyup="calGrnTotalAmount()">
                    </div>
                </div>
                <div class="row mt-lg">
                    <div class="col-md-4">
                        <label>Payment Type</label>
                        <input id="paymenttype" type="text" readonly class="form-control"
                               value="<?= $grn['paymenttype'] ?>">
                    </div>
                    <div class="col-md-4 text-center">
                        <label>VAT Registered</label>
                        <div class="checkbox d-flex justify-content-center">
                            <label class="d-flex align-items-center">
                                <input id="vat_registered" onchange="enableVatDesc(this)" type="checkbox"
                                       style="height: 40px;width: 40px;" <?= $grn['vat_registered'] == 1 ? 'checked' : '' ?>>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <label>VAT Description</label>
                        <textarea id="vat_desc" disabled class="form-control"
                                  placeholder="VAT description"><?= $grn['vat_desc'] ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-lg">
                        <table class="table table-bordered" style="font-size: 10pt;">
                            <thead>
                            <tr>
                                <th class="stick">#</th>
                                <th class="stick">Product</th>
                                <th class="stick text-right">Rate</th>
                                <th class="stick text-right">Qty</th>
                                <th class="stick text-right" title="Quantity that is being payed for to supplier">Billable Qty</th>
                                <th class="stick text-right">Used Qty</th>
                                <th class="stick text-right" title="Overall held stock for this product">Held stock</th>
                                <th class="stick text-right">VAT</th>
                                <th class="stick text-right">Total Inc Cost</th>
                                <th class="stick text-right"></th>
                                <th class="stick text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? $count = 1;
                            foreach ($grn['stock'] as $index => $stock) { ?>
                                <tr class="products prod<?= $stock['stockid'] ?>">
                                    <td><?= $count ?></td>
                                    <td>
                                        <?= $stock['productname'] ?>
                                        <input type="hidden" class="gdi"
                                               value="<?= $stock['gdi'] ?>">
                                        <input type="hidden" class="productname"
                                               value="<?= $stock['productname'] ?>">
                                        <input type="hidden" class="track_expire_date"
                                               value="<?= $stock['track_expire_date'] ?>">
                                        <input type="hidden" class="trackserialno"
                                               value="<?= $stock['trackserialno'] ?>">
                                    </td>
                                    <td class="text-right rate"><?= $stock['rate'] ?></td>
                                    <td class="text-right qty"><?= $stock['qty'] ?></td>
                                    <td class="text-right billable_qty"><?= $stock['billable_qty'] ?></td>
                                    <td class="text-right minqty"><?= $stock['qty'] - $stock['current_stock_qty'] ?></td>
                                    <td class="text-right <?= $stock['held_stock'] > 0 ? 'bg-rosepink text-white' : '' ?> held_stock"><?= $stock['held_stock'] ?></td>
                                    <td class="text-right vat_rate"><?= $stock['vat_rate'] ?></td>
                                    <td class="text-right total_cost">
                                        <span><?= formatN($stock['total_cost']) ?></span>
                                        <input type="hidden" class="vat_amount">
                                    </td>
                                    <td>
                                        <? if ($stock['trackserialno'] == 1) { ?>
                                            <button type="button" class="btn btn-default btn-xs text-primary"
                                                    data-target="#edit-serialno-modal<?= $stock['stockid'] ?>"
                                                    data-toggle="modal"
                                                    data-stockid="<?= $stock['stockid'] ?>"
                                                    data-qty="<?= $stock['qty'] ?>"
                                                    data-stockqty="<?= $stock['current_stock_qty'] ?>">
                                                <i class="fa fa-barcode"></i> Serial no
                                            </button>
                                            <div id="edit-serialno-modal<?= $stock['stockid'] ?>"
                                                 class="modal fade serialno-modal"
                                                 tabindex="-1" role="dialog"
                                                 aria-labelledby="edit-serialno-modal<?= $stock['stockid'] ?>"
                                                 aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="d-flex justify-content-between">
                                                                <h4 class="modal-title"><?= $stock['productname'] ?></h4>
                                                                <button type="button" class="btn btn-default btn-sm"
                                                                        onclick="addSerialNo(this)">
                                                                    <i class="fa fa-plus"></i> Add
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <form action="<?= url('grns', 'grn_save_serialno') ?>"
                                                              method="post"
                                                              onsubmit="return confirmSerialNo(this)">
                                                            <input type="hidden" name="current_stock_id"
                                                                   value="<?= $stock['stockid'] ?>">
                                                            <input type="hidden" name="gdi"
                                                                   value="<?= $stock['gdi'] ?>">
                                                            <div class="modal-body"
                                                                 style="max-height: 60vh;overflow-y: auto;">
                                                                <table class="serial-table table table-condensed table-bordered"
                                                                       style="font-size: 10pt;">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Serial No</th>
                                                                        <th></th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <? if (empty($stock['serialnos'])) {
                                                                        for ($i = 0; $i < $stock['qty']; $i++) { ?>
                                                                            <!--                                                                            <tr>-->
                                                                            <!--                                                                                <td>--><?//= $i + 1 ?><!--</td>-->
                                                                            <!--                                                                                <td>-->
                                                                            <!--                                                                                    <input type="text" name="number[]"-->
                                                                            <!--                                                                                           class="form-control serial_number">-->
                                                                            <!--                                                                                    <input type="hidden" name="id[]">-->
                                                                            <!--                                                                                    <input type="hidden" name="state[]"-->
                                                                            <!--                                                                                           value="new">-->
                                                                            <!--                                                                                </td>-->
                                                                            <!--                                                                                <td></td>-->
                                                                            <!--                                                                            </tr>-->
                                                                        <? }
                                                                    } else {
                                                                        $count = 1;
                                                                        foreach ($stock['serialnos'] as $si => $serialno) { ?>
                                                                            <tr class="<?= $serialno['used'] ? 'used' : '' ?>">
                                                                                <td><?= $count ?></td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                           name="number[]"
                                                                                           class="form-control serial_number"
                                                                                           value="<?= $serialno['number'] ?>"
                                                                                           readonly <?= $serialno['used'] ? 'disabled' : '' ?>>
                                                                                    <input type="hidden" name="id[]"
                                                                                           value="<?= $serialno['id'] ?>"
                                                                                        <?= $serialno['used'] ? 'disabled' : '' ?>>
                                                                                    <input type="hidden" name="state[]"
                                                                                        <?= $serialno['used'] ? 'disabled' : '' ?>>
                                                                                </td>
                                                                                <td class="status"
                                                                                    style="vertical-align: middle;text-align: center;">
                                                                                    <? if (!$serialno['used']) { ?>
                                                                                        <button type="button"
                                                                                                class="btn btn-sm btn-default text-primary"
                                                                                                title="edit"
                                                                                                onclick="enableEditing(this)">
                                                                                            <i class="fa fa-pencil"></i>
                                                                                        </button>
                                                                                        <button type="button"
                                                                                                class="btn btn-sm btn-default text-danger"
                                                                                                title="delete"
                                                                                                onclick="deleteSerialNo(this)">
                                                                                            <i class="fa fa-trash"></i>
                                                                                        </button>
                                                                                        <?
                                                                                    } else {
                                                                                        ?>
                                                                                        <span class="text-success">Used</span>
                                                                                    <? } ?>
                                                                                </td>
                                                                            </tr>
                                                                            <? $count++;
                                                                        }
                                                                    } ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default btn-sm"
                                                                        data-dismiss="modal">Close
                                                                </button>
                                                                <button
                                                                        class="btn btn-success btn-sm confirmBtn"
                                                                >Confirm
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <? } ?>
                                        <button type="button" class="btn btn-default btn-xs text-danger"
                                                data-target="#edit-product-modal" data-toggle="modal"
                                                data-stockid="<?= $stock['stockid'] ?>"
                                                data-trackexpire="<?= $stock['track_expire_date'] ?>">
                                            <i class="fa fa-pencil"></i> Edit
                                        </button>
                                    </td>
                                    <td rowspan="2" style="vertical-align : middle;text-align:center;">
                                        <button type="button" class="btn btn-success updateBtn" disabled
                                                data-stockid="<?= $stock['stockid'] ?>"
                                                onclick="updateGRN(this)">
                                            <span class="d-flex justify-content-center align-items-center">
                                                <object class="saveLoadingSpinner" data="images/loading_spinner.svg"
                                                        type="image/svg+xml" height="20" width="20"
                                                        style="display: none"></object>
                                                <span>Update</span>
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                                <? if ($stock['track_expire_date'] == 1) { ?>
                                    <tr class="batches<?= $stock['stockid'] ?>" data-stockid="<?= $stock['stockid'] ?>">
                                        <td colspan="10">
                                            <div class="col-md-6 col-md-offset-6 mt-md">
                                                <table class="table table-bordered" style="font-size: 9pt;">
                                                    <thead>
                                                    <tr>
                                                        <th>Batch No</th>
                                                        <th>GRN Qty</th>
                                                        <th>Used Qty</th>
                                                        <th>Expire date</th>
                                                        <th></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($stock['batches'] as $b => $batch) { ?>
                                                        <tr class="batch<?= $batch['batchId'] ?>"
                                                            data-batchid="<?= $batch['batchId'] ?>">
                                                            <td class="batch_no"><?= $batch['batch_no'] ?></td>
                                                            <td class="batch_qty"><?= $batch['batchqty'] ?></td>
                                                            <td class="minqty"><?= $batch['batchqty'] - $batch['current_stock'] ?></td>
                                                            <td class="expire_date"><?= $batch['expire_date'] ?></td>
                                                            <td class="text-right">
                                                                <button type="button"
                                                                        class="btn btn-default btn-xs text-primary"
                                                                        data-target="#edit-batch-modal"
                                                                        data-toggle="modal"
                                                                        data-stockid="<?= $stock['stockid'] ?>"
                                                                        data-batchid="<?= $batch['batchId'] ?>">
                                                                    <i class="fa fa-pencil"></i> Edit
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <? } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                <? } ?>
                                <tr>
                                    <td colspan="9"></td>
                                </tr>
                                <? $count++;
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>
<div class="modal fade" id="edit-product-modal" tabindex="-1" role="dialog" aria-labelledby="edit-product-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Product name</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="inputs stockid">
                <input type="hidden" class="inputs track_expire">
                <div class="form-group">
                    <div class="col-md-3">
                        <label>Current Rate</label>
                        <input type="text" readonly class="inputs form-control current_rate">
                    </div>
                    <div class="col-md-3">
                        <label>New Rate</label>
                        <input type="number" class="inputs form-control new_rate">
                    </div>
                    <div class="col-md-3">
                        <label>Qty min: <span class="min_qty text-danger"></span>, held: <span class="held_qty text-danger"></span> </label>
                        <input type="number" class="inputs form-control grn_qty">
                    </div>
                    <div class="col-md-3">
                        <label>Billable Qty</label>
                        <input type="number" class="inputs form-control billable_qty">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" onclick="updateProduct()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-batch-modal" tabindex="-1" role="dialog" aria-labelledby="edit-batch-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Batch</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="inputs stockid">
                <input type="hidden" class="inputs batchid">
                <div class="form-group">
                    <div class="col-md-4">
                        <label>Batch No</label>
                        <input type="text" class="inputs form-control batch_no">
                    </div>
                    <div class="col-md-4">
                        <label>Batch Qty min: <span class="minqty text-danger"></span></label>
                        <input type="number" class="inputs form-control batch_qty">
                    </div>
                    <div class="col-md-4">
                        <label>Expire Date</label>
                        <input type="date" class="inputs form-control expire_date">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" onclick="updateBatch()">Confirm</button>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>


    let productModal;
    let batchModal;
    let historyHolder = {};
    $(function () {
        <? if ($grnPayment['paid_amount'] == 0) { ?>
        initSelectAjax('#supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);
        <? } ?>
        enableVatDesc('#vat_registered');

        productModal = $('#edit-product-modal');
        batchModal = $('#edit-batch-modal');
        $(productModal).on('show.bs.modal', function (e) {


            let source = $(e.relatedTarget);
            let modal = $(this);
            let parentRow = $(source).closest('tr');
            let held_qty = parseInt($(parentRow).find('.held_stock').text()) || 0;
            if (!checkPendingUpdate(parentRow)) return false;
            if (held_qty > 0) {
                triggerWarning("Some of this product stock is on held\n Editing might effect onHeld Stock.\nCancel on Held then Edit GRN ", 10000);
            }
            //clear
            $(modal).find('.inputs').val('');

            $(modal).find('.modal-title').text($(parentRow).find('.productname').val());
            $(modal).find('.stockid').val(source.data('stockid'));
            $(modal).find('.track_expire').val(source.data('trackexpire'));
            $(modal).find('.current_rate').val($(parentRow).find('.rate').text());
            $(modal).find('.new_rate').val($(parentRow).find('.rate').text());
            $(modal).find('.grn_qty').val($(parentRow).find('.qty').text());
            $(modal).find('.grn_qty').attr('min', $(parentRow).find('.minqty').text());
            $(modal).find('.min_qty').text($(parentRow).find('.minqty').text());
            $(modal).find('.billable_qty').val($(parentRow).find('.billable_qty').text());
            $(modal).find('.held_qty').text(held_qty);
            // console.log(source.data('trackexpire'));
            if (source.data('trackexpire') === 1) {
                $(modal).find('.grn_qty').prop('readonly', true);
            } else {
                $(modal).find('.grn_qty').prop('readonly', false);
            }
        });

        $(batchModal).on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            let parentBatchRow = $(source).closest('tr');
            let parentProductRow = $(`.prod${source.data('stockid')}`);
            let held_qty = parseInt($(parentProductRow).find('.held_stock').text()) || 0;
            // console.log(parentProductRow);
            if (!checkPendingUpdate(parentProductRow)) return false;
            if (held_qty > 0) {
                triggerWarning("Some of this product stock is on held\n Editing might effect onHeld Stock.\nCancel on Held then Edit GRN ", 10000);
            }
            //clear
            $(modal).find('.inputs').val('');

            $(modal).find('.stockid').val(source.data('stockid'));
            $(modal).find('.batchid').val(source.data('batchid'));
            $(modal).find('.batch_no').val($(parentBatchRow).find('.batch_no').text());
            $(modal).find('.batch_qty').val($(parentBatchRow).find('.batch_qty').text());
            $(modal).find('.batch_qty').attr('min', $(parentBatchRow).find('.minqty').text());
            $(modal).find('.minqty').text($(parentBatchRow).find('.minqty').text());
            $(modal).find('.expire_date').val($(parentBatchRow).find('.expire_date').text());
        });
    });

    function checkPendingUpdate(parentProductRow) {
        //block if there is pending update in other buttons
        let otherUpdateButtons = $('.updateBtn:not(:disabled)').not($(parentProductRow).find('.updateBtn'));
        // console.log('Other buttons: ' + otherUpdateButtons.length);
        if (otherUpdateButtons.length > 0) {
            triggerError('Update first before editing another product', 4000);
            otherUpdateButtons.focus();
            return false;
        }
        return true;
    }

    function addSerialNo(obj) {
        let requiredQty = parseInt($(obj).closest('.products').find('.qty').text());
        let parentModal = $(obj).closest('.modal');
        let availableSerialQty = $(parentModal).find(`table.serial-table tbody tr input[name='state[]'][value!='delete']`).length; //find all serial which are not deleted

        if (requiredQty > availableSerialQty) {
            let row = `<tr>
                           <td>${availableSerialQty + 1}</td>
                           <td>
                               <input type="text" name="number[]"
                                      class="form-control serial_number">
                                      <input type="hidden" name="id[]">
                                      <input type="hidden" name="state[]" value="new">
                           </td>
                           <td></td>
                       </tr>`;
            $(parentModal).find('table.serial-table tbody').append(row);
        } else {
            triggerError(`You can't add more than available quantity (${requiredQty})`);
        }
    }

    function enableEditing(obj) {
        let parentSerialRow = $(obj).closest('tr');
        $(parentSerialRow).find('.serial_number').prop('readonly', false).focus();
    }

    function deleteSerialNo(obj) {
        let parentSerialRow = $(obj).closest('tr');
        $(parentSerialRow).find(`input[name="state[]"]`).val('delete');
        $(parentSerialRow).find('td.status').empty().append(`<span class="text-danger">delete</span>`);
    }

    function confirmSerialNo(obj) {
        let parentModal = $(obj).closest('.modal');
        let requiredSerialQty = parseInt($(obj).closest('.products').find('.qty').text());
        let valid = true;

        if (requiredSerialQty < $(parentModal).find(`input[name='state[]'][value!='delete']`).length) {
            triggerError(`Product qty (${requiredSerialQty}) dont match with entered serial qty!`, 5000);
            return false;
        }

        //check if empty
        $(parentModal).find('.serial_number').each(function () {
            let value = $.trim($(this).val());
            if (value == '' || value == null) {
                triggerError('Serial no is required', 2000);
                $(this).focus();
                valid = false;
                return false;
            }
        });

        if (!valid) return false;

        //check duplicate
        let serialNos = [];
        $(parentModal).find('.serial_number').each(function () {
            serialNos.push($(this).val());
        });

        let sorted = serialNos.slice().sort();

        let duplicates = [];
        for (let i = 0; i < sorted.length - 1; i++) {
            if (sorted[i] === sorted[i + 1]) {
                duplicates.push(sorted[i]);
            }
        }

        $(parentModal).find('.serial_number').each(function () {
            if ($.inArray($(this).val(), duplicates) !== -1) {
                $(this).addClass('input-error');
            } else {
                $(this).removeClass('input-error');
            }
        });

        if (duplicates.length > 0) {
            triggerError('Duplicate serial no found!');
            return false;
        }
    }

    function enableVatDesc(obj) {
        if ($(obj).is(':checked')) {
            $('#vat_desc').prop('disabled', false).prop('required', true);
        } else {
            $('#vat_desc').prop('disabled', true).val('');
        }
    }

    function updateProduct() {
        //validate new cost
        let stockid = $(productModal).find('.stockid').val();
        let new_rate = parseFloat($(productModal).find('.new_rate').val());
        let grn_qty = parseInt($(productModal).find('.grn_qty').val());
        let grn_min_qty = parseInt($(productModal).find('.grn_qty').attr('min'));
        let billable_qty = parseInt($(productModal).find('.billable_qty').val());
        if (isNaN(new_rate)) {
            triggerError('Enter new rate first');
            $(productModal).find('.new_rate').focus();
            return;
        }

        if (isNaN(grn_qty) || grn_qty < grn_min_qty) {
            triggerError(`Required min qty is ${grn_min_qty}`);
            $(productModal).find('.grn_qty').focus();
            return;
        }

        if (isNaN(billable_qty) || billable_qty < 0 || billable_qty > grn_qty) {
            triggerError(`Enter valid billable qty`);
            $(productModal).find('.billable_qty').focus();
            return;
        }

        //update product info
        let parent = $(`.prod${stockid}`);

        //make copy for restore
        makeCopy(stockid, $(parent).clone(), $(`.batches${stockid}`).clone());

        $(parent).find('.rate').text(new_rate);
        $(parent).find('.qty').text(grn_qty);
        $(parent).find('.billable_qty').text(billable_qty);

        //hide modal
        $(productModal).modal('hide');
        // console.log('stockid', stockid, 'rate', new_rate, 'vat', vat_id, 'percent', vat_percent, 'grnqty', grn_qty, 'min', grn_min_qty);
        calProductAmount(stockid);
    }

    function calProductAmount(stockid) {
        let productRow = $(`.prod${stockid}`);
        let batchesRow = $(`.batches${stockid}`);
        // console.log('prod', productRow, 'batches', batchesRow);
        let billableQty = parseInt($(productRow).find('.billable_qty').text());

        let rate = parseFloat($(productRow).find('.rate').text());
        let vat_rate = parseFloat($(productRow).find('.vat_rate').text());
        let excAmount = (rate * billableQty);
        let vatAmount = excAmount * (vat_rate / 100);
        let total_cost = excAmount + vatAmount;
        // console.log('qty', billableQty, 'rate', rate, vat_rate, vat_rate, 'total', total_cost);

        $(productRow).find('.total_cost span').text(numberWithCommas(total_cost.toFixed(2)));
        $(productRow).find('.total_cost .vat_amount').val(vatAmount.toFixed(2));


        //enable update btn
        $(productRow).find('.updateBtn').prop('disabled', false);

        calGrnTotalAmount();
    }

    function updateBatch() {
        let stockid = $(batchModal).find('.stockid').val();
        let batchid = $(batchModal).find('.batchid').val();

        let batch_no = $(batchModal).find('.batch_no').val();
        let batch_qty = parseFloat($(batchModal).find('.batch_qty').val());
        let min_qty = parseFloat($(batchModal).find('.batch_qty').attr('min'));
        let expire_date = $(batchModal).find('.expire_date').val();

        //validating
        if ($.trim(batch_no).length === 0) {
            triggerError(`Enter batch no`);
            $(batchModal).find('.batch_no').focus();
            return;
        }
        if (isNaN(batch_qty) || batch_qty < min_qty) {
            triggerError(`Required min qty is ${min_qty}`);
            $(batchModal).find('.batch_qty').focus();
            return;
        }
        if (expire_date == null || expire_date === '') {
            triggerError(`Enter expire date`);
            $(batchModal).find('.expire_date').focus();
            return;
        }


        //update product info
        let parentProduct = $(`.prod${stockid}`);
        let batchesRow = $(`.batches${stockid}`);
        let parentBatch = $(`.batch${batchid}`);

        //make copy for restore
        makeCopy(stockid, $(parentProduct).clone(), $(batchesRow).clone());

        $(parentBatch).find('.batch_no').text(batch_no);
        $(parentBatch).find('.batch_qty').text(batch_qty);
        $(parentBatch).find('.expire_date').text(expire_date);

        //update product total
        let totalQty = 0; //total grn qty
        $(batchesRow).find('.batch_qty').each(function () {
            totalQty += parseFloat($(this).text());
        });

        let billable_qty = parseInt($(parentProduct).find('.billable_qty').text());
        if (billable_qty > totalQty) {
            billable_qty = totalQty;
            $(parentProduct).find('.billable_qty').text(billable_qty);
        }
        $(parentProduct).find('.qty').text(totalQty);
        calProductAmount(stockid);
        $(batchModal).modal('hide');
    }

    function calGrnTotalAmount() {
        let excAmount = 0, vatAmount = 0, fullAmount = 0, paidAmount = 0, outstandingAmount = 0;
        let adjustmentAmount = parseFloat($('.adjustment_amount').val()) || 0;
        $('tr.products').each(function () {
            let rate = parseFloat($(this).find('.rate').text());
            let billable_qty = parseInt($(this).find('.billable_qty').text());
            let vat_rate = parseFloat($(this).find('.vat_rate').text());
            excAmount += (rate * billable_qty);
            vatAmount += (rate * billable_qty * (vat_rate / 100));
        });
        fullAmount = excAmount + vatAmount + adjustmentAmount;

        <?if($grn['paymenttype'] == PAYMENT_TYPE_CREDIT){?>
        paidAmount = parseFloat($('#paid_amount').val()) || 0;
        if (fullAmount < paidAmount) {
            $('.updateBtn').prop('disabled', true);
            triggerError(`Grn Total Amount cant be below amount paid to supplier!`, 4000);
            restoreHistory();
            return;
        }
        <?}else{?> //cash grn paid also change
        paidAmount = fullAmount;
        $('#paid_amount').val(paidAmount.toFixed(2));
        $('#paid_amount_label').val(numberWithCommas(paidAmount.toFixed(2)));
        <?}?>
        outstandingAmount = fullAmount - paidAmount;
        $('#outstanding_amount').val(outstandingAmount.toFixed(2));
        $('#outstanding_amount_label').val(numberWithCommas(outstandingAmount.toFixed(2)));
        $('.total_exc_amount').val(numberWithCommas(excAmount.toFixed(2)));
        $('.total_vat_amount').val(numberWithCommas(vatAmount.toFixed(2)));
        $('.total_inc_amount').val(numberWithCommas((fullAmount).toFixed(2)));
    }

    function makeCopy(stockid, productRow, batchesRow) {
        historyHolder.stockid = stockid;
        historyHolder.productRow = productRow;
        historyHolder.batchesRow = batchesRow;
    }

    function restoreHistory() {
        if (historyHolder.stockid) {
            $(`.prod${historyHolder.stockid}`).replaceWith(historyHolder.productRow);
            $(`.batches${historyHolder.stockid}`).replaceWith(historyHolder.batchesRow);
            setTimeout(function () {
                triggerMessage('Product restored to previous state', 4000);
            }, 4000);
        }
        historyHolder.empty();
    }

    function updateGRN(obj) {
        $(obj).prop('disabled', true); //disable update btn
        let spinner = $(obj).find('.saveLoadingSpinner');
        spinner.show('fast');
        let stockid = $(obj).data('stockid');
        let parentProductRow = $(`.prod${stockid}`);
        let parentBatchesRow = $(`.batches${stockid}`);
        let batches = [];
        if (parentBatchesRow.length > 0) {
            $(parentBatchesRow).find('tbody tr').each(function () {
                batches.push({
                    id: $(this).data('batchid'),
                    batch_no: $(this).find('.batch_no').text(),
                    qty: $(this).find('.batch_qty').text(),
                    expire_date: $(this).find('.expire_date').text(),
                    gdi: $(parentProductRow).find('.gdi').val()
                });
            });
        }

        let grnData = {
            grn: {
                id: $('.grnid').val(),
                supplierid: $('#supplierid').val(),
                vat_registered: $('#vat_registered').is(':checked') ? 1 : 0,
                vat_desc: $('#vat_desc').val(),
                invoiceno: $('#invoiceno').val(),
                verificationcode: $('#verificationcode').val(),
                currency_rateid: $('#currencyid').val(),
                total_amount: $('.total_exc_amount').val(),
                grand_vatamount: $('.total_vat_amount').val(),
                full_amount: $('.total_inc_amount').val(),
                adjustment_amount: $('.adjustment_amount').val()
            },
            detail: {
                id: $(parentProductRow).find('.gdi').val(),
                grnid: $('.grnid').val(),
                stockid: stockid,
                track_expire_date: $(parentProductRow).find('.track_expire_date').val(),
                trackserialno: $(parentProductRow).find('.trackserialno').val(),
                rate: $(parentProductRow).find('.rate').text(),
                qty: $(parentProductRow).find('.qty').text(),
                billable_qty: $(parentProductRow).find('.billable_qty').text(),
                vat_rate: $(parentProductRow).find('.vat_rate').text(),
                vat_amount: $(parentProductRow).find('.vat_amount').val(),
                batches: batches
            }
        };
        grnData = JSON.stringify(grnData);
        // console.log(grnData);

        //AJAX QUERY TO STORE UPDATES
        $.post('?module=grns&action=updateGRN&format=json', {
            data: grnData
        }, function (data) {
            let result = JSON.parse(data);
            // console.log(result);
            spinner.hide('fast');
            if (result.status === "success") {
                triggerMessage(result.msg, 3000);
            } else {
                triggerError(result.msg, 3000);
            }
        });
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
