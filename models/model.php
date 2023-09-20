<?php
/**
 *
 */
class Models extends model{
  var $table = 'model';
  static $staticClass = null;

  function __construct()
  {
    self::$staticClass = $this;
  }

  function search($name){
    $sql = "select * from model where status = 'active' and name like '%".$name."%'";
    return fetchRows($sql);
  }
}
 ?>
 