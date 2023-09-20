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
<div id="formHolder" class="formholder">
    <h5>Search Query</h5>
    <form action="<?= url('grns', 'canceled_grn_list') ?>" method="post">
        <div id="filter_table">
            <div class="row">
                <div class="col-md-4">
                    <? if (IS_ADMIN) { ?>
                        <select class="form-control" name="search[branchid]">
                            <option value="" selected>All Branch</option>
                            <? foreach ($branches as $key => $R) { ?>
                                <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                            <? } ?>
                        </select>
                    <? } else { ?>
                        <input type="text" class="form-control" readonly value="<?= $branches['name'] ?>">
                        <input type="hidden" value="<?= $branches['id'] ?>" name="search[branchid]">
                    <? } ?>
                </div>
                <div class="col-md-4">
                    <? if (IS_ADMIN) { ?>
                        <select id="locationid" class="form-control" name="search[locationid]">

                        </select>
                    <? } else { ?>
                        <input type="text" class="form-control" readonly value="<?= $location['name'] ?>">
                        <input type="hidden" value="<?= $location['id'] ?>" name="search[locationid]">
                    <? } ?>
                </div>
                <div class="col-md-4">
                    <select id="clientid" class="form-control" name="search[supplierid]">
                        <option value="" selected disabled>Select Supplier</option>
                        <? if ($suppliers) {
                            foreach ($suppliers as $ID => $S) { ?>
                                <option value="<?= $S['id'] ?>"><?= $S['name'] ?></option>
                            <? }
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row mt-md">
                <div class="col-md-2">
                    <label>From:</label>
                    <input type="date" name="search[from]" placeholder="LPO Date"
                           class="form-control for-input">
                </div>
                <div class="col-md-2">
                    <label>To:</label>
                    <input type="date" name="search[to]" placeholder="LPO Date"
                           class="form-control for-input">
                </div>
            </div>

            <div class="row mt-md">
                <div class="col-md-4">
                    <div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
                </div>
                <div class="col-md-4">
                    <a href="?module=grns&action=canceled_grn_list" class="btn btn-success btn-block"><i
                                class="fa fa-minus"></i>
                        RESET</a>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i>
                        SEARCH
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<header class="page-header">
    <h2>Canceled GRN List</h2>
</header>
<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <button id="openModel" class="btn" href="?module=home&action=index" title="Home"><i
                            class="fa fa-search"></i> Open filter
                </button>
            </div>
            <h2 class="panel-title">Canceled GRN</h2>
            <p class="text-primary"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="forCheckbox table table-hover mb-none" style="font-size: 10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>GRN #</th>
                        <th>Branch</th>
                        <th>Location</th>
                        <th>Supplier Name</th>
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
                            <td><?= $R['grnid'] ?></td>
                            <td><?= $R['branchname'] ?></td>
                            <td><?= $R['locationname'] ?></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td><?= $R['decoded']['paymenttype'] ?></td>
                            <td><?= formatN($R['decoded']['full_amount']) ?></td>
                            <td><?= formatN($R['decoded']['grand_vatamount']) ?></td>
                            <td><?= $R['decoded']['issuedby'] ?></td>
                            <td><?= fDate($R['decoded']['issuedate']) ?></td>
                            <td><?= $R['username'] ?></td>
                            <td><?= fDate($R['doc']) ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#view-grn-modal" data-toggle="modal"
                                           title="view GRN"
                                           data-payload="<?= $R['payload'] ?>">
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


<div class="modal fade" id="view-grn-modal" tabindex="-1" role="dialog" aria-labelledby="view-grn-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">GRN: <span class="text-primary grnno"></span></h4>
            </div>
            <div class="modal-body">
                <fieldset class="row-panel" style="margin-top: 0;">
                    <legend>Info</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <p>Supplier: <span class="text-primary supplier"></span></p>
                            <p>Location: <span class="text-primary location"></span></p>
                            <p>Verification code: <span class="text-primary verificationcode"></span></p>
                            <p>Currency: <span class="text-primary currency"></span></p>
                            <p>Full Amount: <span class="text-primary full_amount"></span></p>
                            <p>VAT Amount: <span class="text-primary vat_amount"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p>Invoice No: <span class="text-primary invoiceno"></span></p>
                            <p>Lpo No: <span class="text-primary lpono"></span></p>
                            <p>Issue By: <span class="text-primary issuedby"></span></p>
                            <p>Issue Date: <span class="text-primary issuedate"></span></p>
                        </div>
                    </div>
                </fieldset>
                <table class="table table-condensed mt-md" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th class="stick">#</th>
                        <th class="stick">Stock No</th>
                        <th class="stick">Name</th>
                        <th class="stick">Qty</th>
                        <th class="stick">Unit Cost</th>
                    </tr>
                    </thead>
                    <tbody class="product-body">
                    <tr>
                        <td>1</td>
                        <td>23</td>
                        <td>Product 1</td>
                        <td>10</td>
                        <td>20,000</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="col-md-5 col-md-offset-7">
                                <table class="table table-bordered" style="font-size: 9pt;">
                                    <thead>
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Qty</th>
                                        <th>Expire date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>1334</td>
                                        <td>12</td>
                                        <td>12-02-2025</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", "Choose location", 1);
        $('#view-grn-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            let payload = source.data('payload');
            let grnInfo = JSON.parse(atob(payload));
            // console.log(grnInfo);

            //clear
            $(modal).find('span.text-primary').text('');
            $(modal).find('.product-body').empty();

            $(modal).find('.grnno').text(grnInfo.grnnumber);
            $(modal).find('.supplier').text(grnInfo.suppliername);
            $(modal).find('.location').text(grnInfo.stock_location);
            $(modal).find('.invoiceno').text(grnInfo.invoiceno);
            $(modal).find('.lpono').text(grnInfo.lpoid);
            $(modal).find('.currency').text(grnInfo.currency_name);
            $(modal).find('.verificationcode').text(grnInfo.verificationcode);
            $(modal).find('.issuedby').text(grnInfo.issuedby);
            $(modal).find('.issuedate').text(grnInfo.issuedate);
            $(modal).find('.full_amount').text(numberWithCommas(grnInfo.full_amount));
            $(modal).find('.vat_amount').text(numberWithCommas(grnInfo.grand_vatamount));

            let count = 1;
            $.each(grnInfo.stock,function (i,stock) {
                // console.log(stock);
                let batchRow =``;
                if(stock.track_expire_date==='1'){
                    let batches = ``;
                    $.each(stock.batches,function (b,batch) {
                       batches +=`<tr>
                                        <td>${batch.batch_no}</td>
                                        <td>${batch.batchqty}</td>
                                        <td>${batch.expire_date}</td>
                                    </tr>`;
                    });
                    batchRow = `<div class="col-md-5 col-md-offset-7">
                                    <table class="table table-bordered" style="font-size: 9pt;">
                                        <thead>
                                        <tr>
                                            <th>Batch No</th>
                                            <th>Qty</th>
                                            <th>Expire date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        ${batches}
                                        </tbody>
                                    </table>
                                </div>`;
                }
                let productRow = `<tr>
                                    <td>${count}</td>
                                    <td>${stock.stockid}</td>
                                    <td>${stock.productname}</td>
                                    <td>${stock.qty}</td>
                                    <td>${stock.rate}</td>
                                </tr>
                                <tr>
                                    <td colspan="5">${batchRow}</td>
                                </tr>`;
                $(modal).find('.product-body').append(productRow);
                count++;
            });

        });
    });

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
