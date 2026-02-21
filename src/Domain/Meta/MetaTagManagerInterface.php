<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain\Meta;

interface MetaTagManagerInterface
{
    public function add(MetaTag $metaTag): void;

    /**
     * @return array<MetaTag>
     */
    public function get(): array;

    public function clear(): void;
}
