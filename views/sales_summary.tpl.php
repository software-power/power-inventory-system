<header class="page-header">
    <h2>Sales Summary Report</h2>
</header>


<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="sales_summary">
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
                                <option value="">--All branches --</option>
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
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
                            <label>Product Category</label>
                            <select id="productcategory" class="form-control" name="productcategory"></select>
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


<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search-modal"><i class="fa fa-search"></i> Open
                        filter
                    </button>
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Sales Summary Report</h2>
                <p class="text-primary"><?= $title ?> </p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Brand Name</th>
                            <th>Department</th>
                            <th>Count</th>
                            <th>Units</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($summary as $key => $P) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $P['productname'] ?></td>
                                <td><?= $P['brandname'] ?></td>
                                <td><?= $P['departmentname'] ?></td>
                                <td><?= $P['qty'] ?></td>
                                <td><?= $P['unitname'] ?></td>
                            </tr>
                            <?
                            $count++;
                        }?>
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
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Choose location');
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", 'Choose product');
        initSelectAjax('#brandid', "?module=model&action=getModels&format=json", 'Choose brand', 2);
        initSelectAjax('#depart', "?module=departments&action=getDepartments&format=json", 'Choose department', 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'Choose sales person', 2);
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'Choose client', 2);
        initSelectAjax('#productcategory', "?module=product_categories&action=getCategories&format=json", 'Choose category', 2);

        $('#branchid').select2({width:'100%'});
    });


</script>