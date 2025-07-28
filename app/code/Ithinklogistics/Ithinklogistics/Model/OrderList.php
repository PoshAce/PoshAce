<?php

/**
 * Ithinklogistics
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ithinklogistics
 * @package     Ithinklogistics_Ithinklogistics
 * @copyright   Copyright (c) Ithinklogistics (https://www.ithinklogistics.com/)
 */ 

namespace Ithinklogistics\Ithinklogistics\Model;

use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class OrderList implements \Ithinklogistics\Ithinklogistics\Api\OrderListInterface {
    
    /**
     * @var CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ShipmentItemCollectionFactory
     */
    protected $_shipmentItemCollectionFactory;

    protected $_scopeConfig;

    protected $timezone;

    const ACCESS_TOKEN = 'ithinklogistics/general/access_token';

    const SECRET_KEY = 'ithinklogistics/general/secret_key';

    /**
     * Index constructor.
     * @param AitrillionConfig $aitrillionConfig
     * @param CollectionFactory $orderCollectionFactory
     * @param OrderRepository $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $orderCollectionFactory, 
        OrderRepository $orderRepository, 
        CustomerRepositoryInterface $customerRepository, 
        StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customer, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory $shipmentItemCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {

        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_objectManager = $objectManager;
        $this->_customer = $customer;
        $this->_customerRepository = $customerRepository;
        $this->_storeManager = $storeManager;       
        $this->_shipmentItemCollectionFactory = $shipmentItemCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function OrderList($status, $from_date, $page, $accesstoken, $secretkey) {
        
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $access_token = $this->scopeConfig->getValue(self::ACCESS_TOKEN, $storeScope);
        $secret_key = $this->scopeConfig->getValue(self::SECRET_KEY, $storeScope);
       
        if($access_token != $accesstoken){
           print_r(json_encode(array('msg'=>'error','data'=>"Please check access token"))); die;
        }else if($secret_key != $secretkey){
           print_r(json_encode(array('msg'=>'error','data'=>"Please check secret key"))); die;
        }else{
            
            $from_date = date("Y-m-d H:i:s", strtotime($from_date));
            $to_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")));
            $order = 'updated_at';
            $dir = 'desc';
            $orders = $this->_orderCollectionFactory->create()->addFieldToSelect('*')
             //->addFieldToFilter('updated_at', array('from'=>$from_date, 'to'=>$to_date))
             ->addFieldToFilter('updated_at', ['gteq' => $from_date]);
             if($status){
             $orders->addFieldToFilter('status', $status);
             $orders->setOrder('created_at','DESC');

             }else{
                $orders->setOrder('created_at','DESC');
             }

             $orders->addAttributeToSort($order, $dir)->setPageSize(50)->setCurPage($page);

             $order_list = array();
            foreach ($orders as $order) {
                $currency = $order->getOrderCurrency();
                if (is_object($currency)) {
                    $currency_code = $currency->getCurrencyCode();
                }

                $order->getData();

                $shipping = array(
                    'name'          => $order->getShippingAddress()->getFirstname()." ".$order->getShippingAddress()->getLastname(),
                    'email'         => $order->getCustomerEmail(),
                    'telephone'     => $order->getShippingAddress()->getTelephone(),
                    'street'        => $order->getShippingAddress()->getStreet(),
                    'pincode'       => $order->getShippingAddress()->getPostcode(),
                    'city'          => $order->getShippingAddress()->getCity(),
                    'region'        => $order->getShippingAddress()->getRegion(),
                    'region_id'     => $order->getShippingAddress()->getRegionId(),
                    'country'       => $order->getShippingAddress()->getCountryId(),
                    'company'       => $order->getShippingAddress()->getCompany()
                    );

                $billing = array(
                    'name'          => $order->getBillingAddress()->getFirstname()." ".$order->getBillingAddress()->getLastname(),
                    'email'         => $order->getCustomerEmail(),
                    'telephone'     => $order->getBillingAddress()->getTelephone(),
                    'street'        => $order->getBillingAddress()->getStreet(),
                    'pincode'       => $order->getBillingAddress()->getPostcode(),
                    'city'          => $order->getBillingAddress()->getCity(),
                    'region'        => $order->getBillingAddress()->getRegion(),
                    'region_id'     => $order->getBillingAddress()->getRegionId(),
                    'country'       => $order->getBillingAddress()->getCountryId(),
                    'company'       => $order->getBillingAddress()->getCompany(),
                    );
                 
                 $items = array();
                 $isshipped = 0;
                 foreach ($order->getAllItems() as $item) {
                    if($item->getProductType()=='configurable')
                        continue;

                    if($item->getQtyOrdered() == $item->getQtyShipped())
                    {
                       $isshipped = 1;
                    }

                    $childProductId = '';
                    if($item->getParentItem()) {
                        $productId = $item->getParentItem()->getproduct_id();
                        $childProductId = $item->getproduct_id();
                        $name = $item->getParentItem()->getName();
                        $price = $item->getParentItem()->getPrice();
                        $priceInclTax = $item->getParentItem()->getprice_incl_tax();
                        $taxPercent = $item->getParentItem()->gettax_percent();
                    }else {
                        $productId = $item->getProductId();
                        $name = $item->getName();
                        $price = $item->getPrice();
                        $priceInclTax = $item->getprice_incl_tax();
                        $taxPercent = $item->gettax_percent();
                    }

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                    $productUrl = $product->getProductUrl();

                    $items[] = array(
                    'product_id'    => $productId,
                    'child_product_id' => $childProductId,
                    'name'          => $name,
                    'sku'           => $item->getSku(),
                    'price'         => $price,
                    'price_incl_tax'=> $priceInclTax,
                    'tax_percent'   => $taxPercent,
                    'weight'        => $item->getWeight(),
                    'weight_unit'     => $this->getWeightUnit(),
                    'product_url'   => $productUrl,
                    'ordered_qty'   => $item->getQtyOrdered(),
                    'shipped_qty'   => $item->getQtyShipped()
                    );
                 }
                 
                $order_list[] = array(
                    'entity_id' => $order-> getEntityId(),
                    'order_id' => $order->getRealOrderId(), 
                    'remote_ip' => $order->getRemoteIp(),
                    'customer_id' => $order->getCustomerId()!=""?$order->getCustomerId():0,
                    'email'         => $order->getCustomerEmail(),
                    'order_currency_code' => $order->getOrderCurrencyCode(),
                    'base_currency_code' => $order->getBaseCurrencyCode(),
                    'created_at' => $this->getTimeZone($order->getCreatedAt()),
                    'updated_at' => $this->getTimeZone($order->getUpdatedAt()),
                    'tax_amount' => $order->getTaxAmount(),
                    'shipping_amount' => number_format($order->getShippingAmount(), 2, '.', '' ),
                    'discount_amount' => number_format($order->getDiscountAmount(), 2, '.', '' ),
                    'subtotal' => number_format($order->getSubtotal(), 2, '.', '' ),
                    'subtotal_incl_tax' => number_format($order->getSubtotalInclTax(), 2, '.', '' ),
                    'grand_total' => number_format($order->getGrandTotal(), 2, '.', '' ),
                    'coupon_code' => $order->getCouponCode(),
                    'total_qty_ordered' => $order->getTotalQtyOrdered(),
                    'shipping_address_name' => ($order->getShippingAddress()?$order->getShippingAddress()->getName():null),
                    'status' => $order->getStatusLabel(),
                    'weight'        => $order->getWeight(),
                    'weight_unit'     => $this->getWeightUnit(),
                    'shipping_method' => $order->getShippingMethod(),
                    'payment_method' => $order->getPayment()->getMethodInstance()->getTitle(),
                    'shipping' => $shipping,
                    'billing' => $billing,
                    'items' => $items,
                    'is_shipped' => $isshipped
                );
            }
            print_r(json_encode(array('msg'=>"success",'data'=>$order_list)));

        }
        
    }

    public function getTimeZone($date){
        $dateTimeZone = $this->timezone->date(new \DateTime($date))->format('Y-m-d H:i:s');
        return $dateTimeZone;
    }

    public function getWeightUnit()
    {
        return $this->scopeConfig->getValue('general/locale/weight_unit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}