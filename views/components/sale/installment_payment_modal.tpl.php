<?
if (isset($detail['dist_plan'])) {
    $checked = $detail['dist_plan'];
} else {
    $checked = 'monthly';
}
?>

<div class="modal fade" id="installment-plan-modal" tabindex="-1" role="dialog" aria-labelledby="installment-plan-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-list-alt"></i> Installment Plan</h4>
            </div>
            <div class="modal-body">
                <p class="mb-md text-lg">Invoice amount: <span class="text-primary text-weight-bold invoice-amount"></span></p>
                <div class="d-flex align-items-center mt-md">
                    <div>Distribution Plan:</div>
                    <label class="d-flex align-items-center ml-md" style="cursor: pointer">
                        <input type="radio" name="dist_plan" value="monthly" <?= $checked == 'monthly' ? 'checked' : '' ?>
                               style="height: 18px;width: 18px;" onchange="distribute_amount()">
                        <span class="ml-xs">Monthly</span>
                    </label>
                    <label class="d-flex align-items-center" style="cursor: pointer;margin-left: 100px">
                        <input type="radio" name="dist_plan" value="custom" <?= $checked != 'monthly' ? 'checked' : '' ?>
                               style="height: 18px;width: 18px;" onchange="distribute_amount()">
                        <span class="ml-xs">Custom</span>
                    </label>
                </div>
                <div style="margin-left: 100px">
                    <label class="d-flex align-items-center ml-sm" style="cursor: pointer">
                        <input id="installment-init-month" type="checkbox" checked style="height: 18px;width: 18px;">
                        <span class="ml-xs">Start this Month</span>
                    </label>
                </div>
                <div class="row ml-xs mt-xl">
                    <div class="col-md-3 pl-xs d-flex align-items-center">
                        <span>No:</span>
                        <input id="installment_distribution_count" type="text" class="form-control input-sm ml-xs" placeholder="no of distribution"
                               value="<?= $detail['installments'] ? count($detail['installments']) : '' ?>"
                               oninput="$('#installment_distribution_amount').val('')">
                    </div>
                    <div class="col-md-6 pl-xs d-flex align-items-center">
                        <span>Amount:</span>
                        <input id="installment_distribution_amount" type="text" class="form-control input-sm ml-xs" placeholder="distribute amount"
                               oninput="$('#installment_distribution_count').val('')">
                    </div>
                    <div class="col-md-2 pl-xs">
                        <button type="button" class="btn btn-success btn-sm" onclick="distribute_amount()">Distribute</button>
                    </div>
                </div>
                <table class="table table-bordered table-condensed mt-xlg" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <th>Time</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="tbody">
                    <? foreach ($detail['installments'] as $i) { ?>
                        <tr>
                            <td>
                                <input type="<?= $detail['dist_plan'] === 'monthly' ? 'month' : 'date' ?>" name="installment_plans[time][]"
                                       class="form-control input-sm installment_date" min="<?= TODAY ?>"
                                       value="<?= $detail['dist_plan'] === 'monthly' ? fDate($i['time'], 'Y-m') : fDate($i['time'], 'Y-m-d') ?>"
                                       onfocus="check_min_date(this)">
                            </td>
                            <td>
                                <input type="text" name="installment_plans[amount][]" class="form-control input-sm installment_amount"
                                       value="<?= formatN($i['amount']) ?>"
                                       placeholder="amount" oninput="total_plan_amount()">
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning" title="remove" onclick="remove_plan(this)"><i
                                            class="fa fa-close"></i></button>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
                <div class="mt-md">
                    <p class="text-lg">Total installment amount: <span class="text-success total_plan_amount">0</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"> Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let installment_plan_modal = $('#installment-plan-modal');

    $(function () {
        total_plan_amount();
    });

    $(installment_plan_modal).on('show.bs.modal', function (e) {
        thousands_separator('#installment_distribution_amount,.installment_amount');
        qtyInput('#installment_distribution_count');
    });

    function add_plan(amount, time, plan) {
        let min_date = `<?=TODAY?>`;
        let row = `<tr>
                        <td>
                            <input type="${plan === 'monthly' ? 'month' : 'date'}" name="installment_plans[time][]" class="form-control input-sm installment_date"
                                    min="${min_date}" value="${time}" onfocus="check_min_date(this)">
                        </td>
                        <td>
                            <input type="text" name="installment_plans[amount][]" class="form-control input-sm installment_amount" value="${amount}"
                                   placeholder="amount" oninput="total_plan_amount()">
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning" title="remove" onclick="remove_plan(this)"><i
                                        class="fa fa-close"></i></button>
                        </td>
                    </tr>`;
        $(installment_plan_modal).find('tbody.tbody').append(row);
    }

    function check_min_date(obj) {
        let prev_date = $(obj).closest('tr').prev('tr').find('.installment_date').val();
        console.log('prev date: ', prev_date);
        let plan = $(`[name="dist_plan"]:checked`).val();
        let min_date = `<?=TODAY?>`;
        if (prev_date) {
            min_date = plan === 'monthly' ? `${prev_date}-01` : prev_date;
            if (plan === 'monthly') {
                let date = new Date(min_date);
                date.setMonth(date.getMonth() + 1);
                min_date = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}`;
            } else {
                let date = new Date(min_date);
                date.setDate(date.getDate() + 1);
                min_date = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${(date.getDate()).toString().padStart(2, '0')}`;
            }
        }

        $(obj).attr('min', min_date);
    }

    function remove_plan(obj) {
        $(obj).closest('tr').remove();
        total_plan_amount();
    }

    function distribute_amount() {
        $(installment_plan_modal).find('tbody.tbody').empty();
        let invoice_amount = removeCommas($('.invoice-amount').text());
        let start_this_month = $('#installment-init-month').is(':checked');
        let plan = $(`[name="dist_plan"]:checked`).val();

        let dist_count = parseInt($('#installment_distribution_count').val()) || 0;
        let dist_amt = removeCommas($('#installment_distribution_amount').val()) || 0;

        if (dist_count <= 0 && dist_amt <= 0) {
            triggerError('Enter valid distribution values');

            $('#installment_distribution_count,#installment_distribution_amount').addClass('border-danger');
            setTimeout(function () {
                $('#installment_distribution_count,#installment_distribution_amount').removeClass('border-danger');

            }, 1000);
            return;
        }
        // console.log(invoice_amount, this_month, plan, dist_count, dist_amt);

        let distribution = [];
        let remain_amt = invoice_amount;
        let remainder = 0;
        if (dist_count > 0) { //source count
            dist_amt = Math.floor(remain_amt / dist_count);
            remainder = remain_amt % dist_count;
            // console.log(dist_amt, remainder);
        }

        //distribution
        while (remain_amt > 0) {
            if (dist_amt >= remain_amt) {//single dist amt covers whole qty
                distribution.push(remain_amt);
                remain_amt = 0;
            } else {
                distribution.push(dist_amt);
                remain_amt -= dist_amt;
            }
            if (remain_amt == remainder) remain_amt = 0; //end loop
        }
        // console.log(distribution);
        if (remainder > 0) distribution[distribution.length - 1] += remainder; //adding remainder in last distribution
        // console.log(distribution);

        $.each(distribution, function (i, amt) {
            let time = "";
            if (plan === 'monthly') {
                let date = new Date();
                date.setMonth(date.getMonth() + (start_this_month ? i : i + 1));
                time = `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}`;
                // console.log(time);
            }
            add_plan(amt, time, plan);
        });
        thousands_separator('.installment_amount');
        total_plan_amount();

    }

    function total_plan_amount() {
        let total = 0;
        $(installment_plan_modal).find('tbody.tbody .installment_amount').each(function (i, item) {
            total += removeCommas($(item).val()) || 0;
        });

        $(installment_plan_modal).find('.total_plan_amount').text(numberWithCommas(total));
    }
</script>