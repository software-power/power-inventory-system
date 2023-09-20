<style>
    .panel-actions-grn {
        right: 15px;
        position: absolute;
        top: 15px;
    }

    .dropleft .dropdown-menu {
        top: 0;
        right: 100%;
        left: auto;
        margin-top: 0;
        margin-right: .125rem;
    }

    a.dropdown-item {
        text-decoration: none;
    }

    .dropdown-item {
        display: block;
        width: 100%;
        padding: .25rem 1.5rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }
</style>


<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Search</h4>
            </div>
            <form>
                <input type="hidden" name="module" value="grns">
                <input type="hidden" name="action" value="lpo_list">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select class="form-control" name="search[branchid]">
                                <option value="" selected>All Branch</option>
                                <? foreach ($branches as $key => $R) { ?>
                                    <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Supplier:
                            <select id="supplier" class="form-control" name="search[supplierid]">
                                <? if ($supplier) { ?>
                                    <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="location" class="form-control" name="search[locationid]">
                                <? if ($supplier) { ?>
                                    <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            Issued by:
                            <?if(Users::can(OtherRights::approve_other_lpo)){?>
                                <select id="userid" class="form-control" name="search[createdby]"></select>
                            <?}else{?>
                                <input type="hidden" name="search[createdby]" value="<?=$creator['id']?>">
                                <input type="text" readonly class="form-control" value="<?=$creator['name']?>">
                            <?}?>
                        </div>
                        <div class="col-md-4">
                            Lpo:
                            <input type="text" class="form-control" placeholder="LPO Number" name="search[lpo]">
                        </div>
                        <div class="col-md-4">
                            Grn No:
                            <input type="text" class="form-control" placeholder="GRN Number" name="search[grn]">
                        </div>
                    </div>
                    <div class="row">
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
                            <input type="date" name="search[from]" value="<?= $fromdate ?>"
                                   class="form-control for-input">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="search[to]" value="<?= $todate ?>" class="form-control for-input">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<header class="page-header">
    <h2>LPO List</h2>
</header>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions-grn">
                <button class="btn" data-toggle="modal" data-target="#search-modal"><i
                            class="fa fa-search"></i> Open filter
                </button>
                <? if (Users::can(OtherRights::add_lpo)) { ?>
                    <a href="?module=grns&action=lpo_process" class="btn grn-list">
                        <i class="fa fa-plus"></i> Create LPO</a>
                <? } ?>
            </div>
            <h2 class="panel-title">LPO List</h2>
            <p class="text-primary"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="forCheckbox table table-hover mb-none" style="font-size: 10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>LPO #</th>
                        <th class="text-center">GRN #</th>
                        <th>Currency</th>
                        <th>Supplier Name</th>
                        <th>Full Amount</th>
                        <th>Total Amount</th>
                        <th>Total VAT Amount</th>
                        <th>Issued By</th>
                        <th>Issued date</th>
                        <th>Approve Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <? $count = 1;
                    $USER_CAN_APPROVE = Users::can(OtherRights::approve_lpo) || Users::can(OtherRights::approve_other_lpo);
                    $USER_CAN_ADD_GRN = Users::can(OtherRights::add_grn);
                    $USER_CAN_EDIT_LPO = Users::can(OtherRights::edit_lpo);
                    foreach ($lpolist as $id => $R) { ?>
                        <tr id="asset<?= $count ?>">
                            <td><?= $count ?></td>
                            <td><?= $R['lponumber'] ?></td>
                            <td class="text-center">
                                <? if ($R['grnnumber']) { ?>
                                    <p class="m-none" title="view transfer">
                                        <a target="_blank"
                                           href="<?= url('grns', 'print_grn', ['grn' => $R['grnnumber']]) ?>"><?= $R['grnnumber'] ?></a>
                                    </p>
                                    <span class="badge bg-default text-success text-xs">processed</span>
                                <? } ?>
                            </td>
                            <td><strong><?= $R['currency_name'] ?></strong></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td>
                                <p><?= formatN($R['full_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <span class="text-xs text-muted">
                                        <?= $baseCurrency['name'] ?> <?= formatN($R['base_full_amount']) ?>
                                    </span>
                                <? } ?>
                            </td>
                            <td>
                                <p><?= formatN($R['total_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <span class="text-xs text-muted">
                                        <?= $baseCurrency['name'] ?> <?= formatN($R['base_total_amount']) ?>
                                    </span>
                                <? } ?>
                            </td>
                            <td>
                                <p><?= formatN($R['grand_vatamount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <span class="text-xs text-muted">
                                        <?= $baseCurrency['name'] ?> <?= formatN($R['base_grand_vatamount']) ?>
                                    </span>
                                <? } ?>
                            </td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['issuedate']) ?></td>
                            <td>
                                <? if ($R['status'] != 'active') { ?>
                                    <p class="text-rosepink">Canceled</p>
                                <? } else if ($R['approver']) { ?>
                                    <? if ($R['auto_approve']) { ?>
                                        <p class="text-success">Auto approved</p>
                                    <? } else { ?>
                                        <p class="text-success"><small><?= $R['approver'] ?> <?= fDate($R['approval_date']) ?></small></p>
                                    <? } ?>
                                <? } else { ?>
                                    <p class="text-muted">not approved</p>
                                <? } ?>
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <? if (!$R['approver'] && $R['status'] == 'active' && $USER_CAN_APPROVE) { ?>
                                            <a class="dropdown-item" href="#approve-modal" data-toggle="modal"
                                               title="Approve LPO" data-id="<?= $R['lponumber'] ?>"
                                               data-suppliername="<?= $R['suppliername'] ?>"
                                               data-currency="<?= $R['currency_name'] ?>"
                                               data-exchange-rate="<?= $R['currency_amount'] ?>"
                                               data-issuedby="<?= $R['issuedby'] ?>"
                                               data-excamount="<?= formatN($R['total_amount']) ?>"
                                               data-vatamount="<?= formatN($R['grand_vatamount']) ?>"
                                               data-incamount="<?= formatN($R['full_amount']) ?>"
                                               data-issuedate="<?= fDate($R['issuedate']) ?>">
                                                <i class="fa fa-check-circle-o text-success"></i> Approve</a>
                                        <? } ?>
                                        <? if (!$R['grnnumber'] && $R['approver'] && $R['status'] == 'active' && $USER_CAN_APPROVE) { ?>
                                            <form action="<?= url('grns', 'disapprove_lpo') ?>" class="ml-xs" style="margin: 0;" method="post"
                                                  onsubmit="return confirm('Do you want to disapprove this LPO!')">
                                                <input type="hidden" class="lpoid" name="lpoid" value="<?=$R['lponumber']?>">
                                                <button class="dropdown-item" title="Disapprove LPO"><i class="fa fa-close"></i> Disapprove LPO</button>
                                            </form>
                                        <? } ?>
                                        <? if (!$R['grnnumber'] && $R['approver'] && $R['status'] == 'active' && $USER_CAN_ADD_GRN) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('grns', 'grn_full_edit', 'lpoid=' . $R['lponumber']) ?>"
                                               title="Issue GRN"><i class="fa fa-angle-right"></i> Make GRN</a>
                                        <? } ?>
                                        <?if($R['status'] != 'active' && $USER_CAN_APPROVE){?>
                                            <form action="<?= url('grns', 'cancel_lpo') ?>" class="ml-xs" style="margin: 0;" method="post"
                                                  onsubmit="return confirm('Do you want to restore this LPO!')">
                                                <input type="hidden" class="lpoid" name="lpoid" value="<?=$R['lponumber']?>">
                                                <input type="hidden" name="revive">
                                                <button class="dropdown-item" title="Restore LPO"><i class="fa fa-recycle"></i> Restore</button>
                                            </form>
                                        <?}?>
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('grns', 'view_lpo', 'lpo=' . $R['lponumber']) ?>"
                                           title="View LPO"><i class="fa fa-list"></i> View</a>
                                        <? if (!$R['grnnumber'] && !$R['approver'] && $R['status'] == 'active' && $USER_CAN_EDIT_LPO) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('grns', 'lpo_process', 'lpoid=' . $R['lponumber']) ?>"
                                               title="View LPO"><i class="fa fa-pencil"></i> Edit</a>
                                        <? } ?>
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('grns', 'print_lpo', 'lpo=' . $R['lponumber']) ?>"
                                           title="Print LPO"><i class="fa fa-print"></i> LPO print</a>
                                    </div>
                                </div>
                            </td>
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

<div class="modal fade" id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="approve-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="modal-title">Approve LPO No: <span class="lpono text-primary"></span></h4>
                    <div class="d-flex mr-md">
                        <form action="<?= url('grns', 'approve_lpo') ?>" method="post"
                              style="margin: 0;">
                            <input type="hidden" class="lpoid" name="lpoid">
                            <button class="btn btn-success btn-sm" title="Approve LPO">Approve</button>
                        </form>
                        <form class="ml-xs" style="margin: 0;">
                            <input type="hidden" name="module" value="grns">
                            <input type="hidden" name="action" value="lpo_process">
                            <input type="hidden" class="lpoid" name="lpoid">
                            <button class="btn btn-primary btn-sm" title="Edit LPO">Edit</button>
                        </form>
                        <? if ($USER_CAN_APPROVE) { ?>
                            <form action="<?= url('grns', 'cancel_lpo') ?>" class="ml-xs" style="margin: 0;" method="post"
                                  onsubmit="return confirm('Do you want to cancel this LPO!')">
                                <input type="hidden" class="lpoid" name="lpoid">
                                <button class="btn btn-danger btn-sm" title="Cancel LPO">Cancel</button>
                            </form>
                        <? } ?>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div id="spinner-holder" style="display: none">
                    <div class="d-flex justify-content-center align-items-center">
                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="100"
                                width="100"></object>
                        <span class="ml-sm">Loading ...</span>
                    </div>
                </div>
                <div id="content-holder">
                    <p>Supplier: <span class="suppliername text-primary"></span></p>
                    <p>Date: <span class="issuedate text-primary"></span></p>
                    <p>Issued By: <span class="issuedby text-primary"></span></p>
                    <p>Currency: <span class="currency text-primary"></span></p>
                    <p>Exchange Rate: <span class="exchange_rate text-primary"></span></p>
                    <table class="table table-bordered mt-xl" style="font-size: 9pt">
                        <thead>
                        <tr>
                            <td>#</td>
                            <td>Product</td>
                            <td>Qty</td>
                            <td align="right">Rate</td>
                            <td align="right">Purchase Cost</td>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-5 col-md-offset-7">
                            <div class="d-flex justify-content-between">
                                <span>Exclusive Amount</span>
                                <span class="text-weight-bold excamount"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>VAT Amount</span>
                                <span class="text-weight-bold vatamount"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Inclusive Amount</span>
                                <span class="text-weight-bold incamount"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default btn-sm"
                        data-dismiss="modal">Close
                </button>
            </div>
        </div>
    </div>
</div>


<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#location', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        initSelectAjax('#supplier', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);

        $('#approve-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let lpoid = source.data('id');
            let modal = $(this);
            $(modal).find('.lpono').text(lpoid);
            $(modal).find('.lpoid').val(lpoid);
            $(modal).find('.suppliername').text(source.data('suppliername'));
            $(modal).find('.issuedate').text(source.data('issuedate'));
            $(modal).find('.issuedby').text(source.data('issuedby'));
            $(modal).find('.currency').text(source.data('currency'));
            $(modal).find('.exchange_rate').text(source.data('exchange-rate'));
            $(modal).find('.excamount').text(source.data('excamount'));
            $(modal).find('.vatamount').text(source.data('vatamount'));
            $(modal).find('.incamount').text(source.data('incamount'));

            getLPODetails(lpoid, modal);
        });
    });

    function getLPODetails(lpoid, modal) {
        $(modal).find('tbody').empty();
        $.get(`?module=stocks&action=getLPODetails&format=json&lponumber=${lpoid}`, null, function (data) {
            let result = JSON.parse(data);
            console.log(result);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.data.details, function (i, item) {
                    let row = `<tr>
                                   <td>${count}</td>
                                   <td>${item.productname}</td>
                                   <td>${item.qty}</td>
                                   <td align="right">${numberWithCommas(item.rate)}</td>
                                   <td align="right">${numberWithCommas(item.incamount)}</td>
                               </tr>`;
                    $(modal).find('tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || 'Error', 5000);
                $(modal).modal('hide');
            }
        });

    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
