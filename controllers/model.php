<?php
if ($action == 'model_list') {
    Users::isAllowed();
    $tData['model_list'] = $Models->find(array('status' => 'active'));
    $data['content'] = loadTemplate('model_list.tpl.php', $tData);
}


if ($action == 'search') {
    Users::isAllowed();
    $tData['model_list'] = $Models->search($_POST['name']);
    $data['content'] = loadTemplate('model_list.tpl.php', $tData);
}


if ($action == 'model_edit') {
    Users::isAllowed();
    $action = 'model_add';
    $tData['edit'] = 1;
    $tData['model'] = $Models->get($_GET['id']);
}

if ($action == 'model_add') {
    Users::isAllowed();
    $data['content'] = loadTemplate('model_edit.tpl.php', $tData);
}

if ($action == 'model_delete') {
    $Models->delete($_GET['id']);
    $_SESSION['message'] = 'Brand Deleted Succcesfully';
    redirect('model', 'model_list');
    # code...
}

if ($action == 'model_save') {
    $id = $_POST['id'];
    $model = $_POST['model'];

    validate($model);
    if ($id) {
        //Edit
        $model['modifiedby'] = $_SESSION['member']['id'];
        $Models->update($id, $model);
        $_SESSION['message'] = 'Mdel Edited successfully';
    } else {

        $exists = $Models->find(array('name' => $_POST['model']['name'], 'status' => "active"));
        if (empty($exists)) {
            //New
            $model['createdby'] = $_SESSION['member']['id'];
            $Models->insert($model);
            $lastId = $Models->lastId();
            $_SESSION['message'] = 'New Model was created successfully';
        } else {
            $_SESSION['error'] = 'Brand Already Exists';
            redirect('model', 'model_list');
        }
    }
    redirect('model', 'model_list');
}

if ($action == 'ajax_getModels') {
    $data['layout'] = '../layout_blank.tpl.php';
    $icData = $Models->search($_GET['search']['term']);
//  debug($icData);
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


if ($action == 'ajax_saveNewModel') {
    $response = [];
    $obj = null;
    if (empty(cleanInput($_POST['name']))) {
        $obj->status = 'error';
        $obj->details = "Name is required";
    } else {
        $name = cleanInput($_POST['name']);
        if ($Models->search($name)) { //if exists
            $obj->status = 'error';
            $obj->details = "Brand name already exists";
        } else {
            $Models->insert([
                'name' => $name,
                'createdby' => $_SESSION['member']['id']
            ]);

            $obj->status = 'success';
            $obj->details = "Brand added successfully";
        }
    }

    $response[] = $obj;
    $data['content'] = $response;
}
