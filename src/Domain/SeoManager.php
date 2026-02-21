<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain;

use Mitopp\EasySeoBundle\Domain\Breadcrumb\Breadcrumb;
use Mitopp\EasySeoBundle\Domain\Breadcrumb\BreadcrumbManagerInterface;
use Mitopp\EasySeoBundle\Domain\Config\Config;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTag;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTagManagerInterface;

final class SeoManager implements SeoManagerInterface
{
    private Config $config;

    private ?string $pageTitle = null;

    /**
     * @param array<string, string> $defaultMetaTags
     */
    public function __construct(
        private readonly BreadcrumbManagerInterface $breadcrumbManager,
        private readonly MetaTagManagerInterface $metaTagManager,
        private readonly string $siteTitle,
        private readonly string $separator,
        private readonly bool $appendSiteTitle,
        array $defaultMetaTags = [],
    ) {
        $this->config = new Config($siteTitle, $separator, $appendSiteTitle);

        foreach ($defaultMetaTags as $name => $content) {
            $this->metaTagManager->add(MetaTag::create($name, $content));
        }
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfigAsArray(): array
    {
        return $this->config->asArray();
    }

    public function setPageTitle(string $title): void
    {
        $this->pageTitle = $title;
    }

    public function getTitle(): string
    {
        if (null === $this->pageTitle) {
            return $this->siteTitle;
        }

        $separator = html_entity_decode($this->separator);

        if ($this->appendSiteTitle) {
            return sprintf('%s %s %s', $this->pageTitle, $separator, $this->siteTitle);
        }

        return sprintf('%s %s %s', $this->siteTitle, $separator, $this->pageTitle);
    }

    public function addMetaTag(MetaTag $metaTag): void
    {
        $this->metaTagManager->add($metaTag);
    }

    public function addBreadcrumb(Breadcrumb $breadcrumb): void
    {
        $this->breadcrumbManager->add($breadcrumb);
    }

    /**
     * @return array<Breadcrumb>
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbManager->get();
    }

    /**
     * @return array<MetaTag>
     */
    public function getMetaTags(): array
    {
        return $this->metaTagManager->get();
    }

    /**
     * @return array<string, string>
     */
    public function getValidationStatus(): array
    {
        $status = [];

        // 1. Title validation
        $title = $this->getTitle();
        $titleLength = mb_strlen($title);
        if ($titleLength < 30) {
            $status['title'] = sprintf('Title is too short (%d characters). Recommended: 30-60 characters.', $titleLength);
        } elseif ($titleLength > 60) {
            $status['title'] = sprintf('Title is too long (%d characters). Recommended: 30-60 characters.', $titleLength);
        } else {
            $status['title'] = 'OK';
        }

        // 2. Meta tags validation
        $metaTags = $this->getMetaTags();
        $hasDescription = false;
        $hasKeywords = false;
        $robots = null;

        foreach ($metaTags as $metaTag) {
            if ($metaTag->getName() === 'description') {
                $hasDescription = true;
                $descLength = mb_strlen($metaTag->getContent());
                if ($descLength < 50) {
                    $status['description'] = sprintf('Description is too short (%d characters). Recommended: 50-160 characters.', $descLength);
                } elseif ($descLength > 160) {
                    $status['description'] = sprintf('Description is too long (%d characters). Recommended: 50-160 characters.', $descLength);
                } else {
                    $status['description'] = 'OK';
                }
            }

            if ($metaTag->getName() === 'keywords') {
                $hasKeywords = true;
                $status['keywords'] = 'OK';
            }

            if ($metaTag->getName() === 'robots') {
                $robots = $metaTag->getContent();
            }
        }

        if (!$hasDescription) {
            $status['description'] = 'Meta description is missing.';
        }

        if (!$hasKeywords) {
            $status['keywords'] = 'Meta keywords are missing.';
        }

        // 3. Robots validation
        if (null === $robots) {
            $status['robots'] = 'Robots tag is missing.';
        } elseif (str_contains($robots, 'noindex')) {
            $status['robots'] = 'Indexing not allowed';
        } else {
            $status['robots'] = 'Indexing is allowed';
        }

        return $status;
    }
}
