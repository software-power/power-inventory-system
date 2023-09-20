<style>
    #spinnerHolder {
        position: fixed;
        display: none;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        height: 100vh;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.16);
        z-index: 50000;
    }
</style>
<header class="page-header">
    <h2>Approve Stock Transfer</h2>
</header>


<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h1 class="text-weight-semibol text-danger">Please wait</h1>
    </div>
</div>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="d-flex justify-content-between align-items-center">
                <h4>
                    Approve Transfer No: <span class="text-primary receiptno"
                                               style="font-weight: bold"><?= $transfer['transferno'] ?></span>
                </h4>
                <div id="spinner" style="display: none">
                    <div class="d-flex align-items-center">
                        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml"
                                height="100"
                                width="100"></object>
                        <h4>Please wait..</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <? if (Users::can(OtherRights::approve_transfer)) { ?>
                        <form id="cancel-form" action="<?= url('stocks', 'cancel_transfer') ?>" method="POST" style="margin:0;"
                              class="d-flex align-items-center" onsubmit="return confirm('Do you want to cancel this transfer?')">
                            <input type="hidden" name="transferno" value="<?= $transfer['transferno'] ?>">
                            <button class="btn btn-danger ml-xs">
                                <i class="fa fa-edit"></i> Cancel Transfer
                            </button>
                        </form>
                    <? } ?>
                </div>
            </div>
        </header>
        <form id="transfer-form" action="<?= url('stocks', 'confirm_approval') ?>" method="POST" onsubmit="return validateInputs()">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="transferno" value="<?= $transfer['transferno'] ?>">
                        <p>From: <span class="text-primary"><?= $transfer['fromlocation'] ?> - <?= $transfer['frombranchname'] ?></span></p>
                        <p>To: <span class="text-primary"><?= $transfer['tolocation'] ?> - <?= $transfer['tobranchname'] ?></span></p>
                        <p>Issued By: <span class="text-primary"><?= $transfer['transferby'] ?></span></p>
                        <p>Issued Date: <span class="text-primary"><?= fDate($transfer['doc'], 'd F Y H:i') ?></span></p>
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Total Transfer Cost %</h5>
                                <input id="transfer-cost-percent" type="number" step="0.00001" min="0" class="form-control"
                                       placeholder="Transfer cost %" onchange="calProductPrice(this)" onkeyup="calProductPrice(this)"
                                       data-source="percent">
                            </div>
                            <div class="col-md-4">
                                <h5>Total Transfer Cost (<span class="text-weight-bold"><?= $basecurrency['name'] ?></span>):</h5>
                                <input id="transfer-cost" type="number" step="0.01" min="0" class="form-control" name="transfer_cost"
                                       placeholder="Transfer cost" onchange="calProductPrice(this)" onkeyup="calProductPrice(this)"
                                       data-source="amount">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-success ml-md approveBtn">
                                <i class="fa fa-check"></i> Approve
                            </button>
                            <? if (false) { ?>
                                <a href="<?= url('stocks', 'issue_transfer', ['transferno' => $transfer['transferno']]) ?>"
                                   class="btn btn-warning ml-xs editBtn"><i class="fa fa-edit"></i> Edit</a>
                            <? } ?>
                        </div>
                    </div>
                </div>
                <h5 class="mt-lg">Products</h5>
                <small class="text-rosepink">NB: system will always pick the highest price</small>
                <table class="table table-hover mb-none table-bordered" style="font-size:10pt;">
                    <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align: middle">#</th>
                        <th rowspan="2" style="vertical-align: middle;width: 6%">Barcode</th>
                        <th rowspan="2" style="vertical-align: middle;width: 25%">Description</th>
                        <th rowspan="2" style="vertical-align: middle">Qty</th>
                        <th colspan="3" class="text-center"><?= $transfer['frombranchname'] ?></th>
                        <th colspan="2" class="text-center"><?= $transfer['tobranchname'] ?></th>
                        <th rowspan="2" style="vertical-align: middle"></th>
                    </tr>
                    <tr>
                        <td>Base Price Inc</td>
                        <td>Cost</td>
                        <td>Quick Sale</td>
                        <td>Cost</td>
                        <td>Quick Sale</td>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($transfer['products'] as $R) { ?>
                        <tr class="product">
                            <td><?= $count ?></td>
                            <td><?= $R['barcode_office']?:$R['barcode_manufacture'] ?></td>
                            <td>
                                <?= $R['productname'] ?>
                                <i class="d-block mt-xs text-muted"><?= $R['productdescription'] ?></i>
                            </td>
                            <td class="qty"><?= $R['qty'] ?></td>
                            <td><?= formatN($R['inc_base']) ?></td>
                            <td>
                                <?= formatN($R['from']['costprice']) ?>
                                <input type="hidden" class="from_costprice" value="<?= $R['from']['costprice'] ?>">
                            </td>
                            <td>
                                <?= formatN($R['from']['inc_quicksale_price']) ?>
                                <input type="hidden" class="from_quicksale_price" value="<?= $R['from']['inc_quicksale_price'] ?>">
                            </td>
                            <td>
                                <input type="hidden" name="prices[<?= $R['detailId'] ?>][id]" value="<?= $R['detailId'] ?>">
                                <input type="number" step="0.01" min="<?= $R['from']['costprice'] ?>" class="form-control to_costprice"
                                       name="prices[<?= $R['detailId'] ?>][costprice]" value="<?= $R['to']['costprice'] ?>"
                                       data-current="<?= $R['to']['costprice'] ?>">
                                <small>Current cost: <?= formatN($R['to']['costprice']) ?></small>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="<?= $R['inc_base'] ?>" class="form-control to_quicksale_price"
                                       name="prices[<?= $R['detailId'] ?>][quicksale_price]" value="<?= $R['to']['inc_quicksale_price'] ?>"
                                       data-current="<?= $R['to']['inc_quicksale_price'] ?>">
                                <small>Current quick sale price: <?= formatN($R['to']['inc_quicksale_price']) ?></small>
                            </td>
                            <td>
                        <? if ($R['trackserialno']) { ?>
                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#view-serialno-<?=$R['detailId']?>">Serial no</button>
                            <div class="modal fade" id="view-serialno-<?=$R['detailId']?>" tabindex="-1" role="dialog" aria-labelledby="view-serialno-<?=$R['detailId']?>"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-center">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                        aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">Serial Nos</h4>
                                            <p class="text-primary"><?=$R['productname']?></p>
                                        </div>
                                        <div class="modal-body">
                                            <div style="max-height: 50vh;overflow-y: auto;padding:2px;">
                                                <table class="table table-bordered batches" style="font-size: 9pt">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 15%">#</th>
                                                        <th>Number</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? $count = 1;
                                                    foreach ($R['serialnos'] as $s) { ?>
                                                        <tr>
                                                            <td><?= $count ?></td>
                                                            <td><?= $s['number'] ?></td>
                                                        </tr>
                                                        <? $count++;
                                                    } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? } ?>
                            </td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </form>
    </section>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#client', "?module=clients&action=getClients&format=json", 'Search client');
        calProductPrice();
    });

    function validateInputs() {
        $('#spinnerHolder').show();
        // return false;
    }

    function calProductPrice(obj) {
        let transfer_cost = parseFloat($('#transfer-cost').val()) || 0;
        let transfer_cost_percent = parseFloat($('#transfer-cost-percent').val()) || 0;
        let source = $(obj).data('source');
        if (transfer_cost < 0) {
            triggerError("Enter valid value");
            $('#transfer-cost').focus().val(0);
        }
        let total_product_value = 0, increase_ratio = 0;
        $('tr.product').each(function (i, tr) {
            let qty = parseInt($(tr).find('.qty').text());
            let from_costprice = parseFloat($(tr).find('.from_costprice').val()) || 0;

            total_product_value += (qty * from_costprice);
        });

        if (source === 'percent') {
            increase_ratio = transfer_cost_percent/100;
            transfer_cost = increase_ratio*total_product_value;
            $('#transfer-cost').val(`${(transfer_cost).toFixed(2)}`);
        } else {
            increase_ratio = transfer_cost / total_product_value;
            $('#transfer-cost-percent').val(`${(increase_ratio * 100).toFixed(5)}`);
        }


        $('tr.product').each(function (i, tr) {
            let from_costprice = parseFloat($(tr).find('.from_costprice').val()) || 0;
            let from_quicksale_price = parseFloat($(tr).find('.from_quicksale_price').val()) || 0;
            let current_to_costprice = parseFloat($(tr).find('.to_costprice').data('current')) || 0;
            let current_to_quicksale_price = parseFloat($(tr).find('.to_quicksale_price').data('current')) || 0;

            let new_to_costprice = from_costprice * (1 + increase_ratio);
            let new_to_quicksale_price = from_quicksale_price * (1 + increase_ratio);
            if (new_to_costprice > current_to_costprice) {
                $(tr).find('.to_costprice').val(new_to_costprice.toFixed(2));
            } else {
                $(tr).find('.to_costprice').val(current_to_costprice.toFixed(2));
            }
            if (new_to_quicksale_price > current_to_quicksale_price) {
                $(tr).find('.to_quicksale_price').val(new_to_quicksale_price.toFixed(2));
            } else {
                $(tr).find('.to_quicksale_price').val(current_to_quicksale_price.toFixed(2));
            }
        });

    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
