<?php

declare(strict_types=1);

namespace Web200\Seo\Plugin;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magiccart\Shopbrand\Controller\Brand\ListBrand;
use Magiccart\Shopbrand\Helper\Data;
use Web200\Seo\Provider\CanonicalConfig;

class AddBrandListCanonical
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
     * @var Data
     */
    private Data $shopBrandHelper;
    private CanonicalConfig $canonicalConfig;

    /**
     * @param PageConfig $pageConfig
     * @param UrlInterface $urlBuilder
     * @param Data $shopBrandHelper
     */
    public function __construct(
        PageConfig $pageConfig,
        UrlInterface $urlBuilder,
        Data $shopBrandHelper,
        CanonicalConfig $canonicalConfig
    ) {
        $this->pageConfig = $pageConfig;
        $this->urlBuilder = $urlBuilder;
        $this->shopBrandHelper = $shopBrandHelper;
        $this->canonicalConfig = $canonicalConfig;
    }

    /**
     * @param ListBrand $subject
     * @param $page
     * @return mixed
     */
    public function afterExecute(ListBrand $subject, $page)
    {
        if (!$this->canonicalConfig->isBrandListActive()) {
            return $page;
        }

        if (!$page->getLayout()) {
            return $page;
        }

        $this->pageConfig->addRemotePageAsset(
            $this->getCanonicalUrl(),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        return $page;
    }

    /**
     * @return string
     */
    private function getCanonicalUrl(): string
    {
        $router = trim((string)$this->shopBrandHelper->getRouter(), '/');

        return $this->urlBuilder->getUrl($router);
    }
}
