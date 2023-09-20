<header class="page-header">
    <h2>Stock Adjustment List</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= url('stocks', 'stock_adjustment_list') ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select class="form-control branch" name="search[branchid]">
                                <option value="" selected>-- Branch --</option>
                                <? foreach ($branches as $index => $branch) { ?>
                                    <option value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select class="form-control locationid" name="search[locationid]">
                                <option value="" selected>--Location--</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Adjustment No:
                            <input type="text" name="search[adjustmentno]" class="form-control" placeholder="adjustment no">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="search[fromdate]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="search[todate]" class="form-control">
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
                    <h2 class="panel-title">Stock Adjustment List</h2>
                    <p>Filter: <span class="text-primary"><?= $title ?></span></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <button type="button" data-toggle="modal" data-target="#search-modal" class="btn"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                    <? if (Users::can(OtherRights::adjust_stock)) { ?>
                        <a href="?module=stocks&action=make_adjustment" class="btn">
                            <i class="fa fa-plus"></i> Make Adjustment</a>
                    <? } ?>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Adjustment No</th>
                        <th>Location</th>
                        <th>Product Count</th>
                        <th>Issued By</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($adjustments as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['id'] ?></td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td><?= $R['productCount'] ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['doc'],'d M Y H:i') ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('stocks', 'adjustment_print', 'adjustmentno=' . $R['id']) ?>"
                                           title="Print"><i class="fa fa-print"></i> Print</a>
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
        initSelectAjax('.locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
    });
</script>