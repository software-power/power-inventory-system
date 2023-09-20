<header class="page-header">
    <h2>Stock Transfer List</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="stocks">
                <input type="hidden" name="action" value="transfer_list">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Transfer No:
                            <input type="text" name="search[transferno]" placeholder="Transfer No" autocomplete="off"
                                   class="form-control">
                        </div>
                    </div>
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
                            Issued by:
                            <? if (Users::can(OtherRights::approve_other_transfer) || Users::can(OtherRights::view_all_transfer)) { ?>
                                <select id="userid" class="form-control" name="search[createdby]"></select>
                            <? } else { ?>
                                <input type="hidden" name="search[createdby]" value="<?= $creator['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $creator['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Status:
                            <select class="form-control" name="search[status]">
                                <option value="" selected>-- All --</option>
                                <option value="approved">Approved</option>
                                <option value="not-approved">Not approved</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>
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
                    <h2 class="panel-title">Stock Transfer List</h2>
                    <p>Filter: <span class="text-primary"><?= $title ?></span></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <button type="button" data-toggle="modal" data-target="#search-modal" class="btn"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                    <? if (!CS_TRANSFER_REQUIRE_REQUISITION && Users::can(OtherRights::transfer_stock)) { ?>
                        <a href="?module=stocks&action=issue_transfer" class="btn">
                            <i class="fa fa-plus"></i> Transfer Stock</a>
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
                        <th>Transfer No</th>
                        <th>Req No</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Products</th>
                        <th>Transfer Cost (<?= $basecurrency['name'] ?>)</th>
                        <th>Issued By</th>
                        <th>Issue Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    $CAN_APPROVE_OTHER_TRANSFER = Users::can(OtherRights::approve_other_transfer);
                    $CAN_APPROVE_TRANSFER = Users::can(OtherRights::approve_transfer);

                    foreach ($transfers as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['id'] ?></td>
                            <td><?= $R['reqid'] ?? '-' ?></td>
                            <td><?= $R['fromlocation'] ?> - <?= $R['frombranchname'] ?></td>
                            <td><?= $R['tolocation'] ?> - <?= $R['tobranchname'] ?></td>
                            <td><?= $R['productCount'] ?></td>
                            <td><?= formatN($R['transfer_cost']) ?></td>
                            <td><?= $R['transferby'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td>
                                <? if ($R['status'] != 'active') { ?>
                                    <p class="text-rosepink">Canceled</p>
                                <? } else if ($R['approver']) { ?>
                                    <? if ($R['auto_approve']) { ?>
                                        <p class="text-success">Auto approved</p>
                                    <? } else { ?>
                                        <p class="text-success"><small>Approved by: <?= $R['approver'] ?>
                                                , <?= fDate($R['doa'], 'd M Y H:i') ?></small></p>
                                    <? } ?>
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
                                        <? if (!$R['approver'] && $R['status'] == 'active' && ($CAN_APPROVE_OTHER_TRANSFER || ($CAN_APPROVE_TRANSFER && $R['createdby'] == $_SESSION['member']['id']))) { ?>
                                            <a class="dropdown-item" href="<?= url('stocks', 'approve_transfer', ['transferno' => $R['id']]) ?>">
                                                <i class="fa fa-check-circle-o text-success"></i> Approve Transfer</a>
                                        <? } ?>
                                        <? if ($R['status'] != 'active' &&  ($CAN_APPROVE_OTHER_TRANSFER || ($CAN_APPROVE_TRANSFER && $R['createdby'] == $_SESSION['member']['id']))) { ?>
                                            <form action="<?= url('stocks', 'cancel_transfer') ?>" class="ml-xs" style="margin: 0;" method="post"
                                                  onsubmit="return confirm('Do you want to restore this transfer!')">
                                                <input type="hidden" class="lpoid" name="transferno" value="<?= $R['id'] ?>">
                                                <input type="hidden" name="revive">
                                                <button class="dropdown-item" title="Restore Transfer"><i class="fa fa-recycle"></i> Restore</button>
                                            </form>
                                        <? } ?>
                                        <a class="dropdown-item"
                                           href="<?= url('stocks', 'transfer_view', 'transferno=' . $R['id']) ?>"
                                           title="View Transfer"><i class="fa fa-list text-primary"></i> View Transfer</a>
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('stocks', 'transfer_print', 'transferno=' . $R['id']) ?>"
                                           title="Print Transfer"><i class="fa fa-print text-primary"></i> Print</a>
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
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
    });
</script>