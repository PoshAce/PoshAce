<?php
namespace Magecomp\Mobilelogintwilio\Helper;

class Apicall extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_TWILIOSMS_ACCOUNTSID = 'mobilelogin/smsgatways/twiliosid';
    const XML_TWILIOSMS_AUTHTOKEN = 'mobilelogin/smsgatways/twiliotoken';
    const XML_TWILIOSMS_MOBILENUMBER = 'mobilelogin/smsgatways/twilionumber';

    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }

    public function getTitle()
    {
        return __("Twilio");
    }

    public function getAccountsid()
    {
        return $this->scopeConfig->getValue(
            self::XML_TWILIOSMS_ACCOUNTSID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAuthtoken()
    {
        return $this->scopeConfig->getValue(
            self::XML_TWILIOSMS_AUTHTOKEN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getMobileNumber()
    {
        return '+'.$this->scopeConfig->getValue(
            self::XML_TWILIOSMS_MOBILENUMBER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function validateSmsConfig()
    {
        $twilioclassExist = class_exists('Twilio\Rest\Client');

        if (!$twilioclassExist) {
            $this->_logger->error(__("Run 'composer require twilio/sdk' from CLI to use Twilio."));
        }

        return $twilioclassExist && $this->getAccountsid() && $this->getAuthtoken() && $this->getMobileNumber();
    }

    public function callApiUrl($message, $mobilenumbers)
    {
        try {

            $account_sid = $this->getAccountsid();
            $auth_token = $this->getAuthtoken();

            if (substr((string)$mobilenumbers, 0, 1) !== '+') {
                $mobilenumbers = '+'.$mobilenumbers;
            }

            $client = new \Twilio\Rest\Client($account_sid, $auth_token);
            $returntwilio = $client->messages->create(
                $mobilenumbers,
                ['from' => $this->getMobileNumber(),'body' => $message]
            );

            if ($returntwilio->status == 'undelivered') {
                return ["status"=>false, "message"=>'Message Undelivered'];
            }
            return ["status"=>true, "message"=>'Message Sent !!!'];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }
}
