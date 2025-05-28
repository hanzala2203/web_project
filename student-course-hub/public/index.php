<?php
// Application entry point

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Initialize application
$app = require __DIR__ . '/../bootstrap.php';

// Create Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'settings' => [
        'displayErrorDetails' => true,
        'determineRouteBeforeAppMiddleware' => true,
    ]
]);

// Add Twig to Container
$containerBuilder->addDefinitions([
    Twig::class => function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
]);

$container = $containerBuilder->build();
AppFactory::setContainer($container);

// Create App
$app = AppFactory::create();

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Add Twig Middleware
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

// Register routes
require __DIR__ . '/../routes/web.php';

// Run app
$app->run();
