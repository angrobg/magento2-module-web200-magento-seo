<?php declare(strict_types=1);

namespace Web200\Seo\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Config\Model\Config\Backend\Image\Logo;
use Web200\Seo\Model\Config\Backend\Image;
use Web200\Seo\Model\Store\LocaleProvider;

class Data extends AbstractHelper
{
    public const XML_PATH_CONFIG_GENERAL_ACTIVE = 'seo/general/active';
    public const XML_PATH_CONFIG_GENERAL_REMOVE_NATIVE_RICH_SNIPPET = 'seo/general/remove_native_rs';
    public const XML_PATH_CONFIG_GENERAL_IMAGE_PATH = 'seo/general/image_path';
    public const XML_PATH_CONFIG_GENERAL_OG_IMAGE_PATH = 'seo/general/og_default_image';

    protected ?StoreInterface $store = null;
    private StoreManagerInterface $storeManager;
    private LocaleProvider $localeProvider;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        LocaleProvider $localeProvider
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->localeProvider = $localeProvider;
    }

    public function isModuleActive($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_GENERAL_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isRemoveNativeRichSnippets($storeId = null): bool
    {
        return $this->isModuleActive($storeId)
            && $this->scopeConfig->isSetFlag(
                self::XML_PATH_CONFIG_GENERAL_REMOVE_NATIVE_RICH_SNIPPET,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    /**
     * Retrieves the base URL of the application.
     *
     * @return string The base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->_urlBuilder->getBaseUrl();
    }

    /**
     * Retrieves the current store instance.
     *
     * @return StoreInterface The current store instance.
     */
    public function getStore(): StoreInterface
    {
        if ($this->store === null) {
            $this->store = $this->storeManager->getStore();
        }

        return $this->store;
    }

    /**
     * Retrieves the website URL of the application.
     *
     * @return string The website URL.
     */
    public function getWebsiteUrl(): string
    {
        return $this->_urlBuilder->getBaseUrl([
            '_type' => UrlInterface::URL_TYPE_WEB,
        ]);
    }

    /**
     * Retrieves the logo image path
     *
     * @return string The Logo URL.
     */
    public function getStoreLogoUrl($storeId = null): string
    {
        $seoLogoPath = $this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_GENERAL_IMAGE_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($seoLogoPath !== null) {
            $path = Image::UPLOAD_DIR . '/' . $seoLogoPath;

            return $this->_urlBuilder->getBaseUrl([
                    '_type' => UrlInterface::URL_TYPE_MEDIA,
                ]) . $path;
        }

        return $this->getDefaultDesignLogo($storeId);
    }

    public function getOgDefaultImage($storeId = null): ?string
    {
        $defaultOgImagePath = $this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_GENERAL_OG_IMAGE_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($defaultOgImagePath !== null) {
            $path = Image::UPLOAD_DIR . '/' . $defaultOgImagePath;

            return $this->_urlBuilder->getBaseUrl([
                    '_type' => UrlInterface::URL_TYPE_MEDIA,
                ]) . $path;
        }

        return $this->getDefaultDesignLogo($storeId);
    }

    public function getDefaultDesignLogo($storeId = null): ?string
    {
        $headerLogoPath = $this->scopeConfig->getValue(
            'design/header/logo_src',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($headerLogoPath !== null) {
            $path = Logo::UPLOAD_DIR . '/' . $headerLogoPath;

            return $this->_urlBuilder->getBaseUrl([
                    '_type' => UrlInterface::URL_TYPE_MEDIA,
                ]) . $path;
        }

        return '';
    }

    public function prepareOgDescription(?string $text, int $limit = 200): string
    {
        if (!$text) {
            return '';
        }

        // Remove style and script blocks
        $text = preg_replace('#<style\b[^>]*>.*?</style>#is', ' ', $text);
        $text = preg_replace('#<script\b[^>]*>.*?</script>#is', ' ', $text);

        // Remove Magento widgets {{widget ...}}
        $text = preg_replace('/\{\{.*?\}\}/s', ' ', $text);

        // Remove CSS-like content (very important for Page Builder)
        $text = preg_replace('/\.[a-zA-Z0-9\-_]+\s*\{[^}]+\}/', ' ', $text);

        // Remove HTML
        $text = strip_tags($text);

        // Decode entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Replace bullets
        $text = preg_replace('/[∙•]+/u', ', ', $text);

        // Clean spacing
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text, " ,");

        // Cut to 200 chars (word-safe)
        if (mb_strlen($text) > $limit) {
            $cut = mb_substr($text, 0, $limit);
            $lastSpace = mb_strrpos($cut, ' ');
            $text = ($lastSpace !== false ? mb_substr($cut, 0, $lastSpace) : $cut) . '...';
        }

        return $text;
    }

    public function getOgLocale(): string
    {
        return $this->localeProvider->getOgLocale();
    }
}
