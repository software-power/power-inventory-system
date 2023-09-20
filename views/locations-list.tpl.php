<header class="page-header">
    <h2>Locations</h2>
</header>


<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">List of Locations</h2>
                <a href="#location-modal" class="btn btn-success btn-sm" data-toggle="modal"><i class="fa fa-plus"></i> Add
                    Locations</a>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <? if (CS_TALLY_TRANSFER) { ?>
                                <th>Tally Cash Ledger</th>
                            <? } ?>
                            <? if (CS_SUPPORT_INTEGRATION) { ?>
                                <th title="Support Serial branch code">Serial Branch code</th>
                            <? } ?>
                            <th>Bulk Store</th>
                            <th>Status</th>
                            <th>Address</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($locations as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $R['id'] ?></td>
                                <td>
                                    <?= $R['name'] ?>
                                    <? if ($R['default_load']) { ?>
                                        <small class="badge bg-success" style="font-size: 8pt">default</small>
                                    <? } ?>
                                </td>
                                <td><?= $R['branchname'] ?></td>
                                <? if (CS_TALLY_TRANSFER) { ?>
                                    <td><?= $R['tally_cash_ledger'] ?></td>
                                <? } ?>
                                <? if (CS_SUPPORT_INTEGRATION) { ?>
                                    <td><?= $R['support_branchcode'] ?></td>
                                <? } ?>
                                <td><?= $R['bulk_store'] ? 'Yes' : 'No' ?></td>
                                <td class="text-capitalize"><?= $R['status'] ?></td>
                                <td title="<?= $R['address'] ?>"><?= explode(PHP_EOL, $R['address'])[0] ?>...</td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#location-modal" data-toggle="modal"
                                               title="Edit" data-mode="'edit" data-id="<?= $R['id'] ?>" data-name="<?= $R['name'] ?>"
                                               data-address="<?= $R['address'] ?>" data-cashledger="<?= $R['tally_cash_ledger'] ?>"
                                               data-branchid="<?= $R['branchid'] ?>" data-default="<?= $R['default_load'] ?>"
                                               data-supportbranchcode="<?= $R['support_branchcode'] ?>"
                                               data-bulkstore="<?= $R['bulk_store'] ?>"
                                               data-bankids='<?= json_encode(explode(',', $R['bankids'])) ?>'
                                               data-status="<?= $R['status'] ?>">
                                                <i class="fa-pencil fa"></i> edit</a>
                                            <? if ($R['status'] == 'active') { ?>
                                                <form action="<?= url('locations', 'enable_disable') ?>" class="m-none" method="post"
                                                      onsubmit="return confirm('Do you want to disable this location?')">
                                                    <input type="hidden" name="location[id]" value="<?= $R['id'] ?>">
                                                    <input type="hidden" name="location[status]" value="inactive">
                                                    <button class="dropdown-item"><i class="fa fa-trash"></i> Disable</button>
                                                </form>
                                            <? } else { ?>
                                                <form action="<?= url('locations', 'enable_disable') ?>" class="m-none" method="post"
                                                      onsubmit="return confirm('Do you want to enable this location?')">
                                                    <input type="hidden" name="location[id]" value="<?= $R['id'] ?>">
                                                    <input type="hidden" name="location[status]" value="active">
                                                    <button class="dropdown-item"><i class="fa fa-check"></i> Enable</button>
                                                </form>
                                            <? } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>


<div class="modal fade" id="location-modal" tabindex="-1" role="dialog" aria-labelledby="location-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add Location</h4>
            </div>
            <form action="<?= url('locations', 'save_location') ?>" method="post">
                <input type="hidden" name="location[id]" class="locationid">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            Name:
                            <input type="text" name="location[name]" class="form-control name" placeholder="location name" required>
                        </div>
                        <div class="form-group col-md-6">
                            Branch:
                            <select name="location[branchid]" class="form-control branchid" required>
                                <? foreach ($branches as $b) { ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6" title="default location for branch">
                            Default Load:
                            <select name="location[default_load]" class="form-control default-load">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <? if (CS_SUPPORT_INTEGRATION) { ?>
                            <div class="form-group col-md-6" title="Branch code in support where serial numbers will be sent">
                                Serial Branch code:
                                <input type="text" name="location[support_branchcode]" class="form-control support_branchcode"
                                       placeholder="serial branch code">
                            </div>
                        <? } ?>
                        <? if (CS_TALLY_TRANSFER) { ?>
                            <div class="form-group col-md-6">
                                Tally Cash Ledger:
                                <input type="text" name="location[tally_cash_ledger]" class="form-control cash_ledger"
                                       placeholder="tally cash ledger"
                                       required>
                            </div>
                        <? } ?>
                        <div class="form-group col-md-6">
                            Bulk Store:
                            <select name="location[bulk_store]" class="form-control bulk_store">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            Status:
                            <select name="location[status]" class="form-control status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="form-group col-md-6">
                            Address: <small class="text-danger">This information will appear while printing eg. invoices,orders,
                                etc..</small>
                            <textarea name="location[address]" class="form-control text-sm address"
                                      placeholder="Building&#10;Street, City&#10;Tel or Mobile&#10;PO BOX&#10;Email&#10;Website" rows="8"
                                      required></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <h5>Bank details <small class="text-danger">This information will appear while printing eg. invoices,orders,
                                    etc..</small>
                            </h5>
                            <div style="max-height: 30vh;overflow-y: auto;">
                                <table class="table table-condensed table-bordered" style="font-size: 10pt;">
                                    <thead>
                                    <tr>
                                        <th class="text-center">
                                            <button type="button" class="btn btn-default btn-xs" onclick="checkAllBanks(this)">Check all
                                            </button>
                                        </th>
                                        <th>Bank name</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <? foreach ($banks as $b) { ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="bankids" name="bankids[]" value="<?= $b['id'] ?>">
                                            </td>
                                            <td title="<?= $b['name'] ?> <?= $b['accno'] ?>"><?= $b['name'] ?></td>
                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm save-btn">Save</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#location-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        let bankids = source.data('bankids');
        // console.log(bankids);


        $(modal).find('.bankids').prop('checked', false);
        CHECK_ALL_BANK = false;

        if (source.data('mode')) {
            $(modal).find('.modal-title').text('Edit Location');
            $(modal).find('.locationid').val(source.data('id'));
            $(modal).find('.name').val(source.data('name'));
            $(modal).find('.branchid').val(source.data('branchid'));
            $(modal).find('.address').val(source.data('address'));
            $(modal).find('.cash_ledger').val(source.data('cashledger'));
            $(modal).find('.default-load').val(source.data('default'));
            $(modal).find('.support_branchcode').val(source.data('supportbranchcode'));
            $(modal).find('.bulk_store').val(source.data('bulkstore'));
            $(modal).find('.status').val(source.data('status'));

            //check bankids
            $(modal).find('.bankids').each(function (i, item) {
                if ($.inArray($(item).val(), bankids) !== -1) $(item).prop('checked', true);
            });

        } else {
            $(modal).find('.modal-title').text('Add Location');
            $(modal).find('.locationid,.name,.address,.cash_ledger,.support_branchcode').val('');

            $(modal).find('.branchid').val($(modal).find('.branchid option').eq(0).val());
            $(modal).find('.default-load,.bulk_store').val('0');
            $(modal).find('.status').val('active');

        }
    });

    let CHECK_ALL_BANK = false;

    function checkAllBanks(obj) {
        CHECK_ALL_BANK = !CHECK_ALL_BANK;
        $(obj).closest('.modal').find('.bankids').prop('checked', CHECK_ALL_BANK);
    }
</script>