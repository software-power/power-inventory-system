<header class="page-header">
    <h2>Pending Tally Transfers</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-lg-7">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between align-items-center">
                <h2 class="panel-title">Pending Tally Transfers</h2>
                <div class="d-flex align-items-center">
                    <object id="post-all-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40"
                            width="40" style="visibility: hidden"></object>
                    <a href="<?= url('tally', 'post_all') ?>" class="btn btn-success" onclick="disable_link(this)"> Post All</a>
                </div>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Voucherno</th>
                            <th>Voucher Date</th>
                            <th>Voucher</th>
                            <th>Tally Message</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($pending_transfers as $t) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td>
                                    <a href="<?= $t['url'] ?>"><?= $t['voucherno'] ?></a>
                                </td>
                                <td><?= fDate($t['issuedate'], 'd-M-Y') ?></td>
                                <td><?= $t['voucher_type'] ?></td>
                                <td><?= $t['tally_message'] ?></td>
                                <td>
                                    <a href="<?= $t['tally_url'] ?>" class="btn btn-default btn-sm"><i class="fa fa-upload"></i> Post Tally</a>
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
                        <td>Amount</td>
                    </tr>
                    </thead>
                    <tbody class="tbody">
                    <tr>
                        <td>1</td>
                        <td>Sales</td>
                        <td>Dr</td>
                        <td>2,000</td>
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
                         <td>${item.amount}</td>
                     </tr>`;
            $(modal).find('tbody.tbody').append(row);
        });
    });

    function disable_link(obj) {
        $('#post-all-spinner').css('visibility', 'visible');
    }
</script>