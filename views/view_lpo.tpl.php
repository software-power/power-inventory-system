<header class="page-header">
    <h2>View LPO</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-lg-8 col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>LPO No: <span class="text-primary receiptno" style="font-weight: bold"><?= $lpo['lponumber'] ?></span></h4>
                </div>
            </header>
            <div class="panel-body">
                <input type="hidden" name="transferno" value="<?= $lpo['transferno'] ?>">
                <p>Location: <span class="text-primary"><?= $lpo['locationname'] ?> - <?= $lpo['branchname'] ?></span></p>
                <p>Supplier: <span class="text-primary"><?= $lpo['suppliername'] ?></span></p>
                <p>GRN no: <span class="text-primary"><?= $lpo['grnnumber'] ?></span></p>
                <p>Issued By: <span class="text-primary"><?= $lpo['issuedby'] ?></span></p>
                <p>Issued Date: <span class="text-primary"><?= fDate($lpo['issuedate'], 'd F Y H:i') ?></span></p>
                <p>Currency: <span class="text-primary"><?= $lpo['currency_name'] ?> - <?= $lpo['currency_description'] ?></span></p>
                <p>Exchange rate: <span class="text-primary"><?= $lpo['currency_amount'] ?></span></p>
                <? if ($lpo['status'] != 'active') { ?>
                    <p>Status: <span class="text-rosepink">Canceled</span></p>
                <? } elseif ($lpo['auto_approve']) { ?>
                    <p>Status: <span class="text-success">Auto approved</span></p>
                <? } else {
                    if ($lpo['approver']) { ?>
                        <p>Status: <span class="text-weight-bold text-success">Approved by <?= $lpo['approver'] ?>, <?= fDate($lpo['doa']) ?></span>
                        </p>
                    <? } else { ?>
                        <p>Status: <span class="text-muted">Not approved</span></p>
                    <? }
                } ?>
                <h5 class="mt-lg text-weight-bold">Products</h5>
                <table class="table table-hover mb-none table-bordered" style="font-size:10pt;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Rate (<?= $lpo['currency_name'] ?>)</th>
                        <th>VAT %</th>
                        <th>Total Cost</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($lpo['details'] as $R) { ?>
                        <tr class="product">
                            <td><?= $count ?></td>
                            <td><?= $R['productname'] ?></td>
                            <td><?= $R['qty'] ?></td>
                            <td><?= formatN($R['rate']) ?></td>
                            <td><?= $R['vat_rate'] ?>%</td>
                            <td><?= formatN($R['incamount']) ?></td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>

                <div class="totals d-flex justify-content-end mt-md">
                    <table>
                        <tbody>
                        <tr>
                            <td class="text-right text-weight-bold">Exclusive Amount</td>
                            <td><span class="text-weight-bold ml-sm"><?= $lpo['currency_name'] ?></span></td>
                            <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($lpo['total_amount']) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right text-weight-bold">VAT Amount</td>
                            <td><span class="text-weight-bold ml-sm"><?= $lpo['currency_name'] ?></span></td>
                            <td class="text-right"><span
                                        class="text-weight-bold ml-md"><?= formatN($lpo['grand_vatamount']) ?></span></td>
                        </tr>
                        <tr>
                            <td class="text-right text-weight-bold">Inclusive Amount</td>
                            <td><span class="text-weight-bold ml-sm"><?= $lpo['currency_name'] ?></span></td>
                            <td class="text-right"><span class="text-weight-bold ml-md"><?= formatN($lpo['full_amount']) ?></span>
                            </td>
                        </tr>
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
