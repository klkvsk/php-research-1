<?php
require __DIR__ . '/vendor/autoload.php';

$new = 0;
$newWithCall = 0;
$potential = 0;

foreach (sources() as $source) {
    $matched = preg_match_all('/\Wnew\s+([\\\\a-z_0-9]+)/i', $source, $m);
    foreach ($m[1] as $className) {
        $new += 1;
    }

    $matched = preg_match_all('/\W\(\s*new\s+([\\\\a-z_0-9]+)(\([^)]+\))?\s*\)\s+->/i', $source, $m);
    foreach ($m[1] as $className) {
        $newWithCall += 1;
    }

    $matched = preg_match_all('/(\$.+?)\s*=\s*new\s+[^;]+;\s*\\1->/i', $source, $m);
    foreach ($m[1] as $className) {
        $potential += 1;
    }
}

echo "Number of 'new Class' statements: $new\n";
echo "Number of '(new Class)->' statements: $newWithCall\n";
echo "Potentially convertable to '(new Class)->' statements: $potential\n";
echo "\n";
echo sprintf("Percent of statements used or convertable to this way: %.2f\n",
    100 * ($newWithCall + $potential) / $new
);