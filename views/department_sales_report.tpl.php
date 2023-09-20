<style media="screen">

    .formholder h5 {
        font-size: 15px;
        font-weight: 600;
    }

    .panelControl {
        float: right;
    }


    .dropleft .dropdown-menu {
        top: 0;
        right: 100%;
        left: auto;
        margin-top: 0;
        margin-right: .125rem;
    }

    a.dropdown-item {
        text-decoration: none;
    }

    .dropdown-item {
        display: block;
        width: 100%;
        padding: .25rem 1.5rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }

    .table-responsive {
        min-height: 223px;
    }

    .select2-container {
        border: 1px solid #dadada;
        border-radius: 5px;
    }
</style>
<header class="page-header">
    <? if ($SR_MODE) { ?>
        <h2>Department Sales Report SR</h2>
    <? } else { ?>
        <h2>Department Sales Report</h2>
    <? } ?>
</header>

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
                        <div class="col-md-3">
                            Department:
                            <select id="departmentid" name="departmentid" class="form-control">
                                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                    <option value="" selected>--All departments --</option>
                                <? } ?>
                                <? foreach ($departments as $d) { ?>
                                    <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            Client:
                            <select id="clientid" name="clientid" class="form-control"></select>
                        </div>
                        <div class="col-md-3">
                            Order/Invoice by:
                            <select id="userid" name="createdby" class="form-control"></select>
                        </div>
                        <div class="col-md-3">
                            Brand:
                            <select id="brandid" name="brandid" class="form-control">
                                <option value="" selected>--All brands --</option>
                                <? foreach ($brands as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-3">
                            Product category:
                            <select id="productcategoryid" name="productcategoryid" class="form-control">
                                <option value="" selected>--All categories --</option>
                                <? foreach ($productcategories as $pc) { ?>
                                    <option value="<?= $pc['id'] ?>"><?= $pc['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            Subcategory:
                            <select id="subcategoryid" name="subcategoryid" class="form-control">
                                <option value="" selected>--All subcategories --</option>
                                <? foreach ($subcategories as $sub) { ?>
                                    <option value="<?= $sub['id'] ?>"><?= $sub['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            From:
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-3">
                            To:
                            <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mt-md">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-success">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panelControl">
                <button type="button" class="btn" title="Home" data-toggle="modal"
                        data-target="#search-modal"><i
                            class="fa fa-search"></i> Open filter
                </button>
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
            </div>
            <? if ($SR_MODE) { ?>
                <h2 class="panel-title">Department Sales Report SR</h2>
            <? } else { ?>
                <h2 class="panel-title">Department Sales Report</h2>
            <? } ?>
            <p class="text-primary mt-md"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th style="width:80px">Invoice</th>
                        <th>Customer Name</th>
                        <th>Location</th>
                        <th>Order By</th>
                        <th>Invoice By</th>
                        <th>Invoice date</th>
                        <th>Currency</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Exc price</th>
                        <th>VAT%</th>
                        <th class="text-right">Exc Amount</th>
                        <th class="text-right">VAT Amount</th>
                        <th class="text-right">Total Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($salesdetails as $R) { ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><?= $R['invoiceno'] ?></td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                            <td>
                                <? if ($R['orderno']) { ?>
                                    <div><?= $R['order_creator'] ?></div>
                                    <small><a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderno']]) ?>">Order <?= $R['orderno'] ?></a></small>
                                <? } ?>
                            </td>
                            <td><?= $R['salesperson'] ?></td>
                            <td><?= fDate($R['invoicedate'], 'd F Y H:i') ?></td>
                            <td><?= $R['currencyname'] ?></td>
                            <td><?= $R['productname'] ?></td>
                            <td><?= $R['quantity'] ?></td>
                            <td class="text-right"><?= $R['sellingprice'] ?></td>
                            <td class="text-right"><?= $R['vat_rate'] ?></td>
                            <td class="text-right"><?= formatN($R['excamount']) ?></td>
                            <td class="text-right"><?= formatN($R['vatamount']) ?></td>
                            <td class="text-right"><?= formatN($R['incamount']) ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#detail-modal" data-toggle="modal"
                                           data-salesid="<?= $R['salesid'] ?>"
                                           data-receiptno="<?= $R['receipt_no'] ?>"
                                           title="View"><i class="fa-file fa"></i> View Details</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", "client", 2);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        $('#departmentid,#brandid,#productcategoryid,#subcategoryid').select2({width: '100%'});
    });

</script>
