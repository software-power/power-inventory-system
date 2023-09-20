<header class="page-header">
    <h2>Client Receipts</h2>
</header>
<style>
    .big-checkbox {
        width: 20px;
        height: 20px;
    }
</style>
<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Client Receipts</h2>
                        <p class="text-primary"><?= $title ?></p>
                    </div>
                    <div class="col-md-4 col-md-offset-3">
                        <form class=" d-flex justify-content-end align-items-center">
                            <input type="hidden" name="module" value="payments">
                            <input type="hidden" name="action" value="client_receipts">
                            <select id="client" name="clientid" class="form-control" required>
                                <option value="" selected disabled>search client</option>
                            </select>
                            <button class="btn btn-success ml-lg">Search</button>
                        </form>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <? if ($client) { ?>
                    <h5>Client name: <span class="text-primary"><?= $client['name'] ?></span></h5>
                    <div class="row">
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
                        <div class="col-md-3">
                            <h5>Total Outstanding:</h5>
                            <table class="table table-bordered" style="font-size: 9pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($totalOutstandingAmounts as $index => $R) { ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $R['currencyname'] ?></td>
                                        <td class="text-danger"><?= formatN($R['pending_amount']) ?></td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-3">
                            <form id="multiple-pay-form" action="<?= url('payments', 'credit_payment') ?>" method="post"
                                  style="margin:0" onsubmit="return checkSelected()">
                                <h5>Total Selected:</h5>
                                <table id="totalSelectedTable" class="table table-bordered" style="font-size: 9pt;">
                                    <thead>
                                    <tr>
                                        <th>Currency</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <input type="hidden" name="selectedclient" value="<?= $client['id'] ?>">
                                <input type="hidden" name="receipt_type" value="<?= SalesPayments::RECEIPT_TYPE_TRA ?>">
                                <input type="hidden" value="<?= base64_encode(url('payments', 'client_receipts')) ?>" name="redirect">
                                <button class="btn btn-success btn-sm">Confirm</button>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary btn-sm" onclick="selectAllItems()"><i class="fa fa-check"></i> Check all
                            </button>
                        </div>
                    </div>
                <? } ?>
                <div class="table-responsive mt-md">
                    <table class="table table-hover mb-none" id="sales-table" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Invoice No.</th>
                            <th>Client Name</th>
                            <th>Currency</th>
                            <th>Grand selling Amount</th>
                            <th>Grand VAT Amount</th>
                            <th>Outstanding Amount</th>
                            <th>Payment Type</th>
                            <th>Payment Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if ($sales_list || $openingOutstanding) {
                            foreach ($sales_list as $id => $R) { ?>
                                <tr>
                                    <td>
                                        <div class="checkbox checkbox-success">
                                            <label>
                                                <input onchange="sumSelected(this);" type="checkbox"
                                                       data-salesid="<?= $R['salesid'] ?>"
                                                       data-currencyname="<?= $R['currencyname'] ?>"
                                                       data-pendindamount="<?= $R['pending_amount'] ?>"
                                                       class="big-checkbox">
                                            </label>
                                        </div>
                                    </td>
                                    <td><?= $R['receipt_no'] ?></td>
                                    <td>
                                        <?= $R['clientname'] ?>
                                    </td>
                                    <td><?= $R['currencyname'] ?></td>
                                    <td><?= formatN($R['full_amount']) ?></td>
                                    <td><?= formatN($R['grand_vatamount']) ?></td>
                                    <td class="text-danger"><?= formatN($R['pending_amount']) ?></td>
                                    <td style="text-transform:capitalize"><?= $R['paymenttype'] ?></td>
                                    <td style="text-transform:capitalize"><?= $R['payment_status'] ?></td>
                                </tr>
                            <? } ?>

                            <? foreach ($openingOutstanding as $id => $R) { ?>
                                <tr>
                                    <td>
                                        <div class="checkbox checkbox-success">
                                            <label>
                                                <input onchange="sumSelected(this);" type="checkbox"
                                                       data-salesid="<?= $R['id'] ?>" data-opening="yes"
                                                       data-currencyname="<?= $R['currencyname'] ?>"
                                                       data-pendindamount="<?= $R['pending_amount'] ?>"
                                                       class="big-checkbox">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="p-none m-none"><?= $R['invoiceno'] ?></p>
                                        <small class="text-primary">opening</small>
                                    </td>
                                    <td>
                                        <?= $R['clientname'] ?>
                                    </td>
                                    <td><?= $R['currencyname'] ?></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-danger"><?= formatN($R['pending_amount']) ?></td>
                                    <td></td>
                                    <td style="text-transform:capitalize"><?= $R['payment_status'] ?></td>
                                </tr>
                            <? } ?>
                        <? } else {
                            ?>
                            <tr>
                                <td align="center" colspan="9">No Outstanding amounts found</td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>
    let salesTable;
    $(function () {
        initSelectAjax('#client', "?module=clients&action=getClients&format=json", 'Search client');
        //checkbox column sorting
        $.fn.dataTable.ext.order['dom-checkbox'] = function (settings, col) {
            return this.api().column(col, {order: 'index'}).nodes().map(function (td, i) {
                return $('input', td).prop('checked') ? '1' : '0';
            });
        };

        salesTable = $('#sales-table').DataTable({
            dom: '<"top"l>t<"bottom"ip>',
            // pageLength:20,
            colReorder: true,
            columnDefs: [
                {
                    targets: [0],
                    orderDataType: 'dom-checkbox'
                }
            ],
            keys: true,
            buttons: ['excelHtml5', 'csvHtml5'],
            exportOptions: {
                columns: ':not(:last-child)',
            },
            title: `<?=CS_COMPANY?>`,
        });
    });

    function checkSelected() {
        if ($('.big-checkbox:checked').length < 1) {
            triggerError('Select at least one invoice');
            return false;
        }
        return true;
    }

    let CHECK_ALL = true;

    function selectAllItems() {
        salesTable.column(0).nodes().each(function (n) {
            $(n).find('input:checkbox').prop('checked', false).trigger('change');
            if (CHECK_ALL) {
                $(n).find('input:checkbox').prop('checked', true).trigger('change');
            } else {
                $(n).find('input:checkbox').prop('checked', false).trigger('change');
            }
        });
        CHECK_ALL = !CHECK_ALL;
    }

    function sumSelected(obj) {
        let salesid = $(obj).data('salesid');
        let opening = $(obj).data('opening') === 'yes';
        let currencyname = $(obj).data('currencyname');
        let totalSelectedTbody = $('#multiple-pay-form tbody');
        let pendingAmount = parseFloat($(obj).data('pendindamount'));
        if ($(obj).is(':checked')) {
            let item = ``;
            if (opening) {
                item = `<input type="hidden" name="opening[]" class="selected-invoices opening"
                                                data-currencyname="${currencyname}" data-pendingamount="${pendingAmount}" value="${salesid}">`;
            } else {
                item = `<input type="hidden" name="invoicenum[]" class="selected-invoices normal"
                                                data-currencyname="${currencyname}" data-pendingamount="${pendingAmount}" value="${salesid}">`;
            }
            $('#multiple-pay-form').prepend(item);
        } else {
            if (opening) {
                $('#multiple-pay-form').find(`input.opening[value="${salesid}"]`).remove();
            } else {
                $('#multiple-pay-form').find(`input.normal[value="${salesid}"]`).remove();
            }
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
