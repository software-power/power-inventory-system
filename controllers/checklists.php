<?

if($action=='list'){
    $tData['checklists'] = $Checklists->getAll();
    $data['content'] = loadTemplate('checklists.tpl.php',$tData);
}
