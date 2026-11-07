<?php

$dir = new RecursiveDirectoryIterator('resources/views');
$iterator = new RecursiveIteratorIterator($dir);
$matches = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        if (preg_match_all('/[\x{0980}-\x{09FF}]+/u', $content, $m)) {
            $matches[$path] = count($m[0]);
        }
    }
}

arsort($matches);
foreach ($matches as $path => $count) {
    echo "$count matches: $path\n";
}

