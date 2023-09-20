<style media="screen">
    .out-of-stock {
        background: #ff000057;
    }
</style>
<header class="page-header">
    <h2>Branch Location-wise Stock Report</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Branch Location-wise Stock Report</h2>
                <p>Filter: <span class="ml-md text-primary"><?= $title ?></span></p>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-10">
                        <form action="" class="d-flex align-items-center">
                            <input type="hidden" name="module" value="stocks">
                            <input type="hidden" name="action" value="branch_locationwise_stock_report">
                            <label>Product: </label>
                            <select id="productid" name="productid" class="form-control"></select>
                            <label>Category: </label>
                            <select id="productcategory" name="productcategoryid" class="form-control">
                                <option value="">-- All --</option>
                                <? foreach ($productCategories as $pc) { ?>
                                    <option value="<?= $pc['id'] ?>"><?= $pc['name'] ?></option>
                                <? } ?>
                            </select>
                            <label>Subcategory: </label>
                            <select id="subcategory" name="subcategoryid" class="form-control">
                                <option value="">-- All --</option>
                                <? foreach ($productSubcategories as $ps) { ?>
                                    <option value="<?= $ps['id'] ?>"><?= $ps['name'] ?></option>
                                <? } ?>
                            </select>
                            <label class="ml-sm">Branch: </label>
                            <select id="branches" name="branchid" id="" class="form-control ml-sm">
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($branch['id'], $b['id']) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                            <label class="ml-sm">StockDate: </label>
                            <input type="date" name="stockdate" class="form-control" value="<?= $stockdate ?>">
                            <button class="btn btn-success btn-sm ml-sm"> Search</button>
                        </form>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
                </div>
                <div class="table-responsive">
                    <table id="stock-table" class="table table-hover table-condensed table-bordered mb-none" style="font-size:10pt;">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 80px;">#</th>
                            <th rowspan="2" style="width: 30%">Product name</th>
                            <th rowspan="2">Unit</th>
                            <th rowspan="2" class="text-right">Cost Price</th>
                            <? foreach ($locations as $l) { ?>
                                <th colspan="2" class="text-center">
                                    <a href="<?= url('stocks', 'stock_report', ['stocklocation' => $l['id'], 'stockdate' => $stockdate]) ?>"><?= $l['name'] ?></a>

                                </th>
                            <? } ?>
                            <th rowspan="2" class="text-right">Total Qty</th>
                            <th rowspan="2" class="text-right">Stock Value</th>
                        </tr>
                        <tr>
                            <? foreach ($locations as $l) { ?>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Stock value</th>
                            <? } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($products as $p) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $p['productname'] ?></td>
                                <td><?= $p['unitname'] ?></td>
                                <td class="text-right"><?= formatN($p['costprice']) ?></td>
                                <? foreach ($locations as $l) { ?>
                                    <td class="text-right"><?= $p['locations'][$l['id']]['qty'] ?></td>
                                    <td class="text-right"><?= $p['locations'][$l['id']]['stock_value'] ? formatN($p['locations'][$l['id']]['stock_value']) : '' ?></td>
                                <? } ?>
                                <td class="text-right"><?= $p['total_qty'] ?></td>
                                <td class="text-right"><?= formatN($p['stock_value']) ?></td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr class="text-right text-weight-bold" style="font-size: 12pt;">
                            <td colspan="4" class="text-right">TOTAL STOCK VALUE</td>
                            <? foreach ($locations as $l) { ?>
                                <td colspan="2"><?= formatN($l['stock_value']) ?></td>
                            <? } ?>
                            <td colspan="2"><?= formatN($total_stock_value) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <table id="stock-export-table" class="table table-hover table-condensed table-bordered mb-none" style="font-size:10pt;display:none; ">
                    <thead>
                    <tr>
                        <th rowspan="2" style="width: 30%">Product name</th>
                        <th rowspan="2">Unit</th>
                        <th rowspan="2" class="text-right">Cost Price</th>
                        <? foreach ($locations as $l) { ?>
                            <th colspan="2" class="text-center"><?= $l['name'] ?></th>
                        <? } ?>
                        <th rowspan="2" class="text-right">Total Qty</th>
                        <th rowspan="2" class="text-right">Stock Value</th>
                    </tr>
                    <tr>
                        <? foreach ($locations as $l) { ?>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Stock value</th>
                        <? } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    foreach ($products as $p) { ?>
                        <tr>
                            <td><?= $p['productname'] ?></td>
                            <td><?= $p['unitname'] ?></td>
                            <td class="text-right"><?= formatN($p['costprice']) ?></td>
                            <? foreach ($locations as $l) { ?>
                                <td class="text-right"><?= $p['locations'][$l['id']]['qty'] ?></td>
                                <td class="text-right"><?= $p['locations'][$l['id']]['stock_value'] ? formatN($p['locations'][$l['id']]['stock_value']) : '' ?></td>
                            <? } ?>
                            <td class="text-right"><?= $p['total_qty'] ?></td>
                            <td class="text-right"><?= formatN($p['stock_value']) ?></td>
                        </tr>
                    <? } ?>
                    </tbody>
                    <tfoot>
                    <tr class="text-right text-weight-bold" style="font-size: 12pt;">
                        <td colspan="3" class="text-right">TOTAL STOCK VALUE</td>
                        <? foreach ($locations as $l) { ?>
                            <td colspan="2"><?= formatN($l['stock_value']) ?></td>
                        <? } ?>
                        <td colspan="2"><?= formatN($total_stock_value) ?></td>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </section>
    </div>
</div>

<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', '?module=products&action=getProducts&format=json', 'choose product');
        $('#productcategory').select2();
        $('#subcategory').select2();
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
            name: `Branch-Stock <?=$title?>.xlsx`, // fileName you could use any name
            sheet: {
                name: 'STOCKS' // sheetName
            }
        });
    }
</script>
