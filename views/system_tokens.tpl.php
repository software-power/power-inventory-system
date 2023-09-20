<header class="page-header">
    <h2>Sub Systems</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between">
                <h2 class="panel-title">System Tokens</h2>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-sm">
                            <? if (CS_MAIN_SYSTEM) { ?>
                            <span class="text-weight-semibold">Main System Token:</span>
                            <? } else { ?>
                            <span class="text-weight-semibold">System Token:</span>
                            <?}?>
                            <button type="button" class="btn btn-default btn-sm ml-xs" title="copy master token" onclick="copySystemToken()">
                                <i class="fa fa-copy"></i> Copy Token
                            </button>
                        </div>
                        <textarea id="system-token" class="form-control input-sm" rows="3" readonly><?= CS_SYSTEM_TOKEN ?></textarea>
                    </div>
                    <div class="col-md-4 pt-lg d-flex align-items-center">
                        <div id="generate_loading_spinner" style="display: none">
                            <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm ml-xs" title="generate master token" onclick="generateMasterToken()">
                            <i class="fa fa-cogs"></i> Generate Token
                        </button>

                    </div>
                </div>
                <? if (CS_MAIN_SYSTEM) { ?>
                    <form action="<?= url('home', 'save_system_tokens') ?>" method="post">
                        <table id="sub-systems" class="table table-hover table-condensed mb-none" style="font-size: 10pt">
                            <thead>
                            <tr>
                                <th style="width: 50px;">No.</th>
                                <th>Name</th>
                                <th>End point</th>
                                <th style="width: 35%">Token</th>
                                <th>Status</th>
                                <th class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-default btn-sm" onclick="addItem()"><i class="fa fa-plus"></i> Add</button>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="tbody">
                            <? $count = 1;
                            foreach ($system_tokens as $st) { ?>
                                <tr>
                                    <td class="counter"><?= $count++ ?></td>
                                    <td>
                                        <input type="hidden" class="inputs subid" name="subid[]" value="<?= $st['id'] ?>" disabled>
                                        <input type="hidden" class="inputs do" name="do[]" disabled>
                                        <input type="text" class="form-control inputs name" placeholder="Name" name="name[]"
                                               value="<?= $st['name'] ?>"
                                               required disabled>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control inputs endpoint" placeholder="End point eg. http://123.123.123.123:80"
                                               name="endpoint[]" value="<?= $st['endpoint'] ?>" required disabled>
                                    </td>
                                    <td>
                                        <textarea rows="5" class="form-control input-sm inputs token" name="token[]" placeholder="token"
                                                  required disabled><?= $st['token'] ?></textarea>
                                    </td>
                                    <td>
                                        <span class="status"><?= ucfirst($st['status']) ?></span>
                                    </td>
                                    <td class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-success btn-sm mr-xs" onclick="testConnection(this)"
                                                title="Test Connection"><i class="fa fa-refresh"></i></button>
                                        <button type="button" class="btn btn-primary btn-sm mr-xs edit-btn" onclick="enableInputs(this)"
                                                title="Edit"><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="removeItem(this)" title="remove"><i
                                                    class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                        <div class="row d-flex justify-content-end mt-lg">
                            <div class="col-md-2">
                                <button class="btn btn-success btn-block">Save</button>
                            </div>
                        </div>
                    </form>
                <? } else { ?>
                    <form action="<?= url('home', 'save_main_system_info') ?>" method="post">
                        <div class="row d-flex justify-content-center mt-lg">
                            <div class="col-md-6">
                                <span>Main System Url</span>
                                <input id="main-system-url" type="text" name="main_url" class="form-control" placeholder="main system url"
                                       value="<?= CS_MAIN_SYSTEM_URL ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-default mt-lg" onclick="testMainConnection(this)"><i class="fa fa-refresh"></i> Test
                                    Connection
                                </button>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center mt-lg">
                            <div class="col-md-6">
                                <span>Main System Public Url</span>
                                <input id="main-system-public-url" type="text" name="main_public_url" class="form-control" placeholder="main system public url"
                                       value="<?= CS_MAIN_SYSTEM_PUBLIC_URL ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-default mt-lg" onclick="testMainConnection(this)"><i class="fa fa-refresh"></i> Test
                                    Connection
                                </button>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-center mt-lg">
                            <div class="col-md-6">
                                <span>Main System Token <small class="text-danger">Token from main system</small></span>
                                <textarea id="main-system-token" name="token" class="form-control input-sm" rows="3"
                                          required><?= CS_MAIN_SYSTEM_TOKEN ?></textarea>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <div class="row mt-md d-flex justify-content-center">
                            <div class="col-md-3">
                                <button class="btn btn-success btn-block ml-xs">Save</button>
                            </div>
                        </div>
                    </form>
                <? } ?>
            </div>
        </section>
    </div>
</div>

<script>
    function generateMasterToken() {
        let token = $('#system-token').val();
        if (token.length > 0) {
            let answer = confirm(`Do you want to regenerate system token?\nThis will impact all other systems`);
            if (!answer) return;
        }
        let spinner = $('#generate_loading_spinner');
        spinner.show();
        $.post(`?module=home&action=generateSystemToken&format=json`, {request: 'generate'}, function (data) {
            spinner.hide();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                $('#system-token').val(result.data);
                triggerMessage('Token Generated successfully', 5000);
            } else {
                triggerError(result.msg || 'error found', 5000);
            }
        });
    }

    function copySystemToken() {
        $('#system-token').focus().select();
        try {
            let successful = document.execCommand('copy');
            let msg = successful ? 'successful' : 'unsuccessful';
            successful ? triggerMessage('Copied successfully') : triggerError('Failed copying');
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err);
        }
        document.getSelection().empty();
        $('#system-token').blur();
    }

    function addItem() {
        let row = `<tr>
                       <td class="counter">1</td>
                       <td>
                           <input type="hidden" class="inputs subid" name="subid[]">
                           <input type="hidden" class="inputs do" name="do[]" value="new">
                           <input type="text" class="form-control inputs name" placeholder="Name" name="name[]" required>
                       </td>
                       <td>
                           <input type="text" class="form-control inputs endpoint" placeholder="End point eg. http://123.123.123.123:80"
                                  name="endpoint[]" required>
                       </td>
                       <td>
                           <textarea rows="5" class="form-control input-sm inputs token" name="token[]" placeholder="token" required></textarea>
                       </td>
                       <td>
                           <span class="status"></span>
                       </td>
                       <td class="d-flex justify-content-end">
                           <button type="button" class="btn btn-success btn-sm mr-xs" onclick="testConnection(this)" title="Test Connection">
                               <i class="fa fa-refresh"></i></button>
                           <button type="button" class="btn btn-warning btn-sm" onclick="removeItem(this)" title="remove"><i
                                       class="fa fa-trash"></i></button>
                       </td>
                   </tr>`;
        $('#sub-systems tbody.tbody').append(row);
        count_item('#sub-systems tbody.tbody');
    }

    function enableInputs(obj) {
        let tr = $(obj).closest('tr');
        $(tr).find('.inputs').prop('disabled', false);
        $(tr).find('.status').text('active');
    }

    function removeItem(obj) {
        let tr = $(obj).closest('tr');
        if ($(tr).find('.subid').val().length > 0) {
            $(tr).find('.do').val('delete');
            $(tr).find('.status').text('inactive');
            $(tr).find('.edit-btn').hide();
            $(tr).find('.inputs').prop('disabled', false).prop('readonly', true);
        } else {
            $(tr).remove();
        }
        count_item('#sub-systems tbody.tbody');
    }

    function count_item(group_parent) {
        let count = 1;
        $(group_parent).find('.counter').each(function (i, item) {
            $(item).text(count++);
        });
    }

    function testConnection(obj) {
        let tr = $(obj).closest('tr');
        let sub_url = $(tr).find('.endpoint').val();
        let sub_token = $(tr).find('.token').val();

        $.post(`?module=home&action=testSubSystemConnection&format=json`, {
            sub_url: sub_url,
            sub_token: sub_token
        }, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                triggerMessage(result.msg,5000);
            } else {
                triggerError(result.msg || 'error',5000);
            }
        });
    }

    function testMainConnection(btn) {
        let testing_url = $(btn).closest('.row').find('input').val();
        if(testing_url.length===0){
            triggerError('Enter url');
            $(btn).closest('.row').find('input').focus();
            return;
        }

        let main_system_token = $('#main-system-token').val();
        if(main_system_token.length===0){
            triggerError('Enter token');
            $('#main-system-token').focus();
            return;
        }

        $.post(`?module=home&action=testMainSystemConnection&format=json`, {
            url: testing_url,
            main: main_system_token
        }, function (data) {
            let result = JSON.parse(data);
            if (result.status === 'success') {
                triggerMessage(result.msg,5000);
            } else {
                triggerError(result.msg || 'error',5000);
            }
        });
    }
</script>
