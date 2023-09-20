<?
/*
 * PM2 setup
 * pm2 start "C:\xampp\htdocs\inventory-pctl\controllers\background\backup_system.php" --interpreter="C:\xampp\php\php.exe" --name="Inventory system auto backup"
 *
 *
 * */

use Coderatio\SimpleBackup\SimpleBackup;

include 'vars.php';

$script_log_file = __DIR__ . "/script.log";

//while (true) {

$db_only = in_array("--db-only", $_SERVER['argv']);

logData("---- START AUTO SYSTEM BACKUP ----", $script_log_file);
try {
    $backup_path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . "backups";
    $temp_path = $backup_path . DIRECTORY_SEPARATOR . 'temp';
    if (!is_dir($temp_path)) mkdir($temp_path, 0777, true);
    $db_filename = "db backup " . date('d-M-Y His') . '.sql';
    $full_path = $temp_path . DIRECTORY_SEPARATOR . $db_filename;
//    debug($full_path);

    $simpleBackup = SimpleBackup::start()
        ->setDbHost($config['server'])
        ->setDbUser($config['username'])
        ->setDbPassword($config['password'])
        ->setDbName($config['database'])
        ->storeAfterExportTo($temp_path, $db_filename);


//    debug($simpleBackup);
//        debug($simpleBackup->getExportedName());
    if (!$db_only)
        foreach (BACKUP_FOLDERS as $name => $foldername) {
            $foldername = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $foldername;
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
                    $relativePath = substr($relativePath, strlen(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR));
                    // Add current file to archive
                    $zipFolder->addFile($filePath, $relativePath);
                }
            }
            $zipFolder->close();
        }


    $backup_name = ($db_only ? "database_backup_" : "system_backup_") . date("Ymd_His") . '.zip';
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
    echo "file: $backup_name";
    logData("file: $backup_name", $script_log_file);

    //clear temp files and folder
    foreach ($temp_files as $temp_file) {
        $temp_file_path = $temp_path . DIRECTORY_SEPARATOR . $temp_file;
        unlink($temp_file_path);
    }
    rmdir($temp_path);
} catch (Exception $e) {
    logData("ERROR:: " . $e->getMessage(), $script_log_file);
}
logData("---- END AUTO SYSTEM BACKUP ----\n", $script_log_file);

//    sleep(10);
//}
