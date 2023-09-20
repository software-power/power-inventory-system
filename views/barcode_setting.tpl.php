<div class="row">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Barcode Setting</h2>
            </header>
            <div class="panel-body">
                <form action="<?= url('products', 'save_barcode_settings') ?>" method="post">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Size:</h5>
                            <div class="ml-xlg">
                                <label class="radio" title="return amount & stock of selected item">
                                    <input type="radio" name="barcode_size" value="large" class="barcode-size" checked
                                           onchange="checkType()" <?= $barcode_settings['barcode_size'] == 'large' ? 'checked' : '' ?>
                                           data-unit='{"w":80,"h":50}'>Large (80mm x 50mm)
                                </label>
                                <label class="radio" title="return amount & stock of selected item">
                                    <input type="radio" name="barcode_size" value="small" class="barcode-size"
                                           onchange="checkType()" <?= $barcode_settings['barcode_size'] == 'small' ? 'checked' : '' ?>
                                           data-unit='{"w":51,"h":28}'>Small
                                    (51mm x 28mm)
                                </label>
                                <label class="radio" title="return amount & stock of selected item">
                                    <input type="radio" name="barcode_size" value="custom" class="barcode-size"
                                           onchange="checkType()" <?= $barcode_settings['barcode_size'] == 'custom' ? 'checked' : '' ?>
                                           data-unit="custom">Custom
                                </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h5>Sample Info</h5>
                            <div class="row mb-xlg">
                                <div class="col-md-3">
                                    <label>Product:</label>
                                    <input id="product-name" type="text" class="form-control input-sm size-inputs" min="1"
                                           value="<?= $product['name'] ?>" onchange="previewBarcode()" onkeyup="previewBarcode()">
                                    <input id="company-name" type="hidden" value="<?= CS_COMPANY ?>">
                                    <input id="currencyname" type="hidden" value="<?= $basecurrency['name'] ?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Price:</label>
                                    <input id="product-price" type="number" step="0.01" class="form-control input-sm size-inputs" min="0.01"
                                           value="<?= $product['quick_price'] ?>" onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Expiry date:</label>
                                    <input id="product-expiry" type="date" class="form-control input-sm size-inputs"
                                           value="<?= TODAY ?>" onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Barcode Text:</label>
                                    <input id="barcode-text" type="text" class="form-control input-sm size-inputs"
                                           value="<?= $product['barcode'] ?>" onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                            </div>
                            <h5>Settings</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Barcode Type:</label>
                                    <select id="barcode-type" name="barcode_type" class="form-control input-sm" onchange="previewBarcode()">
                                        <option <?= selected($barcode_settings['barcode_type'], 'code128') ?> value="code128">Code 128</option>
                                        <option <?= selected($barcode_settings['barcode_type'], 'qrcode') ?> value="qrcode">QR Code</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Paper Width (mm):</label>
                                    <input id="paper-width" type="number" name="paper_width"
                                           step="1" <?= $barcode_settings['barcode_size'] == 'custom' ? '' : 'readonly' ?>
                                           class="form-control input-sm size-inputs" min="1" required value="<?= $barcode_settings['paper_width'] ?>"
                                           onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Paper height (mm):</label>
                                    <input id="paper-height" type="number" name="paper_height"
                                           step="1" <?= $barcode_settings['barcode_size'] == 'custom' ? '' : 'readonly' ?>
                                           class="form-control input-sm size-inputs" min="1" required value="<?= $barcode_settings['paper_height'] ?>"
                                           onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Barcode Height (mm):</label>
                                    <input id="barcode-height" type="number" name="barcode_height"
                                           step="1" <?= $barcode_settings['barcode_size'] == 'custom' ? '' : 'readonly' ?>
                                           class="form-control input-sm size-inputs" min="1"
                                           required value="<?= $barcode_settings['barcode_height'] ?>"
                                           onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-3">
                                    <label>Scale:</label>
                                    <input id="scale" type="number" name="scale" step="1" class="form-control input-sm" min="1"
                                           value="<?= $barcode_settings['scale'] ?>"
                                           onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Text Size:</label>
                                    <input id="text-size" type="number" name="text_size" step="1" class="form-control input-sm" min="1"
                                           value="<?= $barcode_settings['text_size'] ?: '9' ?>" onchange="previewBarcode()"
                                           onkeyup="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Barcode Text Size:</label>
                                    <input id="barcode-text-size" type="number" name="barcode_text_size" step="1" class="form-control input-sm"
                                           min="1"
                                           value="<?= $barcode_settings['barcode_text_size'] ?: '5' ?>" onchange="previewBarcode()"
                                           onkeyup="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Quantity:</label>
                                    <input id="barcode-qty" type="number" step="1" class="form-control input-sm" min="1" value="1" required
                                           onchange="previewBarcode()" onkeyup="previewBarcode()">
                                </div>
                            </div>
                            <div class="row mt-md">
                                <div class="col-md-3 ml-xlg">
                                    <label class="checkbox">
                                        <input id="show-company" type="checkbox" name="show_company"
                                               onchange="previewBarcode()" <?= $barcode_settings['show_company'] ? 'checked' : '' ?>>
                                        Show company name
                                    </label>
                                    <label class="checkbox">
                                        <input id="show-name" type="checkbox" name="show_name"
                                               onchange="previewBarcode()" <?= $barcode_settings['show_name'] ? 'checked' : '' ?>>
                                        Show name
                                    </label>
                                    <label class="checkbox">
                                        <input id="show-price" type="checkbox" name="show_price"
                                               onchange="previewBarcode()" <?= $barcode_settings['show_price'] ? 'checked' : '' ?>>
                                        Show Price
                                    </label>
                                    <label class="checkbox">
                                        <input id="show-expiry" type="checkbox" name="show_expiry"
                                               onchange="previewBarcode()" <?= $barcode_settings['show_expiry'] ? 'checked' : '' ?>>
                                        Show Nearby expiry
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <button class="btn btn-success btn-sm ml-sm"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <div class="col-md-4 d-flex justify-content-center">
        <div id="preview-holder" style="background-color: white;border-radius: 1px; box-shadow: 0 0 1px grey;">
            <div class="barcode-item text-dark d-flex flex-column align-items-center">
                <div class="text-weight-bold company-name"></div>
                <img class="barcode" alt="">
                <div>
                    <div class="m-none p-none text-weight-bold name_text">Item: Product</div>
                    <div class="m-none p-none text-weight-bold price_text">Price: TSH 5,000</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/barcode/bwip-js.js"></script>
<script>
    $(function () {
        <?if($barcode_settings){?>
        previewBarcode();
        <?}else{?>
        checkType();
        <?}?>
    });

    function checkType() {
        let selected = $('.barcode-size:checked');
        let sizes = $(selected).data('unit');
        if (sizes === 'custom') {
            $('.size-inputs').prop('readonly', false);
        } else {
            $('.size-inputs').prop('readonly', true);
            $('#paper-height-height').val(sizes.h);
            $('#paper-width').val(sizes.w);
        }
        previewBarcode();
    }

    function previewBarcode() {
        let previewHolder = $('#preview-holder');
        let company_name = $('#company-name').val();
        let product_name = $('#product-name').val();
        let currencyname = $('#currencyname').val();
        let price = $('#product-price').val();
        let expiry = $('#product-expiry').val();
        let barcode_text = $('#barcode-text').val();
        let barcode_type = $('#barcode-type').val();
        let paper_width = $('#paper-width').val();
        let paper_height = $('#paper-height').val();
        let barcode_height = $('#barcode-height').val();
        let qty = $('#barcode-qty').val();
        let scale = $('#scale').val();
        let text_align = 'center';
        let text_size = $('#text-size').val();
        let barcode_text_size = $('#barcode-text-size').val();
        let show_company = $('#show-company').is(':checked');
        let show_name = $('#show-name').is(':checked');
        let show_price = $('#show-price').is(':checked');
        let show_expiry = $('#show-expiry').is(':checked');

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
                            <div class="m-none p-none text-weight-bold  ${!show_price ? 'hidden' : ''}">Price: ${currencyname} ${numberWithCommas(price)}</div>
                            <div class="m-none p-none text-weight-bold  ${!show_expiry ? 'hidden' : ''}">Nearby: ${expiry}</div>
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

    function numberWithCommas(number) {
        var parts = number.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }

</script>