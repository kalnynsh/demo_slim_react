<?php

declare(strict_types=1);

namespace App\Frontend;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FrontendUrlTwigExtension extends AbstractExtension
{
    public function __construct(private readonly FrontendUrlGenerator $url)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('frontend_url', [$this, 'url']),
        ];
    }

    /** @psalm-param array<array-key, mixed> $params */
    public function url(string $path, $params = []): string
    {
        return $this->url->generate($path, $params);
    }
}
