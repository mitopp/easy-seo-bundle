<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Tests\Unit\Domain;

use Mitopp\EasySeoBundle\Domain\Breadcrumb\Breadcrumb;
use Mitopp\EasySeoBundle\Domain\Breadcrumb\BreadcrumbManagerInterface;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTag;
use Mitopp\EasySeoBundle\Domain\Meta\MetaTagManagerInterface;
use Mitopp\EasySeoBundle\Domain\SeoManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SeoManager::class)]
final class SeoManagerTest extends TestCase
{
    public function testAddMetaTagAvoidsDuplicates(): void
    {
        $breadcrumbManager = $this->createMock(BreadcrumbManagerInterface::class);
        $metaTagManager = $this->createMock(MetaTagManagerInterface::class);

        $seoManager = $this->createSeoManager('My Site', '|', true, $breadcrumbManager, $metaTagManager);

        $metaTag = new MetaTag('robots', 'noindex,nofollow');

        $metaTagManager->expects($this->once())
            ->method('add')
            ->with($metaTag);

        $metaTagManager->expects($this->once())
            ->method('get')
            ->willReturn([$metaTag]);

        $seoManager->addMetaTag($metaTag);

        $metaTags = $seoManager->getMetaTags();

        $this->assertCount(1, $metaTags);
        $this->assertSame('noindex,nofollow', $metaTags[0]->getContent());
    }

    public function testGetTitleReturnsSiteTitleIfNoPageTitleIsSet(): void
    {
        $breadcrumbManager = $this->createMock(BreadcrumbManagerInterface::class);
        $seoManager = $this->createSeoManager('My Site', '|', true, $breadcrumbManager);

        $this->assertSame('My Site', $seoManager->getTitle());
    }

    public function testGetTitleAppendsSiteTitle(): void
    {
        $breadcrumbManager = $this->createMock(BreadcrumbManagerInterface::class);
        $seoManager = $this->createSeoManager('My Site', '|', true, $breadcrumbManager);
        $seoManager->setPageTitle('Home');

        $this->assertSame('Home | My Site', $seoManager->getTitle());
    }

    public function testGetTitlePrependsSiteTitle(): void
    {
        $breadcrumbManager = $this->createMock(BreadcrumbManagerInterface::class);
        $seoManager = $this->createSeoManager('My Site', '|', false, $breadcrumbManager);
        $seoManager->setPageTitle('Home');

        $this->assertSame('My Site | Home', $seoManager->getTitle());
    }

    public function testGetTitleWithHtmlEntitySeparator(): void
    {
        $breadcrumbManager = $this->createMock(BreadcrumbManagerInterface::class);
        $seoManager = $this->createSeoManager('My Site', '&raquo;', true, $breadcrumbManager);
        $seoManager->setPageTitle('Home');

        // html_entity_decode('&raquo;') results in »
        $this->assertSame('Home » My Site', $seoManager->getTitle());
    }

    public function testBreadcrumbDelegation(): void
    {
        $breadcrumbManager = $this->createMock(BreadcrumbManagerInterface::class);
        $seoManager = $this->createSeoManager('My Site', '|', true, $breadcrumbManager);

        $breadcrumb = new Breadcrumb('Label', '/url');

        $breadcrumbManager->expects($this->once())
            ->method('add')
            ->with($breadcrumb);

        $breadcrumbManager->expects($this->once())
            ->method('get')
            ->willReturn([$breadcrumb]);

        $seoManager->addBreadcrumb($breadcrumb);
        $this->assertSame([$breadcrumb], $seoManager->getBreadcrumbs());
    }

    public function testGetValidationStatus(): void
    {
        $breadcrumbManager = $this->createMock(BreadcrumbManagerInterface::class);
        $metaTagManager = $this->createMock(MetaTagManagerInterface::class);

        $seoManager = $this->createSeoManager('My Site Title', '|', true, $breadcrumbManager, $metaTagManager);

        // Scenario 1: Empty / Defaults
        $metaTagManager->method('get')->willReturn([]);
        $status = $seoManager->getValidationStatus();

        $this->assertArrayHasKey('title', $status);
        $this->assertArrayHasKey('description', $status);
        $this->assertArrayHasKey('keywords', $status);
        $this->assertArrayHasKey('robots', $status);

        // Title is "My Site Title" (13 chars) - too short (expecting > 30)
        $this->assertStringContainsString('short', $status['title']);

        // Description and Keywords are missing
        $this->assertStringContainsString('missing', $status['description']);
        $this->assertStringContainsString('missing', $status['keywords']);

        // Robots missing - default should probably be "index" but we want it explicit
        $this->assertStringContainsString('missing', $status['robots']);

        // Scenario 2: Valid setup
        $metaTagManager = $this->createMock(MetaTagManagerInterface::class);
        $seoManager = $this->createSeoManager('Site Title', '|', true, $breadcrumbManager, $metaTagManager);
        $seoManager->setPageTitle('A page title with valid length');

        $metaTagManager->method('get')->willReturn([
            new MetaTag('description', 'This is a valid description that has enough length to be accepted by seo standards.'),
            new MetaTag('keywords', 'seo, symfony, bundle'),
            new MetaTag('robots', 'index, follow'),
        ]);

        $status = $seoManager->getValidationStatus();
        // Title: "A page title with valid length | Site Title" -> 27 + 3 + 10 = 40 chars. OK.
        $this->assertSame('OK', $status['title']);
        $this->assertSame('OK', $status['description']);
        $this->assertSame('OK', $status['keywords']);
        $this->assertSame('Indexing is allowed', $status['robots']);

        // Scenario 3: Noindex
        $metaTagManager = $this->createMock(MetaTagManagerInterface::class);
        $seoManager = $this->createSeoManager('My Site Title', '|', true, $breadcrumbManager, $metaTagManager);
        $metaTagManager->method('get')->willReturn([
            new MetaTag('robots', 'noindex, follow'),
        ]);
        $status = $seoManager->getValidationStatus();
        $this->assertSame('Indexing not allowed', $status['robots']);
    }

    private function createSeoManager(
        string $siteTitle,
        string $separator,
        bool $appendSiteTitle,
        BreadcrumbManagerInterface $breadcrumbManager,
        MetaTagManagerInterface $metaTagManager = null,
    ): SeoManager {
        return new SeoManager(
            $breadcrumbManager,
            $metaTagManager ?? $this->createMock(MetaTagManagerInterface::class),
            $siteTitle,
            $separator,
            $appendSiteTitle,
            []
        );
    }
}
