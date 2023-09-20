<?
if ($action == "system_receipt") {
    $salesid = removeSpecialCharacters($_GET['salesno']);
    $sale = Sales::$saleClass->get($salesid);
    if ($sale) {
        $sale = $Sales->salesList($salesid)[0];
        if ($sale['paymenttype'] == PAYMENT_TYPE_CASH && CS_SHOW_CHANGE) {
            $payments = SalesPayments::$salePaymentClass->getSalesPayment($salesid)[0];
            $change = $payments['handed_amount'] - $payments['paid_totalmount'];
            if ($change > 0) {
                $sale['handed_amount'] = $payments['handed_amount'];
                $sale['change_amount'] = $change;
            }
        }

        $sale['products'] = $Sales->getProductListForFiscalize($salesid);
        $location = $Locations->get($sale['locationid']);
        $address = $location['address'];
        $address = explode(PHP_EOL, $address);
        if (count($address) > 1) {
            define("LOCATION_ADDRESS", $address);
        }
        if (isset($_GET['with_bank_info'])) {
            $bankids = explode(',', $location['bankids']);
            $banks = Banks::$banksClass->findMany(['id' => $bankids]);
            $data['banks'] = $banks;
        }
        $data['sale'] = $sale;
        if (isset($_GET['print_size'])) $sale['print_size'] = $_GET['print_size'];
        if ($sale['print_size'] == 'A4') {
            $data['layout'] = "system_A4_receipt.tpl.php";
        } else {
            $data['layout'] = "system_small_receipt.tpl.php";
        }

    } else {
        die("<h2>Invoice Not Found</h2>" . PHP_EOL .
            "<a href='?'>Home</a>");
    }
}

if ($action == 'zvfd_local') {
    $salesid = $_GET['salesid'];
    if ($sale = $Sales->get($salesid)) {
        $sale['currencyname'] = $CurrenciesRates->getCurrency_rates($sale['currency_rateid'])['currencyname'];
        $sale['client'] = $Clients->get($sale['clientid']);
        $sale['salescounter'] = $Users->get($sale['createdby']);
        $sale['products'] = $Sales->getProductListForFiscalize($salesid);
        // debug($tData['sales_details']);
//        debug($sale);

        $file_path = "zvfdsample.pdf";
        $data['url'] = $file_path;
            $data['layout'] = 'print_zvfd_receipt.tpl.php';
    } else {
        die("<h2>Invoice Not Found</h2>" . PHP_EOL .
            "<a href='?'>Home</a>");
    }
}

if ($action == 'vfd' || $action == 'tax_invoice_with_serialno') {
    $salesid = $_GET['salesid'];
    if (!$sale = $Sales->salesWithFiscalization($salesid)) {
        debug('Invoice not found!');
    }

    if ($sale['paymenttype'] == PAYMENT_TYPE_CASH && CS_SHOW_CHANGE) {
        $payments = SalesPayments::$salePaymentClass->getSalesPayment($salesid)[0];
        $change = $payments['handed_amount'] - $payments['paid_totalmount'];
//        $sale['change_amount'] = $change > 0 ? $change : 0;
        if ($change > 0) {
            $sale['handed_amount'] = $payments['handed_amount'];
            $sale['change_amount'] = $change;
        }
    }

    if (isset($_GET['print_size'])) $sale['print_size'] = $_GET['print_size'];
    if ($sale['print_size'] == 'A4') {
        $sale['products'] = $Sales->getProductListForFiscalize($salesid);
        $location = $Locations->get($sale['locationid']);

        if ($sale['fiscalization_type'] == 'zvfd') {
            define("LOCATION_ADDRESS", [
                "companyname" => $sale['companyname'],
                "street" => "Street: " . $sale['street'],
                "tinnumber" => "TIN: " . $sale['tinnumber'],
            ]);
        } else {
            $address = $location['address'];
            $address = explode(PHP_EOL, $address);
            if (count($address) > 1) {
                define("LOCATION_ADDRESS", $address);
            }
        }

        if ($action == 'tax_invoice_with_serialno') {
            foreach ($sale['products'] as $p) {
                if ($p['trackserialno']) {
                    $numbers = array_column(SerialNos::$serialNoClass->find(['sdi' => $p['sdi']]), 'number');
                    if (count($numbers) > 0)
                        $sale['serialnos'][] = [
                            'productname' => CS_PRINTING_SHOW_DESCRIPTION ? ($p['print_extra'] ? $p['extra_description'] : $p['productdescription']) : $p['productname'],
                            'numbers' => $numbers
                        ];
                }
            }
        }

        if (isset($_GET['with_bank_info'])) {
            $bankids = explode(',', $location['bankids']);
            $banks = Banks::$banksClass->findMany(['id' => $bankids]);
            $data['banks'] = $banks;
        }

//        debug($sale);
        $data['sale'] = $sale;
        $data['layout'] = "sales_tax_invoice.tpl.php";
    } else {
        $sale['items'] = $Sales->getProductListForFiscalize($salesid);
        if ($sale['fiscalization_type'] == 'zvfd') {
//            $url = getZvfdReceiptUrl($sale['rctvcode'], $sale['receipt_url']);
//            $data['url'] = $url;
//            $data['layout'] = 'print_zvfd_receipt.tpl.php';
//        debug($sale);

            $data['sale'] = $sale;
            $data['layout'] = "zvfd_small_receipt.tpl.php";
        } else {
            $tData['sale'] = $sale;
            $data['content'] = loadTemplate('tra_receipt_print.tpl.php', $tData);  //tra receipt
        }
    }
}

if ($action == 'print_installment_plan') {
    $salesid = $_GET['salesid'];
    if (!$sale = $Sales->salesWithFiscalization($salesid)) debug('Invoice not found!');
    if (!$sale['has_installment']) debug("Invoice does not have installment plans");
    $sale['products'] = $Sales->getProductListForFiscalize($salesid);
    $address = $Locations->get($sale['locationid'])['address'];
    $address = explode(PHP_EOL, $address);
    if (count($address) > 1) {
        define("LOCATION_ADDRESS", $address);
    }

    $sale['installment_plans'] = SalesInstallmentPlans::$staticClass->withStatus($salesid);
//    debug($sale);
    $data['sale'] = $sale;
    $data['layout'] = "invoice_installment_plan_print.tpl.php";
}

if ($action == 'print_order') {
    $orderno = $_GET['orderno'];
    if ($order = $Orders->withDetails($orderno)[0]) {
        $location = $Locations->get($order['locid']);
        $address = $location['address'];
        $address = explode(PHP_EOL, $address);
        if (count($address) > 1) {
            define("LOCATION_ADDRESS", $address);
        }

        if (isset($_GET['print_size'])) $order['print_size'] = $_GET['print_size'];
        $data['order'] = $order;

        if (isset($_GET['with_bank_info'])) {
            $bankids = explode(',', $location['bankids']);
            $banks = Banks::$banksClass->findMany(['id' => $bankids]);
            $data['banks'] = $banks;
        }
//        debug($data);
        if ($order['print_size'] == 'A4') {
            if (isset($_GET['efd'])) {
                $data['client'] = $Clients->get($order['clientid']);
                $data['contact'] = json_decode(base64_decode($_GET['contacts']), 1);
//                debug([$data['contact'],$data['client'],$order]);
                $data['layout'] = 'efd_order_form_print.tpl.php';
            } else {
                $data['layout'] = 'normal_order_print.tpl.php';
            }
        } else {
            $data['layout'] = 'quick_order_receipt.tpl.php';
        }
    } else {
        debug('Order not found!');
    }
}

if ($action == 'print_delivery' || $action == 'print_delivery_with_serialno') {
    $salesid = $_GET['salesno'];
    if ($sale = $Sales->salesList($salesid)[0]) {
        $sale['products'] = $Sales->getProductListForFiscalize($salesid);
        $address = $Locations->get($sale['locationid'])['address'];
        $address = explode(PHP_EOL, $address);
        if (count($address) > 1) {
            define("LOCATION_ADDRESS", $address);
        }

        if ($action == 'print_delivery_with_serialno') {
            foreach ($sale['products'] as $p) {
                if ($p['trackserialno']) {
                    $numbers = array_column(SerialNos::$serialNoClass->find(['sdi' => $p['sdi']]), 'number');
                    if (count($numbers) > 0)
                        $sale['serialnos'][] = [
                            'productname' => CS_PRINTING_SHOW_DESCRIPTION ? ($p['print_extra'] ? $p['extra_description'] : $p['productdescription']) : $p['productname'],
                            'numbers' => $numbers
                        ];
                }
            }
        }
        $data['sale'] = $sale;
//        debug($sale);
        $data['layout'] = "delivery_note_print.tpl.php";
    } else {
        debug("Invoice not found!");
    }
}