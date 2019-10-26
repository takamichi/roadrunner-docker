<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

(static function (): void {
    $psr7 = new Spiral\RoadRunner\PSR7Client(
        new Spiral\RoadRunner\Worker(new Spiral\Goridge\StreamRelay(STDIN, STDOUT))
    );

    while ($request = $psr7->acceptRequest()) {
        try {
            $response = (new Zend\Diactoros\Response());
            $response->getBody()->write('Hello RoadRunner !');

            $psr7->respond($response);
        } catch (Throwable $e) {
            $psr7->getWorker()->error((string)$e);
        }
    }
})();
