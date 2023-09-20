<?php
if ($action == 'ajax_saveNewDoctor') {
//    debug($_POST);
    $response = [];
    $obj = null;
    if (empty(cleanInput($_POST['name']))) {
        $obj->status = 'error';
        $obj->details = "Name is required";
    } else {
        $name = cleanInput($_POST['name']);
        if ($Doctors->searchResults($name)) { //if exists
            $obj->status = 'error';
            $obj->details = "Doctor's name already exists";
        } else {
            $Doctors->insert([
                'name' => $name,
                'createdby' => $_SESSION['member']['id']
            ]);

            $obj->status = 'success';
            $obj->details = "Doctor added successfully";
        }
    }

    $response[] = $obj;
    $data['content'] = $response;
}

if ( $action == 'ajax_getDoctors' ) {
//    debug($_GET);
    $icData = $Doctors->searchResults($_GET['search']['term']);

    $response = array();
    if ($icData) {
        foreach ((array)$icData as $ic) {
            $obj=null;
            $obj->text=$ic['name'];
            $obj->id=$ic['id'];
            $response['results'][]=$obj;
        }
    }else {
        $obj=null;
        $obj->text='No results';
        $obj->id=0;
        $response['results'][]=$obj;
    }
    $data['content']=$response;
}
