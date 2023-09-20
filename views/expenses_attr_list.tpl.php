<header class="page-header">
    <h2>Expense Attributes</h2>
</header>
<div class="col-md-8 col-md-offset-2">
    <div class="center-panel">
        <section class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Expense Attributes</h2>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end align-items-center">
                        <a href="#expense-attr-modal" data-toggle="modal"
                           class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn"><i class="fa fa-plus"></i> Add
                            Attribute</a>
                        <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <? if (CS_TALLY_TRANSFER) { ?>
                                <th>Tally Ledger Name</th>
                            <? } ?>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($attribute_list as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $id + 1 ?></td>
                                <td><?= $R['name'] ?></td>
                                <? if (CS_TALLY_TRANSFER) { ?>
                                    <td><?= $R['ledgername'] ?></td>
                                <? } ?>
                                <td><?= $R['status'] ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#expense-attr-modal" data-toggle="modal"
                                               data-mode="edit" data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>" data-ledgername="<?= $R['ledgername'] ?>"
                                               data-status="<?= $R['status'] ?>" title="Edit">
                                                <i class="fa-pencil fa"></i> Edit expenses</a>
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

<div class="modal fade" id="expense-attr-modal" tabindex="-1" role="dialog" aria-labelledby="expense-attr-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="?module=expenses&action=expense_attr_save" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="mode">Add</span> Attribute</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Name:</label>
                        <input type="hidden" class="attr_id inputs" name="attr[id]">
                        <input type="text" class="form-control name inputs" name="attr[name]" placeholder="name" required>
                    </div>
                    <? if (CS_TALLY_TRANSFER) { ?>
                        <div class="form-group">
                            <label for="">Tally Ledger Name:</label>
                            <input type="text" class="form-control ledgername inputs" name="attr[ledgername]" placeholder="ledger name" required>
                        </div>
                    <? } ?>
                    <div class="form-group">
                        <label for="">Status:</label>
                        <select class="form-control status inputs" name="attr[status]" required>
                            <option value="" selected>--status--</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btn-sm">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#expense-attr-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            $(modal).find('.inputs').val('');

            if ($(source).data('mode') === 'edit') {
                $(modal).find('.mode').text('Edit');
                $(modal).find('.attr_id').val(source.data('id'));
                $(modal).find('.name').val(source.data('name'));
                $(modal).find('.ledgername').val(source.data('ledgername'));
                $(modal).find('.status').val(source.data('status'));
            } else {
                $(modal).find('.mode').text('Add');
            }
        });
    });
</script>