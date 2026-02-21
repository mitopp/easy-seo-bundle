<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain\Meta;

final class MetaTagManager implements MetaTagManagerInterface
{
    /**
     * @var array<MetaTag>
     */
    private array $metaTags = [];

    public function add(MetaTag $metaTag): void
    {
        $this->metaTags[$metaTag->getName()] = $metaTag;
    }

    /**
     * @return array<MetaTag>
     */
    public function get(): array
    {
        return array_values($this->metaTags);
    }

    public function clear(): void
    {
        $this->metaTags = [];
    }
}
