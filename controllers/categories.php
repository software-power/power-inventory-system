<?php
if ($action == 'category_list') {
    Users::isAllowed();
    $tData['categoryList'] = $Categories->getList();
    $tData['taxcodes'] = $TaxCode->getAll();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Tax Categories";
    $data['content'] = loadTemplate('category_list.tpl.php', $tData);
}

if ($action == 'category_save') {
//    debug($_POST);
    $category = $_POST['category'];

    //validate
    validate($category);

    if ($category['id']) {
        //Edit
        $category['modifiedby'] = $_SESSION['member']['id'];
        $Categories->update($category['id'], $category);
        $_SESSION['message'] = 'category Edited successfully';
    } else {
        //New
        $exists = $Categories->find(['name' => $category['name']]);
        if ($exists) {
            $_SESSION['error'] = 'Category Already Exists';
            redirect('categories', 'category_list');
        } else {
            $category['createdby'] = $_SESSION['member']['id'];
            $Categories->insert($category);
            $_SESSION['message'] = 'New category created successfully';
        }
    }
    redirect('categories', 'category_list');
}

if ($action == 'delete_category') {
    Users::isAllowed();
    $categoryid = removeSpecialCharacters($_POST['id']);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $category = Categories::$categoryClass->get($categoryid);
        if (!$category) throw new Exception("Category not found!");
        if (Products::$productClass->countWhere(['categoryid' => $categoryid]) > 0)
            throw new Exception("Category cant be deleted because some of the product still use this category");


        Categories::$categoryClass->deleteWhere(['id' => $categoryid]);
        mysqli_commit($db_connection);
        $_SESSION['message'] = "Category deleted";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}