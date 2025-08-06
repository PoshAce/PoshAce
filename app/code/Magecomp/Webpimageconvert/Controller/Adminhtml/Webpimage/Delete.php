<?php
namespace Magecomp\Webpimageconvert\Controller\Adminhtml\Webpimage;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magecomp\Webpimageconvert\Helper\Data;
use Magento\Backend\App\Action\Context;

class Delete extends \Magento\Backend\App\Action 
{
    protected $notificationFactory;
    protected $_jsonResultFactory;
    protected $helper;

    public function __construct(
        Context $context,
        Data $helper,
        JsonFactory $jsonResultFactory
    ) {
        $this->_jsonResultFactory = $jsonResultFactory;
        $this->helper = $helper;
    	parent::__construct($context);	
    }

    public function execute()
    {
        try {
            $jsonResult = $this->_jsonResultFactory->create();
            if ($this->helper->clearWebp() == 'nowebpFolder') {
                $response['message'] = __("WebP Image Cache is empty");
            } elseif ($this->helper->clearWebp()) {
                $response['message'] = __("All WebP images have been removed");
            } else {
                $response['message'] = __("Can not remove the webp_image folder (please check folder permissions)");
            }
            $jsonResult->setData($response);
            return $jsonResult;
       }catch (\Exception $e) {
            $jsonResult = $this->_jsonResultFactory->create();
            $response['message'] = __("Something Went Wrong. ".$e->getMessage());
            $jsonResult->setData($response);
            return $jsonResult;
        }
    }

}
