<script src="assets/slick-carousel/slick/slick.js"></script>
<link rel="stylesheet" type="text/css" href="assets/slick-carousel/slick/slick.css">
<style type="text/css">
    .dashboard_container .table thead td, .dashboard_container .table thead th {
        text-align: left !important;
        /*border: solid .5px #d2322d;*/
    }

    .dashboard_container .card-header::after {
        background-color: #d2322d !important;
    }
</style>
<section>
    <div id="main_dashboard" class="col-md-12 dashboard_container">
        <div class="row">
            <div class="col-md-12">
                <h6 class="card-header">Personal Information</h6>
                <div class="row-card">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="float:left">Hello <strong><?= $_SESSION['member']['name'] ?></strong>, welcome
                                back</h5>
                            <p style="float:right" class="card-p">
                                <span><i class="fa fa-user"></i> <?= $_SESSION['member']['username'] ?> |</span>
                                <span><i class="fa fa-phone"></i> <?= $_SESSION['member']['mobile'] ?> |</span>
                                <span><i class="fa fa-at"></i> <?= $_SESSION['member']['email']; ?> </span>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row-card-sub">
                                <label for="">Branch</label>
                                <p class="value"><?= $branch['name'] ? $branch['name'] : 'Unassigned' ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row-card-sub">
                                <label for="">Location</label>
                                <p class="value"><?= $location['name'] ? $location['name'] : 'Unassigned' ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row-card-sub">
                                <label for="">Department</label>
                                <p class="value"><?= $department['name'] ? $department['name'] : 'Unassigned' ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row-card-sub">
                                <label for="">Position</label>
                                <p class="value"><?= $_SESSION['member']['rolename'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <? if (IS_ADMIN) { ?>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="card-header">Top Five Cash Sales </h6>
                    <div style="margin-top:10px;" class="row-card">
                        <div class="table-responsive">
                            <p style="margin:0">Top Five Today Cash Sales</p>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Invoice Type</th>
                                    <th>Issue Date</th>
                                    <th>Sales Person</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody style="font-size: 10pt">
                                <? foreach ($cash_sales as $key => $CR) { ?>
                                    <tr>
                                        <td><?= $CR['clientname'] ?></td>
                                        <td><?= ucfirst($CR['paymenttype']) ?></td>
                                        <td><?= $CR['issudate'] ?></td>
                                        <td><?= $CR['salesPerson'] ?></td>
                                        <td><?= $CR['currency_name'] ?></td>
                                        <td>
                                            <p><?= formatN($CR['full_amount']) ?></p>
                                            <? if ($CR['base_currency'] != 'yes') { ?>
                                                <i class="text-xs text-muted">
                                                    <?= $baseCurrency['name'] ?> <?= formatN($CR['base_full_amount']) ?></i>
                                            <? } ?>
                                        </td>
                                        <td>
                                            <a title="View More" target="_blank"
                                               class="btn btn-xs btn-default text-primary"
                                               href="<?= url('sales', 'view_invoice', 'salesid=' . $CR['invoiceno']) ?>">
                                                <?= $CR['receipt_no'] ?></a>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8" class=""
                                        style="background: #d2322d; color: #ffffff">
                                        <? foreach ($total_cash as $currency=>$total) {?>
                                            <span class="ml-xlg d-inline-block"><?=$currency?> <?= formatN($total) ?></span>
                                        <?}?>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="card-header">Top Five Credit Sales </h6>
                    <div style="margin-top:10px;" class="row-card">
                        <div class="table-responsive">
                            <p style="margin:0">Top Five Today Credit Sales</p>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Invoice Type</th>
                                    <th>Issue Date</th>
                                    <th>Sales Person</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody style="font-size: 10pt">
                                <? foreach ($credit_sales as $key => $CR) { ?>
                                    <tr>
                                        <td><?= $CR['clientname'] ?></td>
                                        <td><?= ucfirst($CR['paymenttype']) ?></td>
                                        <td><?= $CR['issudate'] ?></td>
                                        <td><?= $CR['salesPerson'] ?></td>
                                        <td><?= $CR['currency_name'] ?></td>
                                        <td>
                                            <p><?= formatN($CR['full_amount']) ?></p>
                                            <? if ($CR['base_currency'] != 'yes') { ?>
                                                <i class="text-xs text-muted">
                                                    <?= $baseCurrency['name'] ?> <?= formatN($CR['base_full_amount']) ?></i>
                                            <? } ?>
                                        </td>
                                        <td>
                                            <a title="View More" target="_blank"
                                               class="btn btn-xs btn-default text-primary"
                                               href="<?= url('sales', 'view_invoice', 'salesid=' . $CR['invoiceno']) ?>">
                                                <?= $CR['receipt_no'] ?></a>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="8" class=""
                                        style="background: #d2322d; color: #ffffff">
                                        <? foreach ($total_credit as $currency=>$total) {?>
                                            <span class="ml-xlg d-inline-block"><?=$currency?> <?= formatN($total) ?></span>
                                        <?}?>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <h6 class="card-header">Top Selling Products This Month </h6>
                    <div style="margin-top:10px;" class="row-card">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Brand</th>
                                    <th>Tax</th>
                                    <th>Count</th>
                                </tr>
                                </thead>
                                <tbody style="font-size: 9pt">
                                <? foreach ($topsales as $key => $P) { ?>
                                    <tr>
                                        <td><?= $P['productname'] ?></td>
                                        <td><?= $P['brandname'] ?></td>
                                        <td><?= $P['taxcategory'] ?></td>
                                        <td><?= $P['qty'] ?></td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="card-header">Top In Stock Products</h6>
                    <div style="margin-top:10px;" class="row-card">
                        <div class="table-responsive">
                            <table class="table table-lightborder">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody style="font-size: 9pt">
                                <? $count = 0;
                                foreach ($topProducts as $key => $P) { ?>
                                    <tr>
                                        <td><?= $count + 1 ?></td>
                                        <td><?= $P['name'] ?></td>
                                        <td><?= $P['total'] ?></td>
                                    </tr>
                                    <? $count++;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <h6 class="card-header">Top Out of Stock Products</h6>
                    <div style="margin-top:10px;" class="row-card">
                        <div class="table-responsive">
                            <!-- <p style="margin:0">Top Five Today Credidt Sales</p> -->
                            <table class="table table-lightborder">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody style="font-size: 9pt">
                                <? $count = 0;
                                foreach ($topProductsOut as $key => $P) { ?>
                                    <tr>
                                        <td><?= $count + 1 ?></td>
                                        <td><?= $P['name'] ?></td>
                                        <td><?= $P['total'] ?></td>
                                    </tr>
                                    <? $count++;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h6 class="card-header">Order Summary </h6>
                    <div class="col-md-12">
                        <div class="row-card">
                            <label for="">TOTAL NUMBER OF PENDING ORDERS</label>
                            <p class="value"><?= $totalOrders ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <? } else { ?>
            <div class="row">
                <div class="col-md-12">
                    <h6 class="card-header">Sales summary </h6>
                    <div class="col-md-12">
                        <div class="row-card">
                            <label for="">TODAY TOTAL SALES</label>
                            <p class="value"><?=$baseCurrency['name']?>   <?= formatN($todayTotalSales) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>

        <div class="row bottom-wrapper"></div>
    </div>
</section>