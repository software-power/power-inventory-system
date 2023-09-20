<style>

    .panel-actions a, .panel-actions .panel-action {
        font-size: 21px;
    }

    .input-group-rounded input.form-control:first-child, .input-group-rounded input.form-control:last-child, .input-search input.form-control:first-child, .input-search input.form-control:last-child {
        border-radius: 0;
        height: 44px;
        font-size: 15px;
    }

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

    .badge-orange {
        background-color: #47a447;
    }

    .badge-red {
        background-color: #d2322d;
    }

    .center-panel {
        width: 80%;
        margin: 0 auto;
    }

    .table-responsive {
        min-height: 150px;
    }

    .ticketholder {
        position: fixed;
        z-index: 99;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5803921568627451);
        display: none;
    }

    .table-holder {
        position: relative;
        top: 10%;
        background: white;
        width: 88%;
        max-height: 85%;
        margin: 0 auto;
        margin-left: 6%;
        overflow-x: hidden;
        overflow-y: auto;
        padding: 10px;
        border-radius: 3px;
    }


    .title-model {
        padding-top: 4px;
        float: left;
        margin-left: 26px;
    }

    .close-btn-holder {
        padding-top: 4px;
        width: 73px;
        position: absolute;
        top: 0;
        right: 0;
        background: transparent !important;
    }

    .table-responsive {
        min-height: 223px;
    }

    .badge-danger {
        background-color: #d2322d;
    }

    .badge-success {
        background-color: #47a447;
    }

    .badge-primary {
        background-color: #0099e6;
    }

    .rowcolor {
        background: #ecedf0;
        font-weight: bold;
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
    <h2>Sales</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="sales">
                <input type="hidden" name="action" value="sales_list">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Location:
                            <input type="text" class="form-control" name="invoiceno" placeholder="invoice no">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select id="branchid" class="form-control" name="branchid">
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                                <option value=""> -- All Branches --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="locationid" class="form-control" name="locationid"></select>
                        </div>
                        <div class="col-md-4">
                            Order/Invoice By:
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" class="form-control" name="userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>

                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Client
                            <select id="clientid" class="form-control" name="clientid"></select>
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
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="panel-title"><i class="fa fa-file"></i> Sales List (Invoice List)</h2>
                    <p class="text-primary"><?= $title ?></p>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-default mr-md" data-toggle="modal" data-target="#search-modal" title="Filter">
                        <i class="fa fa-search"></i> Open filter
                    </button>
                </div>
            </div>
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
                        <th>Payment Type</th>
                        <th>Payment Status</th>
                        <th>Approval Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?

                    foreach ($sales_list as $id => $R) { ?>
                        <tr>
                            <td width="30px"><?= $id + 1 ?></td>
                            <td>
                                <div><?= $R['receipt_no'] ?></div>
                                <? if ($R['billid']) { ?>
                                    <small class="text-success">Automated billing</small>
                                <? } ?>
                                <? if ($R['orderno']) { ?>
                                    <a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order
                                        no <?= $R['orderno'] ?></a>
                                <? } ?>
                                <? if ($R['fisc_invoiceid']) { ?>
                                    <div class="text-primary"><i>Converted to <?= $R['fisc_invoiceno'] ?></i></div>
                                <? } ?>
                            </td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal"
                                   data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['stocklocation'] ?></td>
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
                            <td style="text-transform:capitalize"><?= $R['paymenttype'] ?></td>
                            <td style="text-transform:capitalize"><?= $R['payment_status'] ?></td>
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
                                        <a class="dropdown-item" href="#invoice-detail-modal" data-toggle="modal"
                                           data-saleid="<?= $R['salesid'] ?>" data-currencyname="<?= $R['currencyname'] ?>" title="View">
                                            <i class="fa-file fa"></i> View Details</a>
                                        <a class="dropdown-item" href="#sales-document-modal" data-toggle="modal"
                                           data-saleid="<?= $R['salesid'] ?>" data-invoiceno="<?= $R['receipt_no'] ?>"
                                           data-approved="<?= $R['iscreditapproved'] ?>" title="attach document">
                                            <i class="fa fa-file-archive-o"></i> Documents</a>
                                        <? if ($R['fisc_invoiceid']) { ?>

                                        <? } elseif ($R['iscreditapproved']) { ?>
                                            <? if ($R['receipt_method'] == 'vfd' || $R['receipt_method'] == 'efd') { ?>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('receipts', 'vfd', 'salesid=' . $R['salesid'] . '&print_size=A4') ?>"
                                                   title="Print Tax Invoice"><i class="fa-print fa"></i> Print Tax Invoice</a>
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
                                            <? } ?>
                                            <? if ($R['has_installment']) { ?>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('receipts', 'print_installment_plan', 'salesid=' . $R['salesid']) ?>"
                                                   title="Print Tax Invoice"><i class="fa-print fa"></i> Print Installment Plan</a>
                                            <? } ?>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'print_delivery', 'salesno=' . $R['salesid']) ?>"
                                               title="print delivery note"><i class="fa-print fa"></i> Print delivery note</a>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('receipts', 'print_delivery_with_serialno', 'salesno=' . $R['salesid']) ?>"
                                               title="print delivery note"><i class="fa-print fa"></i> Print delivery note with Serial</a>
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

<?= component('sale/sales_document_modal.tpl.php') ?>
<?= component('shared/client_info_modal.tpl.php') ?>

<div class="modal fade" id="invoice-detail-modal" tabindex="-1" role="dialog" aria-labelledby="invoice-detail-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Invoice Details: <span class="text-primary receipt_no"></span>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 80vh;overflow-y: auto;">
                <div class="row">
                    <div class="col-md-4">
                        <p class="p-none m-none">Invoice No: <span class="text-primary receipt_no"></span></p>
                        <p class="p-none m-none">Client Name: <span class="text-primary clientname"></span></p>
                        <p class="p-none m-none">Vat Exempted: <span class="text-primary vat_exempted"></span></p>
                        <p class="p-none m-none">Payment Status: <span class="text-uppercase paymentstatus"
                                                                       style="font-weight: bold">Completed</span></p>
                        <p class="p-none m-none">Currency: <span class="text-primary currencyname"></span></p>
                        <p class="p-none m-none">Full Amount: <span class="text-primary full_amount"></span></p>
                        <p class="p-none m-none">Credit Note: <span class="client_tin text-rosepink salereturn_amount"></span></p>
                        <p class="p-none m-none">Total Paid: <span class="client_tin text-success paid_amount"></span></p>
                        <p class="p-none m-none">Pending Amount: <span class="text-danger pending_amount"></span></p>
                        <p class="p-none m-none">Expense Amount: <span class="text-danger expense_amount"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p class="p-none m-none">Sales ID: <span class="text-primary salesid"></span></p>
                        <p class="p-none m-none">Payment Type: <span class="text-primary paymenttype"></span></p>
                        <p class="p-none m-none">Issue Date: <span class="text-primary issue_date"></span></p>
                        <p class="p-none m-none">Sales person: <span class="client_tin text-primary sales_person"></span></p>
                        <p class="p-none m-none">Invoice Remarks: </p>
                        <div class="col-md-12">
                            <textarea class="form-control text-sm remarks" readonly rows="2"></textarea>
                        </div>
                        <p>Internal Remarks: </p>
                        <div class="col-md-12">
                            <textarea class="form-control text-sm internal_remarks" readonly rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <? if (CS_SUPPORT_INTEGRATION) { ?>
                            <div class="send-serialno-holder">
                                <div class="d-flex align-items-center ml-md">
                                    <span class="spinner-border" style="height: 30px;width: 30px;display: none"></span>
                                    <a class="btn btn-danger ml-xs" onclick="show_serial_send_spinner(this)"
                                       href=""
                                       title="Sends all serial nos to support system">
                                        <i class="fa fa-send"></i> Send Serialno to Support</a>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                </div>

                <table class="table table-hover mb-none mt-md" style="font-size:13px;" id="">
                    <thead>
                    <tr>
                        <th class="stick">#</th>
                        <th class="stick">Product Name</th>
                        <th class="stick">Print</th>
                        <th class="stick">Price</th>
                        <th class="stick">Discount</th>
                        <th class="stick" title="inclusive">Selling Price</th>
                        <th class="stick">Quantity</th>
                        <th class="stick">VAT%</th>
                        <th class="stick">VAT Amount</th>
                        <th class="stick">Total Amount</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyProducts">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#locationid', '?module=locations&action=getLocations&format=json', 'Choose location');
        initSelectAjax('#userid', '?module=users&action=getUser&format=json', 'Choose client');
        initSelectAjax('#clientid', '?module=clients&action=getClients&format=json', 'Choose client');
        $('#branchid').select2({width: '100%'});

        $('#invoice-detail-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let salesid = $(source).data('saleid');
            let currencyname = $(source).data('currencyname');
            $(this).find('p span').text('');//clear
            $(this).find('.send-serialno-holder').hide();//clear
            $(this).find('.send-serialno-holder a.btn').attr('href', '');//clear
            getSalesDetails(salesid);
        });
    });

    function getSalesDetails(salesid) {
        $('#tbodyProducts').empty();
        $.get("?module=sales&action=getSalesDetails&format=json", {salesid: salesid}, function (d) {
            let result = JSON.parse(d);
            if (result.status === "success") {
                let invoice = result.data;
                // console.log(invoice);

                //fill modal span fields
                let modal = $('#invoice-detail-modal');
                $(modal).find('.salesid').text(invoice.salesid);
                let send_url = `<?= url('serialnos', 'send_serialno_to_support') ?>&salesid=${invoice.salesid}`;
                console.log(send_url);
                $(modal).find('.send-serialno-holder a.btn').attr('href', send_url);

                $(modal).find('.clientname').text(invoice.clientname);
                $(modal).find('.vat_exempted').text(invoice.vat_exempted);
                $(modal).find('.paymentstatus').text(invoice.payment_status);
                $(modal).find('.currencyname').text(invoice.currencyname);
                if (invoice.payment_status === `<?=PAYMENT_STATUS_COMPLETE?>`) {
                    $(modal).find('.paymentstatus').css('color', 'green');
                } else if (invoice.payment_status === `<?=PAYMENT_STATUS_PARTIAL?>`) {
                    $(modal).find('.paymentstatus').css('color', 'orange');
                } else {
                    $(modal).find('.paymentstatus').css('color', 'red');
                }

                if (invoice.has_serialno == 1) $(modal).find('.send-serialno-holder').show();

                $(modal).find('.full_amount').text(numberWithCommas(invoice.full_amount));
                $(modal).find('.paid_amount').text(numberWithCommas(invoice.lastpaid_totalamount));
                $(modal).find('.pending_amount').text(numberWithCommas(invoice.pending_amount));
                $(modal).find('.salereturn_amount').text(numberWithCommas(invoice.total_increturn));
                $(modal).find('.expense_amount').text(numberWithCommas(invoice.expense_amount));
                $(modal).find('.receipt_no').text(invoice.receipt_no);
                $(modal).find('.paymenttype').text(invoice.paymenttype);
                $(modal).find('.issue_date').text(invoice.issue_date);
                $(modal).find('.sales_person').text(invoice.sales_person);
                $(modal).find('.remarks').text(invoice.remarks);
                $(modal).find('.internal_remarks').text(invoice.internal_remarks);
                let count = 1;
                $.each(invoice.products, function (index, detail) {
                    var prescription = ``;
                    if (detail.prescription) {
                        prescription = `<fieldset class="row-panel">
                                                        <legend>Prescription</legend>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <p>Doctor: <span class="doctor text-primary text-uppercase">${detail.prescription_doctor}</span></p>
                                                                <p>Hospital: <span class="hospital text-primary text-uppercase">${detail.prescription_hospital}</span></p>
                                                                <p>Referred: <span class="referral text-primary text-uppercase">${detail.referred == 1 ? 'Yes' : 'No'}</span></p>
                                                            </div>
                                                            <div class="col-md-7">
                                                                <textarea class="form-control prescription text-sm" rows="5" readonly>${detail.prescription}</textarea>
                                                            </div>
                                                        </div>
                                                    </fieldset>`;
                    }

                    var batchTable = ``;
                    if (detail.track_expire_date == 1) { //if track expire
                        var batches = ``;
                        $.each(detail.batches, function (i, batch) {
                            batches += `<tr>
                                            <td>${batch.batch_no}</td>
                                            <td style="text-align: center;">${batch.batchSoldQty}</td>
                                            <td style="text-align: right;">${batch.expire_date}</td>
                                        </tr>`;
                        });
                        batchTable = `<div class="inner-table-holder">
                                        <h5>Batches</h5>
                                        <table class="table table-bordered table-condensed" style="font-size:9pt;">
                                            <thead>
                                            <tr style="font-weight: bold;">
                                                <td>Batch No</td>
                                                <td style="text-align: center;">Sold Qty</td>
                                                <td style="text-align: right;">Expire Date</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            ${batches}
                                            </tbody>
                                        </table>
                                      </div>`;
                    }

                    let serialTable = ``;
                    if (detail.trackserialno == 1) { //if track serialno
                        // console.log(detail.serialnos);
                        let serialnos = ``;
                        $.each(detail.serialnos, function (index, item) {
                            serialnos += `<tr>
                                        <td>${index + 1}</td>
                                        <td style="text-align: center;">${item.number}</td>
                                    </tr>`;
                        });
                        serialTable = `<div class="inner-table-holder">
                                        <h5>Serial Nos.</h5>
                                        <table class="table table-bordered table-condensed" style="font-size:9pt;">
                                            <thead>
                                            <tr style="font-weight: bold;">
                                                <td>#</td>
                                                <td style="text-align: center;">Serial number</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            ${serialnos}
                                            </tbody>
                                        </table>
                                       </div>`;
                    }
                    var tableRow = `<tr>
                                        <td>${count}</td>
                                        <td>${detail.productname}</td>
                                        <td>${detail.show_print == 1 ? 'Yes' : 'No'}</td>
                                        <td>${detail.currencyname} ${numberWithCommas(detail.price)}</td>
                                        <td>${numberWithCommas(detail.discount)}</td>
                                        <td>${numberWithCommas(detail.incprice)}</td>
                                        <td>${detail.quantity}</td>
                                        <td>${detail.vat_rate}</td>
                                        <td>${numberWithCommas(detail.vat_amount)}</td>
                                        <td>${numberWithCommas(detail.total_amount)}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="10">
                                            <div class="row d-flex justify-content-end">
                                                <div class="col-md-5 prescription">
                                                ${prescription}
                                                </div>
                                                <div class="col-md-4 prescription">
                                                ${batchTable}
                                                ${serialTable}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="10"></td>
                                    </tr>`;
                    count++;
                    $('#tbodyProducts').append(tableRow);
                });
            } else {
                triggerError(result.msg || 'Error Found');
            }

        });
    }

    function show_serial_send_spinner(obj) {
        $(obj).closest('div.d-flex').find('.spinner-border').show();
    }
    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }


    function show_submit_spinner(form) {
        $(form).find('.save-spinner').show();
        $(form).find('button').prop('disabled', true);
    }
</script>
