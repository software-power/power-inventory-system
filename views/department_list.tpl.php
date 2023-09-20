<header class="page-header">
    <h2>Department</h2>
</header>
<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">List of Department</h2>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end align-items-center">
                        <a href="#department-modal" class="btn btn-default btn-sm" data-toggle="modal">
                            <i class="fa fa-plus"></i> Add Department</a>
                        <a class="btn btn-default ml-sm" href="?module=home&action=index"> <i class="fa fa-home"></i> </a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="userTable" class="table table-hover mb-none" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <? if (CS_TALLY_TRANSFER) { ?>
                                <th>Tally Sales Account</th>
                            <? } ?>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($department as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $id + 1 ?></td>
                                <td><?= $R['name'] ?></td>
                                <? if (CS_TALLY_TRANSFER) { ?>
                                    <td><?= $R['tally_sales_account'] ?></td>
                                <? } ?>
                                <td><?= $R['status'] ?></td>
                                <td>
                                    <? if (IS_ADMIN) { ?>
                                        <a class="btn btn-default btn-xs" href="#department-modal" data-toggle="modal"
                                           data-edit="on" data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>"
                                           data-tallyaccount="<?= $R['tally_sales_account'] ?>" data-status="<?= $R['status'] ?>"
                                           title="Edit"><i class="fa-pencil fa"></i> edit</a>
                                    <? }; ?>
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

<div class="modal fade" id="department-modal" tabindex="-1" role="dialog" aria-labelledby="department-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="<?= url('departments', 'department_save') ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="mode">Add</span> Department</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="hidden" class="departid" name="depart[id]">
                        <input type="text" class="form-control name" name="depart[name]" placeholder="name" required>
                    </div>
                    <? if (CS_TALLY_TRANSFER) { ?>
                        <div class="form-group">
                            <label for="">Tally Sales Account</label>
                            <input type="text" class="form-control tally_account" name="depart[tally_sales_account]" placeholder="tally sales account"
                                   required>
                        </div>
                    <? } ?>
                    <div class="form-group">
                        <label for="">Status</label>
                        <select class="form-control status" name="depart[status]" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm confirm-btn">Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#department-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        if (source.data('edit') === 'on') {
            $(modal).find('.mode').text('Edit');
            $(modal).find('.departid').val(source.data('id'));
            $(modal).find('.name').val(source.data('name'));
            $(modal).find('.tally_account').val(source.data('tallyaccount'));
            $(modal).find('.status').val(source.data('status'));
        } else {
            $(modal).find('.mode').text('Add');
            $(modal).find('.departid, .name, .tally_account').val('');
            $(modal).find('.status').val($(modal).find('select.status option').eq(0).val());
        }
    });
</script>