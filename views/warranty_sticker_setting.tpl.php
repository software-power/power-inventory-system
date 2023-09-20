<div class="row">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Warranty Sticker Setting</h2>
            </header>
            <div class="panel-body">
                <form action="<?= url('products', 'save_warranty_sticker_setting') ?>" method="post">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Size:</h5>
                            <div class="ml-xlg">
                                <label class="radio" title="return amount & stock of selected item">
                                    <input type="radio" name="sticker[sticker_size]" value="large" class="sticker-size" checked
                                           onchange="checkType()" <?= $sticker_settings['sticker_size'] == 'large' ? 'checked' : '' ?>
                                           data-unit='{"w":80,"h":50}'>Large (80mm x 50mm)
                                </label>
                                <label class="radio" title="return amount & stock of selected item">
                                    <input type="radio" name="sticker[sticker_size]" value="small" class="sticker-size"
                                           onchange="checkType()" <?= $sticker_settings['sticker_size'] == 'small' ? 'checked' : '' ?>
                                           data-unit='{"w":51,"h":28}'>Small
                                    (51mm x 28mm)
                                </label>
                                <label class="radio" title="return amount & stock of selected item">
                                    <input type="radio" name="sticker[sticker_size]" value="custom" class="sticker-size"
                                           onchange="checkType()" <?= $sticker_settings['sticker_size'] == 'custom' ? 'checked' : '' ?>
                                           data-unit="custom">Custom
                                </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h5>Sample Info</h5>
                            <div class="row mb-xlg">
                                <div class="col-md-3">
                                    <label>Serial No:</label>
                                    <input id="serialno-text" type="text" step="0.01" class="form-control input-sm"
                                           value="<?= $sample['serialno'] ?>" oninput="previewBarcode()">
                                </div>
                            </div>
                            <h5>Settings</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Header:</label>
                                    <input id="header-text" type="text" name="sticker[header_text]" class="form-control input-sm"
                                           value="<?= $sticker_settings['header_text']?:$sample['header'] ?>" oninput="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Footer:</label>
                                    <input id="footer-text" type="text" name="sticker[footer_text]" class="form-control input-sm"
                                           value="<?= $sticker_settings['footer_text']?:$sample['footer'] ?>" oninput="previewBarcode()">
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-3">
                                    <label>Barcode Type:</label>
                                    <select id="barcode-type" name="sticker[barcode_type]" class="form-control input-sm" onchange="previewBarcode()">
                                        <option <?= selected($sticker_settings['barcode_type'], 'code128') ?> value="code128">Code 128</option>
                                        <option <?= selected($sticker_settings['barcode_type'], 'qrcode') ?> value="qrcode">QR Code</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Sticker Width (mm):</label>
                                    <input id="sticker-width" type="number" name="sticker[sticker_width]"
                                           step="1" <?= $sticker_settings['sticker_size'] == 'custom' ? '' : 'readonly' ?>
                                           class="form-control input-sm size-inputs" min="1" required
                                           value="<?= $sticker_settings['sticker_width'] ?>"
                                           oninput="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Sticker height (mm):</label>
                                    <input id="sticker-height" type="number" name="sticker[sticker_height]"
                                           step="1" <?= $sticker_settings['sticker_size'] == 'custom' ? '' : 'readonly' ?>
                                           class="form-control input-sm size-inputs" min="1" required
                                           value="<?= $sticker_settings['sticker_height'] ?>"
                                           oninput="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Barcode Height (mm):</label>
                                    <input id="barcode-height" type="number" name="sticker[barcode_height]"
                                           step="1" <?= $sticker_settings['sticker_size'] == 'custom' ? '' : 'readonly' ?>
                                           class="form-control input-sm size-inputs" min="1"
                                           required value="<?= $sticker_settings['barcode_height'] ?: '20' ?>"
                                           oninput="previewBarcode()">
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-3 hidden">
                                    <label>Sticker Horizontal padding (mm):</label>
                                    <input id="sticker-h-padding" type="number" name="sticker[sticker_h_padding]"
                                           step="1" class="form-control input-sm" min="1"
                                           required value="<?= $sticker_settings['sticker_h_padding'] ?: '1' ?>"
                                           oninput="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Sticker vertical padding (mm):</label>
                                    <input id="sticker-v-padding" type="number" name="sticker[sticker_v_padding]"
                                           step="0.5" class="form-control input-sm" min="1"
                                           required value="<?= $sticker_settings['sticker_v_padding'] ?: '1' ?>"
                                           oninput="previewBarcode()">
                                </div>
                            </div>
                            <div class="row mt-sm">
                                <div class="col-md-3">
                                    <label>Scale:</label>
                                    <input id="scale" type="number" name="sticker[scale]" step="1" class="form-control input-sm" min="1"
                                           value="<?= $sticker_settings['scale'] ?: '1' ?>"
                                           oninput="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Header text size:</label>
                                    <input id="header-text-size" type="number" name="sticker[header_text_size]" step="1" class="form-control input-sm"
                                           min="1"
                                           value="<?= $sticker_settings['header_text_size'] ?: '9' ?>" oninput="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Serialno text size:</label>
                                    <input id="serialno-text-size" type="number" name="sticker[serialno_text_size]" step="1"
                                           class="form-control input-sm"
                                           min="1" value="<?= $sticker_settings['serialno_text_size'] ?: '5' ?>" oninput="previewBarcode()">
                                </div>
                                <div class="col-md-3">
                                    <label>Footer text size:</label>
                                    <input id="footer-text-size" type="number" name="sticker[footer_text_size]" step="1" class="form-control input-sm"
                                           min="1"
                                           value="<?= $sticker_settings['footer_text_size'] ?: '5' ?>" required oninput="previewBarcode()">
                                </div>
                            </div>
                            <div class="row mt-md">
                                <div class="col-md-3 ml-xlg">
                                    <label class="checkbox">
                                        <input id="show-header" type="checkbox" name="sticker[show_header]"
                                               onchange="previewBarcode()" <?= $sticker_settings['show_header'] ? 'checked' : '' ?>>
                                        Show header
                                    </label>
                                    <label class="checkbox">
                                        <input id="show-serialno" type="checkbox" name="sticker[show_serialno]"
                                               onchange="previewBarcode()" <?= $sticker_settings['show_serialno'] ? 'checked' : '' ?>>
                                        Show serialno
                                    </label>
                                    <label class="checkbox">
                                        <input id="show-footer" type="checkbox" name="sticker[show_footer]"
                                               onchange="previewBarcode()" <?= $sticker_settings['show_footer'] ? 'checked' : '' ?>>
                                        Show Footer
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
            <div class="sticker-item text-dark d-flex flex-column align-items-center">
                <div class="text-weight-bold sticker-header"></div>
                <img class="barcode" alt="">
                <div class="text-weight-bold sticker-footer"></div>
            </div>
        </div>
    </div>
</div>

<script src="assets/barcode/bwip-js.js"></script>
<script>
    $(function () {
        <?if($sticker_settings){?>
        previewBarcode();
        <?}else{?>
        checkType();
        <?}?>
    });

    function checkType() {
        let selected = $('.sticker-size:checked');
        let sizes = $(selected).data('unit');
        if (sizes === 'custom') {
            $('.size-inputs').prop('readonly', false);
        } else {
            $('.size-inputs').prop('readonly', true);
            $('#sticker-height').val(sizes.h);
            $('#sticker-width').val(sizes.w);
        }
        previewBarcode();
    }

    function previewBarcode() {
        let previewHolder = $('#preview-holder');
        let header_text = $('#header-text').val();
        let serialno_text = $('#serialno-text').val();
        let footer_text = $('#footer-text').val();
        let barcode_type = $('#barcode-type').val();
        let sticker_width = $('#sticker-width').val();
        let sticker_height = $('#sticker-height').val();
        let sticker_h_padding = $('#sticker-h-padding').val()||0;
        let sticker_v_padding = $('#sticker-v-padding').val()||0;
        let barcode_height = $('#barcode-height').val();
        let scale = $('#scale').val();
        let text_align = 'center';
        let header_text_size = $('#header-text-size').val();
        let serialno_text_size = $('#serialno-text-size').val();
        let footer_text_size = $('#footer-text-size').val();
        let show_header = $('#show-header').is(':checked');
        let show_serialno = $('#show-serialno').is(':checked');
        let show_footer = $('#show-footer').is(':checked');

        $(previewHolder).empty();
        $(previewHolder).css('width', `${sticker_width}mm`);

        let item = `
            <div class="sticker-item text-dark d-flex flex-column align-items-center"
                 style="box-shadow:0 0 1px grey;height: ${sticker_height}mm;width:${sticker_width}mm;padding:${sticker_v_padding}mm ${sticker_h_padding}mm;">
                <div class="text-weight-bold text-center sticker-header mb-xs" style="display:${show_header ? '' : 'none'};font-size:${header_text_size}pt;">${header_text}</div>
                <img class="barcode" alt="">
                <div class="text-weight-bold text-center sticker-serialno" style="display:${show_serialno ? '' : 'none'};font-size:${serialno_text_size}pt;">${serialno_text}</div>
                <div class="text-weight-bold text-center sticker-footer" style="display:${show_footer ? '' : 'none'};font-size:${footer_text_size}pt;">${footer_text}</div>
            </div>
        `;
        try {
            $(previewHolder).append(item);

            let canvas = document.createElement('canvas');
            bwipjs.toCanvas(canvas, {
                bcid: barcode_type,       // Barcode type
                text: serialno_text,    // Text to encode
                // alttext: alt_text,    // Alt Text
                scale: scale,               // 3x scaling factor
                height: barcode_height,              // Bar height, in millimeters
                textxalign: text_align,// Always good to set this
            });
            $('.sticker-item .barcode').attr('src', canvas.toDataURL('image/png'));
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