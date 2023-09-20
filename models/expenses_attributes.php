<?

class ExpensesAttributes extends model{
  var $table = 'expenses_attributes';
  function search($name){
    $sql = "select name, id from expenses_attributes where status = 'active' and name like '%".$name."%'";
    return fetchRows($sql);
  }
}
