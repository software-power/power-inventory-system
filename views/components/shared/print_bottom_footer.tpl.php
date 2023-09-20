<style>
    .footer-img {
        height: 30mm;

    }

    .bottom-footer {
        width: 100%;
    }

    @media print {
        div.bottom-footer {
            margin: 0 !important;
            padding: 0 5mm !important;
            position: fixed;
            bottom: 0.15mm;
            left: 0;
            right: 0;
            color-adjust: exact !important;
        }
    }
</style>


<? if (CS_SHOW_PRINT_FOOTER && file_exists(CS_PRINT_FOOTER) && !isset($_GET['no_header_footer'])) { ?>
    <div class="bottom-footer">
        <div class="footer-img">
            <img src="<?= CS_PRINT_FOOTER ?>" style="height: 100%;width: 100%;" alt="">
        </div>
    </div>
<? } ?>