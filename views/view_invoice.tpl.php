<div class="row d-flex justify-content-center">
    <div class="col-xs-12 col-xl-10">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">Invoice No: <span class="text-primary"><?= $invoice['receipt_no'] ?></span></h2>
                <div class="d-flex align-items-center">
                    <? if (IS_ADMIN && $invoice['clientid'] != 1 && $invoice['paymenttype'] == PAYMENT_TYPE_CASH && $invoice['iscreditapproved']) { ?>
                        <a class="btn btn-primary mr-xs" href="#convert-invoice-modal" data-toggle="modal"
                           title="Change cash invoice to credit invoice">
                            <i class="fa fa-recycle"></i> Convert to Credit invoice</a>
                    <? } ?>
                    <? if ($invoice['can_fiscalize'] && Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                        <a class="btn btn-danger mr-xs" href="#fiscalize-invoice-modal" data-toggle="modal"
                           title="Convert invoice for fiscalization">
                            <i class="fa fa-cogs"></i> Fiscalize Invoice</a>
                    <? } ?>
                    <? if ($invoice['iscreditapproved'] && !$invoice['fisc_invoiceid'] && Users::can(OtherRights::issue_credit_note)) { ?>
                        <a class="btn btn-warning" href="#credit-note-modal" data-toggle="modal"
                           title="Issue credit note">
                            <i class="fa fa-reply"></i> Credit Note</a>
                    <? } ?>
                    <? if ($invoice['has_serialno'] && $invoice['iscreditapproved'] && CS_SUPPORT_INTEGRATION) { ?>
                        <div class="d-flex align-items-center ml-md">
                            <span class="spinner-border" style="height: 30px;width: 30px;display: none"></span>
                            <a class="btn btn-danger ml-xs" onclick="show_serial_send_spinner(this)"
                               href="<?= url('serialnos', 'send_serialno_to_support', ['salesid' => $invoice['salesid']]) ?>"
                               title="Sends all serial nos to support system">
                                <i class="fa fa-send"></i> Send Serialno to Support</a>
                        </div>
                    <? } ?>
                </div>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <p class="m-xs">Client Name: <span class="text-primary"><?= $invoice['clientname'] ?></span></p>
                        <p class="m-xs">Vat Exempted: <span class="text-primary"><?= $invoice['vat_exempted'] ? 'Yes' : 'No' ?></span></p>
                        <? if ($invoice['fisc_invoiceid']) { ?>
                            <div class="mt-md mb-md">
                                <h4>
                                    <span class="text-danger text-weight-bold">CONVERTED</span>
                                    <a href="<?= url('sales', 'view_invoice', ['salesid' => $invoice['fisc_invoiceid']]) ?>"
                                       class="btn btn-default btn-sm">View <?= $invoice['fisc_invoiceno'] ?></a>
                                </h4>
                                <p class="m-xs">Converted by: <span class="text-primary"><?= $invoice['converter'] ?></span></p>
                                <p class="m-xs">Convert date: <span
                                            class="text-primary"><?= fDate($invoice['convertdate'], 'd M Y H:i') ?></span></p>
                            </div>
                        <? } else { ?>
                            <p class="m-xs">Payment Status:
                                <? if ($invoice['payment_status'] == PAYMENT_STATUS_COMPLETE) { ?>
                                    <span class="text-uppercase text-success" style="font-weight: bold">Completed</span>
                                <? } ?>
                                <? if ($invoice['payment_status'] == PAYMENT_STATUS_PARTIAL) { ?>
                                    <span class="text-uppercase text-warning" style="font-weight: bold">Partial</span>
                                <? } ?>
                                <? if ($invoice['payment_status'] == PAYMENT_STATUS_PENDING) { ?>
                                    <span class="text-uppercase text-danger" style="font-weight: bold">Pending</span>
                                <? } ?>
                            </p>
                        <? } ?>
                        <p class="m-xs">Currency: <span
                                    class="text-primary"><?= $invoice['currencyname'] ?> - <?= $invoice['currency_description'] ?></span>
                        </p>
                        <p class="m-xs">Full Amount: <span class="text-primary"><?= formatN($invoice['full_amount']) ?></span></p>
                        <p class="m-xs">Total Paid: <span
                                    class="client_tin text-success"><?= formatN($invoice['lastpaid_totalamount']) ?></span>
                        </p>
                        <p class="m-xs">Credit Notes Amount: <span class="text-rosepink"><?= formatN($invoice['total_increturn']) ?></span>
                        </p>
                        <? if ($invoice['paymenttype'] == PAYMENT_TYPE_CREDIT && $invoice['pending_amount'] > 0) { ?>
                            <p class="m-xs">Pending Amount: <span class="text-danger"><?= formatN($invoice['pending_amount']) ?></span></p>
                        <? } ?>
                        <p class="m-xs">Expense Amount: <span class="text-danger"><?= formatN($invoice['expense_amount']) ?></span></p>
                        <? if ($invoice['has_combine']) { ?>
                            <p class="m-xs">Has Combination: <span class="text-danger text-weight-bold">Yes</span></p>
                        <? } ?>
                    </div>
                    <div class="col-md-4">
                        <p class="m-xs">Sales No: <span class="text-primary"><?= $invoice['salesid'] ?></span></p>
                        <p class="m-xs mt-md">Payment Type: <span class="text-primary text-capitalize"><?= $invoice['paymenttype'] ?></span>
                        </p>
                        <? if ($invoice['credit_convertedby']) { ?>
                            <p class="m-xs">
                                <span class="text-danger"><i>Converted from cash, <?= fDate($invoice['credit_convertedat'], 'd M Y H:i') ?></i></span>
                            </p>
                            <p class="m-xs">Reason:</p>
                            <div class="d-flex">
                                <div class="col-md-10">
                                    <textarea class="form-control text-sm" readonly
                                              rows="3"><?= $invoice['credit_convert_remarks'] ?></textarea>
                                </div>
                            </div>
                        <? } ?>
                        <p class="m-xs">Location: <span
                                    class="text-primary text-capitalize"><?= $invoice['stocklocation'] ?> - <?= $invoice['branchname'] ?></span>
                        </p>
                        <p class="m-xs">Sales Type: <span
                                    class="text-primary text-capitalize"><?= $invoice['source'] == Sales::SOURCE_DETAILED ? 'Normal' : 'Quick' ?> Sale</span>
                        </p>
                        <p class="m-xs">Issue Date: <span class="text-primary"><?= fDate($invoice['doc'], 'd M Y H:i') ?></span></p>
                        <p class="m-xs">Sales person: <span class="client_tin text-primary"><?= $invoice['sales_person'] ?></span></p>
                        <p class="m-xs">Approval Status:
                            <? if ($invoice['approver']) { ?>
                                <span class="text-success">Approved by <?= $invoice['approver'] ?>, <?= fDate($invoice['approvedate'], 'd M Y H:i') ?></span>
                            <? } else { ?>
                                <span class="text-danger">Not approved</span>
                            <? } ?>
                        </p>
                        <? if ($invoice['orderno']) { ?>
                            <div class="d-flex align-items-center">
                                <span>Order No:</span>
                                <a class="text-primary ml-md"
                                   href="<?= url('orders', 'order_list', ['ordernumber' => $invoice['orderno']]) ?>">
                                    <?= $invoice['orderno'] ?>, <?= $invoice['order_creator'] ?></a>
                                <? if (!$invoice['foreign_orderid'] && Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                    <button type="button" data-toggle="modal" data-target="#change-order-person-modal"
                                            class="btn btn-default btn-sm ml-md">Change order person
                                    </button>
                                <? } ?>
                            </div>
                        <? } else { ?>
                            <p class="m-xs">Order No:
                                <span class="text-muted">N/A</span>
                            </p>
                        <? } ?>

                        <p class="m-xs">Proforma No: <span
                                    class="client_tin text-primary"><?= $invoice['proformaid'] ? $invoice['proformaid'] : 'N/A' ?></span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <? $approved = !$invoice['approver'] || Users::can(OtherRights::approve_other_credit_invoice) || Users::can(OtherRights::approve_credit) ?>
                            <h5>Documents</h5>
                            <button class="btn btn-primary btn-sm ml-xlg" data-target="#sales-document-modal" data-toggle="modal"
                                    data-saleid="<?= $invoice['salesid'] ?>" data-invoiceno="<?= $invoice['receipt_no'] ?>"
                                    data-approved="<?= $approved ? '' : 1 ?>"
                                    title="attach document">
                                <i class="fa fa-file-archive-o"></i> Add Documents
                            </button>
                        </div>
                        <? if ($documents) { ?>
                            <ol>
                                <? foreach ($documents as $d) { ?>
                                    <li>
                                        <a href="<?= $d['path'] ?>" target="_blank"><?= $d['documentname'] ?></a>
                                    </li>
                                <? } ?>
                            </ol>
                        <? } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <p class="m-xs">Internal Remarks:</p>
                        <div class="col-md-10">
                            <textarea class="form-control text-sm" readonly rows="3"><?= $invoice['internal_remarks'] ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <p class="m-xs">Invoice Remarks:</p>
                        <div class="col-md-10">
                            <textarea class="form-control text-sm" readonly rows="3"><?= $invoice['remarks'] ?></textarea>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs mt-md">
                    <li class="active"><a data-toggle="tab" href="#products"><i class="fa fa-shopping-cart"></i> Products</a></li>
                    <li><a data-toggle="tab" href="#credit-notes"><i class="fa fa-reply"></i> Credit Notes</a></li>
                </ul>
                <div class="tab-content">
                    <div id="products" class="tab-pane fade in active">
                        <table class="table table-hover mb-none" style="font-size:10pt;">
                            <thead>
                            <tr>
                                <th class="stick">#</th>
                                <th class="stick">Barcode</th>
                                <th class="stick">Product Name</th>
                                <th class="stick">Print</th>
                                <th class="stick">Price</th>
                                <th class="stick">Discount</th>
                                <th class="stick">Selling Price</th>
                                <th class="stick">Quantity</th>
                                <th class="stick">VAT%</th>
                                <th class="stick">VAT Amount</th>
                                <th class="stick">Total Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <? $count = 1;
                            foreach ($invoice['products'] as $index => $R) { ?>
                                <tr>
                                    <td><?= $count ?></td>
                                    <td><?= $R['barcode_office'] ?: $R['barcode_manufacture'] ?></td>
                                    <td>
                                        <div><?= $R['productname'] ?></div>
                                        <div class="text-muted"><i><?= $R['extra_description']?:$R['product_description'] ?></i></div>
                                    </td>
                                    <td><?= $R['show_print'] ? 'Yes' : 'No' ?></td>
                                    <td><?= formatN($R['price']) ?></td>
                                    <td><?= formatN($R['discount']) ?></td>
                                    <td><?= formatN($R['selling_price']) ?></td>
                                    <td><?= $R['quantity'] ?></td>
                                    <td><?= $R['vat_rate'] ?></td>
                                    <td><?= formatN($R['vat_amount']) ?></td>
                                    <td><?= formatN($R['total_amount']) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="11">
                                        <div class="row d-flex justify-content-end">
                                            <div class="col-md-4 prescription">
                                                <? if ($R['prescription_required'] == 1) { ?>
                                                    <fieldset class="row-panel">
                                                        <legend>Prescription</legend>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <p>Doctor: <span
                                                                            class="doctor text-primary text-uppercase"><?= $R['prescription_doctor'] ?></span>
                                                                </p>
                                                                <p>Hospital: <span
                                                                            class="hospital text-primary text-uppercase"><?= $R['prescription_hospital'] ?></span>
                                                                </p>
                                                                <p>Referred: <span
                                                                            class="referral text-primary text-uppercase"><?= $R['referred'] == 1 ? 'Yes' : 'No' ?></span>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-7">
                                                    <textarea class="form-control prescription text-sm" rows="5"
                                                              readonly><?= $R['prescription'] ?></textarea>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                <? } ?>
                                            </div>
                                            <div class="col-md-5 prescription">
                                                <? if ($R['track_expire_date'] == 1) { ?>
                                                    <table class="table table-bordered table-condensed" style="font-size: 9pt;">
                                                        <thead>
                                                        <tr style="font-weight: bold;">
                                                            <td>Batch No</td>
                                                            <td style="text-align: center;">Sold Qty</td>
                                                            <td style="text-align: right;">Expire Date</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody style="font-size:10pt;">
                                                        <? foreach ($R['batches'] as $index => $batch) { ?>
                                                            <tr>
                                                                <td><?= $batch['batch_no'] ?></td>
                                                                <td style="text-align: center;"><?= $batch['batchSoldQty'] ?></td>
                                                                <td style="text-align: right;"><?= $batch['expire_date'] ?></td>
                                                            </tr>
                                                        <? } ?>
                                                        </tbody>
                                                    </table>
                                                <? } ?>
                                                <? if ($R['trackserialno'] == 1) { ?>
                                                    <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                                        <div class="d-flex justify-content-end mb-xs">
                                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                                    data-target="#edit-serialno-modal"
                                                                    data-serialnos="<?= base64_encode(json_encode($R['serialnos'])) ?>">
                                                                <i class="fa fa-pencil"></i> Edit Serial numbers
                                                            </button>
                                                        </div>
                                                    <? } ?>
                                                    <table class="table table-bordered table-condensed" style="font-size: 9pt;">
                                                        <thead>
                                                        <tr style="font-weight: bold;">
                                                            <td>#</td>
                                                            <td style="text-align: center;">Serial number</td>
                                                        </tr>
                                                        </thead>
                                                        <tbody style="font-size:9pt;">
                                                        <? foreach ($R['serialnos'] as $si => $sno) { ?>
                                                            <tr>
                                                                <td><?= $si + 1 ?></td>
                                                                <td class="serialno-<?= $sno['id'] ?>"
                                                                    style="text-align: center;"><?= $sno['number'] ?></td>
                                                            </tr>
                                                        <? } ?>
                                                        </tbody>
                                                    </table>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11"></td>
                                </tr>
                                <? $count++;
                            } ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="credit-notes" class="tab-pane fade">
                        <table class="table table-hover mb-none" style="font-size:13px">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>No.</th>
                                <th>Type</th>
                                <th>Issued By</th>
                                <th>Issued Date</th>
                                <th>Credit note amount</th>
                                <th>Returned amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? if ($returns) {
                                $count = 1;
                                foreach ($returns as $index => $R) { ?>
                                    <tr>
                                        <td><?= $count ?></td>
                                        <td><?= getCreditNoteNo($R['id']) ?></td>
                                        <td class="text-capitalize">
                                            <? if ($R['type'] == SalesReturns::TYPE_PRICE) { ?>
                                                Price Correction
                                            <? } elseif ($R['type'] == SalesReturns::TYPE_FULL) { ?>
                                                Full invoice
                                            <? } else { ?>
                                                Items return
                                            <? } ?>
                                        </td>
                                        <td><?= $R['issuedby'] ?></td>
                                        <td><?= fDate($R['doc'], 'd F Y H:i') ?></td>
                                        <td><?= formatN($R['total_incamount']) ?></td>
                                        <td>
                                            <?
                                            if ($R['apid']) { ?>
                                                <a href="<?= url('advance_payments', 'list', ['apid' => $R['apid']]) ?>">
                                                    Advance Receipt of <?= formatN($R['advance_amount']) ?>
                                                </a>
                                                <?
                                            } elseif ($R['return_method']) { ?>
                                                <p class="m-none"><?= formatN($R['return_amount']) ?></p>
                                                <? if ($R['return_method'] == PaymentMethods::CASH) { ?>
                                                    <? $text = "Cash"; ?>
                                                    <p class="m-none"><?= $text ?></p>
                                                <? } ?>
                                                <? if ($R['return_method'] == PaymentMethods::BANK) { ?>
                                                    <? $text = "Bank Name: {$R['bankname']}, Bank Reference: {$R['bankreference']}"; ?>
                                                    <p class="m-none"><?= $text ?></p>
                                                <? } ?>
                                                <? if ($R['return_method'] == PaymentMethods::CHEQUE) { ?>
                                                    <? $text = "Cheque No: {$R['chequename']}, Cheque Type: {$R['chequetype']}"; ?>
                                                    <p class="m-none"><?= $text ?></p>
                                                <? } ?>
                                                <? if ($R['return_method'] == PaymentMethods::CREDIT_CARD) { ?>
                                                    <? $text = "Reference No: {$R['credit_cardno']}"; ?>
                                                    <p class="m-none"><?= $text ?></p>
                                                <? } ?>
                                                <?
                                            } ?>
                                        </td>
                                        <td>
                                            <? if ($R['return_status'] == 'approved') { ?>
                                                <p class="text-success">
                                                    <small>Approved: <?= $R['approver'] ?> <?= fDate($R['approval_date'], 'd M Y H:i') ?></small>
                                                </p>
                                            <? } elseif ($R['return_status'] == 'canceled') { ?>
                                                <p class="text-rosepink">Canceled</p>
                                            <? } else { ?>
                                                <p class="text-muted">not approved</p>
                                            <? } ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-sm" title="Approve credit note"
                                               href="?module=sales_returns&action=view&returnno=<?= $R['id'] ?>">
                                                <i class="fa fa-list"></i> View</a>
                                            <? if ($R['return_status'] == 'approved') { ?>
                                                <a class="btn btn-primary btn-sm" target="_blank" title="Print credit note"
                                                   href="?module=sales_returns&action=print_credit_note&returnno=<?= $R['id'] ?>">
                                                    <i class="fa fa-print"></i> Print</a>
                                            <? } ?>
                                        </td>
                                    </tr>
                                    <? $count++;
                                }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No credit notes issued</td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="credit-note-modal" tabindex="-1" role="dialog" aria-labelledby="credit-note-modal" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= url('sales_returns', 'issue_credit_note') ?>" method="post">
            <input type="hidden" name="salesid" value="<?= $invoice['salesid'] ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Choose Return Type</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">
                            <label class="radio mb-lg" title="return amount & stock of selected item">
                                <input id="partial-return" type="radio" name="return_type" value="<?= SalesReturns::TYPE_ITEM ?>" checked
                                       onchange="checkType()">Items return
                            </label>
                            <label class="radio mb-lg" title="price correction">
                                <input id="partial-return" type="radio" name="return_type" value="<?= SalesReturns::TYPE_PRICE ?>"
                                       onchange="checkType()">Price Change
                            </label>
                            <label class="radio" title="return full invoice, amount & stock">
                                <input id="full-return" type="radio" name="return_type" value="<?= SalesReturns::TYPE_FULL ?>"
                                       onchange="checkType()">Full
                                return
                            </label>
                            <textarea id="return-description" name="description" rows="4" class="form-control input-sm"
                                      placeholder="full return description" disabled></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm mr-sm">Continue</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="convert-invoice-modal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="convert-invoice-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= url('sales', 'convert_to_credit_invoice') ?>" method="post" onsubmit="return check_convert_form(this)">
            <input type="hidden" name="salesid" value="<?= $invoice['salesid'] ?>">
            <div class="modal-content">
                <div class="modal-body">
                    <div>
                        <h4>Do you want to convert this invoice to credit invoice?</h4>
                        <p>Remarks: </p>
                        <textarea class="form-control" name="remarks" rows="3" required placeholder="remarks"></textarea>
                    </div>
                    <div class="d-flex justify-content-end mt-md">
                        <div class="d-flex align-items-center">
                            <object class="save-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"
                                    style="display: none"></object>
                            <button class="btn btn-danger mr-sm">Confirm</button>
                        </div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="fiscalize-invoice-modal" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="fiscalize-invoice-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= url('sales', 'fiscalize_sr_invoice') ?>" method="post" onsubmit="return show_submit_spinner(this)">
            <input type="hidden" name="salesid" value="<?= $invoice['salesid'] ?>">
            <div class="modal-content">
                <div class="modal-body">
                    <div>
                        <h4>Do you want to fiscalize this invoice?</h4>
                        <p>NB: <span class=" text-primary">New invoice will be created for approval</span></p>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="d-flex align-items-center">
                            <object class="save-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"
                                    style="display: none"></object>
                            <button class="btn btn-danger mr-sm">Confirm</button>
                        </div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit-serialno-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="edit-serialno-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <input type="hidden" name="sdi" class="sdi">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit serial number</h4>
                <p>Product: <span class="text-primary productname"></span></p>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <td>#</td>
                        <td>Serial Number</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody class="tbody">
                    <tr>
                        <td>1</td>
                        <td>
                            <input type="text" readonly class="form-control input-sm serial_number" autocomplete="off"
                                   ondblclick="enableSerialInput(this)">
                            <input type="hidden" class="snoid">
                            <input type="hidden" class="sdi">
                        </td>
                        <td class="status">
                            <object class="change-spinner" data="images/loading_spinner.svg" style="visibility: hidden" type="image/svg+xml"
                                    height="25" width="25"></object>
                            <button type="button" class="btn btn-primary btn-sm change-btn" title="validate" onclick="changeSerialNo(this)"
                                    disabled>
                                Change
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="change-order-person-modal" data-backdrop="static" role="dialog" aria-labelledby="change-order-person-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change Order Person</h4>
            </div>
            <form action="<?= url('orders', 'change_order_person') ?>" method="post" onsubmit="return check_order_person_form(this)">
                <input type="hidden" name="orderid" value="<?= $invoice['orderno'] ?>">
                <div class="modal-body">
                    <label>Choose Order person:</label>
                    <select id="order-person" name="personid" class="form-control" required></select>
                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center">
                    <object class="save-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"
                            style="display: none"></object>
                    <button class="btn btn-success mr-sm">Confirm</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= component('sale/sales_document_modal.tpl.php') ?>
<script src="assets/js/quick_adds.js"></script>
<script>

    $(function () {
        initSelectAjax('#order-person', "?module=users&action=getUser&format=json", 'Choose order person', 2,);
    });

    function check_order_person_form(form) {
        $(form).find('.save-spinner').show();
        $(form).find('button.btn').prop('disabled', true);
    }

    function check_convert_form(form) {
        $(form).find('.save-spinner').show();
        $(form).find('button').prop('disabled', true);
    }

    function show_submit_spinner(form) {
        $(form).find('.save-spinner').show();
        $(form).find('button').prop('disabled', true);
    }
    function show_serial_send_spinner(obj) {
        $(obj).closest('div.d-flex').find('.spinner-border').show();
    }

    $('#credit-note-modal').on('show.bs.modal', function (e) {
        checkType();
    });


    function checkType() {
        if ($('#full-return').is(':checked')) {
            $('#return-description').prop('required', true).prop('disabled', false).val('');
        } else {
            $('#return-description').prop('required', false).prop('disabled', true).val('');
        }
    }

    $('#edit-serialno-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        let serialnos = $(source).data('serialnos');

        $(modal).find('tbody.tbody').empty();

        serialnos = atob(serialnos);
        serialnos = JSON.parse(serialnos);
        // console.log(serialnos);

        let count = 1;
        $.each(serialnos, function (i, item) {
            let row = `<tr>
                            <td>1</td>
                            <td>
                                <input type="text" readonly class="form-control input-sm serial_number" autocomplete="off"
                                       ondblclick="enableSerialInput(this)" value="${item.number}">
                                <input type="hidden" class="snoid" value="${item.id}">
                                <input type="hidden" class="sdi" value="${item.sdi}">
                            </td>
                            <td class="status">
                                <object class="change-spinner" data="images/loading_spinner.svg" style="visibility: hidden" type="image/svg+xml"
                                        height="25" width="25"></object>
                                <button type="button" class="btn btn-primary btn-sm change-btn" title="validate" onclick="changeSerialNo(this)" disabled>
                                    Change
                                </button>
                            </td>
                        </tr>`;
            $(modal).find('tbody.tbody').append(row);
            count++;
        });
    });

    function enableSerialInput(obj) {
        $(obj).prop('readonly', false);
        $(obj).closest('tr').find('.change-btn').prop('disabled', false);
    }

    function changeSerialNo(obj) {
        let row = $(obj).closest('tr');
        let spinner = $(row).find('.change-spinner');
        spinner.css('visibility', 'visible');
        let serial_number = $(row).find('.serial_number').val();
        let snoid = $(row).find('.snoid').val();
        let sdi = $(row).find('.sdi').val();
        if (serial_number.length === 0) {
            triggerError('Invalid serial number');
            spinner.css('visibility', 'hidden');
            $(row).find('.serial_number').focus();
            return;
        }
        $(obj).prop('disabled', true);
        $.get(`?module=sales&action=changeSerialNo&format=json&serial_number=${serial_number}&snoid=${snoid}&sdi=${sdi}`, null, function (data) {
            spinner.css('visibility', 'hidden');
            let result = JSON.parse(data);
            if (result.status === 'success') {
                triggerMessage("Serial number changed successfully", 2000);
                $(row).find('.serial_number').prop('readonly', true);
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            } else if (result.status === 'same') {
                triggerWarning("Same serial number nothing changed", 2000);
                $(row).find('.serial_number').focus();
                $(obj).prop('disabled', false);
            } else {
                triggerError(result.msg || 'Error found', 3000);
                $(row).find('.serial_number').focus();
                $(obj).prop('disabled', false);
            }
        });
    }
</script>
