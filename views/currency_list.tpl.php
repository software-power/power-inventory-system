<header class="page-header">
    <h2>Currency</h2>
</header>
<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <h2 class="panel-title col-md-3 pl-lg">Currencies</h2>
                    <div class="col-md-9 d-flex justify-content-end">
                        <a class="btn btn-default" href="#currency-modal" data-toggle="modal"> <i
                                    class="fa fa-plus"></i>
                            Add Currency</a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover mb-none" style="font-size:10pt">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Currency Code</th>
                        <th>Description</th>
                        <th>Base Exchange Rate</th>
                        <th>Created On</th>
                        <th>Created by</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($currency_list as $id => $R) { ?>
                        <tr>
                            <td width="80px"><?= $id + 1 ?></td>
                            <td>
                                <?= $R['currencyname'] ?>
                                <? if ($R['base'] == 'yes') { ?>
                                    <span class="badge bg-success ml-md">base</span>
                                <? } ?>
                            </td>
                            <td><?= $R['description'] ?></td>
                            <td><?= $R['rate_amount'] ?></td>
                            <td><?= fDate($R['doc'], 'd M Y') ?></td>
                            <td><?= $R['createdbyname'] ?></td>
                            <td><?= $R['status'] ?></td>
                            <td style="text-align:center">
                                <button type="button" class="btn btn-default btn-sm" data-toggle="modal"
                                        data-target="#currency-modal" data-mode="edit"
                                        data-currencyid="<?= $R['currencyid'] ?>"
                                        data-currencyname="<?= $R['currencyname'] ?>"
                                        data-description="<?= $R['description'] ?>"
                                        data-rateid="<?= $R['rateid'] ?>"
                                        data-exchange-rate="<?= $R['rate_amount'] ?>"
                                        data-status="<?= $R['status'] ?>">
                                    <i class="fa fa-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="currency-modal" tabindex="-1" role="dialog" aria-labelledby="currency-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <form action="<?= url('currencies', 'save_currency') ?>" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="mode">Add</span> Currency</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Code</label>
                        <input type="hidden" class="inputs currencyid" name="currency[id]">
                        <input type="text" class="form-control inputs name" name="currency[name]"
                               placeholder="currency code eg. TSH" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" name="currency[description]" class="form-control inputs description"
                               placeholder="eg. Tanzanian Shillings" required>
                    </div>
                    <div class="form-group" title="exchange rate to base currency">
                        <label>Exchange rate</label>
                        <input type="hidden" class="inputs rateid" name="rateid">
                        <input type="number" name="exchange_rate" class="form-control inputs exchange_rate"
                               placeholder="eg. 2300" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="">Status</label>
                        <select class="form-control status" name="currency[status]" required>
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
        $('#currency-modal').on('show.bs.modal', function (e) {
            let source = $(e.relatedTarget);
            let modal = $(this);
            $(modal).find('.inputs').val('');

            if ($(source).data('mode') === 'edit') {
                $(modal).find('.mode').text('Edit');

                $(modal).find('.currencyid').val($(source).data('currencyid'));
                $(modal).find('.name').val($(source).data('currencyname'));
                $(modal).find('.description').val($(source).data('description'));
                $(modal).find('.rateid').val($(source).data('rateid'));
                $(modal).find('.exchange_rate').val($(source).data('exchange-rate'));
                $(modal).find('.status').val($(source).data('status'));
            } else {
                $(modal).find('.mode').text('Add');
            }



        })
    });
</script>