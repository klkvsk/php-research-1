<?php
require __DIR__ . '/vendor/autoload.php';

$numReturns = 0;
$numReturnThis = 0;

foreach (sources() as $source) {
    $returns = preg_match_all('/\Wreturn\s+(.+?);/m', $source, $m);
    if ($returns) {
        $numReturns += $returns;
        foreach ($m[1] as $whatReturns) {
            if ($whatReturns == '$this') {
                $numReturnThis++;
            }
        }
    }
}

echo sprintf("Total returns: %-10s\nThis returns:  %-10s\n\n%.2f%%\n",
    $numReturns, $numReturnThis, 100 * $numReturnThis / $numReturns
);