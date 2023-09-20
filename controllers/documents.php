<?

if ($action == 'list') {
    $tData['documents'] = Documents::$staticClass->getAll();
    $data['content'] = loadTemplate('document_list.tpl.php', $tData);
}