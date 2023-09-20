<!doctype html>
<html class="fixed sidebar-left-collapsed">
<head>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/qr/qrcode.js"></script>
    <script src="assets/barcode/bwip-js.js"></script>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Print Warranty Sticker <?= $sno['productname'] ?></title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>
    <link rel="stylesheet" href="assets/css/custom.css"/>
    <link rel="stylesheet" href="assets/css/paper.css"/>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style>
        body.small80 .sheet {
            width: <?=$sticker_settings['sticker_width']?>mm !important;
        }

        /*.sticker-item{*/
        /*    border:1px solid red;*/
        /*}*/

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
                width: <?=$sticker_settings['sticker_width']?>mm !important
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
        let header_text = `<?=$sticker_settings['header_text']?>`;
        let serialno_text = `<?=$sno['number']?>`;
        let footer_text = `<?=$sticker_settings['footer_text']?>`;
        let barcode_type = `<?=$sticker_settings['barcode_type']?>`;
        let sticker_width = `<?=$sticker_settings['sticker_width']?>`;
        let sticker_height = `<?=$sticker_settings['sticker_height']?>`;
        let sticker_h_padding = `<?=$sticker_settings['sticker_h_padding']?:'0'?>`;
        let sticker_v_padding = `<?=$sticker_settings['sticker_v_padding']?:'0'?>`;
        let barcode_height = `<?=$sticker_settings['barcode_height']?>`;
        let scale = `<?=$sticker_settings['scale']?>`;
        let text_align = 'center';
        let header_text_size = `<?=$sticker_settings['header_text_size']?>`;
        let serialno_text_size = `<?=$sticker_settings['serialno_text_size']?>`;
        let footer_text_size = `<?=$sticker_settings['footer_text_size']?>`;
        let show_header = `<?=$sticker_settings['show_header']?>`;
        let show_serialno = `<?=$sticker_settings['show_serialno']?>`;
        let show_footer = `<?=$sticker_settings['show_footer']?>`;

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
</script>
</body>
</html>
