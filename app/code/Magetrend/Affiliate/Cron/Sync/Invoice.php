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

namespace Magetrend\Affiliate\Cron\Sync;

/**
 * Send emails cron class
 *
 * @category MageTrend
 * @package  Magetend/AbandonedCart
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-abandoned-cart
 */
class Invoice
{
    public $invoiceCollectionFactory;

    public $recordManager;

    public $invoiceRepository;

    public $orderRepository;

    public function __construct(
        \Magetrend\Affiliate\Model\ResourceModel\Sales\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magetrend\Affiliate\Model\RecordManager $recordManager,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->recordManager = $recordManager;
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Send scheduled emails
     * @return bool
     */
    public function execute()
    {
        $collection = $this->invoiceCollectionFactory->create()
            ->addFieldToFilter('main_table.required_update', 1)
            ->joinTransactoionTable(['transaction_id' => 'transaction.entity_id'])
            ->joinOrderTable(['referral_code' => 'order.referral_code']);

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $invoiceData) {
            if ($invoiceData->getTransactionId() || !$invoiceData->getReferralCode()) {
                $invoiceData->setRequiredUpdate(0);
                continue;
            }

            $invoice = $this->invoiceRepository->get($invoiceData->getInvoiceId());
            $order = $this->orderRepository->get($invoice->getOrderId());

            $data = [
                'object_type' => 'invoice',
                'object_id' => $invoice->getId(),
                'referral_code' => $invoiceData->getReferralCode(),
                'order_id' => $order->getId(),
                'store_id' => $invoice->getStoreId(),
                'order_increment_id' => $order->getIncrementId(),
                'customer_id' => $order->getCustomerId(),
                'customer_name' => $order->getCustomerFirstname().' '.$order->getCustomerLastname(),
                'amount' => $order->getBaseGrandTotal(),
                'currency' => $order->getBaseCurrencyCode(),
                'order_status' => $order->getStatus(),
                'created_at' => $invoice->getCreatedAt(),
            ];

            $this->recordManager->recordTransaction($data);
            $invoiceData->setRequiredUpdate(0);
        }

        $collection->walk('save');

        return true;
    }
}
