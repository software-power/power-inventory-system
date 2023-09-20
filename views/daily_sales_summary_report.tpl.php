<style media="screen">
    .out-of-stock {
        background: #ff000057;
    }
</style>
<header class="page-header">
    <h2>Daily Sales Summary</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-10">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Daily Sales Summary</h2>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <form action="">
                            <input type="hidden" name="module" value="reports">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    Choose Branches:
                                    <select id="branchid" name="branchids[]" multiple class="form-control">
                                        <? foreach ($branchlist as $b) { ?>
                                            <option <?= in_array($b['id'], $branchids) ? 'selected' : '' ?>
                                                    value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-md">
                                <div class="col-md-3">
                                    Product:
                                    <select id="productid" name="productid" class="form-control"></select>
                                </div>
                                <div class="col-md-3">
                                    Brand:
                                    <select id="brandid" name="brandid" class="form-control">
                                        <option value="">-- All --</option>
                                        <? foreach ($brands as $b) { ?>
                                            <option <?= selected($b['id'], $brandid) ?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Category:
                                    <select id="productcategory" name="productcategoryid" class="form-control">
                                        <option value="">-- All --</option>
                                        <? foreach ($productCategories as $pc) { ?>
                                            <option <?= selected($pc['id'], $productcategoryid) ?>
                                                    value="<?= $pc['id'] ?>"><?= $pc['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    Subcategory:
                                    <select id="subcategory" name="subcategoryid" class="form-control">
                                        <option value="">-- All --</option>
                                        <? foreach ($productSubcategories as $ps) { ?>
                                            <option <?= selected($ps['id'], $subcategoryid) ?> value="<?= $ps['id'] ?>"><?= $ps['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    From:
                                    <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                                </div>
                                <div class="col-md-3">
                                    To:
                                    <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                                </div>
                                <div class="col-md-2 pt-lg">
                                    <button class="btn btn-success btn-block"> Search</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <p class="text-primary"><?= $title ?></p>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="stock-table" class="table table-hover table-condensed table-bordered mb-none" style="font-size:10pt;">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <? foreach ($branches as $b) { ?>
                                <th><?= $b['name'] ?></th>
                            <? } ?>
                            <th class="text-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $total_amount = 0;
                        foreach ($summary as $date => $R) { ?>
                            <tr>
                                <td data-sort="<?= strtotime($date) ?>"><?= $date ?></td>
                                <? foreach ($branches as $b) { ?>
                                    <td class="text-right"><?= $R['branches'][$b['name']] ? formatN($R['branches'][$b['name']]) : '' ?></td>
                                <? } ?>
                                <td class="text-right"><?= $R['total'] ? formatN($R['total']) : '' ?></td>
                            </tr>
                            <? $total_amount += $R['total'];
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr class="text-lg text-weight-semibold">
                            <td>TOTAL</td>
                            <td colspan="<?= (1 + count($branches)) ?>" class="text-right"><?= formatN($total_amount) ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <? if (!empty($summary)) { ?>
                    <div class="row mt-lg">
                        <div class="col-md-10">
                            <h4>Chart</h4>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-10">
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
        initSelectAjax('#productid', '?module=products&action=getProducts&format=json', 'choose product');
        $('#branchid,#brandid,#productcategory,#subcategory').select2();
        $('#stock-table').DataTable({
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

    function showChart() {
        let labels = [<?="'" . implode("','", array_keys($chart_data['labels'])) . "'"?>];
        console.log(labels);

        let datasets = [];
        //total sales line
        datasets.push({
            type:'line',
            label:`TOTAL DAY SALES`,
            data: [<?=implode(',', $chart_data['labels'])?>],
            // backgroundColor: 'red',
            borderColor: 'rgb(75, 192, 192)',
            borderWidth: 2,
            fill:true,
            lineTension: 0.3,
        });

        let random_color = '';
        <?foreach ($chart_data['branches'] as $branchname=>$amounts) { ?>
        random_color = getRandomColor();
        datasets.push({
            type:'bar',
            label:`<?=$branchname?>`,
            data: [<?=implode(',', $amounts)?>],
            backgroundColor: random_color,
            borderColor: random_color,
            borderWidth: 1,
            lineTension: 0.3,
        });
        <?}?>

        console.log(datasets);


        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
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

    function exportExcel(e) {
        let table = document.getElementById("stock-export-table");
        TableToExcel.convert(table, { // html code may contain multiple tables so here we are refering to 1st table tag
            name: `Branch Stock <?=$title?>.xlsx`, // fileName you could use any name
            sheet: {
                name: 'STOCKS' // sheetName
            }
        });
    }

    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
</script>