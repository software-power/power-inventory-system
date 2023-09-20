<?

logData("EXPIRE NOTIFICATIONS",$script_log_file);
$notification_count = 0;
$locations = $Locations->getAllActive();
foreach ($Products->forExpireNotifications() as $key => $product) {
    foreach ($locations as $i => $location) {
        $notificationReceivers = $Users->locationUserForExpireNotification($location['id']);
//        debug($notificationReceivers);
        $stock = $Stocks->calcStock(
            $location['id'],
            "", "", "",
            $product['id'], "", "",
            "", "", "",
            "", "", "",
            "", "", "",
            "", false, true);
        $stock = array_slice($stock, 0);
//        debug($stock);
        foreach ($stock[0]['batches'] as $j => $batch) {
            if ($product['notify_before_days'] >= $batch['expire_remain_days']) {
                //notify
                foreach ($notificationReceivers as $receiver) {
                    $Notifications->insert([
                        'fromid' => 0,
                        'toid' => $receiver['id'],
                        'about' => Notifications::NOTIFICATION_ABOUT_EXPIRE,
                        'title' => 'Expire Notification',
                        'type'=>Notifications::NOTIFICATION_TYPE_DANGER,
                        'body' => 'Product: ' . $product['description'] . PHP_EOL .
                            'Batch No: ' . $batch['batch_no'] . PHP_EOL .
                            'Quantity: ' . $batch['total'] . PHP_EOL . PHP_EOL .
                            'Remains ' . $batch['expire_remain_days'] . ' days to expire'
                    ]);
                    $notification_count++;
                }
            }
        }
    }
}

logData("Found $notification_count notifications",$script_log_file);