<style>
    .select2-selection .select2-selection--single {
        border: 1px solid grey;
    }

    .below-base-opacity {
        opacity: 0.7;
    }
</style>
<header class="page-header">
    <h2>Price List</h2>
</header>

<div class="col-md-12 p-none">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Price List</h2>
            <form class="row d-flex align-items-center mt-lg mb-lg">
                <input type="hidden" name="module" value="hierarchics">
                <input type="hidden" name="action" value="product_hierarchs_list">
                <div class="col-md-3">
                    <label class="ml-md">Product <span class="text-danger text-lg">*</span></label>
                    <select id="productid" name="productid" class="form-control col-md-3"></select>
                </div>
                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                    <div class="col-md-2">
                        <label class="ml-md">Hierarchic:</label>
                        <select class="form-control" name="hierarchicId">
                            <option selected value="">-- All --</option>
                            <? foreach ($hierarchicList as $key => $R) { ?>
                                <option <?= selected($R['id'], $_GET['hierarchicId']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="ml-md">Branch:</label>
                        <select class="form-control" name="branchid">
                            <? foreach ($branches as $key => $R) { ?>
                                <option <?= selected($R['id'], $_GET['branchid']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                            <? } ?>
                        </select>
                    </div>
                <? } ?>
                <div class="col-md-2">
                    <label class="ml-md">Brand <span class="text-danger text-lg">*</span></label>
                    <select id="brandid" class="form-control" name="brandid">
                        <option value="">-- All --</option>
                        <? foreach ($brands as $key => $R) { ?>
                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                        <? } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ml-md">Category <span class="text-danger text-lg">*</span></label>
                    <select id="categoryid" class="form-control" name="categoryid">
                        <option value="">-- All --</option>
                        <? foreach ($categories as $key => $R) { ?>
                            <option value="<?= $R['id'] ?>"><?= $R['name'] ?></option>
                        <? } ?>
                    </select>
                </div>
                <button class="btn btn-success btn-sm ml-md">Search</button>
            </form>
            <p class="text-primary"><?= $title ?></p>
            <div>
                <button type="button" class="btn btn-primary" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="price-list-datatable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product</th>
                        <? if (CS_SHOW_BRAND) { ?>
                            <th>Brand</th>
                        <? } ?>
                        <? if (CS_SHOW_DEPARTMENT) { ?>
                            <th>Department</th>
                        <? } ?>
                        <th>Tax Category</th>
                        <th>Base Percentage%</th>
                        <?
                        $USER_CAN_EDIT_COSTPRICE = Users::can(OtherRights::edit_costprice);
                        $USER_CAN_EDIT_PRODUCT = Users::can(OtherRights::edit_product);
                        $USER_CAN_EDIT_PRICE = Users::can(OtherRights::edit_price);
                        if ($USER_CAN_EDIT_COSTPRICE) { ?>
                            <th>Cost Price Exc</th>
                        <? } ?>
                        <th></th>
                        <th style="width: 40%;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    foreach ($pricelist as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $id + 1 ?></td>
                            <td><?= $R['productname'] ?></td>
                            <? if (CS_SHOW_BRAND) { ?>
                                <td><?= $R['brandName'] ?></td>
                            <? } ?>
                            <? if (CS_SHOW_DEPARTMENT) { ?>
                                <td><?= $R['departmentName'] ?></td>
                            <? } ?>
                            <td><?= $R['taxCategory'] ?> - <?= $R['vat_percent'] ?>%</td>
                            <td><?= $R['baseprice'] ?></td>
                            <? if ($USER_CAN_EDIT_COSTPRICE) { ?>
                                <td><?= $R['costprice'] ? formatN($R['costprice']) : 'n/a' ?></td>
                            <? } ?>
                            <td>
                                <div class="d-flex flex-column" style="width: 100px">
                                    <? if ($USER_CAN_EDIT_COSTPRICE && $R['costprice']) { ?>
                                        <button class="btn btn-xs btn-primary mb-xs" title="edit cost price" data-target="#edit-costprice-modal"
                                                data-toggle="modal" data-productid="<?= $R['productid'] ?>"
                                                data-costprice="<?= $R['costprice'] ?>"
                                                data-branchid="<?= $R['branchid'] ?>" data-productname="<?= $R['productname'] ?>"
                                                data-branchname="<?= $R['branchname'] ?>">
                                            Edit-costprice
                                        </button>
                                    <? } ?>
                                    <? if ($USER_CAN_EDIT_PRODUCT) { ?>
                                        <a class="btn btn-default btn-xs mb-xs"
                                           href="<?= url('products', 'product_edit', 'id=' . $R['productid'] . '&redirect=' . base64_encode($current_url)) ?>"
                                           title="Edit"> Edit-Product</a>
                                    <? } ?>
                                    <? if ($USER_CAN_EDIT_PRICE) { ?>
                                    <a class="btn btn-default btn-xs mb-xs"
                                       href="<?= url('hierarchics', 'product_hierarchics', [
                                           'productid' => $R['productid'], 'redirect' => base64_encode($current_url)]) ?>"
                                       title="Edit"> Edit-Prices</a>
                                    <?}?>
                                </div>
                            </td>
                            <td>
                                <table class="table table-bordered" style="font-size: 9pt">
                                    <tbody>
                                    <tr class="text-weight-bold">
                                        <td>Hierarchic</td>
                                        <td>Level</td>
                                        <td>Commission %</td>
                                        <td>Target %</td>
                                        <td>Percent %</td>
                                        <td>Price <span class="text-danger">Inc</span></td>
                                        <td></td>
                                    </tr>
                                    <? foreach ($R['hierarchics'] as $index => $h) { ?>
                                        <tr class="<?= $h['below_base'] ? 'text-danger below-base-opacity' : '' ?>"
                                            title="<?= $h['below_base'] ? "below product base percentage {$R['baseprice']} %" : '' ?>">
                                            <td>
                                                <span><?= $h['hierarchicname'] ?></span>
                                                <small class="text-danger ml-sm"><?= $h['source'] ?></small>
                                            </td>
                                            <td>Level <?= $h['level'] ?></td>
                                            <td><?= $h['commission'] ?>%</td>
                                            <td><?= $h['target'] ?>%</td>
                                            <td><?= $h['percentage'] ?>%</td>
                                            <td><?= formatN($h['inc_price']) ?></td>
                                            <td>
                                                <? if ($USER_CAN_EDIT_PRICE && $R['costprice']) { ?>
                                                    <button type="button" class="btn btn-default btn-xs"
                                                            data-target="#edit-modal" title="edit"
                                                            data-toggle="modal" data-productid="<?= $R['productid'] ?>"
                                                            data-costprice="<?= $R['costprice'] ?>"
                                                            data-branchid="<?= $R['branchid'] ?>"
                                                            data-branchname="<?= $R['branchname'] ?>"
                                                            data-vatpercent="<?= $R['vat_percent'] ?>"
                                                            data-productname="<?= $R['productname'] ?>"
                                                            data-hid="<?= $h['hierarchicId'] ?>"
                                                            data-hname="<?= $h['hierarchicname'] ?>"
                                                            data-level="<?= $h['level'] ?>"
                                                            data-percentage="<?= $h['percentage'] ?>"
                                                            data-commission="<?= $h['commission'] ?>"
                                                            data-targetpercent="<?= $h['target'] ?>"
                                                            data-excamount="<?= $h['exc_price'] ?>"
                                                            data-incamount="<?= $h['inc_price'] ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <? if ($h['hpi']) { ?>
                                                        <form action="<?= url('hierarchics', 'delete_price_hierarchic') ?>" method="post"
                                                              style="margin: 0;display: inline;"
                                                              onsubmit="return confirm('Do you want to delete this hierarchic?\n The system will use the default hierarchic percent')">
                                                            <input type="hidden" name="hpi" value="<?= $h['hpi'] ?>">
                                                            <button class="btn btn-default btn-xs" title="delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <? }
                                                } ?>
                                            </td>
                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>


            </div>
            <table class="table table-hover mb-none" id="price-list-table" style="display: none">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>Product</th>
                    <? if (CS_SHOW_BRAND) { ?>
                        <th>Brand</th>
                    <? } ?>
                    <? if (CS_SHOW_DEPARTMENT) { ?>
                        <th>Department</th>
                    <? } ?>
                    <th>Tax Category</th>
                    <th>Base Percentage%</th>
                    <th>Cost Price</th>
                    <th>Hierarchic</th>
                    <th>Level</th>
                    <td>Commission %</td>
                    <td>Target %</td>
                    <th>Percent %</th>
                    <th>Price Inc</th>
                </tr>
                </thead>
                <tbody>
                <?
                $USER_CAN_EDIT_PRICE = Users::can(OtherRights::edit_price);
                foreach ($pricelist as $id => $R) { ?>
                    <? foreach (array_values($R['hierarchics']) as $index => $h) { ?>
                        <? if ($index == 0) { ?>
                            <tr>
                                <td width="80px"><?= $id + 1 ?></td>
                                <td><?= $R['productname'] ?></td>
                                <? if (CS_SHOW_BRAND) { ?>
                                    <td><?= $R['brandName'] ?></td>
                                <? } ?>
                                <? if (CS_SHOW_DEPARTMENT) { ?>
                                    <td><?= $R['departmentName'] ?></td>
                                <? } ?>
                                <td><?= $R['taxCategory'] ?> - <?= $R['vat_percent'] ?>%</td>
                                <td><?= $R['baseprice'] ?></td>
                                <td><?= $R['costprice'] ? formatN($R['costprice']) : 'n/a' ?></td>
                                <td><?= $h['hierarchicname'] ?></td>
                                <td><?= $h['level'] ?></td>
                                <td><?= $h['commission'] ?>%</td>
                                <td><?= $h['target'] ?>%</td>
                                <td><?= $h['percentage'] ?>%</td>
                                <td><?= formatN($h['inc_price']) ?></td>
                            </tr>
                        <? } else { ?>
                            <tr>
                                <td colspan="<?= 7 - (CS_SHOW_BRAND ? 0 : 1) - (CS_SHOW_DEPARTMENT ? 0 : 1) ?>"></td>
                                <td><?= $h['hierarchicname'] ?></td>
                                <td><?= $h['level'] ?></td>
                                <td><?= $h['commission'] ?>%</td>
                                <td><?= $h['target'] ?>%</td>
                                <td><?= $h['percentage'] ?>%</td>
                                <td><?= formatN($h['inc_price']) ?></td>
                            </tr>
                        <? } ?>
                    <? } ?>

                <? } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal fade" id="edit-modal" role="dialog" aria-labelledby="edit-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Hierarchic Price</h4>
                <h5 class="text-primary productname">Product Name</h5>
            </div>
            <form action="<?= url('hierarchics', 'product_hierarchics_save') ?>" method="post">
                <input type="hidden" class="form-control inputs productid" name="productid">
                <input type="hidden" class="form-control inputs branchid" name="branchid">
                <input type="hidden" class="form-control inputs hid" name="hierarchicid[]">
                <div class="modal-body">
                    <div class="row mb-md">
                        <div class="col-md-3">
                            Branch:
                            <input type="text" class="form-control inputs branchname" readonly>
                        </div>
                        <div class="col-md-3">
                            Cost Price:
                            <input type="text" class="form-control inputs costprice" readonly>
                        </div>
                        <div class="col-md-3">
                            VAT %:
                            <input type="text" class="form-control inputs vatpercent" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            Hierarchic:
                            <input type="text" class="form-control inputs hname" readonly>
                        </div>
                        <div class="col-md-2">
                            Level:
                            <input type="text" class="form-control inputs level" readonly>
                        </div>
                        <div class="col-md-2">
                            Percent %:
                            <input type="number" class="form-control inputs percentage" name="percentage[]"
                                   step="0.01" required onkeyup="calculatePrice(true)" onchange="calculatePrice(true)">
                        </div>
                        <div class="col-md-2">
                            Price Exc:
                            <input type="text" readonly class="form-control inputs excamount">
                        </div>
                        <div class="col-md-2">
                            Price: <small class="text-danger">(VAT inclusive)</small>
                            <input type="number" class="form-control inputs incamount"
                                   step="0.01" onkeyup="calculatePrice(false)"
                                   onchange="calculatePrice(false)">
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-2">
                            Commission %:
                            <input type="number" class="form-control inputs commission" name="commission[]" step="0.01" required>
                        </div>
                        <div class="col-md-2">
                            Target %:
                            <input type="number" class="form-control inputs targetpercent" name="target[]" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-costprice-modal" role="dialog" aria-labelledby="edit-costprice-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Cost Price</h4>
                <h5 class="text-primary productname">Product Name</h5>
            </div>
            <form action="<?= url('hierarchics', 'update_costprice') ?>" method="post">
                <input type="hidden" class="form-control inputs productid" name="productid">
                <input type="hidden" class="form-control inputs branchid" name="branchid">
                <div class="modal-body">
                    <div class="row mb-md">
                        <div class="col-md-4">
                            Branch:
                            <input type="text" class="form-control inputs branchname" readonly>
                        </div>
                        <div class="col-md-4">
                            Current Cost Price:
                            <input type="text" class="form-control inputs costprice" readonly>
                        </div>
                        <div class="col-md-4">
                            New Cost Price:
                            <input type="number" step="0.01" min="0" class="form-control inputs new_costprice" name="costprice" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm">Save</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="assets/js/quick_adds.js"></script>
<script src="assets/js/tableToExcel.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $("#brandid,#categoryid").select2({width: '100%'});
        $('#price-list-datatable').DataTable({
            dom: '<"top"fl>t<"bottom"ip>',
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

        $('#edit-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);

            $(modal).find('.inputs').val('');

            $(modal).find('.productid').val(source.data('productid'));
            $(modal).find('.costprice').val(source.data('costprice'));
            $(modal).find('.branchid').val(source.data('branchid'));
            $(modal).find('.branchname').val(source.data('branchname'));
            $(modal).find('.vatpercent').val(source.data('vatpercent'));
            $(modal).find('.productname').text(source.data('productname'));
            $(modal).find('.hid').val(source.data('hid'));
            $(modal).find('.hname').val(source.data('hname'));
            $(modal).find('.level').val(source.data('level'));
            $(modal).find('.percentage').val(source.data('percentage'));
            $(modal).find('.commission').val(source.data('commission'));
            $(modal).find('.targetpercent').val(source.data('targetpercent'));
            $(modal).find('.excamount').val(source.data('excamount'));
            $(modal).find('.incamount').val(source.data('incamount'));

        });

        $('#edit-costprice-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);

            $(modal).find('.inputs').val('');

            $(modal).find('.productid').val(source.data('productid'));
            $(modal).find('.costprice').val(source.data('costprice'));
            $(modal).find('.branchid').val(source.data('branchid'));
            $(modal).find('.branchname').val(source.data('branchname'));
            $(modal).find('.productname').text(source.data('productname'));
            setTimeout(function () {
                $(modal).find('.new_costprice').focus();
            }, 1000);
        });
    });

    function calculatePrice(source_percentage) {
        let modal = $('#edit-modal');
        let costprice = parseFloat($(modal).find('.costprice').val()) || 0;
        let vatpercent = parseFloat($(modal).find('.vatpercent').val()) || 0;
        let price = parseFloat($(modal).find('.incamount').val()) || 0;
        let percentage = parseFloat($(modal).find('.percentage').val()) || 0;

        if (source_percentage) {
            let excamount = costprice * (1 + percentage / 100);
            let incamount = excamount * (1 + vatpercent / 100);
            $(modal).find('.excamount').val(excamount.toFixed(2));
            $(modal).find('.incamount').val(incamount.toFixed(2));
        } else {
            let excamount = price / (1 + (vatpercent / 100));
            percentage = 100 * ((excamount / costprice) - 1);
            $(modal).find('.percentage').val(percentage.toFixed(2));
            $(modal).find('.excamount').val(excamount.toFixed(2));
        }

    }

    function exportExcel(e) {
        console.log('export');
        let table = document.getElementById("price-list-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Price List.xlsx`, // fileName you could use any name
            sheet: {
                name: 'PRICES' // sheetName
            }
        });
    }
</script>