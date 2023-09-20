<style media="screen">
    .action-holder {
        float: right;
    }

    .center-panel {
        width: 71%;
        margin: 0 auto;
    }
</style>
<header class="page-header">
    <h2><? if ($edit) echo 'Edit'; else echo 'Add'; ?>Royalty Cards</h2>
</header>
<div class="row">
    <div class="col-lg-12">
        <section class="panel center-panel">
            <header class="panel-heading">
                <div class="action-holder">
                    <a href="?module=home&action=index" class="btn"><i class="fa fa-home"></i> Home</a>
                </div>
                <h2 class="panel-title">Add Royalty Cards</h2>
            </header>
            <div class="panel-body">
                <form id="form" class="form-horizontal form-bordered" method="post"
                      action="<?= url('royalty_card', 'add') ?>">
                    <input type="hidden" name="id" value="<?= $group['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Quantity</h5>
                            <input type="number" placeholder="Quantity" class="required form-control" title="Quantity"
                                   name="royalty[quantity]" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <button type="submit" class="mb-xs mt-xs mr-xs btn btn-danger btn-block">
                                        <i class="fa fa-gear"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <section class="panel center-panel">
            <header class="panel-heading">
                <h2 class="panel-title">All Royalty Cards</h2>
            </header>
            <div class="panel-body">
                <!-- <table class="table table-striped table-sm col-lg-12" id="userTable"> -->
                <table class="table table-hover table-striped mb-none" id="userTable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Card No.</th>
                        <th>Client</th>
                        <th>Assigned On</th>
                        <th>Created on</th>
                        <th>Created by</th>
                        <th>Status</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?
                    foreach ($cards as $key => $c) { ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td style="color: #D2322D; font-weight: 700;"><?= $c['name'] ?></td>
                            <td><?= $c['clientname'] ?></td>
                            <td><?= $c['assign_date'] ? fDate($c['assign_date']) : '' ?></td>
                            <td><?= fDate($c['doc']) ?></td>
                            <td><?= $c['creator'] ?></td>
                            <td><?= $c['status'] ?></td>

                        </tr>
                        <?
                    }
                    ?>

                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>

</script>
