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

class AddShipment implements \Ithinklogistics\Ithinklogistics\Api\AddShipmentInterface {
    
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

    protected $_logger;

    protected $order;

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
        \Magento\Sales\Model\Order\Shipment\TrackFactory $shipmentTrackFactory,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\Data\OrderInterface $order

    ) {

        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_objectManager = $objectManager;
        $this->_customer = $customer;
        $this->_customerRepository = $customerRepository;
        $this->_storeManager = $storeManager;       
        $this->_shipmentItemCollectionFactory = $shipmentItemCollectionFactory;
        $this->_shipmentTrackFactory = $shipmentTrackFactory;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->order = $order;
    }

    /**
     * {@inheritdoc}
     */
    public function AddShipment($order_id, $tracking_number, $tracking_company, $notify, $comment, $accesstoken, $secretkey) {

        try {

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $access_token = $this->scopeConfig->getValue(self::ACCESS_TOKEN, $storeScope);
            $secret_key = $this->scopeConfig->getValue(self::SECRET_KEY, $storeScope);
            

            if($access_token != $accesstoken){
               print_r(json_encode(array('msg'=>'error','data'=>"Please check access token"))); die;
            }else if($secret_key != $secretkey){
               print_r(json_encode(array('msg'=>'error','data'=>"Please check secret key"))); die;
            }
            $order = $this->order->loadByIncrementId($order_id);




            if ($order){
                $data = array(array(
                    'carrier_code' => $order->getShippingMethod(),
                    'title' => $tracking_company,
                    'number' => $tracking_number,
                ));

                $shipment = $this->prepareShipment($order, $data);
                if ($shipment) {
                    $order->setIsInProcess(true);
                    $order->addStatusHistoryComment($comment, false);
                    $transactionSave =  $this->_transactionFactory->create()->addObject($shipment)->addObject($shipment->getOrder());
                    $transactionSave->save();
                    if($notify){
                      $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')->notify($shipment);
                    }
                }
                if($shipment){
                    print_r(json_encode(array('msg'=>'success','data'=>"Shipment Created SuccessFully for Order Number".$order_id)));
                }else{
                    print_r(json_encode(array('msg'=>'error','data'=>"Shipment already Created for Order Number".$order_id)));
                }
            }

        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }

    public function prepareShipment($order, $track)
    {
       $shipment = $this->_shipmentFactory->create(
           $order,
           $this->prepareShipmentItems($order),
           $track
       );
       return $shipment->getTotalQty() ? $shipment->register() : false;
    }
     
    /**
    * @param $order \Magento\Sales\Model\Order
    * @return array
    */
    public function prepareShipmentItems($order)
    {
       $items = [];
     
       foreach($order->getAllItems() as $item) {
           $items[$item->getItemId()] = $item->getQtyOrdered();
       }
       return $items;
    }
}