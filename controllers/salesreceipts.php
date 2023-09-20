<?

if($action=='post_tally'){
    $receiptno= $_GET['receiptno'];

    $ping = pingTally();
    if ($ping['status'] == 'error') {
        $_SESSION['error'] = $ping['msg'];
    } else {
        $result = SalesPayments::tallyPost($receiptno);
        if ($result['status'] == 'success') $_SESSION['message'] = $result['msg'];
        if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}
