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

namespace Magetrend\Affiliate\Block\Account;

/**
 * Referrals coupon list block
 */
class Coupon extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magetrend\Affiliate\Helper\Account
     */
    public $accountHelper;

    /**
     * @var \Magetrend\Affiliate\Model\ResourceModel\Coupon\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magetrend\Affiliate\Helper\Data
     */
    public $moduleHelper;

    /**
     * @var \Magetrend\Affiliate\Model\CouponManager
     */
    public $couponManager;

    /**
     * @var \Magetrend\Affiliate\Model\ResourceModel\Coupon\Collection|null
     */
    private $collection = null;

    /**
     * Coupon constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magetrend\Affiliate\Helper\Data $moduleHelper
     * @param \Magetrend\Affiliate\Helper\Account $accountHelper
     * @param \Magetrend\Affiliate\Model\CouponManager $couponManager
     * @param \Magetrend\Affiliate\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magetrend\Affiliate\Model\CouponManager $couponManager,
        \Magetrend\Affiliate\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->moduleHelper = $moduleHelper;
        $this->accountHelper = $accountHelper;
        $this->couponManager = $couponManager;
        parent::__construct($context, $data);
    }

    /**
     * Returns current refferal account
     * @return null
     */
    public function getAccount()
    {
        return $this->accountHelper->getCurrentAccount();
    }

    /**
     * Returns collection of coupons
     * @return \Magetrend\Affiliate\Model\ResourceModel\Coupon\Collection|null
     */
    public function getCollection()
    {
        if ($this->collection == null) {
            $this->couponManager->createCoupons($this->getAccount()->getId());
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('referral_id', $this->getAccount()->getId());

            $collection->joinProgram(['program_name' => 'program.name']);
            $collection->joinSalesRule([
                'salesrule_name' => 'salesrule.name',
                'salesrule_description' => 'salesrule.description',
            ]);

            $this->collection = $collection;
        }

        return $this->collection;
    }
}
