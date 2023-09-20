<header class="page-header">
    <h2>View Stock Transfer</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-lg-8 col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Transfer No: <span class="text-primary receiptno" style="font-weight: bold"><?= $transfer['transferno'] ?></span></h4>
                </div>
            </header>
            <div class="panel-body">
                <p>From: <span class="text-primary"><?= $transfer['fromlocation'] ?> - <?= $transfer['frombranchname'] ?></span></p>
                <p>To: <span class="text-primary"><?= $transfer['tolocation'] ?> - <?= $transfer['tobranchname'] ?></span></p>
                <p>Transfer Cost: <span class="text-primary"><?= $basecurrency['name'] ?> <?= formatN($transfer['transfer_cost']) ?></span></p>
                <p>Issued By: <span class="text-primary"><?= $transfer['transferby'] ?></span></p>
                <p>Issued Date: <span class="text-primary"><?= fDate($transfer['doc'], 'd F Y H:i') ?></span></p>
                <? if ($transfer['status'] != 'active') { ?>
                    <p>Status: <span class="text-rosepink">Canceled</span></p>
                <? } elseif ($transfer['auto_approve']) { ?>
                    <p>Status: <span class="text-success">Auto approved</span></p>
                <? } else {
                    if ($transfer['approver']) { ?>
                        <p>Status: <span
                                    class="text-weight-bold text-success">Approved by <?= $transfer['approver'] ?>, <?= fDate($transfer['doa']) ?></span>
                        </p>
                    <? } else { ?>
                        <p>Status: <span class="text-muted">Not approved</span></p>
                    <? }
                } ?>
                <h5 class="mt-lg text-weight-bold">Products</h5>
                <table class="table table-hover mb-none table-bordered" style="font-size:10pt;">
                    <thead>
                    <tr>
                        <th style="width: 60px">#</th>
                        <th style="width: 10%">Barcode</th>
                        <th style="width: 50%">Description</th>
                        <th>Qty</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($transfer['products'] as $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['barcode_office']?:$R['barcode_manufacture'] ?></td>
                            <td>
                                <?= $R['productname'] ?>
                                <i class="d-block mt-xs text-muted"><?= $R['productdescription'] ?></i>
                            </td>
                            <td><?= $R['qty'] ?></td>
                        </tr>
                        <? if ($R['track_expire_date']) { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="row d-flex justify-content-end">
                                        <div class="col-md-4">
                                            <h6>Batches:</h6>
                                            <div style="max-height: 200px;overflow-y: auto;padding:2px;">
                                                <table class="table table-bordered batches" style="font-size: 8pt">
                                                    <thead>
                                                    <tr>
                                                        <th>Batch no</th>
                                                        <th>Qty</th>
                                                        <th>Expire date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($R['batches'] as $b) { ?>
                                                        <tr>
                                                            <td><?= $b['batch_no'] ?></td>
                                                            <td><?= $b['qty'] ?></td>
                                                            <td><?= $b['expire_date'] ?></td>
                                                        </tr>
                                                    <? } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <? } ?>
                        <? if ($R['trackserialno']) { ?>
                            <tr>
                                <td colspan="4">
                                    <div class="row d-flex justify-content-end">
                                        <div class="col-md-4">
                                            <h6>Serial Nos:</h6>
                                            <div style="max-height: 200px;overflow-y: auto;padding:2px;">
                                                <table class="table table-bordered batches" style="font-size: 8pt">
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
                                    </div>
                                </td>
                            </tr>
                        <? } ?>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
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

    function calProductPrice() {
        let transfer_cost = parseFloat($('#transfer-cost').val()) || 0;
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
        increase_ratio = transfer_cost / total_product_value;
        console.log('transfer_cost: ', transfer_cost, 'total_value: ', total_product_value, 'increase_ratio: ', increase_ratio);

        $('.increment_percent').text(`${(increase_ratio * 100).toFixed(5)}%`);

        $('tr.product').each(function (i, tr) {
            let from_costprice = parseFloat($(tr).find('.from_costprice').val()) || 0;
            let from_quicksale_price = parseFloat($(tr).find('.from_quicksale_price').val()) || 0;
            let current_to_costprice = parseFloat($(tr).find('.to_costprice').data('current')) || 0;
            let current_to_quicksale_price = parseFloat($(tr).find('.to_quicksale_price').data('current')) || 0;

            let new_to_costprice = from_costprice * (1 + increase_ratio);
            let new_to_quicksale_price = from_quicksale_price * (1 + increase_ratio);
            console.log("costprice: ", new_to_costprice, 'quick: ', new_to_quicksale_price);
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
