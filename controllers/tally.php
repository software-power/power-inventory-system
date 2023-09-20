<?

if ($action == 'tally_transfers') {
    $voucher_type = $_GET['voucher_type'];
    $reference = $_GET['reference'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];

    $title = [];
    if ($voucher_type) $title[] = "Voucher Type: " . $voucher_type;
    if ($reference) $title[] = "Reference: " . $reference;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $tData['transfers'] = $TallyTransfers->getList($voucher_type, $reference, $fromdate, $todate, true);
//    debug($tData['transfers']);
    # code...
    $data['content'] = loadTemplate('tally_transfers.tpl.php', $tData);
}

if ($action == 'pending_transfers') {
    $transfers = $TallyTransfers->pendingTransfer();

    foreach ($transfers as $index => $item) {
        switch ($item['sourcetable']) {
            case 'sales':
                $transfers[$index]['url'] = url('sales', 'view_invoice', ['salesid' => $item['sourceid']]);
                $transfers[$index]['tally_url'] = url('sales', 'post_tally', ['salesid' => $item['sourceid']]);
                break;
            case 'advance_payments':
                $transfers[$index]['url'] = url('advance_payments', 'list', ['apid' => $item['sourceid']]);
                $transfers[$index]['tally_url'] = url('advance_payments', 'tally_post', ['receiptno' => $item['sourceid']]);
                break;
            case 'salespayments':
                $transfers[$index]['url'] = url('reports', 'sales_payment_sr', ['receiptno' => $item['sourceid']]);
                $transfers[$index]['tally_url'] = url('salesreceipts', 'post_tally', ['receiptno' => $item['sourceid']]);
                break;
            case 'expenses':
                $transfers[$index]['url'] = url('expenses', 'issued_list', ['expenseid' => $item['sourceid']]);
                $transfers[$index]['tally_url'] = url('expenses', 'post_tally', ['expenseno' => $item['sourceid']]);
                break;
            case 'sales_returns':
                $transfers[$index]['url'] = url('sales_returns', 'view', ['returnno' => $item['sourceid']]);
                $transfers[$index]['tally_url'] = url('sales_returns', 'post_tally', ['returnno' => $item['sourceid']]);
                break;
            case 'grn':
                $transfers[$index]['url'] = url('grns', 'view_grn', ['grn' => $item['sourceid']]);
                $transfers[$index]['tally_url'] = url('grns', 'tally_post', ['grnno' => $item['sourceid']]);
                break;
            default:
        }
    }
    $tData['pending_transfers'] = $transfers;
    $data['content'] = loadTemplate('pending_tally_transfers.tpl.php', $tData);

}

if ($action == 'post_all') {
    $transfers = $TallyTransfers->pendingTransfer();
    try {
        foreach ($transfers as $index => $item) {
            $ping = pingTally();
            if ($ping['status'] == 'error') throw new Exception($ping['msg']);
            switch ($item['sourcetable']) {
                case 'sales':
                    Sales::tallyPost($item['sourceid']);
                    break;
                case 'advance_payments':
                    AdvancePayments::tallyPost($item['sourceid']);
                    break;
                case 'salespayments':
                    SalesPayments::tallyPost($item['sourceid']);
                    break;
                case 'expenses':
                    Expenses::tallyPost($item['sourceid']);
                    break;
                case 'sales_returns':
                    SalesReturns::tallyPost($item['sourceid']);
                    break;
                case 'grn':
                    GRN::tallyPost($item['sourceid']);
                    break;
                default:
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    redirectBack();

}
