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
class Order
{
    public $orderCollectionFactory;

    public $recordManager;

    public $orderRepository;

    public function __construct(
        \Magetrend\Affiliate\Model\ResourceModel\Sales\Order\CollectionFactory $orderCollectionFactory,
        \Magetrend\Affiliate\Model\RecordManager $recordManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->recordManager = $recordManager;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Send scheduled emails
     * @return bool
     */
    public function execute()
    {

        $collection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('required_update', 1);

        if ($collection->getSize() == 0) {
            return;
        }

        foreach ($collection as $orderData) {
            $order = $this->orderRepository->get($orderData->getOrderId());
            $data = [
                'referral_code' => $orderData->getReferralCode(),
                'order_id' => $order->getId(),
                'store_id' => $order->getStoreId(),
                'order_increment_id' => $order->getIncrementId(),
                'customer_id' => $order->getCustomerId(),
                'customer_name' => $order->getCustomerFirstname().' '.$order->getCustomerLastname(),
                'amount' => $order->getBaseGrandTotal(),
                'currency' => $order->getBaseCurrencyCode(),
                'order_status' => $order->getStatus(),
                'created_at' => $order->getCreatedAt(),
            ];

            $this->recordManager->recordOrder($data);
            $orderData->setRequiredUpdate(0);
        }

        $collection->walk('save');

        return true;
    }
}
