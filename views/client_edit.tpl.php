<link rel="stylesheet" href="assets/intl-tel/css/intlTelInput.css">
<style media="screen">
    .border-danger {
        box-shadow: 0 0 5px red !important;
    }

    .num-hide {
        display: block;
        background: white;
        width: 32px;
        height: 28px;
        position: absolute;
        right: 21px;
        top: 40px;
    }
</style>
<header class="page-header">
    <h2><? if ($edit) echo 'Edit'; else echo 'Add'; ?> Client</h2>
</header>

<div class="row d-flex justify-content-center">
    <? if (!$_SESSION['REMOTE_ACCESS']){ ?>
    <div class="col-md-10 col-lg-8">
        <? }else{ ?>
        <div class="col-md-12">
            <? } ?>
            <section class="panel">
                <header class="panel-heading">
                    <h2 class="panel-title"><span
                                class="text-primary"><?= $_SESSION['REMOTE_ACCESS'] ? 'Main System' : '' ?>:</span> <?= $client['id'] ? 'Edit' : 'Add' ?>
                        Client</h2>
                </header>
                <div class="panel-body">
                    <form id="form" class="form-horizontal form-bordered" method="post"
                          action="<?= url('clients', 'client_save') ?>" onsubmit="validateInputs()">
                        <input id="clientid" type="hidden" name="client[id]" value="<?= $client['id'] ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <h5>Client Name</h5>
                                        <div id="clientname-spinner" style="display: none;">
                                            <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                                        </div>
                                    </div>
                                    <? $CAN_EDIT_NAME = Users::can(OtherRights::add_client) || Users::can(OtherRights::edit_client) ?>
                                    <input id="name" <?= $CAN_EDIT_NAME ? 'name="client[name]"' : 'readonly' ?> autofocus title="Name is required"
                                           placeholder="Client Name" type="text" class="form-control"
                                           value="<?= $_GET['newclient'] ?: $client['name'] ?>" required
                                        <?= $client['id'] ? "" : "onblur='verifyClient(this)'" ?>>
                                </div>
                                <? if (CS_TALLY_TRANSFER && CS_DIFF_CLIENT_LEDGERNAME) { ?>
                                    <div class="col-md-6">
                                        <h5>Tally Name</h5>
                                        <input id="name" <?= $CAN_EDIT_NAME ? 'name="client[tally_name]"' : 'readonly' ?> autofocus
                                               title="Tally name is required"
                                               placeholder="Tally Name" type="text" class="form-control"
                                               value="<?= $_GET['newclient'] ?: $client['tally_name'] ?>" required>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                        <? if ($client['id'] != 1) { ?>
                            <div class="row mt-sm">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div class="d-flex">
                                            <h5>TIN No.</h5>
                                            <div id="tin-spinner" style="display: none;">
                                                <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                                            </div>
                                        </div>
                                        <input id="tinNumber" placeholder="TIN number" type="number" class="form-control" autocomplete="off"
                                               name="client[tinno]" value="<?= $client['tinno'] ?? '' ?>"
                                               oninput="checkTIN(this)">
                                        <small id="tin-error" class="text-danger"></small>
                                    </div>
                                    <div class="col-md-4">
                                        <h5>VRN/VAT No.</h5>
                                        <input id="vatNumber" placeholder="VRN/VAT number" type="text" class="form-control" autocomplete="off"
                                               name="client[vatno]" value="<?= $client['vatno'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <? if (Users::can(OtherRights::make_client_reseller)) { ?>
                                            <h5>Registered Reseller</h5>
                                            <select name="client[reseller]" class="form-control">
                                                <option <?= selected($client['reseller'], 0) ?> value="0">No</option>
                                                <option <?= selected($client['reseller'], 1) ?> value="1">Yes</option>
                                            </select>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <h5>Mobile</h5>
                                        <span class="num-hide"></span>
                                        <input id="mobile" placeholder="Mobile number" type="tel" class="form-control mobile-number"
                                               name="client[mobile]"
                                               value="<?= ($client['mobile_country_code'] ? "+" . $client['mobile_country_code'] : "") . $client['mobile'] ?>"
                                               required>
                                        <input id="mobile_country_code" type="hidden" name="client[mobile_country_code]">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Tel</h5>
                                        <input id="tel" placeholder="Telephone number" type="text" class="form-control"
                                               name="client[tel]" value="<?= $client['tel'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Email</h5>
                                        <input id="email" placeholder="Email" type="text" class="form-control"
                                               name="client[email]" value="<?= $client['email'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <h5>Address</h5>
                                        <input id="address" placeholder="Address" type="text" class="form-control"
                                               name="client[address]" value="<?= $client['address'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Plot No.</h5>
                                        <input id="plotno" placeholder="Plot number" type="text" class="form-control"
                                               name="client[plotno]" value="<?= $client['plotno'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Status</h5>
                                        <select name="client[status]" class="form-control">
                                            <!-- <option selected disabled>-Choose Status-</option> -->
                                            <option selected value="active" <?= selected($client['status'], 'active') ?>>
                                                Active
                                            </option>
                                            <option value="inactive" <?= selected($client['status'], 'inactive') ?>>
                                                In-Active
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <h5>City</h5>
                                        <input id="city" placeholder="City" type="text" class="form-control"
                                               name="client[city]" value="<?= $client['city'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>District</h5>
                                        <input id="district" placeholder="District" type="text" class="form-control"
                                               name="client[district]" value="<?= htmlspecialchars($client['district']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h5>Street</h5>
                                        <input id="street" placeholder="Street" type="text" class="form-control"
                                               name="client[street]" value="<?= $client['street'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <h5>Account Manager</h5>
                                        <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                            <select id="account-manager" name="client[acc_mng]" class="form-control">
                                                <? if ($client) { ?>
                                                    <option value="<?= $client['acc_mng'] ?>"><?= $client['account_manager'] ?></option>
                                                <? } else { ?>
                                                    <option value="<?= $_SESSION['member']['id'] ?>"><?= $_SESSION['member']['name'] ?></option>
                                                <? } ?>
                                            </select>
                                        <? } else { ?>
                                            <? if ($client) { ?>
                                                <input type="hidden" name="client[acc_mng]" value="<?= $client['acc_mng'] ?>">
                                                <input type="text" readonly class="form-control" value="<?= $client['account_manager'] ?>">
                                            <? } else { ?>
                                                <input type="hidden" name="client[acc_mng]" value="<?= $_SESSION['member']['id'] ?>">
                                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                                            <? } ?>
                                        <? } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-md">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <h5>Credit Limit (<?= Currencies::$currencyClass->find(['base' => 'Yes'])[0]['name'] ?>)</h5>
                                        <input id="credit-limit" <?= $CAN_EDIT_NAME ? 'name="client[credit_limit]"' : 'readonly' ?>
                                               placeholder="Credit Limit" type="text" class="form-control text-weight-bold input-lg"
                                               value="<?= $client['credit_limit'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-lg">
                                <div class="col-md-12 d-flex align-items-center">
                                    <h5><strong>Contacts</strong></h5>
                                    <button type="button" class="btn btn-default btn-sm ml-md" onclick="addRow()" title="add contact">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Full name</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>Email</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>Mobile</h5>
                                </div>
                                <div class="col-md-2">
                                    <h5>Position</h5>
                                </div>
                                <div class="col-md-1">

                                </div>
                            </div>
                            <div id="droparea">
                                <? foreach ($contacts as $c) { ?>
                                    <div class="row mb-sm">
                                        <div class="col-md-3">
                                            <input placeholder="Full name" type="text" class="form-control"
                                                   name="fullname[]" required value="<?= $c['name'] ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <input placeholder="Email" type="text" class="form-control" name="email[]" value="<?= $c['email'] ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <input placeholder="Mobile" type="text" class="form-control" name="mobile[]" value="<?= $c['mobile'] ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <input placeholder="Position" type="text" class="form-control" name="position[]"
                                                   value="<?= $c['position'] ?>">
                                        </div>
                                        <div class="col-md-1">
                                            <div class="btn-row btn btn-secondary" onclick='removeRow(this);'><i
                                                        class="fa fa-minus"></i></div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        <? } else { ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-danger">Limited info for cash client</h5>
                                </div>
                            </div>
                        <? } ?>
                        <div class="form-group">
                            <div class="col-md-12 d-flex justify-content-center">
                                <? if (!$_SESSION['REMOTE_ACCESS']) { ?>
                                    <div class="col-md-6">
                                        <a href="?module=clients&action=client_index"
                                           class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i class="fa fa-list"></i> Back
                                            to List</a>
                                    </div>
                                <? } ?>
                                <div class="col-md-6">
                                    <button id="forsave" type="submit" class="mb-xs mt-xs mr-xs btn btn-primary btn-block">
                                        <i class="fa fa-save"></i> Save Client
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade" id="related-client-modal" role="dialog" aria-labelledby="related-client-modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Related Client Names</h4>
                </div>
                <div class="modal-body" style="max-height: 50vh;overflow: hidden;overflow-y: auto;">
                    <table class="table" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="tbody">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/quick_adds.js"></script>
    <script src="assets/intl-tel/js/intlTelInput-jquery.js"></script>
    <script>
        $(function () {
            $('.mobile-number').intlTelInput({
                preferredCountries: ['tz'],
                separateDialCode: true,
            });
            initSelectAjax('#account-manager', `?module=users&action=getUser&format=json`, 'Choose account manager');
            thousands_separator('#credit-limit');

            <?if($_SESSION['REMOTE_ACCESS'] && !empty($_SESSION['message'])){
                unset($_SESSION['member']);
                session_destroy();
            ?>
            setTimeout(function () {
                window.close();
            },3000);
            <?}?>
        });

        function validateInputs() {
            let country_code = $('#mobile').intlTelInput('getSelectedCountryData').dialCode;
            $('#mobile_country_code').val(country_code ? country_code : '');
        }

        function verifyClient(obj) {
            let clientname = $(obj).val();
            let spinner = $('#clientname-spinner');
            let relatedClientModal = $('#related-client-modal');
            $(relatedClientModal).find('tbody.tbody').empty();
            if (clientname.length >= 3) {
                spinner.show();
                $.get("?module=clients&action=getClients&format=json&clientname=" + clientname, null, function (data) {
                    spinner.hide();
                    let client = JSON.parse(data);
                    if (client[0].test === 'No results') {
                        triggerMessage('No client use this name');
                    } else {
                        $(relatedClientModal).modal('show');
                        $.each(client, function (index, det) {
                            let i = index + 1;

                            let tbl_row = `<tr>
                                            <td style='width:80px;'>${i}</td> +
                                            <td>${det.text}</td> +
                                            <td>
                                                <a class='btn btn-default btn-sm' title='client edit' href='?id=${det.id}&module=clients&action=client_edit'>
                                                    <i class='fa fa-pencil'></i> edit</a>
                                            </td>
                                        </tr>`;

                            setTimeout(function () {
                                $(relatedClientModal).find('tbody.tbody').append(tbl_row);
                            }, 10 * index)
                        });
                    }
                });
            }
        }

        function checkTIN(obj) {
            let submitbutn = $('#forsave');
            let tin = $(obj).val();
            $(obj).removeClass("border-danger");
            $(submitbutn).removeClass("disabled");


            if (tin.length > 0) {
                if (tin.length !== 9) {
                    $(obj).addClass("border-danger");
                    $(submitbutn).prop("disabled", true);
                    $('#tin-error').text('TIN Should be Nine (9) Characters');
                } else if (tin.includes("-")) {
                    $(submitbutn).prop("disabled", true);

                    $(obj).addClass("border-danger");
                    $('#tin-error').text('TIN Should Not Contain " - "');
                } else {
                    $('#tin-error').text('');
                    $(obj).removeClass("border-danger");
                    $(submitbutn).prop("disabled", false);
                }
            } else {
                $('#tin-error').text('');
                $(obj).removeClass("border-danger");
                $(submitbutn).prop("disabled", false);
            }

        }

        let tinTimer = null;

        function verifyTIN() {
            let tin = $('#tinNumber').val();
            let clientid = $('#clientid').val();
            let spinner = $('#tin-spinner');
            let submitbutn = $('#forsave');
            if (tinTimer) clearTimeout(tinTimer);
            spinner.hide();
            submitbutn.prop('disabled', false);

            if (tin.length === 9) {
                spinner.show();
                // submitbutn.prop('disabled', true);
                tinTimer = setTimeout(function () {
                    $.get(`?module=clients&action=checkTIN&format=json&tin=${tin}&clientid=${clientid}`, null, function (data) {
                        spinner.hide();
                        let result = JSON.parse(data);
                        // console.log(result);
                        if (result.status === 'success') {
                            submitbutn.prop('disabled', false);
                            triggerMessage('TIN is okay');
                            $('#tin-error').text('');
                            $('#tinNumber').removeClass("border-danger");
                        } else {
                            // submitbutn.prop('disabled', true);
                            triggerError(result.msg || 'error found');
                            $('#tin-error').text(result.msg);
                            $('#tinNumber').addClass("border-danger");
                        }
                    });
                }, 250);
            }

        }

        function addRow() {
            let contact = `<div class="row mb-sm">
                            <div class="col-md-3">
                                <input placeholder="Full name" type="text" class="form-control"
                                       name="fullname[]" required>
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Email" type="text" class="form-control" name="email[]">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Mobile" type="text" class="form-control" name="mobile[]">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Position" type="text" class="form-control" name="position[]">
                            </div>
                            <div class="col-md-1">
                                <div class="btn-row btn btn-secondary" onclick='removeRow(this);'><i
                                            class="fa fa-minus"></i></div>
                            </div>
                        </div>`;

            // console.log(scontact);
            $('#droparea').append(contact);
        }

        function removeRow(obj) {
            $(obj).closest('.row').remove();
        }
    </script>
