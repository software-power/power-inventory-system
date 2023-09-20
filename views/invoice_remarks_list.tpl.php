<div class="row d-flex justify-content-center">
    <div class="col-md-5">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Invoice Remarks</h2>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end align-items-center">
                        <a href="#remark-modal" data-toggle="modal">
                            <i class="fa fa-plus"></i> Add Remarks</a>
                        <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="<?=count($remarks)>10?'userTable':''?>" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($remarks as $id => $R) { ?>
                            <tr>
                                <td><?= $id + 1 ?></td>
                                <td><?= $R['name'] ?></td>
                                <td><?= $R['status'] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" title="Edit" data-toggle="modal"
                                               href="#remark-modal" data-id="<?= $R['id'] ?>" data-type="edit"
                                               data-name="<?= $R['name'] ?>" data-status="<?= $R['status'] ?>">
                                                <i class="fa-pencil fa"></i> Edit</a>
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

<div class="modal fade" id="remark-modal" tabindex="-1" role="dialog" aria-labelledby="remark-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="?module=sales&action=save_invoice_remark" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="mode">Add</span> Remarks</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Name</label>
                        <input type="hidden" class="remarkid" name="remark[id]">
                        <input type="text" class="form-control name" name="remark[name]" placeholder="name" required
                               onkeyup="checkExist(this)">
                    </div>
                    <div class="form-group">
                        <label for="">Status</label>
                        <select class="form-control status" name="remark[status]" required>
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
    $(function () {
        $('#remark-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            if (source.data('type') == 'edit') {
                $(modal).find('.mode').text('Edit');
                $(modal).find('.remarkid').val(source.data('id'));
                $(modal).find('.name').val(source.data('name'));
                $(modal).find('.status').val(source.data('status'));
            } else {
                $(modal).find('.mode').text('Add');
                $(modal).find('.remarkid').val('');
                $(modal).find('.name').val('');
                $(modal).find('.status').val('active');
            }
        });
    })
</script>
