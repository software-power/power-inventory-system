<?
if ($action == 'list') {
    Users::isAllowed();
    $tData['accounts'] = $ElectronicAccounts->getAll();
    $data['content'] = loadTemplate('electronic_account_list.tpl.php', $tData);
}

if ($action == 'save') {
    $account = $_POST['account'];
    validate($account);

    try {
        if ($account['id']) {
            // editing
            $account['modifiedby'] = $_SESSION['member']['id'];
            $account['dom'] = TIMESTAMP;
            $ElectronicAccounts->update($account['id'], $account);
        } else {
            if ($ElectronicAccounts->find(['name' => $account['name']])) throw new Exception('Bank Already Exists');
            $account['createdby'] = $_SESSION['member']['id'];;
            $ElectronicAccounts->Insert($account);
        }


        $_SESSION['message'] = "Account " . ($account['id'] ? 'updated' : 'added');
         redirect('electronic_accounts', 'list');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 3000;
        redirectBack();
    }
}