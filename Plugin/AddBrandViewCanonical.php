<?php declare(strict_types=1);

namespace Web200\Seo\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magiccart\Shopbrand\Controller\Brand\View;
use Magiccart\Shopbrand\Helper\Data;
use Magiccart\Shopbrand\Model\ShopbrandFactory;
use Web200\Seo\Provider\CanonicalConfig;

class AddBrandViewCanonical
{
    private PageConfig $pageConfig;
    private UrlInterface $urlBuilder;
    private Data $shopBrandHelper;
    private CanonicalConfig $canonicalConfig;
    private RequestInterface $request;
    private ShopbrandFactory $brandFactory;

    public function __construct(
        PageConfig $pageConfig,
        UrlInterface $urlBuilder,
        Data $shopBrandHelper,
        CanonicalConfig $canonicalConfig,
        RequestInterface $request,
        ShopbrandFactory $brandFactory
    ) {
        $this->pageConfig      = $pageConfig;
        $this->urlBuilder      = $urlBuilder;
        $this->shopBrandHelper = $shopBrandHelper;
        $this->canonicalConfig = $canonicalConfig;
        $this->request         = $request;
        $this->brandFactory    = $brandFactory;
    }

    public function beforeExecute(View $subject): void
    {
        if ($this->canonicalConfig->isBrandViewActive()) {
            $canonicalUrl = $this->getCanonicalUrl();
            if ($canonicalUrl) {
                $this->pageConfig->addRemotePageAsset(
                    $canonicalUrl,
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }
        }
    }

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
}
