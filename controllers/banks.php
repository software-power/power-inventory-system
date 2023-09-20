<?
if ($action == 'list') {
    Users::isAllowed();
    $tData['banks'] = $Banks->getAll();
    $data['content'] = loadTemplate('bank_list.tpl.php', $tData);
}

if ($action == 'bank_save') {
    $bank = $_POST['bank'];
    validate($bank);

    try {
        if ($bank['id']) {
            $old_bank = $Banks->get($bank['id']);
            // editing
            $bank['modifiedby'] = $_SESSION['member']['id'];
            $bank['dom'] = TIMESTAMP;
            $Banks->update($bank['id'], $bank);
        } else {
            if ($Banks->find(['name' => $bank['name']])) throw new Exception('Bank Already Exists');
            $bank['createdby'] = $_SESSION['member']['id'];;
            $Banks->Insert($bank);
        }


        $_SESSION['message'] = "Bank " . ($bank['id'] ? 'updated' : 'added');
         redirect('banks', 'list');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 3000;
        redirectBack();
    }
}