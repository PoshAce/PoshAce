<?php
namespace Magecomp\Mobileloginmsg91\Helper;

class Apicall extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_MSG91_API_SENDERID = 'mobilelogin/smsgatways/msg91senderid';
    const XML_MSG91_API_AUTHKEY = 'mobilelogin/smsgatways/msg91authkey';
    const XML_MSG91_API_URL = 'mobilelogin/smsgatways/msg91apiurl';
    const XML_MSG91_API_ROUTER = 'mobilelogin/smsgatways/msg91route';
    const XML_MSG91_API_DEVMODE = 'mobilelogin/smsgatways/msg91enabledev';
    const XML_MSG91_WHICH_API = 'mobilelogin/smsgatways/whichapiusing';


    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }

    public function getTitle()
    {
        return __("Msg91");
    }

    public function getApiSenderId()
    {
        return $this->scopeConfig->getValue(
            self::XML_MSG91_API_SENDERID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAuthKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_MSG91_API_AUTHKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getRouter()
    {
        return $this->scopeConfig->getValue(
            self::XML_MSG91_API_ROUTER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }


    public function getWhichApiUsing()
    {
        return $this->scopeConfig->getValue(
                self::XML_MSG91_WHICH_API,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getDevmode()
    {
            return $this->scopeConfig->getValue(
                self::XML_MSG91_API_DEVMODE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    public function getApiUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_MSG91_API_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function validateSmsConfig()
    {
        return $this->getApiUrl() && $this->getAuthKey() && $this->getApiSenderId();
    }

    public function callApiUrl($message, $mobilenumbers, $dlt)
    {      


        try {
           
            if($this->getWhichApiUsing()=='dlt'){

                $url = $this->getApiUrl();
                $authkey = $this->getAuthKey();
                $senderid = $this->getApiSenderId();
                $router = $this->getRouter();
                $devmode=$this->getWhichApiUsing();

                $ch = curl_init();
                if (!$ch) {
                    return "Couldn't initialize a cURL handle";
                }
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt(
                    $ch,
                    CURLOPT_POSTFIELDS,
                    "authkey=$authkey&mobiles=$mobilenumbers&message=$message&sender=$senderid&route=$router&country=0&DLT_TE_ID=$dlt&dev_mode=$devmode"
                );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $curlresponse = curl_exec($ch); // execute

                if (curl_errno($ch)) {
                    curl_close($ch);
                     return ["status"=>false, "message"=>'Error: '.curl_error($ch)];
                }
                curl_close($ch);
                return ["status"=>true, "message"=>'Message Sent !!!'];
            }else{
                
                $curl = curl_init();    
                $authkey= $this->getAuthKey();
                $url =  "https://control.msg91.com/api/v5/campaign/api/campaigns/".$compaignid."/run";
                $otp = ["value"=> $message];
                $variable = ["mobiles"=>$mobilenumbers,"variables"=>$otp];
                $sendto = ["sendTo"=> [["to"=>[$variable]]]];
                $data = ["data"=>$sendto];
                $array =   stripslashes(json_encode($data));
                  curl_setopt_array($curl, array(
                  CURLOPT_URL =>$url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>$array,
                  CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "authkey: $authkey",
                    "Cookie: PHPSESSID=45076ap8c8r1v2fsjr4m8mtgj0"
                  ),
                  
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                if ($err) {
                    return ["status"=>false, "message"=>'Something wrong Please Check.'];               
                } else {
                    return ["status"=>true, "message"=>'Message Sent !!!'];
                }  

                curl_close($curl);
            }
           
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }
}
