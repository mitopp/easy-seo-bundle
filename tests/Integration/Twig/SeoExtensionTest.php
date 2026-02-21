<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Tests\Integration\Twig;

use Mitopp\EasySeoBundle\Domain\Breadcrumb\Breadcrumb;
use Mitopp\EasySeoBundle\Domain\SeoManagerInterface;
use Mitopp\EasySeoBundle\Twig\SeoExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\Markup;

#[CoversClass(SeoExtension::class)]
final class SeoExtensionTest extends TestCase
{
    private \PHPUnit\Framework\MockObject\MockObject $seoManager;

    private SeoExtension $extension;

    protected function setUp(): void
    {
        $this->seoManager = $this->createMock(SeoManagerInterface::class);
        $this->extension = new SeoExtension($this->seoManager);
    }

    public function testGenerateBreadcrumbsIncludesJsonLd(): void
    {
        $breadcrumbs = [
            new Breadcrumb('Home', null, 'https://example.com/'),
            new Breadcrumb('Products', null, 'https://example.com/products'),
            new Breadcrumb('Product A', null),
        ];

        $this->seoManager->method('getBreadcrumbs')->willReturn($breadcrumbs);

        $result = $this->extension->generateBreadcrumbs();
        $this->assertInstanceOf(Markup::class, $result);
        $content = (string) $result;

        // Check for HTML parts
        $this->assertStringContainsString('<ol class="breadcrumb" aria-label="breadcrumb">', $content);
        $this->assertStringContainsString('<li class="breadcrumb-item"><a href="https://example.com/">Home</a></li>', $content);
        $this->assertStringContainsString('<li class="breadcrumb-item"><a href="https://example.com/products">Products</a></li>', $content);
        $this->assertStringContainsString('<li class="breadcrumb-item active" aria-current="page">Product A</li>', $content);

        // Check for JSON-LD script tag
        $this->assertStringContainsString('<script type="application/ld+json">', $content);

        // Extract and verify JSON-LD data
        $jsonPart = '';
        if (preg_match('/<script type="application\/ld\+json">(.*)<\/script>/s', $content, $matches)) {
            $jsonPart = $matches[1];
        }

        $this->assertNotEmpty($jsonPart);
        $data = json_decode($jsonPart, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('http://schema.org', $data['@context']);
        $this->assertSame('BreadcrumbList', $data['@type']);
        $this->assertCount(3, $data['itemListElement']);
        $this->assertSame(1, $data['itemListElement'][0]['position']);
        $this->assertSame('Home', $data['itemListElement'][0]['name']);
        $this->assertSame('https://example.com/', $data['itemListElement'][0]['item']);
    }
}
