<?
logData("STOCK LEVEL NOTIFICATIONS",$script_log_file);
$notification_count = 0;

$locations = $Locations->getAllActive();
foreach ($Products->forStockNotifications() as $key => $product) {
    foreach ($locations as $i => $location) {
        $notificationReceivers = $Users->locationUserForStockLevelNotification($location['id']);
//        debug($notificationReceivers);
        $stock = $Stocks->calcStock(
            $location['id'],
            "", "", "",
            $product['id'], "", "",
            "", "", "",
            "", "", "",
            "", "", "",
            "", false, true,"","",false);
        if (empty($stock)) continue;
        $stock = array_slice($stock, 0);
        $level = $Products->reorderList($stock[0]['id']);
        if ($stock[0]['total'] < $level[0]['minqty']) {
            //notify below level
            foreach ($notificationReceivers as $receiver) {
                $Notifications->insert([
                    'fromid' => 0,
                    'toid' => $receiver['id'],
                    'about' => Notifications::NOTIFICATION_ABOUT_STOCK,
                    'title' => 'Stock Level Notification',
                    'type' => Notifications::NOTIFICATION_TYPE_WARNING,
                    'body' => 'Product: ' . $product['description'] . PHP_EOL .
                        'Quantity: ' . $stock[0]['total'] . PHP_EOL . PHP_EOL .
                        'Stock quantity is below minimum quantity (' . $level[0]['minqty'] . ')'
                ]);
                $notification_count++;
            }
        }
        if ($stock[0]['total'] > $level[0]['maxqty']) {
            //notify above level
            foreach ($notificationReceivers as $receiver) {
                $Notifications->insert([
                    'fromid' => 0,
                    'toid' => $receiver['id'],
                    'about' => Notifications::NOTIFICATION_ABOUT_STOCK,
                    'title' => 'Stock Level Notification',
                    'type' => Notifications::NOTIFICATION_TYPE_SUCCESS,
                    'body' => 'Product: ' . $product['description'] . PHP_EOL .
                        'Quantity: ' . $stock[0]['total'] . PHP_EOL . PHP_EOL .
                        'Stock quantity exceed maximum quantity (' . $level[0]['maxqty'] . ')'
                ]);
                $notification_count++;
            }
        }
    }
}

logData("Found $notification_count notifications",$script_log_file);