<?php

	class StockItems extends model
	{
		var $table = "stockitems";

			function getAll($name="",$status="") {
				$sql = "select * from stockitems where 1=1";
				if ($name) $sql.=" and name like '%". $name ."%'";
				if ($status) $sql.=" and status = '". $status ."'";
				//echo $sql;
				return fetchRows($sql);
			}


			function calcStock($locid="",$stockid="",$prodid="",$stkdate="") {
			if (!$stkdate) $stkdate = date('Y-m-d',strtotime('+1 day'));

			$sql = "select id, stockitemid, item, serialno, sum(total) as total from (
						   (select s.id, s.stockitemid, si.name as item, s.serialno, sum(gd.qty) as total from grns as g
						   INNER JOIN grndetails as gd ON g.id = gd.grnid
						   INNER JOIN stocks as s ON s.id = gd.stockid
						   INNER JOIN stockitems as si on si.id = s.stockitemid
							 where g.date <= '".$stkdate."' and g.locid = ".$locid."
							 group by s.id,s.stockitemid,s.serialno)
					union all
						   (select s.id, s.stockitemid, si.name as item, s.serialno, -sum(td.qty) as total from transfers as t
						   INNER JOIN transferdetails as td ON t.id = td.transferid
						   INNER JOIN stocks as s ON s.id = td.stockidfrom
						   INNER JOIN stockitems as si on si.id = s.stockitemid
							 where t.date <= '".$stkdate."' and t.fromid = ".$locid."
							 group by s.id,s.stockitemid,s.serialno)
					union all
						   (select s.id, s.stockitemid, si.name as item, s.serialno, sum(td.qty) as total from transfers as t
						   INNER JOIN transferdetails as td ON t.id = td.transferid
						   INNER JOIN stocks as s ON s.id = td.stockidto
						   INNER JOIN stockitems as si on si.id = s.stockitemid
							 where t.date <= '".$stkdate."' and t.toid = ".$locid."
							 group by s.id,s.stockitemid,s.serialno)
					union all
						   (select s.id, s.stockitemid, si.name as item, s.serialno, -sum(gss.appqty) as total from garageservicespares as gss
						   INNER JOIN garageservices as gs ON gs.id = gss.serviceid
						   INNER JOIN stocks as s ON s.id = gss.stockid
						   INNER JOIN stockitems as si on si.id = s.stockitemid
							 where gss.doa <= '".$stkdate."' and gs.locid = ".$locid."  and gss.status = 'approved'
							 group by s.id,s.stockitemid,s.serialno)
					) as x
					where 1 = 1";
			if ($stockid) $sql .= " and id = " . $stockid;
			if ($prodid) $sql .= " and prodid = " . $prodid;
			$sql .= " group by id, stockitemid, item, serialno";
			// echo '<pre>'.$sql . '<br><br><br>';die();

			return fetchRows($sql);
		}



		function getStockItems($locId="",$status="",$itemName="",$category="",$serialNo="",$stockId=""){
			$sql = "select s.id, s.serialno, si.name as item, si.id as stockitemid, c.name as category
			from stocks as s
			INNER JOIN stockitems as si ON s.stockitemid = si.id
			INNER JOIN fueltanks as d ON s.locid = d.id
			INNER JOIN categories as c on c.id = si.catid
			where 1 = 1 ";

			if ( $locId ) $sql .= " and d.id = " . $locId;
			if ( $status ) $sql .= " and si.status = '" . $status . "'";
			if ( $itemName || $category || $serialNo) $sql .= " and (si.name like '%" . $itemName . "%' or c.name like '%" . $category . "%'or s.serialno like '%" . $serialNo . "%')";
			if ( $stockId ) $sql .= " and s.id = '" . $stockId . "'";
			$sql .= " order by si.name, s.serialno";
			// echo $sql;
			// die();
			return fetchRows($sql);
		}

	}

?>
