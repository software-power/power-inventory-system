<div class="modal fade" id="client-document-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="client-document-modal"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title productName">Documents</h4>
            </div>
            <form action="<?= url('clients', 'attach_document') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="clientid" class="clientid">
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-md">
                        <h5>Client: <span class="text-primary clientname"></span></h5>
                        <button type="button" class="btn btn-default btn-sm add-btn" onclick="add_document()"><i class="fa fa-plus"></i> Add document
                        </button>
                    </div>
                    <div style="max-height: 60vh;overflow-y: auto">
                        <table class="table table-bordered" style="font-size: 10pt">
                            <thead>
                            <tr>
                                <th style="width: 40%">Document type</th>
                                <th>File</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <select name="docid[]" class="form-control input-sm docid" required onchange="assign_document_name(this)">
                                        <option value="" selected disabled>-- choose document --</option>
                                        <? foreach (Documents::$staticClass->getAllActive() as $d) { ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                        <? } ?>
                                    </select>
                                    <input type="hidden" name="sdocid[]" class="sdocid">
                                    <input type="hidden" name="document_action[]" class="document_action" value="new">
                                </td>
                                <td>
                                    <input type="file" name="" class="form-control input-sm document-file"
                                           accept="application/pdf,image/jpeg,image/png"
                                           onchange="$(this).closest('tr').find('.document_action').val('new')" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm disable-btn" title="disable"
                                            onclick="remove_document(this)">
                                        <i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-sm save-btn">Save</button>
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let client_document_modal = $('#client-document-modal');

    $(client_document_modal).on('show.bs.modal', function (e) {
        let source = $(e.relatedTarget);
        let modal = $(this);
        let clientid = source.data('clientid');
        let approved = source.data('approved') == 1;
        console.log('approved: ',approved);
        $(modal).find('.clientid').val('').val(clientid);
        $(modal).find('.clientname').text('').text(source.data('clientname'));


        $(client_document_modal).find('tbody').empty();
        $.get(`?module=clients&action=getClientDocuments&format=json`, {clientid:clientid}, function (data) {
            $(client_document_modal).find('tbody').empty();
            let result = JSON.parse(data);
            if (result.status === 'success') {
                $.each(result.data, function (i, item) {
                    let delete_btn = `
                                    <button type="button" class="btn btn-warning btn-sm disable-btn" title="disable"
                                         onclick="remove_document(this)">
                                     <i class="fa fa-trash"></i></button>
                    `;
                    let row = `<tr>
                                <td>
                                    <input type="text" readonly class="form-control input-sm" value="${item.documentname}">
                                    <input type="hidden" name="docid[]" class="docid" value="${item.docid}">
                                    <input type="hidden" name="sdocid[]" class="sdocid" value="${item.id}">
                                    <input type="hidden" name="document_action[]" class="document_action" value="">
                                </td>
                                <td>
                                    <a href="${item.path}" target="_blank" class="btn btn-info btn-sm" title="view document">
                                       <i class="fa fa-eye"></i> view</a>
                                </td>
                                <td>
                                    ${!approved ? delete_btn : ''}
                                </td>
                            </tr>`;
                    $(client_document_modal).find('tbody').append(row);
                });
            } else {

            }
        });
    });


    function add_document() {
        let doc_types = `
                        <? foreach (Documents::$staticClass->getAllActive() as $d) { ?>
                             <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                         <? } ?>
        `;
        let row = `<tr>
                                <td>
                                    <select name="docid[]" class="form-control input-sm docid" required onchange="assign_document_name(this)">
                                        <option value="" selected disabled>-- choose document --</option>
                                        ${doc_types}
                                    </select>
                                    <input type="hidden" name="sdocid[]" class="sdocid">
                                    <input type="hidden" name="document_action[]" class="document_action" value="new">
                                </td>
                                <td>
                                    <input type="file" name="" class="form-control input-sm document-file"
                                                   accept="application/pdf,image/jpeg,image/png" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm disable-btn" title="disable"
                                            onclick="remove_document(this)">
                                        <i class="fa fa-trash"></i></button>
                                </td>
                            </tr>`;
        $(client_document_modal).find('tbody').append(row);
    }

    function assign_document_name(obj) {
        let tr = $(obj).closest('tr');
        let docid = $(obj).val();
        let name = docid ? `file${docid}` : '';
        $(tr).find('.document-file').attr('name', name);
    }

    function remove_document(obj) {
        let tr = $(obj).closest('tr');
        let sdocid = $(tr).find('.sdocid').val();
        if (sdocid) {
            $(tr).find('.document_action').val('remove');
            $(tr).hide();
        } else {
            $(tr).remove();
        }
    }
</script>