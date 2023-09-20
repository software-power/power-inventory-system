<?
logData("CLEAR OLD DATA",$script_log_file);

/*
 * Delete backup files order than $days
 * */
$backup_days = 7;
try {
    $fileSystemIterator = new FilesystemIterator('backups');
    $now = time();
    foreach ($fileSystemIterator as $file) {
        if ($now - $file->getCTime() >= 60 * 60 * 24 * $backup_days && $file->getExtension() == 'zip') // 7 days
        {
            $filename = 'backups/' . $file->getFilename();
            unlink($filename);
            logData("delete old file $filename",$script_log_file);
        }
        $files[] = $file->getBasename();
    }
} catch (Exception $e) {
    logData("Error: ".$e->getMessage(),$script_log_file);
}

/*
 * Clear notifications older than $days
 */
$notification_days = 4;
$date = date('Y-m-d', strtotime("-$notification_days days"));
$sql = "delete from notifications n where n.doc < '$date'";
logData("Run query:  $sql",$script_log_file);
if(!executeQuery($sql))logData("Query failed",$script_log_file);


