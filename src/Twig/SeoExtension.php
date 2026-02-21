<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Twig;

use Mitopp\EasySeoBundle\Domain\SeoManagerInterface;
use Twig\Attribute\AsTwigFunction;
use Twig\Markup;

final readonly class SeoExtension
{
    public function __construct(
        private SeoManagerInterface $seoManager,
    ) {
    }

    #[AsTwigFunction(name: 'easy_seo_title')]
    public function generatePageTitle(): string
    {
        return $this->seoManager->getTitle();
    }

    #[AsTwigFunction(name: 'easy_seo_meta_tags')]
    public function generateMetaTags(): Markup
    {
        $rendered = '';

        foreach ($this->seoManager->getMetaTags() as $metaTag) {
            $rendered .= sprintf('<meta name="%s" content="%s">', $metaTag->getName(), $metaTag->getContent());
        }

        return new Markup($rendered, 'UTF-8');
    }

    #[AsTwigFunction(name: 'easy_seo_breadcrumbs')]
    public function generateBreadcrumbs(): Markup
    {
        $breadcrumbs = $this->seoManager->getBreadcrumbs();
        $rendered = '<ol class="breadcrumb" aria-label="breadcrumb">';

        $itemListElement = [];
        $total = count($breadcrumbs);
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $isLast = ($index === $total - 1);
            $classes = ['breadcrumb-item'];
            if ($isLast) {
                $classes[] = 'active';
            }

            $ariaCurrent = $isLast ? ' aria-current="page"' : '';

            if ($isLast || null === $breadcrumb->getUrl()) {
                $rendered .= sprintf(
                    '<li class="%s"%s>%s</li>',
                    implode(' ', $classes),
                    $ariaCurrent,
                    $breadcrumb->getLabel()
                );
            } elseif (null === $breadcrumb->getTitle()) {
                $rendered .= sprintf(
                    '<li class="%s"><a href="%s">%s</a></li>',
                    implode(' ', $classes),
                    $breadcrumb->getUrl(),
                    $breadcrumb->getLabel()
                );
            } else {
                $rendered .= sprintf(
                    '<li class="%s"><a href="%s" title="%s">%s</a></li>',
                    implode(' ', $classes),
                    $breadcrumb->getUrl(),
                    $breadcrumb->getTitle(),
                    $breadcrumb->getLabel()
                );
            }

            $item = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb->getLabel(),
            ];

            if (null !== $breadcrumb->getUrl()) {
                $item['item'] = $breadcrumb->getUrl();
            }

            $itemListElement[] = $item;
        }

        $rendered .= '</ol>';

        $data = [
            '@context' => 'http://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];

        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $rendered .= sprintf('<script type="application/ld+json">%s</script>', $json);
        } catch (\JsonException) {
            // should never happen
        }

        return new Markup($rendered, 'UTF-8');
    }
}
