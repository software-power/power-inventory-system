<style media="screen">
</style>
<header class="page-header">
    <h2>Batch-Wise Stock Report</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="stocks">
                <input type="hidden" name="action" value="<?= $action ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row row-height">
                        <div class="col-md-4">
                            Location:
                            <? if (STOCK_LOCATIONS) { ?>
                                <select id="stockloc" class="form-control" name="stocklocation">
                                    <? foreach ($branchLocations as $R) { ?>
                                        <option <?= selected($location['id'], $R['id']) ?> value="<?= $R['id'] ?>"><?= $R['name'] ?>
                                            - <?= $R['branchname'] ?></option>
                                    <? } ?>
                                </select>
                            <? } else { ?>
                                <input type="text" readonly class="form-control"
                                       value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                                <input type="hidden" class="form-control" name="stocklocation" value="<?= $location['id'] ?>">
                            <? } ?>
                        </div>
                        <div class="col-md-4">
                            Product:
                            <select id="productid" class="form-control for-input" name="productid">
                                <option selected value="">All products</option>

                            </select>
                        </div>
                        <div class="col-md-4">
                            Product Category:
                            <select id="productcategory" class="form-control for-input" name="productcategoryid">
                                <option selected value="">Product Category</option>
                                <? foreach ($productCategories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Subcategory:
                            <select id="subcategory" class="form-control for-input" name="subcategoryid">
                                <option selected value="">Subcategory</option>
                                <? foreach ($subcategories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Brand:
                            <select id="brand" class="form-control for-input" name="brand">
                                <option selected value="">Select Brand Name</option>
                                <?
                                foreach ($brands as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Department:
                            <select id="depart" class="form-control for-input" name="depart">
                                <option selected value="">Select Depatment</option>
                                <?
                                foreach ($depart as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Tax Category:
                            <select class="form-control for-input" name="category">
                                <option selected value="">Select TAX Category</option>
                                <?
                                foreach ($categories as $key => $D) { ?>
                                    <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="">Batch No.</label>
                            <input type="text" class="form-control" name="batchno" placeholder="Batch No.">
                        </div>
                        <div class="col-md-4">
                            <label for="">Expire Before</label>
                            <input type="date" class="form-control" name="expirebefore">
                        </div>
                        <div class="col-md-4">
                            <label for="">Expire After</label>
                            <input type="date" class="form-control" name="expireafter">
                        </div>
                    </div>
                    <div class="row mb-lg">
                        <div class="col-md-4">
                            <div class="checkbox" title="check this if you want to include expired batches">
                                <label>
                                    <input type="checkbox" name="with_expired" checked>
                                    With expired
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checkbox" title="check this if you want see only batch with stock qty">
                                <label>
                                    <input type="checkbox" name="with_stock" checked>
                                    With stock
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panelControl">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search-modal"><i
                            class="fa fa-search"></i> Open Search
                </button>
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
            </div>
            <h2 class="panel-title">Batch-Wise Stock Report</h2>
            <strong class="text-danger"><p><?= $location['name']; ?></p></strong>
            <p>Filter: <span class="ml-md text-primary" style="font-size: 10pt;"><?= $title ?></span></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:13px;" id="stock-detailed">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Barcode</th>
                        <th>Product name</th>
                        <? if (CS_SHOW_GENERIC_NAME) { ?>
                            <th>Generic name</th>
                        <? } ?>
                        <th>Tax</th>
                        <? if (CS_SHOW_CATEGORY) { ?>
                            <th>Category</th>
                            <th>Subcategory</th>
                        <? } ?>
                        <? if (CS_SHOW_BRAND) { ?>
                            <th>Brand</th>
                        <? } ?>
                        <? if (CS_SHOW_DEPARTMENT) { ?>
                            <th>Department</th>
                        <? } ?>
                        <th>Batch No.</th>
                        <th>Expire Date</th>
                        <th style="text-align:center">Remaining days</th>
                        <th style="text-align:center">Quantity</th>
                        <th>Unit</th>
                        <th>Bulk Unit</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($stocklist as $ins => $list) { ?>
                        <? if ($list['track_expire_date'] == 0) { ?>
                            <tr style="background-color:<?= $R['color'] ?>">
                                <td><?= $count ?></td>
                                <td><?= $list['barcode_office'] ?></td>
                                <td><?= $list['name'] ?></td>
                                <? if (CS_SHOW_GENERIC_NAME) { ?>
                                    <td><?= $list['generic_name'] ?></td>
                                <? } ?>
                                <td><?= $list['catName'] ?></td>
                                <? if (CS_SHOW_CATEGORY) { ?>
                                    <td><?= $list['productcategoryname'] ?></td>
                                    <td><?= $list['subcategoryname'] ?></td>
                                <? } ?>
                                <? if (CS_SHOW_BRAND) { ?>
                                    <td><?= $list['brandName'] ?></td>
                                <? } ?>
                                <? if (CS_SHOW_DEPARTMENT) { ?>
                                    <td><?= $list['departName'] ?></td>
                                <? } ?>
                                <td><?= $list['expire_date'] ? $list['batch_no'] : '-' ?></td>
                                <td><?= $list['expire_date'] ? fDate($list['expire_date']) : '-' ?></td>
                                <td class="text-center"> -</td>
                                <td style="text-align:center"><strong><?= $list['total'] ?></strong></td>
                                <td><?= $list['unitName'] ?></td>
                                <td><?= $list['total'] / $list['bulkRate'] ?> <?= $list['bulkUnit'] ?></td>
                            </tr>
                            <? $count++;
                        } else {
                            foreach ($list['batches'] as $ins => $batch) { ?>
                                <tr style="background-color:<?= $R['color'] ?>">
                                    <td><?= $count ?></td>
                                    <td><?= $list['barcode_office'] ?></td>
                                    <td><?= $list['name'] ?></td>
                                    <? if (CS_SHOW_GENERIC_NAME) { ?>
                                        <td><?= $list['generic_name'] ?></td>
                                    <? } ?>
                                    <td><?= $list['catName'] ?></td>
                                    <? if (CS_SHOW_CATEGORY) { ?>
                                        <td><?= $list['productcategoryname'] ?></td>
                                        <td><?= $list['subcategoryname'] ?></td>
                                    <? } ?>
                                    <? if (CS_SHOW_BRAND) { ?>
                                        <td><?= $list['brandName'] ?></td>
                                    <? } ?>
                                    <? if (CS_SHOW_DEPARTMENT) { ?>
                                        <td><?= $list['departName'] ?></td>
                                    <? } ?>
                                    <td><?= $batch['batch_no'] ?></td>
                                    <td><?= fDate($batch['expire_date']) ?></td>
                                    <td class="text-center <?= $batch['expire_remain_days'] < 1 ? 'text-danger' : '' ?>"
                                        data-order="<?= $batch['expire_remain_days'] ?? 0 ?>">
                                        <?= $batch['expire_remain_days'] <= 0
                                            ? fExpireDays($batch['expire_remain_days'])
                                            : $batch['expire_remain_days'] ?>
                                    </td>
                                    <td style="text-align:center"><strong><?= $batch['total'] ?></strong></td>
                                    <td><?= $list['unitName'] ?></td>
                                    <td><?= $list['total'] / $list['bulkRate'] ?> <?= $list['bulkUnit'] ?></td>
                                </tr>
                                <?
                                $count++;
                            }
                        } ?>
                        <?
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#productid', "?module=products&action=getProducts&format=json", "Choose product");
        $('#stockloc,#productcategory,#subcategory,#subcategory,#brand,#depart').select2({width: '100%'});

        $('#stock-detailed').DataTable({
            dom: '<"top"fBl>t<"bottom"ip>',
            colReorder: true,
            keys: true,
            buttons: ['excelHtml5', 'csvHtml5'],
        });
    });

</script>
