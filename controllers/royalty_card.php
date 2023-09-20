<?php

if ($action == 'list') {
    $cards = $RoyaltyCard->cardList();
    $tData['cards'] = $cards;
    $data['content'] = loadTemplate('royaltycards.tpl.php', $tData);

}

if ($action == 'add') {
    // debug($_POST);
    $quantity = $_POST['royalty']['quantity'];

    $companynames = explode(" ", CS_COMPANY);
    foreach ($companynames as $key => $companyname) {
        $companyname_letters[] = str_split($companyname, 1);
        $inial[] = $companyname_letters[$key][0];
        $name = $name . $inial[$key];
    }

    for ($i = 0; $i < $quantity; $i++) {
        $latcardid = $RoyaltyCard->maxid();
        $newcardID = $latcardid['maxID'] + 1;
        $today = cleandash(TODAY);
        // $name = $name."-".TODAY."-".$newcardID;
        $card['name'] = $name . $today . $newcardID;
        $card['status'] = 'active';
        $card['createdby'] = $_SESSION['member']['id'];
        $RoyaltyCard->insert($card);
    }
    // debug($latcardid);
    $_SESSION['message'] = "Cards Generated Successfully";
    redirect('royalty_card', 'list');

}


if ($action == 'ajax_findcard') {
    $icData = $RoyaltyCard->findUnAssigned($_GET['search']['term']);

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
        $obj->text = 'No results';
        $obj->id = 0;
        $response['results'][] = $obj;
    }
    $data['content'] = $response;
}
