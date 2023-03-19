<?php
require __DIR__ . '/vendor/autoload.php';

$num = 1000;
$done = 0;
$client = new GuzzleHttp\Client();

$downloadDir = __DIR__ . '/packages';
if (!file_exists($downloadDir)) {
    mkdir($downloadDir, 0777, true);
}

$page = 1; $perPage = 20;
while ($done < $num) {
    echo "Fetching page #$page of " . ceil($num / $perPage) . "\n";

    $url = "https://packagist.org/explore/popular.json?per_page=$perPage&page=$page&type=library";

    $popularResponse = $client->request('GET', $url);

    $popularPage = json_decode($popularResponse->getBody()->getContents(), true);
    $promises = [];
    foreach ($popularPage['packages'] as $package) {
        $packageName = $package['name'];
        $zipFile = $downloadDir . DIRECTORY_SEPARATOR . str_replace('/', '__', $packageName) . '.zip';
        if (file_exists($zipFile)) {
            // already downloaded
            continue;
        }

        $promise = $client->requestAsync('GET', "https://repo.packagist.org/p2/$packageName.json")
            ->then(
                function (Psr\Http\Message\ResponseInterface $response) use ($packageName, $downloadDir) {
                    if ($response->getStatusCode() !== 200) {
                        echo "$packageName: returned " . $response->getStatusCode() . ' ' . $response->getReasonPhrase();
                        return null;
                    }
                    $body = $response->getBody()->getContents();
                    $json = json_decode($body, true);
                    $versions = $json['packages'][$packageName];
                    $topVersion = reset($versions);
                    //echo "$packageName: downloading {$topVersion['version']}\n";

                    return $topVersion['dist']['url'] ?? null;
                },
                function (\Throwable $e) use ($packageName) {
                    echo "Getting info on $packageName failed: [" . get_class($e) . '] ' . $e->getMessage() . "\n";
                }
            )
            ->then(
                function ($url) use ($client, $packageName, $zipFile) {
                    if (!$url) {
                        echo "$packageName: empty url\n";
                        return false;
                    }
                    return $client->requestAsync('GET', $url, [ 'sink' => $zipFile, 'timeout' => 40 ])
                        ->then(
                            function () {
                                // downloaded
                                return true;
                            },
                            function (\Throwable $e) use ($url) {
                                echo "Downloading $url failed: [" . get_class($e) . '] ' . $e->getMessage() . "\n";
                            }
                        );
                }
            );

        $promises []= $promise;
    }

    $done += $perPage;
    $page += 1;

    GuzzleHttp\Promise\Utils::all($promises)->wait(true);
}
