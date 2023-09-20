<header class="page-header">
    <h2>Ledger Account - Report</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <? if ($SR_MODE) { ?>
                        <a class="btn" href="?module=reports&action=client_ledger_sr" title="Home"> <i class="fa fa-list"></i> Client ledger SR</a>
                    <? } else { ?>
                        <a class="btn" href="?module=reports&action=client_ledger" title="Home"> <i class="fa fa-list"></i> Client ledger</a>
                    <? } ?>
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">
                    <? if ($SR_MODE) { ?>
                        Ledger Account SR
                    <? } else { ?>
                        Ledger Account
                    <? } ?>
                </h2>
                <p class="text-primary"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div>
                    <a href="<?=$pdf_url?>" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Export PDF</a>
                    <button type="button" class="btn btn-default btn-sm" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
                </div>
                <div class="table-responsive">
                    <table id="ledger-table" class="table mb-none" style="font-size:10pt;">
                        <thead>
                        <tr>
                            <th>DATE</th>
                            <th>VOUCHER TYPE</th>
                            <th>VOUCHER NUMBER</th>
                            <th>DEBIT</th>
                            <th>CREDIT</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="text-weight-bold">
                            <td colspan="3" class="text-right">OPENING BALANCE <span class="text-primary"><?= $currency['name'] ?></span></td>
                            <td><?= $opening_balance['balance'] > 0 ? formatN($opening_balance['balance']) : '' ?></td>
                            <td><?= $opening_balance['balance'] < 0 ? formatN(abs($opening_balance['balance'])) : '' ?></td>
                        </tr>
                        <?
                        foreach ($ledgers as $index => $l) { ?>
                            <tr>
                                <td><?= fDate($l['action_date'], 'd M Y H:i') ?></td>
                                <td class="text-capitalize"><?= $l['voucher_type'] ?></td>
                                <td><?= $l['voucherno'] ?></td>
                                <td><?= $l['side'] == 'debit' ? formatN($l['amount']) : '' ?></td>
                                <td><?= $l['side'] == 'credit' ? formatN($l['amount']) : '' ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                        <tfoot class="text-weight-bold">
                        <tr>
                            <td colspan="3" class="text-right">TOTAL <?= $currency['name'] ?>:</td>
                            <td><?= formatN($total['debit']) ?></td>
                            <td><?= formatN($total['credit']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">Closing Balance <?= $currency['name'] ?>:</td>
                            <td><?= formatN($total['closing_debit']) ?></td>
                            <td><?= formatN($total['closing_credit']) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/tableToExcel.js"></script>
<script>
    function exportExcel(e) {
        let table = document.getElementById("ledger-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Ledger Account <?=$title?>.xlsx`, // fileName you could use any name
            sheet: {
                name: 'LEDGER' // sheetName
            }
        });
    }
</script>


