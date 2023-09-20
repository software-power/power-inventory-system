<style media="screen">
    .out-of-stock {
        background: #ff000057;
    }
</style>
<header class="page-header">
    <h2>Stock Report</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="stocks">
                <input type="hidden" name="action" value="stock_report_admin">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Location:
                            <? if (IS_ADMIN) { ?>
                                <select id="stockloc" class="form-control" name="stocklocation">
                                    <option value="<?= $location['id'] ?>"><?= $location['name'] ?></option>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $location['name'] ?>">
                                <input type="hidden" class="form-control" name="stocklocation" value="<?= $location['id'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control" name="productid">
                                <option selected value="">All products</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Tax Category:
                            <select class="form-control" name="category">
                                <option selected value="" disabled>Select TAX Category</option>
                                <? foreach ($categories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-md">
                        <div class="col-md-4">
                            Product Category:
                            <select id="productcategory" class="form-control" name="productcategoryid">
                                <option selected value="">All category</option>
                                <? foreach ($productCategories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Product Subcategory:
                            <select id="subcategory" class="form-control" name="subcategoryid">
                                <option selected value="">All subcategory</option>
                                <? foreach ($productSubcategories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Brand:
                            <select id="brand" class="form-control" name="brand">
                                <option selected value="" disabled>Select Brand Name</option>
                                <? foreach ($brands as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Department:
                            <select id="depart" class="form-control" name="depart">
                                <option selected value="" disabled>Select Department</option>
                                <? foreach ($depart as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
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
                <a class="btn btn-warning btn-sm"
                   href="?module=stocks&action=stock_report_admin_with_supplier<?= !empty($_GET['stocklocation']) ? "&stocklocation=" . $_GET['stocklocation'] : "" ?>"
                   title="With supplier">With Supplier</a>
                <a class="btn btn-primary btn-sm"
                   href="?module=stocks&action=stock_report_admin_detailed<?= !empty($_GET['stocklocation']) ? "&stocklocation=" . $_GET['stocklocation'] : "" ?>"
                   title="View detailed report">Detailed Report</a>
            </div>
            <h2 class="panel-title">Stock Reports - Admin</h2>
            <p><strong class="text-danger"><?= $location['name'] ?> - <?= $location['branchname'] ?></strong></p>
            <p>Filter: <span class="ml-md text-primary"><?= $title ?></span></p>
        </header>
        <div class="panel-body">
            <div>
                <h4>TOTAL STOCK VALUE: <strong class="text-rosepink"><?= formatN($totalStockValue) ?></strong></h4>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
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
                        <th>Cost Price</th>
                        <th>Quantity</th>
                        <th style="text-align:center">Stock Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    $totalStockValue = 0;
                    foreach ($stocklist as $ins => $list) { ?>
                        <tr class="<?= $list['level'] == 'below' ? 'out-of-stock' : '' ?>"
                            title="<?= $list['level'] == 'below' ? 'Below level' : '' ?>">
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
                            <td><?= formatN($list['costprice']) ?></td>
                            <td><?= $list['total'] ?></td>
                            <td style="text-align:right">
                                <strong><?= formatN($list['stock_value']) ?></strong></td>
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
        initSelectAjax('#stockloc', "?module=locations&action=getLocations&format=json", "Stock location");
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#productcategory').select2({width: '100%'});
        $('#subcategory').select2({width: '100%'});
        $('#brand').select2({width: '100%'});
        $('#depart').select2({width: '100%'});
    });
</script>
