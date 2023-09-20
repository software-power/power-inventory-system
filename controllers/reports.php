<?

if ($action == 'master_report' || $action == 'ceo_report') {
    Users::isAllowed();
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $currencyid = $_GET['currencyid'];
    if (!$fromdate) $fromdate = TODAY;
    if (!$todate) $todate = TODAY;

    $title = [];
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    if ($branchid) {
        $branch = $Branches->get($branchid);
        $tData['currentBranch'] = $branch;
        $title[] = "Branch: " . $branch['name'];
    } else {
        $title[] = "All branches";
    }

    if ($locationid) {
        $location = $Locations->get($locationid);
        $tData['location'] = $location;
        $title[] = "Location: " . $location['name'];
    }
    if ($currencyid) {
        $currency = $Currencies->get($currencyid);
        $tData['selectedCurrency'] = $currency;
        $title[] = "Currency: " . $currency['name'] . " - " . $currency['description'];
    }
    $title[] = "From: " . fDate($fromdate);
    $title[] = "To: " . fDate($todate);
    $tData['title'] = implode(' | ', $title);
    //sales

    $baseCurrency = $CurrenciesRates->getBaseCurrency();

    $sales = $Sales->simpleList($fromdate, $todate, $locationid, $branchid, $currencyid);

    $salesPaymentType = [];
    foreach ($sales as $index => $sale) {
        $salesPaymentType['currencies'][$sale['currencyname']] = $sale['currencyname'];
        $salesPaymentType['type'][$sale['paymenttype']]['amounts']['sale_amount'][$sale['currencyname']] += $sale['full_amount'];
        if ($sale['outstanding_amount'] > 0)
            $salesPaymentType['type'][$sale['paymenttype']]['amounts']['outstanding_amount'][$sale['currencyname']] += $sale['outstanding_amount'];
        $salesPaymentType['type'][$sale['paymenttype']]['count']++;
    }
//    debug($salesPaymentType);

    $creditNotes = [];
    $returns = $SalesReturns->getList('', '', '', '', 'approved', '', '', $fromdate, $todate, $locationid, $branchid, '', $currencyid);
    foreach ($returns as $sr) {
        $creditNotes['currencies'][$sr['currencyname']] = $sr['currencyname'];
        if (fDate($sr['invoice_date'], 'Y-m-d') == TODAY) {
            $creditNotes['today'][$sr['currencyname']] += $sr['total_incamount'];
            $creditNotes['today']['count']++;
        } else {
            $creditNotes['previous'][$sr['currencyname']] += $sr['total_incamount'];
            $creditNotes['previous']['count']++;
        }

    }
//    debug($creditNotes);


    $payments = $Salespayments->withSaleDetails('', '', '', '', '',
        PaymentMethods::CASH, $fromdate, $todate, $currencyid, $locationid, $branchid,
        '', '', '', SalesPayments::SOURCE_DIRECT);
//    debug($payments);
    $receivedCash = [];
    foreach ($payments as $index => $p) {
        $receivedCash[$p['currencyname']]['users'][$p['creator']][$p['source']] += $p['amount'];
    }
//    debug($receivedCash);

    // fetch advance payment received per user
    $advances = $AdvancePayments->paymentList('', '', $fromdate, $todate, PaymentMethods::CASH, $branchid, '', $currencyid);
//    debug($advances);
    foreach ($advances as $a) {
        $receivedCash[$a['currencyname']]['users'][$a['creator']]['advance_payment'] += $a['amount'];
        $receivedCash[$a['currencyname']]['users'][$a['creator']]['debtor_collection'] += $a['amount'];
    }

    //fetch debtors collection => payment made for outstanding credit invoices
    $payments = $Salespayments->getSalesPayment('', '', '', $fromdate, $todate, PaymentMethods::CASH,
        SalesPayments::SOURCE_RECEIPT, true, '', '', $currencyid);
//    debug($payments);
    foreach ($payments as $index => $p) {
        if ($p['received_amount'] > 0) $receivedCash[$p['currencyname']]['users'][$p['creator']]['credit_payment'] += $p['received_amount'];
        if ($p['received_amount'] > 0) $receivedCash[$p['currencyname']]['users'][$p['creator']]['debtor_collection'] += $p['received_amount'];
    }


    //fetch expense made per user
    $expense = $Expenses->issuedList('', '', '', 'approved', '', $fromdate, $todate, $branchid, true, $currencyid);
    foreach ($expense as $e) {
        $receivedCash[$e['currencyname']]['users'][$e['username']]['expense'] += $e['total_amount'];
    }

    //fetch return made per user
    $salesreturns = $SalesReturns->getList('', '', '', '', 'approved', '', '',
        $fromdate, $todate, '', $branchid, PaymentMethods::CASH, $currencyid);
    foreach ($salesreturns as $sr) {
        if ($sr['invoice_source'] == Sales::SOURCE_QUICK)
            $receivedCash[$sr['currencyname']]['users'][$sr['issuedby']]['quick_return'] += $sr['return_amount'];
        if ($sr['invoice_source'] == Sales::SOURCE_DETAILED)
            $receivedCash[$sr['currencyname']]['users'][$sr['issuedby']]['detailed_return'] += $sr['return_amount'];
    }
//    debug($receivedCash);


    $totals = [];
    foreach ($receivedCash as $currencyname => $details) {
        foreach ($details['users'] as $username => $user) {
//        debug($username);
            $total_quick = $user['quick'] - $user['quick_return'];
            $total_detailed = $user['detailed'] - $user['detailed_return'];
            $receivedCash[$currencyname]['users'][$username]['total_quick'] = $total_quick;
            $receivedCash[$currencyname]['users'][$username]['total_detailed'] = $total_detailed;
            $receivedCash[$currencyname]['users'][$username]['staff_total'] = $total_quick + $total_detailed + $user['debtor_collection'] - $user['expense'];

            $totals[$currencyname]['quick_total'] += $user['quick'];
            $totals[$currencyname]['detailed_total'] += $user['detailed'];
            $totals[$currencyname]['quick_return_total'] += $user['quick_return'];
            $totals[$currencyname]['detailed_return_total'] += $user['detailed_return'];
            $totals[$currencyname]['expense_total'] += $user['expense'];
//            $total[$currencyname]['advance_total'] += $user['advance_payment'];
//            $total[$currencyname]['credit_total'] += $user['credit_payment'];
            $totals[$currencyname]['debtor_total'] += $user['debtor_collection'];
            $totals[$currencyname]['total_quick_total'] += $total_quick;
            $totals[$currencyname]['total_detailed_total'] += $total_detailed;
            $totals[$currencyname]['total_staff_total'] += $total_quick + $total_detailed + $user['debtor_collection'] - $user['expense'];
        }
    }
//    debug($totals);

    //stock values
//    $locationStockValues = Stocks::locationStockValue($todate, $locationid, $branchid);
//debug($locationStockValues);

    //grns date wise
    $grns = $GRN->withPaymentAmount("", "", "", $branchid, $locationid, $fromdate, $todate);
    $grnDetails['purchases'] = array_sum(array_column($grns, 'full_amount'));
    $grnDetails['outstanding_amount'] = array_sum(array_column($grns, 'outstanding_amount'));

    //overall supplier (grn) outstanding
    $grns = $GRN->withPaymentAmount("", "", "", $branchid, $locationid);
    $overallOutstanding[$baseCurrency['name']]['suppliers'] = array_sum(array_column($grns, 'outstanding_amount'));

    //overall client(sales) outstandings
    $with_pending = $Sales->simpleList('', '', $locationid, $branchid, $currencyid, true);
//        debug(array_column($with_pending,'outstanding_amount'));
    foreach ($with_pending as $sale) $overallOutstanding[$sale['currencyname']]['sales'] += $sale['outstanding_amount'];
    //opening outstanding
    $opening_outstandings = $ClientOpeningOutstandings->getList('', '', $currencyid, '', '', '', '', $locationid, $branchid, true);
    foreach ($opening_outstandings as $op) $overallOutstanding[$op['currencyname']]['sales'] += $op['pending_amount'];

//    debug($opening_outstandings);

    //for ceo report
    if ($action == 'ceo_report') {

    }

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['salesPaymentType'] = $salesPaymentType;
    $tData['creditNotes'] = $creditNotes;
    $tData['receivedCash'] = $receivedCash;
    $tData['totals'] = $totals;
    $tData['locationStockValues'] = $locationStockValues;
    $tData['totalStockValues'] = array_sum(array_column($locationStockValues, 'stockvalue'));
    $tData['grnDetails'] = $grnDetails;
    $tData['overallOutstanding'] = $overallOutstanding;


//    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['currencies'] = $Currencies->getAllActive();
    $tData['baseCurrency'] = $baseCurrency;

    $data['content'] = loadTemplate('master_report.tpl.php', $tData);
}

if ($action == 'index') {
    Users::isAllowed();
    $tData['fromdate'] = $_GET['fromdate'];
    $tData['todate'] = $_GET['todate'];

    $tData['type'] = $_GET['type'];
    $tData['client'] = $_GET['client'];
    $tData['supportype'] = $_GET['supportype'];
    $tData['user'] = $_GET['user'];
    $tData['brach'] = $_GET['brach'];
    $tData['department'] = $_GET['department'];
    $tData['ticketid'] = $_GET['ticketid'];
    $tData['amcstatus'] = $_GET['amcstatus'];
    $tData['invoicestatus'] = $_GET['invoicestatus'];
    $tData['warrantystatus'] = $_GET['warrantystatus'];

    $brachid = $_GET['brach'];
    $departId = $_GET['department'];
    $clientid = $_GET['client'];
    $userid = $_GET['user'];
    $status = $_GET['type'];
    $serialno = $_GET['serialno'];
    $ticketno = $_GET['ticketid'];
    $supportype = $_GET['supportype'];

    //filter for management
    $amcstatus = $_GET['amcstatus'];
    $invoicestatus = $_GET['invoicestatus'];
    $warrantystatus = $_GET['warrantystatus'];

    if ($tData['fromdate']) {
        $dateInput = explode('/', $tData['fromdate']);
        $fromdate = $dateInput[2] . '-' . $dateInput[1] . '-' . $dateInput[0];

    } else {
        //one month date
        $tData['fromdate'] = date('d/m/Y', strtotime('1 month ago'));
        $fromdate = date('Y-m-d', strtotime('1 month ago'));
    }

    if ($tData['todate']) {
        $dateInput = explode('/', $tData['todate']);
        $todate = $dateInput[2] . '-' . $dateInput[1] . '-' . $dateInput[0];
    } else {
        //today date
        $tData['todate'] = date('d/m/Y');
        $todate = date('Y-m-d');
    }

    $currentuser = $_SESSION['member'];

    //Default for all users admin,normal user
    $tData['braches'] = $Branches->find(array('status' => 'active'));
    $tData['departments'] = $Departments->find(array('status' => 'active'));
    $tData['statuses'] = $Statuses->getAllStatuses();
    $tData['clients'] = $Clients->find(array('status' => 'active'), $sortby = "name");
    $tData['supportypes'] = $Supporttype->find(array('status' => 'active'));

    if ($currentuser['role'] == 'Admin') {
        #ADMIN...
        $tData['users'] = $Users->find(array('status' => 'active'));
        //populating data from the database
        $tData['listData'] = $Tickets->getTicketReports(
            $departId,
            $brachid,
            $status,
            $clientid,
            $userid,
            $serialno,
            $ticketno,
            $supportype,
            $amcstatus,
            $invoicestatus,
            $warrantystatus,
            $fromdate, $todate
        );

    } else if ($currentuser['role'] == 'Master') {
        #MASTER

        if ($currentuser['deptid']) {
            if ($currentuser['head']) {

                $tData['users'] = $Users->find(array(
                    'deptid' => $currentuser['deptid'],
                    'status' => 'active'
                ));
                $selectedBranch = "";

                $tData['listData'] = $Tickets->getTicketReports(
                    $currentuser['deptid'],
                    $brachid,
                    $status,
                    $clientid,
                    $userid,
                    $serialno,
                    $ticketno,
                    $supportype,
                    $fromdate, $todate
                );

            }
        }
        // die();

    } else if ($currentuser['role'] == 'User') {
        // user...
        //echo "user";
        if ($currentuser['branchid']) {

            if ($currentuser['deptid']) {
                #DEPARTMENT

                if ($currentuser['head']) {
                    #HOD
                    //print_r($currentuser);
                    $tData['users'] = $Users->find(array(
                        'deptid' => $currentuser['deptid'],
                        'branchid' => $currentuser['branchid'],
                        'status' => 'active'
                    ));

                    //Populating data according the department and brach
                    $tData['listData'] = $Tickets->getTicketReports(
                        $currentuser['deptid'],
                        $currentuser['branchid'],
                        $status,
                        $clientid,
                        $userid,
                        $serialno,
                        $ticketno,
                        $supportype,
                        $fromdate, $todate
                    );

                } else {
                    #NORMAL SATFF
                    $tData['users'] = $Users->find(array(
                        'id' => $currentuser['id'],
                        'deptid' => $currentuser['deptid'],
                        'branchid' => $currentuser['branchid'],
                        'status' => 'active'
                    ));
                    //Populating data according the department and brach
                    $tData['listData'] = $Tickets->getTicketReports(
                        $currentuser['deptid'],
                        $currentuser['branchid'],
                        $status,
                        $clientid,
                        $currentuser['id'],
                        $serialno,
                        $ticketno,
                        $supportype,
                        $fromdate, $todate
                    );
                }

            } else {
                #NO DEPARTMENT
                //TODO:what they suppose to see if noe department
                echo "NO department";
            }
        } else {
            #NO BRANCH
            //TODO:what they suppose to see if no branch
            echo "NO branch";
        }

    }

    //echo "<pre>";
    //print_r($tData['listData']);
    //die();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Reports";
    $data['content'] = loadTemplate('report_list.tpl.php', $tData);
}

if ($action == 'contact') {
    Users::isAllowed();
    $clientId = $_GET['clientid'];
    $search_contact = htmlspecialchars($_GET['search_contact']);
    $search_client = htmlspecialchars($_GET['search_client']);

    $title = [];
    if ($search_contact) $title[] = "Search contact: " . $search_contact;
    if ($search_client) $title[] = "Search client: " . $search_client;

    $tData['title'] = implode(' | ', $title);
    if ($clientId || $search_contact || $search_client) $tData['contacts'] = $Clients->getClientContact($clientId, $search_contact, $search_client);

    $_SESSION['pagetitle'] = CS_COMPANY . " - Client Contacts";
    $data['content'] = loadTemplate('contact_report.tpl.php', $tData);
}

if ($action == 'view_report') {
    Users::isAllowed();
    $tab = $_GET['preferred'];
    $period = $_GET['period'];
    $userdetails = $_SESSION[member];

    //weekly wise
    $weektodate = date('Y-m-d');
    $weekfromdate = date('Y-m-d', strtotime('1 week ago'));

    //monthly wise
    $monthtodate = date('Y-m-d');
    $monthfromdate = date('Y-m-d', strtotime('1 month ago'));
    /*
    id |name
    1. |Pending
    3.|completed
    */
    switch ($tab) {
        case 'pending':
            $statusid = 1;

            if ($period == 'week') {
                //echo $tab." ".$period." ".$weektodate." - ".$weekfromdate;
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = "",
                    $weekfromdate, $weektodate);

            } else if ($period == 'month') {
                //echo $tab." ".$period." ".$monthtodate." - ".$monthfromdate;
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = "",
                    $monthfromdate, $monthtodate);
            } else {
                //echo "default";
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = ""
                //$monthfromdate,$monthtodate
                );
            }

            break;
        case 'assigned':
            $statusid = "";

            if ($period == 'week') {
                //echo $tab." ".$period." ".$weektodate." - ".$weekfromdate;
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = "",
                    $weekfromdate, $weektodate);

            } else if ($period == 'month') {
                //echo $tab." ".$period." ".$monthtodate." - ".$monthfromdate;
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = "",
                    $monthfromdate, $monthtodate);

            } else {
                //echo $tab;
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = ""
                //$monthfromdate,$monthtodate
                );
                //die();
            }

            break;
        case 'completed':
            $statusid = 3;

            if ($period == 'week') {
                //echo $tab." ".$period." ".$weektodate." - ".$weekfromdate;
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = "",
                    $weekfromdate, $weektodate);

            } else if ($period == 'month') {
                //echo $tab." ".$period." ".$monthtodate." - ".$monthfromdate;
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = "",
                    $monthfromdate, $monthtodate);
            } else {
                //echo "default";
                $tData['listData'] = $Tickets->getTicketReports(
                    $userdetails['deptid'],
                    $userdetails['branchid'],
                    $statusid,
                    $clientid = "",
                    $userdetails['id'],
                    $serialno = "",
                    $supportype = ""
                //$monthfromdate,$monthtodate
                );
            }

            break;
        case 'admin':
            // departId
            // depart
            // notVerified
            // pending
            // pendingForParts
            $ticketDepartment = $_GET['depId'];
            $statusTicket = $_GET['status'];
            $userdetails['id'] = 0;

            switch ($statusTicket) {
                case 'pending':
                    $tData['listData'] = $Tickets->getTicketReportsAdmin(
                        $ticketDepartment,
                        1,
                        $statusid = 1);
                    break;
                case 'notVerified':
                    $tData['listData'] = $Tickets->getTicketReportsAdmin(
                        $ticketDepartment,
                        1,
                        $statusid = 3);
                    break;
                case 'pendingForParts':
                    $tData['listData'] = $Tickets->getTicketReportsAdmin(
                        $ticketDepartment,
                        1,
                        $statusid = 5);
                    break;

                default:
                    // code...
                    break;
            }

            break;
        default:
            echo "please choose tab";
            break;
    }

    //die();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Report List";
    $data['content'] = loadTemplate('reportview_list.tpl.php', $tData);
}

if ($action == 'full_report') {
    Users::isAllowed();
    $data['layout'] = 'layout_blank.tpl.php';
    $id = $_GET['id'];
    $tdetails = $Ticketdetails->getTicketDet($id);


    foreach ($tdetails as $key => $details) {
        $fulldate = explode('-', $details['date']);
        $new[$details['ticketdetId']]['ticketdetId'] = $details['ticketdetId'];
        $new[$details['ticketdetId']]['date'] = $fulldate[2] . '/' . $fulldate[1] . '/' . $fulldate[0];
        $new[$details['ticketdetId']]['time'] = $details['time'];
        $new[$details['ticketdetId']]['remark'] = $details['remark'];
        $new[$details['ticketdetId']]['name'][] = $details['name'];
    }

    $tData['tecDetails'] = $new;

    $tData['ticketdata'] = $Tickets->getTicketReport($id);
    $_SESSION['pagetitle'] = CS_COMPANY . " - Full Report";
    $data['content'] = loadTemplate('ticketfull_report_list.tpl.php', $tData);
}

if ($action == 'profit_loss') {
    Users::isAllowed();
    debug("<h3>Under development</h3>");
}

if ($action == 'sales_report' || $action == 'sales_report_with_sr' || $action == 'sales_report_with_time' || $action == 'sales_report_sr_with_time') {
    Users::isAllowed();

    if ($action == 'sales_report_with_sr' || $action == 'sales_report_sr_with_time') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }

    $tData['WITH_TIME'] = ($action == 'sales_report_with_time' || $action == 'sales_report_sr_with_time');

//     debug($_GET);
    $clientid = $_GET['clientid'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $paymenttype = $_GET['paymenttype'];
    $currencyid = $_GET['currencyid'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $fromtime = $_GET['fromtime'] ?: '00:00';
    $totime = $_GET['totime'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $createdby = $_SESSION['member']['id'];
    $title = [];

    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($paymenttype) $title[] = "Invoice Type: " . ucfirst($paymenttype);
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($createdby) $title[] = "Sales Person: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate) . " " . $fromtime;
    if ($todate) $title[] = "To: " . fDate($todate) . " " . $totime;

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['fromtime'] = $fromtime;
    $tData['totime'] = $totime;

    $fromdate = "$fromdate $fromtime";
    $todate = $todate && $totime ? "$todate $totime" : "";


    $salesid = "";


    $invoices = Sales::$saleClass->salesList('', $createdby, $fromdate, $todate, $clientid, $locationid,
        $branchid, $paymenttype, !$SR_MODE, '', $currencyid, true);
//    debug($client);
    $totals = [];
    foreach ($invoices as $index => $i) {
        $totals['currency'][$i['currencyname']]['full_amount'] += $i['full_amount'];
        $totals['currency'][$i['currencyname']]['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['currency'][$i['currencyname']]['pending_amount'] += $i['pending_amount'];

        //base
        $totals['base']['full_amount'] += $i['base_full_amount'];
//        $totals['base']['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['base']['pending_amount'] += $i['base_pending_amount'];
    }
//    debug($totals);


    $tData['invoice_list'] = $invoices;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['currencies'] = $Currencies->getAllActive();

    $_SESSION['pagetitle'] = CS_COMPANY . " - Sales Report";
    $data['content'] = loadTemplate('sales_report.tpl.php', $tData);
}

if ($action == 'branch_sales_report' || $action == 'branch_sales_report_sr') {
    Users::isAllowed();

    if ($action == 'branch_sales_report_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }
    // debug($_GET);
    $clientid = $_GET['clientid'];
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $paymenttype = $_GET['paymenttype'];
    $currencyid = $_GET['currencyid'];
    $fromdate = $_GET['fromdate'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $branchid = $_GET['branchid'];

    if (Users::can(OtherRights::approve_other_credit_invoice)) {
        $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    } else {
        $branchid = $_SESSION['member']['branchid'];
        $tData['branches'] = $Branches->find(['id' => $_SESSION['member']['branchid']]);
    }
    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];

    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($paymenttype) $title[] = "Invoice Type: " . ucfirst($paymenttype);
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($createdby) $title[] = "Sales Person: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $salesid = "";


    $invoices = Sales::$saleClass->salesList('', $createdby, $fromdate, $todate, $client, $locationid,
        $branchid, $paymenttype, !$SR_MODE, '', $currencyid, true);
//    debug($client);
    $totals = [];
    foreach ($invoices as $index => $i) {
        $totals['currency'][$i['currencyname']]['full_amount'] += $i['full_amount'];
        $totals['currency'][$i['currencyname']]['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['currency'][$i['currencyname']]['pending_amount'] += $i['pending_amount'];

        //base
        $totals['base']['full_amount'] += $i['base_full_amount'];
//        $totals['base']['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['base']['pending_amount'] += $i['base_pending_amount'];
    }
//    debug($totals);


    $tData['invoice_list'] = $invoices;
    $tData['totals'] = $totals;
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['currencies'] = $Currencies->getAllActive();

    $_SESSION['pagetitle'] = CS_COMPANY . " - Sales Report";
    $data['content'] = loadTemplate('branch_sales_report.tpl.php', $tData);
}

if ($action == 'location_sales_report' || $action == 'location_sales_report_sr') {
    Users::isAllowed();

    if ($action == 'location_sales_report_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }
    // debug($_GET);
    $clientid = $_GET['clientid'];
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $paymenttype = $_GET['paymenttype'];
    $currencyid = $_GET['currencyid'];
    $fromdate = $_GET['fromdate'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $locationid = $_GET['locationid'];

    if (Users::can(OtherRights::approve_other_credit_invoice)) {
        $tData['locations'] = Locations::$locationClass->locationList('', '', '', $_SESSION['member']['locationid']);
    } else {
        $locationid = $_SESSION['member']['locationid'];
        $tData['locations'] = Locations::$locationClass->locationList($_SESSION['member']['locationid']);
    }
    $title = [];
    if ($locationid) $title[] = "Location: " . Locations::$locationClass->get($locationid)['name'];

    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($paymenttype) $title[] = "Invoice Type: " . ucfirst($paymenttype);
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($createdby) $title[] = "Sales Person: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $salesid = "";


    $invoices = Sales::$saleClass->salesList('', $createdby, $fromdate, $todate, $client, $locationid,
        '', $paymenttype, !$SR_MODE, '', $currencyid, true);
//    debug($client);
    $totals = [];
    foreach ($invoices as $index => $i) {
        $totals['currency'][$i['currencyname']]['full_amount'] += $i['full_amount'];
        $totals['currency'][$i['currencyname']]['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['currency'][$i['currencyname']]['pending_amount'] += $i['pending_amount'];

        //base
        $totals['base']['full_amount'] += $i['base_full_amount'];
//        $totals['base']['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['base']['pending_amount'] += $i['base_pending_amount'];
    }
//    debug($totals);


    $tData['invoice_list'] = $invoices;
    $tData['totals'] = $totals;
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['currencies'] = $Currencies->getAllActive();

    $_SESSION['pagetitle'] = CS_COMPANY . " - Sales Report";
    $data['content'] = loadTemplate('location_sales_report.tpl.php', $tData);
}

if ($action == 'department_sales_report' || $action == 'department_sales_report_sr') {
    Users::isAllowed();

    if ($action == 'department_sales_report_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }

//     debug($_GET);
    $clientid = $_GET['clientid'];
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $departmentid = $_GET['departmentid'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $brandid = $_GET['brandid'];

    if (Users::can(OtherRights::approve_other_credit_invoice)) {
        $tData['departments'] = Departments::$deptClass->getAllActive("field(id, {$_SESSION['member']['deptid']}) desc, id");
    } else {
        $departmentid = $_SESSION['member']['deptid'];
        $tData['departments'] = Departments::$deptClass->find(['id' => $_SESSION['member']['deptid']]);
    }
    $title = [];
    if ($locationid) $title[] = "Location: " . Locations::$locationClass->get($locationid)['name'];
    if ($departmentid) $title[] = "Department: " . Departments::$deptClass->get($departmentid)['name'];
    if ($productid) $title[] = "Product: " . Products::$productClass->get($productid)['name'];

    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($createdby) $title[] = "Order/Invoice by: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $salesid = "";


    $salesdetails = Salesdetails::$saleDetailsClass->getDetailedList($productid, $clientid, '', $productcategoryid, $subcategoryid, $brandid,
        $departmentid, $fromdate, $todate, '1', !$SR_MODE, '', $locationid, '', $createdby);
//    debug($salesdetails);

    $tData['salesdetails'] = $salesdetails;
    $tData['totals'] = $totals;
    $tData['productcategories'] = ProductCategories::$class->getAllActive();
    $tData['subcategories'] = ProductSubCategories::$class->getAllActive();
    $tData['brands'] = Models::$staticClass->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Department Sales Report";
    $data['content'] = loadTemplate('department_sales_report.tpl.php', $tData);
}

if ($action == 'support_sales_report' || $action == 'support_sales_report_sr') {
    Users::isAllowed();

    if ($action == 'support_sales_report_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }

//     debug($_GET);
    $clientid = $_GET['clientid'];
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $locationid = $_GET['locationid'];

    if (Users::can(OtherRights::approve_other_credit_invoice)) {
        $tData['locations'] = Locations::$locationClass->locationList('', '', '', $_SESSION['member']['locationid']);
    } else {
        $locationid = $_SESSION['member']['locationid'];
        $tData['locations'] = Locations::$locationClass->locationList($_SESSION['member']['locationid']);
    }
    $title = [];

    if ($locationid) $title[] = "Location: " . Locations::$locationClass->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($createdby) $title[] = "Order/Invoice by: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $salesid = "";


    $salesdetails = Salesdetails::$saleDetailsClass->getDetailedList($productid, $clientid, '', $productcategoryid, $subcategoryid, $brandid,
        $departmentid, $fromdate, $todate, '1', !$SR_MODE, '', $locationid, '', $createdby, 'yes');
//    debug($salesdetails);

    $new_array = [];
    foreach ($salesdetails as $d) {
        $new_array[$d['salesid']]['salesid'] = $d['salesid'];
        $new_array[$d['salesid']]['invoiceno'] = $d['invoiceno'];
        $new_array[$d['salesid']]['orderno'] = $d['orderno'];
        $new_array[$d['salesid']]['supportno'] = $d['foreign_orderid'];
        $new_array[$d['salesid']]['clientid'] = $d['clientid'];
        $new_array[$d['salesid']]['clientname'] = $d['clientname'];
        $new_array[$d['salesid']]['locationid'] = $d['locationid'];
        $new_array[$d['salesid']]['locationname'] = $d['locationname'];
        $new_array[$d['salesid']]['branchid'] = $d['branchid'];
        $new_array[$d['salesid']]['branchname'] = $d['branchname'];
        $new_array[$d['salesid']]['order_creator'] = $d['order_creator'];
        $new_array[$d['salesid']]['salesperson'] = $d['salesperson'];
        $new_array[$d['salesid']]['invoicedate'] = $d['invoicedate'];
        $new_array[$d['salesid']]['currencyname'] = $d['currencyname'];
        $new_array[$d['salesid']]['full_amount'] = $d['full_amount'];
        $new_array[$d['salesid']]['service'] += ($d['sold_non_stock'] ? $d['incamount'] : 0);
        $new_array[$d['salesid']]['spare'] += ($d['sold_non_stock'] ? 0 : $d['incamount']);
        $new_array[$d['salesid']]['items'][] = [
            'name' => $d['productname'],
            'amount' => formatN($d['incamount']),
        ];

    }
//    debug($new_array);

    $tData['salesdetails'] = $new_array;
    $tData['totals'] = $totals;
    $tData['productcategories'] = ProductCategories::$class->getAllActive();
    $tData['subcategories'] = ProductSubCategories::$class->getAllActive();
    $tData['brands'] = Models::$staticClass->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Department Sales Report";
    $data['content'] = loadTemplate('support_sales_report.tpl.php', $tData);
}

if ($action == 'sales_report_sr_only') {
    Users::isAllowed();

    // debug($_GET);
    $clientid = $_GET['clientid'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $paymenttype = $_GET['paymenttype'];
    $currencyid = $_GET['currencyid'];
    $fromdate = $_GET['fromdate'];
    $fromdate = $_GET['fromdate'] ?? TODAY;
    $todate = $_GET['todate'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $createdby = $_SESSION['member']['id'];
    $title = [];
    if ($clientid) {
        $client = $Clients->get($clientid);
        $title[] = "Client: " . $client['name'];
    }

    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($paymenttype) $title[] = "Invoice Type: " . ucfirst($paymenttype);
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($createdby) $title[] = "Sales Person: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $salesid = "";


    $invoices = Sales::$saleClass->salesList('', $createdby, $fromdate, $todate, $client, $locationid,
        $branchid, $paymenttype, '', '', $currencyid, true, '', '', '', true);
//    debug($invoices);
    $totals = [];
    foreach ($invoices as $index => $i) {
        $totals['currency'][$i['currencyname']]['full_amount'] += $i['full_amount'];
        $totals['currency'][$i['currencyname']]['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['currency'][$i['currencyname']]['pending_amount'] += $i['pending_amount'];

        //base
        $totals['base']['full_amount'] += $i['base_full_amount'];
//        $totals['base']['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT)
            $totals['base']['pending_amount'] += $i['base_pending_amount'];
    }
//    debug($totals);


    $tData['invoice_list'] = $invoices;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['currencies'] = $Currencies->getAllActive();

    $_SESSION['pagetitle'] = CS_COMPANY . " - Sales Report SR Only";
    $data['content'] = loadTemplate('sales_report_sr_only.tpl.php', $tData);
}

if ($action == 'sales_by_order_report' || $action == 'sales_by_order_report_sr') {
    Users::isAllowed();

    if ($action == 'sales_by_order_report_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }
    // debug($_GET);
    $clientid = $_GET['clientid'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $createdby = $_GET['createdby'];
    $paymenttype = $_GET['paymenttype'];
    $currencyid = $_GET['currencyid'];
    $fromdate = $_GET['fromdate'];
    $fromdate = $_GET['fromdate'] ?? TODAY;
    $todate = $_GET['todate'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $createdby = $_SESSION['member']['id'];
    $title = [];
    if ($clientid) {
        $client = $Clients->get($clientid);
        $title[] = "Client: " . $client['name'];
    }

    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($paymenttype) $title[] = "Invoice Type: " . ucfirst($paymenttype);
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($createdby) $title[] = "Order & invoice by: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $salesid = "";


    $invoices = Sales::$saleClass->salesList('', '', $fromdate, $todate, $client, $locationid,
        $branchid, $paymenttype, !$SR_MODE, '', $currencyid, true, '', $createdby);
//    debug($invoices);
    $totals = [];
    foreach ($invoices as $index => $i) {
        $totals['currency'][$i['currencyname']]['full_amount'] += $i['full_amount'];
        $totals['currency'][$i['currencyname']]['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['pending_amount'] > 0) $totals['currency'][$i['currencyname']]['pending_amount'] += $i['pending_amount'];

        //base
        $totals['base']['full_amount'] += $i['base_full_amount'];
//        $totals['base']['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['base_pending_amount'] > 0) $totals['base']['pending_amount'] += $i['base_pending_amount'];
    }
//    debug($totals);


    $tData['invoice_list'] = $invoices;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['currencies'] = $Currencies->getAllActive();

    $_SESSION['pagetitle'] = CS_COMPANY . " - Sales By Order Report";
    $data['content'] = loadTemplate('sales_by_order_report.tpl.php', $tData);
}

if ($action == 'salesperson_summary' || $action == 'salesperson_summary_sr') {
    Users::isAllowed();

    if ($action == 'salesperson_summary_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }
    // debug($_GET);
    $createdby = $_GET['createdby'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $createdby = $_SESSION['member']['id'];
    $title = [];
    if ($createdby) $title[] = "Order & invoice by: " . $Users->get($createdby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $invoices = Sales::$saleClass->salesList('', $createdby, $fromdate, $todate, '', '',
        '', '', !$SR_MODE, '', '', true, '');
    $salesperson = [];
    $totals = [];
    $array = [];
    foreach ($invoices as $index => $i) {
        $personid = $i['order_createdby'] ?: $i['createdby'];
        $personname = $i['order_creator'] ?: $i['issuedby'];
        $salesperson[$personid]['createdby'] = $personid;
        $salesperson[$personid]['issuedby'] = $personname;
        $salesperson[$personid]['total_amount'] += $i['base_full_amount'];
        $salesperson[$personid]['paid_amount'] += $i['lastpaid_totalamount'] * $i['currency_amount'];
        if ($i['base_pending_amount'] > 0) $salesperson[$personid]['pending_amount'] += $i['base_pending_amount'];

        $totals['currency'][$i['currencyname']]['full_amount'] += $i['full_amount'];
        $totals['currency'][$i['currencyname']]['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['pending_amount'] > 0) $totals['currency'][$i['currencyname']]['pending_amount'] += $i['pending_amount'];

        //base
        $totals['base']['full_amount'] += $i['base_full_amount'];
//        $totals['base']['paid_amount'] += $i['lastpaid_totalamount'];
        if ($i['base_pending_amount'] > 0) $totals['base']['pending_amount'] += $i['base_pending_amount'];

    }


    $tData['salespersons'] = $salesperson;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['currencies'] = $Currencies->getAllActive();

    $_SESSION['pagetitle'] = CS_COMPANY . " - Salesperson Summary";
    $data['content'] = loadTemplate('salesperson_summary_report.tpl.php', $tData);
}

if ($action == 'sales_by_order_detailed_report_sr') {
    Users::isAllowed();
//     debug($_GET);
    $invoiceno = $_GET['invoiceno'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $locationid = $_GET['locationid'];
    $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    $clientid = $_GET['clientid'];
    $productid = $_GET['productid'];
    $modelid = $_GET['modelid'];
    $depart = $_GET['depart'];
    $category = $_GET['category'];
    $order_invoice_createdby = $_GET['userid'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $order_invoice_createdby = $_SESSION['member']['id'];

    $title = [];
    if ($invoiceno) {
        $title[] = "Invoice No: " . $invoiceno;
        $fromdate = '';
        $branchid = '';
    }
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($modelid) $title[] = "Brand: " . $Models->get($modelid)['name'];
    if ($depart) $title[] = "Department: " . $Departments->get($depart)['name'];
    if ($category) $title[] = "Tax: " . $Categories->get($category)['name'];
    if ($order_invoice_createdby) $title[] = "Order or Invoice by: " . $Users->get($order_invoice_createdby)['name'];

    $tData['title'] = implode(' | ', $title);

    $sales = Sales::$saleClass->salesAuditReport($fromdate, $todate, '', $clientid, $productid,
        $modelid, $depart, $category, $locationid, $branchid, $invoiceno, $tra_invoice_only = false, $non_stock_only = false, $creditapproved = true, $order_invoice_createdby);

//    debug($sales);

//    debug($totals);

    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['sales'] = $sales;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['categories'] = $Categories->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY;
    $data['content'] = loadTemplate('sales_by_order_detailed.tpl.php', $tData);
}

if ($action == 'overall_sales_outstanding' || $action == 'branch_sales_outstanding' || $action == 'account_manager_sales_outstanding' || $action == 'salesperson_sales_outstanding') {
    Users::isAllowed();
    $clientid = removeSpecialCharacters($_GET['clientid']);
    $acc_mng = removeSpecialCharacters($_GET['acc_mng']);
    $order_invoice_createdby = removeSpecialCharacters($_GET['order_invoice_createdby']);
    $print_pdf = isset($_GET['print_pdf']);
    $tData['pdf_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&print_pdf";

    $top_title = "Overall Sales Outstanding";
    if ($action == 'branch_sales_outstanding') {
        $branchid = $_SESSION['member']['branchid'];
        $top_title = "Branch Sales Outstanding";
    }
    if ($action == 'account_manager_sales_outstanding') {
        $acc_mng = $_SESSION['member']['id'];
        $branchid = '';
        $top_title = "Account Manager Sales Outstanding";
    }
    if ($action == 'salesperson_sales_outstanding') {
        $order_invoice_createdby = $_SESSION['member']['id'];
        $branchid = '';
        $top_title = "Salesperson Outstanding";
    }

    $tData['top_title'] = $top_title;
    $title = [];
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($acc_mng) {
        $data['acc_manager'] = $tData['acc_manager'] = $acc_manager = $Users->get($acc_mng);
        $title[] = "Account Manager: " . $acc_manager['name'];
    }
    if ($order_invoice_createdby) $title[] = "Invoice/Order by: " . $Users->get($order_invoice_createdby)['name'];
    if ($clientid) {
        $data['client'] = $tData['client'] = $client = $Clients->get($clientid);
        $title[] = "Client: " . $client['name'];
    }

    $tData['title'] = implode(' | ', $title);
    $list = Sales::$saleClass->getSalesOutstanding("", $clientid, '', true, '', $order_invoice_createdby, $branchid, $acc_mng);
    $opening_outstandings = ClientOpeningOutstandings::$staticClass->getList('', $clientid, '', $order_invoice_createdby,
        '', '', '', '', $branchid, true, $acc_mng);
//    debug($opening_outstandings);

    $total_outstanding = [];
    foreach ($list as $key => $l) {
        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }
    foreach ($opening_outstandings as $key => $l) {
        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }
//    debug($total_outstanding);
    $_SESSION['pagetitle'] = CS_COMPANY . " - $top_title";
    if ($print_pdf) {
        $location = Locations::$locationClass->get($_SESSION['member']['id']);
        $address = $location['address'];
        $address = explode(PHP_EOL, $address);
        if (count($address) > 1) {
            define("LOCATION_ADDRESS", $address);
        }

        $bankids = explode(',', $location['bankids']);
        $banks = Banks::$banksClass->findMany(['id' => $bankids]);
        $data['banks'] = $banks;


        $data['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
        $data['invoice_list'] = $list;
        $data['opening_outstandings'] = $opening_outstandings;
        $data['total_outstanding'] = $total_outstanding;
        $data['layout'] = 'sales_outstanding_print.tpl.php';
    } else {

        $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
        $tData['remarks'] = $InvoiceRemarks->getAllActive();
        $tData['invoice_list'] = $list;
        $tData['opening_outstandings'] = $opening_outstandings;
        $tData['total_outstanding'] = $total_outstanding;
        $data['content'] = loadTemplate('sales_outstanding.tpl.php', $tData);
    }
}

if ($action == 'clients_outstanding_summary') {
    Users::isAllowed();
    $clientid = removeSpecialCharacters($_GET['clientid']);

    $title = [];

    if ($clientid) {
        $data['client'] = $tData['client'] = $client = $Clients->get($clientid);
        $title[] = "Client: " . $client['name'];
    }
    $tData['title'] = implode(' | ', $title);

    $list = Sales::$saleClass->getSalesOutstanding("", $clientid, '', true, '', $order_invoice_createdby, $branchid, $acc_mng);
    $opening_outstandings = ClientOpeningOutstandings::$staticClass->getList('', $clientid, '', $order_invoice_createdby,
        '', '', '', '', $branchid, true, $acc_mng);

    $total_outstanding = [];
    $clients = [];
    foreach ($list as $key => $l) {
        $clients[$l['clientid']]['clientid'] = $l['clientid'];
        $clients[$l['clientid']]['clientname'] = $l['clientname'];
        $clients[$l['clientid']]['account_manager'] = $l['account_manager'];
        $clients[$l['clientid']]['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $clients[$l['clientid']]['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $clients[$l['clientid']]['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $clients[$l['clientid']]['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $clients[$l['clientid']]['(>90 days)'] += $l['base_pending_amount'];


        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }

    foreach ($opening_outstandings as $key => $l) {
        $clients[$l['clientid']]['clientid'] = $l['clientid'];
        $clients[$l['clientid']]['clientname'] = $l['clientname'];
        $clients[$l['clientid']]['account_manager'] = $l['account_manager'];
        $clients[$l['clientid']]['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $clients[$l['clientid']]['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $clients[$l['clientid']]['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $clients[$l['clientid']]['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $clients[$l['clientid']]['(>90 days)'] += $l['base_pending_amount'];


        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }

    $_SESSION['pagetitle'] = CS_COMPANY . " - Client Outstanding Summary";

    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['clients'] = $clients;
    $tData['total_outstanding'] = $total_outstanding;
    $data['content'] = loadTemplate('clients_outstanding_summary.tpl.php', $tData);

}

if ($action == 'staff_outstanding_summary') {
    Users::isAllowed();

    $list = Sales::$saleClass->getSalesOutstanding("", $clientid, '', true, '', $order_invoice_createdby, $branchid, $acc_mng);
    $opening_outstandings = ClientOpeningOutstandings::$staticClass->getList('', $clientid, '', $order_invoice_createdby,
        '', '', '', '', $branchid, true, $acc_mng);

    $total_outstanding = [];
    $staffs = [];
    foreach ($list as $key => $l) {
        $staffs[$l['acc_mng']]['acc_mng'] = $l['acc_mng'];
        $staffs[$l['acc_mng']]['account_manager'] = $l['account_manager'];
        $staffs[$l['acc_mng']]['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $staffs[$l['acc_mng']]['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $staffs[$l['acc_mng']]['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $staffs[$l['acc_mng']]['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $staffs[$l['acc_mng']]['(>90 days)'] += $l['base_pending_amount'];


        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }

    foreach ($opening_outstandings as $key => $l) {
        $staffs[$l['acc_mng']]['acc_mng'] = $l['acc_mng'];
        $staffs[$l['acc_mng']]['account_manager'] = $l['account_manager'];
        $staffs[$l['acc_mng']]['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $staffs[$l['acc_mng']]['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $staffs[$l['acc_mng']]['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $staffs[$l['acc_mng']]['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $staffs[$l['acc_mng']]['(>90 days)'] += $l['base_pending_amount'];


        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }

//        debug($staffs);
    $_SESSION['pagetitle'] = CS_COMPANY . " - Staff Outstanding Summary";

    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['staff'] = $staffs;
    $tData['total_outstanding'] = $total_outstanding;
    $data['content'] = loadTemplate('staff_outstanding_summary.tpl.php', $tData);

}

if ($action == 'salesperson_outstanding_summary') {
    Users::isAllowed();

    $list = Sales::$saleClass->getSalesOutstanding("", $clientid, '', true, '', $order_invoice_createdby, $branchid, $acc_mng);
    $opening_outstandings = ClientOpeningOutstandings::$staticClass->getList('', $clientid, '', $order_invoice_createdby,
        '', '', '', '', $branchid, true, $acc_mng);

//    debug($opening_outstandings);

    $total_outstanding = [];
    $salespersons = [];
    foreach ($list as $key => $l) {
        $salespersons[$l['order_createdby'] ?: $l['invoice_createdby']]['userid'] = $l['order_createdby'] ?: $l['invoice_createdby'];
        $salespersons[$l['order_createdby'] ?: $l['invoice_createdby']]['salesperson'] = $l['order_creator'] ?: $l['acc_manager'];
        $salespersons[$l['order_createdby'] ?: $l['invoice_createdby']]['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $salespersons[$l['order_createdby'] ?: $l['invoice_createdby']]['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $salespersons[$l['order_createdby'] ?: $l['invoice_createdby']]['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $salespersons[$l['order_createdby'] ?: $l['invoice_createdby']]['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $salespersons[$l['order_createdby'] ?: $l['invoice_createdby']]['(>90 days)'] += $l['base_pending_amount'];


        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }
//    debug($salespersons);

    foreach ($opening_outstandings as $key => $l) {
        $salespersons[$l['createdby']]['userid'] = $l['createdby'];
        $salespersons[$l['createdby']]['salesperson'] = $l['issuedby'];
        $salespersons[$l['createdby']]['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $salespersons[$l['createdby']]['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $salespersons[$l['createdby']]['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $salespersons[$l['createdby']]['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $salespersons[$l['createdby']]['(>90 days)'] += $l['base_pending_amount'];


        $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
        $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
        $total_outstanding['base_total'] += $l['base_pending_amount'];
        if ($l['(<30 days)']) $total_outstanding['(<30 days)'] += $l['base_pending_amount'];
        if ($l['(30 to 45 days)']) $total_outstanding['(30 to 45 days)'] += $l['base_pending_amount'];
        if ($l['(45 to 90 days)']) $total_outstanding['(45 to 90 days)'] += $l['base_pending_amount'];
        if ($l['(>90 days)']) $total_outstanding['(>90 days)'] += $l['base_pending_amount'];
    }

//        debug($salespersons);
    $_SESSION['pagetitle'] = CS_COMPANY . " - Salesperson Outstanding Summary";

    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['salespersons'] = $salespersons;
    $tData['total_outstanding'] = $total_outstanding;
    $data['content'] = loadTemplate('salesperson_outstanding_summary.tpl.php', $tData);

}

if ($action == 'installment_outstanding') {
    Users::isAllowed();
    $clientid = removeSpecialCharacters($_GET['clientid']);
    $with_completed = isset($_GET['with_completed']);

    if ($clientid) {
        $data['client'] = $tData['client'] = $client = $Clients->get($clientid);
        $title[] = "Client: " . $client['name'];
    }
    $tData['title'] = implode(' | ', $title);
    $list = Sales::$saleClass->getSalesOutstanding("", $clientid, '', true, '', '', '', '', '', true);

    foreach ($list as $index => $item) {
        $list[$index]['installments'] = SalesInstallmentPlans::$staticClass->withStatus($item['salesid']);
        if (!$with_completed) {
            $list[$index]['installments'] = array_filter($list[$index]['installments'], function ($i) {
                return $i['pending'] > 0;
            });
        }
//       if($item['salesid']==1054) debug($list[$index]['installments']);
    }

    $tData['list'] = $list;
    $data['content'] = loadTemplate('installments_outstanding.tpl.php', $tData);
}

if ($action == 'sales_outstanding_detailed') {
    Users::isAllowed();
    $clientid = $_GET['clientid'];
    $print_pdf = isset($_GET['print_pdf']);
    $tData['pdf_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&print_pdf";

    $_SESSION['pagetitle'] = CS_COMPANY . " - Sales Outstanding Report";
    if (!$clientid) {
        $data['content'] = loadTemplate('sales_outstanding_detailed.tpl.php', $tData);
    } else {
        $order_invoice_createdby = Users::cannot(OtherRights::approve_other_credit_invoice) ? $_SESSION['member']['id'] : '';

        $title = [];
        $data['client'] = $tData['client'] = $client = $Clients->get($clientid);
        $title[] = "Client: " . $client['name'];
        if ($order_invoice_createdby) $title[] = "Order or Invoice by: " . $Users->get($order_invoice_createdby)['name'];

        $tData['title'] = implode(' | ', $title);
        //Users::isAllowed();
        $list = $Sales->getSalesOutstanding("", $clientid, "", true, '', $order_invoice_createdby);
        $opening_outstandings = $ClientOpeningOutstandings->getList('', $clientid, '', $order_invoice_createdby, '', '', '', '', '', true);

        $total_outstanding = [];
        foreach ($list as $key => $l) {
            $list[$key]['details'] = $Sales->getProductListForFiscalize($l['salesid']);
            $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
            $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
            $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
            $total_outstanding['base_total'] += $l['base_pending_amount'];
        }
        foreach ($opening_outstandings as $key => $l) {
            $total_outstanding['currencies'][$l['currencyname']]['amount'] += $l['pending_amount'];
            $total_outstanding['currencies'][$l['currencyname']]['base_amount'] += $l['base_pending_amount'];
            $total_outstanding['currencies'][$l['currencyname']]['base_currency'] = $l['base_currency'];
            $total_outstanding['base_total'] += $l['base_pending_amount'];
        }
//     debug($list);
        if ($print_pdf) {
            $data['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
            $data['invoice_list'] = $list;
            $data['with_details'] = true;
            $data['opening_outstandings'] = $opening_outstandings;
            $data['total_outstanding'] = $total_outstanding;
            $data['layout'] = 'sales_outstanding_print.tpl.php';
        } else {
            $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
            $tData['invoice_list'] = $list;
            $tData['opening_outstandings'] = $opening_outstandings;
            $tData['total_outstanding'] = $total_outstanding;
            $data['content'] = loadTemplate('sales_outstanding_detailed.tpl.php', $tData);
        }
    }
}

if ($action == 'daily_cash' || $action == 'daily_cash_with_sr') {
    Users::isAllowed();
    if ($action == 'daily_cash_with_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }

    $user = $_SESSION['member'];
    $fromdate = $_GET['fromdate'] ?? TODAY;
    $todate = $_GET['todate'];
    $createdby = $_GET['createdby'];
    $currencyid = $_GET['currencyid'];
    $clientid = $_GET['client'];
    $locationid = $_GET['locationid'];
    $branchid = $_GET['branchid'];
    $eaccount = $_GET['eaccount'];
    // debug($tData['fromdate']);

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $createdby = $_SESSION['member']['id'];

    $title = [];

    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($createdby) $title[] = "Sales Person: " . $Users->get($createdby)['name'];
    if ($eaccount) $title[] = "E-Account: " . $ElectronicAccounts->get($eaccount)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;


    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");

    $cashInvoices = $Salespayments->withSaleDetails('', $createdby, '', $clientid, PAYMENT_TYPE_CASH, '',
        $fromdate, $todate, $currencyid, $locationid, $branchid, !$SR_MODE, $eaccount);

    //totals
    $totals = [];
    foreach ($cashInvoices as $index => $p) {
        $totals['currencies'][$p['currencyname']][$p['method']] += $p['amount'];


        $totals['base'][$p['method']] += $p['base_amount'];
    }
//     debug($cashInvoices);


    $tData['invoice_list'] = $cashInvoices;
    $tData['totals'] = $totals;
    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['currencies'] = $Currencies->getAllActive();
    $tData['eaccounts'] = $ElectronicAccounts->getAllActive();
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");

    $_SESSION['pagetitle'] = CS_COMPANY . " - Daily Cash Sales Reports";
    $data['content'] = loadTemplate('daily_cash.tpl.php', $tData);
}

if ($action == 'product_history') {
    Users::isAllowed();
    $tData['fromdate'] = date('Y-m-d', strtotime('-1 months'));
    $tData['todate'] = TODAY;

    $locationid = $_GET['locationid'];
    $productid = $_GET['productid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $batch_no = $_GET['batch_no'];
    $fromdate ? $tData['fromdate'] = $fromdate : $fromdate = $tData['fromdate'];
    $todate ? $tData['todate'] = $todate : $todate = $tData['todate'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$locationid) $locationid = $_SESSION['member']['locationid'];
        $branchLocations = $ALL_BRANCH ? $Locations->locationList() : $Locations->locationList('', $_SESSION['member']['branchid']);
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($locationid, array_column($branchLocations, 'id'))) $locationid = $_SESSION['member']['locationid'];
    } else {
        $locationid = $_SESSION['member']['locationid'];
    }

    if ($locationid && $productid) {
        $tData['location'] = $Locations->get($locationid);
        $product = $Products->get($productid);
        if ($product['non_stock']) {
            $_SESSION['error'] = "No stock report for non-stock items";
            redirect('reports', 'product_history');
        }
        $tData['product'] = $product;
        $title = [];
        $title[] = "Location: " . $tData['location']['name'];
        $title[] = "Product: " . $tData['product']['name'];
        $title[] = "From: " . fDate($fromdate);
        $title[] = "To: " . fDate($todate);
        if ($batch_no) $title[] = "Batch No: " . $batch_no;

        $tData['title'] = implode(' | ', $title);

        $stock_date = date('Y-m-d', strtotime("$fromdate -1 day"));
//        debug($stock_date);
        $stock = Stocks::$stockClass->calcStock($locationid, "", $stock_date,
            "", $productid, "", "",
            "", "", "", "", "",
            "", "", $batch_no, "", "",
            true, true, "", "", false);
        $stock = array_values($stock)[0];
        $opening_balance = $stock['total'] ?? 0;
        $history = Stocks::$stockClass->productHistory($locationid, $productid, $fromdate, $todate, $batch_no);


        $current_balance = $opening_balance;
        foreach ($history as $index => $item) {
//            debug($item);
            $voucher_url = '';
            switch ($item['voucher']) {
                case 'grn':
                    $voucher_url = url('grns', 'view_grn', ['grn' => $item['voucherno']]);
                    break;
                case 'adjustment':
                    $voucher_url = url('stocks', 'adjustment_print', ['adjustmentno' => $item['voucherno']]);
                    break;
                case 'transfer out':
                case 'transfer in':
                    $voucher_url = url('stocks', 'transfer_view', ['transferno' => $item['voucherno']]);
                    break;
                case 'return':
                    $voucher_url = url('grns', 'grn_return_print', ['returnid' => $item['voucherno']]);
                    break;
                case 'sale':
                    $salesid = $Sales->find(['receipt_no' => $item['voucherno']])[0]['id'];
                    $voucher_url = url('sales', 'view_invoice', ['salesid' => $salesid]);
                    break;
                case 'sale return':
                    $voucher_url = url('sales_returns', 'view', ['returnno' => $item['voucherno']]);
                    $history[$index]['voucherno'] = getCreditNoteNo($item['voucherno']);
                    break;
                case 'manufacture raw material':
                case 'manufacture end product':
                    $voucher_url = url('stocks', 'view_manufacture', ['manufactureno' => $item['voucherno']]);
                    break;
            }
            $history[$index]['voucher_url'] = $voucher_url;
            $history[$index]['balance'] = $current_balance + ($item['action'] == 'in' ? $item['qty'] : -$item['qty']);
            $current_balance = $history[$index]['balance'];
        }
//                    debug($history);

        $tData['opening_balance'] = $opening_balance;
        $tData['current_balance'] = $current_balance;
        $tData['history'] = $history;
    }

//    debug($tData);
    $data['content'] = loadTemplate('product_history_list.tpl.php', $tData);
}

if ($action == 'audit_report') {
    Users::isAllowed();
//     debug($_GET);
    $invoiceno = $_GET['invoiceno'];
    $fromdate = $_GET['fromdate'] ?? TODAY;
    $todate = $_GET['todate'];
    $locationid = $_GET['locationid'];
    $tData['branchid'] = $branchid = $_GET['branchid'];
    $clientid = $_GET['clientid'];
    $productid = $_GET['productid'];
    $modelid = $_GET['modelid'];
    $depart = $_GET['depart'];
    $category = $_GET['category'];
    $userid = $_GET['userid'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) {
        $userid = $_SESSION['member']['id'];
        $branchid = $_SESSION['member']['branchid'];
    }

    $title = [];
    if ($invoiceno) {
        $title[] = "Invoice No: " . $invoiceno;
        $fromdate = '';
        $branchid = '';
    }
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All branches";
    }
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($modelid) $title[] = "Brand: " . $Models->get($modelid)['name'];
    if ($depart) $title[] = "Department: " . $Departments->get($depart)['name'];
    if ($category) $title[] = "Tax: " . $Categories->get($category)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];

    $tData['title'] = implode(' | ', $title);

    $sales = Sales::$saleClass->salesAuditReport($fromdate, $todate, $userid, $clientid, $productid,
        $modelid, $depart, $category, $locationid, $branchid, $invoiceno);

//    debug($sales);

    $totals = [];
    foreach ($sales as $s) {
        $totals['currencies'][$s['currencyname']]['price'] += ($s['price'] - $s['discount']) * $s['quantity'];
        $totals['currencies'][$s['currencyname']]['cost'] += $s['unit_cost'] * $s['quantity'];

        //base
        $totals['base']['price'] += ($s['base_price'] - $s['base_discount']) * $s['quantity'];
        $totals['base']['cost'] += $s['base_unit_cost'] * $s['quantity'];
    }
//    debug($totals);

    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['sales'] = $sales;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['categories'] = $Categories->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Audit Reports";
    $data['content'] = loadTemplate('sales_audit_list.tpl.php', $tData);
}

if ($action == 'proforma_audit') {
    Users::isAllowed();
//     debug($_GET);
    $proformano = $_GET['proformano'];
    $proforma_status = $_GET['proforma_status'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $locationid = $_GET['locationid'];
    $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    $clientid = $_GET['clientid'];
    $productid = $_GET['productid'];
    $modelid = $_GET['modelid'];
    $depart = $_GET['depart'];
    $productcategory = $_GET['productcategory'];
    $userid = $_GET['userid'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];

    $title = [];
    if ($proformano) {
        $title[] = "Proforma No: " . $proformano;
        $fromdate = '';
        $branchid = '';
    }
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    if ($proforma_status) $title[] = "Proforma status: " . $proforma_status;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($modelid) $title[] = "Brand: " . $Models->get($modelid)['name'];
    if ($depart) $title[] = "Department: " . $Departments->get($depart)['name'];
    if ($productcategory) $title[] = "Category: " . $ProductCategories->get($productcategory)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];

    $tData['title'] = implode(' | ', $title);

    $details = Proformas::$proformaClass->auditList($proformano, $proforma_status, $fromdate, $todate, $userid, $clientid, $productid, $modelid, $depart, $productcategory, $locationid, $branchid);

//    debug($details);

    $totals = [];
    foreach ($details as $s) {
        $totals['currencies'][$s['currencyname']]['price'] += $s['excamount'];
        $totals['currencies'][$s['currencyname']]['cost'] += $s['totalcost'];

        //base
        $totals['base']['price'] += $s['base_excamount'];
        $totals['base']['cost'] += $s['base_totalcost'];
    }
//    debug($totals);

    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['details'] = $details;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['categories'] = $ProductCategories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Proforma Audit Reports";
    $data['content'] = loadTemplate('proforma_audit_report.tpl.php', $tData);
}

if ($action == 'audit_report_invoice_wise') {
    Users::isAllowed();
//     debug($_GET);
    $invoiceno = $_GET['invoiceno'];
    $fromdate = $_GET['fromdate'] ?? TODAY;
    $todate = $_GET['todate'];
    $locationid = $_GET['locationid'];
    $tData['branchid'] = $branchid = $_GET['branchid'];
    $clientid = $_GET['clientid'];
    $userid = $_GET['userid'];
    $with_non_stock = $_GET['with_non_stock'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $branchid = $_SESSION['member']['branchid'];

    $title = [];
    if ($invoiceno) {
        $title[] = "Invoice No: " . $invoiceno;
        $fromdate = '';
        $branchid = '';
    }
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All branches";
    }
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];
    if ($with_non_stock == 'yes') $title[] = "With Non Stock Item";
    if ($with_non_stock == 'no') $title[] = "Without Non Stock Item";

    $tData['title'] = implode(' | ', $title);

    $invoices = $Sales->salesList('', $userid, $fromdate, $todate, $clientid, $locationid, $branchid);
    $totals = [];
    foreach ($invoices as $index => $s) {
        $invoices[$index]['return_excamount'] = array_sum(array_column($SalesReturns->getList('', $s['salesid'], '', '', 'approved'), 'total_excamount'));
        $invoices[$index]['base_sale_expense'] = array_sum(array_column($Expenses->issuedList('', $s['salesid'], '', 'approved'), 'total_amount'));
        $invoices[$index]['sale_expense'] = round($invoices[$index]['base_sale_expense'] / $invoices[$index]['currency_amount'], 2);

        $sale_details = $Salesdetails->getList($s['salesid']);
        foreach ($sale_details as $detail) {
            if ($detail['sold_non_stock']) $invoices[$index]['has_non_stock'] = true;
            $total_previous_return = array_sum(array_column($SalesReturnDetails->previousReturns($detail['id']), 'qty'));
            $detail['quantity'] -= $total_previous_return;
            $invoices[$index]['cost_amount'] += ($detail['quantity'] * $detail['hidden_cost']);
        }

        if (($with_non_stock == 'yes' && !$invoices[$index]['has_non_stock']) || ($with_non_stock == 'no' && $invoices[$index]['has_non_stock'])) {
            unset($invoices[$index]);
            continue;
        }

        //check different department
        $departmentids = array_unique(array_column($sale_details, 'departid'));
        if (count($departmentids) > 1) $invoices[$index]['has_different_department'] = true;

        $invoices[$index]['base_return_excamount'] = round($invoices[$index]['return_excamount'] * $s['currency_amount'], 2);
        $invoices[$index]['base_cost_amount'] = round($invoices[$index]['cost_amount'] * $s['currency_amount'], 2);
        $invoices[$index]['profit_amount'] = $s['grand_amount'] - $invoices[$index]['sale_expense'] - $invoices[$index]['return_excamount'] - $invoices[$index]['cost_amount'];

        $base_profit = $s['base_grand_amount'] - $invoices[$index]['base_sale_expense'] - $invoices[$index]['base_return_excamount'] - $invoices[$index]['base_cost_amount'];
        $invoices[$index]['base_profit_amount'] = $base_profit;
        $invoices[$index]['profit_margin'] = ($base_profit / $invoices[$index]['base_cost_amount'] * 100);
        //totals
        $totals['currencies'][$s['currencyname']]['grand_amount'] += $s['grand_amount'];
        $totals['currencies'][$s['currencyname']]['sale_expense'] += $invoices[$index]['sale_expense'];
        $totals['currencies'][$s['currencyname']]['return_excamount'] += $invoices[$index]['return_excamount'];
        $totals['currencies'][$s['currencyname']]['cost_amount'] += $invoices[$index]['cost_amount'];
        $totals['currencies'][$s['currencyname']]['profit_amount'] += $invoices[$index]['profit_amount'];

        //base
        $totals['base']['grand_amount'] += $s['base_grand_amount'];
        $totals['base']['sale_expense'] += $invoices[$index]['base_sale_expense'];
        $totals['base']['return_excamount'] += round($invoices[$index]['return_excamount'] * $s['currency_amount'], 2);
        $totals['base']['cost_amount'] += $invoices[$index]['base_cost_amount'];
        $totals['base']['profit_amount'] = ($totals['base']['grand_amount'] - $totals['base']['sale_expense'] - $totals['base']['return_excamount'] - $totals['base']['cost_amount']);
    }

    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['invoices'] = $invoices;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $data['content'] = loadTemplate('sales_audit_invoice_wise.tpl.php', $tData);
}

if ($action == 'non_stock_sales_report' || $action == 'non_stock_sales_report_sr') {
    Users::isAllowed();

    if ($action == 'non_stock_sales_report_sr') $tData['SR_MODE'] = $SR_MODE = true;

//     debug($_GET);
    $invoiceno = $_GET['invoiceno'];
    $fromdate = $_GET['fromdate'] ?? TODAY;
    $todate = $_GET['todate'];
    $locationid = $_GET['locationid'];
    $branchid = $_GET['branchid'] ?: $_SESSION['member']['branchid'];
    $clientid = $_GET['clientid'];
    $productid = $_GET['productid'];
    $category = $_GET['category'];
    $userid = $_GET['userid'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];

    $title = [];
    if ($invoiceno) {
        $title[] = "Invoice No: " . $invoiceno;
        $fromdate = '';
        $branchid = '';
    }
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($category) $title[] = "Tax: " . $Categories->get($category)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];

    $tData['title'] = implode(' | ', $title);

    $sales = $Sales->salesAuditReport($fromdate, $todate, $userid, $clientid, $productid,
        '', '', $category, $locationid, $branchid, $invoiceno, !$SR_MODE, true);

//    debug(array_column($sales, 'base_price'));

    $totals = [];
    foreach ($sales as $s) {
        $totals['currencies'][$s['currencyname']]['price'] += ($s['price'] - $s['discount']);

        //base
        $totals['base']['price'] += ($s['base_price'] - $s['base_discount']);
    }
//    debug($totals);

    $tData['basecurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['sales'] = $sales;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['categories'] = $Categories->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Audit Reports";
    $data['content'] = loadTemplate('non_stock_sales_report.tpl.php', $tData);
}

if ($action == 'sales_summary') {
    Users::isAllowed();

    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $clientid = $_GET['clientid'];
    $userid = $_GET['userid'];
    $productid = $_GET['productid'];
    $modelid = $_GET['modelid'];
    $productcategory = $_GET['productcategory'];
    $depart = $_GET['depart'];

    $title = [];
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All Branches";
    }
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($productcategory) $title[] = "Product Category: " . $ProductCategories->get($productcategory)['name'];
    if ($modelid) $title[] = "Brand: " . $Models->get($modelid)['name'];
    if ($depart) $title[] = "Department: " . $Departments->get($depart)['name'];
    if ($category) $title[] = "Tax: " . $Categories->get($category)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];

    $tData['title'] = implode(' | ', $title);

    $summary = Sales::$saleClass->salesSummary($fromdate, $todate, $userid, $clientid, $productid, $modelid, $productcategory, $depart, $locationid, $branchid);
//debug($summary);
    $tData['summary'] = $summary;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $data['content'] = loadTemplate('sales_summary.tpl.php', $tData);
}

if ($action == 'sales_summary_monthly') {
    Users::isAllowed();

//    debug($_GET);
    $productid = $_GET['productid'];
    $tData['modelid'] = $modelid = $_GET['modelid'];
    $tData['catid'] = $catid = $_GET['catid'];
    $tData['selected_months'] = $months = $_GET['months'];
    $tData['branchid'] = $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $tData['userid'] = $userid = $_GET['userid'];
    $tData['productcategoryid'] = $productcategoryid = $_GET['productcategoryid'];
    $tData['subcategoryid'] = $subcategoryid = $_GET['subcategoryid'];
    $tData['deptid'] = $deptid = $_GET['deptid'];
    $tData['nonstock'] = $nonstock = $_GET['nonstock'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];

    //creating title
    $title = [];
    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All Branches";
    }
    if ($locationid) {
        $tData['location'] = Locations::$locationClass->locationList($locationid)[0];
        $title[] = "Location: " . $tData['location']['name'];
    }
    if ($userid){
        $tData['salesperson'] = $Users->get($userid);
        $title[] = "Sales Person: " . $tData['salesperson']['name'];
    }
    if ($productid) {
        $tData['product'] = $Products->get($productid);
        $title[] = "Product: " . $tData['product']['name'];
    }
    if ($modelid) $title[] = "Brand: " . $Models->get($modelid)['name'];
    if ($productcategoryid) $title[] = "Product category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($deptid) $title[] = "Department: " . $Departments->get($deptid)['name'];
    if ($nonstock == 'yes') $title[] = "Non-Stock Items Only";
    if ($nonstock == 'no') $title[] = "Stock Items Only";
    if ($catid) {
        $category = $Categories->get($catid);
        $title[] = "Tax Category: " . $category['name'] . " " . $category['vat_percent'] . "%";
    }
    $tData['title'] = implode(' | ', $title);
    $monthWiseSales = Sales::$saleClass->monthWiseSales($months, $userid, $productid, $modelid, $deptid, $nonstock, $catid, $productcategoryid, $subcategoryid, $locationid, $branchid);
//        debug($monthWiseSales);

    //arrange products
    $monthNames = [];
    $productArray = [];
    $totals = [];
    foreach ($monthWiseSales as $index => $sale) {
        //collecting months
        $monthNames[$sale['salesMonth']] = $sale['salesMonth'];
        $totals[$sale['salesMonth']]['amount'] += $sale['amount'];
        $totals[$sale['salesMonth']]['incamount'] += $sale['incamount'];

        //products
        $productArray[$sale['productID']]['productdescription'] = $sale['productdescription'];
        $productArray[$sale['productID']]['productName'] = $sale['productName'];
        $productArray[$sale['productID']]['barcode'] = $sale['barcode'];
        $productArray[$sale['productID']]['brandName'] = $sale['brandName'];
        $productArray[$sale['productID']]['catName'] = $sale['catName'];
        $productArray[$sale['productID']]['unit'] = $sale['unit'];
        $productArray[$sale['productID']]['months'][$sale['salesMonth']]['quantity'] = $sale['quantity'];
        $productArray[$sale['productID']]['months'][$sale['salesMonth']]['amount'] = $sale['amount'];
        $productArray[$sale['productID']]['months'][$sale['salesMonth']]['incamount'] = $sale['incamount'];
    }
//        debug($totals);
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['months'] = $monthNames;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['models'] = $Models->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $tData['branchid'] = $branchid;
    $tData['salesSummary'] = $productArray;
    $data['content'] = loadTemplate('sales_summary_monthly.tpl.php', $tData);
}

if ($action == 'daily_sales_summary') {


    $productid = $_GET['productid'];
    $tData['branchids'] = $branchids = removeSpecialCharacters($_GET['branchids']);
    $tData['productcategoryid'] = $productcategoryid = removeSpecialCharacters($_GET['productcategoryid']);
    $tData['subcategoryid'] = $subcategoryid = removeSpecialCharacters($_GET['subcategoryid']);
    $tData['brandid'] = $brandid = removeSpecialCharacters($_GET['brandid']);
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];

    $title = [];
    if ($stockdate) $title[] = "Stock Date: " . fDate($stockdate);
    if ($productid) $title[] = "Product: " . Products::$productClass->get($productid)['name'];
    if ($brandid) $title[] = "Brand: " . Models::$staticClass->get($brandid)['name'];
    if ($productcategoryid) $title[] = "Category: " . ProductCategories::$class->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "SubCategory: " . ProductSubCategories::$class->get($subcategoryid)['name'];
    if ($fromdate) {
        $title[] = "From: " . fDate($fromdate);
        $tData['fromdate'] = $fromdate;
    }
    if ($todate) {
        $title[] = "To: " . fDate($todate);
        $tData['todate'] = $todate;
    }

    $tData['title'] = implode(' | ', $title);
    $branchlist = Branches::$branchClass->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $selectedBranches = array_filter($branchlist, function ($b) use ($branchids) {
        return in_array($b['id'], $branchids);
    });

    $salesdetails = Salesdetails::$saleDetailsClass->getDetailedList($productid, '', '', $productcategoryid, $subcategoryid, $brandid,
        '', $fromdate, $todate, '1', '', '', '', $branchids);
//    debug($salesdetails);

    $summary = [];
    $branches = [];
    $chart_data = [];
    $chart_data['branches'] = array_flip(array_column($selectedBranches, 'name'));
    foreach ($chart_data['branches'] as $branchname => $val) $chart_data['branches'][$branchname] = [];

    foreach ($salesdetails as $d) {
        $date = fDate($d['invoicedate'], 'd M Y, l');
        $summary[$date]['branches'][$d['branchname']] += $d['incamount'];
        $summary[$date]['total'] += $d['incamount'];

        $label = fDate($d['invoicedate'], 'D, d M Y');

        $chart_data['labels'][$label] += $d['incamount'];
        $chart_data['branches'][$d['branchname']][$label] += $d['incamount'];
        foreach ($chart_data['branches'] as $branchname => $amounts) {
            if (!in_array($label, array_keys($amounts))) $chart_data['branches'][$branchname][$label] = 0;
        }
//        debug($chart_data);
    }
//    debug([$summary, $chart_data]);

    $tData['summary'] = $summary;
    $tData['chart_data'] = $chart_data;
    $tData['branches'] = $selectedBranches;
    $tData['branchlist'] = $branchlist;
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive();
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive();
    $_SESSION['pagetitle'] = "Daily Sales Summary";
    $data['content'] = loadTemplate('daily_sales_summary_report.tpl.php', $tData);
}

if ($action == 'client_monthly_sale_summary') {
    Users::isAllowed();

//    debug($_GET);
    $clientid = $_GET['clientid'];
    $productid = $_GET['productid'];
    $modelid = $_GET['modelid'];
    $catid = $_GET['catid'];
    $months = $_GET['months'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $userid = $_GET['userid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $deptid = $_GET['deptid'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];

    //creating title
    $title = [];
    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All Branches";
    }
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($modelid) $title[] = "Brand: " . $Models->get($modelid)['name'];
    if ($productcategoryid) $title[] = "Product category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($deptid) $title[] = "Department: " . $Departments->get($deptid)['name'];
    if ($catid) {
        $category = $Categories->get($catid);
        $title[] = "Tax Category: " . $category['name'] . " " . $category['vat_percent'] . "%";
    }
    $tData['title'] = implode(' | ', $title);
    $clientSales = Sales::$saleClass->clientMonthWiseSales($months, $clientid, $userid, $productid, $modelid, $deptid, $catid, $productcategoryid, $subcategoryid, $locationid, $branchid);
//        debug($clientSales);

    //arrange products
    $monthNames = [];
    $client_summary = [];
    $totals = [];
    foreach ($clientSales as $index => $sale) {
        //collecting months
        $monthNames[$sale['salesMonth']] = $sale['salesMonth'];
        $totals[$sale['salesMonth']]['amount'] += $sale['amount'];
        $totals[$sale['salesMonth']]['incamount'] += $sale['incamount'];

        //products
        $client_summary[$sale['clientid']]['clientid'] = $sale['clientid'];
        $client_summary[$sale['clientid']]['clientname'] = $sale['clientname'];
        $client_summary[$sale['clientid']]['months'][$sale['salesMonth']]['qty'] = $sale['qty'];
        $client_summary[$sale['clientid']]['months'][$sale['salesMonth']]['amount'] = $sale['amount'];
        $client_summary[$sale['clientid']]['months'][$sale['salesMonth']]['incamount'] = $sale['incamount'];
    }
//        debug($client_summary);
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $tData['months'] = $monthNames;
    $tData['client_summary'] = $client_summary;
    $tData['totals'] = $totals;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['models'] = $Models->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['departments'] = $Departments->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['subcategories'] = $ProductSubCategories->getAllActive();
    $tData['branchid'] = $branchid;
    $data['content'] = loadTemplate('client_monthly_sales_summary.tpl.php', $tData);
}

if ($action == 'sales_payment' || $action == 'sales_payment_sr') {
    Users::isAllowed();
    if ($action == 'sales_payment_sr') {
        $SR_MODE = true;
        $tData['SR_MODE'] = $SR_MODE;
    }
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $branchid = $_GET['branchid'];
    $locationid = $_GET['locationid'];
    $currencyid = $_GET['currencyid'];
    $clientid = $_GET['clientid'];
    $payment_issuedby = $_GET['payment_issuedby'];
    $method = $_GET['method'];
    $eaccount = $_GET['eaccount'];
    $receiptno = $_GET['receiptno'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) {
        $payment_issuedby = $_SESSION['member']['id'];
        $branchid = $_SESSION['member']['branchid'];
        $tData['branches'] = $Branches->find(['id' => $_SESSION['member']['branchid']]);
    } else {
        $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    }

    $title = [];
    if ($receiptno) {
        $fromdate = $toDate = '';
    }

    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All Branches";
    }
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($currencyid) {
        $currency = $Currencies->get($currencyid);
        $title[] = "Currency: " . $currency['name'] . " - " . $currency['description'];
    }
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($payment_issuedby) $title[] = "Payment received by: " . $Users->get($payment_issuedby)['name'];
    if ($method) $title[] = "Payment Method: " . $method;
    if ($eaccount) $title[] = "E-Account: " . $ElectronicAccounts->get($eaccount)['name'];

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['title'] = implode(' | ', $title);

    $receipt_type = $SR_MODE ? '' : SalesPayments::RECEIPT_TYPE_TRA;

    $payments = SalesPayments::$salePaymentClass->detailedPaymentInfo($receiptno, $fromdate, $todate, $locationid, $branchid, $method, $receipt_type, true, $currencyid,
        $payment_issuedby, $clientid, false, $eaccount);
    $tData['payments'] = $payments;
//debug($payments);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['eaccounts'] = $ElectronicAccounts->getAllActive();
    $tData['currencies'] = $Currencies->getAllActive();
    $tData['baseCurrency'] = $CurrenciesRates->getBaseCurrency();
    $data['content'] = loadTemplate('sales_payment_report_sr.tpl.php', $tData);
}

if ($action == 'canceled_receipt') {
    Users::isAllowed();
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $userid = removeSpecialCharacters($_GET['issuedby']);

    $title = [];

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $tData['canceled'] = CanceledReceipts::$staticClass->getList($userid, $fromDate, $toDate);
//    debug($tData['canceled']);
    $data['content'] = loadTemplate('canceled_receipt.tpl.php', $tData);
}

if ($action == 'cash_summary') {
    Users::isAllowed();
    $branchid = $_GET['branchid'];
    $toDate = $fromDate = $_GET['date'] ?: TODAY;

    $title = [];
    $title[] = ($branchid) ? "Branch: " . $Branches->get($branchid)['name'] : "All Branches";
    $title[] = "Date: " . fDate($fromDate);
    $tData['title'] = implode(' | ', $title);
    $tData['date'] = $fromDate;

    if (!IS_ADMIN) $branchid = $_SESSION['member']['branchid'];

    //CASH IN
    //advance payments
    $totalCashAdvance = 0;
    $advPayments = $AdvancePayments->paymentList("", "", $fromDate, $toDate, PaymentMethods::CASH, $branchid);
    $totalCashAdvance = [];
    foreach ($advPayments as $a) $totalCashAdvance[$a['currencyname']] += $a['amount'];
//    debug($totalCashAdvance);

    //sales payments tra
    $payments = $Salespayments->detailedPaymentInfo("", $fromDate, $toDate, "", $branchid, PaymentMethods::CASH,
        SalesPayments::RECEIPT_TYPE_TRA, true);
    $totalTraCashSalesPayment = [];
    foreach ($payments as $p) $totalTraCashSalesPayment[$p['currencyname']] += $p['received_amount'];
//    debug($payments);

    $baseCurrency = $CurrenciesRates->getBaseCurrency();
    //CASH OUT
    //grn supplier payments
    $totalSupplierPayment = [];
    $supplierPayments = $SupplierPayments->detailedPaymentInfo("", "", $fromDate, $toDate, "", $branchid, PaymentMethods::CASH);
    $totalSupplierPayment[$baseCurrency['name']] = array_sum(array_column($supplierPayments, 'input_amount'));

    //todo include supplier clean advance payment

    //expenses
    $totalExpense = [];
    $expenses = $Expenses->issuedList("", "", "", "approved", "", $fromDate, $toDate, $branchid, false);
    $totalExpense[$baseCurrency['name']] = array_sum(array_column($expenses, 'amount'));

    //sales returns/ credit notes
    $totalTraSalesReturn = [];
    $salesreturns = $SalesReturns->getList('', '', '', '', 'approved', '', '', $fromDate, $toDate,
        '', $branchid, PaymentMethods::CASH, '', true);
    foreach ($salesreturns as $sr) $totalTraSalesReturn[$sr['currencyname']] += $sr['return_amount'];
//    debug($totalTraSalesReturn);


    $usedCurrency = array_unique(array_merge(array_keys($totalCashAdvance), array_keys($totalTraCashSalesPayment),
        array_keys($totalExpense), array_keys($totalSupplierPayment), array_keys($totalTraSalesReturn)));
//    debug($usedCurrency);

    //expense
    $expenseLedger = [];
    foreach ($expenses as $index => $item) {
        $expenseLedger[] = [
            'date' => $item['doc'],
            'voucherno' => getVoucherNo($item['id']),
            'amount' => $item['amount'],
            'paidto' => $item['paidto'],
            'invoice_no' => $item['invoiceno'],
            'attrname' => $item['attrname'],
            'receipt_no' => $item['receipt_no'],
            'remarks' => $item['remarks']
        ];
    }
    //group expense by attribute name
    $groupedExpenses = [];
    foreach ($expenseLedger as $index => $item) {
        $groupedExpenses[$item['attrname']]['name'] = $item['attrname'];
        $groupedExpenses[$item['attrname']]['amount'] += $item['amount'];
        $groupedExpenses[$item['attrname']]['count'] += 1;
    }

    //cash in hand per currency
    $cashInHand = [];
    foreach ($usedCurrency as $currencyname) {
        $cashInHand[$currencyname] = ($totalCashAdvance[$currencyname] + $totalTraCashSalesPayment[$currencyname] - $totalExpense[$currencyname] - $totalSupplierPayment[$currencyname] - $totalTraSalesReturn[$currencyname]);
    }
//    debug($cashInHand);
    $tData = array_merge($tData, [
        'totalTraCashSalesPayment' => $totalTraCashSalesPayment,
        'supplierPayment' => $totalSupplierPayment,
        'totalCashAdvance' => $totalCashAdvance,
        'totalExpenses' => $totalExpense,
        'totalTraSalesReturn' => $totalTraSalesReturn,
        'cashInHand' => $cashInHand,
        'usedCurrency' => $usedCurrency,
        'baseCurrency' => $baseCurrency,
        'voucherCount' => count($expenses),
        'expenses' => $expenseLedger,
        'groupedExpenses' => $groupedExpenses
    ]);


//    debug($tData);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);

    $data['content'] = loadTemplate('cash_summary.tpl.php', $tData);
}

if ($action == 'cash_summary_with_sr') {
    Users::isAllowed();
    $branchid = $_GET['branchid'];
    $toDate = $fromDate = $_GET['date'] ?: TODAY;

    $title = [];
    $title[] = ($branchid) ? "Branch: " . $Branches->get($branchid)['name'] : "All Branches";
    $title[] = "Date: " . fDate($fromDate);
    $tData['title'] = implode(' | ', $title);
    $tData['date'] = $fromDate;

    if (!IS_ADMIN) $branchid = $_SESSION['member']['branchid'];

    //CASH IN
    //advance payments
    $totalCashAdvance = 0;
    $advPayments = AdvancePayments::$advancePaymentClass->paymentList("", "", $fromDate, $toDate, PaymentMethods::CASH, $branchid);
    $totalCashAdvance = [];
    foreach ($advPayments as $a) $totalCashAdvance[$a['currencyname']] += $a['amount'];
//    debug($totalCashAdvance);

    //sales payments tra
    $payments = $Salespayments->detailedPaymentInfo("", $fromDate, $toDate, "", $branchid, PaymentMethods::CASH, SalesPayments::RECEIPT_TYPE_TRA, true);
    $totalTraCashSalesPayment = [];
    foreach ($payments as $p) $totalTraCashSalesPayment[$p['currencyname']] += $p['received_amount'];
//    debug($totalTraCashSalesPayment);

    //sales payments sr
    $payments = $Salespayments->detailedPaymentInfo("", $fromDate, $toDate, "", $branchid, PaymentMethods::CASH, SalesPayments::RECEIPT_TYPE_SR, true);
    $totalSrCashSalesPayment = [];
    foreach ($payments as $p) $totalSrCashSalesPayment[$p['currencyname']] += $p['received_amount'];
//    debug($totalSrCashSalesPayment);

    $baseCurrency = $CurrenciesRates->getBaseCurrency();
    //CASH OUT
    //grn supplier payments
    $totalSupplierPayment = [];
    $supplierPayments = $SupplierPayments->detailedPaymentInfo("", "", $fromDate, $toDate, "", $branchid, PaymentMethods::CASH);
    $totalSupplierPayment[$baseCurrency['name']] = array_sum(array_column($supplierPayments, 'input_amount'));

    //todo include supplier clean advance payment

    //expenses
    $totalExpense = [];
    $expenses = $Expenses->issuedList("", "", "", "approved", "", $fromDate, $toDate, $branchid, false);
    $totalExpense[$baseCurrency['name']] = array_sum(array_column($expenses, 'amount'));

    //sales returns/ credit notes
    $totalSalesReturn = [];
    $salesreturns = $SalesReturns->getList('', '', '', '', 'approved', '', '', $fromDate, $toDate,
        '', $branchid, PaymentMethods::CASH, '');
    foreach ($salesreturns as $sr) $totalSalesReturn[$sr['currencyname']] += $sr['return_amount'];
//    debug($totalSalesReturn);

    $usedCurrency = array_unique(array_merge(array_keys($totalCashAdvance), array_keys($totalTraCashSalesPayment), array_keys($totalSrCashSalesPayment),
        array_keys($totalExpense), array_keys($totalSupplierPayment), array_keys($totalSalesReturn)));
//    debug($usedCurrency);

    //expense
    $expenseLedger = [];
    foreach ($expenses as $index => $item) {
        $expenseLedger[] = [
            'date' => $item['doc'],
            'voucherno' => getVoucherNo($item['id']),
            'amount' => $item['amount'],
            'paidto' => $item['paidto'],
            'invoice_no' => $item['invoiceno'],
            'attrname' => $item['attrname'],
            'receipt_no' => $item['receipt_no'],
            'remarks' => $item['remarks']
        ];
    }
    //group expense by attribute name
    $groupedExpenses = [];
    foreach ($expenseLedger as $index => $item) {
        $groupedExpenses[$item['attrname']]['name'] = $item['attrname'];
        $groupedExpenses[$item['attrname']]['amount'] += $item['amount'];
        $groupedExpenses[$item['attrname']]['count'] += 1;
    }

    //cash in hand per currency
    $cashInHand = [];
    foreach ($usedCurrency as $currencyname) {
        $cashInHand[$currencyname] = ($totalCashAdvance[$currencyname] + $totalSrCashSalesPayment[$currencyname] + $totalTraCashSalesPayment[$currencyname] - $totalExpense[$currencyname] - $totalSupplierPayment[$currencyname] - $totalSalesReturn[$currencyname]);
    }
//    debug($cashInHand);

    $tData = array_merge($tData, [
        'totalSrCashSalesPayment' => $totalSrCashSalesPayment,
        'totalTraCashSalesPayment' => $totalTraCashSalesPayment,
        'supplierPayment' => $totalSupplierPayment,
        'totalCashAdvance' => $totalCashAdvance,
        'totalExpenses' => $totalExpense,
        'totalSalesReturn' => $totalSalesReturn,
        'cashInHand' => $cashInHand,
        'usedCurrency' => $usedCurrency,
        'baseCurrency' => $baseCurrency,
        'voucherCount' => count($expenses),
        'expenses' => $expenseLedger,
        'groupedExpenses' => $groupedExpenses
    ]);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);

    $data['content'] = loadTemplate('cash_summary_with_sr.tpl.php', $tData);
}

if ($action == 'referral_report') {
    Users::isAllowed();
//    debug($_GET);
    if (empty($_GET['fromdate'])) {
        $fromdate = date('Y-m-d', strtotime($todate . ' -1 month'));
    } else {
        $fromdate = $_GET['fromdate'];
    }
    if (empty($_GET['todate'])) {
        $todate = TODAY;
    } else {
        $todate = $_GET['todate'];
    }

    $productId = $_GET['productId'];
    $doctorId = $_GET['doctorId'];
    $hospitalId = $_GET['hospitalId'];
    $title = [];
    if ($productId) {
        $product = $Products->get($productId);
        $title[] = "Product: " . $product['name'];
    }
    if ($doctorId) {
        $doctor = $Doctors->get($doctorId);
        $title[] = "Doctor: " . $doctor['name'];
    }
    if ($hospitalId) {
        $hospital = $Hospitals->get($hospitalId);
        $title[] = "Hospital: " . $hospital['name'];
    }

    $title[] = "From: " . fDate($fromdate);
    $title[] = "To: " . fDate($todate);
    $tData['title'] = implode(' | ', $title);

    $referrals = $SalesPrescriptions->getReferrals($productId, $doctorId, $hospitalId, $fromdate, $todate);
    $tData['referrals'] = $referrals;
    $data['content'] = loadTemplate('referral_report.tpl.php', $tData);
}

if ($action == 'cash_summary_userwise') {
    Users::isAllowed();
    $branchid = $_GET['branchid'];
    $currencyid = $_GET['currencyid'];
    $userid = $_GET['userid'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];

    $branchid = IS_ADMIN ? $branchid : $_SESSION['member']['branchid'];
    $userid = Users::can(OtherRights::approve_other_credit_invoice) ? $userid : $_SESSION['member']['id'];

    $title = [];
    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All Branches";
    }
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $invoices = Salespayments::$salePaymentClass->withSaleDetails('', $userid, '', '', PAYMENT_TYPE_CASH, '',
        $fromdate, $todate, $currencyid, '', $branchid);
    $summary = [];
    foreach ($invoices as $i) {
        $summary[$i['branchid']]['branchid'] = $i['branchid'];
        $summary[$i['branchid']]['branchname'] = $i['branchname'];
        $summary[$i['branchid']]['users'][$i['sale_createdby']]['name'] = $i['salesperson'];
        $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']]['count']++;
        if ($i['method'] == PaymentMethods::CASH) $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']][PaymentMethods::CASH] += $i['received_amount'];
        if ($i['method'] == PaymentMethods::CREDIT_CARD) $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']][PaymentMethods::CREDIT_CARD] += $i['received_amount'];
        $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']]['total'] += $i['received_amount'];

        //branch total
        $summary[$i['branchid']]['currency'][$i['currencyname']]['count']++;
        if ($i['method'] == PaymentMethods::CASH) $summary[$i['branchid']]['currency'][$i['currencyname']][PaymentMethods::CASH] += $i['received_amount'];
        if ($i['method'] == PaymentMethods::CREDIT_CARD) $summary[$i['branchid']]['currency'][$i['currencyname']][PaymentMethods::CREDIT_CARD] += $i['received_amount'];
        $summary[$i['branchid']]['currency'][$i['currencyname']]['total'] += $i['received_amount'];
    }
//    debug($invoices);

    $tData['summary'] = $summary;
    $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $tData['currencies'] = $Currencies->getAllActive();
    $data['content'] = loadTemplate('cash_summary_userwise.tpl.php', $tData);
}

if ($action == 'cash_summary_userwise_overall') {
    Users::isAllowed();
    $branchid = $_GET['branchid'];
    $currencyid = $_GET['currencyid'];
    $userid = $_GET['userid'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];

//    $branchid = IS_ADMIN ? $branchid : $_SESSION['member']['branchid'];
//    $userid = Users::can(OtherRights::approve_other_credit_invoice) ? $userid : $_SESSION['member']['id'];

    $title = [];
    if ($branchid) {
        $title[] = "Branch: " . $Branches->get($branchid)['name'];
    } else {
        $title[] = "All Branches";
    }
    if ($currencyid) $title[] = "Currency: " . $Currencies->get($currencyid)['name'];
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $invoices = Salespayments::$salePaymentClass->withSaleDetails('', $userid, '', '', PAYMENT_TYPE_CASH, '',
        $fromdate, $todate, $currencyid, '', $branchid);
//    debug($invoices);
    $summary = [];
    foreach ($invoices as $i) {
        $summary[$i['branchid']]['branchid'] = $i['branchid'];
        $summary[$i['branchid']]['branchname'] = $i['branchname'];
        $summary[$i['branchid']]['users'][$i['sale_createdby']]['name'] = $i['salesperson'];
        $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']]['count']++;
        if ($i['method'] == PaymentMethods::CASH)
            $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']][PaymentMethods::CASH][$i['receipt_method'] == 'sr' ? 'sr' : 'non-sr'] += $i['received_amount'];
        if ($i['method'] == PaymentMethods::CREDIT_CARD)
            $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']][PaymentMethods::CREDIT_CARD][$i['receipt_method'] == 'sr' ? 'sr' : 'non-sr'] += $i['received_amount'];
        $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']]['total'][$i['receipt_method'] == 'sr' ? 'sr' : 'non-sr'] += $i['received_amount'];
        $summary[$i['branchid']]['users'][$i['sale_createdby']]['currency'][$i['currencyname']]['total']['overall'] += $i['received_amount'];
        //branch total
        $summary[$i['branchid']]['currency'][$i['currencyname']]['count']++;
        if ($i['method'] == PaymentMethods::CASH) $summary[$i['branchid']]['currency'][$i['currencyname']][PaymentMethods::CASH][$i['receipt_method'] == 'sr' ? 'sr' : 'non-sr'] += $i['received_amount'];
        if ($i['method'] == PaymentMethods::CREDIT_CARD) $summary[$i['branchid']]['currency'][$i['currencyname']][PaymentMethods::CREDIT_CARD][$i['receipt_method'] == 'sr' ? 'sr' : 'non-sr'] += $i['received_amount'];
        $summary[$i['branchid']]['currency'][$i['currencyname']]['total'][$i['receipt_method'] == 'sr' ? 'sr' : 'non-sr'] += $i['received_amount'];
        $summary[$i['branchid']]['currency'][$i['currencyname']]['total']['overall'] += $i['received_amount'];
    }
//    debug($summary);
//    debug($invoices);

    $tData['summary'] = $summary;
    $tData['branches'] = IS_ADMIN ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $tData['currencies'] = $Currencies->getAllActive();
    $data['content'] = loadTemplate('cash_summary_userwise_overall.tpl.php', $tData);
}


//stock reports
if ($action == 'grn_return_detailed_report') {

    Users::isAllowed();
    $searchterms = $_GET['search'];

    $locationid = $searchterms['locationid'];
    $returnid = $searchterms['returnid'];
    $grnid = $searchterms['grn'];
    $productid = $searchterms['productid'];
    $batchno = $searchterms['batchno'];
    $fromdate = $searchterms['from'];
    $todate = $searchterms['to'];
    $supplierid = $searchterms['supplierid'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$locationid) $locationid = $_SESSION['member']['locationid'];
        if ($ALL_BRANCH) {
            $branchLocations = $Locations->locationList();
        } else {
            $branchLocations = $Locations->locationList('', $_SESSION['member']['branchid']);
        }
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($locationid, array_column($branchLocations, 'id'))) $locationid = $_SESSION['member']['locationid'];
    } else {
        $locationid = $_SESSION['member']['locationid'];
    }
    $tData['location'] = $location = $Locations->get($locationid);
    $title = [];
    if ($returnid) $title[] = "Return No: " . $returnid;
    if ($locationid) $title[] = "Location: " . $location['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($grnid) $title[] = "GRN No: " . $grnid;
    if ($batchno) $title[] = "Batch No: " . $batchno;
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if (!$returnid) {
        if (!$fromdate) $fromdate = date('Y-m-d', strtotime('-1 months'));
        if (!$todate) $todate = date('Y-m-d');
        $title[] = "From: " . fDate($fromdate);
        $title[] = "To: " . fDate($todate);
    }

    $tData['title'] = implode(' | ', $title);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $tData['returnList'] = $GrnReturns->getGrnReturnBatchWise($returnid, $grnid, $supplierid, $productid, "", $batchno, $fromdate, $todate, $locationid, $branchid);
//     debug($tData['returnList']);
    $tData['suppliers'] = $Suppliers->find(array('status' => 'active'));
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $data['content'] = loadTemplate('grn_return_detailed_report.tpl.php', $tData);
}

if ($action == 'grn_sales_analysis') {
    $grnid = removeSpecialCharacters($_GET['grnid']);
    $supplierid = removeSpecialCharacters($_GET['supplierid']);
    $productid = removeSpecialCharacters($_GET['productid']);
    $fromdate = $_GET['fromdate'] ?: date('Y-m-d', strtotime('-1 month'));
    $todate = $_GET['todate'];

    if ($grnid) $fromdate = $todate = '';

    $title = [];
    if ($grnid) $title[] = "GRN no: " . $grnid;
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $batches = Batches::$batchesClass->getList($grnid, $supplierid, $productid, $fromdate, $todate);
//    debug($batches);
    $items = [];
    foreach ($batches as $b) {
        $batchStock = Stocks::$stockClass->calcStock(
            $b['locid'],
            $b['stockid'],
            "", "", "",
            "", "", "",
            "", "", "", "",
            "", $b['batchid'], "",
            "", "", true, false,
            "", "", false
        );
        $b['current_batchqty'] = $batchStock[0]['total'];
        $items[$b['gdi']]['gdi'] = $b['gdi'];
        $items[$b['gdi']]['grnid'] = $b['grnid'];
        $items[$b['gdi']]['locid'] = $b['locid'];
        $items[$b['gdi']]['locationname'] = $b['locationname'];
        $items[$b['gdi']]['supplierid'] = $b['supplierid'];
        $items[$b['gdi']]['suppliername'] = $b['suppliername'];
        $items[$b['gdi']]['createdby'] = $b['createdby'];
        $items[$b['gdi']]['doc'] = $b['doc'];
        $items[$b['gdi']]['currencyname'] = $b['currencyname'];
        $items[$b['gdi']]['productid'] = $b['productid'];
        $items[$b['gdi']]['productname'] = $b['productname'];
        $items[$b['gdi']]['barcode_office'] = $b['barcode_office'];
        $items[$b['gdi']]['barcode_manufacture'] = $b['barcode_manufacture'];
        $items[$b['gdi']]['stockid'] = $b['stockid'];
        $items[$b['gdi']]['price'] = $b['price'];
        $items[$b['gdi']]['quantity'] = $b['quantity'];
        $items[$b['gdi']]['current_qty'] += $b['current_batchqty'];
        if (!isset($items[$b['gdi']]['opening_qty'])) {
            $stkdate = strtotime($b['doc'] . " -1 second");
            $stkdate = date('Y-m-d H:i:s', $stkdate);
            $stock = Stocks::$stockClass->calcStock(
                $b['locid'],
                $b['stockid'],
                $stkdate, "", "",
                "", "", "",
                "", "", "", "",
                "", "", "",
                "", "", true, true,
                "", "", true
            );
            $stock = array_values($stock);
            $items[$b['gdi']]['opening_qty'] = $stock[0]['total'] ?: 0;
        }
//    debug([$b, $items]);
    }

    $tData['items'] = $items;
    $tData['locations'] = $Locations->locationList();
    $tData['suppliers'] = $Suppliers->getAll();

    $data['content'] = loadTemplate('grn_sales_analysis_report.tpl.php', $tData);
}

if ($action == "transfer_detailed_report") {
    Users::isAllowed();
    $fromdate = $_GET['search']['fromdate'];
    $todate = $_GET['search']['todate'];
    $productid = $_GET['search']['productid'];
    $transferno = $_GET['search']['transferno'];
    $batchno = $_GET['search']['batchno'];
    $fromlocation = $_GET['search']['fromlocation'];
    $tolocation = $_GET['search']['tolocation'];

    $title = [];
    if ($transferno) $title[] = "Transfer No: " . $transferno;
    if ($fromlocation) $title[] = "From " . $Locations->get($fromlocation)['name'];
    if ($tolocation) $title[] = "To " . $Locations->get($tolocation)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($batchno) $title[] = "Batch No: " . $batchno;
    if (!$transferno) {
        if (!$fromdate) $fromdate = date('Y-m-d', strtotime('-1 months'));
        $title[] = "From date: " . fDate($fromdate);
        if ($todate) $title[] = "To date: " . fDate($todate);
    }

    $tData['title'] = implode(' | ', $title);

    $tData['transfers'] = $StockTransfers->stockTransferBatchWise($transferno, $productid, $batchno, $fromlocation, $tolocation, $fromdate, $todate);
//     debug($tData['transfers']);
    $data['content'] = loadTemplate('transfer_detailed_report.tpl.php', $tData);
}

if ($action == 'adjustment_detailed_report') {
    Users::isAllowed();

    $adjustmentno = $_GET['search']['adjustmentno'];
    $locationid = $_GET['search']['locationid'];
    $productid = $_GET['search']['productid'];
    $batchno = $_GET['search']['batchno'];
    $fromdate = $_GET['search']['fromdate'];
    $todate = $_GET['search']['todate'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$locationid) $locationid = $_SESSION['member']['locationid'];
        if ($ALL_BRANCH) {
            $branchLocations = $Locations->locationList();
        } else {
            $branchLocations = $Locations->locationList('', $_SESSION['member']['branchid']);
        }
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($locationid, array_column($branchLocations, 'id'))) $locationid = $_SESSION['member']['locationid'];
    } else {
        $locationid = $_SESSION['member']['locationid'];
    }

    $title = [];
    if ($adjustmentno) $title[] = "Adjustment No: " . $adjustmentno;
    if ($batchno) $title[] = "Batch No: " . $batchno;
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if (!$adjustmentno && !$batchno) {
        if (!$fromdate) $fromdate = date('Y-m-d', strtotime('-1 months'));
    }

    $title[] = "From date: " . fDate($fromdate);
    $tData['fromdate'] = $fromdate;
    if ($todate) $title[] = "To date: " . fDate($todate);
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $adjustmentBatches = $Stocks->stockAdjustmentBatchWise($adjustmentno, $productid, $batchno, $locationid, $fromdate, $todate, $branchid);
    $newArray = [];
    foreach ($adjustmentBatches as $index => $item) {
//        debug($item);
        $newArray[$item['detailId']]['detailId'] = $item['detailId'];
        $newArray[$item['detailId']]['stockid'] = $item['stockid'];
        $newArray[$item['detailId']]['adjustmentno'] = $item['id'];
        $newArray[$item['detailId']]['locationname'] = $item['locationname'];
        $newArray[$item['detailId']]['doc'] = $item['doc'];
        $newArray[$item['detailId']]['issuedby'] = $item['issuedby'];
        $newArray[$item['detailId']]['productname'] = $item['productname'];
        $newArray[$item['detailId']]['action'] = $item['action'];
        $newArray[$item['detailId']]['current_stock'] = $item['current_stock'];
        $newArray[$item['detailId']]['qty'] += $item['qty'];

        $newArray[$item['detailId']]['batch_variation'] += $item['action'] == StockAdjustmentBatches::ACTION_ADD
            ? $item['qty']
            : (-$item['qty']);

        $newArray[$item['detailId']]['after_qty'] = $item['current_stock'] + $newArray[$item['detailId']]['batch_variation'];

        $newArray[$item['detailId']]['track_expire_date'] = $item['track_expire_date'];
        $newArray[$item['detailId']]['trackserialno'] = $item['trackserialno'];
        $newArray[$item['detailId']]['detail_remarks'] = $item['detail_remarks'];
        $newArray[$item['detailId']]['batches'][$item['batchId']]['batchId'] = $item['batchId'];
        $newArray[$item['detailId']]['batches'][$item['batchId']]['batch_no'] = $item['batch_no'];
        $newArray[$item['detailId']]['batches'][$item['batchId']]['qty'] = $item['qty'];
        $newArray[$item['detailId']]['batches'][$item['batchId']]['action'] = $item['action'];
        $newArray[$item['detailId']]['batches'][$item['batchId']]['before_qty'] = $item['before_qty'];
        $newArray[$item['detailId']]['batches'][$item['batchId']]['after_qty'] = $item['after_qty'];
        $newArray[$item['detailId']]['batches'][$item['batchId']]['expire_date'] = $item['expire_date'];
    }
//    debug($newArray);
    $tData['adjustmentList'] = $newArray;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $data['content'] = loadTemplate('stock_adjustment_detailed_report.tpl.php', $tData);
}

if ($action == 'stock_holders') {
    Users::isAllowed();
    $locationid = $_GET['locationid'];
    $clientid = $_GET['clientid'];
    $productid = $_GET['productid'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$locationid) $locationid = $_SESSION['member']['locationid'];
        $branchLocations = $ALL_BRANCH ? $Locations->locationList() : $Locations->locationList('', $_SESSION['member']['branchid']);
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($locationid, array_column($branchLocations, 'id'))) $locationid = $_SESSION['member']['locationid'];
    } else {
        $locationid = $_SESSION['member']['locationid'];
    }
    $tData['selected_location'] = $locationid;


    $title = [];
    if ($locationid) $title[] = "Location: " . $Locations->get($locationid)['name'];
    if ($clientid) $title[] = "Client: " . $Clients->get($clientid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    $tData['title'] = implode(' | ', $title);

    $tData['holders'] = $Proformas->clientStockHolder($clientid, $productid, $locationid, $branchid);
    $tData['holders_detailed'] = $Proformas->clientStockHolder($clientid, $productid, $locationid, $branchid, true);
//    debug($tData['holders_detailed']);

    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['defaultBranch'] = $Branches->get($_SESSION['member']['branchid']);
    $data['content'] = loadTemplate('stock_holders_list.tpl.php', $tData);
}

if ($action == 'serialnos') {
    $locationid = $_GET['locationid'];
    $initlocationid = $_GET['initlocationid'];
    $productid = $_GET['productid'];
    $number = $_GET['number'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];
    $status = $_GET['status'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$locationid) $locationid = $_SESSION['member']['locationid'];
        if ($ALL_BRANCH) {
            $branchLocations = $Locations->locationList();
        } else {
            $branchLocations = $Locations->locationList('', $_SESSION['member']['branchid']);
        }
        $tData['branchLocations'] = $branchLocations;
        if ($number) $locationid = '';
        if (!$number && !in_array($locationid, array_column($branchLocations, 'id'))) $locationid = $_SESSION['member']['locationid'];
    } else {
        $locationid = $_SESSION['member']['locationid'];
    }

    if ($number) {
        $productid = $fromdate = $todate = '';
    }
    $tData['location'] = $Locations->get($locationid);
    $title = [];
    if ($locationid) $title[] = "Location: " . $tData['location']['name'];
    if ($initlocationid) $title[] = "Initial Location: " . $Locations->get($initlocationid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($number) $title[] = "Number: " . $number;
    if ($status) $title[] = "Status: " . $status;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);
//    debug($locationid);
    $serialnos = SerialNos::$serialNoClass->getList($locationid, $productid, $number, $fromdate, $todate, $status, $initlocationid);
//    debug($serialnos);
    $tData['serialnos'] = $serialnos;
    $data['content'] = loadTemplate('serialno_report.tpl.php', $tData);
}


if ($action == 'sold_serialnos') {
    $locationid = $_GET['locationid'];
    $productid = $_GET['productid'];
    $userid = $_GET['userid'];
    $invoiceno = $_GET['invoiceno'];
    $number = $_GET['number'];
    $fromdate = $_GET['fromdate'] ?: TODAY;
    $todate = $_GET['todate'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $userid = $_SESSION['member']['id'];

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$locationid) $locationid = $_SESSION['member']['locationid'];
        if ($ALL_BRANCH) {
            $branchLocations = $Locations->locationList();
            if ($number) $locationid = '';
        } else {
            $branchLocations = $Locations->locationList('', $_SESSION['member']['branchid']);
        }
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($locationid, array_column($branchLocations, 'id'))) $locationid = $_SESSION['member']['locationid'];
    } else {
        $locationid = $_SESSION['member']['locationid'];
    }

    if ($number || $invoiceno) {
        $productid = $fromdate = $todate = '';
    }
    $tData['location'] = $Locations->get($locationid);
    $title = [];
    if ($invoiceno) $title[] = "Invoice no: " . $invoiceno;
    if ($userid) $title[] = "Sales Person: " . $Users->get($userid)['name'];
    if ($locationid) $title[] = "Location: " . $tData['location']['name'];
    if ($initlocationid) $title[] = "Initial Location: " . $Locations->get($initlocationid)['name'];
    if ($productid) $title[] = "Product: " . $Products->get($productid)['name'];
    if ($number) $title[] = "Number: " . $number;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $serialnos = SerialNos::$serialNoClass->getList($locationid, $productid, $number, $fromdate, $todate, 'sold', '', $userid, $invoiceno);
//    debug($serialnos);
    $tData['serialnos'] = $serialnos;
    $data['content'] = loadTemplate('sold_serialno_report.tpl.php', $tData);
}


if ($action == 'serialno_stock') {
    Users::isAllowed();
//    debug($_GET);

    $stocklocation = $_GET['stocklocation'];
    $stockdate = $_GET['stockdate'];
    $depart = $_GET['depart'];
    $categories = $_GET['category'];
    $brands = $_GET['brand'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $subcategoryid = $_GET['subcategoryid'];
    $print_pdf = isset($_GET['print_pdf']);
    $tData['pdf_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&print_pdf";
    $title = [];
    if ($depart) {
        $department = $Departments->get($depart);
        $title[] = "Department: " . $department['name'];
    }
    if ($categories) {
        $category = $Categories->get($categories);
        $title[] = "Tax Category: " . $category['name'] . " (" . $category['vat_percent'] . "%)";
    }
    if ($brands) {
        $brand = $Models->get($brands);
        $title[] = "Brand: " . $brand['name'];
    }
    if ($productid) {
        $product = $Products->get($productid);
        $title[] = "Product: " . $product['name'];
    }
    if ($productcategoryid) $title[] = "Category: " . $ProductCategories->get($productcategoryid)['name'];
    if ($subcategoryid) $title[] = "Subcategory: " . $ProductSubCategories->get($subcategoryid)['name'];
    if ($stockdate) $title[] = "Stock Date: " . fDate($stockdate);
    $tData['stockdate'] = $stockdate;
    $tData['title'] = implode(' | ', $title);

    $ALL_BRANCH = Users::can(OtherRights::view_all_branch_stock);
    $USER_BRANCH = Users::can(OtherRights::view_branch_stock);
    define('STOCK_LOCATIONS', $ALL_BRANCH || $USER_BRANCH);
    if ($ALL_BRANCH || $USER_BRANCH) {
        if (!$stocklocation) $stocklocation = $_SESSION['member']['locationid'];
        $branchLocations = $ALL_BRANCH ? $Locations->locationList() : $Locations->locationList('', $_SESSION['member']['branchid']);
        $tData['branchLocations'] = $branchLocations;
        if (!in_array($stocklocation, array_column($branchLocations, 'id'))) $stocklocation = $_SESSION['member']['locationid'];
    } else {
        $stocklocation = $_SESSION['member']['locationid'];
    }

    $tData['location'] = $location = $Locations->locationList($stocklocation)[0];
    $stockList = Stocks::$stockClass->calcStock(
        $location['id'], "",
        $stockdate, "", $productid, "", "", "",
        "", $categories, $brands, $depart, "", "", "", "",
        "", $with_expired = true, $group_batch = true, $productcategoryid, $subcategoryid,
        $with_stock = false, '', 'yes');
//    debug($stockList);

    foreach ($stockList as $index => $s) {
        $serialnos = SerialNos::$serialNoClass->getList($location['id'], $s['productid'], '', '', '', 'in_stock');
        $stockList[$index]['serialno_qty'] = count($serialnos);
    }
//        debug($stockList);

//    if ($print_pdf) {
//        $data['stockdate'] = $stockdate ?: TODAY;
//        $data['stocklist'] = $stockList;
//        $data['layout'] = 'stock_report_pdf_print.tpl.php';
//    } else {
    $tData['stocklist'] = $stockList;
    // debug($tData['stocklist']);
    $tData['depart'] = $Departments->getAllActive();
    $tData['categories'] = $Categories->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productCategories'] = $ProductCategories->getAllActive('name');
    $tData['productSubcategories'] = $ProductSubCategories->getAllActive('name');
    // debug($tData['stocklist']);
    $data['content'] = loadTemplate('serialno_stock.tpl.php', $tData);
//    }
}


//supplier
if ($action == 'supplier_payment_history') {
    Users::isAllowed();
    $supplierid = $_GET['supplierid'];
    $payment_method = $_GET['payment_method'];
    $issuedby = $_GET['issuedby'];
    $branchid = $_GET['branchid'] ?? $_SESSION['member']['branchid'];
    $fromdate = $_GET['fromdate'] ?? TODAY;
    $todate = $_GET['todate'];

    $title = [];
    $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($supplierid) $title[] = "Supplier: " . $Suppliers->get($supplierid)['name'];
    if ($payment_method) $title[] = "Payment method: " . $payment_method;
    if ($issuedby) $title[] = "Issued By: " . $Users->get($issuedby)['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $payments = $SupplierPayments->getList($supplierid, $branchid, $fromdate, $todate, $payment_method, $issuedby);
//    debug($payments);
    $tData['payments'] = $payments;
    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['paymentmethods'] = $Paymentmethods->getReceiving();
    $tData['currentBranch'] = $Branches->get($branchid);
    $data['content'] = loadTemplate('supplier_payment_history.tpl.php', $tData);
}

//client ledger
if ($action == 'client_ledger' || $action == 'client_ledger_sr') {
    Users::isAllowed();
    if ($action == 'client_ledger_sr') $tData['SR_MODE'] = $SR_MODE = true;
    $tData['basecurrency'] = $basecurrency = $Currencies->find(['base' => 'yes'])[0];
    $currencyid = $_GET['currencyid'] ?: $basecurrency['id'];

    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $acc_mng = $_SESSION['member']['id'];

    $title = [];
    if ($acc_mng) $title[] = "Account Manager: " . Users::$userClass->get($acc_mng)['name'];
    if ($clientid) $title[] = "Client: " . Clients::$clientClass->get($clientid)['name'];

    $tData['currency'] = $currency = $Currencies->get($currencyid);
    if ($currencyid) $title[] = "Currency: " . $currency['name'];

    $tData['title'] = implode(' | ', $title);

    $list = Sales::$saleClass->getSalesOutstanding('', $c['id'], '', true, $currencyid, '', '', $acc_mng, !$SR_MODE);
    $openingOutstanding = [];
    if (!$SR_MODE) {
        $openingOutstanding = ClientOpeningOutstandings::$staticClass->getList('', $c['id'], $currencyid, '', '', '', '', '', '', true, $acc_mng);
    }
    $clients = [];
    foreach ($list as $l) {
        $clients[$l['clientid']]['clientid'] = $l['clientid'];
        $clients[$l['clientid']]['clientname'] = $l['clientname'];
        $clients[$l['clientid']]['account_manager'] = $l['account_manager'];
        $clients[$l['clientid']]['bill_wise'] += $l['pending_amount'];

    }
    foreach ($openingOutstanding as $l) {
        $clients[$l['clientid']]['clientid'] = $l['clientid'];
        $clients[$l['clientid']]['clientname'] = $l['clientname'];
        $clients[$l['clientid']]['account_manager'] = $l['account_manager'];
        $clients[$l['clientid']]['bill_wise'] += $l['pending_amount'];

    }

    foreach ($clients as $index => $c) {
        $advanceBalance = array_sum(array_column(AdvancePayments::$advancePaymentClass->clientAdvanceBalances($c['clientid'], $currencyid), 'remaining_advance'));
        $clients[$index]['credit'] = $advanceBalance;
        $clients[$index]['debit'] = $clients[$index]['bill_wise'] - $advanceBalance;
        $clients[$index]['difference'] = $clients[$index]['bill_wise'] - $clients[$index]['debit'];
    }
    $tData['client_list'] = $clients;
    $tData['currencies'] = $Currencies->getAllActive();
    $_SESSION['pagetitle'] = CS_COMPANY . " - Client Ledger";
    $data['content'] = loadTemplate('client_ledger_list.tpl.php', $tData);
}

if ($action == 'generate_ledger_report') {
//    debug($_GET);
    $clientid = $_GET['clientid'];
    $currencyid = $_GET['currencyid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $tData['SR_MODE'] = $SR_MODE = isset($_GET['sr']);
    $print_pdf = isset($_GET['print_pdf']);
    $tData['pdf_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&print_pdf";
//    debug($tData['pdf_url']);

    if (Users::cannot(OtherRights::approve_other_credit_invoice) && Users::cannot(OtherRights::view_all_client_ledger)) {
        $client = $Clients->withAccManager($clientid, $_SESSION['member']['id']);
        if (!$client) {
            $_SESSION['error'] = "You are not the client account manager!";
            redirectBack();
        }
    }

    if (empty($currencyid)) $currencyid = Currencies::$currencyClass->find(['base' => 'yes'])[0]['id'];
    $currency = $Currencies->get($currencyid);
    if ($clientid) {
        $client = $Clients->get($clientid);
        $title[] = "Client: " . $client['name'];
    }
    if ($currencyid) $title[] = "Currency: " . $currency['name'];
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['title'] = implode(' | ', $title);

    $openingdate = date('Y-m-d', strtotime("$fromdate -1 day"));

    $openingBalance = $Sales->openingLedgerBalance($clientid, $currencyid, $openingdate, !$SR_MODE)[0];
    $ledgers = $Sales->ledgerAccount($clientid, $currencyid, $fromdate, $todate, !$SR_MODE);
//    debug($openingBalance);
    $total = [];
    if ($openingBalance['balance'] > 0) $total['debit'] = $openingBalance['balance'];
    if ($openingBalance['balance'] < 0) $total['credit'] = abs($openingBalance['balance']);
    foreach ($ledgers as $index => $l) {

        switch ($l['voucher_type']) {
            case 'advance payment':
            case 'receipt':
                $ledgers[$index]['voucherno'] = getTransNo($l['voucherno']);
                break;
            case 'credit note':
            case 'returned to client':
                $ledgers[$index]['voucherno'] = getCreditNoteNo($l['voucherno']);
                break;
        }

        if ($l['side'] == 'debit') $total['debit'] += $l['amount'];
        if ($l['side'] == 'credit') $total['credit'] += $l['amount'];
    }
    $total_balance = $total['debit'] - $total['credit'];
    if ($total_balance > 0) {
        $total['closing_debit'] = $total_balance;
    } else {
        $total['closing_credit'] = abs($total_balance);
    }

//    debug($total);


    if (!$print_pdf) {
        $tData['currency'] = $currency;
        $tData['opening_balance'] = $openingBalance;
        $tData['ledgers'] = $ledgers;
        $tData['total'] = $total;
        $_SESSION['pagetitle'] = CS_COMPANY . " - Ledger Report";
        $data['content'] = loadTemplate('ledger_account_report.tpl.php', $tData);
    } else {
        $data['client'] = $client;
        $data['fromdate'] = $fromdate;
        $data['todate'] = $todate ?: TODAY;
        $data['currency'] = $currency;
        $data['opening_balance'] = $openingBalance;
        $data['ledgers'] = $ledgers;
        $data['total'] = $total;
        $data['layout'] = 'ledger_account_print.tpl.php';
    }

}

if ($action == 'client_history') {
    Users::isAllowed();
    $search = htmlspecialchars($_GET['search']);
    $fromdate = $_GET['fromdate'] ?: date('Y-m-d', strtotime('-30 days'));
    $todate = $_GET['todate'];
    $createdby = '';
    if (Users::cannot(OtherRights::approve_other_credit_invoice)) $createdby = $_SESSION['member']['id'];

    $title = [];
    if ($search) $title[] = "Search: " . $search;
    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);
    if ($createdby) $title[] = "Issued By: " . $Users->get($createdby)['name'];
    $tData['title'] = implode(' | ', $title);

    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;
    $tData['clients'] = Clients::$clientClass->history('', $search, $createdby, $fromdate, $todate);
    $tData['brands'] = $Models->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['basecurrency'] = $Currencies->find(['base' => 'yes'])[0];
    $data['content'] = loadTemplate('client_history.tpl.php', $tData);
}

if ($action == 'supplier_history') {
    $createdby = '';
    if (Users::cannot(OtherRights::approve_other_grn)) $createdby = $_SESSION['member']['id'];
    $tData['suppliers'] = $Suppliers->history('', $createdby);

    $tData['products'] = $Products->getAllActive();
    $tData['brands'] = $Models->getAllActive();
    $tData['productcategories'] = $ProductCategories->getAllActive();
    $tData['locations'] = Users::can(OtherRights::approve_other_grn)
        ? $Locations->locationList('', '', 'active')
        : $Locations->locationList($_SESSION['member']['locationid']);
    $tData['branches'] = Users::can(OtherRights::approve_other_grn) ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id") : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $data['content'] = loadTemplate('supplier_history.tpl.php', $tData);
}

//ajax
if ($action == 'ajax_heldProducts') {
    $proformaid = $_GET['proformaid'];
    $salesid = $_GET['salesid'];
    $orderid = $_GET['orderid'];
    $obj->status = 'success';
    if ($held = Proformas::heldStock('', '', '', $proformaid, $salesid, $orderid)) {
        $obj->data = $held;
    } else {
        $obj->status = 'error';
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientInvoiceHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $createdby = Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        $total = [];
        if (!$clientid) throw new Exception("Choose client");
        $invoices = $Sales->salesList('', $createdby, $fromdate, $todate, $clientid, '', '', '', false, '', '', true);
        foreach ($invoices as $index => $i) {
            $base_paid_amount = $i['base_full_amount'] - $i['base_pending_amount'];
            $invoices[$index]['url'] = url('sales', 'view_invoice', ['salesid' => $i['salesid']]);
            $invoices[$index]['base_paid_amount'] = formatN($base_paid_amount);
            $invoices[$index]['date'] = fDate($i['doc'], 'd M Y H:i');
            if ($i['paymenttype'] == PAYMENT_TYPE_CASH) {
                $total['cash']['full_amount'] += $i['base_full_amount'];
                $total['cash']['pending_amount'] += $i['base_pending_amount'];
                $total['cash']['paid_amount'] += $base_paid_amount;
                $total['cash']['count']++;
            }
            if ($i['paymenttype'] == PAYMENT_TYPE_CREDIT) {
                $total['credit']['full_amount'] += $i['base_full_amount'];
                $total['credit']['pending_amount'] += $i['base_pending_amount'];
                $total['credit']['paid_amount'] += $base_paid_amount;
                $total['credit']['count']++;
            }
            $total['total']['full_amount'] += $i['base_full_amount'];
            $total['total']['pending_amount'] += $i['base_pending_amount'];
            $total['total']['paid_amount'] += $base_paid_amount;
            $total['total']['count']++;
//            debug($invoices[$index]);
        }
        $obj->total = $total;
        $obj->invoices = $invoices;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientInvoiceDetailHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $brandid = $_GET['brandid'];
    $createdby = Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$clientid) throw new Exception("Choose client");
        $products = Salesdetails::$saleDetailsClass->getDetailedList($productid, $clientid, $createdby, $productcategoryid, '',
            $brandid, '', $fromdate, $todate, '1');
        $obj->products = $products;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientReceiptHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $createdby = Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$clientid) throw new Exception("Choose client");
        $receipts = $Salespayments->detailedPaymentInfo('', $fromdate, $todate, '', '', '', '', true, '', $createdby, $clientid, true);
        $receipts = array_map(function ($r) {
            $r['id'] = getTransNo($r['id']);
            $r['issuedate'] = fDate($r['doc'], 'd M Y H:i');
            switch ($r['method']) {
                case PaymentMethods::BANK:
                    $r['method_text'] = "Bank Name: {$r['bankname']}, Bank Reference: {$r['bankreference']}";
                    break;
                case PaymentMethods::CHEQUE:
                    $r['method_text'] = "Cheque No: {$r['chequename']}, Cheque Type: {$r['chequetype']}";
                    break;
                case PaymentMethods::CREDIT_CARD:
                    $r['method_text'] = "Reference No: {$r['credit_cardno']}";
                    break;
                default:
                    $r['method_text'] = "";
                    break;
            }
            return $r;
        }, $receipts);
        $obj->receipts = $receipts;

    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientOrderHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $order_status = $_GET['order_status'];
    $createdby = Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$clientid) throw new Exception("Choose client");
        $orders = $Orders->getAllOrders('', $createdby, $order_status, $clientid, $fromdate, $todate);
        $orders = array_map(function ($o) {
            $o['orderdate'] = fDate($o['issueddate'], 'd M Y H:i');
            $o['valid_until'] = $o['order_status'] == 'pending' ? $o['valid_until'] : '';
            return $o;
        }, $orders);
        $obj->orders = $orders;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientProformaHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $proforma_status = $_GET['proforma_status'];
    $holding_stock = $_GET['holding_stock'];
    $createdby = Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$clientid) throw new Exception("Choose client");
        $proformas = Proformas::$proformaClass->proformaList('', $createdby, $proforma_status, $clientid, $holding_stock, $fromdate, $todate);
        $proformas = array_map(function ($p) {
            $p['proformadate'] = fDate($p['doc'], 'd M Y H:i');
            $p['hold_until'] = $p['isholding'] ? fDate($p['hold_until'], 'd M Y H:i') : '';
            $p['valid_until'] = $p['proforma_status'] == 'pending' ? fDate($p['valid_until'], 'd M Y H:i') : '';

            return $p;
        }, $proformas);
//        debug($proformas);
        $obj->proformas = $proformas;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientProformaDetailHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $brandid = $_GET['brandid'];
    $createdby = Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$clientid) throw new Exception("Choose client");
        $products = ProformaDetails::$proformaDetailsClass->getList($productid, $clientid, $createdby, $productcategoryid, '',
            $brandid, '', $fromdate, $todate);
//        debug($products);
        $obj->products = $products;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientAdvanceHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $createdby = Users::can(OtherRights::approve_other_credit_invoice) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$clientid) throw new Exception("Choose client");
        $advances = $AdvancePayments->paymentList('', $clientid, $fromdate, $todate, '', '', $createdby);
        $advances = array_map(function ($r) {
            $r['id'] = getTransNo($r['id']);
            $r['issuedate'] = fDate($r['doc'], 'd M Y H:i');
            switch ($r['methodname']) {
                case PaymentMethods::BANK:
                    $r['method_text'] = "Bank Name: {$r['bankname']}, Bank Reference: {$r['bankreference']}";
                    break;
                case PaymentMethods::CHEQUE:
                    $r['method_text'] = "Cheque No: {$r['chequename']}, Cheque Type: {$r['chequetype']}";
                    break;
                case PaymentMethods::CREDIT_CARD:
                    $r['method_text'] = "Reference No: {$r['credit_cardno']}";
                    break;
                case PaymentMethods::FROM_CREDIT_NOTE:
                    $r['method_text'] = "Credit Note " . getCreditNoteNo($r['srid']);
                    break;
                default:
                    $r['method_text'] = "";
                    break;
            }
            return $r;
        }, $advances);
//        debug($advances);
        $obj->advances = $advances;

    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getClientCreditNoteHistory') {
    $clientid = $_GET['clientid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $createdby = Users::can(OtherRights::approve_other_credit_note) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$clientid) throw new Exception("Choose client");
        $salesreturns = $SalesReturns->getList('', '', '', '', 'approved', $clientid, $createdby, $fromdate, $todate);
        $salesreturns = array_map(function ($r) {
            $r['url'] = url('sales_returns', 'view', ['returnno' => $r['id']]);
            $r['id'] = getCreditNoteNo($r['id']);
            if ($r['type'] == SalesReturns::TYPE_ITEM) {
                $r['type'] = "Item return";
            } elseif ($r['type'] == SalesReturns::TYPE_PRICE) {
                $r['type'] = "Price Change";
            } else {
                $r['type'] = "Full invoice return";
            }
            $r['issuedate'] = fDate($r['doc'], 'd M Y H:i');
            switch ($r['return_method']) {
                case PaymentMethods::CASH:
                    $r['method_text'] = "Cash";
                    break;
                case PaymentMethods::BANK:
                    $r['method_text'] = "Bank Name: {$r['bankname']}, Bank Reference: {$r['bankreference']}";
                    break;
                case PaymentMethods::CHEQUE:
                    $r['method_text'] = "Cheque No: {$r['chequename']}, Cheque Type: {$r['chequetype']}";
                    break;
                case PaymentMethods::CREDIT_CARD:
                    $r['method_text'] = "Reference No: {$r['credit_cardno']}";
                    break;
                case PaymentMethods::FROM_CREDIT_NOTE:
                    $r['method_text'] = "Credit Note " . getCreditNoteNo($r['srid']);
                    break;
                default:
                    $r['method_text'] = "";
                    break;
            }
            if ($r['apid']) {
                $r['return_method'] = "Advance Payment";
                $r['return_amount'] = $r['advance_amount'];
                $r['method_text'] = "Advance Payment " . formatN($r['advance_amount']);
            }
            return $r;
        }, $salesreturns);
//        debug($salesreturns);
        $obj->salesreturns = $salesreturns;

    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getSupplierGRNHistory') {
    $supplierid = $_GET['supplierid'];
    $branchid = $_GET['branchid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $createdby = Users::can(OtherRights::approve_other_grn) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$supplierid) throw new Exception("Choose supplier");
        $outstandingGrns = $GRN->withPaymentAmount($supplierid, "", "", $branchid,
            "", "", "", true, true, true, $createdby);
        $totalOutstanding = array_sum(array_column($outstandingGrns, 'outstanding_amount'));
        $grns = $GRN->withPaymentAmount($supplierid, "", "", $branchid,
            "", $fromdate, $todate, false, true, true, $createdby, false);

        foreach ($grns as $index => $grn) {
            $grns[$index]['lpono'] = $grn['lpono'] ?: '';
            $grns[$index]['supplier_payment'] = $grn['supplier_payment'] ? 'Yes' : 'No';
            $grns[$index]['type'] = $grn['source'] == 'opening' ? 'Opening Outstanding' : 'Normal';
            $grns[$index]['locationname'] = $grn['locationname'] . ' - ' . $grn['branchname'];
            $grns[$index]['issuedate'] = fDate($grn['doc'], 'd M Y H:i');
            $grns[$index]['full_amount'] = formatN($grn['full_amount']);
            $grns[$index]['paid_amount'] = formatN($grn['paid_amount']);
            $grns[$index]['outstanding_amount'] = formatN($grn['outstanding_amount']);
        }
//        debug($grns);
        $obj->outstanding_amount = formatN($totalOutstanding);
        $obj->grns = $grns;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getSupplierGRNDetailHistory') {
    $supplierid = $_GET['supplierid'];
    $locationid = $_GET['locationid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $brandid = $_GET['brandid'];
    $createdby = Users::can(OtherRights::approve_other_grn) ? '' : $_SESSION['member']['id'];
    $locationid = Users::can(OtherRights::approve_other_grn) ? $locationid : $_SESSION['member']['locationid'];
    $obj->status = "success";
    try {
        if (!$supplierid) throw new Exception("Choose supplier");
        $grns = $GRN->getGrnWithReturnQty('', true, $locationid, $productid, $productcategoryid, $brandid, $fromdate, $todate, '', '', $supplierid);
//        debug($grns);
        $obj->grns = $grns;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getSupplierLPOHistory') {
    $supplierid = $_GET['supplierid'];
    $locationid = $_GET['locationid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $createdby = Users::can(OtherRights::approve_other_grn) ? '' : $_SESSION['member']['id'];
    $locationid = Users::can(OtherRights::approve_other_grn) ? $locationid : $_SESSION['member']['locationid'];
    $obj->status = "success";
    try {
        if (!$supplierid) throw new Exception("Choose supplier");
        $lpos = $LPO->getList('', $createdby, $fromdate, $todate, $supplierid, $locationid, '', 'approved');
        foreach ($lpos as $index => $lpo) {
            $lpos[$index]['grnnumber'] = $lpos[$index]['grnnumber'] ?: '';
            $lpos[$index]['locationname'] = $lpo['locationname'] . ' - ' . $lpo['branchname'];
            $lpos[$index]['issuedate'] = fDate($lpo['issuedate'], 'd M Y H:i');
            $lpos[$index]['full_amount'] = formatN($lpo['full_amount']);
        }
//        debug($lpos);
        $obj->lpos = $lpos;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getSupplierLPODetailHistory') {
    $supplierid = $_GET['supplierid'];
    $locationid = $_GET['locationid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $productid = $_GET['productid'];
    $productcategoryid = $_GET['productcategoryid'];
    $brandid = $_GET['brandid'];
    $createdby = Users::can(OtherRights::approve_other_grn) ? '' : $_SESSION['member']['id'];
    $locationid = Users::can(OtherRights::approve_other_grn) ? $locationid : $_SESSION['member']['locationid'];
    $obj->status = "success";
    try {
        if (!$supplierid) throw new Exception("Choose supplier");
        $lpodetails = $LPODetails->getList('', $supplierid, $createdby, $productid, $productcategoryid, $brandid, $fromdate, $todate, 'approved', $locationid);
        foreach ($lpodetails as $index => $ld) {
            $lpodetails[$index]['locationnname'] = $ld['locationnname'] . ' - ' . $ld['branchname'];
            $lpodetails[$index]['issuedate'] = fDate($ld['issuedate'], 'd M Y H:i');
            $lpodetails[$index]['incamount'] = formatN($ld['incamount']);
        }
//        debug($lpodetails);
        $obj->lpodetails = $lpodetails;
    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}

if ($action == 'ajax_getSupplierPaymentHistory') {
    $supplierid = $_GET['supplierid'];
    $branchid = $_GET['branchid'];
    $fromdate = $_GET['fromdate'];
    $todate = $_GET['todate'];
    $createdby = Users::can(OtherRights::approve_other_grn) ? '' : $_SESSION['member']['id'];
    $obj->status = "success";
    try {
        if (!$supplierid) throw new Exception("Choose client");
        $payments = $SupplierPayments->getList($supplierid, $branchid, $fromdate, $todate, '', $createdby);
        $payments = array_map(function ($r) {
            $r['total_amount'] = formatN($r['total_amount']);
            $r['issuedate'] = fDate($r['doc'], 'd M Y H:i');
            switch ($r['method']) {
                case PaymentMethods::BANK:
                    $r['method_text'] = "Bank Name: {$r['bankname']}, Bank Reference: {$r['bankreference']}";
                    break;
                case PaymentMethods::CHEQUE:
                    $r['method_text'] = "Cheque No: {$r['chequename']}, Cheque Type: {$r['chequetype']}";
                    break;
                case PaymentMethods::CREDIT_CARD:
                    $r['method_text'] = "Reference No: {$r['credit_cardno']}";
                    break;
                default:
                    $r['method_text'] = "";
                    break;
            }
            return $r;
        }, $payments);
//        debug($payments);

        $obj->payments = $payments;

    } catch (Exception $e) {
        $obj->status = "error";
        $obj->msg = $e->getMessage();
    }
    $data['content'] = $obj;
}
