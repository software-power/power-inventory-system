<header class="page-header">
    <h2>Client Ledger Report</h2>
</header>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Client Ledgers Summary</h2>
                        <p><?=$title?></p>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end">
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#filter-modal">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Client</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>TIN</th>
                            <th>Created at</th>
                            <th>Total Deposited Advance</th>
                            <th>Total Offset</th>
                            <th class="text-right">Balance</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($clientLedger as $R) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $R['name'] ?></td>
                                <td><?= $R['mobile'] ?></td>
                                <td><?= $R['email'] ?></td>
                                <td><?= $R['tinno'] ?></td>
                                <td><?= fDate($R['doc']) ?></td>
                                <td><?= formatN($R['totalDeposit']) ?></td>
                                <td><?= formatN($R['totalOffset']) ?></td>
                                <td align="right" class="text-success" style="font-weight: bold"><?= formatN($R['balance']) ?></td>
                                <td><a href="#" class="btn btn-default btn-sm" title="view detailed">View</a></td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>

<div class="modal fade" id="filter-modal" role="dialog" aria-labelledby="filter-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-center">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="referral_report">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Product</label>
                            <select id="product" name="productId" class="form-control"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Doctor</label>
                            <select id="doctor" name="doctorId" class="form-control"></select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Hospital</label>
                            <select id="hospital" name="hospitalId" class="form-control"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>From</label>
                            <input type="date" class="form-control" name="fromdate">
                        </div>
                        <div class="form-group col-md-6">
                            <label>To</label>
                            <input type="date" class="form-control" name="todate">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="reset" class="btn btn-default btn-sm">Reset</button>
                    <button type="submit" class="btn btn-success btn-sm confirm-btn">Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax("#product", "?module=products&action=getProducts&format=json", "Select product", 2);
        initSelectAjax("#doctor", "?module=doctors&action=getDoctors&format=json", "Select doctor", 2);
        initSelectAjax("#hospital", "?module=hospitals&action=getHospitals&format=json", "Select hospital", 2);
    });
</script>
