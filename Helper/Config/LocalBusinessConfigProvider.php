<?php declare(strict_types=1);

namespace Web200\Seo\Helper\Config;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\Information as StoreInformation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Web200\Seo\Helper\Data as DataHelper;

class LocalBusinessConfigProvider extends DataHelper
{
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_ACTIVE = 'seo/local_business/active';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_ID = 'seo/local_business/id';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_NAME = 'seo/local_business/is_custom_name';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_NAME = 'seo/local_business/custom_name';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_PHONE = 'seo/local_business/is_custom_phone';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_PHONE = 'seo/local_business/custom_phone';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_EMAIL = 'seo/local_business/is_custom_email';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_EMAIL = 'seo/local_business/custom_email';
    public const XML_PATH_TRANS_EMAIL = 'trans_email/ident_general/email';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_INCLUDE_ADDRESS = 'seo/local_business/include_address';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_STREET = 'seo/local_business/is_custom_street';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_STREET = 'seo/local_business/custom_street';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_CITY = 'seo/local_business/is_custom_city';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_CITY = 'seo/local_business/custom_city';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_REGION = 'seo/local_business/is_custom_region';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_REGION = 'seo/local_business/custom_region';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_POSTCODE = 'seo/local_business/is_custom_postcode';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_POSTCODE = 'seo/local_business/custom_postcode';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_COUNTRY = 'seo/local_business/is_custom_country';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_COUNTRY = 'seo/local_business/custom_country';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_SAME_AS = 'seo/local_business/same_as';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_AREA_SERVED = 'seo/local_business/area_served';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_PARENT_ORGANIZATION_ID = 'seo/local_business/parent_organization_id';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_INCLUDE_CONTACT_POINT = 'seo/local_business/include_contact_point';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_CONTACT_TYPE = 'seo/local_business/contact_type';
    public const XML_PATH_CONFIG_LOCAL_BUSINESS_AVAILABLE_LANGUAGE = 'seo/local_business/available_language';
    private StoreManagerInterface $storeManager;
    private RegionFactory $regionFactory;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        RegionFactory $regionFactory
    ) {
        parent::__construct($context, $storeManager);
        $this->storeManager = $storeManager;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Checks if the local business markup is enabled for the given store ID.
     *
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function isLocalBusinessMarkupEnabled($storeId = null): bool
    {
        return $this->isModuleActive($storeId)
            && $this->scopeConfig->isSetFlag(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    /**
     * Retrieves the local business markup identifier for the given store ID.
     *
     * @param null|int|string $storeId
     *
     * @return string
     */
    public function getLocalBusinessMarkupId($storeId = null): string
    {
        $localBusinessId = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (str_starts_with($localBusinessId, '#')) {
            return $this->getWebsiteUrl() . $localBusinessId;
        }

        return $localBusinessId;
    }

    /**
     * Retrieves the local business markup name for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessName($storeId = null): string
    {
        $isCustomLocalBusinessNameEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessNameEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_NAME,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_NAME);
    }

    /**
     * Retrieves the local business markup phone for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessPhone($storeId = null): string
    {
        $isCustomLocalBusinessPhoneEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_PHONE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessPhoneEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_PHONE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_PHONE);
    }

    /**
     * Retrieves the local business markup email for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessEmail($storeId = null): string
    {
        $isCustomLocalBusinessEmailEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessEmailEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_EMAIL,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(self::XML_PATH_TRANS_EMAIL);
    }

    /**
     * Checks if the local business markup include address is enabled for the given store ID.
     *
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function includeAddressInLocalBusiness($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_INCLUDE_ADDRESS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves the local business markup street for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessStreet($storeId = null): string
    {
        $streetAddress = $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_STREET_LINE1);
        if ($streetLine2 = $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_STREET_LINE2)) {
            $streetAddress .= ' ' . $streetLine2;
        }

        $isCustomLocalBusinessStreetEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_STREET,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessStreetEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_STREET,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $streetAddress;
    }

    /**
     * Retrieves the local business markup city for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessCity($storeId = null): string
    {
        $isCustomLocalBusinessCityEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_CITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessCityEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_CITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_CITY);
    }

    /**
     * Retrieves the local business markup region for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessRegion($storeId = null): string
    {
        $regionId = $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_REGION_CODE);
        $region = $this->regionFactory->create()->load($regionId)->getCode();

        $isCustomLocalBusinessRegionEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_REGION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessRegionEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_REGION,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $region;
    }

    /**
     * Retrieves the local business markup postcode for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessPostcode($storeId = null): string
    {
        $isCustomLocalBusinessPostcodeEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_POSTCODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessPostcodeEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_POSTCODE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_POSTCODE);
    }

    /**
     * Retrieves the local business markup country for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessCountry($storeId = null): string
    {
        $isCustomLocalBusinessCountryEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_IS_CUSTOM_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomLocalBusinessCountryEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_LOCAL_BUSINESS_CUSTOM_COUNTRY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_COUNTRY_CODE);
    }

    /**
     * Retrieves the local business markup same as for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return array
     */
    public function getLocalBusinessSameAs($storeId = null): array
    {
        $localBusinessSameAs = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_SAME_AS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$localBusinessSameAs) {
            return [];
        }

        $values = preg_split('/[\r\n,]+/', $localBusinessSameAs) ?: [];

        return array_values(array_filter(array_map('trim', $values)));
    }

    /**
     * Retrieves the local business markup area served for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessAreaServed($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_AREA_SERVED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves the local business markup parent organization id for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessParentOrganizationId($storeId = null): string
    {
        $localBusinessParentOrganizationId = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_PARENT_ORGANIZATION_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (str_starts_with($localBusinessParentOrganizationId, '#')) {
            return $this->getWebsiteUrl() . $localBusinessParentOrganizationId;
        }

        return $localBusinessParentOrganizationId;
    }

    /**
     * Checks if the local business markup include contactPoint is enabled for the given store ID.
     *
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function includeContactPointInLocalBusiness($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_INCLUDE_CONTACT_POINT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves the local business markup contactType for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getLocalBusinessContactType($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_CONTACT_TYPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves the local business markup available languages for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return array
     */
    public function getLocalBusinessAvailableLanguages($storeId = null): array
    {
        $localBusinessAvailableLanguages = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_LOCAL_BUSINESS_AVAILABLE_LANGUAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$localBusinessAvailableLanguages) {
            return [];
        }

        $values = preg_split('/[\r\n,]+/', $localBusinessAvailableLanguages) ?: [];

        return array_values(array_filter(array_map('trim', $values)));
    }
}
