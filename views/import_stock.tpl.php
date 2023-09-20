<header class="page-header">
    <h2>Import Product Stock & Prices</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-4">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <? if (isset($upload_result)) { ?>
                    <h2 class="panel-title">Import Result</h2>
                    <a href="?" class="btn btn-default btn-sm">Go Home</a>
                <? } else { ?>
                    <h2 class="panel-title">Import Product Stocks & Prices</h2>
                    <form class="d-flex align-items-center m-0">
                        <input type="hidden" name="module" value="import">
                        <input type="hidden" name="action" value="download_stock_template">
                        <div class="check-box mr-sm">
                            <input type="checkbox" name="with_products" id="with_products" checked value="">
                            <label for="with_products">With products</label>
                        </div>
                        <button class="btn btn-primary btn-sm">Download template</button>
                    </form>
                <? } ?>
            </header>
            <div class="panel-body">
                <form action="<?= url('import', 'upload_stocks') ?>" method="post" enctype="multipart/form-data"
                      onsubmit="return validateInputs()">
                    <div class="form-group">
                        <label>Stock Location</label>
                        <select id="locationid" name="locationid" class="form-control" required></select>
                    </div>
                    <div class="form-group">
                        <label>Choose Excel file</label>
                        <input type="file" class="form-control" name="excel_file"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    </div>
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <object id="loading-spinner" data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"
                                    style="visibility: hidden"></object>
                            <button id="save-btn" class="btn btn-success">Upload</button>
                        </div>
                    </div>
                    <? if ($_SESSION['error']) { ?>
                        <p class="text-danger">Error found</p>
                        <textarea rows="5" readonly class="form-control"><?= $_SESSION['error'] ?></textarea>
                    <? } ?>
                </form>
            </div>
        </section>
    </div>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#locationid', `?module=locations&action=getLocations&format=json`, 'Choose location', 1);
    });

    function validateInputs() {
        let locationid = $('#locationid').val();
        if (!locationid) {
            triggerError('Choose stock location');
            return false;
        }
        $('#loading-spinner').css('visibility', 'visible');
        $('#save-btn').prop('disabled', true);
    }
</script>
