<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>


<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Location Stock In/Out report</h2>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <form onsubmit="
                          $(this).find('.spinner-border').show();
                          $(this).find('button').prop('disabled',true);">
                            <input type="hidden" name="module" value="stocks">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    Location:
                                    <? if (STOCK_LOCATIONS) { ?>
                                        <select id="locationid" class="form-control" name="locationid">
                                            <option value="">-- choose location --</option>
                                            <? foreach ($branchLocations as $R) { ?>
                                                <option <?= selected($location['id'], $R['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?>
                                                    - <?= $R['branchname'] ?></option>
                                            <? } ?>
                                        </select>
                                    <? } else { ?>
                                        <select class="form-control" name="locationid">
                                            <option value="">-- choose location --</option>
                                            <option value="<?= $location['id'] ?>"><?= $location['name'] ?>
                                                - <?= $location['branchname'] ?></option>
                                        </select>
                                    <? } ?>
                                </div>
                                <div class="col-md-3">
                                    Product:
                                    <select id="productid" class="form-control" name="productid">
                                        <? if ($product) { ?>
                                            <option value="<?= $product['id'] ?>" selected><?= $product['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Category:
                                    <select id="pcategoryid" class="form-control" name="pcategoryid">
                                        <option value="">-- All --</option>
                                        <? foreach ($productcategories as $b) { ?>
                                            <option <?= selected($b['id'], $pcategoryid) ?>
                                                    value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Subcategory:
                                    <select id="subcategoryid" class="form-control" name="subcategoryid">
                                        <option value="">-- All --</option>
                                        <? foreach ($subcategories as $b) { ?>
                                            <option <?= selected($b['id'], $subcategoryid) ?>
                                                    value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    Brand:
                                    <select id="brandid" class="form-control" name="brandid">
                                        <option value="">-- All --</option>
                                        <? foreach ($brands as $b) { ?>
                                            <option <?= selected($b['id'], $brandid) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <span class="text-danger text-weight-bold">From</span>:
                                    <input type="date" name="fromdate" value="<?= $fromdate ?>" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    To:
                                    <input type="date" name="todate" value="<?= $todate ?>" class="form-control">
                                </div>
                                <div class="col-md-3 d-flex justify-content-end">
                                    <div class="d-flex align-items-center mt-lg">
                                        <span class="spinner-border spinner-border-sm mr-xs"
                                              style="height: 30px;width: 30px;display:none "></span>
                                        <button class="btn btn-success">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <h5>Filter: <span class="text-primary"><?= $title ?></span></h5>
                <div>
                    <button type="button" class="btn btn-primary" onclick="exportExcel()">Export Excel</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed table-bordered mb-none" style="font-size:13px;" id="stock-table">
                        <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Barcode</th>
                            <th rowspan="2">Product</th>
                            <th rowspan="2">Opening Balance</th>
                            <th colspan="5" class="text-center"><i class="fa fa-arrow-down text-success"></i> STOCK IN</th>
                            <th colspan="5" class="text-center"><i class="fa fa-arrow-up text-danger"></i> STOCK OUT</th>
                            <th rowspan="2">CURRENT STOCK</th>
                            <th rowspan="2"></th>
                        </tr>
                        <tr>
                            <th>Purchase</th>
                            <th>Trans In</th>
                            <th>Adjust(+)</th>
                            <th>Credit Note</th>
                            <th>Manufacture End</th>
                            <th>Sales</th>
                            <th>Returns</th>
                            <th>Trans Out</th>
                            <th>Adjust(-)</th>
                            <th>Manufacture Raw</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($products as $index => $p) { ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $p['barcode_office'] ?: $p['barcode_manufacture'] ?></td>
                                <td>
                                    <div><?= $p['name'] ?></div>
                                    <i class="text-muted"><?= $p['description'] ?></i>
                                </td>
                                <td><?= $p['opening_balance'] ?></td>
                                <td><?= $p['history']['in']['purchase'] ?></td>
                                <td><?= $p['history']['in']['trans_in'] ?></td>
                                <td><?= $p['history']['in']['adj_in'] ?></td>
                                <td><?= $p['history']['in']['sale_return'] ?></td>
                                <td><?= $p['history']['in']['man_end'] ?></td>
                                <td><?= $p['history']['out']['sale'] ?></td>
                                <td><?= $p['history']['out']['grn_return'] ?></td>
                                <td><?= $p['history']['out']['trans_out'] ?></td>
                                <td><?= $p['history']['out']['adj_out'] ?></td>
                                <td><?= $p['history']['out']['man_raw'] ?></td>
                                <td><?= $p['total'] ?></td>
                                <td>
                                    <a class="btn btn-link btn-sm" target="_blank"
                                       href="<?= url('reports', 'product_history',
                                           ['fromdate' => $fromdate, 'todate' => $todate,
                                               'productid' => $p['productid'],
                                               'locationid' => $location['id']]) ?>">View history</a>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
                <table class="table table-condensed table-bordered mb-none" style="font-size:13px;display: none" id="stock-table-export">
                    <thead>
                    <tr>
                        <th rowspan="2">Barcode</th>
                        <th rowspan="2">Product</th>
                        <th rowspan="2">Opening Balance</th>
                        <th colspan="5" class="text-center"><i class="fa fa-arrow-down text-success"></i> STOCK IN</th>
                        <th colspan="5" class="text-center"><i class="fa fa-arrow-up text-danger"></i> STOCK OUT</th>
                        <th rowspan="2">CURRENT STOCK</th>
                    </tr>
                    <tr>
                        <th>Purchase</th>
                        <th>Trans In</th>
                        <th>Adjust(+)</th>
                        <th>Credit Note</th>
                        <th>Manufacture End</th>
                        <th>Sales</th>
                        <th>Returns</th>
                        <th>Trans Out</th>
                        <th>Adjust(-)</th>
                        <th>Manufacture Raw</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($products as $index => $p) { ?>
                        <tr>
                            <td><?= $p['barcode_office'] ?: $p['barcode_manufacture'] ?></td>
                            <td>
                                <div><?= $p['name'] ?></div>
                                <i class="text-muted"><?= $p['description'] ?></i>
                            </td>
                            <td><?= $p['opening_balance'] ?></td>
                            <td><?= $p['history']['in']['purchase'] ?></td>
                            <td><?= $p['history']['in']['trans_in'] ?></td>
                            <td><?= $p['history']['in']['adj_in'] ?></td>
                            <td><?= $p['history']['in']['sale_return'] ?></td>
                            <td><?= $p['history']['in']['man_end'] ?></td>
                            <td><?= $p['history']['out']['sale'] ?></td>
                            <td><?= $p['history']['out']['grn_return'] ?></td>
                            <td><?= $p['history']['out']['trans_out'] ?></td>
                            <td><?= $p['history']['out']['adj_out'] ?></td>
                            <td><?= $p['history']['out']['man_raw'] ?></td>
                            <td><?= $p['total'] ?></td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script src="assets/js/tableToExcel.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json&non_stock=no", 'choose product');
        $('#locationid,#brandid,#pcategoryid,#subcategoryid').select2({width: '100%'});
        $('#stock-table').DataTable({
            dom: '<"top"fl>t<"bottom"ip>',
            colReorder: true,
            // pageLength:15,
            lengthMenu: [10, 25, 50, 75, 100],
            keys: true,
            buttons: [
                'excel',
                'csv'
            ],
            exportOptions: {
                columns: ':not(:last-child)',
            },
            title: `<?=CS_COMPANY?>`,
        });
    });

    function exportExcel(e) {
        let table = document.getElementById("stock-table-export");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Stock In/Out <?=$title?>.xlsx`, // fileName you could use any name
            sheet: {
                name: 'Stock' // sheetName
            }
        });
    }
</script>
