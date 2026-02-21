<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/config/reference.php',
    ])
    ->withComposerBased(
        phpunit: true,
        symfony: true,
    )
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        instanceOf: true,
        earlyReturn: true,
        rectorPreset: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withParallel(
        timeoutSeconds: 60,
        maxNumberOfProcess: 2,
        jobSize: 2,
    )
;
