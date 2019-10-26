<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

(static function (): void {
    $worker = new Spiral\RoadRunner\Worker(new Spiral\Goridge\StreamRelay(\STDIN, \STDOUT));
    $psr17Factories = new Nyholm\Psr7\Factory\Psr17Factory();
    $psr7Client = new Spiral\RoadRunner\PSR7Client($worker, $psr17Factories, $psr17Factories, $psr17Factories);

    // Load .env file
    try {
        Dotenv\Dotenv::create(__DIR__)->load();
    } catch (Throwable $e) {
        error_log((string)$e);
        $worker->stop();
        return;
    }

    while ($request = $psr7Client->acceptRequest()) {
        try {
            $response = $psr17Factories->createResponse();
            $response->getBody()->write('Hello RoadRunner !');

            $psr7Client->respond($response);
        } catch (Throwable $e) {
            $psr7Client->getWorker()->error((string)$e);
        }
    }
})();
