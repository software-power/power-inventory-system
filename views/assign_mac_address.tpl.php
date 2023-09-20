<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    span.select2-selection.select2-selection--single {
    height: fit-content;
}
    .center-panel {
        margin: 0 auto;
        width: 85%;
        padding: 10px;
    }

    .btn-client, .btn-serial {
        position: absolute;
    }

    .btn-client {
        right: 20%;
        top: 34%;
    }

    .btn-serial {
        right: 20%;
        top: 28%;
    }

    .panel-actions a, .panel-actions .panel-action {
        font-size: 21px;
    }

    .bt-row-remove {
        padding: 9px;
    }
</style>

<header class="page-header">
    <h2>Asset Assignment</h2>
</header>

<div class="row">
    <div class="col-lg-6">
        <section class="panel">

            <?php if (isset($_GET['page_title'])) { ?>
                <header class="panel-heading">
                <h2 class="panel-title">Selected Target Detail <span id="assetName"></span></h2>
            </header>
                <div class="panel-body">
                <table class=" table table-hover mb-none table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Target Name</th>
                        <th>Amount</th>
                        <!-- <th>Product Name</th> -->
                    </tr>
                    </thead>
                    <tbody id="attachUser">

                        <? $count = 1;?>

                            <tr id="user<?= $count ?>">
                                <td><?= $count ?></td>
                                <td><?= ucwords(base64_decode($_GET['target_name'])) ?></td>
                                <td><?= base64_decode($_GET['amount']) ?></td>
                                <!-- <td><?= base64_decode($_GET['product_name']) ?></td> -->
                            </tr>
                            <?php $count++; ?>
                    </tbody>
                </table>
            </div>
            <?php }else{ ?>
                            <header class="panel-heading">
                <h2 class="panel-title">Selected MAC-Address Detail <span id="assetName"></span></h2>
            </header>
                <div class="panel-body">
                <table class=" table table-hover mb-none table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Device Name</th>
                        <th>MAC-ADDRESS</th>
                    </tr>
                    </thead>
                    <tbody id="attachUser">

                        <? $count = 1;?>

                            <tr id="user<?= $count ?>">
                                <td><?= $count ?></td>
                                <td><?= ucwords($details['device_name']) ?></td>
                                <td><?= $details['mac_address'] ?></td>
                            </tr>
                            <?php $count++; ?>
                    </tbody>
                </table>
            </div>
                <?php } ?>

        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">

                <h2 class="panel-title">Assign to User</h2>

            </header>
            <div class="panel-body">

                <form id="form" class="form-horizontal form-bordered" method="post"
                      action="<?= url('mac_address', 'save_mac_address_assignment') ?>">

                    <? if ($_GET['id']) { ?>
                        <input type="hidden" name="assetid" value="<?= $_GET['id'] ?>"/>
                    <? }?>
                    <div class="col-md-12" id="cloneUser">
                        <div class="build-row-main">
                            <span width="100px">Staff Name</span>
                            <div width="50px">
                                <?php if (isset($_GET['page_title'])) { ?>
                                    <input type="hidden" name="targetId" value="<?= base64_decode( $_GET['id'] )?>"/>
                                    <input type="hidden" name="page_title" value="<?= base64_decode( $_GET['page_title'] )?>"/>
                               <?php }else{ ?>
                                <input type="hidden" name="mac_address_id" value="<?= $details['mac_address_id'] ?>"/>
                                <?php } ?>
                                <select style="height:fit-content; float: left" id="staffName" class="form-control" name="userid" required
                                        title="Choose User ">
                                    <option selected disabled>--Choose User --</option>
                                    <? foreach ($details['users'] as $r) { ?>
                                        <option
                                                value="<?= $r['id'] ?>"><?= $r['username'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4" style="margin-top: 2.5%">
                                <span style="height:fit-content; float:left; margin-top:  20px;" onclick="removeUserRow(this);"
                                 class="btn btn-success bt-row-remove"><i class="fa fa-minus"></i></span>
                    </div> -->

                    <!--Drop Area-->
                    <div id="dropArea" class="build-container" width="100%"></div>
                    <!--End-->

                    <div class="form-group">
                        <div class="col-md-12">
                            <!-- <div class="col-md-6">
                                <div onclick="addUserRow();" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i
                                            class="fa fa-plus"></i> Add User
                                </div>

                            </div> -->
                            <div class="col-md-12">
                                <button type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block">Save
                                    Transaction
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

           
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">   </script>
            <script>
                                    
                var assetId = $('#assetname_id').val();
                $(document).ready(function() {
                $('#assetname_id').select2();
                $('#staffName').select2();
                });
                
                function addUserRow() {
                    var appendRow = '<div class="col-md-8" id="cloneUser"><div class="build-row-main"><span width="100px">Staff Name</span><div width="50px"><input type="hidden" name="userid" value="<?= $_GET['id'] ?>"/><select style="/* width: 90%; */ float: left" id="staffName" class="form-control" name="user[]" required                          title="Choose User for Asset"><option selected disabled>--Choose User For Asset--</option><? foreach ($users as $r) { ?>
                                        <option <?= $r['roomid'], $roomassets ?>
                                                value="<?= $r['id'] ?>"><?= $r['name'] ?></option><? } ?>
                                </select></div></div></div><div class="col-md-4" style="margin-top: 2.5%"><span style="/* width: 10%; */ float:left margin-top:  20px;" onclick="removeUserRow(this);"class="btn btn-success bt-row-remove"><i class="fa fa-minus"></i></span></div>'

                    var cloneUser = $('#cloneUser').html();
                    $('#dropArea').append(appendRow);
                }

                function removeUserRow(obj) {
                    $(obj).closest(".build-row-main").remove();
                }

                function getAssetCurrentUser() {

                    var assetId = $('#assetname_id').val();
                    var forAttchUser = $('#attachUser');
                    $("#assetName").empty()

                    $.get("?module=assets&action=getAssetCurrentUser&format=json&assetid=" + assetId, null, function (users) {
                        var userslist = eval(users);
                        console.log(userslist);
                        forAttchUser.empty();

                        if (userslist[0].status == 'found') {
                            $.each(userslist[0].userlist, function (index, user) {
                                var count = index + 1;
                                var list = "<tr><td>" + count + "</td><td>" + user.username + "</td>" +
                                    "<td>" + user.assignedate + "</td><td>" + user.transactiontype + "</td>" +
                                    // "<td class='actions-hover actions-fade'><a href='?module=assets&action=branch_edit&id="+user.userassetid+"' title='Asset Re-assign'>"+
                                    // "<i class='fa fa-retweet'></i></a></td>" +
                                    "</tr>";

                                forAttchUser.append(list);
                            });
                            $("#assetName").text($('#assetname_id option:selected').text())
                            triggerMessage('All users are populated, successfully');
                        } else {
                            forAttchUser.append('<tr><td class="text-center" colspan="4">No Assignment Found.</td></tr>')
                            triggerError('No user for this asset');
                        }


                    });
                }
            </script>
