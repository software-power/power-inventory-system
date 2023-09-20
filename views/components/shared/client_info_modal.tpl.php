<link rel="stylesheet" href="assets/intl-tel/css/intlTelInput.css">
<style>
    .iti {
        width: 100%;
    }

    .border-danger {
        box-shadow: 0 0 5px red !important;
    }

    @media (min-width: 768px) {
        #client-info-modal .modal-lg {
            width: 50% !important;
        }
    }
</style>
<div class="modal fade" id="client-info-modal" tabindex="-1" role="dialog" aria-labelledby="client-info-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <div class="d-flex align-items-center">
                    <h4 class="modal-title" id="myModalLabel">Client Info</h4>
                    <div class="client-info-spinner" style="visibility: hidden;">
                        <object data="images/loading_spinner.svg" type="image/svg+xml" height="30" width="30"></object>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mb-md">
                    <div class="col-md-5">
                        <label>Client code</label>
                        <div>
                            <span class="text-danger text-weight-semibold clientinfocode" style="user-select: none"></span>
                            <button type="button" class="btn btn-default btn-sm ml-sm copy-btn support_message" title="copy code"
                                    data-clientcode='' onclick="copyClientCode(this)">Copy code for support
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Client name</label>
                        <input type="text" readonly class="form-control clientinfoname" placeholder="client name">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>TIN</label>
                        <input type="text" readonly class="form-control clientinfotinno" placeholder="TIN">
                    </div>
                    <div class="col-md-4">
                        <label>VRN</label>
                        <input type="text" readonly class="form-control clientinfovatno" placeholder="VRN">
                    </div>
                    <div class="col-md-4">
                        <label>Reseller</label>
                        <input type="text" readonly class="form-control clientinforeseller" placeholder="Reseller">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Mobile</label>
                        <input type="text" readonly class="form-control clientinfomobile" placeholder="mobile">
                    </div>
                    <div class="col-md-4">
                        <label>Tel</label>
                        <input type="text" readonly class="form-control clientinfotel" placeholder="tel">
                    </div>
                    <div class="col-md-4">
                        <label>Email</label>
                        <input type="text" readonly class="form-control clientinforemail" placeholder="email">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Address</label>
                        <input type="text" readonly class="form-control clientinfoaddress" placeholder="address">
                    </div>
                    <div class="col-md-4">
                        <label>Plot</label>
                        <input type="text" readonly class="form-control clientinfoplot" placeholder="plot">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>City</label>
                        <input type="text" readonly class="form-control clientinforcity" placeholder="city">
                    </div>
                    <div class="col-md-4">
                        <label>District</label>
                        <input type="text" readonly class="form-control clientinfodistrict" placeholder="district">
                    </div>
                    <div class="col-md-4">
                        <label>Street</label>
                        <input type="text" readonly class="form-control clientinfostreet" placeholder="street">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Account manager</label>
                        <input type="text" readonly class="form-control clientinformanager" placeholder="acc manager">
                    </div>
                </div>
                <div class="row mt-md">
                    <div class="col-md-12">
                        <h5>Contacts</h5>
                    </div>
                </div>
                <div class="clientinfocontacts">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Full name</label>
                            <input type="text" readonly class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Email</label>
                            <input type="text" readonly class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Mobile</label>
                            <input type="text" readonly class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Position</label>
                            <input type="text" readonly class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    let client_info_modal = $('#client-info-modal');
    $(client_info_modal).on('show.bs.modal', function (e) {
        let clientid = $(e.relatedTarget).data('clientid');
        let forsupport = $(e.relatedTarget).attr('forsupport');
        forsupport = typeof forsupport !== 'undefined' && forsupport !== false;
        let modal = $(this);
        let spinner = $(modal).find('.client-info-spinner');
        if (forsupport) {
            $(modal).find('.support_message').show();
        } else {
            $(modal).find('.support_message').hide();
        }

        $(modal).find('.form-control').val('');
        $(modal).find('.copy-btn').attr('data-clientcode', '');
        $(modal).find('.clientinfocontacts').empty();
        spinner.css('visibility', 'visible');
        $.get(`?module=clients&action=getClientDetails&format=json&clientId=${clientid}`, null, function (data) {
            spinner.css('visibility', 'hidden');
            let client = JSON.parse(data);
            // console.log(client);
            if (client.length > 0) {
                client = client[0];
                //copy code btn
                let btn_data = {};
                btn_data.code = client.id;
                btn_data.support_name = `<?=CS_SUPPORT_NAME?>`;
                // console.log(btn_data);
                btn_data = JSON.stringify(btn_data);
                btn_data = btoa(btn_data);
                // console.log(btn_data);
                $(modal).find('.copy-btn').attr('data-clientcode', btn_data);
                // console.log(btn_data);

                $(modal).find('.clientinfocode').text(client.id);
                $(modal).find('.clientinfoname').val(client.name);
                $(modal).find('.clientinfotinno').val(client.tinno);
                $(modal).find('.clientinfovatno').val(client.vatno);
                $(modal).find('.clientinforeseller').val(client.reseller == 1 ? 'Yes' : 'No');
                $(modal).find('.clientinfomobile').val(client.mobile);
                $(modal).find('.clientinfotel').val(client.tel);
                $(modal).find('.clientinforemail').val(client.email);
                $(modal).find('.clientinfoaddress').val(client.address);
                $(modal).find('.clientinfoplot').val(client.plot);
                $(modal).find('.clientinforcity').val(client.city);
                $(modal).find('.clientinfodistrict').val(client.district);
                $(modal).find('.clientinfostreet').val(client.street);
                $(modal).find('.clientinfodistrict').val(client.district);
                $(modal).find('.clientinformanager').val(client.accmanager);
                $.each(client.contacts, function (i, c) {
                    let div = `<div class="row">
                                    <div class="col-md-3">
                                        <label>Full name</label>
                                        <input type="text" readonly class="form-control" value="${c.name}">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Email</label>
                                        <input type="text" readonly class="form-control" value="${c.email}">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Mobile</label>
                                        <input type="text" readonly class="form-control" value="${c.mobile}">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Position</label>
                                        <input type="text" readonly class="form-control" value="${c.position}">
                                    </div>
                                </div>`;
                    $(modal).find('.clientinfocontacts').append(div);
                });
            } else {
                triggerError('client info not found');
            }
        });
    });


    function copyClientCode(obj) {

        let clientcode = $(obj).data('clientcode');
        console.log(clientcode);
        try {
            let tempInput = document.createElement("input");
            tempInput.style = "position: absolute; left: -1000px; top: -1000px";
            tempInput.value = clientcode;
            $(client_info_modal).append(tempInput);  //todo append too modal
            tempInput.select();
            // console.log(tempInput.value);
            let successful = document.execCommand('copy');
            $(tempInput).remove();

            let msg = successful ? 'successful' : 'unsuccessful';
            successful ? triggerMessage('Code copied successfully') : triggerError('Failed copying');
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err);
        }
        document.getSelection().empty();
    }
</script>
