<?php declare(strict_types=1);

namespace Web200\Seo\Helper\Config;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\Information as StoreInformation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Web200\Seo\Helper\Data;

class OrganizationConfigProvider extends Data
{
    public const XML_PATH_CONFIG_ORGANIZATION_ACTIVE = 'seo/organization/active';
    public const XML_PATH_CONFIG_ORGANIZATION_ID = 'seo/organization/id';
    public const XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_NAME = 'seo/organization/is_custom_name';
    public const XML_PATH_CONFIG_ORGANIZATION_CUSTOM_NAME = 'seo/organization/custom_name';
    public const XML_PATH_CONFIG_ORGANIZATION_URL = 'seo/organization/url';
    public const XML_PATH_CONFIG_ORGANIZATION_INCLUDE_ADDRESS = 'seo/organization/include_address';
    public const XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_STREET = 'seo/organization/is_custom_street';
    public const XML_PATH_CONFIG_ORGANIZATION_CUSTOM_STREET = 'seo/organization/custom_street';
    public const XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_CITY = 'seo/organization/is_custom_city';
    public const XML_PATH_CONFIG_ORGANIZATION_CUSTOM_CITY = 'seo/organization/custom_city';
    public const XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_REGION = 'seo/organization/is_custom_region';
    public const XML_PATH_CONFIG_ORGANIZATION_CUSTOM_REGION = 'seo/organization/custom_region';
    public const XML_PATH_CONFIG_ORGANIZATION_CUSTOM_POSTCODE = 'seo/organization/custom_postcode';
    public const XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_COUNTRY = 'seo/organization/is_custom_country';
    public const XML_PATH_CONFIG_ORGANIZATION_CUSTOM_COUNTRY = 'seo/organization/custom_country';
    public const XML_PATH_CONFIG_ORGANIZATION_SAME_AS = 'seo/organization/same_as';
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
     * Checks if the organization markup is enabled for the given store ID.
     *
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function isOrganizationMarkupEnabled($storeId = null): bool
    {
        return $this->isModuleActive($storeId)
            && $this->scopeConfig->isSetFlag(
                self::XML_PATH_CONFIG_ORGANIZATION_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
    }

    /**
     * Retrieves the organization markup identifier for the given store ID.
     *
     * @param null|int|string $storeId
     *
     * @return string
     */
    public function getOrganizationMarkupId($storeId = null): string
    {
        $organizationId = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_ORGANIZATION_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (str_starts_with($organizationId, '#')) {
            return $this->getWebsiteUrl() . $organizationId;
        }

        return $organizationId;
    }

    /**
     * Retrieves the organization markup name for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getOrganizationName($storeId = null): string
    {
        $isCustomOrganizationNameEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_NAME,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomOrganizationNameEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_ORGANIZATION_CUSTOM_NAME,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_NAME);
    }

    /**
     * Retrieves the organization markup URL for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getOrganizationUrl($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_ORGANIZATION_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ) ?: $this->getWebsiteUrl();
    }

    /**
     * Checks if the organization markup include address is enabled for the given store ID.
     *
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function includeAddressInOrganization($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_ORGANIZATION_INCLUDE_ADDRESS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieves the organization markup street for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getOrganizationStreet($storeId = null): string
    {
        $streetAddress = $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_STREET_LINE1);
        if ($streetLine2 = $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_STREET_LINE2)) {
            $streetAddress .= ' ' . $streetLine2;
        }

        $isCustomOrganizationStreetEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_STREET,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomOrganizationStreetEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_ORGANIZATION_CUSTOM_STREET,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $streetAddress;
    }

    /**
     * Retrieves the organization markup city for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getOrganizationCity($storeId = null): string
    {
        $isCustomOrganizationCityEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_CITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomOrganizationCityEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_ORGANIZATION_CUSTOM_CITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_CITY);
    }

    /**
     * Retrieves the organization markup region for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getOrganizationRegion($storeId = null): string
    {
        $regionId = $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_REGION_CODE);
        $region = $this->regionFactory->create()->load($regionId)->getCode();

        $isCustomOrganizationRegionEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_REGION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomOrganizationRegionEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_ORGANIZATION_CUSTOM_REGION,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $region;
    }

    /**
     * Retrieves the organization markup postcode for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getOrganizationPostcode($storeId = null): string
    {
        $isCustomOrganizationPostcodeEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_REGION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomOrganizationPostcodeEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_ORGANIZATION_CUSTOM_POSTCODE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_POSTCODE);
    }

    /**
     * Retrieves the organization markup country for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return string
     */
    public function getOrganizationCountry($storeId = null): string
    {
        $isCustomOrganizationCountryEnabled = $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIG_ORGANIZATION_IS_CUSTOM_COUNTRY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isCustomOrganizationCountryEnabled
            ? (string)$this->scopeConfig->getValue(
                self::XML_PATH_CONFIG_ORGANIZATION_CUSTOM_COUNTRY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
            : $this->getStore()->getConfig(StoreInformation::XML_PATH_STORE_INFO_COUNTRY_CODE);
    }

    /**
     * Retrieves the organization markup same as for the given store ID.
     *
     * @param int|string|null $storeId
     *
     * @return array
     */
    public function getOrganizationSameAs($storeId = null): array
    {
        $organizationSameAs = (string)$this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_ORGANIZATION_SAME_AS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$organizationSameAs) {
            return [];
        }

        $values = preg_split('/[\r\n,]+/', $organizationSameAs) ?: [];

        return array_values(array_filter(array_map('trim', $values)));
    }
}
