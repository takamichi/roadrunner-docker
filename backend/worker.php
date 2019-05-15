<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$relay = new Spiral\Goridge\StreamRelay(STDIN, STDOUT);
$psr7 = new Spiral\RoadRunner\PSR7Client(new Spiral\RoadRunner\Worker($relay));

while ($request = $psr7->acceptRequest()) {
    try {
        $response = new \Zend\Diactoros\Response();
        $response->getBody()->write('Hello world from RoadRunner!');
        $psr7->respond($response);
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);
    }
}
