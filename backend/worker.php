<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

(static function (): void {
    $worker = new Spiral\RoadRunner\Worker(new Spiral\Goridge\StreamRelay(\STDIN, \STDOUT));
    $psr17 = new Nyholm\Psr7\Factory\Psr17Factory();
    $psr7 = new Spiral\RoadRunner\PSR7Client($worker, $psr17, $psr17, $psr17);

    // Load .env file
    try {
        Dotenv\Dotenv::create(__DIR__)->load();
    } catch (Throwable $e) {
        error_log((string)$e);
        $worker->stop();
        return;
    }

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
