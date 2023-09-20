<header class="page-header">
    <h2>Stock Transfer List</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="stocks">
                <input type="hidden" name="action" value="transfer_requisition_list">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Requisition No:
                            <input type="text" name="search[reqno]" placeholder="Requisition No" autocomplete="off"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            From Location:
                            <select class="form-control locationid" name="search[fromlocation]">
                                <option value="" selected>--From Location--</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            To Location:
                            <select class="form-control locationid" name="search[tolocation]">
                                <option value="" selected>--To Location--</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Issued by:
                            <? if (Users::can(OtherRights::approve_other_requisition)) { ?>
                                <select id="userid" class="form-control" name="search[createdby]"></select>
                            <? } else { ?>
                                <input type="hidden" name="search[createdby]" value="<?= $creator['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $creator['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="search[fromdate]" value="<?= $fromdate ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="search[todate]" value="<?= $todate ?>" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-danger btn-block" data-dismiss="modal"><i
                                        class="fa fa-search"></i>
                                CANCEL
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-success btn-block"><i
                                        class="fa fa-refresh"></i>
                                RESET
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block"><i
                                        class="fa fa-search"></i>
                                SEARCH
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">Stock Transfer Requisitions</h2>
                    <p>Filter: <span class="text-primary"><?= $title ?></span></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <button type="button" data-toggle="modal" data-target="#search-modal" class="btn"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                    <? if (Users::can(OtherRights::add_requisition)) { ?>
                        <a href="?module=stocks&action=issue_transfer_requisition" class="btn">
                            <i class="fa fa-plus"></i> Create Requisition</a>
                    <? } ?>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Req No</th>
                        <th class="text-center">Transfer No</th>
                        <th>Request product from</th>
                        <th>To Location</th>
                        <th>Issued By</th>
                        <th>Issue Date</th>
                        <th>Approval status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    $USER_CAN_APPROVE_REQUISITION = Users::can(OtherRights::approve_other_requisition) || Users::can(OtherRights::approve_requisition);
                    foreach ($requisitions as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['id'] ?></td>
                            <td class="text-center">
                                <? if ($R['transferid']) { ?>
                                    <p class="m-none" title="view transfer">
                                        <a target="_blank"
                                           href="<?= url('stocks', 'transfer_print', ['transferno' => $R['transferid']]) ?>"><?= $R['transferid'] ?></a>
                                    </p>
                                    <span class="badge bg-default text-success text-xs">processed</span>
                                <? } ?>
                            </td>
                            <td><?= $R['tolocation'] ?></td>
                            <td><?= $R['fromlocation'] ?></td>
                            <td><?= $R['issuedby'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td>
                                <? if ($R['approver']) { ?>
                                    <? if ($R['auto_approve']) { ?>
                                        <p class="text-muted">auto approved</p>
                                    <? } else { ?>
                                        <p><small><?= $R['approver'] ?></small>
                                            <small><?= fDate($R['approve_date']) ?></small></p>
                                    <? } ?>
                                <? } else { ?>
                                    <p class="text-muted">not approved</p>
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
                                        <? if (!$R['approver'] && $USER_CAN_APPROVE_REQUISITION) { ?>
                                            <a class="dropdown-item" href="#approve-modal" data-toggle="modal"
                                               title="Approve Transfer" data-id="<?= $R['id'] ?>"
                                               data-fromlocation="<?= $R['fromlocation'] ?>"
                                               data-tolocation="<?= $R['tolocation'] ?>"
                                               data-issuedby="<?= $R['issuedby'] ?>"
                                               data-issuedate="<?= fDate($R['doc']) ?>">
                                                <i class="fa fa-check-circle-o text-success"></i> Approve</a>
                                        <? } ?>
                                        <? if ($R['approver'] && !$R['transferid']) { ?>
                                            <a class="dropdown-item" href="<?= url('stocks', 'issue_transfer', 'reqno=' . $R['id']) ?>"
                                               title="Print Requisition"><i class="fa fa-truck text-primary"></i> Make Transfer</a>
                                        <? } ?>
                                        <a class="dropdown-item" target="_blank"
                                           href="<?= url('stocks', 'requisition_print', 'reqno=' . $R['id']) ?>"
                                           title="Print Requisition"><i class="fa fa-print text-primary"></i> Print</a>
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
                    <p>Request Products From: <span class="fromlocation text-primary"></span></p>
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
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
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