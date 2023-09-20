<header class="page-header">
    <h2>Tally Transfers</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-lg-9">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>
                <h2 class="panel-title">Tally Transfers</h2>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center mt-md">
                    <div class="col-md-10">
                        <form>
                            <input type="hidden" name="module" value="tally">
                            <input type="hidden" name="action" value="tally_transfers">
                            <div class="row">
                                <div class="col-md-2">
                                    Reference:
                                    <input type="text" class="form-control" name="reference" placeholder="invoiceno receiptno creditnote etc.">
                                </div>
                                <div class="col-md-2">
                                    Voucher Type:
                                    <select name="voucher_type" class="form-control">
                                        <option value="">-- All --</option>
                                        <option value="Sales">Sales</option>
                                        <option value="Credit Note">Credit Note</option>
                                        <option value="Payment">Payment</option>
                                        <option value="Receipt">Receipt</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    From:
                                    <input type="date" class="form-control" name="fromdate" value="<?=$fromdate?>">
                                </div>
                                <div class="col-md-3">
                                    To:
                                    <input type="date" class="form-control" name="todate" value="<?=$todate?>">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-success mt-lg">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-primary mb-md"><?=$title?></p>
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Reference</th>
                            <th>Voucher Date</th>
                            <th>Voucher Type</th>
                            <th>Trans ID</th>
                            <th>Issued by</th>
                            <th>Issued at</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($transfers as $t) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td>
                                    <a href="<?=$t['url']?>"><?= $t['reference'] ?></a>
                                </td>
                                <td><?= fDate($t['date'], 'd-M-Y') ?></td>
                                <td><?= $t['voucher_type'] ?></td>
                                <td title="<?= $t['trxno'] ?>"><?= substr($t['trxno'], 0, 10) ?>...</td>
                                <td><?= $t['username'] ?></td>
                                <td><?= fDate($t['doc'], 'd M Y H:i') ?></td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm" title="view details" data-toggle="modal"
                                            data-target="#transfer-modal" data-trxno="<?= $t['trxno'] ?>" data-vouchertype="<?= $t['voucher_type'] ?>"
                                            data-reference="<?= $t['reference'] ?>" data-date="<?= fDate($t['date'], 'd-M-Y') ?>"
                                            data-details='<?= json_encode($t['details']) ?>'>
                                        <i class="fa fa-list"></i></button>
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

<div class="modal fade" id="transfer-modal" tabindex="-1" role="dialog" aria-labelledby="transfer-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tally Transfer details</h4>
            </div>
            <div class="modal-body">
                <p>Voucher type: <span class="text-primary voucher_type"></span></p>
                <p>Reference: <span class="text-primary reference"></span></p>
                <p>Date: <span class="text-primary date"></span></p>
                <p>Tally trxno: <span class="text-primary trxno" style="font-size: 8pt;"></span></p>
                <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <td>#</td>
                        <td>Ledger Name</td>
                        <td>Dr/Cr</td>
                        <td class="text-right">Amount</td>
                    </tr>
                    </thead>
                    <tbody class="tbody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#transfer-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        let details = source.data('details');
        console.log(details);

        $(modal).find('.trxno').text(source.data('trxno'));
        $(modal).find('.voucher_type').text(source.data('vouchertype'));
        $(modal).find('.reference').text(source.data('reference'));
        $(modal).find('.date').text(source.data('date'));

        $(modal).find('tbody.tbody').empty();
        $.each(details, function (i, item) {
            let row = `<tr>
                         <td>${item.partno}</td>
                         <td>${item.ledgername}</td>
                         <td>${item.dr_cr}</td>
                         <td class="text-right">${item.amount}</td>
                     </tr>`;
            $(modal).find('tbody.tbody').append(row);
        });
    });
</script>