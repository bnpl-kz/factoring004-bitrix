<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);

    // get services (needed for register a single rule)
    $services = $containerConfigurator->services();

    $paths = glob(__DIR__ . '/vendor/rector/rector/rules/DowngradePhp7*/Rector/*/*.php');
    $paths = array_filter($paths, fn($path) => strpos($path, 'DowngradePhp70') === false);

    $serviceClasses = [];

    foreach ($paths as $path) {
        $tokens = token_get_all(file_get_contents($path));

        foreach ($tokens as $token) {
            if ($token[0] === 265) {
                $serviceClasses[] = $token[1] . '\\' . basename($path, '.php');
                break;
            }
        }
    }

    // register rules
    foreach (array_unique($serviceClasses) as $serviceClass) {
        $services->set($serviceClass);
    }
};
