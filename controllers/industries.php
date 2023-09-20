<?

if ($action == 'list') {
    $tData['industries'] = $Industries->getAll();
    $data['content'] = loadTemplate('industry_list.tpl.php', $tData);
}

if ($action == 'add_industry') {
    $industry = $_POST['industry'];

    validate($industry);

    if (!$industry['id']) {
        if ($Industries->find(['name' => $industry['name']])) {
            $_SESSION['error'] = "Industry already exists";
            redirectBack();
        }
        $Industries->insert($industry);
    } else {
        $Industries->update($industry['id'], $industry);
    }
    $_SESSION['message'] = "Industry " . ($industry['id'] ? 'updated' : 'created');
    redirectBack();
}
