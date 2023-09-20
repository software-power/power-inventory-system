<?php /**
 *
 */
class Units extends model{
  var $table = 'units';

   function search($name){
     $sql = "select * from units where status = 'active' and (name like '%".$name."%' or abbr like '%".$name."%')";
//     debug($sql);
     return fetchRows($sql);
   }
}
 ?>
