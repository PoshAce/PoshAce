<?php
namespace Magecomp\Mobilelogin\Block\Account\Dashboard;

use Magento\Framework\View\Element\Template\Context;

class Updatemobile extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customersession;

    protected $_helper;

    /**
     * Updatemobile constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magecomp\Mobilelogin\Helper\Data $helper
    ) {
        $this->_customersession = $customerSession;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return int|mixed
     */
    public function getCustomerid()
    {
        $customerId = 0;
        if ($this->_customersession->isLoggedIn()) {
            $customerId = $this->_customersession->getCustomer()->getId();
        }

        return $customerId;
    }

    /**
     * @return int
     */
    public function getMobilenumber()
    {

        $mobileNumber = 0;
        $char="";
        if ($this->_customersession->isLoggedIn()) {
            $mobileNumber = $this->_customersession->getCustomer()->getMobilenumber();
        }
        $mobile_length=(strlen($mobileNumber))-4;        
        for($i=0;$i<$mobile_length;$i++)
        {
            $char.="X";
        }
        return substr_replace($mobileNumber, $char, '2', '-2');
    }

    public function getGeoCountryCode()
    {
        return $this->_helper->getGeoCountryCode();
    }

    public function getApplicableCountryJson()
    {
        return $this->_helper->getApplicableCountry(false);
    }
}
