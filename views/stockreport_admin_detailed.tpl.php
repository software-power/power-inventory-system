<link rel="stylesheet" type="text/css" href="./assets/DataTables/datatables.min.css"/>
<script type="text/javascript" src="./assets/DataTables/datatables.min.js"></script>
<style media="screen">
    div.dataTables_wrapper div.dataTables_filter input {
        width: 100%;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0;
    }

    .panel-actions a, .panel-actions .panel-action {
        font-size: 21px;
    }

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

    .select2-container--default .select2-selection--single {
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

    .for-formanage {
        border: 1px solid #47a447;
        padding: 9px;
        height: auto;
        border-radius: 5px;
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
        <input type="hidden" name="action" value="stock_report_admin_detailed">
        <div id="filter_table">
            <div class="row row-height">
                <div class="col-md-6">
                    <? if ($_SESSION['member']['role'] == 'Admin') { ?>
                        <select id="stockloc" class="form-control for-input" name="stocklocation">
                            <option selected value="">Stock location</option>
                        </select>
                    <? } else { ?>
                        <input type="text" readonly class="form-control" value="<?= $location['name'] ?>">
                        <input type="hidden" class="form-control" value="<?= $location['id'] ?>">
                    <? } ?>
                </div>
                <div class="col-md-6">
                    <select class="form-control for-input" name="productcategoryid">
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
                    <select id="stockloc" class="form-control for-input" name="brand">
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
                    <select id="stockloc" class="form-control for-input" name="depart">
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
                    <select id="stockloc" class="form-control for-input" name="category">
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
                <div class="col-md-4">
                    <label for="">Expire Before</label>
                    <input type="date" class="form-control" name="expirebefore">
                </div>
                <div class="col-md-4">
                    <label for="">Expire After</label>
                    <input type="date" class="form-control" name="expireafter">
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
            <h2 class="panel-title">Detailed Admin Stock Report</h2>
            <p><strong class="text-danger"><?= $location['name']; ?></strong></p>
            <p class="text-primary" style="font-size: 11pt;"><?= $title ?></p>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:13px;" id="printing_area">
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
                        <th>Cost Price</th>
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
                                <td><?= formatN($list['costprice']) ?></td>
                                <td><?= $list['expire_date'] ? $list['batch_no'] : '-' ?></td>
                                <td><?= $list['expire_date'] ? fDate($list['expire_date']) : '-' ?></td>
                                <td class="text-center <?= $list['expire_date'] ? ($list['expire_remain_days'] < 1 ? 'text-danger' : '') : '-' ?>">
                                    <?= $list['expire_date'] ? $list['expire_remain_days'] : '-' ?>
                                </td>
                                <td style="text-align:center"><strong><?= $list['total'] ?></strong></td>
                                <td><?= $list['unitName'] ?></td>
                                <td><?= $list['total'] / $list['bulkRate'] ?> <?= $list['bulkUnit'] ?></td>
                            </tr>
                            <? $count++;
                        } else {
                            foreach ($list['batches'] as $ins => $batch) { ?>
                                <tr style="background-color:<?= $R['color'] ?>">
                                    <td><?= $count ?></td>
                                    <td><?= $list['id'] ?></td>
                                    <td><?= $list['name'] ?></td>
                                    <td><?= $list['generic_name'] ?></td>
                                    <td><?= $list['catName'] ?></td>
                                    <td><?= $list['productcategoryname'] ?></td>
                                    <td><?= $list['subcategoryname'] ?></td>
                                    <td><?= $list['brandName'] ?></td>
                                    <td><?= $list['departName'] ?></td>
                                    <td><?= $list['costprice'] ?>/=</td>
                                    <td><?= $batch['batch_no'] ?></td>
                                    <td><?= fDate($batch['expire_date']) ?></td>
                                    <td class="text-center <?= $batch['expire_remain_days'] < 1 ? 'text-danger' : '' ?>">
                                        <?= $batch['expire_remain_days'] ?>
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
<script type="text/javascript">
    $(function () {
        $('#stockloc').select2({
            width: '100%', minimumInputLength: 3,
            ajax: {
                url: "?module=locations&action=getLocations&format=json",
                dataType: 'json',
                delay: 250,
                quietMillis: 200,
                data: function (term) {
                    return {search: term};
                },
                results: function (data, page) {
                    return {result: data};
                }
            }
        });
    })

    $('#openModel').on('click', function () {
        $('#formHolder').show('slow');
        $('#formModel').show('slow');
        /*$('html, body').css({
        overflow: 'hidden',
        height: '100%'
        });*/
    })

    $('#closeSearchModel').on('click', function () {
        $('#formHolder').hide('slow');
        $('#formModel').hide('slow');
        /*$('html, body').css({
        overflow: 'auto',
        height: 'auto'
        });*/
    })

    // $(document).ready(function(){
    //  $('#printing_area').DataTable({
    // 	 dom: '<"top"fB>t<"bottom"ip>',
    // 	 colReorder:true,
    // 	 keys:true,
    // 	 buttons: [
    // 		 'copyHtml5', 'excelHtml5', 'pdfHtml5','csvHtml5','print'],
    // 	 <?if($_GET['status']){?>
    // 	 title:'<?=$_GET['status']?>',
    // 	 <?}?>
    //  });
    // })

</script>
