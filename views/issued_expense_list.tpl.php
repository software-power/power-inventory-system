<header class="page-header">
    <h2>Issued Expenses</h2>
</header>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <div>
                    <h2 class="panel-title">Issued Expenses</h2>
                    <p class="text-primary"><?= $title ?></p>
                </div>
                <div class="d-flex justify-content-end align-items-center">
                    <button class="btn btn-info btn-sm mr-md" data-toggle="modal" data-target="#search-modal">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <? if (Users::can(OtherRights::issue_expense)) { ?>
                        <a class="btn btn-primary btn-sm" href="<?= url('expenses', 'issue_expense') ?>">
                            <i class="fa fa-plus"></i> Issue Expense
                        </a>
                    <? } ?>
                </div>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Voucher No</th>
                            <th>Branch</th>
                            <th>Currency</th>
                            <th>Total Amount</th>
                            <th>Date</th>
                            <th>Paid To</th>
                            <th class="text-center">Invoice No.</th>
                            <th class="text-center">Verification Code</th>
                            <th class="text-center">Sale Receipt No.</th>
                            <th>Issued By</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        $USER_CAN_APPROVE = Users::can(OtherRights::approve_other_expense) || Users::can(OtherRights::approve_expense);
                        $USER_CAN_CANCEL = Users::can(OtherRights::cancel_expense);
                        $USER_CAN_ISSUE = Users::can(OtherRights::issue_expense);
                        foreach ($issuedList as $id => $R) { ?>
                            <tr title="<?= $R['remarks'] ?>">
                                <td width="80px"><?= $count ?></td>
                                <td><?= getVoucherNo($R['id']) ?></td>
                                <td><?= $R['branchname'] ?></td>
                                <td><?= $R['currencyname'] ?></td>
                                <td><?= formatN($R['total_amount']) ?></td>
                                <td><?= fDate($R['doc']) ?></td>
                                <td><?= $R['paidto'] ?></td>
                                <td align="center"><?= $R['invoiceno'] ?></td>
                                <td align="center"><?= $R['verificationcode'] ?></td>
                                <td align="center">
                                    <? if ($R['receipt_no']) { ?>
                                        <a href="<?=url('payments', 'invoice_list',['invoiceno'=>$R['receipt_no']])?>"><?= $R['receipt_no'] ?></a>
                                    <? } ?>
                                </td>
                                <td><?= $R['username'] ?></td>
                                <td>
                                    <? if ($R['expense_status'] == 'canceled') { ?>
                                        <span class="text-danger">Canceled</span>
                                    <? } ?>
                                    <? if ($R['expense_status'] == 'not_approved') { ?>
                                        <span class="text-muted">not approved</span>
                                    <? } ?>
                                    <? if ($R['expense_status'] == 'approved') { ?>
                                        <? if ($R['auto_approve']) { ?>
                                            <span class="text-success">Auto approved</span>
                                        <? } else { ?>
                                            <small class="text-success">Approved by: <?= $R['approver'] ?>
                                                , <?= fDate($R['approval_date'], 'd M Y H:i') ?></small>
                                        <? } ?>
                                    <? } ?>
                                </td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#view-expense-modal" data-toggle="modal"
                                               data-voucherno="<?= getVoucherNo($R['id']) ?>"
                                               data-currencyname="<?= $R['currencyname'] ?>"
                                               data-total="<?= formatN($R['total_amount']) ?>"
                                               data-details='<?= json_encode(array_values($R['details'])) ?>'
                                               title="Edit Expense">
                                                <i class="fa fa-list"></i> View
                                            </a>
                                            <? if ($R['expense_status'] == 'not_approved') { ?>
                                                <? if ($USER_CAN_APPROVE) { ?>
                                                    <form action="?module=expenses&action=approve_expense" method="post" class="m-none"
                                                          onsubmit="return confirm('Do you want to approve this expense?')">
                                                        <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                                        <button class="dropdown-item" title="Approve expense"><i class="fa fa-check"></i>
                                                            Approve
                                                        </button>
                                                    </form>
                                                <? } ?>
                                                <? if ($USER_CAN_ISSUE) { ?>
                                                    <a class="dropdown-item" title="Edit Expense"
                                                       href="<?= url('expenses', 'issue_expense', 'voucherno=' . $R['id']) ?>">
                                                        <i class="fa-pencil fa"></i> Edit
                                                    </a>
                                                <? } ?>
                                                <? if ($USER_CAN_CANCEL) { ?>
                                                    <form action="?module=expenses&action=cancel_expense" method="post" class="m-none"
                                                          onsubmit="return confirm('Do you want to cancel this expense?')">
                                                        <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                                        <button class="dropdown-item" title="Cancel expense"><i class="fa fa-trash"></i>
                                                            Cancel
                                                        </button>
                                                    </form>
                                                <? } ?>
                                            <? } ?>
                                            <? if ($R['expense_status'] == 'approved') { ?>
                                                <? if (IS_ADMIN || $R['approvedby'] == $_SESSION['member']['id']) { ?>
                                                    <a class="dropdown-item" title="Edit Expense"
                                                       href="<?= url('expenses', 'issue_expense', 'voucherno=' . $R['id']) ?>">
                                                        <i class="fa-pencil fa"></i> Edit
                                                    </a>
                                                <? } ?>
                                                <? if ($R['transfer_tally'] && !$R['tally_post']) { ?>
                                                    <a class="dropdown-item" href="<?= url('expenses', 'post_tally', ['expenseno' => $R['id']]) ?>"
                                                       title="Print Expense voucher"> <i class="fa fa-upload"></i> Post Tally </a>
                                                <? } ?>
                                                <a class="dropdown-item" href="?module=expenses&action=print_expense&id=<?= $R['id'] ?>"
                                                   title="Print Expense voucher" target="_blank">
                                                    <i class="fa fa-print"></i> Print
                                                </a>
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
            </div>
    </div>
</div>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="expenses">
                <input type="hidden" name="action" value="issued_list">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Branch</label>
                                <select name="search[branchid]" class="form-control">
                                    <? foreach ($branches as $index => $R) { ?>
                                        <option <?= selected($R['id'], $branchid) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Issued by</label>
                                <? if (Users::can(OtherRights::approve_other_expense)) { ?>
                                    <select id="userid" name="search[userid]" class="form-control"></select>
                                <? } else { ?>
                                    <input type="hidden" name="search[userid]" value="<?= $creator['id'] ?>">
                                    <input type="text" readonly class="form-control" value="<?= $creator['name'] ?>">
                                <? } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Expense Status</label>
                                <select name="search[expense_status]" class="form-control">
                                    <option value="" selected>-- All --</option>
                                    <option value="approved">Approved</option>
                                    <option value="not_approved">Not Approved</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">From</label>
                                <input type="date" name="search[fromdate]" class="form-control"
                                       value="<?= $fromdate ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">To</label>
                                <input type="date" name="search[todate]" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm confirm-btn">Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="view-expense-modal" tabindex="-1" role="dialog" aria-labelledby="view-expense-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Voucher No: <span class="text-success voucherno">005</span></h4>
                <h5>Total: <span class="text-primary currencyname"></span> <span class="text-primary total"></span></h5>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Attribute</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>Electricity</td>
                        <td>5,000</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#userid', '?module=users&action=getUser&format=json', 'choose user');
        $('#view-expense-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);

            $(modal).find('.voucherno').text('');
            $(modal).find('.currencyname').text('');
            $(modal).find('.total').text('');
            $(modal).find('table tbody').empty();


            $(modal).find('.voucherno').text(source.data('voucherno'));
            $(modal).find('.currencyname').text(source.data('currencyname'));
            $(modal).find('.total').text(source.data('total'));
            let details = source.data('details');
            let count = 1;
            $.each(details, function (index, item) {
                let row = `<tr>
                              <td>${count}</td>
                              <td>${item.attrname}</td>
                              <td>${numberWithCommas(item.amount)}</td>
                          </tr>`;
                $(modal).find('table tbody').append(row);
                count++;
            });
        });
    });

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
