<div class="row m-xs group">
    <button type="button" class="btn btn-danger" style="position: absolute;top:5px;right: 5px;" onclick="removeRow(this);">
        <i class="fa fa-remove"></i>
    </button>
    <div class="col-md-6 raw-material" style="border: 1px dashed #4f874f;">
        <h5 class="text-weight-bold text-center m-xs">Raw Material</h5>
        <div class="row">
            <div class="col-md-6 p-none pl-xs pr-xs" style="position: relative;">
                <span>Product</span>
                <object class="loading-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="30"
                        width="30" style="display:none;position: absolute;top: -10px;right: 0;z-index: 1000"></object>
                <input type="hidden" name="raw_materials[stockid][]" class="inputs stockid" value="<?= $detail['stockid'] ?>">
                <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                       onclick="open_modal(this,'.group',fetchDetails)" value="<?= $detail['productname'] ?>">
                <input type="hidden" class="inputs productid" value="<?= $detail['productid'] ?>">
            </div>
            <div class="col-md-3 p-none pr-xs">
                Current Stock
                <input type="text" class="form-control inputs current_stock" name="raw_materials[current_stock][]" required readonly
                       value="<?= $detail['current_stock'] ?>">
            </div>
            <div class="col-md-3 p-none pr-xs">
                Cost price
                <input type="text" class="form-control inputs costprice" name="raw_materials[costprice][]" required
                       readonly value="<?= $detail['costprice'] ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 p-none pl-xs pr-xs">
                Description
                <textarea rows="3" class="form-control inputs product_description" readonly><?= $detail['description'] ?></textarea>
            </div>
            <div class="col-md-6 p-none">
                <div class="col-md-6 p-none pr-xs">
                    Total Cost price
                    <input type="text" class="form-control inputs total_costprice" readonly value="<?= $detail['total_costprice'] ?>">
                </div>
                <div class="col-md-6 p-none pr-xs">
                    Using Qty
                    <input type="text" class="form-control inputs qty" name="raw_materials[qty][]" required
                           oninput="checkRawMaterialQty(this)" value="<?= $detail['qty'] ?>" max="<?= $detail['current_stock'] ?>" min="1">
                </div>
                <div class="col-md-6 p-none pr-xs">
                    Overall end products Costprice
                    <input type="text" class="form-control text-danger inputs overall_end_products_costprice" readonly
                           value="<?= formatN($detail['overall_end_products_costprice']) ?>">
                </div>
                <div class="col-md-12 p-none pr-xs d-flex justify-content-end pt-xs pb-xs">
                    <div class="serialno-holder">
                        <? if ($detail['trackserialno']) { ?>
                            <button type="button" class="btn btn-default btn-sm serialBtn" title="Serial numbers"
                                    data-toggle="modal" data-target="#serialModal<?= $detail['stockid'] ?>">
                                <i class="fa fa-barcode"></i> Serial no
                            </button>
                            <div class="modal fade serial-modal" id="serialModal<?= $detail['stockid'] ?>" tabindex="-1" role="dialog"
                                 aria-labelledby="serialModal<?= $detail['stockid'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-center">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title productName">Serial No</h4>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="text-primary"><?= $detail['productname'] ?></h5>
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
            </div>
        </div>
    </div>
    <div class="col-md-6" style="border: 1px dashed #cd705d;">
        <h5 class="text-weight-bold text-center m-xs">End Product</h5>
        <div class="end-products-holder">
            <? foreach ($detail['end_products'] as $e) { ?>
                <div class="end-product">
                    <button type="button" class="btn btn-warning btn-sm" title="remove product"
                            style="position: absolute;top: -6px;right: -14px;z-index: 2;" onclick="removeEndProduct(this);">
                        <i class="fa fa-trash"></i>
                    </button>
                    <div class="row">
                        <div class="col-md-6 p-none pl-xs pr-xs" style="position: relative;">
                            <span>Product</span>
                            <object class="end-product-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="30"
                                    width="30" style="display:none;position: absolute;top: -10px;right: 0;z-index: 1000"></object>
                            <input type="text" readonly class="form-control inputs end_productname" placeholder="search product"
                                   onclick="open_modal(this,'.end-product',fetchEndProductDetails)" value="<?= $e['productname'] ?>">
                            <input type="hidden" class="inputs end_productid" name="end_products[<?= $detail['stockid'] ?>][productid][]" required
                                   value="<?= $e['productid'] ?>">
                        </div>
                        <div class="col-md-3 p-none pr-xs">
                            <div class="d-flex align-items-center">
                                <span>New Qty</span>
                                <object class="end-qty-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="25"
                                        width="25" style="display:none;top: 0;right: 0;z-index: 1000"></object>
                            </div>
                            <input type="text" class="form-control inputs end_qty" name="end_products[<?= $detail['stockid'] ?>][qty][]"
                                   data-source="end_qty" required oninput="calEndProductCostprice(this)" value="<?= $e['qty'] ?>" min="1">
                        </div>
                        <div class="col-md-3 p-none pr-xs">
                            Total Cost
                            <input type="text" class="form-control end_total_costprice" readonly value="<?= $e['end_total_costprice'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 p-none pl-xs pr-xs">
                            Description
                            <textarea rows="3" class="form-control inputs end_product_description" readonly><?= $e['description'] ?></textarea>
                        </div>
                        <div class="col-md-6 p-none pl-xs pr-xs">
                            <div class="col-md-6 p-none pr-xs">
                                Current unit cost
                                <input type="text" class="form-control end_costprice" readonly value="<?= $e['current_costprice'] ?>">
                            </div>
                            <div class="col-md-6 p-none pr-xs">
                                <div class="d-flex align-items-center">
                                    <span>New cost price</span>
                                    <object class="end-costprice-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="25"
                                            width="25" style="display:none;top: 0;right: 0;z-index: 1000"></object>
                                </div>
                                <input type="text" class="form-control inputs end_new_costprice"
                                       name="end_products[<?= $detail['stockid'] ?>][costprice][]"
                                       data-source="end_new_costprice" required oninput="calEndProductCostprice(this)" value="<?= $e['costprice'] ?>">
                            </div>
                            <div class="col-md-6 p-none pr-xs">
                                Current Quick Price Inc
                                <input type="text" class="form-control end_costprice" readonly value="<?= $e['current_quickprice'] ?>">
                            </div>
                            <div class="col-md-6 p-none pr-xs">
                                <div class="d-flex align-items-center">
                                    <span>New quick price inc</span>
                                    <object class="end-quickprice-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="25"
                                            width="25" style="display:none;top: 0;right: 0;z-index: 1000"></object>
                                </div>
                                <input type="text" class="form-control inputs end_new_quickprice"
                                       name="end_products[<?= $detail['stockid'] ?>][quickprice][]"
                                       required oninput="checkQuickPrice(this)" value="<?= $e['quickprice'] ?>">
                                <input type="hidden" class="inputs end_base_percentage" value="<?= $e['baseprice'] ?>">
                                <input type="hidden" class="inputs end_vat_rate" value="<?= $e['vat_percent'] ?>">
                                <small class="text-danger quickprice-error"></small>
                            </div>
                        </div>
                    </div>
                </div>
            <? } ?>
        </div>
        <div class="mt-xs mb-xs">
            <button type="button" class="btn btn-primary btn-sm add-end-product-btn" onclick="addEndProduct(this)">Add End
                product
            </button>
        </div>
    </div>
</div>