<style>
    table.dataTable tbody th.focus, table.dataTable tbody td.focus {
        box-shadow: none !important;
    }

    .alert-img {
        border-radius: 35px;
        height: 20px;
        width: 20px;
        line-height: 35px;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<header class="page-header">
    <h2>Notifications</h2>
</header>

<div class="col-md-12">
    <div class="center-panel">
        <section class="panel">
            <header class="panel-heading">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="panel-title">Notifications</h2>
                    </div>
                    <div class="col-md-9 d-flex justify-content-end">
                        <a href="?module=notifications&action=mark_all_read" class="btn btn-primary btn-sm"> <i
                                    class="fa fa-envelope"></i> Read all</a>
                        <a href="?module=notifications&action=clear_all" class="btn btn-danger btn-sm ml-md"> <i
                                    class="fa fa-trash"></i> Clear all</a>
                    </div>
                </div>
            </header>
            <div class="panel-body">
                <table class="table table-hover mb-none" id="notifications-table" style="font-size: 10pt">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Body</th>
                        <th>State</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
    $(function () {
        $('#notifications-table').DataTable({
            processing: true,
            ordering: false,
            serverSide: true,
            serverMethod: 'get',
            ajax: {
                'url': `?module=notifications&action=getNotifications&format=json`
            },
            columns: [
                {data: 'title'},
                {
                    data: 'body',
                    'render': function (data, type, row, meta) {
                        return data.replace('\n','<br>');
                    }
                },
                {data: 'state'},
                {data: 'doc'},
                {data: `btn`}
            ]
        });

    });
</script>