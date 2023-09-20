<style media="screen">
    .formholder h5 {
        font-size: 15px;
        font-weight: 600;
    }

    .for-input {
        padding: 8px;
        height: 40px;
        font-size: 14px;
        border: none;
        outline: none;
        margin-top: 2px;
    }

    .formModel {
        display: none;
        position: fixed;
        width: 100%;
        z-index: 14;
        background: rgba(238, 238, 238, 0.6196078431372549);
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        height: 100%;
    }

    .formholder {
        position: relative;
        display: none;
        z-index: 26;
        border-radius: 5px;
        padding: 24px;
        width: 100%;
        background: #ededee;
        /* height: 166px; */
        -webkit-box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
        -moz-box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
        box-shadow: 0px 4px 33px -4px rgba(0, 0, 0, 0.41);
    }

    .panelControl {
        float: right;
    }


    .row.row-height {
        height: 57px;
    }
</style>
<header class="page-header">
    <h2>Stock Report</h2>
</header>
<div id="formModel" class="formModel"></div>
<div id="formHolder" class="formholder">
    <h5>Search Query</h5>
    <form>
        <input type="hidden" name="module" value="stocks">
        <input type="hidden" name="action" value="expired_report">
        <div id="filter_table">
            <div class="row row-height">
                <div class="col-md-4">
                    <? if (STOCK_LOCATIONS) { ?>
                        <select id="stockloc" class="form-control" name="stocklocation">
                            <? foreach ($branchLocations as $R) {?>
                                <option <?=selected($location['id'], $R['id'])?> value="<?= $R['id'] ?>"><?= $R['name'] ?> - <?= $R['branchname'] ?></option>
                            <?}?>
                        </select>
                    <? } else { ?>
                        <input type="text" readonly class="form-control" value="<?= $location['name'] ?> - <?= $location['branchname'] ?>">
                        <input type="hidden" class="form-control" name="stocklocation" value="<?= $location['id'] ?>">
                    <? } ?>
                </div>
                <div class="col-md-4">
                    <select id="productid" class="form-control for-input" name="productid">
                        <option selected value="">All products</option>

                    </select>
                </div>
                <div class="col-md-4">
                    <select id="productcategory" class="form-control for-input" name="productcategoryid">
                        <option selected value="">All category</option>
                        <?
                        foreach ($productCategories as $key => $D) { ?>
                            <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                            <?
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row row-height">
                <div class="col-md-4">
                    <select id="brand" class="form-control for-input" name="brand">
                        <option selected value="" disabled>Select Brand Name</option>
                        <?
                        foreach ($brands as $key => $D) { ?>
                            <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                            <?
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="depart" class="form-control for-input" name="depart">
                        <option selected value="" disabled>Select Depatment</option>
                        <?
                        foreach ($depart as $key => $D) { ?>
                            <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                            <?
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-control for-input" name="category">
                        <option selected value="" disabled>Select TAX Category</option>
                        <?
                        foreach ($categories as $key => $D) { ?>
                            <option value="<?= $D['id'] ?>"><?= $D['name'] ?></option>
                            <?
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row mb-lg">
                <div class="col-md-4">
                    <label for="">Batch No.</label>
                    <input type="text" class="form-control" name="batchno" placeholder="Batch No.">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div id="closeSearchModel" class="btn btn-danger btn-block"><i CLASS="fa fa-close"></i> CANCEL</div>
                </div>
                <div class="col-md-4">
                    <button type="reset" class="btn btn-success btn-block"><i class="fa fa-minus"></i> RESET
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-block" name="button"><i class="fa fa-search"></i>
                        SEARCH
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="panelControl">
                <button id="openModel" class="btn" href="?module=home&action=index" title="Home"><i
                            class="fa fa-search"></i> Open Search
                </button>
                <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
            </div>
            <h2 class="panel-title">Expired Stock Report</h2>
            <strong class="text-danger"><?= $location['name']; ?></strong>
            <p>Filter: <span class="ml-md text-primary" style="font-size: 10pt;"><?= $title ?></span></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:13px;" id="userTable">
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
                        <? if ($list['track_expire_date'] == 1) {
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
                        }
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

        $('#stockloc').select2({width: '100%'});
        $('#productcategory').select2({width: '100%'});
        $('#subcategory').select2({width: '100%'});
        $('#brand').select2({width: '100%'});
        $('#depart').select2({width: '100%'});
    });

</script>
