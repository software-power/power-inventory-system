<header class="page-header">
    <h2>Canceled Receipt</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="canceled_receipt">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Canceled by:
                            <select name="issuedby" class="form-control userid"></select>
                        </div>
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
                    <div class="mt-md">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-success">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-7">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <button type="button" class="btn" title="Home" data-toggle="modal"
                            data-target="#search-modal"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h3>Canceled Receipt</h3>
                <p class="text-primary mt-md"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Receipt No</th>
                            <th>Issued by</th>
                            <th>Issue Date</th>
                            <th>Currency</th>
                            <th class="text-right">Amount</th>
                            <th>Remarks</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $total = 0;
                        foreach ($canceled as $index => $R) { ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <?= getTransNo($R['receiptno']) ?>
                                    <p><i><?=$R['source']?></i></p>
                                </td>
                                <td><?= $R['issuedby'] ?></td>
                                <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                                <td><?= $R['currencyname'] ?></td>
                                <td class="text-right"><?= formatN($R['amount']) ?></td>
                                <td><?= $R['remarks'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm text-danger" data-toggle="modal" title="cancel receipt"
                                            data-target="#view-receipt-modal" data-payload='<?= $R['payload'] ?>'>
                                        <i class="fa fa-list"></i> View
                                    </button>
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

<div class="modal fade" id="view-receipt-modal" tabindex="-1" role="dialog" aria-labelledby="view-receipt-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-danger">View canceled receipt</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <textarea readonly class="form-control payload" rows="40"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('.userid', "?module=users&action=getUser&format=json", 'User', 2);
    });

    $('#view-receipt-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        $(modal).find('.payload').val('');
        let text = atob(source.data('payload'));
        text = JSON.parse(text);
        text = JSON.stringify(text, undefined, 4);
        text = text.replace(/"/g,'');
        console.log(text);

        $(modal).find('.payload').val(text);
    });
</script>
