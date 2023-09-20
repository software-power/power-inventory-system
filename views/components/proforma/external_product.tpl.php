<div class="row mb-md external">
    <div class="col-md-3 pl-none pr-none" style="position: relative;">
        <span class="external-label">external</span>
        <input required name="external[productname][]" class="form-control productname" placeholder="product name" minlength="3"
        value="<?=$detail['productname']?>"/>
    </div>
    <div class="col-md-1 pl-none pr-none">
        <input placeholder="Quantity" type="text" class="form-control qty" name="external[qty][]"
               oninput="calProductAmount(this)" min="1" value="<?= $detail['qty'] ?>" data-source="qty">
    </div>
    <div class="col-md-2 pl-none pr-none" style="position: relative">
        <div class="price_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
            <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
        </div>
        <input placeholder="Price" type="text" class="form-control price" name="external[price][]" min="0"
               oninput="calProductAmount(this)" value="<?= $detail['price'] ?>" data-source="price">
        <input type="hidden" class="base_price" value="<?= $detail['base_price'] ?>">
    </div>
    <div class="col-md-1 pl-none pr-none">
        <input type="hidden" class="vat_rate" name="external[vat_rate][]" value="<?= $detail['vat_rate'] ?>">
        <select class="form-control vat_category" name="external[tax_category][]" onchange="calProductAmount(this)">
            <? foreach ($categories as $c) { ?>
                <option <?=selected($detail['taxcategory'], $c['id'])?> value="<?= $c['id'] ?>"
                        data-vatrate="<?= $c['vat_percent'] ?>"><?= $c['name'] ?> <?= $c['vat_percent'] ?>%
                </option>
            <? } ?>
        </select>
    </div>
    <div class="col-md-2 pl-none pr-none" style="position: relative">
        <div class="incprice_loading_spinner" style="position: absolute;right:1px;visibility: hidden;">
            <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
        </div>
        <input placeholder="Inclusive Price" type="text" class="form-control incprice" name="external[incprice][]"
               min="<?= $detail['min_incprice'] ?>" oninput="calProductAmount(this)" value="<?= $detail['incprice'] ?>" data-source="incprice">
        <input type="hidden" class="sinc" name="external[sinc][]" value="<?= $detail['sinc'] ?>">
    </div>
    <div class="col-md-2 pl-none pr-none">
        <input type="text" readonly class="form-control incamount">
    </div>
    <div class="col-md-1 pl-xs pr-none">
        <button type="button" class='btn btn-danger btn-sm' onclick='removeRow(this);'><i class='fa fa-close'></i></button>
    </div>
</div>