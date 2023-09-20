<header class="page-header">
    <h2>Banks</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-6">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">List of Banks</h2>
                <a href="#banks-modal" class="btn btn-success" data-toggle="modal">Add</a>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Bank Name</th>
                            <th>Account Name</th>
                            <th>Account No</th>
                            <? if (CS_TALLY_TRANSFER) { ?>
                                <th>Ledger Name</th>
                            <? } ?>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($banks as $id => $R) { ?>
                            <tr>
                                <td><?= $id + 1 ?></td>
                                <td><?= $R['name'] ?></td>
                                <td><?= $R['accname'] ?></td>
                                <td><?= $R['accno'] ?></td>
                                <? if (CS_TALLY_TRANSFER) { ?>
                                    <td><?= $R['ledgername'] ?></td>
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
                                            <a class="dropdown-item" title="Edit" data-toggle="modal" data-target="#banks-modal" data-mode="edit"
                                               data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>" data-accname="<?= $R['accname'] ?>" data-accno="<?= $R['accno'] ?>" data-ledgername="<?= $R['ledgername'] ?>"
                                               data-status="<?= $R['status'] ?>">
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

<div class="modal fade" id="banks-modal" tabindex="-1" role="dialog" aria-labelledby="banks-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add Bank</h4>
            </div>
            <form action="<?= url('banks', 'bank_save') ?>" method="post">
                <input type="hidden" name="bank[id]" class="bankid">
                <div class="modal-body">
                    <div class="form-group">
                        Bank Name:
                        <input type="text" name="bank[name]" class="form-control name" placeholder="bank name" required>
                    </div>
                    <div class="form-group">
                        Account Name:
                        <input type="text" name="bank[accname]" class="form-control accname" placeholder="account name">
                    </div>
                    <div class="form-group">
                        Account Number:
                        <input type="text" name="bank[accno]" class="form-control accno" placeholder="account number">
                    </div>
                    <? if (CS_TALLY_TRANSFER) { ?>
                        <div class="form-group">
                            Ledger Name:
                            <input type="text" name="bank[ledgername]" class="form-control ledgername" placeholder="ledger name" required>
                        </div>
                    <? } ?>
                    <div class="form-group">
                        Status:
                        <select name="bank[status]" class="form-control status" required>
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
    $('#banks-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        if ($(source).data('mode')) {
            $(modal).find('.modal-title').text('Edit Bank');
            $(modal).find('.save-btn').text('Update');

            $(modal).find('.bankid').val(source.data('id'));
            $(modal).find('.name').val(source.data('name'));
            $(modal).find('.accname').val(source.data('accname'));
            $(modal).find('.accno').val(source.data('accno'));
            $(modal).find('.ledgername').val(source.data('ledgername'));
            $(modal).find('.status').val(source.data('status'));
        } else {
            $(modal).find('.modal-title').text('Edit Bank');
            $(modal).find('.save-btn').text('Save');
            $(modal).find('.name,.accname,.accno').val('');
            $(modal).find('.status').val('active');
        }
    });
</script>
