<style media="screen">
    .out-of-stock, .out-of-stock:hover {
        background: #ff000057 !important;
        cursor: pointer;
    }
</style>
<header class="page-header">
    <h2>Stock Report With Quantity Category</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="stocks">
                <input type="hidden" name="action" value="<?= $action ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3 p-xs">
                            Location:
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="stockloc" class="form-control" name="stocklocation">
                                    <? foreach ($branchLocations as $R) { ?>
                                        <option <?= selected($location['id'], $R['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?>
                                            - <?= $R['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control"
                                       value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                <input type="hidden" class="form-control" name="stocklocation" value="<?= $location['id'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-3 p-xs">
                            Product:
                            <select id="productid" class="form-control" name="productid">
                                <option selected value="">All products</option>
                            </select>
                        </div>
                        <div class="col-md-3 p-xs">
                            Tax Category:
                            <select class="form-control" name="category">
                                <option selected value="" disabled>Select TAX Category</option>
                                <? foreach ($categories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3 p-xs">
                            Product Category:
                            <select id="productcategory" class="form-control" name="productcategoryid">
                                <option selected value="">All category</option>
                                <? foreach ($productCategories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3 p-xs">
                            Product Subcategory:
                            <select id="subcategory" class="form-control" name="subcategoryid">
                                <option selected value="">All subcategory</option>
                                <? foreach ($productSubcategories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3 p-xs">
                            Brand:
                            <select id="brand" class="form-control" name="brand">
                                <option selected value="" disabled>Select Brand Name</option>
                                <? foreach ($brands as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3 p-xs">
                            Department:
                            <select id="depart" class="form-control" name="depart">
                                <option selected value="" disabled>Select Department</option>
                                <? foreach ($depart as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3 p-xs">
                            Stock Date:
                            <input type="date" class="form-control" name="stockdate" value="<?= $stockdate ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 p-xs">
                            Quantity:
                            <input type="number" class="form-control quantity" name="quantity" min="0" value="<?= $quantity ?>" required
                                   placeholder="category qty" oninput="check_qty(this)">
                        </div>
                        <div class="col-md-3 p-xs">
                            Category:
                            <div class="d-flex flex-column ml-md">
                                <label>
                                    <input type="radio" name="qtycategory" value="equalto"
                                           required <?= $qtycategory == 'equalto' ? 'checked' : '' ?>>
                                    <span class="text-weight-semibold">equal to <span class="label-qty"><?= $quantity ?></span></span>
                                </label>
                                <label>
                                    <input type="radio" name="qtycategory"
                                           value="lessthan" <?= $qtycategory == 'lessthan' ? 'checked' : '' ?>>
                                    <span class="text-weight-semibold">less than <span class="label-qty"><?= $quantity ?></span></span>
                                </label>
                                <label>
                                    <input type="radio" name="qtycategory"
                                           value="morethan" <?= $qtycategory == 'morethan' ? 'checked' : '' ?>>
                                    <span class="text-weight-semibold">more than <span class="label-qty"><?= $quantity ?></span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panelControl">
                <button class="btn" href="#search-modal" data-toggle="modal">
                    <i class="fa fa-search"></i> Open Search
                </button>
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
            </div>
            <h2 class="panel-title">Stock Report With Quantity Category</h2>
            <p><strong class="text-danger"><?= $location['name'] ?> - <?= $location['branchname'] ?></strong></p>
            <p>Filter: <span class="ml-md text-primary"><?= $title ?></span></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Barcode</th>
                        <th>Product name</th>
                        <th>Description</th>
                        <? if (CS_SHOW_GENERIC_NAME) { ?>
                            <th>Generic name</th>
                        <? } ?>
                        <th>Tax</th>
                        <? if (CS_SHOW_CATEGORY) { ?>
                            <th>Category</th>
                            <th>Subcategory</th>
                        <? } ?>
                        <? if (CS_SHOW_BRAND) { ?>
                            <th>Brand</th>
                        <? } ?>
                        <? if (CS_SHOW_DEPARTMENT) { ?>
                            <th>Department</th>
                        <? } ?>
                        <th style="text-align:center">Stock Qty</th>
                        <th>In Unit</th>
                        <!--                        <th>Bulk Unit</th>-->
                    </tr>
                    </thead>
                    <tbody>

                    <? $count = 1;
                    foreach ($stocklist as $ins => $list) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $list['barcode_office'] ?></td>
                            <td><?= $list['name'] ?></td>
                            <td><?= $list['productdescription'] ?></td>
                            <? if (CS_SHOW_GENERIC_NAME) { ?>
                                <td><?= $list['generic_name'] ?></td>
                            <? } ?>
                            <td><?= $list['catName'] ?></td>
                            <? if (CS_SHOW_CATEGORY) { ?>
                                <td><?= $list['productcategoryname'] ?></td>
                                <td><?= $list['subcategoryname'] ?></td>
                            <? } ?>
                            <? if (CS_SHOW_BRAND) { ?>
                                <td><?= $list['brandName'] ?></td>
                            <? } ?>
                            <? if (CS_SHOW_DEPARTMENT) { ?>
                                <td><?= $list['departName'] ?></td>
                            <? } ?>
                            <td style="text-align:center"><strong><?= $list['total'] ?></strong></td>
                            <td class="text-sm"><?= $list['inunit'] ?></td>
                            <!--                            <td>--><? //= $list['total'] / $list['bulkRate'] ?><!-- -->
                            <? //= $list['bulkUnit'] ?><!--</td>-->
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">

    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#stockloc').select2({width: '100%'});
        $('#productcategory').select2({width: '100%'});
        $('#subcategory').select2({width: '100%'});
        $('#brand').select2({width: '100%'});
        $('#depart').select2({width: '100%'});
        <?if(!isset($quantity)){?>
        $('#search-modal').modal('show');
        setTimeout(function () {
            $('#search-modal').find('.quantity').focus();
        }, 1000);
        <?}?>
    });

    function check_qty(obj) {
        let qty = parseInt($(obj).val());
        if (!isNaN(qty)) {
            $('label span.label-qty').text(qty);
        } else {
            $('label span.label-qty').text('');
        }
    }
</script>
