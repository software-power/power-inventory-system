<div class="modal fade" id="resend-efd-modal" tabindex="-1" role="dialog" aria-labelledby="resend-efd-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Resend EFD Receipt</h4>
                <h5>Invoice No: <span class="text-primary invoiceno"></span></h5>
            </div>
            <form action="<?=url('sales','failed_fiscalization')?>" method="post" onsubmit="show_spinner(this)">
                <input type="hidden" name="id" class="salesid">
                <div class="modal-body">
                    <div class="d-none">
                        <span>Send as:</span>
                        <div class="ml-md d-flex flex-column">
                            <label>
                                <input type="radio" name="state" value="duplicate" >
                                <span>Duplicate receipt</span>
                            </label>
                            <label>
                                <input type="radio" name="state" value="new" checked>
                                <span>New receipt</span>
                            </label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="mr-md d-flex align-items-center">
                            <div class="submit-spinner" style="display: none">
                                <div class="d-flex align-items-center mr-md">
                                    <span class="spinner-border  spinner-border-sm mr-xs" style="height: 15px;width: 15px;"></span>
                                    <span class="text-sm">please wait...</span>
                                </div>
                            </div>
                            <button class="btn btn-success btn-sm submit-btn">Confirm</button>
                        </div>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let resend_efd_modal = $('#resend-efd-modal');

    $(resend_efd_modal).on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let salesid = source.data('salesid');
        let modal = $(this);
        $(modal).find('.salesid').val('').val(source.data('salesid'));
        $(modal).find('.invoiceno').text('').text(source.data('invoiceno'));
    });
    function show_spinner(form) {
        $(form).find('.submit-spinner').show();
        $(form).find('.submit-btn').prop('disabled',true);
    }
</script>