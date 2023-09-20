<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title"><?= $role['id'] ? 'Edit' : 'Add' ?> Role</h2>
            </header>
            <div class="panel-body">
                <form method="post" action="<?= url('roles', 'save_role') ?>">
                    <input type="hidden" value="<?= $role['id'] ?>" name="role[id]">
                    <div class="row mb-md">
                        <div class="col-md-3">
                            Role Name:
                            <input id="new-role" type="text" class="form-control" name="role[name]" placeholder="role name eg. manager, cashier, seller"
                                   value="<?= $role['name']?:$_GET['new_role'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            Status:
                            <select class="form-control" name="role[status]" required>
                                <option <?= selected($role['status'], 'active') ?> value="active">Active</option>
                                <option <?= selected($role['status'], 'inactive') ?> value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            Replicate from:
                            <select class="form-control" onchange="replicateRights(this)">
                                <option value="">-- none --</option>
                                <? foreach ($replicate_roles as $r) {
                                    if ($r['id'] == $role['id']) continue; ?>
                                    <option <?= selected($replicate_from, $r['id']) ?> value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-lg text-weight-bold">
                        <h5>Role Permissions</h5>
                    </div>
                    <div class="mt-lg mb-lg text-weight-bold">
                        <button type="button" class="btn btn-primary btn-sm" onclick="checkAllItem(true)"><i class="fa fa-check"></i> Check all
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="checkAllItem(false)"><i class="fa fa-close"></i> Uncheck all
                        </button>
                    </div>
                    <table class="table table-bordered" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>Menu</th>
                            <th>Submenu(s)</th>
                            <th>Permissions</th>
                        </tr>
                        </thead>
                        <? foreach ($menus as $m) { ?>
                            <tr>
                                <td style="vertical-align: middle;width: 10%;">
                                    <div class="form-check">
                                        <input class="form-check-input menu" type="checkbox" value="<?= $m['id'] ?>"
                                               onchange="toggleMenu(this)" id="menu_<?= $m['id'] ?>"
                                            <?= in_array($m['id'], $role['role_menuid']) ? 'checked' : '' ?>>
                                        <label class="form-check-label text-md" for="menu_<?= $m['id'] ?>">
                                            <?= $m['label'] ?>
                                        </label>
                                    </div>
                                </td>
                                <td style="width: 30%">
                                    <div class="row">
                                        <? foreach ($m['submenus'] as $index => $sub) { ?>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input submenu" type="checkbox"
                                                           name="submenuid[]" value="<?= $sub['id'] ?>" id="sub_<?= $sub['id'] ?>"
                                                           onchange="toggleSubmenu(this)"
                                                        <?= in_array($sub['id'], $role['role_submenuid']) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="sub_<?= $sub['id'] ?>">
                                                        <?= $sub['label'] ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                                </td>
                                <td style="width: 25%">
                                    <div class="row">
                                        <? foreach ($m['otherrights'] as $index => $other) { ?>
                                            <div class="col-md-4" data-container="body" data-toggle="popover" data-trigger="hover"
                                                 data-placement="left" title="Description"
                                                 data-content="<?= $other['description'] ?>">
                                                <div class="form-check">
                                                    <input class="form-check-input other-right" type="checkbox"
                                                           name="other[]"
                                                           onchange="toggleOtherRight(this)"
                                                           value="<?= $other['id'] ?>" id="or_<?= $other['id'] ?>"
                                                        <?= in_array($other['id'], $role['role_other_rights']) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="or_<?= $other['id'] ?>">
                                                        <?= $other['label'] ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <? } ?>
                                    </div>
                                </td>
                            </tr>
                        <? } ?>
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?= url('roles', 'roles_list') ?>" class="btn btn-success btn-block">
                                <i class="fa fa-list"></i> Roles List
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script>
    $(function () {
        $('[data-toggle="popover"]').popover();
    });

    function toggleMenu(obj) {
        if (!$(obj).is(':checked')) {
            $(obj).closest('tr').find('input:checkbox').prop('checked', false);
        }
    }

    function toggleSubmenu(obj) {
        if ($(obj).is(':checked')) {
            $(obj).closest('tr').find('input.menu:checkbox').prop('checked', true);
        }
    }

    function toggleOtherRight(obj) {
        if ($(obj).is(':checked')) {
            $(obj).closest('tr').find('input.menu:checkbox').prop('checked', true);
        }
    }

    function replicateRights(obj) {
        let replicate_from = $(obj).val();
        let roleid = `<?=$role['id']?>`;
        let new_role = $('#new-role').val();
        window.location.replace(`?roleid=${roleid}&module=roles&action=add_role&replicate=${replicate_from}&new_role=${new_role}`);
    }

    function checkAllItem(state = true) {
        $('input:checkbox').prop('checked', state);
    }
</script>