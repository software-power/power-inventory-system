<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="audit_report">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Invoice No</label>
                            <input type="text" name="invoiceno" class="form-control" placeholder="invoice no">
                        </div>
                    </div>
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
                            <label>Product</label>
                            <select id="productid" class="form-control" name="productid"></select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <label>Sales Person</label>
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" class="form-control" name="userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            <label>Client</label>
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                        <div class="col-md-4">
                            <label>Brand</label>
                            <select id="brandid" class="form-control" name="modelid"></select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <label>Department</label>
                            <select id="depart" class="form-control" name="depart"></select>
                        </div>
                        <div class="col-md-4">
                            <label>Tax Category</label>
                            <select id="" class="form-control" name="category">
                                <option value="" selected disabled>--Tax--</option>
                                <? foreach ($categories as $key => $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
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
                <h2 class="panel-title">Sales audit report</h2>
                <p class="text-primary"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Sales person</th>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Order By</th>
                            <th>Client</th>
                            <th>Product</th>
                            <th>Department</th>
                            <th>Brand</th>
                            <th>Currency</th>
                            <th>Unit Cost</th>
                            <th>Unit Price</th>
                            <th>Disc%</th>
                            <th>Discount</th>
                            <th>Qty</th>
                            <th>Amount Exc</th>
                            <th>Vat Amount</th>
                            <th>TAX %</th>
                            <th>Profit Amount</th>
                            <th>Profit Margin (%)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($sales as $key => $R) { ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $R['salesperson'] ?></td>
                                <td><?= $R['doc'] ?></td>
                                <td>
                                    <a href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>" target="_blank">
                                        <?= $R['receipt_no'] ?></a>
                                </td>
                                <td>
                                    <? if ($R['orderno']) { ?>
                                        <div class="d-block"><?=$R['order_creator']?></div>
                                        <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order <?= $R['orderno'] ?></a></small>
                                    <? } ?>
                                </td>
                                <td title="view client info">
                                    <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                                </td>
                                <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                    title="<?= $R['productname'] ?>"><?= $R['productname'] ?></td>
                                <td><?= $R['depatName'] ?></td>
                                <td><?= $R['brandname'] ?></td>
                                <td><?= $R['currencyname'] ?></td>
                                <td><?= formatN($R['unit_cost']) ?></td>
                                <td><?= formatN($R['price']) ?></td>
                                <td><?= formatN($R['discpercent']) ?>%</td>
                                <td><?= formatN($R['discount']) ?></td>
                                <td><?= $R['quantity'] ?></td>
                                <td><?= formatN($R['excamount']); ?></td>
                                <td><?= formatN($R['vatamount']); ?></td>
                                <td><?= $R['vat_rate'] ?></td>
                                <td><?= formatN($R['unit_profit'] * $R['quantity']) ?></td>
                                <td><?= formatN($R['non_stock'] ? 100 : $R['margin_percent']) ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="19">
                                <div class="row d-flex justify-content-end">
                                    <div class="col-md-5">
                                        NB: <span class="text-danger">Without expenses</span>
                                        <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                            <thead>
                                            <tr>
                                                <td>Currency</td>
                                                <td class="text-right">Total Price</td>
                                                <td class="text-right">Total Cost</td>
                                                <td class="text-right">Total Profit</td>
                                                <td class="text-right">Overall margin %</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <? foreach ($totals['currencies'] as $currency => $R) { ?>
                                                <tr>
                                                    <td><?= $currency ?></td>
                                                    <td class="text-right"><?= formatN($R['price']) ?></td>
                                                    <td class="text-right"><?= formatN($R['cost']) ?></td>
                                                    <td class="text-right"><?= formatN($R['price'] - $R['cost']) ?></td>
                                                    <td class="text-right"><?= formatN(($R['price'] - $R['cost']) * 100 / $R['cost']) ?></td>
                                                </tr>
                                            <? } ?>
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                                            <tr class="bg-rosepink text-white text-weight-bold">
                                                <td>Base (<?= $basecurrency['name'] ?>)</td>
                                                <td class="text-right"><?= formatN($totals['base']['price']) ?></td>
                                                <td class="text-right"><?= formatN($totals['base']['cost']) ?></td>
                                                <td class="text-right"><?= formatN($totals['base']['price'] - $totals['base']['cost']) ?></td>
                                                <td class="text-right"><?= formatN(($totals['base']['price'] - $totals['base']['cost']) * 100 / $totals['base']['cost']) ?></td>
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
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", 'Choose product');
        initSelectAjax('#brandid', "?module=model&action=getModels&format=json", 'Choose brand', 2);
        initSelectAjax('#depart', "?module=departments&action=getDepartments&format=json", 'Choose department', 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'Choose sales person', 2);
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'Choose client', 2);
    });


</script>
