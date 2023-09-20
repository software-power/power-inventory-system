<link rel="stylesheet" href="assets/css/custom.css">
<!-- start: header -->
<style media="screen">
    .character_dp {
        width: 35px;
        height: 35px;
        display: inline-block;
        color: #777777;
        font-size: 20px;
        text-align: center;
        border-radius: 50%;
        background: #ecedf0;
        padding: 6px;
    }

    .versionb {
        background: #007BFF !important;
    }
</style>
<header class="header">
    <div onclick="openMenu()" class="menu-open-btn">
        <div class="sidebar-toggle-btn hidden-xs">
            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>

    <div class="logo-container">
        <a href="?module=home&action=index" class="logo">
            <img src="<?= CS_LOGO ?>" height="35" alt=""/>
        </a>
        <div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html"
             data-fire-event="sidebar-left-opened">
            <i class="fa fa-bars" aria-label="Toggle sidebar" style="margin-top:7px"></i>
        </div>
    </div>

    <!-- start: search & user box -->
    <div class="header-right">
        <span class="text-danger mr-md" style="font-weight: bold;"><?= LICENSE_REMAIN_DAYS ?></span>
        <span class="badge badge-danger versionb mr-lg">V <?= SUPPORT_VERSION ?></span>
        <span class="separator" style="display:none"></span>

        <!--Admin notifications------------------------------------------>
        <? //if ($_SESSION['member']['roleid']==1){?>
        <ul class="notifications">
            <li>
                <? if ($user_notifications>0) { ?>
                    <a href="#" class="dropdown-toggle notification-icon" data-toggle="dropdown">
                        <i class="fa fa-bell"></i>
                        <span class="badge"><?= $user_notifications ?></span>
                    </a>
                <? } ?>
                <div class="dropdown-menu notification-menu large" style="    width: 360px;">
                    <div class="notification-title">
                        <a href="?module=notifications&action=mark_all_read" class="btn pull-right label label-info">
                            Mark all as Read
                        </a>
                        <span class="pull-right label label-default"><?= $user_notifications ?></span>
                        Notifications
                    </div>
                    <div class="content">
                        <ul>
                            <? $count = 5;
                            foreach ($unreadNotifications as $n) { ?>
                                <li>
                                    <div style="position: relative;display: flex;">

                                        <?
                                        if ($n['type'] == Notifications::NOTIFICATION_TYPE_DANGER) $bg = 'bg-danger';
                                        elseif ($n['type'] == Notifications::NOTIFICATION_TYPE_WARNING) $bg = 'bg-warning';
                                        else $bg = 'bg-success';

                                        if ($n['about'] == Notifications::NOTIFICATION_ABOUT_SUPPLIER) $icon = "fa fa-truck";
                                        elseif ($n['about'] == Notifications::NOTIFICATION_ABOUT_STOCK) $icon = "fa fa-long-arrow-down";
                                        elseif ($n['about'] == Notifications::NOTIFICATION_ABOUT_EXPIRE) $icon = "fa fa-warning";

                                        ?>

                                        <div class="image">
                                            <i class="<?=$icon?> <?=$bg?>"></i>
                                        </div>
                                        <div style="flex:1">
                                            <span class="title" style="font-weight: 600;"><?= $n['title'] ?></span>
                                            <span class="message"
                                                  style="color: #646464;"><?= $n['body'] ?></span>
                                            <p class="text-right" style="margin: 0;padding: 0;margin-top: 10px;">
                                                <small><?= fDate($n['doc'], 'H:i d M Y') ?></small>
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                <hr>
                                <? $count--;
                                if ($count <= 0) break;
                            } ?>
                        </ul>
                        <a href="?module=notifications&action=list" class="btn btn-sm btn-primary">view all</a>
                    </div>
                </div>
            </li>


        </ul>
        <? //}?>
        <!-----End admin notifications-------------------------------------->

        <span class="separator"></span>

        <div id="userbox" class="userbox">
            <a href="#" data-toggle="dropdown">
                <figure class="profile-picture">
                    <? if (empty($_SESSION['member']['image'])) { ?>
                        <!--<img src="assets/images/!logged-user.jpg" alt="User-admin" class="img-circle" data-lock-picture="assets/images/!logged-user.jpg" /> -->
                        <span class="character_dp">
								<? $default_character = str_split($_SESSION['member']['name']); ?>
                                <?= $default_character[0] ?>
							</span>
                    <? } else { ?>
                        <img src="images/dp/<?= $_SESSION['member']['image'] ?>" alt="User-admin" class="img-circle"
                             data-lock-picture="images/dp/<?= $_SESSION['member']['image'] ?>"/>
                    <? } ?>
                </figure>
                <div class="profile-info" data-lock-name="<?= $_SESSION['member']['name'] ?>"
                     data-lock-email="<?= $_SESSION['member']['username'] ?>">
                    <!-- <span class="name"><?= $_SESSION['member']['name'] ?></span>
					<span class="role"><?= $_SESSION['member']['role'] ?></span> -->
                    <span class="name"
                          style="text-transform: lowercase"><?= explode(' ', $_SESSION['member']['name'])[0] ?></span>
                    <span class="role">
						<?= $_SESSION['member']['rolename'] ?>
				</span>
                </div>

                <i class="fa custom-caret"></i>
            </a>

            <div class="dropdown-menu">
                <ul class="list-unstyled">
                    <li class="divider"></li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="#" data-lock-screen="true"><i class="fa fa-lock"></i>
                            Lock Screen</a>
                    </li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="?module=notifications&action=list"><i
                                    class="fa fa-bell"></i>Notifications</a>
                    </li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="?module=profile&action=index"><i class="fa fa-cogs"></i>Settings</a>
                    </li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="?module=authenticate&action=logout"><i
                                    class="fa fa-power-off"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end: search & user box -->
</header>
<!-- end: header -->

<script>
    $(document).on('click', '.dontClose', function (e) {
        if ($(this).hasClass('keep-open-on-click')) {
            e.stopPropagation();
        }
    });


    $(document).on('blur', '.dontClose', function (e) {
        if ($(this).hasClass('keep-open-on-click')) {
            e.stopPropagation();
            saveRate(this);
        }
    });

    function saveRate(obj) {

        tr = $(obj).closest('li');
        rate = $(tr).find('.dontClose').val();
        rid = $(tr).find('.rateid').val();
        currid = $(tr).find('.currid').val();


        // reverserate = $(tr).find('.reverserate').val(1/rate);

        $.get('?module=rates&action=saveRate&format=json&rid=' + rid + '&rate=' + rate + '&currid=' + currid, null, function (d) {
            CC = JSON.parse(d);
            var msg = CC[0].msg;
            // var rid = CC[0].rid;

            // $(tr).find('.rateid').val(rid)
            // $(tr).find('.currid').val(currid)
            // });

        });
    }

    function sendMsg() {

        to = $("#msgtoid").val();
        message = $("#message").val();

        $("#message").val('');
        $("#msgtoid").val('');

        $.get('?module=home&action=sendMsg&format=json&to=' + to + '&message=' + message, null, function (d) {
            CC = JSON.parse(d);
            var msg = CC[0].msg;

            new PNotify({
                title: 'Sent',
                text: 'Message has been sent',
                type: 'success'
            });


        });
    }

    function deleteMsg(obj) {

        tr = $(obj).closest('li');
        id = $(tr).find('.msgid').val();


        $.get('?module=home&action=markRead&format=json&id=' + id, null, function (d) {
            CC = JSON.parse(d);
            var msg = CC[0].msg;

            new PNotify({
                title: 'Success',
                text: 'Message has archived',
                type: 'success'
            });

            tr.remove();
            count = parseInt($("#msgCount").html());
            $("#msgCount").html(count - 1);


        });
    }

    function showMore(obj) {

        tr = $(obj).closest('li');
        $(tr).find('.showLess').hide();
        $(tr).find('.showMore').show();


    }
</script>
