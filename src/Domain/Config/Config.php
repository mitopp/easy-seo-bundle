<?php

declare(strict_types=1);

namespace Mitopp\EasySeoBundle\Domain\Config;

final readonly class Config
{
    public function __construct(
        private string $siteTitle,
        private string $separator,
        private bool $appendSiteTitle,
    ) {
    }

    public function isAppendSiteTitle(): bool
    {
        return $this->appendSiteTitle;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function getSiteTitle(): string
    {
        return $this->siteTitle;
    }

    /**
     * @return array<string, mixed>
     */
    public function asArray(): array
    {
        return [
            'Site title' => $this->siteTitle,
            'Title separator' => $this->separator,
            'Append site title' => $this->appendSiteTitle ? 'yes' : 'no',
        ];
    }
}
