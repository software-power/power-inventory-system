<style>
    .input-error {
        border: 1px solid red;
    }
</style>
<header class="page-header">
    <h2>Add Serial No</h2>
</header>
<style>
    .big-checkbox {
        width: 20px;
        height: 20px;
    }
</style>
<div class="col-md-8 col-md-offset-2">
    <section class="panel">
        <header class="panel-heading">
            <div class="row">
                <div class="col-md-3">
                    <h2 class="panel-title">Add Serial No</h2>
                </div>
                <div class="col-md-4 col-md-offset-3">
                    <form class=" d-flex justify-content-end align-items-center">
                        <input type="text" autocomplete="off" class="form-control" name="grnid" placeholder="GRN no.">
                        <input type="hidden" name="module" value="grns">
                        <input type="hidden" name="action" value="add_serialno">
                        <button class="btn btn-success ml-lg">Search</button>
                    </form>
                </div>
            </div>
        </header>
        <div class="panel-body">
            <? if ($grnInfo) { ?>
                <h5>GRN No: <span class="text-primary"><?= $grnInfo['grnnumber'] ?></span></h5>
                <h5>Supplier: <span class="text-primary"><?= $grnInfo['suppliername'] ?></span></h5>
            <? } ?>

            <div class="table-responsive mt-md">
                <table class="table table-hover mb-none" id="userTable" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>In Stock Qty</th>
                        <th>Without serialno</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    if ($grnInfo['stock']) {
                        $count = 1;
                        foreach ($grnInfo['stock'] as $id => $stock) { ?>
                            <tr class="products">
                                <td><?= $count ?></td>
                                <td><?= $stock['productname'] ?></td>
                                <td class="qty"><?= $stock['qty'] ?></td>
                                <td class="stock"><?= $stock['current_stock_qty'] ?></td>
                                <td><?= $stock['current_stock_qty'] - $stock['serialnos_count'] ?></td>
                                <td>
                                    <a href="<?=url('serialnos', 'add_product_serialno',"grnid={$grnInfo['grnnumber']}&productid={$stock['prodid']}")?>" class="btn btn-default btn-sm"> Add serial no</a>
                                </td>
                            </tr>
                            <? $count++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td align="center" colspan="4">No Product(s) found requiring serial no</td>
                        </tr>
                    <? } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
    function addSerialNo(obj) {
        let requiredQty = parseInt($(obj).closest('.products').find('.qty').text());
        let parentModal = $(obj).closest('.modal');
        let availableSerialQty = $(parentModal).find(`table.serial-table tbody tr`).length; //find all serial which are not deleted

        if (requiredQty > availableSerialQty) {
            let row = `<tr>
                           <td class="rowno"></td>
                           <td>
                               <input type="text" name="number[]" class="form-control input-sm serial_number">
                           </td>
                           <td>
                               <button type="button" class="btn btn-warning btn-sm" onclick="deleteRow(this)">delete</button>
                           </td>
                       </tr>`;
            $(parentModal).find('table.serial-table tbody').append(row);
            row_counter(obj);
        } else {
            triggerError(`You can't add more than available quantity (${requiredQty})`);
        }
    }

    function row_counter(obj) {
        $(obj).closest('.modal').find('tbody tr').each(function (i, tr) {
            $(tr).find('.rowno').text(i + 1);
        });
    }

    function deleteRow(obj) {
        let tbody = $(obj).closest('tbody');
        $(obj).closest('tr').remove();
        row_counter(tbody);
    }

    function confirmSerialNo(obj) {
        let parentModal = $(obj).closest('.modal');
        let requiredSerialQty = parseInt($(obj).closest('.products').find('.qty').text());
        let valid = true;

        if (requiredSerialQty < $(parentModal).find(`input[name='state[]'][value!='delete']`).length) {
            triggerError(`Product qty (${requiredSerialQty}) dont match with entered serial qty!`, 5000);
            return false;
        }

        //check if empty
        $(parentModal).find('.serial_number').each(function () {
            let value = $.trim($(this).val());
            if (value == '' || value == null) {
                triggerError('Serial no is required', 2000);
                $(this).focus();
                valid = false;
                return false;
            }
        });

        if (!valid) return false;

        //check duplicate
        let serialNos = [];
        $(parentModal).find('.serial_number').each(function () {
            serialNos.push($(this).val());
        });

        let sorted = serialNos.slice().sort();

        let duplicates = [];
        for (let i = 0; i < sorted.length - 1; i++) {
            if (sorted[i] === sorted[i + 1]) {
                duplicates.push(sorted[i]);
            }
        }

        $(parentModal).find('.serial_number').each(function () {
            if ($.inArray($(this).val(), duplicates) !== -1) {
                $(this).addClass('input-error');
            } else {
                $(this).removeClass('input-error');
            }
        });

        if (duplicates.length > 0) {
            triggerError('Duplicate serial no found!');
            return false;
        }
    }
</script>
