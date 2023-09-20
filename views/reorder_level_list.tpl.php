<style>
    table.dataTable tbody td.focus {
        box-shadow: none !important;
    }
</style>
<header class="page-header">
    <h2>Reorder Levels</h2>
</header>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Reorder Levels</h2>
                    </div>
                    <div class="col-md-9 form-search">
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-9">
                        <form>
                            <input type="hidden" name="module" value="<?= $module ?>">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <div class="row">
                                <div class="col-md-3">
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
                                <div class="col-md-3">
                                    Product:
                                    <select id="productid" class="form-control" name="productid"></select>
                                </div>
                                <div class="col-md-3">
                                    Product Category:
                                    <select id="categoryid" class="form-control" name="categoryid">
                                        <option value=""> -- all --</option>
                                        <? foreach ($categories as $key => $R) { ?>
                                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Subcategory:
                                    <select id="subcategoryid" class="form-control" name="subcategoryid">
                                        <option value=""> -- all --</option>
                                        <? foreach ($categories as $key => $R) { ?>
                                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    Brand:
                                    <select id="brandid" class="form-control" name="brandid">
                                        <option value=""> -- all --</option>
                                        <? foreach ($brands as $key => $R) { ?>
                                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-success btn-sm mt-lg">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-primary"><?= $title ?></p>
                <button class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
                <div class="table-responsive">
                    <table id="reorder-level-table" class="table table-hover mb-none" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Barcode</th>
                            <th style="width: 30%;">Name</th>
                            <? if (CS_SHOW_GENERIC_NAME) { ?>
                                <th style="width: 30%;">Generic name</th>
                            <? } ?>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        $USER_CAN_EDIT_LEVEL = Users::can(OtherRights::edit_reorder_level);
                        foreach ($products as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $count ?></td>
                                <td><?= $R['barcode_office']?:$R['barcode_manufacture'] ?></td>
                                <td>
                                    <div><?= $R['productname'] ?></div>
                                    <i class="text-muted"><?= $R['description'] ?></i>
                                </td>
                                <? if (CS_SHOW_GENERIC_NAME) { ?>
                                    <td><?= $R['generic_name'] ?></td>
                                <? } ?>
                                <td>
                                    <table class="table table-bordered" style="font-size: 9pt;">
                                        <thead>
                                        <tr>
                                            <th>Location</th>
                                            <th>Min Qty</th>
                                            <th>Max Qty</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? foreach ($R['levels'] as $l) { ?>
                                            <tr>
                                                <td>
                                                    <?= $l['locname'] ?>
                                                    <? if ($l['source'] == 'default') { ?>
                                                        <small class="text-danger ml-md"
                                                               title="uses company default levels">default</small>
                                                    <? } ?>
                                                </td>
                                                <td><?= $l['minqty'] ?? 0 ?></td>
                                                <td><?= $l['maxqty'] ?? 0 ?></td>
                                                <td>
                                                    <? if ($USER_CAN_EDIT_LEVEL) { ?>
                                                        <button class="btn btn-default btn-xs" title="edit" data-toggle="modal"
                                                                data-target="#edit-level-modal"
                                                                data-stockid="<?= $l['stockid'] ?>"
                                                                data-minqty="<?= $l['minqty'] ?>"
                                                                data-maxqty="<?= $l['maxqty'] ?>"
                                                                data-locname="<?= $l['locname'] ?>"
                                                                data-productname="<?= $R['description'] ?>">
                                                            <i class="fa fa-pencil"></i> Edit
                                                        </button>
                                                    <? } ?>
                                                </td>
                                            </tr>
                                        <? } ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
                <table id="reorder-export-table" class="table table-hover mb-none" style="font-size: 10pt;display: none;">
                    <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>Name</th>
                        <th>Description</th>
                        <? if (CS_SHOW_GENERIC_NAME) { ?>
                            <th>Generic name</th>
                        <? } ?>
                        <th>Location</th>
                        <th>Min Qty</th>
                        <th>Max Qty</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($products as $id => $R) {
                        foreach (array_values($R['levels']) as $index => $l) {
                            if ($index == 0) { ?>
                                <tr>
                                    <td><?= $R['barcode_office']?:$R['barcode_manufacture'] ?></td>
                                    <td><?= $R['productname'] ?></td>
                                    <td><?= $R['description'] ?></td>
                                    <? if (CS_SHOW_GENERIC_NAME) { ?>
                                        <td><?= $R['generic_name'] ?></td>
                                    <? } ?>
                                    <td>
                                        <?= $l['locname'] ?>
                                        <? if ($l['source'] == 'default') { ?>
                                            <small class="text-danger ml-md"
                                                   title="uses company default levels">default</small>
                                        <? } ?>

                                    </td>
                                    <td><?= $l['minqty'] ?? 0 ?></td>
                                    <td><?= $l['maxqty'] ?? 0 ?></td>
                                </tr>
                            <? } else { ?>
                                <tr>
                                    <td colspan="2"></td>
                                    <td><?= $l['locname'] ?></td>
                                    <td><?= $l['minqty'] ?? 0 ?></td>
                                    <td><?= $l['maxqty'] ?? 0 ?></td>
                                </tr>
                            <? }
                        }
                    } ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>

<div class="modal fade" id="edit-level-modal" tabindex="-1" role="dialog" aria-labelledby="edit-level-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="?module=products&action=save_reorder_level" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Reorder Level</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Product</label>
                        <input type="hidden" name="level[stockid]" class="stockid">
                        <input type="text" class="form-control productname" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Location</label>
                        <input type="text" class="form-control location" readonly>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="">Min Qty</label>
                            <input type="number" min="0" class="form-control minqty"
                                   name="level[minqty]" placeholder="min quantity" required>
                        </div>
                        <div class="col-md-6">
                            <label for="">Max Qty</label>
                            <input type="number" min="0" class="form-control maxqty"
                                   name="level[maxqty]" placeholder="max quantity" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm confirm-btn">Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script src="assets/js/tableToExcel.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#brandid,#categoryid,#subcategoryid,#stockloc').select2({width: '100%'});

        $('#reorder-level-table').DataTable({
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

    $('#edit-level-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        //clear
        $(modal).find('.stockid,.productname,.location,.minqty,.maxqty').val('');


        $(modal).find('.stockid').val(source.data('stockid'));
        $(modal).find('.productname').val(source.data('productname'));
        $(modal).find('.location').val(source.data('locname'));
        $(modal).find('.minqty').val(source.data('minqty'));
        $(modal).find('.maxqty').val(source.data('maxqty'));
    });

    function exportExcel(e) {
        let table = document.getElementById("reorder-export-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `<?=$title?> Reorder Level.xlsx`, // fileName you could use any name
            sheet: {
                name: 'LEVELS' // sheetName
            }
        });
    }
</script>
