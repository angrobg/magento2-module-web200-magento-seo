<?php declare(strict_types=1);

namespace Web200\Seo\Model\Config\Source;


use Magento\Framework\Data\OptionSourceInterface;

class CustomValue implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Add from Store Information')],
            ['value' => 1, 'label' => __('Add Manually')],
        ];
    }
}
