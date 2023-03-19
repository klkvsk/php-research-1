<?php
require __DIR__ . '/vendor/autoload.php';

$new = 0;
$newWithCall = 0;
$potential = 0;
$conflicted = 0;

$CLASS_NAME = '([\\\\a-z_0-9]+)';
$ARGS = '(\((?:[^()]*?(?-1)?)*\))'; // checks balanced parentheses inside


foreach (sources() as $source) {
    $matched = preg_match_all("/\Wnew\s+$CLASS_NAME/i", $source, $m);
    if ($matched) {
        $new += count($m[0]);
    }

    $matched = preg_match_all("/(?<!->)\s*\(\s*new\s+$CLASS_NAME\s*$ARGS?\s*\)\s*->/i", $source, $m);
    if ($matched) {
        $newWithCall += count($m[0]);
    }

    $matched = preg_match_all("/(?<!\()\s*new\s+$CLASS_NAME\s*$ARGS?\s*->/i", $source, $m);
    if ($matched) {
        // PHP would throw throw syntax error on this, but still
        $conflicted += count($m[0]);
    }

    $matched = preg_match_all("/(\\$[a-z0-9_\->]+)\s*=\s*new\s+[^;]+;\s*\\1\s*->/i", $source, $m);
    if ($matched) {
        $potential += count($m[0]);
    }
}

echo "Number of 'new Class' statements: $new\n";
echo "Number of '(new Class)->' statements: $newWithCall\n";
echo "Potentially convertable to '(new Class)->' statements: $potential\n";
//echo "Conflicted 'new fn()->getClassName()' statements: $conflicted\n";

echo "\n";
echo sprintf("Percent of statements with call-after-new: %.2f%%\n",
    100 * $newWithCall / $new
);
echo sprintf("Percent of statements possibly convertable to call-after-new: %.2f%%\n",
    100 * $potential / $new
);
