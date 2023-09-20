<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= url('grns', 'grn_return_list') ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <select id="brabchid" class="form-control" name="search[branchid]">
                                <option value="" selected>--Branch--</option>
                                <? foreach ($branches as $key => $R) { ?>
                                    <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="locationid" class="form-control" name="search[locationid]">
                                <option value="" selected>--Location--</option>
                            </select>
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
                            <input type="text" class="form-control" placeholder="LPO Number" name="search[lpo]">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="GRN Number" name="search[grn]">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From: <span class="text-danger">*</span>
                            <input type="date" name="search[from]" placeholder="LPO Date" value="<?= $fromdate ?>"
                                   class="form-control for-input">
                        </div>
                        <div class="col-md-4">
                            To: <span class="text-danger">*</span>
                            <input type="date" name="search[to]" placeholder="LPO Date" value="<?= $todate ?>"
                                   class="form-control for-input">
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
    <h2>GRN Return List</h2>
</header>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">GRN Return List</h2>
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
                        <th>Product Count</th>
                        <th>Issued By</th>
                        <th>Issued date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($returnList as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['id'] ?></td>
                            <td><?= $R['grnid'] ?></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td><?= $R['productCount'] ?></td>
                            <td><?= $R['username'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('grns', 'grn_return_print', 'returnid=' . $R['id']) ?>"
                                           title="Print Transfer"><i class="fa fa-print"></i> Print</a>
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
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
    });
</script>
