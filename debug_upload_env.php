<?php

declare(strict_types=1);

echo "file_uploads=" . ini_get('file_uploads') . PHP_EOL;
echo "upload_max_filesize=" . ini_get('upload_max_filesize') . PHP_EOL;
echo "post_max_size=" . ini_get('post_max_size') . PHP_EOL;
echo "upload_tmp_dir=" . ini_get('upload_tmp_dir') . PHP_EOL;
echo "sys_temp_dir=" . ini_get('sys_temp_dir') . PHP_EOL;
echo "sys_get_temp_dir=" . sys_get_temp_dir() . PHP_EOL;

$dirs = array_unique(array_filter([
    ini_get('upload_tmp_dir') ?: null,
    ini_get('sys_temp_dir') ?: null,
    sys_get_temp_dir() ?: null,
]));

foreach ($dirs as $dir) {
    echo $dir
        . ' exists=' . (is_dir($dir) ? 'yes' : 'no')
        . ' writable=' . (is_writable($dir) ? 'yes' : 'no')
        . PHP_EOL;
}

$logFile = __DIR__ . '/storage/logs/laravel.log';
echo PHP_EOL . "--- upload log tail ---" . PHP_EOL;
if (! is_file($logFile)) {
    echo "log missing" . PHP_EOL;
    exit(0);
}

$lines = @file($logFile, FILE_IGNORE_NEW_LINES) ?: [];
$matches = array_values(array_filter($lines, static function (string $line): bool {
    return str_contains($line, 'Skipping invalid product image upload.')
        || str_contains($line, 'Path must not be empty');
}));

foreach (array_slice($matches, -10) as $line) {
    echo $line . PHP_EOL;
}
