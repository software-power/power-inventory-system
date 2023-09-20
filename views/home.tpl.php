<link rel="stylesheet" href="assets/vendor/summernote/summernote.css">
<link rel="stylesheet" href="assets/vendor/summernote/summernote-bs3.css">
<header class="page-header">
    <h2>Settings</h2>
</header>
<div class="col-md-12">
    <form id="form" class="form-horizontal form-bordered" method="post" action="<?= url('home', 'settings_save') ?>"
          enctype="multipart/form-data" onsubmit="addTab(this)">
        <section class="panel">
            <div class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">Company Settings</h2>
                <button type="submit" class="btn btn-success btn-lg">Save</button>
            </div>
            <div class="panel-body">
                <ul id="setting-tabs" class="nav nav-tabs">
                    <li class="<?= !isset($_GET['tab']) || $_GET['tab'] == 'info' ? 'active' : '' ?>">
                        <a data-toggle="tab" href="#info" data-tabname="info"> <i class="fa fa-home"></i> Company Info</a>
                    </li>
                    <li class="<?= $_GET['tab'] == 'configuration' ? 'active' : '' ?>">
                        <a data-toggle="tab" href="#configuration" data-tabname="configuration"> <i class="fa fa-cog"></i> Configuration</a>
                    </li>
                    <li class="<?= $_GET['tab'] == 'defaults' ? 'active' : '' ?>">
                        <a data-toggle="tab" href="#defaults" data-tabname="defaults"><i class="fa fa-compress"></i> Defaults</a>
                    </li>
                    <li class="<?= $_GET['tab'] == 'printing' ? 'active' : '' ?>">
                        <a data-toggle="tab" href="#printing" data-tabname="printing"><i class="fa fa-print"></i> Printing</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="info"
                         class="tab-pane fade <?= !isset($_GET['tab']) || $_GET['tab'] == 'info' ? ' in active' : '' ?>">
                        <h3>Company Info</h3>
                        <div class="form-group">
                            <label class="col-md-3 control-label" style="text-align:left">
                                Company Name
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[name]" value="<?= CS_COMPANY ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">Address
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[address]" value="<?= CS_ADDRESS ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                Address 2
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[street]" value="<?= CS_STREET ?>">
                            </label>
                            <label class="col-md-3 control-label" style="text-align:left">
                                Tax Office
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[tax_office]" value="<?= CS_TAX_OFFICE ?>">
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" style="text-align:left">
                                Telephone
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[tel]" value="<?= CS_TEL ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                Email
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[email]" value="<?= CS_EMAIL ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                Logo
                                <input type="file" class="form-control" name="clogo"/>
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">Current Logo<img
                                        style="width: 258px;height: 60px"
                                        src="<?= CS_LOGO ?>" alt="business logo"/>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" style="text-align:left">
                                TIN
                                <input type="text" class="form-control" title="" name="cs[tin]" value="<?= CS_TIN ?>">
                            </label>
                            <label class="col-md-3 control-label" style="text-align:left">
                                VRN
                                <input type="text" class="form-control" title="" name="cs[vrn]" value="<?= CS_VRN ?>">
                            </label>
                        </div>
                    </div>
                    <div id="configuration" class="tab-pane fade <?= $_GET['tab'] == 'configuration' ? ' in active' : '' ?>">
                        <h3>Configurations</h3>
                        <div class="form-group">
                            <label class="col-md-3 control-label" style="text-align:left">
                                <span class="text-weight-semibold">Multi System</span>
                                <select id="multi-system" class="form-control" name="cs[multi_system]" onchange="configMultiSystem()">
                                    <option value="1" <?= selected(CS_MULTI_SYSTEM, 1) ?>>Yes</option>
                                    <option value="0" <?= selected(CS_MULTI_SYSTEM, 0) ?>>No</option>
                                </select>
                            </label>
                            <div id="multi-system-config">
                                <label class="col-md-3 control-label" style="text-align:left"
                                       title="if this is a main system or sub-system/sub-company">
                                    <span class="">System Mode</span>
                                    <select id="main-system" class="form-control" name="cs[main_system]">
                                        <option value="1" <?= selected(CS_MAIN_SYSTEM, 1) ?>>Main System</option>
                                        <option value="0" <?= selected(CS_MAIN_SYSTEM, 0) ?>>Sub system</option>
                                    </select>
                                </label>
                                <label class="col-md-3 control-label pt-lg" style="text-align:left">
                                    <a target="_blank" href="<?= url('home', 'system_tokens') ?>" class="btn btn-primary"><i
                                                class="fa fa-gears"></i>
                                        Configure Tokens</a>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" style="text-align:left">Email Password <input
                                        type="password" required class="form-control" data-toggle="tooltip"
                                        data-trigger="hover" name="cs[emailpass]"
                                        value="<?= CS_EMAILPASS ?>"></label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                Hostname
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[emailhost]" value="<?= CS_EMAILHOST ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                Port No
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[emailport]" value="<?= CS_EMAILPORT ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">Email Status
                                <select required class="form-control" name="cs[emailstat]">
                                    <option value="on" <?= selected(CS_EMAILSTAT, 'on') ?>>On</option>
                                    <option value="off" <?= selected(CS_EMAILSTAT, 'off') ?>>Off</option>
                                </select>
                            </label>

                        </div>
                        <div class="form-group">
                            <h4 class="ml-md">Sales & Order</h4>
                            <label class="col-md-3 control-label" title="Allow creating quick order in quick sale"
                                   style="text-align:left">
                                Allow Quick Order
                                <select class="form-control" name="cs2[quick_order]">
                                    <option value="0" <?= selected(CS_QUICK_ORDER, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_QUICK_ORDER, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="Allow user to issue discount in quick sale"
                                   style="text-align:left">
                                Quick Sale Keyboard-Only
                                <select id="quicksale-discount" class="form-control" name="cs2[quicksale_keyboardonly]">
                                    <option value="0" <?= selected(CS_QUICKSALE_KEYBOARDONLY, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_QUICKSALE_KEYBOARDONLY, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="Allow user to issue discount in quick sale"
                                   style="text-align:left">
                                Allow Discount in Quick Sale
                                <select id="quicksale-discount" class="form-control" name="cs2[quicksale_discount]">
                                    <option value="0" <?= selected(CS_QUICKSALE_DISCOUNT, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_QUICKSALE_DISCOUNT, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="Allow user to issue discount in quick sale"
                                   style="text-align:left">
                                Allow Search in Quick Sale
                                <select class="form-control" name="cs2[quicksale_search]">
                                    <option value="0" <?= selected(CS_QUICKSALE_SEARCH, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_QUICKSALE_SEARCH, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="Allow installment payment from clients" style="text-align:left">
                                Allow Installment Payment
                                <select class="form-control" name="cs2[installment_payment]">
                                    <option value="0" <?= selected(CS_INSTALLMENT_PAYMENT, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_INSTALLMENT_PAYMENT, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="Allow ficalization for paid invoice" style="text-align:left">
                                Allow Fiscalization for Paid Invoice
                                <select class="form-control" name="cs2[fiscalize_paid]">
                                    <option value="0" <?= selected(CS_FISCALIZE_PAID, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_FISCALIZE_PAID, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <div class="col-md-3 control-label" style="text-align:left">
                                Default POS display Stock Location
                                <select id="pos-display-location" class="form-control" name="cs2[pos_display_location]">
                                    <option selected value="0">--none--</option>
                                    <? foreach ($locations as $l) { ?>
                                        <option <?= selected(CS_POS_DISPLAY_LOCATION, $l['id']) ?>
                                                value="<?= $l['id'] ?>"><?= $l['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <div class="col-md-3 control-label" style="text-align:left"
                                 title="Produce beep sound in POS screen when
                                 1. removing item from list
                                 2. scanned item with zero stock
                                 3. reduce item qty">
                                Quick sale beep sound
                                <select class="form-control" name="cs2[quick_sale_beep]">
                                    <option <?=selected(CS_QUICK_SALE_BEEP,0)?> value="0">No</option>
                                    <option <?=selected(CS_QUICK_SALE_BEEP,1)?> value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" title="Allow product reorder levels"
                                   style="text-align:left">
                                Allow Prescription Entry
                                <select class="form-control" name="cs2[prescription_entry]">
                                    <option value="0" <?= selected(CS_PRESCRIPTION_ENTRY, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_PRESCRIPTION_ENTRY, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="Allow product reorder levels"
                                   style="text-align:left">
                                Allow Reorder Levels
                                <select id="reorder" class="form-control" name="cs2[reorder_level]"
                                        onchange="configReorder(this)">
                                    <option value="0" <?= selected(CS_REORDER_LEVEL, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_REORDER_LEVEL, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <div id="reorder-level" class="col-md-6" style="display: none">
                                <label class="col-md-3 control-label" title="Default minimum qty"
                                       style="text-align:left">
                                    Default Minimum Qty
                                    <input type="text" class="form-control" name="cs2[reordermin]"
                                           value="<?= CS_REORDER_DEFAULT_MIN ?>">
                                </label>
                                <label class="col-md-3 control-label" title="Default maximum qty"
                                       style="text-align:left">
                                    Default Maximum Qty
                                    <input type="text" class="form-control" name="cs2[reordermax]"
                                           value="<?= CS_REORDER_DEFAULT_MAX ?>">
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label" title="Backup file password"
                                   style="text-align:left;position: relative;">
                                Backup Password
                                <button type="button" class="btn btn-default btn-sm text-primary"
                                        onclick="showPassword(this)">
                                    <i class="fa fa-eye-slash"></i></button>
                                <input type="password" class="form-control backup_password"
                                       name="cs2[backup_password]"
                                       value="<?= CS_BACKUP_PASSWORD ?>">
                            </label>
                            <div class="col-md-3">
                                <label>Show Columns in reports</label>
                                <div class="d-flex flex-wrap">
                                    <div class="checkbox mr-sm">
                                        <label>
                                            <input type="checkbox" name="cs2[show_generic_name]"
                                                   value="1" <?= CS_SHOW_GENERIC_NAME ? 'checked' : '' ?>>
                                            Generic Name
                                        </label>
                                    </div>
                                    <div class="checkbox mr-sm">
                                        <label title="show product category in stock reports">
                                            <input type="checkbox" name="cs2[show_category]"
                                                   value="1" <?= CS_SHOW_CATEGORY ? 'checked' : '' ?>>
                                            Product Categories
                                        </label>
                                    </div>
                                    <div class="checkbox mr-sm">
                                        <label title="show brand in stock reports">
                                            <input type="checkbox" name="cs2[show_brand]" value="1" <?= CS_SHOW_BRAND ? 'checked' : '' ?>>
                                            Brand
                                        </label>
                                    </div>
                                    <div class="checkbox mr-sm">
                                        <label title="show department in stock reports">
                                            <input type="checkbox" name="cs2[show_department]"
                                                   value="1" <?= CS_SHOW_DEPARTMENT ? 'checked' : '' ?>>
                                            Department
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>While Printing Show</label>
                                <div class="d-flex flex-wrap">
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" name="cs2[printing_show_description]"
                                                   value="0" <?= CS_PRINTING_SHOW_DESCRIPTION ? '' : 'checked' ?>>
                                            Product name
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" name="cs2[printing_show_description]"
                                                   value="1" <?= CS_PRINTING_SHOW_DESCRIPTION ? 'checked' : '' ?>>
                                            Product description
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <h4 class="ml-md">Stock, GRN & Supplier</h4>
                            <label class="col-md-3 control-label" title="Allow creating quick order in quick sale"
                                   style="text-align:left">
                                Supplier Payment Notifications
                                <select class="form-control" name="cs2[supplier_notification]">
                                    <option value="0" <?= selected(CS_SUPPLIER_NOTIFICATION, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_SUPPLIER_NOTIFICATION, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="If you want to track supplier/grn payments"
                                   style="text-align:left">
                                Track Supplier Payments
                                <select class="form-control" name="cs2[supplier_payment]">
                                    <option value="0" <?= selected(CS_SUPPLIER_PAYMENT, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_SUPPLIER_PAYMENT, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="If you want all GRN to require LPO first"
                                   style="text-align:left">
                                All GRN required LPO
                                <select class="form-control" name="cs2[grn_require_lpo]">
                                    <option value="0" <?= selected(CS_GRN_REQUIRE_LPO, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_GRN_REQUIRE_LPO, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="If you want every lpo to be approved"
                                   style="text-align:left">
                                LPO Approval
                                <select class="form-control" name="cs2[lpo_approval]">
                                    <option value="0" <?= selected(CS_LPO_APPROVAL, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_LPO_APPROVAL, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="If you want every GRN to be approved first"
                                   style="text-align:left">
                                GRN Approval
                                <select class="form-control" name="cs2[grn_approval]">
                                    <option value="0" <?= selected(CS_GRN_APPROVAL, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_GRN_APPROVAL, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="If you want all transfer to required requisition first"
                                   style="text-align:left">
                                All Transfer Require Requisition
                                <select class="form-control" name="cs2[transfer_required_requisition]">
                                    <option value="0" <?= selected(CS_TRANSFER_REQUIRE_REQUISITION, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_TRANSFER_REQUIRE_REQUISITION, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label" title="If you want transfer requisition approval"
                                   style="text-align:left">
                                Transfer Requisition approval
                                <select class="form-control" name="cs2[requisition_approval]">
                                    <option value="0" <?= selected(CS_REQUISITION_APPROVAL, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_REQUISITION_APPROVAL, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label"
                                   title="If you want every transfer to be approved first"
                                   style="text-align:left">
                                Stock Transfer Approval
                                <select class="form-control" name="cs2[expense_approval]">
                                    <option value="0" <?= selected(CS_TRANSFER_APPROVAL, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_TRANSFER_APPROVAL, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label"
                                   title="If you want every expense to be approved first"
                                   style="text-align:left">
                                Expense Approval
                                <select class="form-control" name="cs2[transfer_approval]">
                                    <option value="0" <?= selected(CS_EXPENSE_APPROVAL, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_EXPENSE_APPROVAL, 1) ?>>Yes</option>
                                </select>
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-4">
                                <h4>Allowed Receipts</h4>
                                <div class="checkbox">
                                    <label>
                                        <input id="enable-tra" type="checkbox" name="cs2[tra_rc]" value="1"
                                               onchange="enableTRA(this)" <?= CS_ALLOW_TRA_RC ? 'checked' : '' ?>>
                                        Enable TRA integration <b><?= $R['lable'] ?></b>
                                    </label>
                                </div>
                                <?
                                foreach ($reciepts as $key => $R) {
                                    if ($R['name'] == 'sr') { ?>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="reciepts[]" value="<?= $R['id'] ?>"
                                                       class="<?= $R['name'] ?>"
                                                    <?= $R['status'] == 'active' ? 'checked' : '' ?>
                                                       onchange="receiptConfig()">
                                                Allow <b><?= $R['lable'] ?></b>
                                            </label>
                                        </div>
                                        <div id="sr-type" class="col-md-12" style="display: none">
                                            <div class="radio-inline">
                                                <label>
                                                    <input type="radio" name="cs2[sr_type]"
                                                           value="<?= SR_TYPE_A4 ?>"
                                                        <?= CS_SR_TYPE == SR_TYPE_A4 ? 'checked' : '' ?>>A4
                                                </label>
                                            </div>
                                            <div class="radio-inline">
                                                <label>
                                                    <input type="radio" name="cs2[sr_type]"
                                                           value="<?= SR_TYPE_SMALL ?>"
                                                        <?= CS_SR_TYPE == SR_TYPE_SMALL ? 'checked' : '' ?>>Small
                                                </label>
                                            </div>
                                            <div class="radio-inline">
                                                <label>
                                                    <input type="radio" name="cs2[sr_type]"
                                                           value="<?= SR_TYPE_DETAILED ?>"
                                                        <?= CS_SR_TYPE == SR_TYPE_DETAILED ? 'checked' : '' ?>>Detailed
                                                </label>
                                            </div>
                                        </div>
                                    <? } else { ?>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="reciepts[]" value="<?= $R['id'] ?>"
                                                       class="<?= $R['name'] ?> tra_rc"
                                                    <?= $R['status'] == 'active' ? 'checked' : '' ?>
                                                       onchange="receiptConfig()">
                                                Allow <b><?= $R['lable'] ?></b>
                                            </label>
                                        </div>
                                    <? }
                                } ?>
                            </div>
                        </div>
                        <div id="efd-config" class="form-group" style="display: none">
                            <h5>EFD Configuration</h5>
                            <label class="col-md-3 control-label" style="text-align:left">
                                efd machine location
                                <select id="efd-location" required class="form-control" name="cs2[efd_location]"
                                        onchange="enableEfdInputs(this)">
                                    <option value="<?= EFD_LOCATION_LOCAL ?>" <?= selected(CS_EFD_LOCATION, EFD_LOCATION_LOCAL) ?>>
                                        Local
                                    </option>
                                    <option value="<?= EFD_LOCATION_SHARED ?>" <?= selected(CS_EFD_LOCATION, EFD_LOCATION_SHARED) ?>>
                                        Shared
                                    </option>
                                    <option value="<?= EFD_LOCATION_REMOTE ?>" <?= selected(CS_EFD_LOCATION, EFD_LOCATION_REMOTE) ?>>
                                        Remote
                                    </option>
                                    <option value="<?= EFD_LOCATION_DOWNLOAD ?>" <?= selected(CS_EFD_LOCATION, EFD_LOCATION_DOWNLOAD) ?>>
                                        Download
                                    </option>
                                </select>
                            </label>
                            <div id="local" class="col-md-9">
                                <label class="col-md-3 control-label" style="text-align:left">
                                    local directory
                                    <input type="text" class="form-control efd" name="cs2[efd_local_dir]"
                                           data-toggle="tooltip" data-trigger="hover"
                                           value="<?= CS_EFD_LOCAL_DIRECTORY ?>">
                                </label>
                            </div>
                            <div id="shared" class="col-md-9" style="display: none">
                                <label class="col-md-3 control-label" style="text-align:left">
                                    shared host
                                    <input type="text" class="form-control efd" name="cs2[efd_shared_host]"
                                           data-toggle="tooltip" data-trigger="hover"
                                           value="<?= CS_EFD_SHARED_HOST ?>">
                                </label>
                                <label class="col-md-3 control-label" style="text-align:left">
                                    shared directory
                                    <input type="text" class="form-control efd" name="cs2[efd_shared_dir]"
                                           data-toggle="tooltip" data-trigger="hover"
                                           value="<?= CS_EFD_SHARED_DIR ?>">
                                </label>
                            </div>
                            <div id="remote" class="col-md-9" style="display: none">
                                <label class="col-md-3 control-label" style="text-align:left">
                                    ftp server
                                    <input type="text" required class="form-control efd" name="cs2[efd_ftp_server]"
                                           data-toggle="tooltip" data-trigger="hover"
                                           value="<?= CS_EFD_FTP_SERVER ?>">
                                </label>

                                <label class="col-md-3 control-label" style="text-align:left">
                                    ftp username
                                    <input type="text" class="form-control efd" name="cs2[ftp_username]"
                                           data-toggle="tooltip"
                                           data-trigger="hover" value="<?= CS_FTP_USERNAME ?>">
                                </label>

                                <label class="col-md-3 control-label" style="text-align:left">
                                    ftp password
                                    <input type="password" class="form-control efd" name="cs2[ftp_password]"
                                           data-toggle="tooltip" data-trigger="hover"
                                           value="<?= CS_FTP_PASSWORD ?>">
                                </label>
                            </div>
                        </div>
                        <div id="vfd-config" class="form-group" style="display: none">
                            <h5>VFD Configuration</h5>
                            <label class="col-md-2 control-label" style="text-align:left">
                                Type
                                <select id="vfd-mode" required class="form-control" name="cs[vfd_type]"
                                        onchange="enableGrouping(this)">
                                    <option value="<?= VFD_TYPE_VFD ?>" <?= selected(CS_VFD_TYPE, VFD_TYPE_VFD) ?>>
                                        VFD
                                    </option>
                                    <option value="<?= VFD_TYPE_ZVFD ?>" <?= selected(CS_VFD_TYPE, VFD_TYPE_ZVFD) ?>>
                                        ZVFD
                                    </option>
                                </select>
                            </label>
                            <label class="col-md-1 control-label" style="text-align:left">
                                Mode
                                <select id="vfd-url" required class="form-control" name="cs[vfdstatus]"
                                        onchange="enableVfdInputs(this)">
                                    <option value="<?= VFD_MODE_TESTING ?>" <?= selected(CS_VFD_MODE, VFD_MODE_TESTING) ?>>
                                        Testing
                                    </option>
                                    <option value="<?= VFD_MODE_LIVE ?>" <?= selected(CS_VFD_MODE, VFD_MODE_LIVE) ?>>
                                        Live
                                    </option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label vfd_live" style="text-align:left">
                                Live URL
                                <input type="text" class="form-control" name="cs[CS_LIVEURL]"
                                       value="<?= CS_VFD_LIVE_URL ?>">
                            </label>
                            <label class="col-md-3 control-label vfd_test" style="text-align:left">
                                Test URL
                                <input type="text" class="form-control" name="cs[CS_TESTURL]"
                                       value="<?= CS_VFD_TEST_URL ?>">
                            </label>
                            <label class="col-md-2 control-label zvfd_special" style="text-align:left">
                                ZVFD Integration ID
                                <button type="button" class="btn btn-default btn-sm text-primary"
                                        onclick="showIntegrationID(this)">
                                    <i class="fa fa-eye-slash"></i></button>
                                <input type="password" class="form-control integration_id" name="cs[zvfd_integration_id]"
                                       value="<?= CS_ZVFD_INTEGRATION_ID ?>">
                            </label>
                            <label class="col-md-2 control-label zvfd_special" title="ZRB Tax category .eg TAX,Stamp duty,Hotel Levy..."
                                   style="text-align:left">
                                Tax Category <small class="text-weight-semibold text-danger">For all products without taxcode</small>
                                <select id="zvfd-taxcategoryid" class="form-control" name="cs[zvfd_taxcategoryid]">
                                    <? foreach ($taxes as $t) {
                                        if ($t['vat_percent'] > 0) { ?>
                                            <option <?= selected(CS_ZVFD_TAXCATEGORYID, $t['id']) ?>
                                                    value="<?= $t['id'] ?>"><?= $t['name'] ?> (<?= $t['vat_percent'] ?>%)
                                            </option>
                                        <? }
                                    } ?>
                                </select>
                            </label>
                            <label class="col-md-1 control-label vfd_special" style="text-align:left">
                                Device No.
                                <input type="text" class="form-control" name="cs[deviceno]" placeholder="device no"
                                       value="<?= CS_DEVICE_NO ?>">
                            </label>
                            <label class="col-md-1 control-label vfd_special" style="text-align:left">
                                Fcode
                                <input type="text" class="form-control" name="cs[fcode]" placeholder="fcode" value="<?= CS_FCODE ?>">
                            </label>
                            <label class="col-md-2 control-label vfd_special" style="text-align:left">
                                Token
                                <input type="password" class="form-control" name="cs[fcodetoken]" placeholder="fcode token"
                                       value="<?= CS_FCODETOKEN ?>">
                            </label>
                            <label class="col-md-1 control-label" style="text-align:left">
                                Invoice No Prefix
                                <input type="text" class="form-control" name="cs[vfd_invoice_prefix]" placeholder="invoice prefix"
                                       value="<?= CS_VFD_INVOICE_PREFIX ?>">
                            </label>
                        </div>
                        <div class="form-group mt-xlg">
                            <h4>Tally Configuration</h4>
                            <div class="row">
                                <div class="col-md-2" title="If set to Yes from now on all transactions will be sent to tally">
                                    <h5>Allow Tally Data Transfer</h5>
                                    <select id="tally-transfer" class="form-control" name="cs[tally_status]" onchange="configTally()">
                                        <option <?= selected(CS_TALLY_TRANSFER, 1) ?> value="1">Yes</option>
                                        <option <?= selected(CS_TALLY_TRANSFER, 0) ?> value="0">No</option>
                                    </select>
                                </div>
                                <div class="col-md-2 tally-setting" style="display: none">
                                    <h5>Tally Company Name</h5>
                                    <input type="text" name="cs[tally_name]" class="form-control" placeholder="Tally company name"
                                           value="<?= CS_TALLY_COMPANY_NAME ?>">
                                </div>
                                <div class="col-md-2 tally-setting" style="display: none">
                                    <h5>Tally Server IP</h5>
                                    <input type="text" name="cs[tally_server]" class="form-control" placeholder="127.0.0.1"
                                           value="<?= TALLY_SERVER_IP ?>">
                                </div>
                                <div class="col-md-2 tally-setting" style="display: none">
                                    <h5>Tally Server Port</h5>
                                    <input type="text" name="cs[tally_port]" class="form-control" placeholder="9000"
                                           value="<?= TALLY_PORT ?>">
                                </div>
                                <div class="col-md-2 tally-setting">
                                    <h5>Post Direct</h5>
                                    <select class="form-control" name="cs[tally_direct]">
                                        <option <?= selected(CS_TALLY_DIRECT, 1) ?> value="1">Yes</option>
                                        <option <?= selected(CS_TALLY_DIRECT, 0) ?> value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 tally-setting" style="display: none"></div>
                                <div class="col-md-2 tally-setting" style="display: none"
                                     title="Tally ledger that will be used as a control account">
                                    <h5>Transaction Control Acc</h5>
                                    <input type="text" name="cs[tally_control_ac]" class="form-control"
                                           placeholder="Tally trans control acc"
                                           value="<?= CS_TALLY_CONTROL_ACC ?>">
                                </div>
                                <div class="col-md-2 tally-setting" style="display: none"
                                     title="Tally indirect expense ledger that will be used as a GRN adjustment amount">
                                    <h5>Tally adjustment ledger</h5>
                                    <input type="text" name="cs[tally_adjustment_ledger]" class="form-control"
                                           placeholder="Tally adjustment ledger"
                                           value="<?= CS_TALLY_ADJUSTMENT_LEDGER ?>">
                                </div>
                                <div class="col-md-2 tally-setting"
                                     title="If set to Yes from now on all transactions will be sent to tally">
                                    <h5>Different Ledger name for client</h5>
                                    <select class="form-control" name="cs[diff_client_ledgername]">
                                        <option <?= selected(CS_DIFF_CLIENT_LEDGERNAME, 1) ?> value="1">Yes</option>
                                        <option <?= selected(CS_DIFF_CLIENT_LEDGERNAME, 0) ?> value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-xlg" style="display: ">
                            <div class="col-lg-2" style="visibility: ">
                                <h5>Allow App Login With Mac-Address</h5>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="cs[app_login_mac_address_status]"
                                               value="1" <? if (CS_MAC_ADDRESS == '1') {
                                            echo "checked";
                                        } ?>>
                                        Yes Allow
                                    </label>
                                </div>

                                <div class="radio">
                                    <label>
                                        <input type="radio" name="cs[app_login_mac_address_status]"
                                               value="0" <? if (CS_MAC_ADDRESS == '0') {
                                            echo "checked";
                                        } ?>>
                                        No Dont Allow
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="form-group mt-xlg">
                            <label class="col-md-3 control-label" style="text-align:left">
                                SMS Username
                                <input type="text" required class="form-control" data-toggle="tooltip"
                                       data-trigger="hover"
                                       name="cs[smsuser]" value="<?= CS_SMSUSER ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                SMS Password<input type="password" required class="form-control"
                                                   data-toggle="tooltip"
                                                   data-trigger="hover" name="cs[smspass]"
                                                   value="<?= CS_SMSPASS ?>">
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                SMS Sender:<?= CS_SMSNAME ?>
                            </label>

                            <label class="col-md-3 control-label" style="text-align:left">
                                SMS Status
                                <select required class="form-control" name="cs[smsstat]">
                                    <option value="on" <?= selected(CS_SMSSTAT, 'on') ?>>On</option>
                                    <option value="off" <?= selected(CS_SMSSTAT, 'off') ?>>Off</option>
                                </select>
                            </label>
                        </div>
                        <div class="form-group mt-lg">
                            <div class="col-md-12">
                                <h4>Support API</h4>
                            </div>
                            <label class="col-md-3 control-label" title="authorization token get it from support system"
                                   style="text-align: left">
                                <span>Support Integration</span>
                                <select id="support-integration" class="form-control" name="cs2[support_integration]"
                                        onchange="configSupport()">
                                    <option <?= selected(CS_SUPPORT_INTEGRATION, 1) ?> value="1">Yes</option>
                                    <option <?= selected(CS_SUPPORT_INTEGRATION, 0) ?> value="0">No</option>
                                </select>
                            </label>
                            <label class="col-md-3 control-label support-setting" title="support server url" style="text-align: left">
                                <span>Name in support</span>
                                <input type="text" class="form-control" name="cs2[support_name]" value="<?= CS_SUPPORT_NAME ?>"
                                       placeholder="api name in support">
                            </label>
                            <label class="col-md-3 control-label support-setting" title="support server url" style="text-align: left">
                                <span>Server Url</span>
                                <input type="text" class="form-control" name="cs2[support_server]" value="<?= CS_SUPPORT_SERVER ?>"
                                       placeholder="support server url">
                            </label>
                        </div>
                    </div>
                    <div id="defaults" class="tab-pane fade <?= $_GET['tab'] == 'defaults' ? ' in active' : '' ?>">
                        <h4>System Default Values</h4>
                        <div class="form-group mb-lg">
                            <label class="col-md-3 control-label" title="Default proforma valid days"
                                   style="text-align: left">
                                Proforma validity days
                                <input type="number" class="form-control" name="cs2[proforma_valid_days]"
                                       value="<?= CS_PROFORMA_VALID_DAYS ?>"
                                       min="1">
                            </label>
                            <label class="col-md-3 control-label" title="Default proforma stock holding days"
                                   style="text-align: left">
                                Proforma stock holding days
                                <input type="number" class="form-control" name="cs2[proforma_stock_holding_days]"
                                       value="<?= CS_PROFORMA_STOCK_HOLDING_DAYS ?>"
                                       min="1">
                            </label>
                            <label class="col-md-3 control-label" title="Default order valid days"
                                   style="text-align: left">
                                Order validity days
                                <input type="number" class="form-control" name="cs2[order_validity_days]" value="<?= CS_ORDER_VALID_DAYS ?>"
                                       min="1">
                            </label>
                        </div>

                        <h4>Product Default values</h4>
                        <div class="form-group">
                            <label class="col-md-3 control-label" title="Replicate Name to Generic name"
                                   style="text-align:left">
                                Replicate Name
                                <select class="form-control" name="cs2[replicate_name]">
                                    <option value="0" <?= selected(CS_REPLICATE_NAME, 0) ?>>No</option>
                                    <option value="1" <?= selected(CS_REPLICATE_NAME, 1) ?>>Yes</option>
                                </select>
                            </label>
                            <div class="col-md-3">
                                <div class="col-md-12">
                                    <h5>Default Grn Location</h5>
                                </div>
                                <div class="col-md-12">
                                    <select id="grnlocation" class="form-control" name="cs2[def_grnloc]">
                                        <option selected value="0">--Choose location--</option>
                                        <? foreach ($locations as $l) {
                                            if ($l['id'] == CS_DEFAULT_GRNLOC) { ?>
                                                <option value="<?= $l['id'] ?>" selected><?= $l['name'] ?></option>
                                            <? }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="col-md-12" title="Point value in base currency">
                                    <h5>Product points value <span
                                                style="font-weight: bold">(<?= $basePrice['name'] ?>)</span></h5>
                                </div>
                                <div class="col-md-12">
                                    <input type="number" class="form-control" step="0.01" min="0"
                                           name="cs2[point_value]"
                                           value="<?= CS_POINT_VALUE ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3 department product-tax-category">
                                <div class="col-md-12">
                                    <div class="col-md-12 d-flex align-items-center">
                                        <h5>Tax Category</h5>
                                        <button type="button" class="btn btn-danger btn-sm ml-sm"
                                                title="Clear default"
                                                onclick="$('#tax').val('0');">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <select id="tax" class="form-control" name="cs2[def_tax]">
                                        <option selected value="0">--Choose Tax--</option>
                                        <? foreach ($taxes as $d) { ?>
                                            <option value="<?= $d['id'] ?>" <?= selected($d['id'], CS_DEFAULT_TAX) ?>><?= $d['name'] ?>
                                                (<?= $d['vat_percent'] ?>%)
                                            </option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 department">
                                <div class="col-md-12">
                                    <div class="col-md-12 d-flex align-items-center">
                                        <h5>Department</h5>
                                        <button type="button" class="btn btn-primary btn-sm ml-sm"
                                                title="quick add department"
                                                onclick="quickAddDepartment(this)">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm ml-sm"
                                                title="Clear default"
                                                onclick="$('#department').val('0').trigger('change');">
                                            <i class="fa fa-minus"></i>
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
                                    <select id="department" class="form-control" name="cs2[def_department]">
                                        <option selected value="0">--Choose department--</option>
                                        <? foreach ($departments as $d) {
                                            if ($d['id'] == CS_DEFAULT_DEPARTMENT) { ?>
                                                <option value="<?= $d['id'] ?>" selected><?= $d['name'] ?></option>
                                            <? }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 brand">
                                <div class="col-md-12">
                                    <div class="col-md-12 d-flex align-items-center">
                                        <h5>Brand</h5>
                                        <button type="button" class="btn btn-primary btn-sm ml-sm"
                                                title="quick add department"
                                                onclick="quickAddBrand(this)">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm ml-sm"
                                                title="Clear default"
                                                onclick="$('#brand').val('0').trigger('change');">
                                            <i class="fa fa-minus"></i>
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
                                    <select id="brand" class="form-control" name="cs2[def_brand]">
                                        <option selected value="0">--Choose Brand--</option>
                                        <? foreach ($brands as $d) { ?>
                                            <option value="<?= $d['id'] ?>" <?= selected($d['id'], CS_DEFAULT_BRAND) ?>><?= $d['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="col-md-12">
                                    <h5>Base sale Percentage %</h5>
                                </div>
                                <div class="col-md-12">
                                    <input type="number" class="form-control" title="Default base sale percent"
                                           name="cs2[def_base]" value="<?= CS_DEFAULT_BASE ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3 unit">
                                <div class="col-md-12 d-flex align-items-center">
                                    <h5>Unit</h5>
                                    <a href="?module=units&action=index" target="_blank"
                                       class="btn btn-primary btn-sm ml-sm" title="add supplier">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm ml-sm"
                                            title="Clear default"
                                            onclick="clearDefault('#units','.bulk_units')">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <div class="col-md-12">
                                    <select id="units" class="form-control units" name="cs2[def_unit]"
                                            onchange="checkbulk(this)">
                                        <? if ($unit) { ?>
                                            <option value="<?= $unit['id'] ?>"> <?= $unit['name'] ?> (<?= $unit['abbr'] ?>)</option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="col-md-12 d-flex justify-content-center align-items-center">
                                    <h5>Bulk Unit</h5>
                                    <a href="?module=bulk_units&action=index" target="_blank"
                                       class="btn btn-primary btn-sm ml-sm" title="add supplier">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <button type="button" onclick="checkbulk('#units')"
                                            class="btn btn-xs ml-sm text-success" title="refresh"><i
                                                class="fa fa-refresh"></i></button>
                                </div>
                                <div class="col-md-12">
                                    <select class="form-control bulk_units" name="cs2[def_bulk]">
                                        <option selected value="0">--Choose--</option>
                                        <? foreach ($bulkUnits as $d) { ?>
                                            <option value="<?= $d['id'] ?>" <?= selected(CS_DEFAULT_BULK, $d['id']) ?>
                                                    data-unit="<?= $d['unit'] ?>">
                                                <?= $d['name'] ?> - <?= $d['rate'] ?> <?= $d['singleUnitAbbr'] ?>
                                            </option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 productcategory">
                                <div class="col-md-12">
                                    <div class="col-md-12 d-flex align-items-center">
                                        <h5>Product Category</h5>
                                        <button type="button" class="btn btn-primary btn-sm ml-sm"
                                                title="quick add product category"
                                                onclick="quickAddProductCategory(this)">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm ml-sm"
                                                title="Clear default"
                                                onclick="clearDefault('#productCategory','#subcategories')">
                                            <i class="fa fa-minus"></i>
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
                                    <select id="productCategory" class="form-control"
                                            name="cs2[def_category]"
                                            onchange="fetchSubcategories(this)">
                                        <option selected value="0">--Choose Category--</option>
                                        <? foreach ($productCategories as $d) { ?>
                                            <option value="<?= $d['id'] ?>" <?= selected($d['id'], CS_DEFAULT_CATEGORY) ?>><?= $d['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="col-md-12 d-flex justify-content-center align-items-center">
                                    <h5>Subcategory</h5>
                                    <a href="?module=product_categories&action=subcategories" target="_blank"
                                       class="btn btn-primary btn-sm ml-sm" title="add subcategory">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <button type="button" onclick="fetchSubcategories('#productCategory')"
                                            class="btn btn-xs ml-sm text-success" title="refresh"><i
                                                class="fa fa-refresh"></i></button>
                                </div>
                                <div class="col-md-12">
                                    <select id="subcategories" class="form-control"
                                            name="cs2[def_subcategory]">
                                        <option selected value="0">--Choose--</option>
                                        <? foreach ($productSubcategories as $d) { ?>
                                            <option value="<?= $d['id'] ?>" <?= selected($d['id'], CS_DEFAULT_SUBCATEGORY) ?>><?= $d['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <h4 class="mt-xlg">Sales Agreement Terms and conditions</h4>
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea id="summernote" name="cs2[sales_agreement]" rows="10"
                                          class="form-control"><?= base64_decode(CS_SALES_AGREEMENT) ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div id="printing" class="tab-pane fade <?= $_GET['tab'] == 'printing' ? ' in active' : '' ?>">
                        <h4>Print Header & Footer</h4>
                        <div class="form-group">
                            <div class="col-md-3">
                                <div class="control-label" title="Show company logo and address on top of paper when printing"
                                     style="text-align:left">
                                    Show header while Printing
                                    <select class="form-control" name="cs[show_print_header]">
                                        <option value="0" <?= selected(CS_SHOW_PRINT_HEADER, 0) ?>>No</option>
                                        <option value="1" <?= selected(CS_SHOW_PRINT_HEADER, 1) ?>>Yes</option>
                                    </select>
                                </div>
                                <div class="control-label" title="Show bottom footer when printing"
                                     style="text-align:left">
                                    Show bottom footer while Printing
                                    <select id="show-footer" class="form-control" name="cs[show_print_footer]" onchange="configFooter()">
                                        <option value="0" <?= selected(CS_SHOW_PRINT_FOOTER, 0) ?>>No</option>
                                        <option value="1" <?= selected(CS_SHOW_PRINT_FOOTER, 1) ?>>Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div id="footer-holder" class="col-md-9">
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-default btn-xs mb-sm" onclick="$('#footer-img').trigger('click');">
                                        Choose
                                        Footer Img
                                    </button>
                                    <p class="d-block text-primary ml-md">Dimensions Height: 30mm, width: 210mm</p>
                                </div>
                                <input id="footer-img" type="file" name="footer_img" style="visibility: hidden;height: 0;width: 0;"
                                       accept="image/jpeg,image/png" onchange="previewFooter()">
                                <div class="bottom-footer" style="height: 30mm;width: 210mm;border:1px dashed grey;border-radius: 3px;">
                                    <img id="footer-preview" src="<?= CS_PRINT_FOOTER ?>" style="height: 100%;width: 100%;" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3" title="system receipt (SR) custom printing title">
                                <h5>SR printing title <small class="text-primary">default: PROVISIONAL INVOICE</small></h5>
                                <input type="text" name="cs2[sr_invoice_title]" class="form-control" placeholder="invoice title"
                                       value="<?= CS_SR_INVOICE_TITLE ?>">
                            </div>
                            <div class="col-md-6" title="additional remarks while printing SR invoice">
                                <h5>Additional SR invoice printing remarks</h5>
                                <textarea class="form-control" name="cs2[sr_extra_remarks]" rows="3"
                                          placeholder="extra remarks"><?= CS_SR_EXTRA_REMARKS ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3" title="show change in receipt">
                                <h5>Show Change in Receipt</h5>
                                <select name="cs2[show_change]" class="form-control">
                                    <option <?= CS_SHOW_CHANGE == 0 ? 'selected' : '' ?> value="0">No</option>
                                    <option <?= CS_SHOW_CHANGE == 1 ? 'selected' : '' ?> value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>
<script src="assets/js/quick_adds.js"></script>
<script src="assets/vendor/summernote/summernote.js"></script>
<script>

    $(function () {
        $('#pos-display-location').select2({width:'100%'});
        enableEfdInputs('#efd-location');
        receiptConfig();
        configMultiSystem();
        configTally();
        configSupport();
        configFooter();
        configReorder('#reorder');
        enableVfdInputs('#vfd-url');
        enableTRA('#enable-tra');
        enableGrouping('#vfd-mode');

        initSelectAjax("#units", "?module=units&action=getUnits&format=json", 'choose unit', 2);
        initSelectAjax("#department", "?module=departments&action=getDepartments&format=json", 'choose department');
        initSelectAjax("#brand", "?module=model&action=getModels&format=json", 'choose brand', 2);
        initSelectAjax("#productCategory", "?module=product_categories&action=getCategories&format=json", 'choose category', 2);
        initSelectAjax("#grnlocation", "?module=locations&action=getLocations&format=json", 'choose location', 2);
        $('#summernote').summernote({
            height: 300,
            fontSize: 20,
            fontSizeUnit: 'pt'
        });
    });

    function addTab(form) {
        let activeTab = $('#setting-tabs li.active a').data('tabname');
        console.log(activeTab);
        $(form).prepend(`<input type="hidden" name="tabname" value="${activeTab}">`);

    }

    function configMultiSystem() {
        if ($('#multi-system').val() == 1) {
            $('#multi-system-config').show();
        } else {
            $('#multi-system-config').hide();
            $('#main-system').val(1);
        }
    }

    function configTally() {
        if ($('#tally-transfer').val() == 1) {
            $('.tally-setting').show('fast');
        } else {
            $('.tally-setting').hide();
        }
    }

    function configSupport() {
        if ($('#support-integration').val() == 1) {
            $('.support-setting').show('fast');
        } else {
            $('.support-setting').hide();
        }
    }

    function configFooter() {
        if ($('#show-footer').val() == 1) {
            $('#footer-holder').show('fast');
        } else {
            $('#footer-holder').hide();
        }
    }

    let SHOW_PASSWORD = false;
    let SHOW_INTEGRATION_ID = false;

    function showPassword(obj) {
        SHOW_PASSWORD = !SHOW_PASSWORD;
        if (SHOW_PASSWORD) {
            $('.backup_password').attr('type', 'text');
            $(obj).find('i').removeClass('fa-eye-slash').addClass('fa-eye')
        } else {
            $('.backup_password').attr('type', 'password');
            $(obj).find('i').removeClass('fa-eye').addClass('fa-eye-slash')
        }
    }

    function showIntegrationID(obj) {
        SHOW_INTEGRATION_ID = !SHOW_INTEGRATION_ID;
        if (SHOW_INTEGRATION_ID) {
            $('.integration_id').attr('type', 'text');
            $(obj).find('i').removeClass('fa-eye-slash').addClass('fa-eye')
        } else {
            $('.integration_id').attr('type', 'password');
            $(obj).find('i').removeClass('fa-eye').addClass('fa-eye-slash')
        }
    }


    function enableTRA(obj) {
        $('#vfd-config').hide();
        $('#efd-config').hide();
        if (!$(obj).is(':checked')) {
            $('.tra_rc').prop('disabled', true);
        } else {
            $('.tra_rc').prop('disabled', false);
            receiptConfig();
        }
    }

    function enableVfdInputs(obj) {
        let mode = $(obj).val();
        if (mode === `<?=VFD_MODE_LIVE?>`) {
            $('.vfd_live').show('fast');
            $('.vfd_test').hide('fast');
        } else {
            $('.vfd_live').hide('fast');
            $('.vfd_test').show('fast');
        }
    }

    function enableGrouping(obj) {
        $('#tax').val('0');
        if ($(obj).val() === '<?=VFD_TYPE_ZVFD?>') {
            $('.zvfd_special').show();
            $('.vfd_special').hide();
            $('.product-tax-category').hide();
            $('#zvfd-taxcategoryid').prop('required', true);
        } else {
            $('.zvfd_special').hide();
            $('.vfd_special').show();
            $('.product-tax-category').show();
            $('#zvfd-taxcategoryid').prop('required', false);
        }
    }

    function clearDefault(main, sub) {
        $(main).val('0').trigger('change');
        $(sub).empty();
        $(sub).append(`<option selected value="0">--Choose--</option>`);
    }


    function receiptConfig() {
        if ($('.efd').is(':checked')) {
            $('#efd-config').show('slow');
            $('#vfd-config').hide('slow');
        } else {
            $('#efd-config').hide('slow');
            $('#vfd-config').show('slow');
        }

        if ($('.sr').is(':checked')) {
            $('#sr-type').show('slow');
        } else {
            $('#sr-type').hide('slow');
        }
    }


    function configReorder(obj) {
        if ($(obj).val() === '0') {
            $('#reorder-level').hide();
        } else {
            $('#reorder-level').show();
        }
    }

    function enableEfdInputs(obj) {
        let local = $('#local');
        let shared = $('#shared');
        let remote = $('#remote');

        let value = $(obj).val();
        if (value === '<?=EFD_LOCATION_LOCAL?>') {
            local.show();
            shared.hide();
            remote.hide();
            local.find('input').prop('required', true);
            shared.find('input').prop('required', false);
            remote.find('input').prop('required', false);
        }
        if (value === '<?=EFD_LOCATION_SHARED?>') {
            local.hide();
            shared.show();
            remote.hide();
            local.find('input').prop('required', false);
            shared.find('input').prop('required', true);
            remote.find('input').prop('required', false);
        }
        if (value === '<?=EFD_LOCATION_REMOTE?>') {
            local.hide();
            shared.hide();
            remote.show();
            local.find('input').prop('required', false);
            shared.find('input').prop('required', false);
            remote.find('input').prop('required', true);
        }
        if (value === '<?=EFD_LOCATION_DOWNLOAD?>') {
            local.hide();
            shared.hide();
            remote.hide();
            local.find('input').prop('required', false);
            shared.find('input').prop('required', false);
            remote.find('input').prop('required', false);
        }
    }

    function previewFooter() {
        let reader = new FileReader();
        reader.onload = function () {
            let output = document.getElementById('footer-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

</script>