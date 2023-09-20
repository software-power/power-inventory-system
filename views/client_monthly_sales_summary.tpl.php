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

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Search</h4>
            </div>
            <form>
                <div class="modal-body">
                    <input type="hidden" name="module" value="reports">
                    <input type="hidden" name="action" value="client_monthly_sale_summary">
                    <div class="row mb-md">
                        <div class="col-md-4">
                            Client:
                            <select id="clientid" class="form-control" name="clientid"> </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            Branch:
                            <select id="branchid" class="form-control" name="branchid">
                                <option value="">-- All branches --</option>
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($branchid, $b['id']) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="locationid" class="form-control" name="locationid"> </select>
                        </div>
                        <div class="col-md-4">
                            Sales Person:
                            <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                <select id="userid" class="form-control" name="userid"> </select>
                            <? } else { ?>
                                <input type="hidden" name="userid" value="<?= $_SESSION['member']['id'] ?>">
                                <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                            <? } ?>
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control" name="productid"> </select>
                        </div>
                        <div class="col-md-4">
                            Brand:
                            <select id="modelid" class="form-control" name="modelid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($models as $r) { ?>
                                    <option <?= selected($r['id'], $user) ?>
                                            value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Tax Category:
                            <select class="form-control" name="catid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($categories as $r) { ?>
                                    <option <?= selected($r['id'], $categories) ?>
                                            value="<?= $r['id'] ?>"><?= $r['name'] ?> <?= $r['vat_percent'] ?>%
                                    </option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-sm">
                        <div class="col-md-4">
                            Product Category:
                            <select id="productcategory" class="form-control" name="productcategoryid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($productcategories as $r) { ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Subcategory:
                            <select id="subcategory" class="form-control" name="subcategoryid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($subcategories as $r) { ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Department:
                            <select id="department" class="form-control" name="deptid">
                                <option value="" selected>-- All --</option>
                                <? foreach ($departments as $r) { ?>
                                    <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex align-items-center">
                            <label>Months</label>
                            <button type="button" class="btn btn-sm btn-primary ml-md" title="add month"
                                    onclick="addMonth()"><i
                                        class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div id="months-inputs" class="row mt-md">
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
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-default btn-block" data-dismiss="modal">CANCEL</button>
                        </div>
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-success btn-block"><i
                                        class="fa fa-minus"></i> RESET
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block" name="button"><i
                                        class="fa fa-search"></i>
                                SEARCH
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row d-flex justify-content-center">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <button class="btn" data-toggle="modal" data-target="#search-modal"><i class="fa fa-search"></i> Open
                        filter
                    </button>
                </div>
                <h2 class="panel-title">Client Monthly Sales Summary in (<?= $baseCurrency['name'] ?>)</h2>
                <h5 class="mt-sm text-primary mt-md"><?= $title ?></h5>
                <h5 class="mt-sm mt-md">Months: <span class="text-success"><?= implode(' | ', $months) ?></span></h5>
            </header>
            <div class="panel-body">
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="exportExcel()"><i class="fa fa-print"></i> Export Excel</button>
                </div>
                <div class="table-responsive">
                    <table id="summary-table" class="table table-hover table-bordered mb-none" style="font-size:10pt;">
                        <thead class="thead">
                        <tr>
                            <th rowspan="2" style="width: 50px;">#</th>
                            <th rowspan="2">Client</th>
                            <? foreach ($months as $month) { ?>
                                <th colspan="2" class="text-center"><?= $month ?></th>
                            <? } ?>
                        </tr>
                        <tr>
                            <? foreach ($months as $month) { ?>
                                <th class="text-right">Item Qty</th>
                                <th class="text-right">Amt</th>
                            <? } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($client_summary as $key => $P) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td title="view client info">
                                    <a data-toggle="modal" href="#client-info-modal" data-clientid="<?= $P['clientid'] ?>"><?= $P['clientname'] ?></a>
                                </td>
                                <? foreach ($months as $month) { ?>
                                    <td class="text-right"><?= $P['months'][$month]['qty'] ?></td>
                                    <td class="text-right"><?= $P['months'][$month] ? formatN($P['months'][$month]['incamount']) : '' ?></td>
                                    <?
                                } ?>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <th colspan="2" class="text-center text-lg">TOTAL</th>
                            <? foreach ($totals as $t) { ?>
                                <th colspan="2" class="text-right"><?= formatN($t['incamount']) ?></th>
                            <? } ?>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <table id="summary-export-table" class="table table-hover table-bordered mb-none" style="font-size:10pt;display: none">
                    <thead class="thead">
                    <tr>
                        <th rowspan="2">Client</th>
                        <? foreach ($months as $month) { ?>
                            <th colspan="2" class="text-center"><?= $month ?></th>
                        <? } ?>
                    </tr>
                    <tr>
                        <? foreach ($months as $month) { ?>
                            <th class="text-right">Item Qty</th>
                            <th class="text-right">Amt</th>
                        <? } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($client_summary as $key => $P) { ?>
                        <tr>
                            <td><?= $P['clientname'] ?></td>
                            <? foreach ($months as $month) { ?>
                                <td class="text-right"><?= $P['months'][$month]['qty'] ?></td>
                                <td class="text-right"><?= $P['months'][$month] ? formatN($P['months'][$month]['incamount']) : '' ?></td>
                                <?
                            } ?>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>

                    <tfoot>
                    <tr>
                        <th class="text-center text-lg">TOTAL</th>
                        <? foreach ($totals as $t) { ?>
                            <th colspan="2" class="text-right"><?= formatN($t['incamount']) ?></th>
                        <? } ?>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </section>
    </div>
</div>

<?= component('shared/client_info_modal.tpl.php') ?>

<script src="assets/js/tableToExcel.js"></script>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Choose location');
        initSelectAjax('#clientid', "?module=clients&action=getClients&format=json", 'Choose client');
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'Choose sales person', 2);

        $('#branchid,#productcategory,#subcategory,#modelid,#department').select2({width: '100%'});
        $('#summary-table').DataTable({
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
    });

    function exportExcel(e) {
        let table = document.getElementById("summary-export-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Client Monthly sales summary <?=$title?>.xlsx`, // fileName you could use any name
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
</script>
