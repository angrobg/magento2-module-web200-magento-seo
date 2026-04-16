<?php

declare(strict_types=1);

namespace Web200\Seo\Model\Adapter;

use Magento\Cms\Model\Page as CmsPage;
use Magento\Framework\UrlInterface;
use Web200\Seo\Api\Data\AdapterInterface;
use Web200\Seo\Api\Data\PropertyInterface;
use Web200\Seo\Helper\Data;
use Web200\Seo\Model\Property;
use Web200\Seo\Model\Store\LocaleProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Page
 *
 * @package   Web200\Seo\Model\Adapter
 * @author    Web200 <contact@web200.fr>
 * @copyright 2021 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Page implements AdapterInterface
{
    const HOME_PAGE_TYPE = 'website';
    const CMS_PAGE_TYPE = 'article';
    /**
     * Property interface
     *
     * @var PropertyInterface $property
     */
    protected $property;
    /**
     * Cms page
     *
     * @var CmsPage $page
     */
    protected $page;
    /**
     * Url interface
     *
     * @var UrlInterface $url
     */
    protected $url;

    /**
     * @var LocaleProvider
     */
    private LocaleProvider $localeProvider;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var Data
     */
    private Data $helper;

    /**
     * Page constructor.
     *
     * @param CmsPage $page
     * @param UrlInterface $url
     * @param PropertyInterface $property
     * @param LocaleProvider $localeProvider
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     */
    public function __construct(
        CmsPage $page,
        UrlInterface $url,
        PropertyInterface $property,
        LocaleProvider $localeProvider,
        ScopeConfigInterface $scopeConfig,
        Data $helper
    ) {
        $this->property = $property;
        $this->page     = $page;
        $this->url      = $url;
        $this->localeProvider = $localeProvider;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * Get property
     *
     * @return PropertyInterface
     */
    public function getProperty(): PropertyInterface
    {
        if ($this->page->getId()) {
            $this->property->setTitle((string)$this->page->getTitle());
            $this->property->setDescription((string)$this->page->getMetaDescription());
            $this->property->setUrl((string)$this->url->getUrl($this->page->getIdentifier()));
            $this->property->addProperty('item', $this->page->getData(), Property::META_DATA_GROUP);

            $type = self::CMS_PAGE_TYPE;
            $ogImage = $this->helper->getOgDefaultImage();

            if ($this->isHomePage($this->page)) {
                $type = self::HOME_PAGE_TYPE;
                $ogImage = $this->page->getOpenGraphImageUrl();
            }

            if ($ogImage) {
                $this->property->addProperty('image', $ogImage);
            }

            $this->property->addProperty('type', $type);
            $locale = $this->localeProvider->getOgLocale();

            if ($locale) {
                $this->property->addProperty('locale', $this->localeProvider->getOgLocale());
            }

        }

        return $this->property;
    }

    private function isHomePage(CmsPage $page): bool
    {
        $homePageIdentifier = (string)$this->scopeConfig->getValue(
            'web/default/cms_home_page',
            ScopeInterface::SCOPE_STORE
        );

        return $page->getIdentifier() === $homePageIdentifier;
    }
}
