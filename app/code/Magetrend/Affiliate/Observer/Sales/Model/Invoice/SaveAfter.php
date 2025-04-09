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

namespace Magetrend\Affiliate\Observer\Sales\Model\Invoice;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use \Magento\Checkout\Model\Session as CheckoutSession;

class SaveAfter implements ObserverInterface
{
    public $moduleHelper;

    public $cookieManager;

    public $invoiceFactory;

    /**
     * Coupon Observer constructor.
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magetrend\Affiliate\Model\Sales\InvoiceFactory $invoiceFactory
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->cookieManager = $cookieManager;
        $this->invoiceFactory = $invoiceFactory;
    }

    public function execute(Observer $observer)
    {
        if (!$this->moduleHelper->isActive()) {
            return;
        }

        $invoice = $observer->getInvoice();
        $invoiceId = $invoice->getId();
        if (!$invoiceId) {
            return;
        }

        $invoiceHelper = $this->invoiceFactory->create();
        $invoiceHelper->load($invoiceId, 'invoice_id');
        $invoiceHelper->setInvoiceId($invoiceId)
            ->setOrderId($invoice->getOrderId())
            ->setRequiredUpdate(1)
            ->save();
    }
}
