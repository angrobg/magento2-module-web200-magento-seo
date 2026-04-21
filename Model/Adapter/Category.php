<?php

declare(strict_types=1);

namespace Web200\Seo\Model\Adapter;

use Magento\Catalog\Model\Category as ModelCategory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Web200\Seo\Api\Data\AdapterInterface;
use Web200\Seo\Api\Data\PropertyInterface;
use Web200\Seo\Helper\Data;
use Web200\Seo\Model\BlockParser;
use Web200\Seo\Model\Property;
use Web200\Seo\Model\Store\LocaleProvider;

/**
 * Class Category
 *
 * @package   Web200\Seo\Model\Adapter
 * @author    Web200 <contact@web200.fr>
 * @copyright 2021 Web200
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://www.web200.fr/
 */
class Category implements AdapterInterface
{
    /**
     * Message attributes
     *
     * @var string[] $messageAttributes
     */
    protected $messageAttributes = ['description', 'meta_description'];
    /**
     * Registry
     *
     * @var Registry $registry
     */
    protected $registry;
    /**
     * Property interface
     *
     * @var PropertyInterface $property
     */
    protected $property;
    /**
     * Block parser
     *
     * @var BlockParser $blockParser
     */
    protected $blockParser;

    /**
     * @var LocaleProvider
     */
    private LocaleProvider $localeProvider;

    /**
     * @var Data
     */
    private Data $helper;

    /**
     * Category constructor.
     *
     * @param PropertyInterface $property
     * @param BlockParser $blockParser
     * @param Registry $registry
     * @param LocaleProvider $localeProvider
     * @param Data $helper
     */
    public function __construct(
        PropertyInterface $property,
        BlockParser $blockParser,
        Registry $registry,
        LocaleProvider $localeProvider,
        Data $helper
    ) {
        $this->registry    = $registry;
        $this->property    = $property;
        $this->blockParser = $blockParser;
        $this->localeProvider = $localeProvider;
        $this->helper = $helper;
    }

    /**
     * Get property
     *
     * @return PropertyInterface
     * @throws LocalizedException
     */
    public function getProperty(): PropertyInterface
    {
        /** @var ModelCategory $category */
        $category = $this->registry->registry('current_category');
        if ($category) {
            $this->property->setTitle((string)$category->getName());
            $this->property->setUrl((string)$category->getUrl());

            foreach ($this->messageAttributes as $messageAttribute) {
                $description = (string)$category->getData($messageAttribute);
                $description = $this->helper->prepareOgDescription($description);

                if ($description) {
                    $this->property->setDescription($description);
                }
            }

            $type = Page::OG_TYPE;
            $this->property->addProperty('type', $type);

            if ($category->hasLandingPage() && !$this->property->getProperty('description')) {
                $this->property->setDescription(
                    $this->blockParser->getBlockContentById((int)$category->getLandingPage())
                );
            }

            $image = $this->getImage($category->getImageUrl());

            if ($image) {
                $this->property->setImage((string)$image);
            }

            $this->property->addProperty('item', $category->getData(), Property::META_DATA_GROUP);

            $locale = $this->localeProvider->getOgLocale();

            if ($locale) {
                $this->property->addProperty('locale', $this->localeProvider->getOgLocale());
            }
        }

        return $this->property;
    }

    private function getImage(bool $imageUrl)
    {
        return $imageUrl ?: $this->helper->getOgDefaultImage();
    }
}
