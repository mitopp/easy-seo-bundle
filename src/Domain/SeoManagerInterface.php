<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain;

use Mitopp\EasySeoBundle\Domain\Breadcrumb\Breadcrumb;
use Mitopp\EasySeoBundle\Domain\Config\Config;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTag;

interface SeoManagerInterface
{
    public function setPageTitle(string $title): void;

    public function getTitle(): string;

    public function addMetaTag(MetaTag $metaTag): void;

    /**
     * @return array<MetaTag>
     */
    public function getMetaTags(): array;

    public function addBreadcrumb(Breadcrumb $breadcrumb): void;

    /**
     * @return array<Breadcrumb>
     */
    public function getBreadcrumbs(): array;

    public function getConfig(): Config;

    /**
     * @return array<string, mixed>
     */
    public function getConfigAsArray(): array;

    /**
     * @return array<string, string>
     */
    public function getValidationStatus(): array;
}
