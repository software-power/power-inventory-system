<?

//create ledger to tally
function createTallyLedger($parent_group, $new_name = "", $old_name = "")
{
    $new_name = htmlspecialchars($new_name);
    $old_name = htmlspecialchars($old_name);
    $parent_group = htmlspecialchars($parent_group);
    $tally_company = CS_TALLY_COMPANY_NAME;
    $requestXML = "
    <ENVELOPE>
          <HEADER>
            <TALLYREQUEST>Import Data</TALLYREQUEST>
          </HEADER>
          <BODY>
            <IMPORTDATA>
              <REQUESTDESC>
                <REPORTNAME>All Masters</REPORTNAME>
                <STATICVARIABLES>
                    <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
                </STATICVARIABLES>
              </REQUESTDESC>
              <REQUESTDATA>
                <TALLYMESSAGE xmlns:UDF=\"TallyUDF\">
                <LEDGER NAME='$old_name' ACTION='Alter'>
                <NAME.LIST>
                <NAME>$new_name</NAME>
                </NAME.LIST>
                <PARENT>$parent_group</PARENT>
                </LEDGER>
                </TALLYMESSAGE>
              </REQUESTDATA>
            </IMPORTDATA>
          </BODY>
        </ENVELOPE>
    ";

    $result = xml2Tally($requestXML);
    if ($result['status'] == 'success') {
        $xml = (array)simplexml_load_string($result['data']);
        return $xml['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error " . $xml['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally invoice voucher
function createInvoiceVoucher($post_data)
{
    $tally_company = CS_TALLY_COMPANY_NAME;
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Sales' ACTION='Create'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <NARRATION>{$post_data['narration']}</NARRATION>
                    <VOUCHERTYPENAME>Sales</VOUCHERTYPENAME>
                    <PARTYNAME>{$post_data['clientname']}</PARTYNAME>
                    <REFERENCE>{$post_data['invoiceno']}</REFERENCE>
                    <VOUCHERNUMBER>{$post_data['invoiceno']}</VOUCHERNUMBER>
                    <ISINVOICE>No</ISINVOICE>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['dr_ledgername']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-{$post_data['full_amount']}</AMOUNT>";
    if ($post_data['paymenttype'] == PAYMENT_TYPE_CREDIT) {
        $requestXML .= "
                        <BILLALLOCATIONS.LIST>
                            <NAME>{$post_data['invoiceno']}</NAME>
                            <BILLTYPE>New Ref</BILLTYPE>
                            <AMOUNT>-{$post_data['full_amount']}</AMOUNT>
                        </BILLALLOCATIONS.LIST>";
    }
    if ($post_data['paymentmethod'] == PaymentMethods::CREDIT_CARD) {
        $requestXML .= "
                        <CATEGORYALLOCATIONS.LIST>
                            <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                            <COSTCENTREALLOCATIONS.LIST>
                                <NAME>{$post_data['cost_center']}</NAME>
                                <AMOUNT>-{$post_data['full_amount']}</AMOUNT>
                            </COSTCENTREALLOCATIONS.LIST>
                        </CATEGORYALLOCATIONS.LIST>
                        <BANKALLOCATIONS.LIST>
                            <DATE>{$post_data['date']}</DATE>
                            <TRANSACTIONTYPE>Others</TRANSACTIONTYPE>
                            <AMOUNT>-{$post_data['full_amount']}</AMOUNT>
                        </BANKALLOCATIONS.LIST>";
    }

    $requestXML .= "
                    </ALLLEDGERENTRIES.LIST>";
    foreach ($post_data['sales_accounts'] as $acc => $amount) {
        $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>$acc</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                        <AMOUNT>$amount</AMOUNT>
                        <CATEGORYALLOCATIONS.LIST>
                            <CATEGORY>DEPARTMENTS</CATEGORY>
                            <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                            <COSTCENTREALLOCATIONS.LIST>
                                <NAME>{$post_data['cost_center']}</NAME>
                                <AMOUNT>$amount</AMOUNT>
                            </COSTCENTREALLOCATIONS.LIST>
                        </CATEGORYALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>";
    }

    $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>Vat</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                        <AMOUNT>{$post_data['vatamount']}</AMOUNT>
                    </ALLLEDGERENTRIES.LIST>
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";

//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);

    if ($result['status'] == 'success') {
        $xml = simplexml_load_string($result['data']);
        $body_data = (array)$xml->BODY->DATA;
        return $body_data['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error, " . $body_data['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally receipt voucher
function createAdvanceReceiptVoucher($post_data, $alter = false)
{
    $tally_company = CS_TALLY_COMPANY_NAME;
    $action = $alter ? 'Alter' : 'Create';
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Receipt' ACTION='$action'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <NARRATION>{$post_data['narration']}</NARRATION>
                    <VOUCHERTYPENAME>Receipt</VOUCHERTYPENAME>
                    <PARTYLEDGERNAME>{$post_data['clientname']}</PARTYLEDGERNAME>
                    <REFERENCE>{$post_data['receiptno']}</REFERENCE>
                    <VOUCHERNUMBER>{$post_data['receiptno']}</VOUCHERNUMBER>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['clientname']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                        <AMOUNT>{$post_data['amount']}</AMOUNT>
                        <BILLALLOCATIONS.LIST>
                            <NAME>{$post_data['receiptno']}</NAME>
                            <BILLTYPE>New Ref</BILLTYPE>
                            <AMOUNT>{$post_data['amount']}</AMOUNT>
                        </BILLALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>
                    
                    <ALLLEDGERENTRIES.LIST>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>";
    if ($post_data['paymentmethod'] == PaymentMethods::CASH) {
        $requestXML .= "     
                        <LEDGERNAME>{$post_data['dr_ledgername']}</LEDGERNAME>
                        <AMOUNT>-{$post_data['amount']}</AMOUNT>";
    } else {
        $requestXML .= "     
                        <LEDGERNAME>{$post_data['dr_ledgername']}</LEDGERNAME>
                        <AMOUNT>-{$post_data['amount']}</AMOUNT>
                        <BANKALLOCATIONS.LIST>
                            <DATE>{$post_data['date']}</DATE>
                            <TRANSACTIONTYPE>Others</TRANSACTIONTYPE>
                            <AMOUNT>-{$post_data['amount']}</AMOUNT>
                        </BANKALLOCATIONS.LIST>";
    }

    $requestXML .= "     
                    </ALLLEDGERENTRIES.LIST>";

    $requestXML .= "
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";

//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);

    if ($result['status'] == 'success') {
        $xml = simplexml_load_string($result['data']);
        $body_data = (array)$xml->BODY->DATA;
        return $body_data['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error, " . $body_data['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally receipt voucher
function createReceiptVoucher($post_data, $alter = true)
{
    $action = $alter ? 'Alter' : 'Create';
    $tally_company = CS_TALLY_COMPANY_NAME;
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Receipt' ACTION='$action'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <NARRATION>{$post_data['narration']}</NARRATION>
                    <VOUCHERTYPENAME>Receipt</VOUCHERTYPENAME>
                    <PARTYLEDGERNAME>{$post_data['clientname']}</PARTYLEDGERNAME>
                    <REFERENCE>{$post_data['receiptno']}</REFERENCE>
                    <VOUCHERNUMBER>{$post_data['receiptno']}</VOUCHERNUMBER>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['clientname']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                        <AMOUNT>{$post_data['total_amount']}</AMOUNT>";
    foreach ($post_data['bills'] as $bill) {
        $requestXML .= "
                       <BILLALLOCATIONS.LIST>
                            <NAME>{$bill['invoiceno']}</NAME>
                            <BILLTYPE>Agst Ref</BILLTYPE>
                            <AMOUNT>{$bill['amount']}</AMOUNT>
                        </BILLALLOCATIONS.LIST>";
    }

    $requestXML .= "
                    </ALLLEDGERENTRIES.LIST>";

    if ($post_data['received']) {
        $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>";
        if ($post_data['received']['method'] == PaymentMethods::CHEQUE || $post_data['received']['method'] == PaymentMethods::BANK) {
            $requestXML .= "     
                        <LEDGERNAME>{$post_data['received']['dr_ledgername']}</LEDGERNAME>
                        <AMOUNT>-{$post_data['received']['amount']}</AMOUNT>
                        <BANKALLOCATIONS.LIST>
                            <DATE>{$post_data['date']}</DATE>
                            <TRANSACTIONTYPE>Others</TRANSACTIONTYPE>
                            <AMOUNT>-{$post_data['received']['amount']}</AMOUNT>
                        </BANKALLOCATIONS.LIST>";
        } else {
            $requestXML .= "     
                        <LEDGERNAME>{$post_data['received']['dr_ledgername']}</LEDGERNAME>
                        <AMOUNT>-{$post_data['received']['amount']}</AMOUNT>";
        }

        $requestXML .= "     
                    </ALLLEDGERENTRIES.LIST>";
    }

    foreach ($post_data['advance'] as $id => $a) {
        $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>";
        if ($a['method'] == PaymentMethods::CHEQUE || $a['method'] == PaymentMethods::BANK) {
            $requestXML .= "     
                        <LEDGERNAME>{$a['cr_ledgername']}</LEDGERNAME>
                        <AMOUNT>-{$a['amount']}</AMOUNT>
                        <BANKALLOCATIONS.LIST>
                            <DATE>{$post_data['date']}</DATE>
                            <TRANSACTIONTYPE>Others</TRANSACTIONTYPE>
                            <AMOUNT>-{$a['amount']}</AMOUNT>
                        </BANKALLOCATIONS.LIST>";
        } else {
            $requestXML .= "     
                        <LEDGERNAME>{$a['cr_ledgername']}</LEDGERNAME>
                        <AMOUNT>-{$a['amount']}</AMOUNT>";
        }

        $requestXML .= "     
                    </ALLLEDGERENTRIES.LIST>";
    }

    $requestXML .= "
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";

//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);

    if ($result['status'] == 'success') {
        $xml = simplexml_load_string($result['data']);
        $body_data = (array)$xml->BODY->DATA;
        return $body_data['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error, " . $body_data['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally payment against advance voucher
function createPaymentAgainstAdvanceVoucher($post_data, $alter = true)
{
    $action = $alter ? 'Alter' : 'Create';
    $tally_company = CS_TALLY_COMPANY_NAME;
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Payment' ACTION='$action'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <VOUCHERTYPENAME>Payment</VOUCHERTYPENAME>
                    <PARTYLEDGERNAME>{$post_data['clientname']}</PARTYLEDGERNAME>
                    <REFERENCE>{$post_data['usedadvanceno']}</REFERENCE>
                    <VOUCHERNUMBER>{$post_data['usedadvanceno']}</VOUCHERNUMBER>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['clientname']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-{$post_data['amount']}</AMOUNT>
                        <BILLALLOCATIONS.LIST>
                            <NAME>{$post_data['advance_receiptno']}</NAME>
                            <BILLTYPE>Agst Ref</BILLTYPE>
                            <AMOUNT>-{$post_data['amount']}</AMOUNT>
                        </BILLALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>
                    
                    <ALLLEDGERENTRIES.LIST>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>";
    if ($post_data['paymentmethod'] == PaymentMethods::CHEQUE || $post_data['paymentmethod'] == PaymentMethods::BANK) {
        $requestXML .= "     
                        <LEDGERNAME>{$post_data['cr_ledgername']}</LEDGERNAME>
                        <AMOUNT>{$post_data['amount']}</AMOUNT>
                        <BANKALLOCATIONS.LIST>
                            <DATE>{$post_data['date']}</DATE>
                            <TRANSACTIONTYPE>Others</TRANSACTIONTYPE>
                            <AMOUNT>{$post_data['amount']}</AMOUNT>
                        </BANKALLOCATIONS.LIST>";
    } else {
        $requestXML .= "     
                        <LEDGERNAME>{$post_data['cr_ledgername']}</LEDGERNAME>
                        <AMOUNT>{$post_data['amount']}</AMOUNT>";
    }

    $requestXML .= "     
                    </ALLLEDGERENTRIES.LIST>";

    $requestXML .= "
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";
//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);

    if ($result['status'] == 'success') {
        $xml = simplexml_load_string($result['data']);
        $body_data = (array)$xml->BODY->DATA;
        return $body_data['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error, " . $body_data['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally payment against advance voucher
function createPaymentAgainstCreditNoteVoucher($post_data)
{
    $tally_company = CS_TALLY_COMPANY_NAME;
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Payment' ACTION='Alter'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <VOUCHERTYPENAME>Payment</VOUCHERTYPENAME>
                    <PARTYLEDGERNAME>{$post_data['clientname']}</PARTYLEDGERNAME>
                    <REFERENCE>{$post_data['voucherno']}</REFERENCE>
                    <VOUCHERNUMBER>{$post_data['voucherno']}</VOUCHERNUMBER>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['dr_ledgername']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-{$post_data['amount']}</AMOUNT>
                        <BILLALLOCATIONS.LIST>
                            <NAME>{$post_data['agst']}</NAME>
                            <BILLTYPE>Agst Ref</BILLTYPE>
                            <AMOUNT>-{$post_data['amount']}</AMOUNT>
                        </BILLALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>
                    
                    <ALLLEDGERENTRIES.LIST>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>";
    if ($post_data['paymentmethod'] == PaymentMethods::CHEQUE || $post_data['paymentmethod'] == PaymentMethods::BANK) {
        $requestXML .= "     
                        <LEDGERNAME>{$post_data['cr_ledgername']}</LEDGERNAME>
                        <AMOUNT>{$post_data['amount']}</AMOUNT>
                        <BANKALLOCATIONS.LIST>
                            <DATE>{$post_data['date']}</DATE>
                            <TRANSACTIONTYPE>Others</TRANSACTIONTYPE>
                            <AMOUNT>{$post_data['amount']}</AMOUNT>
                        </BANKALLOCATIONS.LIST>";
    } else {
        $requestXML .= "     
                        <LEDGERNAME>{$post_data['cr_ledgername']}</LEDGERNAME>
                        <AMOUNT>{$post_data['amount']}</AMOUNT>";
    }

    $requestXML .= "     
                    </ALLLEDGERENTRIES.LIST>";

    $requestXML .= "
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";
//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);

    if ($result['status'] == 'success') {
        $xml = simplexml_load_string($result['data']);
        $body_data = (array)$xml->BODY->DATA;
        return $body_data['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error, " . $body_data['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally expense voucher
function createExpenseVoucher($post_data,$alter = false)
{
    $action = $alter?'Alter':'Create';
    $tally_company = CS_TALLY_COMPANY_NAME;
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Payment' ACTION='$action'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <NARRATION>{$post_data['narration']}</NARRATION>
                    <VOUCHERTYPENAME>Payment</VOUCHERTYPENAME>
                    <REFERENCE>{$post_data['expenseno']}</REFERENCE>
                    <VOUCHERNUMBER>{$post_data['expenseno']}</VOUCHERNUMBER>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['tally_cash_ledger']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                        <AMOUNT>{$post_data['total_amount']}</AMOUNT>
                    </ALLLEDGERENTRIES.LIST>";
    foreach ($post_data['details'] as $d) {
        $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$d['ledgername']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-{$d['amount']}</AMOUNT>
                        <CATEGORYALLOCATIONS.LIST>
                            <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                            <COSTCENTREALLOCATIONS.LIST>
                                <CATEGORY>DEPARTMENTS</CATEGORY>
                                <NAME>{$post_data['cost_center']}</NAME>
                                <AMOUNT>-{$d['amount']}</AMOUNT>
                            </COSTCENTREALLOCATIONS.LIST>
                        </CATEGORYALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>";
    }

    $requestXML .= "
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";

//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);
    if ($result['status'] == 'success') {
        if (strpos($result['data'], 'Unknown Request, cannot be processed') !== false) {
            $result_error = "Error sending to tally check XML";
        } else {
            $xml = simplexml_load_string($result['data']);
            $xml = json_decode(json_encode($xml), true);
            $result_error = $xml['BODY']['DATA']['LINEERROR'];
        }
//        debug($result_error);
        return $result_error
            ? ['status' => 'error', 'msg' => "Tally error, " . $result_error]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally purchase voucher
function createPurchaseVoucher($post_data, $alter = true)
{
    $tally_company = CS_TALLY_COMPANY_NAME;
    $action = $alter ? 'Alter' : 'Create';
    $voucherno = $post_data['reference'] ?: $post_data['voucherno'];
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Purchase' ACTION='$action'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <NARRATION>{$post_data['narration']}</NARRATION>
                    <VOUCHERTYPENAME>Purchase</VOUCHERTYPENAME>
                    <REFERENCE>$voucherno</REFERENCE>
                    <VOUCHERNUMBER>$voucherno</VOUCHERNUMBER>
                    <VATBRIEFDESCRIPTION>{$post_data['vat_desc']}</VATBRIEFDESCRIPTION>
                    <VCHENTRYMODE>As Voucher</VCHENTRYMODE>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['suppliername']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                        <AMOUNT>{$post_data['totalamount']}</AMOUNT>
                        <BILLALLOCATIONS.LIST>
                                <NAME>{$post_data['voucherno']}</NAME>
                                <BILLTYPE>New Ref</BILLTYPE>
                                <AMOUNT>{$post_data['totalamount']}</AMOUNT>
                            </BILLALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>";

    if ($post_data['adjustment']) {
        $adjustment = $post_data['adjustment'];
        $adjustment['positive'] = $adjustment['dr_cr'] == 'dr' ? 'Yes' : 'No';
        $adjustment['amount'] = ($adjustment['dr_cr'] == 'dr' ? '-' : '') . $adjustment['amount'];
        $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$adjustment['ledgername']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>{$adjustment['positive']}</ISDEEMEDPOSITIVE>
                        <AMOUNT>{$adjustment['amount']}</AMOUNT>
                    </ALLLEDGERENTRIES.LIST>";
    }

    $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['purchase_account']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-{$post_data['excamount']}</AMOUNT>
                    </ALLLEDGERENTRIES.LIST>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>Vat</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-{$post_data['vatamount']}</AMOUNT>
                    </ALLLEDGERENTRIES.LIST>
                    <UDF:VERCODE.LIST DESC='`VerCode`' ISLIST='YES' TYPE='String' INDEX='7844'>
                        <UDF:VERCODE DESC='`VerCode`'>{$post_data['vercode']}</UDF:VERCODE>
                    </UDF:VERCODE.LIST>
                    ";

    $requestXML .= "
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";

//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);

    if ($result['status'] == 'success') {
        $xml = simplexml_load_string($result['data']);
        $body_data = (array)$xml->BODY->DATA;
        return $body_data['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error, " . $body_data['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

//create tally credit note voucher
function createCreditNoteVoucher($post_data, $alter = true)
{
    $action = $alter ? 'Alter' : 'Create';
    $tally_company = CS_TALLY_COMPANY_NAME;
    $requestXML = "
    <ENVELOPE>
    <HEADER>
        <VERSION>1</VERSION>
        <TALLYREQUEST>Import</TALLYREQUEST>
        <TYPE>Data</TYPE>
        <ID>Vouchers</ID>
    </HEADER>
    <BODY>
        <DESC>
            <STATICVARIABLES>
                <SVCURRENTCOMPANY>$tally_company</SVCURRENTCOMPANY>
            </STATICVARIABLES>
        </DESC>
        <DATA>
            <TALLYMESSAGE>
                <VOUCHER REMOTEID='{$post_data['trxno']}' VCHTYPE='Credit Note' ACTION='$action'>
                    <GUID>{$post_data['trxno']}</GUID>
                    <DATE>{$post_data['date']}</DATE>
                    <NARRATION>{$post_data['narration']}</NARRATION>
                    <VOUCHERTYPENAME>Credit Note</VOUCHERTYPENAME>
                    <PARTYNAME>{$post_data['clientname']}</PARTYNAME>
                    <REFERENCE>{$post_data['returnno']}</REFERENCE>
                    <VOUCHERNUMBER>{$post_data['returnno']}</VOUCHERNUMBER>
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>{$post_data['cr_ledgername']}</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                        <AMOUNT>{$post_data['total_amount']}</AMOUNT>
                        <BILLALLOCATIONS.LIST>
                            <NAME>{$post_data['invoiceno']}</NAME>
                            <BILLTYPE>Agst Ref</BILLTYPE>
                            <AMOUNT>{$post_data['total_amount']}</AMOUNT>
                        </BILLALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>";

    foreach ($post_data['sales_accounts'] as $acc => $amount) {
        $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>$acc</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-$amount</AMOUNT>
                        <CATEGORYALLOCATIONS.LIST>
                            <ISDEEMEDPOSITIVE>No</ISDEEMEDPOSITIVE>
                            <COSTCENTREALLOCATIONS.LIST>
                                <CATEGORY>DEPARTMENTS</CATEGORY>
                                <NAME>{$post_data['cost_center']}</NAME>
                                <AMOUNT>-$amount</AMOUNT>
                            </COSTCENTREALLOCATIONS.LIST>
                        </CATEGORYALLOCATIONS.LIST>
                    </ALLLEDGERENTRIES.LIST>";
    }

    $requestXML .= "
                    <ALLLEDGERENTRIES.LIST>
                        <LEDGERNAME>Vat</LEDGERNAME>
                        <ISDEEMEDPOSITIVE>Yes</ISDEEMEDPOSITIVE>
                        <AMOUNT>-{$post_data['vatamount']}</AMOUNT>
                    </ALLLEDGERENTRIES.LIST>
                </VOUCHER>
            </TALLYMESSAGE>
        </DATA>
    </BODY>
</ENVELOPE>
    ";

//    debug(htmlspecialchars($requestXML));

    $result = xml2Tally($requestXML);

    if ($result['status'] == 'success') {
        $xml = simplexml_load_string($result['data']);
        $body_data = (array)$xml->BODY->DATA;
        return $body_data['LINEERROR']
            ? ['status' => 'error', 'msg' => "Tally error, " . $body_data['LINEERROR']]
            : ['status' => 'success', 'msg' => 'Tally success'];
    } else {
        return ['status' => 'error', 'msg' => 'Error Connecting to tally'];
    }
}

function currency_amount($amount, $basecurrency, $foreign_currency, $exchange_rate, $deem_positive = false)
{
    if ($basecurrency == $foreign_currency) {
        return ($deem_positive ? "-" : "") . $amount;
    } else {
        return ($deem_positive ? "-" : "") . "$amount$foreign_currency @ $basecurrency $exchange_rate /$foreign_currency =" . ($deem_positive ? "-" : "") . "$basecurrency $amount";
    }
}

function pingTally()
{
    try {
        $server = CS_TALLY_SERVER;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        $data = curl_exec($ch);

        if (curl_errno($ch)) throw new Exception(curl_error($ch));

        curl_close($ch);
        if (strpos($data, 'TallyPrime Server is Running') === false) throw new Exception("Error");
        $res['status'] = 'success';
        $res['data'] = $data;
        return $res;//$success='success';
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'msg' => $e->getMessage() . ", \n Tally is not running"
        ];
    }
}

function xml2Tally($requestXML)
{
    try {
        // debug($requestXML);
        $server = CS_TALLY_SERVER;
        $headers = array("Content-type: text/xml", "Content-length: " . strlen($requestXML), "Connection: close");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXML);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);

        if (curl_errno($ch)) throw new Exception(curl_error($ch));

        $res['status'] = 'success';
        $res['data'] = $data;
        curl_close($ch);

        return $res;//$success='success';
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
    }
}
