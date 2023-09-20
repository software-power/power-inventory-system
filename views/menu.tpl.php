<!-- start: sidebar -->
<aside id="sidebar-left" class="sidebar-left">

    <!-- <div class="sidebar-header">
        <div class="sidebar-title">
            Menu
        </div>
        <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div> -->

    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <? foreach ($realmenus as $mlabel => $m) {
                        if ($m['subs'][0]['slabel']) { ?>
                            <li class="nav-parent">
                                <a>
                                    <i class="<?= $m['icon'] ?>" aria-hidden="true"></i>
                                    <span><?= $mlabel ?></span>
                                </a>
                                <ul class="nav nav-children">
                                    <? foreach ($m['subs'] as $s) { ?>
                                        <li>
                                            <a class="submenu-link" href="?module=<?= $s['smod'] ?>&action=<?= $s['sact'] ?>">
                                                <span class=""><?= $s['slabel'] ?></span>
                                            </a>
                                        </li>
                                    <? } ?>

                                </ul>
                            </li>
                        <? } else { ?>
                            <li>
                                <a href="?module=<?= $m['module'] ?>&action=<?= $m['action'] ?>">

                                    <i class="<?= $m['icon'] ?>" aria-hidden="true"></i>
                                    <span><?= $mlabel ?></span>
                                </a>
                            </li>
                        <? }
                    } ?>
                </ul>
            </nav>

            <hr class="separator"/>


            <hr class="separator"/>
        </div>

    </div>

</aside>

<script>
    $(function () {
        $('.submenu-link').bind('click', function (e) {
            e.preventDefault();
            let link = $(e.target)[0].localName === 'a' ? $(e.target) : $(e.target).closest('a');
            let url = $(link).attr('href');
            try {
                if (!(e.ctrlKey || e.metaKey)) {
                    $(link).find('span').addClass('link-text');
                    window.location.assign(url);
                }else{
                    window.open(url,'_blank');
                }
            } catch (e) {
                console.log(e);
            }
        });
    });
</script>
