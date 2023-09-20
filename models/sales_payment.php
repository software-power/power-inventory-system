<?php

/**
 * salespayments
 */
class SalesPayments extends model
{

    public const RECEIPT_TYPE_SR = 'sr';
    public const RECEIPT_TYPE_TRA = 'tra';

    //payment source
    public const SOURCE_RECEIPT = 'receipt'; // from credit invoice payments
    public const SOURCE_DIRECT = 'direct'; // direct from cash invoice

    var $table = 'salespayments';
    static $salePaymentClass = null;

    function __construct()
    {
        self::$salePaymentClass = $this;
    }

    function getSalesPayment($salesid = "", $openingid = "", $payid = "", $fromdate = "", $todate = "", $payment_method = "", $payment_source = "", $group = false,
                             $payment_receiver = "", $clientid = "", $currencyid = "", $eaccount = "")
    {
        $sql = "select sp.id,
                       c.name                                                as currencyname,
                       c.description                                         as currency_description,
                       sp.paid_totalmount,
                       sp.handed_amount,
                       sp.source                                             as sp_source,
                       sp.buying_rate,
                       sp.doc,
                       sp.clientid,
                       clients.name                                          as clientname,
                       sp.remark,
                       sp.approvedate,
                       used_advance.amount                                   as advance_amount,
                       used_advance.amount                                   as offset_amount,
                       (sp.paid_totalmount - ifnull(used_advance.amount, 0)) as received_amount,
                       sp.status                                             as sp_status,
                       creator.name                                          as creator,
                       approver.name                                         as approver,
                       pm.name                                               as method,
                       e_account.name                                        as electronic_account,
                       e_account.ledgername                                  as electronic_account_ledgername,
                       sp.credit_cardno,
                       spd.id                                                as detailid,
                       spd.amount,
                       spd.base_selling,
                       spd.opening,
                       spd.salesid
                from salespayments as sp
                         inner join paymentmethods as pm on pm.id = sp.pmethod_id
                         left join electronic_accounts e_account on e_account.id = sp.eaccid
                         inner join clients on clients.id = sp.clientid
                         inner join currencies c on c.id = sp.currencyid
                         inner join salespayment_details as spd on spd.salespaymentId = sp.id
                         inner join users as creator on creator.id = sp.createdby
                         left join users as approver on approver.id = sp.approvalby
                         left join
                     (
                         select spua.spid, sum(spua.amount) amount
                         from sales_payment_used_advances spua
                         group by spua.spid
                     ) as used_advance on used_advance.spid = sp.id
                where 1 = 1";

        if ($salesid) $sql .= " and spd.salesid = $salesid and spd.opening = 0";
        if ($openingid) $sql .= " and spd.salesid = $openingid and spd.opening = 1";
        if ($payid) $sql .= " and sp.id = $payid";
        if ($payment_receiver) $sql .= " and sp.createdby = $payment_receiver";
        if ($clientid) $sql .= " and sp.clientid = $clientid";
        if ($currencyid) $sql .= " and c.id = $currencyid";
        if ($eaccount) $sql .= " and e_account.id = $eaccount";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        if ($payment_source) $sql .= " and sp.source = '$payment_source'";
        if ($fromdate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') <= '$todate'";


        if ($group) $sql .= " group by sp.id";

        $sql .= " order by sp.id desc";
//    debug($sql);
        return fetchRows($sql);
    }

    function detailedPaymentInfo($paymentId = "", $fromDate = "", $toDate = "", $locationid = "", $branchid = "", $payment_method = "",
                                 $receipt_type = "", $group = true, $currencyid = "", $payment_receiver = "", $clientid = "", $with_received = false, $eaccount = "")
    {
        $sql = "select sp.*,
                       currencies.name                                        as currencyname,
                       currencies.description                                 as currency_description,
                       used_advances.amount                                   as advance_amount,
                       (sp.paid_totalmount - ifnull(used_advances.amount, 0)) as received_amount,
                       clients.name                                           as clientname,
                       clients.ledgername                                     as clientledgername,
                       creator.name                                           as creator,
                       approver.name                                          as approver,
                       pm.name                                                as method,
                       banks.name                                             as bank_name,
                       banks.accno                                            as bank_accno,
                       banks.ledgername                                       as bank_ledgername,
                       e_account.name                                         as electronic_account,
                       e_account.ledgername                                   as electronic_account_ledgername,
                       spd.id                                                 as detailId,
                       spd.amount,
                       spd.base_selling,
                       spd.salesid,
                       spd.opening,
                       if(sales.id is null, coo.invoiceno, sales.receipt_no)  as invoiceno,
                       sales.tally_invoiceno,
                       sales.receipt_method,
                       l.name                                                 as locationname,
                       b.name                                                 as branchname,
                       b.cost_center
                from salespayments as sp
                         inner join paymentmethods as pm on pm.id = sp.pmethod_id
                         left join banks on banks.id = sp.bankid
                         left join electronic_accounts e_account on e_account.id = sp.eaccid
                         inner join currencies on sp.currencyid = currencies.id
                         inner join salespayment_details as spd on spd.salespaymentId = sp.id
                         inner join clients on clients.id = sp.clientid
                         inner join users as creator on creator.id = sp.createdby
                         left join users as approver on approver.id = sp.approvalby
                         left join sales on sales.id = spd.salesid and spd.opening = 0
                         left join client_opening_outstanding coo on coo.id = spd.salesid and spd.opening = 1
                         inner join locations l on sales.locationid = l.id or coo.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         left join
                     (
                         select spua.spid, sum(spua.amount) amount
                         from sales_payment_used_advances spua
                         group by spua.spid
                     ) used_advances on used_advances.spid = sp.id
                where 1 = 1";
        if ($paymentId) $sql .= " and sp.id = $paymentId";
        if ($eaccount) $sql .= " and e_account.id = $eaccount";
        if ($payment_receiver) $sql .= " and sp.createdby = $payment_receiver";
        if ($currencyid) $sql .= " and currencies.id = $currencyid";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        if ($receipt_type) $sql .= " and sp.receipt_type = '$receipt_type'";
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($with_received) $sql .= " and (sp.paid_totalmount - ifnull(used_advances.amount, 0)) > 0";
        if ($fromDate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') >= '$fromDate'";
        if ($toDate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') <= '$toDate'";
        $sql .= " order by id desc";

        $payments = fetchRows($sql);
        if (!$group) {
            return $payments;
        } else {
            $newArray = [];
            foreach ($payments as $index => $item) {
                $newArray[$item['id']]['id'] = $item['id'];
                $newArray[$item['id']]['clientid'] = $item['clientid'];
                $newArray[$item['id']]['clientname'] = $item['clientname'];
                $newArray[$item['id']]['clientledgername'] = $item['clientledgername'];
                $newArray[$item['id']]['pmethod_id'] = $item['pmethod_id'];
                $newArray[$item['id']]['currencyname'] = $item['currencyname'];
                $newArray[$item['id']]['currency_description'] = $item['currency_description'];
                $newArray[$item['id']]['buying_rate'] = $item['buying_rate'];
                $newArray[$item['id']]['method'] = $item['method'];
                $newArray[$item['id']]['receipt_type'] = $item['receipt_type'];
                $newArray[$item['id']]['paid_totalmount'] = $item['paid_totalmount'];
                $newArray[$item['id']]['source'] = $item['source'];
                $newArray[$item['id']]['advance_amount'] = $item['advance_amount'];
                $newArray[$item['id']]['received_amount'] = $item['received_amount'];
                $newArray[$item['id']]['status'] = $item['status'];
                $newArray[$item['id']]['remark'] = $item['remark'];
                $newArray[$item['id']]['approvalby'] = $item['approvalby'];
                $newArray[$item['id']]['approver'] = $item['approver'];
                $newArray[$item['id']]['approvedate'] = $item['approvedate'];
                $newArray[$item['id']]['chequename'] = $item['chequename'];
                $newArray[$item['id']]['chequetype'] = $item['chequetype'];
                $newArray[$item['id']]['bankname'] = $item['bankname'];
                $newArray[$item['id']]['bankreference'] = $item['bankreference'];
                $newArray[$item['id']]['bank_name'] = $item['bank_name'];
                $newArray[$item['id']]['bank_accno'] = $item['bank_accno'];
                $newArray[$item['id']]['bank_ledgername'] = $item['bank_ledgername'];
                $newArray[$item['id']]['eaccid'] = $item['eaccid'];
                $newArray[$item['id']]['credit_cardno'] = $item['credit_cardno'];
                $newArray[$item['id']]['electronic_account'] = $item['electronic_account'];
                $newArray[$item['id']]['electronic_account_ledgername'] = $item['electronic_account_ledgername'];
                $newArray[$item['id']]['createdby'] = $item['createdby'];
                $newArray[$item['id']]['creator'] = $item['creator'];
                $newArray[$item['id']]['doc'] = $item['doc'];
                $newArray[$item['id']]['transfer_tally'] = $item['transfer_tally'];
                $newArray[$item['id']]['tally_post'] = $item['tally_post'];
                $newArray[$item['id']]['tally_trxno'] = $item['tally_trxno'];
                $newArray[$item['id']]['payments'][$item['detailId']]['detailId'] = $item['detailId'];
                $newArray[$item['id']]['payments'][$item['detailId']]['amount'] = $item['amount'];
                $newArray[$item['id']]['payments'][$item['detailId']]['base_selling'] = $item['base_selling'];
                $newArray[$item['id']]['payments'][$item['detailId']]['salesid'] = $item['salesid'];
                $newArray[$item['id']]['payments'][$item['detailId']]['opening'] = $item['opening'];
                $newArray[$item['id']]['payments'][$item['detailId']]['invoiceno'] = $item['invoiceno'];
                $newArray[$item['id']]['payments'][$item['detailId']]['tally_invoiceno'] = $item['tally_invoiceno'];
                $newArray[$item['id']]['payments'][$item['detailId']]['receipt_method'] = $item['receipt_method'];
            }
//            debug($newArray);
            return array_values($newArray);
        }
    }

    function withSaleDetails($salesid = "", $salesperson = "", $payid = "", $clientid = "", $paymenttype = "", $payment_method = "", $fromdate = "",
                             $todate = "", $currencyid = "", $locationid = "", $branchid = "", $tra_receipt_only = false, $payment_receiver = "",
                             $invoice_no = "", $payment_source = "", $eaccount = "")
    {
        $sql = "select sp.id,
                       c.id                                                  as currencyid,
                       c.name                                                as currencyname,
                       c.description                                         as currency_description,
                       c.base                                                as base_currency,
                       sp.paid_totalmount,
                       sp.doc,
                       sp.remark,
                       sp.approvedate,
                       sp.bankname,
                       sp.bankreference,
                       sp.chequename,
                       sp.chequetype,
                       sp.eaccid,
                       e_account.name                                        as electronic_account,
                       sp.credit_cardno,
                       used_advance.amount                                   as advance_amount,
                       (sp.paid_totalmount - ifnull(used_advance.amount, 0)) as received_amount,
                       sp.source                                             as sp_source,
                       sp.status                                             as sp_status,
                       creator.id                                            as creatorid,
                       creator.name                                          as creator,
                       approver.name                                         as approver,
                       pm.name                                               as method,
                       sp.clientid,
                       clients.name                                          as clientname,
                       clients.mobile,
                       spd.amount,
                       round(spd.amount * spd.base_selling, 2)               as base_amount,
                       spd.salesid,
                       spd.opening,
                       if(sales.id is null, coo.invoiceno, sales.receipt_no) as invoiceno,
                       sales.source,
                       sales.paymenttype,
                       sales.receipt_method,
                       orders.id                                             as orderno,
                       order_creator.id                                      as order_createdby,
                       order_creator.name                                    as order_creator,
                       l.id                                                  as locationid,
                       l.name                                                as locationname,
                       b.id                                                  as branchid,
                       b.name                                                as branchname,
                       salesperson.id                                        as sale_createdby,
                       salesperson.name                                      as salesperson,
                       ec.name                                               as expense_currencyname,
                       e.total_amount                                        as expense_amount
                from salespayments as sp
                         inner join paymentmethods as pm on pm.id = sp.pmethod_id
                         left join electronic_accounts e_account on e_account.id = sp.eaccid
                         inner join clients on clients.id = sp.clientid
                         inner join currencies c on c.id = sp.currencyid
                         inner join salespayment_details as spd on spd.salespaymentId = sp.id
                         left join sales on sales.id = spd.salesid and spd.opening = 0
                         left join orders on orders.id = sales.orderid
                         left join users as order_creator on order_creator.id = orders.createdby
                         left join client_opening_outstanding coo on coo.id = spd.salesid and spd.opening = 1
                         inner join users as creator on creator.id = sp.createdby
                         inner join locations l on sales.locationid= l.id or coo.locationid= l.id
                         inner join branches b on l.branchid = b.id
                         left join users as salesperson on salesperson.id = sales.createdby
                         left join expenses e on e.saleid = sales.id
                         left join currencies ec on e.currencyid = ec.id
                         left join users as approver on approver.id = sp.approvalby
                         left join
                     (
                         select spua.spid, sum(spua.amount) amount
                         from sales_payment_used_advances spua
                         group by spua.spid
                     ) as used_advance on used_advance.spid = sp.id
                where 1 = 1";

        if ($salesid) $sql .= " and spd.salesid = $salesid";
        if ($invoice_no) $sql .= " and sales.receipt_no = '$invoice_no'";
        if ($salesperson) $sql .= " and sales.createdby = $salesperson";
        if ($payment_receiver) $sql .= " and sp.createdby = $payment_receiver";
        if ($payment_source) $sql .= " and sp.source = '$payment_source'";
        if ($payid) $sql .= " and sp.id = $payid";
        if ($clientid) $sql .= " and clients.id = $clientid";
        if ($paymenttype) $sql .= " and sales.paymenttype = '$paymenttype'";
        if ($payment_method) $sql .= " and pm.name = '$payment_method'";
        if ($eaccount) $sql .= " and e_account.id = $eaccount";
        if ($currencyid) $sql .= " and c.id = $currencyid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($tra_receipt_only) $sql .= " and sales.receipt_method != 'sr'";
        if ($fromdate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(sp.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by sp.id desc";

//        debug($sql);
        return fetchRows($sql);
    }

    static function tallyPost($receiptno)
    {
        $receipt = SalesPayments::$salePaymentClass->detailedPaymentInfo($receiptno)[0];
        if (!$receipt['transfer_tally']) return ['status' => 'error', 'msg' => 'Not for Tally transfer'];
        if ($receipt['receipt_type'] != SalesPayments::RECEIPT_TYPE_TRA) return ['status' => 'error', 'msg' => 'Not for Tally transfer'];

        $receiptno = "R-" . getTransNo($receiptno);
        if (!$receipt['tally_trxno']) {
            $tally_trxno = unique_token(60) . "-$receiptno";
            SalesPayments::$salePaymentClass->update($receipt['id'], ['tally_trxno' => $tally_trxno]);
        } else {
            $tally_trxno = $receipt['tally_trxno'];
        }

        $post_data = [];
        $post_data['trxno'] = $tally_trxno;
        $post_data['receiptno'] = $receiptno;
        $post_data['date'] = fDate($receipt['doc'], 'Ymd');
        $post_data['narration'] = $receipt['remark'];
        $post_data['clientname'] = htmlspecialchars($receipt['clientledgername'] ?: $receipt['clientname']);
        $post_data['total_amount'] = $receipt['paid_totalmount'];
        $post_data['receipt_currency'] = $receipt['currencyname'];
        $post_data['exchange_rate'] = $receipt['buying_rate'];  //todo which exchange rate to use
        $post_data['base_currency'] = Currencies::$currencyClass->find(['base' => 'yes'])[0]['name'];

        try {
            foreach ($receipt['payments'] as $pindex=> $p) {
                // entry for fixing tally OB  prefix issue
                if ($p['opening']) {
                    if (strpos( $p['invoiceno'],"OB")!== false) {
                        $p['invoiceno'] = str_replace("OB", '', $p['invoiceno']);
                    }
                }
                $post_data['bills'][] = [
                    'invoiceno' => $p['tally_invoiceno'] ?: $p['invoiceno'],
                    'amount' => $p['amount'],
                ];
            }

            //check if there is amount received
            if ($receipt['received_amount'] > 0) {
                $post_data['received']['method'] = $receipt['method'];
                switch ($receipt['method']) {
                    case PaymentMethods::CASH:
//                        $post_data['received']['dr_ledgername'] = htmlspecialchars($receipt['tally_cash_ledger']); //todo which cash ledger

                        //temporary fix choose from one invoice
                        $first_payment = array_values($receipt['payments'])[0];
                        $salesid = $first_payment['salesid'];
                        if ($first_payment['opening']) { //from opening outstanding
                            $branchid = ClientOpeningOutstandings::$staticClass->getList($salesid)[0]['branchid'];
                            $branch = Branches::$branchClass->get($branchid);
                            $post_data['received']['dr_ledgername'] = htmlspecialchars($branch['tally_cash_ledger']);
                        } else {
                            $post_data['received']['dr_ledgername'] = htmlspecialchars(Sales::$saleClass->salesList($salesid)[0]['branch_cash_ledger']);
                        }

                        break;
                    case PaymentMethods::CREDIT_CARD:
                        $post_data['received']['dr_ledgername'] = htmlspecialchars($receipt['electronic_account_ledgername']);
                        $post_data['narration'] .= "\n" . $receipt['electronic_account'] . " " . $receipt['credit_cardno'];
                        break;
                    case PaymentMethods::BANK:
                        $post_data['received']['dr_ledgername'] = htmlspecialchars($receipt['bank_ledgername']);
                        break;
                    case PaymentMethods::CHEQUE:
                        $post_data['received']['dr_ledgername'] = htmlspecialchars($receipt['bank_ledgername']);
                        $post_data['narration'] .= "\n" . $receipt['chequename'] . " " . $receipt['chequetype'];
                        break;
                }
                $post_data['received']['amount'] = $receipt['received_amount'];
            }

            //clean narration
            $post_data['narration'] = htmlspecialchars($post_data['narration']);

            //check if there is advance amount used
            if ($receipt['advance_amount'] > 0) {
                $used_advances = SalePaymentUsedAdvances::$salePaymentUsedAdvanceClass->getList($receipt['id']);
                foreach ($used_advances as $a) {
                    $advance = AdvancePayments::$advancePaymentClass->paymentList($a['advance_id'])[0];
                    if (!$advance['tally_post'])
                        throw new Exception("System found \nadvance receipt no " . getTransNo($advance['id']) . " not posted to tally, transaction can not continue");
                    $post_data['advance'][$a['id']]['method'] = PaymentMethods::CASH;
                    $post_data['advance'][$a['id']]['cr_ledgername'] = htmlspecialchars(CS_TALLY_CONTROL_ACC);
                    $post_data['advance'][$a['id']]['date'] = $post_data['date'];
                    $post_data['advance'][$a['id']]['amount'] = $a['amount'];
                    $post_data['advance'][$a['id']]['cost_center'] = $advance['cost_center'];
                    $post_data['advance'][$a['id']]['clientname'] = $post_data['clientname'];
                    $advance_receiptno = "ADV-" . getTransNo($advance['id']);
                    $post_data['advance'][$a['id']]['advance_receiptno'] = $advance_receiptno;
                    $usedadvanceno = "PA-" . getTransNo($a['id']);
                    $post_data['advance'][$a['id']]['usedadvanceno'] = $usedadvanceno;
                    if (!$a['tally_trxno']) {
                        $post_data['advance'][$a['id']]['trxno'] = unique_token(60) . "-$usedadvanceno";
                        SalePaymentUsedAdvances::$salePaymentUsedAdvanceClass->update($a['id'], ['tally_trxno' => $post_data['advance'][$a['id']]['trxno']]);
                    } else {
                        $post_data['advance'][$a['id']]['trxno'] = $a['tally_trxno'];
                    }
                    $pay_result = createPaymentAgainstAdvanceVoucher($post_data['advance'][$a['id']], true);
                    if ($pay_result['status'] == 'error') throw new Exception("Advance no {$advance_receiptno} could not be paid back,\n" . $pay_result['msg']);
                    //record payment transfers
                    //clear old info if exists
                    TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $post_data['advance'][$a['id']]['trxno']]);
                    //dr ledger
                    $partno = 1;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $post_data['date'],
                        'partno' => $partno,
                        'ledgername' => $post_data['advance'][$a['id']]['clientname'],
                        'dr_cr' => 'dr',
                        'amount' => $a['amount'],
                        'reference' => $usedadvanceno,
                        'voucher_type' => 'Payment',
                        'sourceid' => $a['id'],
                        'sourcetable' => 'sales_payment_used_advances',
                        'trxno' => $post_data['advance'][$a['id']]['trxno'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                    //cr ledger
                    $partno = 2;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $post_data['date'],
                        'partno' => $partno,
                        'ledgername' => $post_data['advance'][$a['id']]['cr_ledgername'],
                        'dr_cr' => 'cr',
                        'amount' => $a['amount'],
                        'reference' => $usedadvanceno,
                        'voucher_type' => 'Payment',
                        'sourceid' => $a['id'],
                        'sourcetable' => 'sales_payment_used_advances',
                        'trxno' => $post_data['advance'][$a['id']]['trxno'],
                        'createdby' => $_SESSION['member']['id'],
                    ]);

                    SalePaymentUsedAdvances::$salePaymentUsedAdvanceClass->update($a['id'], ['tally_post' => 1]);

                }
            }

//            debug();
//            debug($post_data);

            $result = createReceiptVoucher($post_data, $receipt['tally_post']);
            TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $tally_trxno]);
            if ($result['status'] == 'success') {
                //save transfer
                //cr ledger
                $partno = 1;
                TallyTransfers::$tallyTransferclass->insert([
                    'date' => $post_data['date'],
                    'partno' => $partno,
                    'ledgername' => $post_data['clientname'],
                    'dr_cr' => 'cr',
                    'amount' => $post_data['total_amount'],
                    'reference' => $receiptno,
                    'voucher_type' => 'Receipt',
                    'sourceid' => $receipt['id'],
                    'sourcetable' => 'salespayments',
                    'trxno' => $tally_trxno,
                    'createdby' => $_SESSION['member']['id'],
                ]);

                if ($post_data['received']) { //for received amount
                    $partno++;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $post_data['date'],
                        'partno' => $partno,
                        'ledgername' => $post_data['received']['dr_ledgername'],
                        'dr_cr' => 'dr',
                        'amount' => $post_data['received']['amount'],
                        'reference' => $receiptno,
                        'voucher_type' => 'Receipt',
                        'sourceid' => $receipt['id'],
                        'sourcetable' => 'salespayments',
                        'trxno' => $tally_trxno,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                }

                foreach ($post_data['advance'] as $id => $a) {
                    $partno++;
                    TallyTransfers::$tallyTransferclass->insert([
                        'date' => $post_data['date'],
                        'partno' => $partno,
                        'ledgername' => $a['cr_ledgername'],
                        'dr_cr' => 'dr',
                        'amount' => $a['amount'],
                        'reference' => $receiptno,
                        'voucher_type' => 'Receipt',
                        'sourceid' => $receipt['id'],
                        'sourcetable' => 'salespayments',
                        'trxno' => $tally_trxno,
                        'createdby' => $_SESSION['member']['id'],
                    ]);
                }

                SalesPayments::$salePaymentClass->update($receipt['id'], ['tally_post' => 1, 'tally_message' => $result['msg']]);
            } else {
                SalesPayments::$salePaymentClass->update($receipt['id'], ['tally_message' => $result['msg']]);
            }
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['msg'] = $e->getMessage();
        }
        return $result;
    }

}
