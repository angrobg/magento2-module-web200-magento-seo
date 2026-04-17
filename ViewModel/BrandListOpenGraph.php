<?php declare(strict_types=1);

namespace Web200\Seo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magiccart\Shopbrand\Helper\Data;
use Web200\Seo\Helper\Data as SeoHelper;
use Web200\Seo\Model\Adapter\Page;

class BrandListOpenGraph implements ArgumentInterface
{
    private Data $brandHelper;
    private SeoHelper $seoHelper;

    public function __construct(
        Data $brandHelper,
        SeoHelper $seoHelper
    ) {
        $this->brandHelper = $brandHelper;
        $this->seoHelper = $seoHelper;
    }

    public function getImage(): ?string
    {
        $image = $this->seoHelper->getOgDefaultImage() ?: $this->seoHelper->getDefaultDesignLogo();

        if (!$image) {
            return null;
        }

        return $image;
    }

    public function getOgUrl(): ?string
    {
        return $this->brandHelper->getUrlRouter() ?: null;
    }

    public function getOgDescription(): ?string
    {
        return $this->brandHelper->getConfigModule(
            'general/description'
        ) ?: $this->seoHelper->getDesignDefaultDescription();
    }

    public function getOgType(): string
    {
        return Page::HOME_PAGE_TYPE;
    }

    public function getOgLocale(): string
    {
        return $this->seoHelper->getOgLocale();
    }

    public function getTitle()
    {
        $title = $this->brandHelper->getConfigModule('general/title');

        return $title ?: null;
    }
}