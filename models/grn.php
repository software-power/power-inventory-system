<?

class GRN extends model
{
    var $table = "grn";

    static $grnClass = null;

    function __construct()
    {
        self::$grnClass = $this;
    }

    function getList($lpoid = "", $grnid = "", $createdby = "", $fromdate = "", $todate = "", $supplierid = "", $paymenttype = "", $currencyid = "", $locationid = "", $branchid = "")
    {
        $sql = "select g.id                                      as grnnumber,
                       g.lpoid                                   as lponumber,
                       g.doc                                     as issuedate,
                       s.name                                    as suppliername,
                       g.locid                                   as locationid,
                       g.supplier_payment,
                       g.paymenttype,
                       g.invoiceno,
                       g.verificationcode,
                       g.vat_registered,
                       g.vat_desc,
                       g.total_amount,
                       g.full_amount,
                       g.adjustment_amount,
                       g.grand_vatamount,
                       (g.total_amount * g.currency_amount)      as base_total_amount,
                       (g.full_amount * g.currency_amount)       as base_full_amount,
                       (g.adjustment_amount * g.currency_amount) as base_adjustment_amount,
                       (g.grand_vatamount * g.currency_amount)   as base_grand_vatamount,
                       g.currency_amount,
                       g.currency_rateid,
                       g.approval_date,
                       g.auto_approve,
                       g.transfer_tally,
                       g.tally_post,
                       g.tally_trxno,
                       g.tally_message,
                       cu.base                                   as base_currency,
                       approver.name                             as approver,
                       cu.name                                   as currency_name,
                       cu.description                            as currency_description,
                       u.name                                    as issuedby,
                       l.name                                    as locationname,
                       b.name                                    as branchname,
                       b.tally_purchase_account,
                       datediff(current_date(), g.doc)           as days
                from grn as g
                         inner join suppliers as s on s.id = g.supplierid
                         inner join currencies_rates as cur on cur.id = g.currency_rateid
                         inner join currencies as cu on cu.id = cur.currencyid
                         inner join users as u on u.id = g.createdby
                         inner join locations as l on g.locid = l.id
                         inner join branches as b on l.branchid = b.id
                         left join users as approver on approver.id = g.approvedby
                where 1 = 1 and g.status = 'active'";

        if ($grnid) $sql .= " and g.id = $grnid";
        if ($lpoid) $sql .= " and g.lpoid = $lpoid";
        if ($createdby) $sql .= " and u.id = $createdby";
        if ($locationid) $sql .= " and g.locid = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($currencyid) $sql .= " and cu.id = $currencyid";
        if ($paymenttype) $sql .= " and g.paymenttype = '$paymenttype'";
        if ($supplierid) $sql .= " and g.supplierid = $supplierid";
        if ($fromdate) $sql .= " and g.doc >= '$fromdate'";
        if ($todate) $sql .= " and g.doc <= '$todate'";

        $sql .= " group by g.id ORDER BY g.id DESC ";

//         debug($sql);
        return fetchRows($sql);
    }


    function purchaseList($productid = '', $fromdate = "", $todate = "", $locationid = "", $branchid = "", $grnid = "", $supplierid = "")
    {
        $sql = "select grn.*,
                       grn.id                           as grnno,
                       l.name                           as locationname,
                       b.name                           as branchname,
                       grn.id                           as grnno,
                       currencies.name                  as currencyname,
                       suppliers.name                   as supplierName,
                       products.name                    as productName,
                       grndetails.id                    as gdi,
                       grndetails.rate                  as price,
                       grndetails.qty                   as quantity
                from `grn`
                         inner join grndetails on grndetails.grnid = grn.id
                         inner join suppliers on suppliers.id = grn.supplierid
                         inner join currencies_rates cr on cr.id = grn.currency_rateid
                         inner join currencies on currencies.id = cr.currencyid
                         inner join stocks on stocks.id = grndetails.stockid
                         inner join products on stocks.productid = products.id
                         inner join locations l on l.id = grn.locid
                         inner join branches b on b.id = l.branchid
                where 1=1";

        if ($productid) $sql .= " and products.id = $productid";
        if ($locationid) $sql .= " and l.id = $locationid";
        if ($branchid) $sql .= " and b.id = $branchid";
        if ($grnid) $sql .= " and grn.id = $grnid";
        if ($supplierid) $sql .= " and suppliers.id = $supplierid";
        if ($fromdate) $sql .= " and date_format(grn.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(grn.doc,'%Y-%m-%d') <= '$todate'";

        $sql .= " order by `grn`.`id` desc";
//        debug($sql);
        return fetchRows($sql);
    }

    function getGrnWithReturnQty($grnnumber = "", $group = true, $locationid = "", $productid = "", $productcategpryid = "", $brandid = "", $fromdate = "", $todate = "", $trackserialno = '', $track_expire = '', $supplierid = '')
    {
        $grn_return_filter = !empty($grnnumber) ? " and gr.grnid = $grnnumber" : "";  //for filtering grn in calculating return qty
        $sql = "select g.id                                        as grnnumber,
                       g.lpoid,
                       g.verificationcode                          as verificationcode,
                       g.paymenttype,
                       g.vat_registered,
                       g.vat_desc,
                       g.supplier_payment,
                       g.total_amount,
                       g.grand_vatamount,
                       g.full_amount,
                       g.adjustment_amount,
                       g.invoiceno,
                       g.doc                                       as issuedate,
                       g.approval_date,
                       g.currency_rateid,
                       g.currency_amount,
                       gd.stockid,
                       gd.id                                       as gdi,
                       gd.qty,
                       gd.billable_qty,
                       gd.rate,
                       gd.vat_percentage,
                       round(gd.billable_qty * gd.rate * (1+gd.vat_percentage/100),2) as incamount,
                       b.id                                        as batchId,
                       b.batch_no,
                       b.qty                                       as batchqty,
                       ifnull(returns.totalReturnQty, 0)           as totalReturnQty,
                       b.expire_date,
                       p.trackserialno,
                       p.validate_serialno,
                       p.track_expire_date,
                       p.prescription_required,
                       p.id                                        as prodid,
                       p.name                                      as productname,
                       p.generic_name                              as productgenericname,
                       pc.name                                     as productcategoryname,
                       model.name                                  as brandname,
                       lo.id                                       as st_locid,
                       lo.name                                     as stock_location,
                       cu.name                                     as currency_name,
                       cu.description                              as currency_description,
                       s.id                                        as supplierid,
                       s.name                                      as suppliername,
                       u.name                                      as issuedby,
                       approver.name                               as approver
                from grn as g
                         inner join grndetails as gd on gd.grnid = g.id
                         inner join batches as b on b.gdi = gd.id
                         inner join stocks as st on st.id = gd.stockid
                         inner join locations as lo on lo.id = st.locid
                         inner join products as p on p.id = st.productid
                         inner join product_categories pc on pc.id = p.productcategoryid
                         inner join model on model.id = p.modelid
                         inner join suppliers as s on s.id = g.supplierid
                         inner join currencies_rates as cr on cr.id = g.currency_rateid
                         inner join currencies as cu on cu.id = cr.currencyid
                         inner join users as u on u.id = g.createdby
                         left join users as approver on approver.id = g.approvedby
                         left join (select grd.stockid, grb.batch_id, sum(grb.qty) totalReturnQty
                                    from grnreturn_details grd
                                             inner join grn_returns gr on gr.id = grd.returnid
                                             inner join grn_return_batches grb on grb.grdi = grd.id
                                    where 1=1 $grn_return_filter
                                    group by stockid, batch_id) as returns on returns.batch_id = b.id
                where 1 = 1";
        if ($grnnumber) $sql .= " and g.id = $grnnumber";
        if ($supplierid) $sql .= " and g.supplierid = $supplierid";
        if ($locationid) $sql .= " and lo.id = $locationid";
        if ($productid) $sql .= " and p.id = $productid";
        if ($trackserialno == 'yes') $sql .= " and p.trackserialno = '1'";
        if ($trackserialno == 'no') $sql .= " and p.trackserialno = '0'";
        if ($track_expire == 'yes') $sql .= " and p.track_expire_date = '1'";
        if ($track_expire == 'no') $sql .= " and p.track_expire_date = '0'";
        if ($productcategpryid) $sql .= " and pc.id = $productcategpryid";
        if ($brandid) $sql .= " and model.id = $brandid";
        if ($fromdate) $sql .= " and date_format(g.doc,'%Y-%m-%d') >= '$fromdate'";
        if ($todate) $sql .= " and date_format(g.doc,'%Y-%m-%d') <= '$todate'";
//         debug($sql);
        $details = fetchRows($sql);
//            debug($details);
        if (!$group) {
            return $details;
        } else {
            //arranging batches for better use
            $newArray = [];
            foreach ($details as $index => $item) {
                $newArray[$item['grnnumber']]['grnnumber'] = $item['grnnumber'];
                $newArray[$item['grnnumber']]['lpoid'] = $item['lpoid'];
                $newArray[$item['grnnumber']]['verificationcode'] = $item['verificationcode'];
                $newArray[$item['grnnumber']]['total_amount'] = $item['total_amount'];
                $newArray[$item['grnnumber']]['grand_vatamount'] = $item['grand_vatamount'];
                $newArray[$item['grnnumber']]['paymenttype'] = $item['paymenttype'];
                $newArray[$item['grnnumber']]['vat_registered'] = $item['vat_registered'];
                $newArray[$item['grnnumber']]['vat_desc'] = $item['vat_desc'];
                $newArray[$item['grnnumber']]['supplier_payment'] = $item['supplier_payment'];
                $newArray[$item['grnnumber']]['full_amount'] = $item['full_amount'];
                $newArray[$item['grnnumber']]['adjustment_amount'] = $item['adjustment_amount'];
                $newArray[$item['grnnumber']]['st_locid'] = $item['st_locid'];
                $newArray[$item['grnnumber']]['stock_location'] = $item['stock_location'];
                $newArray[$item['grnnumber']]['invoiceno'] = $item['invoiceno'];
                $newArray[$item['grnnumber']]['issuedby'] = $item['issuedby'];
                $newArray[$item['grnnumber']]['issuedate'] = $item['issuedate'];
                $newArray[$item['grnnumber']]['approver'] = $item['approver'];
                $newArray[$item['grnnumber']]['approval_date'] = $item['approval_date'];
                $newArray[$item['grnnumber']]['currency_amount'] = $item['currency_amount'];
                $newArray[$item['grnnumber']]['currency_rateid'] = $item['currency_rateid'];
                $newArray[$item['grnnumber']]['currency_name'] = $item['currency_name'];
                $newArray[$item['grnnumber']]['currency_description'] = $item['currency_description'];
                $newArray[$item['grnnumber']]['supplierid'] = $item['supplierid'];
                $newArray[$item['grnnumber']]['suppliername'] = $item['suppliername'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['stockid'] = $item['stockid'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['gdi'] = $item['gdi'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['qty'] = $item['qty'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['billable_qty'] = $item['billable_qty'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['totalReturnedQty'] += $item['totalReturnQty'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['rate'] = $item['rate'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['vat_percentage'] = $item['vat_percentage'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['incamount'] = $item['incamount'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['trackserialno'] = $item['trackserialno'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['validate_serialno'] = $item['validate_serialno'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['track_expire_date'] = $item['track_expire_date'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['prodid'] = $item['prodid'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['productname'] = $item['productname'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['productgenericname'] = $item['productgenericname'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['productcategoryname'] = $item['productcategoryname'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['brandname'] = $item['brandname'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['prescription_required'] = $item['prescription_required'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['batches'][$item['batchId']]['batchId'] = $item['batchId'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['batches'][$item['batchId']]['batch_no'] = $item['batch_no'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['batches'][$item['batchId']]['batchqty'] = $item['batchqty'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['batches'][$item['batchId']]['totalBatchReturnQty'] = $item['totalReturnQty'];
                $newArray[$item['grnnumber']]['stock'][$item['stockid']]['batches'][$item['batchId']]['expire_date'] = $item['expire_date'];
            }
            return $newArray;
        }

    }


    static function currentGrnState($grnid, $productid = '', $trackserialno = '', $track_expire = '', $fetch_serialnos = true, $serial_status = '')
    {
        $grnInfo = array_values(GRN::$grnClass->getGrnWithReturnQty($grnid, true, '',
            $productid, '', '', '', '', $trackserialno, $track_expire))[0];
        foreach ($grnInfo['stock'] as $index => $stock) {
            $product = Products::$productClass->get($stock['prodid']);
            $taxCategory = Categories::$categoryClass->get($product['categoryid']);
            $grnInfo['stock'][$index]['vat_rate'] = $taxCategory['vat_percent'];
            $grnInfo['stock'][$index]['vat_id'] = $taxCategory['id'];
            $grnInfo['stock'][$index]['total_cost'] = addTAX($stock['rate'], $taxCategory['vat_percent']) * $stock['billable_qty'];

            //check if proforma held stock
            $product_stock = Stocks::$stockClass->calcStock(
                $grnInfo['st_locid'],
                $stock['stockid'],
                "", "", "",
                "", "", "",
                "", "", "", "",
                "", "", "",
                "", "", false, true,
                "", "", true, true
            );
            $product_stock = array_values($product_stock)[0];
//            debug($product_stock);
            $grnInfo['stock'][$index]['held_stock'] = $product_stock['held_stock'];


            //find current batch stocks
            foreach ($stock['batches'] as $bkey => $batch) {
                $batchStock = Stocks::$stockClass->calcStock(
                    $grnInfo['st_locid'],
                    $stock['stockid'],
                    "", "", "",
                    "", "", "",
                    "", "", "", "",
                    "", $batch['batchId'], "",
                    "", "", true, false,
                    "", "", false
                );
//            debug($batchStock);
                unset($grnInfo['stock'][$index]['batches'][$bkey]['totalBatchReturnQty']);
                $grnInfo['stock'][$index]['batches'][$bkey]['current_stock'] = $batchStock[0]['total'];
            }
            $grnInfo['stock'][$index]['current_stock_qty'] = array_sum(array_column($grnInfo['stock'][$index]['batches'], 'current_stock'));

            //serialnos
            if ($stock['trackserialno'] == 1) {
                if ($fetch_serialnos) {
                    $grnInfo['stock'][$index]['serialnos'] = SerialNos::$serialNoClass->find(['gdi' => $stock['gdi']]);

                    //todo check if serial is transferred or used
                    foreach ($grnInfo['stock'][$index]['serialnos'] as $sindex => $serialno) {
                        $used = true;
                        if (empty($serialno['sdi']) || $serialno['sdi'] == 0) {
                            $used = false;
                        }
                        $grnInfo['stock'][$index]['serialnos'][$sindex]['used'] = $used;
                    }
                } else {
                    if ($serial_status == 'in_stock') {
                        $grnInfo['stock'][$index]['serialnos_count'] = count(array_filter(SerialNos::$serialNoClass->find(['gdi' => $stock['gdi']]), function ($s) {
                            return $s['sdi'] == null || $s['sdi'] <= 0;
                        }));
                    } else {
                        $grnInfo['stock'][$index]['serialnos_count'] = SerialNos::$serialNoClass->countWhere(['gdi' => $stock['gdi']]);
                    }
                }
            }
        }
        return $grnInfo;
    }

    function withPaymentAmount($supplierid = "", $grnid = "", $openid = "", $branchid = "", $locationid = "", $fromdate = "", $todate = "", $with_outstanding = false, $with_openings = true, $with_grns = true, $issuedby = "", $only_tracking_payment = true)
    {
        //filters
        $supplier = $supplierid ? " and s.id = $supplierid" : "";
        $location = $locationid ? " and l.id = $locationid" : "";
        $branch = $branchid ? " and b.id = $branchid" : "";
        $only_tracking_payment = $only_tracking_payment ? " and grn.supplier_payment = 1" : "";
        $grncreatedby = $issuedby ? " and grn.createdby = $issuedby" : "";
        $grnfrom = $fromdate ? " and date_format(grn.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $grnto = $todate ? " and date_format(grn.doc,'%Y-%m-%d') <= '$todate'" : "";

        $soucreatedby = $issuedby ? " and sou.createdby = $issuedby" : "";
        $soufrom = $fromdate ? " and date_format(sou.doc,'%Y-%m-%d') >= '$fromdate'" : "";
        $souto = $todate ? " and date_format(sou.doc,'%Y-%m-%d') <= '$todate'" : "";
        $with_outstanding = $with_outstanding ? " having outstanding_amount > 0" : "";

        if ($grnid) {
            if (is_array($grnid)) {
                $grns = " and grn.id in (" . implode(',', $grnid) . ")";
            } else {
                $grns = " and grn.id = $grnid";
            }
        }
        if ($openid) {
            if (is_array($openid)) {
                $openings = " and sou.id in (" . implode(',', $openid) . ")";
            } else {
                $openings = " and sou.id = $openid";
            }
        }

        if (!$with_grns) {
            $grns = " and grn.id < 0";
        }
        if (!$with_openings) {
            $openings = " and sou.id < 0";
        }

        $sql = "select *
                from (select grn.id,
                             grn.id                                                                   as grnno,
                             grn.lpoid                                                                as lpono,
                             grn.invoiceno,
                             grn.supplierid,
                             grn.locid,
                             grn.createdby,
                             grn.paymenttype,
                             grn.verificationcode,
                             grn.credit_days,
                             grn.doc,
                             grn.supplier_payment,
                             adddate(grn.doc, grn.credit_days)                                        as credit_due_date,
                             l.name                                                                   as locationname,
                             b.name                                                                   as branchname,
                             s.name                                                                   as suppliername,
                             creator.name                                                             as issuedby,
                             'grn'                                                                    as source,
                             (grn.full_amount * grn.currency_amount)                                  as full_amount,
                             IFNULL(grn_payments.amount, 0)                                           as paid_amount,
                             (grn.full_amount * grn.currency_amount) - IFNULL(grn_payments.amount, 0) as outstanding_amount
                      from grn
                               inner join suppliers s on grn.supplierid = s.id
                               inner join locations l on grn.locid = l.id
                               inner join branches b on l.branchid = b.id
                               inner join users creator on creator.id = grn.createdby
                               left join
                           (
                               select spd.grnid, sum(spd.amount * sp.currency_amount) as amount
                               from supplier_payment_details spd
                                        inner join supplier_payments sp on sp.id = spd.spid
                               group by spd.grnid
                           ) as grn_payments on grn_payments.grnid = grn.id
                      where 1 = 1 $only_tracking_payment $supplier $location $branch $grnfrom $grnto $grns $grncreatedby
                                  and grn.approvedby is not null $with_outstanding
                      union all
                      select sou.id,
                          sou.grnno,
                          '' as lpono,
                          sou.invoiceno,
                          sou.supplierid,
                          sou.locationid,
                          sou.createdby,
                          sou.paymenttype,
                          sou.doc,
                          1 as supplier_payment,
                          '' as verificationcode,
                          null as credit_days,
                          null as credit_due_date,
                          l.name as locationname,
                          b.name as branchname,
                          s.name as suppliername,
                          creator.name as issuedby,
                          'opening' as source,
                          (sou.amount * sou.currency_amount) as full_amount,
                          IFNULL(opening_payment.amount, 0) as paid_amount,
                          (sou.amount * sou.currency_amount) - IFNULL(opening_payment.amount, 0) as outstanding_amount
                      from supplier_opening_outstandings sou
                          inner join suppliers s
                      on sou.supplierid = s.id
                          inner join locations l on sou.locationid = l.id
                          inner join branches b on l.branchid = b.id
                          inner join users creator on creator.id = sou.createdby
                          left join
                          (
                          select spd.openingid, sum(spd.amount * sp.currency_amount) as amount
                          from supplier_payment_details spd
                          inner join supplier_payments sp on sp.id = spd.spid
                          group by spd.openingid
                          ) as opening_payment on opening_payment.openingid = sou.id
                      where 1 = 1 $supplier $location $branch $soufrom $souto $openings $soucreatedby $with_outstanding) as grn_opening_payment";


//        debug($sql);
        return fetchRows($sql);
    }

    static function cancelGrn($grnid)
    {
        //canceling grn
        $grn = array_values(GRN::$grnClass->getGrnWithReturnQty($grnid, $group = true))[0];
        if ($grn['suppliername'] != 'Importing Stock') {
            $grnJSON = json_encode($grn);
            GrnCanceled::$grnCanceledClass->insert([
                'grnid' => $grnid,
                'locationid' => $grn['st_locid'],
                'supplierid' => $grn['supplierid'],
                'payload' => base64_encode($grnJSON),
                'createdby' => $_SESSION['member']['id']
            ]);
        }
        $grn_detailsId = array_column($grn['stock'], 'gdi');
        foreach (array_chunk($grn_detailsId, 100) as $chunk) Batches::$batchesClass->deleteWhereMany(['gdi' => $chunk]); //delete batches
        GRNDetails::$grnDetailsClass->deleteWhere(['grnid' => $grnid]);//delete grn details
        GRN::$grnClass->deleteWhere(['id' => $grnid]);//delete grn master
    }

    static function tallyPost($grnno)
    {
        $grn = GRN::$grnClass->getList('', $grnno)[0];
        if (!$grn['approver']) return ['status' => 'error', 'msg' => 'GRN not approved'];
        if (!$grn['transfer_tally']) return ['status' => 'error', 'msg' => 'Not for Tally transfer'];
        if ($grn['tally_post']) return ['status' => 'error', 'msg' => 'Already posted to tally'];

        $grnno = "GRN-" . $grn['grnnumber'];
        if (!$grn['tally_trxno']) {
            $tally_trxno = unique_token(60) . "-$grnno";
            GRN::$grnClass->update($grn['grnnumber'], ['tally_trxno' => $tally_trxno]);
        } else {
            $tally_trxno = $grn['tally_trxno'];
        }


        $post_data = [];
        $post_data['trxno'] = $tally_trxno;
        $post_data['voucherno'] = $grnno;
        $post_data['reference'] = $grn['invoiceno'];
        $post_data['date'] = fDate($grn['issuedate'], 'Ymd');
        $post_data['narration'] = '';
        $post_data['vercode'] = htmlspecialchars($grn['verificationcode']);
        $post_data['vat_desc'] = htmlspecialchars($grn['vat_desc']);
        $post_data['suppliername'] = htmlspecialchars($grn['suppliername']);
        $post_data['purchase_account'] = htmlspecialchars($grn['tally_purchase_account']);
        $post_data['adjustment_amount'] = $grn['base_adjustment_amount'];
        $post_data['totalamount'] = $grn['base_full_amount'];
        $post_data['excamount'] = $grn['base_total_amount'];
        $post_data['vatamount'] = $grn['base_grand_vatamount'];
        $post_data['grn_currency'] = $grn['currency_name'];
        $post_data['exchange_rate'] = $grn['currency_amount'];  //todo which exchange rate to use
        $post_data['base_currency'] = Currencies::$currencyClass->find(['base' => 'yes'])[0]['name'];


        //check adjustment
        if ($post_data['adjustment_amount'] <> 0) {
            $post_data['adjustment']['ledgername'] = CS_TALLY_ADJUSTMENT_LEDGER;
            $post_data['adjustment']['dr_cr'] = $post_data['adjustment_amount'] < 0 ? 'cr' : 'dr';
            $post_data['adjustment']['amount'] = abs($post_data['adjustment_amount']);
        }

        //clean narration
        $post_data['narration'] = htmlspecialchars($post_data['narration']);
//        debug($post_data);

        $result = createPurchaseVoucher($post_data);

        if ($result['status'] == 'success') {
            //clear old records
            TallyTransfers::$tallyTransferclass->deleteWhere(['trxno' => $tally_trxno]);
            //save transfer
            //cr ledgername
            $partno = 1;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => $post_data['suppliername'],
                'dr_cr' => 'cr',
                'amount' => $post_data['totalamount'],
                'reference' => $post_data['voucherno'],
                'voucher_type' => 'Purchase',
                'sourceid' => $grn['grnnumber'],
                'sourcetable' => 'grn',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);


            //adjustment
            if ($post_data['adjustment']) {
                $partno++;
                TallyTransfers::$tallyTransferclass->insert([
                    'date' => $post_data['date'],
                    'partno' => $partno,
                    'ledgername' => $post_data['adjustment']['ledgername'],
                    'dr_cr' => $post_data['adjustment']['dr_cr'],
                    'amount' => $post_data['adjustment']['amount'],
                    'reference' => $post_data['voucherno'],
                    'voucher_type' => 'Purchase',
                    'sourceid' => $grn['grnnumber'],
                    'sourcetable' => 'grn',
                    'trxno' => $tally_trxno,
                    'createdby' => $_SESSION['member']['id'],
                ]);
            }

            //dr ledger purchase account
            $partno++;
            TallyTransfers::$tallyTransferclass->insert([
                'date' => $post_data['date'],
                'partno' => $partno,
                'ledgername' => $post_data['purchase_account'],
                'dr_cr' => 'dr',
                'amount' => $post_data['excamount'],
                'reference' => $post_data['voucherno'],
                'voucher_type' => 'Purchase',
                'sourceid' => $grn['grnnumber'],
                'sourcetable' => 'grn',
                'trxno' => $tally_trxno,
                'createdby' => $_SESSION['member']['id'],
            ]);

            //dr ledger Vat
            if ($post_data['vatamount'] > 0) {
                $partno++;
                TallyTransfers::$tallyTransferclass->insert([
                    'date' => $post_data['date'],
                    'partno' => $partno,
                    'ledgername' => 'Vat',
                    'dr_cr' => 'dr',
                    'amount' => $post_data['vatamount'],
                    'reference' => $post_data['voucherno'],
                    'voucher_type' => 'Purchase',
                    'sourceid' => $grn['grnnumber'],
                    'sourcetable' => 'grn',
                    'trxno' => $tally_trxno,
                    'createdby' => $_SESSION['member']['id'],
                ]);
            }

            GRN::$grnClass->update($grn['grnnumber'], ['tally_post' => 1, 'tally_message' => $result['msg']]);
        } else {
            GRN::$grnClass->update($grn['grnnumber'], ['tally_message' => $result['msg']]);
        }

        return $result;
    }
}
