<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-4">
                        <h2 class="panel-title">Print Barcode</h2>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <form>
                                <input type="hidden" name="module" value="products">
                                <input type="hidden" name="action" value="generate_barcode">
                                <input type="hidden" name="productid" value="<?= $product['id'] ?>">
                                Location:
                                <select id="locationid" name="locationid" class="form-control input-sm" onchange="$(this).closest('form').trigger('submit')">
                                    <option value="<?= $location['id'] ?>"><?= $location['name'] ?> - <?= $location['branchname'] ?></option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form action="<?= url('products', 'print_barcode') ?>" method="post">
                    <div class="row">
                        <div class="col-md-3">
                            Product Name:
                            <input type="text" readonly class="form-control input-sm" value="<?= $product['name'] ?>">
                            <input type="hidden" name="productid" value="<?= $product['id'] ?>">
                        </div>
                        <div class="col-md-3">
                            Location Name:
                            <input type="text" readonly class="form-control input-sm"
                                   value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                        </div>
                        <div class="col-md-3">
                            Barcode:
                            <input type="text" readonly class="form-control input-sm" value="<?= $product['barcode'] ?>" name="barcode_text">
                        </div>
                        <div class="col-md-2">
                            Stock Qty:
                            <input type="text" readonly class="form-control input-sm" value="<?= $product['stock_qty'] ?>">
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-3">
                            Quick Price:
                            <input type="text" name="quick_price" readonly class="form-control input-sm" value="<?= $product['quick_price'] ?>">
                        </div>
                        <div class="col-md-3">
                            Nearby expiry:
                            <input type="text" name="nearby_expiry" readonly class="form-control input-sm" value="<?= $product['nearby_expiry'] ?>">
                        </div>
                        <div class="col-md-3">
                            Print Qty: <small class="text-danger">max <?= $product['stock_qty'] ?></small>
                            <input type="number" min="1" name="qty" class="form-control input-sm" value="1" max="<?= $product['stock_qty'] ?>" required>
                        </div>
                    </div>
                    <div class="row mt-xl">
                        <div class="col-md-12 d-flex justify-content-center">
                            <div id="preview-holder" style="background-color: white;border-radius: 1px; box-shadow: 0 0 1px grey;">
                                <div class="barcode-item text-dark d-flex flex-column align-items-center">
                                    <div class="text-weight-bold company-name"></div>
                                    <img class="barcode" alt="">
                                    <div>
                                        <div class="m-none p-none text-weight-bold name_text">Item: Product</div>
                                        <div class="m-none p-none text-weight-bold price_text">Price: TSH 5,000</div>
                                        <div class="m-none p-none text-weight-bold expiry_text">Nearby:</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button class="btn btn-success"><i class="fa fa-print"></i> Print</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script src="assets/barcode/bwip-js.js"></script>
<script>
    $(function () {
        initSelectAjax('#locationid', '?module=locations&action=getLocations&format=json', 'Choose location');
        previewBarcode();
    });


    function previewBarcode() {
        let previewHolder = $('#preview-holder');
        let company_name = `<?=CS_COMPANY?>`;
        let product_name = `<?=$product['name']?>`;
        let currencyname = `<?=$basecurrency['name']?>`;
        let price = `<?=formatN($product['quick_price'])?>`;
        let nearby_expiry = `<?=$product['nearby_expiry']?>`;
        let barcode_text = `<?=$product['barcode']?>`;
        let barcode_type = `<?=$barcode_settings['barcode_type']?>`;
        let paper_width = `<?=$barcode_settings['paper_width']?>`;
        let paper_height = `<?=$barcode_settings['paper_height']?>`;
        let barcode_height = `<?=$barcode_settings['barcode_height']?>`;
        let qty = 1;
        let scale = `<?=$barcode_settings['scale']?>`;
        let text_align = 'center';
        let text_size = `<?=$barcode_settings['text_size']?>`;
        let barcode_text_size = `<?=$barcode_settings['barcode_text_size']?>`;
        let show_company = `<?=$barcode_settings['show_company']?>`==1;
        let show_name = `<?=$barcode_settings['show_name']?>`==1;
        let show_price = `<?=$barcode_settings['show_price']?>`==1;
        let show_expiry = `<?=$barcode_settings['show_expiry']?>`==1 && nearby_expiry.length>0;

        $(previewHolder).empty();
        $(previewHolder).css('width', `${paper_width}mm`);

        let alt_text = barcode_text;
        let show_text = true;

        let item = `<div class="barcode-item text-dark d-flex flex-column align-items-center justify-content-center"
                        style="box-shadow:0 0 1px grey;height: ${paper_height}mm;width:${paper_width}mm;font-size: ${text_size}pt">
                        <div class="text-weight-bold company-name ${!show_company ? 'hidden' : ''}">${company_name}</div>
                        <img class="barcode">
                        <div>
                            <div class="m-none p-none text-weight-bold  ${!show_name ? 'hidden' : ''}">Item: ${product_name}</div>
                            <div class="m-none p-none text-weight-bold  ${!show_price ? 'hidden' : ''}">Price: ${currencyname} ${price}</div>
                            <div class="m-none p-none text-weight-bold  ${!show_expiry ? 'hidden' : ''}">Nearby: ${nearby_expiry}</div>
                        </div>
                    </div>`;
        try {
            for (let i = 0; i < qty; i++) $(previewHolder).append(item);

            let canvas = document.createElement('canvas');
            bwipjs.toCanvas(canvas, {
                bcid: barcode_type,       // Barcode type
                text: barcode_text,    // Text to encode
                alttext: alt_text,    // Alt Text
                scale: scale,               // 3x scaling factor
                height: barcode_height,              // Bar height, in millimeters
                includetext: show_text,            // Show human-readable text
                textxalign: text_align,// Always good to set this
                textsize: barcode_text_size,
                addontextsize: text_size,
            });
            $('.barcode-item .barcode').attr('src', canvas.toDataURL('image/png'));
        } catch (e) {
            triggerError(e);
            console.log(e);
        }
    }


</script>