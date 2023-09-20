<header class="page-header">
    <h2><? if ($edit) echo 'Edit';
        else echo 'Add'; ?> Branch</h2>
</header>

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a>
                </div>

                <h2 class="panel-title"><?= $_SESSION["pagetitle"] ?></h2>
            </header>
            <div class="panel-body">
                <?php if(is_null($status)){  ?>
                <button class="btn btn-success btn-sm" style="margin-bottom: 1rem; margin-left: 2rem;" id="addNewRow">New Row</button>
                <?php } ?>
                <form data-toggle="validator" role="form" action=<?= (!is_null($status))?url('mac_address', 'save_edit'):url('mac_address', 'save_mac_address'); ?> method="post">
                <?php if(!is_null($status)){  ?>
                    <input type="hidden" name="mac_address_id" value="<?php echo $mac_address["id"] ?>"
                <?php } ?>
                    <div class="form-wrap col-md-12">

                        <div class="form-group has-feedback col-md-6">
                            <label>Device Name</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                <input type="text" maxlength="255" class="form-control" value="<?= $mac_address['device_name'] ?>" name="device_name[]" placeholder="Eg. SAMSUNG" required>
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>

                        </div>
                        <div class="form-group has-feedback col-md-6">
                            <label>MAC-Address</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="fa fa-user"></span></span>
                                <input type="text" maxlength="255" class="form-control" name="mac_address[]" placeholder="Eg. MAC:80:900" value="<?= $mac_address['mac_address'] ?>">
                                <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                            </div>
                        </div>

                        <div id="forNewRow"></div>

                    </div>
                    <!-- </div> -->
                    <div class="form-group" style=" margin-top: 1rem; margin-left: 3rem;">
                            <button type="submit" class="btn btn-success btn-anim"><i class="icon-rocket"></i><span class="btn-text">submit</span></button>
                            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        </div>
                </form>
            </div>

            <script>
                const addNewRow = document.getElementById("addNewRow");

                addNewRow.addEventListener("click", createNewRow);

                function createNewRow() {

                    const forNewRow = document.getElementById("forNewRow");

                    forNewRow.innerHTML += '<div class="form-group has-feedback col-md-6"> <label>Device Name</label><div class="input-group"> <span class="input-group-addon"><span class="fa fa-user"></span></span> <input type="text" maxlength="255" class="form-control" name="device_name[]" placeholder="Eg. SAMSUNG" required> <span class="glyphicon form-control-feedback" aria-hidden="true"></span></div></div><div class="form-group has-feedback col-md-6"> <label>MAC-Address</label><div class="input-group"> <span class="input-group-addon"><span class="fa fa-user"></span></span> <input type="text" maxlength="255" class="form-control" name="mac_address[]" placeholder="Eg. MAC:80:900"> <span class="glyphicon form-control-feedback" aria-hidden="true"></span></div></div>'
                    eventListener();

                }
            </script>