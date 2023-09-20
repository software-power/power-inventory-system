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
                <h2 class="panel-title">Monthly Sales Summary Report in (<?= $baseCurrency['name'] ?>)</h2>
                <h5 class="mt-sm text-primary mt-md"><?= $title ?></h5>
                <h5 class="mt-sm mt-md">Months: <span class="text-success"><?= implode(' | ', $months) ?></span></h5>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <form>
                            <input type="hidden" name="module" value="reports">
                            <input type="hidden" name="action" value="sales_summary_monthly">
                            <div class="row">
                                <div class="col-md-3">
                                    Branch:
                                    <select id="branchid" class="form-control" name="branchid">
                                        <option value="">-- All branches --</option>
                                        <? foreach ($branches as $b) { ?>
                                            <option <?= selected($branchid, $b['id']) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Location:
                                    <select id="locationid" class="form-control" name="locationid">
                                        <?if(isset($location)){?>
                                            <option value="<?=$location['id']?>"><?=$location['name']?> - <?=$location['branchname']?></option>
                                        <?}?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Product:
                                    <select id="productid" class="form-control" name="productid">
                                        <?if(isset($product)){?>
                                            <option value="<?=$product['id']?>"><?=$product['name']?></option>
                                        <?}?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Sales Person:
                                    <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                        <select id="userid" class="form-control" name="userid">
                                            <?if(isset($salesperson)){?>
                                                <option value="<?=$salesperson['id']?>"><?=$salesperson['name']?></option>
                                            <?}?>
                                        </select>
                                    <? } else { ?>
                                        <input type="hidden" name="userid" value="<?= $_SESSION['member']['id'] ?>">
                                        <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                                    <? } ?>
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
                                    Tax Category:
                                    <select class="form-control" name="catid">
                                        <option value="" selected>-- All --</option>
                                        <? foreach ($categories as $r) { ?>
                                            <option <?= selected($r['id'], $catid) ?>
                                                    value="<?= $r['id'] ?>"><?= $r['name'] ?> <?= $r['vat_percent'] ?>%
                                            </option>
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
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    Department:
                                    <select id="department" class="form-control" name="deptid">
                                        <option value="" selected>-- All --</option>
                                        <? foreach ($departments as $r) { ?>
                                            <option <?= selected($r['id'], $deptid) ?> value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Non Stock Items:
                                    <select class="form-control" name="nonstock">
                                        <option value="" selected>-- All --</option>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
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
                                <?if(isset($selected_months)){?>
                                    <? foreach ($selected_months as $month) {?>
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
                                    <?}?>
                                <?}else{?>
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
                                <?}?>
                            </div>
                            <div class="row d-flex justify-content-end">
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-success btn-block" name="button"><i class="fa fa-search"></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
                </div>
                <div class="table-responsive">
                    <table id="product-table" class="table table-hover table-bordered mb-none" style="font-size:10pt;">
                        <thead class="thead">
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Barcode</th>
                            <th rowspan="2">Product Name</th>
                            <th rowspan="2">Description</th>
                            <th rowspan="2">Brand Name</th>
                            <th rowspan="2">Units</th>
                            <? foreach ($months as $month) { ?>
                                <th colspan="2" class="text-center"><?= $month ?></th>
                            <? } ?>
                        </tr>
                        <tr>
                            <? foreach ($months as $month) { ?>
                                <th class="text-center">Qty</th>
                                <th>Amt</th>
                            <? } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($salesSummary as $key => $P) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $P['barcode'] ?></td>
                                <td><?= $P['productName'] ?></td>
                                <td><?= $P['productdescription'] ?></td>
                                <td><?= $P['brandName'] ?></td>
                                <td><?= $P['unit'] ?></td>
                                <? foreach ($months as $month) { ?>
                                    <td class="text-center"><?= $P['months'][$month] ? $P['months'][$month]['quantity'] : '' ?></td>
                                    <td><?= $P['months'][$month] ? formatN($P['months'][$month]['incamount']) : '' ?></td>
                                    <?
                                } ?>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="6"></th>
                            <? foreach ($totals as $t) { ?>
                                <th colspan="2" class="text-center"><?= formatN($t['incamount']) ?></th>
                            <? } ?>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div style="display: none">
                    <table id="product-export-table" class="table table-hover table-bordered mb-none" style="font-size:10pt;">
                        <thead class="thead">
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Barcode</th>
                            <th rowspan="2">Product Name</th>
                            <th rowspan="2">Description</th>
                            <th rowspan="2">Brand Name</th>
                            <th rowspan="2">Units</th>
                            <? foreach ($months as $month) { ?>
                                <th colspan="2" class="text-center"><?= $month ?></th>
                            <? } ?>
                        </tr>
                        <tr>
                            <? foreach ($months as $month) { ?>
                                <th class="text-center">Qty</th>
                                <th>Amt</th>
                            <? } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($salesSummary as $key => $P) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $P['barcode'] ?></td>
                                <td><?= $P['productName'] ?></td>
                                <td><?= $P['productdescription'] ?></td>
                                <td><?= $P['brandName'] ?></td>
                                <td><?= $P['unit'] ?></td>
                                <? foreach ($months as $month) { ?>
                                    <td class="text-center"><?= $P['months'][$month] ? $P['months'][$month]['quantity'] : '' ?></td>
                                    <td><?= $P['months'][$month] ? formatN($P['months'][$month]['incamount']) : '' ?></td>
                                    <?
                                } ?>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="6"></th>
                            <? foreach ($totals as $t) { ?>
                                <th colspan="2" class="text-center"><?= formatN($t['incamount']) ?></th>
                            <? } ?>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <? if (count($months) > 2) { ?>
                    <div class="row mt-lg">
                        <div class="col-md-10">
                            <h4>Chart</h4>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-6">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                    <script>
                        $(function () {
                            showChart();
                        });
                    </script>
                <? } ?>
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

       // try {
           $('#product-table').DataTable({
               dom: '<"top"fl>t<"bottom"ip>',
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
       // }catch (error) {
       //     console.log(error);
       // }
    });

    function exportExcel(e) {
        let table = document.getElementById("product-export-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Monthly sales summary <?=$title?>.xlsx`, // fileName you could use any name
            sheet: {
                name: 'Sales' // sheetName
            }
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
        $(".month-picker2").datepicker({
            orientation: "top",
            format: "mm-yyyy",
            startView: "months",
            minViewMode: "months",
            autoclose: true,
        });
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

    function showChart() {
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?="'" . implode("','", $months) . "'"?>],
                datasets: [{
                    label: 'Sale Amount',
                    data: [<?=implode(",", array_column($totals, 'incamount'))?>],
                    backgroundColor: 'blue',
                    borderColor: 'red',
                    borderWidth: 1,
                    lineTension: 0.3,
                }
                    //,{
                    //    label: 'exclusive',
                    //    data: [<?//=implode(",", array_column($totals,'amount'))?>//],
                    //    backgroundColor: 'blue',
                    //    borderColor: 'green',
                    //    borderWidth: 1,
                    //    lineTension: 0.3,
                    //}
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    }

</script>
