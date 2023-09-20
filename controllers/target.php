<?

if ($action == 'list') {
    $userid = $_GET['userid'];
    $departmentid = $_GET['departmentid'];
    $tData['departments'] = $Departments->getAllActive();

    $tData['targets'] = $Targets->getList();

    $data['content'] = loadTemplate('targets.tpl.php', $tData);
}

if ($action == 'save_target') {
    $mode = $_POST['mode'];
    $userid = $_POST['userid'];
    $departmentid = $_POST['departmentid'];
    $amount = $_POST['amount'];
//    debug($_POST);
    validate($userid);
    validate($departmentid);
    validate($amount);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        if (!$Users->get($userid)) throw new Exception("User not found");
        if ($mode == 'new') {
            Users::can(OtherRights::add_target, true);
            if ($Targets->find(['userid' => $userid])) throw new Exception("User already have targets");
        } else {
            Users::can(OtherRights::edit_target, true);
        }
        $Targets->deleteWhere(['userid' => $userid]);
        foreach ($departmentid as $index => $did) {
            if (!$Departments->get($did)) throw new Exception("Department not found!");
            $Targets->insert([
                'userid' => $userid,
                'departmentid' => $did,
                'amount' => removeComma($amount[$index]),
                'createdby' => $_SESSION['member']['id']
            ]);
        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Targets saved";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}

if ($action == 'delete_target') {
    Users::can(OtherRights::edit_target, true);
    $userid = $_POST['userid'];
    validate($userid);

    if (!$Users->get($userid)) {
        $_SESSION['error'] = "User not found!";
        redirectBack();
    }
    $Targets->deleteWhere(['userid' => $userid]);
    $_SESSION['message'] = "Target deleted";
    redirectBack();
}