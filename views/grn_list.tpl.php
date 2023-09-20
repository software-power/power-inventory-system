<style>
    .panel-actions-grn {
        right: 15px;
        position: absolute;
        top: 15px;
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

    th.stick {
        position: sticky;
        top: 100px; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }

    @media (min-width: 768px) {
        .modal-lg {
            width: 80% !important;
        }

        .modal-md {
            width: 50% !important;
        }
    }
</style>


<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Search</h4>
            </div>
            <form>
                <input type="hidden" name="module" value="grns">
                <input type="hidden" name="action" value="grn_list">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Grn No:
                            <input type="text" class="form-control" placeholder="GRN Number" name="search[grn]">
                        </div>
                        <div class="col-md-4">
                            Lpo:
                            <input type="text" class="form-control" placeholder="LPO Number" name="search[lpo]">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select class="form-control" name="search[branchid]">
                                <option value="" selected>All Branch</option>
                                <? foreach ($branches as $key => $R) { ?>
                                    <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Supplier:
                            <select id="supplier" class="form-control" name="search[supplierid]">
                                <? if ($supplier) { ?>
                                    <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="location" class="form-control" name="search[locationid]">
                                <? if ($supplier) { ?>
                                    <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            Payment Type:
                            <select class="form-control" name="search[paymenttype]">
                                <option value="" selected>All type</option>
                                <option value="<?= PAYMENT_TYPE_CASH ?>">Cash</option>
                                <option value="<?= PAYMENT_TYPE_CREDIT ?>">Credit</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Currency:
                            <select class="form-control" name="search[currencyid]">
                                <option value="" selected>All</option>
                                <? foreach ($currencies as $index => $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?>
                                        - <?= $c['description'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Issued by:
                            <? if (Users::can(OtherRights::approve_other_grn)) { ?>
                                <select id="userid" class="form-control" name="search[createdby]"></select>
                            <? } else { ?>
                                <input type="hidden" name="search[createdby]" value="<?= $creator['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $creator['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="search[from]" value="<?= $fromdate ?>"
                                   class="form-control for-input">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="search[to]" value="<?= $todate ?>" class="form-control for-input">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>


<header class="page-header">
    <h2>GRN List</h2>
</header>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions-grn">
                <button id="openModel" class="btn" data-toggle="modal" data-target="#search-modal" title="Home"><i
                            class="fa fa-search"></i> Open filter
                </button>
                <? if (!CS_GRN_REQUIRE_LPO && Users::can(OtherRights::add_grn)) { ?>
                    <a href="?module=grns&action=grn_full_edit" class="btn grn-list">
                        <i class="fa fa-plus"></i> Create GRN</a>
                <? } ?>
            </div>
            <h2 class="panel-title">GRN List</h2>
            <p class="text-primary"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="forCheckbox table table-hover mb-none" style="font-size: 10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>GRN #</th>
                        <th>LPO #</th>
                        <th>Location</th>
                        <th>Supplier Name</th>
                        <th>Supplier Payment Tracking</th>
                        <th>Payment Type</th>
                        <th>Currency</th>
                        <th>Total Amount</th>
                        <th>Total VAT Amount</th>
                        <th>Full Amount</th>
                        <th>Adjustment Amount</th>
                        <th>Issued By</th>
                        <th>Issued date</th>
                        <th>Approval</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    <? $count = 1;
                    $USER_CAN_APPROVE = Users::can(OtherRights::approve_other_grn) || Users::can(OtherRights::approve_grn);
                    $USER_CAN_EDIT = Users::can(OtherRights::edit_grn);
                    $USER_CAN_CANCEL = Users::can(OtherRights::cancel_grn);
                    foreach ($grnlist as $id => $R) { ?>
                        <tr id="asset<?= $count ?>">
                            <td><?= $count ?></td>
                            <td><?= $R['grnnumber'] ?></td>
                            <td><?= $R['lponumber'] ? $R['lponumber'] : '-' ?></td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td><?= $R['supplier_payment'] ? 'Yes' : 'No' ?></td>
                            <td><?= strtoupper($R['paymenttype']) ?></td>
                            <td><?= $R['currency_name'] ?></td>
                            <td>
                                <p><?= formatN($R['total_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <span class="text-xs text-muted">
                                        <?= $baseCurrency['name'] ?> <?= formatN($R['base_total_amount']) ?>
                                    </span>
                                <? } ?>
                            </td>
                            <td>
                                <? if (!$R['vat_registered']) { ?>
                                    <small><i>Not VAT registered</i></small>
                                <? } else { ?>
                                    <p><?= formatN($R['grand_vatamount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <span class="text-xs text-muted">
                                        <?= $baseCurrency['name'] ?> <?= formatN($R['base_grand_vatamount']) ?>
                                    </span>
                                    <? } ?>
                                <? } ?>
                            </td>
                            <td>
                                <p><?= formatN($R['full_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <span class="text-xs text-muted">
                                        <?= $baseCurrency['name'] ?> <?= formatN($R['base_full_amount']) ?>
                                    </span>
                                <? } ?>
                            </td>
                            <td><?= formatN($R['adjustment_amount']) ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['issuedate']) ?></td>
                            <td>
                                <? if ($R['approver']) { ?>
                                    <? if ($R['auto_approve']) { ?>
                                        <p class="text-success">Auto approved</p>
                                    <? } else { ?>
                                        <p class="text-success"><small><?= $R['approver'] ?>
                                                , <?= fDate($R['approval_date'], 'd M Y H:i') ?></small></p>
                                    <? } ?>
                                <? } else { ?>
                                    <small>not approved</small>
                                <? } ?>
                            </td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <? if (!$R['approver'] && $USER_CAN_APPROVE) { ?>
                                            <a class="dropdown-item" href="#approve-modal" data-toggle="modal"
                                               title="Approve GRN" data-id="<?= $R['grnnumber'] ?>"
                                               data-lpono="<?= $R['lponumber'] ? $R['lponumber'] : '-' ?>"
                                               data-suppliername="<?= $R['suppliername'] ?>"
                                               data-invoiceno="<?= $R['invoiceno'] ?>"
                                               data-verificationcode="<?= $R['verificationcode'] ?>"
                                               data-currency="<?= $R['currency_name'] ?>"
                                               data-exchange-rate="<?= $R['currency_amount'] ?>"
                                               data-paymenttype="<?= $R['paymenttype'] ?>"
                                               data-issuedby="<?= $R['issuedby'] ?>"
                                               data-vatregistered="<?= $R['vat_registered'] ?>"
                                               data-vatdesc="<?= $R['vat_desc'] ?>"
                                               data-excamount="<?= formatN($R['total_amount']) ?>"
                                               data-vatamount="<?= formatN($R['grand_vatamount']) ?>"
                                               data-incamount="<?= formatN($R['full_amount']) ?>"
                                               data-adjamount="<?= formatN($R['adjustment_amount']) ?>"
                                               data-issuedate="<?= fDate($R['issuedate']) ?>">
                                                <i class="fa fa-check-circle-o text-success"></i> Approve</a>

                                        <? } ?>
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('grns', 'view_grn', 'grn=' . $R['grnnumber']) ?>"
                                           title="View GRN"><i class="fa fa-list"></i> view GRN</a>
                                        <? if ($R['approver']) { ?>
                                            <? if ($USER_CAN_EDIT) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('grns', 'grn_edit', 'grnid=' . $R['grnnumber']) ?>"
                                                   onclick="return confirm('Do you want to edit this GRN?')"
                                                   title="Edit GRN"><i class="fa fa-pencil"></i> Edit GRN</a>
                                            <? } ?>
                                            <a class="dropdown-item"
                                               href="#select-product-modal" data-toggle="modal"
                                               data-grnid="<?= $R['grnnumber'] ?>"
                                               data-supplier="<?= $R['suppliername'] ?>"
                                               title="Cancel GRN"><i class="fa fa-reply"></i> Goods Return</a>
                                            <? if ($R['supplier_payment']) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('suppliers', 'grn_payments', ['grnid' => $R['grnnumber']]) ?>"
                                                   title="Supplier Payments"><i class="fa fa-money"></i> Supplier Payments</a>
                                            <? } ?>
                                            <? if ($R['transfer_tally'] && !$R['tally_post']) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('grns', 'tally_post', ['grnno' => $R['grnnumber']]) ?>"
                                                   title="Supplier Payments"><i class="fa fa-upload"></i> Post Tally</a>
                                            <? } ?>
                                        <? } ?>
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('grns', 'print_grn', 'grn=' . $R['grnnumber']) ?>"
                                           title="Print GRN"><i class="fa fa-print"></i> Print GRN</a>
                                        <? if (!$R['approver']) { ?>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('grns', 'print_grn',['grn'=>$R['grnnumber'],'includestock'=>'']) ?>"
                                               title="Print GRN With current stock"><i class="fa fa-print"></i> Print GRN With Current Stock</a>
                                        <? } ?>
                                        <? if ($USER_CAN_CANCEL) { ?>
                                            <form action="<?= url('grns', 'cancel_grn') ?>" method="post"
                                                  style="margin:0;"
                                                  onsubmit="return confirm('Do you want to delete this grn?')">
                                                <input type="hidden" name="grnid" value="<?= $R['grnnumber'] ?>">
                                                <button class="dropdown-item"><i class="fa fa-close"></i> Cancel GRN
                                                </button>
                                            </form>
                                        <? } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <? $count++;
                    } ?>
                    <input type="hidden" id="count" value="<?= $count ?>"/>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="select-product-modal" tabindex="-1" role="dialog" aria-labelledby="select-product-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Choose Return Products</h4>
            </div>
            <form action="<?= url('grns', 'grn_return') ?>" method="post" onsubmit="return validateForm()">
                <div class="modal-body">
                    <input type="hidden" name="grnid" class="grnid">
                    <h5>Grn No: <span class="text-primary grnno">12</span></h5>
                    <h5>Supplier: <span class="text-primary supplier">Supplier name</span></h5>
                    <div class="col-md-12 mt-md mb-md">
                        <button type="button" class="btn btn-sm btn-success" onclick="selectAll(true)">Check</button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="selectAll(false)">Uncheck</button>
                    </div>
                    <table class="table table-bordered" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th class="stick">#</th>
                            <th class="stick">Product</th>
                            <th class="stick">Qty</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="approve-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="modal-title">Approve GRN No: <span class="grnno text-primary"></span></h4>
                    <div class="d-flex align-items-center mr-md">
                        <object id="approve-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="50"
                                width="50" style="visibility: hidden"></object>
                        <form action="<?= url('grns', 'approve_grn') ?>" method="post"
                              style="margin: 0;" onsubmit="return showSpinner();">
                            <input type="hidden" class="grnid" name="grnid">
                            <button id="approve-btn" class="btn btn-success btn-sm" title="Approve GRN">Approve</button>
                        </form>
                        <form class="ml-xs" style="margin: 0;">
                            <input type="hidden" name="module" value="grns">
                            <input type="hidden" name="action" value="grn_full_edit">
                            <input type="hidden" class="grnid" name="grnid">
                            <button class="btn btn-primary btn-sm" title="Edit GRN">Edit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div id="content-holder">
                    <div class="row">
                        <div class="col-md-6">
                            <div>Supplier: <span class="suppliername text-primary"></span></div>
                            <div>Supplier Invoice: <span class="invoiceno text-primary"></span></div>
                            <div>Verification code: <span class="verificationcode text-primary"></span></div>
                            <div>Lpo no: <span class="lpono text-primary"></span></div>
                            <div>VAT registered: <span class="vat-registered text-primary"></span></div>
                            <div>VAT Description:</div>
                            <div class="row">
                                <div class="col-md-8">
                                    <textarea readonly class="form-control text-sm vat-desc" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>Date: <span class="issuedate text-primary"></span></div>
                            <div>Issued By: <span class="issuedby text-primary"></span></div>
                            <div>Payment Type: <span class="paymenttype text-primary text-uppercase"></span></div>
                            <div>Currency: <span class="currency text-primary"></span></div>
                            <div>Exchange Rate: <span class="exchange_rate text-primary"></span></div>
                        </div>
                    </div>
                    <div id="loading-details-spinner" style="visibility: hidden">
                        <div class="d-flex align-items-center">
                            <object data="images/loading_spinner.svg" type="image/svg+xml" height="50"
                                    width="50"></object>
                            <span class="ml-sm">Loading ...</span>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 50vh;">
                        <table class="table table-bordered mt-xl" style="font-size: 9pt">
                            <thead>
                            <tr>
                                <th class="stick" style="top: 0; ">#</th>
                                <th class="stick" style="top: 0; ">Product</th>
                                <th class="stick" style="top: 0; ">Qty</th>
                                <th class="stick" style="top: 0; ">Billable Qty</th>
                                <th class="stick" style="top: 0; " align="right">Rate</th>
                                <th class="stick" style="top: 0; " align="right">Quick Sale Price</th>
                                <th class="stick" style="top: 0; " align="right">Purchase Cost</th>
                            </tr>
                            </thead>
                            <tbody class="grn-details">
                            </tbody>
                        </table>
                    </div>
                    <div class="row d-flex justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between">
                                <span>Exclusive Amount</span>
                                <span class="text-weight-bold excamount"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>VAT Amount</span>
                                <span class="text-weight-bold vatamount"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Adjustment Amount</span>
                                <span class="text-weight-bold adjamount"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Inclusive Amount</span>
                                <span class="text-weight-bold incamount"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default btn-sm"
                        data-dismiss="modal">Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {

        initSelectAjax('#location', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        initSelectAjax('#supplier', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);

        $('#select-product-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let grnid = source.data('grnid');
            let supplier = source.data('supplier');
            let modal = $(this);
            let tableBody = $(modal).find('table tbody');


            tableBody.empty();

            $(modal).find('.grnid').val(grnid);
            $(modal).find('.grnno').text(grnid);
            $(modal).find('.supplier').text(supplier);

            //ajax
            $.get(`?module=grns&action=getGrnProducts&format=json&grnid=${grnid}`, null, function (data) {
                let result = JSON.parse(data);
                // console.log(result);
                if (result.status === 'found') {
                    $.each(result.grn.details, function (i, item) {
                        let row = `<tr>
                                        <td style="width: 80px;">
                                            <div class="checkbox text-center m-none">
                                                <label>
                                                    <input type="checkbox" name="gdi[]" value="${item.id}">
                                                </label>
                                            </div>
                                        </td>
                                        <td>${item.productname}</td>
                                        <td>${item.qty}</td>
                                    </tr>`;
                        tableBody.append(row);
                    });
                } else {
                    triggerError('GRN not found', 3000);
                }
            });
        });

        $('#approve-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let grnid = source.data('id');
            let modal = $(this);
            $(modal).find('.grnno').text(grnid);
            $(modal).find('.grnid').val(grnid);
            $(modal).find('.suppliername').text(source.data('suppliername'));
            $(modal).find('.invoiceno').text(source.data('invoiceno'));
            $(modal).find('.verificationcode').text(source.data('verificationcode'));
            $(modal).find('.lpono').text(source.data('lpono'));
            $(modal).find('.vat-registered').text(source.data('vatregistered') == 1 ? 'Yes' : 'No');
            $(modal).find('.vat-desc').val(source.data('vatdesc'));
            $(modal).find('.issuedate').text(source.data('issuedate'));
            $(modal).find('.issuedby').text(source.data('issuedby'));
            $(modal).find('.currency').text(source.data('currency'));
            $(modal).find('.exchange_rate').text(source.data('exchange-rate'));
            $(modal).find('.paymenttype').text(source.data('paymenttype'));
            $(modal).find('.excamount').text(source.data('excamount'));
            $(modal).find('.vatamount').text(source.data('vatamount'));
            $(modal).find('.incamount').text(source.data('incamount'));
            $(modal).find('.adjamount').text(source.data('adjamount'));

            getGrnDetails(grnid, modal);
        });
    });

    function showSpinner() {
        $('#approve-spinner').css('visibility', 'visible');
        $('#approve-btn').prop('disabled', true);
    }

    function getGrnDetails(grnid, modal) {
        let spinner = $('#loading-details-spinner');
        spinner.css('visibility', 'visible');
        $(modal).find('tbody').empty();
        $.get(`?module=grns&action=getGrnProducts&format=json&grnid=${grnid}`, null, function (data) {
            spinner.css('visibility', 'hidden');
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'found') {
                let count = 1;
                $.each(result.grn.details, function (i, item) {
                    let batchTable = ``;
                    if (item.batches) {
                        let batchRows = ``;
                        $.each(item.batches, function (bi, batch) {
                            batchRows += `<tr>
                                            <td>${batch.batch_no}</td>
                                            <td>${batch.qty}</td>
                                            <td>${batch.expire_date}</td>
                                        </tr>`;
                        });
                        batchTable = `<tr>
                                        <td colspan="7">
                                            <div class="col-md-5 col-md-offset-7">
                                                <table class="table table-bordered batches" style="font-size: 8pt">
                                                    <thead>
                                                    <tr>
                                                        <th>Batch no</th>
                                                        <th>Qty</th>
                                                        <th>Expire date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>${batchRows}</tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>`;
                    }

                    let quick_sale_price = parseFloat(item.quick_sale_price) || 0;
                    quick_sale_price = quick_sale_price === 0 ? 'n/a' : numberWithCommas(quick_sale_price);
                    let
                        row = `<tr>
                                   <td>${count}</td>
                                   <td>${item.productname}</td>
                                   <td>${item.qty}</td>
                                   <td>${item.billable_qty}</td>
                                   <td align="right">${numberWithCommas(item.rate)}</td>
                                   <td align="right">${quick_sale_price}</td>
                                   <td align="right">${numberWithCommas(item.incamount.toFixed(2))}</td>
                               </tr>
                               ${batchTable}`;
                    $(modal).find('.grn-details').append(row);
                    count++;
                });
            } else {
                triggerError('GRN not found');
                $(modal).modal('hide');
            }
        });
    }

    function selectAll(check) {
        if (check) {
            $('#select-product-modal').find('table tbody input:checkbox').prop('checked', true);
        } else {
            $('#select-product-modal').find('table tbody input:checkbox').prop('checked', false);
        }
    }

    function validateForm() {
        if ($('#select-product-modal').find('table tbody input:checkbox:checked').length < 1) {
            triggerError('Select at least one product!', 3000);
            return false;
        }
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>