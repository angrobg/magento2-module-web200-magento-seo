<?php declare(strict_types=1);

namespace Web200\Seo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Web200\Seo\Helper\Config\FaqConfigProvider;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use InvalidArgumentException;

class Faq implements ArgumentInterface
{
    private JsonSerializer $jsonSerializer;
    private FaqConfigProvider $faqConfigProvider;

    /**
     * Constructor method.
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        FaqConfigProvider $faqConfigProvider
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->faqConfigProvider = $faqConfigProvider;
    }

    public function getJsonData(): string
    {
        if (!$this->faqConfigProvider->isEnabled()) {
            return '';
        }

        $json = trim($this->faqConfigProvider->getJson());

        if ($json === '') {
            return '';
        }

        try {
            $this->jsonSerializer->unserialize($json);
        } catch (InvalidArgumentException $e) {
            return '';
        }

        return $json;
    }
}
