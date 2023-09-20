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

</style>
<header class="page-header">
    <? if ($SR_MODE) { ?>
        <h2>Daily Cash Sales With SR</h2>
    <? } else { ?>
        <h2>Daily Cash Sales</h2>
    <? } ?>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <? if ($SR_MODE) { ?>
                    <input type="hidden" name="action" value="daily_cash_with_sr">
                <? } else { ?>
                    <input type="hidden" name="action" value="daily_cash">
                <? } ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Client:
                            <select id="clientid" name="clientid" class="form-control"></select>
                        </div>
                        <div class="col-md-4">
                            Branch:
                            <select name="branchid" class="form-control">
                                <option value="" selected>All</option>
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="locationid" name="locationid" class="form-control"></select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Sales Person:
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" name="createdby" class="form-control"></select>
                            <? } else { ?>
                                <input type="hidden" name="createdby" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            Currency:
                            <select name="currencyid" class="form-control">
                                <option value="" selected>All</option>
                                <? foreach ($currencies as $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?> - <?= $c['description'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Electronic Account:
                            <select name="eaccount" class="form-control">
                                <option value="" selected>All</option>
                                <? foreach ($eaccounts as $acc) { ?>
                                    <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                        </div>
                    </div>
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

<div class="row d-flex justify-content-center">
    <div class="col-md-10">
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
                    <h2 class="panel-title">Daily Cash Sales With SR</h2>
                <? } else { ?>
                    <h2 class="panel-title">Daily Cash Sales</h2>
                <? } ?>
                <p class="text-primary mt-md"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice no</th>
                            <th>Order By</th>
                            <th>Customer Name</th>
                            <th>Location</th>
                            <th>Issued By</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Currency</th>
                            <th class="text-right">Total Amount</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $total = 0;
                        foreach ($invoice_list as $index => $R) { ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <a href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>" target="_blank">
                                        <?= $R['invoiceno'] ?></a>
                                </td>
                                <td>
                                    <? if ($R['orderno']) { ?>
                                        <div class="d-block"><?=$R['order_creator']?></div>
                                        <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order <?= $R['orderno'] ?></a></small>
                                    <? } ?>
                                </td>
                                <td title="view client info">
                                    <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                                </td>
                                <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                                <td><?= $R['salesperson'] ?></td>
                                <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                                <td>
                                    <div><?= $R['method'] ?></div>
                                    <i class="text-xs text-muted"><?= $R['electronic_account'] ?> <?= $R['credit_cardno'] ?></i>
                                </td>
                                <td><?= $R['currencyname'] ?></td>
                                <td class="text-right">
                                    <p><?= formatN($R['amount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_amount']) ?>
                                        </i>
                                    <? } ?>
                                </td>
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
                            <td colspan="10" class="text-right">
                                <div class="row d-flex justify-content-end">
                                    <div class="col-md-4">
                                        <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                            <thead>
                                            <tr>
                                                <td>Currency</td>
                                                <td class="text-right">Credit Card</td>
                                                <td class="text-right">Cash</td>
                                                <td class="text-right">Total</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <? foreach ($totals['currencies'] as $curencyname => $R) { ?>
                                                <tr>
                                                    <td><?= $curencyname ?></td>
                                                    <td class="text-right"><?= formatN($R['Credit Card']) ?></td>
                                                    <td class="text-right"><?= formatN($R['Cash']) ?></td>
                                                    <td class="text-right"><?= formatN($R['Cash'] + $R['Credit Card']) ?></td>
                                                </tr>
                                            <? } ?>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td class="bg-rosepink text-weight-bold text-white">Base (<?= $basecurrency['name'] ?>)</td>
                                                <td class="text-right bg-rosepink text-weight-bold text-white"><?= formatN($totals['base']['Credit Card']) ?></td>
                                                <td class="text-right bg-rosepink text-weight-bold text-white"><?= formatN($totals['base']['Cash']) ?></td>
                                                <td class="text-right bg-rosepink text-weight-bold text-white"><?= formatN($totals['base']['Cash'] + $totals['base']['Credit Card']) ?></td>
                                            </tr>
                                            </tbody>
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
                        <tbody id="tbodyforticekt">

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


        $('#detail-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);

            //clear
            $(modal).find('.receiptno').text('');
            $('#tbodyforticekt').empty();

            let salesid = source.data('salesid');
            $(modal).find('.receiptno').text(source.data('receiptno'));
            getSalesReportDetails(salesid);
        });
    });


    function getSalesReportDetails(salesid) {

        $.get("?module=sales&action=getSalesDetails&format=json&salesid=" + salesid, null, function (d) {
            let result = JSON.parse(d);
            // console.log(data);
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
                    $('#tbodyforticekt').append(tableRow);
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
