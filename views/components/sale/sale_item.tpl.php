<?
$previous_qty = $detail['quantity'] ?: $detail['qty'];
if ($detail['non_stock']) {
    $group_class = "non-stock";
    $qty = $previous_qty;
} elseif ($detail['stock_qty'] <= 0) {
    $group_class = "out-of-stock";
    if ($detail['source'] == 'external') $group_class .= " external-product";
} elseif ($previous_qty > $detail['stock_qty']) {
    $qty = $detail['stock_qty'];
    $class = 'max-input-warning';
} else {
    $qty = $previous_qty;
} ?>

<div class="group <?= $group_class ?>">
    <small class="text-danger non-stock-label" style="display: none">non-stock</small>
    <button type="button" class="btn btn-danger"
            style="position: absolute;top:10px;right: 10px;"
            onclick="removeRow(this);">
        <i class="fa fa-remove"></i>
    </button>
    <div class="row grn-details">
        <div class="col-md-3" style="position: relative">
            <input type="text" readonly required class="form-control inputs productname"
                   onclick="open_modal(this,'.group')" placeholder="search product" value="<?= $detail['productname'] ?>">
            <input type="hidden" class="inputs productid" name="productid[]" value="<?= $detail['productid'] ?>">
            <input type="hidden" class="inputs stockid" name="stockid[]" value="<?= $detail['non_stock'] ? '' : $detail['stockid'] ?>">
            <input type="hidden" class="inputs validate_serialno" value="<?= $detail['validate_serialno'] ?>">
            <input type="hidden" class="track_expire_date" value="<?= $detail['track_expire_date'] ?>">
            <object class="search-spinner" data="images/loading_spinner.svg"
                    type="image/svg+xml"
                    height="30" width="30"
                    style="position: absolute;top:0;right: 10px;display: none;"></object>
        </div>
        <div class="col-md-1" style="position: relative">
            <input type="text" oninput="calProductAmount(this)" autocomplete="off"
                   data-source="qty" placeholder="qty" <?= $detail['track_expire_date'] ? 'readonly' : '' ?>
                   class="form-control <?= $class ?> inputs qty" name="qty[]" required max="<?= $detail['non_stock'] ? '' : $detail['stock_qty'] ?>"
                   value="<?= $qty ?>" title="available stock <?= $detail['stock_qty'] ?>">
            <? if ($detail['sale_item']) { ?>
                <i class="text-success text-sm">previous qty <?= $previous_qty ?></i>
            <? } else { ?>
                <i class="text-success text-sm">Order qty <?= $previous_qty ?></i>
            <? } ?>
            <input type="hidden" class="unitname">
        </div>
        <div class="col-md-1" style="position: relative">
            <div class="price-range text-left">
                <input type="hidden" class="base_min_price" value="<?= $detail['base_min_price'] ?>">
                <input type="hidden" class="base_suggested_price" value="<?= $detail['base_suggested_price'] ?>">
                <div>Min: <span class="min_price"><?= $detail['min_price'] ?></span></div>
                <div>Suggested: <span class="suggested_price"><?= $detail['suggested_price'] ?></span></div>
            </div>
            <object class="price-spinner" data="images/loading_spinner.svg"
                    type="image/svg+xml"
                    height="25" width="25"
                    style="position: absolute;top:0;right: 10px;display: none;"></object>
            <input autocomplete="off" type="text" oninput="calProductAmount(this)" name="price[]" required data-source="price" placeholder="Price"
                   class="form-control inputs price" step="0.01" title="exclusive price" min="<?=$detail['min_price']?>"
                   value="<?= $detail['price'] ?>">
            <input class="hidden_cost" type="hidden" name="hidden_cost[]" value="<?= $detail['costprice'] ?>"/>
            <input class="base_price" type="hidden" value="<?= $detail['base_price'] ?>"/>
            <input class="base_incprice" type="hidden" value="<?= $detail['base_incprice'] ?>"/>
            <input class="base_hidden_cost" type="hidden" value="<?= $detail['base_hidden_cost'] ?>"/>
        </div>
        <div class="col-md-1">
            <input type="text" readonly placeholder="VAT"
                   class="form-control text-center inputs vat_rate"
                   name="vat_rate[]" value="<?= $detail['vat_rate'] ?>">
            <input type="hidden" class="og_vat_rate" value="<?= $detail['vat_rate'] ?>">
        </div>
        <div class="col-md-1" style="position: relative">
            <div class="incprice-range text-left">
                <input type="hidden" class="base_min_incprice" value="<?= $detail['base_min_incprice'] ?>">
                <input type="hidden" class="base_suggested_incprice" value="<?= $detail['base_suggested_incprice'] ?>">
                <div>Min: <span class="min_incprice"><?= $detail['min_incprice'] ?></span></div>
                <div>Suggested: <span class="suggested_incprice"><?= $detail['suggested_incprice'] ?></span></div>
            </div>
            <object class="incprice-spinner" data="images/loading_spinner.svg"
                    type="image/svg+xml"
                    height="25" width="25"
                    style="position: absolute;top:0;right: 10px;display: none;"></object>
            <input autocomplete="off" type="text" oninput="calProductAmount(this)" data-source="incprice" name="incprice[]" required
                   placeholder="inc Price" class="form-control inputs incprice" title="inclusive price" min="<?=$detail['min_incprice']?>"
                   value="<?= $detail['incprice'] ?>">
            <input type="hidden" name="sinc[]" class="sinc" value="<?= $detail['sinc'] ?>">
        </div>
        <div class="col-md-2">
            <input readonly type="text" placeholder="Exclusive Amount"
                   class="form-control text-center inputs excamountLabel">
        </div>
        <div class="col-md-2">
            <input readonly type="text" placeholder="Inc Amount"
                   class="form-control text-center inputs incamountLabel">
            <input type="hidden" class="inputs excamount">
            <input type="hidden" class="inputs vatamount">
            <input type="hidden" class="inputs incamount">
        </div>
        <div class="col-md-12 mt-sm">
            <div class="row">
                <div class="col-md-6">
                    <textarea  <?= $detail['print_extra'] ? '' : 'readonly' ?> placeholder="Description....." name="product_description[]"
                                                                               class="form-control product_description"
                                                                               rows="2"><?= $detail['productdescription'] ?: $detail['description'] ?></textarea>
                </div>
                <div class="col-md-6 d-flex justify-content-center descriptionButtons">
                    <input type="hidden" name="show_print[]" class="show_print" value="1">
                    <input type="hidden" class="combined">
                    <label class="d-flex align-items-center mr-sm">
                        <input type="checkbox" name="print_extra[]" class="print_extra" <?= $detail['print_extra'] ? 'checked' : '' ?>
                               value="<?= $detail['productid'] ?>" onchange="enableDescription(this)">
                        <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
                    </label>
                    <button type="button" class="btn btn-default mr-sm btn-sm combineDescriptionBtn"
                            title="Combine description" onclick="combine_with(this)">
                        <i class="fa fa-compress"></i> Combine with
                    </button>
                    <button type="button" title="Product view" class="btn btn-default viewProductBtn mr-sm btn-sm" data-toggle="modal"
                            data-target="#product-view-modal" data-productid="<?= $detail['productid'] ?>">
                        <i class="fa fa-eye"></i> View
                    </button>
                    <div class="serialno-holder">
                        <? if ($detail['trackserialno']) { ?>
                            <button type="button" class="btn btn-default serialBtn" title="Serial numbers" data-toggle="modal"
                                    data-target="#serialModal<?= $detail['stockid'] ?>">
                                <i class="fa fa-barcode"></i> Serial no
                            </button>
                            <div class="modal fade serial-modal" id="serialModal<?= $detail['stockid'] ?>" tabindex="-1" role="dialog"
                                 aria-labelledby="serialModal<?= $detail['stockid'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-center">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                        aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title productName">
                                                Serial No: <span class="text-primary"><?= $detail['productname'] ?></span>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-danger"><?= $detail['validate_serialno'] ? 'Validates from Stock' : 'Enter manually' ?></p>
                                            <table class="table table-bordered" style="font-size: 10pt;">
                                                <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Serial Number</td>
                                                    <td>Status</td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? if ($detail['track_expire_date'] || $detail['prescription_required']) { ?>
        <div class="row pt-xs batches">
            <div class="col-md-6 prescription_container">
                <? if ($detail['prescription_required']) { ?>
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <label>Prescription Doctor <span
                                        class="text-danger">*</span></label>
                            <button type="button"
                                    onclick="quickAddDoctor(this)"
                                    title="Quick add doctor"
                                    class="btn btn-primary btn-xs ml-lg">+
                                Add
                            </button>
                            <div class="col-md-12 new_doctor mb-md"
                                 style="padding:0;display: none;">
                                <div class="col-md-10"
                                     style="padding:0;position: relative">
                                    <div class="loading_spinner"
                                         style="position: absolute;top:5px;right: 10px;display: none;">
                                        <object data="images/loading_spinner.svg"
                                                type="image/svg+xml"
                                                height="30"
                                                width="30"></object>
                                    </div>
                                    <input type="text"
                                           class="form-control doctor"
                                           placeholder="Doctor's name">
                                </div>
                                <div class="col-md-1">
                                    <button type="button"
                                            onclick="saveDoctor(this)"
                                            title="Quick add doctor"
                                            class="btn btn-success btn-xs ml-sm">
                                        Save
                                    </button>
                                </div>
                            </div>
                            <select title="Choose doctor"
                                    class="form-control prescription_doctor"
                                    name="prescription[<?= $detail['stockid'] ?>][doctor]"
                                    required>
                            </select>
                        </div>
                        <div class="col-md-6 col-md-offset-1">
                            <label>Prescription Hospital <span
                                        class="text-danger">*</span></label>
                            <button type="button"
                                    onclick="quickAddHospital(this)"
                                    title="Quick add Hospital"
                                    class="btn btn-primary btn-xs ml-lg">+
                                Add
                            </button>
                            <div class="col-md-12 new_hospital mb-md"
                                 style="padding:0;display: none;">
                                <div class="col-md-10"
                                     style="padding:0;position: relative">
                                    <div class="loading_spinner"
                                         style="position: absolute;top:5px;right: 10px;display: none;">
                                        <object data="images/loading_spinner.svg"
                                                type="image/svg+xml"
                                                height="30"
                                                width="30"></object>
                                    </div>
                                    <input type="text"
                                           class="form-control hospital"
                                           placeholder="hospital's name">
                                </div>
                                <div class="col-md-1">
                                    <button type="button"
                                            onclick="saveHospital(this)"
                                            title="Quick add doctor"
                                            class="btn btn-success btn-xs ml-sm">
                                        Save
                                    </button>
                                </div>
                            </div>
                            <select title="Choose hospital"
                                    class="form-control prescription_hospital"
                                    name="prescription[<?= $detail['stockid'] ?>][hospital]"
                                    required>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mt-md">
                        <div class="col-md-2">
                            <label class="checkbox-inline"
                                   title="Check if is referral">
                                <input type="checkbox"
                                       name="prescription[<?= $detail['stockid'] ?>][referred]"
                                       value="1">Referred?
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12 mt-sm">
                        <label>Prescription <span
                                    class="text-danger">*</span></label>
                        <textarea
                                name="prescription[<?= $detail['stockid'] ?>][text]"
                                class="form-control text-sm"
                                rows="4" required></textarea>
                    </div>
                <? } ?>
            </div>
            <div class="col-md-6">
                <? if ($detail['track_expire_date']) { ?>
                    <div class="col-md-12 mb-sm"
                         style="display: flex;justify-content: end;">
                        <button title="distribute qty to batches" type="button"
                                class="btn btn-primary mr-md btn-sm"
                                onclick="distributeQtyToBatches($(this).closest('.group'))">
                            <i class="fa fa-gears"></i>
                        </button>
                        <button title="clear unused" type="button"
                                class="btn btn-warning mr-md btn-sm"
                                onclick="clearUnusedBatches(this)">
                            <i class="fa fa-filter"></i>
                        </button>
                        <button title="refresh batches" type="button"
                                class="btn btn-info btn-sm"
                                onclick="refreshBatches(this)">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                    <div class="col-md-12 batchTableHolder">
                        <table class="table table-condensed table-bordered batch-table"
                               style="font-size: 10pt;">
                            <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Stock Qty</th>
                                <th>Expire Date</th>
                                <th>Sell Qty</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($detail['stock_batches'] as $item) { ?>
                                <tr class="removed">
                                    <td>
                                        <input type="hidden"
                                               name="batch[<?= $detail['stockid'] ?>][batchId][]"
                                               value="<?= $item['batchId'] ?>" disabled>
                                        <input type="text" readonly
                                               placeholder="batch no."
                                               class="form-control input-sm batch_no"
                                               value="<?= $item['batch_no'] ?>" disabled>
                                    </td>
                                    <td>
                                        <input type="number" readonly
                                               placeholder="qty in"
                                               class="form-control input-sm"
                                               value="<?= $item['total'] ?>" disabled>
                                    </td>
                                    <td>
                                        <input type="text" readonly
                                               class="form-control input-sm"
                                               value="<?= $item['expire_date'] ?>" disabled>
                                    </td>
                                    <td>
                                        <input type="text" placeholder="qty out" name="batch[<?= $detail['stockid'] ?>][qty_out][]"
                                               min="1" max="<?= $item['total'] ?>" required
                                               class="form-control input-sm batch_qty_out" oninput="updateQty(this)" disabled>
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
                <? } ?>
            </div>
        </div>
    <? } ?>
</div>
