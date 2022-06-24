<?php

declare(strict_types=1);

use App\FeatureToggle\Features;
use App\FeatureToggle\FeatureFlag;

return [
    FeatureFlag::class => \DI\get(Features::class),
];
