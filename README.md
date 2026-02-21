# Easy SEO Bundle

## Roadmap

- [x] Title
- [x] Meta Tags
- [ ] Breadcrumbs
- [ ] Schema.org
  - [ ] Website

## Installation

~~~shell
composer require mitopp/easy-seo-bundle
~~~

## Configuration

Create configuration file `config/packages/mitopp_easy_seo.yaml`.

~~~yaml
mitopp_easy_seo:
    title:
        # Required
        site: 'My website'
        # Optional. Default: '-'
        separator: '|'
        # Optional. Default: true
        append_site_title: false
    # Add global meta tags. Optional. Default: []
    meta_tags: []
~~~

## Usage

Inject the `SeoManagerInterface` into your controller and define your SEO tags.

~~~php
// Controller

#[AsController]
final class DefaultController extends AbstractController
{
    public const ROUTE_NAME = 'app_homepage';

    #[Route(path: '/', name: self::ROUTE_NAME)]
    public function __invoke(SeoManagerInterface $seoManager): Response
    {
        $seoManager->setPageTitle('Default page');
        $seoManager->addMetaTag(MetaTag::create('robots', 'index,follow'));
        $seoManager->addMetaTag(MetaTag::create('keywords', 'default,page'));
        $seoManager->addMetaTag(MetaTag::create('description', 'This is a default page.'));
        $seoManager->addMetaTag(MetaTag::create('author', 'John Doe'));
        $seoManager->addMetaTag(MetaTag::create('color-schema', 'light dark'));
        $seoManager->addMetaTag(MetaTag::create('generator', 'Symfony'));

        return $this->render('page/default/index.html.twig');
    }
}
~~~

Rendered as...

~~~html
<!-- ... -->
<head>
    <meta charset="UTF-8">
    <title>Default page - My website</title>
    <meta name="robots" content="index,follow">
    <meta name="keywords" content="default,page">
    <meta name="description" content="This is a default page.">
    <meta name="author" content="John Doe">
    <meta name="color-schema" content="light dark">
    <meta name="generator" content="Symfony">
</head>
<!-- ... -->
~~~

## Development

- Create a blank Symfony project at the same level as the Easy SEO Bundle.

~~~shell
composer create-project symfony/skeleton easy-seo-bundle-demo
~~~

- Configure Composer to use the local repository.

~~~json
// composer.json
{
    // ...
    "repositories": [
        {
            "type": "path",
            "url": "../easy-seo-bundle"
        }
    ]
    // ...
}
~~~

- Install the bundle.

~~~shell
composer require mitopp/easy-seo-bundle:@dev
~~~

- Dump the config reference from the bundle.

~~~shell
bin/console config:dump-reference EasySeoBundle
~~~

~~~shell
# Default configuration for extension with alias: "mitopp_easy_seo"

# Easy SEO configuration.
mitopp_easy_seo:      []
~~~

- Dump the current config from the bundle.

~~~shell
bin/console debug:config EasySeoBundle
~~~

~~~shell
Current configuration for extension with alias "mitopp_easy_seo"
================================================================

mitopp_easy_seo: {  }
~~~

- Debug available twig extensions.

~~~shell
bin/console debug:twig
~~~
