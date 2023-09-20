<header class="page-header">
    <h2>Products</h2>
</header>


<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">Product List with stock</h2>
                    <p>Stock location: <span class="text-primary"><?= $location['name'] ?> - <?= $location['branchname'] ?></span></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <? if (Users::can(OtherRights::add_product)) { ?>
                        <a href="?module=products&action=product_add" class="btn btn-default">
                            <i class="fa fa-plus"></i> Add Product</a>
                    <? } ?>
                    <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <form class="pb-lg" style="border-bottom: 1px solid #dadada;">
                <input type="hidden" name="module" value="products">
                <input type="hidden" name="action" value="product_with_stock">
                <div class="row mb-md d-flex justify-content-center">
                    <div class="col-md-4 d-flex align-items-center">
                        <span>Search:</span>
                        <input type="text" name="search" minlength="3" class="form-control"
                               placeholder="Major product search name or barcode or description" value="<?= $search ?>" style="border-radius: 5px;">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-search"></i> SEARCH</button>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-3">
                                Brand:
                                <select id="brandid" class="form-control" name="brandid">
                                    <option value="">-- Brand --</option>
                                    <? foreach ($brands as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Product Category:
                                <select id="productcategoryid" class="form-control" name="productcategoryid">
                                    <option value="">-- Category --</option>
                                    <? foreach ($productcategories as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Product Subcategory:
                                <select id="subcategoryid" class="form-control" name="subcategoryid">
                                    <option value="">-- Subcategory --</option>
                                    <? foreach ($subcategories as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Non-Stock Products:
                                <select class="form-control" name="non_stock">
                                    <option value="">-- All --</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <p class="text-primary"><?= $title ?></p>
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Brand</th>
                        <th>VAT %</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Unit</th>
                        <th>Manufacture Barcode</th>
                        <th>Other Barcode</th>
                        <th>Stock Qty</th>
                        <th>Bulk Store Qty</th>
                        <th>Prices</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $USER_CAN_ADMIN_VIEW = Users::can(OtherRights::admin_view);
                    $USER_CAN_EDIT = Users::can(OtherRights::edit_product);
                    $USER_CAN_EDIT_PRICE = Users::can(OtherRights::edit_price);
                    $USER_CAN_UPLOAD_IMAGE = Users::can(OtherRights::upload_product_image);
                    $USER_CAN_PRINT_BARCODE = Users::can(OtherRights::print_barcode);
                    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    foreach ($products as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $id + 1 ?></td>
                            <td style="width: 15%;">
                                <p class="m-xs"><?= $R['name'] ?></p>
                                <? if ($R['non_stock']) { ?>
                                    <small class="text-danger"> non-stock</small>
                                <? } ?>
                            </td>
                            <td><?= wordwrap($R['description'], 30, "\r\n") ?></td>
                            <td><?= $R['brandName'] ?></td>
                            <td><?= $R['categoryName'] ?: $R['catName'] ?></td>
                            <td><?= $R['productcategory'] ?: $R['productcategoryname'] ?></td>
                            <td><?= $R['productsubcategory'] ?: $R['subcategoryname'] ?></td>
                            <td><?= $R['unitname'] ?: $R['unitName'] ?></td>
                            <td><?= $R['barcode_manufacture'] ?></td>
                            <td><?= $R['barcode_office'] ?></td>
                            <td class="text-success text-weight-bold"><?= $R['non_stock'] ? '-' : ($R['stock_qty'] ?: 0) ?></td>
                            <td class="text-success text-weight-bold"><?= $R['non_stock'] ? '-' : ($R['bulk_store_qty'] ?: 0) ?></td>
                            <td style="width: 30%;">
                                <? if (!$R['non_stock']) { ?>
                                    <table class="table table-bordered" style="font-size: 9pt">
                                        <tbody>
                                        <tr class="text-weight-bold">
                                            <td>Hierarchic</td>
                                            <td>Commission %</td>
                                            <td>Target %</td>
                                            <td>Price <span class="text-success">Exc</span></td>
                                            <td>Price <span class="text-danger">Inc</span></td>
                                        </tr>
                                        <? foreach ($R['prices'] as $p) { ?>
                                            <tr>
                                                <td><?= $p['hierarchicname'] ?></td>
                                                <td><?= $p['commission'] ?></td>
                                                <td><?= $p['target'] ?></td>
                                                <td class="text-right text-success"><?= formatN($p['exc_price']) ?></td>
                                                <td class="text-right text-weight-bold text-danger"><?= formatN($p['inc_price']) ?></td>
                                            </tr>
                                        <? } ?>
                                        <tr>
                                            <td>Quick Sale</td>
                                            <td colspan="2"></td>
                                            <td class="text-right text-success"><?= formatN($R['quick_price_exc']) ?></td>
                                            <td class="text-right text-weight-bold text-danger"><?= formatN($R['quick_price_inc']) ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                <? } ?>
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <? if ($USER_CAN_EDIT) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('products', 'product_edit', 'id=' . $R['productid'] . '&redirect=' . base64_encode($current_url)) ?>"
                                               title="Edit"><i class="fa-pencil fa"></i> Edit Product</a>
                                        <? } ?>
                                        <? if ($USER_CAN_ADMIN_VIEW) { ?>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('products', 'product_admin', 'productid=' . $R['productid']) ?>"
                                               title="Admin View"><i class="fa-arrow-circle-right fa"></i> Admin View</a>
                                        <? } ?>
                                        <? if ($USER_CAN_EDIT_PRICE) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('hierarchics', 'product_hierarchics', [
                                                   'productid' => $R['productid'],
                                                   'redirect' => base64_encode($current_url)]) ?>"
                                               title="Edit Prices"><i class="fa-dollar fa"></i> Edit Prices</a>
                                        <? } ?>
                                        <a data-productid="<?= $R['productid'] ?>" class="dropdown-item"
                                           href="#product-view-modal" title="Product view" data-toggle="modal">
                                            <i class="fa-eye fa"></i> Product View</a>
                                        <? if ($USER_CAN_UPLOAD_IMAGE) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('products', 'image_upload', ['id' => $R['productid']]) ?>"
                                               title="Edit Prices"><i class="fa fa-picture-o"></i> Upload Image</a>
                                        <? } ?>
                                        <? if ($USER_CAN_PRINT_BARCODE && !$R['non_stock']) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('products', 'generate_barcode', ['productid' => $R['productid']]) ?>"
                                               title="Edit Prices"><i class="fa fa-barcode"></i> Print Barcode</a>
                                        <? } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?= component('shared/product_view_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">


    $(function () {
        $('#departmentid,#brandid,#productcategoryid,#subcategoryid,#unitid,#bulkunitid').select2({width: '100%'});
    });
</script>
