<style media="screen">
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
<header class="page-header">
    <h2>Credit Notes</h2>
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
                <input type="hidden" name="module" value="sales_returns">
                <input type="hidden" name="action" value="list">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select class="form-control" name="branchid">
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Location</label>
                            <select id="locationid" class="form-control" name="locationid"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Client</label>
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-4">
                            <label for="">Invoice No</label>
                            <input type="text" name="invoiceno" class="form-control" placeholder="invoice no">
                        </div>
                        <div class="col-md-4">
                            <label for="">Issued by</label>
                            <? if (Users::can(OtherRights::approve_other_credit_note)) { ?>
                                <select id="userid" class="form-control" name="userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $creator['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $creator['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            <label>Type</label>
                            <select class="form-control" name="return_type">
                                <option value="" selected>-- All --</option>
                                <option value="<?= SalesReturns::TYPE_PRICE ?>">Price Change</option>
                                <option value="<?= SalesReturns::TYPE_ITEM ?>">Items return</option>
                                <option value="<?= SalesReturns::TYPE_FULL ?>">Full return</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-4">
                            <label>Status</label>
                            <select class="form-control" name="return_status">
                                <option value="" selected>-- All --</option>
                                <option value="approved">Approved</option>
                                <option value="not_approved">Not Approved</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">From</label>
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="">To</label>
                            <input type="date" name="todate" class="form-control">
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

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading d-flex justify-content-between align-items-start">
            <div>
                <h2 class="panel-title">Credit Notes</h2>
                <p class="text-primary"><?= $title ?></p>
            </div>
            <div class="d-flex">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search-modal"><i class="fa fa-search"></i> Search
                </button>
                <a href="?module=home&action=index" class="btn btn-default ml-xs"><i class="fa fa-home"></i> Home</a>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>No.</th>
                        <th>Invoice no</th>
                        <th>Type</th>
                        <th>Client</th>
                        <th>Location</th>
                        <th>Issued By</th>
                        <th>Issued Date</th>
                        <th>Currency</th>
                        <th>Total amount</th>
                        <th>Amount Returned</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $count = 1;
                    foreach ($list as $index => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= getCreditNoteNo($R['id']) ?></td>
                            <td>
                                <a href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>"><?= $R['invoiceno'] ?></a>
                            </td>
                            <td class="text-capitalize">
                                <? if ($R['type'] == SalesReturns::TYPE_PRICE) { ?>
                                    Price Correction
                                <? } elseif ($R['type'] == SalesReturns::TYPE_FULL) { ?>
                                    Full invoice
                                <? } else { ?>
                                    Items return
                                <? } ?>
                            </td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td>
                                <p><?= formatN($R['total_incamount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_total_incamount']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td>
                                <? if ($R['apid']) { ?>
                                    <a href="<?= url('advance_payments', 'list', ['apid' => $R['apid']]) ?>">
                                        <?= formatN($R['advance_amount']) ?> advance receipt</a>
                                <? } elseif ($R['return_method']) { ?>
                                    <span class="text-rosepink">
                                    <?= formatN($R['return_amount']) ?>
                                        <? if ($R['return_method'] == PaymentMethods::CASH) { ?>Cash<? } ?>
                                        <? if ($R['return_method'] == PaymentMethods::BANK) { ?>
                                            Bank Name: <?= $R['bankname'] ?: $R['bank_name'] ?>, Reference: <?= $R['bankreference'] ?: $R['bank_accno'] ?>
                                        <? } ?>
                                        <? if ($R['return_method'] == PaymentMethods::CHEQUE) { ?>
                                            <?= "Cheque No: {$R['chequename']}, Cheque Type: {$R['chequetype']}" ?>
                                        <? } ?>
                                        <? if ($R['return_method'] == PaymentMethods::CREDIT_CARD) { ?>
                                            <?= "Reference: {$R['electronic_account']}\n {$R['credit_cardno']}" ?>
                                        <? } ?>
                            </span>
                                <? } ?>
                            </td>
                            <td>
                                <? if ($R['return_status'] == 'approved') { ?>
                                    <p class="text-success"><small><?= $R['approver'] ?> <?= fDate($R['approval_date']) ?></small></p>
                                <? } elseif ($R['return_status'] == 'canceled') { ?>
                                    <p class="text-rosepink">Canceled</p>
                                <? } else { ?>
                                    <p class="text-muted">not approved</p>
                                <? } ?>
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <? if ($R['return_status'] == 'approved') { ?>
                                            <? if (!$R['tally_post'] && $R['transfer_tally']) { ?>
                                                <a class="dropdown-item" title="Print credit note"
                                                   href="<?= url('sales_returns', 'post_tally', ['returnno' => $R['id']]) ?>">
                                                    <i class="fa fa-upload"></i> Post Tally</a>
                                            <? } ?>
                                            <a class="dropdown-item" target="_blank" title="Print credit note"
                                               href="?module=sales_returns&action=print_credit_note&returnno=<?= $R['id'] ?>">
                                                <i class="fa fa-print"></i> Print</a>
                                        <? } ?>
                                        <a class="dropdown-item" title="Approve credit note"
                                           href="?module=sales_returns&action=view&returnno=<?= $R['id'] ?>">
                                            <i class="fa fa-list"></i> View</a>
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

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'choose client');
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'choose location');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'choose user');

        $('#stock-holding-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            $(modal).find('.proformaid').val(source.data('proformaid'));
            console.log(source.data('extending'));
            if (source.data('extending') === 1) {
                $(modal).find('.modal-title').text('Extend Holding days');
                $(modal).find('.hold-action').text('Extend days');
                $(modal).find('.holding-days').val('1');
                $(modal).find('.extending').val(1);
            } else {
                $(modal).find('.modal-title').text('Hold Stock');
                $(modal).find('.hold-action').text('Holding days');
                $(modal).find('.holding-days').val(`<?=CS_PROFORMA_STOCK_HOLDING_DAYS?>`);
                $(modal).find('.extending').val('');
            }
        })
    });
</script>
