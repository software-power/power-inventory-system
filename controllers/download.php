<?

if ($action == 'efd_file') {
    if (isset($_GET['download'])) {
        $download = json_decode(base64_decode($_GET['download']), true);
        $sale = $Sales->find(['receipt_no' => $download['invoice_no']])[0];
        $fisc = $SaleFiscalization->find(['salesid' => $sale['id']])[0];
//        debug($fisc);
        $result = download_file($download['file_path'], $download['filename'], '', false);
        $SaleFiscalization->update($fisc['id'], ['efd_download_attempt' => $fisc['efd_download_attempt'] + 1]); //count download attempt

        if ($result['error']) $_SESSION['error'] .= "\n" . $result['error'];
        unset($_SESSION['download_efd']);
        exit();
        //new query
        // ALTER TABLE `sales_fiscalization` ADD `efd_download_attempt` INT NOT NULL AFTER `fiscalization_type`;
    } else {
        $_SESSION['error'] .= "\nDownload file not found";
    }
}
