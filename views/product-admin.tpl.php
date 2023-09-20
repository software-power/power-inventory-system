<style media="screen">
    .detail-header {
        font-weight: 550;
        font-size: 12pt;
    }
</style>


<div class="row d-flex justify-content-center">
    <div class="col-md-12 col-lg-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Product: <span class="text-primary"><?= $productDetails['productname'] ?></span></h4>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <a href="?module=products&action=product_add"
                           class="mb-xs mt-xs mr-xs modal-with-zoom-anim btn"><i class="fa fa-plus"></i> Add Product</a>
                        <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </header>
            <div class="panel-body" style="padding: 20px;">
                <input type="hidden" class="product_id">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#product-details"><i class="fa fa-book"></i> Product
                            Details</a></li>
                    <li><a data-toggle="tab" href="#price-list"><i class="fa fa-list"></i> Price List</a></li>
                    <li><a data-toggle="tab" href="#stock-report"><i class="fa fa-database"></i> Stock Report</a></li>
                    <li><a data-toggle="tab" href="#client-purchase"><i class="fa fa-shopping-cart"></i> Sales</a>
                    <li><a data-toggle="tab" href="#product-grn"><i class="fa fa-truck"></i> Grn</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="product-details" class="tab-pane fade in active">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12" style="">
                                        <div class="row mb-md">
                                            <div class="col-md-12">
                                                <h4><i class="fa fa-file"></i> Product Details</h4>
                                            </div>
                                        </div>
                                        <div class="row mb-md">
                                            <input type="hidden" class="pId" value="<?= $productDetails['proid'] ?>">
                                            <div class="col-md-5"><span class="detail-header">Name</span></div>
                                            <div class="col-md-4"><?= $productDetails['productname'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Generic Name</span></div>
                                            <div class="col-md-4"><?= $productDetails['generic_name'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Department</span></div>
                                            <div class="col-md-4"><?= $productDetails['departname'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Category</span></div>
                                            <div class="col-md-4"><?= $productDetails['categoryname'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Brand</span></div>
                                            <div class="col-md-4"><?= empty($productDetails['brand']) ? "-" : $productDetails['brand'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">VAT</span></div>
                                            <div class="col-md-4"><?= $productDetails['categoryname'] ?> <?= $productDetails['vat_rate'] ?>%</div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Non-Stock</span></div>
                                            <div class="col-md-4"><?= $productDetails['non_stock'] ? "Yes" : "No" ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Serial track</span></div>
                                            <div class="col-md-4"><?= $productDetails['trackserialno'] == 1 ? "Track Serial" : "Not Track Serial" ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Track Expire Date</span></div>
                                            <div class="col-md-4 track_expire_date"><?= $productDetails['track_expire_date'] == 1 ? "Yes" : "No" ?></div>
                                        </div>
                                        <? if (CS_PRESCRIPTION_ENTRY) { ?>
                                            <div class="row mb-md">
                                                <div class="col-md-5"><span class="detail-header">Require Prescription</span></div>
                                                <div class="col-md-4"><?= $productDetails['prescription_required'] == 1 ? "Yes" : "No" ?></div>
                                            </div>
                                        <? } ?>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Unit name</span></div>
                                            <div class="col-md-4"><?= $productDetails['unitname'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Bulk Unit name</span></div>
                                            <div class="col-md-4"><?= $productDetails['bulk_unit_name'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Product Category</span></div>
                                            <div class="col-md-4"><?= $productDetails['productcategoryname'] ?></div>
                                        </div>
                                        <div class="row mb-md">
                                            <div class="col-md-5"><span class="detail-header">Product Subcategory</span></div>
                                            <div class="col-md-4"><?= $productDetails['subcategoryname'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-md">
                                    <div class="col-md-9">
                                        <p class="detail-header">
                                            <span>Description</span>
                                            <? if (CS_PRINTING_SHOW_DESCRIPTION) { ?>
                                                <span class="text-xs text-danger">This info will appear while printing eg. order, invoice, etc..</span>
                                            <? } ?>
                                        </p>
                                        <textarea class="form-control" style="font-size: 10pt;"
                                                  readonly><?= $productDetails['description'] ?></textarea>
                                    </div>
                                </div>
                                <div class="row mb-md">
                                    <div class="col-md-9" style="display: flex;justify-content: center;">
                                        <div style="border:1px dashed grey;border-radius: 10px;height: 300px;width: 300px;">
                                            <? if ($productDetails['image_path']) { ?>
                                                <img src="<?= $productDetails['image_path'] ?>" alt="image"
                                                     style="height: 290px;width: 290px;">
                                            <? } else { ?>
                                                <span class="text-muted">No image</span>
                                            <? } ?>
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
                                <select id="branchid" onchange="fetchPrices()">
                                    <? foreach ($branches as $b) { ?>
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
                                <p>Exclusive Costprice: <span class="text-success text-weight-bold costprice"></span></p>
                                <p>Quick sale price Inclusive: <span class="text-success text-weight-bold quicksale_price"></span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mt-md">
                                <h4><i class="fa fa-money"></i> Price List</h4>
                                <table id="price-table" class="table table-hover table-bordered mb-none" style="font-size:13px;">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Level</th>
                                        <th>Commission</th>
                                        <th>Target</th>
                                        <th>Exclusive Amount</th>
                                        <th>VAT%</th>
                                        <th>Inclusive Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="stock-report" class="tab-pane fade">
                        <div class="row mt-md" style="min-height: 300px">
                            <div class="col-md-12">
                                <h4><i class="fa fa-home"></i> Stock Report</h4>
                            </div>
                            <div class="col-md-12" style="display: flex;align-items: center;">
                                <span class="mr-md">Location:</span>
                                <select id="stockLocation" onchange="fetchLocationStock()">
                                    <? foreach ($locations as $index => $l) { ?>
                                        <option value="<?= $l['id'] ?>"><?= $l['name'] ?> - <?= $l['branchname'] ?></option>
                                    <? } ?>
                                </select>
                                <span class="ml-sm">Total Stock: <span class="text-weight-semibold text-rosepink total-stock"><?=$total_stock?></span></span>
                                <span class="loading_spinner" style="display: none;">
                            <object data="images/loading_spinner.svg" type="image/svg+xml" height="40"
                                    width="40"></object>
                        </span>
                            </div>
                            <div class="col-md-7">
                                <h4>Summary</h4>
                                <table class="table table-hover mb-none table-bordered summary-table" style="font-size:9pt;"
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
                                <div class="d-flex justify-content-end mt-sm">
                                    <button id="expecting-stock-btn" class="btn btn-sm btn-primary" style="display: none"
                                            title="view expecting stock info" data-toggle="modal" data-target="#supplier-info-modal"
                                            data-productid="<?= $productDetails['proid'] ?>">
                                        View Info
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-5">
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
                    <div id="client-purchase" class="tab-pane fade">
                        <h4>Sales</h4>
                        <div class="row">
                            <div class="col-md-3 d-flex align-items-center">
                                Branch:
                                <select id="sale-branch" class="form-control" onchange="fetchSales()">
                                    <? foreach ($branches as $b) { ?>
                                        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                    <? } ?>
                                </select>
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="sale-table" class="table table-hover table-bordered mb-md"
                                           style="font-size:13px;">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Invoice no</th>
                                            <th>Client Name</th>
                                            <th>Location</th>
                                            <th>Sales Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Sales Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="product-grn" class="tab-pane fade">
                        <h4>Purchase Summary</h4>
                        <div class="row">
                            <div class="col-md-3 d-flex align-items-center">
                                From:
                                <input id="purchase-fromdate" type="date" class="form-control" value="<?= date('Y-m-d', strtotime('-3 months')) ?>"
                                       onchange="fetchPurchase()">
                            </div>
                            <div class="col-md-3 d-flex align-items-center">
                                <div class="purchase_loading_spinner" style="display: none">
                                    <object data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"></object>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="purchase-table" class="table table-hover table-bordered mb-none"
                                           style="font-size:13px;">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Supplier Name</th>
                                            <th>GRN no</th>
                                            <th>Location</th>
                                            <th>Currency</th>
                                            <th>Purchase Quantity</th>
                                            <th>Purchase Rate</th>
                                            <th>Purchase Date</th>
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
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="supplier-info-modal" role="dialog" aria-labelledby="supplier-info-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Expecting Stock</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" style="font-size: 9pt">
                    <thead class="text-weight-bold">
                    <tr>
                        <td>#</td>
                        <td>LPO no</td>
                        <td>Supplier</td>
                        <td>Qty</td>
                        <td>Expecting In</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>Bariki</td>
                        <td>500</td>
                        <td>3 days</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>

    $(function () {
        generateBarcode(".barcode-holder", `<?= $productDetails['barcode_manufacture'] ?: $productDetails['barcode_office'] ?>`);
        $('#sale-table').dataTable();
        $('#purchase-table').dataTable();
        fetchPrices();
        fetchLocationStock();
        fetchSales();
        fetchPurchase();
    });

    function fetchPrices() {
        let branchid = $('#branchid').val();
        let productid = $('.pId').val();
        let spinner = $('#price-list .branch_loading_spinner');
        let tbody = $('#price-table tbody');
        spinner.show();

        $('#price-list .costprice').text('');
        $('#price-list .quicksale_price').text('');
        tbody.empty();


        $.get(`?module=hierarchics&action=getProductHierarchics&format=json&branchid=${branchid}&productid=${productid}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'success') {
                let costprice = result.data[0].costprice ? numberWithCommas(result.data[0].costprice) : 'n/a';
                let quick_price = result.data[0].inc_quicksale_price ? numberWithCommas(result.data[0].inc_quicksale_price) : 'n/a';
                $('#price-list .costprice').text(costprice);
                $('#price-list .quicksale_price').text(quick_price);
                let count = 1;
                $.each(result.data, function (i, item) {
                    let row = `<tr>
                                <td>${count}</td>
                                <td>${item.hierarchicname}</td>
                                <td>${item.level}</td>
                                <td>${item.commission}%</td>
                                <td>${item.target}%</td>
                                <td>${numberWithCommas(item.exc_price)}</td>
                                <td>${item.vat_percent}</td>
                                <td>${numberWithCommas(item.inc_price)}</td>
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
        let productId = $('.pId').val();

        let trackExpireDate = $('.track_expire_date').text();

        let loadingSpinner = $('.loading_spinner');
        let summaryTable = $('.summary-table');
        let detailedTable = $('.detailed-table');

        summaryTable.find('tbody').empty();
        detailedTable.find('tbody').empty();
        loadingSpinner.show();
        //ajax
        $.get('?module=products&action=getProductStockView&format=json&productId=' + productId + '&locationId=' + locationId, null, function (data) {
            let result = JSON.parse(data);
            // console.log(result);
            loadingSpinner.hide();

            //summary table
            if (result.found === 'yes') {
                let data = result.data;
                if ((parseInt(data.expecting_stock) || 0) > 1) {
                    $('#expecting-stock-btn').show();
                } else {
                    $('#expecting-stock-btn').hide();
                }
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
                // console.log('expire date', trackExpireDate);
                if (trackExpireDate == "Yes") {
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

    $('#supplier-info-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        let productid = $(source).data('productid');
        let locationid = $('#stockLocation').val();
        let tbody = $(modal).find('tbody');
        $(tbody).empty();
        $.get(`?module=products&action=expectingStocks&format=json&productid=${productid}&locationid=${locationid}`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.data, function (i, item) {
                    let row = `<tr>
                                    <td>${count}</td>
                                    <td>
                                        <a href="?module=grns&action=lpo_list&search%5Blpo%5D=${item.lpoid}">${item.lpoid}</a>
                                    </td>
                                    <td>${item.suppliername}</td>
                                    <td>${item.qty}</td>
                                    <td class="${item.time_passed == '1' ? 'text-danger' : ''}">${item.expecting_in}</td>
                                </tr>`;
                    $(tbody).append(row);
                    count++;
                });
            } else {
                triggerError(result.msg || 'Error');
            }
        });
    });

    function fetchSales() {
        let branchid = $('#sale-branch').val();
        let fromdate = $('#sale-fromdate').val();
        let productid = $('.pId').val();
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
                                <td>${item.currencyname} ${numberWithCommas(item.inc_price)}</td>
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

    function fetchPurchase() {
        let fromdate = $('#purchase-fromdate').val();
        let productid = $('.pId').val();
        let spinner = $('#product-grn .purchase_loading_spinner');
        let tbody = $('#purchase-table tbody');

        spinner.show();
        tbody.empty();


        $.get(`?module=products&action=getPurchaseHistory&format=json&productid=${productid}&fromdate=${fromdate}`, null, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'success') {
                let count = 1;
                $.each(result.data, function (i, item) {
                    let row = `<tr>
                                <td>${count}</td>
                                <td>${item.supplierName}</td>
                                <td>
                                    <a href="?module=grns&action=view_grn&grn=${item.grnno}">${item.grnno}</a>
                                </td>
                                <td>${item.locationname} - ${item.branchname}</td>
                                <td>${item.currencyname}</td>
                                <td>${item.quantity}</td>
                                <td>${numberWithCommas(item.price)}</td>
                                <td>${item.doc}</td>
                            </tr>`;
                    tbody.append(row);
                    count++;
                });
            } else {
                let row = `<tr>
                                <td colspan="9" class="text-center text-danger">${result.msg || "Error found"}</td>
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
