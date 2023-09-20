<div class="company-info d-flex justify-content-between" style="border-bottom: 2px solid grey;min-height: 25mm">
    <? if (CS_SHOW_PRINT_HEADER && !isset($_GET['no_header_footer'])) { ?>
        <div class="logo" style="width: 100mm">
            <img src="<?= CS_LOGO ?>" alt="logo" style="width: inherit;height: 25mm;"/>
        </div>
        <div class="text-weight-semibold">
            <p class="p-none m-none"><?= CS_COMPANY ?></p>
            <? if (defined("LOCATION_ADDRESS")) { ?>
                <? foreach (LOCATION_ADDRESS as $item) { ?>
                    <p class="p-none m-none"><?= $item ?></p>
                <? } ?>
            <? } else { ?>
                <p class="p-none m-none"><?= CS_ADDRESS ?></p>
                <p class="p-none m-none"><?= CS_STREET ?></p>
                <p class="p-none m-none"><?= CS_TEL ?></p>
                <p class="p-none m-none"><?= CS_FAX ?></p>
                <p class="p-none m-none"><?= CS_EMAIL ?></p>
            <? } ?>
        </div>
    <? } ?>
</div>