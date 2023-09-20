<?


class ElectronicAccounts extends model
{
    var $table = "electronic_accounts";

    static $electronicAccountsClass = null;

    function __construct()
    {
        self::$electronicAccountsClass = $this;
    }
}