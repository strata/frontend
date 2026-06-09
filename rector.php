<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpSets(php82: true)
    ->withPreparedSets(symfonyCodeQuality: true)
    ->withComposerBased(symfony: true)
    ->withSkip([
        ReadOnlyPropertyRector::class
    ]);
