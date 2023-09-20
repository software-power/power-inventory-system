<?
if ($action == 'list') {
    Users::isAllowed();

    $tData['location_count'] = Locations::$locationClass->countWhere([
        ['id', '>', 0]
    ]);

    $tData['locations'] = $Locations->locationList();
    $tData['banks'] = Banks::$banksClass->getAllActive();
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $data['content'] = loadTemplate('locations-list.tpl.php', $tData);
}

if ($action == 'save_location') {
    Users::isAllowed();
    $location = $_POST['location'];
    validate($location);
    $bankids = isset($_POST['bankids']) ? $_POST['bankids'] : [];
    $location['bankids'] = implode(',', $bankids);
//    debug($location);

    if (empty($location['id'])) {

        $exists = $Locations->find(['name' => $location['name'], 'branchid' => $location['branchid']]);

        if (!empty($exists)) {
            $_SESSION['error'] = 'Location already Exists';
            redirectBack();
        }
        $location['createdby'] = $_SESSION['member']['id'];
        $Locations->insert($location);
    } else {
        $location['modifiedby'] = $_SESSION['member']['id'];
        $location['dom'] = TIMESTAMP;
        $Locations->update($location['id'], $location);
    }
    if ($location['default_load'] == 1) {
        $Locations->updateWhere(['branchid' => $location['branchid']], ['default_load' => 0]);
        $Locations->update($location['id'], ['default_load' => 1]);
    }

    $_SESSION['message'] = 'Location ' . ($location['id'] ? 'Updated' : 'Created') . ' successfully';
    redirect('locations', 'list');
}

if ($action == 'enable_disable') {
//    debug($_POST);
    $location = $_POST['location'];
    validate($location);
    $Locations->update($location['id'], $location);
    $_SESSION['message'] = 'Location updated';
    redirect('locations', 'list');
}

if ($action == 'ajax_getLocations') {
    $icData = $Locations->search($_GET['search']['term']);
//    debug($icData);
    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj = null;
            $obj->text = "{$ic['locationname']} - {$ic['branchname']}";
            $obj->id = $ic['locationid'];
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

if ($action == 'ajax_getBranchLocations') {
    $icData = $Locations->find(['branchid' => $_GET['branchid']]);
    $obj->status = 'success';
    $obj->data = $icData;
    $data['content'] = $obj;
}