<style media="screen">
    .center-panel {
        width: 70%;
        margin: 0 auto;
    }

    .hierarchic-holder {
        padding: 10px;
        border-bottom: 1px solid;
        background: #ecedf0;
        margin-top: 10px;
    }

    .hierarchic-header {
        margin-top: 10px;
    }

    .for-product-save {
        margin-top: 11px;
    }

    .hierarchic-header label {
        font-size: 16px;
    }

    .for-row {
        padding: 5px;
    }

    .hide-scroll {
        display: block;
        width: 15px;
        height: 21px;
        background: #fdfdfd;
        position: absolute;
        top: 6px;
        right: 24px;
    }

    .border-danger {
        box-shadow: 0 0 3px red !important;
        border: 1px solid red !important;
    }
</style>
<header class="page-header">
    <h2><? if ($edit) echo 'Edit'; else echo 'Add'; ?> Product</h2>
</header>
<div class="row">
    <div class="col-lg-12">
        <form id="form" class="form-horizontal form-bordered" method="post"
              action="<?= url('products', 'product_save') ?>">
            <input type="hidden" name="redirect" value="<?= $_GET['redirect'] ?>">
            <section class="panel center-panel" style="position: relative;">
                <ul class="nav nav-pills nav-stacked" style="position: absolute;right: -96px;top: 8px;">
                    <li>
                        <button type="submit" class="btn btn-primary submit" title="Save Product">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </li>
                    <li class="mt-md">
                        <a href="<?= isset($_GET['redirect']) ? base64_decode($_GET['redirect']) : "?module=products&action=product_index" ?>"
                           title="Back to list"
                           class="btn btn-link btn-success"><i class="fa fa-arrow-left"></i> Back</a>
                    </li>
                </ul>
                <header class="panel-heading">
                    <h2 class="panel-title">Product Details</h2>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4 input-holder">
                            <div class="col-md-12 d-flex align-items-center">
                                <h5>Manufacture Barcode <small class="text-danger error-msg" style="display: none">Barcode already
                                        exists</small></h5>
                                <object class="barcode_spinner" data="images/loading_spinner.svg"
                                        type="image/svg+xml" height="30" width="30" style="display:none "></object>
                            </div>
                            <div class="col-md-12">
                                <input placeholder="Manufacture Barcode" type="text" class="form-control" <?= $product ? '' : 'autofocus' ?>
                                       name="product[barcode_manufacture]" value="<?= $product['barcode_manufacture'] ?>"
                                       onkeyup="checkManufactureBarcode(this)" onchange="checkManufactureBarcode(this)">
                            </div>
                        </div>
                        <div class="col-md-4 input-holder">
                            <div class="col-md-12 d-flex align-items-center">
                                <h5>Other Barcode <small class="text-danger error-msg" style="display: none">Barcode already exists</small>
                                </h5>
                                <object class="barcode_spinner" data="images/loading_spinner.svg"
                                        type="image/svg+xml" height="30" width="30" style="display:none "></object>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="product[barcode_office]"
                                       value="<?= $product['barcode_office'] ?>" onkeyup="checkOfficeBarcode(this)"
                                       onchange="checkOfficeBarcode(this)"
                                       placeholder="<?= date('Ymd') . str_pad("{ID}", 6, "0", STR_PAD_LEFT) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row addproduct mb-md">
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Name <span id="productexist"></span></h5>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="hidden" name="id" class="productid" value="<?= $product['id'] ?>">
                                        <input type="text" required class="border form-control productname" id="name"
                                               title="Name is required" placeholder="Product name" name="product[name]"
                                               value="<?= $product['name'] ?>" onchange="verifyProduct(this)"
                                               onkeyup="makediscription(this)">
                                        <small class="text-danger character-error" style="display: none">avoid '"/\<>?</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Generic Name</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="text" class="border form-control generic_name"
                                               placeholder="Generic name"
                                               name="product[generic_name]" value="<?= $product['generic_name'] ?>"
                                               onkeyup="makediscription(this)" onchange="checkProductName(this)">
                                        <small class="text-danger character-error" style="display: none">avoid '"/\<>?</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Status</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select name="product[status]" class="form-control">
                                            <option disabled>--Choose Status--</option>
                                            <option selected
                                                    value="active" <?= selected($product['status'], 'active') ?>>Active
                                            </option>
                                            <option value="inactive" <?= selected($product['status'], 'inactive') ?>>
                                                In-Active
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 brand">
                                    <div class="col-md-12">
                                        <div class="col-md-12 d-flex align-items-center">
                                            <h5>Brand</h5>
                                            <button type="button" class="btn btn-primary btn-sm ml-sm"
                                                    title="quick add department"
                                                    onclick="quickAddBrand(this)">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-12 new_brand mt-sm mb-sm"
                                             style="padding:0;display:none ;">
                                            <div class="col-md-10"
                                                 style="padding:0;position: relative">
                                                <div class="loading_spinner"
                                                     style="position: absolute;top:5px;right: 10px;display:none ;">
                                                    <object data="images/loading_spinner.svg"
                                                            type="image/svg+xml" height="30"
                                                            width="30"></object>
                                                </div>
                                                <input type="text" class="form-control"
                                                       placeholder="brand name">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="saveBrand(this)"
                                                        title="Quick add brand"
                                                        class="btn btn-success btn-xs ml-sm">Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="brand" class="form-control" required name="product[modelid]">
                                            <? if ($model) { ?>
                                                <option value="<?= $model['id'] ?>"><?= $model['name'] ?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 productcategory">
                                    <div class="col-md-12">
                                        <div class="col-md-12 d-flex align-items-center">
                                            <h5>Product Category</h5>
                                            <button type="button" class="btn btn-primary btn-sm ml-sm"
                                                    title="quick add product category"
                                                    onclick="quickAddProductCategory(this)">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-12 new_product_category mt-sm mb-sm"
                                             style="padding:0;display:none ;">
                                            <div class="col-md-10"
                                                 style="padding:0;position: relative">
                                                <div class="loading_spinner"
                                                     style="position: absolute;top:5px;right: 10px;display:none ;">
                                                    <object data="images/loading_spinner.svg"
                                                            type="image/svg+xml" height="30"
                                                            width="30"></object>
                                                </div>
                                                <input type="text" class="form-control"
                                                       placeholder="category name">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="saveProductCategory(this)"
                                                        title="Quick add product category"
                                                        class="btn btn-success btn-xs ml-sm">Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="productCategory" class="form-control" name="product[productcategoryid]" required
                                                onchange="fetchSubcategories(this)">
                                            <? if ($productCategory) { ?>
                                                <option value="<?= $productCategory['id'] ?>"><?= $productCategory['name'] ?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12 d-flex justify-content-center align-items-center">
                                        <h5>Subcategory <span id="productexist"></span></h5>
                                        <a href="?module=product_categories&action=subcategories" target="_blank"
                                           class="btn btn-primary btn-sm ml-sm" title="add supplier">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        <button type="button" onclick="fetchSubcategories('#productCategory')"
                                                class="btn btn-xs ml-sm text-success" title="refresh"><i
                                                    class="fa fa-refresh"></i></button>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="subcategories" class="form-control" name="product[subcategoryid]" required>
                                            <? if ($productSubCategory) { ?>
                                                <option value="<?= $productSubCategory['id'] ?>"><?= $productSubCategory['name'] ?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-4 unit">
                                    <div class="col-md-12 d-flex align-items-center">
                                        <h5>Unit <span id="productexist"></span></h5>
                                        <a href="?module=units&action=index" target="_blank"
                                           class="btn btn-primary btn-sm ml-sm" title="add supplier">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="units" class="form-control units" required name="product[unit]"
                                                onchange="checkbulk(this)">
                                            <? if ($unit) { ?>
                                                <option value="<?= $unit['id'] ?>"><?= $unit['name'] ?> (<?= $unit['abbr'] ?>)</option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12 d-flex justify-content-center align-items-center">
                                        <h5>Bulk Unit <span id="productexist"></span></h5>
                                        <a href="?module=bulk_units&action=index" target="_blank"
                                           class="btn btn-primary btn-sm ml-sm" title="add supplier">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        <button type="button" onclick="checkbulk('#units')"
                                                class="btn btn-xs ml-sm text-success" title="refresh"><i
                                                    class="fa fa-refresh"></i></button>
                                    </div>
                                    <div class="col-md-12">
                                        <select class="form-control bulk_units" name="product[bulk_units]">
                                            <? if ($bulk_unit) { ?>
                                                <option value="<?= $bulk_unit['id'] ?>"><?= $bulk_unit['name'] ?> (<?= $bulk_unit['rate'] ?>
                                                    )
                                                </option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 department">
                                    <div class="col-md-12">
                                        <div class="col-md-12 d-flex align-items-center">
                                            <h5>Department</h5>
                                            <button type="button" class="btn btn-primary btn-sm ml-sm"
                                                    title="quick add department"
                                                    onclick="quickAddDepartment(this)">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-12 new_department mt-sm"
                                             style="padding:0;display:none ;">
                                            <div class="col-md-10"
                                                 style="padding:0;position: relative">
                                                <div class="loading_spinner"
                                                     style="position: absolute;top:5px;right: 10px;display:none ;">
                                                    <object data="images/loading_spinner.svg"
                                                            type="image/svg+xml" height="30"
                                                            width="30"></object>
                                                </div>
                                                <input type="text" class="form-control"
                                                       placeholder="department name">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="saveDepartment(this)"
                                                        title="Quick add department"
                                                        class="btn btn-success btn-xs ml-sm">Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="department" class="form-control" required name="product[departid]">
                                            <? if ($department) { ?>
                                                <option value="<?= $department['id'] ?>"><?= $department['name'] ?></option>
                                            <? } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-md">
                                <? if (CS_VFD_TYPE == VFD_TYPE_ZVFD) { ?>
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <label class="d-flex align-items-center mb-sm" title="check if tax exempted product" style="cursor: pointer;">
                                                <input id="exempted-checkbox" name="zvfd_exempted" type="checkbox" <?=$product['taxcode']?'checked':''?>
                                                       style="height: 20px;width: 20px;" onchange="check_percent()">
                                                <span class="ml-xs">Exempted?</span>
                                            </label>
                                            <input id="taxcode" type="text" class="form-control" name="product[taxcode]" title="zrb taxcode"
                                                   value="<?= $product['taxcode'] ?>" placeholder="zrb taxcode">
                                        </div>
                                    </div>
                                <? } else { ?>
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <h5>VAT Category</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <select id="taxcategoryid" class="form-control" required name="product[categoryid]">
                                                <option selected disabled>--Choose VAT Category--</option>
                                                <? foreach ($categoryList as $d) { ?>
                                                    <? if (empty($product['id'])) { ?>
                                                        <option value="<?= $d['id'] ?>" <?= selected($d['id'], CS_DEFAULT_TAX) ?>
                                                                data-percent="<?= $d['vat_percent'] ?>">
                                                            <?= $d['name'] ?> &nbsp; (<?= $d['vat_percent'] ?>%)
                                                        </option>
                                                    <? } elseif (!empty($product['id'])) { ?>
                                                        <option value="<?= $d['id'] ?>" <?= selected($d['id'], $product['categoryid']) ?>
                                                                data-percent="<?= $d['vat_percent'] ?>">
                                                            <?= $d['name'] ?> &nbsp; (<?= $d['vat_percent'] ?>%)
                                                        </option>
                                                    <? } else { ?>
                                                        <option value="<?= $d['id'] ?>" data-percent="<?= $d['vat_percent'] ?>">
                                                            <?= $d['name'] ?> &nbsp; (<?= $d['vat_percent'] ?>%)
                                                        </option>
                                                    <? } ?>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                <? } ?>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Base sale Percentage</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <input type="number" class="form-control" id="baseprice"
                                               title="Base sale price is required" style="width:100%"
                                               name="product[baseprice]" min="0"
                                               value="<?= $product['baseprice'] ?? CS_DEFAULT_BASE ?>">
                                        <span class='hide-scroll'></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Product Points</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <input type="number" required class="form-control" id="baseprice"
                                               title="Product points" style="width:100%"
                                               name="product[points]" min="1" onwheel="event.preventDefault()"
                                               value="<?= $product['points'] ?? '1' ?>">
                                        <span class='hide-scroll'></span>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-md">
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Allow Quick Sale</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select name="product[forquick_sale]" class="form-control">
                                            <option selected disabled>--Choose Option--</option>
                                            <option value="1" <?= $product['forquick_sale'] ? selected($product['forquick_sale'], '1') : 'selected' ?>>
                                                Yes
                                            </option>
                                            <option value="0" <?= selected($product['forquick_sale'], '0') ?>>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Export in Price List</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select name="product[price_list]" class="form-control">
                                            <option value="1" <?= $product['price_list'] ? selected($product['price_list'], '1') : 'selected' ?>>
                                                Yes
                                            </option>
                                            <option value="0" <?= selected($product['price_list'], '0') ?>>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Website</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select name="product[website]" class="form-control">
                                            <option value="1" <?= $product['website'] ? selected($product['website'], '1') : 'selected' ?>>
                                                Yes
                                            </option>
                                            <option value="0" <?= selected($product['website'], '0') ?>>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <h5>
                                            <span>Product Description</span>
                                            <? if (CS_PRINTING_SHOW_DESCRIPTION) { ?>
                                                <small class="text-danger text-weight-bold">This info will appear while printing eg. order,
                                                    invoice,
                                                    etc..</small>
                                            <? } ?>
                                        </h5>
                                    </div>
                                    <div class="col-md-12">
                                        <textarea placeholder="Write product description here...." class="form-control description"
                                                  <?= CS_PRINTING_SHOW_DESCRIPTION ? 'required' : '' ?>
                                                  name="product[description]" rows="2" onchange="checkProductName(this)"
                                                  onkeyup="checkProductName(this)"><?= $product['description'] ?></textarea>
                                        <small class="text-danger character-error" style="display: none">avoid '"/\<>?</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-4" title="if product don't maintain stock">
                                    <div class="col-md-12">
                                        <h5>Non-Stock Item</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="non-stock" class="form-control" required name="product[non_stock]"
                                                onchange="configureNonStock(true)">
                                            <option <?= selected($product['non_stock'], 0) ?> value="0">No</option>
                                            <option <?= selected($product['non_stock'], 1) ?> value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <? if (CS_PRESCRIPTION_ENTRY) { ?>
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <h5>Prescription Required</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <select name="product[prescription_required]" class="form-control">
                                                <option value="1" <?= selected($product['prescription_required'], 1) ?>>
                                                    Yes
                                                </option>
                                                <option value="0" <?= selected($product['prescription_required'], 0) ?>>
                                                    No
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                <? } else { ?>
                                    <div class="col-md-4" style="display: none">
                                        <div class="col-md-12">
                                            <h5>Prescription Required</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="hidden" name="product[prescription_required]" value="0">
                                        </div>
                                    </div>
                                <? } ?>
                                <? if (CS_REORDER_LEVEL) { ?>
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <h5>Reorder Level</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <select name="product[reorder_level]" class="form-control reorder_level">
                                                <option value="1" <?= selected($product['reorder_level'], 1) ?>>
                                                    Yes
                                                </option>
                                                <option value="0" <?= selected($product['reorder_level'], 0) ?>>No
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                            <div class="row mt-md track_expire">
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Track Expire Date.</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select name="product[track_expire_date]" class="form-control track_expire_date"
                                                onchange="enableExpireNotification(this)"
                                                <? if ($product){ ?>data-old="<?= $product['track_expire_date'] ?>"<? } ?>>
                                            <option value="1" <?= selected($product['track_expire_date'], 1) ?>> Yes</option>
                                            <option value="0" <?= selected($product['track_expire_date'], 0) ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="expire-notification" class="col-md-8" style="display: none">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <h5>Get Expiry Notifications</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <select name="product[expiry_notification]" class="form-control expiry_notification"
                                                    onchange="enableNotificationDays(this)">
                                                <option value="1" <?= selected($product['expiry_notification'], 1) ?>> Yes</option>
                                                <option value="0" <?= selected($product['expiry_notification'], 0) ?>> No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6"
                                         title="Days before expiring for start receiving notifications">
                                        <div class="col-md-12">
                                            <h5>Notify Before Days</h5>
                                        </div>
                                        <div class="col-md-12">
                                            <input type="number" class="form-control notify_before_days"
                                                   placeholder="days" min="0" onwheel="event.preventDefault()"
                                                   name="product[notify_before_days]"
                                                   value="<?= $product['notify_before_days'] ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-md track_serialno">
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <h5>Track Serial No</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="trackserialno" name="product[trackserialno]" class="form-control"
                                                onchange="configureTracking()">
                                            <option value="1" <?= selected($product['trackserialno'], 1) ?>>Yes</option>
                                            <option value="0" <?= selected($product['trackserialno'], 0) ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4"
                                     title="If serialno should be validated when selling, this requires adding serialno first before selling">
                                    <div class="col-md-12">
                                        <h5>Validate Serial No</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <select id="validate_serialno" name="product[validate_serialno]"
                                                class="form-control">
                                            <option value="1" <?= selected($product['validate_serialno'], 1) ?>>Yes
                                            </option>
                                            <option value="0" <?= selected($product['validate_serialno'], 0) ?>>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4"
                                     title="Product warranty in months">
                                    <div class="col-md-12">
                                        <h5>Warrant in Months</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <input id="warrant_month" type="text" name="product[warrant_month]" class="form-control"
                                               placeholder="warrant months"
                                               value="<?= $product['warrant_month'] ? $product['warrant_month'] : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
        </form>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>

    let MANUFACTURE_BARCODE_OK = true;
    let OFFICE_BARCODE_OK = true;
    let PRODUCT_OK = true;

    $(function () {
        initSelectAjax("#units", "?module=units&action=getUnits&format=json", 'choose unit', 2);
        initSelectAjax("#department", "?module=departments&action=getDepartments&format=json", 'choose department');
        initSelectAjax("#brand", "?module=model&action=getModels&format=json", 'choose brand');
        initSelectAjax("#productCategory", "?module=product_categories&action=getCategories&format=json", 'choose category', 2);

        check_percent();
        enableExpireNotification('.track_expire_date');
        enableNotificationDays('.expiry_notification');
        configureTracking();
        configureNonStock();

        disable_scroll();
        // $('.submit').addClass("disabled");
    });


    function check_percent() {
        let percent = parseFloat($('#taxcategoryid :selected').data('percent'));
        // console.log(percent);
        if ($('#exempted-checkbox').is(':checked')) {
            $('#taxcode').prop('required',true).prop('readonly',false);
        }else {
            $('#taxcode').prop('required',false).prop('readonly',true);
        }
    }

    function enableExpireNotification(obj) {
        if ($(obj).val() == 1) {
            $('#expire-notification').show();
            $('.track_serialno').find('select').val(0).trigger('change');
            $('#non-stock').val(0);
        } else {
            $('#expire-notification').hide();
        }

        <?if($product){?>

        let old = $(obj).data('old');
        if (old != $(obj).val()) {
            // triggerError('changed');
        }

        <?}?>
    }

    function enableNotificationDays(obj) {
        let days = $('.notify_before_days');
        if ($(obj).val() == 1) {
            $(days).focus();
            $(days).prop('required', true);
        } else {
            $(days).prop('required', false);
        }
    }

    function configureTracking() {
        if ($('#trackserialno').val() == 1) {
            $('.track_expire').find('select').val(0).trigger('change');
            $('.expiry_notification').val(0).trigger('change');
            $('.notify_before_days').val('');
            $('#validate_serialno,#warrant_month').prop('disabled', false);
            $('#non-stock').val(0);
        } else {
            $('#validate_serialno,#warrant_month').val(0).prop('disabled', true);
        }
    }

    function configureNonStock(timer = false) {
        if ($('#non-stock').val() == 1) {
            $('.track_expire_date').val(0).trigger('change');
            $('.reorder_level').val(0).trigger('change');
            $('#trackserialno').val(0).trigger('change');

            $('.track_expire_date,.reorder_level').addClass('border-danger');
            let hideFn = function () {
                $('.track_expire_date,.reorder_level').removeClass('border-danger');
                $('.track_expire,.track_serialno').hide(timer ? 'fast' : '');
            };
            timer ? setTimeout(hideFn, 1000) : hideFn();
        } else {
            $('.track_expire,.track_serialno').show(timer ? 'fast' : '');
        }
    }

    function disable_scroll() {
        $(':input[type=number]').on('mousewheel', function (e) {
            $(this).blur();
        });
        $(':input[type=number]').on('mousewheel', function (e) {
            $(this).focus();
        });
        $('input[type=number]').on('keydown', function (e) {
            if (e.which == 38 || e.which == 40) e.preventDefault();
        });
    }

    function verifyProduct(obj) {
        checkProductName(obj);
        let parent = $(obj).closest('form');
        let submitbutn = $(parent).find(".submit");
        let productid = $('.productid').val();
        let productname = $(obj).val();
        PRODUCT_OK = false;
        $(obj).removeClass("border-danger");
        enableSaveButton();

        // $("#productexist").innerHTML = "You wrote: " + productname;
        if ($.trim(productname).length <= 0) return;

        $.get(`?module=products&action=checkProduct&format=json&productid=${productid}&productname=${productname}`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'exists') {
                PRODUCT_OK = false;
                enableSaveButton();
                $(obj).addClass("border-danger");
                triggerError('This Product name exists');
            } else {
                triggerMessage('This Product name is fine');
                $(obj).removeClass("border-danger");
                PRODUCT_OK = true;
                enableSaveButton();
            }
        });

    }

    let manufacture_timer = null;

    function checkManufactureBarcode(obj) {
        let productid = $('.productid').val();
        let barcode = $(obj).val();
        let spinner = $(obj).closest('.input-holder').find('.barcode_spinner');
        let error_msg = $(obj).closest('.input-holder').find('.error-msg');
        if (manufacture_timer) clearTimeout(manufacture_timer);
        spinner.show();
        error_msg.hide();
        MANUFACTURE_BARCODE_OK = false;
        enableSaveButton();

        if ($.trim(barcode).length < 1) {
            MANUFACTURE_BARCODE_OK = true;
            $(obj).removeClass("border-danger");
            enableSaveButton();
            spinner.hide();
            return;
        }

        productid = productid ? productid : '';
        manufacture_timer = setTimeout(function () {
            $.get(`?module=products&action=checkbarcode&format=json&barcode=${barcode}&productid=${productid}`, null, function (data) {
                let result = JSON.parse(data);
                spinner.hide();

                if (result.status === 'exists') {
                    $(obj).addClass("border-danger");
                    error_msg.show();
                } else {
                    $(obj).removeClass("border-danger");
                    MANUFACTURE_BARCODE_OK = true;
                    triggerMessage('Barcode Okay');
                    enableSaveButton();
                }
            });
        }, 750);
    }

    let office_timer = null;

    function checkOfficeBarcode(obj) {
        let productid = $('.productid').val();
        let barcode = $(obj).val();
        let spinner = $(obj).closest('.input-holder').find('.barcode_spinner');
        let error_msg = $(obj).closest('.input-holder').find('.error-msg');
        if (office_timer) clearTimeout(office_timer);
        spinner.show();
        error_msg.hide();
        OFFICE_BARCODE_OK = false;
        enableSaveButton();

        if ($.trim(barcode).length < 1) {
            OFFICE_BARCODE_OK = true;
            $(obj).removeClass("border-danger");
            enableSaveButton();
            spinner.hide();
            return;
        }

        productid = productid ? productid : '';
        office_timer = setTimeout(function () {
            $.get(`?module=products&action=checkbarcode&format=json&barcode=${barcode}&productid=${productid}`, null, function (data) {
                let result = JSON.parse(data);
                spinner.hide();

                if (result.status === 'exists') {
                    $(obj).addClass("border-danger");
                    error_msg.show();
                } else {
                    $(obj).removeClass("border-danger");
                    OFFICE_BARCODE_OK = true;
                    triggerMessage('Barcode Okay');
                    enableSaveButton();
                }
            });
        }, 750);
    }

    function enableSaveButton() {
        $('button.submit').prop('disabled', !(OFFICE_BARCODE_OK && MANUFACTURE_BARCODE_OK && PRODUCT_OK));
    }

    $(function () {
        <?
        if ($_GET['error'] == 1) {?>
        triggerError('Barcode Already in use!');
        <?}
        ?>
        disable_scroll();
    });

    function makediscription(obj) {
        checkProductName(obj);
        var parent = $(obj).closest(".addproduct");
        var productname = $(parent).find(".productname").val();

        if ($(obj).hasClass('generic_name')) { //works for pharmacy
            var genericname = $(".generic_name").val();
            $(".description").val($(".productname").val() + `  (${genericname})`);
        } else {
            $(".description").val(productname);
            <?if(CS_REPLICATE_NAME){?>
            $(".generic_name").val(productname);
            <?}?>
        }
    }

    let key_timer = null;

    function checkProductName(obj) {
        let value = $(obj).val();
        let regexp = /["'/\\<>?]/g;
        if (value.match(regexp)) {
            $(obj).parent().find('.character-error').show('fast');
        }
        if (key_timer) clearTimeout(key_timer);

        key_timer = setTimeout(function () {
            $(obj).parent().find('.character-error').hide('fast');
        }, 3000);

        let parts = value.split(' ');
        $.each(parts, function (i, p) {
            parts[i] = p.replace(regexp, '');
        });
        value = parts.join(' ');
        $(obj).val(value);
    }


</script>
