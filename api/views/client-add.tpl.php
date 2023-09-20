<html>
<!doctype html>
<head>
    <script src="../assets/vendor/jquery/jquery.js"></script>
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" href="../assets/vendor/select2/select2.css"/>
    <link rel="stylesheet" href="../assets/vendor/pnotify/pnotify.custom.css"/>
    <link rel="stylesheet" href="../assets/css/notification-style.css"/>
    <script src="../assets/vendor/select2/select2.full.js"></script>
    <link rel="stylesheet" href="../assets/intl-tel/css/intlTelInput.css">
    <link rel="stylesheet" href="../assets/stylesheets/theme.css">
    <link rel="stylesheet" href="../assets/css/custom.css"/>
    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Client</title>

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <style media="screen">
        body {
            background-color: #ecedf0;
        }

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
</head>
<body>


<div class="row d-flex justify-content-center" style="margin-top: 20px">
    <div class="col-sm-12 col-md-11">
        <section class="panel">
            <header class="panel-heading d-flex justify-content-between align-items-center">
                <h2 class="panel-title"><?= $client['id'] ? 'Edit' : 'Add' ?> Client Screen</h2>
                <div class="d-flex align-items-center" style="cursor: pointer" onclick="window.close()">
                    <i class="fa fa-close fa-3x text-danger"></i>
                    <span class="text-lg ml-xs">Close</span>
                </div>
            </header>
            <div class="panel-body">
                <div class="row d-flex">
                    <? if (!isset($for_support)) { ?>
                        <div class="col-md-3" style="<?= $client['id'] ? '' : 'display:none' ?>">
                            <input id="clientid" type="hidden" class="form-control text-weight-semibold text-danger"
                                   value="<?= $client['id'] ?>" readonly>
                        </div>
                    <? } else { ?>
                        <input id="clientid" type="hidden" value="<?= $client['id'] ?>" readonly>
                        <input type="hidden" class="for_support">
                        <span class="text-danger text-weight-semibold">Client will not be sent to support, You will be required to copy the code from client list</span>
                    <? } ?>
                    <div class="col-md-4 d-flex justify-content-center">
                        <div id="search-spinner" style="display:none ">
                            <div class="d-flex align-items-center">
                                <span class="spinner-border text-danger" style="height: 30px;width: 30px;"></span>
                                <span class="ml-sm">Searching..</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-sm">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h5>Client Name</h5>
                            <input type="text" class="form-control clientname" placeholder="client name" value="<?= $client['name'] ?>"
                                   data-source="name" onblur="search_client(this)">
                        </div>
                        <div class="col-md-4">
                            <h5>TIN No.</h5>
                            <input id="tinNumber" placeholder="TIN number" type="number" class="form-control tinno" autocomplete="off"
                                   data-source="tinno"
                                   value="<?= $client['tinno'] ?? '' ?>" oninput="checkTIN(this)" onblur="search_client(this)">
                            <small id="tin-error" class="text-danger"></small>
                        </div>
                        <div class="col-md-4">
                            <h5>VRN/VAT No.</h5>
                            <input id="vatNumber" placeholder="VRN/VAT number" type="text" class="form-control vatno" data-source="vatno"
                                   autocomplete="off" value="<?= $client['vatno'] ?>" onblur="search_client(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h5>Mobile</h5>
                            <span class="num-hide"></span>
                            <input id="mobile" placeholder="Mobile number" type="tel" class="form-control mobile-number"
                                   value="<?= ($client['mobile_country_code'] ? "+" . $client['mobile_country_code'] : "") . $client['mobile'] ?>"
                            >
                            <input id="mobile_country_code" type="hidden">
                        </div>
                        <div class="col-md-4">
                            <h5>Tel</h5>
                            <input id="tel" placeholder="Telephone number" type="text" class="form-control tel"
                                   value="<?= $client['tel'] ?>">
                        </div>
                        <div class="col-md-4">
                            <h5>Email</h5>
                            <input id="email" placeholder="Email" type="text" class="form-control email"
                                   value="<?= $client['email'] ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h5>Address</h5>
                            <input id="address" placeholder="Address" type="text" class="form-control address"
                                   value="<?= $client['address'] ?>">
                        </div>
                        <div class="col-md-4">
                            <h5>Plot No.</h5>
                            <input id="plotno" placeholder="Plot number" type="text" class="form-control plotno"
                                   value="<?= $client['plotno'] ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <h5>City</h5>
                            <input id="city" placeholder="City" type="text" class="form-control city"
                                   value="<?= $client['city'] ?>">
                        </div>
                        <div class="col-md-4">
                            <h5>District</h5>
                            <input id="district" placeholder="District" type="text" class="form-control district"
                                   value="<?= $client['district'] ?>">
                        </div>
                        <div class="col-md-4">
                            <h5>Street</h5>
                            <input id="street" placeholder="Street" type="text" class="form-control street"
                                   value="<?= $client['street'] ?>">
                        </div>
                    </div>
                </div>
                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <h5>Account Manager</h5>
                                <span class="text-danger">Changing Account Manager Use Main System</span>
                            </div>
                        </div>
                    </div>
                <? } ?>
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
                        <div class="row mb-sm contact-row">
                            <div class="col-md-3">
                                <input placeholder="Full name" type="text" class="form-control fullname" value="<?= $c['name'] ?>">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Email" type="text" class="form-control email" value="<?= $c['email'] ?>">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Mobile" type="text" class="form-control mobile" value="<?= $c['mobile'] ?>">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Position" type="text" class="form-control position" value="<?= $c['position'] ?>">
                            </div>
                            <div class="col-md-1">
                                <div class="btn-row btn btn-secondary" onclick='removeRow(this);'><i
                                            class="fa fa-minus"></i></div>
                            </div>
                        </div>
                    <? } ?>
                </div>
                <div class="form-group mt-sm">
                    <div class="col-md-12 d-flex justify-content-center">
                        <button id="save-btn" type="button" class="btn btn-success col-md-3" onclick="save_client()">
                            <span class="d-flex justify-content-center align-items-center">
                                <span id="save-spinner" style="display:none ">
                                    <span class="spinner-border text-danger mr-sm" style="height: 20px;width: 20px;"></span>
                                </span>

                                <span>Save Client</span>
                            </span>
                        </button>
                        <button type="button" class="btn btn-danger col-md-3 ml-sm" onclick="window.close()">Close window</button>
                    </div>
                </div>
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
                        <th>Tin</th>
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

<script src="../assets/js/quick_adds.js"></script>
<script src="../assets/intl-tel/js/intlTelInput-jquery.js"></script>
<script>
    $(function () {
        let token = `<?=$_GET['access_token']?>`;
        let url = `?module=endpoints&action=ajax_getUsers&access_token=${token}`;
        $('#account-manager').select2({
            placeholder: 'Choose account manager',
            width: '100%', minimumInputLength: 2,
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                quietMillis: 200,
                data: function (term) {
                    return {search: term};
                },
                results: function (data, page) {
                    return {result: data};
                }
            }
        });

        $('.mobile-number').intlTelInput({
            preferredCountries: ['tz'],
            separateDialCode: true,
        });
    });

    let MODAL_SHOWN = false;
    let relatedClientModal = $('#related-client-modal');

    function search_client(obj) {
        let clientname = '', tinno = '', vatno = '';
        if ($(obj).data('source') === 'name') clientname = $(obj).val();
        if ($(obj).data('source') === 'tinno') tinno = $(obj).val();
        if ($(obj).data('source') === 'vatno') vatno = $(obj).val();
        let spinner = $('#search-spinner');
        if (!MODAL_SHOWN && $('#clientid').val().length === 0) {
            if (clientname.length >= 3 || tinno.length == 9 || vatno.length >= 3) {
                $(relatedClientModal).find('tbody.tbody').empty();
                spinner.show();
                $.get("?module=endpoints&action=ajax_getClients", {
                    clientname: clientname,
                    tinno: tinno,
                    vatno: vatno,
                    access_token: `<?=$_GET['access_token']?>`
                }, function (data) {
                    spinner.hide();
                    let result = data;
                    // console.log(result);

                    if (result.status === 'success') {
                        if (result.data.length > 0) {
                            let token = `<?=$_GET['access_token']?>`;
                            $.each(result.data, function (index, det) {
                                let i = index + 1;

                                let tbl_row = `<tr>
                                            <td style='width:80px;'>${i}</td>
                                            <td>${det.name}</td>
                                            <td>${det.tinno}</td>
                                            <td>
                                                <a class='btn btn-default btn-sm' title='client edit' href='?clientid=${det.id}&module=endpoints&action=clients&access_token=${token}'>
                                                    <i class='fa fa-pencil'></i> edit</a>
                                            </td>
                                        </tr>`;

                                $(relatedClientModal).find('tbody.tbody').append(tbl_row);
                            });
                            $(relatedClientModal).modal('show');
                            MODAL_SHOWN = true;
                            console.log('modal show', MODAL_SHOWN);
                            $(spinner).focus();
                        }
                    } else {
                        notifyError(result.msg || "Error found", 3000);
                    }
                });
            }
        }
    }

    $(relatedClientModal).on('hide.bs.modal', function () {
        MODAL_SHOWN = false;
        // console.log('modal show', MODAL_SHOWN);

    });

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

    function addRow() {
        let contact = `<div class="row mb-sm contact-row">
                            <div class="col-md-3">
                                <input placeholder="Full name" type="text" class="form-control fullname">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Email" type="text" class="form-control email">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Mobile" type="text" class="form-control mobile">
                            </div>
                            <div class="col-md-2">
                                <input placeholder="Position" type="text" class="form-control position">
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

    function save_client() {
        let valid = true;
        let contacts = [];
        $('.contact-row').each(function (i, row) {
            let fullname = $(row).find('.fullname').val();
            if (fullname.length === 0) {
                valid = false;
                notifyError('Invalid contact name');
                $(row).find('.fullname').focus();
                return false;
            }
            contacts.push({
                fullname: fullname,
                email: $(row).find('.email').val(),
                mobile: $(row).find('.mobile').val(),
                position: $(row).find('.position').val(),
            });
        });
        if (!valid) return;

        let clientname = $('.clientname').val();
        if (clientname.length === 0) {
            valid = false;
            notifyError('Invalid client name');
            $('.clientname').focus();
            return;
        }

        let country_code = $('#mobile').intlTelInput('getSelectedCountryData').dialCode;
        let client = {
            id: $('#clientid').val(),
            name: clientname,
            tinno: $('.tinno').val(),
            vatno: $('.vatno').val(),
            mobile: $('.mobile-number').val(),
            mobile_country_code: country_code ? country_code : '',
            tel: $('.tel').val(),
            email: $('.email').val(),
            address: $('.address').val(),
            plotno: $('.plotno').val(),
            city: $('.city').val(),
            district: $('.district').val(),
            street: $('.street').val(),
            contacts: contacts
        };

        let post_data = {};
        post_data.client = client;
        if ($('.for_support').length > 0) post_data.for_support = 1;
        let spinner = $('#save-spinner');
        let savebtn = $('#save-btn');
        spinner.show();
        savebtn.prop('disabled', true);

        let token = `<?=$_GET['access_token']?>`;
        $.post(`?module=endpoints&action=ajax_saveClients&access_token=${token}`, JSON.stringify(post_data), function (data) {
            spinner.hide();
            savebtn.prop('disabled', false);
            // console.log(data);
            let result = data;
            if (result.status === 'success') {
                $('#clientid').val(result.data.clientid);
                if ($('.for_support').length === 0) $('#clientid').closest('div').show();

                notifyMessage(result.msg);
                if (result.response_error) notifyError(result.response_error, 5000);
            } else {
                notifyError(result.msg || 'error found');
            }
        });
    }
</script>


<script src="../assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="../assets/vendor/pnotify/pnotify.custom.js"></script>
<script src="../assets/js/cleave.min.js"></script>
<script>
    (function ($) {

        'use strict';

        // use font awesome icons if available
        if (typeof PNotify != 'undefined') {
            PNotify.prototype.options.styling = "fontawesome";

            $.extend(true, PNotify.prototype.options, {
                shadow: false,
                stack: {
                    spacing1: 15,
                    spacing2: 15
                }
            });

            $.extend(PNotify.styling.fontawesome, {
                // classes
                container: "notification",
                notice: "notification-warning",
                info: "notification-info",
                success: "notification-success",
                error: "notification-danger",

                // icons
                notice_icon: "fa fa-exclamation",
                info_icon: "fa fa-info",
                success_icon: "fa fa-check",
                error_icon: "fa fa-times"
            });
        }

    }).apply(this, [jQuery]);

    function notifyMessage(msg, delay = 1000) {
        new PNotify({
            title: 'Success',
            text: '' + msg + '',
            type: 'success',
            delay: delay
        });
    }

    function notifyError(msg, delay = 1000) {
        new PNotify({
            title: 'Error',
            text: '' + msg + '',
            type: 'error',
            delay: delay
        });
    }

    function truncateDecimals(number, digits) {
        let multiplier = Math.pow(10, digits), adjustedNum = number * multiplier,
            truncatedNum = Math[adjustedNum < 0 ? 'ceil' : 'floor'](adjustedNum);

        return truncatedNum / multiplier;
    }

    function thousands_separator(selector, decimal = 2) {
        $(selector).toArray().forEach(function (field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: decimal,
                numeralPositiveOnly: true
            });
        });
    }

    function qtyInput(selector) {
        $(selector).toArray().forEach(function (field) {
            new Cleave(field, {
                numeral: true,
                numeralThousandsGroupStyle: 'none',
                numeralDecimalScale: 0,
                numeralPositiveOnly: true
            });
        });
    }
</script>
</body>
</html>
