<?

class PaymentMethods extends model
{
    public const CASH = 'Cash';
    public const BANK = 'Bank';
    public const CHEQUE = 'Cheque';
    public const CREDIT_CARD = 'Credit Card';
    public const FROM_CREDIT_NOTE = 'From Credit Note';


    public const CHEQUE_TYPE_NORMAL = "Normal";
    public const CHEQUE_TYPE_PDC = "PDC";

    var $table = 'paymentmethods';

    function getReceiving()
    {
        $sql = "select * from paymentmethods where status = 'active' and name not in ('From Credit Note')";
        return fetchRows($sql);
    }
}

