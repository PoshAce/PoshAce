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

namespace Magetrend\Affiliate\Model;

use Magento\Framework\Exception\LocalizedException;

class Program extends \Magento\Framework\Model\AbstractModel
{
    const TYPE_PAY_PER_SALE = 'pay_per_sale';

    const TYPE_PAY_PER_CLICK = 'pay_per_click';

    public $json;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $json,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->json = $json;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\Program');
    }

    public function beforeSave()
    {
        $coupon = $this->getCoupon();
        if (is_array($coupon)) {
            $this->setCoupon($this->json->jsonEncode($coupon));
        }

        $commission = $this->getCommission();
        if (is_array($commission)) {
            $this->setCommission($this->json->jsonEncode($commission));
        }

        return parent::beforeSave();
    }

    protected function _afterLoad()
    {
        $coupon = $this->getCoupon();
        if (!is_array($coupon) && !empty($coupon)) {
            $this->setCoupon($this->json->jsonDecode($coupon));
        }

        $commission = $this->getCommission();
        if (!is_array($commission) && !empty($commission)) {
            $commission = $this->json->jsonDecode($commission);
            if (isset($commission['__empty'])) {
                unset($commission['__empty']);
            }
            $this->setCommission($commission);
        }
        return parent::_afterLoad();
    }

    public function getCommissionTier($nth)
    {
        $commissionTier = $this->getCommission();
        if (empty($commissionTier)) {
            return false;
        }

        if (!is_array($commissionTier)) {
            $commissionTier = $this->json->jsonDecode($commissionTier);
        }

        $closetTier = false;
        foreach ($commissionTier as $commission) {
            if (!isset($commission['tier']) || !is_numeric($commission['tier']) || !is_numeric($commission['rate'])) {
                continue;
            }

            if ($commission['tier'] > $nth) {
                break;
            }

            if ($commission['tier'] == $nth) {
                return $commission;
            }

            $closetTier = $commission;
        }

        return $closetTier;
    }
}
