<?php

declare(strict_types=1);

namespace Web200\Seo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

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
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    public function __construct(
        PageConfig $pageConfig,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->pageConfig = $pageConfig;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
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
        return 'website';
    }

    public function getOgLocale(): string
    {
        return (string)$this->storeManager
            ->getStore()
            ->getConfig('general/locale/code');
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
