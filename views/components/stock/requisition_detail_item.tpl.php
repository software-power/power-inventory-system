<div class="row-margin group">
    <div class="row">
        <div class="col-md-5 p-xs">
            <input type="text" readonly class="form-control inputs productname" placeholder="search product"
                   onclick="open_modal(this,'.group',fetchDetails,$('#location_to'))" value="<?= $detail['productname'] ?>">
            <input type="hidden" class="inputs productid" name="productid[]" value="<?= $detail['productid'] ?>">
            <textarea rows="3" class="form-control inputs product_description" readonly
                      placeholder="description"><?= $detail['productdescription'] ?></textarea>
        </div>
        <div class="col-md-3 p-xs">
            <input type="number" class="form-control inputs qty" name="qty[]" min="1" autocomplete="off" placeholder="quantity" required
                   value="<?= $detail['qty'] ?>">
        </div>
        <div class="col-md-4 p-xs d-flex justify-content-end">
            <button type="button" class="btn btn-info btn-sm view-product-btn" title="view product" data-toggle="modal"
                    data-productid="<?= $detail['productid'] ?>" data-target="#product-view-modal">
                <i class="fa fa-eye"></i>
            </button>
            <button type="button" class="btn btn-warning btn-sm ml-sm" title="remove item" onclick="removeItem(this)">
                <i class="fa fa-close"></i>
            </button>
        </div>
    </div>
</div>