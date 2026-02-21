<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Tests\Unit\Domain\Breadcrumb;

use Mitopp\EasySeoBundle\Domain\Breadcrumb\Breadcrumb;
use Mitopp\EasySeoBundle\Domain\Breadcrumb\BreadcrumbManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BreadcrumbManager::class)]
final class BreadcrumbManagerTest extends TestCase
{
    private BreadcrumbManager $breadcrumbManager;

    protected function setUp(): void
    {
        $this->breadcrumbManager = new BreadcrumbManager();
    }

    public function testAddBreadcrumb(): void
    {
        $breadcrumb = new Breadcrumb('Home', '/');
        $this->breadcrumbManager->add($breadcrumb);

        $this->assertCount(1, $this->breadcrumbManager->get());
        $this->assertSame($breadcrumb, $this->breadcrumbManager->get()[0]);
    }
}
