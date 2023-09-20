<style>
    th.stick {
        position: sticky;
        top: 0; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }
</style>
<div class="modal fade" id="product-search-modal" tabindex="-1" role="dialog" aria-labelledby="product-search-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-center modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <div class="d-flex align-items-center">
                    <h4 class="modal-title productName">Search Product</h4>
                    <? if (Users::can(OtherRights::add_product)) { ?>
                        <a href="<?= url('products', 'product_add') ?>" target="_blank" class="btn btn-primary btn-sm ml-xlg"><i
                                    class="fa fa-plus"></i> Add product</a>
                    <? } ?>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mt-sm">
                    <div class="col-lg-6 d-flex">
                        <input id="product-search-input" type="text" class="form-control input-sm input-rounded" placeholder="search product"
                               oninput="search_product(this)">
                        <object id="product-search-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"
                                style="visibility: hidden"></object>
                    </div>
                </div>
                <div class="table-responsive mt-md" style="max-height: 70vh;">
                    <table class="table table-condensed" style="font-size: 10pt;">
                        <thead>
                        <tr>
                            <th class="stick">#</th>
                            <th class="stick">Product name</th>
                            <th class="stick">Product Description</th>
                            <th class="stick">Barcode</th>
                            <th class="stick">Stock</th>
                            <th class="stick"></th>
                        </tr>
                        </thead>
                        <tbody class="tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

    let productSearchModal = $('#product-search-modal');
    let parentCallbackFunc = null;
    let parentLocationObj = null;

    function open_modal(obj, parent_class, callback, location_object) {
        $(parent_class).removeClass('active-group');
        $(obj).closest(parent_class).addClass('active-group');
        parentCallbackFunc = callback ? callback : fetchDetails;
        parentLocationObj = typeof location_object !== 'undefined' && $(location_object).length > 0 ? location_object : $('#locationid');
        // console.log(parentLocationObj, parentLocationObj.val());
        let locationid = $(parentLocationObj).val();
        if (locationid == null || locationid == '') {
            triggerError('Choose location first', 2000);
            return;
        }
        $('#product-search-input').val('');
        $(productSearchModal).find('tbody.tbody').empty();
        $(productSearchModal).modal('show');
    }

    $(productSearchModal).on('shown.bs.modal', function () {
        $('#product-search-input').focus();
    });

    let searchTimer = null;

    function search_product(obj) {
        let search = $(obj).val();
        let spinner = $('#product-search-spinner');

        spinner.css('visibility', 'visible');
        $(productSearchModal).find('tbody.tbody').empty();
        if (searchTimer) clearTimeout(searchTimer);
        if (search.length < 2) {
            spinner.css('visibility', 'hidden');
            return;
        }

        let locationid = $(parentLocationObj).val();
        let proformaid = $('#proformaid').val() || '';
        if (!locationid) {
            triggerError('Select location first!');
            return;
        }


        let search_non_stock = '';
        if (typeof INCLUDE_NON_STOCK !== "undefined") search_non_stock = INCLUDE_NON_STOCK;
        let with_expired = '';
        if (typeof WITH_EXPIRED !== "undefined") with_expired = WITH_EXPIRED;
        searchTimer = setTimeout(function () {
            $.get(`?module=products&action=searchProduct&format=json&include_stock=on&non_stock=${search_non_stock}&with_expired=${with_expired}&locationid=${locationid}&except_proforma=${proformaid}`, {search:search}, function (data) {
                $(productSearchModal).find('tbody.tbody').empty();
                spinner.css('visibility', 'hidden');
                data = JSON.parse(data);
                if (data.result) {
                    if (data.result.length > 0) {
                        let count = 1;
                        $.each(data.result, function (i, item) {
                            let row = `<tr>
                                            <td>${count}</td>
                                            <td>
                                                <p class="m-none">${item.name}</p>
                                                <small class="text-danger">${item.non_stock == 1 ? 'non-stock' : ''}</small>
                                            </td>
                                            <td>${item.description}</td>
                                            <td>${item.barcode_office}</td>
                                            <td class="text-weight-bold text-success">${item.non_stock == 1 ? '-' : (item.total || 0)}</td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-sm" data-stockid="${item.stockid || ''}"
                                                    data-productid="${item.productid}" data-nonstock="${item.non_stock == 1 ? 1 : ''}"
                                                    data-productname="${item.name}" data-description="${item.description}"
                                                    data-stockqty="${item.total || 0}" data-vatrate="${item.vatPercent || ''}"
                                                    onclick="parentCallbackFunc(this)">select</button>
                                            </td>
                                        </tr>`;
                            $(productSearchModal).find('tbody.tbody').append(row);
                            count++;
                        });
                    } else {
                        triggerError('No product found');
                    }
                } else {
                    triggerError('Error found');
                }
            });
        }, 250);
    }

</script>