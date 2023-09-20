<header class="page-header">
    <h2>Ledger Account - Client</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <div class="panel-heading">
                <h3>
                    <? if ($SR_MODE) { ?>
                        Client Ledger SR
                    <? } else { ?>
                        Client Ledger
                    <? } ?>
                </h3>
            </div>
            <div class="panel-body">
                <form>
                    <input type="hidden" name="module" value="reports">
                    <input type="hidden" name="action" value="<?= $SR_MODE ? 'client_ledger_sr' : 'client_ledger' ?>">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-3">
                            Currency:
                            <select name="currencyid" class="form-control">
                                <? foreach ($currencies as $c) { ?>
                                    <option <?= selected($currency['id'], $c['id']) ?> value="<?= $c['id'] ?>"><?= $c['name'] ?>
                                        - <?= $c['description'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-2 pt-lg">
                            <button class="btn btn-success btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <p class="text-primary mt-md"><?= $title ?></p>
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size:10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Client Name</th>
                            <th>Bill wise Outstanding</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Difference</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($client_list as $index => $c) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td>
                                    <a data-toggle="modal" href="#client-info-modal" title="view client info"
                                       data-clientid="<?= $c['clientid'] ?>"><?= $c['clientname'] ?></a>
                                </td>
                                <td class="text-right text-primary"><?= $c['bill_wise'] ? formatN($c['bill_wise']) : '-' ?></td>
                                <td class="text-right text-danger"><?= $c['debit'] > 0 ? formatN($c['debit']) : '-' ?></td>
                                <td class="text-right text-rosepink"><?= $c['credit'] ? formatN($c['credit']) : '-' ?></td>
                                <td class="text-right text-success"><?= $c['difference'] ? formatN($c['difference']) : '-' ?></td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm" data-target="#generate-ledger-modal" data-toggle="modal"
                                            data-clientid="<?= $c['clientid'] ?>" data-clientname="<?= $c['clientname'] ?>"
                                            data-sr="<?= $SR_MODE ? '1' : '' ?>" data-currencyid="<?= $currency['id'] ?>"
                                            data-currencyname="<?= $currency['name'] ?> - <?= $currency['description'] ?>" title="Open Ledger Filter">
                                        <i class="fa-calendar fa"></i> Open Ledger Filter
                                    </button>
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
</div>
<?= component('shared/client_info_modal.tpl.php') ?>
<?= component('shared/generate_client_ledger_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', '?module=clients&action=getClients&format=json', 'choose client');
    });
</script>
