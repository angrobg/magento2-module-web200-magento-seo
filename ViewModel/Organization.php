<?php declare(strict_types=1);

namespace Web200\Seo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Web200\Seo\Helper\Config\OrganizationConfigProvider;

class Organization implements ArgumentInterface
{
    private OrganizationConfigProvider $organizationConfig;
    private JsonSerializer $jsonSerializer;

    /**
     * Constructor method.
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        OrganizationConfigProvider $organizationConfig
    ) {
        $this->organizationConfig = $organizationConfig;
        $this->jsonSerializer = $jsonSerializer;
    }

    public function getJsonData(): string
    {
        if (!$this->organizationConfig->isOrganizationMarkupEnabled()) {
            return '';
        }

        return $this->jsonSerializer->serialize($this->getJsonDataArray());
    }

    private function getJsonDataArray(): array
    {
        $data = [
            '@context'        => 'https://schema.org',
            '@type'           => 'Organization',
//            '@id'             => $this->organizationConfig->getOrganizationMarkupId(),
            'name'            => $this->organizationConfig->getOrganizationName(),
            'url'             => $this->organizationConfig->getOrganizationUrl(),
            'logo'            => $this->organizationConfig->getStoreLogoUrl(),
        ];

        $sameAs = $this->organizationConfig->getOrganizationSameAs();
        if ($sameAs) {
            $data['sameAs'] = $sameAs;
        }

        if ($this->organizationConfig->includeAddressInOrganization()) {
            $address = $this->getLocalAddress();

            $data['address'] = array_merge([
                '@type' => 'PostalAddress',
            ], $address);
        }

        return $data;
    }

    public function getLocalAddress(): array
    {
        return [
            'streetAddress'   => $this->organizationConfig->getOrganizationStreet(),
            'addressLocality' => $this->organizationConfig->getOrganizationCity(),
            'addressRegion'   => $this->organizationConfig->getOrganizationRegion(),
            'postalCode'      => $this->organizationConfig->getOrganizationPostcode(),
            'addressCountry'  => $this->organizationConfig->getOrganizationCountry(),
        ];
    }
}
