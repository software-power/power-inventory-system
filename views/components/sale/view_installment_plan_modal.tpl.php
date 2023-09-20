<div class="modal fade" id="view-installment-plan-modal" role="dialog" aria-labelledby="view-installment-plan-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Installment Plans</h4>
            </div>
            <div class="modal-body">
                <p>Invoice No: <span class="text-primary text-weight-semibold invoiceno"></span></p>
                <p>Full amount: <span class="text-weight-semibold full_amount"></span></p>
                <p>Paid amount: <span class="text-success text-weight-semibold paid_amount"></span></p>
                <p>Pending amount: <span class="text-danger text-weight-semibold pending_amount"></span></p>
                <div style="max-height: 60vh;overflow-y: auto;">
                    <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Time</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Pending</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody class="tbody">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let view_installment_plan_modal = $('#view-installment-plan-modal');

    $(view_installment_plan_modal).on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let salesid = source.data('salesid');
        let modal = $(this);

        $(modal).find('.invoiceno,.full_amount,.paid_amount,.pending_amount').text('');
        $(modal).find('tbody.tbody').empty();
        if (!salesid) {
            triggerError('Invalid sales ID');
            return false;
        }

        $.get(`?module=sales&action=getInvoiceInstallmentPlans&format=json&salesid=${salesid}`, null, function (data) {
            let result = JSON.parse(data);
            console.log(result);
            if (result.status === 'success') {
                $(modal).find('.invoiceno').text(result.data.sale.receipt_no);
                $(modal).find('.full_amount').text(numberWithCommas(result.data.sale.full_amount));
                $(modal).find('.paid_amount').text(numberWithCommas(result.data.sale.paid_amount));
                $(modal).find('.pending_amount').text(numberWithCommas(result.data.sale.pending_amount));

                let count = 1;
                $.each(result.data.installments,function (i,item) {
                    let installment_status = 'text-danger';
                    if(item.status===`<?=PAYMENT_STATUS_COMPLETE?>`){
                        installment_status = 'text-success';
                    }else if(item.status===`<?=PAYMENT_STATUS_PARTIAL?>`){
                        installment_status = 'text-warning';
                    }
                    let row = `<tr>
                                   <td>${count}</td>
                                   <td>${item.time}</td>
                                   <td>${numberWithCommas(item.amount)}</td>
                                   <td>${numberWithCommas(item.paid)}</td>
                                   <td>${numberWithCommas(item.pending)}</td>
                                   <td class="${installment_status}">${item.status}</td>
                               </tr>`;
                    $(modal).find('tbody.tbody').append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || "Error found");
            }
        })
    });

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>