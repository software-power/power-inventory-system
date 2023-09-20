<?
if ($action == 'list') {
    $data['content'] = loadTemplate('notification_list.tpl.php');
}

if ($action == 'mark_all_read') {
    if (empty($_GET['id'])) {
        $Notifications->updateWhere(
            ['toid' => $_SESSION['member']['id']],
            ['state' => 'read']);
    } else {
        $Notifications->updateWhere(
            [
                'id' => $_GET['id'],
                'toid' => $_SESSION['member']['id']
            ],
            ['state' => 'read']);
    }
    redirectBack();
}
if ($action == 'clear_all') {
    if (empty($_GET['id'])) {
        $Notifications->deleteWhere(['toid' => $_SESSION['member']['id']]);
    } else {
        $Notifications->deleteWhere([
            'id' => $_GET['id'],
            'toid' => $_SESSION['member']['id']
        ]);
    }
    redirectBack();
}


if ($action == 'ajax_getNotifications') {

    $limit = $_GET['start'];
    $offset = $_GET['length'];
    $search = escapeChar($_GET['search']['value']);

//    debug($_GET);
    $totalRecords = Notifications::$staticClass->countWhere(['toid'=>$_SESSION['member']['id']]);

    $notifications = $Notifications->getNotifications($_SESSION['member']['id'], "", "", $limit,$offset,$search);
//    debug($notifications);
    $mapped = array_map(function ($n){
//        debug($n);
        if ($n['type'] == Notifications::NOTIFICATION_TYPE_DANGER) $bg = 'bg-danger';
        elseif ($n['type'] == Notifications::NOTIFICATION_TYPE_WARNING) $bg = 'bg-warning';
        else $bg = 'bg-success';

        if($n['about']==Notifications::NOTIFICATION_ABOUT_SUPPLIER)$icon="fa fa-truck";
        elseif($n['about']==Notifications::NOTIFICATION_ABOUT_STOCK)$icon="fa fa-long-arrow-down";
        elseif($n['about']==Notifications::NOTIFICATION_ABOUT_EXPIRE)$icon="fa fa-warning";
        elseif($n['about']==Notifications::NOTIFICATION_ABOUT_OTHER)$icon="fa fa-envelope";

        $title = "<div class='d-flex align-items-center'>
                      <div class='alert-img $bg'>
                          <i class='$icon'></i>
                      </div>
                      <span class='ml-xs'>{$n['title']}</span>
                  </div>";
        
        $btn = "<div class='btn-group dropleft'>
                   <button type='button' class='btn btn-secondary dropdown-toggle'
                           data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                       <i class='fa fa-list'></i>
                   </button>
                   <div class='dropdown-menu'>
                       <a class='dropdown-item'
                          href='?module=notifications&action=mark_all_read&id={$n['id']}'
                          title='Mark as Read'>
                           <i class='fa fa-pencil'></i> Mark as Read
                       </a>
                       <a class='dropdown-item'
                          href='?module=notifications&action=clear_all&id={$n['id']}'
                          title='Mark as Read'>
                           <i class='fa fa-trash'></i> Delete
                       </a>
                   </div>
               </div>";

        return [
            'id'=>$n['id'],
            'title'=>$title,
            'body'=>$n['body'],
            'state'=>$n['state'],
            'doc'=>$n['doc'],
            'btn'=>$btn,
        ];
    },$notifications);
//    debug($mapped[0],1);

    $response = [
        "draw" => intval($_GET['draw']),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $mapped
    ];

    $data['content'] = $response;
}