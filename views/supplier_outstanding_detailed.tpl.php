<header class="page-header">
    <h2>Supplier Outstanding</h2>
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
                        <h2 class="panel-title">Supplier Outstanding</h2>
                        <p class="text-primary"><?= $title ?></p>
                    </div>
                    <div class="col-md-4 col-md-offset-3">

                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <form class="d-flex align-items-center"
                              style="border:1px dashed grey; border-radius: 10px;padding: 10px;">
                            <input type="hidden" name="module" value="suppliers">
                            <input type="hidden" name="action" value="outstanding_detailed">
                            <div class="col-md-4">
                                Branch:
                                <select name="branchid" class="form-control" required>
                                    <? foreach ($branches as $index => $branch) { ?>
                                        <option <?= selected($currentBranch['id'], $branch['id']) ?>
                                                value="<?= $branch['id'] ?>"><?= $branch['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                Supplier:
                                <select id="supplierid" name="supplierid" class="form-control" required>
                                    <? if ($supplier) { ?>
                                        <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success btn-block">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                <? if ($supplier) { ?>
                    <h5>Supplier name: <span class="text-primary"><?= $supplier['name'] ?></span></h5>
                    <h5>Branch: <span class="text-primary"><?= $currentBranch['name'] ?></span></h5>


                    <h5>Outstanding: <span class="text-danger"
                                           style="font-weight: bold"> <?= $base_currency['name'] ?> <?= formatN($totalOutstanding) ?></span>
                    </h5>
                    <h5>Advance: <span class="text-success"
                                       style="font-weight: bold"> <?= $base_currency['name'] ?> <?= formatN($advanceAmount) ?></span>
                    </h5>
                    <div class="d-flex align-items-center mt-xlg mt-lg">
                        <h5>Total Selected: <span class="text-primary totalSelected" style="font-weight: bold">0</span>
                        </h5>
                        <form id="multiple-pay-form" action="<?= url('suppliers', 'make_payment') ?>" method="post"
                              style="margin:0" onsubmit="return checkSelected()">
                            <input type="hidden" name="supplierid" value="<?= $supplier['id'] ?>">
                            <input type="hidden" name="branchid" value="<?= $currentBranch['id'] ?>">
                            <button class="btn btn-success btn-sm ml-lg">Confirm</button>
                        </form>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm mt-md" onclick="selectAllItems()">Check All
                    </button>
                <? } ?>
                <div class="table-responsive mt-md">
                    <table class="table table-hover mb-none" id="outstanding-grns" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>GRN No</th>
                            <th>Supplier Invoice No</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Supplier Name</th>
                            <th>Credit due</th>
                            <th class="text-right">Full Amount <?= $base_currency['name'] ?></th>
                            <th class="text-right">Paid Amount <?= $base_currency['name'] ?></th>
                            <th class="text-right">Outstanding Amount <?= $base_currency['name'] ?></th>
                            <!--                            <th>Payment Type</th>-->
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        if ($outstandingGrns) {
                            foreach ($outstandingGrns as $id => $R) { ?>
                                <tr>
                                    <td>
                                        <div class="checkbox" style="margin: 5px;">
                                            <label>
                                                <input onchange="sumSelected(this);" type="checkbox"
                                                       data-id="<?= $R['id'] ?>" data-source="<?= $R['source'] ?>"
                                                       data-pendindamount="<?= $R['outstanding_amount'] ?>"
                                                       class="big-checkbox">
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <?
                                        if ($R['source'] == 'grn') { ?>
                                            <a href="?module=grns&action=print_grn&grn=<?= $R['id'] ?>"
                                               target="_blank"><?= $R['id'] ?></a>
                                            <?
                                        } else { ?>
                                            <p class="p-none m-none"><?= $R['id'] ?></p>
                                            <i class="text-muted"><?= $R['grnno'] ?></i>
                                            <?
                                        } ?>
                                    </td>
                                    <td><?= $R['invoiceno'] ?></td>
                                    <td><?= $R['source'] == 'opening' ? 'Opening Outstanding' : 'Normal' ?></td>
                                    <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                                    <td><?= $R['suppliername'] ?></td>
                                    <td><?= $R['credit_due_date'] ? fDate($R['credit_due_date']) : '' ?></td>
                                    <td class="text-right"><?= formatN($R['full_amount']) ?></td>
                                    <td class="text-success text-right"><?= formatN($R['paid_amount']) ?></td>
                                    <td class="text-danger text-right"><?= formatN($R['outstanding_amount']) ?></td>
                                    <!--                                    <td style="text-transform:capitalize">-->
                                    <?//= $R['paymenttype'] ?><!--</td>-->
                                </tr>
                            <? }
                        } else {
                            ?>
                            <tr>
                                <td align="center" colspan="11">No Outstanding amounts found</td>
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

    let outstandingTable;
    $(function () {
        initSelectAjax('#supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Search supplier', 2);

        //checkbox column sorting
        $.fn.dataTable.ext.order['dom-checkbox'] = function (settings, col) {
            return this.api().column(col, {order: 'index'}).nodes().map(function (td, i) {
                return $('input', td).prop('checked') ? '1' : '0';
            });
        };

        outstandingTable = $('#outstanding-grns').DataTable({
            dom: '<"top"fB>t<"bottom"ip>',
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
            <?if(1 == 1){?>
            title: `<?=CS_COMPANY?>`,
            <?}?>
        });


    });

    let CHECK_ALL = true;

    function selectAllItems() {
        outstandingTable.column(0).nodes().each(function (n) {
            $(n).find('input:checkbox').prop('checked', false).trigger('change');
            if (CHECK_ALL) {
                $(n).find('input:checkbox').prop('checked', true).trigger('change');
            } else {
                $(n).find('input:checkbox').prop('checked', false).trigger('change');
            }
        });
        CHECK_ALL = !CHECK_ALL;
    }

    function checkSelected() {
        if ($('.big-checkbox:checked').length < 1) {
            triggerError('Select at least one grn');
            return false;
        }
        return true;
    }

    function sumSelected(obj) {
        let itemId = $(obj).data('id');
        let isGrn = $(obj).data('source') === 'grn';
        let pendingAmount = parseFloat($(obj).data('pendindamount'));
        if ($(obj).is(':checked')) {
            if (isGrn) {
                $('#multiple-pay-form').prepend(`<input type="hidden" name="grnid[]" class="selected-items grn"
                                                data-pendingamount="${pendingAmount}" value="${itemId}">`);
            } else {
                $('#multiple-pay-form').prepend(`<input type="hidden" name="openid[]" class="selected-items opening"
                                                data-pendingamount="${pendingAmount}" value="${itemId}">`);
            }
        } else {
            if (isGrn) {
                $('#multiple-pay-form').find(`input.grn.selected-items[value="${itemId}"]`).remove();
            } else {
                $('#multiple-pay-form').find(`input.opening.selected-items[value="${itemId}"]`).remove();
            }
        }
        let total = 0;
        $('.selected-items').each(function () {
            let pendingAmount = parseFloat($(this).data('pendingamount'));
            total += pendingAmount;
        });
        $('.totalSelected').text(numberWithCommas(total.toFixed(2)));
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
