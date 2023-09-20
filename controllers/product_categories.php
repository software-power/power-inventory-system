<?
if ($action == 'index') {
    Users::isAllowed();
    $tData['categories'] = $ProductCategories->getAllActive();
    $data['content'] = loadTemplate('product_category_list.tpl.php', $tData);
}

if ($action == 'add_category') {
//    debug($_POST);
    $category = $_POST['category'];
    validate($category);
    if (empty($category['id'])) {
        $category['createdby'] = $_SESSION['member']['id'];
        $ProductCategories->insert($category);
        $_SESSION['message'] = 'Category added successfully';
    } else {
        $category['modifiedby'] = $_SESSION['member']['id'];
        $ProductCategories->update($category['id'], $category);
        $_SESSION['message'] = 'Category updated successfully';
    }
    redirect('product_categories', 'index');
}

if ($action == 'delete_category') {
    if (empty($_POST['id'])) redirect('product_categories', 'index');
    $ProductCategories->delete($_POST['id']);
    redirect('product_categories', 'index');
}

if ($action == 'subcategories') {
    Users::isAllowed();
    $tData['categories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllSubcategories();
    $data['content'] = loadTemplate('product_subcategory_list.tpl.php', $tData);
}

if ($action == 'add_subcategory') {
//    debug($_POST);
    $subcategory = $_POST['subcategory'];
    validate($subcategory);
    if (empty($subcategory['id'])) {
        $subcategory['createdby'] = $_SESSION['member']['id'];
        $ProductSubCategories->insert($subcategory);
        $_SESSION['message'] = 'Subcategory added successfully';
    } else {
        $subcategory['modifiedby'] = $_SESSION['member']['id'];
        $ProductSubCategories->update($subcategory['id'], $subcategory);
        $_SESSION['message'] = 'Subcategory updated successfully';
    }
    redirect('product_categories', 'subcategories');
}

if ($action == 'delete_subcategory') {
    if (empty($_POST['id'])) redirect('product_categories', 'subcategories');
    $ProductSubCategories->delete($_POST['id']);
    redirect('product_categories', 'subcategories');
}


if ($action == 'ajax_getCategories') {
    $icData = $ProductCategories->search($_GET['search']['term']);
//    debug($icData);
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

if ($action == 'ajax_saveNewCategory') {
    $response = [];
    $obj = null;
    if (empty(cleanInput($_POST['name']))) {
        $obj->status = 'error';
        $obj->details = "Name is required";
    } else {
        $name = cleanInput($_POST['name']);
        if ($ProductCategories->search($name)) { //if exists
            $obj->status = 'error';
            $obj->details = "Category name already exists";
        } else {
            $ProductCategories->insert([
                'name' => $name,
                'createdby' => $_SESSION['member']['id']
            ]);

            $obj->status = 'success';
            $obj->details = "Category added successfully";
        }
    }

    $response[] = $obj;
    $data['content'] = $response;
}


if ($action == 'ajax_getSubcategories') {
    $categoryId = $_GET['categoryId'];
    $subcategories = $ProductSubCategories->getAllSubcategories($categoryId);
    $response[] = $subcategories;

    $data['content'] = $response;
}


if ($action == 'ajax_getSelectSubcategories') {
    $icData = $ProductSubCategories->search($_GET['search']['term']);
//    debug($icData);
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