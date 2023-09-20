<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="audit_report_invoice_wise">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Branch</label>
                            <select id="branchid" class="form-control" name="branchid">
                                <?if(Users::can(OtherRights::approve_other_credit_invoice)){?>
                                    <option value="">-- All branches --</option>
                                <?}?>
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($branchid, $b['id']) ?>
                                            value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Location</label>
                            <select id="locationid" class="form-control" name="locationid"></select>
                        </div>
                        <div class="col-md-4">
                            <label>Sales Person</label>
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" class="form-control" name="userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <label>Client</label>
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                        <div class="col-md-4">
                            <label>With Non Stock Item</label>
                            <select id="clientid" class="form-control" name="with_non_stock">
                                <option value="">-- All --</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <label>From Date</label>
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-4">
                            <label>To Date</label>
                            <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-12">
                            <button class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal"> Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <a class="btn" href="#search-modal" data-toggle="modal" title="Filter"> <i class="fa fa-search"></i>
                        Open filter </a>
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Sales Audit Invoice Wise report</h2>
                <p class="text-primary"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Order By</th>
                            <th>Sales Person</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Registered Reseller</th>
                            <th>Location</th>
                            <th>Currency</th>
                            <th title="invoice has non stock item">Has Non Stock Item</th>
                            <th title="product from different department">Has Diff Depart</th>
                            <th class="text-right">Expense Amount</th>
                            <th class="text-right">Amount Exc</th>
                            <th class="text-right">Credit Note</th>
                            <th class="text-right">Cost Amount</th>
                            <th class="text-right">Profit Amount</th>
                            <th class="text-right">Margin (%)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($invoices as $key => $R) { ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td>
                                    <a href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>" target="_blank">
                                        <?= $R['receipt_no'] ?></a>
                                </td>
                                <td>
                                    <? if ($R['orderno']) { ?>
                                        <div class="d-block"><?= $R['order_creator'] ?></div>
                                        <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order <?= $R['orderno'] ?></a></small>
                                    <? } ?>
                                </td>
                                <td><?= $R['sales_person'] ?></td>
                                <td><?= $R['doc'] ?></td>
                                <td title="view client info">
                                    <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                                </td>
                                <td class="<?=$R['reseller']?'text-danger text-weight-semibold':''?>"><?= $R['reseller']?'Yes':'No' ?></td>
                                <td><?= $R['stocklocation'] ?> - <?= $R['branchname'] ?></td>
                                <td><?= $R['currencyname'] ?></td>
                                <td><?= $R['has_non_stock'] ? 'Yes' : 'No' ?></td>
                                <td><?= $R['has_different_department'] ? 'Yes' : 'No' ?></td>
                                <td class="text-right">
                                    <p><?= formatN($R['sale_expense']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_sale_expense']) ?>
                                        </i>
                                    <? } ?>
                                </td>
                                <td class="text-right">
                                    <p><?= formatN($R['grand_amount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_grand_amount']) ?>
                                        </i>
                                    <? } ?>
                                </td>
                                <td class="text-right">
                                    <p><?= formatN($R['return_excamount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_return_excamount']) ?>
                                        </i>
                                    <? } ?>
                                </td>
                                <td class="text-right">
                                    <p><?= formatN($R['cost_amount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_cost_amount']) ?>
                                        </i>
                                    <? } ?>
                                </td>
                                <td class="text-right">
                                    <p><?= formatN($R['profit_amount']) ?></p>
                                    <? if ($R['base_currency'] != 'yes') { ?>
                                        <i class="text-xs text-muted">
                                            <?= $basecurrency['name'] ?> <?= formatN($R['base_profit_amount']) ?>
                                        </i>
                                    <? } ?>
                                </td>
                                <td class="text-right <?= $R['profit_margin'] > 0 ? 'text-success' : 'text-danger' ?>"><?= $R['base_profit_amount'] == 0 ? 0 : formatN($R['profit_margin']) ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="17">
                                <div class="row d-flex justify-content-end">
                                    <div class="col-md-5">
                                        <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                            <thead>
                                            <tr>
                                                <td>Currency</td>
                                                <td class="text-right">Amount Exc</td>
                                                <td class="text-right">Expense</td>
                                                <td class="text-right">Credit Note</td>
                                                <td class="text-right">Cost</td>
                                                <td class="text-right">Profit</td>
                                                <td class="text-right">Margin %</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <? foreach ($totals['currencies'] as $currency => $R) { ?>
                                                <tr>
                                                    <td><?= $currency ?></td>
                                                    <td class="text-right"><?= formatN($R['grand_amount']) ?></td>
                                                    <td class="text-right"><?= formatN($R['sale_expense']) ?></td>
                                                    <td class="text-right"><?= formatN($R['return_excamount']) ?></td>
                                                    <td class="text-right"><?= formatN($R['cost_amount']) ?></td>
                                                    <td class="text-right"><?= formatN($R['profit_amount']) ?></td>
                                                    <td class="text-right"><?= formatN($R['profit_amount'] * 100 / $R['cost_amount']) ?></td>
                                                </tr>
                                            <? } ?>
                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right bg-rosepink text-white text-weight-bold">Base (<?= $basecurrency['name'] ?>)
                                                </td>
                                                <td class="text-right bg-rosepink text-white text-weight-bold"><?= formatN($totals['base']['grand_amount']) ?></td>
                                                <td class="text-right bg-rosepink text-white text-weight-bold"><?= formatN($totals['base']['sale_expense']) ?></td>
                                                <td class="text-right bg-rosepink text-white text-weight-bold"><?= formatN($totals['base']['return_excamount']) ?></td>
                                                <td class="text-right bg-rosepink text-white text-weight-bold"><?= formatN($totals['base']['cost_amount']) ?></td>
                                                <td class="text-right bg-rosepink text-white text-weight-bold"><?= formatN($totals['base']['profit_amount']) ?></td>
                                                <td class="text-right bg-rosepink text-white text-weight-bold"><?= formatN($totals['base']['profit_amount'] * 100 / $totals['base']['cost_amount']) ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
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
        $('#branchid').select2({width:'100%'});
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Choose location');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'Choose sales person', 2);
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'Choose client', 2);
    });


</script>
