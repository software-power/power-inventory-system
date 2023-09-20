<?

include 'vars.php';

$script_log_file = __DIR__ . "/script.log";

logData("---- START Dashboard caching ----", $script_log_file);
try {
    $cache_data = Dashboard::cachingItems();
    if ($CachePool->hasItem(CACHE_FILES['dashboard'])) $CachePool->deleteItem(CACHE_FILES['dashboard']);

    $dashboardCache = $CachePool->getItem(CACHE_FILES['dashboard']);

    $dashboardCache->set($cache_data);
    $dashboardCache->expiresAfter(60 * 3); //3 minutes
    $CachePool->save($dashboardCache);
} catch (\Psr\SimpleCache\InvalidArgumentException $e) {
    logData("Cache error: " . $e->getMessage(), $script_log_file);
} catch (\Psr\Cache\InvalidArgumentException $e) {
    logData("Cache error: " . $e->getMessage(), $script_log_file);
} catch (Exception $e) {
    logData("Error: " . $e->getMessage(), $script_log_file);
}

logData("---- END Dashboard caching ----\n", $script_log_file);
