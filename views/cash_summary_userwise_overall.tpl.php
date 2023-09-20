<header class="page-header">
    <h2>Cash Summary User-wise Overall</h2>
</header>
<div class="row d-flex justify-content-center">
    <div class="col-md-11">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Cash Summary User-wise Overall</h2>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-10">
                        <form id="search-form" class="m-none d-flex align-items-center">
                            <input type="hidden" name="module" value="reports">
                            <input type="hidden" name="action" value="cash_summary_userwise_overall">
                            <div>From:</div>
                            <input type="date" name="fromdate" class="form-control ml-sm" value="<?= $fromdate ?>">
                            <div class="ml-sm">To:</div>
                            <input type="date" name="todate" class="form-control ml-sm" value="<?= $todate ?>">
                            <div class="ml-sm">Currency:</div>
                            <select name="currencyid" class="form-control">
                                <option value="">-- All --</option>
                                <? foreach ($currencies as $c) { ?>
                                    <option <?= selected($selectedCurrency['id'], $c['id']) ?> value="<?= $c['id'] ?>"><?= $c['name'] ?>
                                        - <?= $c['description'] ?></option>
                                    <?
                                } ?>
                            </select>
                            <div class="ml-sm">User:</div>
                            <? if (Users::can(OtherRights::approve_other_credit_note)) { ?>
                                <select id="userid" class="form-control" name="userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                            <div class="ml-sm">Branch:</div>
                            <select name="branchid" class="form-control">
                                <? if (IS_ADMIN) { ?>
                                    <option value="">-- All --</option>
                                <? } ?>
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($currentBranch['id'], $b['id']) ?>
                                            value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                            <button class="btn btn-success ml-sm"><i class="fa fa-search"></i></button>
                            <button type="button" class="btn btn-warning ml-sm" title="reset" onclick="resetSearchForm()">
                                <i class="fa fa-recycle"></i></button>
                        </form>
                        <p class="p-md">Filter: <span class="text-primary"><?= $title ?></span></p>
                    </div>
                </div>
                <? if ($summary) {
                    foreach ($summary as $b) { ?>
                        <div class="row mb-xlg d-flex justify-content-center">
                            <div class="col-md-10">
                                <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                    <tbody>
                                    <tr>
                                        <td colspan="2" class="text-weight-bold"><h3><?= $b['branchname'] ?></h3></td>
                                    </tr>
                                    <? $ucount = 1;
                                    foreach ($b['users'] as $user) { ?>
                                        <tr>
                                            <td style="width: 20%;vertical-align: middle;"><?= $ucount ?>. <?= $user['name'] ?></td>
                                            <td>
                                                <table class="table table-bordered" style="font-size: 9pt;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 5%">Currency</th>
                                                        <th style="width: 5%">Invoice</th>
                                                        <th style="width: 10%"><?= PaymentMethods::CREDIT_CARD ?> Normal</th>
                                                        <th style="width: 10%"><?= PaymentMethods::CREDIT_CARD ?> SR</th>
                                                        <th style="width: 10%"><?= PaymentMethods::CASH ?> Normal</th>
                                                        <th style="width: 10%"><?= PaymentMethods::CASH ?> SR</th>
                                                        <th style="width: 10%">Total Normal</th>
                                                        <th style="width: 10%">Total SR</th>
                                                        <th style="width: 15%">Overall Total</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($user['currency'] as $currency => $c) { ?>
                                                        <tr>
                                                            <td><?= $currency ?></td>
                                                            <td><?= $c['count'] ?></td>
                                                            <td class="text-right"><?= $c[PaymentMethods::CREDIT_CARD]['non-sr'] ? formatN($c[PaymentMethods::CREDIT_CARD]['non-sr']) : '' ?></td>
                                                            <td class="text-right"><?= $c[PaymentMethods::CREDIT_CARD]['sr'] ? formatN($c[PaymentMethods::CREDIT_CARD]['sr']) : '' ?></td>
                                                            <td class="text-right"><?= $c[PaymentMethods::CASH]['non-sr'] ? formatN($c[PaymentMethods::CASH]['non-sr']) : '' ?></td>
                                                            <td class="text-right"><?= $c[PaymentMethods::CASH]['sr'] ? formatN($c[PaymentMethods::CASH]['sr']) : '' ?></td>
                                                            <td class="text-right"><?= $c['total']['non-sr'] ? formatN($c['total']['non-sr']) : '' ?></td>
                                                            <td class="text-right"><?= $c['total']['sr'] ? formatN($c['total']['sr']) : '' ?></td>
                                                            <td class="text-right"><?= $c['total']['overall'] ? formatN($c['total']['overall']) : '' ?></td>
                                                        </tr>
                                                    <? } ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <? $ucount++;
                                    } ?>
                                    </tbody>
                                    <tfoot>
                                    <tr class="text-rosepink">
                                        <th class="text-weight-bold text-lg" style="vertical-align: middle;">TOTAL</th>
                                        <th>
                                            <table class="table table-bordered" style="font-size: 10pt;">
                                                <thead>
                                                <tr>
                                                    <th style="width: 5%">Currency</th>
                                                    <th style="width: 5%">Invoice</th>
                                                    <th style="width: 10%"><?= PaymentMethods::CREDIT_CARD ?> Normal</th>
                                                    <th style="width: 10%"><?= PaymentMethods::CREDIT_CARD ?> SR</th>
                                                    <th style="width: 10%"><?= PaymentMethods::CASH ?> Normal</th>
                                                    <th style="width: 10%"><?= PaymentMethods::CASH ?> SR</th>
                                                    <th style="width: 10%">Total Normal</th>
                                                    <th style="width: 10%">Total SR</th>
                                                    <th style="width: 15%">Overall Total</th>
                                                </tr>
                                                </thead>
                                                <tbody class="text-rosepink">
                                                <? foreach ($b['currency'] as $currency => $c) { ?>
                                                    <tr>
                                                        <td><?= $currency ?></td>
                                                        <td><?= $c['count'] ?></td>
                                                        <td class="text-right"><?= $c[PaymentMethods::CREDIT_CARD]['non-sr'] ? formatN($c[PaymentMethods::CREDIT_CARD]['non-sr']) : '' ?></td>
                                                        <td class="text-right"><?= $c[PaymentMethods::CREDIT_CARD]['sr'] ? formatN($c[PaymentMethods::CREDIT_CARD]['sr']) : '' ?></td>
                                                        <td class="text-right"><?= $c[PaymentMethods::CASH]['non-sr'] ? formatN($c[PaymentMethods::CASH]['non-sr']) : '' ?></td>
                                                        <td class="text-right"><?= $c[PaymentMethods::CASH]['sr'] ? formatN($c[PaymentMethods::CASH]['sr']) : '' ?></td>
                                                        <td class="text-right"><?= $c['total']['non-sr'] ? formatN($c['total']['non-sr']) : '' ?></td>
                                                        <td class="text-right"><?= $c['total']['sr'] ? formatN($c['total']['sr']) : '' ?></td>
                                                        <td class="text-right"><?= $c['total']['overall'] ? formatN($c['total']['overall']) : '' ?></td>
                                                    </tr>
                                                <? } ?>
                                                </tbody>
                                            </table>
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    <? }
                } else { ?>
                    <div class="row mb-xlg">
                        <div class="col-md-10 text-center">
                            <h4 class="text-muted">No Records found</h4>
                        </div>
                    </div>
                <? } ?>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'choose user');
    });

    function resetSearchForm() {
        let searchForm = $('#search-form');
        $(searchForm).find('.form-control').val('').trigger('change');
        $(searchForm).find("input[type='date']").val('<?=TODAY?>');
        $(searchForm).find("select[name='branchid']").val('<?=$defaultBranch['id']?>');
    }
</script>
