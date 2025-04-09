<?php 
namespace Magecomp\Chatgptaicontentpro\Block\Adminhtml\Category\Edit;
use Magecomp\Chatgptaicontentpro\Helper\Data;
use Magento\Framework\UrlInterface;

class Categorycontentdiscriptionbutton extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magecomp_Chatgptaicontentpro::category/buttoncontentdisc.phtml';
    protected $helperdata;
    protected $urlInterface;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Data $helperdata,
         UrlInterface $urlInterface,
        array $data = []
    ) {
         $this->helperdata=$helperdata;
         $this->urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }
    public function getButtonHtml()
    {
    if($this->helperdata->isEnabled()){
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(['id' => 'btn_id', 'label' => __('Generate AI Content'),'class'=>'action-default primary chatgptcontentmetadisc']);
        return $button->toHtml();
      }
    }
public function getCurrentUrl()
{
    $currentUrl = $this->urlInterface->getCurrentUrl();
    
    // Look for the position of '/store/' in the URL
    $storePos = strpos($currentUrl, '/store/');
    
    if ($storePos !== false) {
        // Extract the substring after '/store/'
        $storeSubstr = substr($currentUrl, $storePos + 7); // 7 because '/store/' is 7 characters long
        
        // Split the string by '/' to get the parts
        $storeParts = explode('/', $storeSubstr);
        
        // The store ID should be the first part after '/store/'
        $storeId = $storeParts[0];
        
        return $storeId;
    } else {
        return 0; // Default to 0 if '/store/' is not found
    }
}
}
