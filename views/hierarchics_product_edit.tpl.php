<header class="page-header">
    Product Hierarchic Prices
</header>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <section class="panel center-panel">
            <header class="panel-heading d-flex align-items-center">
                <h2 class="panel-title col-md-4">Product : <span class="text-primary"><?= $productPrices[0]['productname'] ?></span></h2>
                <form id="search-form" class="col-md-4 d-flex align-items-center">
                    <input type="hidden" class="module" value="hierarchics">
                    <input type="hidden" class="action" value="product_hierarchics">

                    <input type="hidden" class="productid" value="<?= $productPrices[0]['productid'] ?>">
                    <input type="hidden" class="redirect" value="<?= $_GET['redirect'] ?>">
                    <label class="mr-md">Branch:</label>
                    <select class="form-control branchid" onchange=" fetchPrices(this);">
                        <? foreach ($branches as $b) { ?>
                            <option <?= selected($currentBranch['id'], $b['id']) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                        <? } ?>
                    </select>
                </form>
            </header>
            <div class="panel-body">
                <? if ($productPrices[0]['costprice']) { ?>
                    <form id="form" class="form-horizontal form-bordered" method="post"
                          action="<?= url('hierarchics', 'product_hierarchics_save') ?>">
                        <input type="hidden" name="productid" class="form-control" value="<?= $productPrices[0]['productid'] ?>">
                        <input type="hidden" name="redirect" class="form-control" value="<?= $_GET['redirect'] ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Cost Price: <span
                                            class="text-danger"><?= $productPrices[0]['costprice'] ? formatN($productPrices[0]['costprice']) : 'n/a' ?></span>
                                </h4>
                                <input type="hidden" id="costprice" value="<?= $productPrices[0]['costprice'] ?>">
                                <input type="hidden" id="vatpercent" value="<?= $productPrices[0]['vat_percent'] ?>">
                            </div>
                            <div class="col-md-4">
                                <h4>Branch: <span class="text-danger"><?= $productPrices[0]['branchname'] ?></span></h4>
                                <input type="hidden" name="branchid" value="<?= $productPrices[0]['branchid'] ?>">
                            </div>
                        </div>
                        <div class="row hierarchic-header mt-md ml-sm">
                            <div class="col-md-2 p-xs"><label>Hierarchic Name</label></div>
                            <div class="col-md-1 p-xs"><label>Level</label></div>
                            <div class="col-md-1 p-xs"><label>Commission %</label></div>
                            <div class="col-md-1 p-xs"><label>Target %</label></div>
                            <div class="col-md-2 p-xs"><label>Percentage %</label></div>
                            <div class="col-md-2 p-xs"><label>Price Exc</label></div>
                            <div class="col-md-2 p-xs"><label>Price Inc <small class="text-danger">(Vat inclusive)</small></label></div>
                        </div>
                        <? foreach ($productPrices as $index => $h) { ?>
                            <div class="row mt-sm ml-sm">
                                <div class="col-md-2 p-xs">
                                    <input type="hidden" name="hierarchicid[]" value="<?= $h['hierarchicId'] ?>">
                                    <input type="text" readonly class="form-control" value="<?= $h['hierarchicname'] ?>">
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="text" readonly class="form-control"
                                           value="Level <?= $h['level'] ?>">
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="number" class="form-control commission" name="commission[]" value="<?= $h['commission'] ?>"
                                           step="0.01">
                                </div>
                                <div class="col-md-1 p-xs">
                                    <input type="number" class="form-control targetpercent" name="target[]" value="<?= $h['target'] ?>"
                                           step="0.01">
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="number" class="form-control percentage" name="percentage[]"
                                           value="<?= $h['percentage'] ?>" step="0.01" oninput="calculatePrice(this,true)">
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="text" readonly class="form-control excamount"
                                           value="<?= $h['exc_price'] ?>">
                                </div>
                                <div class="col-md-2 p-xs">
                                    <input type="number" class="form-control incamount" value="<?= $h['inc_price'] ?>"
                                           oninput="calculatePrice(this,false)">
                                </div>
                            </div>
                        <? } ?>
                        <div class="row mt-lg">
                            <div class="col-md-12">
                                <h4>Quick sale price
                                    <small class="text-danger">(VAT inclusive)</small>
                                    <small class="ml-md">Base price:</small>
                                    <small class="text-success"> <?= $productPrices[0]['inc_base'] ?></small>
                                </h4>
                            </div>
                            <div class="col-md-12">
                                <input id="quicksaleprice" placeholder="Quick sale price" type="number" class="form-control"
                                       title="Minimum <?= formatN($productPrices[0]['inc_base']) ?>"
                                       name="quicksale_price" min="<?= $productPrices[0]['inc_base'] ?>"
                                       value="<?= $productPrices[0]['inc_quicksale_price'] ?>">
                                <span class='hide-scroll'></span>
                            </div>
                        </div>
                        <div class="row mt-lg">
                            <div class="col-md-12">
                                <a href="<?= url('products', 'product_index') ?>" class="btn btn-default">Back to list</a>
                                <button class="btn btn-success ml-md">Save</button>
                            </div>
                        </div>
                    </form>
                <? } else { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-danger text-center">No Prices set for this branch!, Issue GRN or Transfer Goods to this branch</h4>
                        </div>
                    </div>
                <? } ?>
            </div>
        </section>
    </div>
</div>
<script>
    function fetchPrices(obj) {

        let branchid = $(obj).val();
        let module = $('#search-form').find('.module').val();
        let action = $('#search-form').find('.action').val();
        let productid = $('#search-form').find('.productid').val();
        let redirect = $('#search-form').find('.redirect').val();
        window.location.replace(`?productid=${productid}&redirect=${redirect}&module=${module}&action=${action}&branchid=${branchid}`);
    }

    function calculatePrice(obj, source_percentage) {
        let parentRow = $(obj).closest('.row');
        let costprice = parseFloat($('#costprice').val()) || 0;
        let vatpercent = parseFloat($('#vatpercent').val()) || 0;
        let price = parseFloat($(parentRow).find('.incamount').val()) || 0;
        let percentage = parseFloat($(parentRow).find('.percentage').val()) || 0;

        if (source_percentage) {
            let excamount = costprice * (1 + percentage / 100);
            let incamount = excamount * (1 + vatpercent / 100);
            $(parentRow).find('.excamount').val(excamount.toFixed(2));
            $(parentRow).find('.incamount').val(incamount.toFixed(2));
        } else {
            let excamount = price / (1 + (vatpercent / 100));
            percentage = 100 * ((excamount / costprice) - 1);
            $(parentRow).find('.percentage').val(percentage.toFixed(2));
            $(parentRow).find('.excamount').val(excamount.toFixed(2));
        }

    }
</script>
