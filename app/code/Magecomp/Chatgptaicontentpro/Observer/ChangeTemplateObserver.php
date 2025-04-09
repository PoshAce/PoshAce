<?php

namespace Magecomp\Chatgptaicontentpro\Observer;
use Magecomp\Chatgptaicontentpro\Helper\Data;


use Magento\Framework\Event\ObserverInterface;

class ChangeTemplateObserver implements ObserverInterface
{
    protected $helperdata;

    public function __construct(
         Data $helperdata
         ) 
    {
        $this->helperdata=$helperdata;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->helperdata->isEnabled()){
        $observer->getBlock()->setTemplate('Magecomp_Chatgptaicontentpro::helper/gallery.phtml');
        }
    }
}

