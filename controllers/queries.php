<?

//for transfer
if ($action == 'transfer') {
    $sql = "select * from stock_transfer_details where batch_id is not null";
    $rows = fetchRows($sql);
//    debug($rows);
    foreach ($rows as $index => $row) {
        $StockTransferBatches->insert([
            'stdi' => $row['id'],
            'batch_id' => $row['batch_id'],
            'qty' => $row['quantity'],
        ]);
        global $db_connection;
        $query = "update stock_transfer_details set batch_id = null where id = {$row['id']}";
        mysqli_query($db_connection, $query);
    }

    debug("DONE");
}

//for returns
if ($action == 'returns') {
    $sql = "select * from grnreturn_details where batch_id is not null";
    $rows = fetchRows($sql);
//    debug($rows);
    foreach ($rows as $index => $row) {
        $GrnReturnBatches->insert([
            'grdi' => $row['id'],
            'batch_id' => $row['batch_id'],
            'qty' => $row['quantity'],
        ]);
        global $db_connection;
        $query = "update grnreturn_details set batch_id = null where id = {$row['id']}";
        mysqli_query($db_connection, $query);
    }

    debug("DONE");
}

// TODO AFTER MARCH 28, 2022


if ($action == 'notifications') {
    global $db_connection;
    $query = "update notifications
                    set `about` = case
                                    when `title` like '%expire%' then 'expire'
                                    when `title` like '%stock%' then 'stock'
                                    when `title` like '%supplier%' then 'supplier'
                        end
                    where `about` is null";

    mysqli_query($db_connection, $query);
    debug("DONE");
}

if ($action == 'transfer_approval') {
    global $db_connection;
    $query = "update stock_transfers set approvedby = createdby, doa = doc";
    mysqli_query($db_connection, $query);
    debug('DONE' . PHP_EOL . 'SHOULD BE RUN ONLY ONCE');
}


//  <--- AFTER MAY 2 2022 UPDATES --->

if ($action == 'sales_currency') {
    $saleSql = "select id from sales";
    foreach (fetchRows($saleSql) as $index => $sale) {
        $detailSql = "select rateid from salesdetails where salesid = {$sale['id']} limit 1";
        $detail = fetchRow($detailSql);

        $rateSql = "select rate_amount from currencies_rates where id = {$detail['rateid']}";
        $rate = fetchRow($rateSql);

        global $db_connection;
        $query = "update sales set currency_rateid = {$detail['rateid']},currency_amount = {$rate['rate_amount']} where id = {$sale['id']}";
        mysqli_query($db_connection, $query);
    }

    debug("DONE");
}

if ($action == 'order_value') {
    foreach ($Orders->getAll() as $order) {
        $fullOrder = $Orders->withDetails($order['id'])[0];
        $order_value = array_sum(array_column($fullOrder['details'], 'incamount'));
//        debug([$fullOrder, $order_value]);
        $Orders->update($order['id'], ['order_value' => $order_value]);
    }
    debug('DONE');
}

if ($action == 'fix_bulk_unit_issue') {
//    select p.id,p.name, u.name from products p
//left join units u on p.unit = u.id
//left join bulk_units bu on p.bulk_units = bu.id
//where bu.id is null

    /*
     * QUERY TO FIX PRODUCT BULK ISSUES
     * */
    $query = "select u.* from units u left join bulk_units bu on u.id = bu.unit where bu.id is null group by u.id";
    foreach (fetchRows($query) as $unit) {
        $BulkUnits->insert(['name' => $unit['name'], 'abbr' => $unit['abbr'], 'unit' => $unit['id'], 'rate' => 1]);
        $bulk_unit_id = $BulkUnits->lastId();
        $Products->updateWhere(['unit' => $unit['id']], ['bulk_units' => $bulk_unit_id]);
    }
    debug('DONE');
}

if ($action == 'delete_invoices') {
    debug("Use credit note");
    global $db_connection;
    mysqli_begin_transaction($db_connection);
    try {
        $content = file_get_contents('invoices_fix.json');
        $invoices = json_decode($content, true)['invoices'];
//        debug($invoices);
        $invoices = [
//            'SR-01-11435',
        ];

        $output = [];
        foreach ($invoices as $receipt_no) {
            if (!$sale = $Sales->find(['receipt_no' => $receipt_no])[0]) throw new Exception("Invoice No: " . $receipt_no . " not found!");

            $currency = $CurrenciesRates->getCurrency_rates($sale['currency_rateid']);
            $sale['currencyname'] = $currency ['currencyname'] . " - " . $currency['description'];
            $sale['locationname'] = $Locations->get($sale['locationid'])['name'];
            $sale['creator'] = $Users->get($sale['createdby'])['name'];
            $sale['salesperson'] = $Users->get($sale['salespersonid'])['name'];
            $sale['clientname'] = $Clients->get($sale['clientid'])['name'];
            $sale['details'] = $Salesdetails->find(['salesid' => $sale['id']]);
            foreach ($sale['details'] as $index => $detail) {
                $stock = $Stocks->get($detail['stockid']);
                $sale['details'][$index]['productname'] = $Products->get($stock['productid'] ?: $detail['productid'])['name'];
                $sale['details'][$index]['batches'] = $SalesBatches->find(['sdi' => $detail['id']]);
                $sale['details'][$index]['prescriptions'] = $SalesPrescriptions->find(['sdi' => $detail['id']]);
                $sale['details'][$index]['serialnos'] = $SerialNos->find(['sdi' => $detail['id']]);
            }
            $sale['descriptions'] = $Salesdescriptions->find(['salesid' => $sale['id']]);

            //payments
            $paymentDetail = $SalespaymentDetails->find(['salesid' => $sale['id']])[0];
            $otherDetails = $SalespaymentDetails->find(['salespaymentId' => $paymentDetail['salespaymentId']]);

            if (count($otherDetails) > 1) throw new Exception("Invoice No: $receipt_no has payment transaction that involves multiple invoice!");

            $paymentMaster = $Salespayments->get($paymentDetail['salespaymentId']);
            $paymentMaster['details'] = $otherDetails;
            $paymentMaster['advances'] = $SalePaymentUsedAdvances->find(['spid' => $paymentDetail['salespaymentId']]);
            $sale['payments'] = $paymentMaster;
//            debug($sale['payments']);


            $SalesCanceled->insert([
                'saleid' => $sale['id'],
                'invoiceno' => $sale['receipt_no'],
                'clientid' => $sale['clientid'],
                'locationid' => $sale['locationid'],
                'payload' => base64_encode(json_encode($sale)),
                'createdby' => $_SESSION['member']['id']
            ]);

            //clear sale related info
            foreach ($sale['details'] as $i => $detail) {
                $SalesBatches->deleteWhere(['sdi' => $detail['id']]);
                $SalesPrescriptions->deleteWhere(['sdi' => $detail['id']]);

                //release serialnos
                $SerialNos->updateWhere(['sdi' => $detail['id']], [
                    'sdi' => null,
                    'salespersonid' => null,
                    'dos' => null,
                ]);
            }
            $Salesdetails->deleteWhere(['salesid' => $sale['id']]);
            $Salesdescriptions->deleteWhere(['salesid' => $sale['id']]);
            $SalePaymentUsedAdvances->deleteWhere(['spid' => $paymentDetail['salespaymentId']]);//used advances
            $SalespaymentDetails->deleteWhere(['salespaymentId' => $paymentDetail['salespaymentId']]); //payment details
            $paymentMaster = $Salespayments->real_delete($paymentDetail['salespaymentId']); //payment master
            $Sales->real_delete($sale['id']);
            //debug($sale);

            $output[] = "Invoice {$receipt_no} canceled";
        }
        mysqli_commit($db_connection);
        debug($output);
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        debug($e->getMessage());
    }
}

if ($action == 'transfer_fiscalized_sales') {
    $sql = "select s.* from sales s where s.iscreditapproved = 1   and (s.receipt_method = 'vfd' or s.receipt_method = 'efd')";
    $count = 0;
    foreach (fetchRows($sql) as $sale) {
        if (!$SaleFiscalization->find(['salesid' => $sale['id']])[0]) {
            if ($sale['receipt_method'] = 'efd') {
                $type = SaleFiscalization::TYPE_EFD;
            } elseif ($sale['bridge_invoice_num'] && $sale['companyname']) {
                $type = SaleFiscalization::TYPE_ZVFD;
            } else {
                $type = SaleFiscalization::TYPE_VFD;
            }
            $SaleFiscalization->insert([
                'salesid' => $sale['id'],
                'receipt_num' => $sale['receipt_num'],
                'receipt_date' => $sale['receipt_date'],
                'receiptby' => $sale['receiptby'],
                'rctvcode' => $sale['rctvcode'],
                'znumber' => $sale['znumber'],
                'fiscalize_status_message' => $sale['fiscalize_status_message'],
                'vfd_qrcode' => $sale['vfd_qrcode'],
                'receipt_v_num' => $sale['receipt_v_num'],
                'bridge_invoice_num' => $sale['bridge_invoice_num'],
                'receipt_url' => $sale['receipt_url'],
                'vrnno' => $sale['vrnno'],
                'street' => $sale['street'],
                'tinnumber' => $sale['tinnumber'],
                'companyname' => $sale['companyname'],
                'fiscalization_type' => $type,
            ]);
            $count++;
        }
    }

    debug([
        'status' => 'DONE',
        'message' => $count . ' modified!'
    ]);
}

if ($action == 'transfer_prices') {
    $branchid = $_GET['branchid'];
    $branch = $Branches->get($branchid);
    $count = 0;
    if (empty($branch)) debug("Branch not found!");
    foreach ($Products->getAll() as $product) {
        $priceLog = $PriceLogs->find(['productid' => $product['id'], 'branchid' => $branchid, 'remarks' => 'Initial']);
        if (!empty($priceLog)) continue;
        $PriceLogs->insert([
            'branchid' => $branchid,
            'productid' => $product['id'],
            'costprice' => $product['costprice'],
            'quicksale_price' => $product['quicksale_price'],
            'createdby' => $_SESSION['member']['id'],
            'remarks' => 'Initial'
        ]);
        $logid = $PriceLogs->lastId();
        $CurrentPrices->insert([
            'branchid' => $branchid,
            'productid' => $product['id'],
            'costprice' => $product['costprice'],
            'quicksale_price' => $product['quicksale_price'],
            'createdby' => $_SESSION['member']['id'],
            'logid' => $logid
        ]);
        $count++;
    }

    debug([
        'status' => 'DONE',
        'message' => $count . ' modified!'
    ]);
}

if ($action == 'fix_canceled_invoiceno') {
    $count = 0;
    foreach (SalesCanceled::$saleCanceledClass->getAll() as $sale) {
        $sale['decoded'] = json_decode(base64_decode($sale['payload']), true);
        $invoiceno = $sale['decoded']['receipt_no'];
        SalesCanceled::$saleCanceledClass->update($sale['id'], ['invoiceno' => $invoiceno]);
        $count++;
    }
    debug([
        'status' => 'DONE',
        'message' => $count . ' modified!'
    ]);
}

if ($action == 'quick_price_fix') {
    set_time_limit(0);
    $count = [];

    foreach ($Branches->getAll() as $branch) {
        $branchid = $branch['id'];
        foreach ($CurrentPrices->find(['branchid' => $branchid]) as $item) {

            if ($PriceLogs->find(['id' => $item['logid'], 'remarks' => 'QuickPriceFix0001'])) continue;
            $product = $Products->getList($item['productid'])[0];
            $PriceLogs->insert([
                'branchid' => $branchid,
                'productid' => $product['id'],
                'costprice' => $item['costprice'],
                'quick_price_inc' => addTAX($item['quicksale_price'], $product['vatPercent']),
                'createdby' => $_SESSION['member']['id'],
                'remarks' => 'QuickPriceFix0001'
            ]);
            $logid = $PriceLogs->lastId();
            $CurrentPrices->update($item['id'], [
                'branchid' => $branchid,
                'productid' => $product['id'],
                'costprice' => $item['costprice'],
                'quick_price_inc' => addTAX($item['quicksale_price'], $product['vatPercent']),
                'createdby' => $_SESSION['member']['id'],
                'logid' => $logid
            ]);
            $count[$branch['name']]['count']++;
        }
    }

    debug([
        'status' => 'DONE',
        'modified message' => $count
    ]);
}

//FIX SUPPORT ORDER
if ($action == 'fix_support_orders') {
    $contents = file_get_contents('support_orders.json');
    $support_ordernos = json_decode($contents, true);
//    debug($support_ordernos);
    $result = [];
    foreach ($support_ordernos as $no) {
        $order = Orders::$staticClass->find(['foreign_orderid' => $no, 'order_source' => 'support']);
        if ($order) {
            $response = Orders::postToSupport($order[0]['id']);
            if ($response['status'] == 'success') {
                $result['success']++;
            } else {
                $result['error']++;
				debug([$response,$no]);
            }
        }
    }
    debug($result);
}