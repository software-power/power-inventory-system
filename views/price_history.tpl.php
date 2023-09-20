<header class="page-header">
    <h2>Price History</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <h2 class="panel-title">Price History</h2>
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-9">
                        <form class="d-flex align-items-center col-md-10">
                            <input type="hidden" name="module" value="hierarchics">
                            <input type="hidden" name="action" value="price_history">
                            <label class="ml-md">Product:</label>
                            <select name="productid" id="productid" class="form-control"></select>
                            <? if (IS_ADMIN) { ?>
                                <label class="ml-md">Branch:</label>
                                <select class="form-control" name="branchid">
                                    <? foreach ($branches as $key => $R) { ?>
                                        <option <?= selected($R['id'], $currentBranch['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                    <? } ?>
                                </select>
                            <? } ?>
                            <button class="btn btn-success btn-sm ml-md">Search</button>
                        </form>
                    </div>
                    <p class="text-primary"><?= $title ?></p>
                </div>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <div class="d-flex align-items-center mb-md">
                        <span class="bg-success mr-sm rounded" style="height: 15px;width: 15px;"></span>
                        <span>Current Price</span>
                    </div>
                    <table class="table table-condensed mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Date</th>
                            <th>Issued By</th>
                            <th>Costprice</th>
                            <th>Quick Price</th>
                            <th>Remarks</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($logs as $l) {
                            $lines = explode(PHP_EOL, $l['remarks']);
                            $remarks = count($lines) > 1 ? $lines[0] . " ..." : $lines[0];
                            ?>
                            <tr class="<?= $l['current_price'] ? 'bg-success' : '' ?>" title="<?= $l['remarks'] ?>">
                                <td width="80px"><?= $count ?></td>
                                <td><?= fDate($l['doc'], 'd F Y H:i') ?></td>
                                <td><?= $l['issuedby'] ?></td>
                                <td><?= formatN($l['costprice']) ?></td>
                                <td><?= formatN($l['quick_price_inc']) ?></td>
                                <td><?= $remarks ?></td>
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

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', `?module=products&action=getProducts&format=json`, 'choose product');
    });
</script>
