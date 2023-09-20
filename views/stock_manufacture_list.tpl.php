<header class="page-header">
    <h2>Stock Manufacture List</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="stocks">
                <input type="hidden" name="action" value="<?= $action ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Manufacture No:
                            <input type="text" name="manufactureno" class="form-control" placeholder="manufacture no">
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="locationid" class="form-control" name="locationid">
                                <option value="" selected>--All Location--</option>
                                <? foreach ($locations as $l) { ?>
                                    <option value="<?= $l['id'] ?>"><?= $l['name'] ?> - <?= $l['branchname'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Issued by:
                            <select id="userid" class="form-control" name="createdby"></select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                        <button type="reset" class="btn btn-default" data-dismiss="modal"><i class="fa fa-search"></i> Cancel</button>
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
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="panel-title">Stock Manufacture List</h2>
                        <p>Filter: <span class="text-primary"><?= $title ?></span></p>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <button type="button" data-toggle="modal" data-target="#search-modal" class="btn btn-default "><i
                                    class="fa fa-search"></i> Open filter
                        </button>
                        <? if (Users::can(OtherRights::manufacture_stock)) { ?>
                            <a href="<?= url('stocks', 'manufacture_stock') ?>" class="btn btn-default ml-sm">
                                <i class="fa fa-plus"></i> Manufacture Stock</a>
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
                            <th>Manufacture No</th>
                            <th>Location</th>
                            <th>Issued By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($manufactures as $id => $R) { ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $R['id'] ?></td>
                                <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                                <td><?= $R['issuedby'] ?></td>
                                <td><?= fDate($R['doc'], 'd M Y H:i') ?></td>
                                <td>
                                    <? if ($R['manufacture_status'] == 'canceled') { ?>
                                        <p class="text-rosepink">Canceled</p>
                                    <? } else if ($R['manufacture_status'] == 'approved') { ?>
                                        <p class="text-success">
                                            <small>Approved by: <?= $R['approver'] ?> , <?= fDate($R['approvedate'], 'd M Y H:i') ?></small>
                                        </p>
                                    <? } else { ?>
                                        <p class="text-muted">not approved</p>
                                    <? } ?>
                                </td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="<?= url('stocks', 'view_manufacture', 'manufactureno=' . $R['id']) ?>"
                                               title="View"><i class="fa fa-list"></i> View</a>
                                            <? if ($R['manufacture_status'] == 'approved') { ?>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('stocks', 'manufacture_print', 'manufactureno=' . $R['id']) ?>"
                                                   title="Print"><i class="fa fa-print"></i> Print</a>
                                            <? } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#userid', '?module=users&action=getUser&format=json', 'choose user');
        $('#locationid').select2({width: '100%'});
    });
</script>