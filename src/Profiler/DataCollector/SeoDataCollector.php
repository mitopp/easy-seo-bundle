<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Profiler\DataCollector;

use Mitopp\EasySeoBundle\Domain\Breadcrumb\Breadcrumb;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTag;
use Mitopp\EasySeoBundle\Domain\SeoManagerInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SeoDataCollector extends AbstractDataCollector
{
    public function __construct(
        private readonly SeoManagerInterface $seoManager,
    ) {
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $status = $this->seoManager->getValidationStatus();
        $formattedStatus = [
            'info' => [],
            'warning' => [],
            'error' => [],
        ];

        foreach ($status as $key => $message) {
            if ($message === 'OK' || str_contains($message, 'is allowed')) {
                $formattedStatus['info'][] = sprintf('%s: %s', ucfirst($key), $message);
            } elseif (str_contains($message, 'missing')) {
                $formattedStatus['error'][] = sprintf('%s: %s', ucfirst($key), $message);
            } else {
                $formattedStatus['warning'][] = sprintf('%s: %s', ucfirst($key), $message);
            }
        }

        $this->data = [
            'status' => $formattedStatus,
            'config' => $this->seoManager->getConfigAsArray(),
            'title' => $this->seoManager->getTitle(),
            'meta_tags' => $this->seoManager->getMetaTags(),
            'breadcrumbs' => $this->seoManager->getBreadcrumbs(),
        ];
    }

    public function getName(): string
    {
        return self::class;
    }

    public static function getTemplate(): string
    {
        return '@EasySeo/profiler/data_collector/seo.html.twig';
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfigAsArray(): array
    {
        /** @var array<string, mixed> $config */
        $config = $this->data['config'];

        return $config;
    }

    public function getTitle(): string
    {
        /** @var string $title */
        $title = $this->data['title'];

        return $title;
    }

    /**
     * @return array<MetaTag>
     */
    public function getMetaTags(): array
    {
        /** @var array<MetaTag> $metaTags */
        $metaTags = $this->data['meta_tags'];

        return $metaTags;
    }

    /**
     * @return array<Breadcrumb>
     */
    public function getBreadcrumbs(): array
    {
        /** @var array<Breadcrumb> $breadcrumbs */
        $breadcrumbs = $this->data['breadcrumbs'];

        return $breadcrumbs;
    }

    /**
     * @return array<string, array<string>>
     */
    public function getStatus(): array
    {
        /** @var array<string, array<string>> $status */
        $status = $this->data['status'];

        return $status;
    }
}
