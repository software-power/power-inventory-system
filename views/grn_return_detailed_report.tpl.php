<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="grn_return_detailed_report">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="locationid" class="form-control" name="search[locationid]">
                                    <? foreach ($branchLocations as $R) { ?>
                                        <option <?= selected($location['id'], $R['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?>
                                            - <?= $R['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                <input type="hidden" class="form-control" name="search[locationid]" value="<?= $location['id'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            <select id="supplierid" class="form-control" name="search[supplierid]">
                                <option value="" selected disabled>Select Supplier</option>
                                <? foreach ($suppliers as $ID => $S) { ?>
                                    <option value="<?= $S['id'] ?>"><?= $S['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Return Number" name="search[returnid]">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="GRN Number" name="search[grn]">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Batch No" name="search[batchno]">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control" name="search[productid]">

                            </select>
                        </div>
                        <div class="col-md-4">
                            From: <span class="text-danger">*</span>
                            <input type="date" name="search[from]" class="form-control for-input" value="<?=$fromdate?>">
                        </div>
                        <div class="col-md-4">
                            To: <span class="text-danger">*</span>
                            <input type="date" name="search[to]" class="form-control for-input" value="<?=$todate?>">
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

<header class="page-header">
    <h2>GRN Return Detailed Report</h2>
</header>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">GRN Return Detailed Report</h2>
                    <p class="text-primary"><?= $title ?></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <button type="button" class="btn" data-toggle="modal" data-target="#search-modal"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="forCheckbox table table-hover mb-none" style="font-size: 10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Return No</th>
                        <th>GRN No</th>
                        <th>Supplier Name</th>
                        <th>Product</th>
                        <th>Batch No</th>
                        <th>Expire date</th>
                        <th>Returned qty</th>
                        <th>Issued By</th>
                        <th>Issued date</th>
                    </tr>
                    </thead>
                    <tbody>

                    <? $count = 1;
                    foreach ($returnList as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td>
                                <a target="_blank"
                                   href="<?= url('grns', 'grn_return_print', 'returnid=' . $R['id']) ?>"
                                   title="Print Transfer"> <?= $R['id'] ?></a>
                            </td>
                            <td><?= $R['grnid'] ?></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td><?= $R['productname'] ?></td>
                            <td><?= $R['track_expire_date'] == 1 ? $R['batch_no'] : '-' ?></td>
                            <td><?= $R['track_expire_date'] == 1 ? $R['expire_date'] : '-' ?></td>
                            <td><?= $R['qty'] ?></td>
                            <td><?= $R['username'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                        </tr>
                        <? $count++;
                    } ?>
                    <input type="hidden" id="count" value="<?= $count ?>"/>
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
        $('#locationid,#supplierid').select2({width: '100%'});

    });
</script>
