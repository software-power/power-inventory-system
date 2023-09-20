<style media="screen">

    form table tr td {
        padding-left: 10px;
    }

    .for-view-filter {
        height: 165px;
        padding: 10px;
    }

    .btn-align {
        float: right;
        position: relative;
        top: -25px;
    }

    .badge-closed {
        background-color: #47a447;
    }

    .badge-pending {
        background-color: #d2322d;
    }

    .badge-invalid {
        background-color: orange;
    }

    .badge-canceled {
        background-color: #d35779;
    }

    .dropleft .dropdown-menu {
        top: 0;
        right: 100%;
        left: auto;
        margin-top: 0;
        margin-right: .125rem;
    }

    a.dropdown-item {
        text-decoration: none;
    }

    .dropdown-item {
        display: block;
        width: 100%;
        padding: .25rem 1.5rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }

    .table-responsive {
        min-height: 150px;
    }

    .panel-body .badge {
        border-radius: unset;
        width: 100%;
        font-weight: 400;
    }
</style>
<header class="page-header">
    <h2>List of Order</h2>
</header>

<?
$CAN_APPROVE_OTHER_INVOICE = Users::can(OtherRights::approve_other_credit_invoice);
$CAN_SALE_OTHER_ORDER = Users::can(OtherRights::sale_other_order);
?>
<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Search</h4>
            </div>
            <form>
                <input type="hidden" name="module" value="orders">
                <input type="hidden" name="action" value="order_list">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Order no:</label>
                            <input type="text" name="ordernumber" class="form-control" placeholder="order number">
                        </div>
                        <? if ($CAN_APPROVE_OTHER_INVOICE || $CAN_SALE_OTHER_ORDER) { ?>
                            <div class="col-md-4">
                                <label for="">Branch</label>
                                <select id="branchid" class="form-control" name="branchid">
                                    <? if ($CAN_APPROVE_OTHER_INVOICE) { ?>
                                        <option value="">-- All Branch --</option>
                                    <? } ?>
                                    <? foreach ($branches as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Client</label>
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Issued by</label>
                            <? if ($CAN_APPROVE_OTHER_INVOICE || $CAN_SALE_OTHER_ORDER) { ?>
                                <select id="userid" class="form-control" name="issuedby"></select>
                            <? } else { ?>
                                <input type="hidden" name="issuedby" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            <label for="">Order Status</label>
                            <select id="sales_status" class="form-control" name="order_status">
                                <option value="" selected disabled>--Choose Status--</option>
                                <option value="closed">Closed</option>
                                <option value="pending">Pending</option>
                                <option value="invalid">Invalid</option>
                                <option value="canceled">Canceled</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <label for="">Order Type</label>
                            <select id="sales_status" class="form-control" name="order_type">
                                <option value="" selected disabled>--Choose Type--</option>
                                <option <?= selected($type, Orders::TYPE_QUICK) ?> value="<?= Orders::TYPE_QUICK ?>">Quick Order
                                </option>
                                <option <?= selected($type, Orders::TYPE_NORMAL) ?> value="<?= Orders::TYPE_NORMAL ?>">Normal
                                    Order
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">From</label>
                            <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="">To</label>
                            <input type="date" name="todate" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button class="btn btn-success btn-sm">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading for-heading">
            <h2 class="panel-title">List of Orders</h2>
            <h5 class="text-primary"><?= $title ?></h5>
            <div class="btn-align">
                <? if (CS_SUPPORT_INTEGRATION) { ?>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#quick-add-client-modal" data-forsupport="1"
                            title="for support purpose">
                        <i class="fa fa-plus"></i> Quick Add Support Client
                    </button>
                <? } ?>
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search-modal"><i class="fa fa-search"></i> Search
                </button>
                <a href="?module=home&action=index" class="btn"><i class="fa fa-home"></i> Home</a>
            </div>
        </header>
        <div class="panel-body">
            <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Order No.</th>
                    <th>Client Name</th>
                    <th>Location</th>
                    <th>Order Type</th>
                    <th>Issued By</th>
                    <th>Issued Date</th>
                    <th>Valid until</th>
                    <th>Currency</th>
                    <th>Order value</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <? $USER_CAN_EDIT = Users::can(OtherRights::edit_order);
                foreach ($orderList as $id => $R) { ?>
                    <tr class="<? if ($R['salestatus'] == 'closed') echo 'colorClosed'; ?>">
                        <td><?= $id + 1 ?></td>
                        <td>
                            <p class="m-none"><?= $R['orderid'] ?></p>
                            <? if ($R['billid']) { ?>
                                <div class="text-success text-xs">Automated billing, bill no <?= $R['billid'] ?></div>
                            <? } ?>
                            <? if ($R['salesid']) { ?>
                                <a href="<?= url('sales', 'view_invoice', ['salesid' => $R['salesid']]) ?>" target="_blank" title="invoice no">
                                    <i class="text-xs"><?= $R['invoiceno'] ?></i>
                                </a>
                            <? } ?>
                            <? if ($R['foreign_orderid']) { ?>
                                <div class="text-primary"><?= ucfirst($R['order_source']) ?> orderno <?= $R['foreign_orderid'] ?></div>
                            <? } ?>
                        </td>
                        <td title="view client info">
                            <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['orderclientid'] ?>"><?= $R['client_name'] ?></a>
                        </td>
                        <td><?= $R['locationname'] ?> - <?= $R['branchname'] ?></td>
                        <td>
                            <span class="badge"><?= $R['ordertype'] == Orders::TYPE_QUICK ? 'Quick' : 'Normal' ?> Order</span>
                        </td>
                        <td><?= $R['issuedby'] ?></td>
                        <td><?= fDate($R['issueddate'], 'd F Y H:i') ?></td>
                        <td>
                            <? if ($R['order_status'] == Orders::STATUS_PENDING) { ?>
                                <?= fDate($R['valid_until'], 'd F Y H:i') ?>
                            <? } ?>
                        </td>
                        <td><?= $R['currencyname'] ?></td>
                        <td>
                            <p><?= formatN($R['order_value']) ?></p>
                            <? if ($R['base_currency'] != 'yes' && $R['salestatus'] == Orders::STATUS_PENDING) { ?>
                                <i class="text-xs text-muted">
                                    <?= $baseCurrency['name'] ?> <?= formatN($R['base_order_value']) ?>
                                </i>
                            <? } ?>
                        </td>
                        <td>
                            <? if ($R['order_status'] == Orders::STATUS_CANCELED) { ?>
                                <span class="badge badge-canceled"><?= $R['order_status'] ?></span>
                            <? } elseif ($R['order_status'] == Orders::STATUS_CLOSED) { ?>
                                <span class="badge badge-closed"><?= $R['order_status'] ?></span>
                            <? } elseif ($R['order_status'] == Orders::STATUS_INVALID) { ?>
                                <span class="badge badge-invalid"><?= $R['order_status'] ?></span>
                            <? } elseif ($R['order_status'] == Orders::STATUS_PENDING) { ?>
                                <span class="badge badge-pending"><?= $R['order_status'] ?></span>
                            <? } ?>
                        </td>
                        <td>
                            <div class="btn-group dropleft">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-list"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#orderDetailsModal" data-toggle="modal"
                                       data-orderid="<?= $R['orderid'] ?>" data-clientname="<?= $R['client_name'] ?>"
                                       data-tin="<?= $R['clienttinno'] ?>" data-mobileno="<?= $R['clientmobile'] ?>"
                                       data-vrn="<?= $R['clientvrn'] ?>" data-address="<?= $R['clientaddress'] ?>"
                                       data-currencyname="<?= $R['currencyname'] ?> - <?= $R['currency_description'] ?>"
                                       data-ordervalue="<?= formatN($R['order_value']) ?>"
                                       data-orderstatus="<?= $R['order_status'] ?>" data-ordertype="<?= $R['ordertype'] ?>"
                                       data-salesperson="<?= $R['closername'] ?>"
                                       data-orderlocation="<?= $R['locationname'] ?> - <?= $R['branchname'] ?>"
                                       data-ordersource="<?= ucfirst($R['order_source']) ?> <?= ucfirst($R['foreign_ordertype']) ?>"
                                       data-foreignno="<?= $R['foreign_orderid'] ?>"
                                       data-remarks="<?= $R['remarks'] ?>"
                                       data-internalremarks="<?= $R['internal_remarks'] ?>"
                                       data-issueddate="<?= fDate($R['issueddate']) ?>"
                                       data-issuedby="<?= $R['issuedby'] ?>" title="View Order">
                                        <i class="fa fa-book"></i> View Order</a>

                                    <? if ($USER_CAN_EDIT && $R['order_status'] != Orders::STATUS_CLOSED) { ?>
                                        <? if (!($R['foreign_orderid'])) {
                                            if ($R['ordertype'] == Orders::TYPE_NORMAL) { ?>
                                                <a class="dropdown-item" href="?module=orders&action=add_order&orderid=<?= $R['orderid'] ?>"
                                                   title="Edit Order"><i class="fa fa-edit"></i> Edit Order</a>
                                            <? } else { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('pos', 'quick_order', ['order_number' => $R['orderid']]) ?>"
                                                   title="Edit Order"><i class="fa fa-edit"></i> Edit Order</a>
                                            <? }
                                        } ?>
                                        <? if ($R['order_status'] != Orders::STATUS_CANCELED) { ?>
                                            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#valid-modal"
                                                    data-orderid="<?= $R['orderid'] ?>"
                                                    title="Edit Order"><i class="fa fa-long-arrow-up"></i> Extend Valid days
                                            </button>
                                            <form action="<?= url('orders', 'cancel_order') ?>" class="m-none" method="post"
                                                  onsubmit="return confirm('Do you want to cancel this order?')">
                                                <input type="hidden" name="orderid" value="<?= $R['orderid'] ?>">
                                                <button class="dropdown-item"><i class="fa fa-close"></i> Cancel Order</button>
                                            </form>
                                        <? } else { ?>
                                            <? if (!$R['foreign_orderid']) { ?>
                                                <form action="<?= url('orders', 'cancel_order') ?>" class="m-none" method="post"
                                                      onsubmit="return confirm('Do you want to cancel this order?')">
                                                    <input type="hidden" name="orderid" value="<?= $R['orderid'] ?>">
                                                    <input type="hidden" name="revive" value="1">
                                                    <button class="dropdown-item"><i class="fa fa-check"></i> Continue Order</button>
                                                </form>
                                            <? } ?>
                                        <? } ?>
                                    <? } ?>
                                    <? if (CS_SUPPORT_INTEGRATION && $R['foreign_orderid']) { ?>
                                        <a class="dropdown-item"
                                           href="<?= url('orders', 'post_support', ['orderid' => $R['orderid']]) ?>"
                                           title="Send response to support"><i class="fa fa-send"></i> Send invoice to support</a>
                                    <? } ?>
                                    <? if ($R['order_status'] == Orders::STATUS_PENDING) { ?>
                                        <a class="dropdown-item" href="?order_number=<?= $R['orderid'] ?>&module=sales&action=add_sales_new"
                                           title="Order to sales"><i class="fa fa-money"></i> Make Sale</a>
                                        <a class="dropdown-item" href="?order_number=<?= $R['orderid'] ?>&module=pos&action=quick_sales"
                                           title="Order to sales"><i class="fa fa-dollar"></i> Quick Sale</a>
                                    <? } ?>
                                    <a class="dropdown-item" href="<?= url('orders', 'checklist', ['orderno' => $R['orderid']]) ?>"
                                       title="Order check list"><i class="fa fa-list-ol"></i> Order Check list</a>
                                    <a class="dropdown-item" href="<?= url('serialnos', 'send_serialno_to_support', ['salesid' => $R['salesid']]) ?>"
                                       title="Order check list"><i class="fa fa-send"></i> Send serialno to support</a>
                                    <a class="dropdown-item" target="_blank"
                                       href="<?= url('receipts', 'print_order', ['orderno' => $R['orderid'], 'print_size' => 'A4']) ?>"
                                       title="Print Order"><i class="fa fa-print"></i> Print Order</a>
                                    <button class="dropdown-item" data-toggle="modal" href="#efd-order-contact-modal"
                                            data-orderno="<?= $R['orderid'] ?>"
                                            title="Print Order"><i class="fa fa-print"></i> Print EFD Order Form
                                    </button>
                                    <a class="dropdown-item" target="_blank"
                                       href="<?= url('receipts', 'print_order', ['orderno' => $R['orderid'], 'print_size' => 'A4', 'with_bank_info' => '']) ?>"
                                       title="Print Order with bank info"><i class="fa fa-print"></i> Print Order with bank info</a>
                                    <a class="dropdown-item" target="_blank"
                                       href="<?= url('receipts', 'print_order', ['orderno' => $R['orderid']]) ?>"
                                       title="Print Order"><i class="fa fa-print"></i> Print Quick Order</a>
                                    <? if ($R['has_checklist']) { ?>
                                        <a class="dropdown-item" href="<?= url('orders', 'print_checklist', ['orderno' => $R['orderid']]) ?>"
                                           title="Order check list"><i class="fa fa-list-ol"></i> Print Order Check list</a>
                                    <? } ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <? } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>


<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Order No: <span class="orderNo text-primary">34</span></h4>
            </div>
            <div class="modal-body" style="max-height: 70vh;overflow-y: auto;">
                <div class="row mb-md">
                    <div class="col-md-6">
                        <div>Client Name: <span class="client_name text-primary"></span></div>
                        <div>Mobile No: <span class="client_mobile text-primary"></span></div>
                        <div>Client TIN: <span class="client_tin text-primary"></span></div>
                        <div>Client VRN: <span class="client_vrn text-primary"></span></div>
                    </div>
                    <div class="col-md-6">
                        <div>Client address: <span class="client_address text-primary"></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div>Created by: <span class="issuedby text-primary"></span></div>
                        <div>Location: <span class="orderlocation text-primary"></span></div>
                        <div>Date: <span class="issueddate text-primary"></span></div>
                        <div>Sales Person: <span class="sales_person text-primary"></span></div>
                        <div>Order source: <span class="order_source text-primary"></span></div>
                        <div class="p-none m-none">Remarks:</div>
                        <textarea readonly class="form-control remarks" rows="3"></textarea>
                        <!--                            <p>Closed date: <span class="sales_person text-primary"></span></p>-->
                    </div>
                    <div class="col-md-6">
                        <div>Order type: <span class="order_type text-uppercase text-primary"></span></div>
                        <div>Order status: <span class="order_status text-weight-bold text-uppercase"></span></div>
                        <div>Currency: <span class="currencyname text-primary"></span></div>
                        <div>Order value: <span class="order_value text-weight-bold text-success"></span></div>
                        <div>Foreign No: <span class="foreign_no text-primary"></span></div>
                        <div class="p-none m-none">Internal remarks:</div>
                        <textarea readonly class="form-control internal_remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="loading-spinner" style="display: none;">
                    <div class="d-flex justify-content-center align-items-center">
                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="120"
                                width="120"></object>
                        <span>Loading...</span>
                    </div>
                </div>
                <fieldset class="row-panel products-area">
                    <legend>Items</legend>
                    <table class="table" style="font-size:11pt;">
                        <thead>
                        <tr style="font-weight: bold;">
                            <td>#</td>
                            <td>Product</td>
                            <td style="text-align: center;">Qty</td>
                            <td style="text-align: center;">Price</td>
                            <td style="text-align: center;">Vat amount</td>
                            <td style="text-align: right;">Total</td>
                        </tr>
                        </thead>
                        <tbody style="font-size:10pt;">
                        <tr>
                            <td>1.</td>
                            <td>Product</td>
                            <td style="text-align: center;">3</td>
                            <td style="text-align: center;">5,000</td>
                            <td style="text-align: center;">1000</td>
                            <td style="text-align: right;">15,000</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: center;font-weight:bold">TOTAL</td>
                            <td style="text-align: right;font-weight:bold" class="order_value">48</td>
                        </tr>
                        </tfoot>
                    </table>
                </fieldset>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="valid-modal" role="dialog" aria-labelledby="valid-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Extend valid days</h4>
            </div>
            <form method="post" action="<?= url('orders', 'extend_days') ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Order no</label>
                            <input type="text" readonly name="orderid" class="form-control orderid">
                        </div>
                        <div class="col-md-6">
                            <label for="" class="hold-action">Extend Days</label>
                            <input type="number" name="extend_days" class="form-control" min="1" value="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="efd-order-contact-modal" role="dialog" aria-labelledby="efd-order-contact-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">EFD Order-Form Contacts</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="orderno">
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control contact_person" placeholder="Name">
                    </div>
                    <div class="col-md-6">
                        <label for="">Mobile</label>
                        <input type="text" name="contact_mobile" class="form-control contact_mobile" placeholder="Mobile">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="viewEFDOrder(this)">Confirm</button>
            </div>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>
<?= component('shared/quick_add_client_modal.tpl.php') ?>

<script src="assets/js/custom.js"></script>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'choose client');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
        $('#branchid').select2({width: '100%'});

        <?if(isset($_SESSION['clientcode'])){?>
        let clientcode = `<?=$_SESSION['clientcode']?>`;
        let a = document.createElement('a');
        a.setAttribute('data-toggle', 'modal');
        a.setAttribute('data-target', '#client-info-modal');
        a.setAttribute('data-clientid', clientcode);
        a.setAttribute('forsupport', '1');
        a.innerText = "Show client code";
        $(a).css('display', 'none');
        $('.panel-body').append(a);
        $(a).trigger('click');
        <? unset($_SESSION['clientcode']);?>
        <?}?>


    });

    $('#orderDetailsModal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let orderId = source.data('orderid');
        let modal = $(this);
        let spinner = $(modal).find('.loading-spinner');
        let productArea = $('.products-area');
        let tbody = $(modal).find('tbody');
        $(modal).find('.orderNo').text(orderId);
        $(modal).find('.client_name').text(source.data('clientname'));
        $(modal).find('.client_mobile').text(source.data('mobileno'));
        $(modal).find('.client_address').text(source.data('address'));
        $(modal).find('.client_tin').text(source.data('tin'));
        $(modal).find('.client_vrn').text(source.data('vrn'));
        $(modal).find('.currencyname').text(source.data('currencyname'));
        $(modal).find('.order_value').text(source.data('ordervalue'));
        $(modal).find('.order_status').text(source.data('orderstatus'));
        $(modal).find('.orderlocation').text(source.data('orderlocation'));
        $(modal).find('.order_source').text(source.data('ordersource'));
        $(modal).find('.foreign_no').text(source.data('foreignno'));
        $(modal).find('.remarks').val(source.data('remarks'));
        $(modal).find('.internal_remarks').val(source.data('internalremarks'));

        let closedOrder = source.data('orderstatus') === '<?=Orders::STATUS_CLOSED?>';

        switch (source.data('orderstatus')) {
            case `<?=Orders::STATUS_CLOSED?>`:
                $(modal).find('.order_status').removeClass('text-danger text-warning');
                $(modal).find('.order_status').addClass('text-success');
                break;
            case `<?=Orders::STATUS_INVALID?>`:
                $(modal).find('.order_status').removeClass('text-success text-danger');
                $(modal).find('.order_status').addClass('text-warning');
                break;
            default:
                $(modal).find('.order_status').removeClass('text-success text-warning');
                $(modal).find('.order_status').addClass('text-danger');
                break;
        }

        $(modal).find('.order_type').text(source.data('ordertype'));
        $(modal).find('.sales_person').text(source.data('salesperson'));
        $(modal).find('.issuedby').text(source.data('issuedby'));
        $(modal).find('.orderlocation').text(source.data('orderlocation'));
        $(modal).find('.issueddate').text(source.data('issueddate'));

        spinner.show();
        productArea.hide();
        tbody.empty();

        $.get('?module=orders&action=getOrderDetails&format=json&orderId=' + orderId, null, function (data) {
            let results = JSON.parse(data);
            spinner.hide();
            productArea.show();
            if (results.status === 'success') {
                let order = results.data;

                let count = 1;
                $.each(results.data.details, function (i, item) {
                    let productState = ``, title = '';
                    if (!closedOrder) {
                        if (item.stock_state === 'exists') {
                            productState = `<i class="fa fa-check text-success" style="font-size: 12pt;"></i>`;
                            title = 'stock exists';
                        } else if (item.stock_state === 'not-enough') {
                            productState = `<i class="fa fa-thumbs-down text-warning" style="font-size: 12pt;"></i>`;
                            title = 'not enough stock';
                        } else if (item.stock_state === 'held') {
                            productState = `<i class="fa fa-tag text-rosepink" style="font-size: 12pt;"></i>`;
                            title = 'not enough stock, stock is on hold';
                        } else {
                            productState = `<i class="fa fa-close text-danger" style="font-size: 12pt;"></i>`;
                            title = 'no stock';
                        }
                    }
                    let row = `<tr title="${title}">
                            <td>
                                ${count}.
                            </td>
                            <td>
                                ${productState}
                                ${item.productname}
                            </td>
                            <td style="text-align: center;">${item.qty}</td>
                            <td style="text-align: center;">${item.price}</td>
                            <td style="text-align: center;">${item.vatamount}</td>
                            <td style="text-align: right;">${item.total}</td>
                        </tr>`;
                    tbody.append(row);
                    count++;
                });
            } else {
                triggerError("Order info not found!");
            }

        });
    });

    $('#valid-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        $(modal).find('.orderid').val(source.data('orderid'));
    });

    $('#openModel').on('click', function () {
        $('#formHolder').show('slow');
        $('#formModel').show('slow');
    });

    $('#closeSearchModel').on('click', function () {
        $('#formHolder').hide('slow');
        $('#formModel').hide('slow');
    });

    $('#open_filter').on('click', function () {
        $('#for-search-report').toggleClass('for-view-filter');
    });

    $('#efd-order-contact-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let orderno = $(source).data('orderno');
        let modal = $(this);

        $(modal).find('.orderno').val(orderno);
        $(modal).find('.form-control').val('');

        $.get(`?module=orders&action=orderFirstContact&format=json&orderno=${orderno}`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                $(modal).find('.contact_person').val(result.data.name);
                $(modal).find('.contact_mobile').val(result.data.mobile);
            } else {
                triggerError(result.msg || 'Error found');
            }
        })
    });

    function viewEFDOrder(obj) {
        let modal = $(obj).closest('.modal');
        let orderno = $(modal).find('.orderno').val();
        let contacts = {
            name: $(modal).find('.contact_person').val(),
            mobile: $(modal).find('.contact_mobile').val()
        };
        contacts = btoa(JSON.stringify(contacts));
        let url = `<?=url('receipts', 'print_order', ['print_size' => 'A4', 'efd' => ''])?>&orderno=${orderno}&contacts=${contacts}`;
        let a = document.createElement('a');
        a.target = '_blank';
        a.href = url;
        a.click();

        $(modal).modal('hide');
    }
</script>
