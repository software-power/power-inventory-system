<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="proforma_projection_report">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Proforma No</label>
                            <input type="text" name="proformano" class="form-control" placeholder="proforma no">
                        </div>
                        <div class="col-md-4">
                            <label for="">Proforma Status</label>
                            <select class="form-control" name="proforma_status">
                                <option value="" selected disabled>--Choose Status--</option>
                                <option value="closed">Closed</option>
                                <option value="pending">Pending</option>
                                <option value="invalid">Invalid</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Branch</label>
                            <select class="form-control" name="branchid">
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($_SESSION['member']['branchid'], $b['id']) ?>
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
                            <select id="brandid" class="form-control" name="modelid">
                                <option value="" selected>--All--</option>
                                <? foreach ($brands as $key => $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <label>Department</label>
                            <select id="depart" class="form-control" name="depart">
                                <option value="" selected>--All--</option>
                                <? foreach ($departments as $key => $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Product Category</label>
                            <select id="categories" class="form-control" name="productcategory">
                                <option value="" selected>--All--</option>
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
                <h2 class="panel-title">Proforma Projection report</h2>
            </header>
            <div class="panel-body">
                <form class="pb-lg" style="border-bottom: 1px solid #dadada;">
                    <input type="hidden" name="module" value="stocks">
                    <input type="hidden" name="action" value="proforma_projection_report">
                    <div class="row mb-md d-flex justify-content-center">
                        <div class="col-md-4 d-flex align-items-center">
                            <span>Search:</span>
                            <input type="text" name="search" minlength="3" class="form-control"
                                   placeholder="Major product search name or barcode or description" value="<?= $search ?>" style="border-radius: 5px;">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-search"></i> SEARCH</button>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    Stock Location:
                                    <? if (STOCK_LOCATIONS) { ?>
                                        <select id="locationid" class="form-control" name="stocklocation">
                                            <? foreach ($branchLocations as $R) {?>
                                                <option <?=selected($location['id'], $R['id'])?> value="<?= $R['id'] ?>"><?= $R['name'] ?> - <?= $R['branchname'] ?></option>
                                            <?}?>
                                        </select>
                                    <? } else { ?>
                                        <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                        <input type="hidden" class="form-control" name="stocklocation" value="<?= $location['id'] ?>">
                                    <? } ?>
                                </div>
                                <div class="col-md-3">
                                    Brand <span class="text-danger">*</span>:
                                    <select id="brandid" class="form-control" name="brandid">
                                        <option value="">-- Brand --</option>
                                        <? foreach ($brands as $d) { ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Product Category <span class="text-danger">*</span>:
                                    <select id="productcategoryid" class="form-control" name="productcategoryid">
                                        <option value="">-- Category --</option>
                                        <? foreach ($productcategories as $d) { ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Department <span class="text-danger">*</span>:
                                    <select id="departmentid" class="form-control" name="departmentid">
                                        <option value="">-- Department --</option>
                                        <? foreach ($departments as $d) { ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-xs">
                                <div class="col-md-3">
                                    From:
                                    <input type="date" class="form-control" name="fromdate" value="<?=$fromdate?>">
                                </div>
                                <div class="col-md-3">
                                    To:
                                    <input type="date" class="form-control" name="todate" value="<?=$todate?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <h5>Stock Location: <span class="text-danger text-weight-bold"><?=$location['name']?> - <?=$location['branchname']?></span></h5>
                <p class="text-primary"><?= $title ?></p>
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Barcode</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Prepared</th>
                            <th>Closed</th>
                            <th>Pending</th>
                            <th>Current Stock</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($products as $key => $R) { ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $R['barcode_office'] ?></td>
                                <td><?= $R['name'] ?></td>
                                <td><?= $R['description'] ?></td>
                                <td><?= $R['brandName'] ?></td>
                                <td><?= $R['productcategory'] ?></td>
                                <td><?= $R['prepared'] ?></td>
                                <td><?= $R['closed'] ?></td>
                                <td><?= $R['pending'] ?></td>
                                <td><?= $R['stock_qty'] ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {

        $('#locationid,#departmentid,#brandid,#productcategoryid,#subcategoryid,#unitid,#bulkunitid').select2({width: '100%'});
    });


</script>
