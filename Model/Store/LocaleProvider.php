<?php declare(strict_types=1);

namespace Web200\Seo\Model\Store;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class LocaleProvider
{
    const GENERAL_LOCALE_CODE = 'general/locale/code';
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getOgLocale(): ?string
    {
        try {
            return (string)$this->storeManager
                ->getStore()
                ->getConfig(self::GENERAL_LOCALE_CODE);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
