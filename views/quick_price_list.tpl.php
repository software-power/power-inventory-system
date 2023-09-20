<header class="page-header">
    <h2>Quick Prices</h2>
</header>

<div class="d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <? if ($WITH_PURCHASE) { ?>
                    <h2 class="panel-title">Quick Prices With Last Purchase Date</h2>
                <? } else { ?>
                    <h2 class="panel-title">Quick Prices</h2>
                <? } ?>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-9">
                        <form>
                            <input type="hidden" name="module" value="hierarchics">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <div class="row">
                                <div class="col-md-3">
                                    <? if (IS_ADMIN) { ?>
                                        Branch:
                                        <select class="form-control" name="branchid">
                                            <? foreach ($branches as $key => $R) { ?>
                                                <option <?= selected($R['id'], $_GET['branchid']) ?>
                                                        value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                            <? } ?>
                                        </select>
                                    <? } ?>
                                </div>
                                <div class="col-md-3">
                                    Product:
                                    <select id="productid" class="form-control" name="productid"></select>
                                </div>
                                <div class="col-md-3">
                                    Product Category:
                                    <select id="categoryid" class="form-control" name="categoryid">
                                        <option value=""> -- all --</option>
                                        <? foreach ($categories as $key => $R) { ?>
                                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Subcategory:
                                    <select id="subcategoryid" class="form-control" name="subcategoryid">
                                        <option value=""> -- all --</option>
                                        <? foreach ($categories as $key => $R) { ?>
                                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    Brand:
                                    <select id="brandid" class="form-control" name="brandid">
                                        <option value=""> -- all --</option>
                                        <? foreach ($brands as $key => $R) { ?>
                                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-success btn-sm mt-lg">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-primary"><?= $title ?></p>
                <h5 class="text-danger text-weight-bold"><?= $below_base > 0 ? "System found $below_base products have quick price below their base percent" : '' ?></h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="quick-price-table" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Barcode</th>
                            <? if ($WITH_PURCHASE) { ?>
                                <th>Last Purchase date</th>
                            <? } ?>
                            <th>Name</th>
                            <? if (CS_SHOW_GENERIC_NAME) { ?>
                                <th>Generic name</th>
                            <? } ?>
                            <th>Category</th>
                            <th>VAT - (%)</th>
                            <th title="including VAT">Base Price Inc</th>
                            <th>Below Base</th>
                            <th title="including VAT">Quick Price Inc</th>
                            <? if ($WITH_PURCHASE) { ?>
                                <th>Cost price</th>
                            <? } ?>
                            <th class="notexport"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        $USER_CAN_EDIT_PRICE = Users::can(OtherRights::edit_price);
                        $USER_CAN_EDIT_PRODUCT = Users::can(OtherRights::edit_product);
                        foreach ($products as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $id + 1 ?></td>
                                <td><?= $R['barcode_office'] ?: $R['barcode_manufacture'] ?></td>
                                <? if ($WITH_PURCHASE) { ?>
                                    <td><?= $R['last_purchasedate'] ? fDate($R['last_purchasedate']) : '' ?></td>
                                <? } ?>
                                <td><?= $R['productname'] ?></td>
                                <? if (CS_SHOW_GENERIC_NAME) { ?>
                                    <td><?= $R['generic_name'] ?></td>
                                <? } ?>
                                <td><?= $R['productcategory'] ?></td>
                                <td><?= $R['taxcategory'] ?> (<?= $R['vat_rate'] ?>%)</td>
                                <td><?= $R['costprice'] ? formatN($R['inc_base']) : 'n/a' ?></td>
                                <td><?= $R['inc_base'] > $R['inc_quicksale_price'] ? 'Yes' : 'No' ?></td>
                                <td class="<?= $R['inc_base'] > $R['inc_quicksale_price'] ? 'text-danger' : '' ?>">
                                    <strong><?= $R['inc_quicksale_price'] ? formatN($R['inc_quicksale_price']) : 'n/a' ?></strong>
                                </td>
                                <? if ($WITH_PURCHASE) { ?>
                                    <td><?= $R['costprice'] ? formatN($R['costprice']) : 'n/a' ?></td>
                                <? } ?>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <? if ($USER_CAN_EDIT_PRICE) { ?>
                                                <? if ($R['quicksale_price']) { ?>
                                                    <a class="dropdown-item" data-toggle="modal" href="#change-price-modal"
                                                       data-id="<?= $R['productid'] ?>" data-name="<?= $R['productname'] ?>"
                                                       data-branchid="<?= $R['branchid'] ?>" data-branchname="<?= $R['branchname'] ?>"
                                                       data-baseprice="<?= $R['inc_base'] ?>"
                                                       data-quickprice="<?= formatN($R['inc_quicksale_price']) ?>"
                                                       title="Edit Quick Prices">
                                                        <i class="fa-dollar fa"></i> Edit Quick Prices</a>
                                                <? } ?>
                                            <? } ?>
                                            <? if (IS_ADMIN) { ?>
                                                <a class="dropdown-item" target="_blank"
                                                   href="<?= url('products', 'product_admin', 'productid=' . $R['productid']) ?>"
                                                   title="Admin View">
                                                    <i class="fa-arrow-circle-right fa"></i> Admin View</a>
                                            <? } ?>
                                            <? if ($USER_CAN_EDIT_PRODUCT) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('products', 'product_edit', 'id=' . $R['productid']) ?>"
                                                   title="Edit"><i class="fa-pencil fa"></i> Edit Product</a>
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
</div>

<div class="modal fade" id="change-price-modal" tabindex="-1" role="dialog" aria-labelledby="change-price-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="?module=hierarchics&action=update_quick_price" method="post">
                <input type="hidden" class="productid" name="productid">
                <input type="hidden" class="branchid" name="branchid">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Quick Price: <span class="text-primary productname"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="">Branch</label>
                            <input type="text" class="form-control branchname" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Base Price Inc</label>
                            <input type="text" class="form-control base_price" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">Current Quick Price</label>
                            <input type="text" class="form-control old_price" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="">New Quick Price <small class="text-danger">(VAT inclusive)</small></label>
                            <input type="number" min="0" step="0.01" class="form-control new_price"
                                   name="newPrice" placeholder="new price" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm confirm-btn">Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#brandid,#categoryid,#subcategoryid').select2({width: '100%'});
        $('#quick-price-table').DataTable({
            dom: '<"top"fBl>t<"bottom"ip>',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    className: 'btn btn-default',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    }
                }]
        });
    });
    $('#change-price-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        let inc_baseprice = source.data('baseprice');
        $(modal).find('.productid').val(source.data('id'));
        $(modal).find('.productname').text(source.data('name'));
        $(modal).find('.branchid').val(source.data('branchid'));
        $(modal).find('.branchname').val(source.data('branchname'));
        $(modal).find('.old_price').val(source.data('quickprice'));
        $(modal).find('.base_price').val(numberWithCommas(inc_baseprice));
        $(modal).find('.new_price').attr('min', inc_baseprice).attr('title', `Minimum price ${numberWithCommas(inc_baseprice)}`);
    });

    $('#change-price-modal').on('hidden.bs.modal', function (e) {
        let modal = $(this);
        $(modal).find('.productid,').val('');
        $(modal).find('.productname').text('');
        $(modal).find('.old_price').val('');
        $(modal).find('.base_price').val('');
        $(modal).find('.new_price').val('');
        $(modal).find('.branchid').val('');
        $(modal).find('.branchname').val('');
    });

    function numberWithCommas(number) {
        let parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
</script>
