<style>

    #spinnerHolder {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        height: 100vh;
        overflow: hidden;
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

<header class="page-header">
    <? if ($SR_MODE) { ?>
        <h2>Sales Receipt Report With SR</h2>
    <? } else { ?>
        <h2>Sales Receipt Report</h2>
    <? } ?>

</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="reports">
                <? if ($SR_MODE) { ?>
                    <input type="hidden" name="action" value="sales_payment_sr">
                <? } else { ?>
                    <input type="hidden" name="action" value="sales_payment">
                <? } ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select id="branchid" name="branchid" class="form-control">
                                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                    <option value="">-- All _branches --</option>
                                <? } ?>
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($b['id'], $_SESSION['member']['branchid']) ?>
                                            value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Client:
                            <select id="clientid" name="clientid" class="form-control"></select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Currency:
                            <select name="currencyid" class="form-control">
                                <option value="" selected>All</option>
                                <? foreach ($currencies as $c) { ?>
                                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?> - <?= $c['description'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Payment Method:
                            <select name="method" class="form-control">
                                <option value="" selected>All</option>
                                <option value="<?= PaymentMethods::CASH ?>"><?= PaymentMethods::CASH ?></option>
                                <option value="<?= PaymentMethods::BANK ?>"><?= PaymentMethods::BANK ?></option>
                                <option value="<?= PaymentMethods::CHEQUE ?>"><?= PaymentMethods::CHEQUE ?></option>
                                <option value="<?= PaymentMethods::CREDIT_CARD ?>"><?= PaymentMethods::CREDIT_CARD ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Electronic Account:
                            <select name="eaccount" class="form-control">
                                <option value="" selected>All</option>
                                <? foreach ($eaccounts as $acc) { ?>
                                    <option value="<?= $acc['id'] ?>"><?= $acc['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Payment Receiver:
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select name="payment_issuedby" class="form-control userid"></select>
                            <? } else { ?>
                                <input type="hidden" name="payment_issuedby" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mt-md">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-success">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <button type="button" class="btn" title="Home" data-toggle="modal"
                            data-target="#search-modal"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <? if ($SR_MODE) { ?>
                    <h2 class="panel-title">Sales Receipts Report With SR</h2>
                <? } else { ?>
                    <h2 class="panel-title">Sales Receipts Report</h2>
                <? } ?>
                <p class="text-primary mt-md"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Transaction No</th>
                            <th>Client</th>
                            <th>Payment Issuedby</th>
                            <th>Payment Date</th>
                            <th>Payment Method</th>
                            <th>Currency</th>
                            <th class="text-right">Offset Amount</th>
                            <th class="text-right">Received Amount</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $total = 0;
                        $USER_CAN_CANCEL = Users::can(OtherRights::cancel_receipt);
                        foreach ($payments as $index => $R) { ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <? if ($R['received_amount'] > 0) { ?>
                                        <a href="<?= url('payments', 'payment_receipt', ['id' => $R['id']]) ?>" target="_blank"
                                           title="view payment slip"><?= getTransNo($R['id']) ?></a>
                                    <? } else { ?>
                                        <span class="text-muted" title="payment offset from advance receipt"><?= getTransNo($R['id']) ?></span>
                                    <? } ?>
                                </td>
                                <td title="view client info">
                                    <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                                </td>
                                <td><?= $R['creator'] ?></td>
                                <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                                <td>
                                    <? if ($R['received_amount'] <= 0 && $R['advance_amount']) { ?>
                                        <span class="text-muted">Offset from Advance</span>
                                    <? } else { ?>
                                        <?= $R['method'] ?>
                                        <? if ($R['method'] == PaymentMethods::BANK) { ?>
                                            <? $text = "Bank Name: " . ($R['bankname'] ?: $R['bank_name']) . ", Reference: " . ($R['bankreference'] ?: $R['bank_accno']); ?>
                                            <i class="text-xs text-muted d-block" title="<?= $text ?>"><?= substr($text, 0, 20) ?>...</i>
                                        <? } ?>
                                        <? if ($R['method'] == PaymentMethods::CHEQUE) { ?>
                                            <? $text = "Bank: {$R['bank_name']},Cheque No: {$R['chequename']}, Cheque Type: {$R['chequetype']}"; ?>
                                            <i class="text-xs text-muted d-block" title="<?= $text ?>"><?= substr($text, 0, 20) ?>...</i>
                                        <? } ?>
                                        <? if ($R['method'] == PaymentMethods::CREDIT_CARD) { ?>
                                            <? $text = "Reference: {$R['electronic_account']} {$R['credit_cardno']}"; ?>
                                            <i class="text-xs text-muted d-block" title="<?= $text ?>"><?= substr($text, 0, 20) ?>...</i>
                                        <? } ?>
                                    <? } ?>
                                </td>
                                <td title="<?= $R['currency_description'] ?>"><?= $R['currencyname'] ?></td>
                                <td class="text-right"><?= $R['advance_amount'] > 0 ? formatN($R['advance_amount']) : '' ?></td>
                                <td class="text-right"><?= $R['received_amount'] > 0 ? formatN($R['received_amount']) : '' ?></td>
                                <td>
                                    <? if ($USER_CAN_CANCEL && $R['source'] == SalesPayments::SOURCE_RECEIPT) { ?>
                                        <button type="button" class="btn btn-default btn-sm text-danger" data-toggle="modal" title="cancel receipt"
                                                data-target="#confirm-cancel-modal"
                                                data-receiptid="<?= $R['id'] ?>" data-receiptno="<?= getTransNo($R['id']) ?>">
                                            <i class="fa fa-close"></i> Cancel
                                        </button>
                                    <? } ?>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="confirm-cancel-modal" tabindex="-1" role="dialog" aria-labelledby="confirm-cancel-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-danger">Do you want to cancel receipt?</h4>
                <p>Receipt No: <span class="text-primary receiptno"></span></p>
            </div>
            <form action="<?= url('payments', 'cancel_receipt') ?>" method="post" onsubmit="return validateInputs()">
                <input type="hidden" class="receiptid" name="receiptid">
                <div class="modal-body">
                    <h5>Tally Transaction</h5>
                    <table class="table table-bordered table-condensed" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <td>#</td>
                            <td>Voucher Type</td>
                            <td>Voucher No</td>
                        </tr>
                        </thead>
                        <tbody class="tbody">
                        <tr>
                            <td>1</td>
                            <td>Receipt</td>
                            <td>R-0053</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row mt-md">
                        <div class="col-md-12">
                            Remark:
                            <textarea name="remark" rows="2" class="form-control" placeholder="remark" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger btn-sm">Confirm</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", "client", 2);
        initSelectAjax('.userid', "?module=users&action=getUser&format=json", 'User', 2);
        $('#branchid').select2({width: '100%'});
    });

    function validateInputs() {
        $('#spinnerHolder').show();
    }

    $('#confirm-cancel-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        $(modal).find('.receiptid').val('');
        $(modal).find('.receiptno').text('');
        $(modal).find('tbody.tbody').empty();


        let receiptid = source.data('receiptid');
        $(modal).find('.receiptid').val(source.data('receiptid'));
        $(modal).find('.receiptno').text(source.data('receiptno'));

        $.get(`?module=payments&action=receiptTallyTransaction&format=json&receiptid=${receiptid}`, null, function (data) {
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'success') {

                $.each(result.data, function (i, item) {
                    let row = `<tr>
                                    <td>${++i}</td>
                                    <td>${item.voucher_type}</td>
                                    <td>${item.voucherno}</td>
                                </tr>`;
                    $(modal).find('tbody.tbody').append(row);
                });
            } else {
                triggerError(result.msg || 'Error found')
            }
        });
    });
</script>
