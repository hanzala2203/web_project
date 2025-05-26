<?php

use DI\ContainerBuilder;
use App\Models\Programme;
use App\Models\Module;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use App\Utils\Security;
use App\Utils\ImageValidator;
use App\Utils\Cache;
use App\Utils\Notification;
use Slim\Views\Twig;
use PDO;
use function DI\autowire;
use function DI\get;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function () {
            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];
            
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            
            return $pdo;
        },

        Programme::class => function ($container) {
            return new Programme($container->get(PDO::class));
        },

        Module::class => function ($container) {
            return new Module($container->get(PDO::class));
        },

        Staff::class => function ($container) {
            return new Staff($container->get(PDO::class));
        },

        Student::class => function ($container) {
            return new Student($container->get(PDO::class));
        },

        Security::class => function () {
            return new Security();
        },

        ImageValidator::class => function () {
            return new ImageValidator();
        },

        Twig::class => function () {
            return Twig::create(__DIR__ . '/../templates', [
                'cache' => false,
                'debug' => true,
                'auto_reload' => true
            ]);
        },
    ]);
};
