<header class="page-header">
    <h2>Suppliers Outstanding Payments</h2>
</header>

<div class="modal fade" id="search-modal" role="dialog" aria-labelledby="search-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <input type="hidden" name="module" value="suppliers">
                <input type="hidden" name="action" value="payment_list">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Search</h4>
                </div>
                <div class="modal-body">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            Branch:
                            <select name="search[branchid]" class="form-control">
                                <? foreach ($branches as $b) { ?>
                                    <option <?=selected($currentBranch['id'], $b['id'])?> value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            Location:
                            <select id="locationid" name="search[locationid]" class="form-control"> </select>
                        </div>
                        <div class="col-md-4">
                            Supplier:
                            <select id="supplierid" name="search[supplierid]" class="form-control"> </select>
                        </div>
                    </div>
                    <div class="row mt-md">
                        <div class="col-md-4">
                            From:
                            <input type="date" name="search[fromdate]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            To:
                            <input type="date" name="search[todate]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <div  class="checkbox checkbox-success ml-xlg d-flex align-items-center">
                                <label>
                                    <input id="enable-tra" type="checkbox" name="search[outstanding_only]" style="height: 40px;width: 40px;">
                                    <span class="ml-xlg">With Outstanding only</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row mt-md">
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-danger btn-block" data-dismiss="modal"><i
                                        class="fa fa-search"></i>
                                CANCEL
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="reset" class="btn btn-success btn-block"><i
                                        class="fa fa-refresh"></i>
                                RESET
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block"><i
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


<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="panel-title">Suppliers Payments</h2>
                    <p>Filter: <span class="text-primary"><?= $title ?></span></p>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <button type="button" data-toggle="modal" data-target="#search-modal" class="btn"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                    <a href="<?= url('suppliers', 'outstanding_detailed') ?>" class="btn"> <i
                                class="fa fa-plus"></i> Make Payment</a>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover mb-none" id="userTable" style="font-size:13px">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier name</th>
                        <th class="text-right">Full Amount (<?=$base_currency['name']?> )</th>
                        <th class="text-right">Paid Amount (<?=$base_currency['name']?> )</th>
                        <th class="text-right">Outstanding Amount (<?=$base_currency['name']?> )</th>
                        <th class="text-right">Advance Balance (<?=$base_currency['name']?> )</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($supplierOutstanding as $id => $R) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $R['suppliername'] ?></td>
                            <td class="text-right"><?= formatN($R['full_amount']) ?></td>
                            <td class="text-right text-success"><?= formatN($R['paid_amount']) ?></td>
                            <td class="text-right text-danger"><?= formatN($R['outstanding_amount']) ?></td>
                            <td class="text-right text-success"><?= formatN($R['advance_balance']) ?></td>
                            <td>
                                <div class="btn-group dropleft">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="<?= url('suppliers', 'outstanding_detailed', ['supplierid'=> $R['supplierid'],'branchid'=>$currentBranch['id']]) ?>"
                                           title="Payment List"><i class="fa fa-money"></i> Make payment</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="6" class="text-center">
                            <h4>TOTAL OUTSTANDING: <span
                                        class="text-danger text-weight-bold"><?= formatN($total_outstanding) ?></span>
                            </h4>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>
<script src="assets/js/quick_adds.js"></script>
<script>
    $(function () {
        initSelectAjax('#locationid', "?module=locations&action=getLocations&format=json", 'Location', 2);
        initSelectAjax('#supplierid', "?module=suppliers&action=getSuppliers&format=json", 'Supplier', 2);
    });
</script>