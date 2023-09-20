<header class="page-header">
    <h2>Account Manager</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-4">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">Account Manager</h2>
                <a href="?" class="btn btn-default btn-sm">Go Home</a>
            </header>
            <div class="panel-body">
                <form action="<?= url('import', 'update_clients_acc_mng') ?>" method="post" enctype="multipart/form-data"
                      onsubmit="return validateInputs()">
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

<script>

    function validateInputs() {
        $('#loading-spinner').css('visibility', 'visible');
        $('#save-btn').prop('disabled', true);
    }
</script>
