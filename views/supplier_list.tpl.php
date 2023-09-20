<style media="screen">

    .dropleft .dropdown-menu {
        top: 0;
        right: 100%;
        left: auto;
        margin-top: 0;
        margin-right: .125rem;
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
    <h2>Suppliers</h2>
</header>
<div class="center-box">
    <div class="col-md-12">
        <section class="panel">
            <div class="panel-heading d-flex justify-content-between align-items-center">
                <h2 class="panel-title">List of Suppliers</h2>
                <? if (Users::can(OtherRights::add_supplier)) { ?>
                    <a href="?module=suppliers&action=supplier_add" class="btn btn-default">
                        <i class="fa fa-plus"></i> Add Supplier</a>
                <? } ?>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>TIN</th>
                            <th>VRN</th>
                            <th>VAT Registered</th>
                            <th>Contact Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        $USER_CAN_EDIT = Users::can(OtherRights::edit_supplier);
                        $count = 1;
                        foreach ($suppliers as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $count ?></td>
                                <td><?= $R['name'] ?></td>
                                <td><?= $R['address'] ?></td>
                                <td><?= $R['tin'] ?></td>
                                <td><?= $R['vat'] ?></td>
                                <td><?= $R['vat_registered'] ? 'Yes' : 'No' ?></td>
                                <td><?= $R['contact_name'] ?></td>
                                <td><?= ($R['country_code'] ? $R['country_code'] : "") . $R['contact_mobile'] ?></td>
                                <td><?= $R['contact_email'] ?></td>
                                <td class="text-capitalize"><?= $R['status'] ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <? if ($USER_CAN_EDIT) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('suppliers', 'supplier_add', ['supplierid' => $R['id']]) ?>">
                                                    <i class="fa-pencil fa"></i> Edit</a>
                                            <? } ?>
                                            <? if (CS_TALLY_TRANSFER && $R['name'] != $R['ledgername']) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('suppliers', 'update_tally_ledger', 'supplierid=' . $R['id']) ?>"
                                                   title="Edit"> <i class="fa fa-upload"></i> Update Tally Ledger</a>
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
    </div>
