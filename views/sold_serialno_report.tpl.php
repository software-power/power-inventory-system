<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="sold_serialnos">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Serial No:
                            <input type="text" class="form-control" name="number" placeholder="serial no" minlength="2">
                        </div>
                        <div class="col-md-4">
                            Invoice No:
                            <input type="text" class="form-control" name="invoiceno" placeholder="invoice no" minlength="2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Sales Person</label>
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" class="form-control" name="userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            Current Location:
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="locationid" class="form-control" name="locationid">
                                    <? foreach ($branchLocations as $R) { ?>
                                        <option <?= selected($location['id'], $R['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?>
                                            - <?= $R['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                <input type="hidden" class="form-control" name="locationid" value="<?= $location['id'] ?>">
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
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" value="<?= $fromdate ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" value="<?= $todate ?>" class="form-control">
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

<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <a id="openModel" class="btn btn-default" href="#search-modal" data-toggle="modal" title="Filter"> <i
                                class="fa fa-search"></i> Open filter </a>
                    <a class="btn btn-default" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Sold Serial Nos</h2>
            </header>
            <div class="panel-body">
                <h5>Filter: <span class="text-primary"><?= $title ?></span></h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:10pt;" id="history-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>SNO</th>
                            <th>Product</th>
                            <th>Invoice no</th>
                            <th>Sales Person</th>
                            <th>Sales date</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($serialnos as $sno) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $sno['number'] ?></td>
                                <td><?= $sno['productname'] ?></td>
                                <td><?= $sno['invoiceno'] ?></td>
                                <td><?= $sno['salesperson'] ?></td>
                                <td><?= fDate($sno['dos'], 'd M Y H:i') ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('serialnos', 'print_warranty_sticker', 'snoid=' . $sno['id']) ?>"
                                               title="Payment List"><i class="fa fa-print"></i> Print warranty sticker</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "choose product");
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'Choose sales person', 2);
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
