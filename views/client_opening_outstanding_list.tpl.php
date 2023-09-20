<style>
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
    <h2>Client Opening Outstanding</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="sales">
                <input type="hidden" name="action" value="opening_outstanding">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            Branch:
                            <select class="form-control" name="branchid">
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            Location:
                            <? if (IS_ADMIN) { ?>
                                <select id="locationid" class="form-control" name="locationid"></select>
                            <? } else { ?>
                                <input type="hidden" name="locationid" value="<?= $defaultLocation['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $defaultLocation['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-6">
                            Client:
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                        <div class="col-md-6">
                            Payment Status:
                            <select class="form-control text-capitalize" name="payment_status">
                                <option value="" selected>--choose type--</option>
                                <option value="<?= PAYMENT_STATUS_COMPLETE ?>"><?= PAYMENT_STATUS_COMPLETE ?></option>
                                <option value="<?= PAYMENT_STATUS_PENDING ?>"><?= PAYMENT_STATUS_PENDING ?></option>
                                <option value="<?= PAYMENT_STATUS_PARTIAL ?>"><?= PAYMENT_STATUS_PARTIAL ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                Cancel
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
                    <h2 class="panel-title"><i class="fa fa-file"></i> Client Opening Outstanding</h2>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <a href="#search-modal" class="btn btn-default btn-sm" data-toggle="modal">
                        <i class="fa fa-search"></i> Search</a>
                    <a class="btn btn-default btn-sm ml-sm" href="?module=sales&action=create_opening_outstanding"> <i class="fa fa-home"></i> Create
                        Outstanding</a>
                </div>
            </div>
            <p class="text-primary"><?= $title ?></p>
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
                        <th>Location</th>
                        <th>Issued Date</th>
                        <th>Issued by</th>
                        <th>Invoice Date</th>
                        <th>Credit Days</th>
                        <th>Due date</th>
                        <th>Currency</th>
                        <th>Opening Amount</th>
                        <th>Paid Amount</th>
                        <th>Pending Amount</th>
                        <th>Payment Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($outstandings as $id => $R) {
                        if ($R['payment_status'] == PAYMENT_STATUS_COMPLETE) {
                            $payment_status_class = 'text-success';
                        } elseif ($R['payment_status'] == PAYMENT_STATUS_PARTIAL) {
                            $payment_status_class = 'text-warning';
                        } else {
                            $payment_status_class = 'text-danger';
                        }
                        ?>
                        <tr class="invoiceList">
                            <td width="30px"><?= $id + 1 ?></td>
                            <td><?= $R['invoiceno'] ?></td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td><?= fDate($R['doc'], 'd M Y H:i') ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['invoicedate'], 'd F Y') ?></td>
                            <td><?= $R['credit_days'] ?></td>
                            <td><?= fDate($R['duedate'], 'd F Y') ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td>
                                <p><?= formatN($R['outstanding_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_outstanding_amount']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td><?= formatN($R['paid_amount']) ?></td>
                            <td>
                                <? if ($R['pending_amount'] > 0) { ?>
                                    <p><?= formatN($R['pending_amount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_pending_amount']) ?>
                                        </i>
                                    <? } ?>
                                <? }?>
                            </td>
                            <td class="<?= $payment_status_class ?> text-weight-bold"
                                style="text-transform:capitalize"><?= $R['payment_status'] ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="<?= url('payments', 'payment_list', 'openingid=' . $R['id']) ?>"
                                           title="Payment List"><i class="fa-money fa"></i> Receipts</a>
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

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">

    $(function () {
        initSelectAjax('#locationid', '?module=locations&action=getLocations&format=json', 'Choose location');
        initSelectAjax('#clientid', '?module=clients&action=getClients&format=json', 'Choose client');
    });

</script>



