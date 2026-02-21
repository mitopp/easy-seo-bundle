<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Tests\Integration;

use Mitopp\EasySeoBundle\Domain\SeoManager;
use Mitopp\EasySeoBundle\EasySeoBundle;
use Mitopp\EasySeoBundle\Profiler\DataCollector\SeoDataCollector;
use Nyholm\BundleTest\TestKernel;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

#[CoversClass(EasySeoBundle::class)]
final class BundleInitializationTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        self::$class = null; // Kill used Kernel class
    }

    public function testBundleWithDefaultConfig(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $seoManager = $container->get(SeoManager::class);

        $this->assertTrue($container->has(SeoManager::class));
        $this->assertInstanceOf(SeoManager::class, $seoManager);
        $this->assertSame('', $seoManager->getTitle());
    }

    public function testBundleWithCustomConfig(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(static function (ContainerBuilder $container): void {
                    $container->loadFromExtension('mitopp_easy_seo', [
                        'title' => [
                            'site' => 'Custom Site Title',
                        ],
                    ]);
                });
            },
        ]);

        $container = self::getContainer();

        /** @var SeoManager $seoManager */
        $seoManager = $container->get(SeoManager::class);

        $this->assertSame('Custom Site Title', $seoManager->getTitle());
    }

    public function testBundleWithAnotherCustomConfig(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(static function (ContainerBuilder $container): void {
                    $container->loadFromExtension('mitopp_easy_seo', [
                        'title' => [
                            'site' => 'Another Site',
                            'separator' => '>>',
                            'append_site_title' => false,
                        ],
                    ]);
                });
            },
        ]);

        $container = self::getContainer();
        /** @var SeoManager $seoManager */
        $seoManager = $container->get(SeoManager::class);

        $seoManager->setPageTitle('Page');
        $this->assertSame('Another Site >> Page', $seoManager->getTitle());

        $config = $seoManager->getConfig();
        $this->assertSame('Another Site', $config->getSiteTitle());
        $this->assertSame('>>', $config->getSeparator());
        $this->assertFalse($config->isAppendSiteTitle());
    }

    public function testDataCollectorIsRegistered(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        $this->assertTrue($container->has(SeoDataCollector::class), 'SeoDataCollector should be registered in the container.');

        $collector = $container->get(SeoDataCollector::class);
        $this->assertInstanceOf(SeoDataCollector::class, $collector);
    }

    public function testTwigNamespaceIsRegistered(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(static function (ContainerBuilder $container): void {
                    $container->loadFromExtension('framework', [
                        'secret' => 'test',
                        'test' => true,
                    ]);
                });
            },
        ]);
        $container = self::getContainer();

        /** @var Environment $twig */
        $twig = $container->get('twig');
        $loader = $twig->getLoader();

        if ($loader instanceof FilesystemLoader) {
            $paths = $loader->getNamespaces();
            $this->assertContains('EasySeo', $paths, 'The EasySeo Twig namespace should be registered.');
        }
    }

    public function testBundleWithMetaTagsConfig(): void
    {
        self::bootKernel([
            'config' => static function (TestKernel $kernel): void {
                $kernel->addTestConfig(static function (ContainerBuilder $container): void {
                    $container->loadFromExtension('mitopp_easy_seo', [
                        'meta_tags' => [
                            'robots' => 'index,follow',
                            'keywords' => 'default,page',
                            'description' => 'This is a default page.',
                            'author' => 'John Doe',
                            'color-schema' => 'light dark',
                            'generator' => 'Symfony',
                        ],
                    ]);
                });
            },
        ]);

        $container = self::getContainer();
        $this->assertTrue($container->hasParameter('seo.meta_tags'));
        $this->assertSame([
            'robots' => 'index,follow',
            'keywords' => 'default,page',
            'description' => 'This is a default page.',
            'author' => 'John Doe',
            'color-schema' => 'light dark',
            'generator' => 'Symfony',
        ], $container->getParameter('seo.meta_tags'));
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(EasySeoBundle::class);
        $kernel->addTestBundle(FrameworkBundle::class);
        $kernel->addTestBundle(TwigBundle::class);
        $kernel->handleOptions($options);
        $kernel->setClearCacheAfterShutdown(true);

        return $kernel;
    }

    protected static function ensureKernelShutdown(): void
    {
        $wasBooted = self::$booted;

        parent::ensureKernelShutdown();

        if ($wasBooted) {
            restore_exception_handler();
        }
    }
}
