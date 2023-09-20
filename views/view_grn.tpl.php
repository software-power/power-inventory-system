<style>
    th.stick {
        position: sticky;
        top: 100px; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }
</style>
<header class="page-header">
    <h2>View GRN</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-lg-10 col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>GRN No: <span class="text-primary receiptno" style="font-weight: bold"><?= $grn['grnnumber'] ?></span></h4>
                </div>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div>Supplier: <span class="text-primary"><?= $grn['suppliername'] ?></span></div>
                        <div>Supplier Invoice: <span class="text-primary"><?= $grn['invoiceno'] ?></span></div>
                        <div>Verification code: <span class="text-primary"><?= $grn['verificationcode'] ?></span></div>
                        <div>Lpo no: <span class="text-primary"><?= $grn['lponumber'] ?: 'n/a' ?></span></div>
                        <div>Location: <span class="text-primary"><?= $grn['locationname'] ?> - <?= $grn['branchname'] ?></span></div>
                        <div>Approval:
                            <? if ($grn['approver']) { ?>
                                <? if ($grn['auto_approve']) { ?>
                                    <span class="text-primary">Auto approved</span>
                                <? } else { ?>
                                    <span class="text-primary"><?= $grn['approver'] ?>, <?= fDate($grn['approval_date'], 'd M Y H:i') ?></span>
                                <? } ?>
                            <? } else { ?>
                                <span>not approved</span>
                            <? } ?>
                        </div>
                        <div>VAT registered: <span class="text-primary"><?= $grn['vat_registered'] ? 'Yes' : 'No' ?></span></div>
                        <div>VAT Description:</div>
                        <div class="row">
                            <div class="col-md-8">
                                <textarea readonly class="form-control text-sm" rows="2"><?= $grn['vat_desc'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>Date: <span class="text-primary"><?= fDate($grn['issuedate'], 'd F Y H:i') ?></span></div>
                        <div>Issued By: <span class="text-primary"><?= $grn['issuedby'] ?></span></div>
                        <div>Payment Type: <span class="text-primary text-uppercase"><?= ucfirst($grn['paymenttype']) ?></span></div>
                        <div>Currency: <span class="text-primary"><?= $grn['currency_name'] ?></span></div>
                        <div>Exchange Rate: <span class="text-primary"><?= $grn['currency_rateid'] ?></span></div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 60vh">
                    <table class="table table-bordered mt-xl" style="font-size: 9pt">
                        <thead>
                        <tr>
                            <th class="stick" style="top: 0; ">#</th>
                            <th class="stick" style="top: 0; ">Barcode</th>
                            <th class="stick" style="top: 0; ">Product</th>
                            <th class="stick" style="top: 0; ">Qty</th>
                            <th class="stick" style="top: 0; ">Billable Qty</th>
                            <th class="stick" style="top: 0; " align="right">Rate</th>
                            <th class="stick" style="top: 0; " align="right">VAT%</th>
                            <th class="stick" style="top: 0; " align="right">Quick Sale Price</th>
                            <th class="stick" style="top: 0; " align="right">Purchase Cost</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($grn['details'] as $R) { ?>
                            <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['barcode_office'] ?: $R['barcode_manufacture'] ?></td>
                            <td><?= $R['productname'] ?></td>
                            <td><?= $R['qty'] ?></td>
                            <td><?= $R['billable_qty'] ?></td>
                            <td><?= formatN($R['rate']) ?></td>
                            <td><?= $R['vat_percentage'] ?></td>
                            <td><?= $R['quick_sale_price'] ? formatN($R['quick_sale_price']) : 'n/a' ?></td>
                            <td><?= formatN($R['incamount']) ?></td>
                            </tr><? if ($R['track_expire_date']) { ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="row d-flex justify-content-end">
                                            <div class="col-md-5">
                                                <p>Batches</p>
                                                <table class="table table-bordered table-condensed" style="font-size: 8pt">
                                                    <thead>
                                                    <tr>
                                                        <th>Batch No</th>
                                                        <th>Qty</th>
                                                        <th>Expire Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <? foreach ($R['batches'] as $i => $b) { ?>
                                                        <tr>
                                                            <td><?= $b['batch_no'] ?></td>
                                                            <td><?= $b['qty'] ?></td>
                                                            <td><?= fDate($b['expire_date']) ?></td>
                                                        </tr>
                                                    <? } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <? } ?>
                            <tr>
                                <td colspan="8"></td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-5 col-md-offset-7">
                        <div class="d-flex justify-content-between">
                            <span>Exclusive Amount</span>
                            <span class="text-weight-bold excamount"><?= formatN($grn['total_amount']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>VAT Amount</span>
                            <span class="text-weight-bold vatamount"><?= formatN($grn['grand_vatamount']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Adjustment Amount</span>
                            <span class="text-weight-bold incamount"><?= formatN($grn['adjustment_amount']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Inclusive Amount</span>
                            <span class="text-weight-bold incamount"><?= formatN($grn['full_amount']) ?></span>
                        </div>
                    </div>
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
