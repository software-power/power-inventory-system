<style media="screen">
    .panel-actions a, .panel-actions .panel-action {
        font-size: 21px;
    }

    form table tr td {
        padding-left: 10px;
    }

    .for-column {
        width: 100px;
    }

    .for-btn {
        padding: 16px;
        display: block;
    }

    .for-holder {
        height: 0px;
        overflow: hidden;
        transition: .3s;
        background: white;
    }

    .for-view-filter {
        height: 165px;
        padding: 10px;
    }

    .btn-align {
        float: right;
        position: relative;
        top: -25px;
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

    .badge-red {
        background-color: #d2322d;
    }

    .badge-orange {
        background-color: orange;
    }

    .badge-green {
        background-color: #47a447;
    }

    .badge-primary {
        background-color: #2885e2;
    }

    .table-responsive {
        min-height: 150px;
    }

    .panel-body .badge {
        border-radius: unset;
        width: 100%;
        font-weight: 400;
    }
</style>
<header class="page-header">
    <h2>Recurring Bills</h2>
</header>


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
                <input type="hidden" name="module" value="recurring_bills">
                <input type="hidden" name="action" value="<?= $action ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Client</label>
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Issued by</label>
                            <? if (IS_ADMIN) { ?>
                                <select id="userid" class="form-control" name="issuedby"></select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            <label for="">Bill Status</label>
                            <select class="form-control" name="bill_status">
                                <option value="" selected>--Choose Status--</option>
                                <option value="active">Active</option>
                                <option value="inactive">Stopped</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <label for="">By billing</label>
                            <select class="form-control" name="billmonth">
                                <option <?= selected($billmonth, "all") ?> value="all">-- All bills --</option>
                                <option <?= selected($billmonth, "thismonth") ?> value="thismonth">This Month bills</option>
                                <option <?= selected($billmonth, "nextmonth") ?> value="nextmonth">Next Month bills</option>
                                <option <?= selected($billmonth, "stopped") ?> value="stopped">Stopped bills</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-11">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="panel-title">Recurring Bills</h2>
                    <h5 class="text-primary"><?= $title ?></h5>
                </div>
                <div class="d-flex">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search-modal"><i class="fa fa-search"></i> Search
                    </button>
                    <? if (Users::can(OtherRights::create_bill)) { ?>
                        <a href="<?= url('recurring_bills', 'create_bill') ?>" class="btn btn-default ml-xs"><i class="fa fa-plus"></i> Create
                            Bill</a>
                    <? } ?>
                    <a href="?module=home&action=index" class="btn btn-default ml-xs"><i class="fa fa-home"></i> Home</a>
                </div>
            </header>
            <div class="panel-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-default" onclick="exportExcel()">Export Excel</button>
                    </div>
                    <form id="bill-selected-form" action="<?= url('recurring_bills', 'bill_clients') ?>" method="post"
                          class="m-none p-none" onsubmit="return check_items()">
                        <div class="d-flex align-items-center">
                            <object class="save-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"
                                    style="display: none"></object>
                            <button class="btn btn-success">Bill Selected</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:10pt">
                        <thead>
                        <tr>
                            <th>
                                <label class="d-flex justify-content-center align-items-center">
                                    <input type="checkbox" style="height: 20px;width: 20px;" onchange="check_all(this)">
                                    <span class="text-weight-semibold mt-xs ml-xs">Check all</span>
                                </label>
                            </th>
                            <th>Bill No.</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Issued By</th>
                            <th>Issued Date</th>
                            <th>Currency</th>
                            <th>Bill Amount</th>
                            <th>Bill Type</th>
                            <th>Bill Interval</th>
                            <th>Billing start month</th>
                            <th>Last bill month</th>
                            <th>Next bill month</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        $USER_CAN_EDIT = Users::can(OtherRights::edit_bill);
                        foreach ($bills as $index => $R) { ?>
                            <tr>
                                <td>
                                    <? if ($R['status'] == 'active') { ?>
                                        <input type="checkbox" class="billid" value="<?= $R['billid'] ?>" style="width: 20px;height: 20px;"
                                               onchange="bill_checked(this)">
                                    <? } ?>
                                </td>
                                <td><?= $R['billid'] ?></td>
                                <td title="view client info">
                                    <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                                </td>
                                <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                                <td><?= $R['issuedby'] ?></td>
                                <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                                <td><?= $R['currency_name'] ?></td>
                                <td><?= formatN($R['total_amount']) ?></td>
                                <td><?= $R['non_stock'] ? 'Services' : 'Stock Items' ?></td>
                                <td>
                                    <?= $R['billtypename'] ?>
                                    <small class="d-block text-muted"><?= $R['bill_interval'] ?> <?= ucfirst($R['billtype']) ?> Interval</small>
                                </td>
                                <td><?= fDate($R['startdate'], 'F Y') ?></td>
                                <td><?= $R['lastbilldate'] ? fDate($R['lastbilldate'], 'F Y') : '' ?></td>
                                <td><?= $R['nextbilldate'] ? fDate($R['nextbilldate'], 'F Y') : '' ?></td>
                                <td>
                                    <? if ($R['status'] == 'active') { ?>
                                        <span class="text-success">Active</span>
                                    <? } else { ?>
                                        <span class="text-danger">Billing Stopped</span>
                                    <? } ?>
                                </td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#bill-details-modal" data-toggle="modal" title="View bill details"
                                               data-billid="<?= $R['id'] ?>">
                                                <i class="fa fa-list"></i> View Details</a>
                                            <a class="dropdown-item"
                                               href="<?= url('recurring_bills', 'view_bill', ['billid' => $R['id']]) ?>"
                                               title="View Bill"> <i class="fa fa-book"></i> View Bill & Logs</a>
                                            <? if ($USER_CAN_EDIT) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('recurring_bills', 'create_bill', ['billid' => $R['id']]) ?>"
                                                   title="Edit Bill"> <i class="fa fa-pencil"></i> Edit Bill</a>
                                                <? if ($R['status'] == 'active') { ?>
                                                    <a class="dropdown-item"
                                                       href="#stop-billing-modal" data-toggle="modal" data-billid="<?= $R['id'] ?>"
                                                       data-nextbillmonth="<?= fDate($R['nextbilldate'], 'F Y') ?>" title="Edit Bill"> <i
                                                                class="fa fa-stop"></i> Stop Billing</a>
                                                <? } else { ?>
                                                    <form action="<?= url('recurring_bills', 'enable_billing') ?>" method="post"
                                                          onsubmit="return confirm(`Do you want to enable billing?`)">
                                                        <input type="hidden" name="billid" value="<?= $R['id'] ?>">
                                                        <button class="dropdown-item"><i class="fa fa-life-saver"></i> Enable Billing</button>
                                                    </form>
                                                <? } ?>
                                            <? } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
                <div style="display: none">
                    <table id="bill-export-table" class="table table-hover mb-none" style="font-size:10pt">
                        <thead>
                        <tr>
                            <th>Bill No.</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Issued By</th>
                            <th>Issued Date</th>
                            <th>Currency</th>
                            <th>Bill Amount</th>
                            <th>Bill Type</th>
                            <th>Bill Interval</th>
                            <th>Billing start month</th>
                            <th>Last bill month</th>
                            <th>Next bill month</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($bills as $index => $R) { ?>
                            <tr>
                                <td><?= $R['billid'] ?></td>
                                <td><?= $R['clientname'] ?></td>
                                <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                                <td><?= $R['issuedby'] ?></td>
                                <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                                <td><?= $R['currency_name'] ?></td>
                                <td><?= formatN($R['total_amount']) ?></td>
                                <td><?= $R['non_stock'] ? 'Services' : 'Stock Items' ?></td>
                                <td>
                                    <?= $R['billtypename'] ?>
                                    <small class="d-block text-muted"><?= $R['bill_interval'] ?> <?= ucfirst($R['billtype']) ?> Interval</small>
                                </td>
                                <td><?= fDate($R['startdate'], 'F Y') ?></td>
                                <td><?= $R['lastbilldate'] ? fDate($R['lastbilldate'], 'F Y') : '' ?></td>
                                <td><?= $R['nextbilldate'] ? fDate($R['nextbilldate'], 'F Y') : '' ?></td>
                                <td>
                                    <? if ($R['status'] == 'active') { ?>
                                        <span class="text-success">Active</span>
                                    <? } else { ?>
                                        <span class="text-danger">Billing Stopped</span>
                                    <? } ?>
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

<div class="modal fade" id="stop-billing-modal" data-backdrop="static" role="dialog" aria-labelledby="stop-billing-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Stop Billing</h4>
            </div>
            <form action="<?= url('recurring_bills', 'stop_billing') ?>" method="post" onsubmit="return stop_billing_form(this)">
                <input type="hidden" name="billid" class="billid">
                <div class="modal-body">
                    <h4>Bill No: <span class="text-primary text-weight-semibold billno"></span></h4>
                    <p>Stop billing for: <span class="text-primary text-weight-semibold nextbillmonth"></span></p>
                    <p>Remarks:</p>
                    <textarea name="remarks" id="" rows="5" class="form-control remarks" placeholder="reason for stop billing..." required></textarea>
                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center">
                    <object class="stop-billing-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"
                            style="display: none"></object>
                    <button class="btn btn-success save-btn">Confirm</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bill-details-modal" role="dialog" aria-labelledby="bill-details-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-center modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4>Bill No: <span class="text-primary text-weight-semibold billno"></span></h4>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title">Bill Items</h4>
                    <object class="bill-details-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"
                            style="display: none"></object>
                </div>
                <div style="max-height: 60vh;overflow-y: auto;">
                    <table class="table table-hover mb-none" style="font-size:10pt;">
                        <thead>
                        <tr>
                            <th class="stick">#</th>
                            <th class="stick">Product Name</th>
                            <th class="stick">Qty</th>
                            <th class="stick">Price</th>
                            <th class="stick">VAT%</th>
                            <th class="stick">Inc Price</th>
                            <th class="stick">VAT Amount</th>
                            <th class="stick">Total Amount</th>
                        </tr>
                        </thead>
                        <tbody class="tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'choose client');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);

        setTimeout(function () {
            $('input:checkbox').prop('checked', false);
        }, 1);
    });

    $('#bill-details-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let billid = source.data('billid');
        let modal = $(this);
        let spinner = $(modal).find('.bill-details-spinner');

        $(modal).find('.billno').text('').text(billid);
        $(modal).find('tbody.tbody').empty();
        spinner.show();
        $.get("?module=recurring_bills&action=getBillDetails&format=json", {billid: billid}, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.data, function (i, item) {
                    let row = `<tr>
                                    <td>${count++}</td>
                                    <td>
                                        ${item.productname}
                                        <div class="text-muted text-sm"><i>${item.extra_desc}</i></div>
                                    </td>
                                    <td>${item.qty}</td>
                                    <td>${item.price}</td>
                                    <td>${item.vat_rate}</td>
                                    <td>${item.incprice}</td>
                                    <td>${item.vatamount}</td>
                                    <td>${item.incamount}</td>
                                </tr>`;
                    $(modal).find('tbody.tbody').append(row);
                });
            } else {
                triggerError(result.msg || 'error found');
            }
        });
    });

    function exportExcel(e) {
        let table = document.getElementById("bill-export-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Recurring bills.xlsx`, // fileName you could use any name
            sheet: {
                name: 'Bills' // sheetName
            }
        });
    }

    $('#stop-billing-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        $(modal).find('span.billno').text('').text(source.data('billid'));
        $(modal).find('span.nextbillmonth').text('').text(source.data('nextbillmonth'));
        $(modal).find('input.billid').val('').val(source.data('billid'));
        $(modal).find('textarea.remarks').val('');
    });

    function stop_billing_form(form) {
        if (!$(form).find('.remarks').val().length) {
            triggerError("Enter reason for stop billing!", 5000);
            $(form).find('.remarks').focus();
        }
        $(form).find('.stop-billing-spinner').show();
        $(form).find('button.btn').prop('disabled', true);
    }

    function check_all(obj) {
        if ($(obj).is(':checked')) {
            $('table input.billid:checkbox').prop('checked', true).trigger('change');
        } else {
            $('table input.billid:checkbox').prop('checked', false).trigger('change');
        }
    }

    function bill_checked(obj) {
        let billid = $(obj).val();
        if ($(obj).is(':checked')) {
            $('#bill-selected-form').append(`<input type="hidden" name="billid[]" class="billid" value="${billid}"/>`);
        } else {
            $('#bill-selected-form').find(`input.billid[value="${billid}"]`).remove();
        }
    }

    function check_items() {
        if ($('#bill-selected-form .billid').length === 0) {
            triggerError("Choose at least one bill!");
            return false;
        }
        $('#bill-selected-form .save-spinner').show();
        $('#bill-selected-form button:submit').prop('disabled', true);
    }
</script>
