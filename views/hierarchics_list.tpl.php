<header class="page-header">
    <h2>Hierarchics</h2>
</header>
<div class="col-md-10 col-md-offset-1">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="panel-title">List of Hierarchics</h2>
                </div>
                <div class="col-md-9 d-flex justify-content-end">
                    <a href="#add-modal" data-toggle="modal" class="btn btn-default">
                        <i class="fa fa-plus"></i> Add Hierarchics</a>
                    <a href="?module=hierarchics&action=product_hierarchs_list" class="btn btn-default ml-md">
                        <i class="fa fa-list"></i> Hierarchics Price list</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Percentages</th>
                        <th>Commission</th>
                        <th>Target</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($hierarchic_list as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $id + 1 ?></td>
                            <td><?= $R['name'] ?></td>
                            <td><?= $R['level'] ?></td>
                            <td><?= formatN($R['percentage'], 0) ?>%</td>
                            <td><?= $R['commission'] ?>%</td>
                            <td><?= $R['target'] ?>%</td>
                            <td><?= $R['status'] ?></td>
                            <td class="d-flex justify-content-center">
                                <a href="#add-modal" data-toggle="modal" class="btn btn-default text-primary" title="Edit"
                                   data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>" data-level="<?= $R['level'] ?>"
                                   data-percentage="<?= $R['percentage'] ?>" data-commission="<?= $R['commission'] ?>" data-targetpercent="<?= $R['target'] ?>"
                                   data-status="<?= $R['status'] ?>">
                                    <i class="fa-pencil fa"></i></a>
                                <form action="<?= url('hierarchics', 'delete') ?>" method="post" style="margin:0"
                                      onsubmit="return confirm('Are you sure you want to delete this hierarchic?')">
                                    <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                    <button class="btn btn-default ml-sm text-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="add-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><span class="mode">Add</span> Hierarchic</h4>
            </div>
            <form action="<?= url('hierarchics', 'hierarchics_save') ?>" method="post">
                <input type="hidden" class="h_id inputs" name="hierarchic[id]">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input required title="Name is required" placeholder="hierarchic Name"
                               type="text" class="form-control inputs name" name="hierarchic[name]">
                    </div>
                    <div class="form-group">
                        <label>Level</label>
                        <input required title="Level is required" placeholder="hierarchic level"
                               type="number" class="form-control inputs level" name="hierarchic[level]">
                    </div>
                    <div class="form-group">
                        <label>Percentage %</label>
                        <input required title="Percentage is required" placeholder="hierarchic percentage"
                               type="number" class="form-control inputs percentage" name="hierarchic[percentage]"
                               step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Commission %</label>
                        <input required placeholder="commission percentage"
                               type="number" class="form-control inputs commission" name="hierarchic[commission]"
                               step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Target %</label>
                        <input required placeholder="target percentage"
                               type="number" class="form-control inputs targetpercent" name="hierarchic[target]"
                               step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="hierarchic[status]" class="form-control inputs status" required>
                            <option value="">-- choose --</option>
                            <option value="active">Active</option>
                            <option value="inactive">In-Active</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#add-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let id = source.data('id');
        let name = source.data('name');
        let level = source.data('level');
        let percentage = source.data('percentage');
        let commission = source.data('commission');
        let targetpercent = source.data('targetpercent');
        let status = source.data('status');
        let modal = $(this);

        $(modal).find('.inputs').val('');

        if (id) {
            $(modal).find('.mode').text('Edit');
            $(modal).find('.h_id').val(id);
            $(modal).find('.name').val(name);
            $(modal).find('.level').val(level);
            $(modal).find('.percentage').val(percentage);
            $(modal).find('.commission').val(commission);
            $(modal).find('.targetpercent').val(targetpercent);
            $(modal).find('.status').val(status);
        } else {
            $(modal).find('.mode').text('Add');
        }
    });
</script>