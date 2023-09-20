<?

if ($action == 'roles_list') {
    Users::isAllowed();
    $tData['roles'] = $Roles->getList();
    $data['content'] = loadTemplate('roles_list.tpl.php', $tData);
}

if ($action == 'add_role') {
    Users::can(OtherRights::edit_roles, true);
    $roleid = $_GET['roleid'];
    $replicate_from = $_GET['replicate'];
    if ($roleid == 1) {
        $_SESSION['error'] = "Role cant be edited";
        redirect('roles', 'roles_list');
    }
    $role = $Roles->get($roleid);
    $menus = $Menus->find(['status' => 1], 'sortno');
    foreach ($menus as $index => $menu) {
        $menus[$index]['submenus'] = $Submenus->find(['menuid' => $menu['id'], 'status' => 1], 'sortno');
        $menus[$index]['otherrights'] = $OtherRights->find(['menuid' => $menu['id']], "sort");
    }

    $role_menuid = array_column($RoleRights->getList($roleid), 'menuid') ?: [];
    $role_submenuid = array_column($RoleRights->getList($roleid), 'submenuid') ?: [];
    $role_other_rights = array_column($RoleOtherRights->find(['roleid' => $roleid]), 'orid') ?: [];

    if ($replicate_from) {
        $replicate_menuid = array_column($RoleRights->getList($replicate_from), 'menuid');
        $replicate_submenuid = array_column($RoleRights->getList($replicate_from), 'submenuid');
        $replicate_other_rights = array_column($RoleOtherRights->find(['roleid' => $replicate_from]), 'orid');

        $role_menuid = array_merge($role_menuid, $replicate_menuid);
        $role_submenuid = array_merge($role_submenuid, $replicate_submenuid);
        $role_other_rights = array_merge($role_other_rights, $replicate_other_rights);
    }

    $role['role_menuid'] = $role_menuid;
    $role['role_submenuid'] = $role_submenuid;
    $role['role_other_rights'] = $role_other_rights;
//    debug($menus);
    $tData['replicate_from'] = $replicate_from;
    $tData['role'] = $role;
    $tData['menus'] = $menus;
    $tData['replicate_roles'] = array_filter($Roles->getAllActive(), function ($r) {
        return $r['id'] != 1;
    });
    $data['content'] = loadTemplate('role_rights.tpl.php', $tData);
}

if ($action == 'save_role') {
    Users::can(OtherRights::edit_roles, true);
    $role = $_POST['role'];
    $submenuids = $_POST['submenuid'];
    $other = $_POST['other'];
    validate($role);
    validate($submenuids);

    if (!$role['id']) {//new
        if ($Roles->find(['name' => $role['name']])) {
            $_SESSION['error'] = "Role name already exists";
            redirectBack();
        }
        $role['createdby'] = $_SESSION['member']['id'];
        $Roles->insert($role);
        $roleid = $Roles->lastId();
    } else {
//        debug('update');
        if ($role['id'] == 1) {
            $_SESSION['error'] = "Admin role cant be edited!";
            redirectBack();
        }
        $roleid = $role['id'];
        //clear old permissions
        $RoleRights->deleteWhere(['roleid' => $roleid]);
        $RoleOtherRights->deleteWhere(['roleid' => $roleid]);

        $role['modifiedby'] = $_SESSION['member']['id'];
        $role['dom'] = TIMESTAMP;
        $Roles->update($roleid, $role);
    }

    //submenus
    foreach ($submenuids as $submenuid) {
        $RoleRights->insert([
            'roleid' => $roleid,
            'submenuid' => $submenuid,
            'createdby' => $_SESSION['member']['id'],
        ]);
    }
    //other rights
    foreach ($other as $orid) {
        $RoleOtherRights->insert([
            'roleid' => $roleid,
            'orid' => $orid,
        ]);
    }

    $_SESSION['message'] = "Role " . ($role['id'] ? 'updated' : 'saved') . " successfully";
    redirect('roles', 'roles_list');
}

if ($action == 'delete_role') {
    Users::isAllowed();
    $roleid = removeSpecialCharacters($_GET['roleid']);
    global $db_connection;
    try {
        if ($roleid == 1) throw new Exception("This role cant be deleted!");
        $role = $Roles->get($roleid);
        if (!$role) throw new Exception("Role not found!");
        if ($Users->countWhere(['roleid' => $roleid]) > 0) throw new Exception("Roles have users, cant delete!");
        $Roles->deleteWhere(['id' => $roleid]);
        $_SESSION['message'] = "Role deleted";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}