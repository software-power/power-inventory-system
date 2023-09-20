<header class="page-header">
    <h2>Branches</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="<?= CS_TALLY_TRANSFER ? 'col-md-8' : 'col-md-6' ?>">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">List of Branches</h2>
                <a href="#branch-modal" class="btn btn-success" data-toggle="modal">Add</a>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <? if (CS_TALLY_TRANSFER) { ?>
                                <th>Tally Cash ledger</th>
                                <th>Tally Cost Center</th>
                                <th>Tally Purchase Account</th>
                                <th>Invoice Prefix</th>
                            <? } ?>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($branch as $id => $R) { ?>
                            <tr>
                                <td><?= $R['id'] ?></td>
                                <td><?= $R['name'] ?></td>
                                <? if (CS_TALLY_TRANSFER) { ?>
                                    <td><?= $R['tally_cash_ledger'] ?></td>
                                    <td><?= $R['cost_center'] ?></td>
                                    <td><?= $R['tally_purchase_account'] ?></td>
                                    <td><?= $R['invoice_prefix'] ?></td>
                                <? } ?>
                                <td class="text-capitalize"><?= $R['status'] ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" title="Edit" data-toggle="modal" data-target="#branch-modal" data-mode="edit"
                                               data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>"
                                               data-ledgername="<?= $R['tally_cash_ledger'] ?>" data-costcenter="<?= $R['cost_center'] ?>"
                                               data-purchaseaccount="<?= $R['tally_purchase_account'] ?>"
                                               data-invoiceprefix="<?= $R['invoice_prefix'] ?>" data-status="<?= $R['status'] ?>">
                                                <i class="fa fa-pencil"></i> Edit</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="branch-modal" tabindex="-1" role="dialog" aria-labelledby="branch-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add Branch</h4>
            </div>
            <form action="<?= url('branches', 'branch_save') ?>" method="post">
                <input type="hidden" name="branch[id]" class="branchid">
                <div class="modal-body">
                    <div class="form-group">
                        Name:
                        <input type="text" name="branch[name]" class="form-control name" placeholder="branch name" required>
                    </div>
                    <? if (CS_TALLY_TRANSFER) { ?>
                        <div class="form-group">
                            Tally Cash Ledger:
                            <input type="text" name="branch[tally_cash_ledger]" class="form-control ledgername" placeholder="tally cash ledger"
                                   required>
                        </div>
                        <div class="form-group">
                            Tally Cost Center:
                            <input type="text" name="branch[cost_center]" class="form-control costcenter" placeholder="tally cost center"
                                   required>
                        </div>
                        <div class="form-group">
                            Tally Purchase Account:
                            <input type="text" name="branch[tally_purchase_account]" class="form-control purchaseaccount"
                                   placeholder="tally purchase account">
                        </div>
                        <div class="form-group">
                            Invoice Prefix:
                            <input type="text" name="branch[invoice_prefix]" class="form-control invoice_prefix"
                                   placeholder="invoice prefix eg. ARS  for ARS02-0001">
                        </div>
                    <? } ?>
                    <div class="form-group">
                        Status:
                        <select name="branch[status]" class="form-control status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm save-btn">Save</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#branch-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        if ($(source).data('mode')) {
            $(modal).find('.modal-title').text('Edit Branch');
            $(modal).find('.save-btn').text('Update');

            $(modal).find('.branchid').val(source.data('id'));
            $(modal).find('.name').val(source.data('name'));
            $(modal).find('.ledgername').val(source.data('ledgername'));
            $(modal).find('.costcenter').val(source.data('costcenter'));
            $(modal).find('.purchaseaccount').val(source.data('purchaseaccount'));
            $(modal).find('.invoice_prefix').val(source.data('invoiceprefix'));
            $(modal).find('.status').val(source.data('status'));
        } else {
            $(modal).find('.modal-title').text('Add Branch');
            $(modal).find('.save-btn').text('Save');
            $(modal).find('.name,.ledgername,.costcenter,.purchaseaccount,.invoice_prefix').val('');
            $(modal).find('.status').val('active');
        }
    });
</script>
