<link rel="stylesheet" href="assets/intl-tel/css/intlTelInput.css">
<style>
    .iti {
        width: 100%;
    }

    @media (min-width: 768px) {
        #quick-add-supplier-modal .modal-lg {
            width: 80% !important;
        }
    }
</style>
<div class="modal fade" id="quick-add-supplier-modal" role="dialog" aria-labelledby="quick-add-supplier-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Quick Add Supplier</h4>
            </div>
            <form action="<?= url('suppliers', 'quick_add') ?>" method="post" onsubmit="return validateModalInputs()">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h5>Name</h5>
                                <input type="text" placeholder="Supplier Name" required class="form-control" id="name" title="Name is required"
                                       name="supplier[name]" value="<?= $supplier['name'] ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h5>Address</h5>
                                <input type="text" placeholder="Address" class="form-control" id="address" title="Address is required"
                                       name="supplier[address]" value="<?= $supplier['address'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <h5>TIN</h5>
                                <input type="text" placeholder="TIN" class="form-control" id="" title="TIN is required" name="supplier[tin]"
                                       value="<?= $supplier['tin'] ?>" onblur="checktin(this)">
                            </div>
                            <div class="col-md-4">
                                <h5>VAT Registered</h5>
                                <select name="supplier[vat_registered]" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <h5>VRN</h5>
                                <input type="text" placeholder="VRN" class="form-control" title="VRN" name="supplier[vat]"
                                       value="<?= $supplier['vat'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <h5>Contact person (Name)</h5>
                                <input type="text" required placeholder="Name" class="form-control" id="" title="name is required"
                                       name="supplier[contact_name]" value="<?= $supplier['contact_name'] ?>">
                            </div>
                            <div class="col-md-4">
                                <h5>Contact person (Mobile)</h5>
                                <input type="tel" required placeholder="Mobile" class="form-control" id="phone" title="Mobile is required"
                                       name="supplier[contact_mobile]"
                                       value="<?= ($supplier['country_code'] ? "+" . $supplier['country_code'] : "") . $supplier['contact_mobile'] ?>">
                                <input id="country_code" type="hidden" name="supplier[country_code]">
                            </div>
                            <div class="col-md-4">
                                <h5>Contact person (Email)</h5>
                                <input type="text" placeholder="Email" class="form-control" id="" title="Email is required"
                                       name="supplier[contact_email]" value="<?= $supplier['contact_email'] ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <h5>Status</h5>
                                <select name="supplier[status]" class="form-control">
                                    <option value="active" <?= selected($supplier['status'], 'active') ?>>Active</option>
                                    <option value="inactive" <?= selected($supplier['status'], 'inactive') ?>>In-Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/intl-tel/js/intlTelInput-jquery.js"></script>
<script>
    $(function () {
        $('#phone').intlTelInput({
            preferredCountries: ['tz'],
            separateDialCode: true,
        });
    });

    function validateModalInputs() {
        let country_code = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
        $('#country_code').val(country_code ? country_code : '');
    }
</script>
