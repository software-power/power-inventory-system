<?

if ($action == 'department_index') {
    Users::isAllowed();
    $tData['department'] = $Departments->getAll();
    $data['content'] = loadTemplate('department_list.tpl.php', $tData);
}

if ($action == 'department_save') {
//    debug($_POST);
    $department = $_POST['depart'];

    validate($department);

    if (!$department['id']) {//new
        $exists = $Departments->find(['name' => $department['name']]);
        if (empty($exists)) {
            $department['doc'] = TIMESTAMP;
            $department['createdby'] = $_SESSION['member']['id'];;
            $Departments->Insert($department);
            $departmentId = $Departments->lastId();
        } else {
            $_SESSION['error'] = 'Department Already Exists';
            redirect('departments', 'department_index');
        }
    } else {
        $department['modifiedby'] = $_SESSION['member']['id'];;
        $department['dom'] = TIMESTAMP;
        $Departments->update($department['id'], $department);
    }

    $_SESSION['message'] = "Department " . $department['id'] ? 'updated' : 'saved';
    redirect('departments', 'department_index');
}

if ($action == 'ajax_getDepartments') {
    $data['layout'] = '../layout_blank.tpl.php';
    $icData = $Departments->search($_GET['search']['term']);
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
