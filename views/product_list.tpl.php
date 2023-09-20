<style media="screen">
    .input-group-rounded input.form-control:first-child, .input-group-rounded input.form-control:last-child, .input-search input.form-control:first-child, .input-search input.form-control:last-child {
        border-radius: 0;
        height: 44px;
        font-size: 15px;
    }

    .table {
        width: 100%;
        font-size: 15px;
    }

    .table .actions a:hover, .table .actions-hover a {
        color: #ffffff;
    }

    .table .actions a:hover, .table .actions-hover a:hover {
        color: #ffffff;
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

    .center-panel {
        margin: 0 auto;
        width: 83%;
    }

    .productpricelist {
        position: fixed;
        z-index: 99;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5803921568627451);
        display: none;
        overflow: hidden;
        overflow-y: scroll;
    }

    .productpricelist .table-holder {
        position: relative;
        top: 129px;
        background: #ffffff;
        width: 70%;
        margin: 0 auto;
        PADDING: 20PX;
        /* margin-left: 121px; */
    }

    .productpricelist .title-model {
        padding-top: 4px;
        float: left;
        margin-left: 26px;
    }

    .productpricelist .close-btn-holder {
        padding-top: 4px;
        width: 73px;
        float: right;
    }

    .product_details {
        width: 100%;
        float: left;
    }

    .pro-details, .pro-barcode {
        float: left;
    }

    .pro-details {
        width: 70%;
    }

    .pro-barcode {
        width: 30%;
    }

    .pro-container .pro-name, .pro-value {
        padding: 5px;
    }

    .pro-container .pro-name {
        font-size: 16px;
        float: left;
        width: 40%;
        font-weight: 600;
    }

    .pro-container .pro-value {
        float: left;
        width: 60%;
    }

    .canvas-holder {
        width: 50%;
        float: left;
    }

    .canvas-holder .barcode-name {
        display: block;
    }

    .category_header {
        font-size: 15px;
        font-weight: 600;
        text-transform: capitalize;
        margin-left: 5px;
    }

    .category_header:before {
        content: '';
        width: 77%;
        display: inline-block;
        border: 1px solid #ecedf0;
        position: absolute;
        right: 0;
        margin-top: 12px;
    }

    .button_tbl {
        width: 51%;
        height: 43px;
        margin: auto;
    }

    .stock_details_holder {
        display: none;
    }

    .button_tbl .btn-primary {
        background: transparent;
        color: #0088cc;
        text-shadow: none;
    }

    .client_details_holder {
        display: none;
    }

    .product-descption {
        width: 100%;
        padding: 10px;
        float: left;
    }

    .detail-header {
        font-weight: 550;
        font-size: 12pt;
    }


</style>
<header class="page-header">
    <h2>Products</h2>
</header>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">List of Products</h2>
                    <small>Total products: <?=$product_count?></small>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <? if (Users::can(OtherRights::add_product)) { ?>
                        <a href="?module=products&action=product_add" class="btn btn-default"> <i class="fa fa-plus"></i> Add Product</a>
                    <? } ?>
                    <a class="btn" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="row mb-md pb-md" style="border-bottom: 1px solid #dadada;">
                <div class="col-md-12 d-flex justify-content-around align-items-center">
                    <? if ($product_count <= 150) { ?>
                        <a href="<?= url('products', 'product_index') ?>" class="circle-link">All</a>
                    <? } ?>
                    <a href="<?= url('products', 'product_index', ['start_char' => '#']) ?>"
                       class="circle-link <?= '#' == $start_char ? 'active' : '' ?>">#</a>
                    <? foreach (range('A', 'Z') as $item) { ?>
                        <a href="<?= url('products', 'product_index', ['start_char' => $item]) ?>"
                           class="circle-link <?= $item == $start_char ? 'active' : '' ?>"><?= $item ?></a>
                    <? } ?>
                </div>
            </div>
            <form class="pb-lg" style="border-bottom: 1px solid #dadada;">
                <input type="hidden" name="module" value="products">
                <input type="hidden" name="action" value="product_index">
                <div class="row mb-md d-flex justify-content-center">
                    <div class="col-md-4 d-flex align-items-center">
                        <span>Search:</span>
                        <input type="text" name="search" minlength="3" class="form-control"
                               placeholder="Major product search name or barcode or description" value="<?= $search ?>"
                               style="border-radius: 5px;">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-search"></i> SEARCH</button>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-3">
                                Brand:
                                <select id="brandid" class="form-control" name="brandid">
                                    <option value="">-- Brand --</option>
                                    <? foreach ($brands as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Product Category:
                                <select id="productcategoryid" class="form-control" name="productcategoryid">
                                    <option value="">-- Category --</option>
                                    <? foreach ($productcategories as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Product Subcategory:
                                <select id="subcategoryid" class="form-control" name="subcategoryid">
                                    <option value="">-- Subcategory --</option>
                                    <? foreach ($subcategories as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Department:
                                <select id="departmentid" class="form-control" name="departmentid">
                                    <option value="">-- Department --</option>
                                    <? foreach ($departments as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                Unit:
                                <select id="unitid" class="form-control" name="unitid">
                                    <option value="">-- Unit --</option>
                                    <? foreach ($units as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Bulk Unit:
                                <select id="bulkunitid" class="form-control" name="bulkunitid">
                                    <option value="">-- Unit --</option>
                                    <? foreach ($bulkunits as $d) { ?>
                                        <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Tax:
                                <select class="form-control" name="taxcategory">
                                    <option value="">-- All --</option>
                                    <? foreach ($categories as $c) { ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?> <?= $c['vat_percent'] ?>%</option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                Non-Stock Products:
                                <select class="form-control" name="non_stock">
                                    <option value="">-- All --</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <p class="text-primary"><?= $title ?></p>

            <div class="table-responsive mt-xlg">
                <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Code</th>
                        <th>Name</th>
                        <? if (CS_SHOW_GENERIC_NAME) { ?>
                            <th>Generic name</th>
                        <? } ?>
                        <th>Description</th>
                        <th>Non-Stock</th>
                        <th>Department</th>
                        <th>Brand</th>
                        <th>VAT %</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Unit</th>
                        <th>Bulk Unit</th>
                        <th>Manufacture Barcode</th>
                        <th>Other Barcode</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $USER_CAN_ADMIN_VIEW = Users::can(OtherRights::admin_view);
                    $USER_CAN_EDIT = Users::can(OtherRights::edit_product);
                    $USER_CAN_EDIT_PRICE = Users::can(OtherRights::edit_price);
                    $USER_CAN_UPLOAD_IMAGE = Users::can(OtherRights::upload_product_image);
                    $USER_CAN_PRINT_BARCODE = Users::can(OtherRights::print_barcode);
                    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

                    foreach ($product as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $id + 1 ?></td>
                            <td><?= $R['productid'] ?></td>
                            <td style="width: 15%;"><?= $R['name'] ?></td>
                            <? if (CS_SHOW_GENERIC_NAME) { ?>
                                <td style="width: 15%;"><?= $R['generic_name'] ?></td>
                            <? } ?>
                            <td><?= $R['description'] ?></td>
                            <td><?= $R['non_stock'] ? 'Yes' : 'No' ?></td>
                            <td><?= $R['departmentName'] ?></td>
                            <td><?= $R['brandName'] ?></td>
                            <td>
                                    <small title="<?=$R['categoryName']?> <?= $R['vatPercent'] ?>%"><?= implode(' ', array_slice(explode(' ', $R['categoryName']), 0, 2)) ?> <?= $R['vatPercent'] ?>%</small>
                                <? if (CS_VFD_TYPE == VFD_TYPE_ZVFD && $R['taxcode']) { ?>
                                    <small class="text-muted"><i>taxcode: <?= $R['taxcode'] ?></i></small>
                                <? } ?>
                            </td>
                            <td><?= $R['productcategory'] ?></td>
                            <td><?= $R['productsubcategory'] ?></td>
                            <td><?= $R['unitname'] ?> (<?= $R['unitabbr'] ?>)</td>
                            <td><?= $R['bulk_unit_name'] ?> (<?= $R['bulk_unit_abbr'] ?>)</td>
                            <td><?= $R['barcode_manufacture'] ?></td>
                            <td><?= $R['barcode_office'] ?></td>
                            <td><?= $R['status'] ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <? if ($USER_CAN_EDIT) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('products', 'product_edit', 'id=' . $R['productid'] . '&redirect=' . base64_encode($current_url)) ?>"
                                               title="Edit"><i class="fa-pencil fa"></i> Edit Product</a>
                                        <? } ?>
                                        <? if ($USER_CAN_ADMIN_VIEW) { ?>
                                            <a class="dropdown-item" target="_blank"
                                               href="<?= url('products', 'product_admin', 'productid=' . $R['id']) ?>"
                                               title="Admin View"><i class="fa-arrow-circle-right fa"></i> Admin View</a>
                                        <? } ?>
                                        <? if ($USER_CAN_EDIT_PRICE) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('hierarchics', 'product_hierarchics', [
                                                   'productid' => $R['id'],
                                                   'redirect' => base64_encode($current_url)]) ?>"
                                               title="Edit Prices"><i class="fa-dollar fa"></i> Edit Prices</a>
                                        <? } ?>
                                        <a data-productid="<?= $R['productid'] ?>" class="dropdown-item"
                                           href="#product-view-modal" title="Product view" data-toggle="modal">
                                            <i class="fa-eye fa"></i> Product View</a>
                                        <? if ($USER_CAN_UPLOAD_IMAGE) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('products', 'image_upload', ['id' => $R['id']]) ?>"
                                               title="Edit Prices"><i class="fa fa-picture-o"></i> Upload Image</a>
                                        <? } ?>
                                        <? if ($USER_CAN_PRINT_BARCODE && !$R['non_stock']) { ?>
                                            <a class="dropdown-item"
                                               href="<?= url('products', 'generate_barcode', ['productid' => $R['id']]) ?>"
                                               title="Edit Prices"><i class="fa fa-barcode"></i> Print Barcode</a>
                                        <? } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?= component('shared/product_view_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">


    $(function () {
        $('#departmentid,#brandid,#productcategoryid,#subcategoryid,#unitid,#bulkunitid').select2({width: '100%'});
    });
</script>
