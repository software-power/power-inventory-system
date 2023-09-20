<?


class SalesReturns extends model
{
    public const TYPE_PRICE = 'price';
    public const TYPE_ITEM = 'item';
    public const TYPE_FULL = 'full';

    public const ACTION_MANUAL = 'manual';
    public const ACTION_ADVANCE = 'advance';

    var $table = "sales_returns";
    static $saleReturnClass = null;

    function __construct()
    {
        self::$saleReturnClass = $this;
    }

    function getList($returnid = '', $salesid = '', $invoiceno = '', $type = '', $return_status = '', $clientid = '', $issuedby = '', $fromdate = '', $todate = '',
                     $locationid = '', $branchid = '', $return_method = '', $currencyid = "", $tra_invoice = false)
    {
        $sql = "select sr.*,
                       round(sr.total_excamount * sr.currency_amount, 2) as base_total_excamount,
                       round(sr.total_vatamount * sr.currency_amount, 2) as base_total_vatamount,
                       round(sr.total_incamount * sr.currency_amount, 2) as base_total_incamount,
                       cu.name                                           as currencyname,
                       cu.base                                           as base_currency,
                       cu.description                                    as currency_description,
                       sales.receipt_no                                  as invoiceno,
                       sales.tally_invoiceno,
                       sales.paymenttype                                 as invoicetype,
                       sales.tally_post                                  as invoice_tally_post,
                       sales.receipt_method                              as invoicereceipt,
                       sales.source                                      as invoice_source,
                       sales.doc                                         as invoice_date,
                       clients.id                                        as clientid,
                       clients.name                                      as clientname,
                       clients.ledgername                                as clientledgername,
                       users.name                                        as issuedby,
                       approver.name                                     as approver,
                       l.id                                              as locationid,
                       l.name                                            as locationname,
                       l.tally_cash_ledger                               as location_cash_ledger ,
                       b.id                                              as branchid,
                       b.name                                            as branchname,
                       b.cost_center,
                       b.tally_cash_ledger                               as branch_cash_ledger,
                       ad.id                                             as apid,
                       ad.amount                                         as advance_amount,
                       case
                           when sr.status != 'active' then 'canceled'
                           when approver.id is not null then 'approved'
                           else 'not_approved'
                           end                                           as return_status,
                       pm.name                                           as return_method,
                       banks.name                                        as bank_name,
                       banks.accno                                       as bank_accno,
                       banks.ledgername                                  as bank_ledgername,
                       e_account.name                                    as electronic_account,
                       e_account.ledgername                              as electronic_account_ledgername
                from sales_returns sr
                         inner join sales on sr.salesid = sales.id
                         inner join clients on sales.clientid = clients.id
                         inner join users on sr.createdby = users.id
                         inner join currencies cu on sr.currencyid = cu.id
                         left join users approver on sr.approvedby = approver.id
                         inner join locations l on sales.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         left join advance_payments ad on ad.srid = sr.id
                         left join paymentmethods pm on sr.pmethod_id = pm.id
                         left join banks on sr.bankid = banks.id
                         left join electronic_accounts e_account on sr.eaccid = e_account.id
                where 1 = 1";
        if ($returnid) $sql .= " and sr.id = $returnid";
        if ($salesid) $sql .= " and sales.id = $salesid";
        if ($invoiceno) $sql .= " and sales.receipt_no like '%$invoiceno%'";
        if ($type) $sql .= " and sr.type = '$type'";
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($currencyid) $sql .= " and cu.id = $currencyid";
        if ($issuedby) $sql .= " and users.id = $issuedby";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($return_method) $sql .= " and pm.name = '$return_method'";
        if ($tra_invoice) $sql .= " and sales.receipt_method != 'sr'";
        if ($fromdate) $sql .= " and date(sr.doc) >= '$fromdate'";
        if ($todate) $sql .= " and date(sr.doc) <= '$todate'";

        $sql .= " having 1=1";
        if ($return_status) $sql .= " and return_status = '$return_status'";
        $sql .= " order by sr.doc desc";
//        debug($sql);
        return fetchRows($sql);
    }


    static function fullInvoiceReturn($salesid, $description)
    {
        $result['status'] = 'success';
        global $db_connection;
        mysqli_begin_transaction($db_connection);
        try {
            $sale = Sales::$saleClass->salesList($salesid)[0];
            SalesReturns::$saleReturnClass->insert([
                'salesid' => $salesid,
                'currencyid' => $sale['currencyid'],
                'currency_amount' => $sale['currency_amount'],
                'type' => SalesReturns::TYPE_FULL,
                'total_excamount' => $sale['grand_amount'],
                'total_vatamount' => $sale['grand_vatamount'],
                'total_incamount' => $sale['full_amount'],
                'createdby' => $_SESSION['member']['id'],
                'description' => $description,
                'transfer_tally' => CS_TALLY_TRANSFER && $sale['receipt_method'] != 'sr',
            ]);
            $srid = SalesReturns::$saleReturnClass->lastId();

            foreach (Salesdetails::$saleDetailsClass->find(['salesid' => $salesid]) as $detail) {
//                debug($detail);

                SalesReturnDetails::$saleReturnDetailsClass->insert([
                    'srid' => $srid,
                    'sdi' => $detail['id'],
                    'rate' => $detail['price'] - $detail['discount'],
                    'qty' => $detail['quantity'],
                    'vat_rate' => $detail['vat_rate'],
                    'sinc' => $detail['sinc'],
                    'return_amount' => $detail['sinc']
                        ? $detail['incprice'] * $detail['quantity']
                        : round(($detail['price'] - $detail['discount']) * $detail['quantity'] * (1 + $detail['vat_rate'] / 100), 2),
                    'remarks' => '',
                ]);
                $srdid = SalesReturnDetails::$saleReturnDetailsClass->lastId();

                //serialno
                if ($serialnos = SerialNos::$serialNoClass->find(['sdi' => $detail['id']])) {
                    foreach ($serialnos as $sindex => $sno) {
                        SalesReturnSerialnos::$saleReturnSerialnosClass->insert([
                            'srdid' => $srdid,
                            'snoid' => $sno['id'],
                        ]);
                    }
                }

                //batches
                foreach (SalesBatches::$salesBatchesClass->find(['sdi' => $detail['id']]) as $saleBatch) {
                    SalesReturnBatches::$saleReturnBatchesClass->insert([
                        'srdid' => $srdid,
                        'batchid' => $saleBatch['batch_id'],
                        'qty' => $saleBatch['qty'],
                    ]);
                }
            }

            mysqli_commit($db_connection);
        } catch (Exception $e) {
            mysqli_rollback($db_connection);
            $result['status'] = 'error';
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }


    static function tallyPost($returnno)
    {
        return ['status' => 'error', 'msg' => "Credit Note Transfer to tally Temporary stopped"];
        $salereturn = SalesReturns::$saleReturnClass->getList($returnno)[0];

        if (!$salereturn['invoice_tally_post']) return ['status' => 'error', 'msg' => "Invoice ({$salereturn['invoiceno']}) for this credit note not posted to tally"];
        if (!$salereturn['approvedby']) return ['status' => 'error', 'msg' => 'Credit Note not approved'];
        if ($salereturn['invoicereceipt'] == 'sr') return ['status' => 'error', 'msg' => 'Credit Note for SR invoice is not for Tally transfer'];
        if (!$salereturn['transfer_tally']) return ['status' => 'error', 'msg' => 'Not for Tally transfer'];
        if ($salereturn['tally_post']) return ['status' => 'error', 'msg' => 'Already posted to tally'];

        $returnno = getCreditNoteNo($salereturn['id']);
        if (!$salereturn['tally_trxno']) {
            $tally_trxno = unique_token(60) . "-{$returnno}";
            SalesReturns::$saleReturnClass->update($salereturn['id'], ['tally_trxno' => $tally_trxno]);
        } else {
            $tally_trxno = $salereturn['tally_trxno'];
        }

//        debug($salereturn);

//debug($sale);
        $post_data = [];
        $post_data['trxno'] = $tally_trxno;
        $post_data['paymenttype'] = $salereturn['invoicetype'];
        $post_data['invoiceno'] = $salereturn['tally_invoiceno']?:$salereturn['invoiceno'];
        $post_data['returnno'] = $returnno;
        $post_data['date'] = fDate($salereturn['doc'], 'Ymd');
        $post_data['narration'] = $salereturn['description'];
        $post_data['clientname'] = htmlspecialchars($salereturn['clientledgername']?:$salereturn['clientname']);
        $post_data['cost_center'] = htmlspecialchars($salereturn['cost_center']);
        $post_data['cash_ledger'] = htmlspecialchars($salereturn['location_cash_ledger']);
        $post_data['total_amount'] = $salereturn['base_total_incamount'];
        $post_data['vatamount'] = $salereturn['base_total_vatamount'];
        $post_data['invoice_currency'] = $salereturn['currencyname'];
        $post_data['exchange_rate'] = $salereturn['currency_amount'];
        $post_data['base_currency'] = Currencies::$currencyClass->find(['base' => 'yes'])[0]['name'];
        $post_data['is_base_currency'] = $salereturn['base_currency'] == 'yes';

//        debug($salereturn);
        if ($post_data['paymenttype'] == PAYMENT_TYPE_CREDIT) {
            $post_data['cr_ledgername'] = $post_data['clientname'];

            if ($salereturn['return_amount'] > 0 || $salereturn['advance_amount'] > 0) { //if there is amount for return

                //TODO make payment voucher for the return amount

                $payment_voucher = [];
                $paymentno = "P-" . getCreditNoteNo($salereturn['id']);

                if (!$salereturn['tally_payment_trxno']) {
                    $tally_payment_trxno = unique_token(60) . "-$paymentno";
                    SalesReturns::$saleReturnClass->update($salereturn['id'], ['tally_payment_trxno' => $tally_payment_trxno]);
                } else {
                    $tally_payment_trxno = $salereturn['tally_payment_trxno'];
                }

                $payment_voucher['date'] = $post_data['date'];
                $payment_voucher['trxno'] = $tally_payment_trxno;
                $payment_voucher['voucherno'] = $paymentno;
                $payment_voucher['agst'] = $salereturn['invoiceno'];
                $payment_voucher['dr_ledgername'] = $post_data['clientname'];
                $payment_voucher['paymentmethod'] = $salereturn['return_method'];
                if ($salereturn['apid']) { //amount made as advance
                    $payment_voucher['amount'] = $salereturn['advance_amount'];
                    $payment_voucher['cr_ledgername'] = htmlspecialchars(CS_TALLY_CONTROL_ACC);


                    //advance voucher
                    $advance = AdvancePayments::$advancePaymentClass->paymentList($salereturn['apid'])[0];
                    $advanceno = "ADV-" . getTransNo($salereturn['apid']);
                    if (!$advance['tally_trxno']) {
                        $advance_tally_trxno = unique_token(60) . "-$advanceno";
                        AdvancePayments::$advancePaymentClass->update($advance['id'], ['tally_trxno' => $advance_tally_trxno]);
                    } else {
                        $advance_tally_trxno = $advance['tally_trxno'];
                    }

                    $advance_voucher['sourceid'] = $advance['id'];
                    $advance_voucher['date'] = $post_data['date'];
                    $advance_voucher['trxno'] = $advance_tally_trxno;
                    $advance_voucher['receiptno'] = $advanceno;
                    $advance_voucher['narration'] = $advance['remark'];
                    $advance_voucher['agst'] = $salereturn['invoiceno'];
                    $advance_voucher['clientname'] = $post_data['clientname'];
                    $advance_voucher['amount'] = $salereturn['advance_amount'];
                    $advance_voucher['paymentmethod'] = PaymentMethods::CASH;
                    $advance_voucher['dr_ledgername'] = htmlspecialchars(CS_TALLY_CONTROL_ACC);
                    $advance_voucher['narration'] = htmlspecialchars($advance_voucher['narration']);
                } else { //amount returned to client
                    $payment_voucher['amount'] = $salereturn['return_amount'];
                    switch ($salereturn['return_method']) {
                        case PaymentMethods::CASH:
                            $payment_voucher['cr_ledgername'] = $salereturn['location_cash_ledger'];
                            break;
                        case PaymentMethods::CREDIT_CARD:
                            $payment_voucher['cr_ledgername'] = $salereturn['electronic_account_ledgername'];
                            $payment_voucher['narration'] .= "\n" . $salereturn['electronic_account'] . " " . $salereturn['credit_cardno'];
                            break;
                        case PaymentMethods::BANK:
                            $payment_voucher['dr_ledgername'] = htmlspecialchars($salereturn['bank_ledgername']);
                            break;
                        case PaymentMethods::CHEQUE:
                            $payment_voucher['dr_ledgername'] = htmlspecialchars($salereturn['bank_ledgername']);
                            $payment_voucher['narration'] .= "\n" . $salereturn['chequename'] . " " . $salereturn['chequetype'];
                            break;
                    }
                }
                //clean narration
                $payment_voucher['narration'] = htmlspecialchars($payment_voucher['narration']);
            }
        } else { //cash invoice
            $salespayments = SalesPayments::$salePaymentClass->getSalesPayment($salereturn['salesid'])[0];
            if ($salereturn['apid']) { //payments made as advance
                $post_data['cr_ledgername'] = htmlspecialchars(CS_TALLY_CONTROL_ACC);
//                debug($salespayments);
//                if ($salespayments['method'] == PaymentMethods::CASH) {
//                    $post_data['cr_ledgername'] = $post_data['cash_ledger'];
//                } elseif ($salespayments['method'] == PaymentMethods::CREDIT_CARD) {
//                    $post_data['cr_ledgername'] = htmlspecialchars($salespayments['electronic_account_ledgername']);
//                    $post_data['narration'] .= "\n" . $salespayments['electronic_account'] . " " . $salespayments['credit_cardno'];
//                }

                //advance voucher
                $advance = AdvancePayments::$advancePaymentClass->paymentList($salereturn['apid'])[0];
                $advanceno = "ADV-" . getTransNo($salereturn['apid']);
                if (!$advance['tally_trxno']) {
                    $advance_tally_trxno = unique_token(60) . "-$advanceno";
                    AdvancePayments::$advancePaymentClass->update($advance['id'], ['tally_trxno' => $advance_tally_trxno]);
                } else {
                    $advance_tally_trxno = $advance['tally_trxno'];
                }

                $advance_voucher['sourceid'] = $advance['id'];
                $advance_voucher['date'] = $post_data['date'];
                $advance_voucher['trxno'] = $advance_tally_trxno;
                $advance_voucher['receiptno'] = $advanceno;
                $advance_voucher['narration'] = $advance['remark'];
                $advance_voucher['agst'] = $salereturn['invoiceno'];
                $advance_voucher['clientname'] = $post_data['clientname'];
                $advance_voucher['amount'] = $salereturn['advance_amount'];
                $advance_voucher['paymentmethod'] = PaymentMethods::CASH;
                $advance_voucher['dr_ledgername'] = htmlspecialchars(CS_TALLY_CONTROL_ACC);
                $advance_voucher['narration'] = htmlspecialchars($advance_voucher['narration']);
//                debug($advance_voucher);
            } else { //payment returned to client
                if ($salespayments['method'] == PaymentMethods::CASH) {
                    $post_data['cr_ledgername'] = $post_data['cash_ledger'];
                } elseif ($salespayments['method'] == PaymentMethods::CREDIT_CARD) {
                    $post_data['cr_ledgername'] = htmlspecialchars($salereturn['electronic_account_ledgername']);
                    $post_data['narration'] .= "\n" . $salereturn['electronic_account'] . " " . $salereturn['credit_cardno'];
                } elseif ($salespayments['method'] == PaymentMethods::BANK) {
                    $post_data['cr_ledgername'] = htmlspecialchars($salereturn['bank_ledgername']);
                } elseif ($salespayments['method'] == PaymentMethods::CHEQUE) {
                    $post_data['cr_ledgername'] = htmlspecialchars($salereturn['bank_ledgername']);
                    $post_data['narration'] .= "\n" . $salereturn['chequename'] . " " . $salereturn['chequetype'];
                }
            }
        }

        //clean narration
        $post_data['narration'] = htmlspecialchars($post_data['narration']);


        $details = SalesReturnDetails::$saleReturnDetailsClass->getList($salereturn['id']);
//        debug($details);
        foreach ($details as $detail) {
            $post_data['sales_accounts'][$detail['tally_sales_account']] += $detail['base_excamount'];
        }
//        debug($post_data,1);

        $creditnote_result = createCreditNoteVoucher($post_data, true);
//        debug($creditnote_result);
        if ($creditnote_result['status'] == 'success') {
            //send payment voucher
            if (isset($payment_voucher)) {
                $payment_result = createPaymentAgainstCreditNoteVoucher($payment_voucher);
                if ($payment_result['status'] == 'success') {
                    //clear old records
                    TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $payment_voucher['trxno']]);

                    //cr_ledgername
                    $partno = 1;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $payment_voucher['date'],
                        'partno' => $partno,
                        'ledgername' => $payment_voucher['cr_ledgername'],
                        'dr_cr' => 'cr',
                        'amount' => $payment_voucher['amount'],
                        'reference' => $payment_voucher['voucherno'],
                        'voucher_type' => 'Payment',
                        'sourceid' => $salereturn['id'],
                        'sourcetable' => 'sales_returns',
                        'trxno' => $payment_voucher['trxno'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    //dr_ledgername
                    $partno = 2;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $payment_voucher['date'],
                        'partno' => $partno,
                        'ledgername' => $payment_voucher['dr_ledgername'],
                        'dr_cr' => 'dr',
                        'amount' => $payment_voucher['amount'],
                        'reference' => $payment_voucher['voucherno'],
                        'voucher_type' => 'Payment',
                        'sourceid' => $salereturn['id'],
                        'sourcetable' => 'sales_returns',
                        'trxno' => $payment_voucher['trxno'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                } else {
                    $creditnote_result['status'] = 'error';
                    $creditnote_result['msg'] .= "\n" . $payment_result['msg'];
                }
            }

            if ($advance_voucher) {
                $advance_result = createAdvanceReceiptVoucher($advance_voucher, true);
                if ($advance_result['status'] == 'success') {
                    //clear old records
                    TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $advance_voucher['trxno']]);

                    //dr_ledgername
                    $partno = 1;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $advance_voucher['date'],
                        'partno' => $partno,
                        'ledgername' => $advance_voucher['dr_ledgername'],
                        'dr_cr' => 'dr',
                        'amount' => $advance_voucher['amount'],
                        'reference' => $advance_voucher['receiptno'],
                        'voucher_type' => 'Receipt',
                        'sourceid' => $advance_voucher['sourceid'],
                        'sourcetable' => 'advance_payments',
                        'trxno' => $advance_voucher['trxno'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    //cr_ledgername
                    $partno = 2;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $advance_voucher['date'],
                        'partno' => $partno,
                        'ledgername' => $advance_voucher['clientname'],
                        'dr_cr' => 'cr',
                        'amount' => $advance_voucher['amount'],
                        'reference' => $advance_voucher['receiptno'],
                        'voucher_type' => 'Receipt',
                        'sourceid' => $advance_voucher['sourceid'],
                        'sourcetable' => 'advance_payments',
                        'trxno' => $advance_voucher['trxno'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);

                    AdvancePayments::$advancePaymentClass->update($advance_voucher['sourceid'], ['tally_post' => 1, 'tally_message' => $advance_result['msg']]);

                } else {
                    $creditnote_result['status'] = 'error';
                    $creditnote_result['msg'] .= "\n" . $advance_result['msg'];
                }
            }

            //clear old transfer
            TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $tally_trxno]);
            //save transfer
            //cr_ledgername
            $partno = 1;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => $post_data['cr_ledgername'],
                'dr_cr' => 'cr',
                'amount' => $post_data['total_amount'],
                'reference' => $post_data['returnno'],
                'voucher_type' => 'Credit Note',
                'sourceid' => $salereturn['id'],
                'sourcetable' => 'sales_returns',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            //sales account
            foreach ($post_data['sales_accounts'] as $acc => $amount) {
                $partno++;
                TallyTransfers::$tallyTransferclass->insert([
                    'date' => $post_data['date'],
                    'partno' => $partno,
                    'ledgername' => $acc,
                    'dr_cr' => 'dr',
                    'amount' => $amount,
                    'reference' => $post_data['returnno'],
                    'voucher_type' => 'Credit Note',
                    'sourceid' => $salereturn['id'],
                    'sourcetable' => 'sales_returns',
                    'trxno' => $tally_trxno,
                    'createdby' => $_SESSION['member']['id'],
                ]);
            }

            //vat
            $partno++;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => 'Vat',
                'dr_cr' => 'dr',
                'amount' => $post_data['vatamount'],
                'reference' => $post_data['returnno'],
                'voucher_type' => 'Credit Note',
                'sourceid' => $salereturn['id'],
                'sourcetable' => 'sales_returns',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            SalesReturns::$saleReturnClass->update($salereturn['id'], [
                'tally_post' => $creditnote_result['status'] == 'success',
                'tally_message' => $creditnote_result['msg']]);
        } else {
            SalesReturns::$saleReturnClass->update($salereturn['id'], ['tally_message' => $creditnote_result['msg']]);
        }

        return $creditnote_result;

    }
}