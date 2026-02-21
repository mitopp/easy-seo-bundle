<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain\Breadcrumb;

final class BreadcrumbManager implements BreadcrumbManagerInterface
{
    /**
     * @var array<Breadcrumb>
     */
    private array $breadcrumbs = [];

    public function add(Breadcrumb $breadcrumb): void
    {
        $this->breadcrumbs[$breadcrumb->getLabel()] = $breadcrumb;
    }

    /**
     * @return array<Breadcrumb>
     */
    public function get(): array
    {
        return array_values($this->breadcrumbs);
    }

    public function clear(): void
    {
        $this->breadcrumbs = [];
    }
}
