<style media="screen">
    div.dataTables_wrapper div.dataTables_filter input {
        width: 100%;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0;
    }

    .formholder h5 {
        font-size: 15px;
        font-weight: 600;
    }

    .panelControl {
        float: right;
    }

    .table-responsive {
        min-height: 223px;
    }

    .duplicate-month {
        border: 1px solid red;
        box-shadow: 0 0 3px red;
    }

    #months-inputs {
        border: 1px dashed grey;
        padding: 10px;
        padding-top: 20px;
        min-height: 80px;
        margin: 5px;
        border-radius: 10px;
        background-color: #efefef;
    }

</style>

<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                </div>
                <h2 class="panel-title">Stock Planning Report</h2>
                <h5 class="mt-sm text-primary mt-md"><?= $title ?></h5>
                <h5 class="mt-sm mt-md">Months: <span class="text-success"><?= implode(' | ', $months) ?></span></h5>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <form onsubmit="$(this).find('.loading_spinner').show();$(this).find('.submit-btn').prop('disabled',true);">
                            <input type="hidden" name="module" value="stocks">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    Branch:
                                    <select id="branchid" class="form-control" name="branchids[]" multiple required>
                                        <? foreach ($branches as $b) { ?>
                                            <option <?= in_array($b['id'], $branchids) ? 'selected' : '' ?>
                                                    value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Product:
                                    <select id="productid" class="form-control" name="productid"></select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    Brand:
                                    <select id="modelid" class="form-control" name="modelid">
                                        <option value="" selected>-- All --</option>
                                        <? foreach ($models as $r) { ?>
                                            <option <?= selected($r['id'], $modelid) ?>
                                                    value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Product Category:
                                    <select id="productcategory" class="form-control" name="productcategoryid">
                                        <option value="" selected>-- All --</option>
                                        <? foreach ($productcategories as $r) { ?>
                                            <option <?= selected($r['id'], $productcategoryid) ?> value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Subcategory:
                                    <select id="subcategory" class="form-control" name="subcategoryid">
                                        <option value="" selected>-- All --</option>
                                        <? foreach ($subcategories as $r) { ?>
                                            <option <?= selected($r['id'], $subcategoryid) ?> value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Department:
                                    <select id="department" class="form-control" name="deptid">
                                        <option value="" selected>-- All --</option>
                                        <? foreach ($departments as $r) { ?>
                                            <option <?= selected($r['id'], $deptid) ?> value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 d-flex align-items-center">
                                    <label>Months</label>
                                    <button type="button" class="btn btn-sm btn-primary ml-md" title="add month"
                                            onclick="addMonth()"><i
                                                class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <div id="months-inputs" class="row">
                                <? if (!empty($selected_months)) { ?>
                                    <? foreach ($selected_months as $month) { ?>
                                        <div class="col-md-3 month mb-sm" style="position: relative;">
                                            <button type="button" class="btn btn-sm text-danger"
                                                    onclick="$(this).closest('.month').remove();checkMonthDuplicates();"
                                                    style="position: absolute;top: -10px;right: 0;">
                                                <i class="fa fa-close"></i>
                                            </button>
                                            <input type="text" autocomplete="off" class="form-control month-picker2" name="months[]"
                                                   value="<?= $month ?>"
                                                   placeholder="choose month" required onchange="checkMonthDuplicates(this)">
                                        </div>
                                    <? } ?>
                                <? } else { ?>
                                    <div class="col-md-3 month mb-sm" style="position: relative;">
                                        <button type="button" class="btn btn-sm text-danger"
                                                onclick="$(this).closest('.month').remove();checkMonthDuplicates();"
                                                style="position: absolute;top: -10px;right: 0;">
                                            <i class="fa fa-close"></i>
                                        </button>
                                        <input type="text" autocomplete="off" class="form-control month-picker2" name="months[]"
                                               value="<?= date('m-Y') ?>"
                                               placeholder="choose month" required onchange="checkMonthDuplicates(this)">
                                    </div>
                                <? } ?>
                            </div>
                            <div class="row mt-md d-flex justify-content-end">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="loading_spinner" style="display: none">
                                            <object data="images/loading_spinner.svg" type="image/svg+xml" height="50" width="50"></object>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block submit-btn"><i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="product-table" class="table table-hover table-bordered mb-none" style="font-size:10pt;">
                        <thead class="thead">
                        <tr>
                            <th>#</th>
                            <th>Barcode</th>
                            <th style="width: 20%">Product Name</th>
                            <th style="width: 20%">Description</th>
                            <th>Brand Name</th>
                            <th>Units</th>
                            <th class="text-center">Current Stock Qty</th>
                            <? foreach ($months as $month) { ?>
                                <th class="text-center"><?= $month ?></th>
                            <? } ?>
                            <th class="text-center" title="average sales qty per month">Average Monthly Sale Qty</th>
                            <th class="text-center">Total Qty</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($salesSummary as $key => $P) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $P['barcode'] ?></td>
                                <td><?= $P['productname'] ?></td>
                                <td><?= $P['description'] ?></td>
                                <td><?= $P['brandname'] ?></td>
                                <td><?= $P['unitname'] ?></td>
                                <td class="text-center text-success text-weight-bold"><?= $P['stock_qty'] ?></td>
                                <? foreach ($months as $month) { ?>
                                    <td class="text-center"><?= $P['months'][$month] ? $P['months'][$month] : '' ?></td>
                                <? } ?>
                                <td class="text-center text-primary"><?= $P['avg_qty'] ?></td>
                                <td class="text-center"><?= $P['total_qty'] ?></td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/chart.js"></script>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Choose location');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'Choose sales person', 2);

        $('#branchid,#productcategory,#subcategory,#modelid,#department').select2({width: '100%'});
        initDatePicker();
        $('#product-table').DataTable({
            dom: '<"top"Bfl>t<"bottom"ip>',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    className: 'btn btn-default',
                    exportOptions: {
                        columns: ':not(.notexport)'
                    }
                }]
        });
    });

    function initDatePicker() {
        $(".month-picker2").datepicker({
            orientation: "top",
            format: "mm-yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true,
            endDate: new Date("<?=TODAY?>"),
        });
    }

    function addMonth() {
        let monthInput = `<div class="col-md-3 month mb-sm" style="position: relative;">
                              <button type="button" class="btn btn-sm text-danger" onclick="$(this).closest('.month').remove();checkMonthDuplicates();" style="position: absolute;top: -10px;right: 0;">
                                  <i class="fa fa-close"></i>
                              </button>
                              <input type="text" autocomplete="off" class="form-control month-picker2" name="months[]" placeholder="choose month" required onchange="checkMonthDuplicates(this)">
                          </div>`;
        $('#months-inputs').append(monthInput);
        initDatePicker();
    }

    function checkMonthDuplicates() {
        let selectedMonths = [];
        $("input[name='months[]']").each(function () {
            if ($(this).val().length > 0)
                selectedMonths.push($(this).val());
        });
        let sorted = selectedMonths.slice().sort();
        let duplicates = [];
        for (let i = 0; i < sorted.length - 1; i++) {
            if (sorted[i] === sorted[i + 1]) {
                duplicates.push(sorted[i]);
            }
        }
        // console.log("duplicates: ",duplicates);

        //mark duplicates
        $("input[name='months[]']").each(function () {
            if ($.inArray($(this).val(), duplicates) != -1) {
                $(this).addClass('duplicate-month');
            } else {
                $(this).removeClass('duplicate-month');
            }
        });

    }
</script>
