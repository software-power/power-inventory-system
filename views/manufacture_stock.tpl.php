<style>
    #spinnerHolder {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100vh;
        width: 100%;
        display: none;
        background-color: black;
        opacity: 0.5;
        z-index: 10000;
    }

    .group {
        /*border: 1px dashed grey;*/
        /*border-radius: 7px;*/
        margin-bottom: 15px;
        padding: 35px 5px 5px 5px;
        position: relative;
    }

    input.productname, input.end_productname {
        cursor: pointer !important;
        background: white !important;
    }

    .end-products-holder {
        /*max-height: 30vh;*/
        /*overflow-y: auto;*/
    }

    .end-product {
        position: relative;
    }

    .end-product:not(:first-of-type) {
        border-top: 3px solid #dadada;
        margin-top: 15px;
        padding-top: 10px;
    }

    .remarks {
        font-size: 9pt;
    }

    .border-danger, .input-error {
        border: 1px solid red;
    }
</style>
<header class="page-header">
    <h2>Manufacture Stock</h2>
</header>


<div id="spinnerHolder">
    <div style="height: 100%;display: flex;justify-content: center;align-items: center;">
        <object id="saveCardSpinner" data="images/loading_spinner.svg" type="image/svg+xml" height="200"
                width="200"></object>
        <h4>Please wait</h4>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="panel-title">Manufacture Stock</h2>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <a href="?" class="btn btn-default">
                            <i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <form action="<?= url('stocks', 'save_manufacture') ?>" method="post"
                      onsubmit="return validateInputs()">
                    <input type="hidden" name="manufacture[id]" value="<?= $manufacture['id'] ?>">
                    <input type="hidden" name="manufacture[token]" value="<?= unique_token() ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Location:</h5>
                            <select id="locationid" class="form-control" name="manufacture[locationid]" required
                                    onchange="setLocation(this)">
                                <? if ($manufacture) { ?>
                                    <option value="<?= $manufacture['locationid'] ?>"><?= $manufacture['locationname'] ?>
                                        - <?= $manufacture['branchname'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <h5>Remarks:</h5>
                            <textarea name="manufacture[remarks]" class="form-control remarks" rows="3"
                                      required><?= $manufacture['remarks'] ?></textarea>
                        </div>
                    </div>
                    <div class="mt-xlg">
                        <h5 class="text-weight-bold">Items</h5>
                    </div>
                    <div id="items-holder">
                        <? if (isset($manufacture)) {
                            foreach ($manufacture['details'] as $detail) {
                                ?>
                                <?= component('stock/manufacture_detail_item.tpl.php', compact('detail')) ?>
                            <? }
                        } ?>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" onclick="addItem()">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-4">
                            <button class="btn btn-success btn-block">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?= component('normal-order/product_search_modal.tpl.php') ?>

<script src="assets/js/quick_adds.js"></script>
<script>


    $(function () {
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        <?if(isset($manufacture)){?>

        $('.group .raw-material .serial-modal').each(function (i, modal) {
            populateSerialTable(modal);
        });

        <?}?>
    });

    function validateInputs() {
        if ($('.group').length < 1) {
            triggerError('Choose at-least one item!');
            return false;
        }

        let valid = true;
        $('.group').each(function (i, group) {
            let stockid = $(group).find('.raw-material .stockid').val();
            let productname = $(group).find('.raw-material .productid').text();
            let qty = parseInt($(group).find('.raw-material .qty').val()) || 0;
            let max_raw_material_qty = parseInt($(group).find('.raw-material .qty').attr('max')) || 0;
            let total_raw_material_costprice = removeCommas($(group).find('.raw-material .total_costprice').val());
            if (stockid == null || stockid == '' || qty <= 0) {
                triggerError('Choose raw material', 3000);
                $(group).find('.raw-material .qty').focus();
                valid = false;
                return false;
            }
            if (qty > max_raw_material_qty) {
                triggerError('Raw material used exceed available stock qty', 3000);
                $(group).find('.raw-material .current_stock').focus();
                valid = false;
                return false;
            }

            //validate serial nos
            let serial_modal = $(group).find('.serial-modal');
            if (serial_modal.length > 0) {
                let serialno_qty = $(serial_modal).find('tbody tr').length;
                // console.log(qty,serialno_qty);
                if (qty !== serialno_qty) {
                    triggerError("Serial number qty does not match raw material qty", 6000);
                    $(group).find('.raw-material .qty').focus();
                    valid = false;
                    return false;
                }

                $(serial_modal).find('tbody tr').each(function (i, tr) {
                    let icon = $(tr).find('td.status i');  //status icon
                    console.log(icon);
                    if ($(icon).hasClass('fa-close')) { //not validated
                        valid = false;
                        triggerError(`${productname} Serial number not validated!`, 2500);
                        $(group).find(".serialBtn").addClass('btn-danger').focus();//focus product
                        setTimeout(function () {
                            $(group).find(".serialBtn").removeClass('btn-danger');
                        }, 2000);
                        return false;
                    }
                });
            }
            if (!valid) return false;

            //validate end products
            let end_products = $(group).find('.end-product');
            if (end_products.length === 0) {
                triggerError(`No end products found for product ${productname}`, 3000);
                $(group).find('.raw-material .qty').focus();
                valid = false;
                return false;
            }

            //check total costprice & qty
            let total_end_product_costprice = 0;
            let valid_end_products = true;
            $(end_products).each(function (i, item) {
                let end_productname = $(item).find('.end_productname').text();
                let end_qty = parseInt($(item).find('.end_qty').val()) || 0;
                let end_new_costprice = removeCommas($(item).find('.end_new_costprice').val());
                let end_total_costprice = removeCommas($(item).find('.end_total_costprice').val()) || 0;
                total_end_product_costprice += end_total_costprice;

                if (end_qty <= 0) {
                    triggerError(`Enter valid qty for end product ${end_productname}`, 3000);
                    $(group).find('.item .end_qty').focus();
                    valid_end_products = valid = false;
                    return false;
                }
                if (isNaN(end_new_costprice) || end_new_costprice < 0) {
                    triggerError(`Enter valid costprice for end product ${end_productname}`, 3000);
                    $(group).find('.item .end_new_costprice').focus();
                    valid_end_products = valid = false;
                    return false;
                }
            });
            if (!valid_end_products) return false;

            //compare total costs
            if (total_end_product_costprice < total_raw_material_costprice) {
                triggerError(`Total end products costprice cant be below total raw material cost`, 3000);
                $(group).find('.raw-material .overall_end_products_costprice').focus();
                valid = false;
                return false;
            }
        });
        if (!valid) return false;
        $('#spinnerHolder').show();
    }

    function setLocation() {
        $('#items-holder').empty();
        addItem();
    }

    function addItem() {
        let item = `<div class="row m-xs group">
                                <button type="button" class="btn btn-danger"
                                        style="position: absolute;top:5px;right: 5px;"
                                        onclick="removeRow(this);">
                                    <i class="fa fa-remove"></i>
                                </button>
                                <div class="col-md-6 raw-material" style="border: 1px dashed #4f874f;">
                                    <h5 class="text-weight-bold text-center m-xs">Raw Material</h5>
                                    <div class="row">
                                        <div class="col-md-6 p-none pl-xs pr-xs" style="position: relative;">
                                            <span>Product</span>
                                            <object class="loading-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="30"
                                                    width="30"
                                                    style="display:none;position: absolute;top: -10px;right: 0;z-index: 1000"></object>
                                            <input type="hidden" name="raw_materials[stockid][]" class="inputs stockid">
                                            <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                                                   onclick="open_modal(this,'.group',fetchDetails)">
                                            <input type="hidden" class="inputs productid">
                                        </div>
                                        <div class="col-md-3 p-none pr-xs">
                                            Current Stock
                                            <input type="text" class="form-control inputs current_stock" name="raw_materials[current_stock][]"
                                                   required
                                                   readonly>
                                        </div>
                                        <div class="col-md-3 p-none pr-xs">
                                            Cost price
                                            <input type="text" class="form-control inputs costprice" name="raw_materials[costprice][]" required
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 p-none pl-xs pr-xs">
                                            Description
                                            <textarea rows="3" class="form-control inputs product_description" readonly></textarea>
                                        </div>
                                        <div class="col-md-6 p-none">
                                            <div class="col-md-6 p-none pr-xs">
                                                Total Cost price
                                                <input type="text" class="form-control inputs total_costprice" readonly>
                                            </div>
                                            <div class="col-md-6 p-none pr-xs">
                                                Using Qty
                                                <input type="text" class="form-control inputs qty" name="raw_materials[qty][]" required
                                                       oninput="checkRawMaterialQty(this)">
                                            </div>
                                            <div class="col-md-6 p-none pr-xs">
                                                Overall end products Costprice
                                                <input type="text" class="form-control text-danger inputs overall_end_products_costprice" readonly>
                                            </div>
                                            <div class="col-md-12 p-none pr-xs d-flex justify-content-end pt-xs pb-xs">
                                                <div class="serialno-holder"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" style="border: 1px dashed #cd705d;">
                                    <h5 class="text-weight-bold text-center m-xs">End Product</h5>
                                    <div class="end-products-holder"></div>
                                    <div class="mt-xs mb-xs">
                                        <button type="button" class="btn btn-primary btn-sm add-end-product-btn" onclick="addEndProduct(this)">Add End
                                            product
                                        </button>
                                    </div>
                                </div>
                            </div>`;
        $('#items-holder').append(item);
    }

    function addEndProduct(obj) {
        let group = $(obj).closest('.group');
        let stockid = $(group).find('.stockid').val();
        // console.log(stockid);
        if (!stockid || stockid == '') {
            triggerError('Choose raw material first', 2000);
            return;
        }
        let row = `<div class="end-product">
                       <button type="button" class="btn btn-warning btn-sm" title="remove product"
                               style="position: absolute;top: -6px;right: -14px;z-index: 2;" onclick="removeEndProduct(this);">
                           <i class="fa fa-trash"></i>
                       </button>
                       <div class="row">
                           <div class="col-md-6 p-none pl-xs pr-xs" style="position: relative;">
                               <span>Product</span>
                               <object class="end-product-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="30"
                                       width="30" style="display:none;position: absolute;top: -10px;right: 0;z-index: 1000"></object>
                                <input type="text" readonly class="form-control inputs end_productname" placeholder="search product"
                                           onclick="open_modal(this,'.end-product',fetchEndProductDetails)">
                                <input type="hidden" class="inputs end_productid" name="end_products[${stockid}][productid][]" required>
                           </div>
                           <div class="col-md-3 p-none pr-xs">
                               <div class="d-flex align-items-center">
                                    <span>New Qty</span>
                                    <object class="end-qty-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="25"
                                    width="25" style="display:none;top: 0;right: 0;z-index: 1000"></object>
                                </div>
                               <input type="text" class="form-control inputs end_qty" name="end_products[${stockid}][qty][]" data-source="end_qty" required oninput="calEndProductCostprice(this)">
                           </div>
                           <div class="col-md-3 p-none pr-xs">
                               Total Cost
                               <input type="text" class="form-control end_total_costprice" readonly>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-md-6 p-none pl-xs pr-xs">
                               Description
                               <textarea rows="3" class="form-control inputs end_product_description" readonly></textarea>
                           </div>
                           <div class="col-md-6 p-none pl-xs pr-xs">
                                <div class="col-md-6 p-none pr-xs">
                                    Current unit cost
                                    <input type="text" class="form-control end_costprice" readonly>
                                </div>
                                <div class="col-md-6 p-none pr-xs">
                                    <div class="d-flex align-items-center">
                                         <span>New cost price</span>
                                         <object class="end-costprice-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="25"
                                         width="25" style="display:none;top: 0;right: 0;z-index: 1000"></object>
                                     </div>
                                    <input type="text" class="form-control inputs end_new_costprice" name="end_products[${stockid}][costprice][]" data-source="end_new_costprice" required oninput="calEndProductCostprice(this)">
                                </div>
                                <div class="col-md-6 p-none pr-xs">
                                    Current Quick Price Inc
                                    <input type="text" class="form-control end_costprice" readonly>
                                </div>
                                <div class="col-md-6 p-none pr-xs">
                                    <div class="d-flex align-items-center">
                                         <span>New quick price inc</span>
                                         <object class="end-quickprice-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="25"
                                         width="25" style="display:none;top: 0;right: 0;z-index: 1000"></object>
                                    </div>
                                    <input type="text" class="form-control inputs end_new_quickprice" name="end_products[${stockid}][quickprice][]" required oninput="checkQuickPrice(this)">
                                    <input type="hidden" class="inputs end_base_percentage">
                                    <input type="hidden" class="inputs end_vat_rate">
                                    <small class="text-danger quickprice-error"></small>
                                </div>
                           </div>
                       </div>
                   </div>`;
        $(group).find('.end-products-holder').append(row);
    }

    function removeEndProduct(obj) {
        $(obj).closest('.end-product').remove();
    }

    function removeRow(obj) {
        $(obj).closest('.group').remove();
    }

    function fetchDetails(obj) {
        let group = $('.group.active-group');
        let locationid = $('#locationid').val();
        let productId = $(obj).data('productid');
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');
        let stockqty = parseInt($(obj).data('stockqty')) || 0;
        $(group).find('.end-products-holder').empty();
        if (productId == null || productId == '') {
            triggerError('Product info not found');
            return;
        }

        //check duplicate
        if ($(`.raw-material input.productid[value='${productId}']`).length > 0) {
            triggerError(`Product already selected`, 2000);
            return;
        }

        if (stockqty <= 0) {
            triggerError(`No enough stock for product ${productname}`, 2000);
            return;
        }

        $(group).find('.raw-material .form-control.inputs').val('');
        $(group).find('.raw-material .productid').val(productId);
        $(group).find('.raw-material .productname').val(productname);
        $(group).find('.raw-material .product_description').val(description);

        $(group).removeClass('active-group');
        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();

        let spinner = $(group).find('.loading-spinner');
        spinner.show();
        //ajax
        $.get(`?module=stocks&action=getAdjustmentStock&format=json`, {
            locationId: locationid,
            productId: productId,
            with_cost: 1
        }, function (data) {
            let result = JSON.parse(data);
            spinner.hide();
            console.log(result);
            if (result.length > 0) {
                let product = result[0];
                $(group).find('.raw-material .stockid').val(product.stockid);
                $(group).find('.raw-material .current_stock').val(product.total);
                $(group).find('.raw-material .costprice').val(numberWithCommas(product.costprice));
                $(group).find('.raw-material .qty').attr('max', product.total).attr('min', 1).val(1);
                $(group).find('.raw-material .total_costprice').val(numberWithCommas(product.costprice));

                $(group).find('.raw-material .serialno-holder').empty();

                qtyInput($(group).find('.qty'));
                if (product.trackserialno == 1) {
                    addSerialModal($(group).find('.raw-material'));
                    populateSerialTable($(group).find('.raw-material'));
                }

                $(group).find('.add-end-product-btn').trigger('click');
            } else {
                triggerError(`No stock found for ${productname}`, 5000);
            }
        });
    }


    function addSerialModal(obj) {
        let group = $(obj).closest('.group');
        let stockid = $(group).find('.raw-material .stockid').val();
        let productname = $(group).find('.raw-material .productname').val();
        let serialModal = `<button type="button" class="btn btn-default btn-sm serialBtn" title="Serial numbers"
                                data-toggle="modal" data-target="#serialModal${stockid}">
                                <i class="fa fa-barcode"></i> Serial no
                            </button>
                            <div class="modal fade serial-modal" id="serialModal${stockid}" tabindex="-1" role="dialog" aria-labelledby="serialModal${stockid}"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-center">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title productName">Serial No</h4>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="text-primary">${productname}</h5>
                                            <div style="max-height: 60vh; overflow-y: auto;">
                                                <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                                    <thead>
                                                    <tr>
                                                        <td>Serial Number</td>
                                                        <td>Status</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
        $(group).find('.raw-material .serialno-holder').append(serialModal);
    }

    function populateSerialTable(obj) {
        let group = $(obj).closest('.group');
        let qty = parseInt($(group).find('.raw-material .qty').val());
        let stockid = $(group).find('.raw-material .stockid').val();
        $(group).find('.raw-material .serial-modal tbody tr').remove();
        // console.log(qty, 'stockid', stockid);
        for (let i = 0; i < qty; i++) {
            let row = `<tr>
                           <td>
                               <input type="text" class="form-control input-sm serial_number" name="serialno[${stockid}][serial_number][]"
                                   autocomplete="off" onchange="validateSerialNo(this)">
                           </td>
                           <td class="status" style="text-align: center;vertical-align: middle;">
                               <object class="validate-spinner" data="images/loading_spinner.svg"
                                style="display: none" type="image/svg+xml" height="25" width="25"></object>
                               <i class="fa fa-close text-danger text-weight-bold" style="display: none"></i>
                           </td>
                       </tr>`;
            $(group).find('.serial-modal tbody').append(row);
        }
    }

    let serialValidateTimer = null;

    function validateSerialNo(obj) {
        let parentSerialRow = $(obj).closest('tr');
        let spinner = $(parentSerialRow).find('.validate-spinner');


        let icon = $(parentSerialRow).find('.status i');
        icon.hide();
        icon.addClass('fa-close text-danger');
        icon.removeClass('fa-check text-success');

        if (duplicateSerialNo()) {
            triggerError('Duplicate serial no found!', 5000);
            return;
        }


        spinner.show();
        if (serialValidateTimer) clearTimeout(serialValidateTimer);
        serialValidateTimer = setTimeout(function () {
            let number = $.trim($(obj).val());
            $(obj).val(number);
            let stockid = $(obj).closest('.group').find(`.raw-material .stockid`).val();
            // console.log(number, 'stockid', stockid);
            //ajax query
            $.get(`?module=serialnos&action=validateSerialno&format=json&number=${number}&stockid=${stockid}`, null, function (data) {
                let result = JSON.parse(data);
                spinner.hide();
                // console.log(result);
                if (result.status === 'success') {
                    triggerMessage(result.message);
                    parentSerialRow.attr('title', result.message);
                    icon.removeClass('fa-close text-danger')
                        .addClass('fa-check text-success');
                } else {
                    triggerError(result.message);
                    parentSerialRow.attr('title', result.message);
                }
                icon.show();
            });
        }, 250);
    }

    function duplicateSerialNo() {
        let serial_numbers = [];
        $('.serial_number').each(function () {
            let sno = $.trim($(this).val());
            if (sno) {
                serial_numbers.push(sno);
            }
        });

        let sorted = serial_numbers.slice().sort();
        let duplicates = [];
        for (let i = 0; i < sorted.length - 1; i++) {
            if (sorted[i] === sorted[i + 1]) {
                duplicates.push(sorted[i]);
            }
        }

        $('.serial_number').each(function () {
            let sno = $.trim($(this).val());
            if ($.inArray(sno, duplicates) !== -1) {
                $(this).addClass('input-error');
            } else {
                $(this).removeClass('input-error');
            }
        });
        return duplicates.length > 0;
    }


    function fetchEndProductDetails(obj) {
        let end_product = $('.end-product.active-group');
        let group = $(end_product).closest('.group');
        let locationid = $('#locationid').val();
        let productId = $(obj).data('productid');
        let productname = $(obj).data('productname');
        let description = $(obj).data('description');
        let stockid = $(group).find('.stockid').val();
        let raw_material_productid = $(group).find('.raw-material .productid').val();
        if (productId == null || productId == '') {
            triggerError('Product info not found');
            return;
        }

        if (productId == raw_material_productid) {
            triggerError(`Raw material product cant be used as end product`);
            return;
        }

        //check duplicate end products
        if ($(group).find(`.end-product input.end_productid[value='${productId}']`).length > 0) {
            triggerError(`End product already selected`, 2000);
            return false;
        }

        if (!stockid || stockid == '') {
            triggerError('Choose raw material first');
            $(group).find('.raw-material .qty').focus();
            return;
        }

        $(end_product).find('.form-control.inputs').val('');
        $(end_product).find('.end_productid').val(productId);
        $(end_product).find('.end_productname').val(productname);
        $(end_product).find('.end_product_description').val(description);

        $(end_product).removeClass('active-group');
        $(productSearchModal).modal('hide');
        $(productSearchModal).find('tbody.tbody').empty();

        let spinner = $(end_product).find('.end-product-spinner');
        spinner.show();
        //ajax
        $.get(`?module=products&action=getProductDetailsForGRN&format=json`, {
            productid: productId,
            locationid: locationid
        }, function (data) {
            let result = JSON.parse(data);
            spinner.hide();
            // console.log(result);
            // return;
            if (result.status === 'success') {
                let product = result.data;
                $(end_product).find('.end_costprice,.end_new_costprice,.end_total_costprice')
                    .attr('min', 0)
                    .val(numberWithCommas(product.costprice));
                $(end_product).find('.end_base_percentage').val(product.baseprice);
                $(end_product).find('.end_new_quickprice').val(product.quicksale_price);
                $(end_product).find('.end_vat_rate').val(product.category.vat_percent);
                $(end_product).find('.end_qty').attr('min', 1).val(1);

                calEndProductCostprice($(end_product).find('.end_new_quickprice'));
                qtyInput($(end_product).find('.end_qty'));
                thousands_separator($(end_product).find('.end_new_costprice, .end_new_quickprice'));
            } else {
                triggerError(`Product not found`, 5000);
            }
        });
    }

    function checkRawMaterialQty(obj) {
        let raw_material = $(obj).closest('.raw-material');
        let qty = parseInt($(raw_material).find('.qty').val()) || 0;
        let max_qty = parseInt($(raw_material).find('.qty').attr('max')) || 0;
        let costprice = removeCommas($(raw_material).find('.costprice').val());

        if (qty > max_qty) {
            triggerError(`Qty cant exceed available stock qty`);
            $(raw_material).find('.current_stock').addClass('border-danger');
            setTimeout(function () {
                $(raw_material).find('.current_stock').removeClass('border-danger')
            }, 1000);
            $(raw_material).find('.qty').val(max_qty);
            checkRawMaterialQty(obj);
            return;
        }

        let total_costprice = (qty * costprice).toFixed(2);
        $(raw_material).find('.total_costprice').val(numberWithCommas(total_costprice));
        populateSerialTable(obj);
    }

    let inputTimer = null;
    let delayTime = 1000;

    function calEndProductCostprice(obj) {
        let end_product = $(obj).closest('.end-product');
        let qty = parseInt($(end_product).find('.end_qty').val()) || 0;
        let new_costprice = removeCommas($(end_product).find('.end_new_costprice').val()) || 0;
        let source = $(obj).data('source');
        let qty_spinner = $(end_product).find('.end-qty-spinner');
        let costprice_spinner = $(end_product).find('.end-costprice-spinner');

        if (inputTimer) {
            clearTimeout(inputTimer);
            inputTimer = null;
        }

        if (source === 'end_qty') {
            qty_spinner.show();
            inputTimer = setTimeout(function () {
                if (qty <= 0) {
                    triggerError(`End product qty cant be below 1`);
                    $(end_product).find('.end_qty').focus().val(1);
                    calEndProductCostprice(obj);
                    return;
                }
                finalizeCal();
            }, delayTime);
        } else if (source === 'end_new_costprice') {
            costprice_spinner.show();
            inputTimer = setTimeout(function () {
                $(end_product).find('.end_new_costprice').val(new_costprice);
                if (new_costprice < 0) {
                    triggerError(`Enter valid costprice`);
                    // console.log(new_costprice);
                    $(end_product).find('.end_new_costprice').val(0);
                    calEndProductCostprice(obj);
                    return;
                }
                thousands_separator($(end_product).find('.end_new_costprice'));
                finalizeCal();
            }, delayTime);
        } else {
            finalizeCal();
        }

        function finalizeCal() {
            qty_spinner.hide();
            costprice_spinner.hide();
            let end_total_costprice = (qty * new_costprice).toFixed(2);
            $(end_product).find('.end_total_costprice').val(numberWithCommas(end_total_costprice));
            checkQuickPrice(obj);
            calOverallEndProductCostprice(obj);
        }
    }

    function checkQuickPrice(obj) {
        let end_product = $(obj).closest('.end-product');
        let new_costprice = removeCommas($(end_product).find('.end_new_costprice').val()) || 0;
        let new_quickprice = removeCommas($(end_product).find('.end_new_quickprice').val()) || 0;
        let base_percentage = removeCommas($(end_product).find('.end_base_percentage').val()) || 0;
        let new_vat_rate = removeCommas($(end_product).find('.end_vat_rate').val()) || 0;

        let incbase_price = (new_costprice * (1 + base_percentage / 100) * (1 + new_vat_rate / 100)).toFixed(2);
        // console.log('incbase: ', incbase_price, 'quick: ', new_quickprice);
        if (new_quickprice < incbase_price) {
            $(end_product).find('.quickprice-error')
                .text(`below ${base_percentage}% base percent, ${numberWithCommas(incbase_price)}`);
        } else {
            $(end_product).find('.quickprice-error').text('');
        }

    }

    function calOverallEndProductCostprice(obj) {
        let overall_total = 0;
        let group = $(obj).closest('.group');
        $(group).find('.end-product .end_total_costprice').each(function (i, item) {
            overall_total += removeCommas($(item).val()) || 0;
        });
        overall_total = overall_total.toFixed(2);
        $(group).find('.raw-material .overall_end_products_costprice').val(numberWithCommas(overall_total));
    }

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

    function removeCommas(amount) {
        return parseFloat(amount.replace(/,/g, ''));
    }
</script>