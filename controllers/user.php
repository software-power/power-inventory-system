<?

if ($action == 'user_list') {
//    debug($_GET);
    Users::isAllowed();
    $status = $_GET['user_status'];

    $tData['user_count'] = Users::$userClass->countWhere("1 = 1");

    $tData['check'] = $Users->userList('', "", $status);
    $data['content'] = loadTemplate('users-list.tpl.php', $tData);
}

if ($action == 'inactive') {

    $uData['status'] = 'inactive';
    $tData['check'] = $Users->find($uData);
    $tData['inactive'] = 'true';
    $data['content'] = loadTemplate('users-list.tpl.php', $tData);
}

if ($action == 'users_undelete') {
    $userId = $_POST['id'];
    $uData['status'] = 'active';
    $Users->update($userId, $uData);
    $_SESSION['message'] = 'User activated';
    redirect('users', 'user_list');
}

if ($action == 'users_delete_perm') {
    $Id = $_GET['id'];
    $uData['status'] = 'deleted';
    $Users->update($Id, $uData);
    $_SESSION['message'] = 'User deleted';
    redirect('users', 'index');
}

if ($action == 'users_delete') {
    $userId = $_POST['id'];

    $Users->delete($userId);
    $_SESSION['message'] = 'User deleted';
    redirect('users', 'user_list');
}

if ($action == 'users_reset_password') {
    Users::can(OtherRights::reset_password, true);
    $id = $_POST['id'];
    $option = ['cost' => 12];
    $Users->update($id, [
        'changepass' => 1,
        'password' => password_hash('power@123', PASSWORD_BCRYPT, $option)
    ]);
    $_SESSION['message'] = 'User password reset successfully!';
    redirect('users', 'user_list');
}

if ($action == 'users_edit') {
    Users::can(OtherRights::edit_user, true);
    $id = $_GET['id'];
    $tData['users'] = $Users->get($id);
    $tData['edit'] = 1;
    $action = 'users_add';
}

if ($action == 'users_add') {
    Users::can(OtherRights::add_user, true);

    $tData['roles'] = IS_ADMIN && $_SESSION['member']['delete'] == 'no'
        ? $Roles->getAll()
        : array_filter($Roles->getAll(), function ($r) {
            return $r['id'] != 1;
        });
    $tData['permitted_users'] = $Users->find(['roleid' => 2, 'status' => 'active']);
    $tData['locations'] = $Locations->getAll();
    $tData['depts'] = $Departments->find(array('status' => 'active'));
    $tData['branches'] = $Branches->find(array('status' => 'active'));
    $tData['hierachicList'] = $Hierarchics->find(array('status' => 'active'));
    $tData['tally_leagers'] = $TallyLedgers->getAllActive();

    $tData['reciepts'] = $RecieptsTypes->getAllActive();

    // debug($tData);
    $data['content'] = loadTemplate('users_edit.tpl.php', $tData);
}

if ($action == 'users_save') {

    $user = $_POST['users'];
    validate($user);
//    debug($_POST);
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {

        if ($user['roleid'] == 1 && !(IS_ADMIN && $_SESSION['member']['delete'] == 'no')) {
            $_SESSION['error'] = "You dont have permission to make user admin!";
            redirectBack();
        }
        $user['sale_limit'] = removeComma($user['sale_limit']);
//    debug($user);
        if (empty($user['id'])) {
            Users::can(OtherRights::add_user, true);

            $user_count = Users::$userClass->countWhere("1 = 1");
//        debug([LICENSE_MODULES,$user_count,$_POST]);
            if ((isset(LICENSE_MODULES['usr']) && LICENSE_MODULES['usr'] > 0 && LICENSE_MODULES['usr'] <= $user_count)) {
                $_SESSION['error'] = "Allowed user limit already reached, You cant add more user";
                $_SESSION['delay'] = 10000;
                redirectBack();
            }

            if (Users::$userClass->countWhere(['username' => $user['username']]) > 0) throw new Exception("Username already taken!");

            $option = ['cost' => 12];
            $user['createdby'] = $_SESSION['member']['id'];
            $user['password'] = password_hash('power@123', PASSWORD_BCRYPT, $option);
            $user['status'] = 'active';

            if(!Users::$userClass->insert($user)) throw new Exception("Failed to insert new user!");
            $_SESSION['message'] = 'User Added';
        } else {
            Users::can(OtherRights::edit_user, true);

            $userid = $user['id'];
            if (Users::$userClass->countWhere(['username' => $user['username'],['id','!=',$userid]]) > 0) throw new Exception("Username already taken!");
            $user['modifiedby'] = $_SESSION['member']['id'];
            if(!Users::$userClass->update($user['id'], $user)) throw new Exception("Failed to update user!");

            $_SESSION['message'] = 'User Updated';
        }
        mysqli_commit($db_connection);
        redirect('users', 'user_list');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'choose_active_user') {
//    debug("choose users");
    $tData['user_count'] = Users::$userClass->countWhere("1 = 1");

    $tData['users'] = $Users->userList();
    $data['content'] = loadTemplate('choose-active-users.tpl.php', $tData);
}

if ($action == 'save_active_user') {
    $userids = $_POST['userid'];
    $userids[] = 1;
//    debug($userids);
    //manual query
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (!executeQuery("update users set status = 'inactive' where id != 1")) throw new Exception("Failed to deactivate users");
        if (!executeQuery("update users set status = 'active' where id in (" . implode(',', $userids) . ")")) throw new Exception("Failed to activate users");

        mysqli_commit($db_connection);
        $_SESSION['message'] = "User updated successfully";
        redirect('home', 'index');
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'ajax_checkusername') {
    $username = $_GET['username'];
    $aData = $Users->find(array('username' => $username));
    $aData = $aData[0];

    $response = array();
    $obj = null;

    if ($aData) {
        $obj->user = $aData['name'];
        $obj->status = 'found';
    } else {
        $obj->status = 'not found';
    }

    $response[] = $obj;
    $data['content'] = $response;
}

if ($action == 'ajax_salesPerson') {
    $icData = $Users->find(array('status' => 'active'));

    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj = null;
            $obj->text = $ic['name'];
            $obj->id = $ic['id'];
            $response['results'][] = $obj;
        }
    } else {
        $obj = null;
        $obj->test = 'No results';
        $obj->id = 0;
        $response['results'][] = $obj;
    }
    $data['content'] = $response;
}

if ($action == 'ajax_getUser') {
    $data['layout'] = '../layout_blank.tpl.php';
    $icData = $Users->search($_GET['search']['term']);

    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj = null;
            $obj->text = $ic['name'];
            $obj->id = $ic['id'];
            $response['results'][] = $obj;
        }
    } else {
        $obj = null;
        $obj->text = 'No results';
        $obj->id = 0;
        $response['results'][] = $obj;
    }
    $data['content'] = $response;
}