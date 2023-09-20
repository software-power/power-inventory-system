<?
define('ORDER_SOURCE', ['support']);

if ($action == "save") {
    required_method("POST");
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
//        logData(json_encode($request_data), 'orders.log');
        //validate order request
        $foreign_orderid = removeSpecialCharacters($request_data['orderid']);
        $order_source = removeSpecialCharacters($request_data['order_source']);
        $foreign_ordertype = removeSpecialCharacters($request_data['type']);
        $clientid = removeSpecialCharacters($request_data['clientcode']);
        $locationcode = removeSpecialCharacters($request_data['locationcode']);
        $deptid = removeSpecialCharacters($request_data['deptcode']);
        $validity_days = removeSpecialCharacters($request_data['validity_days']);
        $order_value = removeComma($request_data['order_value']);
        $remarks = removeSpecialCharacters($request_data['remarks']);

        if (!$foreign_orderid) throw new Exception("Invalid order id");
        if (!in_array($order_source, ORDER_SOURCE)) throw new Exception("Invalid order source");
        if (empty($foreign_ordertype)) throw new Exception("Invalid order type");
        if (!$Clients->get($clientid)) throw new Exception("Client not found!");
        if (strlen($locationcode) == 0) throw new Exception("Invalid branchid");
        if (!$Locations->get($locationcode)) throw new Exception("Location not found");
        if (!is_numeric($order_value)) throw new Exception("Invalid order value");

        $new_order = [
            'clientid' => $clientid,
            'foreign_orderid' => $foreign_orderid,
            'order_source' => $order_source,
            'foreign_ordertype' => $foreign_ordertype,
            'locid' => $locationcode,
            'deptid' => $deptid ?: AUTH_USER['deptid'],
            'currencyid' => 1,
            'order_value' => $order_value,
            'print_size' => 'A4',
//            'validity_days' => $validity_days ?: CS_ORDER_VALID_DAYS,
            'validity_days' => 30,
            'createdby' => AUTH_USER['id'],
            'internal_remarks' => $remarks,
        ];

        $order = $Orders->find(['foreign_orderid' => $new_order['foreign_orderid'], 'order_source' => $new_order['order_source']])[0];
//        debug($order);
        if ($order) {
            //check status
            $orderid = $order['id'];
            $order = $Orders->getAllOrders($orderid)[0];

            switch ($order['order_status']) {
                case Orders::STATUS_CLOSED:
                    throw new Exception("Order status already changed, current status '" . strtoupper($order['order_status']) . "'");
                    break;
                case Orders::STATUS_CANCELED:
                    //recreating new order

                    $old_orderid = $orderid;

                    //create new order
                    $Orders->insert($new_order);
                    $orderid = $Orders->lastId(); //overwrite orderid

                    //detach old order
                    Orders::$staticClass->update($old_orderid, [
                        'foreign_orderid' => '',
                        'order_source' => '',
                        'foreign_ordertype' => '',
                        'internal_remarks' => $order['internal_remarks'] . "\r\nCanceled and replaced support order. New order no $orderid",
                        'updated_at' => TIMESTAMP,
                        'updated_by' => AUTH_USER['id'],
                    ]);

                    break;
                default:
                    $Orderdetails->deleteWhere(['orderid' => $orderid]);//clear previous order details
                    $Orders->update($orderid, $new_order);
                    break;
            }


        } else {//new
            $Orders->insert($new_order);
            $orderid = $Orders->lastId();
        }

        if (!$orderid) throw new Exception("Internal error, Order id not found");

        //details
        $details = $request_data['details'];
        if (!is_array($details)) throw new Exception("Invalid order details");
        foreach ($details as $d) {
            $productid = removeSpecialCharacters($d['productcode']);
            if (empty($productid)) throw new Exception("Product {$d['description']} is missing product code");
            if (!$Products->get($productid)) throw new Exception("No product found with code {$d['productcode']} for product {$d['description']}");
            $qty = removeComma($d['qty']);
            if (!is_numeric($qty) || $qty <= 0) throw new Exception("invalid quantity for product {$d['description']}");
            $incprice = removeComma($d['incprice']);
            if (!is_numeric($incprice) || $incprice < 0) throw new Exception("invalid price for product {$d['description']}");
            $vat_rate = removeComma($d['vat_rate']);
            if (!is_numeric($vat_rate) || $vat_rate < 0) throw new Exception("invalid vat rate for product {$d['description']}");

            $price = round($incprice / (1 + $vat_rate / 100), 2);
            $Orderdetails->insert([
                'orderid' => $orderid,
                'productid' => $productid,
                'qty' => $qty,
                'price' => $price,
                'sinc' => 1,
                'incprice' => $incprice,
                'vat_rate' => $vat_rate,
                'createdby' => AUTH_USER['id'],
            ]);
            $cal_order_value += ($incprice * $qty);
        }

        if ($order_value != $cal_order_value) throw new Exception("Calculated order value ($cal_order_value) dont match sent order value ($order_value)");

        $orderDetails = Orders::$staticClass->withDetails($orderid)[0];
        $orderDetails['details'] = array_values($orderDetails['details']); //clear keys in details array
        mysqli_commit($db_connection);
        json_response([
            'status' => 'success',
            'msg' => 'Order ' . (!$order ? 'created' : 'updated') . ' successfully',
            'orderid' => $foreign_orderid,
            'ack_orderid' => $orderid,
            'data' => $orderDetails
        ]);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        json_response(['status' => 'error', 'msg' => $e->getMessage()]);
    }

}

if ($action == 'get_order_status') {
    required_method("GET");
    $foreign_orderid = removeSpecialCharacters($_GET['orderid']);
    $order_source = removeSpecialCharacters($_GET['order_source']);
    try {
        if (!in_array($order_source, ORDER_SOURCE)) throw new Exception("Invalid order source");
        $order = Orders::$staticClass->find(['foreign_orderid' => $foreign_orderid, 'order_source' => $order_source]);
        if (empty($order)) throw new Exception("Order not found!");
        $orderid = $order[0]['id'];
        $order = Orders::$staticClass->getAllOrders($orderid)[0];
//        debug($order);
        switch ($order['order_status']) {
            case Orders::STATUS_CLOSED:
                $sale = Sales::$saleClass->salesList($order['salesid'])[0];
                $process_type = Orders::PROCESS_TYPE_INVOICE;
                if ($sale['iscreditapproved']) {
                    if ($sale['receipt_method'] == 'sr') {
                        $process_type = Orders::PROCESS_TYPE_NORMALRECEIPT;
                    } else {
                        if ($sale['isfiscalized']) {
                            $fisc_sale = Sales::$saleClass->salesWithFiscalization($order['salesid']);
                            if ($fisc_sale['fiscalization_type'] == 'vfd' && $fisc_sale['fiscalize_status_message'] == 'Success') {
                                $process_type = Orders::PROCESS_TYPE_VFDRECEIPT;
                                $rctvcode = $fisc_sale['rctvcode'];
                                $znumber = $fisc_sale['znumber'];
                                $vfd_qrcode = $fisc_sale['receipt_v_num']; //overridden
                                $vfd_response = $fisc_sale['fiscalize_status_message'];
                            }
                        }
                    }
                }
                json_response([
                    'status' => 'success',
                    'order_status' => $order['order_status'],
                    'orderid' => $foreign_orderid,
                    'ack_orderid' => $orderid,
                    'type' => $order['foreign_ordertype'],
                    'invoiceno' => $sale['receipt_no'],
                    'process_type' => $process_type,
                    'amount' => $sale['full_amount'],
                    'rctvcode' => $rctvcode ?: '',
                    'znumber' => $znumber ?: '',
                    'vfd_qrcode' => $vfd_qrcode ?: '',
                    'vfd_response' => $vfd_response ?: '',
                ]);
                break;
            default:
                json_response([
                    'status' => 'success',
                    'order_status' => $order['order_status'],
                    'orderid' => $foreign_orderid,
                    'ack_orderid' => $orderid,
                    'type' => $order['foreign_ordertype'],
                    'invoiceno' => '',
                    'process_type' => '',
                    'amount' => '',
                    'rctvcode' => '',
                    'znumber' => '',
                    'vfd_qrcode' => '',
                    'vfd_response' => '',
                ]);
        }
    } catch (Exception $e) {
        json_response(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}
