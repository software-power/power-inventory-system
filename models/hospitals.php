<?php

	class Hospitals extends model
	{
		var $table = "hospitals";

		function searchResults($name="") {
				$sql = "select * from hospitals where name like '%".$name."%' order by name";

				// echo $sql;die();
			return fetchRows($sql);
		}

}

?>
