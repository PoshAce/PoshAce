<?php
namespace Magecomp\Mobileloginmsg91\Model\Config;

class Msgid implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'dlt', 'label' => __('DLT Template')],
            ['value' => 'campaign', 'label' => __('Campaign Template')]
        ];
    }
}
