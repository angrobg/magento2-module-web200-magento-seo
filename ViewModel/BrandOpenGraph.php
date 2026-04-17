<?php declare(strict_types=1);

namespace Web200\Seo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;
use Magiccart\Shopbrand\Helper\Data;
use Magiccart\Shopbrand\Model\ShopbrandFactory;
use Web200\Seo\Helper\Data as SeoHelper;
use Web200\Seo\Model\Adapter\Page;

class BrandOpenGraph implements ArgumentInterface
{
    private RequestInterface $request;
    private ShopbrandFactory $shopbrandFactory;

    private $brand = null;
    private Data $brandHelper;
    private SeoHelper $seoHelper;

    public function __construct(
        RequestInterface $request,
        ShopbrandFactory $shopbrandFactory,
        Data $brandHelper,
        SeoHelper $seoHelper
    ) {
        $this->request = $request;
        $this->shopbrandFactory = $shopbrandFactory;
        $this->brandHelper = $brandHelper;
        $this->seoHelper = $seoHelper;
    }

    public function getBrand()
    {
        if ($this->brand !== null) {
            return $this->brand;
        }

        $brandId = (int)$this->request->getParam('id');
        if (!$brandId) {
            return null;
        }

        $this->brand = $this->shopbrandFactory->create()->load($brandId);

        return $this->brand;
    }

    public function getImage(): ?string
    {
        if ($this->brand === null) {
            return null;
        }

        $image = $this->brand->getImage();
        if (!$image) {
            return null;
        }

        return $this->brandHelper->getMediaUrl($image) ?: null;
    }

    public function getOgUrl()
    {
        if ($this->brand === null) {
            return null;
        }

        $urlKey = $this->brand->getData('urlkey');

        if (!$urlKey) {
            return null;
        }

        return $this->brandHelper->getBrandUrl($urlKey) ?: null;
    }

    public function getOgDescription(): ?string
    {
        if ($this->brand === null) {
            return null;
        }

        $description = $this->brand->getDescription();

        if (!$description) {
            return null;
        }

        return $this->seoHelper->prepareOgDescription($description) ?: null;
    }

    public function getOgType(): string
    {
        return Page::HOME_PAGE_TYPE;
    }

    public function getOgLocale(): string
    {
        return $this->seoHelper->getOgLocale();
    }
}