<?php 
namespace Magecomp\Chatgptaicontentpro\Block\Adminhtml\Cms\Edit;
use Magecomp\Chatgptaicontentpro\Helper\Data;
use Magento\Framework\UrlInterface;

class Cmsbutton extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magecomp_Chatgptaicontentpro::cms/button.phtml';
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
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(['id' => 'btn_id', 'label' => __('Generate AI Content'),'class'=>'action-default primary chatgptcsmmetatitle']);
        return $button->toHtml();
      }
    }
    public function getCurrentUrl(){
          $currentUrl = $this->urlInterface->getCurrentUrl();
          $idPos = strpos($currentUrl, '/id/');
            if ($idPos !== false) {
                $idSubstr = substr($currentUrl, $idPos + 4);
                $idParts = explode('/', $idSubstr);
                $storeId = $idParts[0];
                return $storeId;

            } else {
                return $storeId=0;
            }
     }
}
