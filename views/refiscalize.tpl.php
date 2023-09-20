<header class="page-header">
    <h2>Tally Transfers</h2>
</header>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Failed Fiscalized Sales.</h2>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Receipt No</th>
                        <th>Location</th>
                        <th>Client Name</th>
                        <th>Sales Person</th>
                        <th>Branch</th>
                        <th>Department</th>
                        <th>Receipt Type</th>
                        <th>Date</th>
                        <th>Currency</th>
                        <th>Full Amount</th>
                        <th>Error Message</th>
                        <th>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($failedFiscalization as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td>
                                <a href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>"><?= $R['receipt_no'] ?></a>
                            </td>
                            <td><?= $R['locationname'] . ' - ' . $R['branchname'] ?></td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['salesperson'] ?></td>
                            <td><?= $R['branchname'] ?></td>
                            <td><?= $R['departmentname'] ?></td>
                            <td><?= $R['receipt_method'] ?></td>
                            <td><?= $R['doc'] ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td>
                                <p><?= formatN($R['full_amount']) ?></p>
                                <? if ($R['base_currency'] != 'yes') { ?>
                                    <i class="text-xs text-muted">
                                        <?= $basecurrency['name'] ?> <?= formatN($R['base_full_amount']) ?>
                                    </i>
                                <? } ?>
                            </td>
                            <td class="text-danger"><?= $R['fiscalize_status_message'] ?></td>
                            <td>
                                <form action="?module=sales&action=failed_fiscalization"
                                      onsubmit="return confirm('Do you want to fiscalize?')" method="POST">
                                    <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                    <input type="hidden" name="refiscalize" value="1">
                                    <button class="btn btn-primary"><span class="fa fa-arrow-right"></span></button>
                                </form>
                            </td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>
<script type="text/javascript">
    function checkall(obj) {

        if ($('.checkall').prop('checked') == true) {

            $('.checkbox').prop('checked', true);

        } else if ($('.checkall').prop('checked') == false) {

            $('.checkbox').prop('checked', false);

        }

    }
</script>
