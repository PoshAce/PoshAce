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

namespace Magetrend\Affiliate\Observer\Sales\Model\Creditmemo;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use \Magento\Checkout\Model\Session as CheckoutSession;

class SaveAfter implements ObserverInterface
{
    public $moduleHelper;

    public $cookieManager;

    public $creditmemoFactory;

    /**
     * Coupon Observer constructor.
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magetrend\Affiliate\Model\Sales\CreditmemoFactory $creditmemoFactory
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->cookieManager = $cookieManager;
        $this->creditmemoFactory = $creditmemoFactory;
    }

    public function execute(Observer $observer)
    {
        if (!$this->moduleHelper->isActive()) {
            return;
        }

        $creditmemo = $observer->getCreditmemo();
        $creditmemoId = $creditmemo->getId();
        if (!$creditmemoId) {
            return;
        }

        $creditmemoHelper = $this->creditmemoFactory->create();
        $creditmemoHelper->load($creditmemoId, 'creditmemo_id');
        $creditmemoHelper->setCreditmemoId($creditmemoId)
            ->setOrderId($creditmemo->getOrderId())
            ->setRequiredUpdate(1)
            ->save();
    }
}
