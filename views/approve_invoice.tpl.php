<style>
    th.stick {
        position: sticky;
        top: 0; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }

    .non-stock .non-stock-label {
        display: block !important;
        position: absolute;
        top: -5px;
        left: -12px;
        z-index: 4;
        transform: rotateZ(336deg);
    }

    #spinnerHolder {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100vh;
        width: 100%;
        display: none;
        background-color: black;
        opacity: 0.5;
        z-index: 10000;
    }

</style>


<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>
<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Invoice No: <span class="text-primary"><?= $invoice['receipt_no'] ?></span></h2>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-lg">
                            <div class="m-xs d-flex align-items-center">
                                <p class="m-none">Client Name: <span class="text-primary"><?= $invoice['clientname'] ?></span></p>
                                <div>
                                    <? if (count($contacts) > 0) { ?>
                                        <button type="button" class="btn btn-primary btn-xs ml-md" data-toggle="modal"
                                                data-target="#contacts-modal">
                                            view contacts
                                        </button>
                                    <? } else { ?>
                                        <div class="text-danger text-weight-semibold ml-md text-xl">No contact details</div>
                                    <? } ?>
                                </div>
                            </div>
                            <? if ($invoice['reseller']) { ?>
                                <p class="m-xs text-danger text-weight-semibold">Registered Reseller</p>
                            <? } ?>
                            <p class="m-xs">TIN: <span class="text-primary"><?= $invoice['clientino'] ?></span></p>
                            <p class="m-xs">Email: <span class="text-primary"><?= $invoice['clientemail'] ?></span></p>
                            <p class="m-xs">Address: <span class="text-primary"><?= $invoice['clientaddress'] ?></span></p>
                        </div>
                        <p class="m-xs text-md">Payment Type: <span
                                    class="text-primary text-capitalize text-weight-bold"><?= $invoice['paymenttype'] ?></span></p>
                        <p class="m-xs">Vat Exempted: <span
                                    class="<?= $invoice['vat_exempted'] ? 'text-lg text-danger text-weight-bold' : 'text-primary' ?>"><?= $invoice['vat_exempted'] ? 'Yes' : 'No' ?></span>
                        </p>
                        <p class="m-xs">Currency: <span
                                    class="text-primary"><?= $invoice['currencyname'] ?> - <?= $invoice['currency_description'] ?></span>
                        </p>
                        <p class="m-xs">Full Amount: <span class="text-primary"><?= formatN($invoice['full_amount']) ?></span></p>
                        <p class="m-xs">Exc Amount: <span class="text-primary"><?= formatN($invoice['grand_amount']) ?></span></p>
                        <p class="m-xs">Vat Amount: <span class="text-primary"><?= formatN($invoice['grand_vatamount']) ?></span></p>
                        <p class="m-xs">Expense Amount: <span class="text-danger"><?= formatN($invoice['expense_amount']) ?></span></p>
                        <p class="m-xs">Total Cost Amount: <span id="total-cost"
                                                                 class="text-danger"><?= formatN($invoice['total_costamount']) ?></span></p>
                        <? if ($invoice['has_combine']) { ?>
                            <p class="m-xs text-md">Has Combination: <span class="text-danger text-weight-bold">Yes</span></p>
                        <? } ?>
                        <!--                        --><? // if ($client['credit_limit'] > 0) { ?>
                        <!--                            <p class="mt-md text-md">Credit Limit: --><? //= $baseCurrency['name'] ?><!-- -->
                        <? //= formatN($client['credit_limit']) ?><!--</p>-->
                        <!--                            --><? // if ($pending_after_approve > $client['credit_limit']) { ?>
                        <!--                                <p class="mt-md text-lg text-danger text-weight-bold">Exceed Credit Limit</p>-->
                        <!--                            --><? // } ?>
                        <!--                        --><? // } ?>
                        <p class="m-xs mt-xl text-md text-weight-bold">Invoice Profit Margin: <span
                                    id="profit-margin"><?= $invoice['total_margin'] ?></span>%</p>
                    </div>
                    <div class="col-md-4">
                        <p class="m-xs">Sales No: <span class="text-primary"><?= $invoice['salesid'] ?></span></p>
                        <p class="m-xs">Sales Type: <span
                                    class="text-primary text-capitalize"><?= $invoice['source'] == Sales::SOURCE_DETAILED ? 'Normal' : 'Quick' ?> Sale</span>
                        </p>
                        <p class="m-xs">Issue Date: <span class="text-primary"><?= fDate($invoice['doc'], 'd M Y H:i') ?></span></p>
                        <p class="m-xs">Sales person: <span class="client_tin text-primary"><?= $invoice['sales_person'] ?></span></p>
                        <? if ($invoice['paymenttype'] == PAYMENT_TYPE_CREDIT) { ?>
                            <p class="m-xs">Approval Status:
                                <? if ($invoice['approver']) { ?>
                                    <span class="text-primary">Approved by <?= $invoice['approver'] ?>, <?= fDate($invoice['approvedate'], 'd M Y H:i') ?></span>
                                <? } else { ?>
                                    <span class="text-muted">Not approved</span>
                                <? } ?>
                            </p>
                        <? } ?>
                        <p class="m-xs">Order No:
                            <? if ($invoice['orderno']) { ?>
                                <a class="text-primary" href="<?= url('orders', 'order_list', ['ordernumber' => $invoice['orderno']]) ?>">
                                    <?= $invoice['orderno'] ?>, <?= $invoice['order_creator'] ?></a>
                            <? } else { ?>
                                <span class="text-muted">N/A</span>
                            <? } ?>

                        </p>
                        <p class="m-xs">Proforma No: <span
                                    class="client_tin text-primary"><?= $invoice['proformaid'] ? $invoice['proformaid'] : 'N/A' ?></span>
                        </p>
                        <p class="m-xs">Invoice Remarks:</p>
                        <div class="col-md-12">
                            <textarea class="form-control text-sm" readonly rows="3"><?= $invoice['remarks'] ?></textarea>
                        </div>
                        <p class="m-xs">Internal Remarks:</p>
                        <div class="col-md-12">
                            <textarea class="form-control text-sm" readonly rows="3"><?= $invoice['internal_remarks'] ?></textarea>
                        </div>
                    </div>
                    <? if ($invoice['paymenttype'] == PAYMENT_TYPE_CREDIT) { ?>
                        <div class="col-md-4">
                            <h5 class="text-weight-bold">Advance Receipt Balance:</h5>
                            <table class="table table-bordered" style="font-size: 9pt;">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Currency</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($advanceBalances as $index => $advanceBalance) { ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $advanceBalance['currencyname'] ?></td>
                                        <td class="text-success"><?= formatN($advanceBalance['remaining_advance']) ?></td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    <? } ?>
                </div>
                <div class="d-flex justify-content-end">
                    <? if (!$invoice['has_combine']) { ?>
                        <a href="<?= $invoice['source'] == Sales::SOURCE_DETAILED
                            ? url('sales', 'add_sales_new', ['id' => $invoice['salesid']])
                            : url('pos', 'quick_sales', ['salesid' => $invoice['salesid']]) ?>"
                           class="btn btn-warning btn-sm ml-xs editBtn"><i class="fa fa-edit"></i> Edit</a>
                    <? } ?>
                    <form action="<?= url('sales', 'cancel_sale') ?>" method="POST" style="margin:0;"
                          class="cancel-sale-form d-flex align-items-center" title="cancel sale"
                          onsubmit="return confirm('Do you want to cancel this sale?')">
                        <input type="hidden" class="salesno" name="salesno" value="<?= $invoice['salesid'] ?>">
                        <button class="btn btn-danger btn-sm ml-xs">Cancel Invoice</button>
                    </form>
                </div>
                <form action="<?= url('payments', 'confirm_invoice_approval') ?>" method="post" onsubmit="return validateInputs()">
                    <input type="hidden" name="salesid" value="<?= $invoice['salesid'] ?>">
                    <div class="row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end align-items-center mt-md mb-sm">
                                <? if ($invoice['has_installment']) { ?>
                                    <input class="installment" type="hidden" value="1">
                                    <button type="button" class="btn btn-primary btn-sm ml-md installment-btn" style="flex-shrink: 0"
                                            onclick="show_installment_plan_modal()"><i class="fa fa-list-alt"></i>
                                        Installment Plan
                                    </button>
                                <? } ?>
                            </div>
                            <div class="d-flex justify-content-end align-items-center mt-md mb-sm">
                                <? if ($invoice['paymenttype'] == PAYMENT_TYPE_CASH) { ?>
                                    <button type="button" class="btn btn-success ml-md approveBtn" data-toggle="modal"
                                            data-target="#payment-modal"
                                            style="flex-shrink: 0"><i class="fa fa-check"></i> Approve
                                    </button>
                                <? } else { ?>
                                    <input id="paymenttype" type="hidden" value="<?= PAYMENT_TYPE_CREDIT ?>">
                                    <? if (in_array($invoice['currencyid'], array_column($advanceBalances, 'currencyid'))) { ?>
                                        <div class="checkbox offset_advance text-success mr-md" style="flex-shrink: 0;">
                                            <label title="Offset advance paid amount">
                                                <input type="checkbox" name="offset_advance">
                                                Offset Advance Receipt
                                            </label>
                                        </div>
                                    <? } ?>
                                    <span class="mr-xs text-weight-bold" style="flex-shrink: 0">Print Size:</span>
                                    <select name="print_size" class="form-control input-sm" style="width: 20%">
                                        <? foreach ($print_sizes as $size) { ?>
                                            <option <?= selected($invoice['print_size'], $size) ?>
                                                    value="<?= $size ?>"><?= ucfirst($size) ?></option>
                                        <? } ?>
                                    </select>
                                    <button class="btn btn-success ml-md approveBtn" style="flex-shrink: 0"><i class="fa fa-check"></i>
                                        Approve
                                    </button>
                                <? } ?>
                            </div>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center mt-md mb-sm">
                        <div class="col-md-3">
                            <label>Total VAT</label>
                            <input type="text" readonly name="sale[grand_vatamount]" class="form-control total_vatamount"
                                   placeholder="total VAT"
                                   value="<?= $invoice['grand_vatamount'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label>Total Exclusive</label>
                            <input type="text" readonly name="sale[grand_amount]" class="form-control total_excamount"
                                   placeholder="total Exc"
                                   value="<?= $invoice['grand_amount'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label>Total Inclusive</label>
                            <input type="text" readonly name="sale[full_amount]" class="form-control total_incamount"
                                   placeholder="total Inc"
                                   value="<?= $invoice['full_amount'] ?>" required>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 70vh;overflow-y: auto">
                        <table class="table table-hover table-bordered" style="font-size:10pt;">
                            <thead>
                            <tr>
                                <th class="stick">#</th>
                                <th class="stick" style="width: 25%">Product Name</th>
                                <th class="stick">Print</th>
                                <th class="stick">Quantity</th>
                                <th class="stick">VAT <span
                                            class="text-weight-bold text-danger"><?= $invoice['vat_exempted'] ? 'Exempted' : '' ?></span>
                                </th>
                                <th class="stick">Exc Price</th>
                                <th class="stick">Disc%</th>
                                <th class="stick">Disc Exc</th>
                                <th class="stick">Inc Price</th>
                                <th class="stick">VAT Amount</th>
                                <th class="stick">Total Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? $count = 1;
                            foreach ($invoice['products'] as $index => $R) { ?>
                                <tr class="<?= $R['show_print'] ? 'group' : '' ?> <?= $R['sold_non_stock'] ? 'non-stock' : '' ?>">
                                    <td>
                                        <?= $count ?>
                                    </td>
                                    <td style="position: relative">
                                        <p><?= $R['productname'] ?></p>
                                        <textarea readonly
                                                  class="form-control"><?= $R['extra_description'] ?: $R['product_description'] ?></textarea>
                                        <small class="text-danger non-stock-label" style="display: none">non-stock</small>
                                    </td>
                                    <td><?= $R['show_print'] ? 'Yes' : 'No' ?></td>
                                    <td>
                                        <?= $R['quantity'] ?>
                                        <input type="hidden" class="qty" value="<?= $R['quantity'] ?>">
                                    </td>
                                    <td>
                                        <?= $R['vat_rate'] ?>
                                        <input type="hidden" class="vat_rate" value="<?= $R['vat_rate'] ?>">
                                    </td>
                                    <td>
                                        <? if ($R['show_print']) { ?>
                                            <input autocomplete="off" type="text" oninput="calProductAmount(this)"
                                                   name="item[<?= $R['sdi'] ?>][price]" required
                                                   data-source="price" placeholder="Price" class="form-control inputs price" step="0.01"
                                                   title="exclusive price" value="<?= $R['price'] ?>">
                                        <? } else { ?>
                                            <?= formatN($R['price']) ?>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <? if ($R['show_print']) { ?>
                                            <input autocomplete="off" type="number" oninput="calProductAmount(this)" required
                                                   data-source="discount_percent" placeholder="discount percent"
                                                   class="form-control inputs discount_percent" step="0.0001" max="100"
                                                   title="discount percent" value="<?= $R['discountpercent'] ?>">
                                        <? } else { ?>
                                            <?= formatN($R['price']) ?>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <? if ($R['show_print']) { ?>
                                            <input autocomplete="off" type="text" oninput="calProductAmount(this)"
                                                   name="item[<?= $R['sdi'] ?>][discount]" required
                                                   data-source="discount" placeholder="Price" class="form-control inputs discount"
                                                   title="inclusive price"
                                                   value="<?= $R['discount'] ?>">
                                        <? } else { ?>
                                            <?= formatN($R['price']) ?>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <? if ($R['show_print']) { ?>
                                            <input autocomplete="off" type="text" oninput="calProductAmount(this)"
                                                   name="item[<?= $R['sdi'] ?>][incprice]" required
                                                   data-source="incprice" placeholder="Price" class="form-control inputs incprice"
                                                   step="0.01"
                                                   title="inclusive price" value="<?= $R['incprice'] ?>">
                                            <input type="hidden" name="item[<?= $R['sdi'] ?>][sinc]" class="sinc" value="<?= $R['sinc'] ?>">
                                        <? } else { ?>
                                            <?= formatN($R['incprice']) ?>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <? if ($R['show_print']) { ?>
                                            <input type="text" readonly class="form-control vatamount" value="<?= $R['vat_amount'] ?>">
                                        <? } else { ?>
                                            <?= formatN($R['vat_amount']) ?>
                                        <? } ?>
                                    </td>
                                    <td>
                                        <? if ($R['show_print']) { ?>
                                            <input type="text" readonly class="form-control incamount" value="<?= $R['total_amount'] ?>">
                                            <input type="hidden" class="excamount" value="<?= $R['amount'] ?>">
                                        <? } else { ?>
                                            <?= formatN($R['total_amount']) ?>
                                        <? } ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10"></td>
                                </tr>
                                <? $count++;
                            } ?>
                            </tbody>
                        </table>
                    </div>

                    <!--    payment modal-->
                    <div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="cashPaymentModal"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-center modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title"><i class="fa fa-money"></i> Payment</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between ml-md mr-md">
                                            <h2>TOTAL</h2>
                                            <h2 style="font-weight: bold;">
                                                <span><?= $invoice['currencyname'] ?></span> <span
                                                        id="modalTotalAmount"><?= formatN($invoice['full_amount']) ?></span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <h4>Print Size:</h4>
                                            <select class="form-control input-sm" name="sale[print_size]">
                                                <? foreach ($print_sizes as $size) { ?>
                                                    <option <?= selected($invoice['print_size'], $size) ?>
                                                            value="<?= $size ?>"><?= ucfirst($size) ?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label for="">Received Amount</label>
                                            <input type="text" class="form-control receivedCash" name="receivedCash" min="0"
                                                   step="0.01" oninput="receiveAmount(this)">
                                            <p id="changeHolder" style="display: none">Change: <span id="changeAmount"
                                                                                                     class="text-danger"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="">Payment Method</label>
                                            <select id="paymentMethod" class="form-control" name="payment_method"
                                                    onchange="checkPaymentMethod(this)">
                                                <option selected value="<?= PaymentMethods::CASH ?>">Cash</option>
                                                <option value="<?= PaymentMethods::CREDIT_CARD ?>">Credit Card</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="creditCardHolder" class="form-group" style="display: none">
                                        <div class="col-md-6">
                                            <label for="">Electronic Account</label>
                                            <select id="electronic-account" class="form-control" name="electronic_account">
                                                <option value="">-- choose account --</option>
                                                <? foreach ($electronic_accounts as $acc) { ?>
                                                    <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="">Reference</label>
                                            <input id="credit_card_no" type="text" name="credit_card_no" class="form-control"
                                                   placeholder="reference number">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6 text-center">
                                            <button type="button" class="btn btn-primary btn-block" data-dismiss="modal">Cancel</button>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <button type="submit" class="btn btn-success btn-block confirmCashBtn"
                                                    style="display: none;">Confirm
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--    installment plan modal-->
                    <?= component('sale/installment_payment_modal.tpl.php', ['detail' => ['dist_plan' => $invoice['dist_plan'], 'installments' => $invoice['installments']]]) ?>

                </form>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="contacts-modal" tabindex="-1" role="dialog" aria-labelledby="contacts-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Client Contacts</h4>
            </div>
            <div class="modal-body">
                <table class="table" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Person</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Position</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($contacts as $c) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $c['name'] ?></td>
                            <td><?= $c['email'] ?></td>
                            <td><?= $c['mobile'] ?></td>
                            <td><?= $c['position'] ?></td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function () {
        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
        check_margin();
        format_inputs();
        thousands_separator('.total_vatamount, .total_excamount, .total_incamount, .receivedCash');
    });

    function validateInputs(e) {
        // return false;
        let valid = true;
        //check installment
        if ($('#paymenttype').val() === "<?=PAYMENT_TYPE_CREDIT?>" && $('.installment').length > 0) {
            $(installment_plan_modal).find('tbody.tbody tr .border-danger').removeClass('border-danger');
            $(installment_plan_modal).find('tbody.tbody tr').each(function (i, tr) {
                if (!$(tr).find('.installment_date').val()) {
                    triggerError("Invalid installment plan inputs!", 5000);
                    valid = false;
                    show_installment_plan_modal();
                    $(tr).find('.installment_date').addClass('border-danger');
                    return false;
                }
                if (!$(tr).find('.installment_amount').val()) {
                    triggerError("Invalid installment plan inputs!", 5000);
                    valid = false;
                    show_installment_plan_modal();
                    $(tr).find('.installment_amount').addClass('border-danger');
                    return false;
                }
            });
            if (!valid) return false;

            let total_plan_amount = removeCommas($(installment_plan_modal).find('span.total_plan_amount').text()) || 0;
            let invoice_amount = removeCommas($('.total_incamount').val());
            $(installment_plan_modal).find('.invoice-amount').text(numberWithCommas(invoice_amount));
            if (total_plan_amount !== invoice_amount) {
                triggerError("Total installment plan amount does not match with total invoice amount!", 5000);
                $(installment_plan_modal).modal('show');
                return false;
            }
        }
        $('#spinnerHolder').show();
    }

    function format_inputs() {
        qtyInput('.qty');
        thousands_separator('.price, .incprice, .discount, .vatamount, .incamount');
    }

    let timer = null;

    function calProductAmount(obj) {
        let group = $(obj).closest('.group');
        let qty = parseInt($(group).find('.qty').val());
        let price = removeCommas($(group).find('.price').val());
        let vat_rate = removeCommas($(group).find('.vat_rate').val());
        let incprice = removeCommas($(group).find('.incprice').val());
        let discount = removeCommas($(group).find('.discount').val()) || 0;
        let discount_percent = removeCommas($(group).find('.discount_percent').val()) || 0;
        let sinc = $(group).find('.sinc').val() == '1';
        let source = $(obj).data('source');

        if (discount_percent > 0 || discount > 0) {
            $(group).find('.incprice').prop('readonly', true);
        } else {
            $(group).find('.incprice').prop('readonly', false);
        }

        if (source === 'price') {
            incprice = (price * (1 + vat_rate / 100)).toFixed(2);
            $(group).find('.incprice').val(incprice);
            $(group).find('.sinc').val('0');
            sinc = false;
        } else if (source === 'discount_percent') {
            if (discount_percent > 100) {
                triggerError('Max percent is 100');
                $(group).find('.discount_percent').val(0);
                calProductAmount(obj);
                return;
            }
            discount = (price * discount_percent / 100).toFixed(2);
            price = price - discount;
            incprice = (price * (1 + vat_rate / 100)).toFixed(2);
            $(group).find('.incprice').val(incprice);
            $(group).find('.discount').val(discount);
            $(group).find('.sinc').val('0');
            sinc = false;
        } else if (source === 'discount') {
            if (discount > price) {
                triggerError(`Max discount amount is ${price}`);
                $(group).find('.discount').val(0);
                calProductAmount(obj);
                return;
            }
            discount_percent = (discount * 100 / price).toFixed(2);
            price = price - discount;
            incprice = (price * (1 + vat_rate / 100)).toFixed(2);
            $(group).find('.incprice').val(incprice);
            $(group).find('.discount_percent').val(discount_percent);
            $(group).find('.sinc').val('0');
            sinc = false;
        } else if (source === 'incprice') {
            price = (incprice / (1 + vat_rate / 100)).toFixed(2);
            $(group).find('.price').val(price);
            $(group).find('.sinc').val(1);
            sinc = true;
        }
        let excamount = 0, vatamount = 0, incamount = 0;
        if (!sinc) {//price from exc
            excamount = (qty * price).toFixed(2);
            vatamount = (qty * price * (vat_rate / 100)).toFixed(2);
            // incamount2 = (qty * price * (1 + vat_rate / 100)).toFixed(2);

            incamount = (parseFloat(excamount) + parseFloat(vatamount)).toFixed(2);
            // console.log('exc: ',excamount,'vat: ',vatamount,'inc: ',incamount,'inc2: ',incamount2);
        } else {//price from inc
            incamount = qty * incprice;
            excamount = parseFloat((incamount / (1 + vat_rate / 100)).toFixed(2));
            vatamount = (incamount - excamount).toFixed(2);
            // console.log('inc: ',incamount,'exc: ',excamount,'vat: ',vatamount);
        }
        $(group).find('.excamount').val(excamount);
        $(group).find('.vatamount').val(vatamount);
        $(group).find('.incamount').val(incamount);
        format_inputs();
        calTotalAmount();
    }

    function calTotalAmount() {
        let total_vat = 0, total_exc = 0, total_inc = 0;
        $('.group').each(function (i, group) {
            let excamount = removeCommas($(group).find('.excamount').val());
            let vatamount = removeCommas($(group).find('.vatamount').val());
            let incamount = removeCommas($(group).find('.incamount').val());
            total_exc += excamount;
            total_vat += vatamount;
            total_inc += incamount;
        });

        $('.total_vatamount').val(total_vat.toFixed(2));
        $('.total_excamount').val(total_exc.toFixed(2));
        $('.total_incamount').val(total_inc.toFixed(2));
        $('#modalTotalAmount').text(numberWithCommas(total_inc));

        let total_cost = removeCommas($('#total-cost').text());
        let profit_margin = truncateDecimals((total_exc - total_cost) * 100 / total_cost, 2);
        $('#profit-margin').text(profit_margin);
        check_margin();

        thousands_separator('.total_vatamount, .total_excamount, .total_incamount');
    }

    function check_margin() {
        let profit_margin = removeCommas($('#profit-margin').text());
        if (profit_margin > 0) {
            $('#profit-margin').addClass('text-success').removeClass('text-danger');
        } else {
            $('#profit-margin').removeClass('text-success').addClass('text-danger');
        }
    }

    function receiveAmount(obj) {
        let receivedAmount = removeCommas($(obj).val());
        let fullAmount = removeCommas($('.total_incamount').val());
        // console.log(receivedAmount, fullAmount);

        if (receivedAmount >= fullAmount) {
            $('.confirmCashBtn').show('fast');
        } else {
            $('.confirmCashBtn').hide('fast');
        }

        if (receivedAmount > fullAmount) {
            $('#changeAmount').text((receivedAmount - fullAmount).toFixed(2));
            $('#changeHolder').show('fast');
        } else {
            $('#changeAmount').text(0);
            $('#changeHolder').hide('fast');
        }

    }

    function show_installment_plan_modal() {
        let invoice_amount = $('.total_incamount').val();
        $(installment_plan_modal).find('.invoice-amount').text(numberWithCommas(invoice_amount));
        $(installment_plan_modal).modal('show');
    }

    function checkPaymentMethod(obj) {
        let cardHolder = $('#creditCardHolder');
        let method = $(obj).val();
        if (method === '<?=PaymentMethods::CREDIT_CARD?>') {
            $(cardHolder).show('fast');
            $('#credit_card_no,#electronic-account').prop('required', true);
            $('#electronic-account').focus();
        } else {
            $(cardHolder).hide('fast');
            $('#credit_card_no,#electronic-account').val('').prop('required', false);
        }
    }

    $('#payment-modal').on('show.bs.modal', function () {
        $('#changeHolder').hide();
        $('.confirmCashBtn').hide();
        $('.receivedCash').val('');
    });

    function removeCommas(amount) {
        return parseFloat(amount.replace(/,/g, ''));
        //return parseFloat(amount.replace(",", ""));
    }

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
