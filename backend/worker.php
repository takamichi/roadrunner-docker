<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

(static function (): void {
    $psr17 = new Nyholm\Psr7\Factory\Psr17Factory();
    $psr7 = new Spiral\RoadRunner\PSR7Client(
        new Spiral\RoadRunner\Worker(new Spiral\Goridge\StreamRelay(\STDIN, \STDOUT)),
        $psr17,
        $psr17,
        $psr17
    );

    while ($request = $psr7->acceptRequest()) {
        try {
            $response = $psr17->createResponse();
            $response->getBody()->write('Hello RoadRunner !');

            $psr7->respond($response);
        } catch (Throwable $e) {
            $psr7->getWorker()->error((string)$e);
        }
    }
})();
