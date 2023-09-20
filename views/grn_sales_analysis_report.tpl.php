<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="<?= $action ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            GRN no:
                            <input type="text" class="form-control" placeholder="GRN Number" name="grnid">
                        </div>
                        <div class="col-md-4">
                            Supplier:
                            <select id="supplierid" class="form-control" name="supplierid">
                                <option value="" selected disabled>Select Supplier</option>
                                <? foreach ($suppliers as $ID => $S) { ?>
                                    <option value="<?= $S['id'] ?>"><?= $S['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control" name="productid"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            From: <span class="text-danger">*</span>
                            <input type="date" name="fromdate" class="form-control for-input" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-4">
                            To: <span class="text-danger">*</span>
                            <input type="date" name="todate" class="form-control for-input" value="<?= $todate ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                    <button type="reset" class="btn btn-default" data-dismiss="modal"><i class="fa fa-search"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<header class="page-header">
    <h2>GRN Sales Analysis Report</h2>
</header>
<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="panel-title">GRN Sales Analysis Report</h2>
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
                            <th>Barcode</th>
                            <th>Product</th>
                            <th>GRN No</th>
                            <th>Supplier Name</th>
                            <th>Purchase date</th>
                            <th>Opening Qty</th>
                            <th>Purchase qty</th>
                            <th>Costprice</th>
                            <th>Used</th>
                            <th>Balance</th>
                        </tr>
                        </thead>
                        <tbody>

                        <? $count = 1;
                        foreach ($items as $id => $R) { ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $R['barcode_office'] ?: $R['barcode_manufacture'] ?></td>
                                <td><?= $R['productname'] ?></td>
                                <td><?= $R['grnid'] ?></td>
                                <td><?= $R['suppliername'] ?></td>
                                <td><?= fDate($R['doc']) ?></td>
                                <td><?= $R['opening_qty'] ?></td>
                                <td><?= $R['quantity'] ?></td>
                                <td><?= $R['price'] ?></td>
                                <td><?= $R['quantity'] - $R['current_qty'] ?></td>
                                <td><?= $R['current_qty'] ?></td>
                            </tr>
                        <? } ?>
                        <input type="hidden" id="count" value="<?= $count ?>"/>
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
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#locationid,#supplierid').select2({width: '100%'});

    });
</script>
