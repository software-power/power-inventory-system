<header class="page-header">
    <h2>Update base percentage</h2>
</header>

<div class="col-md-6 col-md-offset-3">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Quick update Base Percentage</h2>
            </header>
            <div class="panel-body">
                <form action="<?= url('products', 'update_base_percentage') ?>" method="post">
                    <div class="form-group">
                        <label>Base Percentage</label>
                        <input type="number" class="form-control" name="base_percent"
                               min="0" value="<?= CS_DEFAULT_BASE ?>" required>
                    </div>
                    <div class="d-flex justify-content-end mt-md">
                        <button class="btn btn-success">Update</button>
                    </div>

                </form>
            </div>
    </div>
</div>

