<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain\Breadcrumb;

final readonly class Breadcrumb
{
    public function __construct(
        private string $label,
        private ?string $title = null,
        private ?string $url = null,
    ) {
    }

    public static function create(string $label, string $title = null, string $url = null): self
    {
        return new self($label, $title, $url);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
