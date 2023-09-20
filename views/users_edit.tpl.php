<style media="screen">
    .center-panel {
        width: 80%;
        margin: 0 auto;
    }
</style>
<header class="page-header">
    <h2><? if ($edit) echo 'Edit'; else echo 'Add'; ?> User</h2>
</header>
<div class="row">
    <div class="col-lg-12">
        <section class="panel center-panel">
            <header class="panel-heading">
                <h2 class="panel-title"><i class="fa fa-user"></i> User Details</h2>
            </header>
            <div class="panel-body">
                <form id="form" class="form-horizontal form-bordered" method="post"
                      action="<?= url('users', 'users_save') ?>">
                    <input type="hidden" name="users[id]" value="<?= $users['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h5 class="">User Name</h5>
                            </div>
                            <div class="col-md-12">
                                <input placeholder="Username" onblur="checkusername()" id="username" type="text"
                                       required class="form-control" data-toggle="tooltip" data-trigger="hover"
                                       name="users[username]" value="<?= $users['username'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h5 class="">Full Name</h5>
                            </div>
                            <div class="col-md-12">
                                <input type="text" placeholder="Full Name" class="form-control" data-toggle="tooltip"
                                       data-trigger="hover" data-original-title="Enter full name" name="users[name]"
                                       value="<?= $users['name'] ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h5 class="">Mobile</h5>
                            </div>
                            <div class="col-md-12">
                                <input placeholder="Mobile" type="text" class="form-control" data-toggle="tooltip"
                                       data-trigger="hover" data-original-title="Enter Mobile" name="users[mobile]"
                                       value="<?= $users['mobile'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h5>Email</h5>
                            </div>
                            <div class="col-md-12">
                                <input type="text" placeholder="Email" class="form-control" data-toggle="tooltip"
                                       data-trigger="hover" data-original-title="Enter Email" name="users[email]"
                                       value="<?= $users['email'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <? if ($users['delete'] != 'no') { ?>
                            <div class="col-md-3">
                                <div class="col-md-12">
                                    <h5>Role</h5>
                                </div>
                                <div class="col-md-12">
                                    <select id="roleId" name="users[roleid]" class="form-control mb-md" required>
                                        <option selected disabled>--Choose Role--</option>
                                        <? foreach ($roles as $p) { ?>
                                            <option <?= selected($p['id'], $users['roleid']) ?>
                                                    value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                        <? } ?>
                        <? if (CS_MULTI_SYSTEM && !CS_MAIN_SYSTEM) { ?>
                            <div class="col-md-3">
                                <div class="col-md-12">
                                    <h5>User Code</h5>
                                </div>
                                <div class="col-md-12">
                                    <input type="text" placeholder="user code" class="form-control" data-toggle="tooltip"
                                           data-trigger="hover" data-original-title="Enter Email" name="users[code]"
                                           value="<?= $users['code'] ?: '' ?>">
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-4 branch">
                            <div class="col-md-12">
                                <div class="col-md-12 d-flex align-items-center">
                                    <h5>Branch</h5>
                                    <button type="button" class="btn btn-primary btn-xs ml-sm" title="quick add branch"
                                            onclick="quickAddBranch(this)">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <div class="col-md-12 new_branch mt-sm"
                                     style="padding:0;display:none ;">
                                    <div class="col-md-10"
                                         style="padding:0;position: relative">
                                        <div class="loading_spinner"
                                             style="position: absolute;top:5px;right: 10px;display:none ;">
                                            <object data="images/loading_spinner.svg"
                                                    type="image/svg+xml" height="30"
                                                    width="30"></object>
                                        </div>
                                        <input type="text" class="form-control"
                                               placeholder="branch name">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" onclick="saveBranch(this)"
                                                title="Quick add branch"
                                                class="btn btn-success btn-xs ml-sm">Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <select id="branch" name="users[branchid]" class="form-control mb-md" required onchange="fetchLocations()">
                                    <option value="" disabled>--Branch--</option>
                                    <? foreach ($branches as $p) {
                                        if ($p['id'] == $users['branchid']) { ?>
                                            <option selected value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                        <? }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12 d-flex align-items-center">
                                <h5>Location / Store</h5>
                                <button type="button" class="btn btn-primary btn-xs ml-md" title="refresh locations"
                                        onclick="fetchLocations()"><i
                                            class="fa fa-refresh"></i></button>
                            </div>
                            <div class="col-md-12">
                                <select id="location" class="form-control" required name="users[locationid]">
                                    <option value="" selected disabled>-- Choose Location --</option>
                                    <? foreach ($locations as $p) {
                                        if ($p['id'] == $users['locationid']) {
                                            ?>
                                            <option selected value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                        <? }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 department">
                            <div class="col-md-12">
                                <div class="col-md-12 d-flex align-items-center">
                                    <h5>Department</h5>
                                    <button type="button" class="btn btn-primary btn-xs ml-sm"
                                            title="quick add department"
                                            onclick="quickAddDepartment(this)">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                                <div class="col-md-12 new_department mt-sm"
                                     style="padding:0;display:none ;">
                                    <div class="col-md-10"
                                         style="padding:0;position: relative">
                                        <div class="loading_spinner"
                                             style="position: absolute;top:5px;right: 10px;display:none ;">
                                            <object data="images/loading_spinner.svg"
                                                    type="image/svg+xml" height="30"
                                                    width="30"></object>
                                        </div>
                                        <input type="text" class="form-control"
                                               placeholder="department name">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" onclick="saveDepartment(this)"
                                                title="Quick add department"
                                                class="btn btn-success btn-xs ml-sm">Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <select id="department" name="users[deptid]" class="form-control mb-md" required>
                                    <option value="" selected disabled>Choose department</option>
                                    <? foreach ($depts as $p) {
                                        if ($p['id'] == $users['deptid']) {
                                            ?>
                                            <option selected value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                        <? }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <h5>Hierarchy</h5>
                            </div>
                            <div class="col-md-12">
                                <select name="users[hierachicid]" class="form-control mb-md" required>
                                    <option selected disabled>--Choose Level--</option>
                                    <? foreach ($hierachicList as $p) { ?>
                                        <option <? if ($p['id'] == $users['hierachicid']) echo 'selected'; ?>
                                                value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-12">
                                <h5>Receipt Type</h5>
                            </div>
                            <div class="col-md-12">
                                <select class="form-control" required name="users[receipt_type]">
                                    <option selected disabled>--Choose Receipt type--</option>
                                    <? foreach ($reciepts as $key => $R) { ?>
                                        <option value="<?= $R['name'] ?>" <?= selected($users['receipt_type'], $R['name']) ?>><?= $R['lable'] ?>
                                            (<?= $R['name'] ?>)
                                        </option>
                                    <? } ?>
                                    <option value="both" <?= selected($users['receipt_type'], "both") ?>>Both</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" title="Default system receipt size">
                            <div class="col-md-12">
                                <h5>Default SR Size</h5>
                            </div>
                            <div class="col-md-12">
                                <select class="form-control" name="users[default_print_size]">
                                    <option value="">-- none --</option>
                                    <option value="A4" <?= selected($users['default_print_size'], "A4") ?>>A4</option>
                                    <option value="small" <?= selected($users['default_print_size'], "small") ?>>Small</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" title="Enables user to receive expire notifications">
                            <div class="col-md-12">
                                <h5>Receive expire Notifications</h5>
                            </div>
                            <div class="col-md-12">
                                <select name="users[expire_notification]" class="form-control mb-md" required>
                                    <option <?= selected($users['expire_notification'], 0) ?> value="0">No</option>
                                    <option <?= selected($users['expire_notification'], 1) ?> value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" title="Enables user to receive stock level notifications">
                            <div class="col-md-12">
                                <h5>Receive Stock Level Notifications</h5>
                            </div>
                            <div class="col-md-12">
                                <select name="users[stock_notification]" class="form-control mb-md" required>
                                    <option <?= selected($users['stock_notification'], 0) ?> value="0">No</option>
                                    <option <?= selected($users['stock_notification'], 1) ?> value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4" title="Enables user to receive supplier payments notifications">
                            <div class="col-md-12">
                                <h5>Receive Supplier Notifications</h5>
                            </div>
                            <div class="col-md-12">
                                <select name="users[supplier_notification]" class="form-control mb-md" required>
                                    <option <?= selected($users['supplier_notification'], 0) ?> value="0">No</option>
                                    <option <?= selected($users['supplier_notification'], 1) ?> value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <? if ($users['delete'] != 'no') { ?>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <h5>Status</h5>
                                </div>
                                <div class="col-md-12">
                                    <select class="form-control" required name="users[status]">
                                        <option value="active" <?= selected($users['status'], "active") ?>>Active</option>
                                        <option value="inactive" <?= selected($users['status'], "inactive") ?>>In-Active
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" title="invoice sale limit before required to be approved">
                                <div class="col-md-12">
                                    <h5>Sales Limit</h5>
                                </div>
                                <div class="col-md-12">
                                    <input type="text" name="users[sale_limit]" class="form-control text-weight-bold input-lg sale_limit"
                                           value="<?= $users['sale_limit'] ?>" placeholder="invoice sale limit">
                                </div>
                            </div>
                            <div class="col-md-4" title="if user put extra description during sale approval will be required">
                                <div class="col-md-12">
                                    <h5>Extra description approval</h5>
                                </div>
                                <div class="col-md-12">
                                    <select class="form-control" required name="users[extra_desc_approval]">
                                        <option value="0" <?= selected($users['extra_desc_approval'], 0) ?>>No</option>
                                        <option value="1" <?= selected($users['extra_desc_approval'], 1) ?>>Yes</option>
                                    </select>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <a href="?module=users&action=user_list"
                                   class="mb-xs mt-xs mr-xs btn btn-success btn-block">
                                    <i class="fa fa-list"></i>
                                    <span>Back to list</span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block">
                                    <i class="fa fa-save"></i>
                                    <span>Save User</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax("#branch", "?module=branches&action=getBranches&format=json", 'choose branch');
        initSelectAjax("#department", "?module=departments&action=getDepartments&format=json", 'choose department');
        thousands_separator('.sale_limit');
        $('#user-permission').select2();
    });

    function checkusername() {
        var username = $("#username").val();
        if (username == '' || username == null) {
            triggerError("Don't leave Blank..")
        } else {
            $.get("?module=users&action=checkusername&format=json&username=" + username, null, function (d) {
                var CC = eval(d);
                var status = CC[0].status;
                //console.log(status);
                if (status == 'found') {
                    //$('#showusername').html('Username is used by, '+CC[0].user);
                    triggerError('Username is used by someone else , ' + CC[0].user);
                    /*$('#form').on('submit', function(e){
                        e.preventDefault();
                    })*/
                } else {
                    triggerMessage('Your username is OK.');
                    //$('#form').submit();
                }
            });
        }
    }

    function fetchLocations() {
        let branchid = $('#branch').val();
        if (!branchid) {
            triggerError('Choose branch first');
            return false;
        }
        let locations = $('#location');
        $(locations).find('option:not(:first-child)').remove();
        $.get(`?module=locations&action=getBranchLocations&format=json&branchid=${branchid}`, null, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                $.each(result.data, function (i, item) {
                    let option = `<option value="${item.id}">${item.name}</option>`;
                    $(locations).append(option);
                });
            } else {
                triggerError('Error found');
            }
        });
    }
</script>
