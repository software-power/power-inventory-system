<header class="page-header">
    <h2>User Roles</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-6">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">List of Roles</h2>
                <a href="<?=url('roles', 'add_role')?>" class="btn btn-sm btn-success" data-toggle="modal">Add Role</a>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>User count</th>
                            <th>Created by</th>
                            <th>Created on</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($roles as $id => $R) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $R['name'] ?></td>
                                <td class="text-center"><?= $R['usercount'] ?></td>
                                <td><?= $R['creator'] ?></td>
                                <td><?= fDate($R['doc'],'d M Y H:i') ?></td>
                                <td class="text-capitalize"><?= $R['status'] ?></td>
                                <td>
                                    <? if ($R['id'] != 1) { ?>
                                        <a class="btn btn-default btn-sm" title="Edit role" href="<?=url('roles', 'add_role',['roleid'=>$R['id']])?>">
                                            <i class="fa fa-pencil"></i> Edit</a>
                                    <? } ?>
                                    <? if ($R['usercount']==0) { ?>
                                        <a class="btn btn-danger btn-sm" title="delete role" href="<?=url('roles', 'delete_role',['roleid'=>$R['id']])?>"
                                        onclick="return confirm('Do you want to delete this role?')">
                                            <i class="fa fa-trash"></i> Delete</a>
                                    <? } ?>
                                </td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
