<style>
    .selected-sale, .selected-sale:hover {
        background: #2d8ed2 !important;
        color: #ffffff !important;
    }

    .selected-sale .text-danger {
        color: white !important;
    }

    #spinnerHolder {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100vh;
        width: 100%;
        display: none;
        background-color: black;
        opacity: 0.5;
        z-index: 10000;
    }

</style>
<header class="page-header">
    <h2>Previous Outstanding</h2>
</header>

<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="modal-title" id="myModalLabel">
                        Approve Credit Invoice: <span class="text-primary receiptno"
                                                      style="font-weight: bold"><?= $sale['receipt_no'] ?></span>
                    </h4>
                    <div id="spinner" style="display: none">
                        <div class="d-flex align-items-center">
                            <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml"
                                    height="100"
                                    width="100"></object>
                            <h4>Please wait..</h4>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="<?= url('payments', 'approve_invoice', ['salesid' => $sale['salesid']]) ?>"
                            <? if ($pending_after_approve > $client['credit_limit'] && $client['credit_limit'] > 0) { ?>
                                onclick="return confirm('Client will exceed credit limit\nDo you want to continue?')"
                            <? } ?>
                           class="btn btn-success">Continue Approval</a>
                        <? if (!$sale['has_combine']) { ?>
                            <a href="<?= $sale['source'] == Sales::SOURCE_DETAILED
                                ? url('sales', 'add_sales_new', ['id' => $sale['salesid']])
                                : url('pos', 'quick_sales', ['salesid' => $sale['salesid']]) ?>"
                               class="btn btn-warning btn-sm ml-xs editBtn"><i class="fa fa-edit"></i> Edit</a>
                        <? } ?>
                        <form action="<?= url('sales', 'cancel_sale') ?>" method="POST" style="margin:0;"
                              class="cancel-sale-form d-flex align-items-center" title="cancel sale"
                              onsubmit="return confirm('Do you want to cancel this sale?')">
                            <input type="hidden" class="salesno" name="salesno" value="<?= $sale['salesid'] ?>">
                            <button class="btn btn-danger btn-sm ml-xs">Cancel Sale</button>
                        </form>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <h5>Client name: <span class="text-primary"><?= $client['name'] ?></span></h5>
                <h5>Mobile: <span class="text-primary"><?= $client['mobile'] ?></span></h5>
                <h5>Email: <span class="text-primary"><?= $client['email'] ?></span></h5>
                <h5>Address: <span class="text-primary"><?= $client['address'] ?></span></h5>
                <div>
                    <? if (count($contacts) > 0) { ?>
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#contacts-modal">view contacts</button>
                    <? } else { ?>
                        <p class="text-danger text-weight-semibold text-xl">No contact details</p>
                    <? } ?>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Previous Total Outstanding:</h5>
                        <table class="table table-bordered" style="font-size: 9pt;">
                            <thead>
                            <tr>
                                <th>Currency</th>
                                <th>Amount</th>
                                <th>Base Amount (<?= $baseCurrency['name'] ?>)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($totalOutstandingAmounts as $currency => $R) { ?>
                                <tr>
                                    <td><?= $currency ?></td>
                                    <td class="text-danger"><?= formatN($R['amount']) ?></td>
                                    <td class="text-rosepink"><?= $R['base_currency'] == 'yes' ? '-' : formatN($R['base_amount']) ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                        <? if ($client['credit_limit'] > 0) { ?>
                            <p class="mt-md text-md">Credit Limit: <?= $baseCurrency['name'] ?> <span
                                        class="text-primary"><?= formatN($client['credit_limit']) ?></span></p>
                            <? if ($pending_after_approve > $client['credit_limit']) { ?>
                                <p class="mt-md text-lg text-danger text-weight-bold">Exceed Credit Limit</p>
                            <? } ?>
                        <? } ?>
                    </div>
                    <div class="col-md-3">
                        <h5>Advance Receipt Balance:</h5>
                        <table class="table table-bordered" style="font-size: 9pt;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Currency</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($advanceBalances as $index => $advanceBalance) { ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= $advanceBalance['currencyname'] ?></td>
                                    <td class="text-success"><?= formatN($advanceBalance['remaining_advance']) ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <h5 class="mt-md">Outstanding List</h5>
                <table class="table table-hover mb-none table-bordered" style="font-size:13px;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice No.</th>
                        <th>Invoice Date</th>
                        <th>Order By</th>
                        <th>Approval Status</th>
                        <th>Client</th>
                        <th>Currency</th>
                        <th>Invoice Amount</th>
                        <th>Pending Amount</th>
                        <th>(<30 days)</th>
                        <th>(30 to 45 days)</th>
                        <th>(45 to 90 days)</th>
                        <th>(>90 days)</th>
                        <th>Final Balance</th>
                        <th>Due on</th>
                        <th>Outstanding Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($outstandingSales as $R) { ?>
                        <tr class="<?= $sale['salesid'] == $R['salesid'] ? 'selected-sale' : '' ?>">
                            <td><?= $count ?></td>
                            <td>
                                <a class="<?= $sale['salesid'] == $R['salesid'] ? 'text-white' : '' ?>"
                                   href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>"><?= $R['receipt_no'] ?></a>
                            </td>
                            <td><?= fDate($R['invoice_date'], 'd M Y H:i') ?></td>
                            <td>
                                <? if ($R['orderno']) { ?>
                                    <div><?= $R['order_creator'] ?></div>
                                    <small><a class="<?= $sale['salesid'] == $R['salesid'] ? 'text-white' : '' ?>"
                                              href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order <?= $R['orderno'] ?></a></small>
                                <? } ?>
                            </td>
                            <td class="<?= $R['iscreditapproved'] ? 'text-success' : 'text-danger' ?>"><?= $R['iscreditapproved'] ? 'Approved' : 'Not Approved' ?></td>
                            <td><?= $R['clientname'] ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td class="text-right"><?= formatN($R['full_amount']) ?></td>
                            <td class="text-right text-danger"><?= formatN($R['pending_amount']) ?></td>
                            <td class="text-right"><?= formatN($R['(<30 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['(30 to 45 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['(45 to 90 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['(>90 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['pending_amount']) ?></td>
                            <td><?= $R['due_date'] ?></td>
                            <td><?= $R['outstanding_remarks'] ?></td>
                        </tr>
                        <? $count++;
                    } ?>
                    <? foreach ($opening_outstandings as $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['invoiceno'] ?></td>
                            <td><?= fDate($R['doc'], 'd-F-Y') ?></td>
                            <td></td>
                            <td class="text-success"><?= 'Approved' ?></td>
                            <td><?= $R['clientname'] ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td class="text-right"><?= formatN($R['outstanding_amount']) ?></td>
                            <td class="text-right text-danger"><?= formatN($R['pending_amount']) ?></td>
                            <td class="text-right"><?= formatN($R['(<30 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['(30 to 45 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['(45 to 90 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['(>90 days)']) ?></td>
                            <td class="text-right"><?= formatN($R['pending_amount']) ?></td>
                            <td><?= fDate($R['duedate'], 'd-F-Y') ?></td>
                            <td><?= $R['outstanding_remarks'] ?></td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
    </div>
</div>

<div class="modal fade" id="contacts-modal" tabindex="-1" role="dialog" aria-labelledby="contacts-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Client Contacts</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Person</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Position</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($contacts as $c) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $c['name'] ?></td>
                            <td><?= $c['email'] ?></td>
                            <td><?= $c['mobile'] ?></td>
                            <td><?= $c['position'] ?></td>
                        </tr>
                        <? $count++;
                    } ?>
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
<script>
    $(function () {
        initSelectAjax('#client', "?module=clients&action=getClients&format=json", 'Search client');
    });

    function validateInputs() {
        $('#spinnerHolder').show();
    }

    function checkSelected() {
        if ($('.big-checkbox:checked').length < 1) {
            triggerError('Select at least one invoice');
            return false;
        }
        return true;
    }

    function sumSelected(obj) {
        let salesid = $(obj).data('salesid');
        let currencyname = $(obj).data('currencyname');
        let totalSelectedTbody = $('#multiple-pay-form tbody');
        let pendingAmount = parseFloat($(obj).data('pendindamount'));
        if ($(obj).is(':checked')) {
            $('#multiple-pay-form').prepend(`<input type="hidden" name="invoicenum[]" class="selected-invoices"
                                                data-currencyname="${currencyname}" data-pendingamount="${pendingAmount}" value="${salesid}">`);
        } else {
            $('#multiple-pay-form').find(`input[value="${salesid}"]`).remove();
        }
        let total = {};
        $('.selected-invoices').each(function () {
            let cname = $(this).data('currencyname');
            let pendingAmount = parseFloat($(this).data('pendingamount'));
            if (total[cname]) {
                total[cname] += pendingAmount;
            } else {
                total[cname] = pendingAmount;
            }
        });
        $(totalSelectedTbody).empty();
        for (let c in total) {
            let row = `<tr>
                           <td>${c}</td>
                           <td class="text-danger">${numberWithCommas(total[c].toFixed(2))}</td>
                       </tr>`;
            $(totalSelectedTbody).append(row);
        }
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
