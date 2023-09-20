<?php

class Dashboard extends model
{
    var $table = "";

    static function cachingItems()
    {
        $cache_data = [];
        $fromdate = date('Y-m-d', strtotime(TODAY . ' -1 month'));
        $cache_data['topsales'] = Sales::$saleClass->dashboardMostSold($fromdate, "", "", "", "", "", "", "", 5);

        // Most In Stock
        $locations = Locations::$locationClass->getAllActive();
        $stocksArray = [];
        foreach ($locations as $key => $l) {
			$stocksArray[]  = (array)Stocks::$stockClass->calcStock(
                $l['id'], "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", false, true
            );
        }
        $stocksArray = array_merge(...$stocksArray);
        usort($stocksArray, function ($a, $b) { //asc
            if ($a["total"] == $b["total"]) {
                return 0;
            }
            return ($a["total"] > $b["total"]) ? -1 : 1;
        });
		$cache_data['topProducts'] = array_slice($stocksArray, 0, 6);
		$cache_data['topProductsOut'] = array_slice(array_reverse($stocksArray), 0, 6);
		return $cache_data;
    }
}
