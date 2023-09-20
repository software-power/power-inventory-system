<style media="screen">
    .formholder h5 {
        font-size: 15px;
        font-weight: 600;
    }

    .for-input {
        padding: 8px;
        height: 40px;
        font-size: 14px;
        border: none;
        outline: none;
        margin-top: 2px;
    }

    .formModel {
        display: none;
        position: fixed;
        width: 100%;
        z-index: 14;
        background: rgba(238, 238, 238, 0.6196078431372549);
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        height: 100%;
    }

    .formholder {
        position: relative;
        display: none;
        z-index: 26;
        border-radius: 5px;
        padding: 24px;
        width: 100%;
        background: #ededee;
        /* height: 166px; */
        -webkit-box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
        -moz-box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
        box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
    }

    .panelControl {
        float: right;
    }

    .row.row-height {
        height: 57px;
    }

    .out-of-stock, .out-of-stock:hover {
        background: #ff000057 !important;
        cursor: pointer;
    }
</style>
<header class="page-header">
    <h2>Stock Report Reorder Level</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="stocks">
                <input type="hidden" name="action" value="stock_report_with_reorder">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row mb-md">
                        <div class="col-md-4" title="min level variance percentage">
                            Level Variance %:
                            <input type="number" class="form-control" name="variance" placeholder="level variance %"
                                   value="<?= $_GET['variance'] ?? 0 ?>" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            Location:
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="stockloc" class="form-control" name="stocklocation">
                                    <? foreach ($branchLocations as $R) {?>
                                        <option <?=selected($location['id'], $R['id'])?> value="<?= $R['id'] ?>"><?= $R['name'] ?> - <?= $R['branchname'] ?></option>
                                    <?}?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
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
                                <option selected value="">Select Brand Name</option>
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
                                <option value="">Select Department</option>
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
                <button type="button" id="openModel" class="btn btn-default" data-toggle="modal" data-target="#search-modal">
                    <i class="fa fa-search"></i> Open Search
                </button>
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                <a class="btn btn-primary btn-sm"
                   href="?module=stocks&action=bacthwise_stock_report<?= !empty($_GET['stocklocation']) ? "&stocklocation=" . $_GET['stocklocation'] : "" ?>"
                   title="View detailed report">Batch-wise Report</a>
            </div>
            <h2 class="panel-title">Stock Reports With Reorder Level</h2>
            <p><strong class="text-danger"><?= $location['name'] ?> - <?= $location['branchname'] ?></strong></p>
            <p class="text-primary"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Barcode</th>
                        <th>Product name</th>
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
                        <th style="text-align:center">Quantity</th>
                        <th style="text-align:center">Min Level</th>
                        <th style="text-align:center">Variance Level</th>
                        <th>Unit</th>
                        <th>Bulk Unit</th>
                    </tr>
                    </thead>
                    <tbody>

                    <? $count = 1;
                    foreach ($stocklist as $ins => $list) { ?>
                        <tr class="<?= $list['total'] < $list['varianceLevel'] ? 'out-of-stock' : '' ?>">
                            <td><?= $count ?></td>
                            <td><?= $list['barcode_office'] ?></td>
                            <td><?= $list['name'] ?></td>
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
                            <td style="text-align:center"><?= $list['minLevel'] ?? "-" ?></td>
                            <td style="text-align:center"><?= $list['varianceLevel'] ?? "-" ?></td>
                            <td><?= $list['unitName'] ?></td>
                            <td><?= $list['total'] / $list['bulkRate'] ?> <?= $list['bulkUnit'] ?></td>
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
    });



</script>
