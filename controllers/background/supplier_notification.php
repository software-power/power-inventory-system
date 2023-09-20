<?
logData("SUPPLIER PAYMENT NOTIFICATIONS", $script_log_file);
$notification_count = 0;

//notify branch wise
$branches = $Branches->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id");
foreach ($branches as $branch) {
    $notificationReceivers = $Users->branchUserForSupplierNotification($branch['id']);
    if (!$notificationReceivers) continue;
    $supplierOutstandings = $Suppliers->outstandingPayments("", "", "", "", $branch['id']);
    if (!$supplierOutstandings) continue;
    foreach ($supplierOutstandings as $supplier) {
        //notify
        foreach ($notificationReceivers as $receiver) {
            $Notifications->insert([
                'fromid' => 0,
                'toid' => $receiver['id'],
                'about' => Notifications::NOTIFICATION_ABOUT_SUPPLIER,
                'title' => 'Supplier Payment',
                'type' => Notifications::NOTIFICATION_TYPE_WARNING,
                'body' => 'Supplier: ' . $supplier['suppliername'] . PHP_EOL .
                    'pending payment amount: ' . formatN($supplier['outstanding_amount']) . PHP_EOL .
                    'Branch: ' . $branch['name']
            ]);
            $notification_count++;

        }
    }
}
logData("Found $notification_count notifications", $script_log_file);