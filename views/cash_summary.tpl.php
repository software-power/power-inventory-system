<header class="page-header">
    <h2>Cash Summary</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-xl-8 col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="panel-title">Cash Summary Report</h2>
                    </div>
                    <div class="col-md-6 d-flex">
                        <select id="branchid" name="branchid" class="form-control mr-sm" onchange="fetchSummary()">
                            <?if(IS_ADMIN){?>
                                <option value="" selected>All Branch</option>
                            <?}?>
                            <? foreach ($branches as $index => $b) { ?>
                                <option value="<?= $b['id']?>" <?=selected($b['id'],$_GET['branchid'])?>><?= $b['name'] ?></option>
                            <? } ?>
                        </select>
                        <input id="date" type="date" class="form-control" value="<?=$date?>" onchange="fetchSummary()">
                    </div>
                </div>
                <p class="text-primary"><?= $title ?> </p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none table-bordered" style="font-size:13px;">
                        <thead>
                        <tr>
                            <td></td>
                            <?foreach ($usedCurrency as $c){?>
                                <td class="text-center"><?=$c?></td>
                            <?}?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Sales Receipts</td>
                            <?foreach ($usedCurrency as $c){?>
                                <td class="text-right"><?= formatN($totalTraCashSalesPayment[$c]) ?></td>
                            <?}?>
                        </tr>
                        <tr>
                            <td>Credit Notes</td>
                            <?foreach ($usedCurrency as $c){?>
                                <td class="text-right text-rosepink"><?= formatN($totalTraSalesReturn[$c]) ?></td>
                            <?}?>
                        </tr>
                        <tr>
                            <td>Advance Receipts</td>
                            <?foreach ($usedCurrency as $c){?>
                                <td class="text-right"><?= formatN($totalCashAdvance[$c]) ?></td>
                            <?}?>
                        </tr>
                        <tr>
                            <td>Supplier Payments</td>
                            <?foreach ($usedCurrency as $c){?>
                                <td class="text-right text-danger"><?= formatN($supplierPayment[$c]) ?></td>
                            <?}?>
                        </tr>
                        <tr>
                            <td colspan="<?=1+count($usedCurrency)?>" class="pb-lg"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Expenses of day</strong></td>
                            <td align="center"><strong>Vouchers</strong></td>
                        </tr>
                        <? foreach ($groupedExpenses as $item) { ?>
                            <tr>
                                <td><?= $item['name'] ?></td>
                                <td align="right"><?=$baseCurrency['name']?> <?= formatN($item['amount']) ?></td>
                                <td align="center"><?= $item['count'] ?></td>
                            </tr>
                        <? } ?>
                        <tr>
                            <td><strong>Total Expenses</strong></td>
                            <td align="right" class="text-danger"><strong><?=$baseCurrency['name']?> <?= formatN($totalExpenses[$baseCurrency['name']]) ?></strong></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="<?=1+count($usedCurrency)?>" class="pb-lg"></td>
                        </tr>
                        <tr>
                            <td><strong>Cash In hand</strong></td>
                            <?foreach ($usedCurrency as $c){?>
                                <td class="text-right <?= $cashInHand[$c] <= 0 ? 'text-danger' : 'text-success' ?>"><?=$c?> <?= formatN($cashInHand[$c]) ?></td>
                            <?}?>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <h5 class="mt-lg">Expenses Sheet</h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-none table-bordered" style="font-size:13px;">
                        <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Expense Ledger</th>
                            <th class="text-center">Voucher No.</th>
                            <th>Amount</th>
                            <th>Paid To</th>
                            <th class="text-center">Supplier Invoice No</th>
                            <th class="text-center">Sale No</th>
                            <th>Remarks</th>
                        </tr>
                        <? foreach ($expenses as $index => $item) { ?>
                            <tr>
                                <td><?= fDate($item['date']) ?></td>
                                <td><?= $item['attrname'] ?></td>
                                <td align="center"><?= $item['voucherno']?></td>
                                <td><?=$baseCurrency['name']?> <?= formatN($item['amount']) ?></td>
                                <td><?= $item['paidto'] ?></td>
                                <td align="center"><?= $item['invoice_no'] == 0 ? '-' : $item['invoice_no'] ?></td>
                                <td align="center"><?= $item['receipt_no'] ? $item['receipt_no']:'-' ?></td>
                                <td style="white-space: pre;"><?= $item['remarks'] ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    function fetchSummary() {
        let branchid = $('#branchid').val();
        let date = $('#date').val();
        window.location.replace(`?module=reports&action=cash_summary&branchid=${branchid}&date=${date}`)
    }
</script>
