<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain\Breadcrumb;

interface BreadcrumbManagerInterface
{
    public function add(Breadcrumb $breadcrumb): void;

    /**
     * @return array<Breadcrumb>
     */
    public function get(): array;

    public function clear(): void;
}
