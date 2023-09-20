<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="serialnos">
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
                    </div>
                    <div class="row">
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
                        <? if (STOCK_LOCATIONS) { ?>
                            <div class="col-md-4">
                                Initial Location:
                                <select id="initlocationid" class="form-control" name="initlocationid">
                                    <option value="">-- initial location --</option>
                                    <? foreach ($branchLocations as $R) { ?>
                                        <option value="<?= $R['id'] ?>"><?= $R['name'] ?> - <?= $R['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Status:
                            <select name="status" class="form-control">
                                <option value="">-- All --</option>
                                <option value="sold">Sold</option>
                                <option value="used_manufacture">Used in Manufacture</option>
                                <option value="in_stock">In stock</option>
                            </select>
                        </div>
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
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <a id="openModel" class="btn btn-default" href="#search-modal" data-toggle="modal" title="Filter"> <i
                                class="fa fa-search"></i> Open filter </a>
                    <a class="btn btn-default" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Serial No report</h2>
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
                            <th>Current Location</th>
                            <th>Serial no Source</th>
                            <th>Source Location</th>
                            <th>Created by</th>
                            <th>Created on</th>
                            <th>Status</th>
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
                                <td><?= $sno['current_location'] ?> - <?= $sno['current_branch'] ?></td>
                                <td><?= ucfirst($sno['source']) ?></td>
                                <td><?= $sno['initial_location'] ?> - <?= $sno['initial_branch'] ?></td>
                                <td><?= $sno['creator'] ?></td>
                                <td><?= fDate($sno['doc'], 'd M Y H:i') ?></td>
                                <td>
                                    <? if ($sno['sdi']) { ?>
                                        <span class="text-danger">Sold</span>
                                    <? } ?>
                                    <? if ($sno['smdi']) { ?>
                                        <span class="text-danger">Used in manufacture</span>
                                    <? } ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm" title="view serialno history"
                                            data-target="#serialno-history-modal" data-toggle="modal" data-snoid="<?= $sno['id'] ?>"
                                            data-number="<?= $sno['number'] ?>">
                                        <i class="fa fa-history"></i> view history
                                    </button>
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

<div class="modal fade" id="serialno-history-modal" role="dialog" aria-labelledby="serialno-history-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Serial No: <span class="text-primary serialno"></span></h4>
            </div>
            <div class="modal-body">
                <p>Serial Number History:</p>
                <table class="table table-condensed" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Voucher Type</th>
                        <th>Voucher No</th>
                        <th>Location</th>
                        <th>Supplier</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody class="history"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="reset" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "choose product");
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

        $('#serialno-history-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            let snoid = source.data('snoid');
            $(modal).find('.serialno').text(source.data('number'));

            $.get(`?module=serialnos&action=getSerialnoHistory&format=json&snoid=${snoid}`, null, function (data) {
                let result = JSON.parse(data);
                $(modal).find('tbody.history').empty();

                if (result.status === 'success') {
                    let count = 1;
                    $.each(result.data, function (i, item) {
                        let row = `<tr>
                                        <td>${count}</td>
                                        <td><a href="${item.url}" target="_blank">${item.voucher_type}</a></td>
                                        <td>${item.voucher_no}</td>
                                        <td>${item.locationname}</td>
                                        <td>${item.suppliername}</td>
                                        <td>${item.issue_date}</td>
                                    </tr>`;
                        $(modal).find('tbody.history').append(row);
                        count++;
                    });
                } else {
                    triggerError(result.msg || 'Error found');
                }
            });
        });
    });

</script>
