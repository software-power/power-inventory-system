<header class="page-header">
    <h2>Mac Address</h2>
</header>


<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">Mac Address List</h2>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#macaddress-modal"><i class="fa fa-plus"></i> Add Mac
                    Address
                </button>
            </header>
            <div class="panel-body">
                <ul id="setting-tabs" class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#list"> <i class="fa fa-list"></i> List</a>
                    </li>
                    <li>
                        <a data-toggle="tab" href="#deleted"> <i class="fa fa-trash"></i> Deleted</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="list" class="tab-pane fade in active">
                        <div class="table-responsive">
                            <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Device Name</th>
                                    <th>MAC-Address</th>
                                    <th>Device Status</th>
                                    <th>User</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($mac_addresses as $id => $R) { ?>
                                    <tr>
                                        <td width="80px"><?= $id + 1 ?></td>
                                        <td><?= $R['device_name'] ?></td>
                                        <td><?= $R['mac_address'] ?></td>
                                        <td><?= ucfirst($R['device_status']) ?></td>
                                        <td><?= $R['username'] ?></td>
                                        <td>
                                            <div class="btn-group dropleft">
                                                <button type="button" class="btn btn-secondary dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#macaddress-modal" data-mode="edit" data-toggle="modal"
                                                       data-id="<?= $R['id'] ?>" data-devicename="<?= $R['device_name'] ?>"
                                                       data-macaddress="<?= $R['mac_address'] ?>" data-status="<?= $R['device_status'] ?>">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </a>
                                                    <? if ($R['userid']) { ?>
                                                        <form action="<?= url('mac_address', 'revoke') ?>" class="m-none" method="post"
                                                              onsubmit="return confirm('Do you want to revoke access for this device')">
                                                            <input type="hidden" name="userid" value="<?= $R['userid'] ?>">
                                                            <button class="dropdown-item"><i class="fa fa-recycle"></i> Revoke User</button>
                                                        </form>
                                                    <? } else { ?>
                                                        <a class="dropdown-item" href="#assign-user-modal" data-toggle="modal" data-id="<?= $R['id'] ?>"
                                                           data-devicename="<?= $R['device_name'] ?>" data-macaddress="<?= $R['mac_address'] ?>">
                                                            <i class="fa fa-user-plus"></i> Assign User
                                                        </a>
                                                    <? } ?>
                                                    <? if ($R['device_status'] == Mac_address::DEVICE_STATUS_ACTIVE) { ?>
                                                        <form action="<?= url('mac_address', 'block') ?>" class="m-none" method="post"
                                                              onsubmit="return confirm('Do you want to block access for this device')">
                                                            <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                                            <button class="dropdown-item"><i class="fa fa-warning"></i> Block Device</button>
                                                        </form>
                                                    <? } else { ?>
                                                        <form action="<?= url('mac_address', 'activate') ?>" class="m-none" method="post"
                                                              onsubmit="return confirm('Do you want to activate this device')">
                                                            <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                                            <button class="dropdown-item"><i class="fa fa-check"></i> Activate Device</button>
                                                        </form>

                                                    <? } ?>
                                                    <form action="<?= url('mac_address', 'delete') ?>" class="m-none" method="post"
                                                          onsubmit="return confirm('Do you want to delete this device')">
                                                        <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                                        <button class="dropdown-item"><i class="fa fa-trash"></i> Delete Device</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="deleted" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Device Name</th>
                                    <th>MAC-Address</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($deleted as $id => $R) { ?>
                                    <tr>
                                        <td width="80px"><?= $id + 1 ?></td>
                                        <td><?= $R['device_name'] ?></td>
                                        <td><?= $R['mac_address'] ?></td>
                                        <td>
                                            <div class="btn-group dropleft">
                                                <button type="button" class="btn btn-secondary dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <form action="<?= url('mac_address', 'restore') ?>" class="m-none" method="post"
                                                          onsubmit="return confirm('Do you want to restore this device')">
                                                        <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                                        <button class="dropdown-item"><i class="fa fa-trash"></i> Restore Device</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="macaddress-modal" role="dialog" aria-labelledby="macaddress-modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('mac_address', 'save_macaddress') ?>" method="post">
                <input type="hidden" class="id" name="macaddress[id]">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><span class="mode">Add</span> Mac Address</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        Device Name:
                        <input type="text" class="form-control devicename" name="macaddress[device_name]" required placeholder="eg. Samsung S8">
                    </div>
                    <div class="form-group">
                        Mac Address:
                        <input type="text" class="form-control macaddress" name="macaddress[mac_address]" required placeholder="eg. 2B:6H:8f:8U:99:00"
                               onkeyup="checkMacAddress(this)">
                        <small id="error-text" class="text-danger text-xs " style="display: none">invalid mac address</small>
                    </div>
                    <div class="form-group">
                        Device Status:
                        <select class="form-control status" name="macaddress[device_status]">
                            <option value="<?= Mac_address::DEVICE_STATUS_ACTIVE ?>"><?= Mac_address::DEVICE_STATUS_ACTIVE ?></option>
                            <option value="<?= Mac_address::DEVICE_STATUS_BLOCKED ?>"><?= Mac_address::DEVICE_STATUS_BLOCKED ?></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="assign-user-modal" role="dialog" aria-labelledby="assign-user-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= url('mac_address', 'assign_user') ?>" method="post">
                <input type="hidden" class="id" name="mac_address_id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Assign User</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                Device Name:
                                <input type="text" class="form-control devicename" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                Mac Address:
                                <input type="text" class="form-control macaddress" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                User:
                                <select id="userid" class="form-control" name="userid" required></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'select user', 2);
    });

    $('#macaddress-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        if ($(source).data('mode')) {
            $(modal).find('.mode').text('Edit');
            $(modal).find('.id').val($(source).data('id'));
            $(modal).find('.devicename').val($(source).data('devicename'));
            $(modal).find('.macaddress').val($(source).data('macaddress'));
            $(modal).find('.status').val($(source).data('status'));

        } else {
            $(modal).find('.mode').text('Add');
        }

    });
    $('#assign-user-modal').on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);

        $(modal).find('.id').val($(source).data('id'));
        $(modal).find('.devicename').val($(source).data('devicename'));
        $(modal).find('.macaddress').val($(source).data('macaddress'));
    });

    function checkMacAddress(obj) {
        let value = $.trim($(obj).val());
        let regexp = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/i;
        if (value.length > 0) {
            if (regexp.test(value)) {
                $('#error-text').hide();
            } else {
                $('#error-text').show();
            }
        }
    }
</script>