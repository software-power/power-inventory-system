<?


class AdvancePayments extends model
{
    var $table = "advance_payments";
    static $advancePaymentClass = null;

    function __construct()
    {
        self::$advancePaymentClass = $this;
    }

    function paymentList($id = "", $clientid = "", $fromDate = "", $toDate = "", $payment_method = "", $branchid = "", $receiver = "", $currencyid = "", $eaccount = "")
    {
        $sql = "select ap.*,
                       ap.amount - IFNULL(used_advances.amount, 0) as remaining_advance,
                       IFNULL(used_advances.amount, 0)             as used_advance,
                       currencies.name                             as currencyname,
                       currencies.description                      as currency_description,
                       clients.name                                as clientname,
                       clients.ledgername                          as clientledgername,
                       branches.name                               as branchname,
                       branches.tally_cash_ledger,
                       branches.cost_center,
                       pm.name                                     as methodname,
                       e_account.name                              as electronic_account,
                       e_account.ledgername                        as electronic_account_ledgername,
                       users.name                                  as creator,
                       sr.id                                       as srid,
                       banks.name                                  as bank_name,
                       banks.accno                                 as bank_accno,
                       banks.ledgername                            as bank_ledgername,
                       sr.id                                       as srid
                from advance_payments ap
                         inner join clients on ap.clientid = clients.id
                         inner join branches on ap.branchid = branches.id
                         inner join currencies on ap.currencyid = currencies.id
                         inner join paymentmethods pm on ap.pmethod_id = pm.id
                         inner join users on ap.createdby = users.id
                         left join sales_returns sr on ap.srid = sr.id
                         left join banks on ap.bankid = banks.id
                         left join electronic_accounts e_account on ap.eaccid = e_account.id
                         left join
                     (
                         select spua.advance_id, sum(spua.amount) as amount
                         from sales_payment_used_advances spua
                         group by spua.advance_id
                     ) used_advances on used_advances.advance_id = ap.id
                where 1 = 1";
        if ($id) $sql .= " and ap.id = $id";
        if ($receiver) $sql .= " and ap.createdby = $receiver";
        if ($currencyid) $sql .= " and ap.currencyid = $currencyid";
        if ($clientid) $sql .= " and ap.clientid = $clientid";
        if ($branchid) $sql .= " and ap.branchid = $branchid";
        if ($eaccount) $sql .= " and e_account.id = $eaccount";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        if ($fromDate) $sql .= " and date_format(ap.doc,'%Y-%m-%d') >= '$fromDate'";
        if ($toDate) $sql .= " and date_format(ap.doc,'%Y-%m-%d') <= '$toDate'";
        $sql .= " order by ap.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function advancePaymentBalances($clientid = "", $currencyid = "", $with_balance = true, $fromDate = "", $toDate = "", $payment_method = "", $branchid = "", $receiver = "")
    {
        $sql = "select ap.*,
                       ap.amount - IFNULL(used_advances.amount, 0) as remaining_advance,
                       IFNULL(used_advances.amount, 0)             as advance_used,
                       currencies.name                             as currencyname,
                       currencies.description                      as currency_description,
                       clients.name                                as clientname,
                       pm.name                                     as methodname,
                       users.name                                  as creator
                from advance_payments ap
                         inner join currencies on ap.currencyid = currencies.id
                         inner join clients on ap.clientid = clients.id
                         inner join paymentmethods pm on ap.pmethod_id = pm.id
                         inner join users on ap.createdby = users.id
                         left join
                     (
                         select spua.advance_id, sum(spua.amount) as amount
                         from sales_payment_used_advances spua
                         group by spua.advance_id
                     ) used_advances on used_advances.advance_id = ap.id
                where 1=1";
        if ($with_balance) $sql .= " and ap.amount - IFNULL(used_advances.amount, 0) > 0";
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($currencyid) $sql .= " and currencies.id = $currencyid";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        if ($branchid) $sql .= " and ap.branchid = $branchid";
        if ($receiver) $sql .= " and ap.createdby = $receiver";
        if ($fromDate) $sql .= " and date_format(ap.doc,'%Y-%m-%d') >= '$fromDate'";
        if ($toDate) $sql .= " and date_format(ap.doc,'%Y-%m-%d') <= '$toDate'";
//        debug($sql);
        return fetchRows($sql);
    }

    function clientAdvanceBalances($clientid = "", $currencyid = "")
    {
        $clientid = $clientid ? " and clients.id = $clientid" : "";

        if ($currencyid) {
            if (is_array($currencyid)) {
                $currencyid = " and currencies.id in (" . implode(',', $currencyid) . ")";
            } else {
                $currencyid = " and currencies.id = $currencyid";
            }
        }
        $sql = "select clientid,
                       clientname,
                       currencyid,
                       currencyname,
                       currency_description,
                       sum(remaining_advance) as remaining_advance
                from (select ap.clientid,
                             ap.amount - IFNULL(used_advances.amount, 0) as remaining_advance,
                             currencies.id                               as currencyid,
                             currencies.name                             as currencyname,
                             currencies.description                      as currency_description,
                             clients.name                                as clientname
                      from advance_payments ap
                               inner join currencies on ap.currencyid = currencies.id
                               inner join clients on ap.clientid = clients.id
                               left join
                           (
                               select spua.advance_id, sum(spua.amount) as amount
                               from sales_payment_used_advances spua
                               group by spua.advance_id
                           ) used_advances on used_advances.advance_id = ap.id
                      where ap.amount - IFNULL(used_advances.amount, 0) > 0 $clientid $currencyid  ) as client_advance_balance
                group by currencyid";
//        debug($sql);
        return fetchRows($sql);
    }

    static function tallyPost($receiptno)
    {
        $receipt = AdvancePayments::$advancePaymentClass->paymentList($receiptno)[0];
        if (!$receipt['transfer_tally']) return ['status' => 'error', 'msg' => 'Not for Tally transfer'];
        if ($receipt['tally_post']) return ['status' => 'error', 'msg' => 'Already posted to tally'];

        $receiptno = "ADV-" . getTransNo($receiptno);
        if (!$receipt['tally_trxno']) {
            $tally_trxno = unique_token(60) . "-$receiptno";
            AdvancePayments::$advancePaymentClass->update($receipt['id'], ['tally_trxno' => $tally_trxno]);
        } else {
            $tally_trxno = $receipt['tally_trxno'];
        }

        $post_data = [];
        $post_data['trxno'] = $tally_trxno;
        $post_data['receiptno'] = $receiptno;
        $post_data['date'] = fDate($receipt['doc'], 'Ymd');
        $post_data['narration'] = $receipt['remark'];
        $post_data['clientname'] = htmlspecialchars($receipt['clientledgername']?:$receipt['clientname']);
        $post_data['cost_center'] = htmlspecialchars($receipt['cost_center']);
        $post_data['amount'] = $receipt['amount'];
        $post_data['receipt_currency'] = $receipt['currencyname'];
        $post_data['exchange_rate'] = $receipt['current_rate'];  //todo which exchange rate to use
        $post_data['base_currency'] = Currencies::$currencyClass->find(['base' => 'yes'])[0]['name'];

        $post_data['paymentmethod'] = $receipt['methodname'];
        switch ($receipt['methodname']) {
            case PaymentMethods::CASH:
                $post_data['dr_ledgername'] = htmlspecialchars($receipt['tally_cash_ledger']);
                break;
            case PaymentMethods::CREDIT_CARD:
                $post_data['dr_ledgername'] = htmlspecialchars($receipt['electronic_account_ledgername']);
                $post_data['narration'] .= "\n" . $receipt['electronic_account'] . " " . $receipt['credit_cardno'];
                break;
            case PaymentMethods::BANK:
                $post_data['dr_ledgername'] = htmlspecialchars($receipt['bank_ledgername']);
                break;
            case PaymentMethods::CHEQUE:
                $post_data['dr_ledgername'] = htmlspecialchars($receipt['bank_ledgername']);
                $post_data['narration'] .= "\n" . $receipt['chequename'] . " " . $receipt['chequetype'];
                break;
            case PaymentMethods::FROM_CREDIT_NOTE:
                $salereturn = SalesReturns::$saleReturnClass->getList($receipt['srd'])[0];
                $credit_note_no = getCreditNoteNo($salereturn['id']);
                return ['status' => 'error', 'msg' => "This advance receipt require credit note $credit_note_no to be posted first!"];
                break;
        }

        //clean narration
        $post_data['narration'] = htmlspecialchars($post_data['narration']);
//        debug($post_data);

        $result = createAdvanceReceiptVoucher($post_data);
        global $db_connection;
        mysqli_begin_transaction($db_connection);
        if ($result['status'] == 'success') {
            //save transfer
            //dr_ledgername
            $partno = 1;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => $post_data['dr_ledgername'],
                'dr_cr' => 'dr',
                'amount' => $post_data['amount'],
                'reference' => $post_data['receiptno'],
                'voucher_type' => 'Receipt',
                'sourceid' => $receipt['id'],
                'sourcetable' => 'advance_payments',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            //cr ledger
            $partno++;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => $receipt['clientname'],
                'dr_cr' => 'cr',
                'amount' => $post_data['amount'],
                'reference' => $post_data['receiptno'],
                'voucher_type' => 'Receipt',
                'sourceid' => $receipt['id'],
                'sourcetable' => 'advance_payments',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            AdvancePayments::$advancePaymentClass->update($receipt['id'], ['tally_post' => 1, 'tally_message' => $result['msg']]);
        } else {
            AdvancePayments::$advancePaymentClass->update($receipt['id'], ['tally_message' => $result['msg']]);
        }
        mysqli_commit($db_connection);

        return $result;
    }
}