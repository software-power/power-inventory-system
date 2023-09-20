<div class="group container-fluid <?= $detail['current_stock']<=0 ? 'out-of-stock' : '' ?>"
     title="<?= $detail['current_stock']<=0 ? 'out of stock' : '' ?>">
    <button type="button" class="btn btn-danger" style="position: absolute;top:10px;right: 10px;"
            onclick="removeRow(this);"><i class="fa fa-remove"></i>
    </button>
    <div class="row">
        <div class="col-md-3" style="position: relative;">
            <object class="search-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="30"
                    width="30" style="position: absolute;top:0;right: 10px;display: none;"></object>
            <input type="hidden" class="inputs producttype" name="stockid[]" value="<?= $detail['stockid'] ?>">
            <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                   onclick="open_modal(this,'.group',getProductDetails)" value="<?= $detail['productname'] ?>"
                <?= $detail['current_stock']<=0 ? 'disabled' : '' ?>>
            <input type="hidden" class="inputs productid" value="<?= $detail['productid'] ?>">
        </div>
        <div class="col-md-3">
            <input type="text" data-input='qty' placeholder="stock qty" readonly
                   class="form-control inputs input-block stockQty" value="<?= $detail['current_stock'] ?>">
        </div>
        <div class="col-md-3" style="position: relative;">
            <small class="text-primary" style="position: absolute;top:-22px;left: 5px;font-style: italic">
                requested qty <?= $detail['qty'] ?>
            </small>
            <input type="number" oninput="maxmumSelection(this)" data-input='qty' placeholder="qty"
                   autocomplete="off" class="form-control inputs input-block forqty" min="1" required
                   name="quantity[]" value="<?= $detail['qty'] ?>"
                   max="<?= $detail['current_stock'] ?>" <?= $detail['track_expire_date'] ? 'readonly' : '' ?>>
        </div>
        <div class="col-md-3 serialno-holder">
            <? if ($detail['trackserialno']) { ?>
                <button type="button" class="btn btn-default serialBtn" title="Serial numbers"
                        data-toggle="modal" data-target="#serialModal<?= $detail['stockid'] ?>">
                    <i class="fa fa-barcode"></i> Serial no
                </button>
                <div class="modal fade serial-modal" id="serialModal<?= $detail['stockid'] ?>" tabindex="-1" role="dialog"
                     aria-labelledby="serialModal<?= $detail['stockid'] ?>"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-center">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title productName">Serial No</h4>
                            </div>
                            <div class="modal-body">
                                <h5 class="text-primary"><?= $detail['productname'] ?></h5>
                                <div class="d-flex justify-content-between mb-sm">
                                    <div>
                                        <button type="button" class="btn btn-default btn-sm ml-xs"
                                                onclick="$(this).closest('.modal').find('.file-input').trigger('click')"><i
                                                    class="fa fa-file"></i> Add from file
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm ml-xs" onclick="clearItems(this)"><i
                                                    class="fa fa-trash"></i> Clear
                                        </button>
                                        <input type="file" class="file-input" style="display: none;" accept="*.txt" onchange="getSerialNos(this)">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <object class="validate_spinner" data="images/loading_spinner.svg"
                                                type="image/svg+xml" height="30" width="30" style="display: none"></object>
                                        <button type="button" class="btn btn-primary btn-sm validate-btn" onclick="validateAllSerialNo(this)">
                                            Validate all
                                        </button>
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
                </div>
            <? } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 p-none">
            <textarea rows="2" class="form-control inputs product_description" readonly><?= $detail['product_description'] ?></textarea>
        </div>
        <div class="col-md-6 p-none">
            <div class="batch-holder">
                <? if ($detail['track_expire_date']) { ?>
                    <table class="table table-hover table-bordered batch-table" style="font-size:10pt">
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
                        <tbody>
                        <? foreach ($detail['batches'] as $b) { ?>
                            <tr>
                                <td>
                                    <input type="hidden"
                                           name="batch[<?= $detail['stockid'] ?>][batchId][]"
                                           value="<?= $b['batchId'] ?>">
                                    <input type="text" readonly placeholder="batch no."
                                           class="form-control input-sm input-block batch_no"
                                           value="<?= $b['batch_no'] ?>">
                                </td>
                                <td>
                                    <input type="number" readonly placeholder="qty in"
                                           class="form-control input-sm input-block batch_qty_in"
                                           value="<?= $b['total'] ?>">
                                </td>
                                <td>
                                    <input type="text" readonly placeholder="qty in"
                                           class="form-control input-sm input-block batch_qty_in"
                                           value="<?= $b['expire_date'] ?>">
                                </td>
                                <td>
                                    <input type="number" placeholder="qty out"
                                           name="batch[<?= $detail['stockid'] ?>][qty_out][]"
                                           min="1" max="<?= $b['total'] ?>" required
                                           class="form-control input-sm input-block batch_qty_out"
                                           oninput="updateQty(this)">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="removeBatch(this)">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                <? } ?>
            </div>
        </div>
    </div>
</div>