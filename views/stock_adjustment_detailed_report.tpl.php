<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="adjustment_detailed_report">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="locationid" class="form-control" name="search[locationid]">
                                    <? foreach ($branchLocations as $R) { ?>
                                        <option <?= selected($location['id'], $R['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?>
                                            - <?= $R['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                <input type="hidden" class="form-control" name="search[locationid]" value="<?= $location['id'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-6">
                            <select id="productid" class="form-control" name="search[productid]">

                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-6">
                            Adjustment No:
                            <input type="number" class="form-control" placeholder="Adjustment Number"
                                   name="search[adjustmentno]">
                        </div>
                        <div class="col-md-6">
                            Batch No:
                            <input type="number" class="form-control" placeholder="Batch No"
                                   name="search[batchno]">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-6">
                            From: <span class="text-danger">*</span>
                            <input type="date" name="search[fromdate]" class="form-control for-input" value="<?=$fromdate?>">
                        </div>
                        <div class="col-md-6">
                            To: <span class="text-danger">*</span>
                            <input type="date" name="search[todate]" class="form-control for-input" value="<?=$todate?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-danger btn-block" data-dismiss="modal"><i
                                        class="fa fa-search"></i>
                                CANCEL
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-success btn-block"><i
                                        class="fa fa-refresh"></i>
                                RESET
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block"><i
                                        class="fa fa-search"></i>
                                SEARCH
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<header class="page-header">
    <h2>Stock Adjustment Detailed Report</h2>
</header>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">Stock Adjustment Detailed Report</h2>
                    <p class="text-primary"><?= $title ?></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end">
                    <button type="button" class="btn" data-toggle="modal" data-target="#search-modal"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="forCheckbox table table-hover mb-none" style="font-size: 10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Adjustment No</th>
                        <th>Stockid</th>
                        <th>Location</th>
                        <th>Product</th>
                        <th>Batch No</th>
                        <th>Expire date</th>
                        <th class="text-center">Qty</th>
                        <th>Before</th>
                        <th>After</th>
                        <th>Issued By</th>
                        <th>Issued date</th>
                    </tr>
                    </thead>
                    <tbody>

                    <? $count = 1;
                    foreach ($adjustmentList as $index => $product) { ?>
                        <? if (!$product['track_expire_date']) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $product['adjustmentno'] ?></td>
                                <td><?= $product['stockid'] ?></td>
                                <td><?= $product['locationname'] ?></td>
                                <td><?= $product['productname'] ?></td>
                                <td>-</td>
                                <td>-</td>
                                <td class="text-center <?=$product['action']==StockAdjustmentBatches::ACTION_ADD?'text-success':'text-danger'?>">
                                    <?=($product['action']==StockAdjustmentBatches::ACTION_ADD?'+':'-'). $product['qty'] ?>
                                </td>
                                <td><?= $product['current_stock'] ?></td>
                                <td><?= $product['after_qty'] ?></td>
                                <td><?= $product['issuedby'] ?></td>
                                <td><?= fDate($product['doc']) ?></td>
                            </tr>
                            <? $count++;
                        } else { ?>
                            <? foreach ($product['batches'] as $bi => $batch) { ?>
                                <tr>
                                    <td><?= $count ?></td>
                                    <td><?= $product['adjustmentno'] ?></td>
                                    <td><?= $product['stockid'] ?></td>
                                    <td><?= $product['locationname'] ?></td>
                                    <td><?= $product['productname'] ?></td>
                                    <td><?= $batch['batch_no'] ?></td>
                                    <td><?= $batch['expire_date'] ?></td>
                                    <td class="text-center <?=$batch['action']==StockAdjustmentBatches::ACTION_ADD?'text-success':'text-danger'?>">
                                        <?=($batch['action']==StockAdjustmentBatches::ACTION_ADD?'+':'-'). $batch['qty'] ?>
                                    </td>
                                    <td><?= $batch['before_qty'] ?></td>
                                    <td><?= $batch['after_qty'] ?></td>
                                    <td><?= $product['issuedby'] ?></td>
                                    <td><?= fDate($product['doc']) ?></td>
                                </tr>
                                <? $count++;
                            } ?>
                        <? } ?>
                        <?
                    } ?>
                    <input type="hidden" id="count" value="<?= $count ?>"/>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#locationid').select2({width: '100%'});
    });
</script>
