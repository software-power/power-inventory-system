<div class="modal fade" id="generate-ledger-modal" role="dialog" aria-labelledby="generate-ledger-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="generate_ledger_report">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Generate Ledger Account</h4>
                </div>
                <div class="modal-body">
                    <input type="checkbox" name="sr" class="sr" style="display: none">
                    <div class="row">
                        <div class="col-md-6">
                            Client:
                            <input type="hidden" name="clientid" class="clientid">
                            <input type="text" readonly class="form-control clientname">
                        </div>
                        <div class="col-md-6">
                            Currency:
                            <input type="hidden" name="currencyid" class="currencyid" value="">
                            <input type="text" readonly class="form-control currencyname" value="">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-6">
                            From:
                            <input type="date" name="fromdate" class="form-control fromdate">
                        </div>
                        <div class="col-md-6">
                            To:
                            <input type="date" name="todate" class="form-control todate">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#generate-ledger-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);

            let sr = source.data('sr') == 1;
            let currencyid = source.data('currencyid') ?? '';
            let currencyname = source.data('currencyname') ?? '';

            //reset fields
            let today = `<?=TODAY?>`;
            $(modal).find('.clientid,.clientname').val('');
            $(modal).find('.fromdate').val(today);
            $(modal).find('.todate').val('');

            $(modal).find('.sr').prop('checked', sr);
            $(modal).find('.clientid').val(source.data('clientid'));
            $(modal).find('.clientname').val(source.data('clientname'));
            $(modal).find('.currencyid').val('').val(currencyid);
            $(modal).find('.currencyname').val('').val(currencyname);
        });
    });
</script>