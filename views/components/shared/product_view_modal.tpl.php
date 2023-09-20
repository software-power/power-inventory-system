<?
$modalLocations = Users::can(OtherRights::view_all_location_stock)
    ? Locations::$locationClass->locationList('', '', 'active', $_SESSION['member']['locationid'])
    : Locations::$locationClass->locationList($_SESSION['member']['locationid'], '', 'active');

$modalBranches = Users::can(OtherRights::view_all_location_stock)
    ? Branches::$branchClass->getAllActive("field(id, {$_SESSION['member']['branchid']}) desc, id")
    : Branches::$branchClass->find(['id' => $_SESSION['member']['branchid']]);
$salesBranch = Branches::$branchClass->get($_SESSION['member']['branchid']);
?>
<style>
    @media (min-width: 768px) {
        #product-view-modal .modal-lg {
            width: 80% !important;
        }
    }
</style>
<div class="modal fade" id="product-view-modal" role="dialog" aria-labelledby="product-view-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Product View</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="product_id">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#product-details"><i class="fa fa-book"></i> Product
                            Details</a></li>
                    <li><a data-toggle="tab" href="#price-list"><i class="fa fa-list"></i> Price List</a></li>
                    <li><a data-toggle="tab" href="#stock-report"><i class="fa fa-database"></i> Stock Report</a></li>
                    <li><a data-toggle="tab" href="#client-purchase"><i class="fa fa-shopping-cart"></i> Sales</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="product-details" class="tab-pane fade in active">
                        <div class="row" style="display: flex;align-items: center;flex-wrap: wrap;">
                            <div class="col-md-5 mb-lg">
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">Name</span></div>
                                    <div class="col-md-6 pname">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">Generic Name</span></div>
                                    <div class="col-md-6 generic_name">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">Department</span></div>
                                    <div class="col-md-6 pdepart">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">Category</span></div>
                                    <div class="col-md-6 pcategory">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">Brand</span></div>
                                    <div class="col-md-6 pbrand">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">VAT</span></div>
                                    <div class="col-md-6 pvat">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">Non-Stock</span></div>
                                    <div class="col-md-6 nonstock">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6"><span class="detail-header">Track Serial No</span></div>
                                    <div class="col-md-6 pstrack">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6">
                                        <span class="detail-header">Track Expire Date</span>
                                    </div>
                                    <div class="col-md-6 track_expire_date">Name</div>
                                </div>
                                <? if (CS_PRESCRIPTION_ENTRY) { ?>
                                    <div class="row mb-md">
                                        <div class="col-md-6"><span
                                                    class="detail-header">Require Prescription</span></div>
                                        <div class="col-md-6 require_prescription">Name</div>
                                    </div>
                                <? } ?>
                                <div class="row mb-md">
                                    <div class="col-md-6">
                                        <span class="detail-header">Unit</span>
                                    </div>
                                    <div class="col-md-6 unitname">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6">
                                        <span class="detail-header">Bulk Unit</span>
                                    </div>
                                    <div class="col-md-6 bulkunitname">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6">
                                        <span class="detail-header">Product Category</span>
                                    </div>
                                    <div class="col-md-6 productcategory">Name</div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-6">
                                        <span class="detail-header">Product Subcategory</span>
                                    </div>
                                    <div class="col-md-6 productsubcategory">Name</div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row mb-md">
                                    <div class="col-md-12">
                                        <label class="detail-header">Description</label>
                                        <textarea class="form-control text-sm product_desc" readonly rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-9" style="display: flex;justify-content: center;">
                                        <div style="border:1px dashed grey;border-radius: 10px;padding: 5px;">
                                            <img class="product_image" src="<?= $productDetails['image_path'] ?>"
                                                 alt="image"
                                                 style="height: 300px;width: 300px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-9" style="display: flex;justify-content: center;">
                                        <canvas class="barcode-holder" width="112" height="142"
                                                style="flex-shrink: 1"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="price-list" class="tab-pane fade">
                        <div class="row">
                            <div class="col-md-12 d-flex align-items-center">
                                Branch:
                                <select id="modal-branchid" onchange="fetchPrices()">
                                    <? foreach ($modalBranches as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
                                <div class="branch_loading_spinner" style="display: none">
                                    <object data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"></object>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-sm">
                            <div class="col-md-6">
                                <p>Quick sale price: <span class="text-success text-weight-bold quicksale_price"></span></p>
                            </div>
                        </div>
                        <div class="row mb-md">
                            <div class="col-md-12">
                                <p class="detail-header">Product Price List</p>
                                <input class="modal_currency_rateid" type="hidden">
                                <table id="price-table" class="table table-hover mb-none table-bordered" style="font-size:9pt;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Hierarchic</th>
                                        <th>Commission</th>
                                        <th>Target</th>
                                        <th>Exclusive Amount</th>
                                        <th>VAT%</th>
                                        <th>Inclusive Amount</th>
                                        <th>Inclusive in (<span class="modal-currencyname" style="color: dodgerblue;"></span>)</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbodyPriceList">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="stock-report" class="tab-pane fade">
                        <div class="row mb-lg" style="min-height: 200px;">
                            <div class="col-md-12"><p class="detail-header">Stock Report</p></div>
                            <div class="col-md-12"></div>
                            <div class="col-md-12 mb-md">
                                <span class="mr-md">Location:</span>
                                <select id="stockLocation" onchange="fetchLocationStock()">
                                    <? foreach ($modalLocations as $index => $l) { ?>
                                        <option value="<?= $l['id'] ?>"><?= $l['name'] ?> - <?= $l['branchname'] ?></option>
                                    <? } ?>
                                </select>
                                <span class="ml-sm">Total Stock: <span class="text-weight-semibold text-rosepink total-stock"></span></span>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <h4>Summary</h4>
                                        <table class="table table-hover mb-none table-bordered summary-table"
                                               style="font-size:9pt;"
                                               id="">
                                            <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Total Qty</th>
                                                <th>Held Qty</th>
                                                <th>Available Qty</th>
                                                <th>Pending Order Qty</th>
                                                <th>Pending Sales Qty</th>
                                                <th>Expecting Stock Qty</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <h4>Batches</h4>
                                        <table class="table table-hover mb-none table-bordered detailed-table"
                                               style="font-size:9pt;" id="">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Batch No.</th>
                                                <th>Expire Date</th>
                                                <th>Remain Days</th>
                                                <th>Qty</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="client-purchase" class="tab-pane fade">
                        <h4>Sales</h4>
                        <div class="row">
                            <div class="col-md-3 d-flex align-items-center">
                                Branch:
                                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                    <select id="sale-branch" class="form-control" onchange="fetchSales()">
                                        <? foreach ($modalBranches as $b) { ?>
                                            <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                <? } else { ?>
                                    <input type="hidden" id="sale-branch" value="<?= $salesBranch['id'] ?>">
                                    <span class="text-primary"> <?= $salesBranch['name'] ?></span>
                                <? } ?>
                            </div>
                            <div class="col-md-3 d-flex align-items-center">
                                From:
                                <input id="sale-fromdate" type="date" class="form-control" value="<?= date('Y-m-d', strtotime('-3 months')) ?>"
                                       onchange="fetchSales()">
                            </div>
                            <div class="col-md-3 d-flex align-items-center">
                                <div class="branch_loading_spinner" style="display: none">
                                    <object data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"></object>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-sm" style="max-height:60vh;overflow-y: auto">
                            <div class="col-md-12">
                                <table id="sale-table" class="table table-bordered table-hover mb-none" style="font-size:13px;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice no</th>
                                        <th>Client Name</th>
                                        <th>Location</th>
                                        <th>Sales Quantity</th>
                                        <th>Currency</th>
                                        <th class="text-right">Unit Price</th>
                                        <th>Sales Date</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/barcode/JsBarcode.all.min.js"></script>
<script>
    $(function () {
        $('#product-view-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let productid = $(source).attr('data-productid');
            if (!productid) {
                triggerError('Choose product first!');
                return false;
            }

            $(`a[href="#product-details"]`).tab('show'); // Select first tab
            $(this).find('select').each(function (i, select) { //reset select
                $(select).val($(select).find('option').eq(0).val());
            });

            $(this).find('.product_id').val(productid);
            getProductInfo(productid);
            fetchPrices();
            fetchLocationStock();
            fetchSales();

        });
    });


    function getProductInfo(productid) {
        $.get(`?module=products&action=getProductInfoAndClients&format=json&productid=${productid}&with_total_stock`, null, function (data) {
            let result = JSON.parse(data);
            // console.log(product);
            if (result.found === 'yes') {
                $('#tbodyPriceList').empty();
                $('#purchase_client_list').empty();
                var track = "";
                if (result.details.trackserialno == 1) {
                    track = 'Yes';
                } else {
                    track = 'No';
                }

                $('.total-stock').text(result.total_stock || 0);
                $('.pname').text(result.details.productname);
                $('.pdepart').text(result.details.departname);
                $('.pcategory').text(result.details.categoryname);
                if (result.details.brand == null) {
                    $('.pbrand').text("-");
                } else {
                    $('.pbrand').text(result.details.brand);
                }
                $('.pvat').text(result.details.vat_rate);
                $('.nonstock').text(result.details.non_stock == 1 ? "Yes" : "No");
                $('.pstrack').text(track);
                $('.pcprice').text(result.details.costprice);
                $('.pqsprice').text(result.details.quicksale_price);

                $('.generic_name').text(result.details.generic_name);
                $('.track_expire_date').text(result.details.track_expire_date == 1 ? "Yes" : "No");
                $('.require_prescription').text(result.details.prescription_required == 1 ? "Yes" : "No");

                $('.product_desc').text(result.details.description);
                $('.unitname').text(result.details.unitname);
                $('.bulkunitname').text(result.details.bulk_unit_name || '');
                $('.productcategory').text(result.details.productcategoryname);
                $('.productsubcategory').text(result.details.subcategoryname);
                $('.product_image').attr('src', result.details.image_path);
                generateBarcode('.barcode-holder', result.details.barcode_manufacture || result.details.barcode_office);

                let count = 1;
                $(result.priceLevel).each(function (index, level) {
                    let row = `<tr>
                                   <td>${count}</td>
                                   <td>${level.level}</td>
                                   <td>${numberWithCommas(level.excamount)}</td>
                                   <td>${result.details.vat_rate}</td>
                                   <td>${numberWithCommas(level.incamount)}</td>
                               </tr>`;
                    $('#tbodyPriceList').append(row);
                    count++;
                });
                //client details
                $(result.clientList).each(function (index, list) {
                    count = parseInt(index) + 1;
                    var tableRow = "<tr>" +
                        "<td>" + count + "</td><td>" + list.clientname + "</td>" +
                        "<td>" + list.receipt_no + "</td>" +
                        "<td>" + list.purchase_date + "</td></tr>";
                    $('#purchase_client_list').append(tableRow);
                });
            }
        });
    }

    function fetchPrices() {
        let branchid = $('#modal-branchid').val();
        let productid = $('.product_id').val();
        let currency_rateid = $('#currencyid').val();
        let currencyname = $('#currencyid').find(':selected').data('currencyname');
        $('#product-view-modal').find('.modal-currencyname').text(currencyname);
        // console.log('modal cr: ', currency_rateid,currencyname);
        let spinner = $('#price-list .branch_loading_spinner');
        let tbody = $('#price-table tbody');
        spinner.show();

        $('#price-list .costprice').text('');
        $('#price-list .quicksale_price').text('');
        tbody.empty();


        $.get(`?module=hierarchics&action=getProductHierarchics&format=json&branchid=${branchid}&productid=${productid}&currency_rateid=${currency_rateid}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'success') {
                let quick_price = result.data[0].inc_quicksale_price ? numberWithCommas(result.data[0].inc_quicksale_price) : 'n/a'
                $('#price-list .quicksale_price').text(quick_price);
                let count = 1;
                $.each(result.data, function (i, item) {
                    let foreign_inc = parseFloat(item.foreign_inc_price) || 0;
                    foreign_inc = foreign_inc > 0 ? numberWithCommas(item.foreign_inc_price) : 'n/a';
                    let row = `<tr>
                                <td>${count}</td>
                                <td>${item.hierarchicname}</td>
                                <td>${item.commission}%</td>
                                <td>${item.target}%</td>
                                <td>${numberWithCommas(item.exc_price)}</td>
                                <td>${item.vat_percent}</td>
                                <td>${numberWithCommas(item.inc_price)}</td>
                                <td>${foreign_inc}</td>
                            </tr>`;
                    tbody.append(row);
                    count++;
                });
            } else {
                let row = `<tr>
                               <td colspan="6" class="text-center text-danger">${result.msg || "Error found"}</td>
                           </tr>`;
                tbody.append(row);
            }
        });
    }

    function fetchLocationStock() {
        let locationId = $('#stockLocation').val();
        let productId = $('.product_id').val();

        let summaryTable = $('.summary-table');
        let detailedTable = $('.detailed-table');

        summaryTable.find('tbody').empty();
        detailedTable.find('tbody').empty();
        //ajax
        $.get('?module=products&action=getProductStockView&format=json&productId=' + productId + '&locationId=' + locationId, null, function (data) {
            let result = JSON.parse(data);
            // console.log(result);
            //summary table
            if (result.found === 'yes') {
                let data = result.data;
                let summaryRow = `<tr>
                                         <td>${data.productname}</td>
                                         <td>${data.in_stock_qty}</td>
                                         <td class="bg-rosepink text-white">${data.held_stock}</td>
                                         <td class="bg-success text-white">${data.available_stock}</td>
                                         <td>${data.pending_order}</td>
                                         <td>${data.pending_sale}</td>
                                         <td class="bg-warning text-white">${data.expecting_stock}</td>
                                       </tr>`;
                summaryTable.find('tbody').append(summaryRow);

                let count = 1;
                if (data.track_expire_date == 1) {
                    detailedTable.closest('.col-md-6').show();
                    $.each(data.batches, function (i, batch) {
                        let detailRow = `<tr>
                                            <td>${count}</td>
                                            <td>${batch.batch_no}</td>
                                            <td>${batch.expire_date}</td>
                                            <td>${batch.expire_remain_days}</td>
                                            <td>${batch.total}</td>
                                         </tr>`;
                        detailedTable.find('tbody').append(detailRow);
                        count++;
                    });
                } else {
                    detailedTable.closest('.col-md-6').hide();
                }
            }
        });
    }

    function fetchSales() {
        let branchid = $('#sale-branch').val();
        let fromdate = $('#sale-fromdate').val();
        let productid = $('.product_id').val();
        let spinner = $('#client-purchase .branch_loading_spinner');
        let tbody = $('#sale-table tbody');

        spinner.show();
        tbody.empty();

        $.get(`?module=products&action=getSaleDetails&format=json&branchid=${branchid}&productid=${productid}&fromdate=${fromdate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.data, function (i, item) {
                    let row = `<tr>
                                <td>${count}</td>
                                <td>
                                    <a href="?module=sales&action=view_invoice&salesid=${item.salesid}">${item.receipt_no}</a>
                                </td>
                                <td>${item.clientname}</td>
                                <td>${item.locationname} - ${item.branchname}</td>
                                <td>${item.quantity}</td>
                                <td>${item.currencyname}</td>
                                <td class="text-right">${numberWithCommas(item.inc_price)}</td>
                                <td>${item.doc}</td>
                            </tr>`;
                    tbody.append(row);
                    count++;
                });
            } else {
                let row = `<tr>
                                <td colspan="6" class="text-center text-danger">${result.msg || "Error found"}</td>
                            </tr>`;
                tbody.append(row);
            }
        });
    }

    function generateBarcode(element, barcodedata) {
        if (barcodedata != "") {
            JsBarcode(element, barcodedata, {
                displayValue: true
            });
        }
    }

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>
