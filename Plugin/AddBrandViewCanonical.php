<?php declare(strict_types=1);

namespace Web200\Seo\Plugin;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Theme\Block\Html\Pager;
use Magiccart\Shopbrand\Block\Product\ListProduct;
use Magiccart\Shopbrand\Controller\Brand\View;
use Magiccart\Shopbrand\Helper\Data;
use Magiccart\Shopbrand\Model\ShopbrandFactory;
use Web200\Seo\Provider\CanonicalConfig;

class AddBrandViewCanonical
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

    /**
     * @var CanonicalConfig
     */
    private CanonicalConfig $canonicalConfig;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ShopbrandFactory
     */
    private ShopbrandFactory $brandFactory;

    /**
     * @var ViewInterface
     */
    private ViewInterface $view;

    /**
     * @param PageConfig $pageConfig
     * @param UrlInterface $urlBuilder
     * @param Data $shopBrandHelper
     * @param CanonicalConfig $canonicalConfig
     * @param RequestInterface $request
     * @param ShopbrandFactory $brandFactory
     * @param ViewInterface $view
     */
    public function __construct(
        PageConfig $pageConfig,
        UrlInterface $urlBuilder,
        Data $shopBrandHelper,
        CanonicalConfig $canonicalConfig,
        RequestInterface $request,
        ShopbrandFactory $brandFactory,
        ViewInterface $view
    ) {
        $this->pageConfig      = $pageConfig;
        $this->urlBuilder      = $urlBuilder;
        $this->shopBrandHelper = $shopBrandHelper;
        $this->canonicalConfig = $canonicalConfig;
        $this->request         = $request;
        $this->brandFactory    = $brandFactory;
        $this->view            = $view;
    }

    public function afterExecute(View $subject, $result)
    {
        if (!$this->canonicalConfig->isBrandViewActive()) {
            return $result;
        }

        $layout = $this->view->getLayout();
        if (!$layout) {
            return $result;
        }

        $canonicalUrl = $this->getCanonicalUrl();
        if (!$canonicalUrl) {
            return $result;
        }

        // pagination rel disabled — just add plain canonical without ?p= and stop
        if (!$this->canonicalConfig->isBrandRelPagination()) {
            $this->addCanonical($canonicalUrl);
            return $result;
        }

        // pagination rel enabled — try to get pager blocks
        /** @var ListProduct $productListBlock */
        $productListBlock = $layout->getBlock('shopbrand.products.list');
        if (!$productListBlock) {
            $this->addCanonical($canonicalUrl);
            return $result;
        }

        /** @var Toolbar $toolbarBlock */
        $toolbarBlock = $layout->getBlock('product_list_toolbar');
        if (!$toolbarBlock) {
            $this->addCanonical($canonicalUrl);
            return $result;
        }

        /** @var Pager $pagerBlock */
        $pagerBlock = $toolbarBlock->getChildBlock('product_list_toolbar_pager');
        if (!$pagerBlock) {
            $this->addCanonical($canonicalUrl);
            return $result;
        }

        $brandId = (int)$this->request->getParam('id');
        $brand   = $this->brandFactory->create()->load($brandId);

        $pagerBlock->setAvailableLimit($toolbarBlock->getAvailableLimit())
            ->setCollection($brand->getProductCollection());

        // canonical points to current page — page 1 is clean URL, page 2+ gets ?p=N
        $this->addCanonical(
            $this->getPageUrl($pagerBlock->getPageVarName(), $pagerBlock->getCurrentPage(), $canonicalUrl)
        );

        // rel=prev — only added when not on first page
        if ($pagerBlock->getCurrentPage() > 1) {
            $this->pageConfig->addRemotePageAsset(
                $this->getPageUrl(
                    $pagerBlock->getPageVarName(),
                    $pagerBlock->getCollection()->getCurPage(-1),
                    $canonicalUrl
                ),
                'link_rel',
                ['attributes' => ['rel' => 'prev']]
            );
        }

        // rel=next — only added when not on last page
        if ($pagerBlock->getCurrentPage() < $pagerBlock->getLastPageNum()) {
            $this->pageConfig->addRemotePageAsset(
                $this->getPageUrl(
                    $pagerBlock->getPageVarName(),
                    $pagerBlock->getCollection()->getCurPage(+1),
                    $canonicalUrl
                ),
                'link_rel',
                ['attributes' => ['rel' => 'next']]
            );
        }

        return $result;
    }

    /**
     * Add canonical tag to head of page
     *
     * @param string $url
     * @return void
     */
    private function addCanonical(string $url): void
    {
        $this->pageConfig->addRemotePageAsset(
            $url,
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
    }

    /**
     * Get canonical url
     *
     * @return string
     */
    private function getCanonicalUrl(): string
    {
        $router    = trim((string)$this->shopBrandHelper->getRouter(), '/');
        $urlSuffix = (string)$this->shopBrandHelper->getUrlSuffix();
        $brandId   = (int)$this->request->getParam('id');

        if (!$brandId) {
            return '';
        }

        $brand  = $this->brandFactory->create()->load($brandId);
        $urlKey = $brand->getData('urlkey');

        if (!$urlKey) {
            return '';
        }

        return $this->urlBuilder->getBaseUrl() . $router . '/' . $urlKey . $urlSuffix;
    }

    /**
     * Get page url
     *
     * @param string $varName
     * @param int $page
     * @param string $baseUrl
     * @return string
     */
    private function getPageUrl(string $varName, int $page, string $baseUrl): string
    {
        if ($page <= 1) {
            return $baseUrl;
        }

        return $baseUrl . '?' . $varName . '=' . $page;
    }
}
