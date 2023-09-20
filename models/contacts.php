<?

	class Contacts extends model{
        var $table = "contacts";
        public static $contactClass = null;

        function __construct()
        {
            self::$contactClass = $this;
        }
    }