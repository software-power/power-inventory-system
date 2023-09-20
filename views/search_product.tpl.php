<style>
    .select2-selection .select2-selection--single {
        border: 1px solid grey;
    }

    .below-base-opacity {
        opacity: 0.7;
    }
</style>
<header class="page-header">
    <h2>Search Product</h2>
</header>

<div class="col-md-12 p-none">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Search Product</h2>
            <div class="row d-flex justify-content-center">
                <div class="col-md-3">
                    <input type="text" readonly class="form-control productname" placeholder="search product" onclick="open_modal()" style="background-color: white;cursor: pointer;">
                </div>
            </div>
            <p class="text-primary"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <?if($product){?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-none" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Non-Stock</th>
                            <th>Department</th>
                            <th>VAT %</th>
                            <th>Category</th>
                            <th>Subcategory</th>
                            <th>Unit</th>
                            <th>Bulk Unit</th>
                            <th>Manufacture Barcode</th>
                            <th>Other Barcode</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        foreach ($product as $id => $R) { ?>
                            <tr>
                                <td style="width: 15%;"><?= $R['name'] ?></td>
                                <td><?= $R['description'] ?></td>
                                <td><?= $R['non_stock'] ? 'Yes' : 'No' ?></td>
                                <td><?= $R['departmentName'] ?></td>
                                <td><?= $R['categoryName'] ?> <?= $R['vatPercent'] ?>%</td>
                                <td><?= $R['productcategory'] ?></td>
                                <td><?= $R['productsubcategory'] ?></td>
                                <td><?= $R['unitname'] ?> (<?= $R['unitabbr'] ?>)</td>
                                <td><?= $R['bulk_unit_name'] ?> (<?= $R['bulk_unit_abbr'] ?>)</td>
                                <td><?= $R['barcode_manufacture'] ?></td>
                                <td><?= $R['barcode_office'] ?></td>
                                <td><?= $R['status'] ?></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    <?$USER_CAN_ADMIN_VIEW = Users::can(OtherRights::admin_view);
                    $USER_CAN_EDIT = Users::can(OtherRights::edit_product);
                    $USER_CAN_EDIT_PRICE = Users::can(OtherRights::edit_price);
                    $USER_CAN_UPLOAD_IMAGE = Users::can(OtherRights::upload_product_image);
                    $USER_CAN_PRINT_BARCODE = Users::can(OtherRights::print_barcode);
                    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    ?>

                    <? if ($USER_CAN_EDIT) { ?>
                        <a class="btn btn-default ml-sm"
                           href="<?= url('products', 'product_edit', 'id=' . $R['productid'].'&redirect='.base64_encode($current_url)) ?>"
                           title="Edit"><i class="fa-pencil fa"></i> Edit Product</a>
                    <? } ?>
                    <? if ($USER_CAN_ADMIN_VIEW) { ?>
                        <a class="btn btn-default ml-sm" target="_blank"
                           href="<?= url('products', 'product_admin', 'productid=' . $R['id']) ?>"
                           title="Admin View"><i class="fa-arrow-circle-right fa"></i> Admin View</a>
                    <? } ?>
                    <? if ($USER_CAN_EDIT_PRICE) { ?>
                        <a class="btn btn-default ml-sm"
                           href="<?= url('hierarchics', 'product_hierarchics', [
                               'productid' => $R['id'],
                               'redirect' => base64_encode($current_url)]) ?>"
                           title="Edit Prices"><i class="fa-dollar fa"></i> Edit Prices</a>
                    <? } ?>
                    <a data-productid="<?= $R['productid'] ?>" class="btn btn-default ml-sm"
                       href="#product-view-modal" title="Product view" data-toggle="modal">
                        <i class="fa-eye fa"></i> Product View</a>
                    <? if ($USER_CAN_UPLOAD_IMAGE) { ?>
                        <a class="btn btn-default ml-sm"
                           href="<?= url('products', 'image_upload', ['id' => $R['id']]) ?>"
                           title="Edit Prices"><i class="fa fa-picture-o"></i> Upload Image</a>
                    <? } ?>
                    <? if ($USER_CAN_PRINT_BARCODE && !$R['non_stock']) { ?>
                        <a class="btn btn-default ml-sm"
                           href="<?= url('products', 'generate_barcode', ['productid' => $R['id']]) ?>"
                           title="Edit Prices"><i class="fa fa-barcode"></i> Print Barcode</a>
                    <? } ?>
                </div>
            <?}?>
        </div>
    </section>
</div>

<?=component('grn/product_search_modal.tpl.php')?>
<?=component('shared/product_view_modal.tpl.php')?>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $("#brandid,#categoryid").select2({width: '100%'});
        $('#price-list-datatable').DataTable({
            dom: '<"top"l>t<"bottom"ip>',
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

    function fetchDetails(obj) {
        let productid = $(obj).data('productid');
        console.log(productid);

        window.location.replace(`<?=url('products', 'search_product')?>&productid=${productid}`);
    }
</script>