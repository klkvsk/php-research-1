<?php

/**
 * @return Generator<string> source codes
 */
function sources(): \Generator {
    $downloadDir = __DIR__ . '/../packages';

    if (!file_exists($downloadDir)) {
        die("No packages\n");
    }

    $dir = opendir($downloadDir);
    while ($zipFile = readdir($dir)) {
        $zip = new ZipArchive();
        $zip->open($downloadDir . DIRECTORY_SEPARATOR . $zipFile);
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (str_ends_with($stat['name'], '.php')) {
                yield $zip->getFromIndex($i);
            }
        }
    }
}

function simplifyClassName($maybeFqcn) {
    if (($pos = strrpos($maybeFqcn, '\\')) !== false) {
        return substr($maybeFqcn, $pos + 1);
    }

    return $maybeFqcn;
}