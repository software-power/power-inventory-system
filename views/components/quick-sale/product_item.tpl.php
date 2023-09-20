<?
if ($detail['non_stock']) {
    $non_stock_class = "non-stock";
    $qty = $detail['qty'];
} elseif ($detail['stock_qty'] <= 0) {
    $row_class = "out-of-stock";
    if ($detail['source'] == 'external') $row_class .= " external-product";
} elseif ($detail['qty'] > $detail['stock_qty']) { //if not enough stock
    $qty_title = "Order exceed stock qty ({$detail['stock_qty']})";
    $qty = $detail['stock_qty'];
    $class = 'max-input-warning';
} else {
    $qty_title = "available stock {$detail['stock_qty']}";
    $qty = $detail['qty'];
}

$CAN_DISCOUNT = CS_QUICKSALE_DISCOUNT && Users::can(OtherRights::sale_discount) && !$QUICK_ORDER_MODE;
?>
<tr class="product-item productid-<?= $detail['productid'] ?> <?= $row_class ?>">
    <td>
        <button type="button" class="btn btn-danger btn-xs" title="remove item" onclick="removeItem(this)">
            <i class="fa fa-close"></i></button>
    </td>
    <td class="<?= $non_stock_class ?>" style="position: relative">
        <small class="text-danger non-stock-label" style="display: none">non-stock</small>
        <input type="text" readonly class="form-control p-xs input-sm inputs productname" value="<?= $detail['productname'] ?>">
        <input type="hidden" class="inputs stockid" name="stockid[]" value="<?= $detail['stockid'] ?>">
        <input type="hidden" class="inputs productid" name="productid[]" value="<?= $detail['productid'] ?>">
        <input type="hidden" class="inputs description" name="description[]" value="<?= $detail['description'] ?>">
        <input type="hidden" class="inputs trackserialno" value="<?= $detail['trackserialno'] ?>">
        <input type="hidden" class="inputs validate_serialno" value="<?= $detail['validate_serialno'] ?>">
    </td>
    <td><input type="text" readonly class="form-control p-xs input-sm inputs" value="<?= $detail['unitname'] ?>"></td>
    <td>
        <input type="text"
               class="form-control p-xs text-center input-sm inputs qty <?= $class ?>"
               name="qty[]" autocomplete="off" oninput="checkMaxQty(this)" min="1" value="<?= $qty ?>"
               max="<?= $detail['non_stock'] ? '' : $detail['stock_qty'] ?>"
               title="<?= $detail['non_stock'] ? '' : $qty_title ?>">
        <i class="text-success text-xs"><?= $detail['sale_item'] ? 'previous' : 'order' ?> qty <?= $detail['qty'] ?></i>
    </td>
    <td class="<?= !$CAN_DISCOUNT ? 'hidden' : '' ?>">
        <input type="text" readonly class="form-control p-xs text-center input-sm inputs vat_rate" name="vat_rate[]"
               value="<?= $detail['vat_rate'] ?>">
    </td>
    <td class="<?= !$CAN_DISCOUNT ? 'hidden' : '' ?>">
        <input type="text" <?= $detail['non_stock'] ? '' : 'readonly' ?> class="form-control input-sm inputs price" name="price[]"
               value="<?= $detail['price'] ?>" oninput="calProductAmount(<?= $detail['productid'] ?>,this)" data-source="price">
        <input type="hidden" class="input-sm inputs base_price" value="<?= $detail['base_price'] ?>">
        <input type="hidden" class="input-sm inputs base_incprice" value="<?= $detail['base_incprice'] ?>">
        <input type="hidden" class="inputs hidden_cost" name="hidden_cost[]" value="<?= $detail['hidden_cost'] ?>">
        <input type="hidden" class="inputs base_hidden_cost" value="<?= $detail['base_hidden_cost'] ?>">
        <input type="hidden" class="inputs excamount">
        <input type="hidden" class="inputs discamount">
        <input type="hidden" class="inputs vatamount">
        <input type="hidden" class="inputs incamount">
    </td>
    <td class="<?= !$CAN_DISCOUNT ? 'hidden' : '' ?>">
        <input type="text" class="form-control p-xs text-center input-sm inputs discpercent" autocomplete="off"
               value="<?= $detail['discpercent'] ?>" step="0.01" min="0" max="<?= $detail['max_discount_percent'] ?>"
               title="discount percentage Max: <?= formatN($detail['max_discount_percent']) ?> %"
               oninput="calProductAmount(<?= $detail['productid'] ?>,this)"
               data-source="discpercent">
    </td>
    <td class="<?= !$CAN_DISCOUNT ? 'hidden' : '' ?>">
        <input type="text" <?= $detail['non_stock'] ? 'readonly' : '' ?> class="form-control p-xs text-center input-sm inputs discount"
               name="discount[]"
               value="0" step="0.01" min="0" max="<?= $detail['non_stock'] ? '' : $detail['max_discount'] ?>"
               title="discount\nMax: <?= $detail['non_stock'] ? '' : formatN($detail['max_discount']) ?>" autocomplete="off"
               oninput="calProductAmount(<?= $detail['productid'] ?>,this)"
               data-source="discount">
    </td>
    <td style="position: relative;">
        <div class="incprice_spinner" style="position: absolute;top:-5px;right: 0;display: none;">
            <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
        </div>
        <input type="text" <?= $detail['non_stock'] ? '' : 'readonly' ?> name="incprice[]" class="form-control input-sm inputs unit_price"
               oninput="calProductAmount(<?= $detail['productid'] ?>,this)"
               data-source="unit_price" value="<?= $detail['incprice'] ?>">
        <input type="hidden" class="sinc" name="sinc[]" value="<?= $detail['sinc'] ?>">
    </td>
    <td>
        <input type="text" readonly class="form-control input-sm inputs incamount_label">
    </td>
    <td class="extra-cell d-flex align-items-center">
        <div class="btn-holder d-flex align-items-center">
            <? if ($detail['source'] != 'external') { //for proforma external product?>
                <button type="button" title="view product" class="btn btn-info btn-xs mr-xs" data-toggle="modal" data-target="#product-view-modal"
                        data-productid="<?= $detail['productid'] ?>">
                    <i class="fa fa-eye"></i>
                </button>
            <? } ?>
            <? if (!$QUICK_ORDER_MODE) {
                if ($detail['prescription_required'] || $detail['track_expire_date']) { ?>
                    <button type="button" title="prescription and batch details" class="btn btn-warning btn-xs ml-xs"
                            data-toggle="modal" data-target="#prescriptionModal-<?= $detail['stockid'] ?>">
                        <i class="fa fa-list"></i>
                    </button>
                <? } ?>
                <? if ($detail['trackserialno']) { ?>
                    <button type="button" title="serial numbers" class="btn btn-default btn-xs ml-xs add-serial-btn" data-toggle="modal"
                            data-target="#serialModal-<?= $detail['stockid'] ?>">
                        <i class="fa fa-barcode"></i>
                    </button>
                <? }
            } ?>
        </div>
        <div class="modals-holder">
            <? if (!$QUICK_ORDER_MODE) {
                if ($detail['prescription_required'] || $detail['track_expire_date']) { ?>
                    <?= component('quick-sale/batch_prescription_modal.tpl.php', ['detail' => $detail]) ?>
                <? } ?>
                <? if ($detail['trackserialno']) { ?>
                    <?= component('quick-sale/serial_modal.tpl.php', ['detail' => $detail]) ?>
                <? }
            } ?>
        </div>
    </td>
</tr>