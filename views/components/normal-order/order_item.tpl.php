<div class="row mb-md <?= $detail['source'] == 'external' ? 'external-product' : '' ?> <?= $detail['non_stock'] ? 'non-stock' : '' ?>"
     style="position: relative;">
    <small class="text-danger non-stock-label" style="display: none">non-stock</small>
    <div class="col-md-3 pl-none pr-none">
        <? if ($detail['source'] == 'external') { ?>
            <input type="text" readonly class="form-control productname" value="<?= $detail['productname'] ?>">
        <? } else { ?>
            <input type="text" readonly class="form-control inputs productname" placeholder="search product" value="<?= $detail['productname'] ?>"
                   onclick="open_modal(this,'.row')">
            <input type="hidden" class="inputs productid" name="productid[]" value="<?= $detail['productid'] ?>" required>
        <? } ?>
    </div>
    <div class="col-md-1 pl-none pr-none">
        <input placeholder="Quantity" type="text" class="form-control inputs qty" name="qty[]"
                title="<?= $detail['non_stock'] ? '' : 'current stock '.$detail['stockqty'] ?>"
               oninput="calProductAmount(this)" min="1" value="<?= $detail['qty'] ?>">
    </div>
    <div class="col-md-2 pl-none pr-none" style="position: relative;">
        <div class="price_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
            <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
        </div>
        <input placeholder="Price" type="text" class="form-control inputs price" name="price[]" step="0.01"
               oninput="calProductAmount(this)" value="<?= $detail['price'] ?>" data-source="price"
               min="<?= $detail['min_price'] ?>">
        <input type="hidden" class="base_price" value="<?= $detail['base_price'] ?>">
        <input type="hidden" class="min_price" value="<?= $detail['min_base_price'] ?>">
    </div>
    <div class="col-md-1 pl-none pr-none">
        <input type="text" readonly class="form-control vat_rate" name="vat_rate[]"
               value="<?= $detail['vat_rate'] ?>">
    </div>
    <div class="col-md-2 pl-none pr-none" style="position: relative;">
        <div class="incprice_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
            <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
        </div>
        <input placeholder="Inclusive Price" type="text" class="form-control inputs incprice" name="incprice[]"
               oninput="calProductAmount(this)" value="<?= $detail['incprice'] ?>" data-source="incprice"
               min="<?= $detail['min_incprice'] ?>">
        <input type="hidden" class="inputs sinc" name="sinc[]" value="<?= $detail['sinc'] ?>">
    </div>
    <div class="col-md-2 pl-none pr-none">
        <input type="text" readonly class="form-control incamount">
        <input type="hidden" class="excamount">
        <input type="hidden" class="vatamount">
    </div>
    <div class="col-md-1 pl-xs pr-none">
        <button type="button" class='btn btn-info btn-sm view_product_btn' title="view product" data-productid="<?= $detail['productid'] ?>"
                data-toggle="modal"
                data-target="#product-view-modal">
            <i class='fa fa-eye'></i></button>
        <button type="button" class='btn btn-danger btn-sm' title="remove item" onclick='removeRow(this);'><i class='fa fa-close'></i></button>
    </div>
    <div class="col-md-4 pl-none pr-none">
        <textarea rows="2" name="product_description[]" class="form-control inputs product_description"  <?= $detail['print_extra'] ? '' : 'readonly' ?>
                  placeholder="description"><?= $detail['productdescription'] ?></textarea>
    </div>
    <div class="col-md-4 pl-none pr-none">
        <label class="d-flex align-items-center ml-md mr-sm">
            <input type="checkbox" name="print_extra[]" class="print_extra" <?= $detail['print_extra'] ? 'checked' : '' ?>
                   value="<?= $detail['productid'] ?>" onchange="enableDescription(this)">
            <small class="ml-xs" style="user-select: none;">Print Extra Desc</small>
        </label>
    </div>
</div>