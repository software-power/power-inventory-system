<header class="page-header">
    <h2>Stock Transfer</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="transfer_detailed_report">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            From Location:
                            <select class="form-control locationid" name="search[fromlocation]">
                                <option value="" selected>--From Location--</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            To Location:
                            <select class="form-control locationid" name="search[tolocation]">
                                <option value="" selected>--To Location--</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control" name="search[productid]">

                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Transfer No:
                            <input type="text" name="search[transferno]" placeholder="Transfer No"
                                   class="form-control">
                        </div>
                        <div class="col-md-4">
                            Batch No:
                            <input type="text" name="search[batchno]" placeholder="Batch No"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="search[fromdate]" value="<?= $fromdate ?>"
                                   class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="search[todate]" value="<?= $todate ?>"
                                   class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-danger btn-block" data-dismiss="modal"><i
                                        class="fa fa-search"></i>
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


<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">Stock Transfer Detailed Report</h2>
                    <p>Filter: <span class="text-primary"><?= $title ?></span></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <button type="button" data-toggle="modal" data-target="#search-modal" class="btn"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Transfer No</th>
                        <th>From Location</th>
                        <th>To Location</th>
                        <th>Product</th>
                        <th>Batch No</th>
                        <th>Qty</th>
                        <th>Transfer By</th>
                        <th>Transfer Date</th>
                        <th>Approved By</th>
                        <th>Approval Date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($transfers as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td>
                                <a target="_blank"
                                   href="<?= url('stocks', 'transfer_print', 'transferno=' . $R['id']) ?>"
                                   title="Print Transfer"> <?= $R['id'] ?></a>
                            </td>
                            <td><?= $R['fromlocation'] ?></td>
                            <td><?= $R['tolocation'] ?></td>
                            <td><?= $R['productname'] ?></td>
                            <td><?= $R['track_expire_date'] == 1 ? $R['batch_no'] : '-' ?></td>
                            <td><?= $R['qty'] ?></td>
                            <td><?= $R['transferby'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td class="<?= $R['approver'] ?? 'text-danger' ?>"><?= $R['approver'] ?? 'Not approved' ?></td>
                            <td class="<?= $R['approver'] ?? 'text-danger' ?>"><?= $R['approver'] ? fDate($R['doa']) : 'Not approved' ?></td>
                            <td></td>
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
        initSelectAjax('.locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
    });
</script>