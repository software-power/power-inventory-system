<?php
if ($action == 'index') {
    Users::isAllowed();
    $tData['unitsList'] = $BulkUnits->getBulkUnits();
//    debug($tData['unitsList']);
    $tData['units'] = $Units->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Units List";
    $data['content'] = loadTemplate('bulk_units_list.tpl.php', $tData);
}

if ($action == 'save') {
    $unit = $_POST['unit'];

    //validate
    validate($unit);

    if ($unit['id']) {
        $BulkUnits->update($unit['id'], $unit);
        $_SESSION['message'] = 'Bulk unit updated successfully';
    } else {
        $exists = $BulkUnits->find(array('name' => $unit['name'], 'status' => "active"));
        if (empty($exists)) {
            $BulkUnits->insert($unit);
            $_SESSION['message'] = 'Bulk unit added successfully';
        } else {
            $_SESSION['error'] = 'Bulk unit already Exists';
        }
    }
    redirect('bulk_units', 'index');
}

if ($action == 'delete') {
//    debug($_POST);
    if (!empty($id = intval($_POST['id']))) {
        $BulkUnits->delete($id);
        $_SESSION['message'] = "Bulk unit deleted successfully";
    }
    redirect('bulk_units', 'index');
}

if ($action == 'ajax_getbulk') {

    $unit = $_GET['unit'];
    $bulkunits = $BulkUnits->find(array('unit' => $unit, 'status' => 'active'));

    $results['bulkunits'] = $bulkunits;
    $response[] = $results;
    $data['content'] = $response;
}


if ($action == 'ajax_getBulkUnits') {
    $icData = $BulkUnits->search($_GET['search']['term']);
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
