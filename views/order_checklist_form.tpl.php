<style>
    .enabled-checklist {
        background-color: #ebebeb;
    }
</style>
<header class="page-header">
    <h2>Order Checklist Form</h2>
</header>
<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <div class="panel-heading">
                <h2 class="panel-title">Order Checklist Form <span class="ml-md text-primary">Order No: <?= $orderno ?></span></h2>
            </div>
            <div class="panel-body">
                <form action="<?= url('orders', 'save_orderchecklist') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="orderid" value="<?= $orderno ?>">
                    <div class="table-responsive">
                        <table class="table mb-none" style="font-size: 11pt">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Issued by</th>
                                <th>Issued date</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <? foreach ($checklists as $id => $R) { ?>
                                <tr style="cursor: pointer" title="Double click to enable"
                                    ondblclick="enable_checklist(this)">
                                    <td width="80px"><?= $id + 1 ?>.</td>
                                    <td><?= $R['name'] ?></td>
                                    <td>
                                        <select name="list[<?= $R['id'] ?>][remark]" class="form-control" disabled>
                                            <option value="">-- status --</option>
                                            <option <?= selected($R['remark'], Checklists::STATUS_PROCESSING) ?>
                                                    value="<?= Checklists::STATUS_PROCESSING ?>"><?= ucfirst(Checklists::STATUS_PROCESSING) ?></option>
                                            <option <?= selected($R['remark'], Checklists::STATUS_DONE) ?>
                                                    value="<?= Checklists::STATUS_DONE ?>"><?= ucfirst(Checklists::STATUS_DONE) ?></option>
                                        </select>
                                    </td>
                                    <td><?= $R['username'] ?></td>
                                    <td><?= $R['doc'] ? fDate($R['doc'], 'd M Y H:i') : '' ?></td>
                                    <td>
                                        <div>
                                            <? if (file_exists($R['file_path'])) { ?>
                                                <a target="_blank" href="<?=$R['file_path']?>" class="btn btn-primary btn-sm" title="view document">
                                                    <i class="fa fa-eye"></i> view
                                                </a>
                                            <? } ?>
                                            <button type="button" class="btn btn-default btn-sm upload-btn" title="upload document"
                                                    onclick="choose_document(this)" disabled>
                                                <i class="fa fa-upload"></i> Upload
                                            </button>
                                            <input type="file" name="list<?= $R['id'] ?>" class="document-file"
                                                   accept="application/pdf,image/jpeg,image/png" style="display: none" disabled>
                                            <button type="button" class="btn btn-warning btn-sm disable-btn" title="disable"
                                                    onclick="disable_checklist(this)" disabled>
                                                <i class="fa fa-close"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row d-flex justify-content-center mt-md">
                        <div class="col-md-2">
                            <button class="btn btn-success btn-block"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
    $(function () {
    });

    function enable_checklist(obj) {
        let parent = $(obj);
        $(parent).addClass('enabled-checklist');
        $(parent).find('select,.upload-btn,.disable-btn,.document-file').prop('disabled', false);
    }

    function disable_checklist(obj) {
        let parent = $(obj).closest('tr');
        $(parent).removeClass('enabled-checklist');
        $(parent).find('select,.upload-btn,.disable-btn,.document-file').prop('disabled', true);
    }

    function choose_document(obj) {
        $(obj).closest('tr').find('.document-file').trigger('click');
    }
</script>