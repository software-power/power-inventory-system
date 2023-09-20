<?php /**
 * salesdetails
 */
class SalesBatches extends model{
  var $table = 'salesbatches';

    static $salesBatchesClass = null;

    function __construct()
    {
        self::$salesBatchesClass = $this;
    }
}
 ?>
