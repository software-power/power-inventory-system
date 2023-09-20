<?php

	class Statuses extends model
	{
		var $table = "statuses";

		function getAllStatuses()
		{
			$sql = "select * from statuses order by sortno asc";
			//echo $sql;
			//die();
			return fetchRows($sql);
		}



	}

?>
