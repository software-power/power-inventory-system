<header class="page-header">
    <h2>Advance Receipts</h2>
</header>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Advance Receipts</h2>
                        <p class="text-primary"><?= $title ?></p>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end">
                        <button class="btn btn-default btn-sm mr-md" data-toggle="modal" data-target="#search-modal">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <? if (Users::can(OtherRights::receive_advance)) { ?>
                            <a href="<?= url('advance_payments', 'receive') ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-plus"></i> Make Advance Receipt
                            </a>
                        <? } ?>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>R/No.</th>
                            <th>Client</th>
                            <th>Branch</th>
                            <th>Currency</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Used Amount</th>
                            <th>Remaining Amount</th>
                            <th>Received By</th>
                            <th>Date</th>
                            <th>Remark</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        $USER_CAN_CANCEL = Users::can(OtherRights::cancel_receipt);
                        foreach ($paymentList

                                 as $index => $item) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td>
                                    <p class="m-none"><?= getTransNo($item['id']) ?></p>
                                    <? if ($item['srid']) { ?>
                                        <small> <a href="<?= url('sales_returns', 'view', ['returnno' => $item['srid']]) ?>">credit
                                                note <?= getCreditNoteNo($item['srid']) ?></a></small>
                                    <? } ?>
                                </td>
                                <td title="view client info">
                                    <a data-toggle="modal" href="#client-info-modal"
                                       data-clientid="<?= $item['clientid'] ?>"><?= $item['clientname'] ?></a>
                                </td>
                                <td><?= $item['branchname'] ?></td>
                                <td><?= $item['currencyname'] ?></td>
                                <td>
                                    <?= $item['methodname'] ?>
                                    <? if ($item['methodname'] == PaymentMethods::BANK) { ?>
                                        <? $text = ($item['bankname'] ?: $item['bank_name']) . " " . ($item['bankreference'] ?: $item['bank_accno']) ?>
                                        <p class="text-sm text-muted" title="<?= $text ?>"><?= substr($text, 0, 20) ?></p>
                                    <? } else if ($item['methodname'] == PaymentMethods::CHEQUE) { ?>
                                        <? $text = $item['bank_name'] . " " . $item['chequename'] . " " . $item['chequetype'] ?>
                                        <p class="text-sm text-muted" title="<?= $text ?>"><?= substr($text, 0, 20) ?></p>
                                    <? } else if ($item['methodname'] == PaymentMethods::CREDIT_CARD) { ?>
                                        <? $text = $item['electronic_account'] . " " . $item['credit_cardno'] ?>
                                        <p class="text-sm text-muted" title="<?= $text ?>"><?= substr($text, 0, 20) ?></p>
                                    <? } ?>
                                </td>
                                <td><?= formatN($item['amount']) ?></td>
                                <td class="text-center text-danger"><?= $item['used_advance'] > 0 ? formatN($item['used_advance']) : '-' ?></td>
                                <td class="text-center text-success"><?= $item['remaining_advance'] > 0 ? formatN($item['remaining_advance']) : '-' ?></td>
                                <td><?= $item['creator'] ?></td>
                                <td><?= fDate($item['doc'], 'd F Y H:i') ?></td>
                                <td title="<?= $item['remark'] ?>">
                                    <p class="p-none m-none"><?= substr($item['remark'], 0, 30) ?></p>

                                </td>
                                <td>
                                    <? if (!$item['srid']) { ?>
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-secondary dropdown-toggle"
                                                    data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-list"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('advance_payments', 'print&id=' . $item['id']) ?>"
                                                   title="Edit"> <i class="fa-print fa"></i> Print Receipt</a>
                                                <? if (!$item['tally_post'] && $item['transfer_tally']) { ?>
                                                    <a class="dropdown-item" title="Transfer to tally"
                                                       href="<?= url('advance_payments', 'tally_post', ['receiptno' => $item['id']]) ?>">
                                                        <i class="fa fa-upload"></i> Post Tally</a>
                                                <? } ?>
                                                <? if ($USER_CAN_CANCEL && $item['used_advance'] == 0) { ?>
                                                    <a class="dropdown-item" href="#confirm-cancel-modal" data-toggle="modal" title="Edit"
                                                       data-id="<?= $item['id'] ?>" data-receiptno="<?= getTransNo($item['id']) ?>">
                                                        <i class="fa fa-close"></i> Cancel Receipt</a>
                                                <? } ?>
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
            </div>
    </div>
</div>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center modal-lg">
        <div class="modal-content">
            <form method="get">
                <input type="hidden" name="module" value="advance_payments">
                <input type="hidden" name="action" value="list">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Client</label>
                                <select id="client" name="search[clientid]" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Branch</label>
                                <select name="search[branchid]" class="form-control">
                                    <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                        <option value="" selected>All Branches</option>
                                    <? } ?>

                                    <? foreach ($branches as $index => $R) { ?>
                                        <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Receiver</label>
                                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                    <select id="userid" name="search[receiver]" class="form-control"></select>
                                <? } else { ?>
                                    <input type="hidden" name="search[receiver]" value="<?= $_SESSION['member']['id'] ?>">
                                    <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                                <? } ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Payment Method</label>
                                <select name="search[methodid]" class="form-control">
                                    <option value="" selected>--All method--</option>
                                    <? foreach ($paymentmethod as $index => $R) { ?>
                                        <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">From</label>
                                <input type="date" name="search[fromdate]" class="form-control" value="<?= $fromdate ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">To</label>
                                <input type="date" name="search[todate]" class="form-control" value="<?= $todate ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm confirm-btn">Search
                    </button>
                </div>
            </form>
        </div>
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
                <h4 class="modal-title text-danger">Do you want to cancel advance receipt?</h4>
                <h5>Advance Receipt No: <span class="text-primary receiptno"></span></h5>
            </div>
            <form action="<?= url('advance_payments', 'cancel_receipt') ?>" method="post" onsubmit="return validateInputs()">
                <input type="hidden" class="apid" name="apid">
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
                            <td>ADV-0053</td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="row mt-md">
                        <div class="col-md-12">
                            Remark:
                            <textarea name="remark" rows="2" class="form-control remarks" placeholder="remark" required></textarea>
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
<script>
    $(function () {
        initSelectAjax('#client', '?module=clients&action=getClients&format=json&no_default', 'Choose client');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
    });

    $('#confirm-cancel-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        $(modal).find('.apid').val('').val(source.data('id'));
        $(modal).find('.remarks').val('');
        $(modal).find('.receiptno').text('').text(source.data('receiptno'));
        $(modal).find('tbody.tbody').empty();

        let apid = source.data('id');

        $.get(`?module=advance_payments&action=advanceReceiptTallyTransaction&format=json&apid=${apid}`, null, function (data) {
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
