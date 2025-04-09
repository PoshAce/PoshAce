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

namespace Magetrend\Affiliate\Observer\Sales\Model\Order;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use \Magento\Checkout\Model\Session as CheckoutSession;

class SaveAfter implements ObserverInterface
{
    public $moduleHelper;

    public $cookieManager;

    public $orderFactory;

    public $registry;

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
        \Magento\Framework\Registry $registry
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->cookieManager = $cookieManager;
        $this->orderFactory = $orderFactory;
        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {
        if (!$this->moduleHelper->isActive()) {
            return;
        }

        if ($this->registry->registry('mtaf_order_created')) {
            return;
        }

        $orderId = $observer->getOrder()->getId();
        if (!$orderId) {
            return;
        }

        $order = $this->orderFactory->create();
        $order->load($orderId, 'order_id');
        if (!$order->getId()) {
            return;
        }

        $order->setRequiredUpdate(1)
            ->save();
    }
}
