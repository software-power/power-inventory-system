<?

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;

$filesystemAdapter = new Local(dirname(__DIR__, 1) . DIRECTORY_SEPARATOR);

$filesystem = new Filesystem($filesystemAdapter);
$CachePool = new FilesystemCachePool($filesystem);
