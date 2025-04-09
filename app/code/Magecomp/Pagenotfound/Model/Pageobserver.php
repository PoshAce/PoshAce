<?php
namespace Magecomp\Pagenotfound\Model;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\App\Response\RedirectInterface;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magecomp\Pagenotfound\Model\PagenotfoundFactory;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use \Magecomp\Pagenotfound\Helper\Data;

class Pageobserver implements ObserverInterface
{
    const XML_PATH_EMAIL_RECIPIENT = 'pagenotfound_configuration/pagenotfoundoption/recipient_email';
    const XML_PATH_EMAIL_TEMPLATE = 'pagenotfound_configuration/pagenotfoundoption/email_template';
    const XML_PATH_EMAIL_SENDER = 'pagenotfound_configuration/pagenotfoundoption/sender_email_identity';

	protected $scopeConfig;
	protected $storeManage;
	protected $pagenotfoundFactory;
	protected $transportBuilder;
	protected $inlineTranslation;
    protected $redirect;
    protected $helperData;
    protected $logger;

	public function __construct(LoggerInterface $logger,Data $helperData,ScopeConfigInterface $scopeConfig,StoreManagerInterface $storeManage, PagenotfoundFactory $pagenotfoundFactory, TransportBuilder $transportBuilder, StateInterface $inlineTranslation,RedirectInterface $redirect)
	{
        $this->logger = $logger;
		$this->scopeConfig = $scopeConfig;
		$this->storeManage = $storeManage;
		$this->pagenotfoundFactory = $pagenotfoundFactory;
		$this->transportBuilder = $transportBuilder;
		$this->inlineTranslation = $inlineTranslation;
        $this->redirect = $redirect;
        $this->helperData = $helperData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$id = $observer->getEvent()->getPage()->getIdentifier();

		$gloabal = $this->scopeConfig->getValue(\Magento\Cms\Helper\Page::XML_PATH_NO_ROUTE_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enable = $this->helperData->isEnabled();
        $cmspage = $this->helperData->getcmspage();
        $cmspagechoose = $this->helperData->getCmspageroutechoose();


        if ($id == $gloabal && $enable)
		{
		    $req = $observer->getEvent()->getControllerAction()->getRequest();

			 //Get Current Store ID
			 $storeid = $this->storeManage->getStore()->getId();

			// Get Current Requested Url
			$urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
			$url = $urlInterface->getCurrentUrl();

			//Save Data in Table
			$pagenotfound = $this->pagenotfoundFactory->create();
    		$pagenotfound->setData('store_id',$storeid)
						 ->setData('url',$url)
						 ->setData('client_ip',$req->getClientIp()) //Get Current Client Ip
						 ->setData('creation_date',date('Y-m-d H:i:s')) // Get Current Date and Time :
    			 		 ->save();


			// Send Mail To Admin For This
			/*$this->inlineTranslation->suspend();
			$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->transportBuilder
               ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
			   ->setTemplateOptions(
                    [
                        'area' => 'frontend',
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
               ->setTemplateVars(['url' => $url, 'clientip' => $req->getClientIp(), 'reqdate' => date('Y-m-d H:i:s')])
               ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
               ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
               ->getTransport();

            $transport->sendMessage();*/
			$this->inlineTranslation->resume();
            $controller = $observer->getControllerAction();
			if($cmspage){
			    if($cmspagechoose=="no-route"){
                    return $this;
                }
                    $this->redirect->redirect($controller->getResponse(), $this->storeManage->getStore()->getBaseUrl().$cmspagechoose);
                    return $this;
            }
		}
    }
}
