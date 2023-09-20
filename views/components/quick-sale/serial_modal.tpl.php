<div class="modal fade serial-modal" id="serialModal-<?=$detail['stockid']?>" tabindex="-1" role="dialog" aria-labelledby="serialModal-<?=$detail['stockid']?>"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title productName">Serial No:  <span class="text-primary"><?=$detail['productname']?></span></h4>
            </div>
            <div class="modal-body">
                <p class="text-danger text-sm"><?=$detail['validate_serialno']?'Validates from Stock':'Enter manually'?></p>
                <div style="max-height: 60vh;overflow-y: auto">
                    <table class="table table-bordered" style="font-size: 10pt">
                        <thead>
                        <tr>
                            <td>#</td>
                            <td>Serial Number</td>
                            <td>Status</td>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>