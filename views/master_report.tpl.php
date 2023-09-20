<div id="master-report" class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h2 class="panel-title">Master Report</h2>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-10">
                        <form id="search-form" class="m-none d-flex align-items-center">
                            <input type="hidden" name="module" value="reports">
                            <input type="hidden" name="action" value="master_report">
                            <div>From:</div>
                            <input type="date" name="fromdate" class="form-control ml-sm" value="<?= $fromdate ?>">
                            <div class="ml-sm">To:</div>
                            <input type="date" name="todate" class="form-control ml-sm" value="<?= $todate ?>">
                            <div class="ml-sm">Currency:</div>
                            <select name="currencyid" class="form-control">
                                <option value="">-- All --</option>
                                <? foreach ($currencies as $c) { ?>
                                    <option <?= selected($selectedCurrency['id'], $c['id']) ?> value="<?= $c['id'] ?>"><?= $c['name'] ?>
                                        - <?= $c['description'] ?></option>
                                    <?
                                } ?>
                            </select>
                            <div class="ml-sm">Branch:</div>
                            <select name="branchid" class="form-control">
                                <option value="">-- All branches --</option>
                                <? foreach ($branches as $b) { ?>
                                    <option <?= selected($currentBranch['id'], $b['id']) ?>
                                            value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                            <div class="ml-sm">Location:</div>
                            <select id="locationid" name="locationid" class="form-control">
                                <? if ($location) { ?>
                                    <option value="<?= $location['id'] ?>"><?= $location['name'] ?></option>
                                <? } ?>
                            </select>
                            <button class="btn btn-success ml-sm"><i class="fa fa-search"></i></button>
                            <button type="button" class="btn btn-warning ml-sm" title="reset" onclick="resetSearchForm()">
                                <i class="fa fa-recycle"></i></button>
                            <button type="button" class="btn btn-primary ml-sm" title="Capture Image" onclick="printScreen()">
                                <i class="fa fa-camera"></i></button>
                        </form>
                        <p class="p-md">Filter: <span class="text-primary"><?= $title ?></span></p>
                    </div>
                </div>
                <div class="row mt-xlg">
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="font-size: 9pt;">
                                <thead>
                                <tr>
                                    <th rowspan="2">Sales Type</th>
                                    <th rowspan="2">Count</th>
                                    <th colspan="<?= count($salesPaymentType['currencies']) ?>" class="text-center">Sale amount</th>
                                    <th colspan="<?= count($salesPaymentType['currencies']) ?>" class="text-center">Outstanding amount</th>
                                </tr>
                                <tr>
                                    <? foreach ($salesPaymentType['currencies'] as $currencyname) { ?>
                                        <td class="text-right"><?= $currencyname ?></td>
                                    <? } ?>
                                    <? foreach ($salesPaymentType['currencies'] as $currencyname) { ?>
                                        <td class="text-right"><?= $currencyname ?></td>
                                    <? } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <? foreach ($salesPaymentType['type'] as $type => $R) { ?>
                                    <tr>
                                        <td><?= ucfirst($type) ?></td>
                                        <td class="text-right"><?= $R['count'] ?></td>
                                        <? foreach ($salesPaymentType['currencies'] as $currencyname) { ?>
                                            <td class="text-right"><?= $R['amounts']['sale_amount'][$currencyname] ? formatN($R['amounts']['sale_amount'][$currencyname]) : '-' ?></td>
                                        <? } ?>
                                        <? foreach ($salesPaymentType['currencies'] as $currencyname) { ?>
                                            <td class="text-right"><?= $R['amounts']['outstanding_amount'][$currencyname] ? formatN($R['amounts']['outstanding_amount'][$currencyname]) : '-' ?></td>
                                        <? } ?>
                                    </tr>
                                <? } ?>
                                </tbody>
                            </table>
                            <table class="table table-bordered mt-md" style="font-size: 10pt;">
                                <thead>
                                <tr>
                                    <th rowspan="2">Credit Notes</th>
                                    <th rowspan="2" class="text-center">Count</th>
                                    <th colspan="<?= count($creditNotes['currencies']) ?>" class="text-center">Amount</th>
                                </tr>
                                <tr>
                                    <? foreach ($creditNotes['currencies'] as $currencyname) { ?>
                                        <td class="text-right"><?= $currencyname ?></td>
                                    <? } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Today</td>
                                    <td class="text-center"><?= $creditNotes['today']['count'] ?></td>
                                    <? foreach ($creditNotes['currencies'] as $currencyname) { ?>
                                        <td class="text-right"><?= $creditNotes['today'][$currencyname]?formatN($creditNotes['today'][$currencyname]):'-' ?></td>
                                    <? } ?>
                                </tr>
                                <tr>
                                    <td>Previous</td>
                                    <td class="text-center"><?= $creditNotes['previous']['count'] ?></td>
                                    <? foreach ($creditNotes['currencies'] as $currencyname) { ?>
                                        <td class="text-right"><?= $creditNotes['previous'][$currencyname]?formatN($creditNotes['previous'][$currencyname]):'-' ?></td>
                                    <? } ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <table class="table table-bordered mt-xlg" style="font-size: 10pt;">
                            <thead>
                            <tr>
                                <th>GRN details</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Goods Purchases GRN</td>
                                <td class="text-right"><?= $grnDetails['purchases'] ? $baseCurrency['name'] . ' ' . formatN($grnDetails['purchases']) : '-' ?></td>
                            </tr>
                            <tr>
                                <td>Supplier Outstanding</td>
                                <td class="text-right text-rosepink"><?= $grnDetails['outstanding_amount'] ? $baseCurrency['name'] . ' ' . formatN($grnDetails['outstanding_amount']) : '-' ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8">
                        <ul class="nav nav-tabs">
                            <? $first = true;
                            foreach ($receivedCash as $currency => $_) { ?>
                                <li class="<?= $first ? 'active' : '' ?>"><a data-toggle="tab" href="#<?= $currency ?>"><?= $currency ?></a></li>
                                <? $first = false;
                            } ?>
                        </ul>
                        <div class="tab-content">
                            <? $first = true;
                            foreach ($receivedCash as $currency => $details) { ?>
                                <div id="<?= $currency ?>" class="tab-pane fade <?= $first ? 'in active' : '' ?>">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" style="font-size: 10pt;">
                                            <thead>
                                            <tr>
                                                <th colspan="6">Cash Collection (<span class="text-primary"><?= $currency ?></span>)</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th colspan="2" class="text-center">Received</th>
                                                <th colspan="2" class="text-center">Returns</th>
                                                <th rowspan="2" class="text-center" style="vertical-align: middle"
                                                    title="advance receipt + outstanding payment">Debtor Collection
                                                </th>
                                                <th rowspan="2" class="text-center" style="vertical-align: middle">Expense</th>
                                                <th rowspan="2" class="text-center" style="vertical-align: middle">Total Quick sale</th>
                                                <th rowspan="2" class="text-center" style="vertical-align: middle">Total Normal sale</th>
                                                <th rowspan="2" class="bg-rosepink text-white text-center" style="vertical-align: middle">Total</th>
                                            </tr>
                                            <tr>
                                                <th>Staff</th>
                                                <th title="Cash collected from quick sales" class="text-center">Quick sale</th>
                                                <th title="Cash collected from normal sales" class="text-center">Normal sale</th>
                                                <th title="Cash returned in quick sales" class="text-center">Quick sale</th>
                                                <th title="Cash returned in normal sales" class="text-center">Normal sale</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <? foreach ($details['users'] as $name => $R) { ?>
                                                <tr>
                                                    <td><?= $name ?></td>
                                                    <td class="text-right"><?= $R['quick'] ? formatN($R['quick']) : '-' ?></td>
                                                    <td class="text-right"><?= $R['detailed'] ? formatN($R['detailed']) : '-' ?></td>
                                                    <td class="text-right text-danger"><?= $R['quick_return'] ? formatN($R['quick_return']) : '-' ?></td>
                                                    <td class="text-right text-danger"><?= $R['detailed_return'] ? formatN($R['detailed_return']) : '-' ?></td>
                                                    <td class="text-right"><?= $R['debtor_collection'] ? formatN($R['debtor_collection']) : '-' ?></td>
                                                    <td class="text-right text-danger"><?= $R['expense'] ? formatN($R['expense']) : '-' ?></td>
                                                    <td class="text-right"><?= $R['total_quick'] ? formatN($R['total_quick']) : '-' ?></td>
                                                    <td class="text-right"><?= $R['total_detailed'] ? formatN($R['total_detailed']) : '-' ?></td>
                                                    <td class="text-right"><?= formatN($R['staff_total']) ?></td>
                                                </tr>
                                            <? } ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th class="bg-rosepink text-white">Total</th>
                                                <th class="text-right"><?= $totals[$currency]['quick_total'] ? formatN($totals[$currency]['quick_total']) : '-' ?></th>
                                                <th class="text-right"><?= $totals[$currency]['detailed_total'] ? formatN($totals[$currency]['detailed_total']) : '-' ?></th>
                                                <th class="text-right text-danger"><?= $totals[$currency]['quick_return_total'] ? formatN($totals[$currency]['quick_return_total']) : '-' ?></th>
                                                <th class="text-right text-danger"><?= $totals[$currency]['detailed_return_total'] ? formatN($totals[$currency]['detailed_return_total']) : '-' ?></th>
                                                <th rowspan="2" class="text-right" style="vertical-align: bottom;"><?= $totals[$currency]['debtor_total'] ? formatN($totals[$currency]['debtor_total']) : '-' ?></th>
                                                <th rowspan="2" class="text-right text-danger" style="vertical-align: bottom;"><?= $totals[$currency]['expense_total'] ? formatN($totals[$currency]['expense_total']) : '-' ?></th>
                                                <th class="text-right"><?= $totals[$currency]['total_quick_total'] ? formatN($totals[$currency]['total_quick_total']) : '-' ?></th>
                                                <th class="text-right"><?= $totals[$currency]['total_detailed_total'] ? formatN($totals[$currency]['total_detailed_total']) : '-' ?></th>
                                                <th class="text-right"><?= $totals[$currency]['total_staff_total'] ? formatN($totals[$currency]['total_staff_total']) : '-' ?></th>
                                            </tr>
                                            <tr>
                                                <th class="bg-rosepink text-white">Gross Total</th>
                                                <? //for readability
                                                $amountTotal = $totals[$currency]['quick_total'] + $totals[$currency]['detailed_total'];
                                                $returnTotal = $totals[$currency]['quick_return_total'] + $totals[$currency]['detailed_return_total'];
                                                $allAmountTotal = $totals[$currency]['total_quick_total'] + $totals[$currency]['total_detailed_total'];
                                                ?>
                                                <th colspan="2" class="text-right"><?= $amountTotal ? formatN($amountTotal) : '-' ?></th>
                                                <th colspan="2" class="text-right text-danger"><?= $returnTotal ? formatN($returnTotal) : '-' ?></th>
                                                <th colspan="2" class="text-right"><?= $allAmountTotal ? formatN($allAmountTotal) : '-' ?></th>
                                                <th class="text-right"><?= $totals[$currency]['total_staff_total'] ? formatN($totals[$currency]['total_staff_total']) : '-' ?></th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <? $first = false;
                            } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Overall Collection</h5>
                                <div class="table-responsive" style="min-height: 100px;">
                                    <table class="table table-bordered" style="font-size: 10pt;">
                                        <thead>
                                        <tr>
                                            <? foreach ($totals as $currency => $_) { ?>
                                                <th><?= $currency ?></th>
                                            <? } ?>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr>
                                            <? foreach ($totals as $_ => $total) { ?>
                                                <td><?= formatN($total['total_staff_total']) ?></td>
                                            <? } ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div>
                            <ul class="nav nav-tabs">
                                <? $first = true;
                                foreach ($overallOutstanding as $currency => $R) { ?>
                                    <li class="<?= $first ? 'active' : '' ?>">
                                        <a data-toggle="tab" href="#overall_<?= $currency ?>"><?= $currency ?></a></li>
                                    <? $first = false;
                                } ?>
                            </ul>
                            <div class="tab-content">
                                <? $first = true;
                                foreach ($overallOutstanding as $currency => $R) { ?>
                                    <div id="overall_<?= $currency ?>" class="tab-pane fade <?= $first ? 'in active' : '' ?>">
                                        <table class="table table-bordered mt-xlg" style="font-size: 10pt;">
                                            <thead>
                                            <tr>
                                                <th colspan="2">Overall Outstandings (<span class="text-primary"><?= $currency ?></span>)</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Sales Outstanding</th>
                                                <th class="text-center">Supplier Outstanding</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="text-center"><?= formatN($R['sales']) ?></td>
                                                <td class="text-center"><?= formatN($R['suppliers']) ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <? $first = false;
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script src="assets/js/html2canvas.min.js"></script>
<script>
    $(function () {
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", "Choose location", 2);
    });

    function resetSearchForm() {
        let searchForm = $('#search-form');
        $(searchForm).find('.form-control').val('').trigger('change');
        $(searchForm).find("input[type='date']").val('<?=TODAY?>');
        $(searchForm).find("select[name='branchid']").val('<?=$_SESSION['member']['branchid']?>');
    }

    function printScreen() {
        html2canvas(document.getElementById('master-report'), {
            scale: 4
        }).then(function (canvas) {
            // document.body.appendChild(canvas);
            saveAs(canvas.toDataURL(), `<?= CS_COMPANY ?> Master Report From <?= $fromdate ?> <?= $todate ? "To $todate" : "" ?>`);
        });
    }

    function saveAs(uri, filename) {
        let link = document.createElement('a');
        if (typeof link.download === 'string') {
            link.href = uri;
            link.download = filename;

            //Firefox requires the link to be in the body
            document.body.appendChild(link);

            //simulate click
            link.click();

            //remove the link when done
            document.body.removeChild(link);
        } else {
            window.open(uri);
        }
    }
</script>