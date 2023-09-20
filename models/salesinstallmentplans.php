<?php


class SalesInstallmentPlans extends model
{
    var $table = 'sales_installment_plans';
    static $staticClass = null;

    function __construct()
    {
        self::$staticClass = $this;
    }

    public function getList($salesid)
    {
        $sql = "select sip.*,
                       sales.doc                     as invoicedate,
                       datediff(sip.time, sales.doc) as duedays
                from sales_installment_plans sip
                         inner join sales on sip.salesid = sales.id
                where 1 = 1";
        if ($salesid) $sql .= " and sip.salesid = $salesid";
        $sql .= " order by sip.time";
        return fetchRows($sql);
    }

    function withStatus($salesid)
    {
        $sale = Sales::$saleClass->getSalesOutstanding($salesid)[0];

        $total_paid_amount = $sale['full_amount'] - $sale['pending_amount'];
        $installments = SalesInstallmentPlans::$staticClass->getList($salesid);
        $no = 1;
        foreach ($installments as $index => $i) {
            $installments[$index]['no'] = $no++;
            $installments[$index]['time'] = $sale['dist_plan'] == 'monthly' ? fDate($i['time'], 'F-Y') : fDate($i['time']);
            if ($i['amount'] <= $total_paid_amount) { //complete
                $installments[$index]['paid'] = $i['amount'];
                $installments[$index]['pending'] = 0;
                $installments[$index]['status'] = PAYMENT_STATUS_COMPLETE;
                $total_paid_amount -= $i['amount'];
            } elseif ($i['amount'] > $total_paid_amount && $total_paid_amount > 0) { //partial
                $installments[$index]['paid'] = $total_paid_amount;
                $installments[$index]['pending'] = $i['amount'] - $total_paid_amount;
                $installments[$index]['status'] = PAYMENT_STATUS_PARTIAL;
                $total_paid_amount = 0;
            } else { //pending
                $installments[$index]['paid'] = 0;
                $installments[$index]['pending'] = $i['amount'];
                $installments[$index]['status'] = PAYMENT_STATUS_PENDING;
            }
        }
        return $installments;
    }
}