<div class="d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">List of VAT Categories</h2>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end align-items-center">
                        <a href="#tax-category-modal" class="btn btn-default btn-sm" data-toggle="modal">
                            <i class="fa fa-plus"></i> Add VAT category</a>
                        <a class="btn btn-default btn-sm ml-md" href="?module=home&action=index"> <i class="fa fa-home"></i> Home</a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt;">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>VAT%</th>
                        <? if (CS_VFD_TYPE != VFD_TYPE_ZVFD) { ?>
                            <th>Taxcode</th>
                            <th>Product Count</th>
                        <? } else { ?>
                            <th>API ID</th>
                        <? } ?>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($categoryList as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $id + 1 ?></td>
                            <td><?= $R['name'] ?></td>
                            <td><?= $R['vat_percent'] ?></td>
                            <? if (CS_VFD_TYPE != VFD_TYPE_ZVFD) { ?>
                                <td class="text-capitalize"><?= $R['taxcodename'] ?></td>
                                <td><?= $R['product_count'] ?></td>
                            <? } else { ?>
                                <td><?= $R['zvfd_tax_type'] ?></td>
                            <? } ?>
                            <td><?= $R['status'] ?></td>
                            <td class="d-flex align-items-center">
                                <button type="button" class="btn btn-default btn-xs" data-target="#tax-category-modal" data-toggle="modal"
                                        data-mode="edit"
                                        data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>" data-vatpercent="<?= $R['vat_percent'] ?>"
                                        data-taxcode="<?= $R['taxcode'] ?>" data-status="<?= $R['status'] ?>"
                                        data-taxtype="<?= $R['zvfd_tax_type'] ?>">
                                    <i class="fa-pencil fa"></i> Edit
                                </button>
                                <? if ($R['product_count'] == 0) { ?>
                                    <form action="<?= url('categories', 'delete_category') ?>" method="post" class="m-none ml-md"
                                          onsubmit="return confirm(`Do you want to delete this category?`)">
                                        <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> delete</button>
                                    </form>
                                <? } ?>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="tax-category-modal" tabindex="-1" role="dialog" aria-labelledby="tax-category-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="<?= url('categories', 'category_save') ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="mode">Add</span> Tax Category</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Category name</label>
                        <input type="hidden" class="inputs categoryid" name="category[id]">
                        <input type="text" class="form-control inputs name" name="category[name]"
                               placeholder="category name" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="">VAT %</label>
                        <input type="number" min="0" step="0.01" class="form-control inputs vat_percent" name="category[vat_percent]"
                               required autocomplete="off" placeholder="vat percent">
                    </div>
                    <? if (CS_VFD_TYPE != VFD_TYPE_ZVFD) { ?>
                        <div class="form-group">
                            <label for="">Taxcode</label>
                            <select class="form-control text-capitalize taxcode" name="category[taxcode]" required>
                                <? foreach ($taxcodes as $code) { ?>
                                    <option value="<?= $code['id'] ?>"><?= $code['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    <? } else { ?>
                        <div class="form-group">
                            <label for="">API ID</label>
                            <input type="text" class="form-control inputs zvfd_tax_type" name="category[zvfd_tax_type]"
                                   required autocomplete="off" placeholder="ZRD Tax Type">
                        </div>
                    <? } ?>
                    <div class="form-group">
                        <label for="">Status</label>
                        <select class="form-control status" name="category[status]" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm confirm-btn">Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#tax-category-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);

            if (source.data('mode') === 'edit') {
                $(modal).find('.mode').text('Edit');
                $(modal).find('.categoryid').val(source.data('id'));
                $(modal).find('.name').val(source.data('name'));
                $(modal).find('.vat_percent').val(source.data('vatpercent'));
                $(modal).find('.taxcode').val(source.data('taxcode'));
                $(modal).find('.zvfd_tax_type').val(source.data('taxtype'));
                $(modal).find('.status').val(source.data('status'));
            } else {
                $(modal).find('.mode').text('Add');
                $(modal).find('.inputs').val('');
            }
        });
    })
</script>