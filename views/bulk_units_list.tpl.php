<style>
    .show-fiscal-btn {
        display: 'block';
    }

    .hide-fiscal-btn {
        display: 'none';
    }

    .panel-actions a, .panel-actions .panel-action {
        font-size: 21px;
    }

    .input-group-rounded input.form-control:first-child, .input-group-rounded input.form-control:last-child, .input-search input.form-control:first-child, .input-search input.form-control:last-child {
        border-radius: 0;
        height: 44px;
        font-size: 15px;
    }

    .table {
        width: 100%;
        font-size: 14px;
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

    .badge-orange {
        background-color: #47a447;
    }

    .badge-red {
        background-color: #d2322d;
    }

    .center-panel {
        width: 80%;
        margin: 0 auto;
    }

    .table-responsive {
        min-height: 150px;
    }


</style>
<div class="center-panel">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="panel-title">List of Bulk Units Of Measurments</h2>
                </div>
                <div class="col-md-9 d-flex justify-content-end align-items-center">
                    <a href="#bulkUnitModal" data-toggle="modal" class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn"><i
                                class="fa fa-plus"></i> Add New</a>
                    <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($unitsList as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $id + 1 ?></td>
                            <td><?= $R['name'] ?></td>
                            <td><?= $R['rate'] ?> <?= $R['singleUnitAbbr'] ?></td>
                            <td><?= $R['status'] ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#bulkUnitModal" data-toggle="modal"
                                           data-mode="edit" data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>"
                                           data-abbr="<?= $R['abbr'] ?>" data-unit="<?= $R['unit'] ?>"
                                           data-rate="<?= $R['rate'] ?>" data-status="<?= $R['status'] ?>"
                                           title="Edit"><i class="fa-pencil fa"></i> Edit</a>
                                        <form action="<?= url('bulk_units', 'delete') ?>" method="POST"
                                              onsubmit="return confirm('Do you want to delete this bulk unit?')" style="margin:0;">
                                            <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                            <button class="dropdown-item"><i class="fa-trash fa"></i> Delete</button>
                                        </form>
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

<div class="modal fade" id="bulkUnitModal" role="dialog" aria-labelledby="bulkUnitModal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><span class="mode">Add</span> Bulk Unit</h4>
            </div>
            <form action="<?= url('bulk_units', 'save') ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="hidden" name="unit[id]" class="bulk_id inputs">
                        <input type="text" class="form-control name inputs" name="unit[name]" required>
                    </div>
                    <div class="form-group">
                        <label>Abbreviation</label>
                        <input type="text" class="form-control abbr inputs" name="unit[abbr]" required>
                    </div>
                    <div class="form-group">
                        <label>Single Unit</label>
                        <select id="unitId" name="unit[unit]" class="form-control unit inputs" required>
                            <option value="" selected>--Single Unit--</option>
                            <? foreach ($units as $index => $unit) { ?>
                                <option value="<?= $unit['id'] ?>"><?= $unit['name'] ?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rate</label>
                        <input type="number" min="1" class="form-control rate inputs" name="unit[rate]" required
                               placeholder="rate">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="unit[status]" class="form-control status inputs" required>
                            <option value="" selected>--Status--</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm saveBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#bulkUnitModal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            console.log(source.data('id'));

            if (source.data('mode') == 'edit') {
                $(modal).find('.mode').text('Edit');
                $(modal).find('.saveBtn').text('Update');
                $(modal).find('.bulk_id').val(source.data('id'));
                $(modal).find('.name').val(source.data('name'));
                $(modal).find('.abbr').val(source.data('abbr'));
                $(modal).find('.unit').val(source.data('unit'));
                $(modal).find('.rate').val(source.data('rate'));
                $(modal).find('.status').val(source.data('status'));
            } else {
                $(modal).find('.mode').text('Add');
                $(modal).find('.inputs').val('');
                $(modal).find('.saveBtn').text('Save');

            }
        })
    });
</script>