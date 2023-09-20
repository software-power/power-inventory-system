<style media="screen">

    .formholder h5 {
        font-size: 15px;
        font-weight: 600;
    }

    .panelControl {
        float: right;
    }


    .dropleft .dropdown-menu {
        top: 0;
        right: 100%;
        left: auto;
        margin-top: 0;
        margin-right: .125rem;
    }

    a.dropdown-item {
        text-decoration: none;
    }

    .dropdown-item {
        display: block;
        width: 100%;
        padding: .25rem 1.5rem;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }

    .table-responsive {
        min-height: 223px;
    }

    .select2-container {
        border: 1px solid #dadada;
        border-radius: 5px;
    }
</style>
<header class="page-header">
    <? if ($SR_MODE) { ?>
        <h2>Salesperson Summary SR</h2>
    <? } else { ?>
        <h2>Salesperson Summary</h2>
    <? } ?>
</header>

<div class="row d-flex justify-content-center">
    <div class="col-md-8">
        <section class="panel">
            <header class="panel-heading">
                <div class="panelControl">
                    <button type="button" class="btn" title="Home" data-toggle="modal"
                            data-target="#search-modal"><i
                                class="fa fa-search"></i> Open filter
                    </button>
                    <a class="btn" href="?module=home&action=index" title="Home"> <i class="fa fa-home"></i> Home</a>
                </div>
                <? if ($SR_MODE) { ?>
                    <h2 class="panel-title">Salesperson Summary SR</h2>
                <? } else { ?>
                    <h2 class="panel-title">Salesperson Summary</h2>
                <? } ?>
            </header>
            <div class="panel-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8">
                        <form>
                            <input type="hidden" name="module" value="reports">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <div class="row">
                                <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                    <div class="col-md-4">
                                        Order or Invoice By:
                                        <? if (Users::can(OtherRights::approve_other_credit_invoice)) { ?>
                                            <select id="userid" name="createdby" class="form-control"></select>
                                        <? } else { ?>
                                            <input type="text" readonly class="form-control" value="<?= $_SESSION['member']['name'] ?>">
                                            <input type="hidden" name="createdby" value="<?= $_SESSION['member']['id'] ?>">
                                        <? } ?>
                                    </div>
                                <? } ?>
                                <div class="col-md-3">
                                    From:
                                    <input type="date" name="fromdate" class="form-control" value="<?= $fromdate ?>">
                                </div>
                                <div class="col-md-3">
                                    To:
                                    <input type="date" name="todate" class="form-control" value="<?= $todate ?>">
                                </div>
                                <div class="col-md-2 pt-lg">
                                    <button class="btn btn-success">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <p class="text-primary mt-md"><?= $title ?></p>
                <div class="table-responsive mt-lg">
                    <table class="table table-hover mb-none" style="font-size:10pt;" id="userTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice by</th>
                            <th class="text-right">Total Amount</th>
                            <th class="text-right">Total Paid</th>
                            <th class="text-right">Outstanding</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? $count = 1;
                        foreach ($salespersons as $index => $R) { ?>
                            <tr>
                                <td><?= $count ?></td>
                                <td><?= $R['issuedby'] ?></td>
                                <td class="text-right"><?= formatN($R['total_amount']) ?></td>
                                <td class="text-right text-success"><?= formatN($R['paid_amount']) ?></td>
                                <td class="text-right text-danger"><?= formatN($R['pending_amount']) ?></td>
                            </tr>
                            <? $count++;
                        } ?>
                        </tbody>
                        <tfoot>
                        <tr style="font-size: 12pt;">
                            <td colspan="5" class="text-right">
                                <div class="d-flex justify-content-end">
                                    <div class="col-md-5">
                                        <table class="table table-bordered table-condensed" style="font-size: 10pt;">
                                            <thead>
                                            <tr>
                                                <td>Currency</td>
                                                <td class="text-right">Total Amount</td>
                                                <td class="text-right">Total Paid</td>
                                                <td class="text-right">Total Pending</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <? foreach ($totals['currency'] as $curencyname => $R) { ?>
                                                <tr>
                                                    <td><?= $curencyname ?></td>
                                                    <td class="text-right"><?= formatN($R['full_amount']) ?></td>
                                                    <td class="text-right text-success"><?= formatN($R['paid_amount']) ?></td>
                                                    <td class="text-right text-danger"><?= formatN($R['pending_amount']) ?></td>
                                                </tr>
                                            <? } ?>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td>Total in Base (<?= $basecurrency['name'] ?>)</td>
                                                <td class="text-right"><?= formatN($totals['base']['full_amount']) ?></td>
                                                <td class="text-right text-success">-</td>
                                                <td class="text-right text-danger"><?= formatN($totals['base']['pending_amount']) ?></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="assets/js/quick_adds.js"></script>
<script type="text/javascript">
    $(function () {
        initSelectAjax('#userid', "?module=users&action=getUser&format=json", 'User', 2);
    });


</script>
