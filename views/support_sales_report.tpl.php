<header class="page-header">
    <? if ($SR_MODE) { ?>
        <h2>Support Sales Report SR</h2>
    <? } else { ?>
        <h2>Support Sales Report</h2>
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
                            Support no:
                            <input type="text" name="supportno" class="form-control" placeholder="support no">
                        </div>
                        <div class="col-md-3">
                            Client:
                            <select id="clientid" name="clientid" class="form-control"></select>
                        </div>
                        <div class="col-md-3">
                            Order/Invoice by:
                            <select id="userid" name="createdby" class="form-control"></select>
                        </div>
                        <div class="col-md-3">
                            Location:
                            <select id="locationid" name="locationid" class="form-control">
                                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                    <option value="" selected>--All locations --</option>
                                <? } ?>
                                <? foreach ($locations as $l) { ?>
                                    <option value="<?= $l['id'] ?>"><?= $l['name'] ?> <?= $l['branchname'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            From:
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-3">
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
                <h2 class="panel-title">Support Sales Report SR</h2>
            <? } else { ?>
                <h2 class="panel-title">Support Sales Report</h2>
            <? } ?>
            <p class="text-primary mt-md"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th style="width:80px">Invoice</th>
                        <th>Support no</th>
                        <th>Customer Name</th>
                        <th>Location</th>
                        <th>Order By</th>
                        <th>Invoice By</th>
                        <th>Invoice date</th>
                        <th>Currency</th>
                        <th class="text-right">Service/Amc Amount</th>
                        <th class="text-right">Spare Amount</th>
                        <th class="text-right">Total Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($salesdetails as $R) { ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><?= $R['invoiceno'] ?></td>
                            <td><?= $R['supportno'] ?></td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal"
                                   data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td>
                                <? if ($R['orderno']) { ?>
                                    <div><?= $R['order_creator'] ?></div>
                                    <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order <?= $R['orderno'] ?></a></small>
                                <? } ?>
                            </td>
                            <td><?= $R['salesperson'] ?></td>
                            <td><?= fDate($R['invoicedate'], 'd F Y H:i') ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td class="text-right"><?= $R['service'] > 0 ? formatN($R['service']) : '' ?></td>
                            <td class="text-right"><?= $R['spare'] > 0 ? formatN($R['spare']) : '' ?></td>
                            <td class="text-right"><?= formatN($R['full_amount']) ?></td>
                            <td>
                                <a href="#view-details-modal" class="btn btn-default btn-sm" data-toggle="modal"
                                   data-details="<?= base64_encode(json_encode($R['items'])) ?>"
                                   title="View"><i class="fa fa-list"></i> View</a>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="view-details-modal" tabindex="-1" role="dialog" aria-labelledby="view-details-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody class="tbody"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", "client", 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        $('#departmentid,#brandid,#productcategoryid,#subcategoryid,#locationid').select2({width: '100%'});
    });

    $('#view-details-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        let details = source.data('details');
        $(modal).find('tbody.tbody').empty();
        try {
            details = atob(details);
            details = JSON.parse(details);
            let count = 1;
            $.each(details, function (i, item) {
                // console.log(item);
                let row = `<tr>
                                <td>${count++}</td>
                                <td>${item.name}</td>
                                <td>${item.amount}</td>
                            </tr>`;
                $(modal).find('tbody.tbody').append(row);
            });
        } catch (e) {
            triggerError('Error found!');
        }
    })
</script>
