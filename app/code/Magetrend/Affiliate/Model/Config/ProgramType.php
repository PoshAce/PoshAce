<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\Affiliate\Model\Config;

use Magetrend\Affiliate\Model\Program;

class ProgramType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve option array of store contact variables
     *
     * @param bool $withGroup
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = $this->getData();
        return $optionArray;
    }

    public function toArray()
    {
        $optionsArray = $this->getData();
        $data = [];
        foreach ($optionsArray as $option) {
            $data[$option['value']] = $option['label'];
        }

        return $data;
    }

    /**
     * Return available config variables
     * This method can be extended by plugin if you need to add more variables to the list
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getData()
    {
        return [
            ['value' => Program::TYPE_PAY_PER_SALE, 'label' => __('Pay per Sale')],
            ['value' => Program::TYPE_PAY_PER_CLICK, 'label' => __('Pay per Click')],
        ];
    }
}
