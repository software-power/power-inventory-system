<link rel="stylesheet" href="assets/intl-tel/css/intlTelInput.css">
<style>
    .iti {
        width: 100%;
    }

    .border-danger {
        box-shadow: 0 0 5px red !important;
    }

    @media (min-width: 768px) {
        #quick-add-client-modal .modal-lg {
            width: 75% !important;
        }
    }
</style>
<div class="modal fade" id="quick-add-client-modal" role="dialog" aria-labelledby="quick-add-client-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Quick Add Client</h4>
            </div>
            <form action="<?= url('clients', 'quick_add') ?>" method="post" onsubmit="return validateModalInputs()">
                <input type="checkbox" name="for_support" class="for_support" style="display: none">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="d-flex">
                                    <h5>Client Name <span class="text-danger text-weight-bold text-lg">*</span></h5>
                                    <div id="clientname-spinner" style="display: none;">
                                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                                    </div>
                                </div>
                                <input id="name" autofocus title="Name is required" placeholder="Client Name" type="text"
                                       class="form-control"
                                       name="client[name]" value="<?= $_GET['newclient'] ?: $client['name'] ?>" required
                                    <?= $client['id'] ? "" : "onblur='verifyClient(this)'" ?>>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                       oninput="checkTIN(this)" onchange="verifyTIN()">
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
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
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
                                       value="<?= $client['mobile'] ?>" required>
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
                                       name="client[district]" value="<?= $client['district'] ?>">
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
                                        <option value="<?= $_SESSION['member']['id'] ?>"><?= $_SESSION['member']['name'] ?></option>
                                    </select>
                                <? } else { ?>
                                    <input type="hidden" name="client[acc_mng]" value="<?= $_SESSION['member']['id'] ?>">
                                    <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                                <? } ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-lg">
                        <div class="col-md-12 d-flex align-items-center">
                            <h5><strong>Contacts</strong></h5>
                            <button type="button" class="btn btn-default btn-sm ml-md" onclick="addClientContactRow()" title="add contact">
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
                    <div id="client-droparea">
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="forsave" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="assets/intl-tel/js/intlTelInput-jquery.js"></script>
<script>
    $(function () {
        $('.mobile-number').intlTelInput({
            preferredCountries: ['tz'],
            separateDialCode: true,
        });
        initSelectAjax('#account-manager', `?module=users&action=getUser&format=json`, 'Choose account manager');

        let MAIN_SYSTEM = `<?=CS_MAIN_SYSTEM?>`;

        $('#quick-add-client-modal').on('show.bs.modal', function (e) {

            let source = $(e.relatedTarget);
            let for_support = $(source).data('forsupport') == 1;
            let mainclientcode = $(source).data('mainclientcode') || '';

            if (MAIN_SYSTEM !== `1`) {
                getClientScreen(mainclientcode, for_support);
                return false;
            }

            let modal = $(this);

            $(modal).find('.for_support').prop('checked', for_support)
        });
    });

    function getClientScreen(mainclientcode, for_support = false) {

        $.get(`?module=systems&action=getMainSystemClientScreen&format=json${mainclientcode ? `&mainclientcode=${mainclientcode}` : ''}&${for_support ? 'for_support' : ''}`, {}, function (data) {
            let result = JSON.parse(data);
            // console.log(result);
            if (result.status === 'success') {
                let token = result.token;
                let width = window.screen.width;
                let height = window.screen.height;
                width = width ? width : 0;
                height = height ? height : 0;
                // console.log('width: ',width,'height:',height);
                let url = `<?=(isIpPublic()?(CS_MAIN_SYSTEM_PUBLIC_URL?:CS_MAIN_SYSTEM_URL):CS_MAIN_SYSTEM_URL) . SystemTokens::GET_CLIENT_SCREEN?>&access_token=${token}`;
                const clientWindow = window.open(url, "clientWindow", `location=yes,toolbar=no,menubar=no,width=${width},height=${height},left=-1000,top=-1000`);

                let timer = setInterval(function () {
                    if (clientWindow.closed) {
                        clearInterval(timer);
                        window.location.reload();
                    }
                }, 1000);
            } else {
                if (typeof triggerError !== 'undefined') {
                    triggerError(result.msg || 'error found', 5000);
                } else {
                    notifyError(result.msg || 'error found', 5000);
                }
            }
        });
    }

    function validateModalInputs() {
        let country_code = $('#mobile').intlTelInput('getSelectedCountryData').dialCode;
        $('#mobile_country_code').val(country_code ? country_code : '');
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
                    console.log(result);
                    if (result.status === 'success') {
                        submitbutn.prop('disabled', false);
                        if (typeof triggerMessage !== 'undefined') {
                            triggerMessage('TIN is okay');
                        } else {
                            notifyMessage('TIN is okay')
                        }
                        $('#tin-error').text('');
                        $('#tinNumber').removeClass("border-danger");
                    } else {
                        // submitbutn.prop('disabled', true);
                        if (typeof triggerError !== 'undefined') {
                            triggerError(result.msg || 'error found');
                        } else {
                            notifyError(result.msg || 'error found');
                        }
                        $('#tin-error').text(result.msg);
                        $('#tinNumber').addClass("border-danger");
                    }
                });
            }, 250);
        }

    }

    function addClientContactRow() {
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
        $('#client-droparea').append(contact);
    }

    function removeRow(obj) {
        $(obj).closest('.row').remove();
    }
</script>
