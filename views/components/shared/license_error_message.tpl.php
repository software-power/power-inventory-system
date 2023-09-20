<? if ($_SESSION['license_error_message']) { ?>
    <div class="text-center bg-danger text-lg" style="position: absolute;width: 100%;padding: 5px 0">
        <span>LICENSE ERROR: <?= $_SESSION['license_error_message'] ?></span>
        <a class="btn btn-warning btn-sm ml-lg" href="<?= url('authenticate', 'license') ?>">Fix License</a>
    </div>
<? } ?>
