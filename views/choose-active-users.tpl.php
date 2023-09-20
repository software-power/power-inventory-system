<header class="page-header">
    <h2>Choose Active Users</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-xs-12 col-lg-10">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Choose Active Users</h2>
                <h5 class="text-danger">You are seeing this screen because the number of active user exceed the allowed limit of active user
                    from your license</h5>
            </header>
            <div class="panel-body">
                <form action="<?= url('users', 'save_active_user') ?>" method="post">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <? if (isset(LICENSE_MODULES['usr']) && LICENSE_MODULES['usr'] > 0) { ?>
                                <div>
                                    <h4>
                                        License user limit: <span id="user-limit"
                                                                  class="text-weight-bold text-danger"><?= LICENSE_MODULES['usr'] ?></span>
                                    </h4>
                                </div>
                            <? } ?>
                            <div>
                                <h4>
                                    Current active: <span id="current-active"
                                                          class="text-weight-bold text-success"><?= Users::$userClass->countWhere(['status' => 'active']) ?></span>
                                    <small id="exceed-msg" class="text-danger" style="display: none">limit exceeded</small>
                                </h4>
                            </div>
                        </div>
                        <button id="save-btn" class="btn btn-success btn-lg" style="display: none">Save</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-none" style="font-size: 10pt">
                            <thead>
                            <tr>
                                <th style="width: 30px">
                                    <button type="button" class="btn btn-default btn-sm" onclick="toggle_check()">Toggle</button>
                                </th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Branch</th>
                                <th>Location</th>
                                <th>Department</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?
                            foreach ($users as $id => $R) { ?>
                                <tr>
                                    <td>
                                        <? if ($R['delete'] == 'no') { ?>
                                            <label>
                                                <input type="checkbox" class="userid" checked disabled style="width: 20px;height: 20px;">
                                                <input type="hidden" name="userid[]" value="<?= $R['id'] ?>">
                                            </label>
                                        <? } else { ?>
                                            <label>
                                                <input type="checkbox" name="userid[]" class="userid changeable" value="<?= $R['id'] ?>"
                                                       onchange="count_active()"
                                                       style="width: 20px;height: 20px;" <?= $R['status'] == 'active' ? 'checked' : '' ?>>
                                            </label>
                                        <? } ?>
                                    </td>
                                    <td><?= $R['name'] ?></td>
                                    <td><?= $R['username'] ?></td>
                                    <td><?= $R['rolename'] ?></td>
                                    <td><?= $R['branchname'] ?></td>
                                    <td><?= $R['locationname'] ?></td>
                                    <td><?= $R['departmentname'] ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
    let TOGGLE_STATE = false;

    $(function () {
        count_active();
    });

    function toggle_check() {
        $('.userid.changeable:checkbox').prop('checked', TOGGLE_STATE);
        TOGGLE_STATE = !TOGGLE_STATE;
        count_active();
    }

    function count_active() {
        let user_limit = parseInt($('#user-limit').text()) || 0;
        let active_count = $('.userid:checkbox:checked').length;

        let exceed_limit = user_limit > 0 && active_count > user_limit;
        $('#current-active').text(active_count);
        if (exceed_limit) {
            $('#current-active').addClass('text-danger').removeClass('text-success');
            $('#exceed-msg').show('fast');
            $('#save-btn').hide();
        } else {
            $('#current-active').addClass('text-success').removeClass('text-danger');
            $('#exceed-msg').hide('fast');
            $('#save-btn').show();
        }
    }

    //checkbox column sorting
    $.fn.dataTable.ext.order['dom-checkbox'] = function (settings, col) {
        return this.api().column(col, {order: 'index'}).nodes().map(function (td, i) {
            return $('input', td).prop('checked') ? '1' : '0';
        });
    };
    $('#choose-table').DataTable({
        dom: '<"top"fBl>t<"bottom"ip>',
        columnDefs: [
            {
                targets: [0],
                orderDataType: 'dom-checkbox'
            }
        ],
        colReorder: true,
        keys: true,
        buttons: ['excelHtml5', 'csvHtml5'],
        exportOptions: {
            columns: ':not(:last-child)',
        },
    });
</script>