<style>
    @media (min-width: 768px) {
        #client-info-modal .modal-lg {
            width: 75% !important;
        }
    }
</style>
<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title"><i class="fa fa-money"></i> Staff Outstanding Summary</h2>
                <p class="text-primary mt-md"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5>Staff Outstanding Summary</h5>
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
                    <div class="col-md-5">
                        <h5>Overall In days</h5>
                        <table class="table table-bordered" style="font-size: 10pt;">
                            <thead>
                            <tr>
                                <th>Currency</th>
                                <th>(<30 days)</th>
                                <th>(30 to 45 days)</th>
                                <th>(45 to 90 days)</th>
                                <th>(>90 days)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?= $baseCurrency['name'] ?></td>
                                <td class=" text-right"><?= formatN($total_outstanding['(<30 days)']) ?></td>
                                <td class=" text-right"><?= formatN($total_outstanding['(30 to 45 days)']) ?></td>
                                <td class=" text-right"><?= formatN($total_outstanding['(45 to 90 days)']) ?></td>
                                <td class=" text-right"><?= formatN($total_outstanding['(>90 days)']) ?></td>
                            </tr>
                            <tr class="text-weight-bold">
                                <td colspan="4">BASE TOTAL</td>
                                <td class="text-right text-rosepink"><?= formatN($total_outstanding['base_total']) ?></td>
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
                            <th>Account Manager</th>
                            <th class=" text-right">Total Outstanding (<?= $baseCurrency['name'] ?>)</th>
                            <th class=" text-right">(<30 days)</th>
                            <th class=" text-right">(30 to 45 days)</th>
                            <th class=" text-right">(45 to 90 days)</th>
                            <th class=" text-right">(>90 days)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($staff as $c) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td>
                                    <a href="<?= url('reports', 'overall_sales_outstanding', ['acc_mng' => $c['acc_mng']]) ?>"
                                       title="view details"><?= $c['account_manager'] ?></a>
                                </td>
                                <td class="text-right"><?= formatN($c['base_total']) ?></td>
                                <td class=" text-right"><?= formatN($c['(<30 days)']) ?></td>
                                <td class=" text-right"><?= formatN($c['(30 to 45 days)']) ?></td>
                                <td class=" text-right"><?= formatN($c['(45 to 90 days)']) ?></td>
                                <td class=" text-right"><?= formatN($c['(>90 days)']) ?></td>
                            </tr>
                            <? $count++;
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
