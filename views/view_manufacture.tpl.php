<style>
    th.stick {
        position: sticky;
        top: 100px; /* 0px if you don't have a navbar, but something is required */
        background: grey;
        color: #fff;
        z-index: 1000;
    }
</style>
<header class="page-header">
    <h2>View Stock Manufacture</h2>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-lg-11 col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Manufacture No: <span class="text-primary" style="font-weight: bold"><?= $manufacture['id'] ?></span></h4>
                    <div class="d-flex align-items-center">
                        <div class="loading_spinner" style="display:none; ">
                            <object data="images/loading_spinner.svg" type="image/svg+xml" height="40" width="40"></object>
                        </div>
                        <? if (!$manufacture['approver']) { ?>
                            <? if (Users::can(OtherRights::approve_manufacture)) { ?>
                                <form class="m-none p-none" onsubmit="return show_spinner(this)">
                                    <input type="hidden" name="module" value="stocks">
                                    <input type="hidden" name="action" value="approve_manufacture">
                                    <input type="hidden" name="manufactureno" value="<?= $manufacture['id'] ?>">
                                    <button class="btn btn-success">Approve</button>
                                </form>
                            <? } ?>
                            <a href="<?= url('stocks', 'manufacture_stock', ['manufactureno' => $manufacture['id']]) ?>"
                               class="btn btn-warning ml-sm"><i class="fa fa-pencil"></i> Edit</a>
                        <? } ?>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div>Manufacture no: <span class="text-primary"><?= $manufacture['id'] ?></span></div>
                        <div>Location: <span class="text-primary"><?= $manufacture['locationname'] ?> - <?= $manufacture['branchname'] ?></span></div>
                        <div>Date: <span class="text-primary"><?= fDate($manufacture['doc'], 'd F Y H:i') ?></span></div>
                        <div>Issued By: <span class="text-primary"><?= $manufacture['issuedby'] ?></span></div>
                        <div>Status:
                            <? if ($manufacture['manufacture_status'] == 'approved') { ?>
                                <span class="text-success">Approved by <?= $manufacture['approver'] ?>, <?= fDate($manufacture['approvedate'], 'd M Y H:i') ?></span>
                            <? } elseif ($manufacture['manufacture_status'] == 'not-approved') { ?>
                                <span class="text-muted">not approved</span>
                            <? } else { ?>
                                <span class="text-rosepink">canceled</span>
                            <? } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <textarea readonly class="form-control text-sm" rows="2"><?= $manufacture['remarks'] ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 60vh">
                    <table class="table table-bordered mt-xl" style="font-size: 9pt">
                        <thead>
                        <tr>
                            <th class="stick" style="top: 0; ">#</th>
                            <th class="stick" style="top: 0; ">Barcode</th>
                            <th class="stick" style="top: 0;width: 30% ">Raw Materials</th>
                            <th class="stick" style="top: 0; ">Qty</th>
                            <th class="stick" style="top: 0; ">Unit Costprice</th>
                            <th class="stick" style="top: 0; ">Total Costprice</th>
                            <th class="stick" style="top: 0; ">End Products</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($manufacture['details'] as $R) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $R['barcode_office'] ?: $R['barcode_manufacture'] ?></td>
                                <td>
                                    <div><?= $R['productname'] ?></div>
                                    <div class="text-muted"><?= $R['description'] ?></div>
                                    <? if ($R['trackserialno']) { ?>
                                        <div class="row d-flex justify-content-end">
                                            <div class="col-md-8 col-xl-6">
                                                <h6>Serial Nos:</h6>
                                                <div style="max-height: 200px;overflow-y: auto;padding:2px;">
                                                    <table class="table table-bordered table-condensed batches" style="font-size: 8pt">
                                                        <thead>
                                                        <tr>
                                                            <th style="width: 15%">#</th>
                                                            <th>Number</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <? $count = 1;
                                                       foreach ($R['serialnos'] as $s) { ?>
                                                            <tr>
                                                                <td><?= $count ?></td>
                                                                <td><?= $s['number'] ?></td>
                                                            </tr>
                                                            <? $count++;
                                                        } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <? } ?>
                                </td>
                                <td><?= $R['qty'] ?></td>
                                <td class="text-right"><?= formatN($R['costprice']) ?></td>
                                <td class="text-right"><?= formatN($R['total_raw_material_costprice']) ?></td>
                                <td>
                                    <div>Total End Products Cost: <span class="text-danger text-weight-semibold"><?=formatN($R['overall_end_products_costprice'])?></span></div>
                                    <table class="table table-bordered table-condensed" style="font-size: 9pt;">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Barcode</th>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Unit Costprice</th>
                                            <th>Total Costprice</th>
                                            <th>Quick Price inc</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? $ecount = 1;
                                        foreach ($R['end_products'] as $e) { ?>
                                            <tr>
                                                <td><?= $ecount++ ?></td>
                                                <td><?= $e['barcode_office'] ?: $e['barcode_manufacture'] ?></td>
                                                <td>
                                                    <div><?= $e['productname'] ?></div>
                                                    <div class="text-muted"><?= $e['description'] ?></div>
                                                </td>
                                                <td><?= $e['qty'] ?></td>
                                                <td class="text-right"><?= formatN($e['costprice']) ?></td>
                                                <td class="text-right"><?= formatN($e['end_total_costprice']) ?></td>
                                                <td class="text-right"><?= formatN($e['quickprice']) ?></td>
                                            </tr>
                                        <? } ?>
                                        </tbody>
                                    </table>
                                </td>
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
<script src="assets/js/quick_adds.js"></script>
<script>
    function show_spinner(obj) {
        $('.loading_spinner').show();
        $(obj).find('button').prop('disabled', true);
    }
</script>
