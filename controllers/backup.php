<?

use Coderatio\SimpleBackup\SimpleBackup;

if ($action == 'system_backup') {

    Users::isAllowed();

    try {
        $backup_path = "backups";
        $temp_path = $backup_path . DIRECTORY_SEPARATOR . 'temp';
        if (!is_dir($temp_path)) mkdir($temp_path, 0777, true);
        $db_filename = "db backup " . date('d-M-Y His') . '.sql';
        $full_path = $temp_path . DIRECTORY_SEPARATOR . $db_filename;

        $simpleBackup = SimpleBackup::start()
            ->setDbHost($config['server'])
            ->setDbUser($config['username'])
            ->setDbPassword($config['password'])
            ->setDbName($config['database'])
            //todo time zone
            ->storeAfterExportTo($temp_path, $db_filename);

//        debug($simpleBackup->getExportedName());
//    debug($backup_folders);
        foreach (BACKUP_FOLDERS as $name => $foldername) {
            $zipFolder = new ZipArchive();
            $zipFolder->open($temp_path . DIRECTORY_SEPARATOR . $name . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($foldername),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $iname => $file) {
                // Skip directories (they would be added automatically)
                if (!$file->isDir()) {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = $file->getPathname();
                    // Add current file to archive
//                debug([$filePath,$relativePath]);
                    $zipFolder->addFile($filePath, $relativePath);
                }
            }
            $zipFolder->close();
        }
        $backup_name = "system_backup_".date("His_dMY") . '.zip';
        $final_backup_path = $backup_path . DIRECTORY_SEPARATOR . $backup_name;
        $finalZip = new ZipArchive();
        $finalZip->open($final_backup_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $temp_files = array_diff(scandir($temp_path), array('.', '..', '.gitignore'));
        foreach ($temp_files as $temp_file) {
            $temp_file_path = $temp_path . DIRECTORY_SEPARATOR . $temp_file;
            $finalZip->addFile($temp_file_path, $temp_file);
            $finalZip->setEncryptionName($temp_file, ZipArchive::EM_AES_256, CS_BACKUP_PASSWORD); //get password from admin
        }
        $finalZip->close();

        download_file($final_backup_path, $backup_name);

        //clear temp files and folder
        foreach ($temp_files as $temp_file) {
            $temp_file_path = $temp_path . DIRECTORY_SEPARATOR . $temp_file;
            unlink($temp_file_path);
        }
        rmdir($temp_path);
        exit;
    } catch (Exception $e) {

        debug("ERROR:: ".$e->getMessage());
    }
}
