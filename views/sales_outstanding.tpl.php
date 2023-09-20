<style>
    @media (min-width: 768px) {
        #client-info-modal .modal-lg {
            width: 75% !important;
        }
    }
</style>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panelControl">
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
            </div>
            <h2 class="panel-title"><i class="fa fa-money"></i> <?= $top_title ?></h2>
        </header>
        <div class="panel-body">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="<?= $action ?>">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-3">
                        Client:
                        <select id="clientid" class="form-control" name="clientid"> </select>
                    </div>
                    <? if ($action == 'overall_sales_outstanding') { ?>
                        <div class="col-md-3">
                            Account Manager:
                            <select id="accmg" class="form-control" name="acc_mng"> </select>
                        </div>
                    <? } ?>
                    <div class="col-md-2 pt-lg">
                        <button type="submit" class="btn btn-success btn-sm">Search</button>
                    </div>
                </div>
            </form>
            <p>Filter: <span class="text-primary mt-md"><?= $title ?></span></p>

            <div class="mb-xlg">
                <? if ($client || $acc_manager) { ?>
                    <a href="<?= $pdf_url ?>" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Export PDF</a>
                <? } ?>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h5>Overall Outstanding</h5>
                    <table class="table table-bordered" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Base Amount (<?= $baseCurrency['name'] ?>)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($total_outstanding['currencies'] as $currency => $R) { ?>
                            <tr>
                                <td><?= $currency ?></td>
                                <td class="text-danger"><?= formatN($R['amount']) ?></td>
                                <td class="text-rosepink"><?= $R['base_currency'] == 'yes' ? '-' : formatN($R['base_amount']) ?></td>
                            </tr>
                        <? } ?>
                        <tr class="text-weight-bold">
                            <td colspan="2">BASE TOTAL</td>
                            <td class="text-rosepink"><?= formatN($total_outstanding['base_total']) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <h5>Overall In days</h5>
                    <table class="table table-bordered" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th>Currency</th>
                            <th>(<30 days)</th>
                            <th>(30 to 45 days)</th>
                            <th>(45 to 90 days)</th>
                            <th>(>90 days)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= $baseCurrency['name'] ?></td>
                            <td class=" text-right"><?= formatN($total_outstanding['(<30 days)']) ?></td>
                            <td class=" text-right"><?= formatN($total_outstanding['(30 to 45 days)']) ?></td>
                            <td class=" text-right"><?= formatN($total_outstanding['(45 to 90 days)']) ?></td>
                            <td class=" text-right"><?= formatN($total_outstanding['(>90 days)']) ?></td>
                        </tr>
                        <tr class="text-weight-bold">
                            <td colspan="4">BASE TOTAL</td>
                            <td class="text-right text-rosepink"><?= formatN($total_outstanding['base_total']) ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="table-responsive mt-md">
                <table class="table table-hover mb-none" style="font-size:10pt;" id="outstanding-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Issue Date</th>
                        <th>Invoice No.</th>
                        <th>Invoice By</th>
                        <th>Order By</th>
                        <th>Client</th>
                        <th>Account Manager</th>
                        <th>Currency</th>
                        <th class=" text-right">Full Amount</th>
                        <th class=" text-right">Paid Amount</th>
                        <th class=" text-right">Credit Notes</th>
                        <th class=" text-right">Pending Amount</th>
                        <th class=" text-right">(<30 days)</th>
                        <th class=" text-right">(30 to 45 days)</th>
                        <th class=" text-right">(45 to 90 days)</th>
                        <th class=" text-right">(>90 days)</th>
                        <th class=" text-right">Final Balance</th>
                        <th class=" text-right">Due on</th>
                        <th>Outstanding Remarks</th>
                        <th class="no-export"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($invoice_list as $idnex => $invoice) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td data-sort="<?= strtotime($invoice['invoice_date']) ?>"><?= fDate($invoice['invoice_date'], 'd F Y') ?></td>
                            <td>
                                <a class="d-block"
                                   href="<?= url('sales', 'view_invoice', 'salesid=' . $invoice['salesid']) ?>"><?= $invoice['receipt_no'] ?></a>
                            </td>
                            <td><?= $invoice['acc_manager'] ?></td>
                            <td>
                                <? if ($invoice['orderno']) { ?>
                                    <div><?= $invoice['order_creator'] ?></div>
                                    <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $invoice['orderno']]) ?>">Order <?= $invoice['orderno'] ?></a></small>
                                <? } ?>
                            </td>
                            <td title="view client info"><a data-toggle="modal" href="#client-info-modal"
                                                            data-clientid="<?= $invoice['clientid'] ?>"><?= $invoice['clientname'] ?></a>
                            </td>
                            <td><?= $invoice['account_manager'] ?></td>
                            <td><?= $invoice['currencyname'] ?></td>
                            <td class="text-primary text-right"><?= formatN($invoice['full_amount']) ?></td>
                            <td class="text-success text-right"><?= formatN($invoice['lastpaid_totalamount']) ?></td>
                            <td class="text-rosepink text-right"><?= formatN($invoice['total_increturn']) ?></td>
                            <td class="text-danger text-right"><?= formatN($invoice['pending_amount']) ?></td>
                            <td class=" text-right"><?= formatN($invoice['(<30 days)']) ?></td>
                            <td class=" text-right"><?= formatN($invoice['(30 to 45 days)']) ?></td>
                            <td class=" text-right"><?= formatN($invoice['(45 to 90 days)']) ?></td>
                            <td class=" text-right"><?= formatN($invoice['(>90 days)']) ?></td>
                            <td class=" text-right"><?= formatN($invoice['pending_amount']) . ' Dr' ?></td>
                            <td data-sort="<?= strtotime($invoice['due_date']) ?>" class=" text-right"><?= $invoice['due_date'] ?></td>
                            <td>
                                <? if ($invoice['has_installment']) { ?>
                                    <i class="text-rosepink">Installment Payment</i>
                                <? } else { ?>
                                    <?= $invoice['outstanding_remarks'] ?>
                                <? } ?>
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="<?= url('payments', 'payment_list', 'id=' . $invoice['salesid']) ?>"
                                           title="Payment List"><i class="fa-money fa"></i> Sales receipts</a>
                                        <? if ($invoice['has_installment']) { ?>
                                            <button type="button" class="dropdown-item" data-toggle="modal"
                                                    data-target="#view-installment-plan-modal"
                                                    data-salesid="<?= $invoice['salesid'] ?>" title="Invoice remark"><i
                                                        class="fa fa-list-alt"></i>
                                                View installment plan
                                            </button>
                                        <? } else { ?>
                                            <button type="button" class="dropdown-item" data-toggle="modal"
                                                    data-target="#invoice-remarks-modal"
                                                    data-salesid="<?= $invoice['salesid'] ?>" data-openingid=""
                                                    data-invoiceno="<?= $invoice['receipt_no'] ?>"
                                                    data-remarkid="<?= $invoice['invoiceremarkid'] ?>"
                                                    title="Invoice remark"><i class="fa fa-book"></i> Outstanding Remarks
                                            </button>
                                        <? } ?>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        <? $count++;
                    } ?>
                    <? foreach ($opening_outstandings as $op) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td data-sort="<?= strtotime($op['invoicedate']) ?>"><?= fDate($op['invoicedate'], 'd F Y') ?></td>
                            <td>
                                <p class="m-none"><a
                                            href="<?= url('sales', 'opening_outstanding', 'openingid=' . $op['id']) ?>"><?= $op['invoiceno'] ?></a>
                                </p>
                                <small class="text-muted">opening</small>
                            </td>
                            <td><?= $op['issuedby'] ?></td>
                            <td></td>
                            <td title="view client info"><a data-toggle="modal" href="#client-info-modal"
                                                            data-clientid="<?= $op['clientid'] ?>"><?= $op['clientname'] ?></a></td>
                            <td><?= $op['account_manager'] ?></td>
                            <td><?= $op['currencyname'] ?></td>
                            <td class="text-primary text-right"><?= formatN($op['outstanding_amount']) ?></td>
                            <td class="text-success text-right"><?= formatN($op['paid_amount']) ?></td>
                            <td class="text-rosepink text-right">-</td>
                            <td class="text-danger text-right"><?= formatN($op['pending_amount']) ?></td>
                            <td class=" text-right"><?= formatN($op['(<30 days)']) ?></td>
                            <td class=" text-right"><?= formatN($op['(30 to 45 days)']) ?></td>
                            <td class=" text-right"><?= formatN($op['(45 to 90 days)']) ?></td>
                            <td class=" text-right"><?= formatN($op['(>90 days)']) ?></td>
                            <td class=" text-right"><?= formatN($op['pending_amount']) . ' Dr' ?></td>
                            <td data-sort="<?= strtotime($op['duedate']) ?>" class=" text-right"><?= fDate($op['duedate'], 'd-F-Y') ?></td>
                            <td><?= $op['outstanding_remarks'] ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="<?= url('payments', 'payment_list', 'openingid=' . $op['id']) ?>"
                                           title="Payment List"><i class="fa-money fa"></i> Sales receipts</a>
                                        <button type="button" class="dropdown-item" data-toggle="modal" data-target="#invoice-remarks-modal"
                                                data-salesid="" data-openingid="<?= $op['id'] ?>" data-invoiceno="<?= $op['invoiceno'] ?>"
                                                data-remarkid="<?= $op['invoiceremarkid'] ?>" title="Invoice remark">
                                            <i class="fa fa-book"></i> Outstanding Remarks
                                        </button>
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


<div class="modal fade" id="invoice-remarks-modal" role="dialog" aria-labelledby="invoice-remarks-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Invoice Remarks</h4>
            </div>
            <form action="<?= url('payments', 'save_invoice_remarks') ?>" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            Invoice No:
                            <input type="text" readonly class="form-control invoiceno">
                            <input type="hidden" class="salesid" name="salesid">
                            <input type="hidden" class="openingid" name="openingid">
                        </div>
                        <div class="col-md-6">
                            Remarks:
                            <select name="remarkid" class="form-control remarkid">
                                <option value="">-- Choose remark --</option>
                                <? foreach ($remarks as $r) { ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm">Confirm</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>
<?= component('sale/view_installment_plan_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'Choose client');
        initSelectAjax('#accmg', "?module=user&action=getUser&format=json", 'Choose account manager');
        $('#outstanding-table').DataTable({
            dom: '<"top"fBl>t<"bottom"ip>',
            colReorder: true,
            keys: true,
            buttons: [
                {
                    extend: 'csvHtml5',
                    text: 'Export Excel',
                    exportOptions: {
                        columns: "th:not(.no-export)"
                    }
                }
            ],

        });
    });

    $('#invoice-remarks-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        let salesid = source.data('salesid');
        let openingid = source.data('openingid');
        let remarkid = source.data('remarkid');
        let invoiceno = source.data('invoiceno');

        $(modal).find('.salesid').val('');
        $(modal).find('.openingid').val('');
        $(modal).find('.remarkid').val('');

        $(modal).find('.invoiceno').val(invoiceno);
        $(modal).find('.remarkid').val(remarkid);
        $(modal).find('.salesid').val(salesid);
        $(modal).find('.openingid').val(openingid);
    });

</script>
