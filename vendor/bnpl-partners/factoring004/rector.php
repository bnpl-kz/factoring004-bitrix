<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $config): void {
    $paths = glob(__DIR__ . '/vendor/rector/rector/rules/DowngradePhp7*/Rector/*/*.php');

    $config->skip([
        __DIR__ . '/rector',
    ]);

    $include = [
        BnplPartners\Factoring004RectorRules\DowngradeScalarTypeDeclarationRector::class,
        BnplPartners\Factoring004RectorRules\DowngradeNullableTypeDeclarationRector::class,
    ];

    $exclude = [
        Rector\DowngradePhp72\Rector\ClassMethod\DowngradeParameterTypeWideningRector::class,
        Rector\DowngradePhp70\Rector\FunctionLike\DowngradeScalarTypeDeclarationRector::class,
        Rector\DowngradePhp70\Rector\String_\DowngradeGeneratedScalarTypesRector::class,
        Rector\DowngradePhp71\Rector\FunctionLike\DowngradeNullableTypeDeclarationRector::class,
    ];

    $rules = array_map(function ($path) {
        return 'Rector' . str_replace('/', '\\', substr($path, strlen(__DIR__ . '/vendor/rector/rector/rules'), -4));
    }, $paths);
    $rules = array_merge($rules, $include);

    foreach ($rules as $rule) {
        if (!in_array($rule, $exclude, true)) {
            $config->rule($rule);
        }
    }
};
