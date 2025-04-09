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
class Creditmemo
{
    public $creditmemoCollectionFactory;

    public $recordManager;

    public $creditmemoRepository;

    public $orderRepository;

    public function __construct(
        \Magetrend\Affiliate\Model\ResourceModel\Sales\Creditmemo\CollectionFactory $creditmemoCollectionFactory,
        \Magetrend\Affiliate\Model\RecordManager $recordManager,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->recordManager = $recordManager;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Send scheduled emails
     * @return bool
     */
    public function execute()
    {
        $collection = $this->creditmemoCollectionFactory->create()
            ->addFieldToFilter('main_table.required_update', 1)
            ->joinTransactoionTable(['transaction_id' => 'transaction.entity_id'])
            ->joinOrderTable(['referral_code' => 'order.referral_code']);

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $creditmemoData) {
            if ($creditmemoData->getTransactionId() || !$creditmemoData->getReferralCode()) {
                $creditmemoData->setRequiredUpdate(0);
                continue;
            }

            $creditmemo = $this->creditmemoRepository->get($creditmemoData->getCreditmemoId());
            $order = $this->orderRepository->get($creditmemo->getOrderId());

            $data = [
                'object_type' => 'creditmemo',
                'object_id' => $creditmemo->getId(),
                'referral_code' => $creditmemoData->getReferralCode(),
                'order_id' => $order->getId(),
                'store_id' => $creditmemo->getStoreId(),
                'order_increment_id' => $order->getIncrementId(),
                'customer_id' => $order->getCustomerId(),
                'customer_name' => $order->getCustomerFirstname().' '.$order->getCustomerLastname(),
                'amount' => $order->getBaseGrandTotal(),
                'currency' => $order->getBaseCurrencyCode(),
                'order_status' => $order->getStatus(),
                'created_at' => $creditmemo->getCreatedAt(),
            ];

            $this->recordManager->recordTransaction($data);
            $creditmemoData->setRequiredUpdate(0);
        }

        $collection->walk('save');

        return true;
    }
}
