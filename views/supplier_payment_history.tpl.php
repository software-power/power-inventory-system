<header class="page-header">
    <h2>Supplier payment history</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="supplier_payment_history">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Supplier:
                            <select id="supplierid" name="supplierid" class="form-control">
                                <? if ($supplier) { ?>
                                    <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Branch:
                            <select name="branchid" class="form-control" required>
                                <? foreach ($branches as $index => $branch) { ?>
                                    <option <?= selected($currentBranch['id'], $branch['id']) ?>
                                            value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Issued by:
                            <select id="userid" name="issuedby" class="form-control"></select>
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-4">
                            Payment method:
                            <select name="payment_method" class="form-control">
                                <option value="">-- All --</option>
                                <? foreach ($paymentmethods as $p) { ?>
                                    <option value="<?= $p['name'] ?>"><?= $p['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" class="form-control" value="<?=$fromdate?>">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" class="form-control" value="<?=$todate?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mt-md d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-search"></i>
                                SEARCH
                            </button>
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
                    <h2 class="panel-title">Supplier payment history</h2>
                    <p>Filter: <span class="text-primary"><?= $title ?></span></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <button type="button" data-toggle="modal" data-target="#search-modal" class="btn"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="history-table" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment No</th>
                        <th>Supplier</th>
                        <th>Branch</th>
                        <th>Issued By</th>
                        <th>Issue Date</th>
                        <th>Payment Method</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($payments as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['id'] ?></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td><?= $R['branchname'] ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td>
                                <?= $R['method'] ?>
                                <? if ($R['method'] == PaymentMethods::BANK) { ?>
                                    <? $text = "Bank Name: {$R['bankname']}, Bank Reference: {$R['bankreference']}"; ?>
                                    <i class="text-xs text-muted d-block" title="<?= $text ?>"><?= substr($text, 0, 20) ?>...</i>
                                <? } ?>
                                <? if ($R['method'] == PaymentMethods::CHEQUE) { ?>
                                    <? $text = "Cheque No: {$R['chequename']}, Cheque Type: {$R['chequetype']}"; ?>
                                    <i class="text-xs text-muted d-block" title="<?= $text ?>"><?= substr($text, 0, 20) ?>...</i>
                                <? } ?>
                                <? if ($R['method'] == PaymentMethods::CREDIT_CARD) { ?>
                                    <? $text = "Reference No: {$R['credit_cardno']}"; ?>
                                    <i class="text-xs text-muted d-block" title="<?= $text ?>"><?= substr($text, 0, 20) ?>...</i>
                                <? } ?>
                            </td>
                            <td><?= $R['currencyname'] ?></td>
                            <td><?= formatN($R['total_amount']) ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('suppliers', 'payment_slip', 'id=' . $R['id']) ?>"
                                           title="Print Slip"><i class="fa fa-print text-primary"></i> Print</a>
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
        initSelectAjax('#supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Search supplier', 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'user', 2);
        $('#history-table').DataTable({
            dom: '<"top"fBl>t<"bottom"ip>',
            colReorder: true,
            keys: true,
            buttons: ['excelHtml5', 'csvHtml5'],
            exportOptions: {
                columns: ':not(:last-child)',
            }
        });
    });

</script>