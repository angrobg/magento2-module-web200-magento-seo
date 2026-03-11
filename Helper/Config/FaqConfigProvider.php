<?php declare(strict_types=1);

namespace Web200\Seo\Helper\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class FaqConfigProvider
{
    private const XML_PATH_FAQ_ENABLED = 'seo/faq/enabled';
    private const XML_PATH_FAQ_JSON = 'seo/faq/json';
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FAQ_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getJson(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_JSON,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
