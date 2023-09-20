<html>
<!doctype html>
<html class="fixed sidebar-left-collapsed" lang="en">
<head>
    <script src="assets/vendor/jquery/jquery.js"></script>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <!-- Basic -->
    <meta charset="UTF-8">
    <!-- <title><?= $pagetitle ?></title> -->
    <title><?= $_SESSION['pagetitle'] ?></title>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="icon" href="images/pcfavicon.png" type="image/gif" sizes="16x16">


    <!-- Web Fonts  -->
    <!--    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light"-->
    <!--          rel="stylesheet" type="text/css">-->

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css"/>

    <link rel="stylesheet" href="assets/vendor/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="assets/vendor/magnific-popup/magnific-popup.css"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/css/datepicker3.css"/>

    <!-- Specific Page Vendor CSS -->
    <link rel="stylesheet" href="assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css"/>
    <link rel="stylesheet" href="assets/vendor/pnotify/pnotify.custom.css"/>
    <link rel="stylesheet" href="assets/vendor/select2/select2.css"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css"/>
    <link rel="stylesheet" href="assets/vendor/jquery-datatables-bs3/assets/css/datatables.css"/>
    <link rel="stylesheet" href="assets/vendor/jquery-timepicker/jquery.timepicker.css"/>
    <link rel="stylesheet" href="assets/vendor/multipleInput/multipleInput.css"/>

    <link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
    <script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>

    <!-- Dashboard Page Vendor CSS -->
    <link rel="stylesheet" href="assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css"/>
    <link rel="stylesheet" href="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css"/>
    <link rel="stylesheet" href="assets/vendor/morris/morris.css"/>


    <!-- FROALA Editor-->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="assets/vendor/froala/css/froala_editor.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/froala_style.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/code_view.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/colors.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/emoticons.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/image_manager.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/image.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/line_breaker.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/table.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/char_counter.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/video.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/fullscreen.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/file.css">
    <link rel="stylesheet" href="assets/vendor/froala/css/plugins/quick_insert.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <style>
        .pad20 {

            padding-left: 20px;
        }

        .padtop20 {
            padding-top: 20px
        }

        .text-right {
            text-align: right;
            padding-right: 30px;
        }

        .red {
            color: red;
        }

        .pointer {
            cursor: pointer;
        }

        .for_heading h2 {
            text-transform: capitalize;
        }

        .rowcolor {
            background: #ecedf0;
            font-weight: bold;
        }

        .hide-scroll {
            display: block;
            width: 15px;
            height: 21px;
            background: #fdfdfd;
            position: absolute;
            top: 6px;
            right: 11px;
        }

        ::-moz-selection {
            /* Code for Firefox */
            color: #fff;
            background-color: #d2322d;
        }

        ::selection {
            color: #fff;
            background-color: #d2322d;
        }

        html .pagination > li.active a, html.dark .pagination > li.active a {
            background-color: #d2322d !important;

        }

        html .pagination > li.active a, html.dark .pagination > li.active a, html .pagination > li.active span, html.dark .pagination > li.active span, html .pagination > li.active a:hover, html.dark .pagination > li.active a:hover, html .pagination > li.active span:hover, html.dark .pagination > li.active span:hover, html .pagination > li.active a:focus, html.dark .pagination > li.active a:focus, html .pagination > li.active span:focus, html.dark .pagination > li.active span:focus {
            /* background-color: #0088cc; */
            background-color: #d2322d !important;
            border-color: #d2322d !important;
        }

        table.dataTable tbody th.focus, table.dataTable tbody td.focus {
            box-shadow: inset 0 0 1px 2px #d2322d;
        }
    </style>


    <style media="screen">
        .formholder h5 {
            font-size: 15px;
            font-weight: 600;
        }

        .for-input {
            padding: 8px;
            height: 40px;
            font-size: 14px;
            /* border: none; */
            outline: none;
            margin-top: 2px;
        }

        .select2-container--default .select2-selection--single {
            padding: 8px;
            height: 40px;
            font-size: 14px;
            /* border: none; */
            outline: none;
            margin-top: 2px;
        }

        .formModel {
            display: none;
            position: fixed;
            width: 100%;
            z-index: 14;
            background: rgba(238, 238, 238, 0.6196078431372549);
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 100%;
        }

        .formholder {
            position: relative;
            display: none;
            z-index: 26;
            border-radius: 5px;
            padding: 24px;
            width: 100%;
            background: #ededee;
            height: auto;
            -webkit-box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
            -moz-box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
            box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
        }

        .panelControl {
            float: right;
        }

        .for-formanage {
            border: 1px solid #47a447;
            padding: 9px;
            height: auto;
            border-radius: 5px;
        }

        .dropleft .dropdown-menu {
            top: 0;
            right: 100%;
            left: auto;
            margin-top: 0;
            margin-right: .125rem;
        }

        a.dropdown-item {
            text-decoration: none;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: .25rem 1.5rem;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .close-btn-holder {
            right: 0;
            top: -41px;
            position: absolute;
            background: white;
            padding: 10px;
        }

        .table-responsive {
            min-height: 223px;
        }

        .datepickers {
            /* border:#333 solid 1px; */
            background: #fff !important;
        }

        table.dataTable tbody th.focus, table.dataTable tbody td.focus {
            box-shadow: inset 0 0 1px 2px #d2322d !important;
        }

        .pagination > li a {
            color: #d2322d !important;
        }

        html .pagination > li.active a, html.dark .pagination > li.active a {
            color: #FFFFFF !important;
        }

    </style>


    <!-- Theme CSS -->
    <link rel="stylesheet" href="assets/stylesheets/theme.css"/>

    <!-- Skin CSS -->
    <link rel="stylesheet" href="assets/stylesheets/skins/default.css"/>

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="assets/stylesheets/theme-custom.css">

    <!-- Head Libs -->
    <script src="assets/vendor/modernizr/modernizr.js"></script>


</head>
<body>
<noscript>
    <style>
        section.body {
            display: none !important;
        }

        html, body {
            width: 100%;
            height: 100%;
        }
    </style>
    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
        <div>
            <div class="d-flex align-items-center">
                <object data="images/loading-gear.svg" type="image/svg+xml"></object>
                <h3 class="ml-md">Sorry!, Javascript is disabled in your browser</h3>
            </div>
            <p class="mt-md">You are seeing this page because javascript is disabled in your browser. Enable javascript
                so the system can function well.</p>
        </div>

    </div>
</noscript>
<section class="body">
    <?= $control_panel ?>

    <div class="inner-wrapper">
        <?= $menu ?>

        <section role="main" class="content-body">
            <div id="select2-ajax-custom-loading-spinner" class="d-flex align-items-center" style="height: 0;width: 0;visibility: hidden">
                <object data="images/loading_spinner.svg" type="image/svg+xml" height="25" width="25"></object>
                <span>Searching..</span>
            </div>
            <!-- Help Dialog on each page-->
            <header <? if ($userProfile['help'] == 0){ ?>style="display:none"<? } ?> id="helpBox">
                <div class="help col-sm-12 col-md-12 col-lg-12" <? if (!$help){ ?>style="display:none"<? } ?>>
                    <div class="panel-body">
                        <div class="col-sm-11 col-md-11 col-lg-11">
                            <h3>Guide</h3>
                            <!-- Help info comes here from the controllers-->
                            <?= $help ?>
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <i class="fa fa-2x fa-minus red pointer" title="Hide help" onclick="hideMe('temp')"></i>

                            <i class="fa fa-2x fa-close red pointer" title="Dont show help again"
                               onclick="hideMe('perm')"></i></div>
                    </div>
                    <br/>
                </div>
            </header>


            <?= $content ?>

            <a class="scroll-to-top hidden-mobile visible" href="#"><i class="fa fa-chevron-up"></i></a>
            <!--<a style="display: block;height: 9px;bottom: 30px;text-align: center;" target="_blank" href="http://www.powerwebtz.com">Designed and Maintained by PowerWeb</a>-->
        </section>
</section>

<!------------------------------------->
<!-- Vendor -->

<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

<!-- Specific Page Vendor -->
<script src="assets/vendor/pnotify/pnotify.custom.js"></script>
<script src="assets/vendor/select2/select2.full.js"></script>
<script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
<script src="assets/vendor/jquery-validation/jquery.validate.js"></script>
<script src="assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
<script src="assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js"></script>
<script src="assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>
<script src="assets/vendor/jquery-timepicker/jquery.timepicker.js"></script>
<script src="assets/vendor/multipleInput/multipleInput.js"></script>

<!--Dashboard-->
<script src="assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script src="assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js"></script>
<script src="assets/vendor/jquery-appear/jquery.appear.js"></script>
<script src="assets/vendor/jquery-easypiechart/jquery.easypiechart.js"></script>
<script src="assets/vendor/flot/jquery.flot.js"></script>
<script src="assets/vendor/flot-tooltip/jquery.flot.tooltip.js"></script>
<script src="assets/vendor/flot/jquery.flot.pie.js"></script>
<script src="assets/vendor/flot/jquery.flot.categories.js"></script>
<script src="assets/vendor/flot/jquery.flot.resize.js"></script>
<script src="assets/vendor/jquery-sparkline/jquery.sparkline.js"></script>
<script src="assets/vendor/raphael/raphael.js"></script>
<script src="assets/vendor/morris/morris.js"></script>
<script src="assets/vendor/gauge/gauge.js"></script>
<script src="assets/vendor/snap-svg/snap.svg.js"></script>
<script src="assets/vendor/liquid-meter/liquid.meter.js"></script>

<!-- Theme Base, Components and Settings -->
<script src="assets/javascripts/theme.js"></script>

<!-- Theme Custom -->
<script src="assets/javascripts/theme.custom.js"></script>

<!-- Theme Initialization Files -->
<script src="assets/javascripts/theme.init.js"></script>

<!-- Specific Page Vendor -->
<script src="assets/vendor/jquery-autosize/jquery.autosize.js"></script>
<script src="assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="assets/vendor/date.js"></script>

<!-- Examples -->
<script src="assets/javascripts/forms/examples.validation.js"></script>
<script src="assets/javascripts/tables/examples.datatables.default.js"></script>
<script src="assets/javascripts/tables/examples.datatables.tabletools.js"></script>
<!--<script src="assets/javascripts/dashboard/examples.dashboard.js"></script>-->
<!--<script src="assets/javascripts/ui-elements/examples.modals.js"></script>-->

<!-- FROALA -->
<script type="text/javascript" src="assets/vendor/froala/js/froala_editor.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/align.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/char_counter.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/code_beautifier.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/code_view.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/colors.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/draggable.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/emoticons.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/entities.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/file.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/font_size.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/font_family.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/fullscreen.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/image.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/image_manager.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/line_breaker.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/inline_style.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/link.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/paragraph_format.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/paragraph_style.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/quick_insert.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/quote.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/table.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/save.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/url.min.js"></script>
<script type="text/javascript" src="assets/vendor/froala/js/plugins/video.min.js"></script>
<script type="text/javascript" src="assets/barcode/JsBarcode.all.min.js"></script>

<!-- <script type="text/javascript" src="assets/qr/jquery.min.js"></script> -->
<script src="assets/qr/qrcode.js"></script>
<script src="assets/js/cleave.min.js"></script>
<script src="assets/js/custom.js"></script>

<script type='text/javascript'>

    $(document).ready(function () {
        $("a#search_show").click(function () {
            $(this).hide();
            $("table#filter_table").show();
            $("#search_hide").show();
            $(".content").animate({scrollTop: 1});
        });
    });

    $(document).ready(function () {
        $("a#search_hide").click(function () {
            $(this).hide();
            $("table#filter_table").hide();
            $("#search_show").show();
        });
        $("table#filter_table").hide();
    });

    $(function () {
        $(".froala").froalaEditor()


    });

    function triggerMessage(msg, delay = 1000) {
        new PNotify({
            title: 'Success',
            text: '' + msg + '',
            type: 'success',
            delay: delay
        });
    }

    function triggerError(msg, delay = 1000) {
        new PNotify({
            title: 'Error',
            text: '' + msg + '',
            type: 'error',
            delay: delay
        });
    }

    function triggerWarning(msg, delay = 1000) {
        new PNotify({
            title: 'Warning',
            text: '' + msg + '',
            type: 'warning',
            delay: delay
        });
    }

    function date() {
        $('.datepicker').datepicker({
            orientation: "top",
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        $('.datepicker2').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            // startDate: '+1d'
        });

        $(".monthpicker").datepicker({
            orientation: "top",
            format: "mm/yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true,
        });

        $(".month-picker2").datepicker({
            orientation: "top",
            format: "mm-yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true,
        });

    }

    $(function () {
        try {
            date();
            <? if ($error) {?>
            triggerError(`<?=$error?>`, `<?=$delay?>`);
            <?} ?>
            <? if ($message) {?>
            triggerMessage(`<?=$message?>`, `<?=$delay?>`);
            <?} ?>
        } catch (e) {
        }
    });

    //closing popovers on outside click
    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });


    $('.zoomModal').magnificPopup({
        type: 'inline',

        fixedContentPos: false,
        fixedBgPos: true,

        overflowY: 'auto',

        closeBtnInside: true,
        preloader: false,

        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-zoom-in',
        modal: true
    });

    /*
        Modal Dismiss
    */
    $(document).on('click', '.modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });

    function hideMe(type) {

        if (type == 'perm') {
            $.get('?module=home&action=hidehelp&format=json', null, function (d) {
                CC = JSON.parse(d);


            });
        }
        $("#helpBox").slideUp();
    }

    $(document).ready(function () {
        $('#printing_area').DataTable({
            dom: '<"top"fB>t<"bottom"ip>',
            colReorder: true,
            keys: true,
            buttons: ['excelHtml5', 'csvHtml5'],
            exportOptions: {
                columns: ':not(:last-child)',
            },
            <?if($_GET['status']){?>
            title: '<?=$_GET['status']?>',
            <?}?>
        });
    });

    $(document).ready(function () {
        $('#userTable').DataTable({
            dom: '<"top"fBl>t<"bottom"ip>',
            colReorder: true,
            keys: true,
            buttons: ['excelHtml5', 'csvHtml5'],
            exportOptions: {
                columns: ':not(:last-child)',
            },
        });
    });

    function truncateDecimals(number, digits) {
        let multiplier = Math.pow(10, digits), adjustedNum = number * multiplier,
            truncatedNum = Math[adjustedNum < 0 ? 'ceil' : 'floor'](adjustedNum);

        return truncatedNum / multiplier;
    }

    function thousands_separator(selector,decimal=2) {
        $(selector).toArray().forEach(function(field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: decimal,
                numeralPositiveOnly: true
            });
        });
    }
    function qtyInput(selector) {
        $(selector).toArray().forEach(function(field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'none',
                numeralDecimalScale: 0,
                numeralPositiveOnly: true
            });
        });
    }
    function priceInput(selector,decimal=2) {
        $(selector).toArray().forEach(function(field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'none',
                numeralDecimalScale: decimal,
                numeralPositiveOnly: true
            });
        });
    }
</script>
</body>
</html>
