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

namespace Magetrend\Affiliate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use \Magento\Checkout\Model\Session as CheckoutSession;

class RecordNewOrder implements ObserverInterface
{
    public $moduleHelper;

    public $cookieManager;

    public $orderFactory;

    public $registry;

    public $couponFactory;

    public $accountFactory;

    /**
     * Coupon Observer constructor.
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magetrend\Affiliate\Model\Sales\OrderFactory $orderFactory,
        \Magetrend\Affiliate\Model\CouponFactory $couponFactory,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->cookieManager = $cookieManager;
        $this->orderFactory = $orderFactory;
        $this->registry = $registry;
        $this->couponFactory = $couponFactory;
        $this->accountFactory = $accountFactory;
    }

    public function execute(Observer $observer)
    {
        if (!$this->moduleHelper->isActive()) {
            return;
        }

        $referralCode = $this->cookieManager->getCookie(
            \Magetrend\Affiliate\Helper\Data::REFERRAL_CODE_COOKIE_NAME,
            ''
        );

        $couponCode = $observer->getOrder()->getCouponCode();
        if (empty($referralCode) && empty($couponCode)) {
            return;
        }

        if (empty($referralCode) && !empty($couponCode)) {
            $referralCoupon = $this->couponFactory->create()
                ->load($couponCode, 'coupon_code');

            if (!$referralCoupon->getId()) {
                return;
            }

            $referral = $this->accountFactory->create()
                ->load($referralCoupon->getReferralId());

            if (!$referral->getId()) {
                return;
            }

            $referralCode = $referral->getReferralCode();
        }

        $orderId = $observer->getOrder()->getId();
        $order = $this->orderFactory->create();
        $order->load($orderId, 'order_id');
        $order->addData(
            [
                'order_id' => $orderId,
                'referral_code' => $referralCode,
                'required_update' => 1
            ]
        )
        ->save();

        $this->registry->register('mtaf_order_created', 1, true);
    }
}
