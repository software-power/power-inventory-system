<style media="screen">
    .input-group-rounded input.form-control:first-child, .input-group-rounded input.form-control:last-child, .input-search input.form-control:first-child, .input-search input.form-control:last-child {
        border-radius: 0;
        height: 44px;
        font-size: 15px;
    }

    .table {
        width: 100%;
        font-size: 15px;
    }

    .table .actions a:hover, .table .actions-hover a {
        color: #ffffff;
    }

    .table .actions a:hover, .table .actions-hover a:hover {
        color: #ffffff;
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
</style>
<header class="page-header">
    <h2>Clients</h2>
</header>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="panel-title">List of Clients</h2>
                    <p>Recently added</p>
                </div>
                <div class="col-md-9 d-flex justify-content-end align-items-center">
                    <? if (Users::can(OtherRights::add_client)) { ?>
                        <? if (CS_MAIN_SYSTEM) { ?>
                            <a href="?module=clients&action=client_add" class="btn btn-default"><i class="fa fa-plus"></i> Add Client</a>
                        <? } else { ?>
                            <a data-toggle="modal" data-target="#quick-add-client-modal" class="btn btn-default"><i
                                        class="fa fa-plus"></i> Add Client</a>
                        <? } ?>
                    <? } ?>
                    <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="row mb-md d-flex justify-content-center">
                <div class="col-md-4">
                    <form class="d-flex align-items-center">
                        <input type="hidden" name="module" value="clients">
                        <input type="hidden" name="action" value="client_index">
                        <input type="text" name="search" minlength="3" required class="form-control"
                               placeholder="major client search name or TIN or VRN" value="<?= $search ?>" style="border-radius: 10px;">
                        <button class="btn btn-success btn-sm ml-sm">search</button>
                    </form>
                    <? if ($search) { ?>
                        <small class="text-primary">Search: <?= $search ?></small>
                    <? } ?>
                </div>
            </div>
            <? if (Users::can(OtherRights::add_client) || Users::can(OtherRights::edit_client)) { ?>
                <div class="row mb-md">
                    <div class="col-md-12 d-flex justify-content-around align-items-center">
                        <a href="<?= url('clients', 'client_index', ['start_char' => '#']) ?>"
                           class="circle-link <?= '#' == $start_char ? 'active' : '' ?>">#</a>
                        <? foreach (range('A', 'Z') as $item) { ?>
                            <a href="<?= url('clients', 'client_index', ['start_char' => $item]) ?>"
                               class="circle-link <?= $item == $start_char ? 'active' : '' ?>"><?= $item ?></a>
                        <? } ?>
                    </div>
                </div>
            <? } ?>
            <div class="table-responsive">
                <table class="table table-hover table-condensed mb-none" id="userTable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered reseller</th>
                        <th>Mobile</th>
                        <th>TIN</th>
                        <th>VRN</th>
                        <th>Address</th>
                        <th>Account Manager</th>
                        <th>Royalty Card</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    $USER_CAN_EDIT = Users::can(OtherRights::edit_client) || Users::can(OtherRights::update_client_contact);
                    $USER_CAN_ASSIGN = Users::can(OtherRights::assign_royalty_card);
                    $USER_CAN_VIEW_ALL_LEDGER = Users::can(OtherRights::view_all_client_ledger);
                    $USER_CAN_VIEW_MNG_LEDGER = Users::can(OtherRights::view_managing_client_ledger);
                    $USER_CAN_VIEW_PURCHASES = Users::can(OtherRights::approve_other_credit_invoice) || Users::can(OtherRights::view_client_purchase);
                    foreach ($client as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $count ?></td>
                            <td>
                                <div class="d-flex">
                                    <? if (CS_SUPPORT_INTEGRATION) { ?>
                                        <div>
                                            <button type="button" class="btn btn-default btn-sm" title="copy code"
                                                    data-clientcode='<?= base64_encode(json_encode(['code' => $R['id'], 'support_name' => CS_SUPPORT_NAME])) ?>'
                                                    onclick="copyClientCode(this)">copy
                                            </button>
                                        </div>
                                    <? } ?>
                                    <div class="ml-sm"><?= $R['id'] ?></div>
                                </div>
                            </td>
                            <td><?= $R['name'] ?></td>
                            <td><?= $R['email'] ?></td>
                            <td><?= $R['reseller'] ? 'Yes' : 'No' ?></td>
                            <td><?= $R['mobile'] ? (($R['mobile_country_code'] ?: '') . $R['mobile']) : '' ?></td>
                            <td><?= $R['tinno'] ?></td>
                            <td><?= $R['vatno'] ?></td>
                            <td><?= $R['address'] ?></td>
                            <td><?= $R['account_manager'] ?></td>
                            <td><?= $R['cardNo'] ?></td>
                            <td><?= $R['status'] ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <? if ($USER_CAN_EDIT) { ?>
                                            <? if (CS_MAIN_SYSTEM) { ?>
                                                <a class="dropdown-item" href="<?= url('clients', 'client_edit', 'id=' . $R['id']) ?>"
                                                   title="Edit"> <i class="fa-pencil fa"></i> Edit Client</a>
                                            <? } else { ?>
                                                <? if ($R['id'] != 1) { ?>
                                                    <a class="dropdown-item" data-toggle="modal" href="#quick-add-client-modal"
                                                       data-mainclientcode="<?= $R['code'] ?>"
                                                       title="Edit"> <i class="fa-pencil fa"></i> Edit Client</a>
                                                <? } ?>
                                            <? } ?>
                                        <? } ?>
                                        <? if (CS_TALLY_TRANSFER && $R['name'] != $R['ledgername']) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('clients', 'update_tally_ledger', 'clientid=' . $R['id']) ?>"
                                               title="Edit"> <i class="fa fa-upload"></i> Update Tally Ledger</a>
                                        <? } ?>
                                        <? if (CS_SUPPORT_INTEGRATION) { ?>
                                            <a class="dropdown-item" href="<?= url('clients', 'post_support', 'clientid=' . $R['id']) ?>"
                                               title="Send to support"> <i class="fa fa-send"></i> Send to Support</a>
                                        <? } ?>
                                        <? if (CS_MULTI_SYSTEM && CS_MAIN_SYSTEM) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('clients', 'post_to_subsystems', 'clientid=' . $R['id']) ?>"
                                               title="Send to other systems"> <i class="fa fa-send"></i> Send to Other systems</a>
                                        <? } ?>
                                        <a class="dropdown-item" href="#client-document-modal" data-toggle="modal"
                                           title="Client document" data-clientid="<?= $R['id'] ?>" data-clientname="<?= $R['name'] ?>"> <i
                                                    class="fa fa-file"></i> Documents</a>
                                        <?
                                        if ($USER_CAN_ASSIGN) {
                                            if (!$R['cardNo']) {
                                                ?>
                                                <a class="dropdown-item" href="#royalty-card-modal"
                                                   data-toggle="modal" data-clientid="<?= $R['id'] ?>" data-clientname="<?= $R['name'] ?>"
                                                   title="Edit"> <i class="fa fa-credit-card"></i> Assign Royalty Card</a>
                                            <? } else { ?>
                                                <a class="dropdown-item" href="#"
                                                   data-toggle="modal" data-clientid="<?= $R['id'] ?>"
                                                   data-clientname="<?= $R['name'] ?>" title="Edit">
                                                    <i class="fa fa-dollar"></i> View Card Value</a>
                                            <? }
                                        } ?>
                                        <? if ($USER_CAN_VIEW_ALL_LEDGER || ($USER_CAN_VIEW_MNG_LEDGER && $R['acc_mng'] == $_SESSION['member']['id'])) { ?>
                                            <a class="dropdown-item" href="#generate-ledger-modal" data-toggle="modal"
                                               data-clientid="<?= $R['id'] ?>" data-clientname="<?= $R['name'] ?>"
                                               data-sr="1" title="Open Ledger Filter"> <i class="fa-calendar fa"></i> View client ledger</a>
                                        <? } ?>
                                        <? if ($USER_CAN_VIEW_PURCHASES) { ?>
                                            <a class="dropdown-item" href="#view-client-purchase-modal" data-toggle="modal"
                                               data-clientid="<?= $R['id'] ?>" data-clientname="<?= $R['name'] ?>"
                                               title="View client purchase"> <i class="fa fa-money"></i> View purchases</a>
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

<div class="modal fade" id="royalty-card-modal" role="dialog" aria-labelledby="royalty-card-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="<?= url('clients', 'assign_card') ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">Assign Royalty Card</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Client Name:</label>
                        <input class="clientId" type="hidden" name="card[clientid]">
                        <input type="text" readonly class="form-control clientname">
                    </div>
                    <div class="form-group">
                        <label for="">Card No:</label>
                        <select id="royalty-card" class="form-control" name="card[id]">

                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btn-sm confirmBtn" onclick="changeSellingPrice(this)">Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= component('shared/generate_client_ledger_modal.tpl.php') ?>
<?= component('shared/quick_add_client_modal.tpl.php') ?>
<?= component('client/clients_document_modal.tpl.php') ?>
<?= $USER_CAN_VIEW_PURCHASES ? component('client/view_client_purchase_modal.tpl.php') : '' ?>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#royalty-card', "?module=royalty_card&action=findcard&format=json", 'choose card', 2);

        $('#royalty-card-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            $(modal).find('.clientId').val(source.data('clientid'));
            $(modal).find('.clientname').val(source.data('clientname'));
        });
    });

    function copyClientCode(obj) {

        let clientcode = $(obj).data('clientcode');

        try {
            let tempInput = document.createElement("input");
            tempInput.style = "position: absolute; left: -1000px; top: -1000px";
            tempInput.value = clientcode;
            document.body.appendChild(tempInput);
            tempInput.select();
            let successful = document.execCommand('copy');
            document.body.removeChild(tempInput);

            let msg = successful ? 'successful' : 'unsuccessful';
            successful ? triggerMessage('Code copied successfully') : triggerError('Failed copying');
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err);
        }
        document.getSelection().empty();
    }
</script>
