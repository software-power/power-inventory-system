<header class="page-header">
    <h2>Targets</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="<?= CS_TALLY_TRANSFER ? 'col-md-8' : 'col-md-6' ?>">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">List of Targets</h2>
                <? if (Users::can(OtherRights::add_target)) { ?>
                    <a href="#target-modal" class="btn btn-success" data-toggle="modal">Add</a>
                <? } ?>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>User</th>
                            <th style="width: 40%;">Targets</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        $USER_CAN_EDIT = Users::can(OtherRights::edit_target);
                        foreach ($targets as $R) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $R['username'] ?></td>
                                <td>
                                    <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Department</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? $dcount = 1;
                                        foreach ($R['departments'] as $d) { ?>
                                            <tr>
                                                <td><?= $dcount ?></td>
                                                <td><?= $d['departmentname'] ?></td>
                                                <td class="text-right"><?= formatN($d['amount']) ?></td>
                                            </tr>
                                            <? $dcount++;
                                        } ?>
                                        <tr>
                                            <td colspan="2">TOTAL</td>
                                            <td class="text-weight-bold text-right"><?= formatN($R['total_amount']) ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <? if ($USER_CAN_EDIT) { ?>
                                        <div class="d-flex">
                                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#target-modal"
                                                    data-mode="edit" data-userid="<?= $R['userid'] ?>" data-username="<?= $R['username'] ?>"
                                                    data-departments='<?= json_encode($R['departments']) ?>'>
                                                <i class="fa fa-pencil"></i> Edit
                                            </button>
                                            <form action="<?=url('target', 'delete_target')?>" method="post" class="m-none ml-sm" onsubmit="confirm('Do you want to delete targets?')">
                                                <input type="hidden" name="userid" value="<?=$R['userid']?>">
                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</button>
                                            </form>
                                        </div>
                                    <? } ?>
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
</div>

<div class="modal fade" id="target-modal" role="dialog" aria-labelledby="target-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">User Target & Sale Limit</h4>
            </div>
            <form action="<?= url('target', 'save_target') ?>" method="post">
                <input type="hidden" class="input_mode" name="mode" value="new">
                <div class="modal-body">
                    <div class="form-group">
                        User:
                        <select id="target-userid" name="userid" class="form-control" required></select>
                    </div>
                    <div class="d-flex justify-content-end mb-md">
                        <button type="button" class="btn btn-primary btn-sm" onclick="addTargetDepartment()">Add Target</button>
                    </div>
                    <table class="table  table-condensed departments" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Department</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm save-btn">Save</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
    });

    $('#target-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        $(modal).find('table.departments tbody').empty();

        if ($(source).data('mode') == 'edit') {
            $(modal).find('.input_mode').val('update');
            $(modal).find('#target-userid').select2().select2('destroy');
            $(modal).find('#target-userid').empty().append(`<option value="${source.data('userid')}">${source.data('username')}</option>`);
            let departments = source.data('departments');
            // console.log(departments);
            $.each(departments, function (i, d) {
                let departments = `
                        <option value="">-- choose department --</option>
                        <? foreach ($departments as $d) { ?>
                            <option ${d.departmentid ==<?= $d['id'] ?>? 'selected' : ''} value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                        <? } ?>
        `;
                let row = `<tr>
                            <td class="row-count"></td>
                            <td>
                                <select name="departmentid[]" class="form-control departmentid" required onchange="checkDepartment(this)">
                                    ${departments}
                                </select>
                            </td>
                            <td>
                                <input type="text" name="amount[]" class="form-control amount" placeholder="target amount" required value="${d.amount}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" title="remove" onclick="removeDepartment(this)"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>`;
                $('#target-modal table.departments tbody').append(row);
            });
            row_counter();
            format_inputs();
        } else {
            $(modal).find('.input_mode').val('new');
            $(modal).find('#target-userid').val('').trigger('change');
            initSelectAjax('#target-userid', '?module=users&action=getUser&format=json', 'Choose salesperson');
        }
    });

    function format_inputs() {
        thousands_separator('input.amount');
    }

    function addTargetDepartment() {
        let departments = `
                        <option value="">-- choose department --</option>
                        <? foreach ($departments as $d) { ?>
                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                        <? } ?>
        `;
        let row = `<tr>
                            <td class="row-count"></td>
                            <td>
                                <select name="departmentid[]" class="form-control departmentid" required onchange="checkDepartment(this)">
                                    ${departments}
                                </select>
                            </td>
                            <td>
                                <input type="text" name="amount[]" class="form-control amount" placeholder="target amount" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-sm" title="remove" onclick="removeDepartment(this)"><i class="fa fa-close"></i></button>
                            </td>
                        </tr>`;
        $('#target-modal table.departments tbody').append(row);
        row_counter();
        format_inputs();
    }

    function removeDepartment(obj) {
        $(obj).closest('tr').remove();
        row_counter();
    }

    function row_counter() {
        $('#target-modal table.departments tbody tr').each(function (i, tr) {
            $(tr).find('td.row-count').text(++i);
        });
    }

    function checkDepartment(obj) {
        let departmentid = $(obj).val();
        if (departmentid) {
            $('#target-modal .departmentid').not(obj).each(function (i, d) {
                if (departmentid == $(d).val()) {
                    triggerError('Department already selected!');
                    $(obj).val('').trigger('change');
                }
            });
        }
    }
</script>
