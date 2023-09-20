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
                    <?if(Users::can(OtherRights::add_product)){?>
                        <a href="<?=url('products', 'product_add')?>" target="_blank" class="btn btn-primary btn-sm ml-xlg"><i class="fa fa-plus"></i> Add product</a>
                    <?}?>
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

    function open_modal(obj) {
        $('.group').removeClass('active-group');
        $(obj).closest('.group').addClass('active-group');
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
        searchTimer = setTimeout(function () {
            $.get(`?module=products&action=searchProduct&format=json&non_stock=no&search=${search}`, null, function (data) {
                $(productSearchModal).find('tbody.tbody').empty();
                spinner.css('visibility', 'hidden');
                data = JSON.parse(data);
                if (data.result) {
                    if (data.result.length > 0) {
                        let count = 1;
                        $.each(data.result, function (i, item) {
                            let row = `<tr>
                                            <td>${count}</td>
                                            <td>${item.name}</td>
                                            <td>${item.description}</td>
                                            <td>${item.barcode_office}</td>
                                            <td>
                                                <button type="button" class="btn btn-default btn-sm" data-productid="${item.productid}"
                                                    data-productname="${item.name}" data-description="${item.description}" onclick="fetchDetails(this)">select</button>
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