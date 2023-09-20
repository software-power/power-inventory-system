<header class="page-header">
    <h2>Clients Contacts</h2>
</header>

<div class="col-md-12">
    <section class="panel">
        <header class="panel-heading">
            <h2 class="panel-title">Clients Contacts</h2>
        </header>
        <div class="panel-body">
            <form>
                <input type="hidden" name="module" value="reports">
                <input type="hidden" name="action" value="contact">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-3">
                        <span>Search contact</span>
                        <input type="text" name="search_contact" class="form-control" placeholder="search contact name email mobile position">
                    </div>
                    <div class="col-md-3">
                        <span>Search client</span>
                        <input type="text" name="search_client" class="form-control" placeholder="search client name TIN VRN">
                    </div>
                    <div class="col-md-2 pt-lg">
                        <button class="btn btn-success btn-block">Search</button>
                    </div>
                </div>
            </form>
            <p class="text-primary mt-md"><?= $title ?></p>
            <div class="table-responsive">
                <table class="table table-hover mb-none" style="font-size:10pt;" id="userTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>TIN</th>
                        <th>VRN</th>
                        <th>Contact name</th>
                        <th>Contact mobile</th>
                        <th>Contact email</th>
                        <th>Contact position</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? $count = 1;
                    foreach ($contacts as $c) { ?>
                        <tr>
                            <td><?= $count ?></td>
                            <td><?= $c['clientname'] ?></td>
                            <td><?= $c['tinno'] ?></td>
                            <td><?= $c['vatno'] ?></td>
                            <td><?= $c['name'] ?></td>
                            <td><?= $c['mobile'] ?></td>
                            <td><?= $c['email'] ?></td>
                            <td><?= $c['position'] ?></td>
                        </tr>
                        <? $count++;
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
<script type="text/javascript">
    $(function () {
        $('#departId').select2({
            width: '100%', minimumInputLength: 3,
            ajax: {
                url: "?module=departments&action=getDepartments&format=json", dataType: 'json', delay: 250, quietMillis: 200,
                data: function (term) {
                    return {search: term};
                },
                results: function (data, page) {
                    return {result: data};
                }
            }
        });

        $('#productId').select2({
            width: '100%', minimumInputLength: 3,
            ajax: {
                url: "?module=products&action=getProducts&format=json", dataType: 'json', delay: 250, quietMillis: 200,
                data: function (term) {
                    return {search: term};
                },
                results: function (data, page) {
                    return {result: data};
                }
            }
        });

        $("#clientid").select2({
            width: '100%', minimumInputLength: 3,
            ajax: {
                url: "?module=clients&action=getClients&format=json", dataType: 'json', delay: 250, quietMillis: 200,
                data: function (term) {
                    return {search: term};
                },
                results: function (data, page) {
                    return {results: data};
                }
            }
        });
    })

    $('#openModel').on('click', function () {
        $('#formHolder').show('slow');
        $('#formModel').show('slow');
    })

    $('#closeSearchModel').on('click', function () {
        $('#formHolder').hide('slow');
        $('#formModel').hide('slow');
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
