<?php declare(strict_types=1);

namespace Web200\Seo\ViewModel;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Web200\Seo\Helper\Config\LocalBusinessConfigProvider;

class LocalBusiness implements ArgumentInterface
{
    private LocalBusinessConfigProvider $localBusinessConfig;
    private JsonSerializer $jsonSerializer;

    /**
     * Constructor method.
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        LocalBusinessConfigProvider $localBusinessConfig
    ) {
        $this->localBusinessConfig = $localBusinessConfig;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function getJsonData(): string
    {
        if (!$this->localBusinessConfig->isLocalBusinessMarkupEnabled()) {
            return '';
        }

        return $this->jsonSerializer->serialize($this->getJsonDataArray());
    }

    private function getJsonDataArray(): array
    {
        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'LocalBusiness',
//            '@id'             => $this->localBusinessConfig->getLocalBusinessMarkupId(),
            'name'            => $this->localBusinessConfig->getLocalBusinessName(),
            'url'             => $this->localBusinessConfig->getWebsiteUrl(),
            'logo'            => $this->localBusinessConfig->getStoreLogoUrl(),
            'telephone'       => $this->localBusinessConfig->getLocalBusinessPhone(),
            'priceRange'      => '€€'
        ];

        $sameAs = $this->localBusinessConfig->getLocalBusinessSameAs();
        if ($sameAs) {
            $data['sameAs'] = $sameAs;
        }

        if ($this->localBusinessConfig->includeAddressInLocalBusiness()) {
            $address = $this->getLocalAddress();

            $data['address'] = array_merge([
                '@type' => 'PostalAddress',
            ], $address);
        }

        $areaServed = $this->localBusinessConfig->getLocalBusinessAreaServed();
        if ($areaServed) {
            $data['areaServed'] = [
                '@type' => 'AdministrativeArea',
                'name' => $areaServed,
            ];
        }

        $parentOrganizationId = $this->localBusinessConfig->getLocalBusinessParentOrganizationId();
        if ($parentOrganizationId) {
            $data['parentOrganization'] = [
                '@id' => $parentOrganizationId,
            ];
        }

        if ($this->localBusinessConfig->includeContactPointInLocalBusiness()) {
            $data['contactPoint'] = [
                '@type'             => 'ContactPoint',
                'contactType'       => $this->localBusinessConfig->getLocalBusinessContactType(),
                'telephone'         => $this->localBusinessConfig->getLocalBusinessPhone(),
                'email'             => $this->localBusinessConfig->getLocalBusinessEmail(),
                'availableLanguage' => $this->localBusinessConfig->getLocalBusinessAvailableLanguages(),
            ];
        }

        return $data;
    }

    public function getLocalAddress(): array
    {
        return [
            'streetAddress'   => $this->localBusinessConfig->getLocalBusinessStreet(),
            'addressLocality' => $this->localBusinessConfig->getLocalBusinessCity(),
            'addressRegion'   => $this->localBusinessConfig->getLocalBusinessRegion(),
            'postalCode'      => $this->localBusinessConfig->getLocalBusinessPostcode(),
            'addressCountry'  => $this->localBusinessConfig->getLocalBusinessCountry(),
        ];
    }
}
