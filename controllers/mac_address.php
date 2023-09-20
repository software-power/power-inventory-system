<?php

if ($action == 'index') {
    Users::isAllowed();
    $tData['id'] = base64_decode($_GET['id']);

    $tData['mac_addresses'] = $Mac_address->getMacAddress();
    $tData['deleted'] = $Mac_address->getMacAddress('', true);
    $data['content'] = loadTemplate('mac_address_list.tpl.php', $tData);
}


if ($action == 'save_macaddress') {
//    debug($_POST);
    //mac address pattern ^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$

    $macaddress = $_POST['macaddress'];

    validate($macaddress);
    if (!$macaddress['id']) {
        $macaddress['created_by'] = $_SESSION['member']['id'];
        if ($Mac_address->find(['mac_address' => $macaddress['mac_address']])) {
            $_SESSION['error'] = "Mac Address already exists";
            redirectBack();
            die();
        }

        $Mac_address->insert($macaddress);

    } else {
        $macaddress['updated_by'] = $_SESSION['member']['id'];
        $macaddress['updated_at'] = TIMESTAMP;

        $current = $Mac_address->get($macaddress['id']);
        foreach ($Mac_address->find(['mac_address' => $macaddress['mac_address']]) as $item) {
            if ($current['id'] != $item['id'] && $current['mac_address'] == $item['mac_address']) {
                $_SESSION['error'] = "Mac Address already exists";
                redirectBack();
                die();
            }
        }
        $Mac_address->update($macaddress['id'], $macaddress);
    }
    $_SESSION['message'] = "Mac address " . ($macaddress['id'] ? "updated" : "added") . " successfully";
    redirect('mac_address', 'index');
}


if ($action == 'assign_user') {
//    debug($_POST);
    $mac_address_id = $_POST['mac_address_id'];
    $userid = $_POST['userid'];

    if (!$mac_address_id) {
        $_SESSION['error'] = "mac address is required";
        redirectBack();
    }

    if (!$userid) {
        $_SESSION['error'] = "Select user";
        redirectBack();
    }

    if (!$macaddress = $Mac_address->get($mac_address_id)) {
        $_SESSION['error'] = "Device not found!";
        redirectBack();
    }
    if (!$user = $Users->get($userid)) {
        $_SESSION['error'] = "User not found!";
        redirectBack();
    }

    $Users->update($userid, [
        'mac_address_id' => $mac_address_id,
        'mac_address_assigned_by' => $_SESSION['member']['id'],
        'mac_address_assigned_at' => TIMESTAMP
    ]);
    $_SESSION['message'] = "Mac address assigned successfully";
    redirect('mac_address', 'index');
}


if ($action == 'revoke') {
//    debug($_POST);
    $userid = $_POST['userid'];
    if (!$user = $Users->get($userid)) {
        $_SESSION['error'] = "User not found!";
        redirectBack();
        die();
    }
    $Users->update($userid, [
        'mac_address_id' => '',
        'mac_address_assigned_by' => '',
        'mac_address_assigned_at' => '',
        'mac_address_updated_by' => $_SESSION['member']['id'],
        'mac_address_updated_at' => TIMESTAMP,
    ]);
    $_SESSION['message'] = "Access revoked successfully";
    redirect('mac_address', 'index');
}

if ($action == 'block') {
//    debug($_POST);
    $mac_address_id = $_POST['id'];
    if (!$macaddress = $Mac_address->get($mac_address_id)) {
        $_SESSION['error'] = "Device not found!";
        redirectBack();
        die();
    }

    $Mac_address->update($mac_address_id, [
        'device_status' => Mac_address::DEVICE_STATUS_BLOCKED,
        'updated_by' => $_SESSION['member']['id'],
        'updated_at' => TIMESTAMP,
    ]);
    $_SESSION['message'] = "Device blocked successfully";
    redirect('mac_address', 'index');
}

if ($action == 'activate') {
//    debug($_POST);
    $mac_address_id = $_POST['id'];
    if (!$macaddress = $Mac_address->get($mac_address_id)) {
        $_SESSION['error'] = "Device not found!";
        redirectBack();
        die();
    }
    $Mac_address->update($mac_address_id, [
        'device_status' => Mac_address::DEVICE_STATUS_ACTIVE,
        'updated_by' => $_SESSION['member']['id'],
        'updated_at' => TIMESTAMP,
    ]);
    $_SESSION['message'] = "Device activate successfully";
    redirect('mac_address', 'index');
}

if ($action == 'delete') {
//    debug($_POST);
    $mac_address_id = $_POST['id'];
    if (!$macaddress = $Mac_address->get($mac_address_id)) {
        $_SESSION['error'] = "Device not found!";
        redirectBack();
        die();
    }
    $Mac_address->update($mac_address_id, [
        'device_status' => Mac_address::DEVICE_STATUS_BLOCKED,
        'deleted_by' => $_SESSION['member']['id'],
        'deleted_at' => TIMESTAMP,
    ]);

    $user = $Users->find(['mac_address_id' => $mac_address_id])[0];
    if ($user) {
        $Users->update($user['id'], [
            'mac_address_id' => '',
            'mac_address_assigned_by' => '',
            'mac_address_assigned_at' => '',
            'mac_address_updated_by' => $_SESSION['member']['id'],
            'mac_address_updated_at' => TIMESTAMP,
        ]);
    }
    $_SESSION['message'] = "Device deleted successfully";
    redirect('mac_address', 'index');
}

if ($action == 'restore') {
//    debug($_POST);
    $mac_address_id = $_POST['id'];
    if (!$macaddress = $Mac_address->get($mac_address_id)) {
        $_SESSION['error'] = "Device not found!";
        redirectBack();
        die();
    }
    $Mac_address->updateWhere(['id' => $mac_address_id], [
        'device_status' => Mac_address::DEVICE_STATUS_BLOCKED,
        'deleted_by' => null,
        'deleted_at' => null,
    ]);
    $_SESSION['message'] = "Device restored successfully";
    redirect('mac_address', 'index');
}

if ($action == 'add_mac_address_old') {

    $tData['id'] = base64_decode($_GET['id']);

    switch ($_GET["status"]) {
        case 'edit':

            $tData['mac_address'] = $Mac_address->find($tData)[0];

            $tData['status'] = "edit";

            $data['content'] = loadTemplate('mac_address_edit.tpl.php', $tData);

            break;
        case 'delete':

            $tData['mac_address'] = $Mac_address->update($tData['id'], array('status' => 'inactive'));

            $_SESSION['message'] = "Mac_Address Deleted Successfull";

            redirect("mac_address", "index");

            break;
        case'block-mac-address':

            $tData['mac_address'] = $Mac_address->update($tData['id'], array('status' => 'inactive', 'device_status' => 'BLOCKED'));

            $_SESSION['message'] = "Mac_Address Blocked Successfull";

            redirect("mac_address", "index");

            break;

        case "unblock-mac-address":

            $tData['mac_address'] = $Mac_address->update($tData['id'], array('status' => 'active', 'device_status' => 'active'));

            $_SESSION['message'] = "Mac_Address Un-Blocked Successfull";

            redirect("mac_address", "index&status=BLOCKED");

        default:

            $_SESSION['pagetitle'] = "Add MAC-Address";


            $data['content'] = loadTemplate('mac_address_edit.tpl.php', $tData);

            break;
    }
}

if ($action == "save_edit_old") {

    if (isset($_POST['device_name']) && isset($_POST['mac_address'])) {

        for ($i = 0; $i < sizeof($_POST['device_name']); $i++) {

            $tData['device_name'] = $_POST['device_name'][$i];

            $tData['mac_address'] = $_POST['mac_address'][$i];

            $tData['updated_by'] = $_SESSION['member']['id'];

            $tData['updated_at'] = date('Y-m-d H:i:s');

            $result = $Mac_address->update($_POST['mac_address_id'], $tData);

        }

        $_SESSION['message'] = "Mac Address Updated Successful";

        redirect('mac_address', 'index');
    }
}

if ($action == "save_mac_address_old") {

    if (isset($_POST['device_name']) && isset($_POST['mac_address'])) {

        for ($i = 0; $i < sizeof($_POST['device_name']); $i++) {

            $tData['device_name'] = $_POST['device_name'][$i];

            $tData['mac_address'] = $_POST['mac_address'][$i];

            $result = $Mac_address->checkMacAddress($_POST['mac_address'][$i]);

            if ($result) {
                $_SESSION['error'] = "Sorry Mac Address Exist";

                redirect('mac_address', 'index');
                die();
            }

            $tData['created_by'] = $_SESSION['member']['id'];

            $result = $Mac_address->insert($tData);

        }

        if ($result) {

            $_SESSION['message'] = "Mac Address Inserted Successful";

            redirect('mac_address', 'index');
        } else {

            $_SESSION['error'] = "Failed to Insert";

            redirect('mac_address', 'index');
        }
    } else {

        $_SESSION['error'] = "Error!: Validation Failed Both Fields are Mandatory";

        redirect('mac_address', 'index');
    }


}

if ($action == "assign_mac_address_old") {

    $tData['details'] = array("device_name" => base64_decode($_GET['device_name']), "mac_address" => base64_decode($_GET['mac_address']), "mac_address_id" => base64_decode($_GET['id']), "users" => $Users->getAllActive());
    // var_dump($tData);
    // die();
    $data['content'] = loadTemplate('assign_mac_address.tpl.php', $tData);
}
if ($action == "save_mac_address_assignment_old") {

    if (isset($_POST['targetId']) && isset($_POST['page_title'])) {

        $mResults = $Target_details->checkIfUserExist($_POST['userid']);

        if (!empty($mResults)) {

            $_SESSION['error'] = "Target Already Assigned To This User";

            redirect('target', 'index');

        }
        $result = $Target_details->insert(array('target_id' => $_POST['targetId'], 'user_id' => $_POST['userid'], 'created_by' => $_SESSION['member']['id']));

        if ($result) {

            $_SESSION['message'] = "Target Assigned Successful";

            redirect('target', 'index');

        }


    } else {

        $Users->update($_POST['userid'], array('mac_address_id' => $_POST['mac_address_id'], 'mac_address_assigned_by' => $_SESSION['member']['id'], 'mac_address_assigned_at' => date('Y-m-d H:i:s')));

        $_SESSION['message'] = "MAC Address Assigned Successful";

        redirect("mac_address", "index");

    }
}
