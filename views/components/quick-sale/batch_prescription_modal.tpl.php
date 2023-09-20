<?
$inputState = "readonly disabled";
$prescriptCol = '';
$batchCol = 'col-md-7 col-md-offset-2';
if ($detail['prescription_required']) {
    $batchCol = 'col-md-6 col-md-offset-1';
    $prescriptCol = 'col-md-5';
} ?>

<div class="modal fade prescription_modal" id="prescriptionModal-<?= $detail['stockid'] ?>" role="dialog"
     aria-labelledby="prescriptionModal-<?= $detail['stockid'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg prescription">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Product: <span class="text-rosepink productName"><?= $detail['productname'] ?></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="<?= $prescriptCol ?> prescription-inputs <?= $detail['prescription_required'] ? 'has-prescription' : '' ?>">
                        <? if ($detail['prescription_required']) { ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label style="font-size:10pt">Prescription Doctor <span class="text-danger">*</span></label>
                                        <button type="button" onclick="quickAddDoctor(this)" title="Quick add doctor"
                                                class="btn btn-info btn-xs ml-lg">Add
                                        </button>
                                    </div>
                                    <div class="col-md-12 new_doctor mb-md" style="display:none ;">
                                        <div class="col-md-10"
                                             style="padding:0;position: relative">
                                            <div class="loading_spinner"
                                                 style="position: absolute;top:5px;right: 10px;display: none;">
                                                <object data="images/loading_spinner.svg"
                                                        type="image/svg+xml" height="30"
                                                        width="30"></object>
                                            </div>
                                            <input type="text" class="form-control doctor"
                                                   placeholder="Doctor's name">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" onclick="saveDoctor(this)"
                                                    title="Quick add doctor"
                                                    class="btn btn-success btn-xs ml-sm mt-xs">Save
                                            </button>
                                        </div>
                                    </div>
                                    <select title="Choose doctor" class="form-control prescription_doctor"
                                            name="prescription[<?= $detail['stockid'] ?>][doctor]" required>
                                        <? if ($detail['prescription']) { ?>
                                            <option value="<?= $detail['prescription']['doctor_id'] ?>"><?= $detail['prescription']['doctorname'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label style="font-size:10pt">Prescription Hospital <span class="text-danger">*</span></label>
                                        <button type="button" onclick="quickAddHospital(this)" title="Quick add Hospital"
                                                class="btn btn-info btn-xs ml-lg">Add
                                        </button>
                                    </div>
                                    <div class="col-md-12 new_hospital mb-md"
                                         style="padding:0;display: none;">
                                        <div class="col-md-10"
                                             style="padding:0;position: relative">
                                            <div class="loading_spinner"
                                                 style="position: absolute;top:5px;right: 10px;display: none;">
                                                <object data="images/loading_spinner.svg"
                                                        type="image/svg+xml" height="30"
                                                        width="30"></object>
                                            </div>
                                            <input type="text" class="form-control hospital" placeholder="hospital's name">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" onclick="saveHospital(this)" title="Quick add doctor"
                                                    class="btn btn-success btn-xs ml-sm mt-xs">Save
                                            </button>
                                        </div>
                                    </div>
                                    <select title="Choose hospital" class="form-control prescription_hospital"
                                            name="prescription[<?= $detail['stockid'] ?>][hospital]" required>
                                        <? if ($detail['prescription']) { ?>
                                            <option value="<?= $detail['prescription']['hospital_id'] ?>"><?= $detail['prescription']['hospitalname'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin:15px 0;">
                                <div class="col-md-2">
                                    <label style="font-size:10pt" class="checkbox-inline" title="Check if is referral">
                                        <input type="checkbox" name="prescription[<?= $detail['stockid'] ?>][referred]"
                                               value="1" <?= $detail['prescription']['referred'] ? 'checked' : '' ?>>
                                        Referred?
                                    </label>
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-12">
                                    <label style="font-size:10pt">Prescription <span class="text-danger">*</span></label>
                                    <textarea name="prescription[<?= $detail['stockid'] ?>][text]"
                                              class="form-control text-sm prescription_text"
                                              rows="4" required><?= $detail['prescription']['prescription'] ?></textarea>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="<?= $batchCol ?>">
                        <? if ($detail['track_expire_date']) { ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <div style="font-size: 16pt;">Total Qty: <label class="totalBatchQtyChosen text-rosepink"></label></div>
                                <? if (Users::can(OtherRights::sale_random_batch)) { ?>
                                    <div>
                                        <button title="Choose from random" type="button" class="btn btn-default btn-sm"
                                                onclick="chooseRandomBatches(this)">
                                            Choose random
                                        </button>
                                        <button title="refresh batches" type="button" class="btn btn-info btn-sm" style="margin-left: 20px;"
                                                onclick="refreshBatches(this)">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </div>
                                <? } ?>
                            </div>
                            <table class="table table-hover table-bordered table-condensed batch-table" style="font-size:10pt;">
                                <thead>
                                <tr>
                                    <td>Batch No</td>
                                    <td>Stock Qty</td>
                                    <td>Expire Date</td>
                                    <td>Sell Qty</td>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($detail['stock_batches'] as $index => $item) { ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" <?= $inputState ?> class="batch_id"
                                                   name="batch[<?= $detail['stockid'] ?>][batchId][]"
                                                   value="<?= $item['batchId'] ?>">
                                            <input type="text" readonly placeholder="batch no."
                                                   class="form-control input-sm batch_no"
                                                   value="<?= $item['batch_no'] ?>">
                                        </td>
                                        <td>
                                            <input type="number" readonly placeholder="qty in"
                                                   class="form-control input-sm batch_qty"
                                                   value="<?= $item['total'] ?>">
                                        </td>
                                        <td>
                                            <input type="text" readonly placeholder="qty in"
                                                   class="form-control input-sm"
                                                   value="<?= $item['expire_date'] ?>" title="<?= $item['expire_description'] ?>">
                                        </td>
                                        <td style="width: 20%;">
                                            <input type="number" placeholder="qty" <?= $inputState ?>
                                                   name="batch[<?= $detail['stockid'] ?>][qty_out][]" min="1" max="<?= $item['total'] ?>" required
                                                   class="form-control input-sm batch_qty_out" oninput="updateQty(this)">
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        <? } ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" onclick="closeModal(this)">Close</button>
            </div>
        </div>
    </div>
</div>