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

class AffiliateProgram implements \Magento\Framework\Option\ArrayInterface
{
    public $collectionFactory;

    public function __construct(
        \Magetrend\Affiliate\Model\ResourceModel\Program\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

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
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('is_deleted', ['neq' => 1]);

        if ($collection->getSize() == 0) {
            return [];
        }

        $options = [];
        foreach ($collection as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName()
            ];
        }
        return $options;
    }
}
