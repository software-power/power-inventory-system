<style>
    .bank-details .bank {
        font-size: 8pt;
        width: 33.33%;
    }
</style>

<div class="bank-details mt-lg">
    <h5>Bank details</h5>
    <table class="table table-condensed table-bordered">
        <tbody>
        <? foreach (array_chunk($banks, 3) as $banks_row) {?>
            <tr>
                <? foreach ($banks_row as $b) {?>
                    <td class="bank" style="vertical-align: top">
                        <div class="text-weight-bold"><?= $b['name'] ?></div>
                        <div><?= $b['accname'] ?></div>
                        <div><?= $b['accno'] ?></div>
                    </td>
                <?}?>
            </tr>
        <?}?>
        </tbody>
    </table>
</div>