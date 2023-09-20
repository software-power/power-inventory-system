<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>


<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="product_history">
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
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="locationid" class="form-control" name="locationid">
                                    <? foreach ($branchLocations as $R) { ?>
                                        <option <?= selected($location['id'], $R['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?>
                                            - <?= $R['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                <input type="hidden" class="form-control" name="stocklocation" value="<?= $location['id'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control" name="productid">
                                <? if ($product) { ?>
                                    <option value="<?= $product['id'] ?>" selected><?= $product['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Batch No:
                            <input type="text" class="form-control" name="batch_no" placeholder="batch no">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" value="<?= $fromdate ?>"
                                   class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" value="<?= $todate ?>"
                                   class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-danger btn-block" data-dismiss="modal"><i
                                        class="fa fa-close"></i>
                                CANCEL
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-success btn-block"><i
                                        class="fa fa-refresh"></i>
                                RESET
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block"><i
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

<div class="center-panel">
    <section class="panel">
        <header class="panel-heading">
            <div class="panelControl">
                <a id="openModel" class="btn" href="#search-modal" data-toggle="modal" title="Filter"> <i
                            class="fa fa-search"></i> Open filter </a>
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
            </div>
            <h2 class="panel-title">Product stock history report</h2>
        </header>
        <div class="panel-body">
            <h5>Filter: <span class="text-primary"><?= $title ?></span></h5>
            <h4>Opening Balance: <span
                        class="text-weight-bold <?= $opening_balance <= 0 ? 'text-danger' : 'text-success' ?>"><?= $opening_balance ?></span>
            </h4>
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:13px;" id="history-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>DATE</th>
                        <th>VOUCHER TYPE</th>
                        <th>VOUCHER NO.</th>
                        <th>ACTION</th>
                        <th>BATCH NO</th>
                        <th>QTY</th>
                        <th>BALANCE</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="font-size: 12pt;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Opening Balance</td>
                        <td class="text-weight-bold <?= $opening_balance <= 0 ? 'text-danger' : 'text-success' ?>"><?= $opening_balance ?></td>
                        <td></td>
                    </tr>
                    <? $count = 1;
                    foreach ($history as $index => $h) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $h['action_date'] ?></td>
                            <td class="text-capitalize">
                                <a href="<?= $h['voucher_url'] ?>" target="_blank"><?= $h['voucher'] ?></a>
                            </td>
                            <td><?= $h['voucherno'] ?></td>
                            <td><?= strtoupper($h['action']) ?></td>
                            <td><?= $product['track_expire_date'] ? $h['batch_no'] : '-' ?></td>
                            <td class="text-weight-bold <?= $h['action'] == 'in' ? 'text-success' : 'text-danger' ?>">
                                <?= $h['action'] == 'in' ? '+' : '-' ?><?= $h['qty'] ?>
                            </td>
                            <td><?= $h['balance'] ?></td>
                            <td></td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                    <tfoot>
                    <tr style="font-size: 12pt">
                        <td colspan="7"
                            class="text-right text-primary text-weight-bold">Current balance
                        </td>
                        <td class="text-weight-bold <?= $current_balance <= 0 ? 'text-danger' : 'text-success' ?>"><?= $current_balance ?></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>
<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json&non_stock=no",'choose product');
        $('#locationid').select2({width: '100%'});
        $('#history-table').DataTable({
            dom: '<"top"fBl>t<"bottom"ip>',
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

</script>
