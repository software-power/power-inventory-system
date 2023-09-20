<style media="screen">

    .formholder h5 {
        font-size: 15px;
        font-weight: 600;
    }

    .panelControl {
        float: right;
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

    .table-responsive {
        min-height: 223px;
    }

    .select2-container {
        border: 1px solid #dadada;
        border-radius: 5px;
    }
</style>
<header class="page-header">
    <? if ($SR_MODE) { ?>
        <h2>Sales Report SR <?=$WITH_TIME?'With Time':''?></h2>
    <? } else { ?>
        <h2>Sales Report <?=$WITH_TIME?'With Time':''?></h2>
    <? } ?>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="<?= $action ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            Client:
                            <select id="clientid" name="clientid" class="form-control"></select>
                        </div>
                        <div class="col-md-3">
                            Branch:
                            <select id="branchid" name="branchid" class="form-control">
                                <option value="" selected>--All branches --</option>
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            Location:
                            <select id="locationid" name="locationid" class="form-control"></select>
                        </div>
                        <div class="col-md-3">
                            User:
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" name="createdby" class="form-control"></select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                                <input type="hidden" name="createdby" value="<?= $_SESSION['member']['id'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-3">
                            Invoice Type:
                            <select name="paymenttype" class="form-control">
                                <option value="" selected>All</option>
                                <option value="<?= PAYMENT_TYPE_CASH ?>">Cash invoice</option>
                                <option value="<?= PAYMENT_TYPE_CREDIT ?>">Credit Invoice</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            Currency:
                            <select name="currencyid" class="form-control">
                                <option value="" selected>All</option>
                                <? foreach ($currencies as $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?> - <?= $c['description'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <? if (!$WITH_TIME) { ?>
                            <div class="col-md-3">
                                From:
                                <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                            </div>
                            <div class="col-md-3">
                                To:
                                <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                            </div>
                        <? } ?>
                    </div>
                    <? if ($WITH_TIME) { ?>
                        <div class="row mt-sm pl-sm">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6 p-none">
                                        From date:
                                        <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                                    </div>
                                    <div class="col-md-4 p-none">
                                        Time:
                                        <input type="time" name="fromtime" class="form-control" value="<?= $fromtime ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6 p-none">
                                        To date:
                                        <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                                    </div>
                                    <div class="col-md-4 p-none">
                                        Time:
                                        <input type="time" name="totime" class="form-control" value="<?= $totime ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                </div>
                <div class="modal-footer">
                    <div class="mt-md">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-success">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panelControl">
                <button type="button" class="btn" title="Home" data-toggle="modal"
                        data-target="#search-modal"><i
                            class="fa fa-search"></i> Open filter
                </button>
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
            </div>
            <? if ($SR_MODE) { ?>
                <h2 class="panel-title">Sales Report SR <?=$WITH_TIME?'With Time':''?></h2>
            <? } else { ?>
                <h2 class="panel-title">Sales Report <?=$WITH_TIME?'With Time':''?></h2>
            <? } ?>
            <p class="text-primary mt-md"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Customer Name</th>
                        <th>Store Location</th>
                        <th>Order By</th>
                        <th>Invoice By</th>
                        <th>Invoice date</th>
                        <th>Currency</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-right">Exc Amount</th>
                        <th class="text-right">VAT Amount</th>
                        <th class="text-right">Total Paid</th>
                        <th class="text-right">Outstanding</th>
                        <th>Sale Type</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($invoice_list as $index => $R) { ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $R['receipt_no'] ?></td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal"
                                   data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['stocklocation'] ?> - <?= $R['branchname'] ?></td>
                            <td>
                                <? if ($R['orderno']) { ?>
                                    <div><?= $R['order_creator'] ?></div>
                                    <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order <?= $R['orderno'] ?></a></small>
                                <? } ?>
                            </td>
                            <td><?= $R['issuedby'] ?></td>
                            <td  data-sort="<?=strtotime($R['doc'])?>"><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td class="text-right">
                                <p><?= formatN($R['full_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_full_amount']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td class="text-right">
                                <p><?= formatN($R['grand_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_grand_amount']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td class="text-right">
                                <p><?= formatN($R['grand_vatamount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_grand_vatamount']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td class="text-right <?= $R['lastpaid_totalamount'] > 0 ? 'text-success' : '' ?>"><?= formatN($R['lastpaid_totalamount']) ?></td>
                            <td class="text-right <?= $R['paymenttype'] == PAYMENT_TYPE_CREDIT && $R['pending_amount'] > 0 ? 'text-danger' : '' ?>">
                                <? if ($R['paymenttype'] == PAYMENT_TYPE_CREDIT) { ?>
                                    <p><?= formatN($R['pending_amount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes' && $R['pending_amount'] > 0) { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_pending_amount']) ?>
                                        </i>
                                    <? } ?>
                                <? } ?>
                            </td>
                            <td><?= $R['paymenttype'] ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#detail-modal" data-toggle="modal"
                                           data-salesid="<?= $R['salesid'] ?>"
                                           data-receiptno="<?= $R['receipt_no'] ?>"
                                           title="View"><i class="fa-file fa"></i> View Details</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                    <tfoot>
                    <tr style="font-size: 12pt;">
                        <td colspan="14" class="text-right">
                            <div class="row d-flex justify-content-end">
                                <div class="col-md-5">
                                    <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                        <thead>
                                        <tr>
                                            <td>Currency</td>
                                            <td class="text-right">Total Amount</td>
                                            <td class="text-right">Total Paid</td>
                                            <td class="text-right">Total Pending</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? foreach ($totals['currency'] as $curencyname => $R) { ?>
                                            <tr>
                                                <td><?= $curencyname ?></td>
                                                <td class="text-right"><?= formatN($R['full_amount']) ?></td>
                                                <td class="text-right text-success"><?= formatN($R['paid_amount']) ?></td>
                                                <td class="text-right text-danger"><?= formatN($R['pending_amount']) ?></td>
                                            </tr>
                                        <? } ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="4"></td>
                                        </tr>
                                        <tr>
                                            <td>Total in Base (<?= $basecurrency['name'] ?>)</td>
                                            <td class="text-right"><?= formatN($totals['base']['full_amount']) ?></td>
                                            <td class="text-right text-success">-</td>
                                            <td class="text-right text-danger"><?= formatN($totals['base']['pending_amount']) ?></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="detail-modal" role="dialog" aria-labelledby="detail-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="index">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Sales Invoice Details: <span class="text-primary receiptno"></span></h4>
                </div>
                <div class="modal-body" style="max-height: 60vh;overflow-y: auto;">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="print_invoice_details">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Product description</th>
                            <th>Quantity</th>
                            <th>Currency</th>
                            <th>Sale amount</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyProducts">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", "client", 2);
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        $('#branchid').select2({width: '100%'});

        $('#detail-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);

            //clear
            $(modal).find('.receiptno').text('');
            $('#tbodyProducts').empty();

            let salesid = source.data('salesid');
            $(modal).find('.receiptno').text(source.data('receiptno'));
            getSalesReportDetails(salesid);
        });
    });


    function getSalesReportDetails(salesid) {

        $.get("?module=sales&action=getSalesDetails&format=json&salesid=" + salesid, null, function (d) {
            let result = JSON.parse(d);
            // console.log(result.data);
            if (result.status === 'success') {
                let counter = 1;
                let items = result.data.products;
                // console.log(items);
                $.each(items, function (index, item) {
                    let tableRow = `<tr>
                                        <td>${counter}</td>
                                        <td>${item.productname}</td>
                                        <td>${item.quantity}</td>
                                        <td>${result.data.currencyname}</td>
                                        <td>${numberWithCommas(item.total_amount)}</td>
                                        <td>${result.data.issue_date}</td>
                                    </tr>`;
                    $('#tbodyProducts').append(tableRow);
                    counter++;
                });
            } else {
                triggerError(result.msg || 'Error');
            }
        });
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
