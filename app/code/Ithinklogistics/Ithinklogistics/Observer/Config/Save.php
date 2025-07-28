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

namespace Ithinklogistics\Ithinklogistics\Observer\Config;

use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Save implements ObserverInterface {

    /**
     * Application config
     *
     * @var ScopeConfigInterface
     */
    private $appConfig;

    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

     /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @method __construct
     * @param  TypeListInterface         $cacheTypeList
     * @param  ReinitableConfigInterface $config
     * @param  RequestInterface               $request
     */
    public function __construct(
        TypeListInterface $cacheTypeList,
        ReinitableConfigInterface $config,
        RequestInterface $request
    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->appConfig = $config;
        $this->request = $request;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $scope = ScopeInterface::SCOPE_STORE;
        $groups = $this->request->getParam('groups');
        $access_token = $groups['general']['fields']['access_token']['value'];
        $secret_key = $groups['general']['fields']['secret_key']['value'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $fetchOne = "select * from ithinklogistics_credential";
        $alreadyExist = $connection->fetchAll($fetchOne);
        if(count($alreadyExist) > 0){
            $sql = "Update ithinklogistics_credential Set access_token = '".$access_token."',secret_key = '".$secret_key."'";
            $connection->query($sql);
        }elseif(count($alreadyExist) < 1){
            $sql = "insert into ithinklogistics_credential (access_token, secret_key) VALUES ('".$access_token."','".$secret_key."')";
            $connection->query($sql);
        } 
    }
}
