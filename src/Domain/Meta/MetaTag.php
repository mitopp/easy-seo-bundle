<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain\Meta;

/**
 * Create a html meta tag
 *
 * @example <meta name="robots" content="index, follow">
 * @example <meta name="color-scheme" content="light dark">
 * @example <meta name="keywords" content="one,two,three">
 * @example <meta name="description" content="lorem ipsum dolor sit amet">
 */
final readonly class MetaTag
{
    public function __construct(
        private string $name,
        private string $content,
    ) {
    }

    public static function create(string $name, string $content): self
    {
        return new self($name, $content);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
