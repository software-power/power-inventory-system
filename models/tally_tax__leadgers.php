
<?php
/**
 *tally_ledgers
 */
class TallyTaxLeadgers extends model{
  var $table = 'tally_tax_leadgers';

  // function ledgerList($ledgerid=""){
  //   $sql = "select tl.id, tl.name, tl.status, date_format(tl.doc,'%d-%M-%Y') as createdate,
  //   u.name as createdbyname from tally_ledgers as tl
  //   inner join users as u on u.id = tl.createdby where 1=1 ";
  //   if($ledgerid) $sql.=" and tl.id = $ledgerid ";
  //   return fetchRows($sql);
  // }
}
 ?>
