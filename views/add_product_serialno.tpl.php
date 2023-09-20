<style>
    .border-danger {
        border: 1px solid red !important;
        box-shadow: 0 0 1px red;
    }
</style>

<div class="row d-flex justify-content-center">
    <div class="col-md-9">
        <section class="panel">
            <div class="panel-heading">
                <h2 class="panel-title">Add Product Serial No</h2>
            </div>
            <div class="panel-body">
                <form>
                    <input type="hidden" name="module" value="serialnos">
                    <input type="hidden" name="action" value="add_product_serialno">
                    <input type="hidden" name="productid" value="<?= $product['id'] ?>">
                    <div class="row d-flex align-items-center">
                        <div class="col-md-4">
                            Location:
                            <select id="locationid" name="locationid" class="form-control">
                                <? foreach ($branchLocations as $l) { ?>
                                    <option value="<?= $l['id'] ?>"><?= $l['name'] ?> - <?= $l['branchname'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Product:
                            <select id="productid" name="productid" class="form-control"></select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-success">Search</button>
                        </div>
                    </div>
                </form>
                <? if ($product) { ?>
                    <? if ($grnInfo) { ?>
                        <div class=" mt-lg">
                            <h4>GRN Info:</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                GRN no:
                                <input type="text" readonly class="form-control" value="<?= $grnInfo['grnnumber'] ?>">
                            </div>
                            <div class="col-md-3">
                                Supplier name:
                                <input type="text" readonly class="form-control" value="<?= $grnInfo['suppliername'] ?>">
                            </div>
                        </div>
                    <div class="mt-sm">
                        <h5>Product Info:</h5>
                    </div>
                    <?}?>
                    <div class="row">
                        <div class="col-md-3">
                            Product name:
                            <input type="text" readonly class="form-control" value="<?= $product['name'] ?>">
                        </div>
                        <div class="col-md-3">
                            Stock location:
                            <input id="stocklocation" type="hidden" value="<?= $product['locationid'] ?>">
                            <input type="text" readonly class="form-control" value="<?= $product['locationname'] ?>">
                        </div>
                        <div class="col-md-2">
                            Stock Qty:
                            <input type="text" readonly class="form-control" value="<?= $product['stock_qty'] ?>">
                        </div>
                        <div class="col-md-2">
                            Serial In Stock:
                            <input type="text" readonly class="form-control" value="<?= $product['serial_in_stock'] ?>">
                        </div>
                        <div class="col-md-2">
                            Stock without serial no:
                            <input id="pending-serialno" type="text" readonly class="form-control"
                                   value="<?= $product['stock_qty_without_serial'] ?>">
                        </div>
                    </div>
                    <div class="row mt-xlg d-flex justify-content-center">
                        <div class="col-md-6">
                            <div class="d-flex mb-sm">
                                <button type="button" class="btn btn-default btn-sm" onclick="addItem()"><i class="fa fa-plus"></i> Add</button>
                                <button type="button" class="btn btn-default btn-sm ml-xs" onclick="$('#file-input').trigger('click')"><i
                                            class="fa fa-file"></i> Add from file
                                </button>
                                <button type="button" class="btn btn-danger btn-sm ml-xs" onclick="clearItems()"><i class="fa fa-trash"></i> Clear
                                </button>
                                <input id="file-input" type="file" style="display: none;" accept="*.txt" onchange="getSerialNos()">
                            </div>
                            <p>Count: <span class="text-primary serial_count">0</span></p>
                            <form id="serialno-form" action="<?= url('serialnos', 'upload_serialno') ?>" method="post"
                                  onsubmit="return validateInputs()">
                                <input id="stockid" type="hidden" name="stockid" value="<?= $product['stockid'] ?>">
                                <?if($grnInfo){?><input type="hidden" name="gdi" value="<?= $product['gdi'] ?>"><?}?>
                                <div id="serialno-holder" class="mb-xlg"
                                     style="max-height: 50vh;overflow: hidden;overflow-y: auto;border: 1px dashed #dadada;border-radius: 5px;padding: 10px;">
                                    <table id="serialno-table" class="table table-condensed table-bordered" style="font-size: 10pt;">
                                        <thead>
                                        <tr>
                                            <th style="width: 35px"></th>
                                            <th>Number</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-xs" title="remove" onclick="removeItem(this)">
                                                    <i class="fa fa-close"></i></button>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control input-sm serial_number" name="serialno[]" required>
                                                <input type="hidden" class="valid">
                                            </td>
                                            <td class="status" style="text-align: center;vertical-align: middle;">
                                                <i class="fa fa-check text-success text-lg" style="display: none;"></i>
                                                <small class="error-msg text-danger" style="display: none;">some error</small>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex align-items-center justify-content-end">
                                    <object class="validate_spinner" data="images/loading_spinner.svg"
                                            type="image/svg+xml" height="30" width="30" style="display: none"></object>
                                    <button id="validate-btn" type="button" class="btn btn-primary" onclick="validateSerialNo()"> Validate</button>
                                    <button class="btn btn-success ml-xs"> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <? } ?>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', '?module=products&action=getProducts&format=json', 'Choose product');
        $('#locationid').select2({width: '100%'});
        countSerialNo();
    });

    function validateInputs() {
        let valid = true;
        $('#serialno-table tbody tr').each(function (i, tr) {
            if ($(tr).find('.valid').val() != 1) {
                triggerError('Serial number not validated');
                $(tr).find('.serial_number').focus();
                valid = false;
                return false;
            }
        });

        if (!valid) return false;
    }

    function addItem() {
        let row = `<tr>
                       <td>
                           <button type="button" class="btn btn-danger btn-xs" title="remove" onclick="removeItem(this)">
                               <i class="fa fa-close"></i></button>
                       </td>
                       <td>
                           <input type="text" class="form-control input-sm serial_number" name="serialno[]" required>
                           <input type="hidden" class="valid">
                       </td>
                       <td class="status" style="text-align: center;vertical-align: middle;">
                           <i class="fa fa-check text-success text-lg" style="display: none;"></i>
                           <small class="error-msg text-danger" style="display: none;">some error</small>
                       </td>
                   </tr>`;
        $('#serialno-table tbody').append(row);
        countSerialNo();
        $('#serialno-holder').animate({
            scrollTop: $('#serialno-holder').stop().prop("scrollHeight")
        }, 500);

    }

    function clearItems() {
        $('#serialno-table tbody').empty();
        resetFileInput();
        countSerialNo();
    }

    function resetFileInput() {
        let file_input = `<input id="file-input" type="file" style="display: none;" accept="*.txt" onchange="getSerialNos()">`;
        $('#file-input').replaceWith(file_input);
    }

    function removeItem(obj) {
        $(obj).closest('tr').remove();
        countSerialNo();
    }

    function countSerialNo() {
        $('.serial_count').text($('#serialno-table .serial_number').length);
    }

    function getSerialNos() {
        let file_input = $('#file-input')[0];
        const reader = new FileReader();
        reader.readAsBinaryString(file_input.files[0]);

        reader.onload = function () {
            let serial_numbers = reader.result.split('\n');
            $('#serialno-table tbody').empty();

            $.each(serial_numbers, function (i, no) {
                no = no.replace('\r', '');
                if (no.length > 0) {
                    let row = `<tr>
                                   <td>
                                       <button type="button" class="btn btn-danger btn-xs" title="remove" onclick="removeItem(this)">
                                           <i class="fa fa-close"></i></button>
                                   </td>
                                   <td>
                                       <input type="text" class="form-control input-sm serial_number" name="serialno[]" required value="${no}">
                                       <input type="hidden" class="valid">
                                   </td>
                                   <td class="status" style="text-align: center;vertical-align: middle;">
                                       <i class="fa fa-check text-success text-lg" style="display: none;"></i>
                                       <small class="error-msg text-danger" style="display: none;">some error</small>
                                   </td>
                               </tr>`;
                    $('#serialno-table tbody').append(row);
                }
            });
            countSerialNo();
            resetFileInput();

        };
        // start reading the file. When it is done, calls the onload event defined above.

    }

    function validateSerialNo() {
        let spinner = $('.validate_spinner');
        let validate_btn = $('#validate-btn');
        let serial_number_inputs = $('#serialno-table .serial_number');

        if (serial_number_inputs.length === 0) {
            triggerError('Entered at least one serial number', 3000);
            return false;
        }
        //check values
        let valid = true;
        $(serial_number_inputs).each(function () {
            if ($.trim($(this).val()).length === 0) {
                triggerError('Invalid serial number');
                $(this).focus();
                valid = false;
                return false;
            }
        });
        if (!valid) return;
        let pending_serialno = parseInt($('#pending-serialno').val() || 0);
        if (serial_number_inputs.length > pending_serialno) {
            triggerError('Entered serial number exceed required serial number in stock', 3000);
            return false;
        }


        let serialnos = Array.from($(serial_number_inputs).get(), function (e) {
            return $.trim(e.value);
        });

        //check duplicates
        if (hasDuplicateSerialNo(serialnos)) return false;
        //check duplicates
        serial_number_inputs.prop('readonly', true);
        validate_btn.prop('disabled', true);
        spinner.show();

        let stockid = $('#stockid').val();
        if (!stockid) {
            triggerError('Stock id not found', 3000);
            return false;
        }

        $.post(`?module=serialnos&action=validateSerialnoBundle&format=json&`,
            {
                stockid: stockid,
                serialnos: serialnos
            },
            function (data) {
                spinner.hide();
                validate_btn.prop('disabled', false);
                let result = JSON.parse(data);
                if (result.status === 'success') {
                    validate_btn.prop('disabled', false);
                    $.each(result.data, function (i, item) {
                        $('#serialno-table input.serial_number').each(function (i, input) {
                            if ($(input).val() == item.number) {
                                let tr = $(this).closest('tr');
                                if (item.status === 1) {
                                    $(tr).find('.valid').val(1);
                                    $(tr).find('.status i.fa').show();
                                    $(tr).find('.error-msg').text('').hide();
                                } else {
                                    $(tr).find('.serial_number').prop('readonly', false);
                                    $(tr).find('.valid').val('');
                                    $(tr).find('.status i.fa').hide();
                                    $(tr).find('.error-msg').text('exists').show();
                                }
                            }
                        });
                    });

                } else {
                    triggerError(result.msg || 'error found');
                }
            });

    }

    function hasDuplicateSerialNo(serialnos) {
        let serial_number_inputs = $('#serialno-table .serial_number');
        $(serial_number_inputs).removeClass('border-danger');
        let sorted_arr = serialnos.slice().sort();
        let duplicates = [];
        for (let i = 0; i < sorted_arr.length - 1; i++) {
            if (sorted_arr[i + 1] == sorted_arr[i]) {
                duplicates.push(sorted_arr[i]);
            }
        }

        if (duplicates.length > 0) {
            triggerError('Duplicate serial number found!', 3000);
            $.each(duplicates, function (i, no) {
                $('#serialno-table').find(`input.serial_number[value="${no}"]`).addClass('border-danger');
            });
            return true;
        } else {
            return false;
        }
    }

</script>