<link rel="stylesheet" href="assets/intl-tel/css/intlTelInput.css">
<style media="screen">
    #form .row {
        /* margin-top:10px; */
    }

    .center-box {
        width: 66.66666667%;
        margin: 0 auto;
    }

    .border-danger {
        background: red;
        color: white;
    }
</style>
<header class="page-header">
    <h2><? if ($edit) echo 'Edit'; else echo 'Add'; ?> Supplier</h2>
</header>
<div class="row">
    <div class="center-box">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Supplier Details</h2>
            </header>
            <div class="panel-body">
                <form action="<?= url('suppliers', 'supplier_save') ?>" method="post" onsubmit="return validateInputs()">
                    <input type="hidden" name="supplier[id]" value="<?= $supplier['id'] ?>">
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
                            <div class="col-md-4" title="if supplier should charge you VAT or not">
                                <h5>VAT Registered</h5>
                                <select name="supplier[vat_registered]" class="form-control">
                                    <option value="0" <?= selected($supplier['vat_registered'], 0) ?>>No</option>
                                    <option value="1" <?= selected($supplier['vat_registered'], 1) ?>>Yes</option>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <a href="?module=suppliers&action=supplier_list" class="mb-xs mt-xs mr-xs btn btn-success btn-block"><i
                                            class="fa fa-list"></i> Back</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="submit mb-xs mt-xs mr-xs btn btn-primary btn-block"><i class="fa fa-save"></i> Save
                                    supplier
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
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

    function validateInputs() {
        let country_code = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
        $('#country_code').val(country_code ? country_code : '');
    }

    function checktin(obj) {
        var parent = $(obj).closest(form);
        var submitbutn = $(parent).find(".submit");
        var tin = $(obj).val();
        $(obj).removeClass("border-danger");
        $(submitbutn).removeClass("disabled");


        if (tin.length > 9 || tin.length < 9) {
            $(obj).addClass("border-danger");
            $(submitbutn).addClass("disabled");
            triggerError('TIN Should be Nine(9) Characters');
        } else {
            triggerMessage('TIN is OK');
            $(obj).removeClass("border-danger");
            $(submitbutn).removeClass("disabled");
        }

        var n = tin.includes("-");
        if (n == true) {
            $(submitbutn).addClass("disabled");

            $(obj).addClass("border-danger");
            triggerError('TIN Should Not Contain " - "');
        }

        if (tin.length == 0) {
            $(obj).removeClass("border-danger");
            $(submitbutn).removeClass("disabled");
        }

    }
</script>