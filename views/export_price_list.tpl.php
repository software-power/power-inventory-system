<style>
    .select2-selection .select2-selection--single {
        border: 1px solid grey;
    }

    .below-base-opacity {
        opacity: 0.7;
    }
</style>
<header class="page-header">
    <h2>Export Price List</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Search</h4>
            </div>
            <form>
                <div class="modal-body">
                    <input type="hidden" name="module" value="hierarchics">
                    <input type="hidden" name="action" value="<?= $action ?>">
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select id="branchid" class="form-control" name="branchid">
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($branchid, $b['id']) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Price Level:
                            <select id="price_hierarchic" class="form-control" name="price_hierarchic">
                                <optgroup label="Hierarchic">
                                    <? foreach ($hierarchicList as $h) { ?>
                                        <option value="<?= $h['id'] ?>"><?= $h['name'] ?></option>
                                    <? } ?>
                                </optgroup>
                                <option value="quick_sale">Quick sale Price</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control" name="productid"> </select>
                        </div>
                        <div class="col-md-4">
                            Brand:
                            <select id="modelid" class="form-control" name="modelid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($models as $r) { ?>
                                    <option <?= selected($r['id'], $user) ?>
                                            value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Department:
                            <select id="department" class="form-control" name="deptid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($departments as $r) { ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-4">
                            Product Category:
                            <select id="productcategory" class="form-control" name="productcategoryid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($productcategories as $r) { ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Subcategory:
                            <select id="subcategory" class="form-control" name="subcategoryid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($subcategories as $r) { ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="button"><i class="fa fa-search"></i> Search</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-12 p-none">
    <section class="panel">
        <header class="panel-heading">
            <div class="d-flex justify-content-between align-items-center">
                <? if ($with_stock) { ?>
                    <h2 class="panel-title">Export Price List With Stock</h2>
                <? } else { ?>
                    <h2 class="panel-title">Export Price List</h2>
                <? } ?>
                <button type="button" class="btn btn-default btn-lg" data-toggle="modal" data-target="#search-modal"><i class="fa fa-search"></i>
                    Filter
                </button>
            </div>
            <p class="text-primary"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <a href="<?= $pdf_url ?>" class="btn btn-default" target="_blank"><i class="fa fa-print"></i> PDF</a>
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="price-list-datatable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th style="width:10%">Name</th>
                        <? if (CS_SHOW_GENERIC_NAME) { ?>
                            <th style="width:10%">Generic name</th>
                        <? } ?>
                        <th style="width:10%">Description</th>
                        <th>Department</th>
                        <th>Brand</th>
                        <th>VAT %</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Unit</th>
                        <th>Currency</th>
                        <? if ($with_stock) { ?>
                            <th>Stock Qty</th>
                            <th><span class="text-success">Exc</span> Price</th>
                        <? } ?>
                        <th class="text-right"><span class="text-danger">Inc</span> Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($products as $p) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $p['productname'] ?></td>
                            <? if (CS_SHOW_GENERIC_NAME) { ?>
                                <td><?= $p['generic_name'] ?></td>
                            <? } ?>
                            <td><?= $p['productdescription'] ?></td>
                            <td><?= $p['departmentname'] ?></td>
                            <td><?= $p['brandname'] ?></td>
                            <td><?= $p['vat_percent'] ?>%</td>
                            <td><?= $p['productcategoryname'] ?></td>
                            <td><?= $p['productsubcategoryname'] ?></td>
                            <td><?= $p['unitname'] ?></td>
                            <td><?= $basecurrency['name'] ?></td>
                            <? if ($with_stock) { ?>
                                <td><?= $p['branch_stock_qty'] ?></td>
                                <td class="text-right"><?= formatN($p['export_excprice']) ?></td>
                            <? } ?>
                            <td class="text-right"><?= formatN($p['export_incprice']) ?></td>
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
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#branchid,#price_hierarchic,#productcategory,#subcategory,#modelid,#department').select2({width: '100%'});

        $('#price-list-datatable').DataTable({
            dom: '<"top"Bfl>t<"bottom"ip>',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-print"></i> Excel',
                    className: 'btn btn-default',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    }
                }]
        });


    });
</script>