<style>
    @media (min-width: 768px) {
        #client-info-modal .modal-lg {
            width: 75% !important;
        }
    }
</style>
<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title"><i class="fa fa-money"></i> Outstanding Installments</h2>
                <p class="text-primary mt-md"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Outstanding Installments</h5>
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
                                <td class="text-rosepink"><?= formatN($total_outstanding['base_total']) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-md">
                    <table class="table table-hover mb-none" style="font-size:10pt;" id="outstanding-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice date</th>
                            <th>Invoice no</th>
                            <th>Client name</th>
                            <th>Currency</th>
                            <th>Installment no</th>
                            <th class="text-right">Installment amount</th>
                            <th>Installment date</th>
                            <th class="text-right">Paid amount</th>
                            <th class="text-right">Pending amount</th>
                            <th>Due days</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($list as $invoice) {
                            foreach ($invoice['installments'] as $i) {
                                ?>
                                <tr>
                                    <td><?= $count ?></td>
                                    <td data-sort="<?= strtotime($invoice['invoice_date']) ?>"><?= fDate($invoice['invoice_date'], 'd F Y') ?></td>
                                    <td><?= $invoice['receipt_no'] ?></td>
                                    <td title="view client info">
                                        <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $invoice['clientid'] ?>"><?= $invoice['clientname'] ?></a>
                                    </td>
                                    <td><?= $invoice['currencyname'] ?></td>
                                    <td><?= $i['no'] ?></td>
                                    <td class="text-right"><?= formatN($i['amount']) ?></td>
                                    <td data-sort="<?= strtotime($i['time']) ?>"><?= $i['time'] ?></td>
                                    <td class="text-right text-success"><?= formatN($i['paid']) ?></td>
                                    <td class="text-right text-danger"><?= formatN($i['pending']) ?></td>
                                    <td><?= $i['duedays'] ?></td>
                                </tr>
                                <? $count++;
                            }
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'Choose client');
        $('#outstanding-table').DataTable({
            dom: '<"top"fBl>t<"bottom"ip>',
            colReorder: true,
            keys: true,
            buttons: [
                {
                    extend: 'csvHtml5',
                    text: 'Export Excel',
                    exportOptions: {
                        columns: "th:not(.no-export)"
                    }
                }
            ],

        });
    });
</script>
