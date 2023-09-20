<?


class ClientOpeningOutstandings extends model
{
    var $table = 'client_opening_outstanding';
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    function getList($openingid = '', $clientid = '', $currencyid = '', $userid = '', $payment_status = '', $fromdate = '', $todate = '',
                     $locationid = '', $branchid = '', $with_outstanding = false, $acc_mng = '')
    {
        $sql = "select coo.*,
                       ir.name                                                                    as outstanding_remarks,
                       coo.outstanding_amount - coo.paid_amount                                   as pending_amount,
                       round(coo.outstanding_amount * coo.currency_amount, 2)                     as base_outstanding_amount,
                       round(coo.paid_amount * coo.currency_amount, 2)                            as base_paid_amount,
                       round((coo.outstanding_amount - coo.paid_amount) * coo.currency_amount, 2) as base_pending_amount,
                       c.name                                                                     as clientname,
                       c.acc_mng,
                       acc_mng.name                                                               as account_manager,
                       cu.name                                                                    as currencyname,
                       cu.base                                                                    as base_currency,
                       cu.description                                                             as currency_description,
                       cr.rate_amount                                                             as current_rate,
                       l.name                                                                     as locationname,
                       b.id                                                                       as branchid,
                       b.name                                                                     as branchname,
                       users.name                                                                 as issuedby,
                       date_add(coo.invoicedate, INTERVAL coo.credit_days day)                    as duedate,
                       case
                           when datediff(CURDATE(), coo.invoicedate) < 30
                               then (coo.outstanding_amount - coo.paid_amount) end                as '(<30 days)',
                       case
                           when datediff(CURDATE(), coo.invoicedate) >= 30 and datediff(CURDATE(), coo.invoicedate) < 45
                               then (coo.outstanding_amount - coo.paid_amount) end                as '(30 to 45 days)',
                       case
                           when datediff(CURDATE(), coo.invoicedate) >= 45 and datediff(CURDATE(), coo.invoicedate) < 90
                               then (coo.outstanding_amount - coo.paid_amount) end                as '(45 to 90 days)',
                       case
                           when datediff(CURDATE(), coo.invoicedate) >= 90
                               then (coo.outstanding_amount - coo.paid_amount) end                as '(>90 days)'
                from client_opening_outstanding coo
                         inner join clients c on coo.clientid = c.id
                         left join users as acc_mng on c.acc_mng = acc_mng.id
                         inner join locations l on coo.locationid = l.id
                         inner join branches b on l.branchid = b.id
                         inner join currencies cu on coo.currencyid = cu.id
                         inner join currencies_rates cr on cu.id = cr.currencyid
                         inner join users on coo.createdby = users.id
                         left join invoice_remarks ir on coo.invoiceremarkid = ir.id
                where 1 = 1";

        if ($openingid) {
            if (is_array($openingid)) {
                $sql .= " and coo.id in (" . implode(',', $openingid) . ")";
            } else {
                $sql .= " and coo.id = $openingid";
            }
        }

        if ($clientid) $sql .= " and c.id = $clientid";
        if ($acc_mng) $sql .= " and c.acc_mng = $acc_mng";
        if ($currencyid) $sql .= " and cu.id = $currencyid";
        if ($userid) $sql .= " and coo.createdby = $userid";
        if ($with_outstanding) $sql .= " and coo.payment_status != '" . PAYMENT_STATUS_COMPLETE . "'";
        if ($payment_status) $sql .= " and coo.payment_status = '$payment_status'";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($fromdate) $sql .= " and date_format(coo.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(coo.doc,'%Y-%m-%d') <= '$todate'";

        $sql.=" order by coo.invoicedate";
//        debug($sql);
        return fetchRows($sql);
    }
}