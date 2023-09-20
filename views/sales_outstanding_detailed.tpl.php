<div class="col-md-12">
    <section class="panel">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-4">
                    <h2 class="panel-title"><i class="fa fa-money"></i> Sales Outstanding Detailed</h2>
                    <p class="text-primary mt-md"><?= $title ?></p>
                </div>
                <div class="col-md-5">
                    <form class="d-flex align-items-center">
                        <input type="hidden" name="module" value="reports">
                        <input type="hidden" name="action" value="sales_outstanding_detailed">
                        <label>Client:</label>
                        <select id="clientid" class="form-control" name="clientid"></select>
                        <button class="btn btn-success ml-sm"> Search</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <? if (count($invoice_list) == 0) { ?>
                <div class="text-center">No Outstandings found!</div>
            <? } else { ?>
                <div class="mb-xlg">
                    <? if ($client) { ?>
                        <a href="<?= $pdf_url ?>" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Export PDF</a>
                    <? } ?>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Overall Outstanding</h5>
                        <table class="table table-bordered" style="font-size: 10pt;">
                            <thead>
                            <tr>
                                <th>Currency</th>
                                <th>Amount</th>
                                <th>Base Amount (<?= $baseCurrency['name'] ?>)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($total_outstanding['currencies'] as $currency => $R) { ?>
                                <tr>
                                    <td><?= $currency ?></td>
                                    <td class="text-danger"><?= formatN($R['amount']) ?></td>
                                    <td class="text-rosepink"><?= $R['base_currency'] == 'yes' ? '-' : formatN($R['base_amount']) ?></td>
                                </tr>
                            <? } ?>
                            <tr class="text-weight-bold">
                                <td colspan="2">BASE TOTAL</td>
                                <td class="text-rosepink"><?=formatN($total_outstanding['base_total'])?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive" style="position: relative">
                    <table class="table table-hover mb-none" style="font-size:10pt;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Issue Date</th>
                            <th>Invoice No.</th>
                            <th>Order By</th>
                            <th>Customer / Part's name</th>
                            <th>Currency</th>
                            <th class=" text-right">Full Amount</th>
                            <th class=" text-right">Paid Amount</th>
                            <th class=" text-right">Credit Notes</th>
                            <th class=" text-right">Pending Amount</th>
                            <th class=" text-right">(<30 days)</th>
                            <th class=" text-right">(30 to 45 days)</th>
                            <th class=" text-right">(45 to 90 days)</th>
                            <th class=" text-right">(>90 days)</th>
                            <th class=" text-right">Final Balance</th>
                            <th class=" text-right">Due on</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $icount = 1;
                        foreach ($opening_outstandings as $op) { ?>
                            <tr>
                                <td><?= $icount ?></td>
                                <td><?= fDate($op['invoicedate'], 'd-F-Y') ?></td>
                                <td>
                                    <p class="m-none"><a
                                                href="<?= url('sales', 'opening_outstanding', 'openingid=' . $op['id']) ?>"><?= $op['invoiceno'] ?></a></p>
                                    <small class="text-muted">opening</small>
                                </td>
                                <td></td>
                                <td><?= $op['clientname'] ?></td>
                                <td><?= $op['currencyname'] ?></td>
                                <td class="text-primary text-right"><?= formatN($op['outstanding_amount']) ?></td>
                                <td class="text-success text-right"><?= formatN($op['paid_amount']) ?></td>
                                <td class="text-rosepink text-right">-</td>
                                <td class="text-danger text-right"><?= formatN($op['pending_amount']) ?></td>
                                <td class=" text-right"><?= formatN($op['(<30 days)']) ?></td>
                                <td class=" text-right"><?= formatN($op['(30 to 45 days)']) ?></td>
                                <td class=" text-right"><?= formatN($op['(45 to 90 days)']) ?></td>
                                <td class=" text-right"><?= formatN($op['(>90 days)']) ?></td>
                                <td class=" text-right"><?= formatN($op['pending_amount']) . ' Dr' ?></td>
                                <td class=" text-right"><?= fDate($op['duedate'], 'd-F-Y') ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                               href="<?= url('payments', 'payment_list', 'openingid=' . $op['id']) ?>"
                                               title="Payment List"><i class="fa-money fa"></i> Sales receipts</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="16"></td>
                            </tr>
                            <? $icount++;
                        } ?>
                        <? foreach ($invoice_list as $idnex => $invoice) { ?>
                            <tr>
                                <td><?= $icount ?></td>
                                <td><?= $invoice['issue_date'] ?></td>
                                <td><?= $invoice['receipt_no'] ?></td>
                                <td>
                                    <? if ($invoice['orderno']) { ?>
                                        <div><?=$invoice['order_creator']?></div>
                                        <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $invoice['orderno']]) ?>">Order <?= $invoice['orderno'] ?></a></small>
                                    <? } ?>
                                </td>
                                <td><?= $invoice['clientname'] ?></td>
                                <td><?= $invoice['currencyname'] ?></td>
                                <td class="text-primary text-right"><?= formatN($invoice['full_amount']) ?></td>
                                <td class="text-success text-right"><?= formatN($invoice['lastpaid_totalamount']) ?></td>
                                <td class="text-rosepink text-right"><?= formatN($invoice['total_increturn']) ?></td>
                                <td class="text-danger text-right"><?= formatN($invoice['pending_amount']) ?></td>
                                <td class=" text-right"><?= formatN($invoice['(<30 days)']) ?></td>
                                <td class=" text-right"><?= formatN($invoice['(30 to 45 days)']) ?></td>
                                <td class=" text-right"><?= formatN($invoice['(45 to 90 days)']) ?></td>
                                <td class=" text-right"><?= formatN($invoice['(>90 days)']) ?></td>
                                <td class=" text-right"><?= formatN($invoice['pending_amount']) . ' Dr' ?></td>
                                <td class=" text-right"><?= $invoice['due_date'] ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                               href="<?= url('payments', 'payment_list', 'id=' . $invoice['salesid']) ?>"
                                               title="Payment List"><i class="fa-money fa"></i> Sales receipts</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="16">
                                    <div class="col-md-8 col-md-offset-4">
                                        <div style="max-height: 40vh;overflow-y: auto">
                                            <table class="table table-bordered" style="font-size: 10pt;">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th class="text-right">Price</th>
                                                    <th class="text-right">Qty</th>
                                                    <th class="text-right">Vat %</th>
                                                    <th class="text-right">Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <? $count = 1;
                                                foreach ($invoice['details'] as $index => $detail) { ?>
                                                    <tr>
                                                        <td><?= $count ?></td>
                                                        <td><?= $detail['productname'] ?></td>
                                                        <td class="text-right"><?= formatN($detail['price']) ?></td>
                                                        <td class="text-right"><?= $detail['quantity'] ?></td>
                                                        <td class="text-right"><?= $detail['vat_rate'] ?></td>
                                                        <td class="text-right"><?= formatN($detail['incamount']) ?></td>
                                                    </tr>
                                                    <? $count++;
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="16"></td>
                            </tr>
                            <? $icount++;
                        } ?>
                        </tbody>
                    </table>
                </div>

            <? } ?>
        </div>
    </section>
</div>
<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'Choose client');
    });
</script>
