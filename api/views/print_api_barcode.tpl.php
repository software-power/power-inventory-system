<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="../assets/qr/qrcode.js"></script>
    <script src="../assets/barcode/bwip-js.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Print Barcode <?= $product_name ?></title>
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="../assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="../assets/css/custom.css"/>
    <link rel="stylesheet" href="../assets/css/paper.css"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style>
        body.small80 .sheet {
            width: <?=$barcode_settings['paper_width']?>mm !important;
        }

        @media print {

            #printBtn {
                display: none;
            }

            @page {
                /*margin for each printed piece of paper*/
                margin: 5mm;
            }

            @page :first {
                margin-top: 0;
            }
        }

        @media print {
            body.small80 {
                width: <?=$barcode_settings['paper_width']?>mm !important
            }
        }
    </style>
</head>

<body class="small80">
<div style="position: absolute;top:0;right: 20mm;z-index: 1000;">
    <button id="printBtn" type="button" class="btn btn-primary" style="background-color: cornflowerblue;"
            onclick="window.print()">
        Print
    </button>
</div>
<section class="sheet no-height">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            <div id="preview-holder" style="background-color: white;border-radius: 1px; box-shadow: 0 0 1px grey;">
                <div class="barcode-item text-dark d-flex flex-column align-items-center">
                    <div class="text-weight-bold company-name"></div>
                    <img class="barcode" alt="">
                    <div>
                        <div class="m-none p-none text-weight-bold name_text">Item:</div>
                        <div class="m-none p-none text-weight-bold price_text">Price: TSH </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    window.onload = function () {
        previewBarcode();
        window.print();

    };

    function previewBarcode() {
        let previewHolder = $('#preview-holder');
        let company_name = `<?=CS_COMPANY?>`;
        let product_name = `<?=$barcode_data['product_name']?>`;
        let currencyname = `<?=$barcode_data['currencyname']?>`;
        let price = `<?=formatN($barcode_data['quick_price'])?>`;
        let nearby_expiry = `<?=$barcode_data['nearby_expiry']?>`;
        let barcode_text = `<?=$barcode_data['barcode_text']?>`;
        let barcode_type = `<?=$barcode_settings['barcode_type']?>`;
        let paper_width = `<?=$barcode_settings['paper_width']?>`;
        let paper_height = `<?=$barcode_settings['paper_height']?>`;
        let barcode_height = `<?=$barcode_settings['barcode_height']?>`;
        let qty = parseInt(`<?=$barcode_data['qty']?>`);
        let scale = `<?=$barcode_settings['scale']?>`;
        let vertical_margin = `<?=$barcode_settings['vertical_margin']?>`;
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
        let show_text = false;

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
</body>
</html>
