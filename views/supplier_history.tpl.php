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
    <h2>Supplier History</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <div class="panel-heading">
                <h3>Supplier History</h3>
                <p class="text-primary mt-md"><?= $title ?></p>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-none" id="userTable" style="font-size:10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Supplier Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th class="text-center">Total GRN</th>
                            <th class="text-center">Total LPO</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($suppliers as $s) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $s['name'] ?></td>
                                <td><?= $s['mobile'] ?></td>
                                <td><?= $s['email'] ?></td>
                                <td class="text-center"><?= $s['grncount'] ?></td>
                                <td class="text-center"><?= $s['lpocount'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm" data-target="#supplier-history-modal" data-toggle="modal"
                                            data-supplierid="<?= $s['id'] ?>" data-suppliername="<?= $s['name'] ?>" title="View">
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

<div class="modal fade" id="supplier-history-modal" role="dialog" aria-labelledby="supplier-history-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Supplier History</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="supplierid">
                <h5>Supplier: <span class="text-primary suppliername"></span></h5>
                <ul class="nav nav-tabs mt-md">
                    <li class="active"><a data-toggle="tab" href="#grns"><i class="fa fa-cart-arrow-down"></i> GRNs</a></li>
                    <li><a data-toggle="tab" href="#grns-details"><i class="fa fa-list"></i> GRN Details</a></li>
                    <li><a data-toggle="tab" href="#lpos"><i class="fa fa-truck"></i> LPOs</a></li>
                    <li><a data-toggle="tab" href="#lpo-details"><i class="fa fa-list"></i> LPO Details</a></li>
                    <li><a data-toggle="tab" href="#payments"><i class="fa fa-money"></i> Payment Slips</a></li>
                </ul>
                <div class="tab-content">
                    <div id="grns" class="tab-pane fade in active">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mt-md">
                            <div class="col-md-6 d-flex align-items-center">
                                <span>Branch: </span>
                                <select class="form-control branchid" onchange="getGRNs()">
                                    <? foreach ($branches as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getGRNs()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getGRNs()">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Overall Branch Outstanding: <span class="total_outstanding text-danger"></span></h5>
                            </div>
                        </div>
                        <h5 class="text-weight-bold">GRNs</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered grn-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>GRN no</th>
                                    <th>LPO no</th>
                                    <th>Supplier Invoice no</th>
                                    <th>Type</th>
                                    <th>Track Supplier Payment</th>
                                    <th>Location</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th class="text-right">Full Amount</th>
                                    <th class="text-right">Paid Amount</th>
                                    <th class="text-right">Outstanding Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="grns-details" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-2">
                                <span>Location: </span>
                                <select class="form-control locationid" onchange="getGRNDetails()">
                                    <? foreach ($locations as $l) { ?>
                                        <option value="<?= $l['id'] ?>"><?= $l['name'] ?> - <?= $l['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Product: </label>
                                <select class="form-control productid" onchange="getGRNDetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($products as $p) { ?>
                                        <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Category: </label>
                                <select class="form-control productcategoryid" onchange="getGRNDetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($productcategories as $pc) { ?>
                                        <option value="<?= $pc['id'] ?>"><?= $pc['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Brand: </label>
                                <select class="form-control brandid" onchange="getGRNDetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($brands as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">From: </label>
                                <input type="date" class="form-control fromdate" onchange="getGRNDetails()">
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">To: </label>
                                <input type="date" class="form-control todate" onchange="getGRNDetails()">
                            </div>
                        </div>
                        <h5>Items</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered grn-detail-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>GRN no</th>
                                    <th>Location</th>
                                    <th>Product name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th>Currency</th>
                                    <th>Qty</th>
                                    <th>Billable Qty</th>
                                    <th>VAT%</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="lpos" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-3">
                                <span>Location: </span>
                                <select class="form-control locationid" onchange="getLPOs()">
                                    <? foreach ($locations as $l) { ?>
                                        <option value="<?= $l['id'] ?>"><?= $l['name'] ?> - <?= $l['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="ml-sm">From: </label>
                                <input type="date" class="form-control fromdate" onchange="getLPOs()">
                            </div>
                            <div class="col-md-3">
                                <label class="ml-sm">To: </label>
                                <input type="date" class="form-control todate" onchange="getLPOs()">
                            </div>
                        </div>
                        <h5>LPOs</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered lpo-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>LPO no</th>
                                    <th>GRN no</th>
                                    <th>Location</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th>Currency</th>
                                    <th>Full Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="lpo-details" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-2">
                                <span>Location: </span>
                                <select class="form-control locationid" onchange="getLPODetails()">
                                    <? foreach ($locations as $l) { ?>
                                        <option value="<?= $l['id'] ?>"><?= $l['name'] ?> - <?= $l['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Product: </label>
                                <select class="form-control productid" onchange="getLPODetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($products as $p) { ?>
                                        <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Category: </label>
                                <select class="form-control productcategoryid" onchange="getLPODetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($productcategories as $pc) { ?>
                                        <option value="<?= $pc['id'] ?>"><?= $pc['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">Brand: </label>
                                <select class="form-control brandid" onchange="getLPODetails()">
                                    <option value="">-- All --</option>
                                    <? foreach ($brands as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">From: </label>
                                <input type="date" class="form-control fromdate" onchange="getLPODetails()">
                            </div>
                            <div class="col-md-2">
                                <label class="ml-sm">To: </label>
                                <input type="date" class="form-control todate" onchange="getLPODetails()">
                            </div>
                        </div>
                        <h5>Items</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered lpo-detail-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>LPO no</th>
                                    <th>Location</th>
                                    <th>Product name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Issued by</th>
                                    <th>Date</th>
                                    <th>Currency</th>
                                    <th>Qty</th>
                                    <th>VAT%</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody class="tbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="payments" class="tab-pane fade">
                        <div class="mb-sm" style="position: relative">
                            <div class="loading_spinner" style="position: absolute;top: 0;right: -10px;display: none">
                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-6 d-flex align-items-center">
                                <span>Branch: </span>
                                <select class="form-control branchid" onchange="getPayments()">
                                    <? foreach ($branches as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                                <span>From: </span>
                                <input type="date" class="form-control fromdate" onchange="getPayments()">
                                <span>To: </span>
                                <input type="date" class="form-control todate" onchange="getPayments()">
                            </div>
                        </div>
                        <h5>Payments</h5>
                        <div class="table-responsive" style="max-height: 55vh">
                            <table class="table table-bordered payment-table" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment No</th>
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
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    let historyModal = null;
    $(function () {
        $('.locationid').select2();
        $('.productid').select2();
        $('.productcategoryid').select2();
        $('.brandid').select2();

        historyModal = $('#supplier-history-modal');
        $(historyModal).on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            $(historyModal).find('.supplierid').val(source.data('supplierid'));
            $(historyModal).find('.suppliername').text(source.data('suppliername'));
            $(historyModal).find('.fromdate').val(`<?=date('Y-m-d', strtotime('-2 week'))?>`);
            $(historyModal).find('.todate').val('');

            //TODO supplier history module
            getGRNs();
            getGRNDetails();
            getLPOs();
            getLPODetails();
            getPayments();
        });
    });

    function getGRNs() {
        let supplierid = $(historyModal).find('.supplierid').val();
        let tab = $(historyModal).find('#grns');
        let branchid = $(tab).find('.branchid').val();
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.total_outstanding').text('');
        $(tab).find('.grn-table tbody.tbody').empty();
        $.get(`?module=reports&action=getSupplierGRNHistory&format=json&supplierid=${supplierid}&branchid=${branchid}&fromdate=${fromdate}&todate=${todate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                $(tab).find('.total_outstanding').text(result.outstanding_amount);
                let count = 1;
                $.each(result.grns, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>${item.grnno}</td>
                                    <td>${item.lpono}</td>
                                    <td>${item.invoiceno}</td>
                                    <td>${item.type}</td>
                                    <td>${item.supplier_payment}</td>
                                    <td>${item.locationname}</td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.issuedate}</td>
                                    <td class="text-right">${item.full_amount}</td>
                                    <td class="text-right">${item.paid_amount}</td>
                                    <td class="text-right">${item.outstanding_amount}</td>
                                </tr>`;
                    $(tab).find('.grn-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "error found", 4000);
            }
        });
    }

    function getGRNDetails() {
        let supplierid = $(historyModal).find('.supplierid').val();
        let tab = $(historyModal).find('#grns-details');
        let locationid = $(tab).find('.locationid').val();
        let productid = $(tab).find('.productid').val() || '';
        let productcategoryid = $(tab).find('.productcategoryid').val() || '';
        let brandid = $(tab).find('.brandid').val() || '';
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.grn-detail-table tbody.tbody').empty();
        $.get(`?module=reports&action=getSupplierGRNDetailHistory&format=json&supplierid=${supplierid}&locationid=${locationid}&fromdate=${fromdate}&todate=${todate}&productid=${productid}&productcategoryid=${productcategoryid}&brandid=${brandid}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.grns, function (i, grn) {
                    $.each(grn.stock, function (i, item) {
                        let row = `<tr>
                                    <td>${count}</td>
                                    <td>${grn.grnnumber}</td>
                                    <td>${grn.stock_location}</td>
                                    <td>${item.productname}</td>
                                    <td>${item.productcategoryname}</td>
                                    <td>${item.brandname}</td>
                                    <td>${grn.issuedby}</td>
                                    <td>${grn.issuedate}</td>
                                    <td>${grn.currency_name}</td>
                                    <td>${item.qty}</td>
                                    <td>${item.billable_qty}</td>
                                    <td>${item.vat_percentage}</td>
                                    <td>${numberWithCommas(item.rate)}</td>
                                    <td>${numberWithCommas(item.incamount)}</td>
                                </tr>`;
                        $(tab).find('.grn-detail-table tbody.tbody').append(row);
                        count++;
                    });
                });

            } else {
                triggerError(result.msg || "error found", 4000);
            }
        });
    }

    function getLPOs() {
        let supplierid = $(historyModal).find('.supplierid').val();
        let tab = $(historyModal).find('#lpos');
        let locationid = $(tab).find('.locationid').val();
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.lpo-table tbody.tbody').empty();
        $.get(`?module=reports&action=getSupplierLPOHistory&format=json&supplierid=${supplierid}&locationid=${locationid}&fromdate=${fromdate}&todate=${todate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.lpos, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>${item.lponumber}</td>
                                    <td>${item.grnnumber}</td>
                                    <td>${item.locationname}</td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.issuedate}</td>
                                    <td>${item.currency_name}</td>
                                    <td class="text-right">${item.full_amount}</td>
                                </tr>`;
                    $(tab).find('.lpo-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "error found", 4000);
            }
        });
    }

    function getLPODetails() {
        let supplierid = $(historyModal).find('.supplierid').val();
        let tab = $(historyModal).find('#lpo-details');
        let locationid = $(tab).find('.locationid').val();
        let productid = $(tab).find('.productid').val() || '';
        let productcategoryid = $(tab).find('.productcategoryid').val() || '';
        let brandid = $(tab).find('.brandid').val() || '';
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.lpo-detail-table tbody.tbody').empty();
        $.get(`?module=reports&action=getSupplierLPODetailHistory&format=json&supplierid=${supplierid}&locationid=${locationid}&fromdate=${fromdate}&todate=${todate}&productid=${productid}&productcategoryid=${productcategoryid}&brandid=${brandid}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.lpodetails, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>${item.lpoid}</td>
                                    <td>${item.locationnname}</td>
                                    <td>${item.productname}</td>
                                    <td>${item.productcategoryname}</td>
                                    <td>${item.brandname}</td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.issuedate}</td>
                                    <td>${item.currencyname}</td>
                                    <td>${item.qty}</td>
                                    <td>${item.vat_rate}</td>
                                    <td>${numberWithCommas(item.rate)}</td>
                                    <td>${numberWithCommas(item.incamount)}</td>
                                </tr>`;
                    $(tab).find('.lpo-detail-table tbody.tbody').append(row);
                    count++;
                });

            } else {
                triggerError(result.msg || "error found", 4000);
            }
        });
    }

    function getPayments() {
        let supplierid = $(historyModal).find('.supplierid').val();
        let tab = $(historyModal).find('#payments');
        let branchid = $(tab).find('.branchid').val();
        let fromdate = $(tab).find('.fromdate').val();
        let todate = $(tab).find('.todate').val();
        let spinner = $(tab).find('.loading_spinner');
        spinner.show();
        $(tab).find('.payment-table tbody.tbody').empty();
        $.get(`?module=reports&action=getSupplierPaymentHistory&format=json&supplierid=${supplierid}&branchid=${branchid}&fromdate=${fromdate}&todate=${todate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.payments,function (i,item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>${item.id}</td>
                                    <td>${item.issuedby}</td>
                                    <td>${item.issuedate}</td>
                                    <td>
                                        <p class="m-none">${item.method}</p>
                                        <small class="text-primary">${item.method_text}</small>
                                    </td>
                                    <td>${item.currencyname}</td>
                                    <td class="text-right">${item.total_amount}</td>
                                </tr>`;
                    $(tab).find('.payment-table tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg||"Error found");
            }
        });
    }

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
