<?

class TallyTransfers extends model
{
    var $table = "tally_transfers";
    static $tallyTransferclass = null;

    function __construct()
    {
        self::$tallyTransferclass = $this;
    }

    function getList($vouchertype = "", $reference = "", $fromdate = "", $todate = "", $group = true)
    {
        $sql = "select tt.*,
                       users.name as username
                from tally_transfers tt
                         inner join users on users.id = tt.createdby
                where 1 = 1";
        if ($vouchertype) $sql .= " and tt.voucher_type = '$vouchertype'";
        if ($reference) $sql .= " and tt.reference = '$reference'";
        if ($fromdate) $sql .= " and date_format(tt.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(tt.doc,'%Y-%m-%d') <= '$todate'";
        $sql .= " order by tt.doc desc,tt.voucher_type,tt.partno asc";
        $result = fetchRows($sql);
        if (!$group) {
            return $result;
        } else {
            $array = [];
            foreach ($result as $item) {
                $array[$item['trxno']]['trxno'] = $item['trxno'];
                $array[$item['trxno']]['date'] = $item['date'];
                $array[$item['trxno']]['reference'] = $item['reference'];
                $array[$item['trxno']]['sourceid'] = $item['sourceid'];
                $array[$item['trxno']]['sourcetable'] = $item['sourcetable'];
                $array[$item['trxno']]['voucher_type'] = $item['voucher_type'];
                $array[$item['trxno']]['createdby'] = $item['createdby'];
                $array[$item['trxno']]['username'] = $item['username'];
                $array[$item['trxno']]['doc'] = $item['doc'];
                switch ($item['sourcetable']) {
                    case 'sales':
                        $array[$item['trxno']]['url'] = url('sales', 'view_invoice', ['salesid' => $item['sourceid']]);
                        break;
                    case 'advance_payments':
                        $array[$item['trxno']]['url'] = url('advance_payments', 'list', ['apid' => $item['sourceid']]);
                        break;
                    case 'salespayments':
                        $array[$item['trxno']]['url'] = url('reports', 'sales_payment_sr', ['receiptno' => $item['sourceid']]);
                        break;
                    case 'expenses':
                        $array[$item['trxno']]['url'] = url('expenses', 'issued_list', ['expenseid' => $item['sourceid']]);
                        break;
                    case 'sales_returns':
                        $array[$item['trxno']]['url'] = url('sales_returns', 'view', ['returnno' => $item['sourceid']]);
                        break;
                    case 'grn':
                        $array[$item['trxno']]['url'] = url('grns', 'view_grn', ['grn' => $item['sourceid']]);
                        break;
                    default:

                }


                $array[$item['trxno']]['details'][$item['id']]['id'] = $item['id'];
                $array[$item['trxno']]['details'][$item['id']]['partno'] = $item['partno'];
                $array[$item['trxno']]['details'][$item['id']]['ledgername'] = $item['ledgername'];
                $array[$item['trxno']]['details'][$item['id']]['dr_cr'] = $item['dr_cr'];
                $array[$item['trxno']]['details'][$item['id']]['amount'] = formatN($item['amount']);
            }
            return array_values($array);
        }
    }

    function pendingTransfer()
    {
        $sql = "select * from (
                                  select id         as sourceid,
                                         'sales'    as sourcetable,
                                         'Sales'    as voucher_type,
                                         receipt_no as voucherno,
                                         tally_message,
                                         doc        as issuedate
                                  from sales
                                  where transfer_tally = 1 and tally_post = 0
                                  union all
                                  select id                             as sourceid,
                                         'advance_payments'             as sourcetable,
                                         'Advance Receipt'              as voucher_type,
                                         concat('ADV-', lpad(id, 5, 0)) as voucherno,
                                         tally_message,
                                         doc                            as issuedate
                                  from advance_payments
                                  where transfer_tally = 1 and tally_post = 0
                                  union all
                                  select id                           as sourceid,
                                         'salespayments'              as sourcetable,
                                         'Sales Receipt'              as voucher_type,
                                         concat('R-', lpad(id, 5, 0)) as voucherno,
                                         tally_message,
                                         doc                          as issuedate
                                  from salespayments
                                  where transfer_tally = 1 and tally_post = 0
                                  union all
                                  select id                           as sourceid,
                                         'expenses'                   as sourcetable,
                                         'Expense'                    as voucher_type,
                                         concat('E-', lpad(id, 5, 0)) as voucherno,
                                         tally_message collate utf8mb4_unicode_ci,
                                         doc                          as issuedate
                                  from expenses
                                  where transfer_tally = 1 and tally_post = 0 and approvedby > 0 and status = 'active'
                                  union all
                                  select id                           as sourceid,
                                         'sales_returns'              as sourcetable,
                                         'Credit Note'                as voucher_type,
                                         concat('CN', lpad(id, 5, 0)) as voucherno,
                                         tally_message collate utf8mb4_unicode_ci,
                                         doc                          as issuedate
                                  from sales_returns
                                  where transfer_tally = 1 and tally_post = 0
                                  union all
                                  select id                           as sourceid,
                                         'grn'                        as sourcetable,
                                         'GRN'                        as voucher_type,
                                         concat('GRN-', id)           as voucherno,
                                         tally_message,
                                         doc                          as issuedate
                                  from grn
                                  where transfer_tally = 1 and tally_post = 0
                              ) as pending order by issuedate";
        return fetchRows($sql);
    }
}


