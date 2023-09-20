<div class="row d-flex justify-content-center">
    <div class="col-xs-12 col-xl-9">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">Bill No: <span class="text-primary"><?= $bill['billid'] ?></span></h2>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="m-xs">Client Name: <span class="text-primary"><?= $bill['clientname'] ?></span></p>
                        <p class="m-xs">Currency: <span
                                    class="text-primary"><?= $bill['currency_name'] ?> - <?= $bill['currency_description'] ?></span></p>
                        <p class="m-xs">Total Amount: <span class="text-primary"><?= formatN($bill['total_amount']) ?></span></p>
                        <p class="m-xs">Issued by: <span class="text-primary"><?= $bill['issuedby'] ?></span></p>
                        <p class="m-xs">Issue Date: <span class="text-primary"><?= fDate($bill['doc'], 'd M Y H:i') ?></span></p>
                        <p class="m-xs">Bill Item Type: <span class="text-primary"><?= $bill['non_stock'] ? 'Services' : 'Stock Items' ?></span></p>
                        <p class="m-xs">Billing Interval: <span
                                    class="text-primary"><?= $bill['billtypename'] ?>, <?= $bill['bill_interval'] ?> <?= ucfirst($bill['billtype']) ?> interval</span>
                        </p>
                        <p class="m-xs">Billing Start Month: <span class="text-primary"><?= fDate($bill['startdate'], 'F Y') ?></span></p>
                        <p class="m-xs">Last Billing Month: <span
                                    class="text-primary"><?= $bill['lastbilldate'] ? fDate($bill['lastbilldate'], 'F Y') : '' ?></span></p>
                        <p class="m-xs">Next Billing Month: <span class="text-primary"><?= fDate($bill['nextbilldate'], 'F Y') ?></span></p>
                    </div>
                    <div class="col-md-4">
                        <p class="m-xs">Remarks:</p>
                        <div>
                            <textarea class="form-control text-sm" readonly rows="3"><?= $bill['remarks'] ?></textarea>
                        </div>
                        <p class="m-xs">Internal Remarks:</p>
                        <div>
                            <textarea class="form-control text-sm" readonly rows="3"><?= $bill['internal_remarks'] ?></textarea>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mt-lg">
                    <li class="active"><a data-toggle="tab" href="#bill-items"><i class="fa fa-shopping-cart"></i> Items</a></li>
                    <li><a data-toggle="tab" href="#billing-logs"><i class="fa fa-history"></i> Billing logs</a></li>
                </ul>
                <div class="tab-content">
                    <div id="bill-items" class="tab-pane fade in active">
                        <h4>Bill Items</h4>
                        <table class="table table-hover mb-none" style="font-size:10pt;">
                            <thead>
                            <tr>
                                <th class="stick">#</th>
                                <th class="stick">Product Name</th>
                                <th class="stick">Qty</th>
                                <th class="stick">Price</th>
                                <th class="stick">VAT%</th>
                                <th class="stick">Inc Price</th>
                                <th class="stick">VAT Amount</th>
                                <th class="stick">Total Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? $count = 1;
                            foreach ($bill['details'] as $index => $R) { ?>
                                <tr>
                                    <td><?= $count ?></td>
                                    <td>
                                        <?= $R['productname'] ?>
                                        <div class="text-muted text-sm"><i><?= $R['extra_desc'] ?></i></div>
                                    </td>
                                    <td><?= $R['qty'] ?></td>
                                    <td><?= formatN($R['price']) ?></td>
                                    <td><?= $R['vat_rate'] ?></td>
                                    <td><?= formatN($R['incprice']) ?></td>
                                    <td><?= formatN($R['vatamount']) ?></td>
                                    <td><?= formatN($R['incamount']) ?></td>
                                </tr>
                                <? $count++;
                            } ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="billing-logs" class="tab-pane fade">
                        <h4>Billing logs</h4>
                        <table id="userTable" class="table table-hover table-bordered mb-none" style="font-size:10pt;">
                            <thead>
                            <tr>
                                <th class="stick">#</th>
                                <th class="stick">Invoice no</th>
                                <th class="stick">Bill month</th>
                                <th class="stick">Issued by</th>
                                <th class="stick">Issued at</th>
                                <th class="stick">Remarks</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? $count = 1;
                            foreach ($billing_logs as $l) { ?>
                                <tr>
                                    <td><?= $count++ ?></td>
                                    <td>
                                        <? if ($l['invoiceno']) { ?>
                                            <a href="<?= url('sales', 'view_invoice', ['salesid' => $l['salesid']]) ?>"><?= $l['invoiceno'] ?></a>
                                        <? } else { ?>
                                            <span class="text-danger">Billing stopped</span>
                                        <? } ?>
                                    </td>
                                    <td><?= fDate($l['billmonth'], 'F Y') ?></td>
                                    <td><?= $l['issuedby'] ?></td>
                                    <td><?= fDate($l['doc'], 'd M Y H:i') ?></td>
                                    <td style="white-space: pre;"><?= $l['remarks'] ?></td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>

</script>
