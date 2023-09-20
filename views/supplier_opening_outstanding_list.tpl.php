<header class="page-header">
    <h2>Supplier Opening Outstanding</h2>
</header>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">Supplier Opening Outstanding</h2>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <a href="<?= url('suppliers', 'create_opening_outstanding') ?>" class="btn btn-default">
                        <i class="fa fa-plus"></i> Create Outstanding</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>GRN No</th>
                        <th>Location</th>
                        <th>Supplier Name</th>
                        <th>Currency</th>
                        <th>Opening Amount</th>
                        <th>Paid Amount</th>
                        <th>Outstanding Amount</th>
                        <th>Issued By</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($list as $id => $R) { ?>
                        <tr>
                            <td><?= $R['id'] ?></td>
                            <td><?= $R['grnno'] ?></td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td><?= $R['currency'] ?></td>
                            <td>
                                <p><?= formatN($R['amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <span class="text-xs text-muted">
                                        <?= $baseCurrency['name'] ?> <?= formatN($R['base_amount']) ?>
                                    </span>
                                <? } ?>
                            </td>
                            <td class="text-success"><?= formatN($R['paid_amount']) ?></td>
                            <td class="text-danger"><?= formatN($R['outstanding_amount']) ?></td>
                            <td><?= $R['username'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="<?=url('suppliers','opening_outstanding_payments',['openingid'=>$R['id']])?>" title="Payments">
                                            <i class="fa fa-money text-primary"></i> Supplier payment</a>
                                        <? if (!$R['paid_amount']) { ?>
                                            <a class="dropdown-item" href="#" title="Edit">
                                                <i class="fa fa-pencil text-primary"></i> Edit</a>
                                            <a class="dropdown-item" href="#" title="Delete">
                                                <i class="fa fa-trash text-danger"></i> Delete</a>
                                        <? } ?>
                                    </div>
                                </div>
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

<div class="modal fade serial-modal" id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="approve-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="modal-title">Approve Transfer Requisition No: <span class="reqno text-primary"></span>
                    </h4>
                    <div class="d-flex mr-md">
                        <form action="<?= url('stocks', 'approve_requisition') ?>" method="post"
                              style="margin: 0;">
                            <input type="hidden" class="reqid" name="reqno">
                            <button class="btn btn-success btn-sm" title="Approve Transfer">Approve</button>
                        </form>
                        <form action="<?= url('stocks', 'issue_transfer_requisition') ?>" method="post" class="ml-xs"
                              style="margin: 0;">
                            <input type="hidden" class="reqid" name="reqno">
                            <button class="btn btn-primary btn-sm" title="Edit Transfer">Edit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div id="spinner-holder" style="display: none">
                    <div class="d-flex justify-content-center align-items-center">
                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="100"
                                width="100"></object>
                        <span class="ml-sm">Loading ...</span>
                    </div>
                </div>
                <div id="content-holder">
                    <p>Issued by: <span class="issuedby text-primary"></span></p>
                    <p>Issue date: <span class="issuedate text-primary"></span></p>
                    <p>From: <span class="fromlocation text-primary"></span></p>
                    <p>To: <span class="tolocation text-primary"></span></p>
                    <table class="table table-bordered mt-xl" style="font-size: 9pt">
                        <thead>
                        <tr>
                            <td>#</td>
                            <td>Item</td>
                            <td class="text-right">Qty</td>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
        initSelectAjax('.locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");

        $('#approve-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let reqno = source.data('id');
            let issuedby = source.data('issuedby');
            let issuedate = source.data('issuedate');
            let fromlocation = source.data('fromlocation');
            let tolocation = source.data('tolocation');
            let modal = $(this);
            $(modal).find('.reqno').text(reqno);
            $(modal).find('.reqid').val(reqno);
            $(modal).find('.issuedby').text(issuedby);
            $(modal).find('.issuedate').text(issuedate);
            $(modal).find('.fromlocation').text(fromlocation);
            $(modal).find('.tolocation').text(tolocation);

            getRequisitionDetails(reqno, modal);
        });
    });

    function getRequisitionDetails(reqno, modal) {
        let spinner = $('#spinner-holder');
        let productTbody = $(modal).find('table tbody');
        productTbody.empty();

        spinner.show();

        //todo get requisition details
        $.get(`?module=stocks&action=getRequisitionDetails&format=json&reqno=${reqno}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            console.log(result);
            let count = 1;
            if (result.status === 'success') {
                $.each(result.data.details, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>${item.productname}</td>
                                    <td class="text-right">${item.qty}</td>
                                </tr>`;
                    $(productTbody).append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || 'Error found');
            }
        });
    }
</script>