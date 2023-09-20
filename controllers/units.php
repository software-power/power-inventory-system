<?
if ($action == 'index') {
    Users::isAllowed();
    $tData['unitsList'] = $Units->find(array('status' => 'active'));
    $_SESSION['pagetitle'] = CS_COMPANY . " - Units List";
    $data['content'] = loadTemplate('units.tpl.php', $tData);
}

if ($action == 'save') {
//    debug($_POST);
    $unit = $_POST['unit'];
    validate($unit);
    if ($unit['id']) {
        $Units->update($unit['id'], $unit);
        $_SESSION['message'] = 'Unit updated successfully';
    } else {
        $exists = $Units->find(array('name' => $unit['name'], 'status' => "active"));
        if (empty($exists)) {
            $Units->insert($unit);
            $_SESSION['message'] = 'Unit added successfully';
        } else {
            $_SESSION['error'] = 'Unit Already Exists';
        }
    }
    redirect('units', 'index');
}

if ($action == 'delete') {
//    debug($_POST);
    if (!empty($id = intval($_POST['id']))) {
        $Units->delete($id);
        $_SESSION['message'] = "Unit deleted successfully";
    }
    redirect('units', 'index');
}


if ($action == 'ajax_getUnits') {
    $data['layout'] = '../layout_blank.tpl.php';
    $icData = $Units->search($_GET['search']['term']);
    //	$locId = $_GET['locId'];
    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj = null;
            $obj->text = $ic['name'] . " (" . $ic['abbr'] . ")";
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


if ($action == 'ajax_saveNewDepartment') {
    $response = [];
    $obj = null;
    if (empty(cleanInput($_POST['name']))) {
        $obj->status = 'error';
        $obj->details = "Name is required";
    } else {
        $name = cleanInput($_POST['name']);
        if ($Departments->searchResults($name)) { //if exists
            $obj->status = 'error';
            $obj->details = "Department name already exists";
        } else {
            $Departments->insert([
                'name' => $name,
                'createdby' => $_SESSION['member']['id']
            ]);

            $obj->status = 'success';
            $obj->details = "Department added successfully";
        }
    }

    $response[] = $obj;
    $data['content'] = $response;
}
