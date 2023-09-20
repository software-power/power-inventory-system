<style>
    input.day[type='number'] {

    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<header class="page-header">
    <h2>Stock Aging Report</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-11">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-4">
                        <h2 class="panel-title">Stock Aging Report</h2>
                        <p class="text-primary"><?= $title ?></p>
                    </div>
                    <div class="col-md-8 d-flex justify-content-end">
                        <button type="button" class="btn btn-default btn-sm" data-target="#search-modal" data-toggle="modal"><i
                                    class="fa fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
                </div>
                <div class="table-responsive">
                    <table id="stock-table" class="table table-hover table-condensed table-bordered mb-none" style="font-size:10pt;">
                        <thead>
                        <tr>
                            <th rowspan="2" style="width: 80px;">#</th>
                            <th rowspan="2" style="width: 120px">Barcode</th>
                            <th rowspan="2" style="width: 20%">Product name</th>
                            <th rowspan="2">Unit</th>
                            <th rowspan="2">Cost price</th>
                            <? foreach ($ranges as $r) { ?>
                                <th colspan="2" class="text-center"><?= $r ?> days</th>
                            <? } ?>
                            <th rowspan="2" class="text-right">Total Stock Value</th>
                        </tr>
                        <tr>
                            <? foreach ($ranges as $r) { ?>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Stock value</th>
                            <? } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($stocks as $s) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $s['barcode_office'] ?></td>
                                <td><?= $s['name'] ?></td>
                                <td><?= $s['unitName'] ?></td>
                                <td><?= $s['costprice'] ?></td>
                                <? foreach ($ranges as $r) { ?>
                                    <td class="text-right"><?= $s['aging'][$r]['qty'] ?></td>
                                    <td class="text-right"><?= $s['aging'][$r]['stock_value']?formatN($s['aging'][$r]['stock_value']):'' ?></td>
                                <? } ?>
                                <td class="text-right"><?= formatN($s['stock_value']) ?></td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr class="text-center text-weight-bold">
                            <td colspan="5">TOTAL STOCK VALUE</td>
                            <? foreach ($ranges as $r) { ?>
                                <td colspan="2"><?= formatN($totals['ranges'][$r]) ?></td>
                            <? } ?>
                            <td><?= formatN($totals['total']) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <table id="stock-export-table" class="table table-hover table-condensed table-bordered mb-none" style="font-size:10pt;display: none;">
                    <thead>
                    <tr>
                        <th rowspan="2" style="width: 120px">Barcode</th>
                        <th rowspan="2" style="width: 20%">Product name</th>
                        <th rowspan="2">Unit</th>
                        <th rowspan="2">Cost price</th>
                        <? foreach ($ranges as $r) { ?>
                            <th colspan="2" class="text-center"><?= $r ?> days</th>
                        <? } ?>
                        <th rowspan="2" class="text-right">Total Stock Value</th>
                    </tr>
                    <tr>
                        <? foreach ($ranges as $r) { ?>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Stock value</th>
                        <? } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    foreach ($stocks as $s) { ?>
                        <tr>
                            <td><?= $s['barcode_office'] ?></td>
                            <td><?= $s['name'] ?></td>
                            <td><?= $s['unitName'] ?></td>
                            <td><?= formatN($s['costprice']) ?></td>
                            <? foreach ($ranges as $r) { ?>
                                <td class="text-right"><?= $s['aging'][$r]['qty'] ?></td>
                                <td class="text-right"><?=  $s['aging'][$r]['stock_value']?formatN($s['aging'][$r]['stock_value']):'' ?></td>
                            <? } ?>
                            <td class="text-right"><?= formatN($s['stock_value']) ?></td>
                        </tr>
                    <? } ?>
                    </tbody>
                    <tfoot>
                    <tr class="text-center text-weight-bold">
                        <td colspan="3">TOTAL STOCK VALUE</td>
                        <? foreach ($ranges as $r) { ?>
                            <td colspan="2"><?= formatN($totals['ranges'][$r]) ?></td>
                        <? } ?>
                        <td><?= formatN($totals['total']) ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Search</h4>
            </div>
            <form>
                <div class="modal-body">
                    <input type="hidden" name="module" value="stocks">
                    <input type="hidden" name="action" value="stock_aging_report">
                    <div class="row">
                        <div class="col-md-6">
                            Location:
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="locationid" class="form-control" name="locationid">
                                    <? foreach ($branchLocations as $R) {?>
                                        <option <?=selected($location['id'], $R['id'])?> value="<?= $R['id'] ?>"><?= $R['name'] ?> - <?= $R['branchname'] ?></option>
                                    <?}?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                <input type="hidden" class="form-control" name="locationid" value="<?= $location['id'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-6">
                            Product:
                            <select id="productid" class="form-control" name="productid"> </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex align-items-center">
                            <label>Days Range</label>
                            <button type="button" class="btn btn-xs btn-primary ml-md" title="add day range"
                                    onclick="addDay()"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div id="days-range" class="row mt-md d-flex flex-wrap"
                         style="border: 1px dashed grey; margin: 1px;padding:8px 3px;border-radius: 5px;">

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-default btn-block" data-dismiss="modal">CANCEL</button>
                        </div>
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-success btn-block"><i
                                        class="fa fa-minus"></i> RESET
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block" name="button"><i
                                        class="fa fa-search"></i>
                                SEARCH
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', '?module=products&action=getProducts&format=json', 'choose product');
        $('#locationid').select2({width:'100%'});
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

    function addDay() {
        let item = `<div class="col-md-2 day mb-sm" style="position: relative;">
                            <button type="button" class="btn btn-danger btn-xs"
                                    onclick="$(this).closest('.day').remove();"
                                    style="position: absolute;top: -10px;right: 0;">
                                <i class="fa fa-close"></i>
                            </button>
                            <input type="number" min="1" name="age_range[]" class="form-control input-sm" placeholder="days" required>
                        </div>`;
        $('#days-range').append(item);
    }

    function exportExcel(e) {
        let table = document.getElementById("stock-export-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Stock Aging <?=$title?>.xlsx`, // fileName you could use any name
            sheet: {
                name: 'STOCKS' // sheetName
            }
        });
    }
</script>