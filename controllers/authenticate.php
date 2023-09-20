<?


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($action == 'login') {
    //print_R($_SESSION);
    //die();
    $data['layout'] = 'layout_login.tpl.php';
    $data['content'] = loadTemplate('layout_login.tpl.php', array('username' => $_GET['username']));
}

if ($action == 'dologin') {
    $d['username'] = cleanInput($_POST['username']);
    $d['password'] = cleanInput($_POST['password']);
    $option = ['cost' => 12];
    $hashpaswword = password_hash($d['password'], PASSWORD_BCRYPT, $option);

    if (empty($d['username']) or empty($d['password'])) {
        $_SESSION['error'] = 'Please enter both Username and Password';
        $data['content'] = loadTemplate('layout_login.tpl.php', $d);
        redirect('authenticate', 'login');
    } else {

        $u['username'] = $d['username'];
        $userInfo = $Users->find($u);
        $verifyPassword = password_verify($d['password'], $userInfo[0]['password']);

        if (empty($userInfo) or $verifyPassword != 1) {
            $_SESSION['error'] = 'Invalid Username/Password';
            $data['content'] = loadTemplate('admin/login.tpl.php', $d);

            redirect('authenticate', 'login', 'username=' . $d['username']);
        } elseif ($userInfo[0]['changepass'] == 1) {
            $_SESSION['member'] = $userInfo[0];
            $_SESSION['message'] = 'Enter a new password';
            $data['content'] = loadTemplate('layout_changepassword.tpl.php');
            redirect('changepassword', 'index');

        } elseif ($userInfo[0]['status'] == 'inactive' || $userInfo[0]['status'] == 'deleted') {
            $_SESSION['error'] = 'You are not authorized to access this system';
            $data['content'] = loadTemplate('login.tpl.php', $d);
            redirect('authenticate', 'login', 'username=' . $d['username']);
        } else {
            $userrole = $Roles->find(array('id' => $userInfo[0]['roleid']));
            $userInfo[0]['role'] = $userrole[0]['name'];

            $_SESSION['member'] = $userInfo[0];
            $_SESSION['message'] = 'Successfully Logged In';
            if ($userInfo[0]['role'] != 'admin') redirect('home', 'index');
            else redirect('upload', 'index');

        }
    }
}

if ($action == 'install_index') {

    $dbHost = 'localhost';
    $dbUsername = $_POST['username'];
    $dbPassword = $_POST['password'];
    $dbName = $_POST['database'];
    $filePath = 'queue.sql';

    $insert = restoreDatabaseTables($dbHost, $dbUsername, $dbPassword, $dbName, $filePath);

    if ($insert == 1) {
        $_SESSION['message'] = 'Database imported successfully. <br><b>Username:</b> admin<br> <b>Password:</b> 123';

        $myfile = fopen("cfg/database.php", "w") or die("Unable to open file!");

        $txt = "<?php
					  \$config = array(
					  'server'   => '" . $dbHost . "',
					  'username' => '" . $dbUsername . "',
					  'password' => '" . $dbPassword . "',
					  'database' => '" . $dbName . "',
				    );";
        fwrite($myfile, $txt);
        fclose($myfile);

        unlink('install.php');

    } else {
        $_SESSION['error'] = 'Error connecting to database';
    }

    redirectBack();
}


if ($action == 'logout') {
    session_destroy();
    redirect('authenticate', 'login');
}

if ($action == 'access_page') {
    $data['layout'] = 'layout_blank.tpl.php';
    $data['content'] = loadTemplate('access_page.tpl.php');
}


if ($action == 'license') {
//    debug("License screen");
//    $data['plugins']=true;
    $data['layout'] = 'license_page.tpl.php';
}

if ($action == 'ajax_checkLicense') {
//    debug($_POST);
    $result['status'] = 'success';
    try {
        if (empty($_POST['token'])) throw new Exception("Missing token");
        if (empty($_POST['tin'])) throw new Exception("Missing TIN");

        $license = isKeyValid($_POST['token'], $_POST['tin'], CS_SOFTWARE_ID);
        $check_token = checkTokens($_POST['token'], $_POST['tin'], '', '', CS_COMPANY, 'validate');

//        debug($check_token);

        if ($license['status'] == 'invalid') throw new Exception($license['reason']);
        if ($check_token['status'] != 'success') throw new Exception($check_token['message']);
    } catch (Exception $e) {
        $result = ['status' => 'error', 'msg' => "Error: " . $e->getMessage()];
    }

    $data['content'] = $result;
}

if ($action == 'ajax_registerLicense') {

    global $db_connection;
    mysqli_begin_transaction($db_connection);

    $result['status'] = 'success';
    try {
        if (empty($_POST['token'])) throw new Exception("Missing token");
        if (empty($_POST['tin'])) throw new Exception("Missing TIN");

        //REGISTERING LICENSE
        $check_token = checkTokens($_POST['token'], $_POST['tin'], '', '', CS_COMPANY, 'register');

//        debug($check_token);
        if ($check_token['status'] != 'valid') throw new Exception($check_token['message']);

        if (!Settings::$staticClass->update(1, [
            'tin' => $_POST['tin'],
            'lice_token' => $check_token['token']
        ])) throw new Exception("Failed to update license");

        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $result = ['status' => 'error', 'msg' => "Error: " . $e->getMessage()];
    }

    $data['content'] = $result;
}