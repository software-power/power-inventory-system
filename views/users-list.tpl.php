<style media="screen">
    .panel-heading {
        height: 79px;
    }

    .nameCharacter {
        font-size: 41px;
        display: block;
        padding: 25px;
        text-transform: capitalize;
        text-align: center;
        border-radius: 100%;
        background: #ecedf0;
        height: 77px;
        width: 77px;
    }

    .users .col-md-4 {
        margin-top: 10px;
    }

    .box-wrapper {
        width: 100%;
        padding: 10px;
        height: 120px;
        background-color: #ffffff;
        /* -webkit-box-shadow: 0px 0px 5px 1px rgba(0,0,0,0.16);
    -moz-box-shadow: 0px 0px 5px 1px rgba(0,0,0,0.16);
    box-shadow: 0px 0px 5px 1px rgba(0,0,0,0.16); */
    }

    .box-wrapper h4 {
        margin: 0;
        font-weight: 600;
    }

    .box-wrapper span {
        display: block;
    }

    .box-wrapper span a {
        font-size: 16px;
        width: 19px;
        display: inline-block;
    }

    .box-wrapper .img-circle {
        border: 4px solid #ecedf0;
    }
</style>
<header class="page-header">
    <h2>Users</h2>
</header>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Users</h2>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end align-items-center">
                        <select name="status" onchange="window.location.replace(`?module=users&action=user_list&user_status=${this.value}`)"
                                class="mr-md">
                            <option value="" <?= selected($_GET['user_status'], "") ?>>All User</option>
                            <option value="active" <?= selected($_GET['user_status'], 'active') ?>>Active</option>
                            <option value="inactive" <?= selected($_GET['user_status'], 'inactive') ?>>Inactive</option>
                        </select>
                        <? if (Users::can(OtherRights::add_user) && (isset(LICENSE_MODULES['usr']) && LICENSE_MODULES['usr'] > 0 && LICENSE_MODULES['usr'] > $user_count)) { ?>
                            <a href="?module=users&action=users_add" class="btn btn-default btn-sm mr-md">
                                <i class="fa fa-user-plus"></i> Add user
                            </a>
                        <? } ?>
                    </div>
                </div>
                <?
                if (isset(LICENSE_MODULES['usr']) && LICENSE_MODULES['usr'] > 0 && LICENSE_MODULES['usr'] < 500) {
                    $user_limit_count = LICENSE_MODULES['usr'];
                } else {
                    $user_limit_count = "No limit";
                }
                ?>
            </header>
            <div class="panel-body">
                <p>License User limit: <span class="text-danger text-weight-bold"><?= $user_limit_count ?></span></p>
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>User code</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Branch</th>
                            <th>Location</th>
                            <th>Department</th>
                            <th>Hierarchic</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        $USER_CAN_EDIT = Users::can(OtherRights::edit_user);
                        $USER_CAN_RESET_PASSWORD = Users::can(OtherRights::reset_password);
                        foreach ($check as $id => $R) { ?>
                            <tr>
                                <td width="80px"><?= $count ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= profileImg($R['image']) ?>" alt=""
                                             class="img-circle rounded" width="40">
                                        <div class="ml-sm">
                                            <div><?= $R['name'] ?></div>
                                            <? if ($R['delete'] == 'no') { ?>
                                                <i class="text-danger text-xs">Super user</i>
                                            <? } ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $R['id'] ?></td>
                                <td><?= $R['username'] ?></td>
                                <td><?= $R['rolename'] ?></td>
                                <td>
                                    <span class="badge <?= $R['status'] == 'active' ? 'bg-success' : 'bg-danger' ?>"><?= $R['status'] ?></span>
                                </td>
                                <td><?= $R['branchname'] ?></td>
                                <td><?= $R['locationname'] ?></td>
                                <td><?= $R['departmentname'] ?></td>
                                <td><?= $R['hierarchicname'] ?></td>
                                <td><?= fDate($R['doc']) ?></td>
                                <td><?= $R['createdby'] ?></td>
                                <td>
                                    <div class="btn-group dropleft">
                                        <button type="button" class="btn btn-secondary dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-list"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <? if ($USER_CAN_EDIT) { ?>
                                                <a class="dropdown-item"
                                                   href="<?= url('users', 'users_edit', 'id=' . $R['id']) ?>">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                            <? } ?>
                                            <? if ($R['delete'] != 'no' && $USER_CAN_RESET_PASSWORD) { ?>
                                                <form action="<?= url('users', 'users_reset_password') ?>"
                                                      method="post" style="margin:0;"
                                                      onsubmit="return confirm('Do you want to reset this user password?')">
                                                    <input type="hidden" name="id" value="<?= $R['id'] ?>">
                                                    <button class="dropdown-item"><i class="fa fa-lock"></i>
                                                        Reset Password
                                                    </button>
                                                </form>
                                            <? } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>
