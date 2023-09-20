<?


class Expenses extends model
{
    var $table = 'expenses';
    static $expenseClass = null;

    function __construct()
    {
        self::$expenseClass = $this;
    }

    function issuedList($expenseid = "", $salesid = "", $userid = "", $expense_status = "", $type = "", $fromdate = "", $todate = "", $branchid = "", $group = true, $currencyid = "")
    {
        $sql = "select e.*,
                       c.name        as currencyname,
                       c.description as currency_description,
                       sales.receipt_no,
                       branches.name as branchname,
                       branches.tally_cash_ledger,
                       branches.cost_center,
                       users.name    as username,
                       modifier.name as modifier,
                       approver.name as approver,
                       ea.id         as detailId,
                       ea.name       as attrname,
                       ea.ledgername,
                       ed.attributeid,
                       ed.amount,
                       case
                           when e.status != 'active' then 'canceled'
                           when approver.id is not null then 'approved'
                           else 'not_approved'
                           end       as expense_status
                from expenses e
                         inner join currencies c on c.id = e.currencyid
                         inner join expense_details ed on e.id = ed.expenseid
                         inner join expenses_attributes ea on ea.id = ed.attributeid
                         inner join users on e.createdby = users.id
                         inner join branches on e.branchid = branches.id
                         left join users modifier on modifier.id = e.modifiedby
                         left join users approver on approver.id = e.approvedby
                         left join sales on e.saleid = sales.id
                where 1 = 1";
        if ($expenseid) $sql .= " and e.id = $expenseid";
        if ($salesid) $sql .= " and e.saleid = $salesid";
        if ($currencyid) $sql .= " and c.id = $currencyid";
        if ($type) $sql .= " and ed.attributeid = $type";
        if ($branchid) $sql .= " and e.branchid = $branchid";
        if ($userid) $sql .= " and e.createdby = $userid";
        if ($fromdate) $sql .= " and date_format(e.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(e.doc,'%Y-%m-%d') <= '$todate'";

        $sql .= " having 1=1";
        if ($expense_status) $sql .= " and expense_status = '$expense_status'";

        $sql .= " order by e.doc desc";
//        debug($sql);
        $expenses = fetchRows($sql);
        if (!$group) {
            return $expenses;
        } else {
            //group expense details
            $groupedArray = [];
            foreach ($expenses as $index => $R) {
                $groupedArray[$R['id']]['id'] = $R['id'];
                $groupedArray[$R['id']]['branchid'] = $R['branchid'];
                $groupedArray[$R['id']]['branchname'] = $R['branchname'];
                $groupedArray[$R['id']]['tally_cash_ledger'] = $R['tally_cash_ledger'];
                $groupedArray[$R['id']]['cost_center'] = $R['cost_center'];
                $groupedArray[$R['id']]['currencyname'] = $R['currencyname'];
                $groupedArray[$R['id']]['currency_description'] = $R['currency_description'];
                $groupedArray[$R['id']]['total_amount'] = $R['total_amount'];
                $groupedArray[$R['id']]['paidto'] = $R['paidto'];
                $groupedArray[$R['id']]['verificationcode'] = $R['verificationcode'];
                $groupedArray[$R['id']]['remarks'] = $R['remarks'];
                $groupedArray[$R['id']]['invoiceno'] = $R['invoiceno'];
                $groupedArray[$R['id']]['saleid'] = $R['saleid'];
                $groupedArray[$R['id']]['receipt_no'] = $R['receipt_no'];
                $groupedArray[$R['id']]['createdby'] = $R['createdby'];
                $groupedArray[$R['id']]['username'] = $R['username'];
                $groupedArray[$R['id']]['doc'] = $R['doc'];
                $groupedArray[$R['id']]['modifiedby'] = $R['modifiedby'];
                $groupedArray[$R['id']]['approvedby'] = $R['approvedby'];
                $groupedArray[$R['id']]['approval_date'] = $R['approval_date'];
                $groupedArray[$R['id']]['auto_approve'] = $R['auto_approve'];
                $groupedArray[$R['id']]['approver'] = $R['approver'];
                $groupedArray[$R['id']]['expense_status'] = $R['expense_status'];
                $groupedArray[$R['id']]['transfer_tally'] = $R['transfer_tally'];
                $groupedArray[$R['id']]['tally_post'] = $R['tally_post'];
                $groupedArray[$R['id']]['tally_trxno'] = $R['tally_trxno'];
                $groupedArray[$R['id']]['tally_message'] = $R['tally_message'];
                $groupedArray[$R['id']]['details'][$R['detailId']]['detailId'] = $R['detailId'];
                $groupedArray[$R['id']]['details'][$R['detailId']]['attributeid'] = $R['attributeid'];
                $groupedArray[$R['id']]['details'][$R['detailId']]['attrname'] = $R['attrname'];
                $groupedArray[$R['id']]['details'][$R['detailId']]['ledgername'] = $R['ledgername'];
                $groupedArray[$R['id']]['details'][$R['detailId']]['amount'] = $R['amount'];
            }
            return array_values($groupedArray);
        }
    }

    static function tallyPost($expenseno)
    {
        $expense = Expenses::$expenseClass->issuedList($expenseno)[0];
        if (!$expense['approvedby']) return ['status' => 'error', 'msg' => 'Expense not approved'];

        $expenseno = "E-" . getTransNo($expenseno);
        if (!$expense['tally_trxno']) {
            $tally_trxno = unique_token(60) . "-$expenseno";
            Expenses::$expenseClass->update($expense['id'], ['tally_trxno' => $tally_trxno]);
        } else {
            $tally_trxno = $expense['tally_trxno'];
        }

        $post_data = [];
        $post_data['trxno'] = $tally_trxno;
        $post_data['expenseno'] = $expenseno;
        $post_data['date'] = fDate($expense['doc'], 'Ymd');
        $post_data['tally_cash_ledger'] = htmlspecialchars($expense['tally_cash_ledger']);
        $post_data['cost_center'] = htmlspecialchars($expense['cost_center']);
        $post_data['total_amount'] = $expense['total_amount'];

        $post_data['narration'] = $expense['remarks'];
        $post_data['narration'] .= "\n Paid To: ".$expense['paidto'];
        $post_data['narration'] .= "\n Invoice No: ".$expense['invoiceno'];
        $post_data['narration'] .= "\n Verification Code: ".$expense['verificationcode'];
        $post_data['narration'] = htmlspecialchars($post_data['narration']);//clean narration

        foreach ($expense['details'] as $d) {
            $post_data['details'][]=[
                'ledgername'=>htmlspecialchars($d['ledgername']),
                'amount'=>$d['amount'],
            ];
        }
//        debug($post_data);
        $result = createExpenseVoucher($post_data,true);
        global $db_connection;
        mysqli_begin_transaction($db_connection);
        if ($result['status'] == 'success') {
            TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $tally_trxno]);
            //save transfer
            //dr_ledgername
            $partno = 1;
            foreach ($post_data['details'] as $d) {
                TallyTransfers::$tallyTransferclass->insert([
                    'date' => $post_data['date'],
                    'partno' => $partno,
                    'ledgername' => $d['ledgername'],
                    'dr_cr' => 'dr',
                    'amount' => $d['amount'],
                    'reference' => $post_data['expenseno'],
                    'voucher_type' => 'Payment',
                    'sourceid' => $expense['id'],
                    'sourcetable' => 'expenses',
                    'trxno' => $tally_trxno,
                    'createdby' => $_SESSION['member']['id'],
                ]);
                $partno++;
            }

            //cr ledger
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => $post_data['tally_cash_ledger'],
                'dr_cr' => 'cr',
                'amount' => $post_data['total_amount'],
                'reference' => $post_data['expenseno'],
                'voucher_type' => 'Payment',
                'sourceid' => $expense['id'],
                'sourcetable' => 'expenses',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            Expenses::$expenseClass->update($expense['id'], ['tally_post' => 1, 'tally_message' => $result['msg']]);
        } else {
            Expenses::$expenseClass->update($expense['id'], ['tally_message' => $result['msg']]);
        }
        mysqli_commit($db_connection);

        return $result;
    }
}