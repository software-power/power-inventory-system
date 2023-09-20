<?php
/**
 *tally_ledgers
 */
class TallyLedgers extends model{
  var $table = 'tally_ledgers';

  function ledgerList($ledgerid=""){
    $sql = "select tl.id, tl.name, tl.status from tally_ledgers as tl where 1=1 ";
    // debug($sql);
    if($ledgerid) $sql.=" and tl.id = $ledgerid ";
    return fetchRows($sql);
  }
}
 ?>
