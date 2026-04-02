<?php

declare(strict_types=1);

namespace Web200\Seo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\UrlInterface;
use Web200\Seo\Model\Adapter\Page;
use Web200\Seo\Model\Store\LocaleProvider;

class OpenGraph implements ArgumentInterface
{
    /**
     * @var PageConfig
     */
    private PageConfig $pageConfig;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;


    /**
     * @var LocaleProvider
     */
    private LocaleProvider $localeProvider;

    public function __construct(
        PageConfig $pageConfig,
        UrlInterface $urlBuilder,
        LocaleProvider $localeProvider
    ) {
        $this->pageConfig = $pageConfig;
        $this->urlBuilder = $urlBuilder;
        $this->localeProvider = $localeProvider;
    }

    public function getOgTitle(): string
    {
        return trim((string)$this->pageConfig->getTitle()->get());
    }

    public function getOgDescription(): string
    {
        return trim((string)$this->pageConfig->getDescription());
    }

    public function getOgUrl(): string
    {
        return $this->urlBuilder->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true
        ]);
    }

    public function getOgType(): string
    {
        return Page::CMS_PAGE_TYPE;
    }

    public function getOgLocale(): string
    {
        return $this->localeProvider->getOgLocale();
    }

    public function hasOgTitle(): bool
    {
        return $this->getOgTitle() !== '';
    }

    public function hasOgDescription(): bool
    {
        return $this->getOgDescription() !== '';
    }
}
