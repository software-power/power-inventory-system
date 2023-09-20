<style>
    .table {
        width: 100%;
        font-size: 14px;
    }

    .table .actions a:hover, .table .actions-hover a {
        color: #ffffff;
    }

    .table .actions a:hover, .table .actions-hover a:hover {
        color: #ffffff;
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

    .table-responsive {
        min-height: 150px;
    }

    .container-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .panel-body .badge {
        border-radius: unset;
        width: 100%;
        font-weight: 400;
    }

    th.stick {
        position: sticky;
        top: 100px; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }

    .inner-table-holder {
        padding: 5px;
        max-height: 180px;
        overflow: auto;
    }

    @media (min-width: 768px) {
        .modal-lg {
            width: 90% !important;
        }

        .modal-md {
            width: 60% !important;
        }
    }
</style>
<header class="page-header">
    <h2>Invoices</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="payments">
                <input type="hidden" name="action" value="invoice_list">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Search Invoice No:
                            <input type="text" class="form-control" name="invoiceno" placeholder="invoice no">
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-4">
                            Branch:
                            <select id="branchid" class="form-control" name="branchid">
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($b['id'], $branchid) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                                <option value=""> -- All Branches --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="locationid" class="form-control" name="locationid">
                                <? if ($location) { ?>
                                    <option value="<?= $location['id'] ?>"><?= $location['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Sales Person:
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" class="form-control" name="userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $creator['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $creator['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Client
                            <select id="clientid" class="form-control" name="clientid">
                                <? if ($client) { ?>
                                    <option value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Invoice Type
                            <select class="form-control text-capitalize" name="paymenttype">
                                <option value="" selected>--choose type--</option>
                                <option value="<?= PAYMENT_TYPE_CASH ?>"><?= PAYMENT_TYPE_CASH ?> Invoice</option>
                                <option value="<?= PAYMENT_TYPE_CREDIT ?>"><?= PAYMENT_TYPE_CREDIT ?> Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Payment Status
                            <select class="form-control text-capitalize" name="payment_status">
                                <option value="" selected>--choose type--</option>
                                <option value="<?= PAYMENT_STATUS_COMPLETE ?>"><?= PAYMENT_STATUS_COMPLETE ?></option>
                                <option value="<?= PAYMENT_STATUS_PENDING ?>"><?= PAYMENT_STATUS_PENDING ?></option>
                                <option value="<?= PAYMENT_STATUS_PARTIAL ?>"><?= PAYMENT_STATUS_PARTIAL ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Approval Status
                            <select class="form-control text-capitalize" name="approval_status">
                                <option value="" selected>--choose status--</option>
                                <option value="1">Approved</option>
                                <option value="0">Not approved</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" value="<?= $fromdate ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-close"></i> Cancel</button>
                            <button type="button" class="btn btn-danger" title="reset inputs" onclick="resetInputs(this)"><i
                                        class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- center-panel -->
<div class="col-amd-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title"><i class="fa fa-file"></i> Invoice List</h2>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <a href="#search-modal" class="btn btn-default btn-sm" data-toggle="modal">
                        <i class="fa fa-search"></i> Search</a>
                    <a class="btn btn-default btn-sm ml-sm" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
            </div>
            <p>Filter: <span class="text-primary"><?= $title ?></span></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:12px">
                    <thead>
                    <tr>
                    <tr>
                        <th>No.</th>
                        <th>Invoice No.</th>
                        <th>Client Name</th>
                        <th>Stock location</th>
                        <th>Issued by</th>
                        <th>Time</th>
                        <th>Currency</th>
                        <th>Grand selling Amount</th>
                        <th>Grand VAT Amount</th>
                        <th>Paid/Credit note amount</th>
                        <th>Payment Type</th>
                        <th>Payment Status</th>
                        <th>Approval Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $USER_CAN_APPROVE = Users::can(OtherRights::approve_other_credit_invoice) || Users::can(OtherRights::approve_credit);
                    $USER_CAN_ISSUE_EXPENSE = Users::can(OtherRights::issue_expense);
                    $CAN_RESEND_EFD = Users::can(OtherRights::resend_efd_receipt);
                    foreach ($sales_list as $id => $R) {
                        if ($R['fisc_invoiceid']) {
                            $payment_status_class = 'text-primary';
                        } elseif ($R['payment_status'] == PAYMENT_STATUS_COMPLETE) {
                            $payment_status_class = 'text-success';
                        } elseif ($R['payment_status'] == PAYMENT_STATUS_PARTIAL) {
                            $payment_status_class = 'text-warning';
                        } else {
                            $payment_status_class = 'text-danger';
                        }
                        ?>
                        <tr class="invoiceList">
                            <td width="30px"><?= $id + 1 ?></td>
                            <td>
                                <?= $R['receipt_no'] ?>
                                <? if ($R['billid']) { ?>
                                    <div class="text-success">Automated billing</div>
                                <? } ?>
                                <? if ($R['fisc_invoiceid']) { ?>
                                    <div class="text-muted"><i>Converted to <?= $R['fisc_invoiceno'] ?></i></div>
                                <? } ?>
                                <? if ($R['previd']) { ?>
                                    <div class="text-info"><i>Prev <?= $R['prev_invoiceno'] ?></i></div>
                                <? } ?>
                            </td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal"
                                   data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td>
                                <p><?= formatN($R['full_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_full_amount']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td>
                                <? if ($R['vat_exempted']) { ?>
                                    <i class="text-muted">VAT exempted</i>
                                <? } else { ?>
                                    <p><?= formatN($R['grand_vatamount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_grand_vatamount']) ?>
                                        </i>
                                    <? } ?>
                                <? } ?>
                            </td>
                            <td><?= formatN($R['paymenttype'] == PAYMENT_TYPE_CREDIT ? $R['full_amount'] - $R['pending_amount'] : $R['full_amount'] - $R['total_increturn']) ?></td>
                            <td style="text-transform:capitalize"><?= $R['paymenttype'] ?></td>
                            <td class="<?= $payment_status_class ?> text-weight-bold"
                                style="text-transform:capitalize"><?= $R['payment_status'] ?></td>
                            <td>
                                <? if ($R['fisc_invoiceid']) { ?>
                                    <span class="badge badge-warning bg-primary">Invoice Converted</span>
                                <? } elseif ($R['paymenttype'] == PAYMENT_TYPE_CASH) { ?>
                                    <? if ($R['iscreditapproved'] == 1) { ?>
                                        <span class="badge badge-warning bg-success">Payment Completed</span>
                                    <? } else { ?>
                                        <span class="badge badge-warning bg-danger">Pending Approval</span>
                                    <? } ?>
                                <? } else { ?>
                                    <? if ($R['iscreditapproved'] == 1 && $R['payment_status'] == PAYMENT_STATUS_COMPLETE) { ?>
                                        <span class="badge badge-warning bg-success">Approved</span>
                                    <? } elseif ($R['iscreditapproved'] == 1 && $R['payment_status'] != PAYMENT_STATUS_COMPLETE) { ?>
                                        <span class="badge badge-warning bg-warning">Approved (Credit)</span>
                                    <? } else { ?>
                                        <span class="badge badge-warning bg-danger">Not Approved</span>
                                    <? } ?>
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

                                        <? if ($R['paymenttype'] == PAYMENT_TYPE_CREDIT && $R['iscreditapproved'] && !$R['fisc_invoiceid']) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('payments', 'payment_list', 'id=' . $R['salesid']) ?>"
                                               title="Payment List"><i class="fa-money fa"></i> Sales receipts</a>
                                        <? } ?>
                                        <a class="dropdown-item" href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>"
                                           title="View" target="_blank"><i class="fa-file fa"></i> View Invoice</a>
                                        <? if ($R['fisc_invoiceid']) { ?>

                                        <? } elseif ($R['iscreditapproved']) { ?>
                                            <? if ($USER_CAN_ISSUE_EXPENSE) {
                                                if ($R['expenseid']) { ?>
                                                    <a class="dropdown-item" target="_blank"
                                                       href="<?= url('expenses', 'issued_list', ['expenseid' => $R['expenseid']]) ?>"
                                                       title="View invoice expense">
                                                        <i class="fa-file fa"></i> View Expenses/Cost</a>
                                                <? } else { ?>
                                                    <form action="<?= url('expenses', 'issue_expense') ?>" style="margin:0;"
                                                          method="post">
                                                        <input type="hidden" name="salesid" value="<?= $R['salesid'] ?>">
                                                        <button class="dropdown-item"><i class="fa-ticket fa"></i> Add
                                                            Expenses/Cost
                                                        </button>
                                                    </form>
                                                <? }
                                            } ?>
                                            <? if (!$R['tally_post'] && $R['transfer_tally']) { ?>
                                                <a class="dropdown-item" title="Post to tally"
                                                   href="<?= url('sales', 'post_tally', 'salesid=' . $R['salesid']) ?>">
                                                    <i class="fa fa-upload"></i> Post to Tally</a>
                                            <? } ?>
                                            <? if ($R['receipt_method'] == 'vfd' || $R['receipt_method'] == 'efd') { ?>
                                                <a class="dropdown-item" target="_blank" title="Print Tax Invoice"
                                                   href="<?= url('receipts', 'vfd', 'salesid=' . $R['salesid'] . '&print_size=A4') ?>">
                                                    <i class="fa-print fa"></i> Print Tax Invoice</a>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('receipts', 'tax_invoice_with_serialno', 'salesid=' . $R['salesid'] . '&print_size=A4') ?>"
                                                   title="Print Tax Invoice"><i class="fa-print fa"></i> Print Tax Invoice With serial</a>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('receipts', 'vfd', ['salesid' => $R['salesid'], 'print_size' => 'A4', 'with_bank_info' => '']) ?>"
                                                   title="Print Tax Invoice with bank details"><i class="fa-print fa"></i> Print Tax Invoice
                                                    With bank
                                                    info</a>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('receipts', 'vfd', ['salesid' => $R['salesid'], 'print_size' => 'A4', 'with_bank_info' => '', 'no_header_footer' => '']) ?>"
                                                   title="Print Tax Invoice with bank details"><i class="fa-print fa"></i> Print Tax Invoice
                                                    No Header</a>
                                                <? if ($R['receipt_method'] == 'efd' && $CAN_RESEND_EFD) { ?>
                                                    <a class="dropdown-item" title="Resend EFD Receipt" data-toggle="modal"
                                                       data-salesid="<?= $R['salesid'] ?>"
                                                       data-invoiceno="<?= $R['receipt_no'] ?>" href="#resend-efd-modal"><i
                                                                class="fa fa-send-o"></i> Resend EFD Receipt</a>
                                                <? } ?>
                                            <? } ?>
                                            <? if ($R['has_installment']) { ?>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('receipts', 'print_installment_plan', 'salesid=' . $R['salesid']) ?>"
                                                   title="Print Tax Invoice"><i class="fa-print fa"></i> Print Installment Plan</a>
                                            <? } ?>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'system_receipt', ['salesno' => $R['salesid'], 'print_size' => 'A4', 'redirect' => base64_encode(url('payments', 'invoice_list'))]) ?>"
                                               title="Print Receipt"><i class="fa-print fa"></i> Print Receipt A4</a>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'system_receipt', ['salesno' => $R['salesid'], 'print_size' => 'A4', 'with_bank_info' => '', 'redirect' => base64_encode(url('payments', 'invoice_list'))]) ?>"
                                               title="Print Receipt"><i class="fa-print fa"></i> Print Receipt A4 With bank info</a>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'system_receipt', ['salesno' => $R['salesid'], 'print_size' => 'A4', 'with_bank_info' => '', 'no_header_footer' => '', 'redirect' => base64_encode(url('payments', 'invoice_list'))]) ?>"
                                               title="Print Receipt"><i class="fa-print fa"></i> Print Receipt A4 With No Header</a>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'system_receipt', ['salesno' => $R['salesid'], 'print_size' => '', 'redirect' => base64_encode(url('payments', 'invoice_list'))]) ?>"
                                               title="Print Receipt"><i class="fa-print fa"></i> Print Receipt Small</a>
                                            <? if ($R['receipt_method'] == 'vfd') { ?>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('receipts', 'vfd', 'salesid=' . $R['salesid'] . '&print_size=&redirect=' . base64_encode(url('payments', 'invoice_list'))) ?>"
                                                   title="Print Receipt"><i class="fa-print fa"></i>
                                                    Print <?= CS_VFD_TYPE == VFD_TYPE_VFD ? 'TRA' : 'ZRA' ?> Receipt</a>
                                            <? } ?>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'print_delivery', 'salesno=' . $R['salesid']) ?>"
                                               title="print delivery note"><i class="fa-print fa"></i> Print delivery note</a>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'print_delivery_with_serialno', 'salesno=' . $R['salesid']) ?>"
                                               title="print delivery note"><i class="fa-print fa"></i> Print delivery note with Serial</a>
                                        <? } else { ?>
                                            <? if ($USER_CAN_APPROVE) { ?>
                                                <? if ($R['paymenttype'] == PAYMENT_TYPE_CASH) { ?>
                                                    <a class="dropdown-item generate_receipt"
                                                       href="<?= url('payments', 'approve_invoice', ['salesid' => $R['salesid']]) ?>">
                                                        <i class="fa-check fa"></i> Approve Invoice</a>
                                                <? } else { ?>
                                                    <a class="dropdown-item generate_receipt"
                                                       href="<?= url('payments', 'client_outstanding', ['salesid' => $R['salesid']]) ?>">
                                                        <i class="fa-check fa"></i> Approve Invoice</a>
                                                <? } ?>
                                            <? } ?>
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

<?= component('sale/resend_efd_modal.tpl.php') ?>

<div class="modal fade" id="view-expense-modal" tabindex="-1" role="dialog" aria-labelledby="view-expense-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Invoice Expense Details: <span class="text-primary receipt_no"></span></h4>
                <p>Total Expense: <span class="text-danger totalAmount">10,000</span></p>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-bordered mb-none" style="font-size:13px;" id="">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Expense</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">

    $(function () {
        initSelectAjax('#locationid', '?module=locations&action=getLocations&format=json', 'Choose location');
        initSelectAjax('#userid', '?module=users&action=getUser&format=json', 'Choose salesperson');
        initSelectAjax('#clientid', '?module=clients&action=getClients&format=json', 'Choose client');
        $('#branchid').select2({width: '100%'});

        $('#view-expense-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            let salesid = source.data('salesid');
            let receiptno = source.data('receiptno');

            //clear
            $(modal).find('.receipt_no').text('');
            $(modal).find('.totalAmount').text('');
            $(modal).find('tbody').empty();


            $(modal).find('.receipt_no').text(receiptno);
            $.get(`?module=expenses&action=getIssuedExpense&format=json&salesid=${salesid}`, null, function (data) {
                let result = JSON.parse(data);
                // console.log(result);
                if (result.status === 'found') {
                    let count = 1;
                    $(modal).find('.totalAmount').text(numberWithCommas(result.details.total_amount));
                    $.each(result.details.details, function (i, item) {
                        let row = `<tr>
                                       <td>${count}</td>
                                       <td>${item.attrname}</td>
                                       <td>${numberWithCommas(item.amount)}</td>
                                    </tr>`;
                        $(modal).find('tbody').append(row);
                        count++;
                    });
                } else {
                    triggerError('No expenses found');
                }
            });
        });
    });

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    function resetInputs(obj) {
        let modal = $(obj).closest('.modal');
        $(modal).find('select,input.form-control').val('').trigger('change');
        $(modal).find(`[name="fromdate"]`).val(`<?=TODAY?>`);
    }
</script>



