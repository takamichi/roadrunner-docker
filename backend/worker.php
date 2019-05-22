<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

(function () {
    $psr7 = new Spiral\RoadRunner\PSR7Client(
        new Spiral\RoadRunner\Worker(new Spiral\Goridge\StreamRelay(STDIN, STDOUT))
    );

    while ($request = $psr7->acceptRequest()) {
        $ulid = Ulid\Ulid::generate();
        $request = $request->withAddedHeader('R2-Request-Id', (string)$ulid);

        try {
            $response = (new Zend\Diactoros\Response())->withAddedHeader('R2-Request-Id', (string)$ulid);
            $response->getBody()->write('Hello world from RoadRunner! ' . (string)$ulid);
            $psr7->respond($response);
        } catch (\Throwable $e) {
            $psr7->getWorker()->error((string)$e);
        }
    }
})();
