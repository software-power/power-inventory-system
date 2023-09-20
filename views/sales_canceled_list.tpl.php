<style>
    th.stick {
        position: sticky;
        top: 100px; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }

    @media (min-width: 768px) {
        .modal-lg {
            width: 90% !important;
        }
    }
</style>

<header class="page-header">
    <h2>Canceled Sale List</h2>
</header>


<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="sales">
                <input type="hidden" name="action" value="canceled_list">
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
                            <select class="form-control" name="branchid">
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="locationid" class="form-control" name="locationid"></select>
                            <? } else { ?>
                                <input type="hidden" name="locationid" value="<?= $defaultLocation['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $defaultLocation['name'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            Client
                            <select id="clientid" class="form-control" name="clientid"></select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="fromdate" value="<?= $fromdate ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search-modal" title="Search"><i
                            class="fa fa-search"></i> Open filter
                </button>
            </div>
            <h2 class="panel-title">Canceled Sale</h2>
            <p class="text-primary"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="forCheckbox table table-hover mb-none" style="font-size: 10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Sale #</th>
                        <th>Invoice #</th>
                        <th>Branch</th>
                        <th>Location</th>
                        <th>Client</th>
                        <th>Payment Type</th>
                        <th>Full Amount</th>
                        <th>Total VAT Amount</th>
                        <th>Issued By</th>
                        <th>Issued date</th>
                        <th>Canceled By</th>
                        <th>Canceled date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($canceled as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['saleid'] ?></td>
                            <td><?= $R['invoiceno'] ?></td>
                            <td><?= $R['branchname'] ?></td>
                            <td><?= $R['locationname'] ?></td>
                            <td title="view client info">
                                <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $R['clientid'] ?>"><?= $R['clientname'] ?></a>
                            </td>
                            <td><?= $R['decoded']['paymenttype'] ?></td>
                            <td><?= formatN($R['decoded']['full_amount']) ?></td>
                            <td><?= formatN($R['decoded']['grand_vatamount']) ?></td>
                            <td><?= $R['decoded']['creator'] ?></td>
                            <td><?= fDate($R['decoded']['doc']) ?></td>
                            <td><?= $R['username'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#view-sale-modal" data-toggle="modal"
                                           title="view GRN"
                                           data-payload="<?= $R['payload'] ?>"
                                           data-saleno="<?= $R['saleid'] ?>">
                                            <i class="fa fa-list"></i> View details</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>


<div class="modal fade" id="view-sale-modal" tabindex="-1" role="dialog" aria-labelledby="view-grn-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Sale No: <span class="text-primary saleno"></span> Invoice No: <span class="text-primary receipt_no"></span>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 60vh;overflow-y: auto;">
                <p>Currency: <span class="currencyname"></span></p>
                <table class="table table-condensed table-bordered" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <td>Product</td>
                        <td>Qty</td>
                        <td class="text-right">Price Exc</td>
                    </tr>
                    </thead>
                    <tbody class="products-holder"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", "Choose location", 1);
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", "Choose client", 1);
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", "Choose client", 1);
        $('#view-sale-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            let payload = source.data('payload');
            let saleInfo = JSON.parse(atob(payload));
            console.log(saleInfo);
            let pretty = JSON.stringify(saleInfo, undefined, 10);

            $(modal).find('.saleno').text('');
            $(modal).find('textarea').val('');
            $(modal).find('tbody.products-holder').empty();

            $(modal).find('.saleno').text(source.data('saleno'));
            $(modal).find('.currencyname').text(saleInfo['currencyname']);
            $(modal).find('.receipt_no').text(saleInfo['receipt_no']);

            $.each(saleInfo['details'], function (i, item) {
                let row = `<tr>
                                <td>${item.productname}</td>
                                <td>${item.quantity}</td>
                                <td class="text-right">${item.price}</td>
                            </tr>`;
                $(modal).find('tbody.products-holder').append(row);
            });
        });
    });

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }


</script>
