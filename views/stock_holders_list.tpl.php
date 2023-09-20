<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<header class="page-header">
    <h2>Stock Holders</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="stock_holders">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row mb-md">
                        <div class="col-md-6">
                            Location:
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="locationid" class="form-control" name="locationid">
                                    <? foreach ($branchLocations as $R) {?>
                                        <option <?=selected($selected_location, $R['id'])?> value="<?= $R['id'] ?>"><?= $R['name'] ?> - <?= $R['branchname'] ?></option>
                                    <?}?>
                                </select>
                            <? } else { ?>
                                <input type="hidden" name="locationid" value="<?= $defaultLocation['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $defaultLocation['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            Client:
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                        <div class="col-md-6">
                            Product:
                            <select id="productid" class="form-control" name="productid"></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <button class="btn" href="#search-modal" data-toggle="modal">
                        <i class="fa fa-search"></i> Open Search
                    </button>
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Stock Holders</h2>
                <p>Filter: <span class="ml-md text-primary"><?= $title ?></span></p>
            </header>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#clients"><i class="fa fa-users"></i> Clients</a></li>
                    <li><a data-toggle="tab" href="#detailed"><i class="fa fa-list"></i> Detailed</a></li>
                </ul>
                <div class="tab-content">
                    <div id="clients" class="tab-pane fade in active">
                        <div class="table-responsive">
                            <table class="table table-hover mb-none" style="font-size:13px;" id="stock-report">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Client</th>
                                    <th>Location</th>
                                    <th>Product</th>
                                    <th>Held Qty</th>
                                    <th>Held until</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? $count = 1;
                                foreach ($holders as $R) { ?>
                                    <tr>
                                        <td><?= $count ?></td>
                                        <td><?= $R['clientname'] ?> </td>
                                        <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                                        <td><?= $R['productname'] ?> </td>
                                        <td><?= $R['qty'] ?> </td>
                                        <td><?= fDate($R['hold_until'], 'd F Y H:i') ?></td>
                                        <td>

                                        </td>
                                    </tr>
                                    <? $count++;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="detailed" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-hover mb-none" style="font-size:13px;" id="stock-report">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Held in</th>
                                    <th>Client</th>
                                    <th>Location</th>
                                    <th>Held Qty</th>
                                    <th>Held until</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? $count = 1;
                                foreach ($holders_detailed as $R) { ?>
                                    <tr>
                                        <td><?= $count ?></td>
                                        <td><?= $R['productname'] ?> </td>
                                        <td>
                                            <? if ($R['salesid']) { ?>
                                                <a href="<?= url('payments', 'invoice_list', ['salesid' => $R['salesid']]) ?>" title="Invoice no"
                                                   style="font-size: 9pt"><?= $R['invoiceno'] ?></a>
                                            <? } else if ($R['orderid']) { ?>
                                                <a href="<?= url('orders', 'order_list', ['ordernumber' => $R['orderid']]) ?>" title="order no"
                                                   style="font-size: 9pt">Order No <?= $R['orderid'] ?></a>
                                            <? } else { ?>
                                                <a href="<?= url('proformas', 'proforma_list', ['proforma_no' => $R['proformaid']]) ?>"
                                                   title="proforma no"
                                                   style="font-size: 9pt">
                                                    Proforma No <?= $R['proformaid'] ?></a>
                                            <? } ?>
                                        </td>
                                        <td><?= $R['clientname'] ?> </td>
                                        <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                                        <td><?= $R['qty'] ?> </td>
                                        <td><?= fDate($R['hold_until'], 'd F Y H:i') ?></td>

                                        <td>

                                        </td>
                                    </tr>
                                    <? $count++;
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        <?if(IS_ADMIN){?>
        $('#locationid').select2({width: '100%'});
        <?}?>
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", "Choose client");

    });

</script>
