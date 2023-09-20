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
    <h2>List of Proforma</h2>
</header>

<?
$CAN_APPROVE_OTHER_INVOICE = Users::can(OtherRights::approve_other_credit_invoice);
$CAN_SALE_OTHER_ORDER = Users::can(OtherRights::sale_other_order);
?>

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
                <input type="hidden" name="module" value="proformas">
                <input type="hidden" name="action" value="proforma_list">
                <div class="modal-body">
                    <div class="row">
                        <? if ($CAN_APPROVE_OTHER_INVOICE || $CAN_SALE_OTHER_ORDER) { ?>
                            <div class="col-md-4">
                                <label for="">Branch</label>
                                <select id="branchid" class="form-control" name="branchid">
                                    <? if ($CAN_APPROVE_OTHER_INVOICE) { ?>
                                        <option value="">-- All Branch --</option>
                                    <? } ?>
                                    <? foreach ($branches as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Client</label>
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Issued by</label>
                            <? if ($CAN_APPROVE_OTHER_INVOICE || $CAN_SALE_OTHER_ORDER) { ?>
                                <select id="userid" class="form-control" name="issuedby"></select>
                            <? } else { ?>
                                <input type="hidden" name="issuedby" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            <label for="">Proforma Status</label>
                            <select class="form-control" name="proforma_status">
                                <option value="" selected disabled>--Choose Status--</option>
                                <option value="closed">Closed</option>
                                <option value="pending">Pending</option>
                                <option value="under order">Under Order</option>
                                <option value="invalid">Invalid</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Holding Stock</label>
                            <select class="form-control" name="stock_holding">
                                <option value="" selected disabled>-- All --</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
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
                <h2 class="panel-title">List of Proforma</h2>
                <h5 class="text-primary"><?= $title ?></h5>
            </div>
            <div class="d-flex">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search-modal"><i class="fa fa-search"></i> Search
                </button>
                <? if (Users::can(OtherRights::create_proforma)) { ?>
                    <a href="?module=proformas&action=create_proforma" class="btn btn-default ml-xs"><i class="fa fa-plus"></i> Create proforma</a>
                <? } ?>
                <a href="?module=home&action=index" class="btn btn-default ml-xs"><i class="fa fa-home"></i> Home</a>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Prof No.</th>
                        <th>Client</th>
                        <th>Location</th>
                        <th>Issued By</th>
                        <th>Issued Date</th>
                        <th>Valid until</th>
                        <th class="text-center">Holding Stock until</th>
                        <th>Currency</th>
                        <th>Proforma Total</th>
                        <th>Payment Terms</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $USER_CAN_EDIT = Users::can(OtherRights::edit_proforma);
                    foreach ($proformaList as $index => $R) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="text-center">
                                <div><?= $R['id'] ?></div>
                                <? if ($R['orderid']) { ?>
                                    <small class="d-block text-primary">Order No: <?= $R['orderid'] ?></small>
                                <? } ?>
                                <? if ($R['salesid']) { ?>
                                    <small class="d-block text-primary">Invoice No: <?= $R['invoiceno'] ?></small>
                                <? } ?>
                            </td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                            <td><?= $R['proforma_status'] == 'pending' ? fDate($R['valid_until'], 'd F Y H:i') : '' ?></td>
                            <td class="text-center <?= $R['isholding'] ? 'text-rosepink' : '' ?>"><?= $R['isholding'] ? fDate($R['hold_until'], 'd F Y H:i') : '-' ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td>
                                <p><?= formatN($R['proforma_value']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_proforma_value']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td class="text-uppercase"><?= $R['paymentterms'] ?></td>
                            <td class="text-capitalize">
                                <? if ($R['proforma_status'] == 'closed') { ?>
                                    <span class="badge badge-green"><?= $R['proforma_status'] ?></span>
                                <? } elseif ($R['proforma_status'] == 'under order') { ?>
                                    <span class="badge badge-primary"><?= $R['proforma_status'] ?></span>
                                <? } elseif ($R['proforma_status'] == 'pending') { ?>
                                    <span class="badge badge-red"><?= $R['proforma_status'] ?></span>
                                <? } elseif ($R['proforma_status'] == 'invalid') { ?>
                                    <span class="badge badge-orange"><?= $R['proforma_status'] ?></span>
                                <? } ?>
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- Dropdown menu links -->


                                        <? if ($R['sales_status'] == Orders::STATUS_PENDING) { ?>
                                            <? if ($USER_CAN_EDIT) { ?>
                                                <a class="dropdown-item" href="?proformaid=<?= $R['id'] ?>&module=proformas&action=create_proforma"
                                                   title="Edit Proforma"><i class="fa fa-edit"></i> Edit Proforma</a>
                                            <? } ?>
                                            <? if ($R['proforma_status'] == 'pending') { ?>
                                                <? if ($R['isholding']) { ?>
                                                    <a class="dropdown-item" href="#stock-holding-modal" data-toggle="modal"
                                                       title="extend stock holding days" data-proformaid="<?= $R['id'] ?>" data-extending="1">
                                                        <i class="fa fa-long-arrow-up"></i> Extend Holding Days</a>
                                                    <form action="<?= url('proformas', 'cancel_holding') ?>" method="post" class="m-none"
                                                          onsubmit="return confirm('Do you want to stop holding stock for this proforma?')">
                                                        <input type="hidden" name="proforma_no" value="<?= $R['id'] ?>">
                                                        <button class="dropdown-item"><i class="fa fa-close"></i> Cancel Holding</button>
                                                    </form>
                                                <? } else { ?>
                                                    <a class="dropdown-item" href="#stock-holding-modal" data-toggle="modal"
                                                       title="hold product in stock" data-proformaid="<?= $R['id'] ?>">
                                                        <i class="fa fa-database"></i> Hold Stock</a>
                                                <? } ?>
                                                <a class="dropdown-item" href="?proforma_no=<?= $R['id'] ?>&module=sales&action=add_sales_new"
                                                   title="Make normal sale"><i class="fa fa-money"></i> Make Sales</a>
                                                <a class="dropdown-item" href="?proforma_no=<?= $R['id'] ?>&module=orders&action=add_order"
                                                   title="Make normal order"><i class="fa fa-cart-arrow-down"></i> Normal Order</a>
                                            <? } ?>
                                        <? } else { ?>
                                            <? if ($R['proforma_status'] == 'closed') { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('sales', 'add_sales_new', ['proforma_no' => $R['id'], 'reuse' => '']) ?>"
                                                   title="Reuse proforma for normal sale"><i class="fa fa-money"></i> Reuse make Sales</a>
                                                <a class="dropdown-item"
                                                   href="<?= url('orders', 'add_order', ['proforma_no' => $R['id'], 'reuse' => '']) ?>"
                                                   title="Reuse proforma for normal order"><i class="fa fa-cart-arrow-down"></i> Reuse Normal
                                                    Order</a>
                                            <? } ?>
                                        <? } ?>
                                        <a class="dropdown-item"
                                           href="<?= url('proformas', 'create_proforma', ['proformaid' => $R['id'], 'copy' => '']) ?>"
                                           title="Copy Proforma">
                                            <i class="fa fa-copy"></i> Duplicate Proforma</a>
                                        <a class="dropdown-item" target="_blank"
                                           href="?module=proformas&action=print_proforma&proforma_number=<?= $R['id'] ?>" title="Print Proforma"><i
                                                    class="fa fa-print"></i> Print Proforma</a>
                                        <a class="dropdown-item" target="_blank"
                                           href="?module=proformas&action=print_proforma&with_bank_info=0&proforma_number=<?= $R['id'] ?>"
                                           title="Print Proforma with bank info"><i class="fa fa-print"></i> Print Proforma with bank info</a>
                                        <a class="dropdown-item" target="_blank"
                                           href="?module=proformas&action=print_proforma&with_image=0&proforma_number=<?= $R['id'] ?>"
                                           title="Print Proforma"><i
                                                    class="fa fa-print"></i> Print Proforma with Picture</a>
                                        <a class="dropdown-item" target="_blank"
                                           href="?module=proformas&action=print_proforma&with_image=0&with_bank_info=0&proforma_number=<?= $R['id'] ?>"
                                           title="Print Proforma"><i
                                                    class="fa fa-print"></i> Print Proforma with Picture with bank info</a>
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

<div class="modal fade" id="stock-holding-modal" role="dialog" aria-labelledby="stock-holding-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Hold Stock</h4>
            </div>
            <form method="post" action="<?= url('proformas', 'hold_stock') ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Proforma no</label>
                            <input type="text" readonly name="proforma_no" class="form-control proformaid">
                            <input type="hidden" name="extending" class="extending">
                        </div>
                        <div class="col-md-6">
                            <label for="" class="hold-action">Holding Days</label>
                            <input type="number" name="hold_days" class="form-control holding-days" min="1"
                                   value="<?= CS_PROFORMA_STOCK_HOLDING_DAYS ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Confirm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>


<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'choose client');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        $('#branchid').select2({width:"100%"});

        $('#stock-holding-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            $(modal).find('.proformaid').val(source.data('proformaid'));
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
