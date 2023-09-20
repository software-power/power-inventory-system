<style>
    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }

    @media (min-width: 768px) {
        .modal-lg {
            width: 80% !important;
        }
    }
</style>
<header class="page-header">
    <h2>Client History</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-11">
        <section class="panel">
            <div class="panel-heading">
                <h3>Client History</h3>
            </div>
            <div class="panel-body">
                <form class="">
                    <input type="hidden" name="module" value="reports">
                    <input type="hidden" name="action" value="client_history">
                    <div class="row">
                        <div class="col-md-3">
                            <span class="mr-md">Search: </span>
                            <input type="text" minlength="3" class="form-control" name="search" value="<?= $search ?>"
                                   placeholder="search client name TIN VRN">
                        </div>
                        <div class="col-md-3">
                            <span class="mr-md">From: </span>
                            <input type="date" class="form-control" name="fromdate" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-3">
                            <span class="mr-md">To: </span>
                            <input type="date" class="form-control" name="todate" value="<?= $todate ?>">
                        </div>
                        <div class="col-md-2 pt-lg">
                            <button class="btn btn-success btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <p class="text-primary mt-md mb-md text-lg"><?= $title ?></p>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-none" id="userTable" style="font-size:10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Client Name</th>
                            <th>TIN</th>
                            <th>VRN</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th class="text-center">Invoices</th>
                            <th class="text-center">Receipts</th>
                            <th class="text-center">Orders</th>
                            <th class="text-center">Proformas</th>
                            <th class="text-center">Advances</th>
                            <th class="text-center">Credit Notes</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($clients as $c) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $c['name'] ?></td>
                                <td><?= $c['tinno'] ?></td>
                                <td><?= $c['vatno'] ?></td>
                                <td><?= $c['mobile'] ?></td>
                                <td><?= $c['email'] ?></td>
                                <td class="text-center"><?= $c['invoicecount'] ?></td>
                                <td class="text-center"><?= $c['receiptcount'] ?></td>
                                <td class="text-center"><?= $c['ordercount'] ?></td>
                                <td class="text-center"><?= $c['proformacount'] ?></td>
                                <td class="text-center"><?= $c['advancecount'] ?></td>
                                <td class="text-center"><?= $c['creditnotecount'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm" data-target="#client-history-modal" data-toggle="modal"
                                            data-clientid="<?= $c['id'] ?>" data-clientname="<?= $c['name'] ?>" title="View">
                                        <i class="fa fa-list"></i> View
                                    </button>
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

<div class="modal fade" id="client-history-modal" role="dialog" aria-labelledby="client-history-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Client History</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="clientid">
                <h5>Client: <span class="text-primary clientname"></span></h5>
                <ul class="nav nav-tabs mt-md">
                    <li class="active"><a data-toggle="tab" href="#invoices"><i class="fa fa-shopping-cart"></i> Invoices</a></li>
                    <li><a data-toggle="tab" href="#invoices-detailed"><i class="fa fa-list"></i> Invoice Detailed</a></li>
                    <li><a data-toggle="tab" href="#receipts"><i class="fa fa-money"></i> Receipts</a></li>
                    <li><a data-toggle="tab" href="#orders"><i class="fa fa-truck"></i> Orders</a></li>
                    <li><a data-toggle="tab" href="#proformas"><i class="fa fa-file"></i> Proformas</a></li>
                    <li><a data-toggle="tab" href="#advances"><i class="fa fa-bank"></i> Advances</a></li>
                    <li><a data-toggle="tab" href="#sales-returns"><i class="fa fa-reply-all"></i> Credit Notes</a></li>
                </ul>
                <div class="tab-content">
                    <div id="invoices" class="tab-pane fade in active">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 d-flex align-items-center">
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getInvoices()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getInvoices()">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Base Currency <span class="text-primary"><?= $basecurrency['name'] ?></span></h5>
                                <table class="table table-bordered invoice-summary-table" style="font-size: 10pt;">
                                    <thead>
                                    <tr>
                                        <td>Invoice Type</td>
                                        <td>Count</td>
                                        <td class="text-right">Full Amount</td>
                                        <td class="text-right">Paid Amount</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Cash</td>
                                        <td class="amounts cash_count"></td>
                                        <td class="text-right amounts cash_full_amount"></td>
                                        <td class="text-right amounts cash_paid_amount"></td>
                                    </tr>
                                    <tr>
                                        <td>Credit</td>
                                        <td class="amounts credit_count"></td>
                                        <td class="text-right amounts credit_full_amount"></td>
                                        <td class="text-right amounts credit_paid_amount"></td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td class="amounts total_count"></td>
                                        <td class="text-right amounts total_full_amount"></td>
                                        <td class="text-right amounts total_paid_amount"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <h5 class="text-weight-bold">Invoices</h5>
                        <div class="table-responsive" style="max-height: 45vh">
                            <table class="table table-bordered invoice-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoiceno</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Salesperson</th>
                                    <th>Currency</th>
                                    <th class="text-right">Full Amount</th>
                                    <th class="text-right">Paid Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="invoices-detailed" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-3">
                                <label class="ml-sm">Product: <button class="btn btn-sm btn-default" onclick="resetProductSearch(this)">All Product</button></label>
                                <select class="form-control productid" onchange="getInvoiceDetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($products as $p) { ?>
                                        <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Category: </label>
                                <select class="form-control productcategoryid" onchange="getInvoiceDetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($productcategories as $pc) { ?>
                                        <option value="<?= $pc['id'] ?>"><?= $pc['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Brand: </label>
                                <select class="form-control brandid" onchange="getInvoiceDetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($brands as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">From: </label>
                                <input type="date" class="form-control fromdate" onchange="getInvoiceDetails()">
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">To: </label>
                                <input type="date" class="form-control todate" onchange="getInvoiceDetails()">
                            </div>
                        </div>
                        <h5>Items</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered invoice-detail-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoiceno</th>
                                    <th>Product name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th>Currency</th>
                                    <th>Qty</th>
                                    <th>incPrice</th>
                                    <th>VAT%</th>
                                    <th>excAmount</th>
                                    <th>incAmount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="receipts" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-4 d-flex align-items-center">
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getReceipts()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getReceipts()">
                            </div>
                        </div>
                        <h5>Receipts</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered receipt-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Receipt No</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Currency</th>
                                    <th class="text-right">Received Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="orders" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-6 d-flex align-items-center">
                                <span>Status: </span>
                                <select class="form-control order_status" onchange="getOrders()">
                                    <option value="" selected>-- All --</option>
                                    <option value="closed">Closed</option>
                                    <option value="pending">Pending</option>
                                    <option value="invalid">Invalid</option>
                                </select>
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getOrders()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getOrders()">
                            </div>
                        </div>
                        <h5>Orders</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered order-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order No</th>
                                    <th>Issued by</th>
                                    <th>Issue Date</th>
                                    <th>Valid until</th>
                                    <th>Currency</th>
                                    <th class="text-right">Order Value</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="proformas" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-10 d-flex align-items-center">
                                <span>Status: </span>
                                <select class="form-control proforma_status" onchange="getProformas()">
                                    <option value="" selected>-- All --</option>
                                    <option value="closed">Closed</option>
                                    <option value="pending">Pending</option>
                                    <option value="invalid">Invalid</option>
                                </select>
                                <span>Holding Stock: </span>
                                <select class="form-control holding_stock" onchange="getProformas()">
                                    <option value="" selected>-- All --</option>
                                    <option value="yes">Holding</option>
                                    <option value="no">Not Holding</option>
                                </select>
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getProformas()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getProformas()">
                            </div>
                        </div>
                        <h5>Proformas</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered proforma-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Proforma No</th>
                                    <th>Issued by</th>
                                    <th>Issue Date</th>
                                    <th>Valid until</th>
                                    <th>Hold stock until</th>
                                    <th>Payment Terms</th>
                                    <th>Currency</th>
                                    <th class="text-right">Total Value</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="advances" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-4 d-flex align-items-center">
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getAdvances()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getAdvances()">
                            </div>
                        </div>
                        <h5>Advances</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered advance-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ref No</th>
                                    <th>Received by</th>
                                    <th>Issue Date</th>
                                    <th>Payment Method</th>
                                    <th>Currency</th>
                                    <th class="text-right">Amount</th>
                                    <th class="text-right">Used Amount</th>
                                    <th class="text-right">Remaining Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="sales-returns" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 d-flex align-items-center">
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getCreditNotes()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getCreditNotes()">
                            </div>
                        </div>
                        <h5 class="text-weight-bold">Credit Notes</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered credit-note-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ref No</th>
                                    <th>Invoiceno</th>
                                    <th>Type</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th>Currency</th>
                                    <th class="text-right">Total Amount</th>
                                    <th>Returned Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    let historyModal = null;
    $(function () {
        initSelectAjax('.productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('.productcategoryid').select2();
        $('.brandid').select2();

        historyModal = $('#client-history-modal');
        $(historyModal).on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let clientid = source.data('clientid');
            if (!clientid) {
                triggerError('No client found!');
                return false;
            }
            $(historyModal).find('.clientid').val(clientid);
            $(historyModal).find('.clientname').text(source.data('clientname'));
            $(historyModal).find('.fromdate').val(`<?=date('Y-m-d', strtotime('-2 week'))?>`);
            $(historyModal).find('.todate').val('');
            getInvoices();
            getInvoiceDetails();
            getReceipts();
            getOrders();
            getProformas();
            getAdvances();
            getCreditNotes();
        });
    });

    function resetProductSearch(obj) {
        $(obj).closest('div').find('select.productid').select2('destroy').empty().trigger('change');
        initSelectAjax('.productid', "?module=products&action=getProducts&format=json", "Choose product");

    }

    function getInvoices() {
        let clientid = $(historyModal).find('.clientid').val();
        let tab = $(historyModal).find('#invoices');
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.amounts').text('');
        $(tab).find('.invoice-table tbody.tbody').empty();
        $.get(`?module=reports&action=getClientInvoiceHistory&format=json&clientid=${clientid}&fromdate=${fromdate}&todate=${todate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                if (result.total.cash) {
                    $(tab).find('.cash_count').text(result.total.cash.count);
                    $(tab).find('.cash_full_amount').text(numberWithCommas(result.total.cash.full_amount.toFixed(2)));
                    $(tab).find('.cash_paid_amount').text(numberWithCommas(result.total.cash.paid_amount.toFixed(2)));
                }
                if (result.total.credit) {
                    $(tab).find('.credit_count').text(result.total.credit.count);
                    $(tab).find('.credit_full_amount').text(numberWithCommas(result.total.credit.full_amount.toFixed(2)));
                    $(tab).find('.credit_paid_amount').text(numberWithCommas(result.total.credit.paid_amount.toFixed(2)));
                }
                if (result.total.total) {
                    $(tab).find('.total_count').text(result.total.total.count);
                    $(tab).find('.total_full_amount').text(numberWithCommas(result.total.total.full_amount.toFixed(2)));
                    $(tab).find('.total_paid_amount').text(numberWithCommas(result.total.total.paid_amount.toFixed(2)));
                }

                let count = 1;
                $.each(result.invoices, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="${item.url}" target="_blank">${item.receipt_no}</a>
                                    </td>
                                    <td>${item.paymenttype}</td>
                                    <td>${item.date}</td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.currencyname}</td>
                                    <td class="text-right">${numberWithCommas(item.full_amount)}</td>
                                    <td class="text-right">${numberWithCommas(item.lastpaid_totalamount)}</td>
                                </tr>`;
                    $(tab).find('.invoice-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "error found", 4000);
            }
        });
    }

    function getInvoiceDetails() {
        let clientid = $(historyModal).find('.clientid').val();
        let tab = $(historyModal).find('#invoices-detailed');
        let productid = $(tab).find('.productid').val() || '';
        let productcategoryid = $(tab).find('.productcategoryid').val() || '';
        let brandid = $(tab).find('.brandid').val() || '';
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.invoice-detail-table tbody.tbody').empty();
        $.get(`?module=reports&action=getClientInvoiceDetailHistory&format=json&clientid=${clientid}&fromdate=${fromdate}&todate=${todate}&productid=${productid}&productcategoryid=${productcategoryid}&brandid=${brandid}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.products, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="?module=sales&action=view_invoice&salesid=${item.salesid}" target="_blank">${item.invoiceno}</a>
                                    </td>
                                    <td>${item.productname}</td>
                                    <td>${item.productcategoryname}</td>
                                    <td>${item.brandname}</td>
                                    <td>${item.salesperson}</td>
                                    <td>${item.invoicedate}</td>
                                    <td>${item.currencyname}</td>
                                    <td>${item.quantity}</td>
                                    <td>${numberWithCommas(item.incprice)}</td>
                                    <td>${item.vat_rate}</td>
                                    <td>${numberWithCommas(item.excamount)}</td>
                                    <td>${numberWithCommas(item.incamount)}</td>
                                </tr>`;
                    $(tab).find('.invoice-detail-table tbody.tbody').append(row);
                    count++;
                });

            } else {
                triggerError(result.msg || "error found", 4000);
            }
        });
    }

    function getReceipts() {
        let clientid = $(historyModal).find('.clientid').val();
        let tab = $(historyModal).find('#receipts');
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.receipt-table tbody.tbody').empty();
        $.get(`?module=reports&action=getClientReceiptHistory&format=json&clientid=${clientid}&fromdate=${fromdate}&todate=${todate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.receipts, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="?module=payments&action=payment_receipt&id=${item.id}" target="_blank">${item.id}</a>
                                    </td>
                                    <td>${item.creator}</td>
                                    <td>${item.issuedate}</td>
                                    <td>
                                        <p class="m-none">${item.method}</p>
                                        <small class="text-primary">${item.method_text}</small>
                                    </td>
                                    <td>${item.currencyname}</td>
                                    <td class="text-right">${numberWithCommas(item.received_amount)}</td>
                                </tr>`;
                    $(tab).find('.receipt-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "Error found");
            }
        });
    }

    function getOrders() {
        let clientid = $(historyModal).find('.clientid').val();
        let tab = $(historyModal).find('#orders');
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let order_status = $(tab).find('.order_status').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.order-table tbody.tbody').empty();
        $.get(`?module=reports&action=getClientOrderHistory&format=json&clientid=${clientid}&fromdate=${fromdate}&todate=${todate}&order_status=${order_status}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.orders, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="?module=orders&action=order_list&ordernumber=${item.orderid}" target="_blank">${item.orderid}</a>
                                    </td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.orderdate}</td>
                                    <td>${item.valid_until}</td>
                                    <td>${item.currencyname}</td>
                                    <td class="text-right">${numberWithCommas(item.order_value)}</td>
                                    <td>${item.order_status}</td>
                                </tr>`;
                    $(tab).find('.order-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "Error found");
            }
        });
    }

    function getProformas() {
        let clientid = $(historyModal).find('.clientid').val();
        let tab = $(historyModal).find('#proformas');
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let proforma_status = $(tab).find('.proforma_status').val();
        let holding_stock = $(tab).find('.holding_stock').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.proforma-table tbody.tbody').empty();
        $.get(`?module=reports&action=getClientProformaHistory&format=json&clientid=${clientid}&fromdate=${fromdate}&todate=${todate}&proforma_status=${proforma_status}&holding_stock=${holding_stock}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.proformas, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="?module=proformas&action=print_proforma&proforma_number=${item.id}" target="_blank">${item.id}</a>
                                    </td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.proformadate}</td>
                                    <td>${item.valid_until}</td>
                                    <td>${item.hold_until}</td>
                                    <td>${item.paymentterms}</td>
                                    <td>${item.currencyname}</td>
                                    <td class="text-right">${numberWithCommas(item.proforma_value)}</td>
                                    <td>${item.proforma_status}</td>
                                </tr>`;
                    $(tab).find('.proforma-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "Error found");
            }
        });
    }

    function getAdvances() {
        let clientid = $(historyModal).find('.clientid').val();
        let tab = $(historyModal).find('#advances');
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.advance-table tbody.tbody').empty();
        $.get(`?module=reports&action=getClientAdvanceHistory&format=json&clientid=${clientid}&fromdate=${fromdate}&todate=${todate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.advances, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="?module=advance_payments&action=list&apid=${item.id}" target="_blank">${item.id}</a>
                                    </td>
                                    <td>${item.creator}</td>
                                    <td>${item.issuedate}</td>
                                    <td>
                                        <p class="m-none">${item.methodname}</p>
                                        <small class="text-primary">${item.method_text}</small>
                                    </td>
                                    <td>${item.currencyname}</td>
                                    <td class="text-right">${numberWithCommas(item.amount)}</td>
                                    <td class="text-right">${numberWithCommas(item.used_advance)}</td>
                                    <td class="text-right">${numberWithCommas(item.remaining_advance)}</td>
                                </tr>`;
                    $(tab).find('.advance-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "Error found");
            }
        });
    }

    function getCreditNotes() {
        let clientid = $(historyModal).find('.clientid').val();
        let tab = $(historyModal).find('#sales-returns');
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.credit-note-table tbody.tbody').empty();
        $.get(`?module=reports&action=getClientCreditNoteHistory&format=json&clientid=${clientid}&fromdate=${fromdate}&todate=${todate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {

                let count = 1;
                $.each(result.salesreturns, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="${item.url}" target="_blank">${item.id}</a>
                                    </td>
                                    <td>${item.invoiceno}</td>
                                    <td>${item.type}</td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.issuedate}</td>
                                    <td>${item.currencyname}</td>
                                    <td class="text-right">${numberWithCommas(item.total_incamount)}</td>
                                    <td>
                                        <p class="m-none">${numberWithCommas(item.return_amount)}</p>
                                        <small>${item.method_text}</small>
                                    </td>
                                </tr>`;
                    $(tab).find('.credit-note-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "error found", 4000);
            }
        });
    }


    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
