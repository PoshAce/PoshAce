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

class Currency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Currency
     */
    public $currency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Currency constructor.
     * @param \Magento\Config\Model\Config\Source\Locale\Currency $currency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Locale\Currency $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    ) {
        $this->currency = $currency;
        $this->storeManager = $storeManagerInterface;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        $options = $this->toArray();
        foreach ($options as $key => $label) {
            $data[] = [
                'value' => $key,
                'label' => $label
            ];
        }
        return $data;
    }

    /**
     * Get options in "key-value" format
     * @return array
     */

    public function toArray()
    {
        $availableCurrency = $this->getAvailableStoreList();
        $currencyList = $this->currency->toOptionArray();
        $options = [];
        foreach ($currencyList as $currency) {
            if (in_array($currency['value'], $availableCurrency)) {
                $options[$currency['value']] = $currency['label'];
            }
        }
        return $options;
    }

    /**
     * Returns used in stores currency list
     * @return array
     */
    public function getAvailableStoreList()
    {
        $currency = [];
        $storeList = $this->storeManager->getStores();
        foreach ($storeList as $store) {
            $code = $store->getCurrentCurrencyCode();
            if (!in_array($code, $currency)) {
                $currency[] = $code;
            }
        }

        return $currency;
    }
}
