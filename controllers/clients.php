<?

if ($action == 'client_index') {
    Users::isAllowed();
    $start_char = $_GET['start_char'];
    $search = $_GET['search'];

    $start_char = Users::can(OtherRights::add_client) || Users::can(OtherRights::edit_client) ? $start_char : '';
    $tData['start_char'] = $start_char;
    $tData['search'] = $search;
    if ($start_char || $search) $tData['client'] = $Clients->withRoyaltyCardInfo("", $search, false, $start_char);
    $data['content'] = loadTemplate('client_list.tpl.php', $tData);
}

if ($action == 'client_edit') {
    if (Users::cannot(OtherRights::edit_client) && Users::cannot(OtherRights::update_client_contact)) {
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('edit client')]);
    }

    $id = $_GET['id'];
    $tData['name'] = $_GET['name'];
    $client = $Clients->get($id);
    $client['account_manager'] = $Users->get($client['acc_mng'])['name'];
    $tData['client'] = $client;
    $tData['contacts'] = $Contacts->find($arrayName = array('clientid' => $id));
    $tData['edit'] = 1;
    // debug($tData);
    $action = 'client_add';
}

if ($action == 'client_add') {
    if (!CS_MAIN_SYSTEM) {
        $_SESSION['error'] = "This is a subsystem client can only be added/edited in main system!";
        $_SESSION['delay'] = 5000;
        redirectBack();
    }
    if (Users::cannot(OtherRights::add_client) && $tData['edit'] != 1) {
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('add client')]);
    }
    $data['content'] = loadTemplate('client_edit.tpl.php', $tData);
}

if ($action == 'assign_card') {
    Users::can(OtherRights::assign_royalty_card, true);
    if (empty($_POST['card'])) {
        $_SESSION['error'] = 'Invalid card info';
        redirect('clients', 'client_index');
    }
    $card = $_POST['card'];
    $card['assignby'] = $_SESSION['member']['id'];
    $card['assign_date'] = TIMESTAMP;

    //check if client already have card
    if (!empty($RoyaltyCard->find(['clientid' => $card['clientid']]))) {
        $_SESSION['error'] = 'Client already have a card';
        redirectBack();
        die();
    }
    //check if cash client
    if ($card['clientid'] == 1) {
        $_SESSION['error'] = 'This Client cant have a card';
        redirectBack();
        die();
    }

    $RoyaltyCard->update($card['id'], $card);
    $_SESSION['message'] = 'Card assigned successfully';
    redirect('clients', 'client_index');
}

if ($action == 'client_save' || $action == 'quick_add') {
    $_SESSION['delay'] = 5000;
    if (!CS_MAIN_SYSTEM) {
        $_SESSION['error'] = "This is a subsystem, clients can only be added/edited in main system!";
        redirect('clients', 'client_index');
    }
    $client = $_POST['client'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $position = $_POST['position'];
    $for_support = isset($_POST['for_support']); //if client already exists in support


    //validate
    validate($client);

    $client['credit_limit'] = removeComma($client['credit_limit']);

    try {
        if (isset($client['reseller']) && Users::cannot(OtherRights::make_client_reseller)) throw new Exception("Cant make client reseller, Permission denied");
        if (!$client['id']) {
            $client['doc'] = TIMESTAMP;
            $client['createdby'] = $_SESSION['member']['id'];

            if ($Clients->find(['name' => $client['name']])) throw new Exception("Client already exists");
            $Clients->Insert($client);
            $clientid = $Clients->lastId();
        } else {
            $clientid = $client['id'];
            if (Users::cannot(OtherRights::add_client) && Users::cannot(OtherRights::edit_client)) unset($client['name'], $client['tally_name'], $client['credit_limit']);
            $old_client = $Clients->get($clientid);
            $client['modifiedby'] = $_SESSION['member']['id'];;
            $client['dom'] = TIMESTAMP;
            $Clients->update($client['id'], $client);

            //clear old contacts
            $Contacts->deleteWhere(['clientid' => $clientid]);
        }

        foreach ($fullname as $key => $name) {
            $Contacts->insert([
                'name' => $name,
                'email' => $email[$key],
                'mobile' => $mobile[$key],
                'position' => $position[$key],
                'clientid' => $clientid,
                'createdby' => $_SESSION['member']['id'],
                'status' => 'active',
            ]);
        }

        if (CS_TALLY_TRANSFER) {
            $tally_name = $client['name'];
            if (CS_DIFF_CLIENT_LEDGERNAME) $tally_name = $client['tally_name'] ?: $tally_name;

            $tally_result = createTallyLedger(TallyGroups::SUNDRY_DEBTORS, $tally_name, $old_client['ledgername']);
            if ($tally_result['status'] == 'success') {
                $Clients->update($client['id'], ['ledgername' => $tally_name]);
                $tally_message = $tally_result['msg'];
            } else {
                $_SESSION['error'] = $tally_result['msg'];
            }
        }
        $support_mapping = [];
        if (CS_MULTI_SYSTEM && CS_MAIN_SYSTEM) {
            $result = Clients::postToSubSystem($clientid);

            $support_mapping[] = [ //from main system
                'clientcode' => $clientid,
                'support_name' => CS_SUPPORT_NAME,
            ];
            foreach ($result['responses'] as $r) {
                if ($r['status'] === 'success') {
                    $support_mapping[] = $r['support_data'];
                    $support_message .= $r['msg'] . "\n\n";
                } else {
                    $_SESSION['error'] .= $r['msg'] . "\n\n";
                }
            }
        }

        //support api
        if (CS_SUPPORT_INTEGRATION && !$for_support) {
            $support_result = Clients::postToSupport($clientid,$support_mapping);
            if ($support_result['status'] == 'success') {
                $support_message = $support_result['msg'];
            } else {
                $_SESSION['error'] .= "\n Support: " . $support_result['msg'];
            }
        }

        $_SESSION['message'] = "Client " . ($client['id'] ? 'updated' : 'added') . " \n $tally_message \n $support_message";
        if ($for_support) $_SESSION['clientcode'] = $clientid; //for poping up a modal

        $action == 'quick_add' || $_SESSION['REMOTE_ACCESS'] ? redirectBack() : redirect('clients', 'client_index');
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        redirectBack();
    }
}

if ($action == 'attach_document') {
//    debug([$_POST, $_FILES]);
    $clientid = removeSpecialCharacters($_POST['clientid']);
    $docids = $_POST['docid']; //document master id
    $sdocid = $_POST['sdocid']; //existing doc id
    $document_action = $_POST['document_action'];
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $client = Clients::$clientClass->get($clientid);
        if (!$client) throw new Exception("Client not found");
        $file_to_remove = [];
        foreach ($docids as $index => $docid) {
            if ($document_action[$index] == 'new') {
                if ($sdocid[$index]) { //had document
                    $documentid = $sdocid[$index];
                    $doc = ClientsDocuments::$staticClass->get($sdocid[$index]);
                    if ($doc && file_exists($doc['path'])) unlink($doc['path']);
                } else {
                    ClientsDocuments::$staticClass->insert([
                        'clientid' => $clientid,
                        'docid' => $docid,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    $documentid = ClientsDocuments::$staticClass->lastId();
                }
                $file = $_FILES["file$docid"];
                $allowed_files = ['jpg', 'jpeg', 'png', 'pdf'];
                if ($file['error']) throw new Exception("Invalid file");
                $target_dir = "documents/clients/$clientid/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
                $target_file = $target_dir . $docid . "_" . str_replace([' ', '-'], '_', basename($file["name"]));
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                if (!in_array($file_type, $allowed_files)) throw new Exception("Allowed files extensions are " . implode(',', $allowed_files) . " only");
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    ClientsDocuments::$staticClass->update($documentid, ['path' => $target_file]);
                } else {
                    throw new Exception("Failed to upload the file");
                }
            }
            if ($document_action[$index] == 'remove') {
                if ($sdocid[$index]) { //had document
                    $doc = ClientsDocuments::$staticClass->get($sdocid[$index]);
                    if ($doc && file_exists($doc['path'])) unlink($doc['path']);
                    ClientsDocuments::$staticClass->deleteWhere(['id' => $sdocid[$index]]);
                }
            }

        }

        mysqli_commit($db_connection);
        $_SESSION['message'] = "Document saved";
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
    }
    redirectBack();
}

if ($action == 'update_tally_ledger') {
    $clientid = $_GET['clientid'];

    $client = $Clients->get($clientid);

    $tally_name = $client['name'];
    if (CS_DIFF_CLIENT_LEDGERNAME) $tally_name = $client['tally_name'] ?: $tally_name;

    $tally_result = createTallyLedger(TallyGroups::SUNDRY_DEBTORS, $tally_name, $client['ledgername']);
    if ($tally_result['status'] == 'success') {
        $Clients->update($client['id'], ['ledgername' => $tally_name]);
        $_SESSION['message'] = $tally_result['msg'];
    } else {
        $_SESSION['error'] = $tally_result['msg'];
    }

    redirectBack();
}

if ($action == 'client_delete') {
    $id = intval($_GET['id']);
    $Clients->delete($id);
    redirect('clients', 'client_index');
}

if ($action == 'post_support') {
    $clientid = $_GET['clientid'];
    $result = Clients::postToSupport($clientid);
    if ($result['status'] == 'success') {
        $_SESSION['message'] = $result['msg'];
    } else {
        $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}

if ($action == 'post_to_subsystems') {
    $clientid = $_GET['clientid'];
    $_SESSION['delay'] = 5000;
    $result = Clients::postToSubSystem($clientid);
    if ($result['status'] == 'success') {
        foreach ($result['responses'] as $r) {
            if ($r['status'] == 'success') {
                $_SESSION['message'] .= $r['msg'] . "\n\n";
            } else {
                $_SESSION['error'] .= $r['msg'] . "\n\n";
            }
        }
    } else {
        $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}

if ($action == 'ajax_getClients') {
    $no_default = isset($_GET['no_default']);
    if (!empty($_GET['clientname'])) {

        $icData = $Clients->search(removeSpecialCharacters($_GET['clientname']));//normal search
    } else {
        $icData = $Clients->withRoyaltyCardInfo("", $_GET['search']['term'], $no_default);//select2 searching
    }
    //$locId = $_GET['locId'];
    $response = array();
    if ($icData) {
        foreach ((array)$icData as $client) {
            $obj = null;
            $client['mobile'] = $client['mobile'] ? $client['mobile_country_code'] . $client['mobile'] : "";

            $obj->id = $client['id'];
            $obj->text = $client['name'];
            $obj->mobile = $client['mobile'];
            $obj->email = $client['email'];
            $obj->tel = $client['tel'];
            $obj->address = $client['address'];
            $obj->tinno = $client['tinno'];
            $obj->vatno = $client['vatno'];
            $obj->reseller = $client['reseller'];

            if (!empty($_GET['clientname'])) {
                $response[] = $obj;// for normal search
            } else {
                $response['results'][] = $obj;// for select2 dropdown
            }

        }
    } else {
        $obj = null;
        $obj->test = 'No results';
        $obj->id = 0;

        if (!empty($_GET['clientname'])) {
            $response[] = $obj;// for normal search
        } else {
            $response['results'][] = $obj;// for select2 dropdown
        }

    }

    $data['content'] = $response;
}

if ($action == 'ajax_getClientDetails') {
    $id = $_GET['clientId'];
    $client = $Clients->find(['id' => $id, 'status' => 'active'])[0];
    $response = array();
    $obj = null;

    if ($client) {
        $client['mobile'] = $client['mobile'] ? $client['mobile_country_code'] . $client['mobile'] : "";
        $obj->name = $client['name'];
        $obj->id = $client['id'];
        $obj->reseller = $client['reseller'];
        $obj->mobile = $client['mobile'];
        $obj->address = $client['address'];
        $obj->email = $client['email'];
        $obj->tinno = $client['tinno'];
        $obj->vatno = $client['vatno'];
        $obj->telephone = $client['tel'];
        $obj->district = $client['district'];
        $obj->street = $client['street'];
        $obj->plotnumber = $client['plotno'];
        $obj->location = $client['city'];
        $obj->status = $client['status'];
        $obj->contacts = $Contacts->find(['clientid' => $client['id']]);
        $obj->accmanager = $Users->get($client['acc_mng'])['name'] ?: '';
    } else {
        $obj = null;
    }
    $response[] = $obj;
    $data['content'] = $response;
}

if ($action == 'ajax_getClientCardInfo') {
    $id = $_GET['clientId'];
    $info = $Clients->withRoyaltyCardInfo($id);
    $obj = null;
    if (!empty($info)) {
        $obj->found = 'yes';
        $obj->details = $info;
    } else {
        $obj->found = 'no';
    }

    $data['content'] = $obj;
}

if ($action == 'ajax_assignCard') {
    $clientid = $_POST['clientId'];
    $cardid = $_POST['cardId'];

    $obj = null;
    $card = $RoyaltyCard->get($cardid);
    if ($clientid == 1) {
        $obj->status = 'error';
        $obj->message = 'This client cant have a card!';
    } else {
        if (!empty($card['clientid'])) {
            $obj->status = 'error';
            $obj->message = 'Card is used by another client';
        } else {
            if (!empty($RoyaltyCard->find(['clientid' => $clientid]))) {
                $obj->status = 'error';
                $obj->message = 'Client already have card';
            } else {
                $RoyaltyCard->update($cardid, [
                    'clientid' => $clientid,
                    'assignby' => $_SESSION['member']['id'],
                    'assign_date' => TIMESTAMP
                ]);
                $obj->status = 'success';
                $obj->message = 'Card assigned successfully';
            }
        }
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_checkTIN') {
    $tin = $_GET['tin'];
    $clientid = $_GET['clientid'];

    $result['status'] = 'success';
    if ($tin != '999999999') {
        $clients = $Clients->find(['tinno' => $tin]);
        $clients = array_filter($clients, function ($c) use ($clientid) {
            return $c['id'] != $clientid;
        });
        if ($clients) {
            $result['status'] = 'error';
            $result['msg'] = 'TIN number already in use';
        }
    }

    $data['content'] = $result;
}

if ($action == 'ajax_getClientDocuments') {
    $result['status'] = 'success';
    try {
        $clientid = removeSpecialCharacters($_GET['clientid']);
        if (!$clientid) throw new Exception("Invalid client id");
        $result['data'] = ClientsDocuments::$staticClass->getList($clientid);
    } catch (Exception $e) {
        $result['status'] = 'error';
        $result['msg'] = $e->getMessage();
    }
    $data['content'] = $result;
}