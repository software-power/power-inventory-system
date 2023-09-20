<header class="page-header">
    <h2>Bill Types</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-6">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">Bill Types</h2>
                <a href="#bill-type-modal" class="btn btn-success" data-toggle="modal">Add</a>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Billing Interval</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($bill_types as $id => $R) { ?>
                            <tr>
                                <td><?= $R['id'] ?></td>
                                <td><?= $R['name'] ?></td>
                                <td><?= $R['bill_interval'] ?> <?= ucfirst($R['type']) ?></td>
                                <td class="text-capitalize"><?= $R['status'] ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" title="Edit" data-toggle="modal" data-target="#bill-type-modal" data-mode="edit"
                                               data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>" data-billinterval="<?= $R['bill_interval'] ?>"
                                               data-type="<?= $R['type'] ?>" data-status="<?= $R['status'] ?>">
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

<div class="modal fade" id="bill-type-modal" tabindex="-1" role="dialog" aria-labelledby="bill-type-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add Bill Type</h4>
            </div>
            <form action="<?= url('recurring_bills', 'bill_type_save') ?>" method="post">
                <input type="hidden" name="billtype[id]" class="billtypeid">
                <div class="modal-body">
                    <div class="form-group">
                        Name:
                        <input type="text" name="billtype[name]" class="form-control name" placeholder="type name" required>
                    </div>
                    <div class="form-group">
                        Type:
                        <select name="billtype[type]" class="form-control type" required>
                            <option value="" selected disabled>-- type --</option>
                            <? foreach ($period_types as $p) { ?>
                                <option value="<?= $p ?>"><?= ucfirst($p) ?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        Billing interval:
                        <input type="number" min="1" value="1" name="billtype[bill_interval]" class="form-control interval" placeholder="billing interval" required>
                    </div>
                    <div class="form-group">
                        Status:
                        <select name="billtype[status]" class="form-control status" required>
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
    $('#bill-type-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        if ($(source).data('mode')) {
            $(modal).find('.modal-title').text('Edit Bill type');
            $(modal).find('.save-btn').text('Update');

            $(modal).find('.billtypeid').val(source.data('id'));
            $(modal).find('.name').val(source.data('name'));
            $(modal).find('.type').val(source.data('type'));
            $(modal).find('.interval').val(source.data('billinterval'));
            $(modal).find('.status').val(source.data('status'));
        } else {
            $(modal).find('.modal-title').text('Add Branch');
            $(modal).find('.save-btn').text('Save');
            $(modal).find('.name,.type').val('');
            $(modal).find('.interval').val('1');
            $(modal).find('.status').val('active');
        }
    });
</script>
