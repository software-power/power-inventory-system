<?php

	class Doctors extends model
	{
		var $table = "doctors";

		function searchResults($name="") {
				$sql = "select * from doctors where name like '%".$name."%' order by name";

				// echo $sql;die();
			return fetchRows($sql);
		}

}

?>
