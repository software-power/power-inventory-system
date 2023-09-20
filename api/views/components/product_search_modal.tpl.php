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
                </div>
            </div>
            <div class="modal-body">
                <div class="row mt-sm  d-flex">
                    <div class="col-sm-8 col-lg-6">
                        <input id="product-search-input" type="text" class="form-control input-sm input-rounded"
                               placeholder="search product"
                               oninput="search_product(this)">
                    </div>
                    <div>
                        <div id="product-search-spinner" style="display:none ">
                            <div class="d-flex align-items-center">
                                <span class="spinner-border text-danger mr-xs" style="height: 30px;width: 30px;"></span>
                                <span>please wait ...</span>
                            </div>
                        </div>
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
    let callbackFunc;

    function open_modal(obj, callback) {

        callbackFunc=callback;

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

        spinner.show();
        $(productSearchModal).find('tbody.tbody').empty();
        if (searchTimer) clearTimeout(searchTimer);
        if (search.length < 2) {
            spinner.hide();
            return;
        }
        searchTimer = setTimeout(function () {
            $.get(`?module=endpoints&action=ajax_searchProduct`, {search: search,access_token:''}, function (data) {
                $(productSearchModal).find('tbody.tbody').empty();
                spinner.hide();
                // data = JSON.parse(data);
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
                                                <button type="button" class="btn btn-default btn-sm" data-productid="${item.productid}" data-source="find"
                                                    data-productname="${item.name}" data-description="${item.description}" onclick="callbackFunc(this)">select</button>
                                            </td>
                                        </tr>`;
                            $(productSearchModal).find('tbody.tbody').append(row);
                            count++;
                        });
                    } else {
                        notifyError('No product found');
                    }
                } else {
                    notifyError('Error found');
                }
            });
        }, 250);
    }

</script>