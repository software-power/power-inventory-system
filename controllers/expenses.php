<?php
if ($action == 'expenses_attribute_list') {
    Users::isAllowed();
    $tData['attribute_list'] = $ExpensesAttributes->getAll();
    $data['content'] = loadTemplate('expenses_attr_list.tpl.php', $tData);
}

if ($action == 'expense_attr_save') {
//    debug($_POST);
    $user = $_SESSION['member'];
    $attr = $_POST['attr'];

    validate($attr);

    if (!empty($attr['id'])) {
        //edit
        $attr['modifiedby'] = $user['id'];
        $ExpensesAttributes->update($attr['id'], $attr);
        $_SESSION['message'] = 'Expense Attribute updated successfully';
    } else {
        //new
        $attr['createdby'] = $user['id'];
        $ExpensesAttributes->insert($attr);
        $_SESSION['message'] = 'Expense Attribute saved successfully';
    }
    redirect('expenses', 'expenses_attribute_list');
}

if ($action == 'expense_attr_delete') {
    $id = $_POST['id'];
    if (!empty($id = intval($_POST['id']))) {
        $ExpensesAttributes->delete($id);
        $_SESSION['message'] = 'Expense Attribute deleted successfully';
    }
    redirect('expenses', 'expenses_attribute_list');
}


if ($action == 'issued_list') {
//    debug($_POST);
    Users::isAllowed();

    $branchid = $_GET['search']['branchid'];
    $userid = $_GET['search']['userid'];
    $expense_status = $_GET['search']['expense_status'];
    $fromdate = $_GET['search']['fromdate'] ?: TODAY;
    $todate = $_GET['search']['todate'];

    $expenseid = $_GET['expenseid'];
    if ($expenseid) $fromdate = $todate = '';

    if (Users::cannot(OtherRights::approve_other_expense)) $userid = $_SESSION['member']['id'];

    $title = [];

    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['branchid'] = $branchid;

    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($userid) {
        $tData['creator'] = $creator = $Users->get($userid);
        $title[] = "Issued By: " . $creator['name'];
    }
    if ($expense_status) $title[] = "Status: " . $expense_status;

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $issuedList = $Expenses->issuedList($expenseid, "", $userid, $expense_status, '', $fromdate, $todate, $branchid);
    $tData['issuedList'] = $issuedList;
    $data['content'] = loadTemplate('issued_expense_list.tpl.php', $tData);
}

if ($action == 'issued_list_detailed') {
//    debug($_POST);
    Users::isAllowed();

    $branchid = $_GET['search']['branchid'];
    $userid = $_GET['search']['userid'];
    $attributeid = $_GET['search']['attributeid'];
    $expense_status = $_GET['search']['expense_status'];
    $fromdate = $_GET['search']['fromdate'] ?: TODAY;
    $todate = $_GET['search']['todate'];

    $expenseid = $_GET['expenseid'];
    if ($expenseid) $fromdate = $todate = '';

    if (Users::cannot(OtherRights::approve_other_expense)) $userid = $_SESSION['member']['id'];

    $title = [];

    $tData['branches'] = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
    $tData['branchid'] = $branchid;
    $tData['expense_attributes'] = $ExpensesAttributes->getAllActive();

    if ($branchid) $title[] = "Branch: " . $Branches->get($branchid)['name'];
    if ($userid) {
        $tData['creator'] = $creator = $Users->get($userid);
        $title[] = "Issued By: " . $creator['name'];
    }
    if ($expense_status) $title[] = "Status: " . $expense_status;

    if ($fromdate) $title[] = "From: " . fDate($fromdate);
    if ($todate) $title[] = "To: " . fDate($todate);

    $tData['title'] = implode(' | ', $title);
    $tData['fromdate'] = $fromdate;
    $tData['todate'] = $todate;

    $issuedList = $Expenses->issuedList($expenseid, "", $userid, $expense_status, $attributeid, $fromdate, $todate, $branchid, false);
//    debug($issuedList);
    $tData['issuedList'] = $issuedList;

    $data['content'] = loadTemplate('issued_expense_detailed_list.tpl.php', $tData);
}

if ($action == 'issue_expense') {
    Users::can(OtherRights::issue_expense, true);
    $tData['branches'] = Users::can(OtherRights::approve_other_expense)
        ? $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id")
        : $Branches->find(['id' => $_SESSION['member']['branchid']]);
    $tData['currencies'] = $Currencies->getAllActive();
//    debug($tData['currencies']);


    $expenseId = intval($_GET['voucherno']);//from issue expense
    $salesid = intval($_POST['salesid']); //from add sale expense

    if (!empty($expenseId)) {
        $expense = $Expenses->issuedList($expenseId)[0];
        if ($expense['expense_status'] == 'approved' && !(IS_ADMIN || $expense['approvedby'] == $_SESSION['member']['id'])) {
            $_SESSION['error'] = "Expenses already approved cant be edited!";
            redirectBack();
        }
        if ($expense['expense_status'] == 'canceled') {
            $_SESSION['error'] = "Expenses already canceled cant be edited!";
            redirectBack();
        }
        $tData['expense'] = $expense;
        if ($expense['saleid']) $salesid = $expense['saleid'];  //in case is sale expense
    }

    if (!empty($salesid)) {
        $sale = $Sales->get($salesid);
        $location = $Locations->get($sale['locationid']);
        $tData['branches'] = $Branches->find(['id' => $location['branchid']]);

        //previous expenses
        $prevExpense = $Expenses->issuedList("", $salesid, '', 'not_approved')[0];
        if ($prevExpense['expense_status'] == 'approved') {
            $_SESSION['error'] = "Expenses already approved cant be edited!";
            redirectBack();
        }
        if ($prevExpense) $tData['expense'] = $prevExpense;
        $tData['sale'] = $sale;
    }
//debug($tData['expense']);
    $data['content'] = loadTemplate('issue_expense_edit.tpl.php', $tData);
}

if ($action == 'save_issued_expense') {
    Users::can(OtherRights::issue_expense, true);
//    debug($_POST);

    $expense = $_POST['expense'];
    $expense['total_amount'] = removeComma($expense['total_amount']);//removing comma from the amount
    $attrIds = $_POST['attrId'];
    $amount = $_POST['amount'];

    validate($expense);
    validate($attrIds);
    validate($amount);

    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $expense['transfer_tally'] = CS_TALLY_TRANSFER;
        $amount = array_map(function ($a) {
            return removeComma($a);
        }, $amount);
        $total_amount = array_sum($amount);

        if ($total_amount != $expense['total_amount']) throw new Exception("Expense total and detail total dont match!");
//debug($expense);
        if (empty($expense['id'])) {
            $expense['createdby'] = $_SESSION['member']['id'];
            if (!CS_EXPENSE_APPROVAL) {
                $expense['approvedby'] = $_SESSION['member']['id'];
                $expense['approval_date'] = TIMESTAMP;
                $expense['auto_approve'] = 1;
            }
            if(!$Expenses->insert($expense)) throw new Exception("Error creating expense!");
            $expenseid = $Expenses->lastId();
        } else {
            $expenseid = $expense['id'];
            $old_expense = $Expenses->issuedList($expenseid)[0];
            if ($old_expense['expense_status'] == 'approved' && !(IS_ADMIN || $old_expense['approvedby'] == $_SESSION['member']['id'])) {
                $_SESSION['error'] = "Expenses already approved cant be edited!";
                redirectBack();
            }
            //clear old details
            $ExpenseDetails->deleteWhere(['expenseid' => $expense['id']]);
            $expense['tally_post'] = 0;
            $expense['modifiedby'] = $_SESSION['member']['id'];
            $Expenses->update($expense['id'], $expense);
        }

        foreach ($attrIds as $index => $id) {
            if(!$ExpenseDetails->insert([
                'expenseid' => $expenseid,
                'attributeid' => $id,
                'amount' => removeComma($amount[$index])
            ])) throw new Exception("Error inserting details");
        }
        mysqli_commit($db_connection);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        $_SESSION['error'] = $e->getMessage();
        $_SESSION['delay'] = 4000;
        redirectBack();
    }

    if (CS_TALLY_DIRECT && $expense['transfer_tally']) {
        $ping = pingTally();
        if ($ping['status'] == 'error') {
            $_SESSION['error'] = $ping['msg'];
        } else {
            $result = Expenses::tallyPost($expenseid);
            if ($result['status'] == 'success') $_SESSION['message'] .= "\n" . $result['msg'];
            if ($result['status'] == 'error') $_SESSION['error'] .= "\n" . $result['msg'];
        }
    }

    $_SESSION['message'] .= "\nExpense " . ($expense['id'] ? 'updated' : 'saved') . " Successfully";
    redirect('expenses', 'issued_list');
}

if ($action == 'approve_expense') {
    if (Users::cannot(OtherRights::approve_other_expense) && Users::cannot(OtherRights::approve_expense))
        redirect('authenticate', 'access_page', ['right_action' => base64_encode('Approve Expense')]);

    $expenseid = $_POST['id'];
    if (!$expense = $Expenses->get($expenseid)) {
        $_SESSION['error'] = "Expense not found!";
        redirectBack();
    }
    $expense = $Expenses->issuedList($expenseid)[0];
    if ($expense['expense_status'] == 'approved') {
        $_SESSION['error'] = "Expense already approved";
        redirectBack();
    }
    if ($expense['expense_status'] == 'canceled') {
        $_SESSION['error'] = "Expense already canceled";
        redirectBack();
    }

    $Expenses->update($expenseid, [
        'approvedby' => $_SESSION['member']['id'],
        'approval_date' => TIMESTAMP
    ]);
    $_SESSION['message'] = "Expense approved";

    if (CS_TALLY_DIRECT && $expense['transfer_tally']) {
        $ping = pingTally();
        if ($ping['status'] == 'error') {
            $_SESSION['error'] = $ping['msg'];
        } else {
            $result = Expenses::tallyPost($expense['id']);
            if ($result['status'] == 'success') $_SESSION['message'] .= "\n" . $result['msg'];
            if ($result['status'] == 'error') $_SESSION['error'] .= "\n" . $result['msg'];
        }
    }
    redirectBack();
}

if ($action == 'cancel_expense') {
    Users::can(OtherRights::cancel_expense, true);
    $expenseid = $_POST['id'];
    if (!$expense = $Expenses->get($expenseid)) {
        $_SESSION['error'] = "Expense not found!";
        redirectBack();
    }
    $expense = $Expenses->issuedList($expenseid)[0];
    if ($expense['expense_status'] == 'approved') {
        $_SESSION['error'] = "Expense already approved cant be canceled!";
        redirectBack();
    }

    $Expenses->update($expenseid, ['status' => 'inactive']);
    $_SESSION['message'] = "Expense canceled";
    redirectBack();
}

if ($action == 'print_expense') {
    $expenseid = $_GET['id'];
    if (!$expense = $Expenses->get($expenseid)) {
        $_SESSION['error'] = 'Expense not found!';
        redirectBack();
    }
    $expense = $Expenses->issuedList($expenseid)[0];
    if ($expense['expense_status'] != 'approved') {
        $_SESSION['error'] = 'Expense not approved!';
        redirectBack();
    }
    $data['expense'] = $expense;
    $data['layout'] = "print_expense_voucher.tpl.php";
}

if ($action == 'post_tally') {
    $expenseno = $_GET['expenseno'];

    $ping = pingTally();
    if ($ping['status'] == 'error') {
        $_SESSION['error'] = $ping['msg'];
    } else {
        $result = Expenses::tallyPost($expenseno);
        if ($result['status'] == 'success') $_SESSION['message'] = $result['msg'];
        if ($result['status'] == 'error') $_SESSION['error'] = $result['msg'];
    }
    redirectBack();
}

if ($action == 'ajax_getexpensesAttributes') {
    $icData = $ExpensesAttributes->search($_GET['search']['term']);
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
        $obj->test = 'No results';
        $obj->id = 0;
        $response['results'][] = $obj;
    }
    $data['content'] = $response;
}

if ($action == 'ajax_getIssuedExpense') {
//    debug($_GET);
    $salesid = $_GET['salesid'];
    $expense = $Expenses->issuedList("", $salesid, '', 'approved');
    if (!empty($expense)) {
        $obj->status = 'found';
        $obj->details = $expense[0];
    } else {
        $obj->status = 'not found';
    }
    $data['content'] = $obj;
}