<style media="screen">
    .out-of-stock {
        background: #ff000057;
    }
</style>
<header class="page-header">
    <h2>Branch Stock Without Cost Report</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Branches Stock Without Cost Report</h2>
                    </div>
                    <div class="col-md-8">
                        <form action="">
                            <input type="hidden" name="module" value="stocks">
                            <input type="hidden" name="action" value="<?=$action?>">
                            <div class="row">
                                <div class="col-md-6">
                                    Choose Branches:
                                    <select id="branchid" name="branchids[]" multiple class="form-control">
                                        <? foreach ($branchlist as $b) { ?>
                                            <option <?= in_array($b['id'], $branchids) ? 'selected' : '' ?>
                                                    value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Product:
                                    <select id="productid" name="productid" class="form-control"></select>
                                </div>
                                <div class="col-md-3">
                                    Brand:
                                    <select id="brandid" name="brandid" class="form-control">
                                        <option value="">-- All --</option>
                                        <? foreach ($brands as $b) { ?>
                                            <option <?= selected($b['id'], $brandid) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    Category:
                                    <select id="productcategory" name="productcategoryid" class="form-control">
                                        <option value="">-- All --</option>
                                        <? foreach ($productCategories as $pc) { ?>
                                            <option <?= selected($pc['id'], $productcategoryid) ?>
                                                    value="<?= $pc['id'] ?>"><?= $pc['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Subcategory:
                                    <select id="subcategory" name="subcategoryid" class="form-control">
                                        <option value="">-- All --</option>
                                        <? foreach ($productSubcategories as $ps) { ?>
                                            <option <?= selected($ps['id'], $subcategoryid) ?> value="<?= $ps['id'] ?>"><?= $ps['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Date:
                                    <input type="date" name="stockdate" class="form-control" value="<?= $stockdate ?>">
                                </div>
                                <div class="col-md-2 pt-lg">
                                    <button class="btn btn-success btn-block"> Search</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <p class="text-primary"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div>
                    <? if ($branches) { ?>
                        <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
                    <? } ?>
                </div>
                <div class="table-responsive">
                    <table id="stock-table" class="table table-hover table-condensed table-bordered mb-none" style="font-size:10pt;">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 80px;">#</th>
                            <th rowspan="2" style="width: 50px;">Barcode</th>
                            <th rowspan="2" style="width: 20%">Product name</th>
                            <th rowspan="2" style="width: 20%">Description</th>
                            <th rowspan="2">Unit</th>
                            <? foreach ($branches as $b) { ?>
                                <th class="text-center"><?= $b['name'] ?></th>
                            <? } ?>
                            <th rowspan="2" class="text-right">Total Qty</th>
                        </tr>
                        <tr>
                            <? foreach ($branches as $b) { ?>
                                <th class="text-center">Qty</th>
                            <? } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        $totalStockValue = 0;
                        foreach ($products as $p) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $p['barcode'] ?></td>
                                <td><?= $p['productname'] ?></td>
                                <td><?= $p['description'] ?></td>
                                <td><?= $p['unitname'] ?></td>
                                <? foreach ($branches as $b) { ?>
                                    <td class="text-right"><?= $p['branches'][$b['id']]['qty'] ?></td>
                                <? } ?>
                                <td class="text-right"><?= $p['qty'] ?></td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>

                    <table id="stock-export-table" class="table table-hover table-condensed table-bordered mb-none"
                           style="font-size:10pt;display:none ;">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 120px">Barcode</th>
                            <th rowspan="2" style="width: 20%">Product name</th>
                            <th rowspan="2" style="width: 20%">Description</th>
                            <th rowspan="2">Unit</th>
                            <? foreach ($branches as $b) { ?>
                                <th class="text-center"><?= $b['name'] ?></th>
                            <? } ?>
                            <th rowspan="2" class="text-right">Total Qty</th>
                        </tr>
                        <tr>
                            <? foreach ($branches as $b) { ?>
                                <th class="text-center">Qty</th>
                            <? } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <? $totalStockValue = 0;
                        foreach ($products as $p) { ?>
                            <tr>
                                <td><?= $p['barcode'] ?></td>
                                <td><?= $p['productname'] ?></td>
                                <td><?= $p['description'] ?></td>
                                <td><?= $p['unitname'] ?></td>
                                <? foreach ($branches as $b) { ?>
                                    <td class="text-right"><?= $p['branches'][$b['id']]['qty'] ?></td>
                                <? } ?>
                                <td class="text-right"><?= $p['qty'] ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', '?module=products&action=getProducts&format=json', 'choose product');
        $('#branchid,#brandid,#productcategory,#subcategory').select2();
        $('#stock-table').DataTable({
            dom: '<"top"fl>t<"bottom"ip>',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    className: 'btn btn-default',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    }
                }]
        });
    });

    function exportExcel(e) {
        let table = document.getElementById("stock-export-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Branch Stock Without Cost <?=$title?>.xlsx`, // fileName you could use any name
            sheet: {
                name: 'STOCKS' // sheetName
            }
        });
    }
</script>