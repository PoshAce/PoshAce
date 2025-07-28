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

class OrderCount implements \Ithinklogistics\Ithinklogistics\Api\OrderInterface {
    
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
        StoreManagerInterface $storeManager, 
        \Magento\Framework\ObjectManagerInterface $objectManager, 
    	\Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory $shipmentItemCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;       
        $this->_shipmentItemCollectionFactory = $shipmentItemCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

	public function OrderCount($status, $from_date, $accesstoken, $secretkey) {

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $access_token = $this->scopeConfig->getValue(self::ACCESS_TOKEN, $storeScope);
        $secret_key = $this->scopeConfig->getValue(self::SECRET_KEY, $storeScope);
       
        if($access_token != $accesstoken){
           print_r(json_encode(array('msg'=>'error','data'=>"Please check access token"))); die;
        }else if($secret_key != $secretkey){
           print_r(json_encode(array('msg'=>'error','data'=>"Please check secret key"))); die;
        }
         date_default_timezone_set('Asia/Calcutta');
        $from_date = date("Y-m-d H:i:s", strtotime($from_date));
        $to_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")));

        $collection = $this->_orderCollectionFactory->create()->addFieldToSelect('*')
         ->addFieldToFilter('updated_at', ['gteq' => $from_date]);
         if($status){
            $collection->addFieldToFilter('status', $status);
         }

        print_r(json_encode(array('msg'=>"success",'count'=>count($collection))));
	}
}