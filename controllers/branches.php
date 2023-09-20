<?php

if ($action == 'branch_index') {
    Users::isAllowed();
    $tData['branch'] = $Branches->getAll();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Branches List";
    $data['content'] = loadTemplate('branch_list.tpl.php', $tData);
}


if ($action == 'branch_save') {
    Users::isAllowed();
    $branch = $_POST['branch'];

    //validate
    validate($branch);

    if (empty($branch['id'])) {
        $exists = $Branches->find(['name' => $branch['name']]);
        if (!empty($exists)) {
            $_SESSION['error'] = 'Branch Already Exists';
            redirect('branches', 'branch_index');
        }
        $branch['doc'] = TIMESTAMP;
        $branch['createdby'] = $_SESSION['member']['id'];;
        $Branches->Insert($branch);
        $branchId = $Branches->lastId();
    } else {
        $branch['modifiedby'] = $_SESSION['member']['id'];;
        $branch['dom'] = TIMESTAMP;
        $Branches->update($branch['id'], $branch);
    }

    $_SESSION['message'] = empty($branch['id']) ? 'Branch Added' : 'Branch Updated';
    redirect('branches', 'branch_index');
}

if ($action == 'ajax_getBranches') {
    $data['layout'] = '../layout_blank.tpl.php';
    $icData = $Branches->search($_GET['search']['term']);
    //	$locId = $_GET['locId'];
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

if ($action == 'ajax_saveNewBranch') {
//    debug($_POST);
    $response = [];
    $obj = null;
    if (empty(cleanInput($_POST['name']))) {
        $obj->status = 'error';
        $obj->details = "Name is required";
    } else {
        $name = cleanInput($_POST['name']);
        if ($Branches->searchResults($name)) { //if exists
            $obj->status = 'error';
            $obj->details = "Branch name already exists";
        } else {
            $Branches->insert([
                'name' => $name,
                'createdby' => $_SESSION['member']['id']
            ]);

            $obj->status = 'success';
            $obj->details = "Branch added successfully";
        }
    }

    $response[] = $obj;
    $data['content'] = $response;
}
